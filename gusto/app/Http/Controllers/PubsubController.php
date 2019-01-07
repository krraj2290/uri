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
            'host' => '127.0.0.1',
            'port' => 6379,
            'database' => 0,
            'read_write_timeout' => 0
        ]);
        $this->_adapter = new \Superbalist\PubSub\Redis\RedisPubSubAdapter($this->_client);
    }

    public function publish() {
        try {
            $this->_adapter->publish('my_channel_1_test', 'HELLO WORLD');
//            $this->_adapter->publish('my_channel_1_test', ['hello' => 'world']);
//            $this->_adapter->publish('my_channel_1_test', 1);
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
            $this->_adapter->subscribe($channels, function ($message,$channel) {
                print_r(func_get_args());
//                var_dump($message);
//                var_dump($channel);
            });
        } catch (Exception $ex) {
            
        }
    }

}
