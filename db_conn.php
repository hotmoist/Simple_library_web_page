<?php
// db 연동 php
$tns="
DESCRIPTION =
    (ADDRESS = (PROTOCOL = TCP)(HOST = DESKTOP-BLQ49GQ)(PORT = 1521))
    (CONNECT_DATA =
      (SERVER = DEDICATED)
      (SERVICE_NAME = XE)
    )
  )
";
$dsn = "oci:dbname=".$tsn.";charset=utf8";
$username = 'd201702070';
$password = 'd201702070';
try{
    $conn = new PDO($dsn, $username, $password);
} catch(PDOException $e) {
    echo("에러 내용: ".$e -> getMessage());
}
?>