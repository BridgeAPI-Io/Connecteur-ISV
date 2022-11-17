<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitea87fccd138ad27a53e9b7be62f7042c
{
    public static $prefixLengthsPsr4 = array (
        'B' => 
        array (
            'BridgeApi\\' => 10,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'BridgeApi\\' => 
        array (
            0 => __DIR__ . '/../..' . '/includes',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitea87fccd138ad27a53e9b7be62f7042c::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitea87fccd138ad27a53e9b7be62f7042c::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitea87fccd138ad27a53e9b7be62f7042c::$classMap;

        }, null, ClassLoader::class);
    }
}
