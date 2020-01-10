<?php

namespace App\Classes;

use App\Classes\Session;

class CSRFToken{

    public function _token(){

        if(!Session::has('token')){
            
        $randomToken = base64_encode(openssl_random_pseudo_bytes(32));
        Session::add('token', $randomToken);
        }
        return Session::get('token');

    }

    /**
     * verify  CSRF token
     *
     * @param [type] $requestToken
     * @param boolean $regenerate
     * @return void
     */
    public function verifyCSRFToken($requestToken, $regenerate = true){

        if(Session::has('token') && Session::get('token') === $requestToken){
            if($regenerate){
                Session::remove('token');
            }
            return true;
        }
        return false; 
    }
}