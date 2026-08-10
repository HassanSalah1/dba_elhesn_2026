<?php

$conn = \App\Repositories\Api\V2\SqlServerApiRepository::startConnection();
if (!$conn) {
    echo "Connection failed.\n";
    exit;
}

$sql = "SELECT TOP 1 * FROM dbo.MobileApp_HR_Employees";
$result = sqlsrv_query($conn, $sql);

if ($result === false) {
    echo "Query failed.\n";
    print_r(sqlsrv_errors());
} else {
    $fields = sqlsrv_field_metadata($result);
    echo "Columns in view:\n";
    foreach ($fields as $field) {
        echo "- " . $field['Name'] . "\n";
    }
}
sqlsrv_close($conn);
