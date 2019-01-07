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
            $this->_adapter->publish('my_channel-1-test', 'HELLO WORLD');
            $this->_adapter->publish('my_channel-1-test', ['hello' => 'world']);
            $this->_adapter->publish('my_channel-1-test', 1);
        } catch (Exception $ex) {
            
        }
    }

    public function batch_publish() {
        try {
            $messages = [
                'message 1',
                'message 2',
            ];
            $this->_adapter->publishBatch('my_channel-1-test', $messages);
        } catch (Exception $ex) {
            
        }
    }

    public function subscribe() {
        try {
            $this->_adapter->subscribe('my_channel-1-test', function ($message) {
                var_dump($message);
            });
        } catch (Exception $ex) {
            
        }
    }

}
