<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function dashboard(Request $request): View
    {
        /** @var \App\Models\User $user */
        $user = $request->user();
        $misRecetas = $user->recetas()->with('categoria')->orderByDesc('recetas.id')->get();
        $misFavoritos = $user->recetasFavoritas()->with(['autor', 'categoria'])->orderByDesc('recetas.id')->get();

        return view('dashboard', [
            'misRecetas' => $misRecetas,
            'misFavoritos' => $misFavoritos,
        ]);
    }


    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        $user->fill($request->except('avatar'));

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $nombreAvatar = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('assets/img'), $nombreAvatar);
            $user->avatar = 'assets/img/' . $nombreAvatar;
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function show(User $user): View
    {
        $esSeguido = false;
        if (Auth::check() && Auth::id() !== $user->id) {
            /** @var \App\Models\User $auth */
            $auth = Auth::user();
            $esSeguido = $auth->sigueA($user);
        }

        $recetas = $user->recetas()->with('categoria')->orderByDesc('id')->get();

        return view('usuario.show', [
            'usuario' => $user,
            'recetas' => $recetas,
            'esSeguido' => $esSeguido,
        ]);
    }

    public function seguidores(User $user): View
    {
        $seguidores = $user->seguidores()->with('seguidores')->paginate(20);

        return view('usuario.seguidores', [
            'usuario' => $user,
            'seguidores' => $seguidores,
        ]);
    }

    public function seguidos(User $user): View
    {
        $seguidos = $user->seguidos()->with('seguidos')->paginate(20);

        return view('usuario.seguidos', [
            'usuario' => $user,
            'seguidos' => $seguidos,
        ]);
    }

    public function toggleFollow(\Illuminate\Http\Request $request, \App\Models\User $user): RedirectResponse
    {
        $auth = $request->user();

        if ($auth->id === $user->id) {
            return back()->with('error', 'No puedes seguirte a ti mismo.');
        }

        $estaSeguido = $auth->sigueA($user);
        $auth->seguidos()->toggle($user->id);

        if (! $estaSeguido) {
            $user->notify(new \App\Notifications\NuevoSeguidor($auth));
        }

        return back()->with('success', $estaSeguido ? 'Dejaste de seguir a este usuario.' : 'Ahora sigues a este usuario.');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
