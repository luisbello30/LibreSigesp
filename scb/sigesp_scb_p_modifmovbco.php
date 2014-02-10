<?php
	session_start();
	if(!array_key_exists("la_logusr",$_SESSION))
	{
		print "<script language=JavaScript>";
		print "location.href='../sigesp_inicio_sesion.php'";
		print "</script>";		
	}
$ls_logusr=$_SESSION["la_logusr"];
require_once("class_funciones_banco.php");
$io_fun_banco= new class_funciones_banco();
$io_fun_banco->uf_load_seguridad("SCB","sigesp_scb_p_modifmovbco.php",$ls_permisos,$la_seguridad,$la_permisos);
$li_diasem = date('w');
switch ($li_diasem){
  case '0': $ls_diasem='Domingo';
  break; 
  case '1': $ls_diasem='Lunes';
  break;
  case '2': $ls_diasem='Martes';
  break;
  case '3': $ls_diasem='Mi&eacute;rcoles';
  break;
  case '4': $ls_diasem='Jueves';
  break;
  case '5': $ls_diasem='Viernes';
  break;
  case '6': $ls_diasem='S&aacute;bado';
  break;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Modificaci&oacute;n de Movimientos Bancarios</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/css/report.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/js/css_intra/datepickercontrol.css" rel="stylesheet" type="text/css">
<script type="text/javascript" language="JavaScript1.2" src="js/stm31.js"></script>
<script type="text/javascript" language="JavaScript1.2" src="../shared/js/disabled_keys.js"></script>
<script type="text/javascript" language="JavaScript1.2" src="../shared/js/valida_tecla.js"></script>
<script type="text/javascript" language="JavaScript1.2" src="js/valida_fecha.js"></script>
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
  <?php
  require_once("../shared/class_folder/grid_param.php");
  require_once("../shared/class_folder/ddlb_generic.php");
  require_once("sigesp_scb_c_elimin_anulado.php");  
  require_once("sigesp_scb_c_movbanco.php"); 
  require_once("../shared/class_folder/class_logs.php");					
  $io_logs = new logs();
  $io_grid 			    = new grid_param();
  $in_class_contabiliza = new sigesp_scb_c_elimin_anulado();
  $in_movbco 			= new sigesp_scb_c_movbanco($la_seguridad);
  $ruta_log = 'scb/logs/log_cheques_'.date("dmY").'.txt';
  if (array_key_exists("operacion",$_POST))
     {
       $ls_operacion	 = $_POST["operacion"];
	   $ls_operacion_bco = 'CH';
	   $li_total_record  = $_POST["hide_total_row"];
	   $ls_numdoc		 = $_POST["txtnumdoc"]; 
	   //$ld_fecha		 = $_POST["txtfecha"];
       //$ls_numdocnew	 = $_POST["txtnumdocnew"]; 
	   $datos['cedtit']	 = $_POST["txt_cedtit"];
	   $datos['nomtit']	 = $_POST["txt_nomtit"]; 
	   $datos['cedcau']	 = $_POST["txt_cedcau"]; 
	   $datos['nomcau']	 = $_POST["txt_nomcau"]; 
	   $datos['cedbene']	 = $_POST["txt_cedben"]; 
	   $datos['nombene']	 = $_POST["txt_nomben"]; 
	   $datos['codperi']	 = $_POST["cmb_periodo"]; 
	   
	   
	 }
  else
     {
       $datos['codperi'] ="";
	   $datos['cedtit']	 ="";
	   $datos['nomtit']	 =""; 
	   $datos['cedcau']	 =""; 
	   $datos['nomcau']	 =""; 
	   $datos['cedbene'] =""; 
	   $datos['nombene'] =""; 
	   $ls_operacion     = "";
	   $ls_operacion_bco = "CH";
	   $li_total_record  = 0;
	   $ls_numdoc="";
	   $ls_numdocnew="";
	   $ld_fecha=""; 
	   $li_total=1;
	
	for($li_row=1;$li_row<=$li_total;$li_row++)
	{
		//$arr_object[$li_row][1] = "<input type=checkbox name=chksel".$li_row."   id=chksel".$li_row." value=1 style=width:15px;height:15px onClick='return false;'>";		
		$arr_object[$li_row][1] = "<input type=text     name=txtnumdoc".$li_row."       value='' class=sin-borde readonly style=text-align:center size=17 maxlength=15>";
		$arr_object[$li_row][2] = '<input type=text  onBlur="rellenar_cad(this.value,15,'."'".'doc'."'".',this)"   name=txtnuevo'.$li_row."       value='' class=sin-borde style=text-align:left size=17 maxlength=15>";
		$arr_object[$li_row][3] = "<input type=text     name=txttit".$li_row." value='' class=sin-borde readonly style=text-align:center size=12 maxlength=10>";
		$arr_object[$li_row][4] = "<input type=text     name=txtnomtit".$li_row." value='' class=sin-borde readonly style=text-align:center size=10 >";
		$arr_object[$li_row][5] = "<input type=text     name=txtcau".$li_row." value='' class=sin-borde readonly style=text-align:center size=12 maxlength=10>";
		$arr_object[$li_row][6] = "<input type=text     name=txtnomcau".$li_row." value='' class=sin-borde readonly style=text-align:center size=10 >";
		$arr_object[$li_row][7] = "<input type=text     name=txtmonto".$li_row." value='' class=sin-borde readonly style=text-align:center size=12>";
		$arr_object[$li_row][8] = "<input type=text     name=txtfecmov".$li_row."       value='' class=sin-borde readonly style=text-align:left size=12 maxlength=10>";
		$arr_object[$li_row][9] = '<div style="width:250px"></div>'.
     				                          "<input type=hidden   name=txtcodban".$li_row."       value='' >".
	   				                          "<input type=hidden   name=txtctaban".$li_row."       value='' >".
										      "<input type=hidden   name=txtestmov".$li_row."       value='' >";
		
		
	}
	$li_row=$li_total;
  }
  $la_value=array("ND","NC","CH","DP","RE");
   if ($ls_operacion=="PROCESAR")  
  {  
		
		$procesados=0;
		$noprocesados=0;
		for($li_i=1;$li_i<=$li_total_record;$li_i++)
		{
			if($_POST["txtnuevo".$li_i])
			{
				$ls_docum=$_POST["txtnumdoc".$li_i];
				$ls_estcon=$_POST["estcon".$li_i];
				if($ls_estcon==0)
				{
					//$ls_conmov=$_POST["txtconmov".$li_i];
					$ls_codban=$_POST["txtcodban".$li_i];
					$ls_ctaban=$_POST["txtctaban".$li_i];
					$ls_estmov=$_POST["txtestmov".$li_i];
					$ls_numdocnew=$_POST["txtnuevo".$li_i];
					$ls_fecmov=$_POST["txtfecmov".$li_i];
					
								
					$io_logs->sislog(date("d/m/Y H:i:s").'Actualizando el documento: '.$ls_docum.'****** Nuevo Número: '.$ls_numdocnew,$ruta_log);
					$lb_valido = $in_movbco->uf_actualiza_documento_pensiones($ls_docum,$ls_codban,$ls_ctaban,'',$ls_estmov,$ls_fecmov,$ls_numdocnew);
					if($lb_valido)		
					{
						
						$procesados++;
					}
					else
					{
						$in_movbco->msg->message("Error en el cambio del número de documento del cheque $ls_docum.");
						$noprocesados++;
					}
				}
				else
				{
					$in_movbco->msg->message("El cheque $ls_docum no puede ser cambiado, ya fue Conciliado");
					$noprocesados++;
				}		
			}
		}
	  $ls_operacion='CARGAR_DT';
	  $in_movbco->msg->message("Se actualizaron $procesados documentos ".'\n'."Falló la actualización para $noprocesados documentos");
  }  
  unset($in_movbco);
?>
<table width="762" border="0" align="center" cellpadding="0" cellspacing="0" class="contorno">
  <tr>
    <td width="781" height="30" class="cd-logo"><img src="../shared/imagebank/header.jpg" width="778" height="40"></td>
  </tr>
  <tr>
  <td width="778" height="20" colspan="11" bgcolor="#E7E7E7">
    <table width="778" border="0" align="center" cellpadding="0" cellspacing="0">			
      <td width="430" height="20" bgcolor="#E7E7E7" class="descripcion_sistema">Caja y Banco</td>
	  <td width="350" bgcolor="#E7E7E7"><div align="right"><span class="letras-pequenas"><b><?php print $ls_diasem." ".date("d/m/Y")." - ".date("h:i a ");?></b></span></div></td>
	  <tr>
	    <td height="20" bgcolor="#E7E7E7" class="descripcion_sistema">&nbsp;</td>
	  	<td bgcolor="#E7E7E7"><div align="right" class="letras-pequenas"><b><?php print $_SESSION["la_nomusu"]." ".$_SESSION["la_apeusu"];?></b></div></td>
      </tr>
    </table>
  </td>
  </tr>
  <tr>
    <td height="20" class="cd-menu"><script type="text/javascript" language="JavaScript1.2" src="js/menu.js"></script></td>
  </tr>
  <tr>
    <td height="13" bgcolor="#FFFFFF" class="toolbar">&nbsp;</td>
  </tr>
  <tr>
    <td height="20" bgcolor="#FFFFFF" class="toolbar"><a href="javascript:ue_procesar();"><img src="../shared/imagebank/tools20/ejecutar.gif" alt="Procesar" title="Procesar" width="20" height="20" border="0"></a><a href="javascript: ue_cerrar();"><img src="../shared/imagebank/tools20/salir.gif" alt="Salir" title="Salir" width="20" height="20" border="0"></a></td>	
  </tr>
</table>
<form name="form1" method="post" action="">
<?php
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$io_fun_banco->uf_print_permisos($ls_permisos,$la_permisos,$ls_logusr,"location.href='sigespwindow_blank.php'");
	unset($io_fun_banco);
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>
<p>&nbsp;</p>
  <table width="713" border="0" align="center" cellpadding="0" cellspacing="4" class="formato-blanco">
    <tr class="titulo-ventana">
      <td height="22" colspan="4">Modificaci&oacute;n de Movimientos de Bancos Pensiones</td>
    </tr>
    <tr>
      <td colspan="4">&nbsp;</td>
    </tr>
    <tr>
      <td width="217" align="right"><div align="right">N&uacute;mero de Documento a Buscar</div></td>
      <td width="182"><div align="left">
        <input name="txtnumdoc" type="text" id="txtnumdoc" onBlur="rellenar_cad(this.value,15,'doc1',this)" style="text-align:center" value="<?php print $ls_numdoc;?>" size="20" maxlength="15">
      </div></td>
      <td width="140"><div align="right">Periodo</div></td>
      <td width="152"><div align="left">
        <label>
        <select name="cmb_periodo" id="cmb_periodo">
          <option value="001" <?php if($datos['codperi']=='001'){ echo 'selected';} ?>>Enero</option>
          <option value="002" <?php if($datos['codperi']=='002'){ echo 'selected';} ?>>Febrero</option>
          <option value="003" <?php if($datos['codperi']=='003'){ echo 'selected';} ?>>Marzo</option>
          <option value="004" <?php if($datos['codperi']=='004'){ echo 'selected';} ?>>Abril</option>
          <option value="005" <?php if($datos['codperi']=='005'){ echo 'selected';} ?>>Mayo</option>
          <option value="006" <?php if($datos['codperi']=='006'){ echo 'selected';} ?>>Junio</option>
          <option value="007" <?php if($datos['codperi']=='007'){ echo 'selected';} ?>>Julio</option>
          <option value="008" <?php if($datos['codperi']=='008'){ echo 'selected';} ?>>Agosto</option>
          <option value="009" <?php if($datos['codperi']=='009'){ echo 'selected';} ?>>Septiembre</option>
          <option value="010" <?php if($datos['codperi']=='010'){ echo 'selected';} ?>>Octubre</option>
          <option value="011" <?php if($datos['codperi']=='011'){ echo 'selected';} ?>>Noviembre</option>
          <option value="012" <?php if($datos['codperi']=='012'){ echo 'selected';} ?>>Diciembre</option>
        </select>
        </label>
      </div></td>
    </tr>
    <tr>
      <td width="217" align="right"><div align="right">Cedula Titular:</div></td>
      <td><input name="txt_cedtit" type="text" id="txt_cedtit"  style="text-align:left" value="<?php print $datos['cedtit'];?>" size="20"></td>
      <td align="right">Nombre Titular</td>
      <td><input name="txt_nomtit" type="text" id="txt_nomtit"  style="text-align:left" value="<?php print $datos['nomtit'];?>" size="20"></td>
    </tr>
    <tr>
      <td width="217" align="right"><div align="right">Cedula Causante</div></td>
      <td><input name="txt_cedcau" type="text" id="txt_cedcau"  style="text-align:left" value="<?php print $datos['cedcau'];?>" size="20"></td>
      <td align="right">Nombre Causante</td>
      <td><input name="txt_nomcau" type="text" id="txt_nomcau"  style="text-align:left" value="<?php print $datos['nomcau'];?>" size="20"></td>
    </tr>
    <tr>
      <td align="right"><div align="right">Cedula Benef.</div></td>
      <td><input name="txt_cedben" type="text" id="txt_cedben"  style="text-align:left" value="<?php print $datos['cedbene'];?>" size="20"></td>
      <td align="right">Nombre Benef.</td>
      <td><input name="txt_nomben" type="text" id="txt_nomben"  style="text-align:left" value="<?php print $datos['nombene'];?>" size="20"></td>
    </tr>
    <tr>
      <td colspan="4" style="text-align:left"><a href="javascript: buscarx(1);"><img src="../shared/imagebank/tools20/imprimir.gif" alt="" width="20" height="20" border="0">I</a><a href="javascript: imprimir_consolidado();">mprimir_todas las Cartas</a>&nbsp;</td>
    </tr>
    <tr>
      <td colspan="4" style="text-align:left"><a href="javascript:ue_search();"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar..." width="20" height="20" border="0">Buscar</a></td>
    </tr>
    <tr>
      <td colspan="4"><div align="center">
	  
	  </div></td>
    </tr>
    <tr>
      <td colspan="4"></td>
    </tr>
  </table>
  
  <br>
  
<table width="502" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
    <tr>
      <td>
      
       <?php
	     require_once("sigesp_scb_c_movbanco.php");
		 $in_movbco	= new sigesp_scb_c_movbanco($la_seguridad);
		 //$title[1]='&nbsp;&nbsp;&nbsp;';
		 $title[1]="Nº Cheque"; 
		 $title[2]="Nuevo Cheque";
		 $title[3]="Titular";
		 $title[4]="Nombre";
		 $title[5]="Causante";
		 $title[6]="Nombre";
		 $title[7]="Monto";
		 $title[8]="Fecha Movimiento";
		 $title[9]="Beneficiario";		
		 if($ls_operacion=='CARGAR_DT')
		 {
	     	$in_class_contabiliza->uf_select_banco_contabilizar_documento3( $ls_operacion_bco, $arr_object ,$li_total_record,"N",$ls_numdoc,/*$ld_fecha*/$datos);
		 	if ($li_total_record>1)
			{
				//$in_movbco->msg->message("Recuerde que debe seleccionar un unico movimiento a cambiar");
			}
		 }
		 $io_grid->makegrid($li_total_record,$title,$arr_object,500,"Movimientos de Banco a Actualizar","grdsep" );
	  ?>
      </td>
    </tr>
</table>
 
  <p>&nbsp;</p>
  <input name="operacion" type="hidden" id="operacion">
      <input name="hide_total_row" type="hidden" id="hide_total_row" value="<?php print $li_total_record;?>">
</form>
<p>&nbsp;</p>
</body>
<script language="javascript">
var patron = new Array(2,2,4);
function ue_procesar()
{
	f=document.form1;
	
	li_ejecutar=f.ejecutar.value;
	if(li_ejecutar==1)
	{
		
		if(confirm("Está seguro de cambiar este(os) registro(s)?\n  Esta operación no puede reversarse"))
		{
			f.operacion.value ="PROCESAR";
			f.action="sigesp_scb_p_modifmovbco.php";
			f.submit();
		}
		
	}
	else
	{
		alert("No tiene permiso para realizar esta operacion");
	}
}
function ue_search()
{
	f=document.form1;
	
	
	f.operacion.value ="CARGAR_DT";
	f.action="sigesp_scb_p_modifmovbco.php";
	f.submit();
	
}
function ue_cerrar()
{
	window.location.href="sigespwindow_blank.php";
}
function rellenar_cad(cadena,longitud,campo,objeto)
{
	if (cadena!="")
	{
		var mystring=new String(cadena);
		cadena_ceros="";
		lencad=mystring.length;
	
		total=longitud-lencad;
		for(i=1;i<=total;i++)
		{
			cadena_ceros=cadena_ceros+"0";
		}
		cadena=cadena_ceros+cadena;
		if(campo=="doc")
		{
			objeto.value=cadena;
		}
		if(campo=="doc1")
		{
			objeto.value=cadena;
		}
		
	}
}


function ue_imprimir(ls_codban,ls_ctaban,ls_numdoc,ls_chevau,ls_codope)
{
			
		ls_pagina="reportes/sigesp_scb_rpp_voucher_ipsfa.php?codban="+ls_codban+"&ctaban="+ls_ctaban+"&numdoc="+ls_numdoc+"&chevau="+ls_chevau+"&codope="+ls_codope;
		window.open(ls_pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,left=0,top=0,location=no,resizable=yes");
			
}

function imprimir_consolidado()
{
			
		f=document.form1;
		msj =  "Se dispone a generar un documento con todas las cartas." + '\n' + 
		       "Este seguro de haber cargado todos los números de cheque." + '\n' +
			   "Este proceso puede tomar tiempo." + '\n' + '\n' +
			   "¿Esta seguro de generar el consolidado de todas las cartas?";
		if(confirm(msj)){
			ls_pagina="reportes/sigesp_scb_rpp_carta_tribunal_consolidada.php?codperi="+f.cmb_periodo.value;
			window.open(ls_pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,left=0,top=0,location=no,resizable=yes");
		}
					
}

function ue_imprimir_carta(ls_codban,ls_ctaban,ls_numdoc,ls_chevau,ls_codope)
{
			
		ls_pagina="reportes/sigesp_scb_rpp_carta_tribunal.php?codban="+ls_codban+"&ctaban="+ls_ctaban+"&numdoc="+ls_numdoc+"&chevau="+ls_chevau+"&codope="+ls_codope;
		window.open(ls_pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,left=0,top=0,location=no,resizable=yes");
			
}
</script>
<script language="javascript" src="../shared/js/js_intra/datepickercontrol.js"></script>
</html>