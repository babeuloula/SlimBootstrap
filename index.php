<?php

    // Démarrage de la session
    session_cache_limiter(false);
    session_start();


    // Définition des dossiers
    define('DS', '/');
    define('ROOT', dirname(__FILE__));
    define('BASE_URL', (dirname($_SERVER['SCRIPT_NAME']) == DIRECTORY_SEPARATOR) ? '' : dirname($_SERVER['SCRIPT_NAME']));
    define('URL', $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME']);
    define('VIEWS_URL', BASE_URL . DS . 'views' . DS );
    define('PUBLIC_URL', BASE_URL . DS . 'public' . DS);
    define('DEBUG', true);


    // Définition des variables communes
    define('DKIM_PATH', ROOT . DS . 'dkim' . DS . 'dkim.private.key');
    define('DKIM_DOMAINE', 'your-domaine.com');
    define('DKIM_SELECTOR', 'default');


    // Chargement des autloader
    require 'controllers/autoloader_controllers.php';
    require 'models/autoloader_models.php';
    require 'vendor/autoload.php';


    // Gestion des erreurs PHP
    if(DEBUG) {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
    }


    // Initialisation de Slim Framework
    $app = new \Slim\Slim(array(
        'debug' => DEBUG,
        'templates.path' => 'views/',
        'view' => new \Slim\Views\Twig(),
    ));


    // Intégration de Twig au système de vues de Slim Framework
    $view = $app->view();
    $view->parserOptions = array(
        'debug' => true,
    );
    $view->parserExtensions = array(
        new Core\TwigExtension(),
    );
    $twig = $app->view()->getEnvironment();
    $twig->addExtension(new Twig_Extension_Debug());
    $twig->addGlobal('debug', DEBUG);
    $twig->addGlobal('url', URL);


    // Création des routes
    $router = new \Rooter\Router($app);
    $router->get('/', 'Page:index')->name('index');
    $router->post('/hello/:name', 'Page:hello')->name('hello');






    // Gestion des erreurs
    $app->notFound(function() use ($app) {
        // $app->render('errors/404.html.twig');
        die('404 Error !');
    });
    $app->error(function() use ($app) {
        $app->response->status(500);
        // $app->render('errors/500.html.twig');
    });


    // Lancement de Slim Framework
    $app->run();