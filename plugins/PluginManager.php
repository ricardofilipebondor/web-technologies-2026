<?php

class PluginManager
{
    private static array $plugins = [];

    public static function load(): void
    {
        require_once __DIR__ . '/PluginInterface.php';
        foreach (glob(__DIR__ . '/modules/*.php') as $file) {
            require_once $file;
            $class = basename($file, '.php');
            if (class_exists($class) && is_subclass_of($class, 'PluginInterface')) {
                self::$plugins[] = new $class();
            }
        }
    }

    public static function all(): array
    {
        if (empty(self::$plugins)) {
            self::load();
        }
        return self::$plugins;
    }

    public static function getMenuItems(): array
    {
        $items = [];
        foreach (self::all() as $plugin) {
            $items[] = [
                'module' => $plugin->getId(),
                'route' => $plugin->getDefaultRoute(),
                'label' => $plugin->getMenuLabel(),
            ];
        }
        return $items;
    }

    public static function findByService(string $serviceName): ?PluginInterface
    {
        foreach (self::all() as $plugin) {
            if ($plugin->getServiceName() === $serviceName) {
                return $plugin;
            }
        }
        return null;
    }
}
