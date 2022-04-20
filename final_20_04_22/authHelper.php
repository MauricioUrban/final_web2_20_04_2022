<?php
class authHelper{

    function __construct(){
        if (session_status() != PHP_SESSION_ACTIVE){
            session_start();
        }
    }
    
    public function checkloggedIn(){
        if (empty($_SESSION['USER_ID'])){
            $this->view->showMsj("Usuario no logueado");
            die();
        }
    }
}