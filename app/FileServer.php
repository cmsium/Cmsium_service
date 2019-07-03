<?php
namespace App;

use App\Exceptions\MysqlException;
use App\Exceptions\SwooleSaveException;

class FileServer {

    public $db;
    public $conn;
    public $dbTableName = "file_servers";

    public $table;

    public $file_server_client;

    public $data;

    public function __construct(array $data, $db = null, $table = null) {
        $this->data = $data;
        if ($db){
            $this->db = $db;
        }
        if ($table){
            $this->table = $table;
        }
    }

    public function __get($name){
        return $this->data[$name] ?? null;
    }

    public function __set($name, $value) {
        $this->data[$name] = $value;
    }

    public function addData($data) {
        $this->data = array_merge($this->data, $data);
    }

    public function exist() {
        return $this->table->exist($this->id);
    }

    public function swooleSave() {
        $this->table[$this->id] = $this->data;
        if (!$this->exist()){
            throw new SwooleSaveException();
        }
    }

    public function swooleDelete() {
        $this->table->del($this->id);
    }

    public function dbConnect() {
        if (!$this->conn) {
            $this->conn = new \Swoole\Coroutine\MySQL();
            $this->conn->connect($this->db);
        }
    }

    public function dbSave() {
        $this->dbConnect();
        $query = "INSERT INTO {$this->dbTableName} (".
            implode(', ', array_keys($this->data)).
            ") VALUES (".
            rtrim(str_repeat("?,", count($this->data)), ",").
            ")";
        $stmt = $this->conn->prepare($query);
        $result = $stmt->execute(array_values($this->data));
        if ($result === false){
            throw new MysqlException($this->conn->error);
        }
        return $result;
    }

    public function dbUpdate() {
        $updates=[];
        foreach ($this->data as $key => $value){
            $updates[] = "$key = ?";
        }
        $this->dbConnect();
        $stmt = $this->conn->prepare("UPDATE {$this->dbTableName} SET ".implode(', ', $updates)." WHERE id = ?");
        $preps = array_values($this->data);
        $preps[] = $this->id;
        $result = $stmt->execute($preps);
        if ($result === false){
            throw new MysqlException($this->conn->error);
        }
        return $result;
    }

    public function dbDelete(){
        $this->dbConnect();
        $query = "DELETE from {$this->dbTableName} where id='{$this->id}';";
        $this->conn->query($query);
    }

    public function addFileServerClient($client) {
        $this->file_server_client = $client;
    }

    public function getFileServerClient() {
        return $this->file_server_client;
    }
}