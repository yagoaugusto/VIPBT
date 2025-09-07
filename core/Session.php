<?php
namespace core;

class Session {
    public static function start(){
        if(session_status() == PHP_SESSION_NONE){
            session_start();
        }
    }

    public static function set($key, $value){
        $_SESSION[$key] = $value;
    }

    public static function get($key){
        return $_SESSION[$key] ?? null;
    }

    public static function has($key){
        return isset($_SESSION[$key]);
    }

    public static function remove($key){
        if(self::has($key)){
            unset($_SESSION[$key]);
        }
    }

    public static function destroy(){
        session_destroy();
    }

    public static function flash($name = '', $message = '', $class = 'alert alert-success'){
        if(!empty($name)){
            if(!empty($message) && empty($_SESSION[$name])){
                if(!empty($_SESSION[$name])){
                    unset($_SESSION[$name]);
                }
                if(!empty($_SESSION[$name. '_class'])){
                    unset($_SESSION[$name. '_class']);
                }
                $_SESSION[$name] = $message;
                $_SESSION[$name. '_class'] = $class;
            } elseif(empty($message) && !empty($_SESSION[$name])){
                $class = !empty($_SESSION[$name. '_class']) ? $_SESSION[$name. '_class'] : '';
                echo '<div class="'.$class.'" id="msg-flash">' . $_SESSION[$name] . '</div>';
                unset($_SESSION[$name]);
                unset($_SESSION[$name. '_class']);
            }
        }
    }

    public static function isLoggedIn(){
        return self::has('user_id');
    }
}

// Inicia a sessão em todas as páginas
Session::start();
