<?php

namespace App\Http\Controllers;

use DB;
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
      $alert = session()->pull('alert');
      $success = session()->pull('success');
      $warning = session()->pull('warning');
      $alert = isset($alert[0]) ? $alert[0] : null;
      $success = isset($success[0]) ? $success[0] : null;
      $warning = isset($warning[0]) ? $warning[0] : null;


      $characters = User::find(auth()->id())->characters; 
      $structures = DB::table('users')
                    ->join('characters', 'users.id', '=', 'characters.user_id')
                    ->join('structures', 'structures.corporation_id', '=', 'characters.corporation_id')
                    ->where('users.id',  auth()->id())
                    ->where('characters.is_manager', TRUE)
                    ->select('structures.*')
                    ->distinct()
                    ->get();
      return view('home', compact(['characters', 'structures', 'alert', 'success', 'warning']));
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
