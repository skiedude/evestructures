<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use App\Http\Controllers\CharacterController;
use App\Character;
use App\Structure;
use App\StructureService;
use App\StructureState;
use App\StructureVul;


class StructureController extends Controller
{

  public function __construct() {
      $this->middleware('auth');
  }

	public function show($structure_id) {
		$structure = Structure::where('user_id', \Auth::id())->where('structure_id', $structure_id)->first();
		if(is_null($structure)) {
			$alert = "Structure not found on this account";
			return redirect()->to('/home')->with('alert', [$alert]);
		}
		$services = StructureService::where('character_id', $structure->character_id)->where('structure_id', $structure_id)->get();
		$state = StructureState::where('structure_id', $structure_id)->get();

    return view('structure', compact(['structure', 'services', 'state']));
	}

	public function create($character_id) {

		$tz = array("T", "Z");
		$character = Character::where('user_id', \Auth::id())->where('character_id', $character_id)->first();
		if(is_null($character)) {
			$alert = "Character not found on this account";
			return redirect()->to('/home')->with('alert', [$alert]);
		}

		if(isset($character->last_fetch)){
			$now = new \DateTime();
			$last_fetch = new \DateTime($character->last_fetch);
			$diff = date_diff($now, $last_fetch);
			if($diff->h < 1) {
				$new_min = (60 - $diff->i);
				$warning = "CCP caches structure data for up to 1 hour. Please try again in $new_min minute(s)";
				return redirect()->to('/home')->with('warning', [$warning]);
			}
		}

		$client = new Client(['base_uri' => 'https://esi.tech.ccp.is/']);
    $refresh = CharacterController::tokenRefresh($character->character_id);

		switch ($refresh) {
			case "not_expired":
				//Good to go
				break;

			case "refreshed":
				//Pull down new info from DB
				$character = Character::where('user_id', \Auth::id())->where('character_id', $character_id)->first();
				break;

			default:
				break;
		}

		$auth_headers = [
      'headers' => [
      'User-Agent' => env('USERAGENT'),
      ],
      'query' => [
        'datasource' => 'tranquility',
        'token'   => $character->access_token
      ]
    ];

		try {
			$roles_url = "/v2/characters/$character->character_id/roles/";
			$resp = $client->get($roles_url, $auth_headers);
			$roles = json_decode($resp->getBody());

		} catch (ServerException $e ) {
      $alert = "We received a 5xx error from ESI, this usually means an issue on CCP's end, please try again later.";
      //5xx error, usually and issue with ESI
      return redirect()->to('/home')->with('alert', [$alert]);
    } catch (\Exception $e) {
      //Everything else
      $alert = "We failed to pull your rolls, please try again later.";
      return redirect()->to('/home')->with('alert', [$alert]);
    }

    if(!in_array("Station_Manager", $roles->roles)) {
      $alert = "Character $character->character_name doesn't have the Station Manager Role, this is required to pull Corporation Structures. Once added please wait at least 60 minutes before trying again.";
      return redirect()->to('/home')->with('alert', [$alert]);
		} 

		try {
			$structure_url = "/v1/corporations/$character->corporation_id/structures/";
			$resp = $client->get($structure_url, $auth_headers);
			$esi_structures = json_decode($resp->getBody());

		} catch (ServerException $e ) {
      $alert = "We received a 5xx error from ESI, this usually means an issue on CCP's end, please try again later.";
      //5xx error, usually and issue with ESI
      return redirect()->to('/home')->with('alert', [$alert]);
    } catch (\Exception $e) {
      //Everything else
      $alert = "We failed to pull your structures, please try again later.";
      return redirect()->to('/home')->with('alert', [$alert]);
    }

    $current_structures = Structure::select('structure_id')
													->where('character_id', $character->character_id)
													->get();  

		$api_structures = array();
    foreach($esi_structures as $s) {
			array_push($api_structures, $s->structure_id);
		}

		//Delete Structures and relations that aren't returned in the API call
    foreach($current_structures as $cs) {
			if(!in_array($cs->structure_id, $api_structures)) {
				Structure::where('structure_id', $cs->structure_id)->where('character_id', $character->character_id)->delete();
        StructureService::where('structure_id', $cs->structure_id)->where('character_id', $character->character_id)->delete();
        StructureState::where('structure_id', $cs->structure_id)->where('character_id', $character->character_id)->delete();
        StructureVul::where('structure_id', $cs->structure_id)->where('character_id', $character->character_id)->delete();
			}
		}

		foreach($esi_structures as $strct) {
    	$query = '
    	  SELECT solarSystemName
    	  	FROM mapSolarSystems
    	    WHERE solarSystemID = ?';
			$system_name = \DB::connection('mysql2')->select($query, [$strct->system_id]);

			switch ($strct->type_id) {
				case 35825:
					$type_name = 'Raitaru';
					break;
				case 35826:
					$type_name = 'Azbel';
					break;
				case 35827:
					$type_name = 'Sotiyo';
					break;
				case 35832:
					$type_name = 'Astrahus';
					break;
				case 35833:
					$type_name = 'Fortizar';
					break;
				case 35834:
					$type_name = 'Keepstar';
					break;
				case 40340:
					$type_name = 'Upwell_Palatine_Keepstar';
					break;
				case 35835:
					$type_name = 'Athanor';
					break;
				case 35836:
					$type_name = 'Tatara';
					break;
				default:
					$type_name = 'Unknown';
					break;
			}

			try {
				$unv_url = "/v1/universe/structures/$strct->structure_id/";
				$resp = $client->get($unv_url, $auth_headers);
				$unv = json_decode($resp->getBody());

				//Use for fuel testing
				//$strct->fuel_expires = '2018-02-08T06:0:00Z';
        
				if(isset($strct->fuel_expires)) {
          $fuel_expires_datetime = new \DateTime($strct->fuel_expires);
					$now = new \DateTime();
					$diff = date_diff($now,$fuel_expires_datetime);
				  $fuel_time_left = $diff->d . 'd ' . $diff->h . ':' . $diff->i . ':' . $diff->s;
					$fuel_days_left = $diff->d;
					$fuel_expires = str_replace($tz, " ", $strct->fuel_expires);
				} else {
					$fuel_expires = "n/a";
					$fuel_time_left = null;
					$fuel_days_left = null;
				}

				if(isset($strct->unanchors_at)) {
          $unanchors_at = str_replace($tz, " ", $strct->unanchors_at);
				} else {
					$unanchors_at = "n/a";
				}

    		Structure::updateOrCreate(
    		  ['structure_id' => $strct->structure_id, 'character_id' => $character->character_id],
    		  ['structure_name' => $unv->name,
					 'user_id' => \Auth::id(),
					 'corporation_id' => $character->corporation_id,
					 'type_id' => $strct->type_id,
					 'type_name' => $type_name,
					 'system_id' => $strct->system_id,
           'system_name' => $system_name[0]->solarSystemName,
					 'profile_id' => $strct->profile_id,
					 'fuel_expires' => $fuel_expires,
					 'fuel_time_left' => $fuel_time_left,
					 'fuel_days_left' => $fuel_days_left,
					 'unanchors_at' => $unanchors_at
					]
    		);

    		$current_services = StructureService::select('name')
													->where('structure_id', $strct->structure_id)
													->where('character_id', $character->character_id)
													->get();  

        if(count($current_services) > 0 && isset($strct->services)) {
					//IF we have both current and api services, compare and delete ones that don't exist anymore in API
 					$api_services = array();
					foreach($strct->services as $sr) {
 						array_push($api_services, $sr->name);
					}
					
					foreach($current_services as $cs) {
						if(!in_array($cs->name, $api_services)) {
        			StructureService::where('structure_id', $strct->structure_id)
																->where('name', $cs->name)
																->where('character_id', $character->character_id)
																->delete();
						}
					}
				} elseif(count($current_services) > 0 && !isset($strct->services)) {
						//IF no services are returned, delete them all
						StructureService::where('structure_id', $strct->structure_id)
															->where('character_id', $character->character_id)
															->delete();

				}

        if(isset($strct->services)) {
					foreach($strct->services as $sr) {
						StructureService::updateOrCreate(
							['structure_id' => $strct->structure_id, 'character_id' => $character->character_id],
							['state' => $sr->state,
							 'name' => $sr->name]
						);
					}
				}
				
				$state_timer_start = str_replace($tz, " ", $strct->state_timer_start);
				$state_timer_end = str_replace($tz, " ", $strct->state_timer_end);
				StructureState::updateOrCreate(
					['structure_id' => $strct->structure_id, 'character_id' => $character->character_id],
					['state_timer_start' => $state_timer_start,
					 'state_timer_end' => $state_timer_end]
				);

			} catch (ServerException $e ) {
    	  $alert = "We received a 5xx error from ESI, this usually means an issue on CCP's end, please try again later.";
    	  //5xx error, usually and issue with ESI
    	  return redirect()->to('/home')->with('alert', [$alert]);
    	} catch (\Exception $e) {
    	  //Everything else
				dd($e);
    	  $alert = "We failed to pull the Structure name from ESI, Try again later.";
    	  return redirect()->to('/home')->with('alert', [$alert]);
    	}

		}	

			$new_fetch = new \DateTime();
			Character::where('user_id', \Auth::id())->where('character_id', $character_id)->update(['last_fetch' => $new_fetch]);
			$success = "Successfully added/updated structures.";
      return redirect()->to('/home')->with('success', [$success]);
	}

}
