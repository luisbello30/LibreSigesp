<?php 
session_start(); ?>
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
<title>Asignaci&oacute;n</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<script type="text/javascript" language="JavaScript1.2" src="../shared/js/number_format.js"></script>
<script type="text/javascript" language="javascript1.2" src="../shared/js/valida_tecla.js"></script>
<script type="text/javascript" language="javascript1.2" src="js/valida_tecla_grid.js"></script>
<style type="text/css">
<!--
.Estilo1 {font-size: 15px}
-->
</style>
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../sno/css/nomina.css" rel="stylesheet" type="text/css">
</head>

<body>
<?php
require_once("../shared/class_folder/sigesp_include.php");
$in = new sigesp_include();
$con= $in-> uf_conectar ();
require_once("../shared/class_folder/class_mensajes.php");
$msg=new class_mensajes();
require_once("../shared/class_folder/class_datastore.php");
$ds_prorep=new class_datastore();

if(array_key_exists("operacion",$_POST))
{
$ls_operacion=$_POST["operacion"];
}
else
{
$ls_operacion="";
}
   $ls_cuenta=$_GET["txtCuenta"];
   $ls_denominacion=$_GET["txtDenominacion"];
   $i=$_GET["fila"];
   $ls_codestpro1 = $_GET["codestpro1"];
   $ls_codestpro2 = $_GET["codestpro2"];
   $ls_codestpro3 = $_GET["codestpro3"];
   $ls_codestpro4 = $_GET["codestpro4"];
   $ls_codestpro5 = $_GET["codestpro5"];
   $ls_denestpro1 = $_GET["denestpro1"];
   $ls_denestpro2 = $_GET["denestpro2"];
   $ls_denestpro3 = $_GET["denestpro3"];
   $ls_denestpro4 = $_GET["denestpro4"];
   $ls_denestpro5 = $_GET["denestpro5"];
   $ld_asignado=$_GET["txtAsignacion"];
   $ls_readonly="readonly";
   $ld_total=$ld_asignado;
   $ld_enero=$_GET["enero"];
   $ld_febrero=$_GET["febrero"];
   $ld_marzo=$_GET["marzo"];
   $ld_abril=$_GET["abril"];
   $ld_mayo=$_GET["mayo"];
   $ld_junio=$_GET["junio"];
   $ld_julio=$_GET["julio"];
   $ld_agosto=$_GET["agosto"];
   $ld_septiembre=$_GET["septiembre"];
   $ld_octubre=$_GET["octubre"];
   $ld_noviembre=$_GET["noviembre"];
   $ld_diciembre=$_GET["diciembre"];
   if (array_key_exists("txtTotal",$_POST))
   {
	$ld_total=$_POST["txtTotal"];
   }
   else
   {
	 $ld_total="0,00";
   }
   if (array_key_exists("txtDiferencia",$_POST))
   {
	$ld_diferencia=$_POST["txtDiferencia"];
   }
   else
   {
	 $ld_diferencia="0,00";
   }
?>
<form name="form1" method="post" action="">
  <p>&nbsp;</p>
  <table width="650" height="368" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
    <tr class="titulo-ventana">
      <td colspan="4"><div align="left" class="titulo-ventana">
        <div align="center" class="titulo">
          <div align="left" class="titulo-ventana">
            <div align="center">Asignaci&oacute;n</div>
          </div>
        </div>
      </div></td>
    </tr>
    <tr>
      <?php 
	  $li_estmodest  = $_SESSION["la_empresa"]["estmodest"];
	  $ls_loncodestpro1 = $_SESSION["la_empresa"]["loncodestpro1"];
	  $ls_loncodestpro2 = $_SESSION["la_empresa"]["loncodestpro2"];
	  $ls_loncodestpro3 = $_SESSION["la_empresa"]["loncodestpro3"];
	  $ls_loncodestpro4 = $_SESSION["la_empresa"]["loncodestpro4"];
	  $ls_loncodestpro5 = $_SESSION["la_empresa"]["loncodestpro5"];
	  if($li_estmodest==1)
	  {
	  ?>
      <td height="22" width="134"><div align="right"><?php print $_SESSION["la_empresa"]["nomestpro1"];  ?></div></td>
      <td colspan="3"><input name="codestpro1" type="text" id="codestpro1" size="<?php print $ls_loncodestpro1; ?>" maxlength="<?php print $ls_loncodestpro1; ?>" style="text-align:center" value="<?php print substr($ls_codestpro1,-$ls_loncodestpro1); ?>" readonly>
          <input name="denestpro1" type="text" class="sin-borde" id="denestpro1" size="75" value="<?php echo $ls_denestpro1; ?>" readonly>
          <div align="left"> </div></td>
    </tr>
    <tr>
      <td height="22"><div align="right"><?php print $_SESSION["la_empresa"]["nomestpro2"] ; ?></div></td>
      <td colspan="3"><input name="codestpro2" type="text" id="codestpro2" size="<?php print $ls_loncodestpro2; ?>" maxlength="<?php print $ls_loncodestpro2; ?>" style="text-align:center" value="<?php print substr($ls_codestpro2,-$ls_loncodestpro2); ?>" readonly>
          <input name="denestpro2" type="text" class="sin-borde" id="denestpro2" size="75" value="<?php echo $ls_denestpro2; ?>" readonly></td>
    </tr>
    <tr>
      <td height="22"><div align="right"><?php print $_SESSION["la_empresa"]["nomestpro3"] ; ?></div></td>
      <td colspan="3"><div align="left">
          <input name="codestpro3" type="text" id="codestpro3" size="<?php print $ls_loncodestpro3; ?>" maxlength="<?php print $ls_loncodestpro3; ?>" style="text-align:center" value="<?php print substr($ls_codestpro3,-$ls_loncodestpro3); ?>" readonly>
          <input name="denestpro3" type="text" class="sin-borde" id="denestpro3" size="75" value="<?php echo $ls_denestpro3; ?>" readonly>
      </div></td>
    </tr>
  <? }
     elseif($li_estmodest==2)
     {	
  ?>
    <td height="22"><div align="right"><?php print $_SESSION["la_empresa"]["nomestpro1"];  ?></div></td>
        <td colspan="3"><input name="codestpro1" type="text" id="codestpro1" size="<?php print $ls_loncodestpro1; ?>" maxlength="<?php print $ls_loncodestpro1; ?>" style="text-align:center" value="<?php print substr($ls_codestpro1,-$ls_loncodestpro1); ?>" readonly>
            <input name="denestpro1" type="text" class="sin-borde" id="denestpro1" size="75" value="<?php echo $ls_denestpro1; ?>" readonly>
            <div align="left"> </div></td>
    </tr><tr>
      <td height="22"><div align="right"><?php print $_SESSION["la_empresa"]["nomestpro2"] ; ?></div></td>
      <td colspan="3"><input name="codestpro2" type="text" id="codestpro2" size="<?php print $ls_loncodestpro2; ?>" maxlength="<?php print $ls_loncodestpro2; ?>" style="text-align:center" value="<?php print substr($ls_codestpro2,-$ls_loncodestpro2); ?>" readonly>
          <input name="denestpro2" type="text" class="sin-borde" id="denestpro2" size="75" value="<?php echo $ls_denestpro2; ?>" readonly></td>
    </tr>
    <tr>
      <td height="22"><div align="right"><?php print $_SESSION["la_empresa"]["nomestpro3"] ; ?></div></td>
      <td colspan="3"><div align="left">
          <input name="codestpro3" type="text" id="codestpro3" size="<?php print $ls_loncodestpro3; ?>" maxlength="<?php print $ls_loncodestpro3; ?>" style="text-align:center" value="<?php print substr($ls_codestpro3,-$ls_loncodestpro3); ?>" readonly>
          <input name="denestpro3" type="text" class="sin-borde" id="denestpro3" size="75" value="<?php echo $ls_denestpro3; ?>" readonly>
      </div></td>
    </tr>
    <tr>
      <td height="22"><div align="right"><?php print $_SESSION["la_empresa"]["nomestpro4"] ; ?></div></td>
      <td colspan="3"><div align="left">
          <input name="codestpro4" type="text" id="codestpro4" size="<?php print $ls_loncodestpro4; ?>" maxlength="<?php print $ls_loncodestpro4; ?>" style="text-align:center" value="<?php print substr($ls_codestpro4,-$ls_loncodestpro4); ?>"readonly>
          <input name="denestpro4" type="text" class="sin-borde" id="denestpro4" size="75" value="<?php echo $ls_denestpro4; ?>" readonly>
      </div></td>
    </tr>
    <tr>
      <td height="22"><div align="right"><?php print $_SESSION["la_empresa"]["nomestpro5"] ; ?></div></td>
      <td colspan="3"><div align="left">
          <input name="codestpro5" type="text" id="codestpro5" size="<?php print $ls_loncodestpro5 ?>" maxlength="<?php print $ls_loncodestpro5 ?>" style="text-align:center" value="<?php print substr($ls_codestpro5,-$ls_loncodestpro5); ?>" readonly>
          <input name="denestpro5" type="text" class="sin-borde" id="denestpro5" size="75" value="<?php echo $ls_denestpro5; ?>" readonly>
      </div></td>
    </tr>
    <?php
	}
	?>
    <tr>
      <td class="fd-blanco">&nbsp;</td>
      <td colspan="3">&nbsp;</td>
    </tr>
    <tr>
      <td height="18" class="fd-blanco"><div align="right">Cuenta</div></td>
      <td class="sin-borde3"><div align="left">
        <input name="txtcuenta" type="text" class="sin-borde3" id="txtcuenta" value="    <?php print   $ls_cuenta ?>">
      </div></td>
      <td class="fd-blanco">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td class="fd-blanco">&nbsp;</td>
      <td colspan="3">&nbsp;</td>
    </tr>
    <tr>
      <td class="fd-blanco"><div align="right">Denominaci&oacute;n</div></td>
      <td colspan="3"><div align="left"><span class="fd-blanco"><span class="sin-borde3">
        <input name="txtdenominacion2" type="text" class="sin-borde3" id="txtdenominacion" value="   <?php print   $ls_denominacion  ?>" size="90" maxlength="150">
      </span></span></div></td>
    </tr>
    <tr>
      <td class="fd-blanco">&nbsp;</td>
      <td>&nbsp;</td>
      <td class="fd-blanco">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td height="22"><div align="right">Distribuci&oacute;n</div></td>
      <td><input name="radiobutton" type="radio" value="A" <?php print $ls_auto;?> onClick='ue_distribucion("A")'>
        Autom&aacute;tica</td>
      <td><input name="radiobutton" type="radio" value="M" <?php print $ls_manual;?> onClick='ue_distribucion("M")'>
        Manual </td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td class="fd-blanco">&nbsp;</td>
      <td>&nbsp;</td>
      <td class="fd-blanco">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td class="fd-blanco">&nbsp;</td>
      <td class="fd-blanco"><div align="right">Asignado</div></td>
      <td><input name="txtAsignacion" type="text" class="fd-blanco" id="txtAsignacion"  onKeyPress='return keyRestrictgrid(event)' value="<?php print $ld_asignado ?>" size="25" maxlength="25" readonly style="text-align:right"></td>
      <td class="fd-blanco">&nbsp;</td>
    </tr>
    <tr>
      <td class="fd-blanco">&nbsp;</td>
      <td>&nbsp;</td>
      <td width="67" class="fd-blanco">&nbsp;</td>
      <td width="301">&nbsp;</td>
    </tr>
    <tr>
      <td width="134" class="fd-blanco"><div align="right">Enero</div></td>
      <td width="146">
        <div align="left">
          <input name="txtEnero" type="text" class="fd-blanco" id="txtEnero" onBlur="uf_actualizar(this)" onKeyPress='return keyRestrictgrid(event)' onKeyUp='ue_validarcomas_puntos(this)' value="<?php print $ld_enero ?>" size="25" maxlength="25" style="text-align:right" <?php print $ls_readonly; ?>>
</div></td><td class="fd-blanco"><div align="right">Julio</div></td>
      <td><input name="txtJulio" type="text" class="fd-blanco" id="txtJulio" onBlur="uf_actualizar(this)"  onKeyPress='return keyRestrictgrid(event)' onKeyUp='ue_validarcomas_puntos(this)' value="<?php print $ld_julio?>" size="25" maxlength="25" style="text-align:right" <?php print $ls_readonly; ?>>        <div align="right">
        </div></td>
    </tr>
    <tr>
      <td class="fd-blanco Estilo1">&nbsp;</td>
      <td><span class="Estilo1"></span></td>
      <td class="fd-blanco Estilo1">&nbsp;</td>
      <td><span class="Estilo1"></span><span class="Estilo1"></span></td>
    </tr>
    <tr>
      <td class="fd-blanco"><div align="right">Febrero</div></td>
      <td><input name="txtFebrero" type="text" class="fd-blanco" id="txtFebrero" onBlur="uf_actualizar(this)"  onKeyPress='return keyRestrictgrid(event)' onKeyUp='ue_validarcomas_puntos(this)' value="<?php  print $ld_febrero?>" size="25" maxlength="25" style="text-align:right" <?php print $ls_readonly; ?>></td>
      <td class="fd-blanco"><div align="right">Agosto</div></td>
      <td><input name="txtAgosto" type="text" class="fd-blanco" id="txtAgosto" onBlur="uf_actualizar(this)"  onKeyPress='return keyRestrictgrid(event)' onKeyUp='ue_validarcomas_puntos(this)' value="<?php  print $ld_agosto?>" size="25" maxlength="25" style="text-align:right" <?php print $ls_readonly; ?>></td>
    </tr>
    <tr>
      <td class="fd-blanco Estilo1">&nbsp;</td>
      <td><span class="Estilo1"></span></td>
      <td class="fd-blanco Estilo1">&nbsp;</td>
      <td><span class="Estilo1"></span><span class="Estilo1"></span></td>
    </tr>
    <tr>
      <td class="fd-blanco"><div align="right">Marzo</div></td>
      <td><input name="txtMarzo" type="text" class="fd-blanco" id="txtMarzo" onBlur="uf_actualizar(this)"  onKeyPress='return keyRestrictgrid(event)' onKeyUp='ue_validarcomas_puntos(this)' value="<?php print $ld_marzo?>" size="25" maxlength="25" style="text-align:right" <?php print $ls_readonly; ?>></td>
      <td class="fd-blanco"><div align="right">Septiembre</div></td>
      <td><input name="txtSeptiembre" type="text" class="fd-blanco" id="txtSeptiembre" onBlur="uf_actualizar(this)" onKeyPress='return keyRestrictgrid(event)'onKeyUp='ue_validarcomas_puntos(this)' value="<?php print $ld_septiembre?>" size="25" maxlength="25" style="text-align:right" <?php print $ls_readonly; ?>></td>
    </tr>
    <tr>
      <td class="fd-blanco Estilo1">&nbsp;</td>
      <td><span class="Estilo1"></span></td>
      <td class="fd-blanco Estilo1">&nbsp;</td>
      <td><span class="Estilo1"></span><span class="Estilo1"></span></td>
    </tr>
    <tr>
      <td class="fd-blanco"><div align="right">Abril</div></td>
      <td><input name="txtAbril" type="text" class="fd-blanco" id="txtAbril" onBlur="uf_actualizar(this)"  onKeyPress='return keyRestrictgrid(event)'  onKeyUp='ue_validarcomas_puntos(this)' value="<?php print $ld_abril?>" size="25" maxlength="25" style="text-align:right" <?php print $ls_readonly; ?>></td>
      <td class="fd-blanco"><div align="right">Octubre</div></td>
      <td><input name="txtOctubre" type="text" class="fd-blanco" id="txtOctubre" onBlur="uf_actualizar(this)"  onKeyPress='return keyRestrictgrid(event)' onKeyUp='ue_validarcomas_puntos(this)' value="<?php print $ld_octubre?>" size="25" maxlength="25" style="text-align:right" <?php print $ls_readonly; ?>></td>
    </tr>
    <tr>
      <td class="fd-blanco Estilo1">&nbsp;</td>
      <td><span class="Estilo1"></span></td>
      <td class="fd-blanco Estilo1">&nbsp;</td>
      <td><span class="Estilo1"></span><span class="Estilo1"></span></td>
    </tr>
    <tr>
      <td class="fd-blanco"><div align="right">Mayo</div></td>
      <td><input name="txtMayo" type="text" class="fd-blanco" id="txtMayo" onBlur="uf_actualizar(this)" onKeyPress='return keyRestrictgrid(event)' onKeyUp='ue_validarcomas_puntos(this)' value="<?php print $ld_mayo?>" size="25" style="text-align:right" <?php print $ls_readonly; ?>></td>
      <td class="fd-blanco"><div align="right">Noviembre</div></td>
      <td><input name="txtNoviembre" type="text" class="fd-blanco" id="txtNoviembre" onBlur="uf_actualizar(this)" onKeyPress='return keyRestrictgrid(event)' onKeyUp='ue_validarcomas_puntos(this)' value="<?php print $ld_noviembre?>" size="25" maxlength="25" style="text-align:right" <?php print $ls_readonly; ?>></td>
    </tr>
    <tr>
      <td class="fd-blanco Estilo1">&nbsp;</td>
      <td><span class="Estilo1"></span></td>
      <td class="fd-blanco Estilo1">&nbsp;</td>
      <td><span class="Estilo1"></span><span class="Estilo1"></span></td>
    </tr>
    <tr>
      <td class="fd-blanco"><div align="right">Junio </div></td>
      <td><input name="txtJunio" type="text" class="fd-blanco" id="txtJunio" onBlur="uf_actualizar(this)" onKeyPress='return keyRestrictgrid(event)' onKeyUp='ue_validarcomas_puntos(this)' value="<?php print $ld_junio?>" size="25" maxlength="25" style="text-align:right" <?php print $ls_readonly; ?>></td>
      <td class="fd-blanco"><div align="right">Diciembre</div></td>
      <td><input name="txtDiciembre" type="text" class="fd-blanco" id="txtDiciembre" onBlur="uf_actualizar(this)" onKeyPress='return keyRestrictgrid(event)'  onKeyUp='ue_validarcomas_puntos(this)'value="<?php print $ld_diciembre?>" size="25" maxlength="25" style="text-align:right" <?php print $ls_readonly; ?>></td>
    </tr>
    <tr>
      <td class="fd-blanco Estilo1">&nbsp;</td>
      <td>&nbsp;</td>
      <td class="fd-blanco Estilo1">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td class="fd-blanco"><div align="right">Total Distribuido</div></td>
      <td><input name="txtTotal" type="text" class="texto-azul" id="txtTotal2"  onKeyPress='return keyRestrictgrid(event)' value="<?php print $ld_total ?>" size="25" maxlength="25" readonly style="text-align:right"></td>
      <td class="fd-blanco"><div align="right">Por Distribuir</div></td>
      <td><input name="txtDiferencia" type="text" class="texto-rojo" id="txtDiferencia"  onKeyPress='return keyRestrictgrid(event)' value="<?php print $ld_diferencia ?>" size="25" maxlength="25" readonly style="text-align:right"></td>
    </tr>
    <tr>
      <td class="fd-blanco">&nbsp;</td>
      <td>&nbsp;</td>
      <td colspan="2" rowspan="4"><input name="botAceptar" type="button" class="boton" id="botAceptar" onClick="ue_aceptar()" value="Aceptar">
          <input name="botCancelar" type="button" class="boton" id="botCancelar" onClick="ue_cancelar()" value="Cancelar">
          <input name="operacion" type="hidden" id="operacion" value="<?php print $ls_operacion?>">
          <input name="fila" type="hidden" id="fila" value="<?php print $i?>">
          <input name="tipo" type="hidden" id="tipo" value="<?php print $ls_tipo ?>"></td>
    </tr>

    <tr>
      <td class="fd-blanco Estilo1">&nbsp;</td>
      <td><span class="Estilo1"></span></td>
    </tr>
    <tr>
      <td class="fd-blanco">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td class="fd-blanco">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
  </table>
  <p>&nbsp;</p>
</form>
<script language="javascript">
function ue_aceptar()
{
	f=document.form1;
    li=f.fila.value;
	opcion=f.tipo.value;
	ld_asignado=f.txtAsignacion.value  
	ld_asignado=parseFloat(uf_convertir_monto(ld_asignado));

	ld_m1=f.txtEnero.value; 
	ld_m1=parseFloat(uf_convertir_monto(ld_m1));
	
	ld_m2=f.txtFebrero.value;    
	ld_m2=parseFloat(uf_convertir_monto(ld_m2));
	
	ld_m3=f.txtMarzo.value;    
	ld_m3=parseFloat(uf_convertir_monto(ld_m3));
	
	ld_m4=f.txtAbril.value; 
	ld_m4=parseFloat(uf_convertir_monto(ld_m4));

	ld_m5=f.txtMayo.value;
	ld_m5=parseFloat(uf_convertir_monto(ld_m5));

	ld_m6=f.txtJunio.value;
	ld_m6=parseFloat(uf_convertir_monto(ld_m6));

	ld_m7=f.txtJulio.value;  
	ld_m7=parseFloat(uf_convertir_monto(ld_m7));

	ld_m8=f.txtAgosto.value;      
	ld_m8=parseFloat(uf_convertir_monto(ld_m8));

	ld_m9=f.txtSeptiembre.value;           
	ld_m9=parseFloat(uf_convertir_monto(ld_m9));

	ld_m10=f.txtOctubre.value;           
	ld_m10=parseFloat(uf_convertir_monto(ld_m10));

	ld_m11=f.txtNoviembre.value;    
	ld_m11=parseFloat(uf_convertir_monto(ld_m11));

	ld_m12=f.txtDiciembre.value;       
	ld_m12=parseFloat(uf_convertir_monto(ld_m12));

	ld_total = parseFloat(ld_m1 + ld_m2 + ld_m3 + ld_m4 + ld_m5 + ld_m6 +ld_m7 + ld_m8 + ld_m9 + ld_m10 + ld_m11 + ld_m12);
	if(opcion=="A")
	{
	  //total=redondear2(ld_total,2);
	  total=redondear2(ld_total);
	  ld_total=total
	}
	if (ld_total!=ld_asignado)
	{
	  alert(" La Distribución no cuadra con lo asignado. Por favor revise los montos ");
	}
	else
	{	
        txtm1 = "txtEnero"+li;
        eval("opener.document.form1."+txtm1+".value='"+ld_m1+"'");
        txtm1 = "txtFebrero"+li;
        eval("opener.document.form1."+txtm1+".value='"+ld_m2+"'");
        txtm1 = "txtMarzo"+li;
        eval("opener.document.form1."+txtm1+".value='"+ld_m3+"'");
        txtm1 = "txtAbril"+li;
        eval("opener.document.form1."+txtm1+".value='"+ld_m4+"'");
        txtm1 = "txtMayo"+li;
        eval("opener.document.form1."+txtm1+".value='"+ld_m5+"'");
        txtm1 = "txtJunio"+li;
        eval("opener.document.form1."+txtm1+".value='"+ld_m6+"'");
        txtm1 = "txtJulio"+li;
        eval("opener.document.form1."+txtm1+".value='"+ld_m7+"'");
        txtm1 = "txtAgosto"+li;
        eval("opener.document.form1."+txtm1+".value='"+ld_m8+"'");
        txtm1 = "txtSeptiembre"+li;
        eval("opener.document.form1."+txtm1+".value='"+ld_m9+"'");
        txtm1 = "txtOctubre"+li;
        eval("opener.document.form1."+txtm1+".value='"+ld_m10+"'");
        txtm1 = "txtNoviembre"+li;
        eval("opener.document.form1."+txtm1+".value='"+ld_m11+"'");
        txtm1 = "txtDiciembre"+li;
        eval("opener.document.form1."+txtm1+".value='"+ld_m12+"'");
	    opener.document.form1.fila.value=li;
        close();
	}
}
function ue_cancelar()
{
	if(confirm("El proceso será cancelado, ¿está de acuerdo?"))
	{
	 close();
	}
}
function redondear(num, dec)
{ 
    num = parseFloat(num); 
    dec = parseFloat(dec); 
    dec = (!dec ? 2 : dec); 
    return Math.round(num * Math.pow(10, dec)) / Math.pow(10, dec); 
}
function uf_actualizar(obj)
{
		f=document.form1;
		/*if(obj.value=="")
		{
		  obj.value="0,00";
		}*/
		ldec_temp1=obj.value;
	    if((ldec_temp1=="")||(ldec_temp1==".")||(ldec_temp1==","))
		{
		  ldec_temp1="0";
		}
		obj.value=uf_convertir(ldec_temp1);
		ld_asignado=f.txtAsignacion.value  
		ld_asignado=parseFloat(uf_convertir_monto(ld_asignado));
 
 		ld_m1=f.txtEnero.value; 
		ld_m1=parseFloat(uf_convertir_monto(ld_m1));
		
		ld_m2=f.txtFebrero.value;    
		ld_m2=parseFloat(uf_convertir_monto(ld_m2));
		
		ld_m3=f.txtMarzo.value;    
		ld_m3=parseFloat(uf_convertir_monto(ld_m3));
		
		ld_m4=f.txtAbril.value; 
		ld_m4=parseFloat(uf_convertir_monto(ld_m4));
	
		ld_m5=f.txtMayo.value;
		ld_m5=parseFloat(uf_convertir_monto(ld_m5));
	
		ld_m6=f.txtJunio.value;
		ld_m6=parseFloat(uf_convertir_monto(ld_m6));
	
		ld_m7=f.txtJulio.value;  
		ld_m7=parseFloat(uf_convertir_monto(ld_m7));
	
		ld_m8=f.txtAgosto.value;      
		ld_m8=parseFloat(uf_convertir_monto(ld_m8));
	
		ld_m9=f.txtSeptiembre.value;           
		ld_m9=parseFloat(uf_convertir_monto(ld_m9));
	
		ld_m10=f.txtOctubre.value;           
		ld_m10=parseFloat(uf_convertir_monto(ld_m10));
	
		ld_m11=f.txtNoviembre.value;    
		ld_m11=parseFloat(uf_convertir_monto(ld_m11));
	
		ld_m12=f.txtDiciembre.value;       
		ld_m12=parseFloat(uf_convertir_monto(ld_m12));
	
		ld_total = parseFloat(ld_m1 + ld_m2 + ld_m3 + ld_m4 + ld_m5 + ld_m6 +ld_m7 + ld_m8 + ld_m9 + ld_m10 + ld_m11 + ld_m12);
		//ld_total=redondear(ld_total,2);
		ld_total=redondear2(ld_total);
		
		ld_diferencia = parseFloat(ld_asignado - ld_total);
		if (ld_total>ld_asignado)
		{
		  alert(" El Total es mayor al monto asignado. Por favor revise los montos ");
		}
		else
		{	
			f.txtTotal.value=ld_total;
			ld_total=uf_convertir(f.txtTotal.value);
			f.txtTotal.value=ld_total;
			f.txtDiferencia.value =uf_convertir(ld_diferencia);
		}	
}

function redondear(num, dec)
{ 
    num = parseFloat(num); 
    dec = parseFloat(dec); 
    dec = (!dec ? 2 : dec); 
    return Math.round(num * Math.pow(10, dec)) / Math.pow(10, dec); 
}

function valida_null(field,valor)
{
  with (field) 
  {
    if (value==null||value=="")
      {
        //alert(mensaje);
		field=valor;
        return true;
      }
  }
  
  function EvaluateText(cadena, obj)
  { 
	
    opc = false; 
	
    if (cadena == "%d")  
      if ((event.keyCode > 64 && event.keyCode < 91)||(event.keyCode > 96 && event.keyCode < 123)||(event.keyCode ==32))  
      opc = true; 
    if (cadena == "%f")
	{ 
     if (event.keyCode > 47 && event.keyCode < 58) 
      opc = true; 
     if (obj.value.search("[.*]") == -1 && obj.value.length != 0) 
      if (event.keyCode == 46) 
       opc = true; 
    } 
	 if (cadena == "%s") // toma numero y letras
     if ((event.keyCode > 64 && event.keyCode < 91)||(event.keyCode > 96 && event.keyCode < 123)||(event.keyCode ==32)||(event.keyCode > 47 && event.keyCode < 58)||(event.keyCode ==46)) 
      opc = true; 
	 if (cadena == "%c") // toma numero y punto
     if ((event.keyCode > 47 && event.keyCode < 58)|| (event.keyCode ==46))
      opc = true; 
    if(opc == false) 
     event.returnValue = false; 
   } 
 }  
 //--------------------------------------------------------
//	Función que formatea un número
//--------------------------------------------------------
function ue_formatonumero(fld, milSep, decSep, e)
{ 
	var sep = 0; 
    var key = ''; 
    var i = j = 0; 
    var len = len2 = 0; 
    var strCheck = '0123456789'; 
    var aux = aux2 = ''; 
    var whichCode = (window.Event) ? e.which : e.keyCode; 

	if (whichCode == 13) return true; // Enter 
	if (whichCode == 8) return true; // Return
    key = String.fromCharCode(whichCode); // Get key value from key code 
    if (strCheck.indexOf(key) == -1) return false; // Not a valid key 
    len = fld.value.length; 
    for(i = 0; i < len; i++) 
    	if ((fld.value.charAt(i) != '0') && (fld.value.charAt(i) != decSep)) break; 
    aux = ''; 
    for(; i < len; i++) 
    	if (strCheck.indexOf(fld.value.charAt(i))!=-1) aux += fld.value.charAt(i); 
    aux += key; 
    len = aux.length; 
    if (len == 0) fld.value = ''; 
    if (len == 1) fld.value = '0'+ decSep + '0' + aux; 
    if (len == 2) fld.value = '0'+ decSep + aux; 
    if (len > 2) { 
     aux2 = ''; 
     for (j = 0, i = len - 3; i >= 0; i--) { 
      if (j == 3) { 
       aux2 += milSep; 
       j = 0; 
      } 
      aux2 += aux.charAt(i); 
      j++; 
     } 
     fld.value = ''; 
     len2 = aux2.length; 
     for (i = len2 - 1; i >= 0; i--) 
     	fld.value += aux2.charAt(i); 
     fld.value += decSep + aux.substr(len - 2, len); 
    } 
    return false; 
}
function ue_validarcomas_puntos(valor)
{
	val = valor.value;
	longitud = val.length;
	texto = "";
	textocompleto = "";
	for(r=0;r<=longitud;r++)
	{
		texto = valor.value.substring(r,r+1);
		if((texto != ",")&&(texto != '.'))
		{
			textocompleto += texto;
		}
		else if(texto == ",")
		{
		 break;
		}
			
	}
	valor.value=textocompleto;
}


function redondear2(numero)
{
	numero2='';
	numero=parseFloat(numero);
	numero=Math.ceil(numero*10)/10
	AuxString = numero.toString();
	if(AuxString.indexOf('.')>=0)
	{
		AuxArr=AuxString.split('.');
		if(AuxArr[1]>=5)
		{
			numero=Math.ceil(numero);
		}
		else
		{ 
			numero=Math.floor(numero);
		}
	} 
    return numero;
}

function redondear3(numero)
{
	numero2='';
	numero=parseFloat(numero);
	numero=Math.ceil(numero*10)/10
	AuxString = numero.toString();
	if(AuxString.indexOf('.')>=0)
	{
		AuxArr=AuxString.split('.');
		if(AuxArr[1]>5)
		{
			numero=Math.ceil(numero);
		}
		else
		{ 
			numero=Math.floor(numero);
		}
	} 
    return numero;
}

function uf_formato(obj)
{
 ldec_temp1=obj.value;
 if((ldec_temp1=="")||(ldec_temp1==".")||(ldec_temp1==","))
 {
  ldec_temp1="0,00";
 } 
 obj.value=uf_convertir(ldec_temp1); 
}

function ue_distribucion(opcion)
{
   f=document.form1;
   txtasignacion="txtAsignacion";
   ld_asignacion=eval("f."+txtasignacion+".value");
   if(opcion=="A")
   {
	   f.txtEnero.readOnly = true;
	   f.txtFebrero.readOnly = true;
	   f.txtMarzo.readOnly = true;
	   f.txtAbril.readOnly = true;
	   f.txtMayo.readOnly = true;
	   f.txtJunio.readOnly = true;
	   f.txtJulio.readOnly = true;
	   f.txtAgosto.readOnly = true;
	   f.txtSeptiembre.readOnly = true;
	   f.txtOctubre.readOnly = true;
	   f.txtNoviembre.readOnly = true;
	   f.txtDiciembre.readOnly = true;
	   ld_asignacion=uf_convertir_monto(ld_asignacion);
	   ld_division=parseFloat((ld_asignacion/12));
	   ld_division_aux=redondear2(ld_division);
	   if(!verificarDistAuto(ld_division_aux,ld_asignacion))
	   {
	    ld_division=redondear3(ld_division);
	   }
	   else
	   {
	    ld_division=redondear2(ld_division);
	   }
	   ld_asignacion=redondear2(ld_asignacion);
	   ld_suma_diciembre=redondear2(ld_division*12);
	   ld_mes12=(ld_asignacion-ld_suma_diciembre);
	   ld_mes12=redondear2(ld_mes12);
	   if(ld_mes12>=0)
	   {
		ld_diciembre=ld_division+ld_mes12;
	   } 			
	   else//if(ld_mes12<0)
	   {
		ld_diciembre=ld_division+ld_mes12;
	   } 
	   ld_total=(ld_division*11);
	   ld_total_general=ld_total+ld_diciembre;
	   ld_total_general=redondear2(ld_total_general);
	   ld_resto=(ld_asignacion-ld_total_general);
	   ld_resto=redondear2(ld_resto);
	   ld_diciembre=ld_diciembre+ld_resto;
	   ld_division=uf_convertir(ld_division);
	   ld_diciembre=uf_convertir(ld_diciembre);
	   m1="txtEnero";
	   ld_enero=eval("f."+m1+".value='"+ld_division+"'") ;
	   m2="txtFebrero";
	   ld_febrero=eval("f."+m2+".value='"+ld_division+"'") ;
	   m3="txtMarzo";
	   ld_marzo=eval("f."+m3+".value='"+ld_division+"'") ;
	   m4="txtAbril";
	   ld_abril=eval("f."+m4+".value='"+ld_division+"'") ;
	   m5="txtMayo";
	   ld_mayo=eval("f."+m5+".value='"+ld_division+"'") ;
	   m6="txtJunio";
	   ld_junio=eval("f."+m6+".value='"+ld_division+"'") ;
	   m7="txtJulio";
	   ld_julio=eval("f."+m7+".value='"+ld_division+"'") ;
	   m8="txtAgosto";
	   ld_agosto=eval("f."+m8+".value='"+ld_division+"'") ;
	   m9="txtSeptiembre";
	   ld_septiembre=eval("f."+m9+".value='"+ld_division+"'") ;
	   m10="txtOctubre";
	   ld_octubre=eval("f."+m10+".value='"+ld_division+"'") ;
	   m11="txtNoviembre";
	   ld_noviembre=eval("f."+m11+".value='"+ld_division+"'") ;
	   m12="txtDiciembre";
	   ld_diciembre=eval("f."+m12+".value='"+ld_diciembre+"'") ;
   }
   else if (opcion == "M")
   {
    ld_monto=uf_convertir(0);
	f.txtEnero.readOnly = false;
    f.txtFebrero.readOnly = false;
    f.txtMarzo.readOnly = false;
    f.txtAbril.readOnly = false;
    f.txtMayo.readOnly = false;
    f.txtJunio.readOnly = false;
    f.txtJulio.readOnly = false;
    f.txtAgosto.readOnly = false;
    f.txtSeptiembre.readOnly = false;
    f.txtOctubre.readOnly = false;
    f.txtNoviembre.readOnly = false;
    f.txtDiciembre.readOnly = false;
    m1="txtEnero";
    ld_enero=eval("f."+m1+".value='"+ld_monto+"'") ;
    m2="txtFebrero";
    ld_febrero=eval("f."+m2+".value='"+ld_monto+"'") ;
    m3="txtMarzo";
    ld_marzo=eval("f."+m3+".value='"+ld_monto+"'") ;
    m4="txtAbril";
    ld_abril=eval("f."+m4+".value='"+ld_monto+"'") ;
    m5="txtMayo";
    ld_mayo=eval("f."+m5+".value='"+ld_monto+"'") ;
    m6="txtJunio";
    ld_junio=eval("f."+m6+".value='"+ld_monto+"'") ;
    m7="txtJulio";
    ld_julio=eval("f."+m7+".value='"+ld_monto+"'") ;
    m8="txtAgosto";
    ld_agosto=eval("f."+m8+".value='"+ld_monto+"'") ;
    m9="txtSeptiembre";
    ld_septiembre=eval("f."+m9+".value='"+ld_monto+"'") ;
    m10="txtOctubre";
    ld_octubre=eval("f."+m10+".value='"+ld_monto+"'") ;
    m11="txtNoviembre";
    ld_noviembre=eval("f."+m11+".value='"+ld_monto+"'") ;
    m12="txtDiciembre";
    ld_diciembre=eval("f."+m12+".value='"+ld_monto+"'") ;
   }

}

function ue_actualizar_montos()
{
 	f=document.form1;
	
	ld_asignado=f.txtAsignacion.value  
	ld_asignado=parseFloat(uf_convertir_monto(ld_asignado));
	
	ld_m1=f.txtEnero.value; 
	ld_m1=parseFloat(uf_convertir_monto(ld_m1));
	
	ld_m2=f.txtFebrero.value;    
	ld_m2=parseFloat(uf_convertir_monto(ld_m2));
	
	ld_m3=f.txtMarzo.value;    
	ld_m3=parseFloat(uf_convertir_monto(ld_m3));
	
	ld_m4=f.txtAbril.value; 
	ld_m4=parseFloat(uf_convertir_monto(ld_m4));
	
	ld_m5=f.txtMayo.value;
	ld_m5=parseFloat(uf_convertir_monto(ld_m5));
	
	ld_m6=f.txtJunio.value;
	ld_m6=parseFloat(uf_convertir_monto(ld_m6));
	
	ld_m7=f.txtJulio.value;  
	ld_m7=parseFloat(uf_convertir_monto(ld_m7));
	
	ld_m8=f.txtAgosto.value;      
	ld_m8=parseFloat(uf_convertir_monto(ld_m8));
	
	ld_m9=f.txtSeptiembre.value;           
	ld_m9=parseFloat(uf_convertir_monto(ld_m9));
	
	ld_m10=f.txtOctubre.value;           
	ld_m10=parseFloat(uf_convertir_monto(ld_m10));
	
	ld_m11=f.txtNoviembre.value;    
	ld_m11=parseFloat(uf_convertir_monto(ld_m11));
	
	ld_m12=f.txtDiciembre.value;       
	ld_m12=parseFloat(uf_convertir_monto(ld_m12));
	
	ld_total = parseFloat(ld_m1 + ld_m2 + ld_m3 + ld_m4 + ld_m5 + ld_m6 +ld_m7 + ld_m8 + ld_m9 + ld_m10 + ld_m11 + ld_m12);
	ld_total=redondear2(ld_total);
	
	ld_diferencia = parseFloat(ld_asignado - ld_total);
	f.txtTotal.value=ld_total;
	ld_total=uf_convertir(f.txtTotal.value);		
	f.txtTotal.value=ld_total;
	f.txtDiferencia.value =uf_convertir(ld_diferencia);

}
setInterval("ue_actualizar_montos()",500);

function verificarDistAuto(monto,asignado)
{
 var total = 0;
 var ok = true;
 for(i=1;i<=12;i++)
 {
  total += monto;
  if((total>asignado)&&(i<12))
  {
   ok = false
   break;
  }
 }
 return ok;
}
</script>
<?php
   
   if(($ld_enero == "0,00")&&($ld_febrero == "0,00")&&($ld_marzo == "0,00")&&
	  ($ld_abril == "0,00")&&($ld_mayo == "0,00")&&($ld_junio == "0,00")&&
	  ($ld_julio == "0,00")&&($ld_agosto == "0,00")&&($ld_septiembre == "0,00")&&
	  ($ld_octubre == "0,00")&&($ld_noviembre == "0,00")&&($ld_diciembre == "0,00"))
   {
?>
<script language="javascript">
document.form1.radiobutton[0].checked = true;
ue_distribucion("A");
</script>
<?php    
   }
?>
</html>