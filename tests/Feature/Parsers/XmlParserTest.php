<?php

declare(strict_types=1);

use Xray\AzureStoragePhpSdk\Contracts\Parser;
use Xray\AzureStoragePhpSdk\Exceptions\UnableToParseException;
use Xray\AzureStoragePhpSdk\Parsers\XmlParser;

uses()->group('parsers');

it('should implement Parser interface', function () {
    expect(XmlParser::class)
        ->toImplement(Parser::class);
});

it('should throw an exception if is unable to parse', function () {
    expect(new XmlParser())
        ->parse('')->toBeNull();
})->throws(UnableToParseException::class);

it('should parse xml correctly', function () {
    $xml = <<<XML
    <?xml version="1.0"?>
    <Request>
        <Method>GET</Method>
        <Headers>
            <Header name="Content-Type">application/xml</Header>
            <Header name="Accept">application/xml</Header>
            <Header name="Status">200</Header>
        </Headers>
        <Body>
            <Content type="text/plain" value="test" />
        </Body>
    </Request>
    XML;

    expect(new XmlParser())
        ->parse($xml)
        ->toEqual([
            'Method'  => 'GET',
            'Headers' => [
                'Header' => ['application/xml', 'application/xml', '200'],
            ],
            'Body' => [
                'Content' => [
                    '@attributes' => [
                        'type'  => 'text/plain',
                        'value' => 'test',
                    ],
                ],
            ],
        ]);
});
