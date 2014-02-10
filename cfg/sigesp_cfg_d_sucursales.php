<?php
session_start();
$dat = $_SESSION["la_empresa"];
if (!array_key_exists("la_logusr",$_SESSION))
   {
	 print "<script language=JavaScript>";
	 print "location.href='../../sigesp_inicio_sesion.php'";
	 print "</script>";		
   }
$ls_logusr=$_SESSION["la_logusr"];
require_once("class_folder/class_funciones_cfg.php");
$io_fun_cfg=new class_funciones_cfg();
$io_fun_cfg->uf_load_seguridad("CFG","sigesp_cfg_d_sucursales.php",$ls_permisos,$la_seguridad,$la_permisos,"../");

$ls_loncodestpro1 = $_SESSION["la_empresa"]["loncodestpro1"];
$li_loncodestpro2 = $_SESSION["la_empresa"]["loncodestpro2"];
$li_loncodestpro3 = $_SESSION["la_empresa"]["loncodestpro3"];
$li_loncodestpro4 = $_SESSION["la_empresa"]["loncodestpro4"];
$li_loncodestpro5 = $_SESSION["la_empresa"]["loncodestpro5"];
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Informaci&oacute;n de Sucursales</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
<!--
body {
	background-color: #EAEAEA;
	margin-left: 0px;
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
.Estilo5 {
	font-size: 11px;
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-weight: bold;
}
.Estilo6 {
	color: #006699;
	font-size: 12px;
}
.Estilo8 {font-size: 10px; font-family: Verdana, Arial, Helvetica, sans-serif; font-weight: bold; }
.Estilo10 {font-size: 10px}
.Estilo11 {font-family: Verdana, Arial, Helvetica, sans-serif}
.Estilo13 {font-size: 12px}
.Estilo14 {font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 12px; }
-->
</style>
<link href="css/cfg.css" rel="stylesheet" type="text/css">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css"   rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<script type="text/javascript" language="JavaScript1.2" src="spg/js/stm31.js"></script>
<script type="text/javascript" language="JavaScript1.2" src="js/funciones_configuracion.js"></script>
<script type="text/javascript" language="JavaScript1.2" src="../shared/js/valida_tecla.js"></script>
<script type="text/javascript" language="JavaScript1.2" src="../shared/js/disabled_keys.js"></script>
<style type="text/css">
<!--
a:hover {
	color: #006699;
}
-->
</style></head>
<body>
<table width="762" border="0" align="center" cellpadding="0" cellspacing="0" class="contorno">
  <tr>
    <td width="780" height="30" class="cd-logo"><img src="../shared/imagebank/header.jpg" width="778" height="40"></td>
  </tr>
  <tr>
    <td height="20" bgcolor="#E7E7E7" class="cd-menu">
	<table width="776" border="0" align="center" cellpadding="0" cellspacing="0">
	
		<td width="423" height="20" bgcolor="#E7E7E7" class="descripcion_sistema">Sistema de Configuración</td>
		<td width="353" bgcolor="#E7E7E7"><div align="right"><span class="letras-pequenas"><b><?PHP print date("j/n/Y")." - ".date("h:i a");?></b></span></div></td>
	  <tr>
		<td height="20" bgcolor="#E7E7E7">&nbsp;</td>
		<td bgcolor="#E7E7E7" class="letras-pequenas"><div align="right"><b><?PHP print $_SESSION["la_nomusu"]." ".$_SESSION["la_apeusu"];?></b></div></td>
	</table></td>
  </tr>
  <tr>
    <td height="20" bgcolor="#E7E7E7" class="cd-menu"><script type="text/javascript" language="JavaScript1.2" src="spg/js/menu.js"></script></td>
  </tr>
  <tr>
    <td height="13" class="toolbar">&nbsp;</td>
  </tr>
  <tr>
    <td height="20" class="toolbar"><div align="left"><a href="javascript: ue_nuevo();"><img src="../shared/imagebank/tools20/nuevo.gif" title="Nuevo" alt="Nuevo" width="20" height="20" border="0"></a><a href="javascript: ue_guardar();"><img src="../shared/imagebank/tools20/grabar.gif" title="Guadar" alt="Grabar" width="20" height="20" border="0"></a><a href="javascript: ue_buscar();"><img src="../shared/imagebank/tools20/buscar.gif" title="Buscar" alt="Buscar" width="20" height="20" border="0"></a><a href="javascript: ue_eliminar();"><img src="../shared/imagebank/tools20/eliminar.gif" title="Eliminar" alt="Eliminar" width="20" height="20" border="0"></a><a href="javascript: ue_cerrar();"><img src="../shared/imagebank/tools20/salir.gif" title="Salir" alt="Salir" width="20" height="20" border="0"></a><img src="../shared/imagebank/tools20/ayuda.gif" title="Ayuda" alt="Ayuda" width="20" height="20"></div>      
    <div align="center"></div>      <div align="center"></div>      <div align="center"></div></td>
  </tr>
</table>
<?php
	require_once("../shared/class_folder/class_mensajes.php");	
	require_once("class_folder/sigesp_cfg_c_sucursales.php");
	
	$io_cfg= new sigesp_cfg_c_sucursales();
	$io_msg= new class_mensajes();

	$ls_codemp    = $_SESSION["la_empresa"]["codemp"];
    $li_estmodest = $_SESSION["la_empresa"]["estmodest"];

	if  (array_key_exists("status",$_POST))
		{
		  $ls_estatus=$_POST["status"];
		}
	else
		{
		  $ls_estatus="";	  
		}	
	
    uf_limpiarvariables();	
	if (array_key_exists("operacion",$_POST))
	{
		$ls_operacion  = $_POST["operacion"];
		$ls_codestpro1 = $_POST["txtcodestpro1"];
		$ls_codestpro2 = $_POST["txtcodestpro2"];
		$ls_codestpro3 = $_POST["txtcodestpro3"];
		$ls_denestpro1 = $_POST["txtdenestpro1"];
		$ls_denestpro2 = $_POST["txtdenestpro2"];
		$ls_denestpro3 = $_POST["txtdenestpro3"];
		if ($_SESSION["la_empresa"]["estmodest"]==2)
		{
			$ls_codestpro4 = $_POST["txtcodestpro4"];
			$ls_codestpro5 = $_POST["txtcodestpro5"];
			$ls_denestpro4 = $_POST["txtdenestpro4"];
			$ls_denestpro5 = $_POST["txtdenestpro5"];
		}
		else
		{
			$ls_codestpro4 = "";
			$ls_codestpro5 = "";
			$ls_denestpro4 = "";
			$ls_denestpro5 = "";
		}
		$ls_estcla= $_POST["hidestcla"];
		$ls_codsuc= $_POST["txtcodsuc"];
		$ls_nomsuc= $_POST["txtnomsuc"];
	}
	else
	{
		$ls_operacion="";
	}
	
		
   function uf_limpiarvariables()
   {
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_limpiarvariables
		//		   Access: private
		//	  Description: Función que limpia todas las variables necesarias en la página
		//	   Creado Por: María Beatriz Unda
		// Fecha Creación: 27/11/2007 								Fecha Última Modificación : 
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

   		global $ls_bd,$ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,$ls_codestpro5,$ls_estcla,$ls_codsuc,
		       $ls_nomsuc,$ls_denestpro1,$ls_denestpro2,$ls_denestpro3,$ls_denestpro4,$ls_denestpro5;
	 	$ls_codsuc= "";
	 	$ls_nomsuc= "";
		$ls_codestpro1= $ls_codestpro2 = $ls_codestpro3 = $ls_codestpro4 = $ls_codestpro5 = "";
		$ls_denestpro1= $ls_denestpro2 = $ls_denestpro3 = $ls_denestpro4 = $ls_denestpro5 = "";
		$ls_estcla= "";
   }	
	if ($ls_operacion=="GUARDAR")
	{
		$ls_codsuc = $io_fun_cfg->uf_obtenervalor("txtcodsuc","");
		$ls_nomsuc = $io_fun_cfg->uf_obtenervalor("txtnomsuc","");
		$ls_codestpro1 = $io_fun_cfg->uf_obtenervalor("txtcodestpro1","0000000000000000000000000");
		$ls_codestpro2 = $io_fun_cfg->uf_obtenervalor("txtcodestpro2","0000000000000000000000000");
		$ls_codestpro3 = $io_fun_cfg->uf_obtenervalor("txtcodestpro3","0000000000000000000000000");
		$ls_codestpro4 = $io_fun_cfg->uf_obtenervalor("txtcodestpro4","0000000000000000000000000");
		$ls_codestpro5 = $io_fun_cfg->uf_obtenervalor("txtcodestpro5","0000000000000000000000000");
		$ls_estcla = $io_fun_cfg->uf_obtenervalor("hidestcla","-");
		$ls_status = $io_fun_cfg->uf_obtenervalor("status","");
		if($ls_status!="C")
		{
			$lb_existe=$io_cfg->uf_select_sucursal($ls_nomsuc);
			if(!$lb_existe)
			{
				$lb_existe=$io_cfg->uf_select_estructura_sucursal($ls_codsuc,$ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,$ls_codestpro5,$ls_estcla);
				if(!$lb_existe)
				{
					$lb_valido=$io_cfg->uf_insert_sucursal($ls_codsuc,$ls_nomsuc,$ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,$ls_codestpro5,$ls_estcla,$la_seguridad);
					if ($lb_valido)
					{
						$io_msg->message("Registro con Éxito.");
						uf_limpiarvariables(); 
					}
					else
					{
						$io_msg->message("Error en Registro.");
						uf_limpiarvariables(); 
					}
				}
				else
				{
					$io_msg->message("La estructura presupuestaria esta relacionada con otra sucursal.");
				}
			}
			else
			{
				$io_msg->message("El código de sucursal ya esta registrado.");
			}
		}
		else
		{
			$lb_existe=$io_cfg->uf_select_estructura_sucursal($ls_codsuc,$ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,$ls_codestpro5,$ls_estcla);
			if(!$lb_existe)
			{
				$lb_valido=$io_cfg->uf_update_sucursal($ls_codsuc,$ls_nomsuc,$ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,$ls_codestpro5,$ls_estcla,$la_seguridad);
				if ($lb_valido)
				{
					$io_msg->message("Registro con Éxito.");
					uf_limpiarvariables(); 
				}
				else
				{
					$io_msg->message("Error en Registro.");
					uf_limpiarvariables(); 
				}
			}
			else
			{
				$io_msg->message("La estructura presupuestaria esta relacionada con otra sucursal.");
			}
		}
	}

	if ($ls_operacion=="DELETE")
	{
		$ls_codsuc= $io_fun_cfg->uf_obtenervalor("txtcodsuc","");
		$lb_valido= $io_cfg->uf_delete_sucursales($ls_codsuc,$la_seguridad);
		if ($lb_valido)
		{
			$io_msg->message("Registro eliminado con Éxito.");
			uf_limpiarvariables(); 
		}
		else
		{
			$io_msg->message("Error en eliminar Registro.");
			uf_limpiarvariables(); 
		}
	}
?>
<p>&nbsp;</p>
<div align="center">
  <table width="718" height="223" border="0" cellpadding="0" cellspacing="0" class="formato-blanco">
      <tr>
        <td width="716" height="221" valign="top">
<form name="formulario" method="post" action="" id="sigesp_cfg_d_consolidacion.php">
<?php
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$io_fun_cfg->uf_print_permisos($ls_permisos,$la_permisos,$ls_logusr,"location.href='sigespwindow_blank.php'");
	unset($io_fun_cfg);
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>          <p>&nbsp;</p>
          <table width="680" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
              <tr class="titulo-ventana">
                <td height="22" colspan="2">Informaci&oacute;n de Sucursales <span class="titulo-celda">
                <input name="hidestcla"  type="hidden" id="hidestcla"  value="<?php echo $ls_estcla; ?>" />
                </span></td>
              </tr>
              <tr class="formato-blanco">
                <td width="142" height="22">&nbsp;</td>
                <td width="536" height="22"><input name="status" type="hidden" id="status" value="<?php print $ls_estatus ?>"></td>
              </tr>
                <tr>
                  <td height="22" title="<?php print $dat["nomestpro1"]; ?>" style="text-align:right">C&oacute;digo</td>
                  <td height="22" colspan="2"><input name="txtcodsuc" type="text" id="txtcodsuc" style="text-align:center" onKeyPress="return keyRestrict(event,'1234567890');" value="<?php print $ls_bd;?>" size="12" maxlength="10"></td>
                </tr>
                <tr>
                  <td height="22" title="<?php print $dat["nomestpro1"]; ?>" style="text-align:right">Nombre de Sucursal </td>
                  <td height="22" colspan="2"><input name="txtnomsuc" type="text" id="txtnomsuc" size="70" maxlength="254"  onKeyPress="return keyRestrict(event,'1234567890'+'abcdefghijklmnñopqrstuvwxyz '+'_');"></td>
                </tr>
                <tr>
    <td height="22" title="<?php print $dat["nomestpro1"]; ?>"><div align="right"><?php print $dat["nomestpro1"];  ?></div></td>
    <td height="22" colspan="2">
      <div align="left">
        <input name="txtcodestpro1" type="text" id="txtcodestpro1" style="text-align:center" value="<?php print $ls_codestpro1;?>" size="<?php print $ls_loncodestpro1+2 ?>" maxlength="<?php print $ls_loncodestpro1 ?>" readonly>
        <a href="javascript:catalogo_estpro1();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0" alt="Catálogo de Estructura Programatica 1"></a>        
        <input name="txtdenestpro1" type="text" class="sin-borde" id="txtdenestpro1" value="<?php print $ls_denestpro1;?>" size="65" readonly>
      </div>     </td>
  </tr>
                <tr>
                  <td height="22" title="<?php print $dat["nomestpro2"]; ?>" style="text-align:right"><?php print $dat["nomestpro2"];  ?></td>
                  <td height="22" colspan="2"><input name="txtcodestpro2" type="text" id="txtcodestpro2" style="text-align:center" value="<?php print $ls_codestpro2;?>" size="<?php print $li_loncodestpro2+2 ?>" maxlength="<?php print $li_loncodestpro2 ?>" readonly>
                  <a href="javascript:catalogo_estpro2();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0" alt="Cat&aacute;logo de Estructura Programatica 2"></a>
                  <input name="txtdenestpro2" type="text" class="sin-borde" id="txtdenestpro2" value="<?php print $ls_denestpro2;?>" size="65" readonly>                  </td>
                </tr>
                <tr>
                  <td height="22" title="<?php print $dat["nomestpro3"]; ?>" style="text-align:right"><?php print $dat["nomestpro3"];  ?></td>
                  <td height="22" colspan="2"><input name="txtcodestpro3" type="text" id="txtcodestpro3" style="text-align:center" value="<?php print $ls_codestpro3;?>" size="<?php print $li_loncodestpro3+2 ?>" maxlength="<?php print $li_loncodestpro3 ?>" readonly>
                  <a href="javascript:catalogo_estpro3();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0" alt="Cat&aacute;logo de Estructura Programatica 3"></a>
                  <input name="txtdenestpro3" type="text" class="sin-borde" id="txtdenestpro3" value="<?php print $ls_denestpro3; ?>" size="65" readonly>                  </td>
                </tr>
                <?php
				  if ($_SESSION["la_empresa"]["estmodest"]==2)
				     {
				?>
				<tr>
                  <td height="22" title="<?php print $dat["nomestpro4"]; ?>" style="text-align:right"><?php print $dat["nomestpro4"];  ?></td>
                  <td height="22" colspan="2"><input name="txtcodestpro4" type="text" id="txtcodestpro4" style="text-align:center" value="<?php print $ls_codestpro4;?>" size="<?php print $li_loncodestpro4+2 ?>" maxlength="<?php print $li_loncodestpro4 ?>" readonly>
                  <a href="javascript:catalogo_estpro4();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0" alt="Cat&aacute;logo de Estructura Programatica 4"></a>
                  <input name="txtdenestpro4" type="text" class="sin-borde" id="txtdenestpro4" value="<?php print $ls_denestpro4; ?>" size="65" readonly>                  </td>
                </tr>
                <tr>
                  <td height="22" title="<?php print $dat["nomestpro5"]; ?>" style="text-align:right"><?php print $dat["nomestpro5"];  ?></td>
                  <td height="22" colspan="2"><input name="txtcodestpro5" type="text" id="txtcodestpro5" style="text-align:center" value="<?php print $ls_codestpro5;?>" size="<?php print $li_loncodestpro5+2 ?>" maxlength="<?php print $li_loncodestpro5 ?>" readonly>
                  <a href="javascript:catalogo_estpro5();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0" alt="Cat&aacute;logo de Estructura Programatica 5"></a>
                  <input name="txtdenestpro5" type="text" class="sin-borde" id="txtdenestpro5" value="<?php print $ls_denestpro5; ?>" size="65" readonly>                  </td>
                </tr>
				<?php
				     }
					 else
					 {
				?>
              <input name="txtcodestpro4"  type="hidden" id="txtcodestpro4" >
              <input name="txtdenestpro4"  type="hidden" id="txtdenestpro4" >
              <input name="txtcodestpro5"  type="hidden" id="txtcodestpro5" >
              <input name="txtdenestpro5"  type="hidden" id="txtdenestpro5" >
					 
				<?php
					 }
				?>
       <tr class="formato-blanco">
              <td height="22" colspan="2">&nbsp;&nbsp;
              <div align="left"><a href="javascript: uf_agregar_detalle();"></a></div>              </td>
            </tr>
            <tr class="formato-blanco">
              <td height="22" colspan="2"><p align="center"></td>
            </tr>
          </table>
          <p align="center">&nbsp;          </p>
          <p align="center">
              <input name="operacion"  type="hidden" id="operacion" >
          </p>
		</form></td>
      </tr>
  </table>
</div>
</body>
<script language="javascript">
f = document.formulario;
function ue_nuevo()
{
	li_incluir=f.incluir.value;
	if (li_incluir==1)
	{	
		f.operacion.value ="NUEVO";
		f.action="sigesp_cfg_d_sucursales.php";
		f.submit();
	}
	else
	{
		alert("No tiene permiso para realizar esta operación");
	}
}

function ue_guardar()
{
	li_incluir    = f.incluir.value;
	li_cambiar    = f.cambiar.value;
	lb_status     = f.status.value;
	ls_codestpro3 = f.txtcodestpro3.value;
	ls_codsuc    = f.txtcodsuc.value;
	ls_nomsuc    = f.txtnomsuc.value;
	if (((lb_status=="C")&&(li_cambiar==1))||(lb_status!="C")&&(li_incluir==1))
	{
		if ((ls_codsuc!="")&&(ls_codestpro3!="")&&(ls_nomsuc!=""))
		{
			f.operacion.value ="GUARDAR";
			f.action="sigesp_cfg_d_sucursales.php";
			f.submit();			   
		}
		else
		{
			alert("Todos los campos deben estar llenos.");
		}
	}
	else
	{
		alert("No tiene permiso para realizar esta operación");
	}	
}					

function ue_eliminar()
{
li_eliminar=f.eliminar.value;
if (li_eliminar==1)
   {	
	  if (confirm("¿ Esta seguro de Eliminar el registro?"))
	     {
		   f.operacion.value ="DELETE";
		   f.action="sigesp_cfg_d_sucursales.php";
		   f.submit();
		 
	     }
	}
  else
    {
      alert("No tiene permiso para realizar esta operación");
	}
}

function ue_cerrar()
{
	f.action="sigespwindow_blank.php";
	f.submit();
}

function ue_buscar()
{
  pagina    = "sigesp_cfg_cat_sucursales.php";
  window.open(pagina,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=620,height=400,resizable=yes,location=no");
}

function catalogo_estpro1()
{
  ls_opener = f.id;
  f.operacion.value="CASTEST";
  pagina    = "spg/sigesp_spg_cat_estpro1.php?opener="+ls_opener;
  window.open(pagina,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=620,height=400,resizable=yes,location=no");
}

function catalogo_estpro2()
{
  ls_opener     = f.id;
  ls_codestpro1 = f.txtcodestpro1.value;
  ls_denestpro1 = f.txtdenestpro1.value;
  ls_estcla     = f.hidestcla.value;
  if ((ls_codestpro1!="")&&(ls_denestpro1!=""))
	 {
	   pagina="spg/sigesp_spg_cat_estpro2.php?txtcodestpro1="+ls_codestpro1+"&txtdenestpro1="+ls_denestpro1+"&txtclasificacion="+ls_estcla+"&opener="+ls_opener;
	   window.open(pagina,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=620,height=400,resizable=yes,location=no");
	 }
  else
	 {
	   alert("Debe seleccionar una estructura del Nivel 1 !!!");
	 }
}

function catalogo_estpro3()
{
  ls_codestpro1 = f.txtcodestpro1.value;
  ls_codestpro2 = f.txtcodestpro2.value;
  li_estmodest  = "<?php print $_SESSION["la_empresa"]["estmodest"]; ?>";
  if ((ls_codestpro1!='' && ls_codestpro2=='') || (ls_codestpro1=='' && ls_codestpro2=='' && li_estmodest=='2'))
     {
	   alert("Debe seleccionar una estructura del Nivel 2 !!!");
	 }
  else
     {
	   ls_estcla     = f.hidestcla.value;
	   ls_opener     = f.id;
	   ls_denestpro1 = f.txtdenestpro1.value;
	   ls_denestpro2 = f.txtdenestpro2.value;
	   ls_codestpro3 = f.txtcodestpro3.value;   
	   pagina = "spg/sigesp_spg_cat_estpro3.php?txtcodestpro1="+ls_codestpro1+"&txtdenestpro1="+ls_denestpro1+"&txtcodestpro2="+ls_codestpro2+"&txtdenestpro2="+ls_denestpro2+"&txtclasificacion="+ls_estcla+"&opener="+ls_opener;
	   window.open(pagina,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=780,height=450,resizable=yes,location=no");
	 }
}

function catalogo_estpro4()
{
  ls_codestpro1 = f.txtcodestpro1.value;
  ls_codestpro2 = f.txtcodestpro2.value;
  ls_codestpro3 = f.txtcodestpro3.value;

  if (ls_codestpro1=='' || ls_codestpro2=='' || ls_codestpro3=='')
     {
	   alert("Debe seleccionar una estructura del Nivel 3 !!!");
	 }
  else
     {
	   ls_estcla     = f.hidestcla.value;
	   ls_opener     = f.id;	   
	   ls_denestpro1 = f.txtdenestpro1.value;
	   ls_denestpro2 = f.txtdenestpro2.value;
	   ls_denestpro3 = f.txtdenestpro3.value;
	   
	   pagina = "spg/sigesp_spg_cat_estpro4.php?txtcodestpro1="+ls_codestpro1+"&txtdenestpro1="+ls_denestpro1
	                                     +"&txtcodestpro2="+ls_codestpro2+"&txtdenestpro2="+ls_denestpro2
										 +"&txtcodestpro3="+ls_codestpro3+"&txtdenestpro3="+ls_denestpro3
										 +"&txtclasificacion="+ls_estcla+"&opener="+ls_opener;
	   window.open(pagina,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=780,height=450,resizable=yes,location=no");
	 }
}

function catalogo_estpro5()
{
  ls_codestpro1 = f.txtcodestpro1.value;
  ls_codestpro2 = f.txtcodestpro2.value;
  ls_codestpro3 = f.txtcodestpro3.value;
  ls_codestpro4 = f.txtcodestpro4.value;
  if ((ls_codestpro1!='' && ls_codestpro2!='' && ls_codestpro3!='' && ls_codestpro4=='') ||
     (ls_codestpro1!='' && ls_codestpro2!='' && ls_codestpro3=='' && ls_codestpro4=='') ||
	 (ls_codestpro1!='' && ls_codestpro2=='' && ls_codestpro3=='' && ls_codestpro4==''))
     {
	   alert("Debe seleccionar una estructura del Nivel 4 !!!");
	 }
  else
     {
	   ls_estcla     = f.hidestcla.value;
	   ls_opener     = f.id;
	   
	   ls_denestpro1 = f.txtdenestpro1.value;
	   ls_denestpro2 = f.txtdenestpro2.value;
	   ls_denestpro3 = f.txtdenestpro3.value;
	   ls_denestpro4 = f.txtdenestpro4.value;
	   
	   pagina = "spg/sigesp_spg_cat_estpro5.php?txtcodestpro1="+ls_codestpro1+"&txtdenestpro1="+ls_denestpro1
	                                     +"&txtcodestpro2="+ls_codestpro2+"&txtdenestpro2="+ls_denestpro2
										 +"&txtcodestpro3="+ls_codestpro3+"&txtdenestpro3="+ls_denestpro3
										 +"&txtcodestpro4="+ls_codestpro4+"&txtdenestpro4="+ls_denestpro4
										 +"&txtclasificacion="+ls_estcla+"&opener="+ls_opener;
	   window.open(pagina,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=780,height=450,resizable=yes,location=no");
	 }
}

function uf_delete_all()
{
	f=document.formulario;
	if(confirm("Está Seguro de Eliminar toda la información de consolidación ?"))
	{
		ls_codestpro1 = f.txtcodestpro1.value;
	    ls_bd = f.txtbd.value; 
		
		f.operacion.value="DELETEALL";
		f.action="sigesp_cfg_d_sucursales.php";
		f.submit();
		
	}
}
</script>
</html>