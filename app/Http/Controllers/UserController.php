<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Resources\UserResource;
use App\Interfaces\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * login a user with proper attribute
     *
     * @param UserLoginRequest $request
     *
     * @return JsonResponse
     */
    public function login(UserLoginRequest $request): JsonResponse
    {
        $response = null;

        if (Auth::attempt(
            [
                'email' => $request->email,
                'password' => $request->password,
            ]
        )
        ) {
            $user = Auth()->user();
            $response['status'] = true;
            $response['message'] = trans('messages.auth.login.success');
            $response['user'] = $user;
            $response['token'] = $user->createToken('Token')->accessToken;
            $response['user']->getPermissionsViaRoles();
        } else {
            $response['status'] = false;
            $response['message'] = trans('messages.auth.login.failed');
        }

        return (new AuthLoginResource($response))->response()->setStatusCode(
            $response['status'] ? Response::HTTP_OK : Response::HTTP_UNAUTHORIZED
        );
    }

    /**
     * register a new user
     *
     * @param UserRegisterRequest $request
     *
     * @return JsonResponse
     */
    public function register(UserRegisterRequest $request): JsonResponse
    {
        dd(345);
        $user = $this->userRepository->createUser([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
        ]);

        $response = null;

        if ($user) {
            $response['user'] = $user;
            $response['token'] = $user->createToken('Token')->accessToken;
            $response['user']->getPermissionsViaRoles();
        }

        return (new UserResource($response))->response()->setStatusCode(
            $response ? Response::HTTP_OK : Response::HTTP_UNPROCESSABLE_ENTITY
        );
    }

    /**
     * revoke the api token
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->token()->revoke();

        return (new NullResource(null))->response()->setStatusCode(Response::HTTP_OK);
    }
}
