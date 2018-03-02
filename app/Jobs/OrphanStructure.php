<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Log;
use App\Structure;

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
          Log::debug("Deleting $structure->structure_name and attached services, states, vuls and extractions , no owners found");
          Structure::find($structure->structure_id)->services()->delete();
          Structure::find($structure->structure_id)->states()->delete();
          Structure::find($structure->structure_id)->vuls()->delete();
          Structure::find($structure->structure_id)->extractions()->delete();
          $structure->delete();
        }
      }
    }
}
