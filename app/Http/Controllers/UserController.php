<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Services\BackendAuditService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct(private BackendAuditService $audit) {}

    public function index(Request $request): View
    {
        Gate::authorize('manage-users');

        $query = User::query();

        if ($request->filled('role')) {
            $query->where('role', $request->query('role'));
        }

        if ($request->filled('search')) {
            $search = '%'.$request->query('search').'%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', $search)
                  ->orWhere('email', 'like', $search);
            });
        }

        $users = $query->orderByDesc('id')->paginate(15)->withQueryString();

        $stats = [
            'total' => User::count(),
            'admins' => User::where('role', 'admin')->count(),
            'operadores' => User::where('role', 'operador')->count(),
            'visualizadores' => User::where('role', 'visualizador')->count(),
        ];

        return view('users.index', compact('users', 'stats'));
    }

    public function create(): View
    {
        Gate::authorize('manage-users');

        return view('users.create');
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        Gate::authorize('manage-users');

        DB::transaction(function () use ($request): void {
            $data = $request->validated();
            $user = new User();
            $user->name = $data['name'];
            $user->email = $data['email'];
            $user->password = Hash::make($data['password']);
            $user->role = $data['role']; // Asignación explícita (OWASP A01)
            $user->save();

            $this->audit->record($request, 'created', $user);
        });

        return redirect()->route('users.index')->with('success', 'Usuario registrado exitosamente.');
    }

    public function edit(User $user): View
    {
        Gate::authorize('manage-users');

        return view('users.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        Gate::authorize('manage-users');

        $data = $request->validated();

        if ($user->id === auth()->id() && $data['role'] !== 'admin') {
            return back()->with('error', 'Por seguridad no puedes revocar tu propio rol de administrador.');
        }

        DB::transaction(function () use ($request, $user, $data): void {
            $user->name = $data['name'];
            $user->email = $data['email'];
            if (!empty($data['password'])) {
                $user->password = Hash::make($data['password']);
            }
            $user->role = $data['role'];
            $user->save();

            $this->audit->record($request, 'updated', $user);
        });

        return redirect()->route('users.index')->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        Gate::authorize('manage-users');

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Por seguridad no puedes eliminar tu propia cuenta de usuario activa.');
        }

        DB::transaction(function () use ($request, $user): void {
            $user->delete();
            $this->audit->record($request, 'deleted', $user);
        });

        return redirect()->route('users.index')->with('success', 'Usuario eliminado correctamente.');
    }
}
