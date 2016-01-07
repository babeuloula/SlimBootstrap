<?php

    require 'vendor/autoload.php';

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
    $app->get('/hello/{name}', \Controller\HelloController::class.":Hello")
        ->setName('hello');




    // Routes pour le back office
    $app->get('/' . \Core\Config::getOption('cms.path.name') . '/', \Controller\LoginController::class.':signin')
        ->setName('signin');
    $app->group('/' . \Core\Config::getOption('cms.path.name'), function() use ($app) {
        $app->post('/login/', \Controller\LoginController::class.':login')
            ->setName('login');

        // Dashboard
        $app->get('/dashboard/', \Controller\PageController::class.':index')
            ->setName('dashboard');

        // Déconnexion
        $app->get('/disconnect/', \Controller\LoginController::class.':disconnect')
            ->setName('disconnect');

        // Mes images
        $app->get('/mes-images/', \Controller\ImageController::class.':findAll')
            ->setName('mes_images');
        $app->post('/mes-images/ajouter-une-image/', \Controller\ImageController::class.':ajouter')
            ->setName('ajouter_image');
        $app->post('/mes-images/supprimer-une-image/{image}', \Controller\ImageController::class.':supprimer')
            ->setName('supprimer_image');
        $app->get('/files/images/{image_name}', \Controller\ImageController::class.':afficher')
            ->setName('mon_image');
        $app->get('/files/images/thumb/{image_name}', \Controller\ImageController::class.':thumb')
            ->setName('mon_image_thumb');

        // Filebrowser
        /*$app->get('/[{route:[0-9a-zA-Z/-]+}/]filebrowser/browse/', \Controller\FilebrowserController::class.':browse')
            ->setName('browse');
        $app->post('/[{route:[0-9a-zA-Z/-]+}/]filebrowser/mkdir/', \Controller\FilebrowserController::class.':mkdir')
            ->setName('mkdir');
        $app->post('/[{route:[0-9a-zA-Z/-]+}/]filebrowser/upload/', \Controller\FilebrowserController::class.':upload')
            ->setName('upload');
        $app->post('/[{route:[0-9a-zA-Z/-]+}/]/filebrowser/remove/{file}', \Controller\FilebrowserController::class.':remove')
            ->setName('remove');*/
    })->add(function ($request, $response, $next) use ($app) {
        // Remplace la fonction $this->isConnected();
        if(!isset($_SESSION['admin']['user']) && $request->getAttributes()['route']->getName() != 'login') {
            $container = $app->getContainer();
            $flash = $container->flash;
            $router = $container->router;

            $flash->addMessage('danger', "Vous devez vous connecter pour accéder à cette page.");

            return $response->withStatus(302)
                            ->withHeader('Location', $router->pathFor('signin'));
        }

        $response = $next($request, $response);

        return $response;
    });




    // Ajout d'un middleware global
    $app->add(function($request, $response, $next) {
        $twig = $this->view->getEnvironment();
        $twig->addGlobal('INPUTS', (isset($_SESSION['site']['inputs'])) ? $_SESSION['site']['inputs'] : '');
        $twig->addGlobal('ERRORS', (isset($_SESSION['site']['errors'])) ? $_SESSION['site']['errors'] : '');
        $twig->addGlobal('FLASH', (!empty($this->flash->getMessages())) ? $this->flash->getMessages() : '');

        $uri = $this->get('request')->getUri();
        $basePath = trim($uri->getBasePath(), '/');
        $path = $uri->getPath();
        $twig->addGlobal('SITE_URL_PAGE', \Core\Config::getOption('site.url') . $basePath . '/' . $path);

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