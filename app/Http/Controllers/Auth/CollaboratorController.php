<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Collaborator\StoreCollaboratorRequest;
use App\Http\Requests\Collaborator\UpdateCollaboratorRequest;
use App\Models\Collaborator;
use App\Models\State;
use App\Support\BlindIndex;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CollaboratorController extends Controller
{
    public function index(Request $request): View
    {
        $collaborators = Collaborator::query()
            ->where('user_id', $request->user()->getKey())
            ->with('state')
            ->latest()
            ->paginate(10);

        return view('collaborators.index', compact('collaborators'));
    }

    public function create(): View
    {
        $states = State::query()->orderBy('nombre')->get();

        return view('collaborators.create', compact('states'));
    }

    public function store(StoreCollaboratorRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $data['user_id'] = $request->user()->getKey();

        $data['correo_hash'] = BlindIndex::email($data['correo']);

        $data['rfc_hash'] = BlindIndex::rfc($data['rfc']);

        $data['curp_hash'] = BlindIndex::curp($data['curp']);

        $data['nss_hash'] = BlindIndex::socialSecurityNumber(
            $data['numero_seguridad_social']
        );

        Collaborator::query()->create($data);

        return redirect()
            ->route('collaborators.index')
            ->with(
                'status',
                'Colaborador creado correctamente.'
            );
    }

    public function show(Request $request, Collaborator $collaborator): View
    {
        $this->ensureOwnership(
            $request,
            $collaborator
        );

        $collaborator->load('state');

        return view(
            'collaborators.show',
            compact('collaborator')
        );
    }

    public function edit(Request $request, Collaborator $collaborator): View
    {
        $this->ensureOwnership(
            $request,
            $collaborator
        );

        $states = State::query()->orderBy('nombre')->get();

        return view('collaborators.edit', compact('collaborator', 'states'));
    }

    public function update(UpdateCollaboratorRequest $request, Collaborator $collaborator): RedirectResponse
    {
        $data = $request->validated();

        $data['correo_hash'] = BlindIndex::email($data['correo']);

        $data['rfc_hash'] = BlindIndex::rfc($data['rfc']);

        $data['curp_hash'] = BlindIndex::curp($data['curp']);

        $data['nss_hash'] = BlindIndex::socialSecurityNumber(
            $data['numero_seguridad_social']
        );

        $collaborator->update($data);

        return redirect()
            ->route(
                'collaborators.show',
                $collaborator
            )
            ->with(
                'status',
                'Colaborador actualizado correctamente.'
            );
    }

    public function destroy(Request $request, Collaborator $collaborator): RedirectResponse
    {
        $this->ensureOwnership($request, $collaborator);

        $collaborator->delete();

        return redirect()
            ->route('collaborators.index')
            ->with(
                'status',
                'Colaborador eliminado correctamente.'
            );
    }

    private function ensureOwnership(Request $request, Collaborator $collaborator): void
    {
        abort_unless(
            $collaborator->user_id === $request->user()->getKey(),
            404
        );
    }
}
