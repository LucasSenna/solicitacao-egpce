<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserRegistrationController extends Controller
{
    public function create(): View
    {
        return view('admin.hidden-register');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'is_admin' => ['nullable', 'boolean'],
            'admin_profile' => ['nullable', Rule::in(array_keys(User::adminProfileOptions()))],
        ], [
            'name.required' => 'Informe o nome do usuário.',
            'email.required' => 'Informe o e-mail.',
            'email.email' => 'Informe um e-mail válido.',
            'email.unique' => 'Já existe um usuário com esse e-mail.',
            'password.required' => 'Informe a senha.',
            'password.min' => 'A senha deve ter pelo menos 6 caracteres.',
            'password.confirmed' => 'A confirmação de senha não confere.',
            'admin_profile.in' => 'Perfil administrativo inválido.',
        ]);

        $isAdmin = (bool) ($validated['is_admin'] ?? false);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'is_admin' => $isAdmin,
            'admin_profile' => $isAdmin
                ? ($validated['admin_profile'] ?? User::ADMIN_PROFILE_FULL_ACCESS)
                : User::ADMIN_PROFILE_FULL_ACCESS,
        ]);

        return back()->with('status', 'Usuário criado com sucesso.');
    }
}
