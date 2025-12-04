<?php

namespace App\Shared\Infrastructure\Service;

/**
 * @author Wilhelm Zwertvaegher
 */


class FrontendUrlGenerator
{

    public function __construct(private readonly array $frontends, private readonly array $routes)
    {
    }

    /**
     * Generates full URL
     *
     * @param string $frontend ex: 'app' or 'admin'
     * @param string $routeName ex: 'confirm_identity'
     * @param array $params ex: ['id' => 123]
     * @param array $queryParams ex: ['utm' => 'mail']
     */
    public function generate(string $frontend, string $routeName, array $params = [], array $queryParams = []): string
    {
        if (!isset($this->frontends[$frontend])) {
            throw new \InvalidArgumentException(sprintf('Unknown frontend "%s".', $frontend));
        }

        if (!isset($this->routes[$frontend][$routeName])) {
            throw new \InvalidArgumentException(sprintf('Unknown route "%s" for frontend "%s".', $routeName, $frontend));
        }

        $pathTemplate = $this->routes[$frontend][$routeName];

        // params :token ou :id
        $urlPath = preg_replace_callback('/:(\w+)/', function ($m) use ($params, $routeName) {
            $key = $m[1];
            if (!array_key_exists($key, $params)) {
                throw new \InvalidArgumentException(sprintf('Missing parameter "%s" for route "%s".', $key, $routeName));
            }
            return rawurlencode((string)$params[$key]);
        }, $pathTemplate);

        // assemble base + path
        $base = rtrim($this->frontends[$frontend], '/');
        $url = $base . $urlPath;

        if (!empty($queryParams)) {
            $url .= '?' . http_build_query($queryParams);
        }

        return $url;
    }
}
