<?php

use DiscoRole\Permissions;
use DiscoRole\PermissionConstants;

test('canAdministrate', function ()
{
    $bitmask = (1 << 3);
    expect((new Permissions($bitmask))->canAdministrate())->toBeTrue()
        ->and((new Permissions($bitmask ^ $bitmask))->canAdministrate())->toBeFalse();
});

test('canKick', function ()
{
    $bitmask = (1 << 1);
    expect((new Permissions($bitmask))->canKick())->toBeTrue()
        ->and((new Permissions($bitmask ^ $bitmask))->canKick())->toBeFalse();
});

test('canBan', function ()
{
    $bitmask = (1 << 2);
    expect((new Permissions($bitmask))->canBan())->toBeTrue()
        ->and((new Permissions($bitmask ^ $bitmask))->canBan())->toBeFalse();
});

test('canManage', function ()
{
    $bitmask = (1 << 5);
    expect((new Permissions($bitmask))->canManage())->toBeTrue()
        ->and((new Permissions($bitmask ^ $bitmask))->canManage())->toBeFalse();
});

test('has', function ()
{
    $bitmask = (1 << 5);
    expect((new Permissions($bitmask))->has($bitmask))->toBeTrue()
        ->and((new Permissions($bitmask ^ $bitmask))->has($bitmask))->toBeFalse();
});

test('::has with permission name', function ()
{
    $bitmask = (1 << 5);
    expect((new Permissions($bitmask))->has('MANAGE_GUILD'))->toBeTrue()
        ->and((new Permissions($bitmask ^ $bitmask))->has('MANAGE_GUILD'))->toBeFalse();
});

test('::has will return true when permission has administrator', function ()
{
    $permissions = new Permissions(PermissionConstants::ADMINISTRATOR);
    expect($permissions->has((-1)))->toBeTrue();
});


test('->permissions to return the original int value', function ()
{
    expect((new Permissions(-1))->permissions)->toEqual(-1);
});

test('json serialization with permission original name if label not exist', function ()
{
    $decodedJson = json_decode(json_encode(new Permissions(-1)), true);
    expect($decodedJson['CREATE_INSTANT_INVITE'])->toEqual(true)
        ->and($decodedJson['STREAM'])->toEqual(true);
});