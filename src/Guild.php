<?php

namespace DiscoRole;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Psr\SimpleCache\CacheInterface;
use Psr\Http\Client\ClientInterface;
use DiscoRole\Exceptions\DiscordApiException;

/**
 * @property string $id
 * @property string $name
 * @property ?string $icon
 * @property ?string $description
 * @property string $owner_id
 */
class Guild
{
    /**
     * @var array<string, Role>
     */
    public array $roles = [];

    public function __construct(
        public object           $guild,
        public                  $token,
        public ?ClientInterface $client = null,
        public ?CacheInterface  $cache = null
    )
    {
        if (isset($guild->roles)) {
            $this->mapRoles($guild);
        }

        $this->client ??= $client ?? new Client();
    }

    public function __get(string $name)
    {
        return $this->guild->$name;
    }

    public function getMember(string $memberId): Member
    {
        $key = $this->getCacheKey($memberId);
        if ($this->cache?->has($key)) {
            return new Member(member: $this->cache?->get($key), guild: $this);
        }
        $response = $this->client()->sendRequest(new Request('GET', sprintf('%s/guilds/%s/members/%s', DiscoRole::DISCORD_API, $this->guild->id, $memberId), [
            'Authorization' => 'Bot ' . $this->token
        ]));
        if ($response->getStatusCode() !== 200) {
            throw new DiscordApiException(json_decode((string) $response->getBody())->message ?? 'Unknown error.');
        }
        $memberObject = json_decode((string) $response->getBody());
        $this->cache?->set($key, $memberObject);
        if (! $memberObject) {
            throw new DiscordApiException('Cannot decode member object.');
        }
        return new Member(member: $memberObject, guild: $this);
    }

    public function client(): ?ClientInterface
    {
        return $this->client;
    }

    private function mapRoles(object $guild): void
    {
        foreach ($guild->roles as $role) {
            $this->roles[$role->id] = new Role($role);
        }
    }

    private function getCacheKey(string $memberId): string
    {
        return "discorole.guild.{$this->guild->id}.member.$memberId";
    }
}