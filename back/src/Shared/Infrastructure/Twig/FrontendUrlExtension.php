<?php

namespace App\Shared\Infrastructure\Twig;

/**
 * @author Wilhelm Zwertvaegher
 */

use App\Shared\Infrastructure\Service\FrontendUrlGenerator;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FrontendUrlExtension extends AbstractExtension
{
    public function __construct(private FrontendUrlGenerator $generator)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('frontend_url', [$this, 'url'], ['is_safe' => ['html']]),
        ];
    }

    public function url(string $frontend, string $routeName, array $params = [], array $queryParams = []): string
    {
        return $this->generator->generate($frontend, $routeName, $params, $queryParams);
    }

    // path sans host (utile si tu veux juste le path)
    public function path(string $frontend, string $routeName, array $params = [], array $queryParams = []): string
    {
        $url = $this->generator->generate($frontend, $routeName, $params, $queryParams);
        return (string) parse_url($url, PHP_URL_PATH) . (parse_url($url, PHP_URL_QUERY) ? '?' . parse_url($url, PHP_URL_QUERY) : '');
    }
}
