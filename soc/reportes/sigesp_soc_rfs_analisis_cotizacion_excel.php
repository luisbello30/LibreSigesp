<?php
    session_start();   
	ini_set('max_execution_time','0');

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_seguridad()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_insert_seguridad
		//		   Access: private 
		//	    Arguments: as_titulo // Título del reporte
		//    Description: función que guarda la seguridad de quien generó el reporte
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 25/06/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_soc;
		$ls_descripcion="Generó el Reporte Análisis de Cotización en Excel";
		$lb_valido=$io_fun_soc->uf_load_seguridad_reporte("SOC","sigesp_soc_p_analisis_cotizacion.php",$ls_descripcion);
		return $lb_valido;
	}
	//------------------------------------------------------------------------------------------------------

	//---------------------------------------------------------------------------------------------------------------------------
	// para crear el libro excel
	require_once ("../../shared/writeexcel/class.writeexcel_workbookbig.inc.php");
	require_once ("../../shared/writeexcel/class.writeexcel_worksheet.inc.php");
	$lo_archivo = tempnam("/tmp", "analisiscotizacion.xls");
	$lo_libro = &new writeexcel_workbookbig($lo_archivo);
	$lo_hoja = &$lo_libro->addworksheet();
	//---------------------------------------------------------------------------------------------------------------------------
	// para crear la data necesaria del reporte
	require_once("../../shared/ezpdf/class.ezpdf.php");
	require_once("../../shared/class_folder/class_funciones.php");
	$io_funciones=new class_funciones();				
	require_once("../class_folder/class_funciones_soc.php");
	$io_fun_soc=new class_funciones_soc();
	require_once("sigesp_soc_class_report.php");
	$io_class_report=new sigesp_soc_class_report();
	require_once("../../shared/class_folder/class_datastore.php");
	$io_ds=new class_datastore();				
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ls_titulo="ANÁLISIS DE COTIZACIONES";
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_tipsolcot=$io_fun_soc->uf_obtenervalor_get("tipsolcot","");
	$ls_numanacot=$io_fun_soc->uf_obtenervalor_get("numanacot","");
	$ld_fecha=$io_fun_soc->uf_obtenervalor_get("fecha","");
	$ls_observacion=$io_fun_soc->uf_obtenervalor_get("observacion","");
	//--------------------------------------------------------------------------------------------------------------------------------
	$lb_valido=uf_insert_seguridad(); // Seguridad de Reporte
	if($lb_valido)
	{
		$lb_valido=$io_class_report->uf_cargar_cotizaciones($ls_numanacot, $la_cotizaciones);
	}
	if(($lb_valido==false)) // Existe algún error ó no hay registros
	{
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');"); 
		print(" close();");
		print("</script>");
	}
	else // Imprimimos el reporte
	{
	//	$io_fun_soc->uf_loadmodalidad(&$li_len1,&$li_len2,&$li_len3,&$li_len4,&$li_len5,&$ls_titulo);
		$lo_encabezado= &$lo_libro->addformat();
		$lo_encabezado->set_bold();
		$lo_encabezado->set_font("Verdana");
		$lo_encabezado->set_align('center');
		$lo_encabezado->set_size('12');
		$lo_encabezado2= &$lo_libro->addformat();
		$lo_encabezado2->set_bold();
		$lo_encabezado2->set_font("Verdana");
		$lo_encabezado2->set_align('left');
		$lo_encabezado2->set_size('10');
		$lo_titulo= &$lo_libro->addformat();
		$lo_titulo->set_text_wrap();
		$lo_titulo->set_bold();
		$lo_titulo->set_font("Verdana");
		$lo_titulo->set_align('center');
		$lo_titulo->set_size('9');
		/////////////////////////////////////////////////
		$lo_titulocombinado1 =& $lo_libro->addformat();
		$lo_titulocombinado1->set_size('9');		
		$lo_titulocombinado1->set_bold();
		$lo_titulocombinado1->set_font("Verdana");
		$lo_titulocombinado1->set_align('center');
		$lo_titulocombinado1->set_merge(); # This is the key feature

		$lo_titulocombinado2 =& $lo_libro->addformat();
		$lo_titulocombinado2->set_size('9');		
		$lo_titulocombinado2->set_bold();
		$lo_titulocombinado2->set_font("Verdana");
		$lo_titulocombinado2->set_align('center');
		$lo_titulocombinado2->set_merge(); # This is the key feature
		
		$lo_datacentercombinado1 =& $lo_libro->addformat();
		$lo_datacentercombinado1->set_size('9');		
		$lo_datacentercombinado1->set_font("Verdana");
		$lo_datacentercombinado1->set_align('center');
		$lo_datacentercombinado1->set_merge(); # This is the key feature
		$lo_datacentercombinado1->set_align('vcenter');
		$lo_datacentercombinado1->set_align('vjustify');

		$lo_datacentercombinado2 =& $lo_libro->addformat();
		$lo_datacentercombinado2->set_size('9');		
		$lo_datacentercombinado2->set_font("Verdana");
		$lo_datacentercombinado2->set_align('center');
		$lo_datacentercombinado2->set_merge(); # This is the key feature
		$lo_datacentercombinado2->set_align('vcenter');
		$lo_datacentercombinado2->set_align('vjustify');
		
		$lo_dataleftcombinado1 =& $lo_libro->addformat();
		$lo_dataleftcombinado1->set_size('9');		
		$lo_dataleftcombinado1->set_font("Verdana");
		$lo_dataleftcombinado1->set_align('left');
		$lo_dataleftcombinado1->set_merge(); # This is the key feature

		$lo_dataleftcombinado2 =& $lo_libro->addformat();
		$lo_dataleftcombinado2->set_size('9');		
		$lo_dataleftcombinado2->set_font("Verdana");
		$lo_dataleftcombinado2->set_align('left');
		$lo_dataleftcombinado2->set_merge(); # This is the key feature
		/////////////////////////////////////////////////
		$lo_datacenter= &$lo_libro->addformat();
		$lo_datacenter->set_font("Verdana");
		$lo_datacenter->set_align('center');
		$lo_datacenter->set_size('9');
		$lo_dataleft= &$lo_libro->addformat();
		$lo_dataleft->set_text_wrap();
		$lo_dataleft->set_font("Verdana");
		$lo_dataleft->set_align('left');
		$lo_dataleft->set_size('9');
		$lo_dataright= &$lo_libro->addformat(array("num_format"=> "#,##0.00"));
		$lo_dataright->set_font("Verdana");
		$lo_dataright->set_align('right');
		$lo_dataright->set_size('9');
		$lo_hoja->set_column(0,0,30);
		$lo_hoja->set_column(1,1,15);
		$lo_hoja->set_column(2,2,15);
		$lo_hoja->set_column(3,3,15);
		$lo_hoja->set_column(4,4,15);
		$lo_hoja->set_column(5,5,15);
		$lo_hoja->set_column(6,6,15);
		$lo_hoja->set_column(7,7,15);
		$lo_hoja->set_column(8,8,15);
		$lo_hoja->set_column(9,9,15);
		$lo_hoja->set_column(10,10,20);
		$lo_hoja->set_column(11,11,20);
		$lo_hoja->set_column(12,12,15);
		$lo_hoja->set_column(13,13,15);
		$lo_hoja->set_column(14,14,15);
		$lo_hoja->write(1,3,$ls_titulo,$lo_encabezado);
		$lo_hoja->write(0,7,"NUMERO                    ".$ls_numanacot,$lo_encabezado2);
		$lo_hoja->write(1,7,"FECHA                       ".$ld_fecha,$lo_encabezado2);
		$lo_hoja->write(2,7,"LICITACION N° ",$lo_encabezado2);
		$lo_hoja->write(4, 0, "DATOS DEL PROVEEDOR",$lo_titulo);
		$lo_hoja->write(5, 0, "NOMBRE:",$lo_dataleft);
		$lo_hoja->write(6, 0, "CODIGO DE PROVEEDOR:",$lo_dataleft);
		$lo_hoja->write(7, 0, "FECHA Y REFERENCIA DE COTIZACION:",$lo_dataleft);
		$lo_hoja->write(8, 0, "DOMICILIO FISCAL:",$lo_dataleft);
		$li_total=count($la_cotizaciones);
		$li_pos=0;
		//print_r($la_cotizaciones);
		$lb_valido=$io_class_report->uf_select_items($ls_numanacot,$ls_tipsolcot,$la_articulos);
		$li_articulos=count($la_articulos);
		for($li_k=1;$li_k<=$li_articulos;$li_k++)
		{
			$li_line=10;
			$ls_codart=$la_articulos[$li_k]["codigo"];
			$ls_denominacion=$la_articulos[$li_k]["denominacion"];
			$lo_hoja->write($li_line+$li_k, $li_pos, $ls_denominacion,$lo_dataleft);
		}
		for($li_i=1;$li_i<=$li_total;$li_i++)
		{
			$ls_nompro=$la_cotizaciones[$li_i]["nompro"];
			$ls_codpro=$la_cotizaciones[$li_i]["cod_pro"];
			$ls_dirpro=$la_cotizaciones[$li_i]["dirpro"];
			$ls_rifpro=$la_cotizaciones[$li_i]["rifpro"];
			$ld_feccot=$la_cotizaciones[$li_i]["feccot"];
			$ls_numcot=$la_cotizaciones[$li_i]["numcot"];
			
			$ls_monsubtot=$la_cotizaciones[$li_i]["monsubtot"];
			$ls_monimpcot=$la_cotizaciones[$li_i]["monimpcot"];
			$ls_montotcot=$la_cotizaciones[$li_i]["montotcot"];
			$ls_garanacot=$la_cotizaciones[$li_i]["garanacot"];
			$ls_forpagcom=$la_cotizaciones[$li_i]["forpagcom"];
			$ls_diaentcom=$la_cotizaciones[$li_i]["diaentcom"];
			$ls_estesp=$la_cotizaciones[$li_i]["estesp"];
			if($ls_estesp=="1")
			{
				$ls_estesp="Si Cumple";
			}
			else
			{
				$ls_estesp="No Cumple";
			}
			$ls_estasitec=$la_cotizaciones[$li_i]["estasitec"];
			if($ls_estasitec=="1")
			{
				$ls_estasitec="Si";
			}
			else
			{
				$ls_estasitec="No";
			}
			$lb_valido=$io_class_report->uf_select_items_solicitud($ls_numcot,$ls_codpro,$ls_numanacot,$ls_tipsolcot,$io_ds,$li_j);
			for($li_k=1;$li_k<=$li_articulos;$li_k++)
			{
				$li_line=10;
				$ls_codart=$la_articulos[$li_k]["codigo"];
				$li_find=$io_ds->find("codigo",$ls_codart);
				
				$li_cantidad=number_format($io_ds->getValue("cantidad",$li_find),2,',','.');
				$li_precio=number_format($io_ds->getValue("precio",$li_find),2,',','.');
				$li_monto=number_format($io_ds->getValue("monto",$li_find),2,',','.');
				$lo_hoja->write($li_line+$li_k, $li_pos+1, $li_cantidad,$lo_dataright);
				$lo_hoja->write($li_line+$li_k, $li_pos+2, $li_precio,$lo_dataright);
				$lo_hoja->write($li_line+$li_k, $li_pos+3, $li_monto,$lo_dataright);
			}
			// Pintar los Detalles de la Cotizacion
			$li_newline=$li_line+$li_articulos+4;
			
			$li_pos++;
			$lo_hoja->write(4, $li_pos, "COTIZACION ".$li_i,$lo_titulocombinado1);
			$lo_hoja->write(5, $li_pos, $ls_nompro,$lo_datacentercombinado2);
			$lo_hoja->write(6, $li_pos, $ls_rifpro,$lo_datacentercombinado2);
			$lo_hoja->write(7, $li_pos, $ld_feccot,$lo_datacentercombinado2);
			$lo_hoja->write(8, $li_pos, $ls_dirpro,$lo_datacentercombinado2);
			$lo_hoja->write(10, $li_pos, "CANTIDAD",$lo_datacenter);
			$lo_hoja->write($li_newline, $li_pos, "Sub Total",$lo_dataleftcombinado1);
			$lo_hoja->write($li_newline+1, $li_pos, "IVA",$lo_dataleftcombinado1);
			$lo_hoja->write($li_newline+2, $li_pos, "TOTAL",$lo_dataleftcombinado1);
			$lo_hoja->write($li_newline+3, $li_pos, $ls_garanacot,$lo_datacentercombinado1);
			$lo_hoja->write($li_newline+4, $li_pos, $ls_forpagcom,$lo_datacentercombinado1);
			$lo_hoja->write($li_newline+5, $li_pos, $ls_diaentcom,$lo_datacentercombinado1);
			$lo_hoja->write($li_newline+6, $li_pos, $ls_estesp,$lo_datacentercombinado1);
			$lo_hoja->write($li_newline+7, $li_pos, $ls_estasitec,$lo_datacentercombinado1);
			
			$li_pos++;
			$lo_hoja->write_blank(4, $li_pos,                $lo_titulocombinado2);
			$lo_hoja->write_blank(5, $li_pos,                $lo_titulocombinado2);
			$lo_hoja->write_blank(6, $li_pos,                $lo_titulocombinado2);
			$lo_hoja->write_blank(7, $li_pos,                $lo_titulocombinado2);
			$lo_hoja->write_blank(8, $li_pos,                $lo_titulocombinado2);
			$lo_hoja->write(10, $li_pos, "PRECIO UNITARIO",$lo_datacenter);
			$lo_hoja->write_blank($li_newline, $li_pos,            $lo_titulocombinado2);
			$lo_hoja->write_blank($li_newline+1, $li_pos,            $lo_titulocombinado2);
			$lo_hoja->write_blank($li_newline+2, $li_pos,            $lo_titulocombinado2);
			$lo_hoja->write_blank($li_newline+3, $li_pos,                $lo_titulocombinado2);
			$lo_hoja->write_blank($li_newline+4, $li_pos,                $lo_titulocombinado2);
			$lo_hoja->write_blank($li_newline+5, $li_pos,                $lo_titulocombinado2);
			$lo_hoja->write_blank($li_newline+6, $li_pos,                $lo_titulocombinado2);
			$lo_hoja->write_blank($li_newline+7, $li_pos,                $lo_titulocombinado2);
			
			$li_pos++;
			$lo_hoja->write_blank(4, $li_pos,                $lo_titulocombinado2);
			$lo_hoja->write_blank(5, $li_pos,                $lo_titulocombinado2);
			$lo_hoja->write_blank(6, $li_pos,                $lo_titulocombinado2);
			$lo_hoja->write_blank(7, $li_pos,                $lo_titulocombinado2);
			$lo_hoja->write_blank(8, $li_pos,                $lo_titulocombinado2);
			$lo_hoja->write(10, $li_pos, "TOTAL",$lo_datacenter);
			$lo_hoja->write($li_newline, $li_pos, $ls_monsubtot,$lo_dataright);
			$lo_hoja->write($li_newline+1, $li_pos, $ls_monimpcot,$lo_dataright);
			$lo_hoja->write($li_newline+2, $li_pos, $ls_montotcot,$lo_dataright);
			$lo_hoja->write_blank($li_newline+3, $li_pos,                $lo_titulocombinado2);
			$lo_hoja->write_blank($li_newline+4, $li_pos,                $lo_titulocombinado2);
			$lo_hoja->write_blank($li_newline+5, $li_pos,                $lo_titulocombinado2);
			$lo_hoja->write_blank($li_newline+6, $li_pos,                $lo_titulocombinado2);
			$lo_hoja->write_blank($li_newline+7, $li_pos,                $lo_titulocombinado2);
		}
		$lo_hoja->write($li_newline+3, 0, "GARANTIAS",$lo_dataleft);
		$lo_hoja->write($li_newline+4, 0, "CONDICIONES DE PAGO",$lo_dataleft);
		$lo_hoja->write($li_newline+5, 0, "FECHA DE ENTREGA (DIAS)",$lo_dataleft);
		$lo_hoja->write($li_newline+6, 0, "CUMPLE CON ESPECIFICACIONES",$lo_dataleft);
		$lo_hoja->write($li_newline+7, 0, "ASISTENCIA TECNICA",$lo_dataleft);
		$lo_hoja->write($li_newline+8, 0, "OBSERVACIONES:",$lo_encabezado2);
		$lo_hoja->write($li_newline+8, 1, $ls_observacion,$lo_dataleftcombinado1);
		for($li_k=1;$li_k<$li_pos;$li_k++)
		{
			$lo_hoja->write_blank($li_newline+8, $li_k+1,                $lo_titulocombinado2);
		}
		/////////////////   Cuadro de Ganadores
		$lo_hoja->write($li_newline+10, 5, "Resumen de Proveedores Ganadores",$lo_encabezado);
		$lo_hoja->write($li_newline+11, 0, "Código",$lo_dataleftcombinado1);
		$lo_hoja->write($li_newline+11, 1, "Nombre",$lo_dataleftcombinado1);
		$lo_hoja->write_blank($li_newline+11, 2,                $lo_dataleftcombinado2);
		$lo_hoja->write_blank($li_newline+11, 3,                $lo_dataleftcombinado2);

		$lo_hoja->write($li_newline+11, 4, "Subtotal Bs.",$lo_dataleftcombinado1);
		$lo_hoja->write_blank($li_newline+11, 5,                $lo_dataleftcombinado2);

		$lo_hoja->write($li_newline+11, 6, "Total Cargos Bs.",$lo_dataleftcombinado1);
		$lo_hoja->write_blank($li_newline+11, 7,                $lo_dataleftcombinado2);

		$lo_hoja->write($li_newline+11, 8, "Monto Total Bs.",$lo_dataleftcombinado1);
		$lo_hoja->write_blank($li_newline+11, 9,                $lo_dataleftcombinado2);
		
		$la_ganadores=$io_class_report->uf_select_cotizacion_analisis($ls_numanacot,$ls_tipsolcot);
		$li_totalganadores=count($la_ganadores);
		$li_j=0;
		for($li_i=$li_newline+12;$li_i<$li_totalganadores+$li_newline+12;$li_i++)
		{
			$ls_proveedor= $la_ganadores[$li_j]["cod_pro"];
			$ls_cotizacion= $la_ganadores[$li_j]["numcot"];
			$ls_tipo_proveedor= $la_ganadores[$li_j]["tipconpro"];
			$ls_nombre=$la_ganadores[$li_j]["nompro"];
			$io_class_report->uf_select_items_proveedor($ls_cotizacion,$ls_proveedor,$ls_numanacot,$ls_tipsolcot,$la_items,$li_totrow); 
			$io_class_report->uf_calcular_montos($li_totrow,$la_items,$la_totales,$ls_tipo_proveedor);
			$li_subtotal=number_format($la_totales["subtotal"],2,",",".");
			$li_cargo=number_format($la_totales["totaliva"],2,",",".");
			$li_total=number_format($la_totales["total"],2,",",".");
			$lo_hoja->write($li_i, 0, $ls_proveedor,$lo_dataleftcombinado1);
			$lo_hoja->write($li_i, 1, $ls_nombre,$lo_dataleftcombinado1);
			$lo_hoja->write_blank($li_i, 2,                $lo_dataleftcombinado2);
			$lo_hoja->write_blank($li_i, 3,                $lo_dataleftcombinado2);
	
			$lo_hoja->write($li_i, 4, $li_subtotal,$lo_dataleftcombinado1);
			$lo_hoja->write_blank($li_i, 5,                $lo_dataleftcombinado2);
	
			$lo_hoja->write($li_i, 6, $li_cargo,$lo_dataleftcombinado1);
			$lo_hoja->write_blank($li_i, 7,                $lo_dataleftcombinado2);
	
			$lo_hoja->write($li_i, 8, $li_total,$lo_dataleftcombinado1);
			$lo_hoja->write_blank($li_i, 9,                $lo_dataleftcombinado2);
			$li_j=$li_j+1;

		}
		
		$lo_libro->close();
		header("Content-Type: application/x-msexcel; name=\"analisiscotizacion.xls\"");
		header("Content-Disposition: inline; filename=\"analisiscotizacion.xls\"");
		$fh=fopen($lo_archivo, "rb");
		fpassthru($fh);
		unlink($lo_archivo);
		print("<script language=JavaScript>");
		print(" close();");
		print("</script>");
		unset($io_pdf);
	}
	unset($io_report);
	unset($io_funciones);
	unset($io_fun_soc);
?> 