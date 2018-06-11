<?php

namespace App\Traits;

use App\Character;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Client;
use Log;

trait Tokens {
  
  public function refreshToken($characterName) {
    $character = Character::where('character_name',$characterName)->first();
    if(($character->expires - 120) > time()) {
      Log::notice("Token for $characterName was not expired");
      return "not_expired";
    }

    if($character->token_failures >= 5) {
      return "failure limit reached";
    }

    try {
      $client = new Client();
      $authsite = 'https://login.eveonline.com/oauth/token/';
      $token_headers = [
        'headers' => [
          'Authorization' => 'Basic ' . base64_encode(env('CLIENT_ID') . ':' . env('SECRET_KEY')),
          'User-Agent' => env('USERAGENT'),
          'Content-Type' => 'application/x-www-form-urlencoded',
        ],
        'form_params' => [
          'grant_type' => 'refresh_token',
          'refresh_token' => $character->refresh_token
        ]
      ];

      $result = $client->post($authsite, $token_headers);
      $resp = json_decode($result->getBody());
      $expires_new = time() + $resp->expires_in;
      Character::updateOrCreate(
        ['character_id' => $character->character_id],
        ['access_token' => $resp->access_token,
         'expires' => $expires_new,
         'token_failures' => 0]
      );
    } catch (ClientException $e) {
      //4xx error, usually encountered when token has been revoked on CCP website
      $character->increment('token_failures');
      Log::Error("Token refresh failed (4xx) for $characterName: \n" . $e->getMessage());
      return "revoked";
    } catch (ServerException $e ) {
      //5xx error, usually and issue with ESI
      Log::Error("Token refresh failed (5xx) for $characterName: \n" . $e->getMessage());
      return "5xx";
    } catch (\Exception $e) {
      //Everything else
      Log::Error("Token refresh failed (?xx) for $characterName: \n" . $e->getMessage());
      return "failed";
    }
    return "refreshed";
  }
}

