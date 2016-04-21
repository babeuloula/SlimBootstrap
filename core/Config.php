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
            
            $config_ini = parse_ini_file(__DIR__ . '/../config.ini', false, INI_SCANNER_RAW);

            $options['DS']          = DIRECTORY_SEPARATOR;
            $options['ROOT']        = dirname(__DIR__);
            $options['BASE_URL']    = (dirname($_SERVER['SCRIPT_NAME']) == DIRECTORY_SEPARATOR) ? '' : dirname($_SERVER['SCRIPT_NAME']);
            $options['URL']         = '//' . $_SERVER['SERVER_NAME'];
            $options['CACHE_PATH']  = $options['ROOT'] . $options['DS'] . 'cache' . $options['DS'];
            $options['CTRLS_PATH']  = $options['ROOT'] . $options['DS'] . 'controllers' . $options['DS'];
            $options['MODLS_PATH']  = $options['ROOT'] . $options['DS'] . 'models' . $options['DS'];
            $options['VIEWS_PATH']  = $options['ROOT'] . $options['DS'] . 'views' . $options['DS'];
            $options['FILES_PATH']  = $options['ROOT'] . $options['DS'] . 'public' . $options['DS'] . 'files' . $options['DS'];
            $options['IMAGES_PATH'] = $options['ROOT'] . $options['DS'] . 'public' . $options['DS'] . 'images' . $options['DS'];
            $options['PUBLIC_URL']  = $options['URL'] . $options['BASE_URL'] . '/public/';
            $options['PRIVATE_URL'] = $options['URL'] . $options['BASE_URL'] . '/private/';
            $options['FILES_URL']   = $options['URL'] . $options['BASE_URL'] . '/public/files/';
            $options['IMAGES_URL']  = $options['URL'] . $options['BASE_URL'] . '/public/images/';

            foreach($config_ini as $key => $value) {
                $key = str_replace('.', '_', $key);
                $key = strtoupper($key);

                $value = trim(trim($value, "'"), '"');

                if(is_numeric($value)) {
                    $value = intval($value);
                } else {
                    if($value == 'true' || $value == 'on' || $value == 'yes') {
                        $value = true;
                    } else if($value == 'false' || $value == 'off' || $value == 'no' || $value == 'none') {
                        $value = false;
                    }
                }

                $options[$key] = $value;
            }
            
            return $options;
        }
    }