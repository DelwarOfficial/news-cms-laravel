<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Post;
use App\Models\Revision;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RevisionController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = Revision::with('user:id,name');

        if ($request->filled('post_id')) {
            $query->where('post_id', $request->post_id);
        }

        return $this->paginated($query->latest()->paginate(min((int) $request->get('limit', 30), 100)));
    }
}
