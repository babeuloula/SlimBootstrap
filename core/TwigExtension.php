<?php


    namespace Core;
    
    use Cocur\Slugify\Slugify;

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
        
        public function json_decode ($ressource) {
            return json_decode($ressource);
        }

        public function show_num ($ressource) {
            return wordwrap($ressource, 2, " ", 1);
        }

        public function date_fr ($date, $full = true) {
            if ($full) {
                $texte_en = array(
                    "Monday", "Tuesday", "Wednesday", "Thursday",
                    "Friday", "Saturday", "Sunday", "January",
                    "February", "March", "April", "May",
                    "June", "July", "August", "September",
                    "October", "November", "December"
                );
                $texte_fr = array(
                    "Lundi", "Mardi", "Mercredi", "Jeudi",
                    "Vendredi", "Samedi", "Dimanche", "Janvier",
                    "Février", "Mars", "Avril", "Mai",
                    "Juin", "Juillet", "Août", "Septembre",
                    "Octobre", "Novembre", "Décembre"
                );
                $date_fr = str_replace($texte_en, $texte_fr, $date);
            } else {
                $texte_en = array(
                    "Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun",
                    "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul",
                    "Aug", "Sep", "Oct", "Nov", "Dec"
                );
                $texte_fr = array(
                    "Lun", "Mar", "Mer", "Jeu", "Ven", "Sam", "Dim",
                    "Jan", "Fév", "Mar", "Avr", "Mai", "Jui",
                    "Jui", "Aoû", "Sep", "Oct", "Nov", "Déc"
                );
                $date_fr = str_replace($texte_en, $texte_fr, $date);
            }

            return $date_fr;
        }

        public function slugify($ressource) {
            $slug = new Slugify();

            return $slug->slugify($ressource);
        }

        public function truncate ($ressource, $length = 250) {
            return \Core\Truncate::truncate($ressource, $length);
        }

        public function array_cast ($ressource) {
            return get_object_vars($ressource);
        }
    }
