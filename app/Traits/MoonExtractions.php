<?php

namespace App\Traits;

use GuzzleHttp\Promise;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use App\Extractions;
use App\ExtractionData;
use App\FractureNotice;
use App\NotificationManager;
use App\Notifications\FractureDiscord;
use DB;
use Log;

trait MoonExtractions {

  public function getExtractions($character) {

    $noauth_headers = [
      'headers' => [
        'User-Agent' => env('USERAGENT'),
      ],
      'query' => [
        'datasource' => 'tranquility',
      ]
    ];
    $auth_headers = [
      'headers' => [
        'User-Agent' => env('USERAGENT'),
      ],
      'query' => [
        'datasource' => 'tranquility',
        'token'   => $character->access_token
      ]
    ];

    $client = new Client(['base_uri' => 'https://esi.tech.ccp.is/']);

    $extr_url = "/v1/corporation/$character->corporation_id/mining/extractions/";
    $resp = $client->get($extr_url, $auth_headers);
    $extractions = json_decode($resp->getBody());

    $current_structures = DB::table('extractions')
                  ->leftJoin('structures', 'extractions.structure_id', '=', 'structures.structure_id')
                  ->where('structures.corporation_id', $character->corporation_id)
                  ->select('extractions.structure_id', 'extractions.chunk_arrival_time')
                  ->get();

    $api_structures = array();
    foreach($extractions as $s) {
      array_push($api_structures, $s->structure_id);
    }

    foreach($current_structures as $cs) {
      //Keep extractions that are up to 48hrs old from now
      $now = new \DateTime();
      $now->sub(new \DateInterval('P2D'));
      $arrives = new \DateTime($cs->chunk_arrival_time);
      $diff = date_diff($now, $arrives);

      if(!in_array($cs->structure_id, $api_structures) && $diff->invert == 1) {
        Extractions::find($cs->structure_id)->delete();
        FractureNotice::updateOrCreate(['structure_id' => $cs->structure_id, 'character_id' => $character->character_id],['notice' => FALSE]);
        Log::debug("Deleting Extraction for $cs->structure_id for corporation $character->corporation_name character $character->character_name, it was not returned from ESI or was older than 48hrs");
      }
    }

    $notification = NotificationManager::where('character_id', $character->character_id)->first();
    foreach ($extractions as $ext) {
      ExtractionData::updateOrCreate(['structure_id' => $ext->structure_id]);
      $fracture_notice = FractureNotice::where('structure_id', $ext->structure_id)->where('character_id', $character->character_id)->first(); 
      $fracture_data = ExtractionData::where('structure_id', $ext->structure_id)->first();

      $moon_url = "/v1/universe/moons/{$ext->moon_id}/";
      $moon_resp = $client->get($moon_url, $noauth_headers);
      $moon = json_decode($moon_resp->getBody());

      $ext_start_time = new \DateTime($ext->extraction_start_time);
      $chunk_arr_time = new \DateTime($ext->chunk_arrival_time);
      $nat_decay_time = new \DateTime($ext->natural_decay_time);

      $now = new \DateTime();

      $diff_manual = date_diff($now, $chunk_arr_time);
      $diff_auto = date_diff($now, $nat_decay_time);
      
      if($fracture_data->fracture_pref == 'manual_fracture') {
        if($diff_manual->days == 0 && $diff_manual->h <= 1 && $diff_manual->invert == 1 && !is_null($notification->extraction_webhook)) {
          if(is_null($fracture_notice) || $fracture_notice->notice == FALSE) {
            $notification->notify(new FractureDiscord('manual', $ext, $moon, $fracture_data)); 
            FractureNotice::updateOrCreate(['structure_id' => $ext->structure_id, 'character_id' => $character->character_id],['notice' => TRUE]);
          }
        }
      }

      if($fracture_data->fracture_pref == 'auto_fracture') {
        if($diff_auto->days == 0 && $diff_auto->h <= 1 && $diff_auto->invert == 0 && !is_null($notification->extraction_webhook)) {
          if(is_null($fracture_notice) || $fracture_notice->notice == FALSE) {
            $notification->notify(new FractureDiscord('auto', $ext, $moon, $fracture_data));
            FractureNotice::updateOrCreate(['structure_id' => $ext->structure_id, 'character_id' => $character->character_id],['notice' => TRUE]);
          }
        }
      }

      Extractions::updateOrCreate(
        ['structure_id' => $ext->structure_id, 'moon_id' => $ext->moon_id],
        ['moon_name' => $moon->name,
         'extraction_start_time' => $ext_start_time,
         'chunk_arrival_time' => $chunk_arr_time,
         'natural_decay_time' => $nat_decay_time ]
      );
    }
  }

}

