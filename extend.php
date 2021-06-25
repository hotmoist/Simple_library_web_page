<?php
    include "db_conn.php";
    session_start();
    $isbn = $_POST['isbn'];
    $cno = $_SESSION['cno'];

    if(isset($_SESSION['cno']) == FALSE){
        // 로그인 여부 확인
        echo "<script>alert('로그인이 필요한 기능입니다.'); location.href='/page/login_page.php';</script>"; 
    }else{
        // 예약 여부 확인
        // 해당 도서에 대해 예약 정보가 존재하는 경우 더이상 연장을 할 수 없다
        $stmt = $conn -> prepare("SELECT COUNT(ISBN) COUNT 
                              FROM RESERVE 
                              WHERE ISBN = :isbn");
    $stmt -> execute(array($isbn));
    $row = $stmt -> fetch(PDO::FETCH_ASSOC);
    
    if($isbn == ""){
        echo "<script>alert('도서를 선택해주세요.'); history.back();</script>";
    }
    else if ($row['COUNT'] > 0){
        // 예약이 존재하는 경우
        echo "<script>alert('예약이 존재하여 더이상 연장이 불가능함니다.'); history.back();</script>";
    } else{
        // 예약 없는 경우
        $stmt = $conn -> prepare("SELECT DATEDUE, EXTTIMES FROM EBOOK WHERE ISBN = :isbn");
        $stmt -> execute(array($isbn));
        $row = $stmt -> fetch(PDO::FETCH_ASSOC);
        $datedue = $row['DATEDUE'];
        
        if($row['EXTTIMES'] == ''){
            $exttimes = 1;
        }else{
            $exttimes = $row['EXTTIMES'] + 1;
        }
        
        //예약이 2회 넘는 경우 더이상 예약 불가능하다
        if ($exttimes > 2 ){
            echo "<script>alert('연장 횟수를 초과하였습니다.'); history.back();</script>";
        }else{
            $stmt = $conn -> prepare("UPDATE EBOOK 
                                      SET DATEDUE = TO_DATE(:datedue) + 10, EXTTIMES = :exttimes 
                                      WHERE ISBN = :isbn");
            $stmt -> execute(array($datedue, $exttimes, $isbn));
            echo "<script>alert('대출이 연장되었습니다.'); history.back();</script>";
        }
    }
    }
?>