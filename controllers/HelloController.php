<?php

    namespace Controller;

    class HelloController extends Controller {

        public function Hello($request, $response, $args) {
            return $this->view->render($response, 'Hello/Hello.html.twig', [
                'name' => $args['name']
            ]);
        }
    }
