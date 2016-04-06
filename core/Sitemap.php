<?php
    namespace Core;

    class Sitemap {

        private $xml;
        private $url;
        private $allowedFreq = array(
            'always',
            'hourly',
            'daily',
            'weekly',
            'monthly',
            'yearly',
            'never',
        );

        public function __construct () {
            $this->url = trim(\Core\Config::getOption('site.url'), '/');

            $this->xml = new \SimpleXMLElement('<urlset/>');
            $this->xml->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        }

        public function addItem($loc, $priority = 0.5, $changefreq = NULL, $lastmod = NULL) {
            $item = $this->xml->addChild('url');

            $item->addChild('loc', $this->url . $loc);
            $item->addChild('priority', $priority);

            if($changefreq) {
                if(!in_array($changefreq, $this->allowedFreq)) {
                    throw new \Exception('changefreq: unrecognized value');
                }

                $item->addChild('changefreq', $changefreq);
            }

            if($lastmod) {
                $date = new \DateTime($lastmod);
                $item->addChild('lastmod', $date->format('c'));
            }
        }

        public function output($filename = false) {
            if($filename) {
                return $this->xml->asXML($filename);
            }

            return $this->xml->asXML();
        }
    }