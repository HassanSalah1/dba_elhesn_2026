<?php
$conn = \App\Repositories\Api\V2\SqlServerApiRepository::startConnection();
$sql = "SELECT Username, COUNT(*) as cnt FROM dbo.MobileApp_HR_Employees GROUP BY Username HAVING COUNT(*) > 1";
$result = sqlsrv_query($conn, $sql);
if ($result === false) { print_r(sqlsrv_errors()); exit; }
echo "Duplicate usernames in SQL Server:\n";
while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
    echo "- '" . $row['Username'] . "' (Count: " . $row['cnt'] . ")\n";
}
$sql2 = "SELECT EmployeeRowID, Username FROM dbo.MobileApp_HR_Employees WHERE Username = 'ayman'";
$result2 = sqlsrv_query($conn, $sql2);
echo "\nRows with username 'ayman':\n";
while ($row = sqlsrv_fetch_array($result2, SQLSRV_FETCH_ASSOC)) {
    echo "- RowID: " . $row['EmployeeRowID'] . "\n";
}
sqlsrv_close($conn);
