<!doctype html>
<html lang="en">

<head>
    <title>EMS Attendance Scanner</title>
</head>
<style>
    body {
    background-color: ivory;
    overflow: hidden;
    padding: 0;
    display: block;
    justify-content: center;
    align-items: center;
    font-family: "Roboto", "Helvetica", "Arial", sans-serif;
    }

    .mainContainer {
        margin: 5rem;
    }

    h1 {
    margin-bottom: -0.3em;
    font-size: 3rem;
    text-align: center;
    }

    h2 {
    margin-top: 2em;
    font-size: 2.75rem;
    }

    h3 {
    margin-bottom: -0.2em;
    margin-top: 2em;
    font-size: 2.75rem;
    }
    
    .output {
    background-color: #f0f0f0;
    border-radius: 0.75em;
    display: none;
    margin: 0.5em;
    padding: 0.5em;
    }

    #log {
    margin: .5em 0;
    white-space: pre-wrap;
    font-size: 2rem;
    }

    #status:empty,
    #log:empty {
    display: none;
    }

    .scanContainer {
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    }

    #scanButton {
    display: flex;
    justify-content: center;
    align-items: center;
    border: none;
    width: 12rem;
    height: 5rem;
    border: 2px solid #36454F;
    padding: 0.75rem 1.5rem;
    border-radius: 1.5rem;
    font-size: 12px;
    font-size: 1.5rem;
    letter-spacing: 2px;
    }
</style>

<body>
    <div class="mainContainer">
        <h1>EMS Attendance Scanner</h1>
        <br><br>

        <div class="scanContainer">
            <button id="scanButton">Scan</button>
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

                setStatus: function (status) {
                    document.querySelector('#status').textContent = status;
                }
            };

            const clearLog = () => {
                document.querySelector('#log').textContent = '';
            }
        </script>

        <h2 id="status"></h2>

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
            var output = document.getElementById("output");
            let scanning = false;
            let scanTimeout;

            scanButton.addEventListener("click", async () => {
                if (scanning) return; // Prevent multiple scans

                output.style.display = "block";
                        
                clearLog();
                log("Scanning for ID...");
                scanning = true;

                try {
                    const abortController = new AbortController();
                    abortController.signal.onabort = event => {
                        // All NFC operations have been aborted.
                    };

                    const ndef = new NDEFReader();
                    let successfulRead = false;

                    const startScanTimeout = () => {
                        setTimeout(() => {
                            if (scanning && !successfulRead) {
                                log("Nothing has been scanned. Please try again.");
                                abortController.abort();
                                resetScanState(); // Reset state after abort
                            }
                        }, 5000); // 5 seconds timeout
                    };

                    startScanTimeout(); // Start initial timeout

                    await ndef.scan({ signal: abortController.signal });

                    ndef.addEventListener("readingerror", () => {
                        log("Cannot read data from the NFC tag. Please try again.");
                        abortController.abort();
                        resetScanState(); // Reset state after abort
                    });

                    ndef.addEventListener("reading", ({ message, serialNumber }) => {
                        log('Scan successful!');
                        log('Check attendance on computer.');
                        successfulRead = true;

                        fetch('test.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({ serialNumber: serialNumber }),
                        })
                        .catch(error => {
                            console.error('Error: ', error);
                            abortController.abort();
                            resetScanState(); // Reset state after abort
                        })
                        .finally(() => {
                            abortController.abort();
                            resetScanState(); // Reset state after abort
                        });
                    });
                } catch (error) {
                    log("Argh! " + error);
                    abortController.abort();
                    resetScanState(); // Reset state after abort
                }
            });

            const resetScanState = () => {
                scanning = false; // Reset scanning flag
                successfulRead = false; // Reset successful read flag
            };
        </script>
    </div>

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