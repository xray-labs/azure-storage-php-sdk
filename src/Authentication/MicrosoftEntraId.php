<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\Authentication;

use DateTime;
use GuzzleHttp\Client;
use Psr\Http\Client\RequestExceptionInterface;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Enums\HttpVerb;
use Sjpereira\AzureStoragePhpSdk\Contracts\Authentication\Auth;
use Sjpereira\AzureStoragePhpSdk\Exceptions\RequestException;
use Sjpereira\AzureStoragePhpSdk\Http\{Headers};

final class MicrosoftEntraId implements Auth
{
    protected string $token = '';

    protected ?DateTime $tokenExpiresAt = null;

    public function __construct(
        protected string $account,
        protected string $directoryId,
        protected string $applicationId,
        protected string $applicationSecret,
    ) {
        //
    }

    public function getDate(): string
    {
        return gmdate('D, d M Y H:i:s T');
    }

    public function getAccount(): string
    {
        return $this->account;
    }

    public function getAuthentication(
        HttpVerb $verb,
        Headers $headers,
        string $resource,
    ): string {
        if (!empty($this->token) && $this->tokenExpiresAt > new DateTime()) {
            return $this->token;
        }

        $this->authenticate();

        return $this->token;
    }

    protected function authenticate(): void
    {
        try {
            $response = (new Client())->post("https://login.microsoftonline.com/{$this->directoryId}/oauth2/v2.0/token", [
                'form_params' => [
                    'grant_type'    => 'client_credentials',
                    'client_id'     => $this->applicationId,
                    'client_secret' => $this->applicationSecret,
                    'scope'         => 'https://storage.azure.com/.default',
                ],
            ]);
        } catch (RequestExceptionInterface $e) {
            throw RequestException::createFromRequestException($e);
        }

        /** @var array{token_type: string, expires_in: int, access_token: string} */
        $body = json_decode((string) $response->getBody(), true);

        $this->token = "{$body['token_type']} {$body['access_token']}";

        $this->tokenExpiresAt = (new DateTime())->modify("+{$body['expires_in']} seconds");
    }
}
