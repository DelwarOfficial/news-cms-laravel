<?php

namespace App\Http\Controllers\Api\V1\Cms;

use App\Http\Controllers\Api\V1\BaseApiController;

class StatusController extends BaseApiController
{
    public function index()
    {
        return $this->success([
            'status' => 'ok',
            'version' => '1.0.0',
            'time' => now()->toIso8601String(),
        ]);
    }
}
