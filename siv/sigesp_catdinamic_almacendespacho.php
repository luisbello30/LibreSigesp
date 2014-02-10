<?php
session_start();
  //-----------------------------------------------------------------------------------------------------------------------------------
   	function uf_formatonumerico($as_valor)
   	{
		//////////////////////////////////////////////////////////////////////////////
		//	Function:     uf_formatonumerico
		//	Arguments:    as_valor  // valor sin formato numérico
		//	Returns:	  $as_valor valor numérico formateado
		//	Description:  Función que le da formato a los valores numéricos que vienen de la BD
		//////////////////////////////////////////////////////////////////////////////
		$as_valor=str_replace(".",",",$as_valor);
		$li_poscoma = strpos($as_valor, ",");
		$li_contador = 1;
		if ($li_poscoma==0)
		{
			$li_poscoma = strlen($as_valor);
			$as_valor = $as_valor.",00";
		}
		$as_valor = substr($as_valor,0,$li_poscoma+3);
		$li_poscoma = $li_poscoma - 1;
		for($li_index=$li_poscoma;$li_index>=0;--$li_index)
		{
			if(($li_contador==3)&&(($li_index-1)>=0)) 
			{
				$as_valor = substr($as_valor,0,$li_index).".".substr($as_valor,$li_index);
				$li_contador=1;
			}
			else
			{
				$li_contador=$li_contador + 1;
			}
		}
		return $as_valor;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------
  //-----------------------------------------------------------------------------------------------------------------------------------
   	function uf_obtener_alternos($as_codart)
   	{
		//////////////////////////////////////////////////////////////////////////////
		//	Function:     uf_obtener_alternos
		//	Arguments:    as_codart  // Codigo de articulo
		//	Returns:	  $la_alternos arreglo que contiene codigos alternos
		//	Description:  Función que obtiene los codigos alternos relacionados con determinado articulo
		//////////////////////////////////////////////////////////////////////////////
		global $io_sql;
		$la_alternos="";
		$li_i=0;
		$ls_sql="SELECT codart".
				"  FROM siv_articulo".
				" WHERE codemp='".$_SESSION["la_empresa"]["codemp"]."'".
				"   AND codartpri='".$as_codart."' ";
		$rs_data=$io_sql->select($ls_sql);
		while(!$rs_data->EOF)
		{
			$li_i++;
			$la_alternos[$li_i]= $rs_data->fields["codart"];
			$rs_data->MoveNext();
		}
		return $la_alternos;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------
  //-----------------------------------------------------------------------------------------------------------------------------------
   	function uf_obtener_datos_articulo($as_codart,&$ls_denart,&$ls_sccuenta,&$li_unidad)
   	{
		//////////////////////////////////////////////////////////////////////////////
		//	Function:     uf_obtener_datos_articulo
		//	Arguments:    as_codart  // Codigo de articulo
		//	Returns:	  $la_alternos arreglo que contiene codigos alternos
		//	Description:  Función que obtiene los datos relacionados al articulo.
		//////////////////////////////////////////////////////////////////////////////
		global $io_sql,$io_msg;
		$ls_denart="";
		$ls_sccuenta="";
		$li_unidad="";
		$lb_valido=true;
		$ls_sql= "SELECT denart,sc_cuenta,".
				 "       (SELECT unidad FROM siv_unidadmedida ".
				 "         WHERE siv_articulo.codunimed=siv_unidadmedida.codunimed) AS unidad".
				 "  FROM siv_articulo".
				 " WHERE codemp='". $_SESSION["la_empresa"]["codemp"] ."'".
				 "   AND codart='". $as_codart ."'";
		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{
			print $io_sql->message;
			$lb_valido=false;
		}
		else
		{
			while(!$rs_data->EOF)
			{
				$ls_denart= $rs_data->fields["denart"];
				$ls_sccuenta= $rs_data->fields["sc_cuenta"];
				$li_unidad= $rs_data->fields["unidad"];
				$rs_data->MoveNext();
			}
		}
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------



?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Articulos Disponibles por Almac&eacute;n </title>
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
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<script type="text/javascript" language="JavaScript1.2" src="../shared/js/number_format.js"></script>
<script type="text/javascript" language="JavaScript1.2" src="js/funciones.js"></script>
<script type="text/javascript" language="JavaScript1.2" src="js/funcion_siv.js"></script>
</head>

<body>
<?php
	require_once("../shared/class_folder/sigesp_include.php");
	require_once("../shared/class_folder/class_mensajes.php");
	require_once("../shared/class_folder/class_datastore.php");
	require_once("../shared/class_folder/class_sql.php");
	require_once("class_funciones_inventario.php");
	$io_fun_inventario= new class_funciones_inventario();
	$in     =new sigesp_include();
	$con    =$in->uf_conectar();
	$io_msg =new class_mensajes();
	$ds_despacho= new class_datastore();
	$io_sql =new class_sql($con);
	
	$ls_codemp=$_SESSION["la_empresa"]["codemp"];

	if (array_key_exists("linea",$_GET))
	{
		$li_linea=  $_GET["linea"];
		$ls_codart= $_GET["codart"];
		$li_cansol= $_GET["cansol"];
		$li_penart= $_GET["penart"];
		$ls_opunidad=$_GET["unidad"];
		$ls_sql="SELECT denart FROM siv_articulo".
				" WHERE codemp = '".$ls_codemp."'".
				"   AND codart = '".$ls_codart."'";
				
		$rs_data=$io_sql->select($ls_sql);
		if($row=$io_sql->fetch_row($rs_data))
		{
			$ls_denart= $row["denart"];
		}
	}
	else
	{
		if(array_key_exists("hidlinea",$_POST))
		{
			$li_linea=$_POST["hidlinea"];
		}
		else
		{
			$li_linea="";
		}
	}

	if(array_key_exists("operacion",$_POST))
	{
		$ls_operacion=  $_POST["operacion"];
		$ls_codalm=		$_POST["hidalmacen"];
		$ls_codart=		$_POST["txtcodart"];
		$ls_denart=		$_POST["txtdenart"];
		$li_existencia=	$_POST["hidexistencia"];
	}
	else
	{
		$ls_operacion="";
	
	}

?>
<form name="form1" method="post" action="">
  <p align="center">
    <input name="operacion" type="hidden" id="operacion">
    <input name="hidstatus" type="hidden" id="hidstatus">
</p>
  <table width="422" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
    <tr>
      <td width="420">          <input name="txtdenart" type="text" class="sin-borde2" id="txtdenart" value="<?php print $ls_denart ?>" size="40">
            <input name="txtcodart" type="hidden" id="txtcodart" value="<?php print $ls_codart ?>">
    </tr>
  </table>  
  <table width="422" height="21" border="0" align="center" cellpadding="1" cellspacing="1">
    <tr>
      <td width="414" colspan="2" class="titulo-celda">Articulos Disponibles por Almac&eacute;n </td>
    </tr>
  </table>    
  <?php
	print "<table width=422 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
	print "<tr class=titulo-celda>";
	print "<td>Artículo</td>";
	print "<td>Almacén</td>";
	print "<td>Existencia Actual (Detal)</td>";
	print "<td>Cantidad a Despachar</td>";
	print "</tr>";
	if($ls_operacion=="")
	{
		$ls_aux="";
		$la_alternos=uf_obtener_alternos($ls_codart);
		if(!empty($la_alternos))
		{
			$li_total=count($la_alternos);
			if($li_total>0)
			{
				for($li_i=1;$li_i<=$li_total;$li_i++)
				{
					$ls_aux=$ls_aux."OR codart='".$la_alternos[$li_i]."'";
				}
			}
		}
		$ls_aux=$ls_aux. ")";
		$ls_sql="SELECT siv_articuloalmacen.*,".
				"      (SELECT nomfisalm FROM siv_almacen".
				"        WHERE siv_articuloalmacen.codalm=siv_almacen.codalm) AS nomfisalm".
				"  FROM siv_articuloalmacen".
				" WHERE codemp = '".$ls_codemp."'".
				"   AND (codart = '".$ls_codart."'".
				$ls_aux.
				" ORDER BY codalm";
		$rs_data=$io_sql->select($ls_sql);
		$li_i=0;
		$li_rows=$io_sql->num_rows($rs_data);		
		if($li_rows>0)
		{
			while($row=$io_sql->fetch_row($rs_data))
			{
			    $li_existencia=$row["existencia"];
				//$li_existencia=0;
				$li_existenciaaux=$row["existencia"];				
				if ($li_existenciaaux!=0)
				{
					$li_i=$li_i +1;
					print "<tr class=celdas-blancas>";
					$ls_codart    =    $row["codart"];
					$ls_codalm    =    $row["codalm"];
					$ls_nomfisalm = $row["nomfisalm"];
					print "<td><a href=\"javascript: aceptar('$ls_codart','$ls_codalm','$li_existencia','$li_linea');\">".$ls_codart."</a></td>";
					print "<td>".$ls_nomfisalm."</td>";
//					print "<td>".$li_existenciaaux."</td>";
					print "<td><input  name=txtexiart".$li_i." type=text id=txtexiart".$li_i." class=sin-borde size=12 maxlength=12 value='".number_format ($li_existenciaaux,2,",",".")."' readonly>".
						  "<input  name=txtcodart".$li_i." type=hidden id=txtcodart".$li_i." class=sin-borde size=12 value='".$ls_codart."'>".
						  "<input  name=txtcodalm".$li_i." type=hidden id=txtcodalm".$li_i." class=sin-borde size=12 value='".$ls_codalm."'></td>";
					print "<td><input  name=txtcanart".$li_i." type=text id=txtcanart".$li_i." class=sin-borde size=12 maxlength=12 value='0,00' onKeyPress=return(ue_formatonumero(this,'.',',',event));></td>";
					print "</tr>";			
				}
				if($li_existenciaaux!=0)
				{
					$li_existencia=$li_existenciaaux;
					$li_existenciaaux=uf_formatonumerico($li_existenciaaux);
				}
			}
		}
		else 
		{
			$io_msg->message("No hay existencia de articulos en el almacen ");
			print "<script language= javascript>";
			print "close();";
			print "</script>";
		}
		$li_totrow=$li_i;
		print "</table>";
	}
	if($ls_operacion=="BUSCAR")
	{
		if(array_key_exists("hidexistencia",$_POST))
		{
			$li_existencia= $_POST["hidexistencia"];
			$ls_codartaux= $_POST["hidarticulo"];
		}
			$ls_sql="SELECT * FROM siv_config";
			$li_exec=$io_sql->select($ls_sql);
			if($row=$io_sql->fetch_row($li_exec))
			{
				$ls_metodo=$row["metodo"];
			}
			$ls_metodo=trim($ls_metodo);
			if($ls_metodo=="FIFO")
			{
				$ls_sql="SELECT * FROM siv_dt_movimiento".
						" WHERE  codart='". $ls_codartaux ."'".
						" AND codalm='". $ls_codalm ."'".
						" AND opeinv='ENT' AND numdocori NOT IN".
						" (SELECT numdocori FROM siv_dt_movimiento".
						"   WHERE opeinv ='REV')".
						" ORDER BY nummov";  
				$rs_data=$io_sql->select($ls_sql);
			}
	
			if($ls_metodo=="LIFO")
			{
				$ls_sql="SELECT * FROM siv_dt_movimiento".
						" WHERE  codart='". $ls_codartaux ."'".
						" AND codalm='". $ls_codalm ."'".
						" AND opeinv='ENT' AND numdocori NOT IN".
						" (SELECT numdocori FROM siv_dt_movimiento".
						"   WHERE opeinv ='REV')".
						" ORDER BY nummov DESC";
				$rs_data=$io_sql->select($ls_sql); 
			}	
			if($ls_metodo=="CPP")
			{
				$ls_sql="SELECT Avg(cosart) as cosart, nummov".
						" FROM siv_dt_movimiento".
						" WHERE  codart='". $ls_codartaux ."'".
						" AND codalm='". $ls_codalm ."'".
						" AND opeinv='ENT' AND codprodoc<>'REV' AND numdocori NOT IN".
						" (SELECT numdocori FROM siv_dt_movimiento".
						"   WHERE opeinv ='REV')".
						" GROUP BY nummov".
                        " ORDER BY nummov DESC"; 
				$rs_data=$io_sql->select($ls_sql);
			}	
			if($ls_metodo!="CPP")
			{
				$lb_break=false;
				while(($row=$io_sql->fetch_row($rs_data))&&(!$lb_break))
				{
					$li_preuniart=$row["cosart"];
					$ls_numdocori=$row["numdocori"];
					$li_preuniart=uf_formatonumerico($li_preuniart);
/*					$ls_sql="SELECT SUM(CASE opeinv WHEN 'ENT' THEN canart ELSE -canart END) total FROM siv_dt_movimiento".
							" WHERE codart='". $ls_codart ."'".
							" AND codalm='". $ls_codalm ."'".
							" AND numdocori='". $ls_numdocori ."'".
							" AND numdocori NOT IN".
							"  (SELECT numdocori FROM siv_dt_movimiento".
							"    WHERE opeinv ='REV')".
							" ORDER BY nummov";
//					print $ls_sql;		
					$li_exec1=$io_sql->select($ls_sql);
					if($row1=$io_sql->fetch_row($li_exec1))
					{
						$li_existencia=$row1["total"];
/*						if ($li_existencia > 0)
						{
							$lb_break=true;
							$io_sql->free_result($li_exec1);
						}
						
					}  //fin  if($row=$io_sql->fetch_row($li_exec))
	
*/				}
			}
			else
			{
				if($row=$io_sql->fetch_row($rs_data))
				{
					$ls_numdocori="";
					$li_preuniart=$row["cosart"];
					$li_preuniart=uf_formatonumerico($li_preuniart);
				}
			}
			
			print "<script language= javascript>";
			print "obj=eval(opener.document.form1.txtcodart".$li_linea.");";
			print "obj.value='".$ls_codartaux."';";
			print "obj=eval(opener.document.form1.txtcodalm".$li_linea.");";
			print "obj.value='".$ls_codalm."';";
			print "obj1=eval(opener.document.form1.hidexistencia".$li_linea.");";
			print "obj1.value=".$li_existencia.";";
			print "obj2=eval(opener.document.form1.txtpreuniart".$li_linea.");";
			print "obj2.value='".$li_preuniart."';";
			print "obj3=eval(opener.document.form1.hidnumdocori".$li_linea.");";
			print "obj3.value='".$ls_numdocori."';";
			print "close();";
			print "</script>";
			
	} // fin if($ls_operacion=="buscar")
	if($ls_operacion=="AGREGAR")
	{
		if(array_key_exists("despacho",$_SESSION))
		{
			unset($_SESSION["despacho"]);
		}
		$li_totrow=$io_fun_inventario->uf_obtenervalor("totrow",1);
		$ls_unidad=$io_fun_inventario->uf_obtenervalor("opunidad","Detal");
		$li_cansol=$io_fun_inventario->uf_obtenervalor("cansol","0");
		$ls_codartpri=$io_fun_inventario->uf_obtenervalor("txtcodart","");
		$li_pendiente=$li_penart=$io_fun_inventario->uf_obtenervalor("penart","");
		$li_contador=0;
		$li_total=0;
		for($li_i=1;$li_i<=$li_totrow;$li_i++)
		{
			$ls_codart=$io_fun_inventario->uf_obtenervalor("txtcodart".$li_i,"");
			$ls_codalm=$io_fun_inventario->uf_obtenervalor("txtcodalm".$li_i,"");
			$li_canart=$io_fun_inventario->uf_obtenervalor("txtcanart".$li_i,"");
			$li_exiart=$io_fun_inventario->uf_obtenervalor("txtexiart".$li_i,"");
			$li_canartaux=$io_fun_inventario->uf_formatocalculo($li_canart);
			if($li_canartaux>0)
			{
				$ls_sql="SELECT metodo FROM siv_config";
				$li_exec=$io_sql->select($ls_sql);
				if($row=$io_sql->fetch_row($li_exec))
				{
					$ls_metodo=$row["metodo"];
				}
				$ls_metodo=trim($ls_metodo);
				switch($ls_metodo)
				{
					case"FIFO";
						$ls_sql="SELECT cosart FROM siv_dt_movimiento".
								" WHERE codemp='". $ls_codemp ."'".
								"   AND codart='". $ls_codart ."'".
								"   AND codalm='". $ls_codalm ."'".
								"   AND opeinv='ENT' AND numdocori NOT IN".
								"       (SELECT numdocori FROM siv_dt_movimiento".
								"         WHERE opeinv ='REV')".
								" ORDER BY nummov";  
					break;
					case"LIFO";
						$ls_sql="SELECT cosart FROM siv_dt_movimiento".
								" WHERE codemp='". $ls_codemp ."'".
								"   AND codart='". $ls_codart ."'".
								"   AND codalm='". $ls_codalm ."'".
								"   AND opeinv='ENT' AND numdocori NOT IN".
								"       (SELECT numdocori FROM siv_dt_movimiento".
								"         WHERE opeinv ='REV')".
								" ORDER BY nummov DESC";
					break;
					case"CPP";
						$ls_sql="SELECT Avg(cosart) as cosart, nummov".
								"  FROM siv_dt_movimiento".
								" WHERE codemp='". $ls_codemp ."'".
								"   AND codart='". $ls_codart ."'".
								"   AND codalm='". $ls_codalm ."'".
								"   AND opeinv='ENT' AND codprodoc<>'REV' AND numdocori NOT IN".
								"       (SELECT numdocori FROM siv_dt_movimiento".
								"         WHERE opeinv ='REV')".
								" GROUP BY nummov".
								" ORDER BY nummov DESC"; 
					break;
				}
				$rs_data=$io_sql->select($ls_sql);
				if($row=$io_sql->fetch_row($rs_data))
				{
					$li_preuniart=$row["cosart"];
					$li_preuniart=uf_formatonumerico($li_preuniart);
				}
				$lb_valido=uf_obtener_datos_articulo($ls_codart,&$ls_denart,&$ls_sccuenta,&$li_unidad);
				if($lb_valido)
				{
					$li_contador++;
					$li_pendiente=$li_pendiente-$li_canart;
					$_SESSION["despacho"]["codart".$li_contador]=$ls_codart;
					$_SESSION["despacho"]["codalm".$li_contador]=$ls_codalm;
					$_SESSION["despacho"]["denart".$li_contador]=$ls_denart;
					$_SESSION["despacho"]["sc_cuenta".$li_contador]=$ls_sccuenta;
					$_SESSION["despacho"]["preuniart".$li_contador]=$li_preuniart;
					$_SESSION["despacho"]["unidad".$li_contador]=$li_unidad;
					$_SESSION["despacho"]["canart".$li_contador]=$li_canart;
					$_SESSION["despacho"]["exiart".$li_contador]=$li_exiart;
					$_SESSION["despacho"]["penart".$li_contador]=$li_pendiente;
					$li_total=$li_total+$li_canartaux;
				}
			}
		}
		$_SESSION["despacho"]["contador"]=$li_contador;
		$_SESSION["despacho"]["unidad"]=$ls_unidad;
		$_SESSION["despacho"]["cansol"]=$li_cansol;
		$_SESSION["despacho"]["codartpri"]=$ls_codartpri;
		$_SESSION["despacho"]["penart"]=$li_penart;
		$_SESSION["despacho"]["totart"]=$li_total;
		if($lb_valido)
		{
			$ls_opeopener="AGREGARDETALLES";
			print "<script>";
			print "opener.document.form1.operacion.value=";
			print "obj=eval(opener.document.form1.operacion);";
			print "obj.value='".$ls_opeopener."';";
			print "opener.document.form1.submit();";
			print "close();";
			print "</script>";
		}

	}
?>
      <input name="hidalmacen" type="hidden" id="hidalmacen" value="<?php print $ls_codalm ?>">
      <input name="hidexistencia" type="hidden" id="hidexistencia" value="<?php print $li_existencia ?>"></td>

</div>
<input name="hidlinea" type="hidden" id="hidlinea" value="<?php print $li_linea?>">
<input name="hidarticulo" type="hidden" id="hidarticulo" value="<?php print $ls_codartaux; ?>">
<input name="cansol" type="hidden" id="cansol" value="<?php print $li_cansol; ?>">
<input name="penart" type="hidden" id="penart" value="<?php print $li_penart; ?>">
<input name="totrow" type="hidden" id="totrow" value="<?php print $li_totrow; ?>">
<input name="opunidad" type="hidden" id="opunidad" value="<?php print $ls_opunidad; ?>">
<table width="422" height="22" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><div align="right"><a href="javascript: ue_agregar();"><img src="../shared/imagebank/aprobado.gif" alt="Aceptar" width="15" height="15" class="sin-borde">Aceptar</a> <a href="javascript: ue_cancelar();"><img src="../shared/imagebank/eliminar.gif" alt="Cancelar" width="15" height="15" class="sin-borde">Cancelar</a></div></td>
  </tr>
</table>
</form>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script language="JavaScript">
	function aceptar(ls_codart,ls_codalm,li_existencia,li_linea)
	{
		f= document.form1;
		f.hidalmacen.value= ls_codalm;
		f.hidarticulo.value= ls_codart;
		f.hidexistencia.value= li_existencia;
		f.operacion.value="BUSCAR";
		f.action="sigesp_catdinamic_almacendespacho.php";
		f.submit();
	}
	
	function ue_search()
  	{
		f=document.form1;
		f.operacion.value="BUSCAR";
		f.action="sigesp_catdinamic_almacen.php";
		f.submit();
	}
	
	function ue_agregar()
	{
		f=document.form1;
		penart=f.penart.value;
		cansol=f.cansol.value;
		totrow=f.totrow.value;
		total=0;
		for(i=1;i<=totrow;i++)
		{
			existencia= eval("f.txtexiart"+i+".value");
			cantidad= eval("f.txtcanart"+i+".value");
			existencia=ue_formato_calculo(existencia);
			cantidad=ue_formato_calculo(cantidad);
			if(parseFloat(existencia)<parseFloat(cantidad))
			{
				alert("No existe existencia para despachar la cantidad solicitada.");
				objeto=eval("f.txtcanart"+i);
				objeto.value="0,00";
				total=0;
				break;
			}
			else
			{
				total=(parseFloat(total) + parseFloat(cantidad));
			}
		}
		if(total>0)
		{
			if(penart>0)
			{
				if(total<=penart)
				{
					f.operacion.value="AGREGAR";
					f.action="sigesp_catdinamic_almacendespacho.php";
					f.submit();
					opener.form1.rdtipodespacho[1].checked=true;
				}
				else
				{
					alert("El total de articulos a despachar es mayor que el solicitado/pendiente.");
				}
			}
			else
			{
				if(total<=cansol)
				{
					f.operacion.value="AGREGAR";
					f.action="sigesp_catdinamic_almacendespacho.php";
					f.submit();
					opener.form1.rdtipodespacho[1].checked=true;
				}
				else
				{
					alert("El total de articulos a despachar es mayor que el solicitado/pendiente.");
				}
			}
		}
		else
		{
			alert("Debe indicar las cantides a despachar.");
		}
	}
	
</script>
</html>
