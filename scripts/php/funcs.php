<?php
    function begin_session(){
        session_start();
        if (empty($_SESSION['token'])) {
            $_SESSION['token'] = bin2hex(random_bytes(32));
        }
        if(!isset($_SESSION["loggedin"]){
            $_SESSION["loggedin"] = false;
        })
    }

    function csrf(){
        if (!empty($_POST['token'])) {
            if (hash_equals($_SESSION['token'], $_POST['token'])) {
                return true;
            }
            else {
                $victim_ip = $_SERVER["REMOTE_ADDR"];
                $error = "Possible CSRF attack on $victim_ip (CSRF tokens from existing session and submitted form did not match)";
                error_log($error->getMessage());
                exit("An error occurred.");
            }
        }
    }