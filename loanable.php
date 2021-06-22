<?php
    include "db_conn.php";

    if($_POST['title'] == ""){
        echo'<script> alert("error"); history.back(); </script>';
    }else{
        echo 'insert 문 작성하여 table에 인스턴스 추가';
    }
?>