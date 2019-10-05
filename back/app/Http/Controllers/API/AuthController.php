<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Notifications\SignupActivate;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use App\User;


class AuthController extends Controller
{
    /**
     * Registrar un nuevo usuario.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     *
     * @SWG\Post(
     *      path="/api/auth/signup",
     *      tags={"Users"},
     *      summary="Crear nuevo usuario",
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          required=true,
     *          @SWG\Schema(ref="#/definitions/User"),
     *          description="Json format",
     *      ),
     *      @SWG\Response(
     *          response=201,
     *          description="Éxito: un usuario recién creado",
     *          @SWG\Schema(ref="#/definitions/User")
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
    public function signup(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|confirmed',
        ]);

        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'activation_token' => md5(60),
        ]);
        $user->save();

        $user->notify(new SignupActivate($user));

        return response()->json([
            'message' => 'Usuario creado con éxito!', 201,
        ]);
    }

    /**
     * Iniciar sesión un usuario.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     *
     * @SWG\Post(
     *      path="/api/login",
     *      tags={"Users"},
     *      summary="registrar a un usuario",
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          required=true,
     *      @SWG\Schema(ref="#/definitions/User"),
     *          description="Json format",
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
     *          response="404",* description="No encontrado"
     *      )
     * )
     */
    public function login(Request $request)
    {
        $request->validate([
            'name' => 'required|string|email',
            'emai' => 'required|string',
            'remember_me' => 'boolean',
        ]);
        $credential = request(['email', 'password']);
        $credential['active'] = 1;
        $credential['deleted_at'] = null;
        if (!Auth::attempt($credential)) {
            return response()->json(['message' => 'No autorizado'], 401);
        }
        $user = $request->user();

        $tokenResult = $user->createToken('Token Acceso Personal');
        $token = $tokenResult->token;
        if ($request->remember_me) {
            $token->expires_at = Carbon::now()->addWeek(1);
        }
        $token->save();

        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at)
                ->toDateTimeString(),
        ]);
    }

    /**
     * Cerrar sesión de usuario.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     *
     * @SWG\Post(
     *       path="/api/logout",
     *       tags={"Users"},
     *       summary="cerrar sesión de un usuario",
     *       @SWG\Parameter(
     *           name="body",
     *           in="body",
     *           required=true,
     *       @SWG\Schema(ref="#/definitions/User"),
     *       description="Json format",
     *       ),
     *       @SWG\Response(
     *           response=200,
     *           description="Éxito: operación exitosa"
     *       ),
     *       @SWG\Response(
     *           response=401,
     *           description="Rechazado: no autenticado"
     *       ),
     *       @SWG\Response(
     *           response="422",
     *           description="Falta campo obligatorio"
     *       ),
     *       @SWG\Response(
     *           response="404",
     *           description="No encontrado"
     *       ),
     *       @SWG\Response(
     *           response="405",
     *           description="Entrada inválida"
     *       ),
     *       security={{"api_key":{}}}
     * )
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Cerrar sesión de usuario.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     *
     * @SWG\Post(
     *       path="/api/user",
     *       tags={"Users"},
     *       summary="devolver el usuario",
     *       @SWG\Parameter(
     *           name="body",
     *           in="body",
     *           required=true,
     *       @SWG\Schema(ref="#/definitions/User"),
     *       description="Json format",
     *       ),
     *       @SWG\Response(
     *           response=200,
     *           description="Éxito: operación exitosa"
     *       ),
     *       @SWG\Response(
     *           response=401,
     *           description="Rechazado: no autenticado"
     *       ),
     *       @SWG\Response(
     *           response="422",
     *           description="Falta campo obligatorio"
     *       ),
     *       @SWG\Response(
     *           response="404",
     *           description="No encontrado"
     *       ),
     *       @SWG\Response(
     *           response="405",
     *           description="Entrada inválida"
     *       ),
     *       security={{"api_key":{}}}
     * )
     */
    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    /**
     * Activar cuenta de usuario.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     *
     * @SWG\Post(
     *      path="/api/auth/signup/activate/{token}",
     *      tags={"Users"},
     *          summary="Crear un nuevo usuario",
     *          @SWG\Parameter(
     *              name="token",
     *              description="Token recibido",
     *              required=true,
     *              type="string",
     *              in="formData",
     *              description="Json format"
     *          ),
     *          @SWG\Response(
     *              response=201,
     *              description="Éxito: un usuario recién creado",
     *              @SWG\Schema(ref="#/definitions/User")
     *          ),
     *          @SWG\Response(
     *              response=200,
     *              description="Éxito: operación con éxito"
     *          ),
     *          @SWG\Response(
     *              response=401,
     *              description="Rechazado: no autenticado"* ),
     *          @SWG\Response(
     *              response="422",
     *              description="Falta el campo obligatorio"
     *          ),
     *          @SWG\Response(
     *              response="404",
     *              description="No encontrado"
     *          )
     * )
     */
    public function signupActivate($token)
    {
        $user = User::where('activation_token', $token)->first();

        if (!$user) {
            return response()->json(['message' => 'El token de activacion es invalid'], 404);
        }

        $user->active = true;
        $user->activation_token = '';
        $user->save();

        return $user;
    }
}
