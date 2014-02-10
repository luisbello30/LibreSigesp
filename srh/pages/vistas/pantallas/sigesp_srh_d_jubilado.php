<?php
    session_start();
	//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	if(!array_key_exists("la_logusr",$_SESSION))
	{
		print "<script language=JavaScript>";
		print "location.href='../../../../sigesp_inicio_sesion.php'";
		print "</script>";		
	}
	$ls_logusr=$_SESSION["la_logusr"];
	require_once("../../../class_folder/utilidades/class_funciones_srh.php");
	$io_fun_srh=new class_funciones_srh('../../../../');	
	$io_fun_srh->uf_load_seguridad("SRH","sigesp_srh_d_jubilado.php",$ls_permisos,$la_seguridad,$la_permisos);
	//--------------------------------------------------------------------------------------------------------------
	 
	//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title >Ficha de Jubilado</title>

<link rel="stylesheet" type="text/css" href="../../resources/css/ext-all.css" />
<script type="text/javascript" language="JavaScript1.2" src="../../js/sigesp_srh_js_jubilado.js"></script>
<script type="text/javascript" language="JavaScript1.2" src="../../../public/js/librerias_comunes.js"></script>

<style type="text/css">

.Estilo1 {color: #428ACE}


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
	color: #006699;}

</style>
</head>

<body class=" yui-skin-sam" onLoad="ue_inicializar();">

<?php 
?>
<table width="762" border="0" align="center" cellpadding="0" cellspacing="0" class="contorno">
  <tr>
    <td width="780" height="30" colspan="11" class="cd-logo"><img src="../../../public/imagenes/header.jpg" width="778" height="40"></td>
  </tr>
  <tr>
    <td width="432" height="20" colspan="11" bgcolor="#E7E7E7">
		<table width="762" border="0" cellpadding="0" cellspacing="0">
			<td width="432" height="20" align="left" bgcolor="#E7E7E7" class="descripcion_sistema">Sistema de Recursos Humanos</td>
			<td width="346" bgcolor="#E7E7E7"><div align="right"><span class="letras-pequenas"><b><?PHP print date("j/n/Y")." - ".date("h:i a");?></b></span></div></td>
			<tr>
	  	      <td height="20" bgcolor="#E7E7E7" class="descripcion_sistema">&nbsp;</td>
	  	      <td bgcolor="#E7E7E7" class="letras-pequenas"><div align="right"><b><?PHP print $_SESSION["la_nomusu"]." ".$_SESSION["la_apeusu"];?></b></div></td> </tr>
	  	</table>
    </td>
  </tr>
  <tr>
    <td height="20" colspan="11" bgcolor="#E7E7E7" class="cd-menu"><script type="text/javascript" language="JavaScript1.2" src="../../js/menu/menu.js"></script></td>
  </tr>
  <tr>
    <td width="780" height="13" colspan="11" class="toolbar"></td>
  </tr>
  <tr>
    <td height="20" width="25" class="toolbar"><div align="center"><a href="javascript: ue_nuevo();"><img src="../../../../shared/imagebank/tools20/nuevo.gif" alt="Nuevo" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_guardar();"><img src="../../../../shared/imagebank/tools20/grabar.gif" alt="Grabar" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_buscar();"><img src="../../../../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_cerrar();"><img src="../../../../shared/imagebank/tools20/salir.gif" alt="Salir" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_ayuda();"><img src="../../../../shared/imagebank/tools20/ayuda.gif" alt="Ayuda" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="530">&nbsp;</td>
  </tr>
</table>
<p><div id="mostrar" align="center"></div></p>

<form action="" method="post" enctype="multipart/form-data" name="form1">
<?php

//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$io_fun_srh->uf_print_permisos($ls_permisos,$la_permisos,$ls_logusr,"location.href='sigespwindow_blank.php'");
	unset($io_fun_srh);
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>

<input name="hidguardar" type="hidden" id="hidguardar" value="">
<input name="hidguardar_ben" type="hidden" id="hidguardar_ben" value="">
<input name="hidcodest" type="hidden" id="hidcodest" value="">
<input name="hidcodmun" type="hidden" id="hidcodmun" value="">
<input name="hidcodpar" type="hidden" id="hidcodpar" value="">
<input name="hidcodestnac" type="hidden" id="hidcodestnac" value="">
<table width="823" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
<td width="823">       
  <div id="demo" class="yui-navset">
    <ul class="yui-nav">
        <li class="selected"><a href="#tab1"><em>Datos Personales</em></a></li>
        <li><a href="#tab2"><em>Beneficiarios</em></a></li>
    </ul>            
    <div class="yui-content">
	<div><p>
		<table width="769" border="0" align="center" cellpadding="1" cellspacing="0" class="formato-blanco">
        <tr class="titulo-ventana">
          <td height="20" colspan="3" class="titulo-ventana">Ficha de Jubilado  </td>
        </tr>
        <tr>
          <td height="20" colspan="3" class="titulo-celdanew">Informaci&oacute;n</td>
        </tr>
        <tr class="formato-blanco">
          <td height="20" colspan="3"><div align="center"></div></td>
        </tr>
        <tr>
          <td width="206" height="22"><div align="right"> C&oacute;digo</div></td>
          <td width="378"><div align="left">
              <input name="txtcodper" type="text" id="txtcodper" size="13" maxlength="10"   onKeyUp="javascript: ue_validarnumero(this);" onBlur="javascript: ue_rellenarcampo(this,10);">
              <input name="txtestper" type="text" class="sin-borde2" id="txtestper" style="text-align: center"  readonly> <input name="hidstatus" type="hidden" id="hidstatus">
          </div></td>
          <td width="179" rowspan="5"><div align="center"><img id="foto" name="foto" src="../../../fotos/silueta.jpg" width="150" height="200" ></div></td>
        </tr>
		 
        <tr>
          <td height="22"><div align="right">(*) C&eacute;dula</div></td>
          <td><div align="left">
              <input name="txtcedper" type="text" id="txtcedper" size="13" maxlength="8"  onKeyUp="javascript: ue_validarnumero(this)" >
          </div> </td> 
        </tr>
        <tr>
          <td height="22"><div align="right">(*) Nombre </div></td>
          <td><div align="left">
              <input name="txtnomper" type="text" id="txtnomper" size="63" maxlength="60"  onKeyUp="javascript: ue_validarcomillas(this);">
          </div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">(*) Apellido</div></td>
          <td><div align="left">
              <input name="txtapeper" type="text" id="txtapeper"  size="63" maxlength="60" onKeyUp="javascript: ue_validarcomillas(this);">
          </div></td>
        </tr>
		 <tr>
          <td height="20"><div align="right">(*) Tipo de Personal </div></td>
          <td height="20" colspan="2"><label>
            <input name="txtcodtippersss" type="text" id="txtcodtippersss"  size="15" maxlength="10"  readonly>
           <a href="javascript: ue_buscartipopersonalsss();"></a>&nbsp;
            
            <input name="txtdestippersss" type="text" class="sin-borde" id="txtdestippersss"  size="45" maxlength="50" readonly>
            </label></td>
        </tr>
		
		 <tr>
        <td height="22"><div align="right">Cargo Actual</div></td>
        <td colspan="3"><div align="left">
            <input name="txtcaract" type="text" id="txtcaract"  size="100"  readonly>
        </div></td>
      </tr>
	   
	  <tr>
        <td height="22"><div align="right">Unidad Administrativa Actual</div></td>
        <td colspan="3"><div align="left">
            <input name="txtuniadm" type="text" id="txtuniadm"   size="100"  readonly>
        </div></td>
      </tr> 
		  <tr>
          <td height="20" colspan="3" class="titulo-celdanew"><div align="center">Datos Personales</div></td>
        </tr>
      
        <tr>
          <td height="22"><div align="right">(*) Fecha de Nacimiento</div></td>
          <td>
            <div align="left">
              <input name="txtfecnacper" type="text" id="txtfecnacper"  size="18" style="text-align:justify"  readonly>
            </div></td>
        </tr>
        
		 <tr>
          <td height="22"><div align="right">(*) Pa&iacute;s</div></td>
          <td colspan="2"> <div align="left">
            <select name="cmbcodpai" id="cmbcodpai" style="width:145px" disabled>
              <option value="null">Seleccione un Pais</option>
            </select>
          </div></td>
		 </tr>
        <tr>
          <td height="22"><div align="right">(*) Estado</div></td>
          <td colspan="2" ><div align="left">
          <select name="cmbcodest" id="cmbcodest" style="width:145px" disabled>
                  <option value="null">Seleccione un Estado</option>
               </select>
               </div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">(*) Municipio</div></td>
          <td colspan="2"><div align="left">  <select name="cmbcodmun" id="cmbcodmun" style="width:145px" disabled>
                  <option value="null">Seleccione un Municipio</option> 
               </select> </div> </td>
        </tr>
        <tr>
          <td height="22"><div align="right">(*) Parroquia</div></td>
          <td colspan="2"><div align="left">  <select name="cmbcodpar" id="cmbcodpar" style="width:145px" disabled>
                  <option value="null">Seleccione un Parroquia</option> 
               </select> 
          </div> </td>
        </tr>    

		  <tr>
          <td height="22"><div align="right">(*) Direcci&oacute;n</div></td>
          <td><div align="left">
              <input name="txtdirper" type="text" id="txtdirper"  size="63" maxlength="250" onKeyUp="javascript: ue_validarcomillas(this);">
          </div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Tel&eacute;fono de Habitaci&oacute;n</div></td>
          <td colspan="2"><div align="left">
              <input name="txttelhabper" type="text" id="txttelhabper"  size="18" maxlength="15" onKeyUp="javascript: ue_validartelefono(this);">
          </div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Tel&eacute;fono M&oacute;vil </div></td>
          <td colspan="2"><div align="left">
              <input name="txttelmovper" type="text" id="txttelmovper" size="18" maxlength="15" onKeyUp="javascript: ue_validartelefono(this);">
          </div></td>
        </tr>
		 
        <tr class="titulo-celdanew">
          <td height="20" colspan="3"><div align="center">Lugar de Nacimiento </div></td>
          </tr>
        <tr>
          <td height="20"><div align="right">Pais de Nacimiento </div></td>
          <td height="20" colspan="2"><div align="left">
            <select name="cmbcodpainac" id="cmbcodpainac" style="width:145px" disabled>
              <option value="null">Seleccione un Pais</option>
            </select>
          </div></td>
        </tr>
        <tr>
          <td height="20"><div align="right">Estado de Nacimiento </div></td>
          <td height="20" colspan="2"><div align="left">
            <select name="cmbcodestnac" id="cmbcodestnac" style="width:145px"  disabled>
              <option value="null">Seleccione un Estado</option>  
            </select>
          </div></td>
        </tr>
	    <tr>
          <td height="20" colspan="3" class="titulo-celdanew"><div align="center"></div></td>
        </tr>
		  <tr>
		    <td height="22"><div align="right">Situaci&oacute;n del Personal </div></td>
		    <td colspan="2"><div align="left">
		      <select name="cmbsituacion" id="cmbsituacion" disabled>
		        <option value="" selected>--Seleccione--</option>
		        <option value="1" >Ninguno</option>
		        <option value="2" >Fallecido</option>
		        <option value="3"  >Pensionado</option>
		        <option value="4"  >Jubilado</option>
		        <option value="5"  >Retiro</option>
		        </select>
		      </div></td>
		    </tr>
		  <tr>
          <td height="22"><div align="right">(*) Fecha de Ingreso a la Administraci&oacute;n P&uacute;blica </div></td>
          <td colspan="2">
            <div align="left">
              <input type="text" name="txtfecingadmpub" id="txtfecingadmpub" size="11" readonly>
            </div></td>
        </tr>
          <tr>
          <td height="22"><div align="right"> (*) A&ntilde;os de Servicio Previo </div></td>
          <td colspan="2"><div align="left">
            <input name="txtanoservpreper" type="text" id="txtanoservpreper" value="0" size="5" maxlength="2" style="text-align:right" onKeyUp="javascript: ue_validarnumero(this);">
          </div></td>
          </tr>  
		 
        <tr>
          <td height="22"><div align="right">(*) Fecha de Ingreso a la Instituci&oacute;n</div></td>
          <td colspan="2"> <div align="left">
            <input type="text" name="txtfecingper" id="txtfecingper" size="11" readonly>
          </div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Tiempo de Servicio en la Institucion</div></td>
          <td colspan="2"><div align="left">
            <input name="txtano" type="text" id="txtano" value="0" size="5" maxlength="2" style="text-align:right" readonly>
          A&ntilde;os
          <input name="txtmes" type="text" id="txtmes" value="0" size="5" maxlength="2" style="text-align:right" readonly>
          Meses 
          <input name="txtdia" type="text" id="txtdia" value="0" size="5" maxlength="2" style="text-align:right"readonly>
          D&iacute;as </div></td>
        </tr>
        <tr class="titulo-celdanew">
		  <td height="22" colspan="3"><div align="center">Informaci&oacute;n de Jubilaci&oacute;n </div></td>
		  </tr>
		<tr>
          <td height="22"><div align="right">Fecha de Jubilaci&oacute;n </div></td>
          <td colspan="2"> <div align="left">
            <input type="text" name="txtfecjubper" id="txtfecjubper" size="11" readonly>
          </div></td>
		</tr>
		<tr>
          <td height="22"><div align="right">Fecha F&eacute; de Vida </div></td>
          <td colspan="2">
            <div align="left">
              <input type="text" name="txtfecfevid" id="txtfecfevid" size="11" readonly>
              
              <input name="reset" type="reset" onclick="return showCalendar('txtfecfevid', '%d/%m/%Y');" value=" ... " />          
                      </div></td>
        </tr>
		<tr>
		  <td height="22"><div align="right">Primera Remuneraci&oacute;n</div></td>
		  <td colspan="2"><div align="left">
		    <input name="txtprirem" type="text" id="txtprirem" size="23" maxlength="20"  onKeyPress="return(ue_formatonumero(this,'.',',',event))" style="text-align:right">
		  </div></td>
		  </tr>
		<tr>
		  <td height="22"><div align="right">Ultima Remuneraci&oacute;n</div></td>
		  <td colspan="2"><div align="left"><input name="txtultrem" type="text" id="txtultrem" size="23" maxlength="20"  onKeyPress="return(ue_formatonumero(this,'.',',',event))" style="text-align:right">
</div></td>
		  </tr>
		<tr>
		  <td height="22"><div align="right">Porcentaje de Pensi&oacute;n</div></td>
		  <td colspan="2"><div align="left"><input name="txtporpen" type="text" id="txtporpen" size="23" maxlength="20"  onKeyPress="return(ue_formatonumero(this,'.',',',event))" style="text-align:right">
		    %</div></td>
		  </tr>
		<tr>
		  <td height="22"><div align="right">Monto de Pensi&oacute;n</div></td>
		  <td colspan="2"><div align="left"><input name="txtmonpen" type="text" id="txtmonpen" size="23" maxlength="20"  onKeyPress="return(ue_formatonumero(this,'.',',',event))" style="text-align:right"></div></td>
		  </tr>
		<tr>
          <td height="22"><div align="right">Tipo de Jubilaci&oacute;n</div></td>
          <td colspan="2">
            <div align="left">
            <select name="cmbtipjub" id="cmbtipjub">
              <option value="" selected>--Seleccione Uno--</option>
              <option value="R" <?php print $la_tipjub[0];?>>Regular</option>
              <option value="E" <?php print $la_tipjub[1];?>>Especial</option>
            </select>
			</div></td>
        </tr>        
      </table>
          <p>&nbsp;</p>
		   </p></div>
        <div><p><table width="677" border="0" align="center" cellpadding="1" cellspacing="0" class="formato-blanco">
        <tr>
          <td colspan="4"></td>
        </tr>
        <tr class="titulo-ventana">
          <td height="20" colspan="4" class="titulo-ventana">Registro de Beneficiario </td>
        </tr>
		<tr>
        <td  colspan="3"><div align="right" class="sin-borde2">C&oacute;digo Personal</div></td>
        <td align="right"><div align="left">
          <input name="txtcodigo" type="text" id="txtcodigo" size="28" class="sin-borde" readonly>
        </div></td>
      </tr>
	  <tr>
        <td  colspan="3" align="right"><div align="right" class="sin-borde2">Tipo Personal</div></td>
        <td  align="right"><div align="left"><input name="txtdescripcion" type="text" id="txtdescripcion" size="28" class="sin-borde" readonly></div></td>
      </tr>
        <tr>
          <td width="202" height="22"><div align="right">C&oacute;digo</div></td>
          <td colspan="3"><div align="left">
            <input name="txtcodben" type="text" id="txtcodben"  size="13" maxlength="10" onKeyUp="javascript: ue_validarnumero(this);" readonly>
          </div></td>
        </tr>
		<tr>
          <td height="22"><div align="right">C&eacute;dula</div></td>
          <td colspan="3"><div align="left">
            <input name="txtcedben" type="text" id="txtcedben"  size="13" maxlength="10" onKeyUp="javascript: ue_validarnumero(this);">
          </div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Nombre</div></td>
          <td colspan="3"><div align="left">
            <input name="txtnomben" type="text" id="txtnomben" onKeyUp="javascript: ue_validarcomillas(this);"  size="63" maxlength="60">
          </div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Apellido</div></td>
          <td colspan="3"><div align="left">
            <input name="txtapeben" type="text" id="txtapeben" onKeyUp="javascript: ue_validarcomillas(this);"  size="63" maxlength="60">
          </div></td>
        </tr>
		
		<tr>
          <td height="22"><div align="right">Nacionalidad</div></td>
          <td width="273"><div align="left">
            <select name="cmbnacben" id="cmbnacben">
              <option value="null" selected>--Seleccione Uno--</option>
              <option value="V" >Venezolano</option>
              <option value="E" >Extranjero</option>
            </select>
          </div></td>
        </tr>
		
		<tr>
          <td height="22"><div align="right">Direcci&oacute;n</div></td>
          <td colspan="3"><div align="left">
            <input name="txtdirben" type="text" id="txtdirben" onKeyUp="javascript: ue_validarcomillas(this);"  size="63" maxlength="60">
          </div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Tel&eacute;fono</div></td>
          <td colspan="3"><div align="left">
            <input name="txttelben" type="text" id="txttelben" onKeyUp="javascript:ue_validarnumero(this);"  size="20" maxlength="60">
          </div></td>
        </tr>
		
		<tr>
           <td height="22"><div align="right">Parentesco </div></td>
           <td><div align="left">
            <select name="cmbnexben" id="cmbnexben">
              <option value="-" selected>--Sin Parentesco--</option>
              <option value="C" >Conyuge</option>
              <option value="H" >Hijo</option>
              <option value="P">Progenitor</option>
              <option value="E">Hermano</option>
              <option value="V">Viuda(o)</option>
            </select>
          </div></td>
        </tr>
		
      <tr>
          <td height="22"><div align="right">Tipo de Beneficiario </div></td>
          <td><div align="left">
            <select name="cmbtipben" id="cmbtipben">
              <option value="null" selected>--Seleccione Uno--</option>
              <option value="0">Pension Sobrevivientes</option>
              <option value="1">Pension Judicial</option>
			  <option value="2">Pension Alimenticia</option>
            </select>
          </div></td>
        </tr>
		<tr>
          <td height="22"><div align="right">Expediente</div></td>
          <td colspan="3"><div align="left">
            <input name="txtnumexpben" type="text" id="txtnumexpben" onKeyUp="javascript: ue_validarcomillas(this);"  size="40" maxlength="40">
          </div></td>
        </tr>		
        <tr>
          <td height="22"><div align="right">Porcentaje que le corresponde</div></td>
          <td colspan="3"><div align="left">
            <input name="txtporpagben" type="text" id="txtporpagben" onKeyPress="return(ue_formatonumero(this,'.',',',event))"  onBlur="javascript: ue_limpiar('0');" style="text-align:right"  size="20" >
          </div></td>
        </tr>
		
		<tr>
          <td height="22"><div align="right">Monto que le corresponde</div></td>
          <td colspan="3"><div align="left">
            <input name="txtmonpagben" type="text" id="txtmonpagben" onKeyPress="return(ue_formatonumero(this,'.',',',event))"  onBlur="javascript: ue_limpiar('1');" style="text-align:right"  size="20" >
          </div></td>
        </tr>
		
		<tr>
          <td height="22"><div align="right">Forma de Pago </div></td>
          <td><select name="cmbforpagben" id="cmbforpagben">
            <option value="null" selected>--Seleccione Uno--</option>
            <option value="0"  >Cheque</option>
            <option value="1"  >Deposito en Cuenta</option>
                    </select></td>
        </tr>
		 <tr>
          <td height="22"><div align="right">Nombre del cheque</div></td>
          <td colspan="3"><div align="left">
            <input name="txtnomcheben" type="text" id="txtnomcheben" onKeyUp="javascript: ue_validarcomillas(this);"  size="63" maxlength="60">
          </div></td>
        </tr>
		<tr>
          <td height="22"><div align="right">C&eacute;dula del Autorizado</div></td>
          <td><div align="left">
            <input name="txtcedaut" type="text" id="txtcedaut"  size="13" maxlength="10" onKeyUp="javascript: ue_validarnumero(this);">
          </div></td>
        </tr>
		
		 <tr>
          <td height="22"><div align="right">Banco</div></td>
          <td colspan="4"><div align="left">
            <input name="txtcodban" type="text" id="txtcodban"  size="7" maxlength="4" readonly>
            <a href="javascript: ue_buscarbanco();"><img id="banco" src="../../../../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a>
            <input name="txtnomban" type="text" class="sin-borde" id="txtnomban"  size="50" readonly>
</div></td>
        </tr>
		
		 <tr>
          <td height="22"><div align="right">Cuenta de Banco</div></td>
          <td colspan="3"><div align="left">
            <input name="txtctaban" type="text" id="txtctaban" onKeyUp="javascript: ue_validarnumero(this);"  size="30" maxlength="60">
          </div></td>
        </tr>
		
		 <tr>
          <td height="22"><div align="right">Tipo de Cuenta</div></td>
          <td><select name="cmbtipcueben" id="cmbtipcueben">
            <option value="" selected>--Seleccione Una--</option>
            <option value="A" >Ahorro</option>
            <option value="C" >Corriente</option>
            <option value="L" >Activos L&iacute;quidos</option>
          </select></td>
        </tr>
       
        <tr>
          <td height="22">&nbsp;</td>
          <td colspan="3">&nbsp;</td>
        </tr>
		<tr>
         <td height="22"><div align="left"><a href="javascript: ue_limpiar_beneficiario();ue_nuevo_beneficiario();"><img src="../../../../shared/imagebank/tools20/nuevo.gif" alt="Grabar" width="20" height="20" border="0">Nuevo Beneficiario</a></div></td>
        <td colspan="2">
			<a href="javascript: ue_guardar_beneficiarios();"><img src="../../../../shared/imagebank/tools20/grabar.gif" alt="Grabar" width="20" height="20" border="0">Guardar Beneficiario </a>
			<a href="javascript: ue_eliminar_beneficiario();"></a></td>
		  <td width="195"><span class="toolbar"><a href="javascript: ue_buscar_beneficiarios();"><img src="../../../../shared/imagebank/tools20/buscar.gif" alt="Grabar" width="20" height="20" border="0">Buscar Beneficiarios </a></span></td>
      </tr>
      </table>
          <p>&nbsp;</p>
          </p></div>
	  </div></div>
    </tr>
 </table>
<script>
(
	function()
	{
		var tabView = new YAHOO.widget.TabView('demo');
	}
)();
</script>
</form>      
<p>&nbsp;</p>
</body> 
</html>