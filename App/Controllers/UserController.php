<?php

namespace App\Controllers;

use App\Models\Role;
use App\Models\User;
use Core\Exceptions\HttpNotFoundException;
use Core\Exceptions\ValidationException;
use Core\Http\JsonResponse;
use Core\Http\Request;
use Core\Http\Response;
use Core\Validation\RuleBuilder as Rule;
use Throwable;

class UserController
{
    /**
     * Show the admin page for users.
     *
     * @param Request $request
     * @return Response
     * @throws Throwable
     */
    public function index(Request $request): Response
    {
        $users = User::all();
        $availableRoles = Role::all();
        return view('admin/users', [
            'users' => $users,
            'availableRoles' => $availableRoles,
        ]);
    }

    /**
     * Update the given user resource.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws ValidationException
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'role_id' => Rule::new()->required()->numeric()->exists(Role::$table, 'id'),
        ]);

        $user = User::find($id);
        if (is_null($user)) {
            throw new HttpNotFoundException('User not found');
        }

        $user->role_id = $request->input('role_id');
        $user->save();

        return jsonResponse([
            'message' => 'User updated successfully',
            'success' => true,
        ]);
    }

    /**
     * Delete the given user resource.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $user = User::find($id);
        if (is_null($user)) {
            throw new HttpNotFoundException('User not found');
        }

        $user->delete();

        return jsonResponse([
            'message' => 'User deleted successfully',
            'success' => true,
        ]);
    }
}