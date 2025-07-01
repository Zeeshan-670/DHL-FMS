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

    <style>
    .formHeading {
        opacity: 0;
        transform: translateY(20px);
        /* Start off-screen */
        transition: opacity 0.5s ease, transform 0.5s ease;
        display: none;
    }

    .formHeading.visible {
        display: block;
        opacity: 1;
        transform: translateY(0);
        /* Slide to original position */
    }

    .formContainer {
        overflow: hidden;
    }

    .wrapper {
        padding-top: 50px;
    }

    /* .formElements {
        max-width: 400px;
        margin: 0 auto;
    } */
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="loginContainer">
            <div class="logoContainer">
                <img src="./assets/img/logo.png" alt="logo" />
            </div>
            <div class="formContainer">
                <!-- LOGIN FORM -->
                <div class="formHeading visible" id="loginFormContainer" style="display: block;">
                    <h4 class="formTitle">Fleet Management System</h4>
                    <h4 class="formTitle mt-5 mb-0">Welcome Back!</h4>
                    <div class="text-center">
                        <span class="txt1"> Please enter login details below </span>
                    </div>
                    <form class="formElements mt-5" id='loginForm'>
                        <div class="inputContainer my-3">
                            <label for="email" class="visually-hidden">Email</label>
                            <input class="inputElm" id="email" type="text" name="email" placeholder="Email" required />
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

                        <div class="text-center text-white">
                            <span class="txt2">Don't have an account? </span>
                            <a href="#" onclick="toggleForm()">Register</a>
                        </div>
                    </form>
                </div>

                <!-- REGISTER FORM -->
                <div class="formHeading" id="registerFormContainer" style="display: none;">
                    <h4 class="formTitle">Fleet Management System</h4>
                    <h4 class="formTitle mt-4 mb-0">Create an Account</h4>
                    <div class="text-center">
                        <span class="txt1">Please fill in the details below to register</span>
                    </div>
                    <form class="formElements mt-5" id='registerForm'>
                        <div class="row">
                            <!-- Name Field -->
                            <div class="col-md-6">
                                <div class="inputContainer my-3">
                                    <label for="name" class="visually-hidden">Name</label>
                                    <input class="inputElm" id="name" type="text" name="name" placeholder="Full Name"
                                        required />
                                    <span class="focusInputElm"></span>
                                    <span class="symbolInputElm"><i class="fa fa-user"></i></span>
                                </div>
                            </div>

                            <!-- Username Field -->
                            <div class="col-md-6">
                                <div class="inputContainer my-3">
                                    <label for="username" class="visually-hidden">Username</label>
                                    <input class="inputElm" id="username" type="text" name="username"
                                        placeholder="Username" required />
                                    <span class="focusInputElm"></span>
                                    <span class="symbolInputElm"><i class="fa fa-user"></i></span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Password Field -->
                            <div class="col-md-6">
                                <div class="inputContainer my-3">
                                    <label for="password" class="visually-hidden">Password</label>
                                    <input class="inputElm" type="password" id="password" name="password" maxlength="64"
                                        minlength="6" placeholder="Password" required />
                                    <span class="focusInputElm"></span>
                                    <span class="symbolInputElm"><i class="fa fa-lock"></i></span>
                                    <span class="togglePassword" id="myInput" onclick="toggleFunction()"><i
                                            class="fa fa-eye"></i></span>
                                </div>
                            </div>

                            <!-- City Field -->
                            <div class="col-md-6">
                                <div class="inputContainer my-3">
                                    <label for="city" class="visually-hidden">City</label>
                                    <select class="inputElm select-box" id="city" name="city" required>
                                        <option value="">Select City</option>
                                        <option value="city1">City 1</option>
                                        <option value="city2">City 2</option>
                                        <option value="city3">City 3</option>
                                        <!-- Add more cities as needed -->
                                    </select>
                                    <span class="focusInputElm"></span>
                                    <span class="symbolInputElm"><i class="fa fa-city"></i></span>
                                    <span class="select-icon"><i class="fa fa-chevron-down"></i></span>

                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Station Field -->
                            <div class="col-md-6">
                                <div class="inputContainer my-3">
                                    <label for="station" class="visually-hidden">Station</label>
                                    <select class="inputElm select-box" id="station" name="station" required>
                                        <option value="">Select Station</option>
                                        <option value="station1">Station 1</option>
                                        <option value="station2">Station 2</option>
                                        <option value="station3">Station 3</option>
                                        <!-- Add more stations as needed -->
                                    </select>
                                    <span class="focusInputElm"></span>
                                    <span class="symbolInputElm"><i class="fa fa-building"></i></span>
                                    <span class="select-icon"><i class="fa fa-chevron-down"></i></span>

                                </div>
                            </div>

                            <!-- Designation Field -->
                            <div class="col-md-6">
                                <div class="inputContainer my-3">
                                    <label for="designation" class="visually-hidden">Designation</label>
                                    <select class="inputElm select-box" id="designation" name="designation" required>
                                        <option value="">Select Designation</option>
                                        <option value="manager">Manager</option>
                                        <option value="supervisor">Supervisor</option>
                                        <option value="driver">Driver</option>
                                        <option value="staff">Staff</option>
                                        <!-- Add more designations as needed -->
                                    </select>
                                    <span class="focusInputElm"></span>
                                    <span class="symbolInputElm"><i class="fa fa-briefcase"></i></span>
                                    <span class="select-icon"><i class="fa fa-chevron-down"></i></span>

                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Access Level Field -->
                            <div class="col-md-6">
                                <div class="inputContainer my-3">
                                    <label for="access" class="visually-hidden">Access Level</label>
                                    <select class="inputElm select-box" id="access" name="access" required>
                                        <option value="">Select Access Level</option>
                                        <option value="admin">Admin</option>
                                        <option value="user">User</option>
                                        <option value="manager">Manager</option>
                                        <option value="staff">Staff</option>
                                        <!-- Add more access levels as needed -->
                                    </select>
                                    <span class="focusInputElm"></span>
                                    <span class="symbolInputElm"><i class="fa fa-key"></i></span>
                                    <span class="select-icon"><i class="fa fa-chevron-down"></i></span>

                                </div>
                            </div>

                            <!-- Signature Field -->
                            <div class="col-md-6">
                                <div class="inputContainer my-3">
                                    <label for="signature" class="visually-hidden">Signature</label>
                                    <div>
                                        <input class="inputElm" type="file" style="padding:13px 30px 20px 53px"
                                            id="signature" name="signature" accept="image/*" required />
                                    </div>
                                    <span class="focusInputElm"></span>
                                    <span class="symbolInputElm"><i class="fa fa-signature"></i></span>
                                </div>
                            </div>
                        </div>



                        <div class="btnContainer my-4">
                            <button type="submit" class="btnLogin" id="registerBtn">Register</button>
                        </div>

                        <div class="text-center text-white">
                            <span class="txt2">Already have an account? </span>
                            <a href="#" onclick="toggleForm()">Login</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/lobibox@1.2.7/dist/js/lobibox.min.js"></script>
    <script src="./assets/js/main.js"></script>
    <script src="./assets/js/login.js"></script>

    <script>
    // Function to toggle between login and register forms with smooth transition
    function toggleForm() {
        const loginForm = document.getElementById("loginFormContainer");
        const registerForm = document.getElementById("registerFormContainer");

        if (loginForm.classList.contains("visible")) {
            // Hide login, show register
            loginForm.classList.remove("visible");
            setTimeout(() => {
                loginForm.style.display = "none"; // Hide login form
                registerForm.style.display = "block"; // Display register form
                setTimeout(() => registerForm.classList.add("visible"), 50); // Add transition for register form
            }, 500); // Match CSS transition duration
        } else {
            // Hide register, show login
            registerForm.classList.remove("visible");
            setTimeout(() => {
                registerForm.style.display = "none";
                loginForm.style.display = "block";
                setTimeout(() => loginForm.classList.add("visible"), 50); // Add transition for login form
            }, 500); // Match CSS transition duration
        }
    }

    const selectBoxes = document.querySelectorAll('.select-box');
    selectBoxes.forEach(selectBox => {
        selectBox.addEventListener('focus', function() {
            this.classList.add('open');
        });
        selectBox.addEventListener('blur', function() {
            this.classList.remove('open');
        });
        selectBox.addEventListener('change', function() {
            this.classList.remove('open');
        });
    });
    </script>
</body>

</html>