<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\Authentication;

use DateTime;
use GuzzleHttp\{Client, ClientInterface};
use Psr\Http\Client\RequestExceptionInterface;
use Xray\AzureStoragePhpSdk\BlobStorage\Enums\HttpVerb;
use Xray\AzureStoragePhpSdk\Concerns\UseCurrentHttpDate;
use Xray\AzureStoragePhpSdk\Contracts\Authentication\Auth;
use Xray\AzureStoragePhpSdk\Contracts\Http\Request;
use Xray\AzureStoragePhpSdk\Exceptions\RequestException;

final class MicrosoftEntraId implements Auth
{
    use UseCurrentHttpDate;

    protected ?ClientInterface $client = null;

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

    public function withRequestClient(ClientInterface $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getAccount(): string
    {
        return $this->account;
    }

    public function getAuthentication(Request $request): string
    {
        if (!empty($this->token) && $this->tokenExpiresAt && $this->tokenExpiresAt > new DateTime()) {
            return $this->token;
        }

        $this->authenticate();

        return $this->token;
    }

    protected function authenticate(): void
    {
        try {
            $uri      = "https://login.microsoftonline.com/{$this->directoryId}/oauth2/v2.0/token";
            $httpVerb = HttpVerb::POST;

            $response = $this->getRequestClient()->request($httpVerb->value, $uri, [
                'form_params' => [
                    'grant_type'    => 'client_credentials',
                    'client_id'     => $this->applicationId,
                    'client_secret' => $this->applicationSecret,
                    'scope'         => 'https://storage.azure.com/.default',
                ],
            ]);

            // @codeCoverageIgnoreStart
        } catch (RequestExceptionInterface $e) {
            throw RequestException::createFromRequestException($e);
        }
        // @codeCoverageIgnoreEnd

        /** @var array{token_type: string, expires_in: int, access_token: string} $body */
        $body = json_decode((string) $response->getBody(), true);

        $this->token          = "{$body['token_type']} {$body['access_token']}";
        $this->tokenExpiresAt = (new DateTime())->modify("+{$body['expires_in']} seconds");
    }

    protected function getRequestClient(): ClientInterface
    {
        if (!isset($this->client)) {
            $this->client = azure_app(Client::class); // @codeCoverageIgnore
        }

        return $this->client;
    }
}
