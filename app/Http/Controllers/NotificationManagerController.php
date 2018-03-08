<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use DB;

class NotificationManagerController extends Controller
{
  public function __construct() {
    $this->middleware('auth');
  }

  public function index() {
    
    $notifications = DB::table('users')
              ->join('characters', 'users.id', '=', 'characters.user_id')
              ->leftJoin('notification_managers', 'characters.character_id', '=', 'notification_managers.character_id')
              ->where('users.id',  auth()->id())
              ->where('characters.is_manager', TRUE)
              ->select('characters.character_name', 'characters.character_id as char_id', 'notification_managers.*')
              ->get();

    return view('notification_manager', compact(['notifications']));
  }
}
