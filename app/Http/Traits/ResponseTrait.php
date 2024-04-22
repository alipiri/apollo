<?php

namespace App\Http\Traits;

use Illuminate\Http\JsonResponse;

trait ResponseTrait
{
    public function successResponse($message,$data=NULL): JsonResponse
    {
        return response()->json(['status'=>'successful','message' => $message,'data'=>$data]);
    }

    public function failedResponse($error_code,$message): JsonResponse
    {
        return response()->json(['status'=>'failed','message' => $message,'error_code'=>$error_code]);
    }
}
