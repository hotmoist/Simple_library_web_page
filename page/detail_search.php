<?php
    include "db_conn.php";
    include "main.php";
    $book_name = $_POST['book_name'];
    $author = $_POST['author'];
    $publisher = $_POST['publisher'];
    $min_year = $_POST['min_year'];
    $max_year = $_POST['max_year'];
    $count = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>상세검색|트리도서관</title>
</head>
<body>
    <p>
        <h1>상세검색</h1>
        <p>홈 > 도서검색 > 상세검색</p>
        <p>검색조건</p>
        <form method="post">
            <p>
                <label for="book_name_txt">도서명 : </label> 
                <input id="book_name_txt" type="text" name="book_name">
            </p>
            <p>
                <label for="author_txt">저자 : </label>
                <input id="author_txt" type="text" name="author">
            </p>
            <p>
                <label for="publisher_txt">출판사</label>
                <input id="publisher_txt" type="text" name="publisher">
            </p>
            <p>
                <label for="min_year_txt">발행연도(YYYY~YYYY)</label>
                <input id="min_year_txt" type="text" name="min_year">
                ~
                <input id="max_year_txt" type="text" name="max_year">
            </p>
            <p>
                <button type="submit" name="search" title="검색">검색</button>
            </p>
        </form>
        <hr>

        <div class="container">
            <h2 class="text-center">검색 결과</h2>
            <table class="table table-bordered text-center">
                <thead>
                    <th>제목</th>
                    <th>저자</th>
                    <th>출판사</th>
                    <th>연도</th>
                </thead>
                <tbody>
                    <?php
                    $stmt = $conn -> prepare("SELECT E.TITLE, A.AUTHOR, E.PUBLISHER, EXTRACT(YEAR FROM CAST (E.YEAR AS DATE)) AS YEAR  
                    FROM  EBOOK E, AUTHORS A
                    WHERE E.ISBN = A.ISBN
                    AND LOWER(E.TITLE) LIKE '%' || LOWER(:book_name) || '%'
                    AND LOWER(A.AUTHOR) LIKE '%' || LOWER(:author) || '%'
                    AND LOWER(E.PUBLISHER) LIKE '%' || LOWER (:publisher) || '%'
                    AND YEAR BETWEEN TO_DATE(:min_year) AND TO_DATE(:max_year)
                    ORDER BY E.ISBN
                    ");

                    if($min_year != ''){
                        $min_year = $min_year."-01-01";
                    }
                    if($max_year != ''){
                        $max_year = $max_year."-12-31";
                    }
           
                     if(array_key_exists('search', $_POST) && ($book_name == '' || $author == '' 
                     || $publisher =='' || $min_year =='' || $max_year =='')){
                         echo "<script>alert('검색 정보가 부족합니다.');</script>";
                     }else{

                        $stmt -> execute(array($book_name, $author, $publisher, $min_year, $max_year));
                        while($row = $stmt -> fetch(PDO::FETCH_ASSOC)){
                            $count += 1;
                    ?>
                    <tr>
                        <td><?= $row['TITLE'] ?></td>
                        <td><?= $row['AUTHOR'] ?></td>
                        <td><?= $row['PUBLISHER'] ?></td>
                        <td><?= $row['YEAR']?></td>
                    </tr>
                    <?php    
                        }        
                    }            
                    ?>
                </tbody>
            </table>
            <p>검색결과 : <?=$count?>건</p>
        </div>
    </p>
</body>
</html>