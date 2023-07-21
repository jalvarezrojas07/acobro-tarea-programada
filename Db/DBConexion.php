<?php
//require_once "C:\laragon\www\panel-tarea\Utilidades\Log.php";
//require_once "C:\Users\JALVAREZ\Documents\\tarea\Utilidades\Log.php";
require_once "Utilidades\Log.php";

class DBConexion {
    
    public static function conexion_origen() {
        try{
            $driver = new mysqli_driver();
            $driver->report_mode = MYSQLI_REPORT_STRICT | MYSQLI_REPORT_ERROR;

            //$settings = file_get_contents('C:\laragon\www\panel-tarea\settings.json');
            $settings = file_get_contents('settings.json');
            //$settings = file_get_contents('C:\Users\JALVAREZ\Documents\\tarea\settings.json');
            $settings = json_decode($settings,true);
            

            $connection = new mysqli($settings["db_host_origen"], $settings["db_user_origen"], $settings["db_password_origen"], $settings["db_database_origen"]);

            if ( $connection->errno ) {
                echo "Fallo la conexion a MySQL: " . mysqli_connect_error();
            } else {
                $connection->query("SET NAMES 'utf8'");
                return $connection;
            }
        }catch(Exception $e) {
            echo $e->getMessage();
            $log = new Log();
            $log->registrar($e->getMessage());
        }
    
    }

    public static function conexion_destino() {
        //$settings = file_get_contents('C:\laragon\www\panel-tarea\settings.json');
        $settings = file_get_contents('settings.json');
        //$settings = file_get_contents('C:\Users\JALVAREZ\Documents\\tarea\settings.json');
        $settings = json_decode($settings,true);
        

        $connection = new mysqli($settings["db_host_destino"], $settings["db_user_destino"], $settings["db_password_destino"], $settings["db_database_destino"]);

        if ( $connection->errno ) {
            echo "Fallo la conexion a MySQL: " . mysqli_connect_error();
        } else {
            $connection->query("SET NAMES 'utf8'");
            return $connection;
        }
    }
}