<?php

    // Démarrage de la session
    session_start();


    // Définition des dossiers
    define('DS', DIRECTORY_SEPARATOR);
    define('ROOT', dirname(__FILE__));
    define('BASE_URL', dirname($_SERVER['SCRIPT_NAME']));
    define('VIEWS_URL', BASE_URL . DS . 'views' . DS );
    define('PUBLIC_URL', BASE_URL . DS . 'public' . DS);


    // Chargement des autloader
    require 'controllers/autoloader_controllers.php';
    require 'models/autoloader_models.php';
    require 'vendor/autoload.php';


    // Initialisation de Slim Framework
    $app = new \Slim\Slim(array(
        'debug' => true,
        'templates.path' => 'views/',
        'view' => new \Slim\Views\Twig(),
    ));


    // Intégration de Twig au système de vues de Slim Framework
    $view = $app->view();
    $view->parserOptions = array(
        'debug' => true,
    );
    $view->parserExtensions = array(
        new \Slim\Views\TwigExtension(),
    );


    // Création des routes
    $router = new \Rooter\Router($app);
    $router->get('/', 'Pages:index')->name('home');
    $router->get('/hello/:name', 'Pages:hello')->name('hello');


    // Gestion des erreurs
    $app->notFound(function() use ($app) {
        $app->render('errors/404.html.twig');
    });


    // Lancement de Slim Framework
    $app->run();

?>