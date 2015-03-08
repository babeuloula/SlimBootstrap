<?php

    use \Core\Config, \Core\Collection;

    class Model {

        static $connections = array();
        public $db;
        public $pdo;
        public $table = false;
        public $lastId;

        public function __construct($table = false, $sqlite = false) {
            if($this->table === false) {
                if($table === false) {
                    $this->table = strtolower(get_class($this));
                } else {
                    $this->table = $table;
                }
            }

            if(isset(Model::$connections[$this->db])) {
                $this->pdo = Model::$connections[$this->db];
                return true;
            }

            try {
                if($sqlite) {
                    $this->db = 'sqlite';

                    $dir = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'bdd.sqlite';

                    $pdo = new PDO('sqlite:' . $dir);
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                } else {
                    if (DEBUG) {
                        $config = Config::$database['local'];
                        $this->db = 'local';
                    } else {
                        $config = Config::$database['dev'];
                        $this->db = 'dev';
                    }

                    $pdo = new PDO('mysql:host=' . $config['host'] . ';dbname=' . $config['database'], $config['user'], $config['password']);
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
                    $pdo->exec('SET NAMES utf8');
                }

                Model::$connections[$this->db] = $pdo;
                $this->pdo = $pdo;
            } catch (PDOException $e) {
                if(Config::$debug === true) {
                    die($e->getMessage());
                } else {
                    die('Une erreur est survenue lors de la connexion &agrave; la base de donn&eacute;es');
                }
            }
        }


        public function query($query, $datas = array()) {
            $prepare = $this->pdo->prepare($query);
            $prepare->execute($datas);

            return new Collection($prepare->fetchAll(PDO::FETCH_ASSOC));
        }

        public function insert($query, $datas = array()) {
            $prepare = $this->pdo->prepare($query);
            $query = $prepare->execute($datas);
            $this->lastId = $this->pdo->lastInsertId();

            return $query;
        }

        public function update($query, $datas = array()) {
            $prepare = $this->pdo->prepare($query);
            return $prepare->execute($datas);
        }

        public function delete($query, $datas = array()) {
            $prepare = $this->pdo->prepare($query);
            return $prepare->execute($datas);
        }

        public function find($query = null) {
            $sql = "SELECT * FROM ".$this->table;

            // Construction de la condition
            if(isset($query['conditions'])) {
                $sql.= " WHERE ";

                if(!is_array($query['conditions'])) {
                    $sql.= $query['conditions'];
                } else {
                    $conditions = array();
                    foreach($query['conditions'] as $key => $value) {
                        if(!is_numeric($value)) {
                            $value = $this->pdo->quote($value);
                        }

                        array_push($conditions, $key . " = " . $value);
                    }

                    $sql.= implode(' AND ', $conditions);
                }
            }

            // Construction de l'ordre
            if(isset($query['ordre'])) {
                $sql.= " ORDER BY ";

                if(!is_array($query['ordre'])) {
                    $sql.= $query['ordre'];
                } else {
                    $order = array();
                    foreach($query['ordre'] as $key => $value) {
                        array_push($order, $key . " " . $value);
                    }

                    $sql.= implode(', ', $order);
                }
            }

            // Construction de la limit
            if(isset($query['limit'])) {
                if(!is_array($query['limit'])) {
                    $sql.= $query['limit'];
                } else {
                    $sql.= " LIMIT " . $query['limit']['min'] . ", " . $query['limit']['max'];
                }
            }

            return $this->query($sql);
        }

        public function findFirst($query) {
            $first = $this->find($query);
            return $first->first();
        }

        public function findLast($query) {
            $last = $this->find($query);
            return $last->last();
        }
    }
