<?php
    include "db_conn.php";
    include "main.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>회원관리|트리도서관</title>
</head>
<body>
 <!-- 도서 정보 출력 -->
 <div class ="container">
    <h3 class= "text-center">현재 도서</h3>
    <table class ="table table-bordered text-center">
        <thead>
            <th>ISBN</th>
            <th>제목</th>
            <th>저자</th>
            <th>출판사</th>
            <th>연도</th>
        </thead>
        <tbody>
            <?php
                $stmt = $conn -> prepare("SELECT E.ISBN, E.TITLE, A.AUTHOR, E.PUBLISHER, EXTRACT(YEAR FROM CAST (E.YEAR AS DATE)) AS YEAR
                FROM EBOOK E, AUTHORS A
                WHERE E.ISBN = A.ISBN
                ORDER BY E.ISBN
                ");
                $stmt -> execute();
                
                while($row = $stmt -> fetch(PDO::FETCH_ASSOC)){
            ?>
            <tr>
                <td><?= $row['ISBN'] ?></td>
                <td><?= $row['TITLE'] ?></td>
                <td><?= $row['AUTHOR'] ?></td>
                <td><?= $row['PUBLISHER'] ?></td>
                <td><?= $row['YEAR'] ?></td>
            </tr>
            <?php
                }
            ?>
        </tbody>
    </table>
 <tr>
 <!-- 예약 대기 정보 -->
    <br>
    <h3 class="text-center">현재 예약 대기중인 도서</h3>
    <table class ="table table-bordered text-center">
        <thead>
            <th>제목</th>
            <th>예약대기 수</th>
        </thead>
        <tbody>
            <?php
                $stmt = $conn -> prepare("SELECT E.TITLE, COUNT(*) WAITING 
                                          FROM EBOOK E, RESERVE R
                                          WHERE E.ISBN = R.ISBN
                                          GROUP BY E.TITLE
                                          ORDER BY WAITING DESC");
            $stmt -> execute();
            while($row = $stmt -> fetch(PDO::FETCH_ASSOC)){

            ?>
            <tr>
                <td><?=$row['TITLE']?></td>
                <td><?=$row['WAITING']?></td>
            </tr>
            <?php
            }
            ?>
        </tbody>
    </table>
 <tr>
     <!-- 회원 대출 현황 -->
<br>
        <h3 class="text-center">대출 현황</h3>
        <table class="table table-bordered text-center">
            <thead>
                <th>ISBN</th>
                <th>제목</th>
                <th>저자</th>
                <th>대출 시작일</th>
                <th>대출 마감일</th>
            </thead>
            <tbody>
                <?php
                    $stmt = $conn -> prepare("SELECT E.ISBN, E.TITLE, C.NAME, E.DATERENTED, E.DATEDUE
                                                FROM EBOOK E, CUSTOMER C
                                                WHERE E.CNO = C.CNO
                                                ORDER BY NAME
                    ");
                    $stmt -> execute();
                    while($row = $stmt -> fetch(PDO::FETCH_ASSOC)){
                    ?>
                    <tr>
                        <td><?=$row['ISBN']?></td>
                        <td><?=$row['TITLE']?></td>
                        <td><?=$row['NAME']?></td>
                        <td><?=$row['DATERENTED']?></td>
                        <td><?=$row['DATEDUE']?></td>
                    </tr>
                    <?php
                    }
                    ?>
            </tbody>
        </table>
 </div>
</body>
</html>