<?php
    session_start();   
	//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	if(!array_key_exists("la_logusr",$_SESSION))
	{
		print "<script language=JavaScript>";
		print "location.href='../sigesp_inicio_sesion.php'";
		print "</script>";		
	}
	$ls_logusr=$_SESSION["la_logusr"];
	require_once("class_funciones_banco.php");
	$io_fun_banco=new class_funciones_banco();
	$io_fun_banco->uf_load_seguridad("SCB","sigesp_scb_p_int_creditodebito.php",$ls_permisos,$la_seguridad,$la_permisos);
	//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////

   //--------------------------------------------------------------
   function uf_limpiarvariables()
   {
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_limpiarvariables
		//		   Access: private
		//	  Description: Función que limpia todas las variables necesarias en la página
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 17/03/2007								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
   		global $io_fun_banco,$ls_operacion,$ls_codtipsol,$ld_fecregdes,$ld_fecreghas,$ld_fecaprord,$li_totrow;
		
		$ls_operacion=$io_fun_banco->uf_obteneroperacion();
		$ls_codtipsol="";
		$ld_fecregdes=date("01/m/Y");
		$ld_fecreghas=date("d/m/Y");
		$ld_fecaprord=date("d/m/Y");
		$li_totrow=0;
   }
   //--------------------------------------------------------------

   //--------------------------------------------------------------
   function uf_load_variables()
   {
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_variables
		//		   Access: private
		//	  Description: Función que carga todas las variables necesarias en la página
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 17/03/2007								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
   		global $li_totrow,$ls_tipope,$ld_fecaprosol;
		
		$li_totrow = $_POST["totrow"];
		$ls_tipope = $_POST["rdtipooperacion"];
		$ld_fecaprord  =$_POST["txtfecaprord"];
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
<title >Notas de Débito o Crédito</title>
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
<script type="text/javascript" language="JavaScript1.2" src="js/funcion_cxp.js"></script>
<script type="text/javascript" language="JavaScript1.2" src="../shared/js/validaciones.js"></script>
<script language="javascript" src="../shared/js/js_intra/datepickercontrol.js"></script>
<link href="css/cxp.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/js/css_intra/datepickercontrol.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php
	if(array_key_exists("operacion",$_POST))
	{
		$ls_operacion=$_POST["operacion"];
		//$ld_fecha=$_POST["txtfecha"];
		$ls_fecdesde=$_POST["txtfecdesde"];
		$ls_fechasta=$_POST["txtfechasta"];
	}
	else
	{
		$ls_operacion="NUEVO";	
		$ld_fecha=date("d/m/Y");
		$ls_fecdesde=$ld_fecha;
		$ls_fechasta=$ld_fecha;
	}
	require_once("../shared/class_folder/sigesp_include.php");
	$io_in=new sigesp_include();
	$con=$io_in->uf_conectar();

	require_once("../shared/class_folder/class_datastore.php");
	$io_ds=new class_datastore();

	require_once("../shared/class_folder/class_sql.php");
	$io_sql=new class_sql($con);

	require_once("../shared/class_folder/class_mensajes.php");
	$io_msg=new class_mensajes();
	
	require_once("../shared/class_folder/class_funciones.php");
	$io_funcion=new class_funciones(); 
	
	require_once("../shared/class_folder/grid_param.php");
	$grid=new grid_param();
	require_once("sigesp_scb_c_transferencias.php");
	$io_reporte_scb=new sigesp_scb_c_transferencias($la_seguridad);
	switch ($ls_operacion) 
	{
		case "GENDISK":
			$ls_diades=substr($ls_fecdesde,0,2);
			$ls_diahas=substr($ls_fechasta,0,2);
			$ls_yeardes=substr($ls_fecdesde,6,4);
			$ls_mesdes=substr($ls_fecdesde,3,2);
			$ls_meshas=substr($ls_fechasta,3,2);
			$ls_yearhas=substr($ls_fechasta,6,4);
			$ld_desde=$ls_yeardes."-".$ls_mesdes."-".$ls_diades;
			$ld_hasta=$ls_yearhas."-".$ls_meshas."-".$ls_diahas;
			$lb_valido=$io_reporte_scb->uf_gennotdebcrexml($ld_desde,$ld_hasta,$la_seguridad);
			if($lb_valido)
			{
				$io_reporte_scb->io_mensajes->message("El xml fué generado");
			}
			else
			{
				$io_reporte_scb->io_mensajes->message("Ocurrio un error al generar el xml");
			}
		break;
		
		case "PROCESAR":
			$lb_valido=$io_reporte_scb->uf_aprobaciondebcre("banco/CREDITO_DEBITO/aprobados/",$la_seguridad);
			if($lb_valido)
			{
				$io_reporte_scb->io_mensajes->message("El xml fué Procesado");
			}
			else
			{
				$io_reporte_scb->io_mensajes->message("Ocurrio un error al procesar el xml");
			}
		break;
	}
	unset($io_reporte_scb);
?>
<table width="762" border="0" align="center" cellpadding="0" cellspacing="0" class="titulo-catclaro">
  <tr>
    <td width="1535" height="30" class="cd-logo"><img src="../shared/imagebank/header.jpg" width="803" height="40"></td>
  </tr>
  <tr>
    <td width="780" height="20" bgcolor="#E7E7E7">
		<table width="776" border="0" align="center" cellpadding="0" cellspacing="0">
			
            <td width="423" height="20" bgcolor="#E7E7E7" class="descripcion_sistema" align="left">Caja y Banco </td>
			  <td width="353" bgcolor="#E7E7E7"><div align="right"><span class="letras-pequenas"><b><?PHP print date("j/n/Y")." - ".date("h:i a");?></b></span></div></td>
	  	    <tr>
	  	      <td height="20" bgcolor="#E7E7E7" class="descripcion_sistema" align="left">&nbsp;</td>
	  	      <td bgcolor="#E7E7E7"><div align="right" class="letras-pequenas"><b><?php print $_SESSION["la_nomusu"]." ".$_SESSION["la_apeusu"];?></b></div></td>
      </table>    </td>
  </tr>
  <tr>
    <td height="20" bgcolor="#E7E7E7" class="cd-menu"><script type="text/javascript" language="JavaScript1.2" src="js/menu.js"></script></td>
  </tr>
  <tr>
    <td width="780" height="13" class="toolbar"></td>
  </tr>
</table>
<p>&nbsp;</p>
<form name="formulario" method="post" action="">
<?php
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$io_fun_banco->uf_print_permisos($ls_permisos,$la_permisos,$ls_logusr,"location.href='sigespwindow_blank.php'");
	unset($io_fun_banco);
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>
<input name="operacion" type="hidden" id="operacion">
<table width="578" height="18" border="0" align="center" cellpadding="1" cellspacing="1">
  <tr>
    <td width="561" colspan="2" class="titulo-ventana">Notas de Débito o Crédito </td>
  </tr>
</table>
<table width="575" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
  <tr>
    <td width="573"></td>
  </tr>
  <tr>
    <td height="22" colspan="3" align="center">&nbsp;</td>
  </tr>
  <tr>
    <td height="33" colspan="3" align="center"><div align="left">
      <table width="511" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
        <tr>
          <td height="22" colspan="5"><strong>Fecha de Periodo </strong></td>
        </tr>
        <tr>
          <td width="136"><div align="right">Desde</div></td>
          <td width="101"><input name="txtfecdesde" type="text" id="txtfecdesde" style="text-align:center"  onKeyPress="currencyDate(this);" value="<?php print $ls_fecdesde;?>"  size="14" maxlength="10" datepicker="true"></td>
          <td width="80">&nbsp;</td>
		  <td width="40"><div align="right">Hasta</div></td>
          <td width="129"><input name="txtfechasta" type="text" id="txtfechasta" style="text-align:center"  onKeyPress="currencyDate(this);" value="<?php print $ls_fechasta;?>"  size="14" maxlength="10" datepicker="true"></td>
          <td width="101">&nbsp;</td>
       <tr>
          <td>&nbsp;</td>
          <td><div align="right"></div></td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
		<tr>
            <td height="50"><input name="Submit2" type="button" class="boton" value="Generar Crédito o Débito" onClick="javascript: ue_procesarcreditodebito();"></td>
            <td height="50" colspan="2">
              <div align="center"></div></td>
			<td height="50">&nbsp;</td>	
            <td height="50"><input name="Submit" type="button" class="boton" value="Solicitar Autorización" onClick="javascript: ue_procesarautorizacion();"></td>
        </tr>
	  </table>
    </div></td>
  </tr>
  <tr>
    <td height="22" colspan="3" align="center">&nbsp;</td>
  </tr>
</table>
<p align="center">

<div id="solicitudes" align="center"></div></p>
</form>   
<p>&nbsp;</p>
</body>
<script language="javascript">
var patron = new Array(2,2,4);
var patron2 = new Array(1,3,3,3,3);
function fecha( cadena ) {

    //Separador para la introduccion de las fechas
    var separador = "/"

    //Separa por dia, mes y año
    if ( cadena.indexOf( separador ) != -1 ) {
         var posi1 = 0
         var posi2 = cadena.indexOf( separador, posi1 + 1 )
         var posi3 = cadena.indexOf( separador, posi2 + 1 )
         this.dia = cadena.substring( posi1, posi2 )
         this.mes = cadena.substring( posi2 + 1, posi3 )
		 this.mes= this.mes-1;
         this.anio = cadena.substring( posi3 + 1, cadena.length )
    } else {
         this.dia = 0
         this.mes = 0
         this.anio = 0   
    }
}

function ue_diferenciafechas2 () 
{  
    //Obtiene los datos del formulario
	f=document.formulario;
    CadenaFecha1 = f.txtfecdesde.value;
    CadenaFecha2 = f.txtfechasta.value;
	if((CadenaFecha1!="")&&(CadenaFecha2!=""))
	{
		//Obtiene dia, mes y año
		var fecha1 = new fecha( CadenaFecha1 )   
		var fecha2 = new fecha( CadenaFecha2 )
		
		//Obtiene objetos Date
		var miFecha1 = new Date( fecha1.anio, fecha1.mes, fecha1.dia )
		var miFecha2 = new Date( fecha2.anio, fecha2.mes, fecha2.dia )
	
		//Resta fechas y redondea
		var diferencia = miFecha2.getTime() - miFecha1.getTime()
		var dias = Math.floor(diferencia / (1000 * 60 * 60 * 24))
		dias=(parseFloat(dias));
		return dias
	}
}

function ue_procesarcreditodebito()
{
	f=document.formulario;
	li_dias=ue_diferenciafechas2 ();
	if((li_dias>0)||(li_dias==0))
	{
		f.operacion.value="GENDISK";
		f.action="sigesp_scb_p_int_creditodebito.php";
		f.submit();
	}
	else
	{
		alert("Por favor ajuste el rango de fechas.");
	}
}

function ue_procesarautorizacion()
{
	f=document.formulario;
	li_dias=ue_diferenciafechas2 ();
	if((li_dias>0)||(li_dias==0))
	{
		f.operacion.value="PROCESAR";
		f.action="sigesp_scb_p_int_creditodebito.php";
		f.submit();
	}
	else
	{
		alert("Por favor ajuste el rango de fechas.");
	}
}

</script> 
</html>
