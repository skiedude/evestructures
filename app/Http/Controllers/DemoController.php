<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DemoCharacter;
use App\DemoStructure;
use App\DemoStructureService;
use App\DemoStructureState;

class DemoController extends Controller
{
  public function index() {
    $characters = DemoCharacter::all();
    $structures = DemoStructure::all();

    return view('demo.home', compact(['characters', 'structures']));
  }

  public function show($structure_id) {
    $structure = DemoStructure::where('structure_id', $structure_id)->first();
    $services = DemoStructureService::where('character_id', $structure->character_id)->where('structure_id', $structure_id)->get();
    $state = DemoStructureState::where('structure_id', $structure_id)->get();

    return view('demo.structure', compact(['structure', 'services', 'state']));

  }
}
