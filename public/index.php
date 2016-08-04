<?php

    use \Psr\Http\Message\ServerRequestInterface as Request;
    use \Psr\Http\Message\ResponseInterface as Response;

    require __DIR__ . '/../vendor/autoload.php';

    // On récupère la config du site
    $config = \Core\Config::getOptions();

    // Démarrage de la session
    session_cache_limiter(false);
    session_start();

    // Gestion des erreurs PHP
    if($config['debug']) {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
    }

    // Chargement de Slim
    $app = new \Slim\App(new \Slim\Container(array(
        'settings' => array(
            'displayErrorDetails' => $config['debug'],
        ),
        'config' => $config,
    )));

    // Récupération du container Slim
    $container = $app->getContainer();

    // Intégration du système de vues Twig à Slim
    $container['view'] = function ($c) {
        // Activation du cache
        /*$view = new \Slim\Views\Twig($c->get('config')['VIEWS_PATH'], array(
            'cache' => (!$c->get('config')['debug']) ? $c->get('config')['CACHE_PATH'] : false,
        ));*/
        $view = new \Slim\Views\Twig($c->get('config')['VIEWS_PATH']);

        // Intégration des fonctions perso à Twig
        $view->addExtension(new \Core\TwigExtension(
            $c['router'],
            $c['request']->getUri(),
            $c
        ));


        // Intégration des variables de config à Twig
        foreach($c->get('config') as $key => $value) {
            if(strpos($key, ".") === FALSE) {
                $view->getEnvironment()->addGlobal("CONFIG_" . $key, $value);
            }
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
    $app->add(function(Request $request, Response $response, $next) {
        $twig = $this->view->getEnvironment();
        $twig->addGlobal('INPUTS', (isset($_SESSION['site']['inputs'])) ? $_SESSION['site']['inputs'] : '');
        $twig->addGlobal('ERRORS', (isset($_SESSION['site']['errors'])) ? $_SESSION['site']['errors'] : '');
        $twig->addGlobal('COOKIE', $_COOKIE);
        $twig->addGlobal('SESSION', $_SESSION);
        $twig->addGlobal('FLASH', (!empty($this->flash->getMessages())) ? $this->flash->getMessages() : '');

        $uri  = $this->get('request')->getUri();
        $path = $uri->getPath();
        $twig->addGlobal('CONFIG_SITE_URL_PAGE', trim($this->get('config')['site.url'], '/') . $path);

        unset($_SESSION['site']['inputs'], $_SESSION['site']['errors']);

        $response = $next($request, $response);

        return $response;
    });





    // Gestion des erreurs
    // Erreur 500
    if(!$config['debug']) {
        $container['errorHandler'] = function ($c) {
            return function (Request $request, Response $response, $exception) use ($c) {
                $response = $response->withStatus(500)
                                     ->withHeader('Content-Type', 'text/html');

                return $c->view->render($response, 'errors/500.html.twig');
            };
        };
    }
    // Erreur 404
    $container['notFoundHandler'] = function ($c) {
        return function (Request $request, Response $response) use ($c) {
            $response = $response->withStatus(404)
                                 ->withHeader('Content-Type', 'text/html');

            return $c->view->render($response, 'errors/404.html.twig');
        };
    };
    // Erreur 405
    $container['notAllowedHandler'] = function ($c) {
        return function (Request $request, Response $response) use ($c) {
            $response = $response->withStatus(405)
                                 ->withHeader('Content-Type', 'text/html');

            return $c->view->render($response, 'errors/405.html.twig');
        };
    };

    // Démarrage de Slim
    $app->run();