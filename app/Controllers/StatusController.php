<?php
namespace App\Controllers;


/**
 * @description 
 */
class StatusController {
    use \Router\Routable;

    /**
     * @summary Get file servers info
     * @description Get info about availible files servers
     */
    public function getStatus () {
        $manager = app()->file_servers_manager;
        return $manager->getStatus(app()->priority_handler);
    }
}