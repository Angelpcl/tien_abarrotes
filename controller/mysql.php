<?php

class mysql {

    private $conn;
    public $error;
    private $msgerror;
    private $rows;
    private $host = "localhost"; // Nombre del servidor
    private $user = "root"; // Usuario de la base de datos
    private $pass = ""; // Contraseña de la base de datos
    private $database = "crowd_interactive"; // Nombre de la base de datos

    function __construct() {
        $this->connect();
    }

    private function connect() {
        $this->conn = @mysqli_connect($this->host, $this->user, $this->pass, $this->database);
        if (!$this->conn) {
            echo "No es posible conectar al servidor: " . mysqli_connect_error();
            die();
        }
    }

    private function utf8_encode_deep(&$input) {
        if (is_string($input)) {
            $input = utf8_encode($input);
        } elseif (is_array($input)) {
            foreach ($input as &$value) {
                $this->utf8_encode_deep($value);
            }
        } elseif (is_object($input)) {
            $vars = array_keys(get_object_vars($input));
            foreach ($vars as $var) {
                $this->utf8_encode_deep($input->$var);
            }
        }
    }

    public function query($SQL) {
        $result = @mysqli_query($this->conn, $SQL);

        if (!$result) {
            $this->msgerror = mysqli_errno($this->conn) . ': ' . mysqli_error($this->conn);
            $this->error = false;
            return false;
        }

        switch ($this->typeProcess($SQL)) {
            case 1: // SELECT
                $arrayDatos = [];
                while ($arrayDatos[] = mysqli_fetch_array($result, MYSQLI_ASSOC));
                $this->utf8_encode_deep($arrayDatos);
                array_pop($arrayDatos); // Elimina el último elemento vacío
                $this->rows = $arrayDatos;
                return $arrayDatos;
            case 2: // UPDATE
            case 3: // DELETE
            case 4: // INSERT
                return $result;
            default:
                return false;
        }
    }

    private function typeProcess($SQL) {
        $type = 0;
        $SQL = strtoupper($SQL);
        if (strstr($SQL, "SELECT")) $type = 1;
        if (strstr($SQL, "UPDATE")) $type = 2;
        if (strstr($SQL, "DELETE")) $type = 3;
        if (strstr($SQL, "INSERT")) $type = 4;
        return $type;
    }

    public function Begin() {
        @mysqli_query($this->conn, "START TRANSACTION;");
    }

    public function Commit() {
        @mysqli_query($this->conn, "COMMIT;");
        $this->closeConnection();
    }

    public function Rollback() {
        @mysqli_query($this->conn, "ROLLBACK;");
        $this->closeConnection();
    }

    public function insert_id() {
        return mysqli_insert_id($this->conn);
    }

    public function RealEscape($string) {
        return mysqli_real_escape_string($this->conn, $string);
    }

    public function message_error() {
        return $this->msgerror;
    }

    public function details_error() {
        return $this->error;
    }

    private function closeConnection() {
        mysqli_close($this->conn);
    }
}
