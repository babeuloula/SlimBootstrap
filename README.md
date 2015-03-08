# SlimBootstrap
Comment bien débuter avec un projet Slim Framework

# Utilisation
Vous avez juste à cloner le dépôt dans votre dossier de travail, mettre à jour les paquets grâce à composer et commencez à programmer !      
Le système est déjà prêt à fonctionner, vous avez aussi des exemples de routes, de models, de vues et de controllers.

# Arborescence

## Controller
Dossier des controllers perso
 
## Core
Dossier des classes qui permettent le bon fonctionne de ce bootstrap.     
Vous pouvez cependant modifier le fichier **TwigExtention.php** pour créer vos propres fonctions Twig.        
Mais vous pouvez aussi y mettre vos classes mais n'oubliez pas le namespace **Core**

## DKIM
Dossier où sera stocker vos clés pour les envois de mail

## Models
Dossiers de vos models perso

## Public
Dossier de vos ressources à afficheer dans les vues (css, js, images ...). Grâce à la fonction **{{ asset('') }}** vous pourrez directement lier vos fichiers qui sont dans ce dossier.

## Rooter
Dossier contenant la classe permettant le fonctionnement des routes.

## Views
Dossier où seront stockés vos vues
