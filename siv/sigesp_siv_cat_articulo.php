<?php
session_start();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Cat&aacute;logo de Art&iacute;culo</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
<!--
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
<script type="text/javascript" language="JavaScript1.2" src="../shared/js/number_format.js"></script>
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
</head>

<body>
<form name="form1" method="post" action="">
  <p align="center">
    <input name="operacion" type="hidden" id="operacion">
    <input name="hidstatus" type="hidden" id="hidstatus">
</p>
  <table width="500" border="0" align="center" cellpadding="1" cellspacing="1">
    <tr>
      <td width="496" colspan="2" class="titulo-celda">Cat&aacute;logo de Art&iacute;culo</td>
    </tr>
  </table>
<br>
    <table width="500" border="0" cellpadding="0" cellspacing="0" class="formato-blanco" align="center">
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td width="80"><div align="right">C&oacute;digo</div></td>
        <td width="418" height="22"><div align="left">
          <input name="txtcodart" type="text" id="txtnombre2">
        </div></td>
      </tr>
      <tr>
        <td><div align="right">Denominaci&oacute;n</div></td>
        <td height="22"><div align="left">          <input name="txtdenart" type="text" id="txtdenart">
        </div></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td><div align="right"><a href="javascript: ue_search();"><img src="../shared/imagebank/tools15/buscar.gif" alt="Buscar" width="15" height="15" border="0">Buscar</a></div></td>
      </tr>
    </table>
  <br>
<?php
	require_once("../shared/class_folder/sigesp_include.php");
	$in     =new sigesp_include();
	$con    =$in->uf_conectar();
	require_once("../shared/class_folder/class_mensajes.php");
	$io_msg =new class_mensajes();
	require_once("../shared/class_folder/class_funciones.php");
	$io_fun =new class_funciones();
	require_once("../shared/class_folder/class_datastore.php");
	$ds     =new class_datastore();
	require_once("../shared/class_folder/class_sql.php");
	$io_sql =new class_sql($con);
	require_once("sigesp_siv_c_articulo.php");
	$io_siv= new sigesp_siv_c_articulo();
	
	$arre=$_SESSION["la_empresa"];
	$ls_codemp=$arre["codemp"];
	$li_estnum="";
	$li_catalogo=$io_siv->uf_siv_select_catalogo($li_estnum,$li_estcmp);
	if(array_key_exists("operacion",$_POST))
	{
		$ls_operacion=$_POST["operacion"];
		$ls_codart="%".$_POST["txtcodart"]."%";
		$ls_denart="%".$_POST["txtdenart"]."%";
		$ls_status="%".$_POST["hidstatus"]."%";
	}
	else
	{
		$ls_operacion="";
	
	}
	print "<table width=500 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
	print "<tr class=titulo-celda>";
	print "<td width=100>Código</td>";
	print "<td>Denominacion</td>";
	print "<td>Clasificación</td>";
	print "</tr>";
	if($ls_operacion=="BUSCAR")
	{
		$ls_sql="SELECT siv_articulo.*,".
				"      (SELECT dentipart FROM siv_tipoarticulo".
				"        WHERE siv_tipoarticulo.codtipart=siv_articulo.codtipart) as dentipart,".
				"      (SELECT denart FROM siv_articulo as pri".
				"        WHERE siv_articulo.codemp=pri.codemp".
				"		   AND siv_articulo.codartpri=pri.codart) as denartpri,".
				"      (SELECT tipart FROM siv_tipoarticulo".
				"        WHERE siv_tipoarticulo.codtipart=siv_articulo.codtipart) as tipart,".
				"      (SELECT dencat FROM saf_catalogo".
				"        WHERE saf_catalogo.catalogo=siv_articulo.codcatsig) as dencatsig,".
				"      (SELECT denominacion FROM scg_cuentas".
				"        WHERE scg_cuentas.sc_cuenta=siv_articulo.sc_cuenta) as densccuenta,".
				"      (SELECT denunimed FROM siv_unidadmedida".
				"        WHERE siv_unidadmedida.codunimed=siv_articulo.codunimed) as denunimed,".
				"      (SELECT desproducto FROM siv_producto".
				"        WHERE siv_producto.codprod=siv_articulo.codmil) as desproducto".
				"  FROM siv_articulo".
				" WHERE codemp = '".$ls_codemp."'".
				"   AND codart LIKE '".$ls_codart."'".
				"   AND denart LIKE '".$ls_denart."'".
				" ORDER BY codart";
		$rs_data=$io_sql->select($ls_sql);
		while(!$rs_data->EOF)
		{
			print "<tr class=celdas-blancas>";
			$ls_codart= trim($rs_data->fields["codart"]);
			$ls_denart= $rs_data->fields["denart"];
			$ls_codtipart= $rs_data->fields["codtipart"];
			$ls_dentipart= $rs_data->fields["dentipart"];
			$ls_codunimed= $rs_data->fields["codunimed"];
			$ls_denunimed= $rs_data->fields["denunimed"];
			$ld_feccreart= $rs_data->fields["feccreart"];
			$ls_obsart= $rs_data->fields["obsart"];
			$li_exiart= $rs_data->fields["exiart"];
			$li_exiiniart= $rs_data->fields["exiiniart"];
			$li_minart= $rs_data->fields["minart"];
			$li_maxart= $rs_data->fields["maxart"];
			$li_reoart= $rs_data->fields["reoart"];
			$li_prearta= $rs_data->fields["prearta"];
			$li_preartb= $rs_data->fields["preartb"];
			$li_preartc= $rs_data->fields["preartc"];
			$li_preartd= $rs_data->fields["preartd"];
			$ld_fecvenart= $rs_data->fields["fecvenart"];
			$ls_codcatsig= $rs_data->fields["codcatsig"];
			$ls_dencatsig= $rs_data->fields["dencatsig"];
			$ls_spg_cuenta= $rs_data->fields["spg_cuenta"];
			$ls_sccuenta= $rs_data->fields["sc_cuenta"];
			$ls_scdencuenta= $rs_data->fields["densccuenta"];
			$ls_serart= $rs_data->fields["serart"];
			$ls_fabart= $rs_data->fields["fabart"];
			$ls_ubiart= $rs_data->fields["ubiart"];
			$ls_docart= $rs_data->fields["docart"];
			$li_pesart= $rs_data->fields["pesart"];
			$li_altart= $rs_data->fields["altart"];
			$li_ancart= $rs_data->fields["ancart"];
			$li_proart= $rs_data->fields["proart"];
			$li_ultcosart=  $rs_data->fields["ultcosart"];
			$li_cosproart=  $rs_data->fields["cosproart"];
			$ls_fotart= $rs_data->fields["fotart"];
			if($ls_fotart=="")
				$ls_fotart="blanco.jpg";
			$ld_feccreart=$io_fun->uf_convertirfecmostrar($ld_feccreart);
			$ld_fecvenart=$io_fun->uf_convertirfecmostrar($ld_fecvenart);
			//$ls_codmil=$row["codmil"];
			$ls_codprod= $rs_data->fields["codmil"]; 
			//$ls_denmil=$row["denmil"];
			$ls_denprod= $rs_data->fields["desproducto"];
			$ls_tipart= $rs_data->fields["tipart"];
			$ls_estartgen= $rs_data->fields["estartgen"];
			$ls_codartpri= trim($rs_data->fields["codartpri"]);
			$ls_denartpri= $rs_data->fields["denartpri"];
			if ($ls_tipart=="1")
			{
				$ls_clasificacion="Bienes";
			}
			elseif ($ls_tipart=="2")
			{
				$ls_clasificacion="Material y Suministro";
			}
			elseif ($ls_tipart=="")
			{
				$ls_clasificacion="No posee";
			}
			$li_exiart= number_format($li_exiart,2,",",".");
			$li_exiiniart= number_format($li_exiiniart,2,",",".");
			$li_minart= number_format($li_minart,2,",",".");
			$li_maxart= number_format($li_maxart,2,",",".");
			$li_cosproart= number_format($li_cosproart,2,",",".");
			$li_ultcosart= number_format($li_ultcosart,2,",",".");
			$li_proart= number_format($li_proart,2,",",".");
			$li_ancart= number_format($li_ancart,2,",",".");
			$li_altart= number_format($li_altart,2,",",".");
			$li_pesart= number_format($li_pesart,2,",",".");
			$li_preartd= number_format($li_preartd,2,",",".");
			$li_preartc= number_format($li_preartc,2,",",".");
			$li_preartb= number_format($li_preartb,2,",",".");
			$li_prearta= number_format($li_prearta,2,",",".");
			$li_reoart= number_format($li_reoart,2,",",".");
				
				print "<td><a href=\"javascript: aceptar('$ls_codart','$ls_denart','$ls_codtipart','$ls_dentipart','$ls_codunimed',";
				print "'$ls_denunimed','$ld_feccreart','$ls_obsart','$li_exiart','$li_exiiniart','$li_minart','$li_maxart','$li_prearta',";
				print "'$li_preartb','$li_preartc','$li_preartd','$ld_fecvenart','$ls_spg_cuenta','$li_pesart','$li_altart',";
				print "'$li_ancart','$li_proart','$li_ultcosart','$li_cosproart','$ls_fotart','$ls_codcatsig','$ls_dencatsig',";
				print "'$li_catalogo','$ls_sccuenta','$ls_scdencuenta','$li_reoart','$ls_codprod','$ls_denprod','$ls_serart',";
				print "'$ls_fabart','$ls_ubiart','$ls_docart','$ls_tipart','$ls_estartgen','$ls_codartpri','$ls_denartpri');\">".$ls_codart."</a></td>";
				print "<td>".$ls_denart."</td>";
				print "<td>".$ls_clasificacion."</td>";
				print "</tr>";			
			$rs_data->MoveNext();
			}
		}
	print "</table>";
?>
</div>
</form>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script language="JavaScript">
	function aceptar(ls_codart,ls_denart,ls_codtipart,ls_dentipart,ls_codunimed,ls_denunimed,ld_feccreart,ls_obsart,li_exiart,
	                 li_exiiniart,li_minart,li_maxart,li_prearta,li_preartb,li_preartc,li_preartd,ld_fecvenart,ls_spg_cuenta,
					 li_pesart,li_altart,li_ancart,li_proart,li_ultcosart,li_cosproart,ls_fotart,ls_codcatsig,ls_dencatsig,
					 li_catalogo,ls_sccuenta,ls_scdencuenta,li_reoart,ls_codprod,ls_denprod,ls_serart,ls_fabart,ls_ubiart,ls_docart,
					 ls_tipart,ls_estartgen,ls_codartpri,ls_denartpri)
	{
		opener.document.form1.txtcodart.value=   ls_codart;
		opener.document.form1.txtdenart.value=   ls_denart;
		opener.document.form1.txtcodtipart.value=ls_codtipart;
		opener.document.form1.txtdentipart.value=ls_dentipart;
		opener.document.form1.txtcodunimed.value=ls_codunimed;
		opener.document.form1.txtdenunimed.value=ls_denunimed;
		opener.document.form1.txtfeccreart.value=ld_feccreart;
		opener.document.form1.txtobsart.value=   ls_obsart;
		opener.document.form1.txtexiart.value=   li_exiart;
		opener.document.form1.txtexiiniart.value=li_exiiniart;
		opener.document.form1.txteximinart.value=li_minart;
		opener.document.form1.txteximaxart.value=li_maxart;
		opener.document.form1.txtreoart.value=   li_reoart;
		opener.document.form1.txtprearta.value=  li_prearta;
		opener.document.form1.txtpreartb.value=  li_preartb;
		opener.document.form1.txtpreartc.value=  li_preartc;
		opener.document.form1.txtpreartd.value=  li_preartd;
		opener.document.form1.txtfecvenart.value=ld_fecvenart;
		opener.document.form1.txtspg_cuenta.value=ls_spg_cuenta;
		opener.document.form1.txtpesart.value=   li_pesart;
		opener.document.form1.txtaltart.value=   li_altart;
		opener.document.form1.txtancart.value=   li_ancart;
		opener.document.form1.txtproart.value=   li_proart;
		opener.document.form1.txtultcosart.value=li_ultcosart;
		opener.document.form1.txtcosproart.value=li_cosproart;
		opener.document.form1.txtsccuenta.value=ls_sccuenta;
		opener.document.form1.txtdensccuenta.value=ls_scdencuenta;
		if(ls_fotart!="")
		{opener.document.images["foto"].src="fotosarticulos/"+ls_fotart;}
		else
		{opener.document.images["foto"].src="fotosarticulos/blanco.jpg";}
		opener.document.form1.hidstatusc.value="C";
		opener.document.form1.btnregistrar.disabled=false;
		opener.document.form1.btncargos.disabled=false;
		/*opener.document.form1.txtcodmil.value=ls_codmil;
		opener.document.form1.txtdenmil.value=ls_denmil;*/
		opener.document.form1.txtcodprod.value=ls_codprod;
		opener.document.form1.txtdenprod.value=ls_denprod;
		opener.document.form1.txtserart.value=ls_serart;
		opener.document.form1.txtfabart.value=ls_fabart;
		opener.document.form1.txtubiart.value=ls_ubiart;
		opener.document.form1.txtdocart.value=ls_docart;
		opener.document.form1.txtclasif.value=ls_tipart;
		if(li_catalogo==1)
		{
			opener.document.form1.txtcodcatsig.value= ls_codcatsig;
			opener.document.form1.txtdencatsig.value= ls_dencatsig;		
		}
		if(ls_estartgen=="0")
		{
			opener.document.form1.rdgenerico[0].checked=true;
		}
		else
		{
			opener.document.form1.rdgenerico[1].checked=true;
		}
		opener.document.form1.txtcodartpri.value=ls_codartpri;
		opener.document.form1.txtdenartpri.value=ls_denartpri;
		close();
		if(li_exiart<=li_minart)
		{alert("El artículo esta por debajo del mínimo requerido en el almacén");}
		else
		{
			if(li_exiart<=li_reoart)
			{alert("El artículo esta por debajo de su punto de reorden");}
		}
	}
	
	function ue_search()
  	{
		f=document.form1;
		f.operacion.value="BUSCAR";
		f.action="sigesp_siv_cat_articulo.php";
		f.submit();
	}
</script>
</html>