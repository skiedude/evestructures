<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\NotificationManager;
use App\Character;
use DB;

class NotificationManagerController extends Controller
{
  public function __construct() {
    $this->middleware('auth');
  }

  public function index() {
    $alert = session()->pull('alert');
    $success = session()->pull('success');
    $warning = session()->pull('warning');
    $alert = $alert[0];
    $success = $success[0];
    $warning = $warning[0];


    $characters = DB::table('characters')->where('user_id', auth()->id())->where('is_manager', TRUE)->select('character_id')->get();
    foreach($characters as $char) {
      #Delete entries for a character that was on a previous account that has been moved
      $old = NotificationManager::where('user_id', '<>', auth()->id())->where('character_id', $char->character_id)->delete();

      #If a character doesn't exit here, but should, create it
      $find = NotificationManager::where('user_id', auth()->id())->where('character_id', $char->character_id)->get();
      if(count($find) < 1) {
        $new = new NotificationManager;
        $new->character_id = $char->character_id;
        $new->user_id = auth()->id();
        $new->save();
      };
    }

    $notifications = DB::table('characters')
              ->join('notification_info', 'characters.character_id', '=', 'notification_info.character_id')
              ->where('characters.user_id',  auth()->id())
              ->where('characters.is_manager', TRUE)
              ->select('characters.character_name', 'characters.character_id as char_id', 'notification_info.*')
              ->get();

    return view('notification_manager', compact(['notifications','alert','warning','success']));
  }
}
