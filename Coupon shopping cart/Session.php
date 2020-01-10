<?php

namespace App\Classes;

class Session{

    /**
     * create a session
     *
     * @param [type] $name
     * @param [type] $value
     * @return void
     */
    public static function add($name, $value){
        if($name!= '' && !empty($name) && $value != '' && !empty($value)){
            return $_SESSION[$name] = $value;
        }
        throw new \Exception('Name and value required');
    }
    //get value from session
    public static function get($name){
        return $_SESSION[$name];
    }

    /**
     * check if session exists
     *
     * @param [type] $name
     * @return boolean
     */
    public function has($name){
        if($name != '' && !empty($name)){
            return(isset($_SESSION[$name])) ? true : false;
        }

        throw new \Exception('name required');
    }
    //remove session
    public static function remove($name){
        if(self::has($name)){
            unset($_SESSION[$name]);
        }
    }

    /**
     * Flash a message and unset old Session
     *
     * @param [type] $name
     * @param [type] $value
     * @return void
     */
    public static function flash($name, $value = ''){
        if(self::has($name)){
            $old_value = self::get($name);
            self::remove($name);

            return $old_value;
        }
        else{
            self::add($name, $value);
        }

        return null;
    }

}