<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\BlobStorage\Entities\Account;

use DateTimeImmutable;
use DateTimeInterface;
use Xray\AzureStoragePhpSdk\Contracts\{Arrayable, Xmlable};
use Xray\AzureStoragePhpSdk\Converter\XmlConverter;
use Xray\AzureStoragePhpSdk\Exceptions\RequiredFieldException;

/** @implements Arrayable<array{KeyInfo: array{Start: string, Expiry: string}}> */
final readonly class KeyInfo implements Arrayable, Xmlable
{
    public DateTimeInterface $start;

    public DateTimeInterface $expiry;

    /**
     * @param array{Start?: string|DateTimeInterface, Expiry?: string|DateTimeInterface} $keyInfo
     *
     * @throws RequiredFieldException
     */
    public function __construct(array $keyInfo)
    {
        // @codeCoverageIgnoreStart
        if (!isset($keyInfo['Start'], $keyInfo['Expiry'])) {
            throw RequiredFieldException::missingField(
                !isset($keyInfo['Start']) ? 'Start' : 'Expiry'
            );
        }
        // @codeCoverageIgnoreEnd

        $this->start = $keyInfo['Start'] instanceof DateTimeInterface
            ? $keyInfo['Start']
            : new DateTimeImmutable($keyInfo['Start']);

        $this->expiry = $keyInfo['Expiry'] instanceof DateTimeInterface
            ? $keyInfo['Expiry']
            : new DateTimeImmutable($keyInfo['Expiry']);
    }

    public function toArray(): array
    {
        return [
            'KeyInfo' => [
                'Start'  => convert_to_ISO($this->start), // @phpstan-ignore-line
                'Expiry' => convert_to_ISO($this->expiry), // @phpstan-ignore-line
            ],
        ];
    }

    public function toXml(): string
    {
        return azure_app(XmlConverter::class)->convert($this->toArray());
    }
}
