<?php

namespace App\Exceptions\Formatters;

use Illuminate\Http\Request;
use MioNext\Jesponse\Status;
use Symfony\Component\HttpFoundation\Response;

class ServiceUnavailable
{
    /**
     * @param \Exception $exception
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public static function format(\Throwable $throwable, Request $request = null)
    {
        $message = Status::getText($code = Status::ServiceUnavailable);
        $response = fail($message, $code);
        $response->headers->set('X-Response-Desc', 'Code crash');

        return $response
            ->setStatusCode(Response::HTTP_SERVICE_UNAVAILABLE);
    }
}
