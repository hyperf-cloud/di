<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace Hyperf\Di\Annotation;

use Hyperf\Di\MetadataCollector;

class AnnotationCollector extends MetadataCollector
{
    /**
     * @var array
     */
    protected static $container = [];

    public static function collectClass(string $class, string $annotation, $value): void
    {
        static::$container[$class]['_c'][$annotation] = $value;
    }

    public static function collectProperty(string $class, string $property, string $annotation, $value): void
    {
        static::$container[$class]['_p'][$property][$annotation] = $value;
    }

    public static function collectMethod(string $class, string $method, string $annotation, $value): void
    {
        static::$container[$class]['_m'][$method][$annotation] = $value;
    }

    public static function clear(?string $key = null): void
    {
        if ($key) {
            static::$container[$key] = [];
        } else {
            static::$container = [];
        }
    }

    /**
     * @deprecated v3.0
     */
    public static function getClassByAnnotation(string $annotation): array
    {
        return self::getClassesByAnnotation($annotation);
    }

    public static function getClassesByAnnotation(string $annotation)
    {
        $result = [];
        foreach (static::$container as $class => $metadata) {
            if (! isset($metadata['_c'][$annotation])) {
                continue;
            }
            $result[$class] = $metadata['_c'][$annotation];
        }
        return $result;
    }

    /**
     * @deprecated v3.0
     */
    public static function getMethodByAnnotation(string $annotation): array
    {
        return static::getMethodsByAnnotation($annotation);
    }

    public static function getMethodsByAnnotation(string $annotation): array
    {
        $result = [];
        foreach (static::$container as $class => $metadata) {
            foreach ($metadata['_m'] ?? [] as $method => $_metadata) {
                if ($value = $_metadata[$annotation] ?? null) {
                    $result[] = ['class' => $class, 'method' => $method, 'annotation' => $value];
                }
            }
        }
        return $result;
    }

    public static function getPropertiesByAnnotation(string $annotation): array
    {
        $properties = [];
        foreach (static::$container as $class => $metadata) {
            foreach ($metadata['_p'] ?? [] as $property => $_metadata) {
                if ($value = $_metadata[$annotation] ?? null) {
                    $properties[] = ['class' => $class, 'property' => $property, 'annotation' => $value];
                }
            }
        }
        return $properties;
    }

    public static function getClassAnnotation(string $class, string $annotation)
    {
        return static::get($class . '._c.' . $annotation);
    }

    public static function getClassMethodAnnotation(string $class, string $method)
    {
        return static::get($class . '._m.' . $method);
    }

    public static function getClassPropertyAnnotation(string $class, string $property)
    {
        return static::get($class . '._p.' . $property);
    }

    public static function getContainer(): array
    {
        return static::$container;
    }
}
