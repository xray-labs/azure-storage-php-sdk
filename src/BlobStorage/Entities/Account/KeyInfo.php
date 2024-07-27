<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\BlobStorage\Entities\Account;

use DateTimeImmutable;
use Xray\AzureStoragePhpSdk\Contracts\{Arrayable, Xmlable};
use Xray\AzureStoragePhpSdk\Converter\XmlConverter;
use Xray\AzureStoragePhpSdk\Exceptions\RequiredFieldException;

/** @implements Arrayable<array{KeyInfo: array{Start: string, Expiry: string}}> */
final readonly class KeyInfo implements Arrayable, Xmlable
{
    public DateTimeImmutable $start;

    public DateTimeImmutable $expiry;

    /**
     * @param array{Start?: string, Expiry?: string} $keyInfo
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

        $this->start  = new DateTimeImmutable($keyInfo['Start']);
        $this->expiry = new DateTimeImmutable($keyInfo['Expiry']);
    }

    public function toArray(): array
    {
        return [
            'KeyInfo' => [
                'Start'  => $this->start->format(DateTimeImmutable::ATOM),
                'Expiry' => $this->expiry->format(DateTimeImmutable::ATOM),
            ],
        ];
    }

    public function toXml(): string
    {
        return (new XmlConverter())->convert($this->toArray());
    }
}
