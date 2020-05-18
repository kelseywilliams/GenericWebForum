<?php
    function register($username, $password, $conf_password){
        include("funcs.php");
        begin_session();
        csrf();
        clean_register_input($username, $password, $conf_password);
        $password = password_hash($password, PASSWORD_DEFAULT);
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        set_exception_handler(function($e){
            error_log($e->getMessage());
            echo $e;
            //exit("An error occurred.");
        });
        $config = include("config.php");
        $mysqli = new mysqli($config["host"], $config["username"], $config["password"], $config["db"]);
        $mysqli->set_charset("utf8mb4");
        // Check for duplicate username
        $dup_stmt = $mysqli->prepare("SELECT COUNT(*) FROM accounts WHERE username = ?");
        $dup_stmt->bind_param("s", $username);
        $dup_stmt->execute();
        $result = $dup_stmt->get_result();
        while($row = $result->fetch_assoc()) {
            $dup_stmt->close();
            if($row > 0){
                // Update database with username and password
                $update_stmt = $mysqli->prepare("INSERT INTO accounts (username, password) VALUES (?, ?)");
                $update_stmt->bind_param("ss", $username, $password);
                $update_stmt->execute();
                $update_stmt->close();
                $_SESSION["success"] = "Account successfully created.";
                header("Location: /genericwebforum/", true, 301);
                exit();
            }
            else{
                $_SESSION["error"] = "Username already exists.";
                header("Location: /genericwebforum/");
                exit();
            }
        }
        
    }

    function clean_register_input($username, $password, $conf_password){
        if($password != $conf_password){
            $_SESSION["error"] = "Passwords do not match.";
            header("Location: /genericwebforum/", true, 301);
            exit();
        }
        if(empty($username) || empty($password) || empty($conf_password)){
            $_SESSION["error"] = "Username and password cannot be empty.";
            header("Location: /genericwebforum/", true, 301);
            exit();
        }
    }

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        register($_POST["username"], $_POST["password"], $_POST["conf-password"]);
    }
    else{
        header("Location: /genericwebforum/", true, 301);
    }
?>