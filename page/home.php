<?php
    include "main.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>트리도서관</title>
</head>
<body>
<p>
    <form name="home_total_search" action="total_search.php" method="POST">
        <h1>통합검색</h1>
            <br>
            <input type="text" name="home_search" id="home_search">
            <button type="submit">검색</button>
    </form>
</p>
</body>
</html>