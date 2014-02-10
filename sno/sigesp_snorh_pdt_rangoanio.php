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
	$ls_logusr=$_SESSION["la_logusr"];
	require_once("class_folder/class_funciones_nomina.php");
	$io_fun_nomina=new class_funciones_nomina();
	$io_fun_nomina->uf_load_seguridad("SNR","sigesp_snorh_d_tabulador.php",$ls_permisos,$la_seguridad,$la_permisos);
	//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////

   //--------------------------------------------------------------
   function uf_limpiarvariables()
   {
		//////////////////////////////////////////////////////////////////////////////
		//	     Function:  uf_limpiarvariables
		//		   Access: private
		//	  Description: Función que limpia todas las variables necesarias en la página
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 03/03/2010 								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
   		global $ls_destab,$ls_codpas,$ls_codgra,$ls_operacion,$li_aniodes,$li_aniohas,$li_fila,$io_fun_nomina;
		
		$ls_destab="";
		$ls_codpas="";
		$ls_codgra="";
		$li_aniodes="";
		$li_aniohas="";
		$li_fila="";
		$ls_operacion=$io_fun_nomina->uf_obteneroperacion();
   }
   //--------------------------------------------------------------

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
		if(window.event && (window.event.keyCode == 122 || window.event.keyCode == 116 || window.event.ctrlKey)){
		window.event.keyCode = 505; 
		}
		if(window.event.keyCode == 505){ 
		return false; 
		} 
		} 
	}
</script>
<title >Definici&oacute;n de Rango de A&ntilde;os</title>
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
<link href="css/nomina.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
.Estilo1 {color: #333333}
-->
</style>
</head>

<body>
<?php 
	uf_limpiarvariables();
	$ls_destab=$_GET["destab"];
	$ls_codpas=$_GET["codpas"];
	$ls_codgra=$_GET["codgra"];
	$li_aniodes=$_GET["aniodes"];
	$li_aniohas=$_GET["aniohas"];
	$li_fila=$_GET["fila"];
?>
<p>&nbsp;</p>
<form name="form1" method="post" action="">
<?php
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$io_fun_nomina->uf_print_permisos($ls_permisos,$la_permisos,$ls_logusr,"close();");
	unset($io_fun_nomina);
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>		  
<table width="600" height="138" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
  <tr>
    <td height="136">
      <p>&nbsp;</p>
      <table width="550" border="0" align="center" cellpadding="1" cellspacing="0" class="formato-blanco">
        <tr>
          <td width="111" height="22"><div align="right">Tabulador</div></td>
          <td height="20" colspan="3">
            <div align="left">
              <input name="txtdestab" type="text" class="sin-borde3" id="txtdestab" style="cursor:text; font-weight: bolder; font-family: Arial, Helvetica, sans-serif; font-size: 11px; font-style: italic;" value="<?php print $ls_destab;?>" size="60" maxlength="100" readonly #invalid_attr_id="none">
            </div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Paso</div></td>
          <td width="128" height="20">
            <div align="left">
              <input name="txtcodpas" type="text" class="sin-borde3" id="txtcodpas" style="cursor:text; font-weight: bolder; font-family: Arial, Helvetica, sans-serif; font-size: 11px; font-style: italic;" value="<?php print $ls_codpas;?>" size="10" maxlength="3" readonly #invalid_attr_id="none">          
            </div></td>
          <td width="64"><div align="right">Grado</div></td>
          <td width="237"><div align="left">
            <input name="txtcodgra" type="text" class="sin-borde3" id="txtcodgra" style="cursor:text; font-weight: bolder; font-family: Arial, Helvetica, sans-serif; font-size: 11px; font-style: italic;" value="<?php print $ls_codgra;?>" size="10" maxlength="3" readonly #invalid_attr_id="none">
            <input name="fila" type="hidden" id="fila" value="<?php print $li_fila;?>" >
          </div></td>
        </tr>
        <tr>
          <td><p align="right">A&ntilde;o Desde </p></td>
          <td><label>
            <div align="left">
              <input name="txtaniodes" type="text" id="txtaniodes" size="10" maxlength="3" onKeyUp="javascript: ue_validarnumero(this);" style="text-align:right" value="<?php print $li_aniodes;?>">
              </div>
          </label></td>
          <td><div align="right">A&ntilde;o Hasta </div></td>
          <td><label>
            <div align="left">
              <input name="txtaniohas" type="text" id="txtaniohas" size="10" maxlength="3" onKeyUp="javascript: ue_validarnumero(this);" style="text-align:right" value="<?php print $li_aniohas;?>">
              </div>
          </label></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td colspan="4"><p align="right">
		  		<a href="javascript:ue_aceptar();"><img src="../shared/imagebank/tools20/aprobado.gif" alt="Aceptar" width="20" height="20" border="0">Aceptar</a>
				<a href="javascript:ue_cerrar();"><img src="../shared/imagebank/tools20/eliminar.gif" alt="Cancelar" width="20" height="20" border="0">Cancelar</a></p></td>
          </tr>
      </table>    
      <p>&nbsp;</p></td>
  </tr>
</table>
</form>      
<p>&nbsp;</p>
</body>
<script language="javascript">
function ue_aceptar()
{
	f=document.form1;
	li_fila=f.fila.value;
	li_aniodes=f.txtaniodes.value;
	li_aniohas=f.txtaniohas.value;
	if(li_fila!="")
	{		
		if(li_aniodes=="")
		{
			li_aniodes="0";
		}
		if(li_aniohas=="")
		{
			li_aniohas="0";
		}
		if(li_aniodes<=li_aniohas)
		{
			eval("opener.document.form1.txtaniodes"+li_fila+".value='"+li_aniodes+"'");
			eval("opener.document.form1.txtaniohas"+li_fila+".value='"+li_aniohas+"'");
			close();
		}
		else
		{
			alert("Rango Inválido.");
		}
	}
}

function ue_cerrar()
{
	close();
}
</script> 
</html>