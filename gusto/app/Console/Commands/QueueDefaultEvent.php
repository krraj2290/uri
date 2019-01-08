<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use App\Http\Controllers\TracksController;
use App\Http\Controllers\QueuesController;

use App\Http\Controllers\FileWriteController;
use App\Http\Controllers\PubsubController;

ini_set('default_socket_timeout', -1);

class QueueDefaultEvent extends Command {

    protected $signature = 'queue:channel-default-event';
    protected $_queue_name = "defaultsubscriber-channel-default-event-queue";
    protected $description = 'Process QUEUE "channel-default-event-queue" to get message and save to S3 File';
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
                            
                            //write file 
                            $file_name = "/tmp/" . $queueName . ".json";
                            $file_name1 = "/tmp/" . $queueName . "_1.json";
                            $bfileSize1 = 0;
                            $bfileSize = 0;
                            if (file_exists($file_name)) {
                                $bfileSize = filesize($file_name);
                            }
                            if (file_exists($file_name1)) {
                                $bfileSize1 = filesize($file_name1);
                            }
                            if (file_exists($file_name1) && ($bfileSize1 / (1024 * 1024)) > 10) {
                                $file_name = "/tmp/" . $queueName . ".json";
                            }
                            if (file_exists($file_name) && ($bfileSize / (1024 * 1024)) > 10) {
                                $file_name = "/tmp/" . $queueName . "_1.json";
                            }
                            
                            echo "\n Write api payload to log file file_name:$file_name \n";
                            
                            $fileWriteObj = new FileWriteController();
                            $fileWriteObj->file_append($file_name, $queueDataArr);
                            $bytesSize = filesize($file_name);
                            if (($bytesSize / (1024 * 1024)) > 10) {
                                //Send file name to Queue for process and send to s3
                                $pubsubObj = new PubsubController();
                                $pubsubObj->send_to_queue('file_name_for_s3_upload', $file_name);
    //                           unlink($file_name);
                            }
                            
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
