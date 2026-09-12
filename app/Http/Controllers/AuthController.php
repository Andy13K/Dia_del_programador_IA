<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function __construct(private readonly AuditService $auditService)
    {
    }

    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->validated();

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            $this->auditService->log('login_fallido', User::class, payload: ['email' => $credentials['email']]);

            // Mensaje genérico (OWASP A06): nunca revelar si el correo existe o no.
            return back()
                ->withErrors(['email' => 'Credenciales inválidas.'])
                ->onlyInput('email');
        }

        // Regenerar sesión previene fijación de sesión (OWASP A07).
        $request->session()->regenerate();

        $this->auditService->log('login_exitoso', User::class, Auth::id());

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        $this->auditService->log('logout', User::class, Auth::id());

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
