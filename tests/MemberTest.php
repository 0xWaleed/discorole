<?php /** @noinspection PhpParamsInspection */

use DiscoRole\Role;
use DiscoRole\Guild;
use DiscoRole\Member;
use DiscoRole\Permissions;
use DiscoRole\PermissionConstants;

beforeEach(function ()
{
    $this->memberFixture = json_decode(<<<T
{
    "roles": [
        "836614519756554240",
        "745290943363809401"
    ],
    "nick": null,
    "avatar": null,
    "premium_since": null,
    "joined_at": "2021-03-07T15:12:25.426000+00:00",
    "is_pending": false,
    "pending": false,
    "user": {
        "id": "812696818408357949",
        "username": "0x𝗪𝗮𝗹𝗲𝗲𝗱",
        "avatar": "9ddfbda01976697f649abf4493d5e6e5",
        "discriminator": "5899",
        "public_flags": 0
    },
    "mute": false,
    "deaf": false
}
T
    );

    $this->roles = [];
    $this->roles[] = mock(Role::class)->makePartial();
    $this->roles[] = mock(Role::class)->makePartial();
    $this->roles[0]->permissions = new Permissions(0);
    $this->roles[1]->permissions = new Permissions(0);
    $this->guild = mock(Guild::class)->makePartial();
    $this->guild->roles = [
        836614519756554240 => $this->roles[0],
        745290943363809401 => $this->roles[1]
    ];
});

test('member has a parent of guild', function ()
{
    $o = new Member(member: $this->memberFixture, guild: $this->guild);
    expect($o->guild)->toBe($this->guild);
});

test('member has roles', function ()
{
    $o = new Member(member: $this->memberFixture, guild: $this->guild);
    expect($o->roles)->toBeArray();
    expect($o->roles[836614519756554240])->toBe($this->roles[0]);
});

test('Member not to throw when guild->roles is less than member->roles', function ()
{
    unset($this->guild->roles[$this->memberFixture->roles[0]]);
    $o = new Member(member: $this->memberFixture, guild: $this->guild);
    expect($o->roles[745290943363809401])->toBe($this->roles[1]);
});

test('Member roles to be empty when member roles is null', function ()
{
    unset($this->memberFixture->roles);
    $o = new Member(member: $this->memberFixture, guild: $this->guild);
    expect($o->roles)->toBeArray()->and($o->roles)->toBeEmpty();
});

test('accessors to fallback to member object', function ()
{
    $o = new Member(member: $this->memberFixture, guild: $this->guild);
    expect($o->joined_at)->toBe($this->memberFixture->joined_at);
    expect($o->user)->toBe($this->memberFixture->user);
});

test('->has will return true when one of roles has the permission', function ()
{
    $this->roles[1]->permissions = new Permissions(PermissionConstants::BAN_MEMBERS);
    $o = new Member(member: $this->memberFixture, guild: $this->guild);
    expect($o->has(PermissionConstants::BAN_MEMBERS))->toBeTrue();
});

test('->has will return false when none of role has this permissions', function ()
{
    $this->roles[0]->permissions = new Permissions(0);
    $this->roles[1]->permissions = new Permissions(0);
    $o = new Member(member: $this->memberFixture, guild: $this->guild);
    expect($o->has(PermissionConstants::BAN_MEMBERS))->toBeFalse();
});

test('->has can check for a specific role if exist', function ()
{
    $o = new Member(member: $this->memberFixture, guild: $this->guild);
    expect($o->has('836614519756554240'))->toBeTrue();
});