<?php

namespace Iluckhack\XhprofBuggregatorBundle\Tests\Integration;

use Iluckhack\XhprofBuggregatorBundle\Tests\Helper\MockResponseFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpClient\MockHttpClient;

final class ControllerTest extends WebTestCase
{
    private const XHPROF_BUGGREGATOR_APP_NAME = 'The best ever app';
    private const XHPROF_BUGGREGATOR_ENDPOINT = 'http://127.0.0.1:3214/api/profiler/store';
    private const XHPROF_BUGGREGATOR_HTTP_ENABLED_HEADER = 'X-Xhprof-Enabled';

    /**
     * @dataProvider sendProfilingResultDataProvider
     */
    public function testSendProfilingResult(array $envVars, ?string $headerValue): void
    {
        (new Dotenv())->populate(
            values: [
                'XHPROF_BUGGREGATOR_APP_NAME' => self::XHPROF_BUGGREGATOR_APP_NAME,
                'XHPROF_BUGGREGATOR_ENDPOINT' => self::XHPROF_BUGGREGATOR_ENDPOINT,
            ] + $envVars,
            overrideExistingVars: true,
        );

        $client = self::createClient();

        $responseFactory = new MockResponseFactory();
        $this->getProfilerClient()->setResponseFactory($responseFactory);

        $client->request(
            method: 'GET',
            uri: '/test',
            server: array_filter([$this->getHeaderAsServerVar() => $headerValue]),
        );

        $this->assertResponseIsSuccessful();

        $this->assertEquals(1, $this->getProfilerClient()->getRequestsCount());

        $this->assertEquals(self::XHPROF_BUGGREGATOR_ENDPOINT, $responseFactory->calls[0]['url']);
        $this->assertEquals(
            self::XHPROF_BUGGREGATOR_APP_NAME,
            json_decode($responseFactory->calls[0]['options']['body'], flags: JSON_THROW_ON_ERROR)->app_name,
        );
    }

    /**
     * @dataProvider notSendProfilingResultDataProvider
     */
    public function testNotSendProfilingResult(array $envVars, ?string $headerValue): void
    {
        (new Dotenv())->populate(
            values: $envVars,
            overrideExistingVars: true,
        );

        $client = self::createClient();

        $client->request(
            method: 'GET',
            uri: '/test',
            server: array_filter([$this->getHeaderAsServerVar() => $headerValue]),
        );

        $this->assertResponseIsSuccessful();

        $this->assertEquals(0, $this->getProfilerClient()->getRequestsCount());
    }

    private function sendProfilingResultDataProvider(): iterable
    {
        yield [
            'envVars' => [
                'XHPROF_BUGGREGATOR_HTTP_ENABLED' => true,
                'XHPROF_BUGGREGATOR_HTTP_ENABLED_HEADER' => null,
            ],
            'headerValue' => null,
        ];

        yield [
            'envVars' => [
                'XHPROF_BUGGREGATOR_HTTP_ENABLED' => true,
                'XHPROF_BUGGREGATOR_HTTP_ENABLED_HEADER' => self::XHPROF_BUGGREGATOR_HTTP_ENABLED_HEADER,
            ],
            'headerValue' => null,
        ];

        yield [
            'envVars' => [
                'XHPROF_BUGGREGATOR_HTTP_ENABLED' => false,
                'XHPROF_BUGGREGATOR_HTTP_ENABLED_HEADER' => self::XHPROF_BUGGREGATOR_HTTP_ENABLED_HEADER,
            ],
            'headerValue' => 'true',
        ];
    }

    private function notSendProfilingResultDataProvider(): iterable
    {
        yield [
            'envVars' => [
                'XHPROF_BUGGREGATOR_HTTP_ENABLED' => false,
                'XHPROF_BUGGREGATOR_HTTP_ENABLED_HEADER' => null,
            ],
            'headerValue' => null,
        ];

        yield [
            'envVars' => [
                'XHPROF_BUGGREGATOR_HTTP_ENABLED' => true,
                'XHPROF_BUGGREGATOR_HTTP_ENABLED_HEADER' => self::XHPROF_BUGGREGATOR_HTTP_ENABLED_HEADER,
            ],
            'headerValue' => 'false',
        ];
    }

    private function getProfilerClient(): MockHttpClient
    {
        return self::getContainer()->get('xhprof_buggregator_bundle.profiler_storage_client');
    }

    private function getHeaderAsServerVar(): string
    {
        return sprintf(
            'HTTP_%s',
            strtoupper(
                str_replace(
                    search: '-',
                    replace: '_',
                    subject: self::XHPROF_BUGGREGATOR_HTTP_ENABLED_HEADER,
                ),
            ),
        );
    }
}