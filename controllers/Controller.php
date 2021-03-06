<?php

    namespace Controller;

    use Interop\Container\ContainerInterface;

    class Controller {

        protected $container;
        protected $view;
        protected $flash;
        protected $router;

        public function __construct (ContainerInterface $container) {
            if(!$container instanceof ContainerInterface) {
                throw new \Exception("Muse be an instance of ContainerInterface");                
            }

            $this->container = $container;
            $this->view      = $container->get('view');
            $this->flash     = $container->get('flash');
            $this->router    = $container->get('router');
        }

        public function notFound () {
            $errorHandler = $this->container->notFoundHandler;
            return $errorHandler($this->container->get('request'), $this->container->get('response'));
        }

        public function notAllowed () {
            $errorHandler = $this->container->notAllowedHandler;
            return $errorHandler($this->container->get('request'), $this->container->get('response'));
        }

        public function redirect ($url) {
            $response = $this->container->get('response');

            return $response->withStatus(302)
                            ->withHeader('Location', $url);
        }

        public function redirectTo ($routeName, $params = array()) {
            return $this->redirect($this->router->pathFor($routeName, $params));
        }

        public function loadModel ($name, $table = false) {
            if (file_exists($this->container->get('config')['MODLS_PATH'] . $name . ".php")) {
                require_once $this->container->get('config')['MODLS_PATH'] . $name . ".php";
            } else {
                die('Le model <strong>' . $name . '</strong> n\'existe pas dans le dossier <strong>/models/' . $name . '</strong>.');
            }

            if(!isset($this->$name)) {
                $modelName = '\Model\\' . $name;
                $this->$name = new $modelName($table);
            }
        }


    }
