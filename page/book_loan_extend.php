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
    <title>도서대출/연장|트리도서관</title>
</head>
<body>  
    <p>
        <h1>대출현황</h1>
        <p>홈 > 도서이용 > 도서대출/연장</p>
        <!-- 도서 대출 현황 정보 출력 -->
        <div class ="container">
            <table class="table table-bordered text-center">
                <thead>
                    <th>제목</th>
                    <th>저자</th>
                    <th>출판사</th>
                    <th>대출일자</th>
                    <th>대출마감일자</th>
                    <th>연장횟수</th>
                </thead>
                <tbody>
                <?php
                    $stmt = $conn -> prepare("SELECT E.TITLE, A.AUTHOR, E.PUBLISHER, E.DATERENTED, E.DATEDUE
                    FROM EBOOK E, AUTHORS A
                    WHERE E.ISBN = A.ISBN
                    AND E.CNO = :cno
                    ");
                    $stmt -> execute(array($cno));
                    while($row = $stmt -> fetch(PDO::FETCH_ASSOC)){
                        $count = $count + 1;
                ?>
                    <tr>
                        <td><?=$row['TITLE'] ?></td>
                        <td><?=$row['AUTHOR'] ?></td>
                        <td><?=$row['PUBLISHER'] ?></td>
                        <td><?=$row['DATERENTED'] ?></td>
                        <td><?=$row['DATEDUE'] ?></td>
                        <?php
                        if($row['EXITTIMES'] == ''){
                            ?>
                            <td>0</td>
                            <?php
                        }else{
                        ?>
                            <td><?=$row['EXTTIMES'] ?></td>
                        <?php
                        }
                        ?>
                    </tr>
                <?php
                    }
                    echo "대출 건수 : $count 건";
                ?>
                </tbody>
            </table>
            <p>
            <!-- 대출 연장 구현 -->
                <button type="submit">대출연장</button>
            </p>
        </div>
    </p>
</body>
</html>