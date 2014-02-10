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
$io_fun_activo->uf_load_seguridad("SAF","sigesp_saf_p_reverso_incorporacion.php",$ls_permisos,$la_seguridad,$la_permisos);
$ls_codemp     = $_SESSION["la_empresa"]["codemp"];
require_once("sigesp_saf_c_activo.php");
$io_saf_tipcat = new sigesp_saf_c_activo();
$ls_rbtipocat  = $io_saf_tipcat->uf_select_valor_config($ls_codemp);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Reverso de Incorporaci&oacute;n</title>
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
</style></head>
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
      <td height="20" width="20" class="toolbar"><a href="javascript: uf_procesar_reverso();"><img src="../shared/imagebank/tools20/ejecutar.gif" alt="Procesar Reverso" title="Procesar Reverso" width="20" height="20" border="0" /></a></td>
      <td class="toolbar" width="22"><a href="javascript: ue_cerrar();"><img src="../shared/imagebank/tools20/salir.gif" alt="Salir" title="Salir" width="20" height="20" border="0" /></a></td>
      <td class="toolbar" width="22"><img src="../shared/imagebank/tools20/ayuda.gif" alt="Ayuda" width="20" height="20" title="Ayuda" /></td>
      <td class="toolbar" width="618">&nbsp;</td>
    </tr>
  </table>
  <p>&nbsp;</p>
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
		global $ls_numcmp,$ls_fecdes,$ls_fechas,$ls_titletable,$li_widthtable,$ls_nametable,$lo_title,$li_totrows;
		
		$ls_numcmp = "";
		$ls_fecdes = date("01/m/Y");
		$ls_fechas = date("d/m/Y");
		$ls_titletable = "Comprobantes";
		$li_widthtable = 150;
		$ls_nametable  = "grid";
		$lo_title[1] = "";
		$lo_title[2] = "Número";
		$lo_title[3] = "Descripción";
		$lo_title[4] = "Fecha";
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
		
		$aa_object[$ai_totrows][1] = "<input name=chk".$ai_totrows."       type=checkbox id=chk".$ai_totrows."        value=1   class=sin-borde>";
		$aa_object[$ai_totrows][2] = "<input name=txtnumcmp".$ai_totrows." type=text     id=txtnumcmp".$ai_totrows."  value=''  class=sin-borde size=15 style=text-align:center readonly maxlength=15><input name=hidcmpmov".$ai_totrows." type=hidden id=hidcmpmov".$ai_totrows." value=''>";
		$aa_object[$ai_totrows][3] = "<input name=txtdescmp".$ai_totrows." type=text     id=txtdescmp".$ai_totrows."  value=''  class=sin-borde size=54 style=text-align:left   readonly><input name=hidcodcau".$ai_totrows." type=hidden id=hidcodcau".$ai_totrows." value=''>";
		$aa_object[$ai_totrows][4] = "<input name=txtfeccmp".$ai_totrows." type=text     id=txtfeccmp".$ai_totrows."  value=''  class=sin-borde size=8  style=text-align:center readonly maxlength=10><input name=hidestcat".$ai_totrows." type=hidden id=hidestcat".$ai_totrows." value=''>";
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
	
		global $ls_numcmp,$ls_fecdes,$ls_fechas,$ls_titletable,$li_widthtable,$ls_nametable,$lo_title;
		
		$ls_numcmp = $_POST["txtnumcmp"];
		$ls_fecdes = $_POST["txtdesde"];
		$ls_fechas = $_POST["txthasta"];
		
		$ls_titletable = "Comprobantes";
	    $li_widthtable = 150;
	    $ls_nametable  = "grid";
	    $lo_title[1] = "";
	    $lo_title[2] = "Número";
	    $lo_title[3] = "Descripción";
	    $lo_title[4] = "Fecha";
    }

switch ($ls_operacion){
  case "":
    uf_limpiarvariables();
	uf_agregarlineablanca($lo_object,1);
  break;
  case "CARGAR_COMPROBANTES":
    uf_load_variables();
	$io_objsaf->uf_load_comprobantes_reversar($ls_numcmp,$ls_fecdes,$ls_fechas,$lo_object,$li_totrows);
	$ls_numcmp = "";
  break;
  case "PROCESAR":
    $li_totrow = uf_obtenervalor("totalfilas",1);
    if ($li_totrow>0)
	   {
	     $lb_valido = true;
		 for ($li_i=1;$li_i<=$li_totrow;$li_i++ && $lb_valido)
		     {
			   if (array_key_exists("chk".$li_i,$_POST))
				  {
				    $ls_numcmp = $_POST["txtnumcmp".$li_i];
				    $ls_feccmp = $_POST["txtfeccmp".$li_i];
				    $ls_cmpmov = $_POST["hidcmpmov".$li_i];
				    $ls_codcau = $_POST["hidcodcau".$li_i];
				    $ls_estcat = $_POST["hidestcat".$li_i];
				    $lb_valido = $io_objsaf->uf_procesar_reverso($ls_numcmp,$ls_feccmp,$ls_cmpmov,$ls_codcau,$ls_estcat,$la_seguridad);
				    if (!$lb_valido)
					   {
						 $io_msg->message("Error en el Proceso de Reverso de la Incorporación !!!");
						 break;
					   }
				  }
			   uf_limpiarvariables();
			   uf_agregarlineablanca($lo_object,1);
			 }	 
	     if ($lb_valido)
		    {
			  $io_objsaf->io_sql->commit();
			  $io_msg->message("Reverso(s) de Incorporación realizado con Éxito !!!");
			}
	   }
  break; 
}
?>
<form id="sigesp_saf_p_reverso_incorporacion.php" name="form1" method="post" action="">
<?php
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$io_fun_activo->uf_print_permisos($ls_permisos,$la_permisos,$ls_logusr,"location.href='sigespwindow_blank.php'");
	unset($io_fun_banco);
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>
<table width="545" height="197" border="0" cellpadding="0" cellspacing="0" class="formato-blanco">
      <tr>
        <td width="543" height="177"><div align="center">
          <table width="391" height="140" border="0" cellpadding="0" cellspacing="0" class="formato-blanco">
            <tr class="titulo-ventana">
              <td height="21" colspan="5">Reverso de Incorporaci&oacute;n 
                <input name="operacion" type="hidden" id="operacion" /></td>
              </tr>
            <tr>
              <td width="80" height="13">&nbsp;</td>
              <td width="70">&nbsp;</td>
              <td width="57">&nbsp;</td>
              <td width="45">&nbsp;</td>
              <td width="52">&nbsp;</td>
            </tr>
            <tr>
              <td height="21" style="text-align:right">Comprobante</td>
              <td height="21" colspan="2"><label>
                <input name="txtnumcmp" type="text" id="txtnumcmp" value="<?php echo $ls_numcmp; ?>" size="20" maxlength="15" style="text-align:center" onKeyPress="return keyRestrict(event,'1234567890');" onBlur="javascript:rellenar_cadena(this.value,15,this.name);" />
              </label></td>
              <td height="21">&nbsp;</td>
              <td height="21">&nbsp;</td>
            </tr>
            <tr>
              <td colspan="5">&nbsp;</td>
            </tr>
            <tr>
              <td colspan="5"><div align="center">
                <table width="350" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
                  <tr>
                    <td height="22" colspan="5"><strong>Intervalo de Fecha de Compra</strong></td>
                  </tr>
                  <tr>
                    <td width="83"><div align="right">Desde</div></td>
                    <td width="87"><input name="txtdesde" type="text" id="txtdesde"  onkeypress="ue_separadores(this,'/',patron,true);" value="<?php echo $ls_fecdes; ?>" size="15" maxlength="10"  datepicker="true" style="text-align:center" /></td>
                    <td width="59"><div align="right">Hasta</div></td>
                    <td width="119"><div align="left">
                        <input name="txthasta" type="text" id="txthasta"  onkeypress="ue_separadores(this,'/',patron,true);" value="<?php echo $ls_fechas; ?>" size="15" maxlength="10"  datepicker="true" style="text-align:center" />
                    </div></td>
                  </tr>
                </table>
              </div></td>
              </tr>
            <tr>
              <td height="13" colspan="5">&nbsp;</td>
              </tr>
            <tr>
              <td height="21" colspan="5"><div align="right"><a href="javascript:uf_load_comprobantes();"><img src="../shared/imagebank/tools20/buscar.gif" width="20" height="20" border="0" />Buscar Comprobantes</a></div></td>
              </tr>
          </table>
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
    </table>
  </form>
  <p>&nbsp;</p>
</div>
</body>
<script language="javascript">
f = document.form1;
function uf_load_comprobantes()
{
  li_leer = f.leer.value;
  if (li_leer==1)
	 {	
	   f.action = "sigesp_saf_p_reverso_incorporacion.php";
	   f.operacion.value = "CARGAR_COMPROBANTES";
	   f.submit();  
     }
  else
     {
	   alert("No tiene permiso para realizar esta operación !!!");
	 }
}

function rellenar_cadena(cadena,longitud,objeto)
{
var mystring = new String(cadena);
cadena_ceros = "";
lencad       = mystring.length;
total        = longitud-lencad;
if (cadena!='')
   {
	 for (i=1;i<=total;i++)
		 {
		  cadena_ceros=cadena_ceros+"0";
		 }
	 cadena = cadena_ceros+cadena;   
	 if (objeto=='txtnumcmp')
	    {
		  document.form1.txtnumcmp.value=cadena;
		}
   }
}

function uf_procesar_reverso()
{
  li_ejecutar = f.ejecutar.value
  if (li_ejecutar==1)
     {
	   li_totrows = f.totalfilas.value;
	   ls_numcmp  = f.txtnumcmp1.value;
	   if (li_totrows>0 && ls_numcmp!="")
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
				 f.action = "sigesp_saf_p_reverso_incorporacion.php";
				 f.operacion.value = "PROCESAR";
				 f.submit();
			   }
		    else
			   {
				 alert("Debe seleccionar al menos un Comprobante de Incorporación !!!");
			   }
		  }
	   else
		  {
		    alert("No Existen Comprobantes para ser Reversados !!!");
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