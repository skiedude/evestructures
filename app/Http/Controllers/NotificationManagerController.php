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
    $alert = session()->pull('alert');
    $success = session()->pull('success');
    $warning = session()->pull('warning');
    $alert = $alert[0];
    $success = $success[0];
    $warning = $warning[0];

    $notifications = DB::table('users')
              ->join('characters', 'users.id', '=', 'characters.user_id')
              ->leftJoin('notification_info', 'characters.character_id', '=', 'notification_info.character_id')
              ->where('users.id',  auth()->id())
              ->where('characters.is_manager', TRUE)
              ->select('characters.character_name', 'characters.character_id as char_id', 'notification_info.*')
              ->get();

    return view('notification_manager', compact(['notifications','alert','warning','success']));
  }
}
