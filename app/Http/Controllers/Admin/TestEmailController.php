<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Notifications\SystemTestEmailNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;
use Throwable;

class TestEmailController extends Controller
{
    public function create(): View
    {
        return view('admin.test-email', [
            'defaultEmail' => config('requests.notify_email'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'context' => ['nullable', 'string', 'max:255'],
        ], [
            'email.required' => 'Informe o e-mail de destino.',
            'email.email' => 'Informe um e-mail válido.',
        ]);

        try {
            Notification::route('mail', $validated['email'])
                ->notify(new SystemTestEmailNotification($validated['context'] ?: 'Teste manual no painel admin'));
        } catch (Throwable $e) {
            Log::error('Falha ao enviar e-mail de teste.', [
                'to' => $validated['email'],
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors([
                'email' => 'Falha no envio SMTP: ' . $e->getMessage(),
            ])->withInput();
        }

        return back()->with('status', 'E-mail de teste enviado para ' . $validated['email'] . '.');
    }
}
