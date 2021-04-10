<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @OA\Info(
     *      version="1.11.0",
     *      title="Laravel Restful API",
     *      description="This API was concepted during classes of the course 'RESTful API with Laravel: Build a Real API with Laravel' that I took from the Udemy platform.",
     *      @OA\Contact(
     *          email="nathanaellimacpc@gmail.com"
     *      ),
     *      @OA\License(
     *          name="Apache 2.0",
     *          url="http://www.apache.org/licenses/LICENSE-2.0.html"
     *      )
     * )
     *
     * @OA\Server(
     *      url=L5_SWAGGER_CONST_HOST,
     *      description="Development API Server"
     * )
     *
     * @OA\Tag(
     *     name="Users",
     *     description="API Endpoints of Users"
     * )
     */
}
