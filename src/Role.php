<?php

namespace DiscoRole;

/**
 * @property string $id
 * @property string $name
 * @property int $color
 * @property boolean $hoist
 * @property int $position
 * @property boolean $managed
 * @property boolean $mentionable
 * @mixin Permissions
 */
class Role
{
    public Permissions $permissions;
    private object $role;

    public function __construct(object $role)
    {
        $this->permissions = new Permissions($role->permissions ?? 0);
        $this->role = $role;
    }

    public function __get(string $name)
    {
        return $this->role->$name;
    }

    public function __call(string $name, array $arguments)
    {
        return $this->permissions->$name(...$arguments);
    }

    public function hasColor(): bool
    {
        return $this->color !== 0;
    }
}