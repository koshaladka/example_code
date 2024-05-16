<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    /**
     * @OA\Info(
     *       version="1.0.0",
     *       title="OpenApi Documentation",
     *
     *  )
     *
     * @OA\Server(
     *       url=L5_SWAGGER_CONST_HOST,
     *       description="ATOL.RU API DOC"
     *  )
     */

}
