<?php

namespace Nwogu\Logeye\Laravel;

use Filebase\Database;
use Illuminate\Http\Request;

class Controller
{
    public static $url = "/logeye/register_logs";

    public function __invoke(Request $request, Database $database)
    {
        $logs = $database->where('status', '=', 'unsent')->results(false);

        $send_data = [];

        foreach ($logs as $log) {

            $send_data[]  = $log->toArray();
            $log->save(['status' => 'sent']);
        }

        $database->where('status', '=', 'sent')->delete();

        return response()->json([
            'logeye_api_key' => config ('logeye.api_key', '1ba7d6e4a71c49354b6a3bd616c1a0547e83e6cf45205b54c8447e0fc377fa92'), 
            'logs'  => $send_data
        ]);
    }
}