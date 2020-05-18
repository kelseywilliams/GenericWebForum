<?php
    function login($username, $password){
        include("funcs.php");
        begin_session();
        csrf();
        verify_login_input($username, $password);
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        set_exception_handler(function($e){
            error_log($e->getMessage());
            exit("An error occurred.");
        });
        $config = include("config.php");
        $mysqli = new mysqli($config["host"], $config["username"], $config["password"], $config["db"]);
        $mysqli->set_charset("utf8mb4");
        $stmt = $mysqli->prepare("SELECT * FROM accounts WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows === 0) exit('No rows');
        while($row = $result->fetch_assoc()) {
            $password_hash = $row["password"];
            if(password_verify($password, $password_hash)){
                $_SESSION["username"] = $username;
                $_SESSION["loggedin"] = true;
                $_SESSION["success"] = "Successfully logged in.";
                header("Location: /genericwebforum/", true, 301);
                exit();
            }
        }
        $stmt->close();
    }

    function verify_login_input($username, $password){
        if(empty($username) || empty($password)){
            $_SESSION["error"] = "Username and password cannot be empty";
            header("Location: /genericwebforum/", true, 301);
            exit();
        }
    }

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        login($_POST["username"], $_POST["password"]);
    }
    else{
        header("Location: /genericwebforum/", true, 301);
        exit();
    }