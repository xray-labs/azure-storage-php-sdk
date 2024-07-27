<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\BlobStorage\Entities\Container\AccessLevel;

use DateTimeImmutable;
use Xray\AzureStoragePhpSdk\Converter\XmlConverter;
use Xray\AzureStoragePhpSdk\Exceptions\RequiredFieldException;

final readonly class ContainerAccessLevel
{
    public string $id;

    public ?DateTimeImmutable $accessPolicyStart;

    public ?DateTimeImmutable $accessPolicyExpiry;

    public string $accessPolicyPermission;

    /**
     * @param array{
     *   Id: ?string,
     *   AccessPolicy: ?array{
     *     Start: string,
     *     Expiry: string,
     *     Permission: string
     *   }
     *  } $containerAccessLevel
     */
    public function __construct(array $containerAccessLevel)
    {
        $this->id = $containerAccessLevel['Id'] ?? '';

        if ($this->id === '') {
            throw RequiredFieldException::missingField('Id'); // @codeCoverageIgnore
        }

        $this->accessPolicyStart      = isset($containerAccessLevel['AccessPolicy']['Start']) ? new DateTimeImmutable($containerAccessLevel['AccessPolicy']['Start']) : null;
        $this->accessPolicyExpiry     = isset($containerAccessLevel['AccessPolicy']['Expiry']) ? new DateTimeImmutable($containerAccessLevel['AccessPolicy']['Expiry']) : null;
        $this->accessPolicyPermission = $containerAccessLevel['AccessPolicy']['Permission'] ?? '';
    }

    /**
     * @return array{
     *  Id: string,
     *  AccessPolicy: array{
     *    Start: ?string,
     *    Expiry: ?string,
     *    Permission: string
     *  }
     * }
     */
    public function toArray(): array
    {
        return [
            'Id'           => $this->id,
            'AccessPolicy' => [
                'Start'      => $this->accessPolicyStart?->format('Y-m-d'),
                'Expiry'     => $this->accessPolicyExpiry?->format('Y-m-d'),
                'Permission' => $this->accessPolicyPermission,
            ],
        ];
    }

    public function toXML(): string
    {
        return (new XmlConverter())->convert([
            'SignedIdentifiers' => ['SignedIdentifier' => $this->toArray()],
        ]);
    }
}
