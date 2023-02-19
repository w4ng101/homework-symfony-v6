<?php
namespace App\Common;

trait Error {

    public function errResponse($params) {
        $fieldName = ucfirst($params['field']);
        $returnMsg = match ($params['code']) {
            51 => [
                'code' => 51,
                'status' =>  false,
                'message' => "{$fieldName} must not be empty!",      
            ],
            404 => [
                'code' => 404,
                'status' =>  false,
                'message' => "{$fieldName} Not Found!",      
            ],
            401 => [
                'code' => 401,
                'status' =>  false,
                'message' => "{$fieldName} Unauthorized!",      
            ],
            409 => [
                'code' => 409,
                'status' =>  false,
                'message' => "{$fieldName} already exist!",      
            ],
        };
        return $returnMsg;
    }

}

class ErrorResponse {
    use Error;
}
