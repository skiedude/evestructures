<?php

namespace App\Traits;

use GuzzleHttp\Promise;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use App\Extractions;
use Log;

trait MoonExtractions {

  public function getExtractions($corporation_id, $access_token) {

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
        'token'   => $access_token
      ]
    ];

    $client = new Client(['base_uri' => 'https://esi.tech.ccp.is/']);

    $extr_url = "/v1/corporation/$corporation_id/mining/extractions/";
    $resp = $client->get($extr_url, $auth_headers);
    $extractions = json_decode($resp->getBody());

    foreach ($extractions as $ext) {
      $moon_url = "/v1/universe/moons/{$ext->moon_id}/";
      $moon_resp = $client->get($moon_url, $noauth_headers);
      $moon = json_decode($moon_resp->getBody());

      $ext_start_time = new \DateTime($ext->extraction_start_time);
      $chunk_arr_time = new \DateTime($ext->chunk_arrival_time);
      $nat_decay_time = new \DateTime($ext->natural_decay_time);

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

