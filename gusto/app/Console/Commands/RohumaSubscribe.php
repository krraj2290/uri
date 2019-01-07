<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use App\Http\Controllers\TracksController;

ini_set('default_socket_timeout', -1);

class RohumaSubscribe extends Command{
    protected $signature = 'channel:subscriberohuma';
    protected $description = 'Subscribe to channel "snaplion-event-track-channel, snaplion-event-track-channel-2" to get message which is publish to channel';
    
    public function __construct() {
        parent::__construct();
    }
    
    public function handle(){
        $channels = array("snaplion-event-track-channel","snaplion-event-track-channel-2");
        try {
            $subscriber = "rohuma";
            $objQueues = new TracksController();
            $message = $objQueues->subscribe_to_channel($channels,$subscriber);
            
            echo "\n\n TracksController:subscribe_to_channel:resp: \n\n";
            print_r($message);
            
        } catch (Exception $e) {
            echo "\n Errors:" . $e->getMessage();
        }
        echo "\n";
    }
    
}
