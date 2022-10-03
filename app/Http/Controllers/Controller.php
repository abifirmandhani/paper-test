<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Traits\GeneralTrait;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, GeneralTrait;
    /**
     * @SWG\Swagger(
     *     host=L5_SWAGGER_CONST_HOST,
     *     @SWG\Info(
     *         version="1.0.0",
     *         title="PAPER API",
     *         description="PAPER API DOCUMENTATION",
     *     ),
     * )
     * @SWG\SecurityScheme(
     *   securityDefinition="ApiKeyAuth",
     *   type="apiKey",
     *   description ="Enter token in format (Bearer <token>)",
     *   in="header",
     *   name="Authorization",
     * )
     */
}
