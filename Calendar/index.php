<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Sample illustrating the use of Web NFC.">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.4.0/fullcalendar.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha.6/css/bootstrap.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.4.0/fullcalendar.min.js"></script>

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
    <div class="mainContainer" style="max-width: 800px;">
        <h1>Event Calendar</h1>
        <div id="calendar"></div>
    </div>
    <script>
        $(document).ready(function () {
            var calendar = $('#calendar').fullCalendar({
                editable: true,
                header: {
                    left: 'prev, next today',
                    center: 'title',
                    right: 'month, agendaWeek, agendaDay'
                },
                events: 'load.php',
                selectable: true,
                selectHelper: true,
                select: function (start, end, allDay) {
                    var title = prompt("Enter Event Title");
                    if (title) {
                        var start = $.fullCalendar.formatDate(start, "Y-MM-DD HH:mm:ss");
                        var end = $.fullCalendar.formatDate(end, "Y-MM-DD HH:mm:ss");
                        $.ajax({
                            url: "insert.php",
                            type: "POST",
                            data: { title: title, start: start, end: end },
                            success: function () {
                                calendar.fullCalendar('refetchEvents');
                                alert("Added Successfully");
                            }
                        })
                    }
                },
                editable: true,
                eventResize: function (event) {
                    var start = $.fullCalendar.formatDate(event.start, "Y-MM-DD HH:mm:ss");
                    var end = $.fullCalendar.formatDate(event.end, "Y-MM-DD HH:mm:ss");
                    var title = event.title;
                    var id = event.id;
                    $.ajax({
                        url: "update.php",
                        type: "POST",
                        data: { title: title, start: start, end: end, id: id },
                        success: function () {
                            calendar.fullCalendar('refetchEvents');
                            alert('Event Update');
                        }
                    })
                },
                eventDrop: function (event) {
                    var start = $.fullCalendar.formatDate(event.start, "Y-MM-DD HH:mm:ss");
                    var end = $.fullCalendar.formatDate(event.end, "Y-MM-DD HH:mm:ss");
                    var title = event.title;
                    var id = event.id;
                    $.ajax({
                        url: "update.php",
                        type: "POST",
                        data: { title: title, start: start, end: end, id: id },
                        success: function () {
                            calendar.fullCalendar('refetchEvents');
                            alert("Event Updated");
                        }
                    });
                },
                eventClick: function (event) {
                    if (confirm("Are you sure you want to remove it?")) {
                        var id = event.id;
                        $.ajax({
                            url: "delete.php",
                            type: "POST",
                            data: { id: id },
                            success: function () {
                                calendar.fullCalendar('refetchEvents');
                                alert("Event Removed");
                            }
                        })
                    }
                },
            });
        });
    </script>

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
            if (event.target == modalNewAccount || event.target == modalChangePassword || event.target == modalDisableAccount) 
            {
                modalNewAccount.style.display = "none";
                modalChangePassword.style.display = "none";
                modalDisableAccount.style.display = "none";
            }
        }
    </script>

    <!-- DISPLAY ALERTS HERE -->
    <div class="alertContainer" id="alerts">
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