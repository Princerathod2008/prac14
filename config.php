<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli(
        "localhost",
        "root",
        "",
        "practical14_db"
    );

    $conn->set_charset("utf8mb4");

} catch (mysqli_sql_exception $e) {
    die("Database connection failed. Check database settings.");
}
?>