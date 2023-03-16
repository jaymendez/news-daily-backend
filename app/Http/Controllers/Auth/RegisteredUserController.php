<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Preference;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\Builder;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): Response
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $preferences = $user->preference()->createQuietly([
            'source' => '',
            'categories' => '',
            'authors' => '',
        ]);
        
        event(new Registered($user));

        Auth::login($user);

        return response()->noContent();
    }

    public function updateUserPreference(Request $request) {
        $preference = Preference::where('user_id', $request->user_id)->first();
        if ($preference) {

            if (isset($request->sources)) {
                $preference->sources = $request->sources;
            }
            if (isset($request->categories)) {
                $preference->categories = $request->categories;
            }
            if (isset($request->authors)) {
                $preference->authors = $request->authors;
            }
            $preference->save();
            return new JsonResponse([
                'success' => true
            ]);
        }
        return new JsonResponse([
            'success' => false,
            'message' => 'No preference for that user.'
        ]);
    }
}
