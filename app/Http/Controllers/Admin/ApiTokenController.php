<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Laravel\Sanctum\PersonalAccessToken;

class ApiTokenController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $tokens = $user->tokens()->latest()->get();

        return view('admin.api-tokens.index', compact('tokens'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'token_name' => ['required', 'string', 'max:100'],
            'abilities'  => ['nullable', 'array'],
        ]);

        $abilities = !empty($validated['abilities']) ? $validated['abilities'] : ['attendance:read'];

        $user = $request->user();
        $token = $user->createToken($validated['token_name'], $abilities);

        return redirect()->route('admin.api-tokens.index')
            ->with('newToken', $token->plainTextToken)
            ->with('tokenName', $validated['token_name'])
            ->with('success', "API Token '{$validated['token_name']}' created successfully.");
    }

    public function destroy(Request $request, int $id): RedirectResponse
    {
        $user = $request->user();
        $token = $user->tokens()->find($id);

        if ($token) {
            $name = $token->name;
            $token->delete();
            return redirect()->route('admin.api-tokens.index')
                ->with('success', "API Token '{$name}' has been revoked.");
        }

        return redirect()->route('admin.api-tokens.index')
            ->with('error', 'Token not found or already deleted.');
    }
}
