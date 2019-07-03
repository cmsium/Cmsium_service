<?php
namespace App;

use App\Exceptions\FileServerConnectError;
use App\Exceptions\ValidationException;
use Validation\Validator;

class FileServersManager {
    public $db;
    public $conn;
    public $dbTableName = "file_servers";

    public $table;

    public $priorities = [
        'status' => ['weight' => 0.7, 'min' => 0, 'max' => 1],
        'space' => ['weight' => 0.1, 'min' => 0, 'max' => 1],
    ];

    public function __construct($db = null, $table = null) {
        if ($db){
            $this->db = $db;
        }
        if ($table){
            $this->table = $table;
        }
    }

    public function dbConnect() {
        if (!$this->conn) {
            $this->conn = new \Swoole\Coroutine\MySQL();
            $this->conn->connect($this->db);
        }
    }

    public function dbGetAll() {
        $this->dbConnect();
        $query = "SELECT * from {$this->dbTableName};";
        $data = $this->conn->query($query);
        $servers = [];
        if ($data) {
            foreach ($data as $server){
                $servers[$server['id']] = new FileServer($server, $this->db, $this->table);
            }
        }
        return $servers;
    }

    public function swooleSaveAll() {
        foreach ($this->dbGetAll() as $server){
            $server->swooleSave();
        }
    }

    public function updateStatus() {
            $servers = [];
            foreach ($this->table as $data) {
                    $server = new FileServer($data, $this->db, $this->table);
                    $server->addFileServerClient(new FileServerClient($server->ip, $server->port, 0.3));
                    $server->getFileServerClient()->getStatusAsync();
                    $servers[] = $server;
            }

            foreach ($servers as $server) {
                try{
                    $result = $server->getFileServerClient()->recv();
                    $validator = new Validator($result,"getStatus");
                    $data = $validator->get();
                    if ($errors = $validator->errors()){
                        throw new ValidationException("Validation error: ".json_encode($errors));
                    }
                } catch (FileServerConnectError $e){
                    $data = ['status' => 0];
                } catch (ValidationException $ex) {
                    //TODO logs
                    var_dump($ex->getMessage());
                    $data = ['status' => 0];
                }
                $server->addData($data);
                $server->swooleSave();
                //TODO batch db update;
                $server->dbUpdate();
            }

            //TODO logs
            unset($servers);
    }

}