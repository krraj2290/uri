<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use App\Http\Controllers\TracksController;
use App\Http\Controllers\QueuesController;

ini_set('default_socket_timeout', -1);

class QueueRohumaEvent extends Command {

    protected $signature = 'queue:channel-rohuma-event';
    protected $_queue_name = "rohumasubscriber-channel-rohuma-event-queue";
    protected $description = 'Process QUEUE "channel-rohuma-event-queue" to get message and save to S3 File';
    protected $_killProcessCount = 5; // kill process after 50 attempt

    public function __construct() {
        parent::__construct();
    }

    public function handle() {
        try {
            // consume the queue
            $this->_consume($this->_queue_name);
        } catch (Exception $e) {
            echo "\n Errors:" . $e->getMessage();
        }
        echo "\n";
    }

    public function _consume($queueName) {
        try {
            if (empty($queueName)) {
                throw new Exception('Empty Queue name supplied.');
            }
            // make redis queue class 
            $objQueues = new QueuesController();
            $i = 0;
            do {
                if ($i > 0) {
                    echo "\n$i). Queue:'$queueName' Waiting for next job : \n";
                }
                // get the data from queue list
                $queueData = $objQueues->_obj->brpoplpush($queueName, "", $objQueues->_waitingSeconds);
                if (!empty($queueData)) {
                    try {
                        echo "\n QueuesController::$queueName:consume:resp:\n";
                        print_r($queueData);
                        echo "\n\n";

                        $queryStrArr = json_decode($queueData, true);
                        $queueDataArr = array();
                        // rearrange the key names
                        foreach ($queryStrArr as $k => $v) {
                            $nk = str_replace(array('"', "'"), '', $k);
                            $queueDataArr[$nk] = $v;
                        }
                        try {
                            // save the entry
                        } catch (Exception $ex) {
                            
                        }
                    } catch (Exception $ex) {
                        echo "\n Queue-Error:" . $ex->getMessage() . "\n\n";
                    }
                } else {
                    echo "\nEmpty Queue : " . date('Y-m-d H:i:s') . "\n\n";
                }
                // break the request after 100th attempt
                if ($i == $this->_killProcessCount) {
                    echo "\nBreaking the connection with queue server after '$this->_killProcessCount' attempt.\n\n";
                    break;
                }
                $i++;
            } while (true);
            return true;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

}
