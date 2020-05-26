<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WelcomeController extends Controller
{
  public function index() {
      $alert = session()->pull('alert');
      $success = session()->pull('success');
      $alert = isset($alert[0]) ? $alert[0] : null;
      $success = isset($success[0]) ? $success[0] : null;
      return view('welcome', compact(['alert', 'success']));

  }
}
