<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\ApiResponse;

abstract class BaseApiController extends Controller
{
    use ApiResponse;
}
