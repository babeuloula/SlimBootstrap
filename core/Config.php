<?php

    namespace Core;

    class Config {

        static $debug = 1;

        static $database = array(
            'local' => array(
                'host' => '',
                'database' => '',
                'user' => '',
                'password' => ''
            ),
            'dev' => array(
                'host' => '',
                'database' => '',
                'user' => '',
                'password' => ''
            )
        );

    }