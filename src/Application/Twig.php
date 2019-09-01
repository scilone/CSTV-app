<?php

namespace App\Application;

use App\Infrastructure\SuperglobalesOO;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Twig
{
    /**
     * @var Environment
     */
    private $twig;

    public function __construct(SuperglobalesOO $superglobalesOO, array $globalVars)
    {
        $loader = new FilesystemLoader(__DIR__ . '/../Templates');
        $this->twig = new Environment($loader);

        foreach ($globalVars as $name => $value) {
            $this->twig->addGlobal($name, $value);
        }

        $userAgent = $superglobalesOO->getServer()->get('HTTP_USER_AGENT');
        $this->twig->addGlobal(
            'isIos',
            stripos($userAgent,'iPod') !== false
            || stripos($userAgent,'iPad') !== false
            || stripos($userAgent,'iPhone') !== false
        );

        $this->twig->addGlobal('isAndroid', stripos($userAgent,'Android') !== false);
    }

    /**
     * @param string $name
     * @param array  $context
     *
     * @return string
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @throws \Twig\Error\LoaderError
     */
    public function render(string $name, array $context = []): string
    {
        return $this->twig->render($name, $context);
    }
}
