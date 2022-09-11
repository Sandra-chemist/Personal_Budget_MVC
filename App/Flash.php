<?php

namespace App;

class Flash{
    public static function addMessage($message){
        if (! isset($_SESSION['flash_notification'])){
            $_SESSION['flash_notification'] = [];
        }

        $_SESSION['flash_notification'][] = $message;
    }
}