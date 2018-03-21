<?php

namespace App\Traits;

use GuzzleHttp\Promise;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use App\Extractions;
use Log;
use App\Structure;
use App\StructureVul;
use App\StructureState;
use App\StructureService;

trait StructureGet {

  public function getStructures($character) {

    $alert = new \stdClass();
    $tz = array("T", "Z");
    $client = new Client(['base_uri' => 'https://esi.tech.ccp.is/']);

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
      //5xx error, usually and issue with ESI
      Log::error("ServerException thrown on Role fetch: " . $e->getMessage());
      $msg = "We received a 5xx error from ESI, this usually means an issue on CCP's end, please try again later.";
      $alert->{'exception'} = $msg;
      return $alert;
    } catch (\Exception $e) {
      //Everything else
      Log::error("Exception thrown on Role fetch: " . $e->getMessage());
      $msg = "We failed to pull your rolls, please try again later.";
      $alert->{'exception'} = $msg;
      return $alert;
    }

    if(!in_array("Station_Manager", $roles->roles)) {
      $msg = "Character $character->character_name doesn't have the Station Manager Role, this is required to pull Corporation Structures. Once added please wait at least 60 minutes before trying again.";
      $character->is_manager = FALSE;
      $character->save();
      $alert->{'exception'} = $msg;
      return $alert;

      return redirect()->to('/home')->with('alert', [$alert]);
    } else {
      $character->is_manager = TRUE;
      $character->save();

    }

    try {
      $structure_url = "/v2/corporations/$character->corporation_id/structures/";
      $resp = $client->get($structure_url, $auth_headers);
      $esi_structures = json_decode($resp->getBody());
    } catch (ServerException $e ) {
      Log::error("ServerException thrown on Role fetch: " . $e->getMessage());
      $msg = "We received a 5xx error from ESI, this usually means an issue on CCP's end, please try again later.";
      //5xx error, usually and issue with ESI
      $alert->{'exception'} = $msg;
      return $alert;
    } catch (\Exception $e) {
      //Everything else
      Log::error("Exception thrown on Role fetch: " . $e->getMessage());
      $msg = "We failed to pull your structures, please try again later.";
      $alert->{'exception'} = $msg;
      return $alert;
    }

    $current_structures = Structure::select('structure_id')
                          ->where('corporation_id', $character->corporation_id)
                          ->get();

    $api_structures = array();
    foreach($esi_structures as $s) {
      array_push($api_structures, $s->structure_id);
    }

    //Delete Structures and relations that aren't returned in the API call
    foreach($current_structures as $cs) {
      if(!in_array($cs->structure_id, $api_structures)) {
        Structure::find($cs->structure_id)->services()->delete();
        Structure::find($cs->structure_id)->states()->delete();
        Structure::find($cs->structure_id)->vuls()->delete();
        Structure::find($cs->structure_id)->extractions()->delete();
        Structure::where('structure_id', $cs->structure_id)->delete();
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

        $current_state = Structure::find($strct->structure_id);
        $current_state = isset($current_state->state) ? $current_state->state : null;

        if(!is_null($current_state)) {
          $current_state = str_replace(' ', '_', $current_state);
          if($current_state != $strct->state) {
            \Artisan::call('strct:state', ['structure_id' => $strct->structure_id, 'old_state' => $current_state, 'new_state' => $strct->state]);
          }
        }
        $unv_url = "/v1/universe/structures/$strct->structure_id/";
        $resp = $client->get($unv_url, $auth_headers);
        $unv = json_decode($resp->getBody());

        if(isset($strct->fuel_expires)) {
          $fuel_expires_datetime = new \DateTime($strct->fuel_expires);
          $now = new \DateTime();
          $diff = date_diff($now,$fuel_expires_datetime);
          $fuel_time_left = $diff->days. 'd ' . $diff->h . ':' . $diff->i . ':' . $diff->s;
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

        $state = str_replace("_", " " , $strct->state);

        Structure::updateOrCreate(
          ['structure_id' => $strct->structure_id],
          ['structure_name' => $unv->name,
           'corporation_id' => $character->corporation_id,
           'type_id' => $strct->type_id,
           'type_name' => $type_name,
           'system_id' => $strct->system_id,
           'system_name' => $system_name[0]->solarSystemName,
           'profile_id' => $strct->profile_id,
           'fuel_expires' => $fuel_expires,
           'fuel_time_left' => $fuel_time_left,
           'fuel_days_left' => $fuel_days_left,
           'unanchors_at' => $unanchors_at,
           'state' => $state
          ]
        )->touch();

        $current_services = StructureService::select('name')
                          ->where('structure_id', $strct->structure_id)
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
                                ->delete();
            }
          }
        } elseif(count($current_services) > 0 && !isset($strct->services)) {
            //IF no services are returned, delete them all
            StructureService::where('structure_id', $strct->structure_id)
                              ->delete();

        }

        if(isset($strct->services)) {
          foreach($strct->services as $sr) {
            StructureService::updateOrCreate(
              ['structure_id' => $strct->structure_id, 'name' => $sr->name],
              ['state' => $sr->state]
            );
          }
        }

        $state_timer_start = isset($strct->state_timer_start) ? str_replace($tz, " ", $strct->state_timer_start) : null;
        $state_timer_end = isset($strct->state_timer_end) ? str_replace($tz, " ", $strct->state_timer_end) : null;

        StructureState::updateOrCreate(
          ['structure_id' => $strct->structure_id],
          ['state_timer_start' => $state_timer_start,
           'state_timer_end' => $state_timer_end,
           'state' => $state]
        );

        $days = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
        $next_day = null;
        $next_hour = null;
        $next_apply = null;

        if(isset($strct->next_reinforce_apply)) {
          //Set if they exist
          $next_day = $days[$strct->next_reinforce_weekday];
          $next_hour = $strct->next_reinforce_hour;
          $next_apply = str_replace($tz, " ", $strct->next_reinforce_apply);
        }

        StructureVul::updateOrCreate(
          ['structure_id' => $strct->structure_id],
          ['day' => $days[$strct->reinforce_weekday], 'hour' => $strct->reinforce_hour,
           'next_day' => $next_day, 'next_hour' => $next_hour, 'next_reinforce_apply' => $next_apply]
        );

        if($type_name == 'Tatara' || $type_name == 'Athanor') {
          $this->getExtractions($character);
        }

      } catch (ServerException $e ) {
        //5xx error, usually and issue with ESI
        Log::error("ServerException thrown on Role fetch: " . $e->getMessage());
        $msg = "We received a 5xx error from ESI, this usually means an issue on CCP's end, please try again later.";
        $alert->{'exception'} = $msg;
        return $alert;

      } catch (\Exception $e) {
        //Everything else
        Log::error("Exception thrown on Role fetch: " . $e->getMessage());
        $msg = "We failed to pull the Structure name from ESI, Try again later.";
        $alert->{'exception'} = $msg;
        return $alert;
      }

    }

      $success = "Successfully added/updated structures.";
      return $success;
  }

}

