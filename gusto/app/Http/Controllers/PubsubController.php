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
            $request = app('request');
            $postVars = $request->all();
//            $jsonData = json_encode($postVars,true);
//            echo $jsonData;
            $this->_adapter->publish('default-snaplion-event-track-channel-queue', $postVars);
//            $this->_adapter->publish('default-snaplion-event-track-channel-queue', 'HELLO WORLD');
//            $this->_adapter->publish('default-snaplion-event-track-channel-queue', ['hello' => 'India']);
//            $this->_adapter->publish('default_test_1', 1);
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
//            if (file_exists($res)) {
//                $s3 = App::make('aws')->createClient('s3');
//                $s3->putObject(array(
//                    'Bucket' => 'YOUR_BUCKET',
//                    'Key' => 'YOUR_OBJECT_KEY',
//                    'SourceFile' => '/the/path/to/the/file/you/are/uploading.ext',
//                ));
//            }
        } catch (Exception $ex) {
            
        }
    }

}
