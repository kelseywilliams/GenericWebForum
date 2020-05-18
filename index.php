<?php
    include("functions.php");
    begin_session();
    csrf();
?>
<!DOCTYPE html>
<html lang="en-us">
    <head>
        <title>Web Forum</title>
        <meta name="author" content="Kelsey Williams">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta charset="utf-8">
        <!--<link href="/genericwebforum/stylesheets/main.css" rel="stylesheet">-->
    </head>
    <body>
        <header>
            <div class="header-container-left">
                <h1>Web Forum</h1>
            </div>
            <div class="header-container-right">
                <ul>
                    <?php
                        if(isset($_SESSION["loggedin"])){
                            if($_SESSION["loggedin"] == "false"){
                                $login = "Login";
                                $login_link = "login.php";
                            }
                           if($_SESSION["loggedin"] == "true"){
                                $login_link = "logout.php";
                                $login = "Logout";
                            }
                        }
                    ?>
                    <a href="<?php echo $login_link ?>">
                        <li class="sign-in">
                            <?php echo $login ?>
                        </li>
                    </a>
                    <a href="register.php">
                        <li>
                            Sign Up
                        </li>
                    </a>
                </ul>
            </div>
        </header>
        <?php
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
            else{
                $status_class = "empty";
                $status = "";
            }
        ?>
        <div class="<?php echo $status_class ?>"><?php echo $status?></div>
       <main>
           <div class="main-container">
                <h1>Boards</h1>
                <ul>
                    <a href="#">
                        <li>Random</li>
                    </a>
                    <a href="#">
                        <li>Politics</li>
                    </a>
                </ul>
           </div>
       </main>
    </body>

</html>