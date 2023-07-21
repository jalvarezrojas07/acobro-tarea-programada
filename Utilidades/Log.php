<?php

class Log{
    public function registrar($message){
        $log  = "[".date("Y-m-d H:i:s")."] ".$message.PHP_EOL;
        //file_put_contents('C:\laragon\www\panel-tarea\Logs\log-'.date("Y-m-d").'.log', $log, FILE_APPEND);
        file_put_contents('Logs\log-'.date("Y-m-d").'.log', $log, FILE_APPEND);
        //file_put_contents('C:\Users\JALVAREZ\Documents\\tarea\Logs\log-'.date("Y-m-d").'.log', $log, FILE_APPEND);
    }

}
?>