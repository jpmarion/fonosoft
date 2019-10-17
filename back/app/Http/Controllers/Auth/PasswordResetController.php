<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Notifications\PasswordResetRequest;
use App\Notifications\PasswordResetSuccess;
use App\PasswordReset;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class PasswordResetController extends Controller
{
    /**
     * Reestablecer contraseña.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     *
     * @SWG\Post(
     *      path="/api/password/create",
     *      tags={"Auth"},
     *      summary="Reestablecer contraseña",
     *      @SWG\Parameter(
     *          name="email",
     *          description="Email",
     *          required=true,
     *          type="string",
     *          in="formData",
     *          description="Json format"
     *      ),
     *      @SWG\Response(
     *          response=201,
     *          description="Éxito: Reestablecimiento de contraseña"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="Éxito: operación exitosa"
     *      ),
     *      @SWG\Response(
     *          response=401,
     *          description="Rechazado: no autenticado"
     *      ),
     *      @SWG\Response(
     *          response="422",
     *          description="Falta campo obligatorio"
     *      ),
     *      @SWG\Response(
     *          response="404",
     *          description="No encontrado"
     *      )
     * )
     */
    public function create(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
        ]);
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'message' => 'No podemos encontrar un usuario con esa dirección de correo electrónico.',
            ], 402);
        }
        $passwordReset = PasswordReset::updateOrCreate(
            ['email' => $user->email],
            [
                'email' => $user->email,
                'token' => md5($user->email),
            ]
        );
        if ($user && $passwordReset) {
            $user->notify(
                new PasswordResetRequest($passwordReset->token)
            );
            return response()->json(
                ['message' => '¡Hemos enviado su enlace de restablecimiento de contraseña por correo electrónico!']
            );
        }
    }

    /**
     * Find token password reset
     *
     * @param  [string] $token
     * @return [string] message
     * @return [json] passwordReset object
     */

    public function find($token)
    {
        $passwordReset = PasswordReset::where('token', $token)->first();
        if (!$passwordReset) {
            return response()->json([
                'message' => 'Este token de restablecimiento de contraseña no es válido.',
            ], 404);
        }
        if (Carbon::parse($passwordReset->update_at)->addMinutes(720)->isPast()) {
            $passwordReset->delete();
            return response()->json([
                'message' => 'Este token de restablecimiento de contraseña no es válido.',
            ], 404);
        }
        return response()->json($passwordReset);
    }

    /**
     * Reset password
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [string] password_confirmation
     * @param  [string] token
     * @return [string] message
     * @return [json] user object
     */
    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string|confirmed',
            'token' => 'required|string',
        ]);
        $passwordReset = PasswordReset::where('token', $request->token)->first();
        if (!$passwordReset) {
            return response()->json([
                'message' => 'Este token de restablecimiento de contraseña no es válido.',
            ], 404);
        }
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'message' => 'No podemos encontrar un usuario con esa dirección de correo electrónico.',
            ], 404);
        }
        $user->password = bcrypt($request->password);
        $user->save();
        $passwordReset->delete();
        $user->notify(new PasswordResetSuccess($passwordReset));
        return response()->json($user);
    }
}
