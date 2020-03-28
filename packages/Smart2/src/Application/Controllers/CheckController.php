<?php

namespace Smart2\Application\Controllers;

use App;
use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Log;

class CheckController extends Controller
{
    public function log(): Response
    {
        Log::debug('checkLog in ' . App::environment());

        return response('A log outputted.');
    }

    public function longResponse(Request $request): Response
    {
        if ($request->ip() !== '118.238.251.137') {
            abort(403, 'Forbidden');
        }

        $seconds = 65;

        sleep($seconds);

        return response("Slept {$seconds} sec.");
    }

    public function connection(): Response
    {
        $this->checkDb();
        return response('All Connection is OK!');
    }

    public function connectionDb(): Response
    {
        $this->checkDb();
        return response('Database connection is OK!');
    }

    private function checkDb(): void
    {
        DB::connection('smart_write_rdb')->select('SELECT 1');
        DB::connection('smart_read_rdb')->select('SELECT 1');
        DB::connection('smart_dwh')->select('SELECT 1');
    }
}
