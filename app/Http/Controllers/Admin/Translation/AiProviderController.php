<?php

namespace App\Http\Controllers\Admin\Translation;

use App\Http\Controllers\Controller;
use App\Models\AiProvider;
use Illuminate\Http\Request;

class AiProviderController extends Controller
{
    public function index()
    {
        $providers = AiProvider::orderBy('sort_order')->get();

        return view('admin.translation-providers.index', compact('providers'));
    }

    public function create()
    {
        $driverOptions = [
            \App\Translation\Drivers\DeepSeekTranslationDriver::class => 'DeepSeek',
            \App\Translation\Drivers\OpenAITranslationDriver::class => 'OpenAI',
            \App\Translation\Drivers\ClaudeTranslationDriver::class => 'Claude (Anthropic)',
            \App\Translation\Drivers\GeminiTranslationDriver::class => 'Gemini (Google)',
        ];

        return view('admin.translation-providers.create', compact('driverOptions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:50|unique:ai_providers,name',
            'driver_class' => 'required|string|max:255',
            'api_key' => 'nullable|string',
            'endpoint' => 'nullable|string|max:500',
            'model' => 'nullable|string|max:100',
            'options' => 'nullable|json',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        if (isset($data['options']) && is_string($data['options'])) {
            $data['options'] = json_decode($data['options'], true);
        }

        $data['is_active'] ??= true;
        $data['sort_order'] ??= 0;

        AiProvider::create($data);

        return redirect()->route('admin.translation.providers.index')
            ->with('success', 'AI provider created successfully.');
    }

    public function edit(AiProvider $provider)
    {
        $driverOptions = [
            \App\Translation\Drivers\DeepSeekTranslationDriver::class => 'DeepSeek',
            \App\Translation\Drivers\OpenAITranslationDriver::class => 'OpenAI',
            \App\Translation\Drivers\ClaudeTranslationDriver::class => 'Claude (Anthropic)',
            \App\Translation\Drivers\GeminiTranslationDriver::class => 'Gemini (Google)',
        ];

        return view('admin.translation-providers.edit', compact('provider', 'driverOptions'));
    }

    public function update(Request $request, AiProvider $provider)
    {
        $data = $request->validate([
            'name' => 'required|string|max:50|unique:ai_providers,name,' . $provider->id,
            'driver_class' => 'required|string|max:255',
            'api_key' => 'nullable|string',
            'endpoint' => 'nullable|string|max:500',
            'model' => 'nullable|string|max:100',
            'options' => 'nullable|json',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        if (isset($data['options']) && is_string($data['options'])) {
            $data['options'] = json_decode($data['options'], true);
        }

        if (empty($data['api_key'])) {
            unset($data['api_key']);
        }

        $provider->update($data);

        return redirect()->route('admin.translation.providers.index')
            ->with('success', 'AI provider updated successfully.');
    }

    public function destroy(AiProvider $provider)
    {
        $provider->delete();

        return redirect()->route('admin.translation.providers.index')
            ->with('success', 'AI provider deleted.');
    }

    public function toggle(AiProvider $provider)
    {
        $provider->update(['is_active' => ! $provider->is_active]);

        return redirect()->route('admin.translation.providers.index')
            ->with('success', $provider->is_active ? 'Provider enabled.' : 'Provider disabled.');
    }
}
