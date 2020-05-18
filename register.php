<?php
    include("functions.php");
    begin_session();
    csrf();

    function register($username, $password, $conf_password){
        filter_register_input($username, $password, $conf_password);
        $password = password_hash($password, PASSWORD_DEFAULT);
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        set_exception_handler(function($e){
            error_log($e->getMessage());
            # FIX THIS ERROR
            exit("An error occurred.");
        });
        $config = include("config.php");
        $mysqli = new mysqli($config["host"], $config["username"], $config["password"], $config["db"]);
        $mysqli->set_charset("utf8mb4");
        // Check for duplicate username
        $dup_stmt = $mysqli->prepare("SELECT BINARY COUNT(*) FROM accounts WHERE BINARY username = ?");
        $dup_stmt->bind_param("s", $username);
        $dup_stmt->execute();
        $result = $dup_stmt->get_result();

        while($row = $result->fetch_assoc()) {
            $dup_stmt->close();
            foreach($row as $count){
                if($count == 0){
                    // Update database with username and password
                    $update_stmt = $mysqli->prepare("INSERT INTO accounts (username, password, created) VALUES (?, ?, ?)");
                    $date = date(DATE_W3C);
                    $update_stmt->bind_param("sss", $username, $password, $date);
                    $update_stmt->execute();
                    $update_stmt->close();
                    $_SESSION["success"] = "Account successfully created.";
                    header("Location: index.php", true, 301);
                    exit();
                }
                else{
                    $_SESSION["error"] = "Username is taken.";
                    header("Location: register.php");
                    exit();
                }
            }
        }        
    }

    function filter_register_input($username, $password, $conf_password){
        if($password != $conf_password){
            $_SESSION["error"] = "Passwords do not match.";
            header("Location: register.php", true, 301);
            exit();
        }
        if(empty($username) || empty($password) || empty($conf_password)){
            $_SESSION["error"] = "Username and password cannot be empty.";
            header("Location: register.php", true, 301);
            exit();
        }
        if(preg_match("/[&\"\'<>]/", $username)){
            $_SESSION["error"] = "Username and password cannot contain the characters \" ' & < >";
            header("Location: register.php", true, 301);
            exit();
        }
        
        if(preg_match("/[&\"\'<>]/", $password)){
            $_SESSION["error"] = "Username and password cannot contain the characters \" ' & < >";
            header("Location: register.php", true, 301);
            exit();
        }
    }

    #return_result("error",);

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        register($_POST["username"], $_POST["password"], $_POST["conf-password"]);
    }

?>

<!DOCTYPE html>
<html lang="en-us">
    <head>
        <title>Generic Web Forum</title>
        <meta name="author" content="Kelsey Williams">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta charset="utf-8">
        <!--<link href="/genericwebforum/stylesheets/main.css" rel="stylesheet">-->
    </head>
    <body>
        <div class="signup-form">
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                <h1>Web Forum</h1>
                <h3>Sign Up</h3>
                <!-- Status message -->
                <?php
                    $status_class = "empty";
                    $status = "";
                     if(isset($_SESSION["error"])){
                        $status_class = "error";
                        $status = $_SESSION["error"];
                        unset($_SESSION["error"]);
                    }
                    if(isset($_SESSION["success"])){
                        $status_class = "success";
                        $status = $_SESSION["success"];
                        unset($_SESSION["success"]);
                    }
                ?>
                <div class="<?php echo $status_class ?>"><?php echo $status?></div>
                <span>Username</span><br>
                <input type="text" name="username" maxlength="15"><br>
                <span>Password</span><br>
                <input type="password" name="password" maxlength="20"><br>
                <span>Reconfirm Password</span><br>
                <input type="password" name="conf-password" maxlength="20"><br>
                <input type="submit" value="Create Account">
            </form>
        </div>
    </body>
</html>