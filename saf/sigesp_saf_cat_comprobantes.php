<?php
session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Catálogo de Comprobantes</title>
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css" />
<link href="../shared/css/general.css" rel="stylesheet" type="text/css" />
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css" />
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css" />
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
<form id="sigesp_saf_cat_comprobantes.php" name="form1" method="post" action="">
  <p>&nbsp;</p>
  <table width="500" border="0" cellpadding="0" cellspacing="0" class="formato-blanco" align="center">
    <tr class="titulo-celda">
      <td height="22" colspan="4">Cat&aacute;logo de Pr&eacute;stamos </td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td height="18">&nbsp;</td>
      <td width="148" rowspan="3"><table width="145" border="0" cellspacing="0" class="formato-blanco">
          <tr>
            <td colspan="2"><div align="center" class="titulo-conect">Rango de Fechas </div></td>
          </tr>
          <tr>
            <td width="36"><div align="right">Desde</div></td>
            <td width="103" height="22"><input name="txtdesde" type="text" id="txtdesde" size="15"  datepicker="true"  onkeypress="ue_separadores(this,'/',patron,true);" /></td>
          </tr>
          <tr>
            <td><div align="right">Hasta</div></td>
            <td height="22"><input name="txthasta" type="text" id="txthasta" size="15"  datepicker="true"  onkeypress="ue_separadores(this,'/',patron,true);" /></td>
          </tr>
      </table></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td width="120"><div align="right">Comprobante</div></td>
      <td width="162" height="22"><div align="left">
          <input name="txtcmpmov" type="text" id="txtcmpmov" />
      </div></td>
      <td width="68">&nbsp;</td>
    </tr>
    <tr>
      <td><div align="right"></div></td>
      <td height="22"><div align="left"></div></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td colspan="3"><div align="right"><a href="javascript: ue_search();"><img src="../shared/imagebank/tools15/buscar.gif" alt="Buscar" width="15" height="15" border="0" />Buscar</a></div></td>
    </tr>
  </table>
  <p align="center"><?php $io_grid->makegrid($li_totrows,$lo_title,$lo_object,$li_widthtable,$ls_titletable,$ls_nametable); ?></p>
</form>
</body>
</html>
