<?php


    namespace Core;

    class Mail {

        public $error = '';

        private $transport;
        private $message;
        private $signer;

        /**
         * Création du mail
         *
         * @param mixed $sujet Sujet du mail
         * @param mixed $from Expéditeur du mail (array ou string)
         * @param mixed $to Destinaires du mail (array)
         * @param mixed $body Contenu du mail
         */
        public function __construct($sujet = false, $from = false, $to = false, $body = false) {
            $this->transport = \Swift_MailTransport::newInstance();

            $this->message = \Swift_Message::newInstance();
            $this->message->setCharset('utf-8');
            $this->message->setPriority(3);

            if($sujet) {
                $this->setSujet($sujet);
            }

            if($from) {
                $this->setFrom($from);
            }

            if($to) {
                $this->setTo($to);
            }

            if($body) {
                $this->setBody($body);
            }
        }


        /**
         * Set le sujet
         *
         * @param string $sujet Sujet du mail
         */
        public function setSujet($sujet) {
            $this->message->setSubject($sujet);
        }


        /**
         * Set l'expediteur
         * array('john@doe.com' => 'John Doe') ou 'john@doe.com'
         *
         * @param mixed $from Expéditeur du mail (array ou string)
         */
        public function setFrom($from) {
            if(is_array($from)) {
                foreach($from as $key => $f) {
                    if(is_numeric($key)) {
                        $this->message->setFrom($f);
                        $this->message->setReplyTo($f);
                        $this->message->setReturnPath($f);
                        $this->message->setSender($f);
                    } else {
                        $this->message->setFrom($key, $f);
                        $this->message->setReplyTo($key, $f);
                        $this->message->setReturnPath($key, $f);
                        $this->message->setSender($key, $f);
                    }
                }
            } else {
                $this->message->setFrom($from);
                $this->message->setReplyTo($from);
                $this->message->setReturnPath($from);
                $this->message->setSender($from);
            }
        }


        /**
         * Set les destinataires
         * array('john@doe.com' => 'John Doe')
         *
         * @param array $to Destinataires du mail
         */
        public function setTo($to) {
            $this->message->setTo($to);
        }


        /**
         * Set le contenu du mail
         *
         * @param string $body Contenu du mail
         */
        public function setBody($body) {
            $this->message->setBody($body, 'text/html');
            $this->message->addPart($this->html2text($body), 'text/plain');
        }


        /**
         * Ajoute la signature DKIM
         *
         * @param string $path Dossier où est stocké la clé
         * @param string $domaine Domaine de la clé
         * @param string $selector Sélecteur de la clé
         */
        public function DKIM($path, $domaine, $selector) {
            $privateKey = file_get_contents($path);
            $this->signer = new \Swift_Signers_DKIMSigner($privateKey, $domaine, $selector);

            $this->message->attachSigner($this->signer);
        }


        /**
         * Ajoute une PJ à SwiftMailer
         *
         * @param string $file Emplacement du fichier ou stream du fichier
         * @param mixed $name Nom du fichier à joindre
         * @param mixed $contentType Type de fichier envoyé
         */
        public function addAttachment($file, $name = false, $contentType = null) {
            if(@is_file($file)) {
                $attachment = \Swift_Attachment::fromPath($file);
            } else {
                $attachment = \Swift_Attachment::newInstance($file, null, $contentType);
            }

            if($name) {
                $attachment->setFilename($name);
            }

            $this->message->attach($attachment);
        }


        /**
         * Envoi le mail
         *
         * @return boolean True en cas de reussite, sinon false
         */
        public function send() {
            $mailer = \Swift_Mailer::newInstance($this->transport);
            try {
                $mailer->send($this->message);
                return true;
            } catch (\Exception $e) {
                $this->error = 'Erreur ligne : ' . $e->getLine() . '<br>' .
                               'Message d\'erreur : ' . $e->getMessage() . '<br>' .
                               'Dans le fichier : ' . $e->getFile();
                return false;
            }
        }


        /**
         * Transforme le contenu du body en plain text
         *
         * @param string $html Contenu du body en HTML
         *
         * @return string Contenu du body sans le HTML
         */
        private function html2text($html) {
            return html_entity_decode(
                trim(strip_tags(preg_replace('/<(head|title|style|script)[^>]*>.*?<\/\\1>/si', '', $html))),
                ENT_QUOTES,
                'utf-8'
            );
        }
    }