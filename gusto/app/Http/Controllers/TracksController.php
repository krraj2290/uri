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

        $result = array('status' => 200, 'message' => "Your request noted successfully and send for further insertions.");
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

}
