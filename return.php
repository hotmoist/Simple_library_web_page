<?php
    session_start();
    include "db_conn.php";
    $isbn = $_POST['isbn'];

    if($isbn == ""){
        echo "<script>alert('도서를 선택해주세요.'); history.back();</script>"; 
    }else{
        $conn -> beginTransaction();
        $stmt = $conn -> prepare("UPDATE EBOOK
        SET CNO = NULL , EXTTIMES = NULL, DATERENTED = NULL, DATEDUE= NULL
        WHERE ISBN = :isbn
        ");
        $stmt -> execute(array($isbn));
        try{
            $conn -> commit();
            echo "<script>alert('반납이 완료 되었습니다.'); history.back();</script>"; 
        } catch (PDOException $e){
            $conn -> rollback();
            echo "<script>alert('error.'); location.href='/page/home.php';</script>"; 
        }
    }
?>
