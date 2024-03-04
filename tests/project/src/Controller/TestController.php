<?php

declare(strict_types=1);

namespace Iluckhack\XhprofBuggregatorBundle\Tests\App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/test', methods: ['GET'])]
final class TestController extends AbstractController
{
    public function __invoke(): JsonResponse
    {
        return $this->json(null);
    }
}