<?php
    session_start();
    include "../db_conn.php";
    include "../auto_return.php";
?>

<!DOCTYPE html>
<html lang="ko">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- Bootstrap CSS -->    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-wEmeIV1mKuiNpC+IOBjI7aAzPcEZeedi5yW5f2yOq55WWLwNGmvvx4Um1vskeMj0"      
    crossorigin="anonymous">
    <style>
        a {
            text-decoration: none;
        }
    </style>
    </head>
    <body>
        <?php
        // 로그인 시 상단 상태 변환 구현
        if(isset($_SESSION['name'])){
            echo "<p1>{$_SESSION['name']} 님 환영합니다.</p1>";
        ?>

        <a href="../logout.php"><input type="button" value="로그아웃" /></a>
        <?php
            if($_SESSION['name'] == 'admin'){
                // 관리자 계정 접속시
        ?>
            <button onclick="location.href='admin_page.php'" title="정보 관리" name="admin">정보관리</button>
        <?php
            }
        }else{
        ?>
        <p>
            로그인이 필요합니다.
            <button onclick="location.href='login_page.php'" title="LOGIN" name="login">LOGIN</button>
        </p>
        <?php
        }
        ?>
        <p>
            <h1><a href="home.php">트리도서관</a></h1>
        </p>
        <hr>
        <p>
        <!-- 상단 바 구현 -->
            <div id="lib_search_list">
                <ul>
                    <li>
                        <button onclick="" title="도서검색" name="book_search">도서검색</button>
                        <ul>
                            <li><button onclick="location.href='total_search.php'" title="통합검색" name="total_search">통합검색</button></li>
                            <li><button onclick="location.href='detail_search.php'" title="상세검색" name="detail_search">상세검색</button> </li>
                        </ul>
                    </li>
                    <li>
                        <button onclick="" title="도서이용" name="book_use">도서이용</button>
                        <ul>
                            <li><button onclick="location.href='book_loan_extend.php'" title="대출현황" name="book_loan_extend">도서대출/연장</button></li>
                            <li><button onclick="location.href='book_reservation.php'" title="예약조회" name="book_reservation">예약조회</button></li>
                            <li><button onclick="location.href='book_return.php'" title="도서반납" name="book_return">도서반납</button></li>
                        </u1>
                    </li>
                </ul>
            </div>
        </p>
        <hr>
    </body>
</html>