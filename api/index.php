<!doctype html>
<html lang="en">

<head>
    <title>EMS Attendance Scanner</title>
    <link rel="stylesheet" href="attendance.css">
</head>
<style>
    body {
    background-color: #ffffff;
    box-sizing: border-box;
    font-family: "Roboto", "Helvetica", "Arial", sans-serif;
    }
    
    @media screen and (min-width: 832px) {
    body {
        width: 100%;
        margin: 0 auto;
    }
    }

    h1 {
    margin-bottom: -0.3em;
    }

    h2 {
    margin-top: 2em;
    }

    h3 {
    margin-bottom: -0.2em;
    margin-top: 2em;
    }

    .pageIcon {
    height: 2.3em;
    float: left;
    margin-right: 0.5em;
    }

    .availability {
    margin-bottom: 2em;
    }

    .output {
    background-color: #f0f0f0;
    border-radius: 0.75em;
    display: block;
    margin: 0.5em;
    padding: 0.5em;
    }

    #log {
    margin: .5em 0;
    white-space: pre-wrap;
    }

    #status:empty,
    #log:empty,
    #content:empty {
    display: none;
    }

    .highlight {
    border-radius: 0.75em;
    border: 1px solid #f0f0f0;
    display: block;
    margin: 0.5em;
    overflow-x: auto;
    padding: 0.5em;
    }

    code {
    font-family: Inconsolata, Consolas, monospace;
    }
</style>

<body>

    <h1>EMS Attendance Scanner</h1>
    <br><br><br><br><br>

    <button id="scanButton">Scan</button>

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

    <h2 id="status"></h2>

    <h3>Live Output</h3>
    <div id="output" class="output">
        <pre id="log"></pre>
    </div>

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

        if (!("NDEFReader" in window))
            ChromeSamples.setStatus("Web NFC is not available. Use Chrome on Android.");
    </script>

    <!-- STARTS SCAN WHEN BUTTON IS CLICKED -->
    <script>
        let isStopped = false;
        let scanTimeout;

        scanButton.addEventListener("click", async () => {
            log("Scanning for ID...");
            
            while (!isStopped) {
                try {
                    onst ndef = new NDEFReader();
                    scanTimeout = setTimeout(() => {
                        if (scanning) {
                            log("Nothing has been scanned. Please try again.");
                            // Check if operation should stop
                            if (isStopped) {
                                break; // Exit the loop
                            }
                        }
                    }, 5000); // 5 seconds timeout

                    await ndef.scan();
                    log("> Scan started");

                    ndef.addEventListener("readingerror", () => {
                        log("Cannot read data from the NFC tag. Please try again.");
                        // Check if operation should stop
                        if (isStopped) {
                            break; // Exit the loop
                        }
                    });

                    ndef.addEventListener("reading", ({ message, serialNumber }) => {
                        log(`> Serial Number: ${serialNumber}`);

                        fetch('test.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({ serialNumber: serialNumber }),
                        })
                        .catch(error => {
                            console.error('Error: ', error);
                        });

                        // Check if operation should stop
                        if (isStopped) {
                            break; // Exit the loop
                        }
                    });
                } catch (error) {
                    log("Argh! " + error);
                    // Check if operation should stop
                    if (isStopped) {
                        break; // Exit the loop
                    }
                }
            }
        })

        // To stop the await operation at a certain point
        function stopAwaitOperation() {
            isStopped = true;
        }
    </script>

    <?php
    // Establish connection to the database (replace these with your credentials)
    $HOSTNAME = "adu-ems.mysql.database.azure.com";
    $USERNAME = "MA2O";
    $PASSWORD = "Mamao...";
    $DATABASE = "event-management-system";

    // Create connection
    $conn = new mysqli($HOSTNAME, $USERNAME, $PASSWORD, $DATABASE);

    // Check connection
    if ($conn -> connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if a serial number was received from the frontend
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $data = json_decode(file_get_contents("php://input"));
        if (isset($data -> serialNumber)) {
            $serialNumber = $data -> serialNumber;

            // Insert the serial number into the database
            $query = "INSERT INTO requests (serialNumber) VALUES ('$serialNumber')";

            if ($conn -> query($query) === TRUE) {
                echo "Serial number inserted successfully";
            } else {
                echo "Error: " . $query . "<br>" . $conn -> error;
            }
        } else {
            echo "Serial number not received";
        }
    }

    $conn->close();
    ?>
</body>

</html>