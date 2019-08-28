<?php

namespace App\Application;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Twig
{
    /**
     * @var Environment
     */
    private $twig;

    public function __construct(array $globalVars)
    {
        $loader = new FilesystemLoader(__DIR__ . '/../Templates');
        $this->twig = new Environment($loader);

        foreach ($globalVars as $name => $value) {
            $this->twig->addGlobal($name, $value);
        }
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
