<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Log;
use App\Structure;
use App\StructureService;
use App\StructureState;
use App\StructureVul;

class OrphanStructure implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      $structures = Structure::all();
      foreach ($structures as $structure) {
        $characters = Structure::find($structure->structure_id)->characters;
        if(count($characters) < 1) {
          Log::debug("Deleting $structure->structure_name and attached services, states and vuls , no owners found");
          $structure->delete();

          StructureService::where('structure_id', $structure->structure_id)->delete();
          StructureState::where('structure_id', $structure->structure_id)->delete();
          StructureVul::where('structure_id', $structure->structure_id)->delete();

        }
      }
    }
}
