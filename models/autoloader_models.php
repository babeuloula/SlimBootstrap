<?php

    Autoloader_Models::Register();

    class Autoloader_Models {

        public static function Register () {
            if (function_exists('__autoload')) {
                spl_autoload_register('__autoload');
            }

            return spl_autoload_register(array('Autoloader_Models', 'Load'));
        }

        public static function Load ($className) {
            if (class_exists($className, false)) {
                return false;
            }

            $classFilePath = __DIR__ . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $className) . '.php';

            if ((file_exists($classFilePath) === false) || (is_readable($classFilePath) === false)) {
                return false;
            }

            require($classFilePath);
        }
    }