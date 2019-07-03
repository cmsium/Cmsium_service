<?php
namespace App;


use App\Exceptions\FileServerConnectError;

class FileServerClient {
    public $url;
    public $port;
    public $timeout;
    public $client;

    public function __construct($url, $port, $timeout) {
        $this->url = $url;
        $this->port = $port;
        $this->timeout = $timeout;
    }

    public function connect() {
        if (!$this->client) {
            $this->client = new \Swoole\Coroutine\Http\Client($this->url, $this->port);
        }
    }

    public function getStatusAsync() {
        $this->connect();
        $this->client->set(['timeout' => $this->timeout]);
        $this->client->setDefer();
        $this->client->get("/status");
    }

    public function recv() {
        $result = $this->client->recv();
        if ($result === false){
            throw new FileServerConnectError();
        }
        $status = json_decode($this->client->body, true);
        return $status;
    }
}