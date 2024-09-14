<?php

declare(strict_types=1);

use Xray\AzureStoragePhpSdk\BlobStorage\Entities\Account\BlobStorageProperty\Cors\Cors;
use Xray\AzureStoragePhpSdk\BlobStorage\Entities\Account\BlobStorageProperty\{BlobProperty, DeleteRetentionPolicy, HourMetrics, Logging, MinuteMetrics, StaticWebsite};
use Xray\AzureStoragePhpSdk\BlobStorage\Managers\Account\StoragePropertyManager;
use Xray\AzureStoragePhpSdk\BlobStorage\Resource;
use Xray\AzureStoragePhpSdk\Http\Response as BaseResponse;
use Xray\AzureStoragePhpSdk\Tests\Http\{RequestFake, ResponseFake};

pest()->group('blob-storage', 'managers', 'account');
covers(StoragePropertyManager::class);

it('should get the blob property', function () {
    $body = <<<XML
    <?xml version="1.0" encoding="utf-8"?>
    <StorageServiceProperties>
        <DefaultServiceVersion>2019-02-02</DefaultServiceVersion>
        <Logging>
            <Read>false</Read>
            <RetentionPolicy>
                <Enabled>false</Enabled>
                <Days>7</Days>
            </RetentionPolicy>
        </Logging>
        <HourMetrics>
            <RetentionPolicy>
                <Enabled>false</Enabled>
                <Days>7</Days>
            </RetentionPolicy>
        </HourMetrics>
        <MinuteMetrics>
            <RetentionPolicy>
                <Enabled>false</Enabled>
                <Days>7</Days>
            </RetentionPolicy>
        </MinuteMetrics>
        <Cors>
            <CorsRule>
                <AllowedOrigins>*</AllowedOrigins>
                <AllowedMethods>GET</AllowedMethods>
                <AllowedHeaders>*</AllowedHeaders>
                <ExposedHeaders>*</ExposedHeaders>
                <MaxAgeInSeconds>60</MaxAgeInSeconds>
            </CorsRule>
        </Cors>
        <DeleteRetentionPolicy>
            <Enabled>false</Enabled>
            <Days>7</Days>
        </DeleteRetentionPolicy>
        <StaticWebsite>
            <Enabled>false</Enabled>
        </StaticWebsite>
    </StorageServiceProperties>
    XML;

    $request = (new RequestFake())
        ->withFakeResponse(new ResponseFake($body));

    $response = (new StoragePropertyManager($request))->get();

    expect($response)
        ->toBeInstanceOf(BlobProperty::class)
        ->and($response->logging)
        ->toBeInstanceOf(Logging::class)
        ->and($response->hourMetrics)
        ->toBeInstanceOf(HourMetrics::class)
        ->and($response->minuteMetrics)
        ->toBeInstanceOf(MinuteMetrics::class)
        ->and($response->cors)
        ->toBeInstanceOf(Cors::class)
        ->and($response->deleteRetentionPolicy)
        ->toBeInstanceOf(DeleteRetentionPolicy::class)
        ->and($response->staticWebsite)
        ->toBeInstanceOf(StaticWebsite::class);
});

it('should save the blob property', function () {
    $request = (new RequestFake())
        ->withFakeResponse(new ResponseFake(statusCode: BaseResponse::STATUS_NO_CONTENT));

    // @phpstan-ignore-next-line
    $blobProperty = new BlobProperty([
        'Logging' => [
            'Read'            => false,
            'RetentionPolicy' => [
                'Enabled' => false,
                'Days'    => 7,
            ],
        ],
        'HourMetrics' => [
            'RetentionPolicy' => [
                'Enabled' => false,
                'Days'    => 7,
            ],
        ],
        'MinuteMetrics' => [
            'RetentionPolicy' => [
                'Enabled' => false,
                'Days'    => 7,
            ],
        ],
        'Cors' => [
            'CorsRule' => [
                'AllowedOrigins'  => '*',
                'AllowedMethods'  => 'GET',
                'MaxAgeInSeconds' => null,
                'ExposedHeaders'  => '',
                'AllowedHeaders'  => '',
            ],
        ],
        'DeleteRetentionPolicy' => [
            'Enabled' => false,
            'Days'    => 7,
        ],
        'StaticWebsite' => ['Enabled' => false],
    ]);

    (new StoragePropertyManager($request))->save($blobProperty, ['test' => 'test']);

    $request->assertPut('?comp=properties&restype=service', $blobProperty->toXml())
        ->assertSentWithOptions(['test' => 'test'])
        ->assertSentWithHeaders([Resource::CONTENT_TYPE => 'application/xml']);
});
