<?php
    function db_connect(){
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        set_exception_handler(function($e){
            error_log($e->getMessage());
            exit("An error occurred.");
        });
        $config = include("config.php");
        $mysqli = new mysqli($config["host"], $config["username"], $config["password"], $config["db"]);
        $mysqli->set_charset("utf8mb4");
    }
?>