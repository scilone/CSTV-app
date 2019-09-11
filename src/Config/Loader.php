<?php

namespace App\Config;

class Loader
{
    const SERVICES = [
        Service::CONTROLLER_TEST => [
            Service::APPLICATION_TWIG,
            Service::INFRASTRUCTURE_CACHE_RAW,
            Param::HELLO_WORLD,
            Service::INFRASTRUCTURE_SUPERGLOBALES
        ],
        Service::CONTROLLER_HOME => [
            Service::APPLICATION_TWIG,
            Service::INFRASTRUCTURE_SUPERGLOBALES,
            Service::APPLICATION_IPTV,
        ],
        Service::CONTROLLER_ACCOUNT => [
            Service::APPLICATION_TWIG,
            Service::INFRASTRUCTURE_SUPERGLOBALES,
            Service::APPLICATION_ACCOUNT,
        ],
        Service::CONTROLLER_CACHE => [
            Service::INFRASTRUCTURE_SUPERGLOBALES,
        ],
        Service::CONTROLLER_EXTRA => [
            Service::APPLICATION_TWIG,
        ],
        Service::CONTROLLER_CATEGORY => [
            Service::APPLICATION_TWIG,
            Service::APPLICATION_IPTV,
            Service::INFRASTRUCTURE_CACHE_RAW,
            Service::INFRASTRUCTURE_SUPERGLOBALES,
            Service::APPLICATION_ACCOUNT,
        ],
        Service::CONTROLLER_STREAM => [
            Service::APPLICATION_TWIG,
            Service::APPLICATION_IPTV,
            Service::INFRASTRUCTURE_CACHE_RAW,
            Service::INFRASTRUCTURE_SUPERGLOBALES,
            Service::APPLICATION_ACCOUNT,
        ],

        Service::APPLICATION_TWIG => [
            Service::INFRASTRUCTURE_SUPERGLOBALES,
            Param::TWIG_GLOBAL_VARS,
        ],
        Service::APPLICATION_IPTV => [
            Service::DOMAIN_IPTV_XCODE_API,
            Service::INFRASTRUCTURE_SUPERGLOBALES,
            Service::INFRASTRUCTURE_CACHE_ITEM
        ],
        Service::APPLICATION_ACCOUNT => [
            Service::INFRASTRUCTURE_SODIUM,
            Service::INFRASTRUCTURE_SUPERGLOBALES,
            Service::DOMAIN_REPOSITORY_ACCOUNT
        ],

        Service::DOMAIN_IPTV_XCODE_API => [
            Service::INFRASTRUCTURE_CURL,
            Service::INFRASTRUCTURE_CACHE_RAW
        ],
        Service::DOMAIN_REPOSITORY_ACCOUNT => [
            Service::INFRASTRUCTURE_MYSQL
        ],


        Service::INFRASTRUCTURE_CACHE_RAW => [
            Service::INFRASTRUCTURE_SUPERGLOBALES,
            Param::PREFIX_CACHE
        ],
        Service::INFRASTRUCTURE_CACHE_ITEM => [
            Service::INFRASTRUCTURE_CACHE_RAW,
            Service::INFRASTRUCTURE_SUPERGLOBALES,
        ],
        Service::INFRASTRUCTURE_MYSQL => [
            SecretParam::DB_HOST,
            SecretParam::DB_USERNAME,
            SecretParam::DB_PASSWORD,
            SecretParam::DB_DATABASE,
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
                if (is_string($argument) && class_exists($argument) === true) {
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
