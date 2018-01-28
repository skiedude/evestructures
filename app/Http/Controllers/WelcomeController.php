<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WelcomeController extends Controller
{
	public function index() {
      $alert = session()->pull('alert');
      $success = session()->pull('success');
      $alert = $alert[0];
      $success = $success[0];
			return view('welcome', compact(['alert', 'success']));

	}
}
