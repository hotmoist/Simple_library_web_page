<?php
    session_start();
    include "db_conn.php";
    include "main.php";
    $count = 0;
    $cno = $_SESSION['cno'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>예약조회|트리도서관</title>
</head>
<body>
    <p>
        <h1>예약조회</h1>
        <P>홈 > 도서이용 > 예약조회</P>
        <!-- 현재 계정에서 예약된 책들 조회 -->
        <div class="container">
        <form method="POST" action="../rCancel.php" name="reserve_form">
            <table class="table table-bordered text-center">
                <thead>
                    <th>제목</th>
                    <th>저자</th>
                    <th>출판사</th>
                    <th>예약 일자</th>
                </thead>
                <tbody>
                <?php
                    $stmt = $conn -> prepare(" SELECT A.ISBN, R.CNO, E.TITLE, A.AUTHOR, E.PUBLISHER, R.DATETIME
                    FROM EBOOK E, AUTHORS A, RESERVE R
                    WHERE E.ISBN = A.ISBN
                    AND A.ISBN = R.ISBN
                    AND R.CNO = :cno
                    ");
                    $stmt -> execute(array($cno));
                    while ($row = $stmt -> fetch(PDO::FETCH_ASSOC)){
                        $count = $count + 1;
                        ?>
                    <tr>
                        <td><input type ='radio' name ='isbn' value=<?=$row['ISBN']?>><?=$row['TITLE']?></td>
                        <td><input type='hidden' name='cno' value=<?=$cno?>><?=$row['AUTHOR']?></td>
                        <td><?=$row['PUBLISHER']?></td>
                        <td><?=$row['DATETIME']?></td>
                    </tr>
                <?php
                    }
                    echo "예약 건수 : $count 건"                   
                    ?>
                </tbody>
            </table>
            <p>
                <!-- 예약 취소 구현 -->
                <button type="submit">예약취소</button>
            </p>
        </form>
        </div>
    </p>
    
</body>
</html>