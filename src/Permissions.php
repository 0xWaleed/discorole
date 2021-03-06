<?php

namespace DiscoRole;

use JsonSerializable;

/**
 * @method boolean canAdministrate()
 * @method boolean canManage()
 * @method boolean canKick()
 * @method boolean canBan()
 */
class Permissions implements JsonSerializable
{
    public function __construct(public int $permissions)
    {
    }

    public function __call(string $name, array $arguments)
    {
        if (! str_starts_with($name, 'can')) {
            return null;
        }

        $permissionLabel = substr($name, 3);

        foreach (PermissionConstants::PERMISSION_LABELS as $permission => $label) {
            if ($permissionLabel === $label) {
                return $this->has($permission);
            }
        }

        return null;
    }

    public function has(int|string $permission): bool
    {
        if (is_string($permission)) {
            $permission = constant(PermissionConstants::class . '::' . $permission);
        }

        if (($this->permissions & PermissionConstants::ADMINISTRATOR) === PermissionConstants::ADMINISTRATOR) {
            return true;
        }

        return ($this->permissions & $permission) === $permission;
    }

    public function jsonSerialize(): array
    {
        $constants = (new \ReflectionClass(PermissionConstants::class))->getConstants();
        $out = [];
        foreach ($constants as $key => $value) {
            if (gettype($value) !== "integer") {
                continue;
            }
            $out[$key] = $this->has($value);
        }
        return $out;
    }
}