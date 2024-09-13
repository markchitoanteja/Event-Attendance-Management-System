<?php
class Model
{
    private $connection;

    public function __construct($host, $username, $password, $database)
    {
        $this->connection = new mysqli($host, $username, $password, $database);

        if ($this->connection->connect_error) {
            die("Connection failed: " . $this->connection->connect_error);
        }
    }

    public function query($sql)
    {
        $result = $this->connection->query($sql);

        if (!$result) {
            die("Error in query: " . $this->connection->error);
        }

        return $result;
    }

    public function escape($value)
    {
        return $this->connection->real_escape_string($value);
    }

    public function close()
    {
        $this->connection->close();
    }

    public function __destruct()
    {
        $this->close();
    }
}
