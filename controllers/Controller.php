<?php

    class Controller {

        protected $app;

        public function __construct($app) {
            $this->app = $app;
        }

        public function loadModel($name, $table = false) {
            require_once ROOT . DS . "models" . DS . $name . ".php";

            if(!isset($this->$name)) {
                $this->$name = new $name($table);
            }
        }
    }
