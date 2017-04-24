<?php
require("class.phpmailer.php");
require("class.smtp.php");

if(empty ($_POST['recaptchaResponse'])){
    echo json_encode(["result" => "ko", "mensaje" => "Verifica el captcha"]);
    exit();
}
else
{
    if ($_REQUEST['name'] == '' || $_REQUEST['mail'] == '' || $_REQUEST['tel'] == '' || $_REQUEST['coment'] == ''):
            echo json_encode(["result" => "ko", "mensaje" => "Falta Algun Campo"]);
            die();
        endif;

        if (!filter_var($_REQUEST['mail'], FILTER_VALIDATE_EMAIL)) {
            echo json_encode(["result" => "ko", "mensaje" => "El Correo No es valido"]);
            die();
    }
    $secretKey = "6Lc7fB4UAAAAAK6RkdaIrDDnbcVF7nEbB-3N2NvP";
    $ip = $_SERVER['REMOTE_ADDR'];
    $captcha = $_REQUEST['recaptchaResponse'];
        
    $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$secretKey."&response=".$captcha."&remoteip=".$ip);
    $responseKeys = json_decode($response,true);
    
    if(intval($responseKeys["success"]) !== 1) {
        echo json_encode(["result" => "ko", "mensaje" => "Verifica el captcha"]);
        exit();
    } 
    
    // Datos de la cuenta de correo utilizada para enviar vía SMTP
        $smtpHost = "c0560024.ferozo.com";  // Dominio alternativo brindado en el email de alta 
        $smtpUsuario = "contacto@teambuilder.com.ar";  // Mi cuenta de correo
        $smtpClave = "4fvTZ@r2cJ";  // Mi contraseña
// Email donde se enviaran los datos cargados en el formulario de contacto
        $emailDestino = "contacto@teambuilder.com.ar";
        try {
            $mail = new PHPMailer();
            $mail->IsSMTP();
            $mail->SMTPAuth = true;
            $mail->Port = 587;
            $mail->IsHTML(true);
            $mail->CharSet = "utf-8";

// VALORES A MODIFICAR //
            $mail->Host = $smtpHost;
            $mail->Username = $smtpUsuario;
            $mail->Password = $smtpClave;
            
            $name = $_REQUEST['name'];
            $email = $_REQUEST['mail'];
            $phone = $_REQUEST['tel'];
            $mensaje = $_REQUEST['coment'];
            
            $mail->From = $email; // Email desde donde envío el correo.
            $mail->FromName = $_REQUEST['name'];
            $mail->AddAddress($emailDestino); // Esta es la dirección a donde enviamos los datos del formulario

            $mail->Subject = "Contacto desde la WEB"; // Este es el titulo del email.
            $mensajeHtml = nl2br($mensaje);
            $mail->Body = "Nombre: {$name}<br /> Email: {$email}<br /> Telefono: {$phone} <br />Mensaje: <br />{$mensajeHtml} <br /><br /><br />"; // Texto del email en formato HTML
            $mail->AltBody = "Nombre: {$name}\nEmail: {$email}\nTelefono: {$phone}\n Mensaje: \n{$mensaje} \n\n "; // Texto sin formato HTML
// FIN - VALORES A MODIFICAR //

            $mail->Send();
            echo json_encode(["result" => "ok", "mensaje" => "Mensaje enviado correctamente"]);
            die();
        } catch (phpmailerException $e) {
            $resp = "Error enviando el mensaje, intende de nuevo más tarde - " + $e->getMessage();
            echo json_encode(["result" => "ko", "mensaje" => $resp]);
            die();
        } catch (Exception $e) {
            $resp = "Error enviando el mensaje, intende de nuevo más tarde - " + $e->getMessage();
            echo json_encode(["result" => "ko", "mensaje" => $resp]);
            die();
        }

}
exit();