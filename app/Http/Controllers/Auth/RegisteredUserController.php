<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Authentication\StartUserSession;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(RegisterRequest $request, StartUserSession $startUserSession): RedirectResponse
    {
        $role = Role::query()->firstOrCreate(
            ['slug' => Role::ACCOUNT_OWNER],
            ['name' => 'Dueño de cuenta'],
        );

        $user = User::query()->create([
            'role_id' => $role->getKey(),
            ...$request->safe()->only(['name', 'email', 'rfc', 'password']),
        ]);

        $startUserSession->handle($user, $request);

        return redirect()->route('dashboard')->with('status', 'Tu cuenta quedó lista.');
    }
}
