<?php

    namespace Rooter;

    class Router {

        private $app;

        public function __construct($app) {
            $this->app = $app;
        }

        private function call($method, $url, $action) {
            $app = $this->app;
            return $this->app->$method($url, function() use ($action, $app) {
                $action = explode(':', $action);

                $controller_name = $action[0] . 'Controller';
                $method = $action[1];

                $controller = new $controller_name($app);

                call_user_func_array(array($controller, $method), func_get_args());
            });
        }

        public function get($url, $action) {
            return $this->call('get', $url, $action);
        }

        public function post($url, $action) {
            return $this->call('post', $url, $action);
        }

        public function insert($url, $action) {
            return $this->call('post', $url, $action);
        }

        public function update($url, $action) {
            return $this->call('post', $url, $action);
        }

        public function delete($url, $action) {
            return $this->call('get', $url, $action);
        }
    }
