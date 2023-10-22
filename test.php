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

    <div class="attendanceCard" id="scanNoRecord">
        <h1 class="cardTitle">Scan Success</h1>
        <p class="cardResult" id="noresult">Student has no record in the database.</p>
        <button class="btn buttonRecord" id="addRecordButton">Add Record</button>
    </div>

    <div id="addRecord">
        <form method="addInformation">
            <h1 class="cardTitle">Add Student Information</h1>
            <p class="cardResult">Student Number</p>
            <input type="text" id="studentNumber" name="studentNumber">
            <p class="cardResult">First Name</p>
            <input type="text" id="nameFirst" name="nameFirst">
            <p class="cardResult">Last Name</p>
            <input type="text" id="nameLast" name="nameLast">
            <p class="cardResult">Email Address</p>
            <input type="text" id="emailAddress" name="emailAddress">
            <button class="btn buttonRecord" id="addToDatabaseButton">Add to Database</button>
        </form>
    </div>

    <script>
        document.getElementById('addRecordButton').addEventListener('click', function() {
            var addRecordDiv = document.getElementById('addRecord');

            if (addRecordDiv.style.display === 'none' || addRecordDiv.style.display === '')
                addRecordDiv.style.display = 'block';
            else
                addRecordDiv.style.display = 'none';
        })
    </script>
</body>

</html>