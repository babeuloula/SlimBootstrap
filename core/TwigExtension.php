<?php


    namespace Core;

    class TwigExtension extends \Slim\Views\TwigExtension {

        public function getFunctions () {
            $functions = parent::getFunctions();

            // Ajoutez vos fonctions ici
            array_push($functions, new \Twig_SimpleFunction('asset', array($this, 'asset')));

            return $functions;
        }

        public function asset ($ressource) {
            return PUBLIC_URL . $ressource;
        }
    }