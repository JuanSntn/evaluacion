<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Authentication\StartUserSession;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RecoverPasswordRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PasswordRecoveryController extends Controller
{
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    public function store(RecoverPasswordRequest $request, StartUserSession $userSessions): RedirectResponse
    {
        $user = $request->recoverableUser();

        abort_if($user === null, 422);

        $user->update(['password' => $request->string('password')->toString()]);
        $userSessions->invalidate($user);

        return redirect()->route('login')->with('status', 'Contraseña actualizada. Ya puedes iniciar sesión.');
    }
}
