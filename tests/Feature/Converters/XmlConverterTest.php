<?php

declare(strict_types=1);

use Xray\AzureStoragePhpSdk\Contracts\Converter;
use Xray\AzureStoragePhpSdk\Converter\XmlConverter;
use Xray\AzureStoragePhpSdk\Exceptions\UnableToConvertException;

uses()->group('converters');

it('should implement Converter interface', function () {
    expect(XmlConverter::class)
        ->toImplement(Converter::class);
});

it('should throw unable to convert exception if the root tag is missing', function (array $source) {
    expect(new XmlConverter())
        ->convert($source)->toBeNull();
})->with([
    'With Root Tag'           => [[]],
    'With Multiple Root Tags' => [['test' => 1, 'test2' => 2]],
])->throws(UnableToConvertException::class, 'Unable to convert. The root tag is missing.');

it('should convert correctly to xml', function () {
    $xml = <<<XML
    <Body>
        <0>Test 1</0>
        <AnotherList>
            <Content>List Content</Content>
            <Type>List Type</Type>
        </AnotherList>
        <Created>true</Created>
        <Modified>false</Modified>
        <Content>Test Content &amp; Html Entities</Content>
    </Body>
    XML;

    $expected = "<?xml version=\"1.0\"?>\n"
        . preg_replace("/\n\s*/", '', $xml)
        . "\n";

    expect(new XmlConverter())
        ->convert([
            'Body' => [
                ['Test 1'],
                'AnotherList' => ['Content' => 'List Content', 'Type' => 'List Type'],
                'Created'     => true,
                'Modified'    => false,
                'Content'     => 'Test Content & Html Entities',
            ],
        ])->toBe($expected);
});
