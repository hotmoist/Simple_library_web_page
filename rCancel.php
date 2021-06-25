<?php
    // 예약 취소 php 
    session_start();
    
    if(isset($_SESSION['cno']) == FALSE){
        // 로그인 여부 확인
        echo "<script>alert('로그인이 필요한 기능입니다.'); location.href='/page/login_page.php';</script>"; 
    }else{
        include "db_conn.php";
        $isbn = $_POST['isbn'];
        $cno = $_SESSION['cno'];
        
        if($isbn == ""){
            echo "<script>alert('도서를 선택해주세요.'); history.back();</script>"; 
        }else{
            // RESERVE 테이블에서 예약 정보 삭제
            $stmt = $conn -> prepare('DELETE FROM RESERVE
                              WHERE ISBN = :isbn
                              AND CNO = :cno
            ');
            $stmt -> execute(array($isbn, $cno));
            
            // 만약 예약 취소할 도서가 대출 가능인 도서인 경우 
            $stmt = $conn -> prepare("SELECT CNO FROM EBOOK WHERE DATEDUE IS NULL AND CNO = :cno");
            $stmt -> execute(array($cno));
            
            if($row = $stmt -> fetch(PDO::FETCH_ASSOC)){
                // 예약 취소자가 대출 가능 도서 목록에 존재하는 경우 
                $stmt = $conn -> prepare("UPDATE EBOOK 
                SET CNO =NULL, EXTTIMES = NULL, DATERENTED = NULL, DATEDUE = NULL
                WHERE ISBN = :isbn");
                $stmt -> execute(array($isbn));
                
                // 대출 가능 도서를 삭제하였슴으로 다음 대기자에게 이메일 통보를 한다.
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

                // 다음 순번을 확인 하기 위한 쿼리 실행
                // 다음 순번 대출자는 cno와 daterented가 정해지지만, datedue가 null인 정보를 갖는다. 
                $stmt = $conn -> prepare("UPDATE EBOOK 
                SET CNO = :next_cno, DATERENTED = SYSDATE, DATEDUE = NULL
                WHERE ISBN = :isbn 
                ");
                $stmt -> execute(array($next_cno, $isbn));
                
                //이메일 통보
                include 'mail.php'; 
                }
            }    
            echo "<script>alert('예약이 취소되었습니다.'); history.back();</script>"; 
        }
    }        
?>