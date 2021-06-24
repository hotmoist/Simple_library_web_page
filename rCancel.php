<?php
    session_start();
    include "db_conn.php";
    $isbn = $_POST['isbn'];
    $cno = $_SESSION['cno'];

    if($isbn == ""){
        echo "<script>alert('도서를 선택해주세요.'); history.back();</script>"; 
    }else{
        $stmt = $conn -> prepare('DELETE FROM RESERVE
                              WHERE ISBN = :isbn
                              AND CNO = :cno
        ');
        $stmt -> execute(array($isbn, $cno));
        echo "<script>alert('예약이 취소되었습니다.'); history.back();</script>"; 
    }
?>