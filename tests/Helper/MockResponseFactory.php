<?php

declare(strict_types=1);

namespace Iluckhack\XhprofBuggregatorBundle\Tests\Helper;

use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class MockResponseFactory
{
    /**
     * @var array<array-key, array{
     *     method: string,
     *     url: string,
     *     options: array,
     * }>
     */
    public array $calls = [];

    public function __invoke(string $method, string $url, array $options): ResponseInterface
    {
        $this->calls[] = ['method' => $method, 'url' => $url, 'options' => $options];

        return new MockResponse();
    }
}