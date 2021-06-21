<?php
    include "db_conn.php";
    include "main.php"
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
        <form action="">
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
                <label for="publish_year_1_txt">발행연도</label>
                <input id="publish_year_1_txt" type="text" name="publish_year_1">
                ~
                <input id="publish_year_2_txt" type="text" name="publish_year_2">
            </p>
            <p>
                <button onclick="" type="submit" name="search" title="검색">검색</button>
            </p>
        </form>

    </p>
</body>
</html>