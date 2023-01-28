<?php

namespace App\Http\Middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Mockery\Matcher\Closure;

class JWTVerify
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function __construct()
	{
	}
	public function handle($request, \Closure $next)
	{
		try {
			$requestHeaders = apache_request_headers();
			$bearerToken = $requestHeaders['Authorization'];
			$token = explode(' ', $bearerToken)[1];

			$key = env('JWT_SECRET');

			JWT::decode($token, new Key($key, 'HS256'));

		} catch (\Exception $e) {
			if ($e instanceof \Firebase\JWT\SignatureInvalidException) {
				return response()->json([
					'status' => 403,
					'message' => 'Token is Invalid'
				], 403);
			}
			
			if ($e instanceof \Firebase\JWT\ExpiredException) {
				return response()->json([
					'status' => 401,
					'message' => 'Token is Expired'
				], 401);
			}
			
			if ($e instanceof \Firebase\JWT\ExpiredException) {
				return response()->json([
					'status' => 400,
					'message' => 'Token is Blacklisted'
				], 400);
			}
			
			return response()->json([
				'status' => 404,
				'message' => 'Authorization Token not present or malformed.'
			], 404);
		}

		return $next($request);
	}
}