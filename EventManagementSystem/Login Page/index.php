<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Sample illustrating the use of Web NFC.">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>AdU-EMS</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php
    session_start();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        include '../testing_connection.php';

        $email = $_POST['email'];
        $password = $_POST['password'];

        $query = "SELECT Password FROM facilitatorinfo WHERE Email = '$email'";
        $result = mysqli_query($conn, $query);
        $row = mysqli_fetch_assoc($result);
        $fetchedPassword = $row['Password'];

        if ($result)
        {
            if (mysqli_num_rows($result) > 0)
            {
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
                    $_SESSION['noMatch'] = "Passwords do not match!";
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

    ?>

    <!-- Login Form -->
    <div class="container">
        <form method="post" class="formContainer">
            <h1>AdU-EMS</h1>
            <p class="inputTitle">Email</p>
            <input type="text" id="email" name="email" autocomplete = "off">
            <p class="inputTitle">Password</p>
            <input type="password" id="password" name="password" autocomplete = "off">
            <a href="">
                <!-- add link to forgot password screen on href -->
                <p class="buttonText" id="forgotPassword">Forgot Password</p>
            </a>
            <button type="submit" name="button" value="btnSubmit" class="btn buttonSubmit">Submit</button>
        </form>
        
        <!-- DISPLAY ALERTS HERE -->
        <div class="alertContainer" id="alerts">
            <!-- DISPLAY ALERT BOX FOR NO RECORD IN DATABASE -->
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

            <!-- DISPLAY ALERT BOX FOR NO MATCH IN USERNAME AND PASSWORD -->
            <?php
                if(isset($_SESSION['noMatch']))
                {
                ?>
                    <div class="alert alertFailed">
                        <?php 
                            echo $_SESSION['noMatch'];
                        ?>
                    </div>
                <?php 
                    unset($_SESSION['noMatch']);

                }
            ?>
        </div>
    </div>

    <!-- SCRIPT TO MAKE FORGOT PASSWORD <P> CLICKABLE -->
    <script>
        document.getElementById("forgotPassword").addEventListener("click", function() {
            //GOES TO FORGOT PASSWORD SCREEN OR SOMETHING. BASTA HOW WE DECIDE TO DO THE FORGOT PASSWORD THING.
        });
    </script>

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