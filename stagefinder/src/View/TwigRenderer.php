<?php
declare(strict_types=1);
namespace App\View;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

class TwigRenderer
{
    private static ?Environment $twig = null;

    private static function init(): void
    {
        if (self::$twig !== null) return;

        $loader = new FilesystemLoader(__DIR__ . '/../../templates');
        self::$twig = new Environment($loader, [
            'cache' => false, // mettre __DIR__.'/../../cache' en prod
            'debug' => true,
            'autoescape' => 'html',
        ]);

        // Globals
        self::$twig->addGlobal('session', $_SESSION ?? []);
        self::$twig->addGlobal('app_name', 'StageFinder');

        // Functions utiles
        self::$twig->addFunction(new TwigFunction('asset', function(string $path): string {
            return '/public/' . ltrim($path, '/');
        }));
        self::$twig->addFunction(new TwigFunction('url', function(string $path): string {
            return '/' . ltrim($path, '/');
        }));
    }

    public static function render(string $template, array $data = []): void
    {
        self::init();
        echo self::$twig->render($template, $data);
    }
}
