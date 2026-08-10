<?php

$conn = \App\Repositories\Api\V2\SqlServerApiRepository::startConnection();
if (!$conn) {
    echo "Connection failed.\n";
    print_r(sqlsrv_errors());
    exit;
}

$sql = "SELECT EmployeeRowID, CategoryID, NameAR, NameEN, JobTitle, Photo, Username, Password FROM dbo.MobileApp_HR_Employees";
$result = sqlsrv_query($conn, $sql);

if ($result === false) {
    echo "Query failed.\n";
    print_r(sqlsrv_errors());
} else {
    echo "Query succeeded.\n";
    $count = 0;
    while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
        $count++;
    }
    echo "Found $count rows.\n";
}
sqlsrv_close($conn);
