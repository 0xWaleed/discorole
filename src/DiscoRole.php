<?php

namespace DiscoRole;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Psr\SimpleCache\CacheInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Client\ClientExceptionInterface;
use DiscoRole\Exceptions\DiscordApiException;

class DiscoRole
{
    const DISCORD_API = 'https://discord.com/api/v9';

    public function __construct(
        public string           $token,
        public ?ClientInterface $client = null,
        public ?CacheInterface  $cache = null
    )
    {
        $this->client = $client ?? new Client();
    }

    /**
     * @param $guildId
     * @return Guild
     * @throws ClientExceptionInterface
     * @throws DiscordApiException
     */
    public function getGuild($guildId): Guild
    {
        $key = $this->getCacheKey($guildId);
        if ($this->cache?->has($key)) {
            return new Guild(
                guild: $this->cache->get($key),
                token: $this->token,
                client: $this->client,
                cache: $this->cache
            );
        }
        $request = new Request('GET', sprintf('%s/guilds/%s', self::DISCORD_API, $guildId), [
            'Authorization' => 'Bot ' . $this->token
        ]);
        $response = $this->client->sendRequest($request);
        if ($response->getStatusCode() !== 200) {
            throw new DiscordApiException(json_decode((string) $response->getBody())?->message ?? 'Unknown error');
        }
        $guild = json_decode($response->getBody()->getContents());
        $this->cache?->set($key, $guild);
        return new Guild(
            guild: $guild,
            token: $this->token,
            client: $this->client,
            cache: $this->cache
        );
    }

    private function getCacheKey($guildId): string
    {
        return "discorole.guild.$guildId";
    }
}