<?php


    namespace Core;

    use Slim\Slim;

    class Flash extends Slim {

        public function flash($type, $message) {
            if (isset($this->environment['slim.flash'])) {
                $this->environment['slim.flash']->set('message', $message);

                switch($type) {
                    case "success":
                        $this->environment['slim.flash']->set('type', 'alert-success');
                        $title = "Succès";
                        break;

                    case "error":
                        $this->environment['slim.flash']->set('type', 'alert-danger');
                        $title = "Erreur";
                        break;

                    case "warning":
                        $this->environment['slim.flash']->set('type', 'alert-warning');
                        $title = "Attention";
                        break;

                    case "tips":
                        $this->environment['slim.flash']->set('type', 'alert-info');
                        $title = "Astuce";
                        break;

                    default:
                        $this->environment['slim.flash']->set('type', 'alert-primary');
                        $title = "Information";
                        break;
                }

                $this->environment['slim.flash']->set('title', $title);
            }
        }
    }