<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Sample illustrating the use of Web NFC.">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <title>AdU-EMS</title>
    <!-- <script src = "sql.js"></script> -->
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
    <!-- DIV SA TAAS (TITLE, SERIAL NUMBER INPUT BOX, SCAN BUTTON) -->
    <div class="mainContainer">
        <div id="attendanceScanner">
            <h1>Attendance Monitoring</h1>
            <form method="post" class="formContainer">
                <input type="text" id="serialNumber" name="RFIDSerialNumber" autocomplete = "off">
                <button type="submit" name="button" value="btnScan" id="btnScan" class="btn buttonScan">Scan</button>
            </form>
        </div>
        <div id="errorOnComputer" style="display: none">
            <h1>ERROR</h1>
            <p id="status"></p>
        </div>
    </div>
    
    <!-- DISPLAYS ERROR IF THE NFC ATTENDANCE SCANNER IS OPENED IN A COMPUTER AND NOT A PHONE -->
    <script>
        var ChromeSamples = {
            log: function () {
                var line = Array.prototype.slice.call(arguments).map(function (argument) {
                    return typeof argument === 'string' ? argument : JSON.stringify(argument);
                }).join(' ');

                document.querySelector('#log').textContent += line + '\n';
            },

            clearLog: function () {
                document.querySelector('#log').textContent = '';
            },

            setStatus: function (status) {
                document.querySelector('#status').textContent = status;
            }
        };
    </script>

    <!-- DISPLAYS ERROR IF THE NFC ATTENDANCE SCANNER DOES NOT WORK ON THE CURRENT VERSION OF CHROME -->
    <script>
        if (/Chrome\/(\d+\.\d+.\d+.\d+)/.test(navigator.userAgent)) {
            if (89 > parseInt(RegExp.$1)) {
                ChromeSamples.setStatus('Warning! Keep in mind this sample has been tested with Chrome ' + 89 + '.');
            }
        }
    </script>

    <!-- DISPLAYS ERROR IF THE NFC ATTENDANCE SCANNER IS OPENED ON A DESKTOP COMPUTER AND NOT A PHONE -->
    <script>
        log = ChromeSamples.log;
        var containerScannerHidden = document.getElementById("attendanceScanner");
        var containerErrorOnComputer = document.getElementById("errorOnComputer");

        if (!("NDEFReader" in window))
        {
            ChromeSamples.setStatus("Web NFC is not available. Use Chrome on Android.");
            containerScannerHidden.style.display = "none";
            containerErrorOnComputer.style.display = "block";
        }
    </script>

    <?php
    session_start();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        include '../testing_connection.php';
        $buttonClicked = $_POST['button'];
        $facilitatorID = $_SESSION['currentFacilitator'];

        if ($buttonClicked == "btnScan")
        {
            $serialNumber = $_POST['serialNumber'];
            $_SESSION['SerialNumber'] = $serialNumber;

            $query = "SELECT FirstName, LastName, StudentNumber FROM studentsinfo WHERE RFIDSerialNumber = '$serialNumber'";
            $result = mysqli_query($conn, $query);
            
            if($result)
            {
                if(mysqli_num_rows($result) > 0)
                {
                    $showScanSuccess = true;
                    $row = mysqli_fetch_assoc($result);

                    $name = $row['FirstName'] .' ' .$row['LastName'];
                    $studentNumber = $row['StudentNumber'];
                }
                else
                {
                    $showNoRecord = true;
                }
            }
            else    
            {
                echo('query not successful');
                $showScanFailed = true;
            }
        }

        if ($buttonClicked == "btnAccept") 
        {
            $serialNumber = $_SESSION['SerialNumber'];
            $query = "SELECT StudentID FROM studentsinfo WHERE RFIDSerialNumber = '$serialNumber'";
            $result = mysqli_query($conn, $query);
            $row = mysqli_fetch_assoc($result);
            $studentID = $row['StudentID'];

            //change eventID value once you can figure out the event stuff
            $query = "INSERT INTO studentattendance (EventID, StudentID, FacilitatorID) VALUES (1, '$studentID', $facilitatorID);";
            $result = mysqli_query($conn, $query);

            if($result)
            {
                $_SESSION['acceptAttendance'] = "Student attendance recorded successfully!";
            }
            else    
            {
                echo 'query not successful';
            }
        }

        if ($buttonClicked == "btnDecline")
        {
            $_SESSION['declineAttendance'] = "Student attendance declined!";
        }

        if ($buttonClicked == "btnAddToDatabase")
        {
            $serialNumber = $_SESSION['SerialNumber'];
            $studentNumber = $_POST['studentNumber'];
            $nameFirst = $_POST['nameFirst'];
            $nameLast = $_POST['nameLast'];
            $emailAddress = $_POST['emailAddress'];

            $query = "INSERT INTO studentsinfo (StudentNumber, FirstName, LastName, Email, RFIDSerialNumber, FacilitatorID) VALUES ('$studentNumber', '$nameFirst', '$nameLast', '$emailAddress', '$serialNumber', $facilitatorID);";

            $result = mysqli_query($conn, $query);
            
            if($result)
            {
                $_SESSION['statusAddInfoToDatabase'] = "New student record created successfully!";
            }
            else    
            {
                echo 'query not successful';
            }
        }
    }

    ?>

    <!-- STARTS SCAN WHEN BUTTON IS CLICKED -->
    <script>
        scanButton.addEventListener("click", async () => {
            log("User clicked scan button");

            try {
                const ndef = new NDEFReader();
                await ndef.scan();
                log("> Scan started");

                ndef.addEventListener("readingerror", () => {
                    log("Argh! Cannot read data from the NFC tag. Try another one?");
                });

                ndef.addEventListener("reading", ({ message, serialNumber }) => {
                    log(`> Serial Number: ${serialNumber}`);
                    log(`> Records: (${message.records.length})`);

                    // Send serialNumber to PHP using fetch
                    fetch(window.location.href, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ serialNumber: serialNumber }),
                    })
                    .then(response => {
                        // Handle the response from PHP if needed
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
                });
            } catch (error) {
                log("Argh! " + error);
            }
        });
    </script>

    <div class="attendanceCard" id="scanSuccess" style="<?php echo $showScanSuccess ? 'display: block;' : 'display: none;'; ?>">
        <h1 class="cardTitle">Scan Success</h1>
        <p class="cardResult" id="name">
            <?php echo $name; ?>
        </p>
        <p class="cardResult" id="studentNumber" name="studentNumber">
            <?php echo $studentNumber; ?>
        </p>
        <form method="post" class="buttonWrapper">
            <button type="submit" class="btn buttonAccept" name="button" value="btnAccept" id="acceptButton">Accept</button>
            <button type="submit" class="btn buttonDecline" name="button" value="btnDecline" id="declineButton">Decline</button>
        </form>
    </div>

    <div class="attendanceCard" id="scanNoRecord" style="<?php echo $showNoRecord ? 'display: block;' : 'display: none;'; ?>">
        <h1 class="cardTitle">Scan Success</h1>
        <p class="cardResult" id="noresult">Student has no information.</p>
        <button class="btn buttonRecord" name="button" value="btnAddRecord" id="addRecordButton">Add Record</button>
    </div>

    <div class="attendanceCard" id="scanFailed" style="<?php echo $showScanFailed ? 'display: block;' : 'display: none;'; ?>">
        <h1 class="cardTitle">Scan Failed</h1>
        <p class="cardResult" id="name">Scan error! Please try again.</p>
    </div>

    <div class="attendanceCard" id="addRecord">
        <form method="post">
            <h1 class="cardTitle">Add Student Information</h1>
            <p class="cardResult">Student Number</p>
            <input type="number" id="studentNumber" name="studentNumber" autocomplete = "off" required>
            <p class="cardResult">First Name</p>
            <input type="text" id="nameFirst" name="nameFirst" autocomplete = "off" required>
            <p class="cardResult">Last Name</p>
            <input type="text"id="nameLast" name="nameLast" autocomplete = "off" required>
            <p class="cardResult">Email Address</p>
            <input type="text" id="emailAddress" name="emailAddress" autocomplete = "off" required>
            <p class="cardResult"></p>
            <button class="btn buttonRecord" name="button" value="btnAddToDatabase" id="addToDatabaseButton">Add Record</button>
        </form>
    </div>

    <!-- DISPLAY ALERTS HERE -->
    <div class="alertContainer" id="alerts">
        <!-- DISPLAY ALERT BOX FOR ADDING NEW STUDENT RECORD IN DATABASE -->
        <?php
            if(isset($_SESSION['statusAddInfoToDatabase']))
            {
            ?>
                <div class="alert alertSuccess">
                    <?php echo $_SESSION['statusAddInfoToDatabase']; ?>
                </div>
            <?php 
                unset($_SESSION['statusAddInfoToDatabase']);

            }
        ?>

        <!-- DISPLAY ALERT BOX FOR ACCEPTING STUDENT ATTENDANCE -->
        <?php
            if(isset($_SESSION['acceptAttendance']))
            {
            ?>
                <div class="alert alertSuccess">
                    <?php echo $_SESSION['acceptAttendance']; ?>
                </div>
            <?php 
                unset($_SESSION['acceptAttendance']);

            }
        ?>

        <!-- DISPLAY ALERT BOX FOR DECLINING STUDENT ATTENDANCE -->
        <?php
            if(isset($_SESSION['declineAttendance']))
            {
            ?>
                <div class="alert alertFailed">
                    <?php echo $_SESSION['declineAttendance']; ?>
                </div>
            <?php 
                unset($_SESSION['declineAttendance']);

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
        <a class="menuIcon">
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

    <script>
        document.getElementById('addRecordButton').addEventListener('click', function() {
            var addRecordDiv = document.getElementById('addRecord');
            var scanNoRecord = document.getElementById('scanNoRecord');

            if (addRecordDiv.style.display === 'none' || addRecordDiv.style.display === '')
            {
                addRecordDiv.style.display = 'block';
                scanNoRecord.style.display = 'none';
            }
            else
                addRecordDiv.style.display = 'none';
        })
    </script>

    <script>
        setTimeout(function() {
            var alert = document.getElementById("alerts");
            var childDiv = alert.getElementsByTagName('div');
            if (childDiv.length > 0) {
                alert.style.display = 'none';
            }
        }, 5000);
    </script>    

    <!-- THIS IS OPTIONAL AS IT IS JUST A WARNING THAT THE NFC READER IS NOT MEANT TO BE USED ON CURRENT CHROME VERSION -->
    <!-- <script>
        if (/Chrome\/(\d+\.\d+.\d+.\d+)/.test(navigator.userAgent)) {
            // Let's log a warning if the sample is not supposed to execute on this
            // version of Chrome.
            if (89 > parseInt(RegExp.$1)) {
                ChromeSamples.setStatus('Warning! Keep in mind this sample has been tested with Chrome ' + 89 + '.');
            }
        }
    </script> -->



    <!-- TELLS THAT NFC READER IS MEANT TO BE USED ONLY ON ANDROID CHROME -->
    <!-- <script>
        log = ChromeSamples.log;

        if (!("NDEFReader" in window))
            ChromeSamples.setStatus("Web NFC is not available. Use Chrome on Android.");
    </script> -->
</body>

</html>