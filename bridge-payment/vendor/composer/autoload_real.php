<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInitea7e134545e4ac567f1827153ac7f8f6
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

        spl_autoload_register(array('ComposerAutoloaderInitea7e134545e4ac567f1827153ac7f8f6', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInitea7e134545e4ac567f1827153ac7f8f6', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInitea7e134545e4ac567f1827153ac7f8f6::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}