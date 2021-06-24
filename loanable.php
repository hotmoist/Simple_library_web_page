<?php
    session_start();
    include "db_conn.php";
    $cno = $_SESSION['cno'];
    $title = $_POST['title'];

    // 현재 cno의 대출 건수 확인하여 3권인 경우 더이상 대출 할 수 없음
    $stmt = $conn -> prepare("SELECT COUNT(CNO) COUNT FROM EBOOK WHERE CNO = :cno");
    $stmt -> execute(array($cno));
    $row = $stmt -> fetch(PDO::FETCH_ASSOC);
    if($row['COUNT'] > 2){
        echo "<script>alert('대출 횟수를 초과하였습니다.'); history.back();</script>"; 
    }else if($title == ""){
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
            echo "<script>alert('대출 완료 되었습니다.'); history.back();</script>"; 
        } catch (PDOException $e){
            $conn -> rollback();
            echo "<script>alert('error.'); history.back();</script>"; 
        }
    }
?>