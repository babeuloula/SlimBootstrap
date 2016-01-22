<?php

    namespace Model;

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
                    $this->table = str_replace('model\\', '', $this->table);
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

                    $dir = \Core\Config::getOption('sqlite.path') . \Core\Config::getOption('sqlite.file');

                    $pdo = new \PDO('sqlite:' . $dir);
                    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                } else {
                    if ($_SERVER['SERVER_ADDR'] == "192.168.1.200") {
                        $config = array(
                            'host'     => Config::getOption('local.host'),
                            'database' => Config::getOption('local.database'),
                            'user'     => Config::getOption('local.user'),
                            'password' => Config::getOption('local.password'),
                        );
                        $this->db = 'local';
                    } else {
                        $config = array(
                            'host'     => Config::getOption('dev.host'),
                            'database' => Config::getOption('dev.database'),
                            'user'     => Config::getOption('dev.user'),
                            'password' => Config::getOption('dev.password'),
                        );
                        $this->db = 'dev';
                    }

                    $pdo = new \PDO('mysql:host=' . $config['host'] . ';dbname=' . $config['database'], $config['user'], $config['password']);
                    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_WARNING);
                    $pdo->exec('SET NAMES utf8');
                }

                Model::$connections[$this->db] = $pdo;
                $this->pdo = $pdo;
            } catch (\PDOException $e) {
                if(Config::getOption('debug') === true) {
                    die($e->getMessage());
                } else {
                    die('Une erreur est survenue lors de la connexion &agrave; la base de donn&eacute;es');
                }
            }
        }

        public function setTable($name) {
            $this->table = $name;
        }


        public function query($query, $datas = array(), $return = \PDO::FETCH_ASSOC) {
            $prepare = $this->pdo->prepare($query);
            $prepare->execute($datas);

            $datas = $prepare->fetchAll($return);

            $prepare->closeCursor();

            return new Collection($datas);
        }

        public function insert($query, $datas = array()) {
            $prepare = $this->pdo->prepare($query);
            $query = $prepare->execute($datas);
            $this->lastId = $this->pdo->lastInsertId();

            $prepare->closeCursor();

            return $query;
        }

        public function update($query, $datas = array()) {
            $prepare = $this->pdo->prepare($query);
            $result = $prepare->execute($datas);

            $prepare->closeCursor();

            return $result;
        }

        public function delete($query, $datas = array()) {
            $prepare = $this->pdo->prepare($query);
            $result = $prepare->execute($datas);

            $prepare->closeCursor();

            return $result;
        }

        public function find($query = null, $datas = array()) {
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

            // Construction du group by
            if(isset($query['group_by'])) {
                $sql.= " GROUP BY " . $query['group_by'];
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

            // Foramt de retour
            $return = \PDO::FETCH_ASSOC;
            if(isset($query['return'])) {
                $return = $query['return'];
            }

            return $this->query($sql, $datas, $return);
        }

        public function findFirst($query, $datas = array()) {
            $first = $this->find($query, $datas);
            return $first->first();
        }

        public function findLast($query, $datas = array()) {
            $last = $this->find($query, $datas);
            return $last->last();
        }
    }
