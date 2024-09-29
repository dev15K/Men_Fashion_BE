<?php

namespace App\Http\Controllers\restapi;

use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Controllers\MainController;
use App\Models\Role;
use App\Models\RoleUser;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthApi extends Controller
{
    public function login(Request $request)
    {
        $newController = (new MainController());
        try {
            $loginRequest = $request->input('login_request');
            $password = $request->input('password');

            $credentials = [
                'password' => $password,
            ];

            if (filter_var($loginRequest, FILTER_VALIDATE_EMAIL)) {
                $credentials['email'] = $loginRequest;
            } else {
                $credentials['phone'] = $loginRequest;
            }

            $user = User::where('email', $loginRequest)->orWhere('phone', $loginRequest)->first();
            if (!$user) {
                return response($newController->returnMessage('User not found!'), 404);
            } else {
                if ($user->status == UserStatus::INACTIVE) {
                    return response($newController->returnMessage('User not active!'), 400);
                } else if ($user->status == UserStatus::BLOCKED) {
                    return response($newController->returnMessage('User has been blocked!'), 400);
                } else if ($user->status == UserStatus::DELETED) {
                    return response($newController->returnMessage('User is deleted!'), 400);
                }
            }

            if (Auth::attempt($credentials)) {
                $token = JWTAuth::fromUser($user);
                $user->save();

                $response = $user->toArray();
                $roleUser = RoleUser::where('user_id', $user->id)->first();
                $role = Role::find($roleUser->role_id);
                $response['role'] = $role->name;
                $response['accessToken'] = $token;
                return response()->json($response);
            }
            return response()->json($newController->returnMessage('Login fail! Please check email or password'), 400);
        } catch (\Exception $exception) {
            return response($newController->returnMessage($exception->getMessage()), 400);
        }
    }

    public function register(Request $request)
    {
        $newController = (new MainController());
        try {
            $name = $request->input('name');
            $email = $request->input('email');
            $phone = $request->input('phone');
            $password = $request->input('password');
            $password_confirm = $request->input('password_confirm');

            $isEmail = filter_var($email, FILTER_VALIDATE_EMAIL);
            if (!$isEmail) {
                return response($newController->returnMessage('Email invalid!'), 400);
            }

            $is_valid = User::checkEmail($email);
            if (!$is_valid) {
                return response($newController->returnMessage('Email already exited!'), 400);
            }

            $is_valid = User::checkPhone($phone);
            if (!$is_valid) {
                return response($newController->returnMessage('Phone already exited!'), 400);
            }

            if ($password != $password_confirm) {
                return response($newController->returnMessage('Password or Password Confirm incorrect!'), 400);
            }

            if (strlen($password) < 5) {
                return response($newController->returnMessage('Password invalid!'), 400);
            }

            $passwordHash = Hash::make($password);

            $user = new User();

            $user->full_name = $name ?? '';
            $user->phone = $phone;
            $user->email = $email;
            $user->password = $passwordHash;

            $user->address = '';
            $user->about = '';

            $user->status = UserStatus::ACTIVE;

            $success = $user->save();

            $newController->saveRoleUser($user->id);

            if ($success) {
                return response($newController->returnMessage('Register success!'), 200);
            }
            return response($newController->returnMessage('Register failed!'), 400);
        } catch (\Exception $exception) {
            return response($newController->returnMessage($exception->getMessage()), 400);
        }
    }

    public function logout(Request $request)
    {
        $newController = (new MainController());
        try {
            return response($newController->returnMessage('Logout success!'), 200);
        } catch (\Exception $exception) {
            return response($newController->returnMessage($exception->getMessage()), 400);
        }
    }
}
