<?php

namespace App\Http\Traits;

trait GeneralTrait {

    public function ResponseJson($statusMessage, $data = null, $error= null) {
            $payload = [
                "status"    => $statusMessage["STATUS"],
                "code"      => $statusMessage["CODE"],
                "message"   => $statusMessage["MESSAGE"],
            ];

            if(!is_null($data)){
                $payload["data"] = $data;
            };
            
            if(!is_null($error)){
                $payload["errors"] = $error;
            };

            return response()->json($payload, $statusMessage["HTTP_CODE"]);
    }

    public function ResponsePaginateJson($statusMessage, $data = [],
        $limit, $next_page, $total_data, $current_page) {
            $payload = [
                "status"    => $statusMessage["STATUS"],
                "code"      => $statusMessage["CODE"],
                "message"   => $statusMessage["MESSAGE"],
                "next_page" => $next_page,
                "current_page"  => $current_page
            ];

            if(count($data) < $limit){
                $payload["next_page"]   = null;
            }

            if(!is_null($total_data)){
                $payload["total_data"] = $total_data;
                $payload["total_page"] = ceil($total_data / $limit);
            };

            if(!is_null($data)){
                $payload["data"] = $data;
            };

            return response()->json($payload, $statusMessage["HTTP_CODE"]);
    }

    public function ResponseJsonError(){
        $data = CONFIG("statusmessage.SERVER_ERROR");
        return response()->json([
            "status"    => $data["STATUS"],
            "code"      => $data["CODE"],
            "message"   => $data["MESSAGE"],
        ], $data["HTTP_CODE"]);
    }

}