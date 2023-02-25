<?php

/**
 * Helper function collections
 *
 *
 * @date       18/02/203
 * @time       07:05 PM
 * @username   PC
 * @author     Hem
 * @package  App\Helpers
 */

namespace App\Helpers;

use DB;
use Throwable;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Str;
use BadMethodCallException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
/**
 * Class Helper
 *
 * @package App\Helpers
 */
class Helper
{
    

    /**
     * Check is keys are present in an array dataset
     *
     * @param array $keys key which we want to check against array (eg.['pharmacy_id'])
     * @param array $arr dataset
     *
     * @return bool
     * @author Hem
     */
    public static function isArrayKeysExists(array $keys, array $arr)
    {
        return count(array_intersect_key(array_flip($keys), $arr)) === count($keys);
    }

    /**
     * Success json response method.
     *
     * @param string $message
     * @param array $result
     * @param array $headers
     *
     * @return JsonResponse
     * @author Hem
     */
    public static function sendSuccessResponse($message, $result = [], $headers = [])
    {
        $response = [
            'success' => true,
            'message' => $message
        ];
        if (!empty($result)) {
            $response['data'] = $result;
        }
        return response()->json($response, 200, $headers);
    }

    /**
     * Error json response method.
     *
     * @return JsonResponse
     * @author Hem
     */
    public static function sendErrorResponse(int $code, string $error, $data = [])
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];
        if (!empty($data)) {
            $response['data'] = $data;
        }
        return response()->json($response, $code);
    }

    /**
     * Return sub function call response
     *
     * @param bool $status
     * @param string $message message
     * @param array $data dataset
     *
     * @return array
     * @author Hem
     */
    public static function returnSubFunctionCallResponse(
        bool   $status,
        string $message = null,
               $data = null
    ): array
    {
        $response = array('status' => $status, 'message' => $message);
        if (!is_null($data)) {
            $response['data'] = $data;
        }
        return $response;
    }

    /**
     * Get HTTP status message
     *
     * @param number $code
     *
     * @return string
     * @author Hem
     */
    public static function getStatusMessage($code): string
    {
        $status = array(
            100 => 'Continue',
            101 => 'Switching Protocols',
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            306 => '(Unused)',
            307 => 'Temporary Redirect',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            422 => 'Unprocessable Entity',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported'
        );
        return isset($status[$code]) ? $status[$code] : $status[500];
    }

    /**
     * Get extra data of common cron
     *
     * @return array $extraData
     * @author Hem
     */
    public static function getExtraRequestData(): array
    {
        $extraData = [];
        $extraData['REQUEST_TIME'] = Carbon::now()->toDateTimeString();
        $extraData['REMOTE_ADDR'] = self::getClientIp();
        $extraData['AUTH_TOKEN'] = self::getReceivedToken();
        $extraData['USER_AGENT'] = self::getUserAgent();
        return $extraData;
    }

    /**
     * Get client ip address
     *
     * @return string
     * @author Hem
     */
    public static function getClientIp(): string
    {
        $ipAddress = 'UNKNOWN';
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ipAddress = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED'])) {
            $ipAddress = $_SERVER['HTTP_X_FORWARDED'];
        } elseif (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
            $ipAddress = $_SERVER['HTTP_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_FORWARDED'])) {
            $ipAddress = $_SERVER['HTTP_FORWARDED'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ipAddress = $_SERVER['REMOTE_ADDR'];
        }
        return $ipAddress;
    }


    /**
     * Get user agent by using $_SERVER['HTTP_USER_AGENT']
     *
     * @return string
     * @author Hem
     */
    public static function getUserAgent(): string
    {
        return Request::header('user-agent') ?? '';
    }

    /**
     * Exception data for end user and developer
     *
     * @param Mixed $forUser Display info to user
     * @param Mixed $forDev Display info to developer
     *
     * @return array
     * @author Hem
     */
    public static function exceptionData($forUser = '', $forDev = '')
    {
        return [
            'end_user' => $forUser,
            'dev' => $forDev,
        ];
    }

    /**
     * Check is multidimensional array
     *
     * @param array $inputArray
     * @return bool
     * @author Hem
     */
    public static function isMultiDimensionalArray(array $inputArray): bool
    {
        return count(
                array_filter(
                    $inputArray,
                    'is_array'
                )
            ) > 0;
    }


    /**
     * Calculate percentage of two numbers
     *
     * @param float $calculateFromNumber Calculate percentage from number
     * @param float $calculateOfNumber Calculate percentage of number
     * @param int $decimalPoints No of decimal points
     * @param bool $addPercentageSign Check percentage sign required
     *
     * @return string
     * @author Hem
     */
    public static function calculatePercentageOfTwoNumbers(
        float $calculateFromNumber,
        float $calculateOfNumber,
        int   $decimalPoints = 2,
        bool  $addPercentageSign = false
    )
    {
        $calPercentage = number_format(
            ($calculateOfNumber / $calculateFromNumber) * 100,
            $decimalPoints
        );
        $percentage = $calPercentage > 0 ? $calPercentage : 0;
        return $addPercentageSign ? $percentage . '%' : $percentage;
    }

    /**
     * Show big no into short format
     *
     * Use to convert large positive numbers in to short form
     * like 1K+, 100K+, 199K+, 1M+, 10M+, 1B+ etc
     *
     * @param float $n
     *
     * @return int|string
     * @author Hem
     */
    public static function numberFormatShort($n): string
    {
        if ($n >= 0 && $n < 1000) {
            // 1 - 999
            $nFormat = floor($n);
            $suffix = '';
        } elseif ($n >= 1000 && $n < 1000000) {
            // 1k-999k
            $nFormat = floor($n / 1000);
            $suffix = 'K+';
        } elseif ($n >= 1000000 && $n < 1000000000) {
            // 1m-999m
            $nFormat = floor($n / 1000000);
            $suffix = 'M+';
        } elseif ($n >= 1000000000 && $n < 1000000000000) {
            // 1b-999b
            $nFormat = floor($n / 1000000000);
            $suffix = 'B+';
        } elseif ($n >= 1000000000000) {
            // 1t+
            $nFormat = floor($n / 1000000000000);
            $suffix = 'T+';
        }
        return !empty($nFormat . $suffix) ? $nFormat . $suffix : 0;
    }

    /**
     * Change random sequence of array
     *
     * @param array $inputArray
     *
     * @return array
     * @author Hem
     */
    public static function changeRandomSequenceOfArray(array $inputArray): array
    {
        uksort(
            $inputArray,
            function () {
                return rand() > rand();
            }
        );
        return $inputArray;
    }


    /**
     * Clean string remove special characters
     *
     * @param string $string
     *
     * @return string
     * @author Hem
     */
    public function cleanString(string $string): string
    {
        $string = str_replace(' ', '_', $string);
        $string = preg_replace('/[^A-Za-z0-9._]/', '', $string);
        return preg_replace('/_+/', '_', $string);
    }

    /**
     * Get current time (microsecond) in unix format
     *
     * @return (string) The rounded value
     * @author Hem
     */
    public function getMicroTime()
    {
        return round(microtime(true) * 1000);
    }

    /**
     * Mask string
     *
     * @param string $string
     * @return string
     * @author Hem
     */
    public static function maskString(string $string): string
    {
        return str_repeat('#', strlen($string));
    }

    /**
     * Get No record found table row
     *
     * @param int $colSpan row colspan
     * @param string $trClasses row classes
     * @param string $tdMessage row classes
     * @return string
     * @author Hem
     */
    public static function getNoRecordFoundTableRow(int $colSpan = 2, string $trClasses = 'tr_no_data_found', string $tdMessage = 'No Data Available'): string
    {
        return "<tr class='{$trClasses}'>
                    <td colspan='{$colSpan}'>
                        <center>{$tdMessage}</center>
                    </td>
                </tr>";
    }

    /**
     * Convert date format
     *
     * @param string $inputDate
     * @param string $outputFormat
     * @param string $inputFormat
     * @return string|bool
     * @author Hem
     */
    public static function convertDateFormat(string $inputDate, string $outputFormat = 'd/m/Y', string $inputFormat = 'Y-m-d')
    {
        $dt = \DateTime::createFromFormat($inputFormat, $inputDate);
        if ($dt !== false && !array_sum($dt::getLastErrors())) {
            return $dt->format($outputFormat);
        }
        return false;
    }

    /**
     * Add day / hour in date time
     *
     * @param string $dateTime
     * @param int $increaseBy
     * @param string $unit
     * @return string|bool
     * @author Hem
     */
    public static function addDayHourInDateTime(string $dateTime, int $increaseBy, string $unit): string
    {
        if ($unit === 'hours') {
            return Carbon::parse($dateTime)->addHours($increaseBy)->format('Y-m-d H:i:s');
        }
        return Carbon::parse($dateTime)->addDays($increaseBy)->format('Y-m-d H:i:s');
    }
}
