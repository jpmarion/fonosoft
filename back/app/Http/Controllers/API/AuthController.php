<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Register a new user.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     *
     * @SWG\Post(
     *      path="/api/register",
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
    public function register(Request $request)
    {}
}
