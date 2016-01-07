<?php

    namespace Core;

    class Config {

        public static function getOption($name) {
            $name = str_replace('.', '_', $name);
            $name = strtoupper($name);

            $options = self::getOptions();
            return $options[$name];
        }

        public static function getOptions() {
            $options = array();
            
            $config_ini = parse_ini_file(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config.ini');

            $options['DS']          = DIRECTORY_SEPARATOR;
            $options['ROOT']        = dirname(__DIR__);
            $options['BASE_URL']    = (dirname($_SERVER['SCRIPT_NAME']) == DIRECTORY_SEPARATOR) ? '' : dirname($_SERVER['SCRIPT_NAME']);
            $options['URL']         = '//' . $_SERVER['SERVER_NAME'];
            $options['CTRLS_PATH']  = $options['ROOT'] . $options['DS'] . 'controllers' . $options['DS'];
            $options['MODLS_PATH']  = $options['ROOT'] . $options['DS'] . 'models' . $options['DS'];
            $options['VIEWS_PATH']  = $options['ROOT'] . $options['DS'] . 'views' . $options['DS'];
            $options['PUBLIC_URL']  = $options['URL'] . $options['BASE_URL'] . '/public/';

            foreach($config_ini as $key => $value) {
                $key = str_replace('.', '_', $key);
                $key = strtoupper($key);

                if($key == 'DEBUG') {
                    $value = ($value == 'true') ? true : false;
                }

                $options[$key] = $value;
            }
            
            return $options;
        }
    }