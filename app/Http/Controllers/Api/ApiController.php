<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\PhotoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ApiController extends Controller
{
    public function getUsers(Request $request): JsonResponse
    {
        $perPage = $request->perPage ?? 6;
        $users = User::query()->orderBy('created_at', 'desc')->limit($perPage)->get();

        return response()->json($users);
    }

    public function save(Request $request): JsonResponse
    {

        $this->validate($request, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'photo' => 'required|mimes:jpeg,jpg,bmp,png',
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // $method это метод по работе с изображением;
        $pass = PhotoService::optimize($request, 'fit', 70, 70);
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'photo' => $pass,
            'password' => Hash::make($request->password),
        ]);
        return response()->json([], 204);
    }

    public function getApiToken()
    {
        return config('apitokens')[0];
    }
}
