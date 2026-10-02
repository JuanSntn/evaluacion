<?php

namespace App\Http\Controllers;

use App\Http\Requests\Account\UpdateAccountRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountSettingsController extends Controller
{
    public function edit(Request $request): View
    {
        return view('account.edit', ['user' => $request->user()]);
    }

    public function update(UpdateAccountRequest $request): RedirectResponse
    {
        $request->user()?->update($request->safe()->only([
            'name',
            'email',
            'rfc',
            'address',
            'phone',
            'website',
        ]));

        return back()->with('status', 'Información de la cuenta actualizada.');
    }
}
