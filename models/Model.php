<?php

    namespace Model;

    use \Core\Config, \Core\Collection;
    use Interop\Container\ContainerInterface;

    class Model {

        static $connections = array();

        public $db;
        public $pdo;
        public $table = false;
        public $lastId;

        private $config;


        public function __construct($table = false, $sqlite = false) {
            $this->config = \Core\Config::getOptions();

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

                    $dir = $this->config['sqlite.path'] . $this->config['sqlite.file'];

                    $pdo = new \PDO('sqlite:' . $dir);
                    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                } else {
                    if ($_SERVER['SERVER_ADDR'] == "192.168.1.200") {
                        $config = array(
                            'host'     => $this->config['local.host'],
                            'database' => $this->config['local.database'],
                            'user'     => $this->config['local.user'],
                            'password' => $this->config['local.password'],
                        );
                        $this->db = 'local';
                    } else {
                        $config = array(
                            'host'     => $this->config['dev.host'],
                            'database' => $this->config['dev.database'],
                            'user'     => $this->config['dev.user'],
                            'password' => $this->config['dev.password'],
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
                if($this->config['debug'] === true) {
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
            if(!is_string($query)) {
                $keys = implode(', ', array_keys($query));

                $values = array();
                foreach(array_values($query) as $value) {
                    array_push($values, $this->pdo->quote($value));
                }
                $values = implode(', ', $values);

                $query = "INSERT INTO ".$this->table . "(" . $keys . ") VALUES (" . $values . ")";
            }

            $prepare = $this->pdo->prepare($query);
            $query = $prepare->execute($datas);
            $this->lastId = $this->pdo->lastInsertId();

            $prepare->closeCursor();

            return $query;
        }

        public function update($query, $datas = array()) {
            if(!is_string($query)) {
                $sql = "UPDATE ".$this->table . " SET ";

                if(!isset($query['datas'])) {
                    die("Vous devez spécifier des valeurs à modifier");
                } else {
                    $set = array();
                    foreach($query['datas'] as $row => $value) {
                        $value = $this->pdo->quote($value);
                        array_push($set, $row . " = " . $value);
                    }

                    $sql.= implode(', ', $set);
                }

                if(isset($query['conditions'])) {
                    $sql.= " WHERE ";

                    if(!is_array($query['conditions'])) {
                        $sql.= $query['where'];
                    } else {
                        $conditions = array();
                        foreach($query['conditions'] as $key => $value) {
                            $value = $this->pdo->quote($value);
                            array_push($conditions, $key . " = " . $value);
                        }

                        $sql.= implode(' AND ', $conditions);
                    }
                }

                $query = $sql;
            }

            $prepare = $this->pdo->prepare($query);
            $result = $prepare->execute($datas);

            $prepare->closeCursor();

            return $result;
        }

        public function delete($query, $datas = array()) {
            if(!is_string($query)) {
                $sql = "DELETE FROM ".$this->table;

                if(isset($query['conditions'])) {
                    $sql.= " WHERE ";

                    if(!is_array($query['conditions'])) {
                        $sql.= $query['where'];
                    } else {
                        $conditions = array();
                        foreach($query['conditions'] as $key => $value) {
                            $value = $this->pdo->quote($value);
                            array_push($conditions, $key . " = " . $value);
                        }

                        $sql.= implode(' AND ', $conditions);
                    }
                }

                $query = $sql;
            }

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
                        $value = $this->pdo->quote($value);
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

        public function findFirst($query = null, $datas = array()) {
            if(!is_array($query)) {
                $query = array();
            }

            if(!isset($query['limit'])) {
                $query['limit'] = array(
                    'min' => 0,
                    'max' => 1,
                );
            }

            $first = $this->find($query, $datas);
            return $first->first();
        }

        public function findLast($query = null, $datas = array()) {
            if(!is_array($query)) {
                $query = array();
            }

            if(isset($query['ordre'])) {
                if(!is_array($query['ordre'])) {
                    $ordres = explode(',', $query['ordre']);
                    foreach($ordres as $key => $ordre) {
                        if(strpos(trim($ordre), 'ASC') !== FALSE) {
                            $ordres[$key] = trim(str_replace('ASC', 'DESC', $ordre));
                        } else if(strpos(trim($ordre), 'DESC') !== FALSE) {
                            $ordres[$key] = trim(str_replace('DESC', 'ASC', $ordre));
                        }
                    }

                    $query['ordre'] = implode(', ', $ordres);
                } else {
                    foreach($query['ordre'] as $key => $value) {
                        if($value == 'ASC') {
                            $query['ordre'][$key] = 'DESC';
                        } else if($value == 'DESC') {
                            $query['ordre'][$key] = 'ASC';
                        }
                    }
                }
            } else {
                $query['ordre'] = array(
                    'id_' . $this->table => 'DESC'
                );
            }

            if(!isset($query['limit'])) {
                $query['limit'] = array(
                    'min' => 0,
                    'max' => 1,
                );
            }

            $last = $this->find($query, $datas);
            return $last->last();
        }
    }