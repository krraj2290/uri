<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers;

use App\UserEvent;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserEventController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        //
    }

    public function save($params) {
        if (empty($params)) {
            return false;
        }
        $params['updated_at'] = date("Y-m-d H:i:s");
        $params['created_at'] = date("Y-m-d H:i:s");
        $objEvent = new \App\UserEvent();
        $result = $objEvent->create($params);
        return $result;
    }

}
