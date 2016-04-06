<?php

    require __DIR__ . '/vendor/autoload.php';

    // Démarrage de la session
    session_cache_limiter(false);
    session_start();

    // Gestion des erreurs PHP
    if(\Core\Config::getOption('debug')) {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
    }

    // Chargement de Slim
    $app = new \Slim\App(new \Slim\Container(array(
        'settings' => array(
            'displayErrorDetails' => \Core\Config::getOption('debug'),
        ),
    )));

    // Récupération du container Slim
    $container = $app->getContainer();

    // Intégration du système de vues Twig à Slim
    $container['view'] = function ($c) {
        // Activation du cache
        /*$view = new \Slim\Views\Twig(\Core\Config::getOption('VIEWS_PATH'), array(
            'cache' => (!\Core\Config::getOption('debug')) ? \Core\Config::getOption('CACHE_PATH') : false,
        ));*/
        $view = new \Slim\Views\Twig(\Core\Config::getOption('VIEWS_PATH'));

        // Intégration des fonctions perso à Twig
        $view->addExtension(new \Core\TwigExtension(
            $c['router'],
            $c['request']->getUri()
        ));

        // Intégration des variables à Twig
        foreach(\Core\Config::getOptions() as $key => $value) {
            $view->getEnvironment()->addGlobal($key, $value);
        }

        return $view;
    };

    // Intégration des flash messages
    $container['flash'] = function () {
        return new \Slim\Flash\Messages();
    };

    // Intégration de la protection contre les failles CSRF
    // A ajouter uniquement sur les pages qui en ont besoins (ex: connexion)
    //$app->add(new \Slim\Csrf\Guard);



    // Routes pour le front office
    $app->get('/sitemap.xml', \Controller\SiteMapController::class . ':sitemap');

    $app->get('/hello/{name}', \Controller\HelloController::class.":Hello")
        ->setName('hello');




    // Ajout d'un middleware global
    $app->add(function($request, $response, $next) {
        $twig = $this->view->getEnvironment();
        $twig->addGlobal('INPUTS', (isset($_SESSION['site']['inputs'])) ? $_SESSION['site']['inputs'] : '');
        $twig->addGlobal('ERRORS', (isset($_SESSION['site']['errors'])) ? $_SESSION['site']['errors'] : '');
        $twig->addGlobal('FLASH', (!empty($this->flash->getMessages())) ? $this->flash->getMessages() : '');

        $uri = $this->get('request')->getUri();
        $basePath = trim($uri->getBasePath(), '/');
        $uri      = $this->get('request')->getUri();
        $basePath = trim($uri->getBasePath(), '/');
        $path     = $uri->getPath();
        $twig->addGlobal('SITE_URL_PAGE', substr_replace(\Core\Config::getOption('site.url'), "", -1) . $path);
        unset($_SESSION['site']['inputs'], $_SESSION['site']['errors']);

        $response = $next($request, $response);

        return $response;
    });





    // Gestion des erreurs
    // Erreur 500
    if(!\Core\Config::getOption('debug')) {
        $container['errorHandler'] = function ($c) {
            return function ($request, $response, $exception) use ($c) {
                $response = $response->withStatus(500)
                                     ->withHeader('Content-Type', 'text/html');

                return $c->view->render($response, 'errors/500.html.twig');
            };
        };
    }
    // Erreur 404
    $container['notFoundHandler'] = function ($c) {
        return function ($request, $response) use ($c) {
            $response = $response->withStatus(404)
                                 ->withHeader('Content-Type', 'text/html');

            return $c->view->render($response, 'errors/404.html.twig');
        };
    };
    // Erreur 405
    $container['notAllowedHandler'] = function ($c) {
        return function ($request, $response) use ($c) {
            $response = $response->withStatus(405)
                                 ->withHeader('Content-Type', 'text/html');

            return $c->view->render($response, 'errors/405.html.twig');
        };
    };

    // Démarrage de Slim
    $app->run();