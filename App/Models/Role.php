<?php

namespace App\Models;

/**
 * Class Role
 *
 * @property int $id
 * @property string $name
 */
class Role extends \Core\Model\Model
{
    public static string $table = 'role';
    public static string $primaryKey = 'id';

    /**
     * Get the users that have this role.
     *
     * @return array<User>
     */
    public function users(): array
    {
        return $this->hasMany(User::class, 'role_id');
    }

    /**
     * Get a role by its name.
     *
     * @param string $name
     * @return Role|null
     */
    public static function getByName(string $name): ?Role
    {
        $roles = self::where('name', '=', $name);
        return $roles[0] ?? null;
    }
}