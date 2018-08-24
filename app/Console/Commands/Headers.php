<?php

namespace App\Console\Commands;
use GuzzleHttp\Promise;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Log;

class Headers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:header';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check what CCP thinks our useragent is, Debugging purposes';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
      $noauth_headers = [
        'headers' => [
          'User-Agent' => env('USERAGENT'),
        ],
      ];

      $client = new Client(['base_uri' => 'https://esi.evetech.net']);
      $resp = $client->get('/headers/', $noauth_headers);
      $body = json_decode($resp->getBody());
      dd($body);
    }

}
