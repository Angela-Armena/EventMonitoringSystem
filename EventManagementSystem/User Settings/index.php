<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Sample illustrating the use of Web NFC.">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <title>AdU-EMS</title>
    <!-- <script src = "script.js"></script> -->
    <link rel="stylesheet" href="style.css">

    <!-- SHOWS ERROR -->
    <!-- <script>
        window.addEventListener('error', function (error) {
            if (ChromeSamples && ChromeSamples.setStatus) {
                console.error(error);
                ChromeSamples.setStatus(error.message + ' (Your browser may not support this feature.)');
                error.preventDefault();
            }
        });
    </script> -->


</head>

<body>
    <?php
    session_start();
    include '../testing_connection.php';
    $facilitatorID = $_SESSION['currentFacilitator'];

    // Check if facilitator is an ADMIN
    $query = "SELECT adminCheck FROM facilitatorinfo WHERE facilitatorID = '$facilitatorID'";
    $result = mysqli_query($conn, $query);

    $isNewAccountDisabled = false; // Initialize variables
    $isDisableAccountDisabled = false;

    if ($result && mysqli_num_rows($result) > 0) {
        $adminCheckRow = mysqli_fetch_assoc($result);
        $adminCheck = $adminCheckRow['adminCheck']; // Get the specific value from the row

        if ($adminCheck == 0) {
            $isNewAccountDisabled = true;
            $isDisableAccountDisabled = true;
        }
    }
    ?>

    <div class="mainContainer">
        <h1>USER SETTINGS</h1>
        <button class="btn" id="btnChangePassword">Change Password</button>
        <button class="btn" id="btnNewAccount" <?php if ($isNewAccountDisabled) echo 'disabled'; ?>>Create Account</button>
        <button class="btn" id="btnDisableAccount" <?php if ($isDisableAccountDisabled) echo 'disabled'; ?>>Disable Account</button>
        <a href = "../Login Page/index.php" style="padding:0;">
            <button class = "btn" id = "btnLogout" href = "../Login Page/index.php">Logout</button>
        </a>
    </div>

    <?php
    $showVerifyModal = false;
    $sendVerification = false;
    $fromModal = "";

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $buttonClicked = $_POST['button'];

        // CREATE ACCOUNT
        if ($buttonClicked == "btnCreate")
        {
            $firstName = $_POST['nameFirst'];
            $lastName = $_POST['nameLast'];
            $email = $_POST['emailAddress'];
            $raw_password = $_POST['password'];
            $raw_retypePassword = $_POST['passwordRetype'];
            $password = password_hash($raw_password, PASSWORD_DEFAULT);

            $_SESSION['userEmail'] = $email;

            $query = "SELECT * FROM facilitatorinfo WHERE Email = '$email'";
            $result = mysqli_query($conn, $query);

            if ($result)
            {
                if (mysqli_num_rows($result) == 0)
                {
                    if ($raw_password == $raw_retypePassword)
                    {
                        $query = "INSERT INTO facilitatorinfo (FirstName, LastName, Email, Password, AdminCheck) VALUES ('$firstName', '$lastName', '$email', '$password', FALSE);";
                        $result = mysqli_query($conn, $query);

                        if($result)
                        {
                            $_SESSION['createAccount'] = "New account created successfully!";
                        }
                        else    
                        {
                            echo('query not successful');
                        }
                    }
                    else
                    {
                        $_SESSION['noMatch'] = "Passwords do not match!";
                    }
                }
                else
                {
                    $_SESSION['userMatch'] = "There is already a user with the same email.";
                }
            }
            else    
            {
                echo('query not successful');
            }
        }

        // CHANGE PASSWORD
        if ($buttonClicked == "btnChange")
        {
            $email = $_POST['emailAddress'];
            $raw_oldPassword = $_POST['passwordCurrent'];
            $raw_password = $_POST['password'];
            $raw_retypePassword = $_POST['passwordRetype'];
            $oldPassword = password_hash($raw_oldPassword, PASSWORD_DEFAULT);
            $password = password_hash($raw_password, PASSWORD_DEFAULT);

            $_SESSION['userEmail'] = $email;

            $query = "SELECT Password FROM facilitatorinfo WHERE Email = '$email'";
            $result = mysqli_query($conn, $query);

            //check if user with given email exists
            if ($result)
            {
                if (mysqli_num_rows($result) > 0)
                {
                    $row = mysqli_fetch_assoc($result);
                    $fetchedPassword = $row['Password'];
                    if (password_verify($raw_oldPassword, $fetchedPassword))
                    {
                        //check if new passwords match
                        if ($raw_password == $raw_retypePassword)
                        {
                            $query = "UPDATE facilitatorinfo SET Password = '$password' WHERE Email = '$email'";
                            $result = mysqli_query($conn, $query);

                            //change password
                            if ($result)
                            {
                                $_SESSION['passwordChange'] = "Password changed successfully!";
                            }
                            else    
                            {
                                echo('query not successful');
                            }
                        }
                        else
                        {
                            $_SESSION['noMatch'] = "Passwords do not match!";
                        }
                    }
                    else
                    {
                        $_SESSION['noMatchEmail'] = "Email and Password do not match!";
                    }
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

        // DISABLE ACCOUNT
        if ($buttonClicked == "btnDisable")
        {
            $email = $_POST['emailAddress'];
            $raw_password = $_POST['password'];
            $password = password_hash($raw_password, PASSWORD_DEFAULT);

            $query = "SELECT Password FROM facilitatorinfo WHERE Email = '$email'";
            $result = mysqli_query($conn, $query);

            if ($result)
            {
                if (mysqli_num_rows($result) > 0)
                {
                    $row = mysqli_fetch_assoc($result);
                    $fetchedPassword = $row['Password'];
                    if (password_verify($raw_password, $fetchedPassword))
                    {
                        $query = "DELETE FROM facilitatorinfo WHERE Email = '$email'";
                        $result = mysqli_query($conn, $query);

                        if($result)
                        {
                            $_SESSION['disableAccount'] = "Account disabled successfully!";
                        }
                    }
                    else
                    {
                        $_SESSION['noMatchEmail'] = "Email and Password do not match!";
                    }
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

    <div id="modalNewAccount" class="modal">
        <!-- CREATE ACCOUNT MODAL -->
        <div class="modal-content">
            <span class="fa fa-times btnExit"></span>
            <form method="post" class = "centerDiv">
                <h1 class="cardTitle">Create User Account</h1>
                <p class="cardResult">First Name</p>
                <input type="text" id="nameFirst" name="nameFirst" autocomplete = "off" required>
                <p class="cardResult">Last Name</p>
                <input type="text"id="nameLast" name="nameLast" autocomplete = "off" required>
                <p class="cardResult">Email Address</p>
                <input type="text" id="emailAddress" name="emailAddress" autocomplete = "off" required>
                <p class="cardResult">Password</p>
                <input type="password" id="password" name="password" autocomplete = "off" required>
                <p class="cardResult">Retype Password</p>
                <input type="password" id="passwordRetype" name="passwordRetype" autocomplete = "off" required>
                <p class="cardResult"></p>
                <button class="btn buttonCreate" name="button" value="btnCreate" id="createButton">Submit</button>
            </form>
        </div>
    </div>
    <div id="modalChangePassword" class="modal">
        <!-- CREATE ACCOUNT MODAL -->
        <div class="modal-content">
            <span class="fa fa-times btnExit"></span>
            <form method="post" class = "centerDiv">
                <h1 class="cardTitle">Change Password</h1>
                <p class="cardResult">Email Address</p>
                <input type="text" id="emailAddress" name="emailAddress" autocomplete = "off" required>
                <p class="cardResult">Current Password</p>
                <input type="password" id="passwordCurrent" name="passwordCurrent" autocomplete = "off" required>
                <p class="cardResult">New Password</p>
                <input type="password" id="password" name="password" autocomplete = "off" required>
                <p class="cardResult">Retype New Password</p>
                <input type="password" id="passwordRetype" name="passwordRetype" autocomplete = "off" required>
                <p class="cardResult"></p>
                <button class="btn buttonChange" name="button" value="btnChange" id="changeButton">Change Password</button>
            </form>
        </div>
    </div>
    <div id="modalDisableAccount" class="modal">
        <!-- CREATE ACCOUNT MODAL -->
        <div class="modal-content">
            <span class="fa fa-times btnExit"></span>
            <form method="post" class = "centerDiv">
                <h1 class="cardTitle">Disable Account</h1>
                <p class="cardResult">Email Address</p>
                <input type="text" id="emailAddress" name="emailAddress" autocomplete = "off" required>
                <p class="cardResult">Password</p>
                <input type="password" id="password" name="password" autocomplete = "off" required>
                <p class="cardResult"></p>
                <button class="btn buttonDisable" name="button" value="btnDisable" id="disableButton">Disable Account</button>
            </form>
        </div>
    </div>

    <!-- SCRIPT TO OPEN MODAL -->
    <script>
        // Get the modals
        var modalNewAccount = document.getElementById("modalNewAccount");
        var modalChangePassword = document.getElementById("modalChangePassword");
        var modalDisableAccount = document.getElementById("modalDisableAccount");

        // Get the buttons that opens the modals
        var btnNewAccount = document.getElementById("btnNewAccount");
        var btnChangePassword = document.getElementById("btnChangePassword");
        var btnDisableAccount = document.getElementById("btnDisableAccount");

        // Get the buttons that closes the modals
        var btnExitNewAccount = document.getElementsByClassName("btnExit")[0];
        var btnExitChangePassword = document.getElementsByClassName("btnExit")[1];
        var btnExitDisableAccount = document.getElementsByClassName("btnExit")[2];

        // Open the modals when buttons are clicked
        btnNewAccount.onclick = function() { modalNewAccount.style.display = "flex"; }
        btnChangePassword.onclick = function() { modalChangePassword.style.display = "flex"; }
        btnDisableAccount.onclick = function() { modalDisableAccount.style.display = "flex"; }

        // Close the modals when buttons are clicked
        btnExitNewAccount.onclick = function() { modalNewAccount.style.display = "none"; }
        btnExitChangePassword.onclick = function() { modalChangePassword.style.display = "none"; }
        btnExitDisableAccount.onclick = function() { modalDisableAccount.style.display = "none"; }

        // Close the modals when user clicks outside of modal
        window.onclick = function(event) {
            if (event.target == modalNewAccount || event.target == modalChangePassword || event.target == modalDisableAccount || event.target == modalVerifyOTP) 
            {
                modalNewAccount.style.display = "none";
                modalChangePassword.style.display = "none";
                modalDisableAccount.style.display = "none";
            }
        }
    </script>

    <!-- DISPLAY ALERTS HERE -->
    <div class="alertContainer" id="alerts">
        <!-- DISPLAY ALERT BOX FOR ADDING NEW ACCOUNT IN DATABASE -->
        <?php
            if(isset($_SESSION['createAccount']))
            {
            ?>
                <div class="alert alertSuccess">
                    <?php echo $_SESSION['createAccount']; ?>
                </div>
            <?php 
                unset($_SESSION['createAccount']);

            }
        ?>

        <!-- DISPLAY ALERT BOX FOR DISABLING AN ACCOUNT -->
        <?php
            if(isset($_SESSION['disableAccount']))
            {
            ?>
                <div class="alert alertSuccess">
                    <?php echo $_SESSION['disableAccount']; ?>
                </div>
            <?php 
                unset($_SESSION['disableAccount']);

            }
        ?>

        <!-- DISPLAY ALERT BOX FOR CHANGING PASSWORD -->
        <?php
            if(isset($_SESSION['passwordChange']))
            {
            ?>
                <div class="alert alertSuccess">
                    <?php echo $_SESSION['passwordChange']; ?>
                </div>
            <?php 
                unset($_SESSION['passwordChange']);

            }
        ?>

        <!-- DISPLAY ALERT BOX FOR PASSWORDS THAT DO NOT MATCH -->
        <?php
            if(isset($_SESSION['noMatch']))
            {
            ?>
                <div class="alert alertFailed">
                    <?php echo $_SESSION['noMatch']; ?>
                </div>
            <?php 
                unset($_SESSION['noMatch']);

            }
        ?>

        <!-- DISPLAY ALERT BOX FOR EMAIL AND PASSWORD THAT DO NOT MATCH -->
        <?php
            if(isset($_SESSION['noMatchEmail']))
            {
            ?>
                <div class="alert alertFailed">
                    <?php echo $_SESSION['noMatchEmail']; ?>
                </div>
            <?php 
                unset($_SESSION['noMatchEmail']);

            }
        ?>

        <!-- DISPLAY ALERT BOX FOR NO USER -->
        <?php
            if(isset($_SESSION['noUser']))
            {
            ?>
                <div class="alert alertFailed">
                    <?php echo $_SESSION['noUser']; ?>
                </div>
            <?php 
                unset($_SESSION['noUser']);

            }
        ?>

        <!-- DISPLAY ALERT BOX FOR USER MATCH -->
        <?php
            if(isset($_SESSION['userMatch']))
            {
            ?>
                <div class="alert alertFailed">
                    <?php echo $_SESSION['userMatch']; ?>
                </div>
            <?php 
                unset($_SESSION['userMatch']);

            }
        ?>
    </div>

    <!-- HAMBURGER MENU STARTS HERE -->
    <div class = "menuHamburger">
        <a href="#home" class="active">Logo</a>
        <div id = "myLinks">
            <a class = "menuLink" href = "../Calendar/index.php">Calendar</a>
            <a class = "menuLink" href = "../Attendance/index.php">Attendance</a>
            <a class = "menuLink" href = "">Forms</a>
            <a class = "menuLink" href = "../User Settings/index.php">User Settings</a>
        </div>
        <a class="menuIcon" id = "aLink">
        <div class="line1"></div>
    <div class="line2"></div>
            <i class="fa fa-bars menuButton" onclick="navBarClick()"></i>
        </a>
    </div>

    <script>
        function navBarClick() {
            var x = document.getElementById("myLinks");
            if (x.style.display === "block") 
            {
                x.style.display = "none";
            } 
            else 
            {
                x.style.display = "block";
            }
        }
    </script>

    <!-- SCRIPT FOR ALERTS -->
    <script>
        setTimeout(function() {
            var alert = document.getElementById("alerts");
            var childDiv = alert.getElementsByTagName('div');
            if (childDiv.length > 0) {
                alert.style.display = 'none';
            }
        }, 5000);
    </script>
</body>

</html>