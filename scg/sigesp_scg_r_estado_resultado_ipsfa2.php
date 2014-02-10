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
	require_once("class_funciones_scg.php");
	$io_fun_scg=new class_funciones_scg();
	$io_fun_scg->uf_load_seguridad("SCG","sigesp_scg_r_estado_resultado.php",$ls_permisos,$la_seguridad,$la_permisos);
	$ls_reporte = $io_fun_scg->uf_select_config("SCG","REPORTE","ESTADO_RESULTADO","sigesp_scg_rpp_estado_resultado.php","C");
	$ls_reporte="sigesp_scg_rpp_estado_resultado_estpre.php";
	
	//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$ldt_periodo=$_SESSION["la_empresa"]["periodo"];
	$li_ano=substr($ldt_periodo,0,4);
	$ls_codemp=$_SESSION["la_empresa"]["codemp"];

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
}?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>


<script type="text/javascript" src="../shared/js/ext/adapter/ext/ext-base.js"></script>
<script type="text/javascript" src="../shared/js/ext/ext-all.js"></script>
<link rel="stylesheet" type="text/css" href="../shared/js/ext/resources/css/ext-all.css">
<link rel="stylesheet" type="text/css" href="../sfp/otros/css/ExtStart.css">

<script type="text/javascript" src="js/sigesp_scg_r_estadoderesultadosest.js"></script>

<script type="text/javascript" language="JavaScript1.2" src="../shared/js/disabled_keys.js"></script>
<title>Estado de Resultado </title>
<meta http-equiv="" content="text/html; charset=iso-8859-1">
<meta http-equiv="" content="text/html; charset=iso-8859-1">
<meta http-equiv="" content="text/html; charset=iso-8859-1"><meta http-equiv="" content="text/html; charset=iso-8859-1">
<meta http-equiv="Content-Type" content="text/html; charset=">
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
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
.Estilo1 {font-weight: bold}
.Estilo2 {font-size: 14px}
-->
</style>
<link href="../shared/js/css_intra/datepickercontrol.css" rel="stylesheet" type="text/css">
</head>
<body>
<table width="780" border="0" align="center" cellpadding="0" cellspacing="0" class="contorno" style="height:35px;border-top:0px;">
  <tr>
    <td height="20" width="25" class="toolbar" style="border-bottom:0px;"><div align="center"><a href="javascript:ue_imprimir('<? print $ls_codemp; ?>','<?php echo $ls_reporte ?>');"><img src="../shared/imagebank/tools20/imprimir.gif" alt="Imprimir" title="Imprimir" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript:ue_openexcel('<? print $ls_codemp; ?>');"><img src="../shared/imagebank/tools20/excel.jpg" alt="Excel" title="Excel" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_cerrar();"><img src="../shared/imagebank/tools20/salir.gif" alt="Salir" title="Salir" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><img src="../shared/imagebank/tools20/ayuda.gif" alt="Ayuda" title="Ayuda" width="20" height="20"></div></td>
    <td class="toolbar" width="530">&nbsp;</td>
  </tr>
</table>
<?php
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

require_once("../shared/class_folder/ddlb_generic_bd.php");
$class_combo=new ddlb_generic_bd($con);

if(array_key_exists("operacion",$_POST))
{
	$ls_operacion=$_POST["operacion"];
}
else
{
	$ls_operacion="MESES";	
}
if(array_key_exists("hidbot",$_POST))
{
	$lb_bot=false;
}
else
{
	$lb_bot=true;	
}
if (array_key_exists("txtfecdes",$_POST)) 
{
	$ldt_fecdes=$_POST["txtfecdes"];
}
else
{
	$ldt_fecdes="01/01/".$li_ano;
}
if (array_key_exists("txtfechas",$_POST)) 
{
	$ldt_fechas=$_POST["txtfechas"];
}
else
{
	$ldt_fechas=date("d/m/Y");
}
?>
</div> 
<p>&nbsp;</p>
<form name="form1" method="post" action="">
<?php
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$io_fun_scg->uf_print_permisos($ls_permisos,$la_permisos,$ls_logusr,"location.href='sigespwindow_blank.php'");
	unset($io_fun_scg);
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>		  
  <table width="715" height="18" border="0" align="center" cellpadding="1" cellspacing="1">
    <tr>
      <td width="654" colspan="2" class="titulo-ventana">Estado de Resultado por Estructura Presupuestaria </td>
    </tr>
  </table>
  <table width="530" border="0" align="center" cellpadding="0" cellspacing="1" class="formato-blanco">
    <tr>
      <td width="521"></td>
    </tr>
    <tr>
      <td height="122" colspan="3" align="center"><div align="left"></div>        <div align="left"></div>        <div align="left" class="style14"></div>        <div align="left">
        <table width="480" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
          <tr class="titulo-celdanew">
            <td height="13" colspan="7"><strong class="titulo-celdanew">Intervalos de Fechas </strong></td>
            </tr>
          <tr style="display:none">
            <td height="27" colspan="2"><div align="right">Reporte en</div></td>
            <td height="27" colspan="2"><div align="left">
              <select name="cmbbsf" id="cmbbsf">
                <option value="0" selected>Bs.</option>
                <option value="1">Bs.F.</option>
              </select>
            </div></td>
            <td height="27">&nbsp;</td>
            <td height="27">&nbsp;</td>
            <td height="27">&nbsp;</td>
          </tr>
          <tr>
            <td height="27">&nbsp;</td>
            <td width="65" height="27">&nbsp;</td>
            <td height="27" colspan="2">&nbsp;</td>
            <td height="27"><input name="botmeses" type="button" class="boton" id="botmeses" value="Meses" onClick="uf_cambio_meses()"></td>
            <td height="27"><div align="left">
              <input name="botdias" type="button" class="boton" id="botdias" value="Dias" onClick="uf_cambio_dias()">
            </div></td>
            <td height="27">&nbsp;</td>
          </tr>
		  <?php
		   if($ls_operacion=="MESES") 
		   {
			  $lb_bot=true;	
		  ?>
          <tr>
            <td height="22">&nbsp;</td>
            <td height="22"><div align="right">Desde
            </div></td>
            <td height="22" colspan="2"><div align="left">
              <select name="cmbmesdes" id="cmbmesdes">
                <option value="01">Enero</option>
                <option value="02">Febrero</option>
                <option value="03">Marzo</option>
                <option value="04">Abril</option>
                <option value="05">Mayo</option>
                <option value="06">Junio</option>
                <option value="07">Julio</option>
                <option value="08">Agosto</option>
                <option value="09">Septiembre</option>
                <option value="10">Octubre</option>
                <option value="11">Noviembre</option>
                <option value="12">Diciembre</option>
              </select>
            </div></td>
            <td height="22">
			<?php
			  if($_SESSION["ls_gestor"]=='INFORMIX')
			  {
			   $ls_selec="distinct substr(cast(fecsal as char(10)),1,4)  as anuales";
			  }
			  else 
			  {
			   $ls_selec="distinct substr(cast(fecsal as char(10)),1,4)  as anuales";
			  }
			  $ls_coddev="anuales";
			  $ls_tabla="scg_saldos";
	          $ls_codemp=$_SESSION["la_empresa"]["codemp"];
			  $ls_codigo="";
			  $ls_clave="";
			  $ls_nomcmb="cmbagnodes";
			  $li_width="50";			  
			  $class_combo->uf_cargar_conceptos($ls_selec,$ls_coddev,$ls_tabla,$ls_codemp,$ls_codigo,$ls_clave,$ls_nomcmb,$li_width,'');
			 ?>			 			 
			 </td>
	            <td height="22"><div align="right">Hasta
	            </div></td>
	            <td height="22">
	            <div align="left">
				<select name="cmbmeshas" id="cmbmeshas">
				  <option value="12">Diciembre</option>
				  <option value="01">Enero</option>
				  <option value="02">Febrero</option>
				  <option value="03">Marzo</option>
				  <option value="04">Abril</option>
				  <option value="05">Mayo</option>
				  <option value="06">Junio</option>
				  <option value="07">Julio</option>
				  <option value="08">Agosto</option>
				  <option value="09">Septiembre</option>
				  <option value="10">Octubre</option>
				  <option value="11">Noviembre</option>
				</select>			  
				<?php
				  if($_SESSION["ls_gestor"]=='INFORMIX')
			       { 
				    $ls_selec="distinct substr(cast(fecsal as char(10)),1,4)  as anuales";
				   }
				   else 
				   { 
				    $ls_selec="distinct substr(cast(fecsal as char(10)),1,4)  as anuales";
				   }
				  $ls_coddev="anuales";
				  $ls_tabla="scg_saldos";
	              $ls_codemp=$_SESSION["la_empresa"]["codemp"];
				  $ls_codigo="";
				  $ls_clave="";
				  $ls_nomcmb="cmbagnohas";
				  $li_width="50";
				  $class_combo->uf_cargar_conceptos($ls_selec,$ls_coddev,$ls_tabla,$ls_codemp,$ls_codigo,$ls_clave,$ls_nomcmb,$li_width,'');
			  ?>
            </div>
            </td>
          </tr>
		  <?php 
		    }
			if($ls_operacion=="DIAS")
			{
			  $lb_bot=false;
		  ?>
          <tr>
            <td width="29" height="22"><div align="right"></div></td>
            <td height="22"><div align="right"> Desde
</div></td>
            <td colspan="2"><div align="left">
              <input name="txtfecdes" type="text" id="txtfecdes" onKeyPress="javascript: ue_formatofecha(this,'/',patron,true);" value="<?php print $ldt_fecdes ; ?>" size="15" maxlength="15" datepicker="true">
            </div></td>
            <td width="46">&nbsp;</td>
            <td width="60"><div align="right"> Hasta
			</div></td>
            <td width="189"><div align="left">
              <input name="txtfechas" type="text" id="txtfechas"  onKeyPress="javascript: ue_formatofecha(this,'/',patron,true);" value="<?php print $ldt_fechas ; ?>" size="15" maxlength="15" datepicker="true">
            </div></td>
            
            
            
            
          </tr>
		  <?php 
		    }
		  ?>
		  <tr>
		  <td colspan="8">
		    <table width="362" height="34" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco" <?php echo $ls_mostrar_estruc; ?>>
        <tr class="titulo-celdanew">
        <td height="13">NIVEL DEL REPORTE</td>
        </tr>
        <tr>
          <td height="21"><div align="center">
            <label>
            <input name="rdotipo" type="radio" id="rdoconsolidado" checked>
            Consolidado</label>
             <input type="radio" name="rdotipo" id="rdodetallado">
            Detallado</label>
          </div></td>
        </tr>
      </table>
   
		  
		  
		  
		  </td>
		</tr>		  
		  
		  
        </table>
        </div></td>
    </tr>
    <tr>
    	<td id="gridestructuras">
    	</td>
    </tr>
    <tr>
      <td height="22" colspan="3" align="center">
        <input name="operacion"   type="hidden"   id="operacion"   value="<?php print $ls_operacion;?>">
		<input name="hidbot" type="hidden" id="hidbot" value="<?php print $lb_bot ?>">
</td>
    </tr>
  </table>
  
<input name="estclades"  type="hidden"   id="estclades">
<input name="estclahas"  type="hidden"   id="estclahas">
<input name="estmodest"  type="hidden"   id="estmodest" value="1">   
</form>      
</body>
<script language="JavaScript">


function catalogo_estpro1()
{
	   pagina="../spg/sigesp_cat_public_estpro1.php?tipo=reporte";
	   window.open(pagina,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=568,height=400,resizable=yes,location=no");
}


function catalogo_estpro2()
{
	f=document.form1;
	codestpro1=f.codestpro1.value;	
	estmodest=f.estmodest.value;
	estcla=f.estclades.value;
	if(estmodest==1)
	{
		if(codestpro1!="")
		{
			pagina="../spg/sigesp_cat_public_estpro2.php?codestpro1="+codestpro1+"&tipo=reporte"+"&estcla="+estcla;
			window.open(pagina,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=568,height=400,resizable=yes,location=no");
		}
		else
		{
			alert("Seleccione nivel anterior");
		}
	}
	else
	{
		
		if(codestpro1=='**')
		{
			pagina="sigesp_cat_estpro2.php?tipo=reporte"+"&estcla="+estcla;
			window.open(pagina,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=568,height=400,resizable=yes,location=no");
		}
		else
		{
			if(codestpro1!="")
			{
				pagina="sigesp_cat_public_estpro2.php?codestpro1="+codestpro1+"&tipo=reporte"+"&estcla="+estcla;
				window.open(pagina,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=568,height=400,resizable=yes,location=no");
			}
			else
			{
				alert("Seleccione  nivel anterior");
			}
		}
	}	
}

function catalogo_estpro3()
{
	f=document.form1;
	codestpro1=f.codestpro1.value;
	codestpro2=f.codestpro2.value;
	codestpro3=f.codestpro3.value;
	estmodest=f.estmodest.value;
	estcla=f.estclades.value;
	if(estmodest==1)
	{
		if((codestpro1!="")&&(codestpro2!="")&&(codestpro3==""))
		{
			pagina="../spg/sigesp_cat_public_estpro3.php?codestpro1="+codestpro1+"&codestpro2="+codestpro2+"&tipo=reporte"
			+"&estcla="+estcla;
			window.open(pagina,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=568,height=400,resizable=yes,location=no");
		}
		else
		{
			pagina="sigesp_cat_public_estpro.php?tipo=reporte"+"&estcla="+estcla;
			window.open(pagina,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=568,height=400,resizable=yes,location=no");
		}
	}
	else
	{
		if((codestpro2=='**')||(codestpro1=='**'))
		{
			if((codestpro2!="")&&(codestpro1!=""))
			{
				pagina="sigesp_cat_estpro3.php?tipo=reporte&codestpro1="+codestpro1+"&codestpro2="+codestpro2+"&estcla="+estcla;
				window.open(pagina,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=568,height=400,resizable=yes,location=no");
			}
			else
			{
				alert("Seleccione niveles anteriores");
			}
		}
		else
		{
			if((codestpro2!="")&&(codestpro1!=""))
			{
				pagina="sigesp_cat_public_estpro3.php?codestpro1="+codestpro1+"&codestpro2="+codestpro2+"&tipo=reporte"
				+"&estcla="+estcla;
				window.open(pagina,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=568,height=400,resizable=yes,location=no");	
			}
			else
			{
				alert("Seleccione niveles anteriores");
			}
		}	
	}	
}

function catalogo_estprohas1()
{
	   pagina="../spg/sigesp_cat_public_estpro1.php?tipo=rephas";
	   window.open(pagina,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=568,height=400,resizable=yes,location=no");
}
function catalogo_estprohas2()
{
	f=document.form1;
	codestpro1=f.codestpro1h.value;
	estmodest=f.estmodest.value;
	estcla=f.estclahas.value;
	if(estmodest==1)
	{
		if(codestpro1!="")
		{
			pagina="../spg/sigesp_cat_public_estpro2.php?codestpro1="+codestpro1+"&tipo=rephas"+"&estcla="+estcla;
			window.open(pagina,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=568,height=400,resizable=yes,location=no");
		}
		else
		{
			alert("Seleccione nivel anterior");
		}
	}
	else
	{
		if(codestpro1=='**')
		{
			pagina="sigesp_cat_estpro2.php?tipo=rephas"+"&estcla="+estcla;
			window.open(pagina,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=568,height=400,resizable=yes,location=no");
		}
		else
		{
			if(codestpro1!="")
			{
				pagina="sigesp_cat_public_estpro2.php?codestpro1="+codestpro1+"&tipo=rephas"+"&estcla="+estcla;
				window.open(pagina,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=568,height=400,resizable=yes,location=no");
			}
			else
			{
				alert("Seleccione  nivel anterior");
			}
		}
	}	
}
function catalogo_estprohas3()
{
	f=document.form1;
	codestpro1=f.codestpro1h.value;
	codestpro2=f.codestpro2h.value;
	codestpro3=f.codestpro3h.value;
	estmodest=f.estmodest.value;
	estcla=f.estclahas.value;
	if(estmodest==1)
	{
		if((codestpro1!="")&&(codestpro2!="")&&(codestpro3==""))
		{
			pagina="../spg/sigesp_cat_public_estpro3.php?codestpro1="+codestpro1+"&codestpro2="+codestpro2+"&tipo=rephas"+"&estcla="+estcla;
			window.open(pagina,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=568,height=400,resizable=yes,location=no");
		}
		else
		{
			pagina="sigesp_cat_public_estpro.php?tipo=rephas"+"&estcla="+estcla;
			window.open(pagina,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=568,height=400,resizable=yes,location=no");
		}
	}
	else
	{
		if((codestpro2=='**')||(codestpro1=='**'))
		{
			if((codestpro2!="")&&(codestpro1!=""))
			{
				pagina="sigesp_cat_estpro3.php?tipo=rephas&codestpro1="+codestpro1+"&codestpro2="+codestpro2+"&estcla="+estcla;
				window.open(pagina,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=568,height=400,resizable=yes,location=no");
			}
			else
			{
				alert("Seleccione niveles anteriores");
			}
		}
		else
		{
			if((codestpro2!="")&&(codestpro1!=""))
			{
				pagina="sigesp_cat_public_estpro3.php?codestpro1="+codestpro1+"&codestpro2="+codestpro2+"&tipo=rephas"+"&estcla="+estcla;
				window.open(pagina,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=568,height=400,resizable=yes,location=no");	
			}
			else
			{
				alert("Seleccione niveles anteriores");
			}
		}	
	}	
}

function ue_imprimir(codemp,as_reporte)
{

	if(document.getElementById('rdodetallado').checked==true)
	{
		as_reporte = "sigesp_scg_rpp_estado_resultado_estpre.php";
	}
	else
	{
		as_reporte = "sigesp_scg_rpp_estado_resultado_estpre_consolidado.php";
	}	

	cantidad=gridEst.store.getCount();
	arrSelect = gridEst.getSelectionModel().getSelections();
	if(arrSelect.length<cantidad)
	{
		if(arrSelect && arrSelect.length>0)
		{
			cadjson = "{'estructuras':[";
			for(i=0;i<arrSelect.length;i++)
			{
				if(i==0)
				{
					cadjson=cadjson+"{'codestpro1':'"+arrSelect[i].get('codestpro1')+"','codestpro2':'"+arrSelect[i].get('codestpro2')+"','codestpro3':'"+arrSelect[i].get('codestpro3')+"','estcla':'"+arrSelect[i].get('estcla')+"'}"	
				}
				else
				{
					cadjson=cadjson+",{'codestpro1':'"+arrSelect[i].get('codestpro1')+"','codestpro2':'"+arrSelect[i].get('codestpro2')+"','codestpro3':'"+arrSelect[i].get('codestpro3')+"','estcla':'"+arrSelect[i].get('estcla')+"'}"
				}
			}
			cadjson =cadjson+"]}";
		}
	}
	else
	{
		cadjson = "{'estructuras':[{'codestpro1':'todas'}]}";
	}
	f=document.form1;
	li_imprimir=f.imprimir.value;
	if(li_imprimir==1)
	{	
		hidbot = f.hidbot.value;
		cmbnivel = "1";
		tiporeporte=f.cmbbsf.value;
		if(hidbot==true)
		{
			cmbmesdes  = f.cmbmesdes.value;
			cmbmeshas = f.cmbmeshas.value;
			cmbagnodes = f.cmbagnodes.value;
			cmbagnohas = f.cmbagnohas.value;
			if((cmbagnodes=="s1")&&(cmbagnohas=="s1"))
			{
				alert ("Debe seleccionar los Parametros de Busqueda");
			}
			else
			{
				pagina="reportes/"+as_reporte+"?hidcodemp="+codemp+"&cmbmesdes="+cmbmesdes
						+"&cmbmeshas="+cmbmeshas+"&cmbagnodes="+cmbagnodes+"&cmbagnohas="+cmbagnohas+"&cmbnivel="+cmbnivel
						+"&hidbot="+hidbot+"&tiporeporte="+tiporeporte+"&jasonest="+cadjson;
						
						
				window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
			}
		}
		if(hidbot==false)
		{
			txtfecdes = f.txtfecdes.value;
			txtfechas = f.txtfechas.value;
			if((txtfecdes=="")&&(txtfechas==""))
			{
				alert ("Debe seleccionar los Parametros de Busqueda");
			}
			else
			{
				pagina="reportes/"+as_reporte+"?hidcodemp="+codemp+"&txtfecdes="+txtfecdes
						+"&txtfechas="+txtfechas+"&cmbnivel="+cmbnivel+"&hidbot="+hidbot+"&tiporeporte="+tiporeporte+"&jasonest="+cadjson;

				window.open(pagina,"catalogo","menubar=yes,toolbar=yes,scrollbars=yes,width=800,height=600,resizable=yes,location=yes");
			}
		}	
   	}
	else
   	{
 		alert("No tiene permiso para realizar esta operación");
   	}		
}

function ue_openexcel(codemp)
{
	if(document.getElementById('rdodetallado').checked==true)
	{
		as_reporte = "sigesp_scg_rpp_estado_resultado_estpre_excel.php";
	}
	else
	{
		as_reporte = "sigesp_scg_rpp_estado_resultado_estpre_excel_consolidado.php";
	}	
	cantidad=gridEst.store.getCount();
	arrSelect = gridEst.getSelectionModel().getSelections();
	if(arrSelect.length<cantidad)
	{
		if(arrSelect && arrSelect.length>0)
		{
			cadjson = "{'estructuras':[";
			for(i=0;i<arrSelect.length;i++)
			{
				if(i==0)
				{
					cadjson=cadjson+"{'codestpro1':'"+arrSelect[i].get('codestpro1')+"','codestpro2':'"+arrSelect[i].get('codestpro2')+"','codestpro3':'"+arrSelect[i].get('codestpro3')+"','estcla':'"+arrSelect[i].get('estcla')+"'}"	
				}
				else
				{
					cadjson=cadjson+",{'codestpro1':'"+arrSelect[i].get('codestpro1')+"','codestpro2':'"+arrSelect[i].get('codestpro2')+"','codestpro3':'"+arrSelect[i].get('codestpro3')+"','estcla':'"+arrSelect[i].get('estcla')+"'}"
				}
			}
			cadjson =cadjson+"]}";
		}
	}
	else
	{
		cadjson = "{'estructuras':[{'codestpro1':'todas'}]}";
	}
	


	f=document.form1;
	li_imprimir=f.imprimir.value;
	if(li_imprimir==1)
	{	
		hidbot = f.hidbot.value;
		cmbnivel = "1";
		tiporeporte=f.cmbbsf.value;
		if(hidbot==true)
		{
			cmbmesdes  = f.cmbmesdes.value;
			cmbmeshas = f.cmbmeshas.value;
			cmbagnodes = f.cmbagnodes.value;
			cmbagnohas = f.cmbagnohas.value;
			if((cmbagnodes=="s1")&&(cmbagnohas=="s1"))
			{
				alert ("Debe seleccionar los Parametros de Busqueda");
			}
			else
			{
				pagina="reportes/"+as_reporte+"?hidcodemp="+codemp+"&cmbmesdes="+cmbmesdes
						+"&cmbmeshas="+cmbmeshas+"&cmbagnodes="+cmbagnodes+"&cmbagnohas="+cmbagnohas+"&cmbnivel="+cmbnivel
						+"&hidbot="+hidbot+"&tiporeporte="+tiporeporte+"&jasonest="+cadjson;
				window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
			}
		}
		
		if(hidbot==false)
		{
			txtfecdes = f.txtfecdes.value;
			txtfechas = f.txtfechas.value;
			if((txtfecdes=="")&&(txtfechas==""))
			{
				alert ("Debe seleccionar los Parametros de Busqueda");
			}
			else
			{
					pagina="reportes/"+as_reporte+"?hidcodemp="+codemp+"&txtfecdes="+txtfecdes
						+"&txtfechas="+txtfechas+"&cmbnivel="+cmbnivel+"&hidbot="+hidbot+"&tiporeporte="+tiporeporte+"&jasonest="+cadjson;
				window.open(pagina,"catalogo","menubar=yes,toolbar=yes,scrollbars=yes,width=800,height=600,resizable=yes,location=yes");
			}
		}	
   	}
	else
   	{
 		alert("No tiene permiso para realizar esta operación");
   	}		
}

function uf_cambio_meses()
{
     f=document.form1;
	 f.operacion.value="MESES";
	 f.action="sigesp_scg_r_estado_resultado_ipsfa2.php";
	 f.submit();

}

function uf_cambio_dias()
{
     f=document.form1;
	 f.operacion.value="DIAS";
	 f.action="sigesp_scg_r_estado_resultado_ipsfa2.php";
	 f.submit();
	
}
   
function ue_cerrar()
{
	parent.location.href = "sigespwindow_blank.php";
}

//--------------------------------------------------------
//	Función que le da formato a la fecha
//--------------------------------------------------------
var patron = new Array(2,2,4);
var patron2 = new Array(1,3,3,3,3);
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

</script>
<script language="javascript" src="../shared/js/js_intra/datepickercontrol.js" ></script>
</html>