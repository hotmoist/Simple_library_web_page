<?php
// 맨 마지막에 기능 구현하기 
    session_start();
    include "db_conn.php";
    // 반납 날짜가 지난 책 자동 반납 기능
    $now = date("Y-m-d H:i:s");
    // $now_date = date("Y-m-d H:i:s");

    $str_now = strtotime($now);
    
    // CASE 1 : 대출자의 도서가 반납 일자를 지나 자동 반납되는 경우 
    $stmt = $conn -> prepare("SELECT ISBN, CNO, TITLE, DATERENTED, TO_CHAR(DATEDUE + 1 -(1/24/60/60) , 'YYYY-MM-DD HH24:MI:SS') DATEDUE 
    FROM EBOOK 
    WHERE DATEDUE IS NOT NULL
    ");
    $stmt -> execute();
    while($row = $stmt -> fetch(PDO::FETCH_ASSOC)){
        $isbn = $row['ISBN'];
        $title = $row['TITLE'];
        $datedue = $row['DATEDUE'];
        $cno = $row['CNO'];
        $str_target = strtotime($datedue);

        if($str_now > $str_target){
            // 기간이 지났으므로 반납을 한다.
            $stmt = $conn -> prepare("UPDATE EBOOK
            SET CNO = NULL , EXTTIMES = NULL, DATERENTED = NULL, DATEDUE= NULL
            WHERE ISBN = :isbn
            ");
            $stmt -> execute(array($isbn));

            //반납 된 도서 정보 PREVIOUSRENTAL 테이블에 저장
            $stmt = $conn -> prepare("INSERT INTO PREVIOUSRENTAL 
            VALUES(:isbn, :daterented, SYSDATE, :cno)");
            $stmt -> execute(array($isbn, $row['DATERENTED'], $cno));

            // 자동 반납 후 다음 순번자가 존재하면 이메일을 보낸다.
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
                
                // 예약 1순위 사람에 대한 DB 변경 쿼리
                // 해당 쿼리문을 이용하여 예약 조회에서 대출 가능 도서 목록에 추가됨 
                $stmt = $conn -> prepare("UPDATE EBOOK 
                SET CNO = :next_cno, DATERENTED = SYSDATE, DATEDUE = NULL
                WHERE ISBN = :isbn 
                ");
                // $now_date = date("Y-m-d");
                $stmt -> execute(array($next_cno, $isbn));
                
                //이메일 통보
                include 'mail.php';
            }
        }
    }

    // CASE 2 : 예약자가 순서가 되어 대출 가능한 도서가 존재하지만 대출하지 않는 경우
    $stmt = $conn -> prepare("SELECT ISBN, CNO, TITLE, TO_CHAR(DATERENTED + 2 -(1/24/60/60) , 'YYYY-MM-DD HH24:MI:SS') DATERENTED
    FROM EBOOK
    WHERE DATEDUE IS NULL 
    AND DATERENTED IS NOT NULL"); 
    $stmt -> execute();
    while($row = $stmt -> fetch(PDO::FETCH_ASSOC)){
        $isbn = $row['ISBN'];
        $title = $row['TITLE'];
        $dateRented = $row['DATERENTED'];   // 대출 해야할 deadline 시각 정보 
        $cno = $row['CNO'];
        $str_target = strtotime($dateRented);

        if($str_now > $str_target){
            // 기간이 지났으므로 반납을 한다.
            $stmt = $conn -> prepare("UPDATE EBOOK
            SET CNO = NULL , EXTTIMES = NULL, DATERENTED = NULL, DATEDUE= NULL
            WHERE ISBN = :isbn
            ");
            $stmt -> execute(array($isbn));

             //반납 된 도서 정보 PREVIOUSRENTAL 테이블에 저장
             $stmt = $conn -> prepare("INSERT INTO PREVIOUSRENTAL 
             VALUES(:isbn, :daterented, SYSDATE, :cno)");
             $stmt -> execute(array($isbn, $dateRented, $cno));

            // 자동 반납 후 다음 순번자가 존재하면 이메일을 보낸다.
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

                // 예약 1순위 사람에 대한 DB 변경 쿼리
                // 해당 쿼리문을 이용하여 예약 조회에서 대출 가능 도서 목록에 추가됨 
                $stmt = $conn -> prepare("UPDATE EBOOK 
                SET CNO = :next_cno, DATERENTED = SYSDATE, DATEDUE = NULL
                WHERE ISBN = :isbn 
                ");
                // $now_date = date("Y-m-d H:i:s");
                $stmt -> execute(array($next_cno, $isbn));

                //이메일 통보
                include 'mail.php';
            }
        }
    }
?>