<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()->withCount('favorites');

        if ($request->filled('q')) {
            $term = '%'.$request->string('q').'%';
            $query->where(fn ($q) => $q->where('name', 'like', $term)->orWhere('email', 'like', $term));
        }

        return UserResource::collection($query->latest()->paginate(20));
    }

    /**
     * Promouvoir / rétrograder un compte (visiteur <-> administrateur).
     */
    public function updateRole(Request $request, User $user)
    {
        $data = $request->validate(['role' => ['required', 'in:visiteur,administrateur']]);
        $user->update(['role' => $data['role']]);

        ActivityLog::record($request->user()->id, 'user.role_update', 'User', $user->id, "Rôle changé en {$data['role']} pour {$user->email}");

        return new UserResource($user);
    }

    public function destroy(Request $request, User $user)
    {
        $email = $user->email;
        $user->delete();
        ActivityLog::record($request->user()->id, 'user.delete', 'User', null, "Suppression du compte {$email}");

        return response()->json(['message' => 'Compte supprimé.']);
    }
}
