<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Aunthenticate\Integration;

class IntegrationController extends Controller {

    public function getData(Request $request)
    {
        try {
            $client_id = $request->header()['x-cons-id'][0];
            $headerSignature = $request->header()['x-signature'][0];
            $tStamp = $request->header()['x-timestamp'][0];

            $checkAccess = Integration::checking($client_id, $headerSignature, $tStamp);

            if (!$checkAccess) {
                return response([
                    'status'    => 400,
                    'message'   => 'Bad Request.',
                ]);
            } else {
                return response([
                    'status'    => 200,
                    'message'   => 'Success.',
                ]);
            }

         }catch(Exception $e){
            return response([
                'ok'        => false,
                'message'   => $e->getMessage()
            ]);
        }
        
    }
}
