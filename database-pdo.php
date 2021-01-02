<?php
class Database {

    static private $connection;
    static private $connectionError;

    static private string $db_name = '';
    static private string $db_username = '';
    static private string $db_password = '';
    static private string $db_host = '';
    static private int $db_port = 3306;
    static private string $db_charset = 'utf8';
    
    static private function init () {
        if (!self::$connection) {
            try {
                $db_name = self::$db_name; $db_host = self::$db_host; $db_port = self::$db_port; $db_charset = self::$db_charset;
                self::$connection = new \PDO("mysql:dbname={$db_name};host={$db_host};port={$db_port};charset={$db_charset}", self::$db_username, self::$db_password);
                return self::$connection;
            } catch (\PDOException $e) {
                self::$connectionError = $e->getMessage();
                self::$connection = null;
            }
        } else {
            return self::$connection;
        }
    }

    static public function getConnection () {
        return self::init();
    }

    static public function getStatus () {
        $connection = self::init();
        return self::$connection->getAttribute(\PDO::ATTR_CONNECTION_STATUS);
    }

    static public function prepare (string $query, array $fillers=[]) {
        $connection = self::init();
        $prepare = $connection->prepare($query);
        if (count($fillers)>0) {
            foreach ($fillers as $placeholder => $filler) {
                if (is_array($filler)) {
                    $prepare->bindValue($placeholder, ($filler['value'] ?? $filler[0]), ($filler['type'] ?? $filler[1]));
                } else {
                    $prepare->bindValue($placeholder, $filler);
                }
            }
        }
    }

    static public function query (string $query) {
        $connection = self::init();
        preg_match_all('/^(?:.*(insert))|^(?:.*(update))|^(?:.*(delete))|^(?:.*(select))/i', $query, $part, PREG_SET_ORDER);
        if ($part[0] && strtolower($part[0][0]) != 'select'){
            return $connection->exec($query);
        } else {
            return $connection->query($query);
        }
    }

}
