<?php


    namespace Core;

    use Slim\Slim;

    class Flash extends Slim {

        public function flash($type, $message) {
            if (isset($this->environment['slim.flash'])) {
                $this->environment['slim.flash']->set('type', $type);
                $this->environment['slim.flash']->set('message', $message);

                switch($type) {
                    case "success": $title = "Succès"; break;
                    case "error": $title = "Erreur"; break;
                    case "warning": $title = "Attention"; break;
                    case "tips": $title = "Astuce"; break;
                    case "info": $title = "Information"; break;
                    default: $title = "Attention"; break;
                }

                $this->environment['slim.flash']->set('title', $title);
            }
        }
    }