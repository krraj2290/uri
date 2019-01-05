<?php

namespace App\Http\Controllers;



use Request;
use Illuminate\Support\Facades\Redis;

class TracksController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        
        env('APP_DEBUG', true);
        
//        $request = app('request');
//        print_r($request->headers);
//        print_r($request->headers->get('authorization'));
//        print_r($request->headers->get('s-id'));
//        print_r($request->getMethod());
        
//        print_r($request->all());
//        print_r($request->toArray());echo "<br />";
//        print_r($request->get('app_id'));echo ", ";
//        print_r($request->input('fan_id'));
        
//        $token = $request->bearerToken();
//
//        
//        print_r($token);
//        print_r(env('DB_HOST'));
//        print_r(env('DB_DATABASE'));
    }

    /**
     * 
     * @param type $event_id
     * @return int
     */
    public function getEvent($event_id) {
        $result = array('status' => 200, 'message' => "Your request noted successfully.", 'data' => array('name' => 'event-' . $event_id));
        return $result;
    }

    /**
     * 
     * @return string
     */
    public function create() {
        
        $result = array('status' => 200, 'message' => "Your request noted successfully and send for further insertions.");
        return $result;
    }

    /**
     * 
     * @return string
     */
    public function update() {
        $result = array('status' => 200, 'message' => "Your request noted successfully and send for further updation.");
        return $result;
    }

    /**
     * 
     * @return int
     */
    public function delete() {
        $result = array('status' => 200, 'message' => "Your request noted successfully and send for further deletion.");
        return $result;
    }

    /**
     * 
     * @return int
     */
    public function getAllEvents() {
        $events = array();
        $events[] = array('name' => 'event-' . rand(100000, 10000000));
        $events[] = array('name' => 'event-' . rand(100000, 10000000));
        $events[] = array('name' => 'event-' . rand(100000, 10000000));
        $result = array(
            'status' => 200, 'message' => "Your request noted successfully.",
            'data' => $events
        );
        return $result;
    }
    
    public function checkredis(){
        
        try{
            $rConf = array('server' => env('REDIS_HOST'), 'port' => env('REDIS_PORT'));
//            $objRedis = new \Redis();
//            $objRedis->connect($rConf['server'], $rConf['port']);
            
            $objRedis = app('redis');
            $objRedis->connect($rConf['server'], $rConf['port']);
            echo " Redis Server is running: " . $objRedis->ping();
            
            echo "<hr />";
            
            $key = "snaplion-events";
            $value = "Snaplion Segmentations";
//            echo "SET::$key:" . $objRedis->set($key, $value);
            echo "GET:KEY::$key, Value ::" . $objRedis->get($key);
            
            echo "<hr />";
            
        } catch (Exception $ex) {
            echo "Error:redis::" . $ex->getMessage();
        }
        die;
    }

}
