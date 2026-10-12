<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\{ JsonResponse, Request };
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function __construct(private readonly AuthService $service)
    {  }
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function loginMethod(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->messages()->first(),
                'success' => false
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }else {
            return $this->service->login($request);
        }
    }

    /**
     * @return UserResource
     */
    public function profileMethod(): UserResource
    {
        return $this->service->profile();
    }

    /**
     * @return JsonResponse
     */
    public function logoutMethod(): JsonResponse
    {
        return $this->service->logout();
    }

    /**
     * @return JsonResponse
     */
    public function refreshMethod(): JsonResponse
    {
        return $this->service->refresh();
    }

}
