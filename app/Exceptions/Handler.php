<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Support\Facades\Log;
use App\Helpers\Helper;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
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
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Throwable
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        $logInfo = $this->_getLogInfo($request, $exception);
        Log::channel('slack')->critical(':laravel: *Back-end* : ' . $exception->getMessage(), $logInfo);
        return parent::render($request, $exception);
    }



    /**
     * Get Log info
     *
     * @param $request
     * @param Throwable $exception
     * @return array
     * @auhtor Hem
     */
    private function _getLogInfo($request, Throwable $exception): array
    {
        $systemAccessType = $request->get('api_access_type');
        $requestData = (!empty($request->all()) ? $request->except(['__tReqData', 'api_access_type']) : '');
        $loginInfo = [];
        if ($systemAccessType === 'internal') {
            $loginInfo = [
                'loginInfo' => collect(c_auth())->except('random_string', 'token_created_on')->toArray()
            ];
        }
        return array_merge(
            $loginInfo,
            [
                'request' => array_merge(
                    [
                        'from' => [
                            'userAgent' => Helper::getUserAgent(),
                            'ip' => Helper::getClientIp()
                        ]
                    ],
                    [
                        'systemAccessType' => $request->get('api_access_type'),
                        'url' => $request->url(),
                        'data' => $requestData
                    ]
                ),
                'exception' => [
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'code' => $exception->getCode(),
                    'trace' => $exception->getTraceAsString()
                ]
            ]
        );
    }
}
