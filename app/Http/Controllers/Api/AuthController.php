<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Iniciar sesión
     *
     * Autentica al usuario y retorna un token de acceso Bearer (Laravel Passport).
     * Usa este token en el header `Authorization: Bearer {token}` para los endpoints protegidos.
     *
     * @unauthenticated
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return response()->json(['message' => 'Credenciales incorrectas.'], 401);
        }

        $user  = Auth::user();
        $token = $user->createToken('api-token')->accessToken;

        return response()->json([
            'token' => $token,
            'user'  => [
                'id'        => $user->id,
                'name'      => $user->name,
                'email'     => $user->email,
                'role'      => $user->role,
                'tienda_id' => $user->tienda_id,
            ],
        ]);
    }

    /**
     * Cerrar sesión
     *
     * Revoca el token Bearer actual. El token queda inválido inmediatamente.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user('api')->token()->revoke();

        return response()->json(['message' => 'Sesion cerrada correctamente.']);
    }

    /**
     * Usuario autenticado
     *
     * Retorna los datos del usuario dueño del token Bearer actual.
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user('api');

        return response()->json([
            'id'              => $user->id,
            'name'            => $user->name,
            'email'           => $user->email,
            'role'            => $user->role,
            'whatsapp_number' => $user->whatsapp_number,
            'tienda_id'       => $user->tienda_id,
        ]);
    }
}
