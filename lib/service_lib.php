<?php
function registerFileServer($server_config_path){
    $server_name = Config::get('server_name',$server_config_path);
    $path = Config::get('path',$server_config_path);
    $conn = DBConnection::getInstance();
    $server_id = md5($server_name.$path);
    $query = "INSERT INTO file_servers (server_id, server_name, path, status, free_disk_space) VALUES
              ('$server_id','$server_name','$path',0,0)";
    return $conn->performQuery($query);
}

function getAllFileServers(){
    $conn = DBConnection::getInstance();
    $query = "CALL getFileServerData();";
    return $conn->performQueryFetchAll($query);
}


function changeFileServerData($server_id,$status,$space){
    $conn = DBConnection::getInstance();
    $query = "CALL changeFileServerData('$server_id',$status,$space);";
    return $conn->performQuery($query);
}

function refreshServerStatus(){
    $servers = getAllFileServers();
    foreach ($servers as $server){
        $result = sendRequest("{$server['path']}/serverStatus",'GET',null,null);
        if ($result['status'] == 'ok'){
            $status = 1;
            $free_disk_space = $result['free_disk_space'];
        } else {
            $status = 0;
            $free_disk_space = null;
        }
        changeFileServerData($server['server_id'],$status,$free_disk_space);
    }
}