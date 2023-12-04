<?php
$HOSTNAME = 'adu-ems.mysql.database.azure.com';
$USERNAME = 'MA2O';
$PASSWORD = 'Mamao...';
$DATABASE = 'event-management-system';

$conn = mysqli_init();
mysqli_real_connect($conn, $HOSTNAME, $USERNAME, "$PASSWORD", "$DATABASE", 3306, MYSQLI_CLIENT_SSL);

// if ($conn->connect_errno) {
//     die('Failed to connect to MySQL: ' . $conn->connect_error);
// }
// else {
//     echo "Connection Successful!";
// }
?>