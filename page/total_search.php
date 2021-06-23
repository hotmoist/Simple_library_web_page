<?php
    include "db_conn.php";
    include "main.php";
    $searchWord= $_GET['searchWord'];
    $count = 0;
?>

<!DOCTYPE html>
<html>
    <head>
        <title>통합검색|트리도서관</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <p>
            <h1>통합검색</h1>
            <p>홈 > 도서검색 > 통합검색</p>
            <form>
                <p>
                    <input type="text" name="searchWord" id="searchWord">
                    <button type="submit" name="search" title="검색">검색</button>
                </p>
            </form>
        </p>
        <p>
        <div class="container">
            <h2 class="text-center">검색 결과</h2>
            <table class="table table-bordered text-center">
                <thead>
                    <tr> 
                        <th>제목</th>
                        <th>저자</th>
                        <th>출판사</th>
                        <th>연도</th>
                    </tr>
                </thead>
            <tbody>
                <?php
                $stmt = $conn -> prepare("SELECT E.TITLE, A.AUTHOR, E.PUBLISHER, EXTRACT(YEAR FROM CAST (E.YEAR AS DATE)) AS YEAR  
                FROM  EBOOK E, AUTHORS A
                WHERE E.ISBN = A.ISBN
                AND (LOWER(E.TITLE) LIKE '%' || LOWER(:searchWord) || '%'
                OR LOWER(A.AUTHOR) LIKE '%' || LOWER(:searchWord) || '%'
                OR LOWER(E.PUBLISHER) LIKE '%' || LOWER(:searchWord) || '%') 
                ORDER BY E.ISBN");

                if($_POST["home_search"] != ""){
                    $stmt -> execute(array($_POST["home_search"]));
                }
                if($searchWord != ''){
                    $stmt -> execute(array($searchWord));
                }
                //print_r($stmt);
                //if(($stmt -> fetch(PDO::FETCH_ASSOC)) != ''){           
                    while($row = $stmt -> fetch(PDO::FETCH_ASSOC)){
                        $count = $count + 1;
                        ?>
                    <tr>
                        <td><a href="book_detail.php?title=<?= $row['TITLE']?>"><?= $row['TITLE'] ?></a></td>
                        <td><?= $row['AUTHOR'] ?></td>
                        <td><?= $row['PUBLISHER'] ?></td>
                        <td><?= $row['YEAR']?></td>
                    </tr>
                <?php  
                    //}else{
                        //    print("none");
                        //}
                    }
                    echo "검색 결과 : $count 건";
                    ?>
                </tbody>
            </table>
        </div>
        </p>
    </body>
</html>