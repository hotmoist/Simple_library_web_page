<?php
// 맨 마지막에 기능 구현하기 
    session_start();
    include "db_conn.php";
    // 반납 날짜가 지난 책 자동 반납 기능
    $stmt = $conn -> prepare("SELECT ISBN, TITLE, TO_CHAR(DATEDUE + 1 -(1/24/60/60) , 'YYYY-MM-DD HH24:MI:SS') DATEDUE 
    FROM EBOOK 
    WHERE DATEDUE IS NOT NULL
    ");
    $stmt -> execute();
    while($row = $stmt -> fetch(PDO::FETCH_ASSOC)){
        $isbn = $row['ISBN'];
        $title = $row['TITLE'];
        $datedue = $row['DATEDUE'];
        $now = date("Y-m-d H:i:s");

        $str_now = strtotime($now);
        $str_target = strtotime($datedue);

        if($str_now > $str_target){
            // 기간이 지났으므로 반납을 한다.
            $stmt = $conn -> prepare("UPDATE EBOOK
            SET CNO = NULL , EXTTIMES = NULL, DATERENTED = NULL, DATEDUE= NULL
            WHERE ISBN = :isbn
            ");
            $stmt -> execute(array($isbn));

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
                //이메일 통보
                include 'mail.php';
            }
        }
    }
?>