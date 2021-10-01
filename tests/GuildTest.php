<?php

use DiscoRole\Role;
use DiscoRole\Guild;
use DiscoRole\Member;
use GuzzleHttp\Psr7\Utils;
use GuzzleHttp\Psr7\Response;
use Psr\SimpleCache\CacheInterface;
use Psr\Http\Client\ClientInterface;
use DiscoRole\Exceptions\DiscordApiException;

beforeEach(function ()
{
    $this->guildFixture = json_decode(<<<T
{
    "id": "646980780924600330",
    "name": "LIMITLESS",
    "icon": "a_e793ac0dfb030eb02a17a75b9411185b",
    "description": null,
    "splash": "3b4f885541bcab0fa3bdd00877e50e52",
    "discovery_splash": null,
    "features": [
        "WELCOME_SCREEN_ENABLED",
        "THREADS_ENABLED",
        "BANNER",
        "NEWS",
        "PRIVATE_THREADS",
        "THREE_DAY_THREAD_ARCHIVE",
        "ROLE_ICONS",
        "VANITY_URL",
        "NEW_THREAD_PERMISSIONS",
        "SEVEN_DAY_THREAD_ARCHIVE",
        "INVITE_SPLASH",
        "COMMUNITY",
        "ANIMATED_ICON"
    ],
    "emojis": [
        {
            "name": "valor",
            "roles": [],
            "id": "666977735440203777",
            "require_colons": true,
            "managed": false,
            "animated": true,
            "available": true
        },
        {
            "name": "Point",
            "roles": [],
            "id": "666985158343131157",
            "require_colons": true,
            "managed": false,
            "animated": true,
            "available": true
        }
    ],
    "stickers": [],
    "banner": "3b4f885541bcab0fa3bdd00877e50e52",
    "owner_id": "535289049943113728",
    "application_id": null,
    "region": "europe",
    "afk_channel_id": "745317065409298593",
    "afk_timeout": 900,
    "system_channel_id": "830641730993913886",
    "widget_enabled": true,
    "widget_channel_id": null,
    "verification_level": 4,
    "roles": [
        {
            "id": "646980780924600330",
            "name": "@everyone",
            "permissions": 37084673,
            "position": 0,
            "color": 0,
            "hoist": false,
            "managed": false,
            "mentionable": false,
            "icon": null,
            "unicode_emoji": null,
            "permissions_new": "1071631425025"
        },
        {
            "id": "646985605573640202",
            "name": "Owner",
            "permissions": 2118123257,
            "position": 60,
            "color": 197379,
            "hoist": true,
            "managed": false,
            "mentionable": false,
            "icon": null,
            "unicode_emoji": null,
            "permissions_new": "8560574201"
        },
        {
            "id": "646994520461803530",
            "name": "Administrator",
            "permissions": 1861611073,
            "position": 34,
            "color": 13068031,
            "hoist": true,
            "managed": false,
            "mentionable": false,
            "icon": null,
            "unicode_emoji": null,
            "permissions_new": "8304062017"
        }
    ],
    "default_message_notifications": 1,
    "mfa_level": 0,
    "explicit_content_filter": 2,
    "max_presences": null,
    "max_members": 250000,
    "max_video_channel_users": 25,
    "vanity_url_code": "lmtls",
    "premium_tier": 3,
    "premium_subscription_count": 34,
    "system_channel_flags": 5,
    "preferred_locale": "en-GB",
    "rules_channel_id": "743167783872757800",
    "public_updates_channel_id": "743167722556358783",
    "hub_type": null,
    "nsfw": false,
    "nsfw_level": 0,
    "embed_enabled": true,
    "embed_channel_id": null
}
T
    );
    $this->response = new Response(200, [], json_encode(['roles' => []]));
});

test('->roles to be instance of Role[]', function ()
{
    $o = new Guild($this->guildFixture, token: '1234',);
    expect($o->roles)->toBeArray();
    expect($o->roles[$this->guildFixture->roles[0]->id])->toBeInstanceOf(Role::class);
});

test('->roles to be empty when guild has no roles or null', function ()
{
    unset($this->guildFixture->roles);
    $o = new Guild($this->guildFixture, token: '1234',);
    expect($o->roles)->toBeArray()->and($o->roles)->toBeEmpty();
});

test('->accessors to fallback to $guild object', function ()
{
    $o = new Guild($this->guildFixture, token: '1234',);
    expect($o->id)->toBe($this->guildFixture->id);
    expect($o->name)->toBe($this->guildFixture->name);
    expect($o->icon)->toBe($this->guildFixture->icon);
    expect($o->description)->toBe($this->guildFixture->description);
    expect($o->owner_id)->toBe($this->guildFixture->owner_id);
});

test('can inject ClientInterface via constructor', function ()
{
    $o = new Guild($this->guildFixture, token: '1234', client: $this->client);
    expect($o->client())->toBeInstanceOf(ClientInterface::class)
        ->and($o->client())->toBe($this->client);
});

test('->getMember makes GET request to the correct endpoint', function ()
{
    $this->guildFixture->id = '123456';
    $this->mockQueue->append($this->response);
    $o = new Guild($this->guildFixture, token: '1234', client: $this->client);
    $o->getMember('12345');
    expect($this->mockQueue->getLastRequest()->getMethod())->toEqual('GET');
    expect((string) $this->mockQueue->getLastRequest()->getUri())->toEqual('https://discord.com/api/v9/guilds/123456/members/12345');
});

test('->getMember makes GET request with the token', function ()
{
    $this->guildFixture->id = '123456';
    $this->mockQueue->append($this->response);
    $o = new Guild($this->guildFixture, token: '1234', client: $this->client);
    $o->getMember('12345');
    $headers = $this->mockQueue->getLastRequest()->getHeaders();
    expect($headers)->toHaveKey('Authorization')
        ->and($headers['Authorization'][0])
        ->toEqual('Bot 1234');
});

test('->getMember to throw DiscordAPIException when non 200 status code returned', function ()
{
    $this->guildFixture->id = '123456';
    $this->mockQueue->append($this->response->withStatus(404)
        ->withBody(Utils::streamFor(json_encode(['message' => 'error message']))));
    $o = new Guild($this->guildFixture, token: '1234', client: $this->client);
    $o->getMember('12345');
})->throws(DiscordApiException::class, 'error message');


test('->getMember to throw DiscordAPIException when non 200 status code returned with `Unknown error` if message field not exist', function ()
{
    $this->guildFixture->id = '123456';
    $this->mockQueue->append($this->response->withStatus(404)
        ->withBody(Utils::streamFor(json_encode([]))));
    $o = new Guild($this->guildFixture, token: '1234', client: $this->client);
    $o->getMember('12345');
})->throws(DiscordApiException::class, 'Unknown error.');


test('->getMember to return instance of Member', function ()
{
    $response = $this->response->withStatus(200)
        ->withBody(Utils::streamFor(json_encode([
            'roles' => [],
            'user'  => [
                'id' => '12345'
            ]
        ])));
    $this->mockQueue->append($response);
    $o = new Guild($this->guildFixture, token: '1234', client: $this->client);
    $member = $o->getMember('12345');
    expect($member)->toBeInstanceOf(Member::class);
    expect($member->user->id)->toEqual('12345');
});

test('->getMember should throw when json_decode returns null', function ()
{
    $response = $this->response->withStatus(200)
        ->withBody(Utils::streamFor('non-json'));
    $this->mockQueue->append($response);
    $o = new Guild($this->guildFixture, token: '1234', client: $this->client);
    $o->getMember('12345');
})->throws(DiscordApiException::class);

it('should not invoke the request when the member of the same guild exist in cache', function ()
{
    $fakeCache = $this->getMockBuilder(CacheInterface::class)->getMock();
    $cacheKey = "discorole.guild.{$this->guildFixture->id}.member.12345";

    $fakeCache->expects($this->once())->method('has')->with($cacheKey)->willReturn(true);
    $fakeCache->expects($this->once())->method('get')->with($cacheKey)->willReturn(new stdClass());
    $this->mockQueue->append($this->response);
    $o = new Guild($this->guildFixture, token: '1234', client: $this->client, cache: $fakeCache);
    $member = $o->getMember('12345');
    expect($member)->toBeInstanceOf(Member::class);
    expect($this->mockQueue->count())->toBe(1);
});

it('should set the value in cache after receiving the api response', function ()
{
    $fakeCache = $this->getMockBuilder(CacheInterface::class)->getMock();
    $cacheKey = "discorole.guild.{$this->guildFixture->id}.member.12345";

    $fakeCache->expects($this->once())->method('has')->with($cacheKey)->willReturn(false);
    $fakeCache->expects($this->once())->method('set')->with($cacheKey, \PHPUnit\Framework\anything());
    $this->mockQueue->append($this->response);
    $o = new Guild($this->guildFixture, token: '1234', client: $this->client, cache: $fakeCache);
    $o->getMember('12345');
});