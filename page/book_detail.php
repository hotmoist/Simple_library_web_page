<?php
    session_start();
    include "db_conn.php";
    include "main.php"; 
    $stmt = $conn -> prepare("SELECT E.TITLE, A.AUTHOR, E.PUBLISHER, EXTRACT(YEAR FROM CAST (E.YEAR AS DATE)) AS YEAR  
    FROM  EBOOK E, AUTHORS A
    WHERE E.ISBN = A.ISBN
    AND E.TITLE = :title
    ");
    $cno = $_SESSION['cno'];
    $title = $_GET['title'];
    $stmt -> execute(array($title));
    $author = '';
    $publisher = '';
    $year = '';

    if($row = $stmt -> fetch(PDO::FETCH_ASSOC)){

        $book_title = $row['TITLE'];
        $author = $row['AUTHOR'];
        $publisher = $row['PUBLISHER'];
        $year = $row['YEAR'];
    }
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>상세내용|트리도서관</title>
</head>
<body>
    <h1>상세내용</h1>
    <div class="container">
        <table class= "table table-bordered text-center">
            <tbody>
            <tr>                
                <td>제목</td>
                <td><?= $book_title ?></td>
            </tr>
            <tr>
                <td>저자</td>
                <td><?= $author ?></td>
            </tr>
            <tr>
                <td>출판사</td>
                <td><?= $publisher ?></td>
            </tr>
            <tr>
                <td>연도</td>
                <td><?= $year ?></td>
            </tr>
            </tbody>
        </table>
        <p>
            <!-- 대출 가능 여부 구현 -->
            <?php
            $stmt = $conn -> prepare("SELECT ISBN, TITLE, DATERENTED
            FROM EBOOK
            WHERE TITLE = :title
            AND DATERENTED IS NOT NULL");
            $stmt -> execute(array($title));
            if($row = $stmt -> fetch(PDO::FETCH_ASSOC)){
                ?> 
            <p>대출된 도서입니다. * 본인이 이미 예약한 도서인 경우 구현 필요</p>
            <!-- 예약 기능 구현 -->
            <form nam="reserve_form" method="POST" action="../reserve.php">
            <input type='hidden' name= "isbn" value="<?=$row['ISBN']?>">
            <button name='reserve' id='reserve' type='submit'>예약</button>
            </form>
            <?php 
            }else {
            ?>
        
            <form name="loan_form" method="POST" action="../loanable.php">
            <input type="hidden" name = "title" value="<?=$book_title?>"> 
            <button name="loan" id="loan" type="submit">대출</button>
           <?php 
            }
            ?>
        </p>
        </form>
    </div>
</body>
</html>