<?php
    session_start();
    include "db_conn.php";
    $isbn = $_POST['isbn'];
    
    if(isset($_SESSION['cno']) == FALSE){
        // 로그인 여부 확인
        echo "<script>alert('로그인이 필요한 기능입니다.'); location.href='/page/login_page.php';</script>"; 
    }else{
        
        if($isbn == ""){
            echo "<script>alert('도서를 선택해주세요.'); history.back();</script>"; 
        }else{
            
            $conn -> beginTransaction();
            
            // 반납 될 도서 정보 저장
            $stmt = $conn -> prepare("SELECT TITLE, CNO, DATERENTED FROM EBOOK WHERE ISBN = :isbn ");
            $stmt -> execute(array($isbn));
            $row = $stmt -> fetch(PDO::FETCH_ASSOC);
            $title = $row['TITLE'];
            $cno = $row['CNO'];
            $dateRented = $row['DATERENTED'];
            
            
            // 반납 될 도서 반납 
            $stmt = $conn -> prepare("UPDATE EBOOK
                                  SET CNO = NULL, EXTTIMES = NULL, DATERENTED = NULL, DATEDUE= NULL
                                  WHERE ISBN = :isbn
        ");
        $stmt -> execute(array($isbn));
        
        //반납 된 도서 정보 PREVIOUSRENTAL 테이블에 저장
        // 무결성 제약 조건을 피하기 위해 시각 정보를 랜덤하게 지정하여 저장하게 한다. 
        $stmt = $conn -> prepare("INSERT INTO PREVIOUSRENTAL 
                                  VALUES(:isbn, TO_DATE(TO_CHAR((TRUNC(TO_DATE(:dateRented)) + (TRUNC(DBMS_RANDOM.value(0,1000))/1440)),'yyyy-mm-dd hh24:mi:ss'),'yyyy-mm-dd hh24:mi:ss'), SYSDATE, :cno)");
        $stmt -> execute(array($isbn, $dateRented, $cno));
        
        
        // 반납 시 다음 예약자에게 이메일 통보
        // 예약자 확인
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
            // 다음 순번을 확인 하기 위한 쿼리 실행
            // 다음 순번 대출자는 cno와 daterented가 정해지지만, datedue가 null인 정보를 갖는다. 
            $stmt = $conn -> prepare("UPDATE EBOOK 
            SET CNO = :next_cno, DATERENTED = SYSDATE, DATEDUE = NULL
            WHERE ISBN = :isbn 
            ");
            $stmt -> execute(array($next_cno, $isbn));
            
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
}
    ?>
    
