<?php
/**
 * Database Class
 *
 * Contains connection information to query PostgresSQL.
 */

class Database {
    private $dbConnector;

    /**
     * Constructor
     *
     * Connects to PostgresSQL
     */
    public function __construct() {
        $host = Config::$db["host"];
        $user = Config::$db["user"];
        $database = Config::$db["database"];
        $password = Config::$db["pass"];
        $port = Config::$db["port"];

        $conn_string = "host=$host port=$port dbname=$database user=$user password=$password";
        $this->dbConnector = pg_connect($conn_string);
        
        if (!$this->dbConnector) {
            throw new Exception("Failed to connect to database. Connection string: $conn_string");
        }
    }

    /**
     * Query
     *
     * Makes a query to postgres and returns an array of the results.
     * The query must include placeholders for each of the additional
     * parameters provided.
     */
    public function query($query, $params = []) {
        if (empty($params)) {
            $res = pg_query($this->dbConnector, $query);
        } else {
            $res = pg_query_params($this->dbConnector, $query, $params);
        }

        if ($res === false) {
            throw new Exception("Query failed: " . pg_last_error($this->dbConnector) . "\nQuery: $query\nParams: " . print_r($params, true));
        }

        $result = pg_fetch_all($res);
        return $result === false ? [] : $result;
    }

    /**
     * Fetch a single row from the query result
     */
    public function fetch($query, $params = []) {
        if (empty($params)) {
            $res = pg_query($this->dbConnector, $query);
        } else {
            $res = pg_query_params($this->dbConnector, $query, $params);
        }

        if ($res === false) {
            throw new Exception("Query failed: " . pg_last_error($this->dbConnector) . "\nQuery: $query\nParams: " . print_r($params, true));
        }

        return pg_fetch_assoc($res);
    }

    /**
     * Fetch all rows from the query result
     */
    public function fetchAll($query, $params = []) {
        return $this->query($query, $params);
    }

    /**
     * Get the last inserted ID from a sequence
     */
    public function lastInsertId($sequence) {
        $result = $this->fetch("SELECT currval($1) as id", [$sequence]);
        return $result['id'];
    }
}
?> 