<?php

namespace App\Http\Controllers;

use Request;

class QueuesController extends Controller {

    protected $_obj;
    protected $_queue_name;
    protected $_msgs;

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
        try {
            $queueData = is_array($queueArr) ? json_encode($queueArr) : $queueArr;
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
        $queueData = $this->_obj->brpoplpush($queue_name, $this->_processingQueue(), 2);
        return ($queueData !== null) ? $queueData : false;
    }

    public function get_all_queue_data($queue_name) {
        if (empty($queue_name)) {
            return false;
        }
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

    public function getMessage() {
        return $this->_msg;
    }

}
