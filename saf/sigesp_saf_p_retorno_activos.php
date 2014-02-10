<?php
session_start();
if (!array_key_exists("la_logusr",$_SESSION))
   {
	 print "<script language=JavaScript>";
	 print "location.href='../sigesp_inicio_sesion.php'";
	 print "</script>";		
   }
$ls_logusr = $_SESSION["la_logusr"];
require_once("class_funciones_activos.php");
$io_fun_activo = new class_funciones_activos();
$io_fun_activo->uf_load_seguridad("SAF","sigesp_saf_p_retorno_activos.php",$ls_permisos,$la_seguridad,$la_permisos);
$ls_codemp     = $_SESSION["la_empresa"]["codemp"];
require_once("sigesp_saf_c_activo.php");
$io_saf_tipcat = new sigesp_saf_c_activo();
$ls_rbtipocat  = $io_saf_tipcat->uf_select_valor_config($ls_codemp);
$ls_reporte = $io_fun_activo->uf_select_config("SAF","REPORTE","RETORNO_ACTIVOS","sigesp_saf_rfs_retorno_activos.php","C");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Retorno de Activos</title>
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css" />
<link href="../shared/css/general.css"  rel="stylesheet" type="text/css" />
<link href="../shared/css/tablas.css"   rel="stylesheet" type="text/css" />
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" language="JavaScript1.2" src="js/stm31.js"></script>
<script type="text/javascript" language="JavaScript1.2" src="js/funciones.js"></script>
<script type="text/javascript" language="JavaScript1.2" src="../shared/js/valida_tecla.js"></script>
<script type="text/javascript" language="JavaScript1.2" src="../shared/js/disabled_keys.js"></script>
<link href="../shared/js/css_intra/datepickercontrol.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
a:link {
	color: #006699;
}
a:visited {
	color: #006699;
}
a:hover {
	color: #006699;
}
a:active {
	color: #006699;
}
-->
</style>
</head>
<body>
<div align="center">
  <table width="762" border="0" align="center" cellpadding="0" cellspacing="0" class="contorno">
    <tr>
      <td height="30" colspan="11" class="cd-logo"><img src="../shared/imagebank/header.jpg" alt="Encabezado" width="778" height="40" /></td>
    </tr>
    <tr>
      <td width="432" height="20" colspan="11" bgcolor="#E7E7E7"><table width="762" border="0" align="center" cellpadding="0" cellspacing="0">
          <tr>
            <td width="432" height="20" bgcolor="#E7E7E7" class="descripcion_sistema">Sistema de Activos Fijos</td>
            <td width="346" bgcolor="#E7E7E7"><div align="right"><span class="letras-pequenas"><b><?php print date("j/n/Y")." - ".date("h:i a");?></b></span></div></td>
          </tr>
        <tr>
            <td height="20" bgcolor="#E7E7E7" class="descripcion_sistema">&nbsp;</td>
          <td bgcolor="#E7E7E7" class="letras-pequenas"><div align="right"><b><?php print $_SESSION["la_nomusu"]." ".$_SESSION["la_apeusu"];?></b></div></td>
        </tr>
      </table></td>
    </tr>
    <tr>
      <?php 
    if ($ls_rbtipocat == 1) 
    {
   ?>
      <td height="20" colspan="11" bgcolor="#E7E7E7" class="cd-menu"><script type="text/javascript" language="JavaScript1.2" src="js/menu_csc.js"></script></td>
      <?php 
    }
	elseif ($ls_rbtipocat == 2)
	{
   ?>
      <td height="20" colspan="11" bgcolor="#E7E7E7" class="cd-menu"><script type="text/javascript" language="JavaScript1.2" src="js/menu_cgr.js"></script></td>
      <?php 
	}
	else
	{
   ?>
      <td height="20" colspan="11" bgcolor="#E7E7E7" class="cd-menu"><script type="text/javascript" language="JavaScript1.2" src="js/menu.js"></script></td>
      <?php 
	}
   ?>
    </tr>
    <tr>
      <td height="13" colspan="11" bgcolor="#E7E7E7" class="toolbar">&nbsp;</td>
    </tr>
    <tr>
      <td height="20" width="20" class="toolbar"><a href="javascript: uf_procesar_retorno();"><img src="../shared/imagebank/tools20/ejecutar.gif" alt="Grabar" width="20" height="20" border="0" /></a></td>
      <td class="toolbar" width="22"><a href="javascript: ue_cerrar();"><img src="../shared/imagebank/tools20/salir.gif" alt="Salir" width="20" height="20" border="0" /></a><a href="javascript: ue_buscar();"></a></td>
      <td class="toolbar" width="22"><a href="javascript: ue_imprimir('<?php print $ls_reporte ?>');"><img src="../shared/imagebank/tools20/ayuda.gif" alt="Ayuda" width="20" height="20" border="0" /></a></td>
      <td class="toolbar" width="24">&nbsp;</td>
      <td class="toolbar" width="24">&nbsp;</td>
      <td class="toolbar" width="24">&nbsp;</td>
      <td class="toolbar" width="24">&nbsp;</td>
      <td class="toolbar" width="618">&nbsp;</td>
    </tr>
  </table>
<?php
require_once("sigesp_saf_c_movimiento.php");
require_once("../shared/class_folder/class_sql.php");
require_once("../shared/class_folder/grid_param.php");
require_once("../shared/class_folder/sigesp_include.php");
require_once("../shared/class_folder/class_mensajes.php");

$io_objsaf  = new sigesp_saf_c_movimiento();
$io_include = new sigesp_include();
$ls_conect  = $io_include->uf_conectar();
$io_sql     = new class_sql($ls_conect);
$io_msg     = new class_mensajes();
$io_grid	= new grid_param();

if (array_key_exists("operacion",$_POST))
   {
     $ls_operacion = $_POST["operacion"];
   }
else
   {
     $ls_operacion = "";
	 uf_limpiarvariables();
	 uf_agregarlineablanca($lo_object,$li_totrows);
	 $ls_readonly="readonly";
   }
if (array_key_exists("cmbtipcmp",$_POST))
   {
     $ls_tipcmp = $_POST["cmbtipcmp"];
	 if ($ls_tipcmp=='P')
	    {
		  $ls_selpre = "selected";
		  $ls_selaut = "";
		  $ls_seldef = "";
		}
     elseif($ls_tipcmp=='S')
	    {
		  $ls_selaut = "selected";
		  $ls_selpre = "";
		  $ls_seldef = "";
		}
   }
else
   {
     $ls_tipcmp = "-";  
	 $ls_seldef = "selected"; 
   }
	function uf_obtenervalor($as_valor, $as_valordefecto)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	Function:  uf_obtenervalor
		//	Access:    public
		//	Arguments:
		// 				as_valor         //  nombre de la variable que desamos obtener
		// 				as_valordefecto  //  contenido de la variable
		// Description: Función que obtiene el valor de una variable si viene de un submit
		//////////////////////////////////////////////////////////////////////////////
		if(array_key_exists($as_valor,$_POST))
		{
			$valor=$_POST[$as_valor];
		}
		else
		{
			$valor=$as_valordefecto;
		}
		return $valor; 
	}

	function uf_limpiarvariables()
	{
		//////////////////////////////////////////////////////////////////////////////
		//	Function:  uf_limpiarvariables
		//	Description: Función que limpia todas las variables necesarias en la página
		//////////////////////////////////////////////////////////////////////////////
		global $ls_numcmp,$ls_tipcmp,$ls_titletable,$li_widthtable,$ls_nametable,$lo_title,$li_totrows,$ls_codbenpre,$ls_nombenpre,$ls_obscmp,$ls_feccmp;
		
		$ls_numcmp = "";
		$ls_tipcmp = "-";
		$ls_codbenpre = "";
		$ls_nombenpre = "";
		$ls_obscmp = "";
		$ls_feccmp = "";
		
		$ls_titletable = "Detalle de Activos";
		$li_widthtable = 200;
		$ls_nametable  = "grid";
		$lo_title[1] = "";
		$lo_title[2] = "Código";
		$lo_title[3] = "Descripción";
		$lo_title[4] = "Identificador";
		$li_totrows  = 1;
	}

	function uf_agregarlineablanca(&$aa_object,$ai_totrows)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_agregarlineablanca
		//         Access: private
		//      Argumento: $aa_object // arreglo de titulos 
		//				   $ai_totrows // ultima fila pintada en el grid
		//	      Returns: 
		//    Description: Funcion que agrega una linea en blanco al final del grid
		//	   Creado Por: Ing. Néstor Falcón.
		// Fecha Creación: 25/06/2009. 								Fecha Última Modificación : 25/06/2009.
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		$aa_object[$ai_totrows][1] = "<input name=chk".$ai_totrows."       type=checkbox id=chk".$ai_totrows."        value=1   class=sin-borde disabled>";
		$aa_object[$ai_totrows][2] = "<input name=txtcodact".$ai_totrows." type=text     id=txtcodact".$ai_totrows."  value=''  class=sin-borde size=22 style=text-align:center readonly maxlength=15>";
		$aa_object[$ai_totrows][3] = "<input name=txtdenact".$ai_totrows." type=text     id=txtdenact".$ai_totrows."  value=''  class=sin-borde size=55 style=text-align:left   readonly>";
		$aa_object[$ai_totrows][4] = "<input name=txtideact".$ai_totrows." type=text     id=txtideact".$ai_totrows."  value=''  class=sin-borde size=22 style=text-align:center readonly maxlength=15>";
	}

    function uf_load_variables()
    {
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_variables
		//		   Access: private
		//	  Description: Función que carga todas las variables necesarias en la página
		//	   Creado Por: Ing. Néstor Falcón.
		// Fecha Creación: 25/06/2009			Fecha Última Modificación : 25/06/2009
		//////////////////////////////////////////////////////////////////////////////
	
		global $ls_numcmp,$ls_tipcmp,$ls_codbenpre,$ls_nombenpre,$ls_titletable,$li_widthtable,$ls_nametable,$lo_title,$ls_obscmp,$ls_feccmp;
		
		$ls_numcmp    = $_POST["hidnumcmp"];
		$ls_tipcmp    = $_POST["cmbtipcmp"];
		$ls_obscmp    = $_POST["hidobscmp"];
		$ls_feccmp    = $_POST["hidfeccmp"];
		$ls_codbenpre = $_POST["txtcodbenpre"];
		$ls_nombenpre = $_POST["txtnombenpre"];
		
		$ls_titletable = "Detalle de Activos";
	    $li_widthtable = 150;
	    $ls_nametable  = "grid";
	    $lo_title[1] = "";
	    $lo_title[2] = "Código";
	    $lo_title[3] = "Descripción";
	    $lo_title[4] = "Identificador";
    }

switch ($ls_operacion){
  case "":
    uf_limpiarvariables();
	uf_agregarlineablanca($lo_object,1);
  break;
  case "LOAD_ACTIVOS":
    uf_load_variables();
	$io_objsaf->uf_load_activos_pendientes($ls_numcmp,$ls_tipcmp,$lo_object,$li_totrows);//Carga de activos en Calidad de Préstamo.
  break;
  case "PROCESAR":
    uf_load_variables();
    $li_totrow = uf_obtenervalor("totalfilas",1);
    if ($li_totrow>0)
	   {
	     $lb_valido = true;
		 $ls_numcmp = $_POST["hidnumcmp"];
		 $ls_feccmp = $_POST["hidfeccmp"];
		 $io_objsaf->io_sql->begin_transaction();
		 $li_row = 0;
		 $la_detret = array();
		 for ($li_i=1;$li_i<=$li_totrow;$li_i++ && $lb_valido)
		     {
			   if (array_key_exists("chk".$li_i,$_POST))
				  {
				    $li_row++;
					$ls_codact = $_POST["txtcodact".$li_i];
				    $ls_ideact = $_POST["txtideact".$li_i];
					$ls_denact = $_POST["txtdenact".$li_i];
					if ($li_row==1 && !empty($ls_codact))
					   {
					     $ls_actcod = $ls_codact;
						 $ls_actide = $ls_ideact;
					   }
					else
					   {
					     $ls_actcod = $ls_actcod.'-'.$ls_codact;
						 $ls_actide = $ls_actide.'-'.$ls_ideact; 
					   }				    
				    $lb_valido = $io_objsaf->uf_retornar_activo($ls_numcmp,$ls_tipcmp,$ls_codact,$ls_ideact,$la_seguridad);//Coloca el activo disponible para un nuevo prestamo.
				    if (!$lb_valido)
					   {
						 $io_msg->message("Error en el Proceso de Retorno de Activos !!!");
						 break;
					   }
				  }
			 }	 
	     if ($lb_valido)
		    {
			  $io_objsaf->io_sql->commit();
			  $io_msg->message("Retorno(s) de Activo(s) realizado con Éxito !!!");
			  ?>
			  <script language="javascript">
			  ls_pagina="reportes/<?php print $ls_reporte ?>?actcod=<?php print $ls_actcod ?>&actide=<?php print $ls_actide ?>&tipcmp=<?php print $ls_tipcmp ?>&numcmp=<?php print $ls_numcmp ?>&feccmp=<?php print $ls_feccmp; ?>&codbenpre=<?php print $ls_codbenpre ?>+&nombenpre=<?php print $ls_nombenpre ?>";
			  window.open(ls_pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=583,height=400,left=50,top=50,location=no,resizable=yes");
			  </script>
			  <?php
			}
	   }
    uf_limpiarvariables();
	uf_agregarlineablanca($lo_object,1);
  break; 
}
?>
<form id="sigesp_saf_p_retorno_activos.php" name="form1" method="post" action="">
<?php
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$io_fun_activo->uf_print_permisos($ls_permisos,$la_permisos,$ls_logusr,"location.href='sigespwindow_blank.php'");
	unset($io_fun_activo);
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>
    <p>&nbsp; </p>
    <table width="665" height="216" border="0" cellpadding="0" cellspacing="0" class="formato-blanco">
      <tr>
        <td width="665" height="172"><div align="center">
          <div align="center">
            <table width="533" height="110" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
              <tr class="titulo-ventana">
                <td height="21" colspan="4">Detalle de Activos
                  <input name="operacion" type="hidden" id="operacion" />
                  <input name="hidnumcmp" type="hidden" id="hidnumcmp" value="<?php print $ls_numcmp; ?>" />
                  <input name="hidobscmp" type="hidden" id="hidobscmp" value="<?php print $ls_obscmp; ?>" />
                  <input name="hidfeccmp" type="hidden" id="hidfeccmp" value="<?php print $ls_feccmp; ?>" /></td>
              </tr>
              <tr>
                <td width="111" height="13">&nbsp;</td>
                <td width="201">&nbsp;</td>
                <td width="55">&nbsp;</td>
                <td width="164">&nbsp;</td>
              </tr>
              <tr>
                <td height="21" style="text-align:right">Tipo Comprobante </td>
                <td height="21"><label>
                  <select name="cmbtipcmp" id="cmbtipcmp">
                    <option value="-" <?php print $ls_seldef; ?>>---seleccione---</option>
                    <option value="P" <?php print $ls_selpre; ?>>Acta de Pr&eacute;stamo</option>
                    <option value="S" <?php print $ls_selaut; ?>>Autorizaci&oacute;n de Salida</option>
                  </select>
                </label></td>
                <td height="21">&nbsp;</td>
                <td height="21">&nbsp;</td>
              </tr>
              <tr>
                <td height="21" style="text-align:right">Beneficiario Pr&eacute;stamo </td>
                <td height="21" colspan="3"><label>
                  <input name="txtcodbenpre" type="text" id="txtcodbenpre" value="<?php print $ls_codbenpre; ?>" size="13" maxlength="10" style="text-align:center" readonly />
                  <a href="javascript:uf_load_benpres();"><img src="../shared/imagebank/tools15/buscar.gif" alt="Buscar" width="15" height="15" border="0" /></a>
                  <input name="txtnombenpre" type="text" class="sin-borde" id="txtnombenpre" value="<?php print $ls_nombenpre; ?>" size="55" readonly />
                </label></td>
              </tr>
              <tr>
                <td height="21" colspan="4" style="text-align:right"><a href="javascript:uf_load_comprobantes();"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="20" height="20" border="0" />Buscar Comprobantes</a></td>
              </tr>
              <tr>
                <td height="13">&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
            </table>
          </div>
        </div></td>
      </tr>
      <tr>
        <td><div align="center">
          <?php
		    $io_grid->makegrid($li_totrows,$lo_title,$lo_object,$li_widthtable,$ls_titletable,$ls_nametable);
		  ?>
          <input name="totalfilas" type="hidden" id="totalfilas" value="<?php print $li_totrows;?>" />
        </div></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
      </tr>
    </table>
  </form>
</div>
</body>
<script language="javascript">
f = document.form1;

function uf_load_comprobantes()
{
  ls_tipcmp    = f.cmbtipcmp.value;
  ls_codbenpre = f.txtcodbenpre.value;
  if (ls_tipcmp!='-' && ls_codbenpre!='')
     {
       if (ls_tipcmp=='P')
	      {
		    window.open("sigesp_saf_cat_prestamo.php?tipo=retorno&codbenpre="+ls_codbenpre+"","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=650,height=500 resizable=yes,location=no,left=50,top=50,dependent=yes");
		  } 
	   else if (ls_tipcmp=='S')
	      {
		    window.open("sigesp_saf_cat_autorizacionsalida.php?tipo=retorno&codbenpre="+ls_codbenpre+"","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=650,height=500 resizable=yes,location=no,left=50,top=50,dependent=yes");
		  }
	 }
  else
     {
	   alert("Debe completar los datos de búsqueda !!!");
	 }
}

function uf_load_benpres()
{
  ls_tipcmp = f.cmbtipcmp.value;
  if (ls_tipcmp!='-')
     {
	   if (ls_tipcmp=='P')
	      {
		    window.open("sigesp_snorh_cat_personal.php?tipo=retornoactivos&buscaresced=buscaresced&ls_codresced=&buscar=0","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
		  }
	   else if (ls_tipcmp=='S')
	      {
		    window.open("sigesp_saf_cat_prov.php?&destino=proveedor","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=650,height=500 resizable=yes,location=no,left=50,top=50,dependent=yes");
		  }
	 }
  else
     {
	   alert("Debe seleccionar Tipo de Comprobante !!!");
	 }
}

function uf_procesar_retorno()
{
  li_ejecutar = f.ejecutar.value
  if (li_ejecutar==1)
     {
	   li_totrows = f.totalfilas.value;
	   ls_codact  = f.txtcodact1.value;
	   if (li_totrows>0 && ls_codact!="")
		  {
		    lb_valido = false;
			for (li_i=1;li_i<=li_totrows;li_i++)
			    {
			      if (eval("f.chk"+li_i+".checked;"))
				     {
					   lb_valido = true;
					 }
			    }
		    if (lb_valido)
			   {
				 f.action = "sigesp_saf_p_retorno_activos.php";
				 f.operacion.value = "PROCESAR";
				 f.submit();
			   }
		    else
			   {
				 alert("Debe seleccionar al menos un Activo !!!");
			   }
		  }
	   else
		  {
		    alert("No Existen Activos para ser Retornados !!!");
		  } 
	 }
  else
     {
	   alert("No tiene permiso para realizar esta operación !!!");	 
	 }
}
</script>
<script language="javascript" src="../shared/js/js_intra/datepickercontrol.js"></script>
</html>