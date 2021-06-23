<?php
    session_start();
    include "db_conn.php";
    $cno = $_SESSION['cno'];
    $title = $_POST['title'];

    if($title == ""){
        echo'<script> alert("error"); history.back(); </script>';
    }else{
        // echo 'insert 문 작성하여 table에 인스턴스 추가';
        $conn -> beginTransaction();
        $stmt = $conn -> prepare("UPDATE EBOOK
        SET CNO = :cno , EXTTIMES = '0', DATERENTED = TO_DATE(SYSDATE, 'YY/MM/DD'), DATEDUE= TO_DATE(SYSDATE + 10, 'YY/MM/DD')
        WHERE TITLE = :title
         ");
        $stmt -> execute(array($cno, $title));
        //print_r($stmt);
        try{
            $conn -> commit();
            echo "<script>alert('대출 완료 되었습니다.'); location.href='/page/home.php';</script>"; 
        } catch (PDOException $e){
            $conn -> rollback();
            echo "<script>alert('error.'); location.href='/page/home.php';</script>"; 
        }
    }
?>