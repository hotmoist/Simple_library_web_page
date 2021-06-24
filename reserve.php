<?php
    session_start();
    include "db_conn.php";
    $cno = $_SESSION['cno'];
    $isbn = $_POST['isbn'];

    // reserve 에서 예약 순위 확인 
    $sql = "SELECT COUNT(ISBN) COUNT FROM RESERVE WHERE ISBN = :isbn";
    $stmt = $conn -> prepare($sql);
    $stmt -> execute(array($isbn));
    $row = $stmt -> fetch(PDO::FETCH_ASSOC);
    $count = $row['COUNT'];

    // 현재 cno의 예약 건수 확인, 3건인 경우 예약 max로 더이상 예약 불가 
    $sql = "SELECT COUNT(CNO) COUNT FROM RESERVE WHERE CNO = :cno";
    $stmt = $conn -> prepare($sql);
    $stmt -> execute(array($cno));
    $row = $stmt -> fetch(PDO::FETCH_ASSOC);
    $c_count = $row['COUNT'];

    if($c_count > 2){
        echo "<script>alert('예약 횟수를 초과 하였습니다.'); history.back();</script>";
    }else if($count > 0){

    // 이미 예약된 경우가 존재하면 가장 마지막 예약 날짜에서 +10 추가 
        // 가장 마지막 순번 날짜 추출
        $sql = "SELECT ISBN, CNO, DATETIME, RANK
                FROM 
                    (SELECT ISBN, CNO, DATETIME, RANK() OVER(ORDER BY DATETIME) AS RANK
                    FROM RESERVE
                    WHERE ISBN = :isbn
                    ORDER BY DATETIME
                    )
                WHERE RANK = :count";
        $stmt = $conn -> prepare($sql);
        $stmt -> execute(array($isbn, $count));
        $row = $stmt -> fetch(PDO::FETCH_ASSOC);
        $datetime = $row['DATETIME'];
        
        // RESERVE에서 ISBN과 CNO가 같은 데이터가 이미 존재하는지 확인 (무결성 확인)
        $sql = "SELECT ISBN, CNO FROM RESERVE WHERE ISBN = :isbn AND CNO = :cno";
        $stmt = $conn -> prepare($sql);
        $stmt -> execute(array($isbn, $cno));
        if($row = $stmt -> fetch(PDO::FETCH_ASSOC)){
             echo "<script>alert('이미 예약 중인 도서입니다.'); history.back();</script>";
         }
        else{
            //RESERVE에 INSERT 
            $sql = "INSERT INTO RESERVE
                    VALUES (:isbn, :cno, TO_DATE(:datetime, 'YY/MM/DD') + 1)";
            $stmt = $conn -> prepare($sql);
            $stmt -> execute(array($isbn, $cno, $datetime));     
            echo "<script>alert('예약이 완료 되었습니다.'); history.back();</script>";
        }

    }else{
    // 최초 예약인 경우 EBOOK에서 DATEDUE 다음 날로 예약 지정 
        //EBOOK 에서 DATEDUE 추출
        $sql = "SELECT DATEDUE FROM EBOOK WHERE ISBN = :isbn";
        $stmt = $conn -> prepare($sql);
        $stmt -> execute(array($isbn));
        $row = $stmt -> fetch(PDO::FETCH_ASSOC);
        $datedue = $row['DATEDUE'];
        
        // 추출 된 DATEDUE 바탕으로 RESERVE에 INSERT
        $sql = "INSERT INTO RESERVE
                VALUES (:isbn, :cno, TO_DATE(:datedue, 'YY/MM/DD') + 1)";
        $stmt = $conn -> prepare($sql);
        $stmt -> execute(array($isbn, $cno, $datedue));
        echo "<script>alert('예약이 완료 되었습니다.'); history.back();</script>";
    }
?>


