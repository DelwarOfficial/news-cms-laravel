<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiKey;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ApiKeyController extends Controller
{
    public function index()
    {
        $keys = ApiKey::with('user')->latest()->paginate(20);
        return view('admin.api-keys.index', compact('keys'));
    }

    public function create()
    {
        $users = User::orderBy('name')->get(['id', 'name', 'email']);
        return view('admin.api-keys.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'user_id' => 'nullable|exists:users,id',
            'scopes' => 'required|array|min:1',
            'scopes.*' => 'in:read,write,media,cms,admin',
            'rate_limit' => 'nullable|integer|min:1|max:10000',
            'expires_at' => 'nullable|date|after:now',
        ]);

        $plainKey = ApiKey::generateKey();

        ApiKey::create([
            'name' => $validated['name'],
            'user_id' => $validated['user_id'],
            'key_prefix' => ApiKey::prefixFromKey($plainKey),
            'key_hash' => hash('sha256', $plainKey),
            'scopes' => $validated['scopes'],
            'rate_limit' => $validated['rate_limit'] ?? 60,
            'expires_at' => $validated['expires_at'] ?? null,
            'is_active' => true,
        ]);

        return redirect()
            ->route('admin.api-keys.index')
            ->with('api_key', $plainKey)
            ->with('success', 'API key created. Copy it now — it won\'t be shown again.');
    }

    public function destroy(ApiKey $api_key)
    {
        $api_key->delete();
        return back()->with('success', 'API key revoked.');
    }

    public function toggle(ApiKey $api_key)
    {
        $api_key->update(['is_active' => ! $api_key->is_active]);
        return back()->with('success', 'API key status updated.');
    }
}
