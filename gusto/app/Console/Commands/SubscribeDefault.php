<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use App\Http\Controllers\TracksController;

ini_set('default_socket_timeout', -1);

class SubscribeDefault extends Command{
    protected $signature = 'subscribe:channel-default-event';
    protected $description = 'Subscribe to channel "channel-default-event" to get message which is publish to channel';
    
    public function __construct() {
        parent::__construct();
    }
    
    public function handle(){
//        $channels = array("channel-default-event", "channel-snaplion-event");
        /**
         * @important if number of subscribed channel increase also queue name also split on the bases of channel and subscriber
         * where we need to implement the backend/server setting for those queue
         */
        $channels = array("channel-default-event");
        try {
            $subscriber = "defaultsubscriber";
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

