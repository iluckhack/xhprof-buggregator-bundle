<?php

declare(strict_types=1);

namespace Iluckhack\XhprofBuggregatorBundle\Tests\Integration;

use Iluckhack\XhprofBuggregatorBundle\Tests\Helper\MockResponseFactory;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\ApplicationTester;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpClient\MockHttpClient;

final class CommandTest extends KernelTestCase
{
    private const XHPROF_BUGGREGATOR_APP_NAME = 'Test App';
    private const XHPROF_BUGGREGATOR_ENDPOINT = 'http://127.0.0.1:1234/api/profiler/store';

    public function testSendProfilingResult(): void
    {
        (new Dotenv())->populate(
            values: [
                'XHPROF_BUGGREGATOR_APP_NAME' => self::XHPROF_BUGGREGATOR_APP_NAME,
                'XHPROF_BUGGREGATOR_ENDPOINT' => self::XHPROF_BUGGREGATOR_ENDPOINT,
                'XHPROF_BUGGREGATOR_CLI_ENABLED' => 'true',
            ],
            overrideExistingVars: true,
        );

        $application = new Application(self::bootKernel());
        $application->setAutoExit(false);

        $responseFactory = new MockResponseFactory();
        $this->getProfilerClient()->setResponseFactory($responseFactory);

        $tester = new ApplicationTester($application);
        $tester->run(['test']);

        $tester->assertCommandIsSuccessful();

        $this->assertEquals(1, $this->getProfilerClient()->getRequestsCount());

        $this->assertEquals(self::XHPROF_BUGGREGATOR_ENDPOINT, $responseFactory->calls[0]['url']);
        $this->assertEquals(
            self::XHPROF_BUGGREGATOR_APP_NAME,
            json_decode($responseFactory->calls[0]['options']['body'], flags: JSON_THROW_ON_ERROR)->app_name,
        );
    }

    public function testNotSendProfilingResult(): void
    {
        (new Dotenv())->populate(
            values: [
                'XHPROF_BUGGREGATOR_CLI_ENABLED' => 'false',
                'XHPROF_BUGGREGATOR_HTTP_ENABLED' => 'true',
            ],
            overrideExistingVars: true,
        );

        $application = new Application(self::bootKernel());
        $application->setAutoExit(false);

        $tester = new ApplicationTester($application);
        $tester->run(['test']);

        $tester->assertCommandIsSuccessful();

        $this->assertEquals(0, $this->getProfilerClient()->getRequestsCount());
    }

    private function getProfilerClient(): MockHttpClient
    {
        return self::getContainer()->get('xhprof_buggregator_bundle.profiler_storage_client');
    }
}