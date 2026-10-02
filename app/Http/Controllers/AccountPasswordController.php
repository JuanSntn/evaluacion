<?php

namespace App\Http\Controllers;

use App\Actions\Authentication\StartUserSession;
use App\Http\Requests\Account\UpdatePasswordRequest;
use Illuminate\Http\RedirectResponse;

class AccountPasswordController extends Controller
{
    public function __invoke(UpdatePasswordRequest $request, StartUserSession $startUserSession): RedirectResponse
    {
        $user = $request->user();

        abort_if($user === null, 401);

        $user->update(['password' => $request->string('password')->toString()]);
        $startUserSession->handle($user, $request);

        return back()->with('password_status', 'Contraseña actualizada. Cerramos cualquier otra sesión activa.');
    }
}
