<?php

namespace App\Http\Controllers;

use Request;
use Illuminate\Support\Facades\Storage;

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
            $request = app('request');
            $postVars = $request->all();
//            $jsonData = json_encode($postVars,true);
            print_r($postVars);
            $this->_adapter->publish('default-snaplion-event-track-channel-queue', $postVars);
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
            $channels = array('default-snaplion-event-track-channel-queue', 'default_test');

            $this->_adapter->subscribe($channels, function ($message) {
//                print_r(func_get_args());
//                var_dump($message);
                $queueController = new QueuesController();
                $res = $queueController->send_to_queue($message['channel'], $message['message']);

//                var_dump($res);
//                var_dump($channel);
            });
        } catch (Exception $ex) {
            
        }
    }

    public function subscribe_rohuma() {
        try {
            $channels = array('my_channel_1_test', 'default_test_1');

            $this->_adapter->subscribe($channels, function ($message) {
//                print_r(func_get_args());
//                var_dump($message);
                $queueController = new QueuesController();
                $res = $queueController->send_to_queue($message['channel'], $message['message']);

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

    public function sendFileToS3() {
        try {
            $queueController = new QueuesController();
            $res = $queueController->get('file_name_for_s3_upload');
            if (file_exists($res)) {
                echo $storagePath = Storage::disk('s3')->put("uploads", $res, 'public');
            }
        } catch (Exception $ex) {
            
        }
    }

}
