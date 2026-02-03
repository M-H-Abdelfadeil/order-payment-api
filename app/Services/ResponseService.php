<?php

namespace App\Services;



use Illuminate\Http\Response;

class ResponseService
{
    /**
     * send response success
     * @param array $data
     * @param string $message
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    public static function sendResponseSuccess($data = [], int  $code = 200, $message = null)
    {
        $response = self::responseData(true, $code, $data, $message);
        return response()->json($response, $code);
    }

    /**
     *  Send response error
     * @param array $data
     * @param mixed $message
     * @param mixed $code
     * @return \Illuminate\Http\JsonResponse
     */
    public static function sendResponseError($data, int  $code, $message = null)
    {

        $response = self::responseData(false, $code, $data, $message);
        return response()->json($response, $code);
    }


    public static function  sendBadRequest($message = null)
    {
        return self::sendResponseError(null, Response::HTTP_BAD_REQUEST, $message);
    }


    public static function  sendNotFound($message = null)
    {
        
        return self::sendResponseError(null, Response::HTTP_NOT_FOUND, $message);
    }




    private static function responseData(bool $status, int $status_code, $data, $message)
    {

        $message = $message ? $message : Response::$statusTexts[$status_code];

        return  [
            'status' =>  $status,
            'status_code' => $status_code,
            'data'    => $data,
            'message' =>  $message,
        ];
    }

    /**
     * send response success
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    public static function sendBackResponseSuccess($route)
    {
        $response = self::responseData(true, 200, ['url' => $route], "Success");
        return response()->json($response, 200);
    }
}
