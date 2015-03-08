<?php

    namespace Core;

    class Config {

        static $debug = DEBUG;

        static $database = array(
            'local' => array(
                'host'     => 'localhost',
                'database' => 'database',
                'user'     => 'root',
                'password' => ''
            ),
            'dev' => array(
                'host'     => 'localhost',
                'database' => 'database',
                'user'     => 'root',
                'password' => ''
            )
        );

    }