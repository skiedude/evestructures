<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\CharacterController;
use App\Character;
use App\User;
use App\Structure;
use App\StructureService;
use App\StructureState;
use App\StructureVul;
use App\Extractions;
use App\ExtractionData;
use App\Traits\StructureGet;
use App\Traits\MoonExtractions;
use App\Traits\Tokens;

class StructureController extends Controller
{
  use StructureGet, MoonExtractions, Tokens;

  public function __construct() {
      $this->middleware('auth');
  }

  public function show($structure_id) {
    $characters = User::find(auth()->id())->characters;
    $corp_ids = array();
    foreach ($characters as $char) {
      if($char->is_manager == FALSE) {
        continue;
      }
      array_push($corp_ids, $char->corporation_id);
    }

    $structure = Structure::whereIn('corporation_id', $corp_ids )->where('structure_id', $structure_id)->first();
    if(is_null($structure)) {
      $alert = "Structure not found on this account";
      return redirect()->to('/home')->with('alert', [$alert]);
    }
    $services = StructureService::where('structure_id', $structure_id)->get();
    $state = StructureState::where('structure_id', $structure_id)->first();
    $vul = StructureVul::where('structure_id', $structure_id)->first();
    $extraction = Extractions::where('structure_id', $structure_id)->first();
    $extraction_data = ExtractionData::where('structure_id', $structure_id)->first();

    return view('structure', compact(['structure', 'services', 'state', 'vul', 'extraction', 'extraction_data']));
  }

  public function create($character_id) {

    $character = Character::where('user_id', \Auth::id())->where('character_id', $character_id)->first();
    if(is_null($character)) {
      $alert = "Character not found on this account";
      return redirect()->to('/home')->with('alert', [$alert]);
    }

    if(isset($character->last_fetch)){
      $now = new \DateTime();
      $last_fetch = new \DateTime($character->last_fetch);
      $diff = date_diff($now, $last_fetch);
      if($diff->h < 1) {
        $new_min = (60 - $diff->i);
        $warning = "CCP caches structure data for up to 1 hour. Please try again in $new_min minute(s)";
        return redirect()->to('/home')->with('warning', [$warning]);
      }
    }

    $refresh = $this->refreshToken($character->character_name);

    switch ($refresh) {
      case "not_expired":
        //Good to go
        break;

      case "refreshed":
        //Pull down new info from DB
        $character = Character::where('user_id', \Auth::id())->where('character_id', $character_id)->first();
        break;

      case "failure limit reached":
        //Must reauth
        $alert = "We have failed to refresh your tokens too many times, no longer trying. Please reauth.";
        return redirect()->to('/home')->with('alert', [$alert]);
        break;

      case "5xx":
        Log::error("ServerException caught in token refresh: " . $e->getMessage());
        $alert = "We received a 5xx error from ESI, this usually means an issue on CCP's end, please try again later.";
        //5xx error, usually and issue with ESI
        return redirect()->to('/home')->with('alert', [$alert]);
        break;

      case "failed":
        Log::error("Exception caught in token refresh: " . $e->getMessage());
        $alert = "We failed to refresh your tokens, please try again later.";
        return redirect()->to('/home')->with('alert', [$alert]);
        break;

      default:
        break;
    }

    $update = $this->getStructures($character);

    if(isset($update->exception)) {
      $alert = $update->exception;
      return redirect()->to('/home')->with('alert', [$alert]);
    }

    $new_fetch = new \DateTime();
    Character::where('user_id', \Auth::id())->where('character_id', $character_id)->update(['last_fetch' => $new_fetch]);
    return redirect()->to('/home')->with('success', [$update]);
  }

  public function updateExtraction($structure_id, Request $request) {
    $characters = User::find(auth()->id())->characters;
    $corp_ids = array();
    foreach ($characters as $char) {
      if($char->is_manager == FALSE) {
        continue;
      }
      array_push($corp_ids, $char->corporation_id);
    }

    $structure = Structure::whereIn('corporation_id', $corp_ids )->where('structure_id', $structure_id)->first();
    if(is_null($structure)) {
      $alert = "Structure not found on this account";
      return redirect()->to('/home')->with('alert', [$alert]);
    }
  
    $this->validate($request, [
      'fractureRadio' => array('required', 'regex:/^(manual_fracture|auto_fracture)$/'),
      'ore_value' => 'required|regex:/^[0-9]+$/',
      'ores' => 'required|regex:/^[a-zA-Z,\s]+$/',
    ]);

    ExtractionData::updateOrCreate(['structure_id' => $structure->structure_id],
      ['value' => $request->ore_value,
      'ores' => $request->ores,
      'fracture_pref' => $request->fractureRadio]
    );

    return redirect()->to("/home/structure/$structure_id");
  }
}
