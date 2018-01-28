<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Character;
use App\Structure;
use App\Http\Controllers\CharacterController;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
				//Add Character Link -- DONE
				//Get Characters if they have them -- DONE
					// show Characters Name/Corporation -- DONE
					// Delete Button TODO
			  //Get structures if they have them -- DONE
					// Show Structure Name/Corporation/Location/Fuel Left -- DONE
					//Refresh Button? Limit to cache timer? TODO

 			$alert = session()->pull('alert');
      $success = session()->pull('success');
      $alert = $alert[0];
      $success = $success[0];


			$characters = User::find(auth()->id())->characters;	
			$structures = User::find(auth()->id())->structures;	


      return view('home', compact(['characters', 'structures', 'alert', 'success']));
    }

		public function deleteAccount() {
			$user = User::find(\Auth::id());
			$characters = User::find(auth()->id())->characters;	
			foreach ($characters as $character) {
				CharacterController::destroy($character->character_id, 1);
			}
			\Auth::logout();

			if($user->delete()) {

				$success = "Successfully deleted your acccount, all your characters, structures and revoked all ESI privileges. Come back soon!";
				return redirect()->to('/')->with('success', [$success]);
			} else {
				$alert = "Failed to delete account, contact Brock Khans in game for help.";
				return redirect()->to('/')->with('alert', [$alert]);
			}
		}	
}
