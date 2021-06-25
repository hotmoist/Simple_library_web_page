<?php
    // 메일 기능 
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    use PHPMailer\PHPMailer\SMTP;

    

    require 'C:\ProgramData\ComposerSetup\bin\vendor\phpmailer\phpmailer\src\Exception.php';
    require 'C:\ProgramData\ComposerSetup\bin\vendor\phpmailer\phpmailer\src\PHPMailer.php';
    require 'C:\ProgramData\ComposerSetup\bin\vendor\phpmailer\phpmailer\src\SMTP.php';

    $mail = new PHPMailer(true); // the true param means it will throw exceptions on errors, which we need to catch $mail->IsSMTP(); // telling the class to use SMTP
    $mail->IsSMTP(); // telling the class to use SMTP
    try {
        $mail->CharSet = "utf-8";   //한글이 안깨지게 CharSet 설정
        $mail->Encoding = "base64";
        $mail->Host = "smtp.gmail.com"; // email 보낼때 사용할 서버를 지정
        $mail->SMTPAuth = true; // SMTP 인증을 사용함
        $mail->Port = 465; // email 보낼때 사용할 포트를 지정
        $mail->SMTPSecure = "ssl"; // SSL을 사용함
        // $mail->Username = $email; // 해당 계정의 이메일, 모두 가짜 이메일이므로 실제 이메일로 하나로 통일한다.
        $mail->Username = "gye0203@gmail.com"; // Gmail 계정
        $mail->Password = "!*gye66031393!*"; // 패스워드
        $mail->SetFrom('gye0203@gmail.com', 'library'); // 보내는 사람 email 주소와 표시될 이름 (표시될 이름은 생략가능)
        $mail->AddAddress('gye0203@gmail.com'); // 받을 사람 email 주소와 표시될 이름 (표시될 이름은 생략가능)
        $mail->Subject = '트리도서관 | 도서 대출 가능 안내'; // 메일 제목
        $mail->Body =
        "   다음 도서가 반납되었습니다.\n
        $title \n
        $email\n
        해당 도서를 다음날 까지 대출하지 않는 경우 자동 예약 취소 됨을 알려드립니다.\n
        해당 도서는 홈 > 도서이용 > 예약조회 페이지에서 대출 가능합니다.
        "; // 내용
        $mail->Send(); // 발송
        
        // echo "Message Sent OK //발송 확인
        // \n";
        // 다음 순번 대기자는 cno와 daterented와 datedue는 null이다  
    }
    catch (phpmailerException $e) {
        echo $e->errorMessage(); //Pretty error messages from PHPMailer
    } catch (Exception $e) {
        echo $e->getMessage(); //Boring error messages from anything else!
    }
?>

