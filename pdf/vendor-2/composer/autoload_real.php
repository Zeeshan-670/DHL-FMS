<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInitf6cf02a5a2d94624ba32542ab2664116
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

        spl_autoload_register(array('ComposerAutoloaderInitf6cf02a5a2d94624ba32542ab2664116', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInitf6cf02a5a2d94624ba32542ab2664116', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInitf6cf02a5a2d94624ba32542ab2664116::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}
