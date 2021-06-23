<?php
    session_start();
    include "db_conn.php";
    $isbn = $_POST['isbn'];
    $cno = $_SESSION['cno'];

    // echo "$isbn";
    // echo "$cno";
    // $conn -> beginTransaction();
    $stmt = $conn -> prepare('DELETE FROM RESERVE
                              WHERE ISBN = :isbn
                              AND CNO = :cno
    ');
    $stmt -> execute(array($isbn, $cno));
    echo "<script>alert('예약이 취소되었습니다.'); history.back();</script>"; 
?>