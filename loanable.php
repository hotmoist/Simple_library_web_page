<?php
    session_start();
    include "db_conn.php";
    $cno = $_SESSION['cno'];
    $isbn = $_POST['isbn'];

    if(isset($_SESSION['cno']) == FALSE){
        // 로그인 여부 확인
        echo "<script>alert('로그인이 필요한 기능입니다.'); location.href='/page/login_page.php';</script>"; 
    }else{

        
        // 현재 cno의 대출 건수 확인하여 3권인 경우 더이상 대출 할 수 없음
        $stmt = $conn -> prepare("SELECT COUNT(CNO) COUNT FROM EBOOK WHERE CNO = :cno AND DATEDUE IS NOT NULL");
        $stmt -> execute(array($cno));
        $row = $stmt -> fetch(PDO::FETCH_ASSOC);
        if($row['COUNT'] > 2){
            echo "<script>alert('대출 횟수를 초과하였습니다.'); history.back();</script>"; 
        }else if($isbn == ""){
            echo'<script> alert("도서를 선택해 주세요."); history.back(); </script>';
        }else{
            
            // echo 'insert 문 작성하여 table에 인스턴스 추가';
            $conn -> beginTransaction();
            $stmt = $conn -> prepare("UPDATE EBOOK
        SET CNO = :cno , EXTTIMES = '0', DATERENTED = SYSDATE, DATEDUE= SYSDATE + 10
        WHERE ISBN = :isbn
         ");
        $stmt -> execute(array($cno, $isbn));
        print_r($stmt);
        echo "$isbn";
        echo "$cno";
        
        // 예약 명단에 존재하는 경우 삭제 
        $stmt = $conn -> prepare("DELETE RESERVE
        WHERE ISBN = :isbn
        AND CNO = :cno");
        $stmt -> execute(array($isbn, $cno));
        
            try{
                $conn -> commit();
                echo "<script>alert('대출 완료 되었습니다.'); history.back();</script>"; 
            } catch (PDOException $e){
                $conn -> rollback();
                echo "<script>alert('error.'); history.back();</script>"; 
            }
        }
    }
?>