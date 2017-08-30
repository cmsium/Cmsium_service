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

function getAllControllerFiles(){
    $controller = Config::get('controller_url');
    $result = sendRequest("$controller/getAllFiles?columns=path.file_id",'GET',null,null);
    if ($result['status'] == 'error')
        return false;
    $files = [];
    array_shift($result);
    foreach ($result as $value){
        $exp = explode('//',$value['path']);
        $files[$exp[0]][$value['file_id']] = '/'.$exp[1];
    }
    return $files;
}

function getAllServersFiles(){
    $servers = getAllFileServers();
    $server_names = [];
    foreach ($servers as $server){
       $result = sendRequest("{$server['path']}/getAllFiles",'GET',null,null);
       if ($result['status'] == 'error')
           return false;
       array_shift($result);
       $server_names[$server['path']] = $result;
    }
    return $server_names;
}

function checkFilesConformity(){
    $controller_files = getAllControllerFiles();
    $server_files = getAllServersFiles();
    if (!$controller_files or !$server_files)
        return;
    foreach ($controller_files as $server => $file){
        $controller_files_to_be_deleted = array_diff($controller_files[$server],$server_files[$server]);
        if (!empty($controller_files_to_be_deleted)) {
            $controller = Config::get('controller_url');
            foreach ($controller_files_to_be_deleted as $id => $path) {
                sendRequest("$controller/delete?id=$id", 'GET', null, null);
            }
        }
        $server_files_to_be_deleted = array_diff($server_files[$server],$controller_files[$server]);
        if (!empty($server_files_to_be_deleted)) {
            foreach ($server_files_to_be_deleted as $path) {
                sendRequest("$server/deleteFile?path=$path", 'GET', null, null);
            }
        }
    }
}