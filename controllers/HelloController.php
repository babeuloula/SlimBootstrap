<?php

    namespace Controller;

    use \Psr\Http\Message\ServerRequestInterface as Request;
    use \Psr\Http\Message\ResponseInterface as Response;

    class HelloController extends Controller {

        public function Hello(Request $request, Response $response, $args) {
            return $this->view->render($response, 'Hello/Hello.html.twig', [
                'name' => $args['name']
            ]);
        }
    }
