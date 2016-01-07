<?php


    namespace Core;

    use Cocur\Slugify\Slugify;

    class TwigExtension extends \Slim\Views\TwigExtension {

        public function getFunctions () {
            $functions = parent::getFunctions();

            // Ajoutez vos fonctions ici
            array_push($functions, new \Twig_SimpleFunction('dump', array($this, 'dump')));
            array_push($functions, new \Twig_SimpleFunction('asset', array($this, 'asset')));
            array_push($functions, new \Twig_SimpleFunction('json_decode', array($this, 'json_decode')));
            array_push($functions, new \Twig_SimpleFunction('show_num', array($this, 'show_num')));
            array_push($functions, new \Twig_SimpleFunction('date_fr', array($this, 'date_fr')));
            array_push($functions, new \Twig_SimpleFunction('slugify', array($this, 'slugify')));
            array_push($functions, new \Twig_SimpleFunction('truncate', array($this, 'truncate')));
            array_push($functions, new \Twig_SimpleFunction('array_cast', array($this, 'array_cast')));
            array_push($functions, new \Twig_SimpleFunction('isMobile', array($this, 'isMobile')));
            array_push($functions, new \Twig_SimpleFunction('isTablet', array($this, 'isTablet')));

            return $functions;
        }

        public function dump() {
            foreach(func_get_args() as $arg) {
                var_dump($arg);
            }
        }

        public function asset ($ressource, $cms = false) {
            if ($cms) {
                return Config::getOption('private_url') . 'cms/' . $ressource;
            } else {
                return Config::getOption('public_url') . $ressource;
            }
        }

        public function json_decode ($ressource) {
            return json_decode($ressource);
        }

        public function show_num ($ressource) {
            return wordwrap($ressource, 2, " ", 1);
        }

        public function date_fr ($date, $full = true) {
            return Date::fr($date, $full);
        }

        public function slugify ($ressource) {
            $slug = new Slugify();

            return $slug->slugify($ressource);
        }

        public function truncate ($ressource, $length = 250) {
            return Truncate::truncate($ressource, $length);
        }

        public function array_cast ($ressource) {
            return get_object_vars($ressource);
        }

        public function isMobile() {
            $detect = new Mobile_Detect;
            return $detect->isMobile() && !$detect->isTablet();
        }

        public function isTablet() {
            $detect = new Mobile_Detect;
            return $detect->isTablet();
        }
    }