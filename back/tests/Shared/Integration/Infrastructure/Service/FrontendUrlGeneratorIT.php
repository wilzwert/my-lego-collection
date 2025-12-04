<?php

namespace App\Tests\Shared\Integration\Infrastructure\Service;

use App\Shared\Infrastructure\Service\FrontendUrlGenerator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @author Wilhelm Zwertvaegher
 */
class FrontendUrlGeneratorIT extends KernelTestCase
{
    private FrontendUrlGenerator $underTest;

    private string $frontendAppBaseUrl;

    protected function setUp(): void
    {
        parent::setUp();
        $container = self::getContainer();
        $this->underTest = $container->get(FrontendUrlGenerator::class);
        $frontends = $container->getParameter('frontends');
        $this->frontendAppBaseUrl = $frontends['app'];
    }

    public static function existingRoutesProvider(): array
    {

        return [
            ['app', 'home', [], [], ''],
            ['app', 'confirm_identity', ['token' => 'validation-token'], [], '/account/confirm/validation-token'],
            ['app', 'account', [], [], '/account'],
            ['app', 'collection', [], ['filter' => 'owned'], '/collection?filter=owned'],
        ];
    }

    #[Test]
    #[DataProvider('existingRoutesProvider')]
    public function shouldGenerateUrl(string $frontend, string $routeName, array $params, array $queryParams, string $expectedUri): void
    {
        self::assertEquals($this->frontendAppBaseUrl.$expectedUri, $this->underTest->generate($frontend, $routeName, $params, $queryParams));
    }

    #[Test]
    public function whenUnknownFrontend_thenShouldThrowInvalidArgumentException(): void
    {
        self::expectException(\InvalidArgumentException::class);
        $this->underTest->generate('unknown', 'home');
    }

    #[Test]
    public function whenUnknownRoute_thenShouldThrowInvalidArgumentException(): void
    {
        self::expectException(\InvalidArgumentException::class);
        $this->underTest->generate('app', 'unknown');
    }



}
