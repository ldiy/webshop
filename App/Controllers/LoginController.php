<?php

namespace App\Controllers;

use Core\Exceptions\ValidationException;
use Core\Http\Request;
use Core\Http\Response;
use Core\Validation\Validator;
use Core\Validation\RuleBuilder as Rule;
use Throwable;

class LoginController
{
    /**
     * Show the login form.
     *
     * @param Request $request
     * @return Response
     * @throws Throwable
     */
    public function show(Request $request): Response
    {
        if (auth()->check()) {
            return redirect('/');
        }
        return view('Auth/login');
    }

    /**
     * Attempt to authenticate a user.
     *
     * @param Request $request
     * @return Response
     * @throws ValidationException
     */
    public function login(Request $request): Response
    {
        $request->validate([
            'email' => Rule::new()->required()->email()->maxLength(319),
            'password' => Rule::new()->required(),
        ]);

        $success = auth()->attempt([
            'email' => $request->input('email'),
            'password' => $request->input('password')
        ]);

        if ($success) {
            return redirect('/');
        }
        else {
            throw ValidationException::fromMessages([
                'email' => 'Invalid credentials'
            ]);
        }
    }

    /**
     * Log the user out.
     *
     * @param Request $request
     * @return Response
     */
    public function logout(Request $request): Response
    {
        auth()->logout();
        return redirect('/login');
    }
}