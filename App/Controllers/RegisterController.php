<?php

namespace App\Controllers;

use App\Models\Role;
use App\Models\User;
use Core\Exceptions\ValidationException;
use Core\Http\Request;
use Core\Http\Response;
use Core\Validation\RuleBuilder as Rule;
use Throwable;

class RegisterController
{
    /**
     * Show the registration form.
     *
     * @return Response
     * @throws Throwable
     */
    public function show(): Response
    {
        if (auth()->check()) {
            return redirect('/');
        }
        return view('Auth/register');
    }

    /**
     * Attempt to register a new user.
     *
     * @param Request $request
     * @return Response
     * @throws ValidationException
     */
    public function register(Request $request): Response
    {
        $request->validate([
            'first_name' => Rule::new()->required()->minLength(2)->maxLength(45),
            'last_name' => Rule::new()->required()->minLength(2)->maxLength(45),
            'email' => Rule::new()->required()->email()->maxLength(319)->unique(User::$table, 'email'),
            'password' => Rule::new()->required()->minLength(8),
            'agreed' => Rule::new()->required(),
        ]);

        User::create([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'email' => $request->input('email'),
            'password' => password_hash($request->input('password'), PASSWORD_DEFAULT),
            'role_id' => Role::getByName('user')->id,
        ]);

        return redirect('/login');
    }
}