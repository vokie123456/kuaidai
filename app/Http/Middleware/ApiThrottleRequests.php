<?php

namespace App\Http\Middleware;

use App\Components\ApiResponse;
use App\Components\ErrorCode;
use Illuminate\Routing\Middleware\ThrottleRequests;

class ApiThrottleRequests extends ThrottleRequests
{

    /**
     * Create a 'too many attempts' response.
     *
     * @param  string  $key
     * @param  int  $maxAttempts
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function buildResponse($key, $maxAttempts)
    {
        $response = ApiResponse::buildFromArray(ErrorCode::ERR_THROTTLE);

        $retryAfter = $this->limiter->availableIn($key);

        return $this->addHeaders(
            $response, $maxAttempts,
            $this->calculateRemainingAttempts($key, $maxAttempts, $retryAfter),
            $retryAfter
        );
    }

}