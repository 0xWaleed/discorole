<?php

namespace DiscoRole;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Client\ClientExceptionInterface;
use DiscoRole\Exceptions\DiscordApiException;

class DiscoRole
{
    const DISCORD_API = 'https://discord.com/api/v9';

    public function __construct(public string $token, public ?ClientInterface $client = null)
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
        $request = new Request('GET', sprintf('%s/guilds/%s', self::DISCORD_API, $guildId), [
            'Authorization' => 'Bot ' . $this->token
        ]);
        $response = $this->client->sendRequest($request);
        if ($response->getStatusCode() !== 200) {
            throw new DiscordApiException(json_decode((string) $response->getBody())?->message ?? 'Unknown error');
        }
        $guild = json_decode($response->getBody()->getContents());
        return new Guild(guild: $guild, token: $this->token, client: $this->client);
    }
}