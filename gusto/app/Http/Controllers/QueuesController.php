<?php

namespace App\Http\Controllers;

use Request;

class QueuesController extends Controller {

    public $_obj;
    protected $_queue_name;
    protected $_subscriber;
    protected $_msgs;
    
    public $_waitingSeconds = 5;
    public $_killWaitingProcessCount = 10;

    /**
     * Create a new controller instance.
     * @return void
     */
    public function __construct() {
        try {
            $this->_obj = app('redis');
            $this->_obj->connect(env('REDIS_HOST'), env('REDIS_PORT'));
        } catch (Exception $ex) {
            $this->_msgs[] = $ex->getMessage();
        }
    }

    public function ping() {
        echo " Redis Server is running: " . $this->_obj->ping();
    }

    public function set($key, $value) {
        if (empty($key) || empty($value)) {
            return false;
        }
        try {
            return $this->_obj->set($key, $value);
        } catch (Exception $ex) {
            $this->_msgs[] = $ex->getMessage();
        }
    }

    public function get($key) {
        if (empty($key)) {
            return false;
        }
        try {
            return $this->_obj->get($key);
        } catch (Exception $ex) {
            $this->_msgs[] = $ex->getMessage();
        }
    }

    public function send_to_queue($queue_name, $queueArr) {
        if (empty($queue_name)) {
            return false;
        }
        $this->_queue_name = $queue_name;
        try {
            $queueData = is_array($queueArr) ? json_encode($queueArr) : $queueArr;
            
            echo "send_to_queue:$queue_name:data:\n";
            echo "\n\n $queueData \n\n";
            // send data to queue
            return $this->_obj->lPush($queue_name, $queueData);
        } catch (Exception $ex) {
            $this->_msgs[] = $ex->getMessage();
        }
    }

    public function consume($queue_name) {
        if (empty($queue_name)) {
            return false;
        }
        if (empty(trim($queue_name))) { // validate queue name
            throw new Exception("Empty queue name supplied");
        }
        $this->_queue_name = $queue_name;
        $queueData = $this->_obj->brpoplpush($queue_name, $this->_processingQueue(), 2);
        return ($queueData !== null) ? $queueData : false;
    }
    
    
    
    /**
     * Remove the enteries from processing queue when successful
     * @param type $data
     * @return type
     */
    public function _success($queueName,$data) {
        // check if queue data is empty then no need to process
        if(empty(trim($data))){
            return false;
        }
        // check if queue data is an array then convert it into string.
        if (is_array($data)) {
            $data = json_encode($data);
        }
        // check if queue name is empty or not
        if (empty(trim($queueName))) { // validate queue name
            throw new Exception("Empty queue name supplied");
        }
        $this->_queue_name = $queueName;
//        echo "<br />removing from processing queue '".$this->_processingQueue()."', data : '$data'";
        // remove data from processing queue
        $this->_obj->lrem($this->_processingQueue(), $data, 1);
        return true;
    }

    /**
     * 
     * @param type $data
     * @return type
     */
    public function _fail($queueName,$data) {
        // check if queue data is empty then no need to process
        if(empty(trim($data))){
            return false;
        }
        // check if queue data is an array then convert it into string.
        if (is_array($data)) {
            $data = json_encode($data);
        }
        // check if queue name is empty or not
        if (empty(trim($queueName))) { // validate queue name
            throw new Exception("Empty queue name supplied");
        }
        $this->_queue_name = $queueName;
        // remove data from processing queue
        $this->_obj->lrem($this->_processingQueue(), $data, 1);
        // add data from failed queue
        $this->_obj->lpush($this->_failedQueue(), $data);
        return true;
    }
    

    public function get_all_queue_data($queue_name) {
        if (empty($queue_name)) {
            return false;
        }
        $this->_queue_name = $queue_name;
        try {
            $queueVals = $this->_obj->lrange($queue_name, 0, -1);
            return $queueVals;
        } catch (Exception $ex) {
            $this->_msgs[] = $ex->getMessage();
        }
    }

    /**
     * send consume data to processing state
     * @return type
     */
    public function _processingQueue() {
        return $this->_queue_name . '-inprocess';
    }

    /**
     * send data to failed queue if not successful
     * @return type
     */
    public function _failedQueue() {
        return $this->_queue_name . "-failed";
    }

    public function queue_data_count($queue_name) {
        try {
            if (empty(trim($queue_name))) { // validate queue name
                throw new Exception("Empty queue name supplied");
            }
            $this->_queue_name = $queue_name;

            return $this->_obj->lLen($this->_queue_name);
        } catch (Exception $ex) {
            $this->_msg[] = $ex->getMessage();
        }
    }

    public function _deleteAll($queue_name) {
        if (empty(trim($queue_name))) { // validate queue name
            throw new Exception("Empty queue name supplied");
        }
        $this->_queue_name = $queue_name;
        $this->_obj->del($this->_queue_name);
        $this->_obj->del($this->_failedQueue());
        $this->_obj->del($this->_processingQueue());
    }

    public function _delete($queue_name) {
        if (empty(trim($queue_name))) { // validate queue name
            throw new Exception("Empty queue name supplied");
        }
        $this->_queue_name = $queue_name;
        $this->_obj->del($this->_queue_name);
    }

    public function publish($channel, $message) {
        if (empty($channel) || empty($message)) {
            return false;
        }
        try {
            $message = is_array($message) ? json_encode($message) : $message;

            return $this->_obj->publish($channel, $message);
        } catch (Exception $ex) {
            $this->_msgs[] = $ex->getMessage();
        }
    }
    
    public function subscribe($channels,$subscriber="default") {
        try {
            if (empty($channels)) {
                return false;
            }
            $this->_subscriber = $subscriber;
            try{
                $returnResult = $this->_obj->subscribe($channels, function($message,$channel){
                    $this->subscribe_callback($channel, $this->_subscriber, $message);
                });
            } catch (Exception $ex) {
                echo "Error::" . $ex->getMessage();
            }
        } catch (Exception $ex) {
            $this->_msgs[] = $ex->getMessage();
        }
    }
    
    public function subscribe_callback($channel, $subscriber, $message) {
        try {
            // send data to queue for further processing
            $queue_name = "$subscriber-$channel-queue";
            echo "\n Sending data to Queue:$queue_name, Message::\n";
            
            $objQueues = new PubsubController();
            $resp = $objQueues->send_to_queue($queue_name,$message);
            
            echo "\n PubsubController::send_to_queue:resp:: \n";
            print_r($resp);
            echo "\n\n";
        } catch (Exception $ex) {
            $this->_msgs[] = $ex->getMessage();
        }
    }

    public function getMessage() {
        return $this->_msg;
    }

}
