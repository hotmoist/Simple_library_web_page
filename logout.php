<?php
    // 로그 아웃 기능
	//session_destroy();
    session_start();
    session_unset();
	include "db_conn.php";
?>
<meta charset="utf-8">
<script>alert("로그아웃되었습니다."); location.href="/page/home.php"; </script>