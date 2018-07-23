<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Character;
use App\NotificationManager;
use App\Notifications\Discord\testDiscord;
use App\Notifications\Slack\testSlack;

class WebhookController extends Controller
{

  public function __construct() {
    $this->middleware('auth');
  }

  public function store($character_id, Request $request) {
    $character = Character::where('user_id', \Auth::id())->where('character_id', $character_id)->first();
    if(is_null($character)) {
      $alert = "Character not found on this account";
      return redirect()->to('/home')->with('alert', [$alert]);
    }

    #allow both slack/discord URLs
    $validation = array('nullable','max:225','regex:/(^https:\/\/(canary.)?discordapp.com\/api\/webhooks.*)|(^https:\/\/hooks.slack.com\/services\/.*)/');

    if(isset($request->fuel_webhook)) {
      $this->validate($request, [
         'fuel_webhook' => $validation
      ]);
      NotificationManager::updateOrCreate(
        ['user_id' => \Auth::id(), 'character_id' => $character->character_id],
        ['fuel_webhook' => $request->fuel_webhook,]
      );

    } elseif(isset($request->state_webhook)) {
      $this->validate($request, [
        'state_webhook' => $validation,  
      ]);
      NotificationManager::updateOrCreate(
        ['user_id' => \Auth::id(), 'character_id' => $character->character_id],
        ['state_webhook' => $request->state_webhook,]
      );

    } elseif(isset($request->unanchor_webhook)) {
      $this->validate($request, [
        'unanchor_webhook' => $validation,  
      ]);
      NotificationManager::updateOrCreate(
        ['user_id' => \Auth::id(), 'character_id' => $character->character_id],
         ['unanchor_webhook' => $request->unanchor_webhook]
      );

    } elseif(isset($request->extraction_webhook)) {
      $this->validate($request, [
        'extraction_webhook' => $validation,  
      ]);
      NotificationManager::updateOrCreate(
        ['user_id' => \Auth::id(), 'character_id' => $character->character_id],
        ['extraction_webhook' => $request->extraction_webhook,]
      );

    } else {

    }
    
    $success = "Successfully added/updated your Webhook for $character->character_name";
    return redirect()->to('/home/notifications')->with('success', [$success]);
  }

  public function destroy($character_id, $request=null) {
    $character = Character::where('user_id', \Auth::id())->where('character_id', $character_id)->first();
      if(is_null($character)) {
        $alert = "Character not found on this account";
        return redirect()->to('/home/notifications')->with('alert', [$alert]);
      }
    
    NotificationManager::where('user_id', \Auth::id())->where('character_id', $character->character_id)->delete();

    $success = "Successfully deleted your Webhook(s) for $character->character_name";
    return redirect()->to('/home/notifications')->with('success', [$success]);
  }

  public function testNotify($character_id, Request $request) {
    $character = Character::where('user_id', \Auth::id())->where('character_id', $character_id)->first();
    if(is_null($character)) {
      $alert = "Character not found on this account";
      return redirect()->to('/home')->with('alert', [$alert]);
    }

    $notification = NotificationManager::where('user_id', \Auth::id())->where('character_id', $character_id)->first();
    if (!isset($notification->{$request->webhook_test})) {
      $alert = "No $request->webhook_test found";
      return redirect()->to('/home/notifications')->with('alert', [$alert]);
    }

    if(preg_match("/slack/", $notification->{$request->webhook_test})) {
      $notification->slackChannel($request->webhook_test)->notify(new testSlack($character, $request->webhook_test));
    } else {
      $notification->notify(new testDiscord($character, $request->webhook_test));
    }

    $success = "Test Successfully Sent";
    return redirect()->to('/home/notifications')->with('success', [$success]);

  }
}
