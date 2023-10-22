<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include 'connect.php';
    $serialNumber = $_POST['RFIDSerialNumber'];

    $query = "SELECT FirstName, LastName, StudentNumber FROM students WHERE RFIDSerialNumber = '$serialNumber'";
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

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Sample illustrating the use of Web NFC.">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Web NFC Sample</title>
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
    <div class="container">
        <h1>Attendance Monitoring</h1>
        <form method="post" class="formContainer">
            <input type="text" id="serialNumber" name="RFIDSerialNumber">
            <button type="submit" class="btn buttonScan">Scan</button>
        </form>
    </div>

    <div class="attendanceCard" id="scanSuccess" style="<?php echo $showScanSuccess ? 'display: block;' : 'display: none;'; ?>">
        <h1 class="cardTitle">Scan Success</h1>
        <p class="cardResult" id="name">
            <?php echo $name; ?>
        </p>
        <p class="cardResult" id="studentNumber">
            <?php echo $studentNumber; ?>
        </p>
        <div class="buttonWrapper">
            <button class="btn buttonAccept" id="acceptButton">Accept</button>
            <button class="btn buttonDecline" id="declineButton">Decline</button>
        </div>
    </div>

    <div class="attendanceCard" id="scanNoRecord" style="<?php echo $showNoRecord ? 'display: block;' : 'display: none;'; ?>">
        <h1 class="cardTitle">Scan Success</h1>
        <p class="cardResult" id="noresult">Student has no record in the database.</p>
        <button class="btn buttonRecord" id="addRecordButton">Add Record</button>
    </div>

    <div class="attendanceCard" id="scanFailed" style="<?php echo $showScanFailed ? 'display: block;' : 'display: none;'; ?>">
        <h1 class="cardTitle">Scan Failed</h1>
        <p class="cardResult" id="name">Scan failed. Please try again.</p>
    </div>


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