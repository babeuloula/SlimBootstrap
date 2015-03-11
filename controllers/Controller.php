<?php

    class Controller {

        protected $app;
        protected $flash;

        public function __construct($app) {
            $this->app = $app;
            $this->flash = new \Core\Flash();

            $this->setGlobals();
        }

        public function loadModel($name, $table = false) {
            if(file_exists(ROOT . DS . "models" . DS . $name . ".php")) {
                require_once ROOT . DS . "models" . DS . $name . ".php";
            } else {
                die('Le model <strong>' . $name . '</strong> n\'existe pas dans le dossier <strong>/models/' . $name . '</strong>.');
            }

            if(!isset($this->$name)) {
                $this->$name = new $name($table);
            }
        }

        public function setGlobals() {
            $twig = $this->app->view()->getEnvironment();
            $twig->addGlobal('inputs', (isset($_SESSION['front']['inputs'])) ? $_SESSION['front']['inputs'] : '');
            $twig->addGlobal('errors', (isset($_SESSION['front']['errors'])) ? $_SESSION['front']['errors'] : '');
            $twig->addGlobal('user', (isset($_SESSION['front']['user'])) ? $_SESSION['front']['user'] : '');

            unset($_SESSION['front']['inputs'], $_SESSION['front']['errors']);
        }


    }
