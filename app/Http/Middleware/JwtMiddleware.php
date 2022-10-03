<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;
use Exception;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use App\Http\Traits\GeneralTrait;

class JwtMiddleware
{
    use GeneralTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $user = auth('api')->userOrFail();
        } catch (\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e) {
            return $this->ResponseJson(
                CONFIG("statusmessage.USER_NOT_FOUND"),
            );
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                return $this->ResponseJson(
                    CONFIG("statusmessage.TOKEN_INVALID"),
                );
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                return $this->ResponseJson(
                    CONFIG("statusmessage.TOKEN_EXPIRED"),
                );
            }else{
                return $this->ResponseJson(
                    CONFIG("statusmessage.TOKEN_INVALID"),
                );
            }
        }
        return $next($request);
    }
}
