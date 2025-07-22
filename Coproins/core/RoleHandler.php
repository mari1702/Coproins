<?php
class RoleHandler{

    public static function OnlyAdmin(){
        session_start();

        if($_SESSION['rol'] != "admin"){
            echo "No tiene permitido realizar esta accion";
            exit;
        }
    }

    public static function checkSession(){
        if (
            !isset($_SESSION['rol']) || ($_SESSION['rol'] !== 'encargado' 
            &&
            $_SESSION['rol'] !== 'admin')) {
            header("Location: ../../index.php");
            exit();
        }
    }

}