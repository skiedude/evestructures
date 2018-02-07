<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Character;

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
		
		$character->discord_webhook = $request->discord_webhook;
		$character->save();

		$success = "Successfully added/updated your Discord Webhook for $character->character_name";
		return redirect()->to('/home')->with('success', [$success]);
	}

	public function destroy($character_id, Request $request) {
 		$character = Character::where('user_id', \Auth::id())->where('character_id', $character_id)->first();
    	if(is_null($character)) {
      	$alert = "Character not found on this account";
      	return redirect()->to('/home')->with('alert', [$alert]);
    	}
			
		$character->discord_webhook = null;
		$character->save();

		$success = "Successfully deleted your Discord Webhook for $character->character_name";
		return redirect()->to('/home')->with('success', [$success]);
	}

}
