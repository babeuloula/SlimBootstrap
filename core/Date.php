<?php
    namespace Core;

    class Date {

        public static function fr($date, $full = true) {
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
    }