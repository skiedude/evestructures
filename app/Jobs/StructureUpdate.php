<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Character;
use App\Structure;
use App\StructureService;
use App\StructureState;
use App\FuelNotice;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use App\Http\Controllers\CharacterController;
use Log;



class StructureUpdate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

		protected $character;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Character $character)
    {
			$this->character = $character;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
			Log::debug("Starting Structure pull for character {$this->character->character_name}");
			$tz = array("T", "Z");
    	$refresh = CharacterController::tokenRefresh($this->character->character_id);
    	switch ($refresh) {
    	  case "not_expired":
    	    //Good to go
    	    break;

    	  case "refreshed":
    	    //Pull down new info from DB
    	    $this->character = Character::where('user_id', $this->character->user_id)->where('character_id', $this->character->character_id)->first();
					Log::debug("Refreshed token for {$this->character->character_name}");
    	    break;

    	  default:
    	    break;
    	}

			$client = new Client(['base_uri' => 'https://esi.tech.ccp.is/']);
			$auth_headers = [
    	  'headers' => [
    	  'User-Agent' => env('USERAGENT'),
    	  ],
    	  'query' => [
    	    'datasource' => 'tranquility',
    	    'token'   => $this->character->access_token
    	  ]
    	];

			try {
				$roles_url = "/v2/characters/{$this->character->character_id}/roles/";
				$resp = $client->get($roles_url, $auth_headers);
				$roles = json_decode($resp->getBody());

			} catch (ServerException $e ) {
    	  //5xx error, usually and issue with ESI
				Log::error('Caught Server(5xx) Exception in roles endpoint' . $e->getMessage());
				return;
    	} catch (\Exception $e) {
    	  //Everything else
				Log::error('Caught Exception in roles endpoint' . $e->getMessage());
				return;
    	}

    	if(!in_array("Station_Manager", $roles->roles)) {
    	  $alert = "Character {$this->character->character_name} doesn't have the Station Manager Role, this is required to pull Corporation Structures. Once added please wait at least 60 minutes before trying again.";
				Log::error("{$this->character->character_name} does not have the Station Manager role");
				return;
			} 

			try {
				$structure_url = "/v1/corporations/{$this->character->corporation_id}/structures/";
				$resp = $client->get($structure_url, $auth_headers);
				$esi_structures = json_decode($resp->getBody());

			} catch (ServerException $e ) {
    	  //5xx error, usually and issue with ESI
				Log::error('Caught Server(5xx) Exception in structures endpoint' . $e->getMessage());
				return;
    	} catch (\Exception $e) {
    	  //Everything else
				Log::error('Caught Exception in structures endpoint' . $e->getMessage());
				return;
    	}

    	$current_structures = Structure::select('structure_id')
														->where('character_id', $this->character->character_id)
														->get();  

			$api_structures = array();
    	foreach($esi_structures as $s) {
				array_push($api_structures, $s->structure_id);
			}

			//Delete Structures and relations that aren't returned in the API call
    	foreach($current_structures as $cs) {
				if(!in_array($cs->structure_id, $api_structures)) {
					Structure::where('structure_id', $cs->structure_id)->where('character_id', $this->character->character_id)->delete();
    	    StructureService::where('structure_id', $cs->structure_id)->where('character_id', $this->character->character_id)->delete();
    	    StructureState::where('structure_id', $cs->structure_id)->where('character_id', $this->character->character_id)->delete();
    	    StructureVul::where('structure_id', $cs->structure_id)->where('character_id', $this->character->character_id)->delete();
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

					if(isset($strct->fuel_expires)) {
    	      $fuel_expires_datetime = new \DateTime($strct->fuel_expires);
						$now = new \DateTime();
						$diff = date_diff($now,$fuel_expires_datetime);
					  $fuel_time_left = $diff->days . 'd ' . $diff->h . ':' . $diff->i . ':' . $diff->s;
						$fuel_days_left = $diff->days;
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
    			  ['structure_id' => $strct->structure_id, 'character_id' => $this->character->character_id],
    			  ['structure_name' => $unv->name,
						 'user_id' => $this->character->user_id,
						 'corporation_id' => $this->character->corporation_id,
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
														->where('character_id', $this->character->character_id)
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
																	->where('character_id', $this->character->character_id)
																	->delete();
							}
						}
					} elseif(count($current_services) > 0 && !isset($strct->services)) {
							//IF no services are returned, delete them all
							StructureService::where('structure_id', $strct->structure_id)
																->where('character_id', $this->character->character_id)
																->delete();

					}

    	    if(isset($strct->services)) {
						foreach($strct->services as $sr) {
							StructureService::updateOrCreate(
								['structure_id' => $strct->structure_id, 'character_id' => $this->character->character_id],
								['state' => $sr->state,
								 'name' => $sr->name]
							);
						}
					}

					$state_timer_start = str_replace($tz, " ", $strct->state_timer_start);
					$state_timer_end = str_replace($tz, " ", $strct->state_timer_end);
					StructureState::updateOrCreate(
						['structure_id' => $strct->structure_id, 'character_id' => $this->character->character_id],
						['state_timer_start' => $state_timer_start,
						 'state_timer_end' => $state_timer_end]
					);

				} catch (ServerException $e ) {
    		  //5xx error, usually and issue with ESI
				Log::error('Caught Server(5xx) Exception in universe endpoint' . $e->getMessage());
				return;
    		} catch (\Exception $e) {
    		  //Everything else
				Log::error('Caught Exception in universe endpoint' . $e->getMessage());
				return;
    		}

			}	

			Log::debug("Finished Structure pull for character {$this->character->character_name}");
    }
}

