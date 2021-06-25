<?php
    include "db_conn.php";
    include "main.php"
?>
<!-- 로그인 페이지 -->
<!DOCTYPE html>
<html lang="ko">
    <head>
        <title>로그인|트리도서관</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <p>
            <h1>로그인</h1>
            <p>
            홈 > 로그인
            </p>
            <form name="login_form" action="../login.php" method="POST">
                <p>
                    <label for="id_txt">아이디 : </label>
                    <input id="id_txt" type="text" name="id">
                </p>
                <p> 
                    <label for="pwd_txt">비밀번호 : </label>
                    <input id="pwd_txt" type="password" name="pwd"><br>
                </p>
                <p>
                    <button type="submit" >로그인</button>
                </p>
            </form>
        </p>

    </body>
</html>