<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class NotificationManagerController extends Controller
{
  public function __construct() {
    $this->middleware('auth');
  }

  public function index() {
    
    $characters = User::find(auth()->id())->characters;

    return view('notification_manager', compact(['characters']));
  }
}
