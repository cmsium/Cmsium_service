<?php

function chooseFileServer (){
    $file_servers = getAllFileServers();
    usort($file_servers,function ($a,$b) {return ($a['disk_space'] <=> $b['disk_space']);});
    $server =  array_shift($file_servers);
    echo json_encode(["status"=>"ok","server"=>$server['path']]);
    return;
}

function getAllFiles(){
    checkAuth();
    $controller = Config::get('controller_url');
    $host_url = Config::get('host_url');
    $result = sendRequest("$controller/getAllFiles?columns=file_id.path.file_name",'GET',null,null);
    if ($result['status'] == 'error')
        return false;
    array_shift($result);
    $str =  "<html><body><table><tr><td>file id</td><td>file name</td><td>file path</td></tr>";
    foreach ($result as $file){
        $str .= "<tr><td>{$file['file_id']}</td><td>{$file['file_name']}</td><td>{$file['path']}</td><td><a href='http://$controller/get?id={$file['file_id']}'>скачать</a></td><td><a href='http://$controller/delete?id={$file['file_id']}'>удалить</a></td><td><a href='http://$host_url/moveFilePage?id={$file['file_id']}&path={$file['path']}'>переместить</a></td></tr>";
    }
    $str .=  "</table></body></html>";
    echo $str;
}


function moveFilePage($file_id,$path){
    checkAuth();
    $validator = Validator::getInstance();
    $file_id = $validator->Check('Md5Type',$file_id,[]);
    if ($file_id === false){
        echo json_encode(["status" => "error", "message" => "Wrong file id format"]);
        exit;
    }
    $path = $validator->Check('Path',$path,[]);
    if ($path === false){
        echo json_encode(["status" => "error", "message" => "Wrong file path format"]);
        return;
    }
    $host = Config::get('host_url');
    $servers = refreshServerStatus();
    $str = "<html><body><p>Сервер для перемещения</p><form action='http://$host/moveFile' method='post'><input type='hidden' name='file_id' value='$file_id'><input type='hidden' name='path' value='$path'><select name='to'>";
    foreach ($servers as $server){
        $str .= "<option value='{$server['path']}'>{$server['server_name']}</option>";
    }
    $str .=  "</select><br/><input type='submit'/></form></body></html>";
    echo $str;
}


function moveFile($file_id,$from,$to){
    checkAuth();
    $validator = Validator::getInstance();
    $file_id = $validator->Check('Md5Type',$file_id,[]);
    if ($file_id === false){
        echo json_encode(["status" => "error", "message" => "Wrong file id format"]);
        exit;
    }
    $from = $validator->Check('Path',$from,[]);
    if ($from === false){
        echo json_encode(["status" => "error", "message" => "Wrong file path format"]);
        return;
    }
    $to = $validator->Check('Path',$to,[]);
    if ($to === false){
        echo json_encode(["status" => "error", "message" => "Wrong file path format"]);
        return;
    }
    $exp = explode('//',$from);
    $server = $exp[0];
    $path = $exp[1];
    $response = sendRequest("$server/moveFile?server=$to&file=$path",'GET',null,null);
    switch ($response['status']){
        case 'error':
            echo json_encode(["status" => "error", "message" => $response['message']]);
            exit;
        case 'ok':
            $new_path = $response['file_path'];
    }
    $controller = Config::get('controller_url');
    $response = sendRequest("$controller/updateData?file_id=$file_id&path=$new_path",'GET',null,null);
    switch ($response['status']) {
        case 'error':
            echo json_encode(["status" => "error", "message" => $response['message']]);
            exit;
        case 'ok':
            echo json_encode(["status" => "ok", "message" => "File successfully moved"]);
            exit;
    }
}


function fileServers(){
    checkAuth();
    $host = Config::get('host_url');
    $servers = refreshServerStatus();
    $str = "<html><body><p><a href='http://$host/addServerPage'>Добавить сервер</a></p><table><tr><td>name</td><td>status</td><td>disk space</td></tr>";
    foreach ($servers as $server){
        $status = $server['status'] ? 'online' : 'offline';
        $str .= "<tr><td><a href='http://$host/getServerFiles?server={$server['server_name']}'>{$server['server_name']}</a></td><td>$status</td><td>{$server['disk_space']}</td><td><a href='http://$host/deleteServer?id={$server['server_id']}'>удалить</a></td></tr>";
    }
    $str .=  "</table></body></html>";
    echo $str;
}

function getServerFiles($server){
    checkAuth();
    $validator = Validator::getInstance();
    $server = $validator->Check('Path',$server,[]);
    if ($server === false){
        echo "Wrong server format";
        exit;
    }
    $result = sendRequest("$server/getAllFiles",'GET',null,null);
    if ($result['status'] == 'error')
        return false;
    array_shift($result);
    $str =  "<html><body><table><tr><td>file path</td></tr>";
    foreach ($result as $file){
        $str .= "<tr><td>$file</td><td><a href='http://$server/deleteFile?path=$file'>delete</a></td></tr>";
    }
    $str .=  "</table></body></html>";
    echo $str;
}

function clearFiles(){
    checkFilesConformity(true);
}

function addServer($name,$path){
    checkAuth();
    $validator = Validator::getInstance();
    $name = $validator->Check('Path',$name,[]);
    if ($name === false){
        echo json_encode(["status" => "error", "message" => "Wrong server name format"]);
        exit;
    }
    $path = $validator->Check('Path',$path,[]);
    if ($path === false){
        echo json_encode(["status" => "error", "message" => "Wrong server id format"]);
        exit;
    }
    if (!registerFileServer(null,['name'=>$name,'path'=>$path])){
        echo json_encode(["status" => "error", "message" => "Server addition error"]);
        exit;
    }
    echo json_encode(["status" => "ok", "message" => "Server addition success"]);
}

function addServerPage(){
    checkAuth();
    $host = Config::get('host_url');
    $str = "
<htlm>
    <body>
        <form action='http://$host/addServer' method='post'>
            Имя сервера: <input type='text' name='server_name'><br/>
            Адрес сервера: <input type='text' name='server_path'><br/>
            <input type='submit'>
        </form>
    </body>
</htlm>";
    echo $str;
}

function deleteServer($server_id){
    checkAuth();
    $validator = Validator::getInstance();
    $server_id = $validator->Check('Md5Type',$server_id,[]);
    if ($server_id === false){
        echo json_encode(["status" => "ok", "message" => "Wrong server id format"]);
        exit;
    }
    if (unregisterFileServer($server_id)) {
        echo json_encode(["status" => "ok", "message" => "Server successfully deleted"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Server delete error"]);
    }
}

function admin(){
    checkAuth();
    $server = Config::get('host_url');
    $sandbox = Config::get('sandbox_url');
    echo "
<html>
    <body>
        <ul>
            <li><a href='http://$sandbox/testFileForm'>Создать файл</a></li>
            <li><a href='http://$server/getAllFiles'>Список файлов</a></li>
            <li><a href='http://$server/fileServers'>Файловые сервера</a></li>
            <li><a href='http://$server/clearFiles'>Очистить мусорные файлы</a></li>
        </ul>
    </body>    
</html>";
}