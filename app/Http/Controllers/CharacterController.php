<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use App\Character;
use App\NotificationManager;
use Log;

class CharacterController extends Controller
{

  public function __construct() {
        $this->middleware('auth');
  }

  public static function destroy($character_id, $account_del = null) {
    $character = Character::where('user_id', \Auth::id())->where('character_id', $character_id)->first();
    if(is_null($character)) {
      $alert = "Character not found on this account";
      return redirect()->to('/home')->with('alert', [$alert]);
    }
    try {
      Character::where('character_id', $character->character_id)->delete();
  
      $client = new Client();
      $authsite = 'https://login.eveonline.com/oauth/revoke';
      $tokens = array($character->refresh_token, $character->access_token);
      foreach($tokens as $tk) {
        $token_headers = [
          'headers' => [
            'Authorization' => 'Basic ' . base64_encode(env('CLIENT_ID') . ':' . env('SECRET_KEY')),
            'User-Agent' => env('USERAGENT'),
            'Content-Type' => 'application/x-www-form-urlencoded',
          ],
          'form_params' => [
            'token' => $tk
          ]
        ];
        $result = $client->post($authsite, $token_headers);
      }

      NotificationManager::where('user_id', \Auth::id())->where('character_id', $character_id)->delete();

      if(!isset($account_del)) {
        $success = "Successfully deleted $character->character_name, structures and revoked ESI privileges";
        return redirect()->to('/home')->with('success', [$success]);
      } else {
        return 1;
      }
    } catch (ClientException $e) {
      //4xx error, usually encountered when token has been revoked on CCP website
      // They already revoked it, so just continue and delete the data
    } catch (ServerException $e ) {
      $alert = "We received a 5xx error from ESI, this usually means an issue on CCP's end, please try again later.";
      //5xx error, usually and issue with ESI
      return redirect()->to('/')->with('alert', [$alert]);
    } catch (\Exception $e) {
      //Everything else
      $alert = "We failed to revoke your tokens, please try again later.";
      return redirect()->to('/')->with('alert', [$alert]);
    }
   
      return redirect()->to('/')->with('alert', [$alert]);
  }

  public function create(Request $request) {
    if( session('auth_state') == $request->state ) {
      $client = new Client();
    
      try {
        $authsite = 'https://login.eveonline.com/oauth/token';
        $token_headers = [
          'headers' => [
            'Authorization' => 'Basic ' . base64_encode(env('CLIENT_ID') . ':' . env('SECRET_KEY')),
            'User-Agent' => env('USERAGENT'),
            'Content-Type' => 'application/x-www-form-urlencoded',
          ],
          'form_params' => [
            'grant_type' => 'authorization_code',
            'code' => $request->code
          ]
        ];

        $resp = $client->post($authsite, $token_headers);
        $tokens = json_decode($resp->getBody());

      } catch (\Exception $e) {
        $alert = "We failed to fetch authentication tokens. ESI may be having problems. Please try again later";
        return redirect()->to('/home')->with('alert', [$alert]);
      }
      
      try { 
        $verify_url='https://esi.tech.ccp.is/verify';
        $verify_headers = [
          'headers' => [
            'Authorization' => 'Bearer ' . $tokens->access_token,
            'User-Agent' => env('USERAGENT'),
            'Content-Type' => 'application/x-www/form-urlencoded'
          ]
        ];

        $resp = $client->get($verify_url, $verify_headers);
        $verify = json_decode($resp->getBody());

      } catch (\Exception $e) {
        $alert = "We failed to verify the tokens we received. ESI may be having problems. Please try again later";
        return redirect()->to('/home')->with('alert', [$alert]);
      }
      
      try {
        $character_url = "https://esi.tech.ccp.is/v4/characters/$verify->CharacterID";
        $noauth_headers = [
          'headers' => [
          'User-Agent' => env('USERAGENT'),
          ],
          'query' => [
            'datasource' => 'tranquility',
          ]
        ];
        $resp = $client->get($character_url, $noauth_headers);
        $character = json_decode($resp->getBody());
        
        $corp_url = "https://esi.tech.ccp.is/v4/corporations/$character->corporation_id";
        $resp = $client->get($corp_url, $noauth_headers);
        $corp = json_decode($resp->getBody());

        Character::updateOrCreate(
          ['character_id' => $verify->CharacterID],
          ['character_name' => static::swapName($verify->CharacterName),
           'user_id' => \Auth::id(),
           'corporation_id' => $character->corporation_id,
           'corporation_name' => $corp->name,
           'access_token' => $tokens->access_token,
           'refresh_token' => $tokens->refresh_token,
           'expires' => ($tokens->expires_in + time())
          ]
        );

      } catch (\Exception $e) {
        $alert = "We failed to fetch the public data for $verify->CharacterName. ESI may be having problems. Please try again later";
        return redirect()->to('/home')->with('alert', [$alert]);
      }
      return redirect()->to('/home'); 

      } else {
      return redirect()->to('/')->with('alert', ['State mismatch, try again']); 
    }

  }

  public static function tokenRefresh($characterID) {
    $entry = Character::where('character_id', $characterID)->first();
    $expires = $entry->expires;
    if(($expires - 120) > time()) {
      //not expired

      return "not_expired";
    }

    $refresh_token = $entry->refresh_token;

    try {
    $client = new Client();
    $authsite = 'https://login.eveonline.com/oauth/token';
    $token_headers = [
      'headers' => [
        'Authorization' => 'Basic ' . base64_encode(env('CLIENT_ID') . ':' . env('SECRET_KEY')),
        'User-Agent' => env('USERAGENT'),
        'Content-Type' => 'application/x-www-form-urlencoded',
      ],
      'form_params' => [
        'grant_type' => 'refresh_token',
        'refresh_token' => $refresh_token
      ]
    ];
    $result = $client->post($authsite, $token_headers);
    $resp = json_decode($result->getBody());
    $expires_new = time() + $resp->expires_in;
    Character::updateOrCreate(
      ['character_id' => $characterID],
      ['access_token' => $resp->access_token,
      'expires' => $expires_new]
    );

    } catch (ClientException $e) {
      //4xx error, usually encountered when token has been revoked on CCP website
      Log::error("ClientException caught in token refresh: " . $e->getMessage());
      $alert = "We failed to refresh our access with your tokens. This usually means they were revoked on the CCP API website. Try re-adding your character.";
      return redirect()->to('/home')->with('alert', [$alert]);
    } catch (ServerException $e ) {
      Log::error("ServerException caught in token refresh: " . $e->getMessage());
      $alert = "We received a 5xx error from ESI, this usually means an issue on CCP's end, pleas try again later.";
      //5xx error, usually and issue with ESI
      return redirect()->to('/home')->with('alert', [$alert]);
    } catch (\Exception $e) {
      //Everything else
      Log::error("Exception caught in token refresh: " . $e->getMessage());
      $alert = "We failed to refresh your tokens, please try again later.";
      return redirect()->to('/home')->with('alert', [$alert]);
    }

    return "refreshed";
  }




  public static function swapName(String $name) {

    if(preg_match("/_/", $name)) {
      $name = str_replace('_',' ', $name);
    } else {
      $name = str_replace(' ', '_', $name);
    }

    return $name;
  }
}

