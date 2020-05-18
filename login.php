<?php
    include("functions.php");
    begin_session();
    csrf();

    function login($username, $password){
        filter_login_input($username, $password);
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        set_exception_handler(function($e){
            error_log($e->getMessage());
            #exit("An error occurred.");
            exit($e);
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
                $_SESSION["loggedin"] = "true";
                $_SESSION["success"] = "Successfully logged in.";
                header("Location: index.php", true, 301);
                exit();
            }
        }
        $stmt->close();
    }

    function filter_login_input($username, $password){
        if(empty($username) || empty($password)){
            $_SESSION["error"] = "Username and password cannot be empty";
            header("Location: index.php", true, 301);
            exit();
        }
    }

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        login($_POST["username"], $_POST["password"]);
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
                <h3>Login</h3>
                <span>Username</span><br>
                <input type="text" name="username" maxlength="15"><br>
                <span>Password</span><br>
                <input type="password" name="password" maxlength="20"><br>
                <input type="submit" value="Login">
            </form>
        </div>
    </body>
</html>