<?php

    namespace Controller;

    use \Psr\Http\Message\ServerRequestInterface as Request;
    use \Psr\Http\Message\ResponseInterface as Response;

    class SiteMapController extends Controller {

        public function sitemap(Request $request, Response $response, $args) {

            $sitemap = new \Core\Sitemap();

            // Index
            $sitemap->addItem('/', '1.0', 'daily', 'yesterday');


            return $response->withHeader('Content-type', 'application/xml')
                            ->write($sitemap->output());
        }
    }