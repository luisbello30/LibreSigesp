<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Documento sin t&iacute;tulo</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<?php
	// en el php.ini hay que buscar [mail function]
	// y colocar la siguiente configuración
	// [mail function] 
	// ; For Win32 only. 
	// SMTP = localhost (si es un servidor externo colocar el nombre del servidor)
	// smtp_port = 25 
	// ; For Win32 only. 
	// sendmail_from = webmaster@tusitio.com 
	// supuestamente funciona tanto para linux como para windows

	//-----------------------------------------------------------------------------
	// envío de correo con formato HTML
	$destinatario = "wilmerbriceno@gmail.com"; 
	$asunto = "Este mensaje es de prueba"; 
	$cuerpo = ' 
				<html> 
				<head> 
				   <title>Prueba de correo</title> 
				</head> 
				<body> 
					<h1>Hola!!!!!!</h1> 
					<p> 
						<b>Correo Electrónico de prueba</b>.Envío de mails por PHP.
					</p> 
				</body> 
				</html>'; 
				
	//para el envío en formato HTML 
	$headers = "MIME-Version: 1.0\r\n"; 
	$headers .= "Content-type: text/html; charset=iso-8859-1\r\n"; 
	
	//dirección del remitente 
	$headers .= "From: Yesenia Moreno <yessimoreno@hotmail.com>\r\n"; 

	//dirección de respuesta, si queremos que sea distinta que la del remitente 
	$headers .= "Reply-To: yessimorenos@yahoo.es\r\n"; 

	//ruta del mensaje desde origen a destino 
	$headers .= "Return-path: yessimoreno@hotmail.com\r\n" 

	//direcciones que recibián copia 
	$headers .= "Cc: yessimoreno@hotmail.com\r\n"; 

	//direcciones que recibirán copia oculta 
	$headers .= "Bcc: yessimoreno@hotmail.com\r\n"; */

	if (mail($destinatario,$asunto,$cuerpo,$headers))
	{
		print "Lo envió";
	}
	else
	{
		print "falló";
	}
	//-----------------------------------------------------------------------------


	//-----------------------------------------------------------------------------
	// Envío sencillo de correos
	//	if (mail("yessimoreno@hotmail.com","asuntillo","Este es el cuerpo del mensaje"))
    //	{
	//		print "lo envió"
	//	}
	//	else
	//	{
	//		print "falló";
	//	}
	//-----------------------------------------------------------------------------

?> 
</body>
</html>