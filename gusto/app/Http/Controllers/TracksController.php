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
//        print_r($request->headers);
//        print_r($request->headers->get('authorization'));
//        print_r($request->headers->get('s-id'));
//        print_r($request->getMethod());
        
//        print_r($request->all());
//        print_r($request->toArray());echo "<br />";
//        print_r($request->get('app_id'));echo ", ";
//        print_r($request->input('fan_id'));
        
        $postVars = $request->all();
        
        $channel = "snaplion-event-track-channel";
        
        $microtime = microtime(true);
        $microtime = str_replace(".", ":", $microtime);
        $postVars['guid'] = $postVars['app_id'] . "-" . $postVars['fan_id'] . "-". $postVars['event'] . "-" . $microtime;
        $postVars['channel'] = $channel;
        
        // send data to channel
        $publish_resp = $this->publish_to_channel($postVars, $channel);
        
        $channel1 = "snaplion-event-track-channel-1";
        $postVars['channel'] = $channel1;
        $publish_resp1 = $this->publish_to_channel($postVars, $channel1);
//        
        $channel2 = "snaplion-event-track-channel-2";
        $postVars['channel'] = $channel2;
        $publish_resp2 = $this->publish_to_channel($postVars, $channel2);
        
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
        $result = array(
            'status' => 200, 'message' => "Your request noted successfully.",
            'data' => $events
        );
        return $result;
    }

    public function checkredis() {
        try {
            $objQueues = new QueuesController();
            return $objQueues->ping();
        } catch (Exception $ex) {
            echo "Error:redis::" . $ex->getMessage();
        }
        die;
    }

    public function queue() {
        $queue_name = "user-events-local-queue";
        $objQueues = new QueuesController();
        return $objQueues->queue_data_count($queue_name);
        //return $objQueues->consume($queue_name);
    }

    public function publish_to_channel($message, $channel) {
        $channel = empty($channel) ? "snaplion-default-event-track-channel" : $channel;
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

}
