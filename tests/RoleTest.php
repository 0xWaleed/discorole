<?php

use DiscoRole\Role;
use DiscoRole\Permissions;
use DiscoRole\PermissionConstants;

beforeEach(function ()
{
    $this->roleFixture = json_decode(<<<T
{
  "id": "646980780924600330",
  "name": "@everyone",
  "permissions": -1,
  "position": 0,
  "color": 0,
  "hoist": false,
  "managed": false,
  "mentionable": false,
  "icon": null,
  "unicode_emoji": null,
  "permissions_new": "1071631425025"
}
T
    );
});


test('->permission to be instance of Permission', function ()
{
    $o = new Role($this->roleFixture);
    expect($o)->toHaveProperty('permissions')
        ->and($o->permissions)->toBeInstanceOf(Permissions::class);
});

test('->permission to be instance of Permission even when role has no permissions field', function ()
{
    unset($this->roleFixture->permissions);
    $o = new Role($this->roleFixture);
    expect($o)->toHaveProperty('permissions')
        ->and($o->permissions)->toBeInstanceOf(Permissions::class)
        ->and($o->permissions->permissions)->toBe(0);
});

test('->accessors', function ()
{
    $o = new Role($this->roleFixture);
    expect($o->id)->toBe($this->roleFixture->id)
        ->and($o->name)->toBe($this->roleFixture->name)
        ->and($o->color)->toBe($this->roleFixture->color)
        ->and($o->hoist)->toBe($this->roleFixture->hoist)
        ->and($o->position)->toBe($this->roleFixture->position)
        ->and($o->managed)->toBe($this->roleFixture->managed)
        ->and($o->mentionable)->toBe($this->roleFixture->mentionable);
});

test('->hasColor to return false when number is 0', function ()
{
    $this->roleFixture->color = 0;
    $o = new Role($this->roleFixture);
    expect($o->hasColor())->toBeFalse();

    $this->roleFixture->color = 112233;
    $o = new Role($this->roleFixture);
    expect($o->hasColor())->toBeTrue();
});

test('->__call will fallback to permissions', function ()
{
    $this->roleFixture->permissions = PermissionConstants::KICK_MEMBERS | PermissionConstants::BAN_MEMBERS | PermissionConstants::MANAGE_GUILD;
    $o = new Role($this->roleFixture);
    expect($o->canManage())->toBeTrue()
        ->and($o->canBan())->toBeTrue()
        ->and($o->canKick())->toBeTrue();
});

