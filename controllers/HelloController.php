<?php

    namespace Controller;

    use \Psr\Http\Message\ServerRequestInterface as Request;
    use \Psr\Http\Message\ResponseInterface as Response;

    class HelloController extends Controller {

        public function Hello(Request $request, Response $response, $args) {
            if($this->container->get('config')['debug']) {
                // Do something
            }

            return $this->view->render($response, 'Hello/Hello.html.twig', [
                'name' => $args['name']
            ]);
        }
    }
