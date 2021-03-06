<?php

namespace App\Api;

use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Response;

class ApiResponse
{
    protected $statusCode = HttpResponse::HTTP_OK;
    protected $status = 'success';

    /**
     * @param int $statusCode
     * @return $this
     */
    public function setStatusCode(int $statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * @param string $status
     * @return $this
     */
    public function setStatus(string $status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @param array|string $data
     * @param array $headers
     * @return \Illuminate\Http\JsonResponse
     */
    public function respond($data, $headers = [])
    {
        return Response::json($data, $this->statusCode, $headers);
    }

    /**
     * @param string $status
     * @param array $data
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function status($status, array $data, $code = null)
    {
        $this->setStatus($status);

        if (!is_null($code)) {
            $this->setStatusCode($code);
        }

        $response_data = [
            'status' => $this->status,
            'code' => $this->statusCode,
            'data' => $data,
        ];

        return $this->respond($response_data);
    }

    /**
     * @param string $message
     * @param string $status
     * @return \Illuminate\Http\JsonResponse
     */
    public function message($message, $status = "success")
    {
        return $this->status($status, ['message' => $message]);
    }

    /**
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function success(array $data)
    {
        return $this->status("success", $data);
    }

    /**
     * failed message
     *
     * @param string $message
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function failed($message, $code = HttpResponse::HTTP_BAD_REQUEST)
    {
        return $this->setStatusCode($code)
            ->message($message, 'error');
    }

    /**
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function created($message = "created")
    {
        return $this->setStatusCode(HttpResponse::HTTP_CREATED)
            ->message($message);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function noContent()
    {
        return $this->setStatusCode(HttpResponse::HTTP_NO_CONTENT)
            ->respond(null);
    }

    /**
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function internalError($message = "500 Internal Error!")
    {
        return $this->failed($message, HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function notFound($message = '404 Not Found!')
    {
        return $this->failed($message, HttpResponse::HTTP_NOT_FOUND);
    }

    /**
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function tooLarge($message = 'Request Entity Too Large.')
    {
        return $this->failed($message, HttpResponse::HTTP_REQUEST_ENTITY_TOO_LARGE);
    }

    /**
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function forbidden($message = '403 Forbidden!')
    {
        return $this->failed($message, HttpResponse::HTTP_FORBIDDEN);
    }

    /**
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function unauthorized($message = '401 Unauthorized!')
    {
        return $this->failed($message, HttpResponse::HTTP_UNAUTHORIZED);
    }
}
