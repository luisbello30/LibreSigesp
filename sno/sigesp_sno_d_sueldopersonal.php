<?php
	session_start();
	//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	if(!array_key_exists("la_logusr",$_SESSION))
	{
		print "<script language=JavaScript>";
		print "close();";
		print "opener.document.form1.submit();";
		print "</script>";		
	}
	error_reporting(0);
	$ls_logusr=$_SESSION["la_logusr"];
	$ls_codnom=$_SESSION["la_nomina"]["codnom"];
	$ls_codemp=$_SESSION["la_empresa"]["codemp"];
	$li_tipnom=$_SESSION["la_nomina"]["tipnom"];	
	require_once("class_folder/class_funciones_nomina.php");
	$io_fun_nomina=new class_funciones_nomina();
	$io_fun_nomina->uf_load_seguridad_nomina("SNO","sigesp_sno_d_sueldopersonal.php",$ls_codnom,$ls_permisos,$la_seguridad,$la_permisos);
	//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////

	require_once("sigesp_sno_c_calcularnomina.php");
	$io_calcularnomina=new sigesp_sno_c_calcularnomina();
	$li_calculada=str_pad($io_calcularnomina->uf_existesalida(),1,"0");
	unset($io_calcularnomina);

	$ls_operacion=$io_fun_nomina->uf_obteneroperacion();
	$ls_existe=$io_fun_nomina->uf_obtenerexiste();
	$ls_desnom=$_SESSION["la_nomina"]["desnom"];
	$ls_desper=$_SESSION["la_nomina"]["descripcionperiodo"];
	$li_rac=$_SESSION["la_nomina"]["racnom"];
	$ls_sueldo="";
	if($li_rac=="1")
		{
		$ls_sueldo=" readonly";
		}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<script type="text/javascript" language="JavaScript1.2" src="../shared/js/disabled_keys.js"></script>
<script language="javascript">
	if(document.all)
	{ //ie 
		document.onkeydown = function(){ 
		if(window.event && (window.event.keyCode == 122 || window.event.keyCode == 116 || window.event.keyCode == 17 || window.event.ctrlKey)){
		window.event.keyCode = 505; 
		}
		if(window.event.keyCode == 505){ 
		return false; 
		} 
		} 
	}
</script>
<title >Asignaci&oacute;n de Sueldos Masivos de Personal a N&oacute;mina</title>
<meta http-equiv="imagetoolbar" content="no"> 
<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
	background-color: #EFEBEF;
}

a:link {
	color: #006699;
}
a:visited {
	color: #006699;
}
a:active {
	color: #006699;
}

-->
</style>
<script type="text/javascript" language="JavaScript1.2" src="js/stm31.js"></script>
<script type="text/javascript" language="JavaScript1.2" src="js/funcion_nomina.js"></script>
<script language="javascript" src="../shared/js/js_intra/datepickercontrol.js"></script>
<link href="css/nomina.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/js/css_intra/datepickercontrol.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php
	require_once("sigesp_sno_c_personalnomina.php");
	$io_personalnomina=new sigesp_sno_c_personalnomina();
	switch ($ls_operacion) 
		{
		case "GUARDAR":
			$array_codper=$_POST['codper'];
			$txtsueper=$_POST['txtsueper'];	
			$lb_valido=$io_personalnomina->uf_update_personalnomina_sueldos($array_codper,$txtsueper,$ls_codemp,$ls_codnom,$la_seguridad);
		break;
		}
?>
<table width="762" border="0" align="center" cellpadding="0" cellspacing="0" class="contorno">
  <tr>
    <td width="780" height="30" colspan="11" class="cd-logo"><img src="../shared/imagebank/header.jpg" width="778" height="40"></td>
  </tr>
  <tr>
    <td width="432" height="20" colspan="11" bgcolor="#E7E7E7">
		<table width="762" border="0" align="center" cellpadding="0" cellspacing="0">
			<td width="432" height="20" bgcolor="#E7E7E7" class="descripcion_sistema"><?php print $ls_desnom;?></td>
			<td width="346" bgcolor="#E7E7E7"><div align="right"><span class="letras-pequenas"><?php print $ls_desper;?></span></div></td>
	  	    <tr>
	  	      <td height="20" bgcolor="#E7E7E7" class="descripcion_sistema">&nbsp;</td>
	  	      <td bgcolor="#E7E7E7" class="letras-pequenas"><div align="right"><b><?php print $_SESSION["la_nomusu"]." ".$_SESSION["la_apeusu"];?></b></div></td>
	  </table>
	</td>
  </tr>
  <tr>
    <td height="20" colspan="11" bgcolor="#E7E7E7" class="cd-menu"><script type="text/javascript" language="JavaScript1.2" src="js/menu_nomina.js"></script></td>
  </tr>
  <tr>
    <td width="780" height="13" colspan="11" class="toolbar"></td>
  </tr>
  <tr>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_guardar();"><img src="../shared/imagebank/tools20/grabar.gif" alt="Grabar" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_cerrar();"><img src="../shared/imagebank/tools20/salir.gif" alt="Salir" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="530">&nbsp;</td>
  </tr>
</table>

<form name="form1" method="post" action="" autocomplete="off">
<?php
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$io_fun_nomina->uf_print_permisos($ls_permisos,$la_permisos,$ls_logusr,"location.href='sigespwindow_blank_nomina.php'");
	//unset($io_fun_nomina);
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>
  <p align="center">
    <input name="operacion" type="hidden" id="operacion">
</p>
  <table width="500" border="0" align="center" cellpadding="1" cellspacing="1">
    <tr>
      <td width="496" height="20" colspan="2" class="titulo-ventana">Cambio Masivo de Sueldos de Personal N&oacute;mina </td>
    </tr>
  </table>
<br>
    <table width="500" border="0" cellpadding="1" cellspacing="0" class="formato-blanco" align="center">
      <tr>
        <td width="67" height="22"><div align="right">C&oacute;digo</div></td>
        <td width="431"><div align="left">
            <input name="txtcodper" type="text" id="txtcodper" size="30" maxlength="10" onKeyPress="javascript: ue_mostrar(this,event);">
        </div></td>
      </tr>
      <tr>
        <td height="22"><div align="right">C&eacute;dula</div></td>
        <td><div align="left">
          <input name="txtcedper" type="text" id="txtcedper" size="30" maxlength="10" onKeyPress="javascript: ue_mostrar(this,event);">
        </div></td>
      </tr>
      <tr>
        <td height="22"><div align="right">Nombre</div></td>
        <td><div align="left">
          <input name="txtnomper" type="text" id="txtnomper" size="30" maxlength="60" onKeyPress="javascript: ue_mostrar(this,event);">
        </div></td>
      </tr>
      <tr>
        <td height="22"><div align="right">Apellido</div></td>
        <td><div align="left">
            <input name="txtapeper" type="text" id="txtapeper" size="30" maxlength="60" onKeyPress="javascript: ue_mostrar(this,event);">
        </div></td>
      </tr>
	<tr>
        <td height="22"><div align="right">Sueldo</div></td>
        <td><div align="left">
	<select name="txtrangosueldo">
	<option value="=">=</option>
	<option value=">">></option>
	<option value=">=">>=</option>
	<option value="<"><</option>
	<option value="<="><=</option>
	</select>
          <input name="txtsueper" type="text" id="txtsueper" size="23" maxlength="60" onKeyPress="return(ue_formatonumero(this,'.',',',event))">
        </div></td>
      </tr>
      <tr>
        <td height="22">&nbsp;</td>
        <td><div align="right"><a href="javascript: ue_search();"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="20" height="20" border="0"> Buscar</a></div></td>
      </tr>
  </table>
</form>
  <br>
<?php
	$ls_operacion =$io_fun_nomina->uf_obteneroperacion();
	if($ls_operacion=="BUSCAR")
		{
		$ls_codper="%".$_POST["txtcodper"]."%";
		$ls_cedper="%".$_POST["txtcedper"]."%";
		$ls_nomper="%".$_POST["txtnomper"]."%";
		$ls_apeper="%".$_POST["txtapeper"]."%";
		
		$ls_sueper=$_POST["txtsueper"];
		$ls_sueper=str_replace(".","",$ls_sueper);
		$ls_sueper=str_replace(",",".",$ls_sueper);
		if ($ls_sueper!="")
			{
			$ls_rangosueldo=$_POST["txtrangosueldo"];
			$ls_criterio=" AND sno_personalnomina.sueper ".$ls_rangosueldo." '".$ls_sueper."'";
			}
		require_once("../shared/class_folder/sigesp_include.php");
		$io_include=new sigesp_include();
		$io_conexion=$io_include->uf_conectar();
		require_once("../shared/class_folder/class_sql.php");
		$io_sql=new class_sql($io_conexion);	
		require_once("../shared/class_folder/class_mensajes.php");
		$io_mensajes=new class_mensajes();		
		require_once("../shared/class_folder/class_funciones.php");
		$io_funciones=new class_funciones();		
		require_once("../shared/class_folder/class_fecha.php");
		$io_fecha=new class_fecha();		
		require_once("sigesp_sno.php");
		$io_sno=new sigesp_sno();

if ($li_rac=="1")
	echo "<center>El proceso no se aplica en nominadas RAC</center>";
else
	{
?>
<form name="form2" method="post" action="" autocomplete="off">
<?php
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$io_fun_nomina->uf_print_permisos($ls_permisos,$la_permisos,$ls_logusr,"location.href='sigespwindow_blank_nomina.php'");
	//unset($io_fun_nomina);
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>
	<table width=500 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>
        <tr class="formato-blanco">
        <td width="200" height="22"><div align="right">Sueldo a colocarles</div></td>
        <td><div align="left">
		
          <input name="txtsueper" type="text" id="txtsueper" size="23" maxlength="20"  onKeyPress="return(ue_formatonumero(this,'.',',',event))" style="text-align:right"<?php print $ls_sueldo;?>>
	<input name="operacion" type="hidden" id="operacion">
     <input name="existe" type="hidden" id="existe" value="<?php print $ls_existe;?>">
          <input name="rac" type="hidden" id="rac" value="<?php print $li_rac;?>">
          <input name="subnomina" type="hidden" id="subnomina" value="<?php print $li_subnomina;?>">
          <input name="camuniadm" type="hidden" id="camuniadm" value="<?php print $li_camuniadm;?>">
          <input name="calculada" type="hidden" id="calculada" value="<?php print $li_calculada;?>">
          <input name="codunirac" type="hidden" id="codunirac" value="<?php print $li_implementarcodunirac;?>">		  
	  <input type="hidden" name="loncueban" id="loncueban" value="<?php print $li_loncueban;?>">
          <input type="hidden" name="valloncueban" id="valloncueban" value="<?php print $li_valloncueban;?>"></td>

        </div></td>

          <td width="300"><div align="right">Aplicar a Todos
              <input type="checkbox" name="codper" value="1" onclick="marcar(this.checked)">
          </div></td>
        </tr>
	</table>
<br/>
<?php		
		print "<table width=600 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
		print "<tr class=titulo-celda>";
		print "<td width=10></td>";
		print "<td width=60>Código</td>";
		print "<td width=40>Cédula</td>";
		print "<td width=440>Nombre y Apellido</td>";
		print "<td width=100>Sueldo</td>";
		print "</tr>";
		$ls_sql="SELECT sno_personalnomina.codper, sno_personalnomina.sueper, sno_personal.cedper, sno_personal.nomper, sno_personal.apeper FROM sno_personalnomina, sno_personal WHERE sno_personalnomina.codemp = '".$ls_codemp."'".
		"   AND sno_personalnomina.codnom = '".$ls_codnom."' ".
		"   AND sno_personal.codper like '".$ls_codper."' ".
		"   AND sno_personal.cedper like '".$ls_cedper."' ".
		"   AND sno_personal.nomper like '".$ls_nomper."' ".
		"   AND sno_personal.apeper like '".$ls_apeper."' ".
		"   AND sno_personal.estper = '1' ".
		"   AND sno_personalnomina.codemp = sno_personal.codemp ".
		"   AND sno_personalnomina.codper = sno_personal.codper ".
		" $ls_criterio".
		$ls_sql=$ls_sql." ORDER BY sno_personal.codper ";
		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{
        	$io_mensajes->message("ERROR->".$io_funciones->uf_convertirmsg($io_sql->message)); 
		}
		else
		{
		while($row=$io_sql->fetch_row($rs_data))
			{
			$ls_codper=$row["codper"];
			$ls_cedper=$row["cedper"];
			$ls_nomper=$row["nomper"]." ".$row["apeper"];
			$li_sueper=$row["sueper"];			
			$li_sueper=$io_fun_nomina->uf_formatonumerico($li_sueper);
			print "<tr class=celdas-blancas><td><input type='checkbox' name='codper[]' value=$ls_codper></td><td>$ls_codper</td><td> $ls_cedper</td> <td>$ls_nomper</td><td> $li_sueper</td></tr>";
			
			}
		}
		echo "</table>";
		unset($io_include);
		unset($io_conexion);
		unset($io_sql);
		unset($io_mensajes);
		unset($io_funciones);
		unset($ls_codemp);
		unset($ls_codnom);
		unset($io_fecha);
		}
	unset($io_fun_nomina);
?>
</form>
<?php
	}
?>

<script language="JavaScript">
function ue_search()
	{
	f=document.form1;
  	f.operacion.value="BUSCAR";
  	f.action="sigesp_sno_d_sueldopersonal.php?tipo=<?php print $ls_tipo;?>&subnom=<?php print $li_subnomina;?>";
  	f.submit();
	}

function marcar(c)
	{
	a=document.form2.getElementsByTagName("INPUT");
	for(b=0;b<=(a.length-1);b++)
		{
		if(a[b].type=="checkbox")
			{
			a[b].checked=c;
			}
		}
	}

function ue_mostrar(myfield,e)
	{
	var keycode;
	if (window.event) keycode = window.event.keyCode;
	else if (e) keycode = e.which;
	else return true;
	if (keycode == 13)
		{
		ue_search();
		return false;
		}
	else
		return true
	}

function ue_guardar()
	{
	valido=true;
	f=document.form2;
	li_calculada=f.calculada.value;
	if(li_calculada=="0")
		{	
		li_incluir=f.incluir.value;
		li_cambiar=f.cambiar.value;
		lb_existe=f.existe.value;
		sueper = ue_validarvacio(f.txtsueper.value);
		if(((lb_existe=="TRUE")&&(li_cambiar==1))||(lb_existe=="FALSE")&&(li_incluir==1))
			{
			if (sueper=="")
				{
				alert("Debe de llenar el campo sueldo");
				valido=false;
				}
				if(valido)
				{
				f.operacion.value="GUARDAR";
				f.action="sigesp_sno_d_sueldopersonal.php";
				f.submit();			
				}
			}
		else
			{
				alert("No tiene permiso para realizar esta operacion");
			}
		}
	else
		{
		alert("La nómina ya se calculó reverse y vuelva a intentar");
		}
	}

function ue_cerrar()
	{
	location.href = "sigespwindow_blank_nomina.php";
	}
</script>

</body>
</html>