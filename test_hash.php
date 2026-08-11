<?php
$conn = \App\Repositories\Api\V2\SqlServerApiRepository::startConnection();
if (!$conn) {
    echo "Connection failed.\n";
    exit;
}
$sql = "SELECT TOP 1 CONVERT(VARCHAR(32), HashBytes('MD5', PlayerPhoto), 2) AS PhotoHash FROM dbo.MobileApp_PlayersPhotos WHERE PlayerPhoto IS NOT NULL";
$result = \sqlsrv_query($conn, $sql);
if ($result === false) {
    echo "Query failed.\n";
    print_r(sqlsrv_errors());
} else {
    $row = sqlsrv_fetch_object($result);
    echo "Hash: " . ($row ? $row->PhotoHash : 'No row') . "\n";
}
sqlsrv_close($conn);
