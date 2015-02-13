<?php

    namespace Core;

    class Collection implements \IteratorAggregate, \ArrayAccess {

        private $items;

        /**
         * @param array $items Tableau à traiter
         *
         * @return Collection
         */
        public function __construct(array $items) {
            $this->items = $items;
        }


        /**
         * Permet de recupérer la valeur d'une entrée du tableau
         *
         * @param string $key Clé du tableau
         *
         * @return string Retourne la valeur du tableau
         */
        public function get($key) {
            $index = explode('.', $key);
            return $this->getValue($index, $this->items);
        }


        private function getValue(array $indexes, $value) {
            $key = array_shift($indexes);

            if(empty($indexes)) {
                if(!array_key_exists($key, $value)) {
                    return null;
                }

                if(is_array($value[$key])) {
                    return new Collection($value[$key]);
                } else {
                    return $value[$key];
                }
            } else {
                return $this->getValue($indexes, $value[$key]);
            }
        }


        /**
         * Permet d'affecter une valeur au tableau
         *
         * @param string $key Clé du tableau
         * @param string $value Nouvelle valeur
         *
         * @return void
         */
        public function set($key, $value) {
            $this->items[$key] = $value;
        }


        /**
         * Vérifie si une clé existe dans un tableau
         *
         * @param string $key Clé du tableau à chercher
         *
         * @return boolean Cette fonction retourne TRUE en cas de succès ou FALSE si une erreur survient
         */
        public function has($key) {
            return array_key_exists($key, $this->items);
        }


        /**
         * Vide le contenu d'une clé
         *
         * @param string $key Clé du tableau à effacer
         *
         * @return void
         */
        public function clear($key) {
            unset($this->items[$key]);
        }


        /**
         * Liste les données dans un tableau associatif
         *
         * @param string $key Clé du tableau
         * @param string $value Valeur du tableau
         *
         * @return Collection Retourne une collection avec les valeurs extraites
         */
        public function lists($key, $value) {
            $results = array();

            foreach($this->items as $item) {
                $results[$item[$key]] = $item[$value];
            }

            return new Collection($results);
        }


        /**
         * Extrait les données en fonction d'une clé
         *
         * @param string $key Clé à extraire
         *
         * @return Collection Retourne une collection avec les valeurs extraites
         */
        public function extract($key) {
            $results = array();

            foreach($this->items as $item) {
                array_push($results, $item[$key]);
            }

            return new Collection($results);
        }


        /**
         * Rassemble les éléments d'un tableau en une chaîne
         *
         * @param string $glue Chaîne de caractère qui permet de lier les morceaux
         *
         * @return boolean Cette fonction retourne la valeur en cas de succès ou FALSE si une erreur survient
         */
        public function join($glue) {
            return implode($glue, $this->items);
        }


        /**
         * Retourne la valeur min d'un tableau
         *
         * @param string $key Nom de la clé en fonction de laquelle il faut donner la valeur min. Par defaut aucune
         *
         * @return boolean Cette fonction retourne la valeur en cas de succès ou FALSE si une erreur survient
         */
        public function min($key = false) {
            if($key) {
                return $this->extract($key)->min();
            } else {
                return min($this->items);
            }
        }


        /**
         * Retourne la valeur max d'un tableau
         *
         * @param string $key Nom de la clé en fonction de laquelle il faut donner la valeur max. Par defaut aucune
         *
         * @return boolean Cette fonction retourne la valeur en cas de succès ou FALSE si une erreur survient
         */
        public function max($key = false) {
            if($key) {
                return $this->extract($key)->max();
            } else {
                return max($this->items);
            }
        }


        /**
         * Trie les éléments d'un tableau
         *
         * @param string $type Type de tri du tableau (nat, natcase, asc, desc). Par defaut la fonction tri en nat
         * @param boolean $key Si la fonction doit trier en fonction des clés
         *
         * @return Collection Cette fonction retourne le tableau trié
         */
        public function sort($type = 'nat', $key = false) {
            if($type == 'asc' && !$key) {
                asort($this->items);
            } else if($type == 'asc' && $key) {
                ksort($this->items);
            } else if($type == 'desc' && !$key) {
                arsort($this->items);
            } else if($type == 'desc' && $key) {
                krsort($this->items);
            } else if($type == 'nat') {
                natsort($this->items);
            } else if($type == 'natcase') {
                natcasesort($this->items);
            }

            return new Collection($this->items);
        }


        /**
         * Mélange les éléments d'un tableau
         *
         * @return boolean Cette fonction retourne TRUE en cas de succès ou FALSE si une erreur survient
         */
        public function shuffle() {
            shuffle($this->items);
            return new Collection($this->items);
        }


        /**
         * Récupère le premier élément du tableau
         *
         * @param string $key Clé du premier élément du tableau à récupérer
         *
         * @return Collection, string Cette fonction retourne le premier élément du tableau
         */
        public function first($key = false) {
            if($key) {
                $first = new Collection($this->items);
                return $first->extract($key)->first();
            } else {
                return reset($this->items);
            }
        }


        /**
         * Récupère le dernier élément du tableau
         *
         * @param string $key Clé du premier élément du tableau à récupérer
         *
         * @return Collection, string Cette fonction retourne le dernier élément du tableau
         */
        public function last($key = false) {
            if($key) {
                $last = new Collection($this->items);
                return $last->extract($key)->last();
            } else {
                return end($this->items);
            }
        }



        /**
         * (PHP 5 &gt;= 5.0.0)<br/>
         * Whether a offset exists
         * @link http://php.net/manual/en/arrayaccess.offsetexists.php
         *
         * @param mixed $offset <p>
         *                      An offset to check for.
         *                      </p>
         *
         * @return boolean true on success or false on failure.
         * </p>
         * <p>
         * The return value will be casted to boolean if non-boolean was returned.
         */
        public function offsetExists ($offset) {
            return $this->has($offset);
        }

        /**
         * (PHP 5 &gt;= 5.0.0)<br/>
         * Offset to retrieve
         * @link http://php.net/manual/en/arrayaccess.offsetget.php
         *
         * @param mixed $offset <p>
         *                      The offset to retrieve.
         *                      </p>
         *
         * @return mixed Can return all value types.
         */
        public function offsetGet ($offset) {
            return $this->get($offset);
        }

        /**
         * (PHP 5 &gt;= 5.0.0)<br/>
         * Offset to set
         * @link http://php.net/manual/en/arrayaccess.offsetset.php
         *
         * @param mixed $offset <p>
         *                      The offset to assign the value to.
         *                      </p>
         * @param mixed $value  <p>
         *                      The value to set.
         *                      </p>
         *
         * @return void
         */
        public function offsetSet ($offset, $value) {
            $this->set($offset, $value);
        }

        /**
         * (PHP 5 &gt;= 5.0.0)<br/>
         * Offset to unset
         * @link http://php.net/manual/en/arrayaccess.offsetunset.php
         *
         * @param mixed $offset <p>
         *                      The offset to unset.
         *                      </p>
         *
         * @return void
         */
        public function offsetUnset ($offset) {
            $this->clear($offset);
        }

        /**
         * (PHP 5 &gt;= 5.0.0)<br/>
         * Retrieve an external iterator
         * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
         * @return Traversable An instance of an object implementing <b>Iterator</b> or
         * <b>Traversable</b>
         */
        public function getIterator () {
            return new \ArrayIterator($this->items);
        }
    }