<?php

namespace App;

class Flash{
    public static function addMessage($message){
        if (! isset($_SESSION['flash_notification'])){
            $_SESSION['flash_notification'] = [];
        }

        $_SESSION['flash_notification'][] = $message;
    }

    public static function getMessages(){
        if (isset($_SESSION['flash_notification'])) {
            $messages = $_SESSION['flash_notification'];
            
            unset($_SESSION['flash_notification']);

            return $messages;
        }
    }
}