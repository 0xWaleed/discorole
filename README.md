[![TEST](https://github.com/0xWaleed/discorole/actions/workflows/php.yml/badge.svg)](https://github.com/0xWaleed/discorole/actions/workflows/php.yml)  
# DiscoRole
Simple PHP library to get all user's rols and permissions from the specified guild.

## The Purpose
Sometimes I build an application and want to synchronize the user with his roles between my application and my Discord server in order to manage roles in one place.

## Requiremensts
* PHP 8 <3
* Bot token and the bot should be in your server

## Install

`composer require 0xwaleed/discorole`

## Examples
### When you need to check for member's roles/permissions
```php
use DiscoRole\PermissionConstants;

$d = new \DiscoRole\DiscoRole(token: 'your bot token');
$guild = $d->getGuild('guild id here');
$member = $guild->getMember('member id here');

//if we want to check for specific role
if ($member->has('role id')) {
    //do your logic
}

//if we want to traverse all member's roles and check if one of them
//has the these permissions
if ($member->has(PermissionContatns::MANAGE_MESSAGES | PermissionContatns::MANAGE_GUILD)) {
    //do your logic
}
```

### When you want to get role details
```php
foreach ($member->roles as $role) {
    $role->id;
    $role->name;
    $role->color;
    ...
}
```
