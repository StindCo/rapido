<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit1a971a1afb68bb5fbface89a7b030a79
{
    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'StindCo\\Rapido\\' => 15,
        ),
        'D' => 
        array (
            'Doctrine\\Common\\Cache\\' => 22,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'StindCo\\Rapido\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
        'Doctrine\\Common\\Cache\\' => 
        array (
            0 => __DIR__ . '/..' . '/doctrine/cache/lib/Doctrine/Common/Cache',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit1a971a1afb68bb5fbface89a7b030a79::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit1a971a1afb68bb5fbface89a7b030a79::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit1a971a1afb68bb5fbface89a7b030a79::$classMap;

        }, null, ClassLoader::class);
    }
}
