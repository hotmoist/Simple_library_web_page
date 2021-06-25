<?php
//로그인 기능
    session_start();
    include "db_conn.php";
    include "password.php";

    // POST로 받아온 아이디와 비밀번호가 비었다면 알림창을 띄우고 전 페이지로 이동
    if($_POST["id"] == "" || $_POST["pwd"] == ""){
		echo '<script> alert("아이디나 패스워드 입력하세요"); history.back(); </script>';
    }else{
        //password변수에 POST로 받아온 값을 저장하고 sql문으로 POST로 받아온 아이디값을 찾습니다.
        $userid = $_POST["id"];
        $password = $_POST["pwd"];
        $stmt = $conn -> prepare("SELECT * FROM CUSTOMER WHERE EMAIL= :userid ");
        $stmt -> execute(array($userid)); // userid로 배열로 값을 입력
        $member = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $hash_pw = $member[0]['PASSWD'];
         
        if($password == $hash_pw){
            
            $_SESSION['id'] = $member[0]['EMAIL'];
            $_SESSION['pwd'] = $member[0]['PASSWD'];
            $_SESSION['name'] = $member[0]['NAME'];
            $_SESSION['cno'] = $member[0]['CNO'];
            echo "<script>alert('로그인되었습니다.'); location.href='/page/home.php';</script>";
        }else{ // 비밀번호가 같지 않다면 알림창을 띄우고 전 페이지로 돌아갑니다
            echo "<script>alert('아이디 혹은 비밀번호를 확인하세요.'); history.back();</script>";
        }    
    }
?>