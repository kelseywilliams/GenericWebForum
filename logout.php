<?php
    include("functions.php");
    begin_session();
    csrf();
    if(isset($_SESSION["loggedin"])){
        if($_SESSION["loggedin"] == "true"){
            $_SESSION["loggedin"] = "false";
            $_SESSION["success"] = "Successfully logged out.";
            unset($_SESSION["username"]);
            header("Location: /genericwebforum/", true, 301);
        }
    }
?>