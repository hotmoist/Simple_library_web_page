<?php
    session_start();
    include "db_conn.php";
    $isbn = $_POST['isbn'];

    if($isbn == ""){
        echo "<script>alert('도서를 선택해주세요.'); history.back();</script>"; 
    }else{
        $conn -> beginTransaction();
        // 반납 될 도서 정보 저장
        $stmt = $conn -> prepare("SELECT TITLE FROM EBOOK WHERE ISBN = :isbn");
        $stmt -> execute(array($isbn));
        $row = $stmt -> fetch(PDO::FETCH_ASSOC);
        $title = $row['TITLE'];


        // 반납 될 도서 반납 
        $stmt = $conn -> prepare("UPDATE EBOOK
        SET CNO = NULL , EXTTIMES = NULL, DATERENTED = NULL, DATEDUE= NULL
        WHERE ISBN = :isbn
        ");
        $stmt -> execute(array($isbn));
        
        
        // 반납 시 다음 예약자에게 이메일 통보
        $stmt = $conn -> prepare("SELECT COUNT(ISBN) COUNT
                                FROM RESERVE
                                WHERE ISBN = :isbn");
        $stmt -> execute(array($isbn));
        $row = $stmt -> fetch(PDO::FETCH_ASSOC);
        if($row['COUNT'] > 0){
            //예약자가 존재하는 경우
            $stmt = $conn -> prepare("SELECT ISBN, CNO, DATETIME, RANK
                                        FROM 
                                            (SELECT ISBN, CNO, DATETIME, RANK() OVER(ORDER BY DATETIME) AS RANK
                                            FROM RESERVE
                                            WHERE ISBN = :isbn
                                            ORDER BY DATETIME
                                            )
                                        WHERE RANK = :count
            ");
            $count = 1;
            $stmt -> execute(array($isbn, $count));
            $row = $stmt -> fetch(PDO::FETCH_ASSOC);
            $next_cno = $row['CNO'];    // 다음 순번

            // 다음 순번 이메일 추출
            $stmt = $conn -> prepare("SELECT EMAIL FROM CUSTOMER WHERE CNO =:cno");
            $stmt -> execute(array($next_cno));
            $row = $stmt -> fetch(PDO::FETCH_ASSOC);
            $email = $row['EMAIL'];
            //이메일 통보
            include 'mail.php';
        }
        try{
            $conn -> commit();
            echo "<script>alert('반납이 완료 되었습니다.'); history.back();</script>"; 
        } catch (PDOException $e){
            $conn -> rollback();
            echo "<script>alert('error.'); location.href='/page/home.php';</script>"; 
        }
    }
?>
    
