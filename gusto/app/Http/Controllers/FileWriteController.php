<?php

namespace App\Http\Controllers;

use Request;

class FileWriteController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $_client;
    public $_adapter;

    public function __construct() {
        
    }

    public function file_write() {
        try {
            
        } catch (Exception $ex) {
            
        }
    }

    public function file_append($fileName, $newData) {
        try {
            $fp = fopen($fileName, 'a'); //opens file in append mode  
            $data = json_encode($newData);
            flock($fp, LOCK_EX);
            fwrite($fp, $data . "\n");
            flock($fp, LOCK_UN);
            fclose($fp);
        } catch (Exception $ex) {
            
        }
    }


}
