<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Slug;
use App\Character;
use App\Structure;
class SlugController extends Controller
{
  public function __construct() {
    $this->middleware('auth');
  }

  public function index() {
    $characters = Character::where('user_id', \Auth::id())->where('is_manager', TRUE)->get();
    $slugs = Slug::where('user_id', \Auth::id())->get();

    return view('slug_manager', compact(['slugs', 'characters']));
 
  }

  public function create($character_id, Request $request) {

    $character = Character::where('user_id', \Auth::id())->where('character_id', $character_id)->first();
    if(is_null($character)) {
      $alert = "Character not found on this account";
      return redirect()->to('/home')->with('alert', [$alert]);
    }
    $this->validate($request, [
     'slug_name' => 'max:25|regex:/^[a-zA-Z0-9]+$/',
     'status' => 'nullable|regex:/^on$/',
    ]);

    $status = isset($request->status) && $request->status == 'on' ? TRUE : FALSE;

    Slug::where('user_id', \Auth::id())->where('character_id', $character_id)
             ->update(['slug_name' => $request->slug_name, 'enabled' => $status]
    );

    return redirect()->to('extraction');
  }

}
