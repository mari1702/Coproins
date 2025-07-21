<?php
class RoleHandler{

    public static function OnlyAdmin(){
        session_start();

        if($_SESSION['rol'] != "admin"){
            echo "No tiene permitido realizar esta accion";
            exit;
        }
    }

}