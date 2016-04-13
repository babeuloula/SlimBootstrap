# Bootstrap Slim Framework 3

Bootstrap pour [Slim Framework 3](http://www.slimframework.com/) afin de démarrer facilement un petit (ou gros) projet grâce à Slim Framework 3.

Pour les personnes qui recherchent le Bootstrap de la verison 2, changez de branche.

## Utilisation
Vous avez juste à cloner le dépôt dans votre dossier de travail, mettre à jour les paquets grâce à composer et commencez à programmer !  
    
Le système est déjà prêt à fonctionner, vous avez aussi des exemples de routes, de models, de vues et de controllers.


## Différences entre ma version 2 et 3

La configuration du site Internet se faire directement dans le fichier *config.ini* présent à la racine du bootstrap.

Le router n'est plus présent dans cette version car Slim3 gère nativement les controller externe.

L'autoloader prend enfin en compte le *namespace* *Controller* et *Model*.

### Routes
```php
// Création d'une route
$app->get('/hello/{name}', \Controller\HelloController::class.":Hello")
    ->setName('hello');
       
// Route avec une condition (uniquement des chiffres)
$rooter->get('/hello/{id:[0-9]+}', \Controller\HelloController::class.":Hello");
       
// Route avec un paramètre optionnel
$rooter->get('/hello/[{id:[0-9]+}]', \Controller\HelloController::class.":Hello");

// Groupe de routes
$app->group('/users', function () use ($app) {
    $app->get('/reset-password', \Controller\HelloController::class.":Hello")
        ->setName('user-password-reset');
});


// Récupère l'URL d'une route
$this->router->pathFor('hello', array(
    'name' => 'Josh'
));
```

```html
<!-- Récupère l'URL d'une route -->
{{ path_for('hello', { 'name' => 'Josh' }
```

Le système de routes etant le même que dans la documentation de Slim3, je ne vais pas le détailler plus longtemps.

### Controllers
Pensez bien à mettre le *namespace Controller* et à faire un *extends Controller*
```php
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
```

### Models
Idem que pour les controller, n'oubliez pas le *namespace Model* et *extends Model*.
```php
<?php
    
    namespace Model;
    
    class Hello extends Model {
        
    }
```
La class *Model* a été réécrite pour correspondre aux modifications de cette nouvelle version du bootstrap mais elle garde les mêmes fonctions.

### Pages d'erreurs
Les pages d'erreurs sont gérés dans le fichier `index.php`. Et comme ce n'était pas simple de les appeler facilement dans un controller, j'ai créé les 2 fonctions suivantes :
```php
// 404
return $this->notFound();

// 405
return $this->notAllowed();
```

### Redirections
Les redirections dans Slim3 étant un peu longue à mon goût j'ai créé là aussi 2 fonctions qui portent le même nom que dans Slim2 :
```php
// Rediction vers une page externe
return $this->redirect('http://www.google.fr');

// Redirection vers une page interne
return $this->redirectTo('cms', array(
    'name' => 'test'
));
```

### Messages flash
Dans Slim2 il existait déjà un système de message flash, il est maintenant externalisé et présent dans un dépot à part mais déjà installé dans ce bootstrap.
```php
// Créer un message
$this->flash->addMessage('Test', 'This is a message');
```

```html
<!-- Récupère le(s) message(s) -->
<!-- Attention, car ceci est un taleau -->
{{ FLASH['Test'][0] }}
```

### Vues
Pour changer les informations de la page (titre, description, keywords ...), il ne faudra plus utiliser les blocks car certaines informations sont en double et il ne peut y avoir qu'un seul block de même nom par page.

Toutes ces informations sont en majusules afin de la différencier des autres variables.
```html
{{ SITE_TITRE = 'Nom de ma page - ' ~ SITE_TITRE }}
```

### Options

#### Création
Vous pouvez rentrer toutes vos options dans le fichier *config.ini*.

Par contre, les boolean ne sont pas pris en compte.

#### Récupération
Vous pouvez récupérer les options dans vos controller ou models grâce à la class *\Core\Config*.
```php
$myOption = \Core\Config::getOption('optName');
```
Dans les vues, les options sont aussi disponible mais seront toutes en majuscules et les `.` sont remplacés par des `_`.


## Arborescence

### Controller
Dossier des controllers perso
 
### Core
Dossier des classes qui permettent le bon fonctionne de ce bootstrap.

Vous pouvez modifier le fichier `TwigExtention.php` pour créer vos propres fonctions Twig en plus de celles de Slim et les miennes.
        
Si vous avez vos propres class, vous pouvez les mettre dans ce dossier en pensant bien à mettre le `namespace Core;`.

### DKIM
Dossier où sera stocker vos clés DKIM.

### Models
Dossiers de vos models perso

### Public
Dossier de vos ressources à afficheer dans les vues (css, js, images ...). Grâce à la fonction `{{ asset('') }}` vous pourrez directement lier vos fichiers qui sont dans ce dossier.

### Views
Dossier où seront stockés vos vues Twig (ou autre)