<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use App\Http\Controllers\TracksController;

ini_set('default_socket_timeout', -1);

class SubscribeRohuma extends Command{
    protected $signature = 'subscribe:channel-rohuma-event';
    protected $description = 'Subscribe to channel "channel-rohuma-event" to get message which is publish to channel';
    
    public function __construct() {
        parent::__construct();
    }
    
    public function handle(){
        $channels = array("channel-rohuma-event");
        
        /**
         * @important if number of subscribed channel increase also queue name also split on the bases of channel and subscriber
         * where we need to implement the backend/server setting for those queue
         */
        try {
//            $subscriber = "rohuma";
            $subscriber = "rohumasubscriber";
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

