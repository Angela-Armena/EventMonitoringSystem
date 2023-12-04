<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Sample illustrating the use of Web NFC.">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <title>AdU-EMS</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <script>
        //Open first modal when Forgot Password Button is Clicked
        function openEnterModal() {
            modalEnterEmail.style.display = "flex";
        }
    </script>

    <?php
    session_start();
    $showVerifyModal = false;
    $sendVerification = false;
    $showChangePasswordModal = false;

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        include '../testing_connection.php';
        $buttonClicked = $_POST['button'];

        if ($buttonClicked == "btnSubmit")
        {
            $email = $_POST['email'];
            $password = $_POST['password'];

            $query = "SELECT Password FROM facilitatorinfo WHERE Email = '$email'";
            $result = mysqli_query($conn, $query);

            if ($result)
            {
                if (mysqli_num_rows($result) > 0)
                {
                    $row = mysqli_fetch_assoc($result);
                    $fetchedPassword = $row['Password'];
                    if (password_verify($password, $fetchedPassword))
                    {
                        $query = "SELECT facilitatorID FROM facilitatorinfo WHERE Email = '$email' AND Password = '$fetchedPassword'";
                        $result = mysqli_query($conn, $query);

                        if($result)
                        {
                            if(mysqli_num_rows($result) > 0)
                            {
                                $row = mysqli_fetch_assoc($result);
                                $_SESSION['currentFacilitator'] = $row['facilitatorID'];
                                header("Location: ../Attendance/index.php");
                                exit;
                            }
                            else    
                            {
                                echo('query not successful');
                            }
                        }
                    }
                    else
                    {
                        $_SESSION['error'] = "Email and password do not match!";
                    }
                }
                else
                {
                    $_SESSION['error'] = "Email has no record!";
                }
            }
            else    
            {
                echo('query not successful');
            }
        }
        if ($buttonClicked == "btnEmail")
        {
            $email = $_POST['emailAddress'];
            $query = "SELECT * FROM facilitatorinfo WHERE Email = '$email'";
            $result = mysqli_query($conn, $query);

            if ($result)
            {
                if (mysqli_num_rows($result) > 0)
                {
                    $showVerifyModal = true;
                    $sendVerification = true;
                    $_SESSION['userEmail'] = $email;
                }
                else
                {
                    $_SESSION['noUser'] = "Email has no record!";
                }
            }
            else    
            {
                echo('query not successful');
            }
        }
    }
    ?>

    <?php
        require "mail.php";
        if ($sendVerification)
        {
            $email = $_SESSION['userEmail'];

            $expire = time() + (60 * 10);
            $code = rand(10000,99999);
            $email = addslashes($email);
        
            $query = "INSERT INTO resets (Email, Code, Expire) VALUES ('$email','$code', '$expire')";
            $query_run = mysqli_query($conn, $query) or die("Could not update");
        
            //send email
            send_mail($email, 'EMS OTP Verification', "Your OTP is " . $code);
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') 
        {
            if ($buttonClicked == "btnVerify")
            {
                $email = $_SESSION['userEmail'];
                $inputOTP = $_POST['otp'];
                $inputOTP = addslashes($inputOTP);
                $expire = time();
                $email = addslashes($email);

                $query = "SELECT * FROM resets WHERE email = '$email' ORDER BY id DESC LIMIT 1";
                $result = mysqli_query($conn, $query);
                if ($result) 
                {
                    if (mysqli_num_rows($result) > 0) 
                    {
                        $row = mysqli_fetch_assoc($result);
                        if ($row['Expire'] > $expire) 
                        {
                            if ($inputOTP == $row['Code'])
                            {
                                $showChangePasswordModal = true;
                            }
                            else
                            {
                                $showVerifyModal = true;
                                $_SESSION['error'] = "The Code You've Entered is Incorrect.";
                            }
                        }
                        else
                        {
                            $_SESSION['error'] = "Your code has expired!";
                        }
                    }else
                    {
                        $_SESSION['error'] = "There has been an error.";
                    }
                }
            }
        }
    ?>

    <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST') 
        {
            if ($buttonClicked == "btnChange")
            {
                $raw_password = $_POST['password'];
                $raw_retypePassword = $_POST['passwordRetype'];
                $password = password_hash($raw_password, PASSWORD_DEFAULT);

                $email = $_SESSION['userEmail'];

                //check if new passwords match
                if ($raw_password == $raw_retypePassword)
                {
                    $query = "UPDATE facilitatorinfo SET Password = '$password' WHERE Email = '$email'";
                    $result = mysqli_query($conn, $query);

                    if ($result)
                    {
                        $_SESSION['success'] = "Password changed successfully!";
                    }
                    else    
                    {
                        echo('query not successful');
                    }
                }
                else
                {
                    $showChangePasswordModal = true;
                    $_SESSION['error'] = "Passwords do not match!";
                }
            }
        }
    ?>

    <!-- Login Form -->
    <div class="container">
        <form method="post" class="formContainer">
            <h1>AdU-EMS</h1>
            <p class="inputTitle">Email</p>
            <input type="text" id="email" name="email" autocomplete = "off">
            <p class="inputTitle">Password</p>
            <input type="password" id="password" name="password" autocomplete = "off">
            <a href="#" onclick="openEnterModal(); return false;">
                <!-- add link to forgot password screen on href -->
                <p class="buttonText" id="forgotPassword">Forgot Password</p>
            </a>
            <button type="submit" name="button" value="btnSubmit" class="btn buttonSubmit">Submit</button>
        </form>

        <div id="modalEnterEmail" class="modal">
            <!-- CREATE ENTER EMAIL MODAL -->
            <div class="modal-content">
                <span class="fa fa-times btnExit"></span>
                <form method="post" class = "centerDiv">
                    <h1 class="cardTitle">Forgot Password</h1><br><br>
                    <p class="cardResult">Email Address</p>
                    <input type="text" id="emailAddress" name="emailAddress" autocomplete = "off" required>
                    <p class="cardResult"></p>
                    <button class="btn buttonEmail" name="button" value="btnEmail" id="emailButton">Send OTP</button>
                </form>
            </div>
        </div>
        <div id="modalVerifyOTP" class="modal">
            <!-- CREATE VERIFICATION MODAL -->
            <div class="modal-content">
                <span class="fa fa-times btnExit"></span>
                <form method="post" class = "centerDiv">
                    <h1 class="cardTitle">Email Verification</h1><br><br>
                    <p>Email verification sent to <?php echo $_SESSION['userEmail']; ?></p><br>
                    <p class="cardResult">OTP</p>
                    <input type="text" id="otp" name="otp" autocomplete = "off" required>
                    <p class="cardResult"></p>
                    <button class="btn buttonOTP" name="button" value="btnVerify" id="otpButton">Verify</button>
                </form>
            </div>
        </div>
        <div id="modalChangePassword" class="modal">
        <!-- CREATE PASSWORD MODAL -->
        <div class="modal-content">
            <span class="fa fa-times btnExit"></span>
            <form method="post" class = "centerDiv">
                <h1 class="cardTitle">Change Password</h1><br><br>
                <p class="cardResult">New Password</p>
                <input type="password" id="password" name="password" autocomplete = "off" required>
                <p class="cardResult">Retype New Password</p>
                <input type="password" id="passwordRetype" name="passwordRetype" autocomplete = "off" required>
                <p class="cardResult"></p>
                <button class="btn buttonChange" name="button" value="btnChange" id="changeButton">Change Password</button>
            </form>
        </div>
    </div>

    <!-- SCRIPT TO MAKE FORGOT PASSWORD <P> CLICKABLE -->
    <script>
        // Get the modals
        var modalEnterEmail = document.getElementById("modalEnterEmail");
        var modalVerifyOTP = document.getElementById("modalVerifyOTP");
        var modalChangePassword = document.getElementById("modalChangePassword");

        // Get the buttons that close the modals
        var btnExitEnterEmail = document.querySelector("#modalEnterEmail .btnExit");
        var btnExitVerifyOTP = document.querySelector("#modalVerifyOTP .btnExit");
        var btnExitChangePassword = document.querySelector("#modalChangePassword .btnExit");

        var showVerifyModal = <?php echo ($showVerifyModal ? 'true' : 'false'); ?>;
        console.log("showVerifyModal: ", showVerifyModal);
        if (showVerifyModal) {
                modalVerifyOTP.style.display = "flex"; 
                modalEnterEmail.style.display = "none"; 
                modalChangePassword.style.display = "none"; 
        }

        var showChangePasswordModal = <?php echo ($showChangePasswordModal ? 'true' : 'false'); ?>;
        console.log("showChangePasswordModal: ", showChangePasswordModal);
        if (showChangePasswordModal) {
                modalVerifyOTP.style.display = "none"; 
                modalEnterEmail.style.display = "none"; 
                modalChangePassword.style.display = "flex"; 
        }

        // Function to close the modals
        function closeModal(modal) {
            modal.style.display = "none";
        }

        // Event listeners for exit buttons
        btnExitEnterEmail.addEventListener("click", function() {
            closeModal(modalEnterEmail);
        });

        btnExitVerifyOTP.addEventListener("click", function() {
            closeModal(modalVerifyOTP);
        });

        btnExitChangePassword.addEventListener("click", function() {
            closeModal(modalChangePassword);
        });

        // Close the modals when user clicks outside of modal
        window.addEventListener("click", function(event) {
            if (event.target === modalEnterEmail || event.target === modalVerifyOTP || event.target === modalChangePassword) {
                closeModal(modalEnterEmail);
                closeModal(modalVerifyOTP);
                closeModal(modalChangePassword);
            }
        });
    </script>

    <!-- DISPLAY ALERTS HERE -->
    <div class="alertContainer" id="alerts">
            <!-- DISPLAY ALERT BOX FOR CHANGING PASSWORD -->
            <?php
                if(isset($_SESSION['success']))
                {
                ?>
                    <div class="alert alertSuccess">
                        <?php echo $_SESSION['success']; ?>
                    </div>
                <?php 
                    unset($_SESSION['success']);

                }
            ?>

            <!-- DISPLAY ALERT BOX FOR ERRORS -->
            <?php
                if(isset($_SESSION['error']))
                {
                ?>
                    <div class="alert alertFailed">
                        <?php 
                            echo $_SESSION['error'];
                        ?>
                    </div>
                <?php 
                    unset($_SESSION['error']);

                }
            ?>
        </div>
    </div>

    <!-- HIDES ALERTS AFTER 5 SECONDS (5000 = 5 SECONDS) -->
    <script>
        setTimeout(function() {
            var alert = document.getElementById("alerts");
            var childDiv = alert.getElementsByTagName('div');
            // CHECKS IF THERE IS A CHILD INSIDE THE PARENT DIV
            if (childDiv.length > 0) {
                alert.style.display = 'none';
            }
        }, 5000);
    </script>
</body>

</html>

<!-- ALERTS SHOW UP WHEN YOU REFRESH THE PAGE. IT SHOULD BE HIDDEN UNLESS SPECIFIED TO SHOW -->