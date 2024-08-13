<?php

declare(strict_types=1);

namespace Projom\Http\Api;

use Projom\Util\Template;

class Pattern
{
    const PARAM_PATTERNS = [
        'id' => [
            'parameter' => '/{id}/',
            'pattern' => '([1-9][0-9]+|[1-9]+)'
        ],
        'name' => [
            'parameter' => '/{name}/',
            'pattern' => '([a-zA-Z,]+)'
        ],
        'bool' => [
            'parameter' => '/{bool}/',
            'pattern' => '(true|false)'
        ]
    ];

    public static function build(string $route): string
    {
        $routePattern = $route;
        foreach (static::PARAM_PATTERNS as $paramData)
            $routePattern = preg_replace($paramData['parameter'], $paramData['pattern'], $routePattern);

        $routePattern = preg_replace('/\//', '\/', $routePattern);

        return static::finalize($routePattern);
    }

    public static function finalize(string $pattern): string
    {
        if (!$pattern)
            return '';

        $template = '/^{{pattern}}$/';
        $vars = ['pattern' => $pattern];
        
        return Template::bind($template, $vars);
    }

    public static function fromType(string $type): string
    {
        $pattern = '';
        switch ($type) {

            case 'id':
            case 'integer':
                $pattern = static::PARAM_PATTERNS['id']['pattern'];
                break;

            case 'name':
            case 'string':
                $pattern = static::PARAM_PATTERNS['name']['pattern'];
                break;

            case 'bool':
                $pattern = static::PARAM_PATTERNS['bool']['pattern'];
                break;
            default:
                return '';
        }

        return static::finalize($pattern);
    }

    public static function test(string $pattern, string $subject): bool
    {
        if (!$pattern || !$subject)
            return false;
        return preg_match($pattern, $subject) === 1;
    }
}
