<?php

namespace App\Http\Controllers;

 /**  Class ApiController
 *
 * @package App\Http\Controllers
 *
 * @SWG\Swagger(
 *      basePath="",
 *      host="fonosoft.local/",
 *      schemes={"http"},
 *      @SWG\SecurityScheme(
 *         securityDefinition="Bearer",
 *         type="apiKey",
 *         in="header",
 *         name="Authorization",
 *         description="Auth Bearer Token Format as 'Bearer <access_token>'"
 *     ),
 *      @SWG\Info(
 *          version="1.0",
 *          title="Fonosoft",
 *          @SWG\Contact(name="Marión Juan Pablo", url="https://www.example.com"),
 *      )
 * )
 */

class ApiController extends Controller
{
    //
}
