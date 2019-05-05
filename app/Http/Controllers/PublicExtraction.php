<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Slug;
use App\Character;
use App\Structure;
use App\ExtractionData;
use Log;

class PublicExtraction extends Controller
{
    public function index($corporation_id, $slug) {
      $slug = Slug::where('slug_name', $slug)->where('corporation_id', $corporation_id)->first();
      if(is_null($slug) || $slug->enabled == FALSE) {
        $alert = "This Public Extraction page has been removed, disabled or doesn't exist";
        return redirect()->to("/")->with('alert', [$alert]);
      }

      $character = Character::find($slug->character_id)->where('corporation_id', $corporation_id)->first();
      if(is_null($character)) {
        $alert = "This Public Extraction page has been removed, disabled or doesn't exist";
        return redirect()->to("/")->with('alert', [$alert]);
      }

      $extractions = array();
      $structures = Character::find($slug->character_id)->structures;
      foreach($structures as $structure) {
        if($structure->corporation_id != $corporation_id) {
          continue;
        }
        $structure_name = Structure::find($structure->structure_id);
        $structure_name = $structure_name->structure_name;
        $extraction = Structure::find($structure->structure_id)->extractions;
        $data = ExtractionData::where('structure_id', $structure->structure_id)->first();
        if(!is_null($extraction)) {
          if(!is_null($data)) {
            $extraction->value = $data->value;
            $extraction->ores = $data->ores;
            $extraction->fracture_pref = $data->fracture_pref;
          } else {
            $extraction->value = 0;
            $extraction->ores = "None";
            $extraction->fracture_pref = "auto_fracture";
          }
          $extraction->structure_name = $structure_name;
          array_push($extractions, $extraction);
        }
      }

      usort($extractions, function($a,$b) {
        $ad = new \DateTime($a->chunk_arrival_time);
        $bd = new \DateTime($b->chunk_arrival_time);

        if($ad == $bd) {
          return 0;
        }

        return $ad < $bd ? -1 : 1;
      });

      return view('public_extractions', compact(['extractions', 'character']));
 
    }

}
