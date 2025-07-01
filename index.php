<?php
session_start();
$_SESSION = array();
session_destroy();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/lobibox@1.2.7/dist/css/lobibox.min.css" rel="stylesheet">
    <!-- Icons Css -->
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />

    <!-- Custom CSS -->
    <link rel="stylesheet" href="./assets/css/login.css" />

    <title>DHL | Fleet Management System</title>
    <link rel="shortcut icon" href="assets/img/favicon.ico" />
</head>

<body>
    <div class="wrapper">
        <div class="loginContainer">
            <div class="logoContainer">
                <img src="./assets/img/logo.png" alt="logo" />
            </div>
            <div class="formContainer">
                <div class="formHeading">
                    <h5 class="formTitle">Fleet Management System</h4>
                        <h4 class="formTitle mt-5 mb-0">Welcome Back!</h4>
                        <div class="text-center">
                            <span class="txt1"> Please enter login details below </span>
                        </div>
                </div>
                <form class="formElements mt-5" id='loginForm'>
                    <div class="inputContainer my-3">
                        <label for="email" class="visually-hidden">Username</label>
                        <input class="inputElm" id="email" type="text" name="email" placeholder="Username" required />
                        <span class="focusInputElm"></span>
                        <span class="symbolInputElm"><i class="fa fa-user"></i></span>
                    </div>

                    <div class="inputContainer my-3">
                        <label for="password" class="visually-hidden">Password</label>
                        <input class="inputElm" type="password" id="password" name="password" maxlength="64"
                            minlength="6" placeholder="Password" required />
                        <span class="focusInputElm"></span>
                        <span class="symbolInputElm"><i class="fa fa-lock"></i></span>
                        <span class="togglePassword" id="myInput" onclick="toggleFunction()"><i
                                class="fa fa-eye"></i></span>
                    </div>

                    <div class="btnContainer my-4">
                        <button type="submit" class="btnLogin" id="loginBtn">Login</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- Optional JavaScript; choose one of the two! -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/lobibox@1.2.7/dist/js/lobibox.min.js"></script>
    <script src="./assets/js/main.js"></script>
    <script src="./assets/js/login.js"></script>
</body>

</html>