<?php

namespace App\Http\Aunthenticate;
use Illuminate\Http\Request;
use Carbon\Carbon;

class Integration {

    public static function checking($client_id, $headerSignature, $tStamp) {
        // 1. cheking client_id dari tabel dan get berdasarkan clinet_id ** harus ada MEMBER_KEY

        try {
            // ini proses get ke model
            $model_integration = [
                [
                    'member_id'      => '22222',
                    'member_key'     => '8474894',
                ],
                [
                    'member_id'      => '25944',
                    'member_key'     => '3dQFCF1426',
                ]
            ];
            
            $data = array_filter($model_integration, function($item) use ($client_id) {
                return $item['member_id'] === $client_id;
            });            
            // end
            
            $generateKey = self::generateAuthentication(array_values($data)[0]['member_id'], array_values($data)[0]['member_key'], $tStamp);
    
            if ($generateKey['X-Signature'] === $headerSignature) {
                return true;
            } else {
                return false;
            }

        } catch(Exception $e){
            return response([
                'status'    => 500,
                'message'   => $e->getMessage()
            ], 500);
        }

    }

     /**
     * Generate autentikasi
     *
     * @return array
     */
    protected static function generateAuthentication($memberID, $memberKey, $tStamp): array
    {
        $consumerId = env('MEMBER_ID', $memberID);
        $consumerKey = env('MEMBER_KEY', $memberKey);
        
        // Computes the timestamp
        // $tStamp = Carbon::now('Asia/Jakarta')->timestamp;
        // Computes the signature by hashing the salt with the secret key as the key
        $signature = hash_hmac('sha256', $consumerId.'&'.$tStamp, $consumerKey, true);

        $encodedSignature = base64_encode($signature);

        return [
            'X-Cons-ID'     => $consumerId,
            'X-Timestamp'   => $tStamp,
            'X-Signature'   => $encodedSignature,
        ];
    }
}