<?php

namespace DiscoRole;


/**
 * @property ?string nick
 * @property ?string avatar
 * @property string premium_since
 * @property string joined_at
 * @property ?boolean pending
 * @property mixed user
 * @property boolean mute
 * @property boolean deaf
 */
class Member
{
    /**
     * @var array<int, Role>
     */
    public array $roles = [];

    public function __construct(public object $member, public Guild $guild)
    {
        if (isset($member->roles)) {
            $this->mapRoles($member->roles, $guild);
        }
    }

    public function __get(string $name)
    {
        return $this->member->$name;
    }

    public function has(int|string $permissions): bool
    {
        if (is_string($permissions)) {
            return isset($this->roles[$permissions]);
        }
        foreach ($this->roles as $role) {
            if ($role->permissions->has($permissions)) {
                return true;
            }
        }
        return false;
    }

    private function mapRoles(array $roles, Guild $guild): void
    {
        foreach ($roles as $role) {
            if (! isset($guild->roles[$role])) {
                continue;
            }
            $this->roles[$role] = $guild->roles[$role];
        }
    }
}