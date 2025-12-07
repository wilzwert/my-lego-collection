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
    public function __construct(private readonly FrontendUrlGenerator $generator)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('frontend_url', [$this, 'url'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * @param string $frontend
     * @param string $routeName
     * @param array<string, string|int> $params
     * @param array<string, string|int> $queryParams
     * @return string
     */
    public function url(string $frontend, string $routeName, array $params = [], array $queryParams = []): string
    {
        return $this->generator->generate($frontend, $routeName, $params, $queryParams);
    }

    /**
     * @param string $frontend
     * @param string $routeName
     * @param array<string, string|int> $params
     * @param array<string, string|int> $queryParams
     * @return string
     */
    public function path(string $frontend, string $routeName, array $params = [], array $queryParams = []): string
    {
        $url = $this->generator->generate($frontend, $routeName, $params, $queryParams);
        return parse_url($url, PHP_URL_PATH) . (parse_url($url, PHP_URL_QUERY) ? '?' . parse_url($url, PHP_URL_QUERY) : '');
    }
}
