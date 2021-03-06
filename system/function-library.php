<?php

//----- function php -----
function showAlert($message){
    echo "<script>alert('$message')</script>";
}

function showAlertDiv($message){
    ?>
        <div class="container">
            <div class="alert alert-warning text-brown font-weight-bold" role="alert">
                <i class="fas fa-exclamation-triangle"></i> &nbsp; <?= $message ?>
            </div>
        </div>
    <?php
}

function showInfoDiv($message){
    ?>
        <div class="container">
            <div class="alert alert-info font-weight-bold" role="alert" style="color: purple">
                <i class="far fa-check-circle"></i> &nbsp; <?= $message ?>
            </div>
        </div>
    <?php
}

function showModal($title, $message, $buttonmessage, $button){
    // $title utk nentuin titlenya
    // $message utk nentuin isinya mau apa
    // $buttonmessage utk nentuin teks button nya mau apa
    // $button utk klo ada tambahan button (opsional)    

    echo '<div id="myModal" class="modal fade">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title">' . $title . '</h5>
            </div>
            <div class="modal-body">
            ' . $message . '
            </div>
            <div class="modal-footer">
            ' . $button . '
                <button type="button" class="btn btn-secondary" data-dismiss="modal">' . $buttonmessage . '</button>
            </div>
        </div>
    </div>
    </div>';
    echo "<script>
        showModal();
    </script>";
}

function showAlertModal($warna, $icon, $message, $buttonmessage, $button){
    // $warna utk nentuin warna background atas, pake class bootstrap aja.
    // $icon utk nentuin iconnya
    // $message utk nentuin isinya mau apa
    // $buttonmessage utk nentuin teks button nya mau apa
    // $button utk klo ada tambahan button (opsional)

    echo '<style>
    /* Khusus Modal Alert */
        .modal-confirm {		
            color: #434e65;
            width: 525px;
        }
        .modal-confirm .modal-content {
            padding: 20px;
            font-size: 16px;
            border-radius: 5px;
            border: none;
        }
        .modal-confirm .modal-header {
            border-bottom: none;   
            position: relative;
            text-align: center;
            margin: -20px -20px 0;
            border-radius: 5px 5px 0 0;
            padding: 35px;
        }
        .modal-confirm h4 {
            text-align: center;
            font-size: 36px;
            margin: 10px 0;
        }
        .modal-confirm .form-control, .modal-confirm .btn {
            min-height: 40px;
            border-radius: 3px; 
        }
        .modal-confirm .close {
            position: absolute;
            top: 15px;
            right: 15px;
            color: #fff;
            text-shadow: none;
            opacity: 0.5;
        }
        .modal-confirm .close:hover {
            opacity: 0.8;
        }
        .modal-confirm .icon-box {
            color: #fff;		
            width: 95px;
            height: 95px;
            display: inline-block;
            border-radius: 50%;
            z-index: 9;
            border: 5px solid #fff;
            padding: 10px;
            margin: 0 auto;
        }
        .modal-confirm .icon-box i {
            font-size: 58px;
            margin: -2px 0 0 -2px;
        }
        .modal-confirm.modal-dialog {
            margin-top: 80px;
        }
        .modal-confirm .btn {
            color: #fff;
            border-radius: 4px;
            text-decoration: none;
            transition: all 0.4s;
            line-height: normal;
            border-radius: 30px;
            margin-top: 10px;
            padding: 6px 20px;
            min-width: 150px;
            border: none;
        }
        .modal-confirm .btn:hover, .modal-confirm .btn:focus {
            background: #014d92;
            outline: none;
        }
        </style>';

    echo '<div id="alertModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="alertModal" aria-hidden="true">
        <div class="modal-dialog modal-confirm text-center">
            <div class="modal-content">
                <div class="modal-header ' . $warna . '">
                    <div class="icon-box">
                        ' . $icon . '
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    ' . $message . '                    
                    <button class="btn btn-primary" data-dismiss="modal">' . $buttonmessage . '</button>
                    ' . $button . '
                </div>
            </div>
        </div>
    </div>';
    echo "<script>
        showAlertModal();
    </script>";
}

function updateDataSession($idxSession, $data){
    $_SESSION[$idxSession] = $data;
}

function refreshPage(){
    header("Refresh:0");
}

function hashPassword($p, $b){ // true -> cek pake hash, false -> cek ga pake hash
    if($b == true){
        return password_hash($p, PASSWORD_DEFAULT);
    }
    else{
        return $p;
    }
}

function cekPassword($p1, $p2, $b){ // true -> cek pake hash, false -> cek ga pake hash
    if($b == true){
        if(password_verify($p1, $p2)){
            return true;
        }
        else{
            return false;
        }
    }
    else{
        if($p1 == $p2){
            return true;
        }
        else{
            return false;
        }
    }
}

// function untuk format data
function getDateFormatted($date){
    try {
        $hasil = date("F d, Y H:i:s", strtotime($date));
        return $hasil;
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}

function getSeparatorNumberFormatted($number){
    try {
        $hasil = number_format($number, 0, ',', '.');
        return $hasil;
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}

function printPre($data){
    echo "<pre>";
    print_r($data);
    echo "</pre>";
}

include(__DIR__."/mailer/Exception.php");
include(__DIR__."/mailer/SMTP.php");
include(__DIR__."/mailer/PHPMailer.php");

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

function generateCode($panjang){
    $result = '';
    $dictionary = array_merge(range(1,9), range("a", "z"));
    // Ambil sejumlah kebutuhan dari user!
    for($i = 0; $i < $panjang; $i++){
        $result .= $dictionary[mt_rand(0, count($dictionary) - 1)];
    }

    return $result;
}

function uniqueCodeEmail(){
    // @see https://www.php.net/manual/en/language.variables.scope.php
    /** @var PDO $db AutoComplete PDO */
    global $db;
    $count = -1;
    do{
        $result = generateCode(20);
        $query = "SELECT COUNT(*) FROM confirm_email WHERE code = '$result'";
        $count = $db->query($query)->fetch(PDO::FETCH_ASSOC)["jumlah"];        
    } while($count > 0);
    
    return $result;
}

function sendEmail($to, $subject, $body){
    // Instantiation and passing `true` enables exceptions
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
        $mail->isSMTP();                                            // Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                    // Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
        $mail->Username   = 'squeestore123@gmail.com';                     // SMTP username
        $mail->Password   = 'Bocahhulk1234';                               // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
        $mail->Port       = 587;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

        //Recipients
        $mail->setFrom('squeesquee.store@gmail.com', 'Auto Reply SqueeStore');
        $mail->addAddress($to, 'User');     // Add a recipient

        // Content
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = $body;

        $mail->send();
        echo "Message has been sent!";
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

function getCurrentDate(){
    date_default_timezone_set('asia/jakarta');
    return date('Y-m-d');
}

function getCurrentDateTime(){
    date_default_timezone_set('asia/jakarta');
    return date('Y-m-d H:i:s');
}