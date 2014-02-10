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
	require_once("class_funciones_gasto.php");
	$io_fun_gasto=new class_funciones_gasto();
	$io_fun_gasto->uf_load_seguridad("SPG","sigesp_spg_p_consolidacion_empresas.php",$ls_permisos,$la_seguridad,$la_permisos);
	$ls_reporte=$io_fun_gasto->uf_select_config("SPG","REPORTE","COMPROBANTE_FORMATO1","sigesp_spg_rpp_comprobante_formato1.php","C");
  //////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////

	require_once("../sigesp_config.php");
	require_once("../shared/class_folder/class_sql.php");
	require_once("../shared/class_folder/sigesp_include.php");
	require_once("../shared/class_folder/class_funciones.php");
	require_once("../shared/class_folder/class_mensajes.php");
	require_once("class_folder/sigesp_spg_c_consolidacion_empresas.php");
	require_once("../shared/class_folder/sigesp_c_seguridad.php");
	$io_seguridad = new sigesp_c_seguridad;
	$msg=new class_mensajes();
	
	$arre=$_SESSION["la_empresa"];
	$ls_empresa=$arre["codemp"];
	$ls_sistema="SPG";
	$ls_ventana="sigesp_spg_p_consolidacion_empresas.php";
	$la_seguridad["empresa"]=$ls_empresa;
	$la_seguridad["logusr"]=$ls_logusr;
	$la_seguridad["sistema"]=$ls_sistema;
	$la_seguridad["ventanas"]=$ls_ventana;
	
	global $ls_operacion, $ld_fecha;	

	$ld_fecha="";	 
	$ls_procede="";
	$ls_operacion="";
	$ls_titletable="Comprobantes de Ejecución Presupuestaria Consolidados";
	$li_widthtable=550;
	$ls_nametable="grid";
	$lo_title[1]="";
	$lo_title[2]="Nro. de Comprobante";
	$lo_title[3]="Procedencia";
	$lo_title[4]="Fecha de Consolidacion";
	$lo_title[5]="Concepto";
	$lo_title[6]="Detalle";
	$li_totrows=$io_fun_gasto->uf_obtenervalor("totalfilas",1);

    function uf_cargar_dt($li_i)
    {
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_limpiarvariables
		//		   Access: private
		//	  Description: Función que limpia todas las variables necesarias en la página
		//	   Creado Por: Ing. Arnaldo Suárez
		// Fecha Creación: 17/09/2009 								Fecha Última Modificación : 
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
   		global $ls_concepto,$ls_comprobante,$ls_fecha,$ls_procede,$ls_selcmp,$ls_codban,$ls_ctaban;
		
		$ls_concepto    = $_POST["txtconcepto".$li_i];
		$ls_comprobante = $_POST["txtcomprobante".$li_i];
		$ls_procede		= $_POST["txtprocede".$li_i];
		$ls_fecha		= $_POST["txtfecha".$li_i];
		$ls_selcmp		= $_POST["txtselcmp".$li_i];	
		$ls_codban      = $_POST["hidcodban".$li_i];
		$ls_ctaban		= $_POST["hidctaban".$li_i];
   }
	
	function uf_agregarlineablanca(&$aa_object,$ai_totrows)
    {
		//////////////////////////////////////////////////////////////////////////////
		//	Function: uf_agregarlineablanca
		//	Arguments: aa_object  // arreglo de Objetos
		//			   ai_totrows  // total de Filas
		//	Description:  Función que agrega una linea mas en el grid
		//////////////////////////////////////////////////////////////////////////////
		$aa_object[$ai_totrows][1] = "<input type=checkbox name=selcmp".$ai_totrows." id=selcmp".$ai_totrows." onChange='javascript: cambiar_valor(".$ai_totrows.");'><input name=txtselcmp".$ai_totrows." type=hidden id=txtselcmp".$ai_totrows." readonly>";
		$aa_object[$ai_totrows][2] = "<input type=text name=txtcomprobante".$ai_totrows."   value=''      class=sin-borde readonly style=text-align:center size=17 maxlength=15 >";
		$aa_object[$ai_totrows][3] = "<input name=txtprocede".$ai_totrows." type=text id=txtprocede".$ai_totrows." class=sin-borde  readonly style=text-align:center value='' size=15 maxlength=12>";
		$aa_object[$ai_totrows][4] = "<input type=text name=txtfecha".$ai_totrows."   value=''    class=sin-borde readonly style=text-align:center size=20 maxlength=10 >";
		$aa_object[$ai_totrows][5] = "<input type=text name=txtconcepto".$ai_totrows."  value=''    class=sin-borde readonly style=text-align:left size=80 maxlength=250>";			
		$aa_object[$ai_totrows][6] = "<div align='center'><img src=../shared/imagebank/mas.gif alt=Detalle width=12 height=24 border=0></div>";
   }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<script type="text/javascript" language="JavaScript1.2" src="../shared/js/disabled_keys.js"></script>
<title>SIGESP - Consolidaci&oacute;n de la Ejecuci&oacute;n Presupuestaria de Gasto</title>
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
<link href="../shared/js/css_intra/datepickercontrol.css" rel="stylesheet" type="text/css">
<script type="text/javascript" language="JavaScript1.2" src="js/stm31.js"></script>
<script type="text/javascript" language="JavaScript1.2" src="js/funciones.js"></script>
<link href="css/rpc.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
</head>
<body>
<table width="762" border="0" align="center" cellpadding="0" cellspacing="0" class="contorno">
  <tr>
    <td height="30" colspan="10" class="cd-logo"><img src="../shared/imagebank/header.jpg" width="778" height="40"></td>
  </tr>
  <tr>
    <td height="20" colspan="10" bgcolor="#E7E7E7" class="cd-menu">
	<table width="778" border="0" align="center" cellpadding="0" cellspacing="0">
			
                <td width="423" height="20" bgcolor="#E7E7E7" class="descripcion_sistema">Sistema de  Presupuesto de Gasto</td>
			      <td width="349" bgcolor="#E7E7E7"><div align="right"><span class="letras-pequeñas"><b><?PHP print date("j/n/Y")." - ".date("h:i a");?></b></span></div></td>
	  	  <tr>
	  	    <td height="20" bgcolor="#E7E7E7" class="descripcion_sistema">&nbsp;</td>
	  	    <td bgcolor="#E7E7E7" class="letras-pequenas"><div align="right"><b><?PHP print $_SESSION["la_nomusu"]." ".$_SESSION["la_apeusu"];?></b></div></td>
      </table></td>
  </tr>
  <tr>
         <?php
	   if(array_key_exists("confinstr",$_SESSION["la_empresa"]))
	  {
      if($_SESSION["la_empresa"]["confinstr"]=='A')
	  {
   ?>
    <td height="20" colspan="11" bgcolor="#E7E7E7" class="cd-menu"><script type="text/javascript" language="JavaScript1.2" src="js/menu.js"></script></td>
  <?php
      }
      elseif($_SESSION["la_empresa"]["confinstr"]=='V')
	  {
   ?>
    <td height="20" colspan="11" bgcolor="#E7E7E7" class="cd-menu"><script type="text/javascript" language="JavaScript1.2" src="js/menu_2007.js"></script></td>
  <?php
      }
      elseif($_SESSION["la_empresa"]["confinstr"]=='N')
	  {
   ?>
       <td height="20" colspan="11" bgcolor="#E7E7E7" class="cd-menu"><script type="text/javascript" language="JavaScript1.2" src="js/menu_2008.js"></script></td>
  <?php
      }
	  	 }
	  else
	  {
   ?>
    <td width="1" height="20" colspan="11" bgcolor="#E7E7E7" class="cd-menu"><script type="text/javascript" language="JavaScript1.2" src="js/menu_2008.js"></script></td>
	<?php 
	}
	?>
  </tr>
  <tr>
    <td height="13" colspan="10" class="toolbar"></td>
  </tr>
  <tr>
    <td width="26" height="20" class="toolbar"><div align="center"><a href="javascript:ue_procesar();"><img src="../shared/imagebank/tools20/ejecutar.gif" title="Procesar" alt="Generar Comprobantes Consolidados..." name="Procesar" width="20" height="20" border="0" id="Procesar"></a></div></td>
    <td class="toolbar" width="21"><div align="center"><a href="javascript: ue_cerrar();"></a><a href="javascript: ue_reversar();"><img src="../shared/imagebank/tools20/eliminar.gif" alt="Revertir" width="20" height="20" border="0" title="Revertir"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_eliminar();"></a><a href="javascript: ue_ayuda();"></a><a href="javascript: ue_cerrar();"></a><a href="javascript: ue_imprimir();"><img src="../shared/imagebank/tools20/imprimir.gif"  title="Imprimir" alt="Imprimir" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="27"><div align="center"><a href="javascript: ue_ayuda();"></a><a href="javascript: ue_ayuda();"></a><a href="javascript: ue_cerrar();"><img src="../shared/imagebank/tools20/salir.gif"  title="Salir" alt="Salir" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="31"><div align="center"></div></td>
    <td class="toolbar" width="26"><div align="center"></div></td>
    <td class="toolbar" width="26"><div align="center"></div></td>
    <td class="toolbar" width="26"><div align="center"></div></td>
    <td class="toolbar" width="26"><div align="center"></div></td>
    <td class="toolbar" width="544">&nbsp;</td>
  </tr>
</table>

<p>
<?php
	function uf_conectar_destino($as_gestor,$as_dbdestino,$as_puerto,$as_servidor,$as_usuario,$as_password) 
	{
		global $msg;	
		if (strtoupper($as_gestor)==strtoupper("mysqlt"))
		{ 
		    $conec = @mysql_connect($as_servidor,$as_usuario,$as_password);						
			if($conec===false)
			{
				$msg->message("No pudo conectar con el servidor de datos MYSQL ".$as_servidor." , consulte a su Administrador de Sistema");	
			}
			else
			{			    
				$lb_ok=@mysql_select_db(trim($as_dbdestino),$conec);
				if (!$lb_ok)
				{
					$msg->message("No existe la base de datos ".$as_dbdestino);					
				}
			}
		}		
		elseif(strtoupper($as_gestor)==strtoupper("postgres"))
		{
			$conec = @pg_connect("host=".$as_servidor." port=".$as_puerto."  dbname=".$as_dbdestino." user=".$as_usuario." password=".$as_password); 
		    
			if (!$conec)
			{
				$msg->message("No pudo conectar a la base de datos ".$as_dbdestino." en el servidor ".$as_servidor.", consulte a su Administrador de Sistema");				
			}
      	 
	    }	
	 return $conec;
	}// fin de function uf_conectar_destino() 
	
	function uf_get_datos_bddestino($as_dbdestino,$aa_empresa,&$ab_errorbdcon)
	{
	 $encontrado = false;
	 global $msg;
	 $li_total = count($aa_empresa["database"]);
	 for($i=1;(($i <= $li_total)&&(!$encontrado)); $i++)
	 {
       if(trim($aa_empresa["database"][$i])==trim($as_dbdestino))
	   {
	     if($as_dbdestino != $_SESSION["ls_database"])
		 {
		    $lb_conecta = uf_conectar_destino($aa_empresa["gestor"][$i],$aa_empresa["database"][$i],$aa_empresa["port"][$i],$aa_empresa["hostname"][$i], $aa_empresa["login"][$i],$aa_empresa["password"][$i]);
			if($lb_conecta)
			{
				 $_SESSION["ls_database_destino"] = $aa_empresa["database"][$i];							
				 $_SESSION["ls_hostname_destino"] = $aa_empresa["hostname"][$i];
				 $_SESSION["ls_login_destino"]    = $aa_empresa["login"][$i];
				 $_SESSION["ls_password_destino"] = $aa_empresa["password"][$i];
				 $_SESSION["ls_gestor_destino"]   = $aa_empresa["gestor"][$i];	
				 $_SESSION["ls_port_destino"]     = $aa_empresa["port"][$i];
				 $encontrado = true;
				 $ab_errorbdcon=false;
			}
		 
		 }
		 else
		 {
		  $msg->message("La Base de Datos ".strtoupper($as_dbdestino)." no puede ser consolidadora de si misma, verifique la configuración de las Bases de Datos para consolidación");
		  $ab_errorbdcon=true;
		  return false;
		 }
		
	   }		 
	}
	return $encontrado;  
   }
   
   if(array_key_exists("txtfecha",$_POST))
   {
		$ld_fecha = $_POST["txtfecha"];
   }
   else
   {
		$ld_fecha = date("d/m/Y");
   }
    
 if (array_key_exists("operacion",$_POST))
	{
		$ls_operacion=$_POST["operacion"];
		if ($ls_operacion=="BUSCAR")
	    {
		   $io_consolidacion = new sigesp_spg_c_consolidacion_empresas();
		   $li_totrows=1;
	       $lb_valido=$io_consolidacion->uf_select_comprobantes_consolidacion($lo_object,$li_totrows);  
		   unset($io_consolidacion);
		}	
		elseif ($ls_operacion=="PROCESAR")
	    {
		  
		  $ld_fecha    =$_POST["txtfecha"];
		  $io_consolidacion = new sigesp_spg_c_consolidacion_empresas();
		  $rs_basedatos=$io_consolidacion->uf_cargar_bdconsolidacion();
		  while(!$rs_basedatos->EOF)
		  {
		   $ls_basedatos = $rs_basedatos->fields["nombasdat"];
		   $lb_errorbdcon=false;
		   $lb_encontrado = uf_get_datos_bddestino($ls_basedatos,$empresa,$lb_errorbdcon);
		   if($lb_encontrado)
		   {
			  $io_consolidacion->uf_conectar_destino($_SESSION["ls_hostname_destino"],$_SESSION["ls_login_destino"],$_SESSION["ls_password_destino"] ,$_SESSION["ls_database_destino"],$_SESSION["ls_gestor_destino"]);
			  $lb_valido=$io_consolidacion->uf_generar_comprobante_consolidacion($ld_fecha,$la_seguridad);
			  if($lb_valido)
			  {
			    $io_consolidacion->uf_liberar_conexion_destino();
			  }
			  													
		   }
		   else
		   {
			  if(!$lb_errorbdcon)
			  {
			   $msg->message("La Base de Datos para consolidación ".strtoupper($ls_basedatos)." no está en el archivo de configuración del sistema, consulte a su Administrador de Sistema");
			  }
		   }
		   $rs_basedatos->MoveNext();
		  }
		  $li_totrows=1;
	      $lb_valido=$io_consolidacion->uf_select_comprobantes_consolidacion($lo_object,$li_totrows);
		  unset($io_consolidacion);
	    }
		elseif($ls_operacion=="REVERSAR")
		{
		  $io_consolidacion = new sigesp_spg_c_consolidacion_empresas();
		  for ($li_i=1;$li_i<=$li_totrows;$li_i++)//$li_totrows numeros de comprobantes
		  {
		    uf_cargar_dt($li_i);
            if($ls_selcmp==1)
			{
			  $ls_cod_prov = "----------";
			  $ls_ced_ben  = "----------";
			  $ls_tipo     = "-";
			  $lb_valido=$io_consolidacion->uf_revertir_comprobante_consolidacion($ls_empresa,$ls_procede,$ls_comprobante, $ls_fecha,$ls_tipo,$ls_ced_ben,$ls_cod_prov,$ls_codban,$ls_ctaban,$la_seguridad);
			}
		  }
		  $lb_valido=$io_consolidacion->uf_select_comprobantes_consolidacion($lo_object,$li_totrows);
		  unset($io_consolidacion);
        }
    }
 else
    {
	 $io_consolidacion = new sigesp_spg_c_consolidacion_empresas();
     $li_totrows=1;
     $lb_valido=$io_consolidacion->uf_select_comprobantes_consolidacion($lo_object,$li_totrows);	  
     unset($io_consolidacion);
    }	
?>
</p>
<form name="form1" method="post" action="">
  <div align="center">
    <?php
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$io_fun_gasto->uf_print_permisos($ls_permisos,$la_permisos,$ls_logusr,"location.href='sigespwindow_blank.php'");
	unset($io_fun_gasto);
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>
  </div>
  <table width="595" border="0" align="center">
    <tr>
      <td width="589"><div align="center">
        <table width="575" border="0" cellpadding="1" cellspacing="0" class="formato-blanco" align="center">
          <tr>
            <td height="22" colspan="5" class="titulo-celdanew">CONSOLIDACI&Oacute;N DE LA EJECUCI&Oacute;N PRESUPUESTARIA DE GASTO</td>
          </tr>
          <tr>
            <td width="147" ><div align="right"></div></td>
            <td colspan="4"><div align="center"></div></td>
          </tr>
          <tr style="display:none">
            <td ><div align="right">
                <p>&nbsp;</p>
            </div></td>
            <td colspan="4"><p>
                <input name="operacion" type="hidden" id="operacion" value="<?php print ($ls_operacion); ?>">
            </p></td>
          </tr>



          <tr>
            <td><div align="right">Fecha de Consolidaci&oacute;n al :</div></td>
            <td><input name="txtfecha" type="text" id="txtfecha" style="text-align:center" value="<?php print $ld_fecha; ?>" maxlength="10" readonly="true">            </td>
            <td colspan="3" align="right"><span class="toolbar"><a href="javascript: ue_buscar();"> <img src="../shared/imagebank/tools20/buscar.gif"  width="20" height="20" border="0">Buscar Comprobantes </a></span></td>
          </tr>
          <tr>
            <td height="22" colspan="5" class="titulo-celdanew">&nbsp;</td>
          </tr>
          <tr>
            <td height="22"><div align="right"></div></td>
            <td width="125"></td>
            <td width="33">&nbsp;</td>
            <td width="131"><a href="javascript: deseleccionar_todos();"><span class="toolbar"></span></a><span class="toolbar"><a href="javascript: seleccionar_todos();"><img src="../shared/imagebank/tools20/aprobado.gif"  width="20" height="20" border="0">Seleccionar Todos</a><a href="javascript: deseleccionar_todos();"></a></span></td>
            <td width="127"><a href="javascript: deseleccionar_todos();"><img src="../shared/imagebank/tools20/deshacer.gif" width="20" height="20" border="0">Deseleccionar Todos</a></td>
          </tr>
          <tr>
            <td colspan="5"><div align="center">
                <?php
				require_once("../shared/class_folder/grid_param.php");
				$io_grid=new grid_param();
				if(empty($lo_object))
				{
				 uf_agregarlineablanca($lo_object,1);
				}
				$io_grid->makegrid($li_totrows,$lo_title,$lo_object,$li_widthtable,$ls_titletable,$ls_nametable);
				unset($io_grid);
			?>
              </div>
                <input name="totalfilas" type="hidden" id="totalfilas" value="<?php print $li_totrows;?>">
                <input name="hidrango" type="hidden" id="hidrango">
                <input name="hidconsolida" type="hidden" id="hidconsolida">
				<input name="nomreporte" type="hidden" id="nomreporte" value=<?php echo $ls_reporte; ?>></td>
          </tr>
        </table>
      </div></td>
    </tr>
  </table>
  <div align="center"><img src="../shared/imagebank/tools20/progress.gif" alt="mostrar"  name="mostrar" width="32" height="32" id="mostrar" style="visibility:hidden">
  </div>
</form>      
</body>
<script language="javascript">
f = document.form1;
var patron = new Array(2,2,4);
var patron2 = new Array(1,3,3,3,3);
function ue_procesar()
{
	li_ejecutar=f.ejecutar.value;
	if (li_ejecutar==1)
   	{
		  document.images["mostrar"].style.visibility="";
		  document.images["mostrar"].style.visibility="visible";
		  f.operacion.value = "PROCESAR";
		  f.action="sigesp_spg_p_consolidacion_empresas.php";
		  f.submit();
   	}
	else
   	{
 		alert("No tiene permiso para realizar esta operacion");
   	}
}


function cambiar_valor (li_i)
{
	sel= eval ('document.form1.selcmp'+li_i);	
	if (sel.checked)
	{
		selpro = eval ('document.form1.txtselcmp'+li_i);	
		selpro.value = '1';
	}	
	else
	{
		selpro = eval ('document.form1.txtselcmp'+li_i);	
		selpro.value = '0';
	}
}

function seleccionar_todos()
{	
	li_total = f.totalfilas.value;
	for(li_i=1;li_i<=li_total;li_i++)
	{
	  ch= eval ('document.form1.selcmp'+li_i);
	  ch.checked=true;
	  selcmp = eval ('document.form1.txtselcmp'+li_i);	
	  selcmp.value = '1';
	}
}

function chequear_seleccion()
{	
	var ok = false;
	li_total = f.totalfilas.value;
	for(li_i=1;li_i<=li_total;li_i++)
	{
	  ch= eval ('document.form1.selcmp'+li_i);
	  if(ch.checked)
	  {
	   ok = true;
	   break;
	  }
	}
	return ok;
}

function deseleccionar_todos()
{	
	li_total = f.totalfilas.value;
	for(li_i=1;li_i<=li_total;li_i++)
	{
	  ch= eval ('document.form1.selcmp'+li_i);	
	  ch.checked=false;
	  selcmp = eval ('document.form1.txtselcmp'+li_i);	
	  selcmp.value = '0';
	}
}

function ue_buscar()
{	
	document.images["mostrar"].style.visibility="";
    document.images["mostrar"].style.visibility="visible";
	f.operacion.value="BUSCAR";
	f.action="sigesp_spg_p_consolidacion_empresas.php";
	f.submit();
}

function ue_reversar()
{	
	li_eliminar=f.eliminar.value;
	if (li_eliminar==1)
   	{
		comprobanteaux = eval ('document.form1.txtcomprobante1');
		
		if(comprobanteaux.value != "")
		{
		 
		 if(chequear_seleccion())
		 {
		  document.images["mostrar"].style.visibility="";
		  document.images["mostrar"].style.visibility="visible";
		  f.operacion.value="REVERSAR";
		  f.action="sigesp_spg_p_consolidacion_empresas.php";
		  f.submit();
		 }
		 else
		 {
		  alert("No ha seleccionado ningún comprobante para reverso, verifique por favor");
		 }
		}
		else
		{
		 alert("No hay comprobantes para eliminar");
		}
	}
	else
	{
	 alert("No tiene permiso para realizar esta operacion");
	}
}

function ue_cerrar()
{
   location.href='../index_modules.php' 
}

function ue_formatofecha(d,sep,pat,nums)
{
	if(d.valant != d.value)
	{
		val = d.value
		largo = val.length
		val = val.split(sep)
		val2 = ''
		for(r=0;r<val.length;r++)
		{
			val2 += val[r]	
		}
		if(nums)
		{
			for(z=0;z<val2.length;z++)
			{
				if(isNaN(val2.charAt(z)))
				{
					letra = new RegExp(val2.charAt(z),"g")
					val2 = val2.replace(letra,"")
				}
			}
		}
		val = ''
		val3 = new Array()
		for(s=0; s<pat.length; s++)
		{
			val3[s] = val2.substring(0,pat[s])
			val2 = val2.substr(pat[s])
		}
		for(q=0;q<val3.length; q++)
		{
			if(q ==0)
			{
				val = val3[q]
			}
			else
			{
				if(val3[q] != "")
				{
					val += sep + val3[q]
				}
			}
		}
		d.value = val
		d.valant = val
	}
}

function ue_imprimir()
{
 	f=document.form1;
	nombreporte=document.getElementById('nomreporte').value;
	li_imprimir=f.imprimir.value;
	if(li_imprimir==1)
	{
		var comprobantedes = "";
		var comprobantehas = "";
		var comprobanteaux = "";
		li_total = f.totalfilas.value;
		
		comprobanteaux = eval ('document.form1.txtcomprobante1');
		
		if(comprobanteaux.value != "")
		{
		 comprobantedes = comprobanteaux.value;
		 comprobanteaux = eval ('document.form1.txtcomprobante'+li_total);
		 comprobantehas = comprobanteaux.value;
		 
		txtcompdes  = comprobantedes;
		txtcomphas    = comprobantehas;
		txtprocdes  = "SPGCMP";
		txtprochas  = "SPGCMP";
		txtfecdes   = "";
		txtfechas   = "";
		tipoformato=0;
		orden="N";
		pagina="reportes/"+nombreporte+"?txtcompdes="+txtcompdes
				+"&txtcomphas="+txtcomphas+"&txtprocdes="+txtprocdes+"&txtprochas="+txtprochas
				+"&txtfecdes="+txtfecdes+"&rborden="+orden+"&txtfechas="+txtfechas+"&tipoformato="+tipoformato;
		window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
		}
		else
		{
		 alert("No hay nada que reportar");
		}

		
	}
	else
	{
       alert("No tiene permiso para realizar esta operacion");	
	}	


}

function uf_verdetalle(codcom,procede)
{
	Xpos=((screen.width/2)-(500/2)); 
	Ypos=((screen.height/2)-(400/2));
	window.open("sigesp_spg_pdt_consolidacion.php?codcom="+codcom+"&procede="+procede+"","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=500,height=400,left="+Xpos+",top="+Ypos+",location=no,resizable=no");
}
</script> 
<script language="javascript" src="../shared/js/js_intra/datepickercontrol.js" ></script>
</html>