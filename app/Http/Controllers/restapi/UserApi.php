<?php

namespace App\Http\Controllers\restapi;

use App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use OpenApi\Annotations as OA;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserApi extends Api
{
    /**
     * @OA\Get(
     *     path="/api/auth/users/get-info",
     *     summary="Get user information from token",
     *     description="Get user information from token",
     *     tags={"Users Api"},
     *     @OA\Response(
     *         response=200,
     *         description="successful operation"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized user"
     *     )
     * )
     */
    public function getUserFromToken()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $data = $user->toArray();

            $res = returnMessage(1, $data, 'Success!');

            return response()->json($res, 200);
        } catch (TokenInvalidException $e) {
            $res = returnMessage(-1, null, 'Token is Invalid!');
            return response()->json($res, 400);
        } catch (TokenExpiredException $e) {
            $res = returnMessage(-1, null, 'Token is Expired!');
            return response()->json($res, 400);
        } catch (\Exception $e) {
            $res = returnMessage(-1, null, 'Authorization Token not found!');
            return response()->json($res, 401);
        }
    }
}
