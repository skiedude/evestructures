<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Character;
use App\NotificationManager;

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
      
    $this->validate($request, [
      'discord_webhook' => 'required|max:225|regex:/(^https:\/\/(canary.)?discordapp.com\/api\/webhooks.*)/'  
    ]);
    
    $low_fuel = isset($request->low_fuel) ? TRUE : FALSE;
    $strct_state = isset($request->strct_state) ? TRUE : FALSE;
    $extractions = isset($request->extractions) ? TRUE : FALSE;
    $unanchor = isset($request->unanchor) ? TRUE : FALSE;

    NotificationManager::updateOrCreate(
      ['user_id' => \Auth::id(), 'character_id' => $character->character_id],
      ['discord_webhook' => $request->discord_webhook,
       'low_fuel' => $low_fuel,
       'strct_state' => $strct_state,
       'extractions' => $extractions,
       'unanchor' => $unanchor]
    );

    $success = "Successfully added/updated your Discord Webhook for $character->character_name";
    return redirect()->to('/home/notifications')->with('success', [$success]);
  }

  public function destroy($character_id, $request=null) {
    $character = Character::where('user_id', \Auth::id())->where('character_id', $character_id)->first();
      if(is_null($character)) {
        $alert = "Character not found on this account";
        return redirect()->to('/home/notifications')->with('alert', [$alert]);
      }
    
    NotificationManager::where('user_id', \Auth::id())->where('character_id', $character->character_id)->delete();

    $success = "Successfully deleted your Discord Webhook for $character->character_name";
    return redirect()->to('/home/notifications')->with('success', [$success]);
  }

}
