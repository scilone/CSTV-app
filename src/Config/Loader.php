<?php

namespace App\Config;

class Loader
{
    const SERVICES = [
        Service::CONTROLLER_TEST => [
            Service::APPLICATION_TWIG,
            Service::APPLICATION_CACHE_RAW,
            Param::HELLO_WORLD,
            Service::INFRASTRUCTURE_SUPERGLOBALES
        ],
        Service::CONTROLLER_HOME => [
            Service::APPLICATION_TWIG,
        ],
        Service::CONTROLLER_CATEGORY => [
            Service::APPLICATION_TWIG,
            Service::APPLICATION_IPTV,
            Service::APPLICATION_CACHE_RAW,
            Service::INFRASTRUCTURE_SUPERGLOBALES,
        ],
        Service::CONTROLLER_STREAM => [
            Service::APPLICATION_TWIG,
            Service::APPLICATION_IPTV,
            Service::APPLICATION_CACHE_RAW,
            Service::INFRASTRUCTURE_SUPERGLOBALES,
        ],

        Service::APPLICATION_IPTV => [
            Service::INFRASTRUCTURE_CURL,
            Service::INFRASTRUCTURE_SUPERGLOBALES,
        ],
        Service::APPLICATION_CACHE_RAW => [
            Service::INFRASTRUCTURE_SUPERGLOBALES,
        ],

        Service::INFRASTRUCTURE_CACHE_RAW => [
            Service::INFRASTRUCTURE_SUPERGLOBALES,
        ],
    ];

    const NO_LAZY_LOADING = [
        Service::CONTROLLER_TEST,
    ];

    /**
     * @var array
     */
    private static $services = [];

    /**
     * @param string $fqcn
     *
     * @throws \ReflectionException
     * @return object
     */
    public static function getService(string $fqcn)
    {
        if (isset(self::$services[$fqcn])) {
            return self::$services[$fqcn];
        }

        if (class_exists($fqcn) === false) {
            return null;
        }

        $objectReflection = new \ReflectionClass($fqcn);

        $args = [];

        if (isset(self::SERVICES[$fqcn])) {
            foreach (self::SERVICES[$fqcn] as $key => $argument) {
                if (class_exists($argument) === true) {
                    $args[$key] = self::initClass($argument);
                } else {
                    $args[$key] = $argument;
                }
            }
        }

        if (in_array($fqcn, self::NO_LAZY_LOADING) === false) {
            self::$services[$fqcn] = $objectReflection->newInstanceArgs($args);

            return self::$services[$fqcn];
        }

        return $objectReflection->newInstanceArgs($args);
    }

    /**
     * @param $class
     *
     * @throws \ReflectionException
     * @return object
     */
    private static function initClass(string $class)
    {
        if (isset(self::$services[$class])) {
            return self::$services[$class];
        }

        $args = [];

        if (isset(self::SERVICES[$class])) {
            foreach (self::SERVICES[$class] as $key => $argument) {
                if (class_exists($argument) === true) {
                    $args[$key] = self::initClass($argument);
                } else {
                    $args[$key] = $argument;
                }
            }
        }

        $objectReflection = new \ReflectionClass((string) $class);

        if (in_array($class, self::NO_LAZY_LOADING) === false) {
            self::$services[$class] = $objectReflection->newInstanceArgs($args);

            return self::$services[$class];
        }

        return $objectReflection->newInstanceArgs($args);
    }
}
