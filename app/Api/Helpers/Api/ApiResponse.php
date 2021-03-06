<?php

namespace App\Api\Helpers\Api;

use Symfony\Component\HttpFoundation\Response as FoundationResponse;
use Response;

trait ApiResponse
{
    public function respond($status, $respond)
    {
        return Response::json(['status' => $status, is_string($respond) ? 'message' : 'data' => $respond]);
    }

    public function success($respond = 'success!')
    {
        return $this->respond(true, $respond);
    }

    public function failed($respond = "Request failed!")
    {
        return $this->respond(false, $respond);
    }

    public function error($respond = "error")
    {
        return $this->respond(false, $respond);
    }


//    protected $statusCode = FoundationResponse::HTTP_OK;
//
//    public function getStatusCode()
//    {
//        return $this->statusCode;
//    }
//
//    public function setStatusCode( $statusCode )
//    {
//        $this->statusCode = $statusCode;
//        return $this;
//    }
//
//    public function respond( $data, $header = [] )
//    {
//        return Response::json( $data, $this->getStatusCode(), $header);
//    }
//
//    public function status( $status, array $data, $code = null )
//    {
//        if ( $code ) {
//            $this->setStatusCode( $code );
//        }
//
//        $status = [
//            'status' => $status,
//            'code' => $this->statusCode
//        ];
//
//        $data = array_merge( $status, $data );
//        return $this->respond( $data );
//    }
//
//    public function message( $message, $status = 'success' )
//    {
//        return $this->status( $status, [
//            'message' => $message
//        ]);
//    }
//
//    public function internalError( $message = 'Internal Error!')
//    {
//        return $this->setStatusCode( FoundationResponse::HTTP_INTERNAL_SERVER_ERROR)
//            ->failed($message);
//    }
//
//    public function failed( $message, $code = FoundationResponse::HTTP_BAD_REQUEST, $status = 'error' )
//    {
//        return $this->status( $status, [
//            'message' => $message
//        ], $code);
//    }
//
//    public function created( $message = 'created' )
//    {
//        return $this->setStatusCode( FoundationResponse::HTTP_CREATED )
//            ->message( $message );
//    }
//
//    public function success( $data, $status = 'success' )
//    {
//        return $this->status( $status, compact('data') );
//    }
//
//    public function error( $message )
//    {
//        return $this->message( $message, 'error' );
//    }
//
//
//    public function notFond( $message = 'Not Fond!')
//    {
//        return $this->setStatusCode( FoundationResponse::HTTP_NOT_FOUND )->failed( $message );
//    }
}