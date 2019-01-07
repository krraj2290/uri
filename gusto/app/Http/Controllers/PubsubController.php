<?php

namespace App\Http\Controllers;

use Request;

class PubsubController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $_client;
    public $_adapter;

    public function __construct() {
        $this->_client = new \Predis\Client([
            'scheme' => 'tcp',
            'host' => env('REDIS_HOST'),
            'port' => env('REDIS_PORT'),
            'database' => 0,
            'read_write_timeout' => 0
        ]);
        $this->_adapter = new \Superbalist\PubSub\Redis\RedisPubSubAdapter($this->_client);
    }

    public function publish() {
        try {
            $this->_adapter->publish('my_channel_1_test', 'HELLO WORLD');
            $this->_adapter->publish('default_test', ['hello' => 'India']);
            $this->_adapter->publish('default_test_1', 1);
        } catch (Exception $ex) {
            
        }
    }

    public function batch_publish() {
        try {
            $messages = [
                'message 1',
                'message 2',
            ];
            $this->_adapter->publishBatch('my_channel_1_test', $messages);
        } catch (Exception $ex) {
            
        }
    }

    public function subscribe() { 
        try {
            $channels = array('my_channel_1_test','default_test');
            
            $this->_adapter->subscribe($channels, function ($message) {
//                print_r(func_get_args());
//                var_dump($message);
                $queueController = new QueuesController();
                $res = $queueController->send_to_queue($message['channel'],$message['message']);
                
                var_dump($res);
//                var_dump($channel);
            });
        } catch (Exception $ex) {
            
        }
    }
    
    public function subscribe_rohuma() { 
        try {
            $channels = array('my_channel_1_test','default_test_1');
            
            $this->_adapter->subscribe($channels, function ($message) {
//                print_r(func_get_args());
//                var_dump($message);
                $queueController = new QueuesController();
                $res = $queueController->send_to_queue($message['channel'],$message['message']);
                
                var_dump($res);
            });
        } catch (Exception $ex) {
            
        }
    }

    public function send_to_queue($queue_name, $queueArr) {
        if (empty($queue_name)) {
            return false;
        }
        try {
            $queueData = is_array($queueArr) ? json_encode($queueArr) : $queueArr;
            // send data to queue
            return $this->_adapter->queue($queue_name, $queueData);
        } catch (Exception $ex) {
            $this->_msgs[] = $ex->getMessage();
        }
    }

}
