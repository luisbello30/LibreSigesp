<?php
	session_start();
	ini_set('memory_limit','256M');
	ini_set('max_execution_time ','0');
	
	if(!array_key_exists("la_logusr",$_SESSION))
	{
		print "<script language=JavaScript>";
		print "close();";
		print "</script>";		
	}
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina(&$li_row,&$lo_titulo,$lo_hoja,$lo_encabezado,$as_titulo,$as_moneda)
	{//uf_print_encabezado_pagina(&$li_row,&$lo_titulo,$lo_hoja,$lo_encabezado,$ls_titulo,'(En Bolivares Fuertes)'); 
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//       Function: uf_print_encabezadopagina
	//		   Acess: private 
	//	Arguments: as_titulo // Título del Reporte
	//	    	   as_periodo_comp // Descripción del periodo del comprobante
	//	    	   as_fecha_comp // Descripción del período de la fecha del comprobante 
	//	    	   io_pdf // Instancia de objeto pdf
	//    Description: función que imprime los encabezados por página
	//     Creado Por: Ing. Yozelin Barragán
	// Fecha Creación: 26/06/2006 
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		$lo_hoja->write($li_row, 5, $as_titulo,$lo_titulo);
		$li_row++;
		$lo_hoja->write($li_row, 5, $as_moneda,$lo_titulo);
		$li_row++;
		
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_titulo_reporte(&$li_row,&$lo_titulo,$lo_hoja,$lo_encabezado,$io_encabezado)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		    Acess: private 
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   as_periodo_comp // Descripción del periodo del comprobante
		//	    		   as_fecha_comp // Descripción del período de la fecha del comprobante 
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Yozelin Barragán
		// Fecha Creación: 26/06/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		$ls_codemp    = $_SESSION["la_empresa"]["codemp"];
		$ls_nombre    = $_SESSION["la_empresa"]["nombre"];
		$ls_nomorgads = $_SESSION["la_empresa"]["nomorgads"];
		$ls_codasiona = $_SESSION['la_empresa']['codasiona'];

		require_once("../../shared/class_folder/class_funciones.php");
		$io_funciones = new class_funciones();	
		$ls_periodo   = $io_funciones->uf_convertirfecmostrar(substr($_SESSION['la_empresa']['periodo'],0,10));
		
		$lo_hoja->write($li_row, 0, "CODIGO PRESUPUESTARIO DEL ENTE: $ls_codasiona ",$lo_titulo);
		$li_row++;
		$lo_hoja->write($li_row, 0, "DENOMINACION: $ls_nombre ",$lo_titulo);
		$li_row++;
		$lo_hoja->write($li_row, 0, "ORGANO DE ADSCRIPCION: $ls_nomorgads ",$lo_titulo);
		$li_row++;
		$lo_hoja->write($li_row, 0, "PERIODO PRESUPUESTARIO: $ls_periodo ",$lo_titulo);
		$li_row++;
		
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_titulo(&$li_row,&$lo_titulo,$lo_hoja,$lo_encabezado,$io_titulo,$as_trimestre)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_titulo
		//		   Access: private 
		//      Arguments: as_codper // total de registros que va a tener el reporte
		//	    		   as_nomper // total de registros que va a tener el reporte
		//	    		   io_pdf // total de registros que va a tener el reporte
		//    Description: función que imprime la cabecera de cada página
		//     Creado Por: Ing. Yozelin Barragán
		// Fecha Creación: 26/06/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$li_row++;
		
		$lo_hoja->write($li_row, 5, "EJECUTADO EN EL TRIMESTRE $as_trimestre ",$lo_titulo);
		$lo_hoja->write($li_row, 8, "ACUMULADO AL TRIMESTRE $as_trimestre ",$lo_titulo);
		
		$li_row++;

	}// end function uf_print_titulo
	//--------------------------------------------------------------------------------------------------------------------------------	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera(&$li_row,&$lo_titulo,$lo_hoja,$lo_encabezado,$io_cabecera,$as_trimestre)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: private 
		//	    Arguments: as_codper // total de registros que va a tener el reporte
		//	    		   as_nomper // total de registros que va a tener el reporte
		//	    		   io_pdf // total de registros que va a tener el reporte
		//    Description: función que imprime la cabecera de cada página
		//	   Creado Por: Ing. Yozelin Barragán
		// Fecha Creación: 26/06/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		$li_row++;
		$lo_hoja->write($li_row, 0, "PARTIDA",$lo_titulo);
		$lo_hoja->write($li_row, 1, "DENOMINACION",$lo_titulo);
		$lo_hoja->write($li_row, 2, "PRESUPUESTO APROBADO",$lo_titulo);
		$lo_hoja->write($li_row, 3, "PRESUPUESTO MODIFICADO",$lo_titulo);
		$lo_hoja->write($li_row, 4, "PROGRAMADO EN EL TRIMESTRE $as_trimestre ",$lo_titulo);
		$lo_hoja->write($li_row, 5, "DEVENGADO",$lo_titulo);
		$lo_hoja->write($li_row, 6, "LIQUIDADO",$lo_titulo);
		$lo_hoja->write($li_row, 7, "RECAUDADO",$lo_titulo);
		$lo_hoja->write($li_row, 8, "PROGRAMADO",$lo_titulo);
		$lo_hoja->write($li_row, 9, "DEVENGADO",$lo_titulo);
		$lo_hoja->write($li_row, 10, "LIQUIDADO",$lo_titulo);
		$lo_hoja->write($li_row, 11, "RECAUDADO",$lo_titulo);
		$lo_hoja->write($li_row, 12, "INGRESOS POR RECIBIR",$lo_titulo);
		$li_row++;
		
	
	}// end function uf_print_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle(&$li_row,&$lo_titulo,$lo_hoja,$lo_datacenter,$lo_dataleft,$lo_dataright,$la_data)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		    Acess: private 
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Objeto PDF
		//    Description: función que imprime el detalle
		//	   Creado Por: Ing. Yozelin Barragán
		// Fecha Creación: 26/06/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		for( $i = 1; $i < count($la_data); $i ++)
		{
			$lo_hoja->write($li_row, 0, $la_data[$i]['cuenta'],$lo_datacenter);
			$lo_hoja->write($li_row, 1, $la_data[$i]['denominacion'],$lo_dataleft);
			$lo_hoja->write($li_row, 2, $la_data[$i]['presupuesto'],$lo_dataright);
			$lo_hoja->write($li_row, 3, $la_data[$i]['presupuesto_modificado'],$lo_dataright);
			$lo_hoja->write($li_row, 4, $la_data[$i]['programado'],$lo_dataright);
			$lo_hoja->write($li_row, 5, $la_data[$i]['devengado'],$lo_dataright);
			$lo_hoja->write($li_row, 6, $la_data[$i]['liquidado'],$lo_dataright);
			$lo_hoja->write($li_row, 7, $la_data[$i]['recaudado'],$lo_dataright);
			$lo_hoja->write($li_row, 8, $la_data[$i]['programado_acumulado'],$lo_dataright);
			$lo_hoja->write($li_row, 9, $la_data[$i]['devengado_acumulado'],$lo_dataright);
			$lo_hoja->write($li_row, 10, $la_data[$i]['liquidado_acumulado'],$lo_dataright);
			$lo_hoja->write($li_row, 11, $la_data[$i]['recaudado_acumulado'],$lo_dataright);
			$lo_hoja->write($li_row, 12, $la_data[$i]['ingresosxrecibir'],$lo_dataright);
			$li_row++;
		}
		$li_row++;
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_pie_cabecera(&$li_row,&$lo_titulo,$lo_hoja,$lo_datacenter,$lo_dataleft,$lo_dataright,$la_data_totales,$z)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function : uf_print_pie_cabecera
		//		    Acess : private 
		//	    Arguments : ad_total // Total General
		//    Description : función que imprime el fin de la cabecera de cada página
		//	   Creado Por: Ing. Yozelin Barragán
		// Fecha Creación: 26/06/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

			$li_row++;$li_row++;
		
			$lo_hoja->write($li_row, 1, $la_data_totales[$z]['total'],$lo_datacenter);
			$lo_hoja->write($li_row, 2, $la_data_totales[$z]['presupuesto'],$lo_dataright);
			$lo_hoja->write($li_row, 3, $la_data_totales[$z]['presupuesto_modificado'],$lo_dataright);
			$lo_hoja->write($li_row, 4, $la_data_totales[$z]['programado'],$lo_dataright);
			$lo_hoja->write($li_row, 5, $la_data_totales[$z]['devengado'],$lo_dataright);
			$lo_hoja->write($li_row, 6, $la_data_totales[$z]['liquidado'],$lo_dataright);
			$lo_hoja->write($li_row, 7, $la_data_totales[$z]['recaudado'],$lo_dataright);
			$lo_hoja->write($li_row, 8, $la_data_totales[$z]['programado_acumulado'],$lo_dataright);
			$lo_hoja->write($li_row, 9, $la_data_totales[$z]['devengado_acumulado'],$lo_dataright);
			$lo_hoja->write($li_row, 10, $la_data_totales[$z]['liquidado_acumulado'],$lo_dataright);
			$lo_hoja->write($li_row, 11, $la_data_totales[$z]['recaudado_acumulado'],$lo_dataright);
			$lo_hoja->write($li_row, 12, $la_data_totales[$z]['ingresosxrecibir'],$lo_dataright);

	}// end function uf_print_pie_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------
	require_once("../../shared/ezpdf/class.ezpdf.php");
	require_once("../../shared/class_folder/class_funciones.php");
	$io_funciones = new class_funciones();	
	require_once("sigesp_spi_funciones_reportes.php");
	$io_function_report = new sigesp_spi_funciones_reportes();	
	require_once("../../shared/class_folder/class_fecha.php");
	$io_fecha = new class_fecha();
	require_once("sigesp_spi_class_reportes_instructivos.php");
	$io_report = new sigesp_spi_class_reportes_instructivos();
	//-----------------------------------------------------------------------------------------------------------------------------
	//---------------------------------------------------------------------------------------------------------------------------
	//para crear el libro excel
	require_once ("../../shared/writeexcel/class.writeexcel_workbookbig.inc.php");
	require_once ("../../shared/writeexcel/class.writeexcel_worksheet.inc.php");
	$lo_archivo =  tempnam("/tmp", "spi_consolidado_ejecucion_trimestral_inst_08_2009.xls");
	$lo_libro = &new writeexcel_workbookbig($lo_archivo);
	$lo_hoja = &$lo_libro->addworksheet();
	$li_row = 1;
	//---------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ldt_periodo=$_SESSION["la_empresa"]["periodo"];
	$li_ano=substr($ldt_periodo,0,4);
	$li_estmodest=$_SESSION["la_empresa"]["estmodest"];

	$ls_trimestre=$_GET["trimestre"];
	$li_mesdes=substr($ls_trimestre,0,2);
	$ldt_fecdes=$li_ano."-".$li_mesdes."-01";
	$li_meshas=substr($ls_trimestre,2,2);
	switch($ls_trimestre)
	{
		case '0103': $trimestre = "01";
		break;
		
		case '0406': $trimestre = "02";
		break;
		
		case '0709': $trimestre = "03";
		break;
		
		case '1012': $trimestre = "04";
		break;	
	}
	$ldt_ult_dia=$io_fecha->uf_last_day($li_meshas,$li_ano);
	$fechas=$ldt_ult_dia;
	$ldt_fechas=$io_funciones->uf_convertirdatetobd($fechas);
	$ls_mesdes=$io_fecha->uf_load_nombre_mes($li_mesdes);
	$ls_meshas=$io_fecha->uf_load_nombre_mes($li_meshas);
	//----------------------------------------------------  Parámetros del encabezado  ---------------------------------------------
		$ls_titulo="CONSOLIDADO DE EJECUCIÓN TRIMESTRAL DE INGRESOS FINANCIEROS";       
	//--------------------------------------------------------------------------------------------------------------------------------
	// Cargar el dts_cab con los datos de la cabecera del reporte( Selecciono todos comprobantes )	
	$lb_valido=$io_report->uf_spi_reporte_consolidado_de_ejecucion_trimestral($ldt_fecdes,$ldt_fechas,$ls_mesdes,$ls_meshas);
	if($lb_valido==false) // Existe algún error ó no hay registros
	{
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');"); 
		print(" close();");
		print("</script>");
	}
	else // Imprimimos el reporte
	{
		//--------------------------------------------------------------------------------------------------
		$lo_encabezado= &$lo_libro->addformat();
		$lo_encabezado->set_bold();
		$lo_encabezado->set_font("Verdana");
		$lo_encabezado->set_align('center');
		$lo_encabezado->set_size('11');
		$lo_titulo= &$lo_libro->addformat();
		$lo_titulo->set_bold();
		$lo_titulo->set_font("Verdana");
		$lo_titulo->set_align('left');
		$lo_titulo->set_size('9');
		$lo_datacenter= &$lo_libro->addformat();
		$lo_datacenter->set_font("Verdana");
		$lo_datacenter->set_align('center');
		$lo_datacenter->set_size('9');
		$lo_dataleft= &$lo_libro->addformat();
		$lo_dataleft->set_text_wrap();
		$lo_dataleft->set_font("Verdana");
		$lo_dataleft->set_align('left');
		$lo_dataleft->set_size('9');
		$lo_dataright= &$lo_libro->addformat(array(num_format => '#,##0.00'));
		$lo_dataright->set_font("Verdana");
		$lo_dataright->set_align('right');
		$lo_dataright->set_size('9');
		$lo_hoja->set_column(0,0,10);
		$lo_hoja->set_column(1,1,70);
		$lo_hoja->set_column(2,2,25);
		$lo_hoja->set_column(3,3,25);
		$lo_hoja->set_column(4,4,25);
		$lo_hoja->set_column(5,5,25);
		$lo_hoja->set_column(6,6,25);
		$lo_hoja->set_column(7,7,25);
		$lo_hoja->set_column(8,8,25);
		$lo_hoja->set_column(9,9,25);
		$lo_hoja->set_column(10,10,25);
		$lo_hoja->set_column(11,11,25);
		$lo_hoja->set_column(12,12,25);

		$li_row = 4;
		//--------------------------------------------------------------------------------------------------
				
		uf_print_encabezado_pagina(&$li_row,&$lo_titulo,$lo_hoja,$lo_encabezado,$ls_titulo,'(En Bolivares Fuertes)'); // Imprimimos el encabezado de la página
				
		$li_total=$io_report->dts_reporte->getRowCount("spi_cuenta");
		$ld_previsto_total=0;
		$ld_previsto_modificado_total=0;
		$ld_programado_trimestral_total=0;
		$ld_cobrado_anticipado_total=0;
		$ld_cobrado_total=0;
		$ld_devengado_total=0;
		$ld_programado_acumulado_total=0;
		$ld_cobrado_anticipado_acumulado_total=0;
		$ld_cobrado_acumulado_total=0;
		$ld_devengado_acumulado_total=0;
		$ld_ingresosxrecibir_total=0;
		for($z=1;$z<=$li_total;$z++)
		{
			
			$ls_spi_cuenta=substr(trim($io_report->dts_reporte->data["spi_cuenta"][$z]),0,3);
			$ls_denominacion=trim($io_report->dts_reporte->data["denominacion"][$z]);
			$ld_previsto=$io_report->dts_reporte->data["previsto"][$z];
			$ld_previsto_modificado=$io_report->dts_reporte->data["previsto_modificado"][$z];
			$ld_programado_trimestral=$io_report->dts_reporte->data["programado"][$z];
			//$ld_cobrado_anticipado=$io_report->dts_reporte->data["cobrado_anticipado"][$z];
			$ld_cobrado_anticipado=0;
			$ld_cobrado=$io_report->dts_reporte->data["cobrado"][$z];
			$ld_devengado=$io_report->dts_reporte->data["devengado"][$z];
			$ld_programado_acumulado=$io_report->dts_reporte->data["programado_acumulado"][$z];
			$ld_cobrado_anticipado_acumulado=$io_report->dts_reporte->data["cobrado_anticipado_acumulado"][$z];
			$ld_cobrado_acumulado=$io_report->dts_reporte->data["cobrado_acumulado"][$z];
			$ld_devengado_acumulado=$io_report->dts_reporte->data["devengado_acumulado"][$z];
			$ld_ingresosxrecibir=$io_report->dts_reporte->data["ingresosxrecibir"][$z];
			
			$ld_previsto_total=$ld_previsto_total+$ld_previsto;
			$ld_previsto_modificado_total=$ld_previsto_modificado_total+$ld_previsto_modificado;
			$ld_programado_trimestral_total=$ld_programado_trimestral_total+$ld_programado_trimestral;
			$ld_cobrado_anticipado_total=$ld_cobrado_anticipado_total+$ld_cobrado_anticipado;
			$ld_cobrado_total=$ld_cobrado_total+$ld_cobrado;
			$ld_devengado_total=$ld_devengado_total+$ld_devengado;
			$ld_programado_acumulado_total=$ld_programado_acumulado_total+$ld_programado_acumulado;
			$ld_cobrado_anticipado_acumulado_total=$ld_cobrado_anticipado_acumulado_total+$ld_cobrado_anticipado_acumulado;
			$ld_cobrado_acumulado_total=$ld_cobrado_acumulado_total+$ld_cobrado_acumulado;
			$ld_devengado_acumulado_total=$ld_devengado_acumulado_total+$ld_devengado_acumulado;
			$ld_ingresosxrecibir_total=$ld_ingresosxrecibir_total+$ld_ingresosxrecibir;
			
			$ld_previsto=number_format($ld_previsto,2,",",".");
			$ld_previsto_modificado=number_format($ld_previsto_modificado,2,",",".");
			$ld_programado_trimestral=number_format($ld_programado_trimestral,2,",",".");
			$ld_cobrado_anticipado=number_format($ld_cobrado_anticipado,2,",",".");
			$ld_cobrado=number_format($ld_cobrado,2,",",".");
			$ld_devengado=number_format($ld_devengado,2,",",".");
			$ld_programado_acumulado=number_format($ld_programado_acumulado,2,",",".");
			$ld_cobrado_anticipado_acumulado=number_format($ld_cobrado_anticipado_acumulado,2,",",".");
			$ld_cobrado_acumulado=number_format($ld_cobrado_acumulado,2,",",".");
			$ld_devengado_acumulado=number_format($ld_devengado_acumulado,2,",",".");
			$ld_ingresosxrecibir=number_format($ld_ingresosxrecibir,2,",",".");
			
			$la_data[$z]=array('cuenta'=>$ls_spi_cuenta,
						'denominacion'=>$ls_denominacion,
						'presupuesto'=>$ld_previsto,
						'presupuesto_modificado'=>$ld_previsto_modificado,
						'programado'=>$ld_programado_trimestral,
						'devengado'=>$ld_devengado,
						'liquidado'=>$ld_cobrado,
						'recaudado'=>$ld_cobrado_anticipado,
						'programado_acumulado'=>$ld_programado_acumulado,
						'devengado_acumulado'=>$ld_cobrado_anticipado_acumulado,
						'liquidado_acumulado'=>$ld_cobrado_anticipado_acumulado,
						'recaudado_acumulado'=>$ld_devengado_acumulado,
						'ingresosxrecibir'=>$ld_ingresosxrecibir);
		}
		
		$ld_previsto_total=number_format($ld_previsto_total,2,",",".");
		$ld_previsto_modificado_total=number_format($ld_previsto_modificado_total,2,",",".");
		$ld_programado_trimestral_total=number_format($ld_programado_trimestral_total,2,",",".");
		$ld_devengado_total=number_format($ld_devengado_total,2,",",".");
		$ld_cobrado_anticipado_total=number_format($ld_cobrado_anticipado_total,2,",",".");
		$ld_cobrado_total=number_format($ld_cobrado_total,2,",",".");
		$ld_programado_acumulado_total=number_format($ld_programado_acumulado_total,2,",",".");
		$ld_cobrado_anticipado_acumulado_total=number_format($ld_cobrado_anticipado_acumulado_total,2,",",".");
		$ld_cobrado_acumulado_total=number_format($ld_cobrado_acumulado_total,2,",",".");
		$ld_devengado_acumulado_total=number_format($ld_devengado_acumulado_total,2,",",".");
		$ld_ingresosxrecibir_total=number_format($ld_ingresosxrecibir_total,2,",",".");
	  
		$la_data_totales[$z]=array('total'=>'TOTALES Bs.',
						'presupuesto'=>$ld_previsto_total,
						'presupuesto_modificado'=>$ld_previsto_modificado_total,
						'programado'=>$ld_programado_trimestral_total,
						'devengado'=>$ld_devengado_total,
						'liquidado'=>$ld_cobrado_total,
						'recaudado'=>$ld_cobrado_anticipado_total,
						'programado_acumulado'=>$ld_programado_acumulado_total,
						'devengado_acumulado'=>$ld_cobrado_anticipado_acumulado_total,
						'liquidado_acumulado'=>$ld_cobrado_anticipado_acumulado_total,
						'recaudado_acumulado'=>$ld_devengado_acumulado_total,
						'ingresosxrecibir'=>$ld_ingresosxrecibir_total);
							   
		
		
		uf_print_titulo_reporte(&$li_row,&$lo_titulo,$lo_hoja,$lo_encabezado,$io_encabezado);
		uf_print_titulo(&$li_row,&$lo_titulo,$lo_hoja,$lo_encabezado,$io_titulo,$trimestre);
		uf_print_cabecera(&$li_row,&$lo_titulo,$lo_hoja,$lo_encabezado,$io_cabecera,$trimestre);
		uf_print_detalle(&$li_row,&$lo_titulo,$lo_hoja,$lo_datacenter,$lo_dataleft,$lo_dataright,$la_data); // Imprimimos el detalle 
		uf_print_pie_cabecera(&$li_row,&$lo_titulo,$lo_hoja,$lo_datacenter,$lo_dataleft,$lo_dataright,$la_data_totales,$z);
		
		unset($la_data);
		unset($la_data_totales);
		
		if (isset($d) && $d)
		{
			//none
		}
		else
		{
			$lo_libro->close();
			header("Content-Type: application/x-msexcel; name=\"spi_consolidado_ejecucion_trimestral_inst_08_2009.xls\"");
			header("Content-Disposition: inline; filename=\"spi_consolidado_ejecucion_trimestral_inst_08_2009.xls\"");
			$fh=fopen($lo_archivo, "rb");
			fpassthru($fh);
			unlink($lo_archivo);
		}
		
	}//else
	unset($io_report);
	unset($io_funciones);
	
?> 