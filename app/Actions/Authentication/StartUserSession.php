<?php

namespace App\Actions\Authentication;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StartUserSession
{
    public function handle(User $user, Request $request): void
    {
        $this->invalidate($user);

        Auth::login($user);
        $request->session()->regenerate();
    }

    public function invalidate(User $user): void
    {
        if (config('session.driver') !== 'database') {
            return;
        }

        DB::table((string) config('session.table'))
            ->where('user_id', $user->getKey())
            ->delete();
    }
}
