<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInitea87fccd138ad27a53e9b7be62f7042c
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        spl_autoload_register(array('ComposerAutoloaderInitea87fccd138ad27a53e9b7be62f7042c', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInitea87fccd138ad27a53e9b7be62f7042c', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInitea87fccd138ad27a53e9b7be62f7042c::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}
