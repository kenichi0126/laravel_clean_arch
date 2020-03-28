<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Foundation\Http\Exceptions\MaintenanceModeException;
use RuntimeException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Smart2\Application\Exceptions\BusinessException::class,
        \Switchm\SmartApi\Components\Common\Exceptions\BusinessException::class,
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Exception $exception
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof \Smart2\Application\Exceptions\BusinessException || $exception instanceof \Switchm\SmartApi\Components\Common\Exceptions\BusinessException) {
            return response([
                'title' => ' ',
                'message' => $exception->getMessage(),
            ], 422);
        } elseif ($exception instanceof MaintenanceModeException) {
            return $this->convertMaintenanceModeExceptionToResponse($exception, $request);
        }

        return parent::render($request, $exception);
    }

    /**
     * Convert an authentication exception into a response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Auth\AuthenticationException $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        $requestUri = urldecode($request->getRequestUri());

        $redirectPath = '/login';

        if (strpos($requestUri, '/smartplus/download') !== false) {
            // jsで利用するのでrawでエンコード
            $encodedUrl = rawurlencode($requestUri);
            $redirectPath .= "?redirect={$encodedUrl}";
        }

        return $request->expectsJson()
            ? response()->json(['message' => $exception->getMessage()], 401)
            : redirect($redirectPath);
    }

    /**
     * Convert a MaintenanceMode exception into a response.
     *
     * @param \RuntimeException $e
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    private function convertMaintenanceModeExceptionToResponse(RuntimeException $e, $request)
    {
        $message = ($e instanceof MaintenanceModeException) ? $e->getMessage() : '';
        $isPartially = ($e instanceof MaintenanceModeException) ? $e->getIsPartially() : false;

        return $request->expectsJson()
            ? response()->json(['message' => $message, 'isPartially' => $isPartially], 503)
            : response($message, 503);
    }
}
