<?php

use App\Http\Controllers\Admin\RequestPdfController;
use App\Http\Controllers\Admin\TestEmailController;
use App\Http\Controllers\Admin\UserRegistrationController;
use App\Livewire\Requests\Index;
use App\Livewire\Requests\Space\Create as SpaceCreate;
use App\Livewire\Requests\Training\Create as TrainingCreate;
use App\Models\TrainingRequest;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rotas Públicas (SEM LOGIN)
|--------------------------------------------------------------------------
*/

Route::get('/', Index::class)->name('requests.index');

Route::redirect('/login', '/admin/login')->name('login');

Route::get('/solicitacoes/curso/{scope}', TrainingCreate::class)
    ->whereIn('scope', [TrainingRequest::SCOPE_STATE, TrainingRequest::SCOPE_MUNICIPALITY])
    ->name('requests.training.create');

Route::get('/solicitacoes/formacao', function () {
    return redirect()->route('requests.training.create', ['scope' => TrainingRequest::SCOPE_STATE]);
})->name('requests.training.legacy');

Route::get('/cessao-de-espaco', SpaceCreate::class)->name('space-requests.create');

Route::get('/session/ping', function () {
    session()->put('_last_public_form_ping', now()->timestamp);

    return response()->noContent();
})->name('session.ping');

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->group(function (): void {
        Route::get('/training-requests/{record}/pdf', [RequestPdfController::class, 'training'])
            ->name('admin.training-requests.pdf');

        Route::get('/space-requests/{record}/pdf', [RequestPdfController::class, 'space'])
            ->name('admin.space-requests.pdf');
    });

Route::middleware(['auth', 'admin'])->group(function (): void {
    Route::get('/cadastro', [UserRegistrationController::class, 'create'])->name('hidden-register.create');
    Route::post('/cadastro', [UserRegistrationController::class, 'store'])->name('hidden-register.store');

    Route::get('/admin/teste-email', [TestEmailController::class, 'create'])->name('admin.test-email.create');
    Route::post('/admin/teste-email', [TestEmailController::class, 'store'])->name('admin.test-email.store');
});
