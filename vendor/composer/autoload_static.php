<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitf905025f2bcf178b1bdc2d876e971647
{
    public static $files = array (
        '6e60481d8c04e99474e2ba7b3658ab5a' => __DIR__ . '/..' . '/php-activerecord/php-activerecord/ActiveRecord.php',
    );

    public static $prefixLengthsPsr4 = array (
        'L' => 
        array (
            'Libs\\' => 5,
        ),
        'A' => 
        array (
            'App\\Views\\' => 10,
            'App\\Models\\' => 11,
            'App\\Controllers\\' => 16,
            'App\\Components\\' => 15,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Libs\\' => 
        array (
            0 => __DIR__ . '/..' . '/libs',
        ),
        'App\\Views\\' => 
        array (
            0 => __DIR__ . '/../..' . '/views',
        ),
        'App\\Models\\' => 
        array (
            0 => __DIR__ . '/../..' . '/models',
        ),
        'App\\Controllers\\' => 
        array (
            0 => __DIR__ . '/../..' . '/controllers',
        ),
        'App\\Components\\' => 
        array (
            0 => __DIR__ . '/../..' . '/components',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitf905025f2bcf178b1bdc2d876e971647::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitf905025f2bcf178b1bdc2d876e971647::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
