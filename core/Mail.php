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
         * @param mixed $sujet
         * @param mixed $from
         * @param mixed $to
         * @param mixed $body
         */
        public function __construct($sujet = false, $from = false, $to = false, $body = false) {
            $this->transport = \Swift_MailTransport::newInstance();

            $this->message = \Swift_Message::newInstance();
            $this->message->setCharset('utf-8');
            $this->message->setPriority(3);

            if($sujet) {
                $this->message->setSubject($sujet);
            }

            if($from) {
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

            if($to) {
                if(is_array($to)) {
                    foreach($to as $key => $t) {
                        if(is_numeric($key)) {
                            $this->message->setTo($t);
                        } else {
                            $this->message->setTo($key, $t);
                        }
                    }
                } else {
                    $this->message->setTo($to);
                }
            }

            if($body) {
                $this->message->setBody($body, 'text/html');
                $this->message->addPart($this->html2text($body), 'text/plain');
            }
        }

        public function setSujet($sujet) {
            $this->message->setSubject($sujet);
        }

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

        public function setTo($to) {
            if(is_array($to)) {
                foreach($to as $key => $t) {
                    if(is_numeric($key)) {
                        $this->message->setTo($t);
                    } else {
                        $this->message->setTo($key, $t);
                    }
                }
            } else {
                $this->message->setTo($to);
            }
        }

        public function setBody($body) {
            $this->message->setBody($body, 'text/html');
            $this->message->addPart($this->html2text($body), 'text/plain');
        }


        /**
         * Ajoute la signature DKIM
         *
         * @param $path
         * @param $domaine
         * @param $selector
         */
        public function DKIM($path, $domaine, $selector) {
            $privateKey = file_get_contents($path);
            $this->signer = new \Swift_Signers_DKIMSigner($privateKey, $domaine, $selector);

            $this->message->attachSigner($this->signer);
        }


        /**
         * Ajoute le bandeau au message
         *
         * @param $path
         */
        public function addBandeau($path) {
            $cid = $this->message->embed(\Swift_Image::fromPath($path));
            $body = str_replace('%%BANDEAU%%', $cid, $this->message->getBody());
            $this->message->setBody($body, 'text/html');
        }


        /**
         * Ajoute une PJ à SwiftMailer
         *
         * @param string $path
         * @param mixed $name
         */
        public function addAttachment($path, $name = false) {
            $attachment = \Swift_Attachment::fromPath($path);

            if($name) {
                $attachment->setFilename($name);
            }

            $this->message->attach($attachment);
        }


        /**
         * Envoi le mail
         *
         * @return boolean
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
         * @param string $html
         *
         * @return string
         */
        private function html2text($html) {
            return html_entity_decode(
                trim(strip_tags(preg_replace('/<(head|title|style|script)[^>]*>.*?<\/\\1>/si', '', $html))),
                ENT_QUOTES,
                'utf-8'
            );
        }
    }