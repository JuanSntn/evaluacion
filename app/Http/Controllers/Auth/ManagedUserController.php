<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ManagedUser\StoreManagedUserRequest;
use App\Http\Requests\ManagedUser\UpdateManagedUserRequest;
use App\Models\ManagedUser;
use App\Support\BlindIndex;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ManagedUserController extends Controller
{

    public function index(Request $request): View
    {
        $users = ManagedUser::query()
            ->where('created_by', $request->user()->getKey())
            ->latest()
            ->paginate(10);

        return view('users-and-services.index', compact('users'));
    }


    public function create(): View
    {
        return view('users-and-services.create');
    }


    public function store(StoreManagedUserRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $data['created_by'] = $request->user()->getKey();

        $data['rfc_hash'] = BlindIndex::rfc(
            $data['rfc']
        );
        ManagedUser::query()->create($data);

        return redirect()->route('users-and-services.index')->with('status', 'Usuario creado correctamente.');
    }

    public function show(Request $request, ManagedUser $user): View
    {
        $this->ensureOwnership($request, $user);

        $user->load('creator');

        return view('users-and-services.show', ['managedUser' => $user,]);
    }


    public function edit(Request $request, ManagedUser $user): View
    {
        $this->ensureOwnership($request, $user);

        return view('users-and-services.edit', ['managedUser' => $user,]);
    }


    public function update(UpdateManagedUserRequest $request, ManagedUser $user): RedirectResponse
    {

        $this->ensureOwnership($request, $user);

        $data = $request->validated();

        $data['rfc_hash'] = BlindIndex::rfc(
            $data['rfc']
        );

        $user->update($data);

        return redirect()->route('users-and-services.users.show', $user)
            ->with('status', 'Usuario actualizado correctamente.');
    }

    /**
     * Elimina un usuario.
     */
    public function destroy(Request $request, ManagedUser $user): RedirectResponse
    {
        $this->ensureOwnership($request, $user);

        $user->delete();

        return redirect()->route('users-and-services.index')
            ->with('status', 'Usuario eliminado correctamente.');
    }


    private function ensureOwnership(Request $request, ManagedUser $user): void
    {
        abort_unless(
            $user->created_by === $request->user()->getKey(),
            404
        );
    }
}
