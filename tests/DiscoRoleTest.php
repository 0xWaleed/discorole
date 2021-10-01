<?php /** @noinspection ALL */


use DiscoRole\DiscoRole;
use GuzzleHttp\Psr7\Utils;
use GuzzleHttp\Psr7\Response;
use DiscoRole\Exceptions\DiscordApiException;
use DiscoRole\Exceptions\TokenIsRequiredException;

beforeEach(function ()
{
    $this->response = new Response(200, [], json_encode(['roles' => []]));
});

test('DiscoRole to hit discord api for guilds', function ()
{
    $this->mockQueue->append(fn() => $this->response);
    (new DiscoRole(client: $this->client, token: '667788'))->getGuild('123456789');
    expect((string) $this->mockQueue->getLastRequest()->getUri())->toBe('https://discord.com/api/v9/guilds/123456789');
    $headers = $this->mockQueue->getLastRequest()->getHeaders();
    expect($headers)->toHaveKey('Authorization')->and($headers['Authorization'][0])->toEqual('Bot 667788');
});

test('DiscoRole should throw when api returns non 200 status code', function ()
{
    $this->mockQueue->append(fn() => $this->response->withStatus(401)->withBody(Utils::streamFor(json_encode(['message' => 'error message']))));
    (new DiscoRole(client: $this->client, token: '123'))->getGuild('123456789');
})->throws(DiscordAPIException::class, 'error message');


test('DiscoRole should throw when api returns non 200 status code without message field', function ()
{
    $this->mockQueue->append(fn() => $this->response->withStatus(401));
    (new DiscoRole(client: $this->client, token: '123'))->getGuild('123456789');
})->throws(DiscordAPIException::class, 'Unknown error');

test('DiscoRole->getGuild return Guild instance', function ()
{
    $guildId = '123456789';
    $this->mockQueue->append(fn() => $this->response->withBody(Utils::streamFor(json_encode(['id' => $guildId]))));
    $guild = (new DiscoRole(client: $this->client, token: '123'))->getGuild($guildId);
    expect($guild)->toBeInstanceOf(\DiscoRole\Guild::class);
    expect($guild->id)->toEqual($guildId);
});

test('DiscoRole->getGuild pass client and cache when creating instance of Guild', function ()
{
    $fakeCache = $this->getMockBuilder(\Psr\SimpleCache\CacheInterface::class)->getMock();
    $this->mockQueue->append(fn() => $this->response);
    $guild = (new DiscoRole(client: $this->client, token: '123', cache: $fakeCache))->getGuild('1234');
    expect($guild)->toBeInstanceOf(\DiscoRole\Guild::class);
    expect($guild->cache)->toBe($fakeCache);
    expect($guild->client)->toBe($this->client);
});

it('should not invoke the http request when cache already exist', function ()
{
    $fakeCache = $this->getMockBuilder(\Psr\SimpleCache\CacheInterface::class)->getMock();
    $this->mockQueue->append($this->response);

    $fakeCache->expects($this->once())->method('has')->with('discorole.guild.123456789')->willReturn(true);
    $fakeCache->expects($this->once())->method('get')->with('discorole.guild.123456789')->willReturn(new stdClass());
    $guild = (new DiscoRole(client: $this->client, token: '667788', cache: $fakeCache))->getGuild('123456789');

    expect($guild)->toBeInstanceOf(\DiscoRole\Guild::class);
    expect($this->mockQueue->count())->toBe(1);
});

it('should pass the cache and client to Guild constructor when getting cached value', function ()
{
    $fakeCache = $this->getMockBuilder(\Psr\SimpleCache\CacheInterface::class)->getMock();
    $this->mockQueue->append($this->response);

    $fakeCache->expects($this->once())->method('has')->with('discorole.guild.123456789')->willReturn(true);
    $fakeCache->expects($this->once())->method('get')->with('discorole.guild.123456789')->willReturn(new stdClass());
    $guild = (new DiscoRole(client: $this->client, token: '667788', cache: $fakeCache))->getGuild('123456789');

    expect($guild)->toBeInstanceOf(\DiscoRole\Guild::class);
    expect($guild->cache)->toBe($fakeCache);
    expect($guild->client)->toBe($this->client);
    expect($this->mockQueue->count())->toBe(1);
});

it('should set the cache when is not exist', function ()
{
    $fakeCache = $this->getMockBuilder(\Psr\SimpleCache\CacheInterface::class)->getMock();
    $this->mockQueue->append($this->response);

    $fakeCache->expects($this->once())->method('has')->with('discorole.guild.123456789')->willReturn(false);
    $fakeCache->expects($this->once())->method('set')->with('discorole.guild.123456789', \PHPUnit\Framework\anything());

    (new DiscoRole(client: $this->client, token: '667788', cache: $fakeCache))->getGuild('123456789');

    expect($this->mockQueue->count())->toBe(0);
});