<?php

namespace App\Http\Controllers;

use Request;

class TracksController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        
    }

    /**
     * 
     * @param type $event_id
     * @return int
     */
    public function getEvent($event_id) {
        $result = array('status' => 200, 'message' => "Your request noted successfully.", 'data' => array('name' => 'event-' . $event_id));
        return $result;
    }

    /**
     * 
     * @return string
     */
    public function create() {
        $request = app('request');
        $postVars = $request->all();
        
        $channel = "channel-default-event";
        
        $microtime = microtime(true);
        $microtime = str_replace(".", ":", $microtime);
        $postVars['guid'] = $postVars['app_id'] . "-" . $postVars['fan_id'] . "-". $postVars['event'] . "-" . $microtime;
        $postVars['channel'] = $channel;
        
        // send data to channel
        $publish_resp = $this->publish_to_channel($postVars, $channel);

        $status = 500;
        if($publish_resp){
            $status = 200;
        }
        $result = array('status' => $status, 'message' => "Your request noted successfully and send for further insertions.", 'data' => $postVars);
        return $result;
    }

    /**
     * 
     * @return string
     */
    public function update() {
        $result = array('status' => 200, 'message' => "Your request noted successfully and send for further updation.");
        return $result;
    }

    /**
     * 
     * @return int
     */
    public function delete() {
        $result = array('status' => 200, 'message' => "Your request noted successfully and send for further deletion.");
        return $result;
    }

    /**
     * 
     * @return int
     */
    public function getAllEvents() {
        $events = array();
        $events[] = array('name' => 'event-' . rand(100000, 10000000));
        $events[] = array('name' => 'event-' . rand(100000, 10000000));
        $events[] = array('name' => 'event-' . rand(100000, 10000000));
        $result = array( 'status' => 200, 'message' => "Your request noted successfully.", 'data' => $events );
        return $result;
    }

    public function checkredis() {
        try {
            $objQueues = new QueuesController();
            return $objQueues->ping();
        } catch (Exception $ex) {
            echo "Error:redis::" . $ex->getMessage();
        }
    }

    public function publish_to_channel($message, $channel) {
        if(empty($channel) || empty($message)){
            return false;
        }
        $objQueues = new QueuesController();
        return $objQueues->publish($channel, $message);
    }
    
    public function subscribe_to_channel($channels,$subscriber="default") {
        if(empty($channels)){
            return false;
        }
        $objQueues = new QueuesController();
        return $objQueues->subscribe($channels,$subscriber);
    }
    
    public function subscribed_queue_process($queue_name) {
        if(empty($queue_name)){
            return false;
        }
        $objQueues = new QueuesController();
        $queue_data = $objQueues->consume($queue_name);
        
        echo "\n TracksController::subscribed_queue_process:$queue_name:consume:resp:\n";
        print_r($queue_data);
        echo "\n\n";
    }

}
