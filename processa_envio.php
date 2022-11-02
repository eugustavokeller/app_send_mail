<?php

    require "./lib/PHPMailer/Exception.php";
    require "./lib/PHPMailer/OAuth.php";
    require "./lib/PHPMailer/PHPMailer.php";
    require "./lib/PHPMailer/POP3.php";
    require "./lib/PHPMailer/SMTP.php";

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    use PHPMailer\PHPMailer\SMTP;


    class Mensagem {
        private $nome = null;
        private $para = null;
        private $assunto = null;
        private $mensagem = null;
        private $status = array( 'codigo_status' => null, 'descricao_status' => '');

        public function __get($atributo)
        {
            return $this->$atributo;
        }

        public function __set($atributo, $valor)
        {
            $this->$atributo = $valor;
        }

        public function mensagemValida() {
            if(empty($this->para) || empty($this->assunto) || empty($this->mensagem) || empty($this->nome)) {
                return false; 
            }
            return true;
        }
    }

    $mensagem = new Mensagem();

    $mensagem->__set('para', $_POST['para']);
    $mensagem->__set('assunto', $_POST['assunto']);
    $mensagem->__set('mensagem', $_POST['mensagem']);
    $mensagem->__set('nome', $_POST['nome']);

    if(!$mensagem->mensagemValida()) {
        echo 'Mensagem é invalida';
        header('Location: index.php');
    }

    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->SMTPDebug = false;                      //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = 'gustavo.lealtec@gmail.com';                     //SMTP username
        $mail->Password   = 'facybfzoxeuiooeu';                               //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         //Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
        $mail->Port       = 587;                                    //TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

        //Recipients
        $mail->setFrom('gustavo.lealtec@gmail.com', 'PHPMailer');
        $mail->addAddress($mensagem->para, 'Gustavo Keller');     //Add a recipient
        // $mail->addReplyTo('info@example.com', 'Information');
        // $mail->addCC('cc@example.com');
        // $mail->addBCC('bcc@example.com');

        //Attachments
        // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
        // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = "E-mail teste PHPMailer";
        $mail->Body    = "Oi <strong>$mensagem->nome</strong>, este e-mail é apenas um <strong>teste</strong>!";
        $mail->AltBody = "Oi $mensagem->nome, eu sou o conteudo do email!";

        $mail->send();
        $mensagem->__set('status[codigo_status]', 1);
        $mensagem->__set('status[descricao_status]', 'E-mail enviado com sucesso!');
        
    } catch (Exception $e) {

        $mensagem->__set('status[codigo_status]', 2);
        $mensagem->__set('status[descricao_status]', "Não foi possível enviar o e-mail. Mensagem do Erro: {$mail->ErrorInfo}");
    }

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>App Mail Send</title>

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    </head>
    <body>

        <div class="container">
        <div class="py-3 text-center">
				<img class="d-block mx-auto mb-2" src="logo.png" alt="" width="72" height="72">
				<h2>Send Mail</h2>
				<p class="lead">Seu app de envio de e-mails particular!</p>
			</div>
        </div>
        
        <div class="row">
            <div class="col-md-12">

                <? if($mensagem->status['codigo_status'] == 1) { ?>
                    
                    <div class="container">
                        <h1 class="display-4 mx-auto text-success">Sucesso</h1>
                        <p><?= $mensagem->status['descricao_status'] ?></p>
                        <a href="index.php" class="btn btn-success btn-lg mt-5 text-white">Voltar</a>
                    </div>
                    
                <?} ?>

                <? if($mensagem->status['codigo_status'] == 2) { ?>
                    
                    <div class="container">
                        <h1 class="display-4 text-dander">Ops!</h1>
                        <p><?= $mensagem->status['descricao_status'] ?></p>
                        <a href="index.php" class="btn btn-danger btn-lg mt-5 text-white">Voltar</a>
                    </div>

                <?} ?>

            </div>
        </div>

    </body>
</html>