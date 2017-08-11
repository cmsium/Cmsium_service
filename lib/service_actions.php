<?php
function chooseFileServer (){
    $file_servers = getAllFileServers();
    usort($file_servers,function ($a,$b) {return -($a['free_disk_space'] <=> $b['free_disk_space']);});
    $server =  array_shift($file_servers);
    echo json_encode(["status"=>"ok","server"=>$server['path']]);
    return;
}
