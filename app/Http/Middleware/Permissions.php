<?php

namespace App\Http\Middleware;

use App\Components\ApiResponse;
use App\Components\ErrorCode;
use App\Components\Exception;
use App\Services\RbacService;
use Closure;
use View;

class Permissions
{

    /** @var RbacService */
    private $rbacService;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  Closure $next
     * @param  string|null $guard
     * @return mixed
     * @throws Exception
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $this->rbacService = new RbacService($guard);

        $this->shareViewData();

        if (!$this->rbacService->check($request->route())) {
            if ($request->ajax() || $request->wantsJson()) {
                return ApiResponse::buildFromArray(ErrorCode::PERMISSION_DENIED);
            }

            throw Exception::buildFromArray(ErrorCode::PERMISSION_DENIED);
        }

        return $next($request);
    }

    /**
     * 视图数据共享
     */
    private function shareViewData()
    {
        $share = array(
            'menu' => $this->rbacService->menuSBAdmin()
        );

        View::share('share', $share);
    }

}