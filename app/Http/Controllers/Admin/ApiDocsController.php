<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;

class ApiDocsController extends Controller
{
    public function index()
    {
        $routes = collect(Route::getRoutes()->getRoutes())
            ->filter(fn ($r) => str_contains($r->uri(), 'api/v1'))
            ->map(fn ($r) => [
                'method' => implode('|', $r->methods()),
                'uri' => '/' . $r->uri(),
                'name' => $r->getName(),
                'action' => class_basename($r->getAction('controller') ?? ''),
            ])
            ->sortBy('uri')
            ->values();

        return view('admin.api-docs.index', compact('routes'));
    }
}
