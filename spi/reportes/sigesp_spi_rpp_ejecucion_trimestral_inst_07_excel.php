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
	function uf_print_encabezado_pagina(&$li_row,&$lo_titulo,$lo_hoja,$as_titulo,$as_moneda,$as_trimestre,$ai_numnivcuenta=4)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		    Acess: private    &$li_row,&$lo_titulo,$lo_hoja,
		//	    Arguments: as_titulo // Título del Reporte
		//                 $as_moneda // Moneda
		//	    		   as_trimestre // Nro. del Trimestre
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Arnaldo Suárez
		// Fecha Creación: 26/06/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		if($ai_numnivcuenta == 5)
		{
			$lo_hoja->write($li_row, 0, "RAMO",$lo_titulo);
			$lo_hoja->write($li_row, 1, "SUB-RAMO",$lo_titulo);
			$lo_hoja->write($li_row, 2, "ESPECIFICA",$lo_titulo);
			$lo_hoja->write($li_row, 3, "SUB-ESPECÍFICA",$lo_titulo);
			$lo_hoja->write($li_row, 4, "INT",$lo_titulo);
			$lo_hoja->write($li_row, 5, "DENOMINACION",$lo_titulo);
			$lo_hoja->write($li_row, 6, "PRESUPUESTO APROBADO",$lo_titulo);
			$lo_hoja->write($li_row, 7, "PRESUPUESTO MODIFICADO",$lo_titulo);
			$lo_hoja->write($li_row, 8, "PROGRAMADO EN EL TRIMESTRE No. $as_trimestre",$lo_titulo);
			$lo_hoja->write($li_row-1, 9, "EJECUTADO EN EL TRIMESTRE No. $as_trimestre",$lo_titulo);
			$lo_hoja->write($li_row, 9, "DEVENGADO",$lo_titulo);
			$lo_hoja->write($li_row, 10, "LIQUIDADO",$lo_titulo);
			$lo_hoja->write($li_row, 11, "RECAUDADO",$lo_titulo);
			$lo_hoja->write($li_row-1, 12, "ACUMULADO AL TRIMESTRE No. $as_trimestre ",$lo_titulo);
			$lo_hoja->write($li_row, 12, "PROGRAMADO",$lo_titulo);
			$lo_hoja->write($li_row, 13, "DEVENGADO",$lo_titulo);
			$lo_hoja->write($li_row, 14, "LIQUIDADO",$lo_titulo);
			$lo_hoja->write($li_row, 15, "RECAUDADO",$lo_titulo);
			$lo_hoja->write($li_row, 16, "INGRESOS POR RECIBIR",$lo_titulo);
		}
		else
		{
		 	$lo_hoja->write($li_row, 0, "RAMO",$lo_titulo);
			$lo_hoja->write($li_row, 1, "SUB-RAMO",$lo_titulo);
			$lo_hoja->write($li_row, 2, "ESPECIFICA",$lo_titulo);
			$lo_hoja->write($li_row, 3, "SUB-ESPECÍFICA",$lo_titulo);
			$lo_hoja->write($li_row, 4, "DENOMINACION",$lo_titulo);
			$lo_hoja->write($li_row, 5, "PRESUPUESTO APROBADO",$lo_titulo);
			$lo_hoja->write($li_row, 6, "PRESUPUESTO MODIFICADO",$lo_titulo);
			$lo_hoja->write($li_row, 7, "PROGRAMADO EN EL TRIMESTRE No. $as_trimestre",$lo_titulo);
			$lo_hoja->write($li_row-1, 8, "EJECUTADO EN EL TRIMESTRE No. $as_trimestre",$lo_titulo);
			$lo_hoja->write($li_row, 8, "DEVENGADO",$lo_titulo);
			$lo_hoja->write($li_row, 9, "LIQUIDADO",$lo_titulo);
			$lo_hoja->write($li_row, 10, "RECAUDADO",$lo_titulo);
			$lo_hoja->write($li_row-1, 11, "ACUMULADO AL TRIMESTRE No. $as_trimestre ",$lo_titulo);
			$lo_hoja->write($li_row, 11, "PROGRAMADO",$lo_titulo);
			$lo_hoja->write($li_row, 12, "DEVENGADO",$lo_titulo);
			$lo_hoja->write($li_row, 13, "LIQUIDADO",$lo_titulo);
			$lo_hoja->write($li_row, 14, "RECAUDADO",$lo_titulo);
			$lo_hoja->write($li_row, 15, "INGRESOS POR RECIBIR",$lo_titulo);
		}
		$li_row++;
		$li_row++;
		
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_titulo_reporte(&$li_row,&$lo_titulo,$lo_hoja,$lo_encabezado,$as_programatica,$ai_ano,$as_mes,$as_codestpro1,$as_denestpro1,$as_trimestre,$la_data_cab_ep)
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
		//	uf_print_titulo_reporte(&$li_row,$lo_hoja,$io_encabezado,"",$li_ano,$ls_mesdes,$ls_codestpro1,$ls_denestpro1,$ls_trimestre,$la_data_cab_ep);
		
		$ls_codemp	= $_SESSION["la_empresa"]["codemp"];
		$ls_nombre	= $_SESSION["la_empresa"]["nombre"];
		$ls_nomorgads 	= $_SESSION["la_empresa"]["nomorgads"];
		$ls_codasiona   = $_SESSION['la_empresa']['codasiona'];
		
		$lo_hoja->write($li_row, 0, "CODIGO PRESUPUESTARIO DEL ENTE: $ls_codasiona ",$lo_titulo);
		$li_row++;
		$lo_hoja->write($li_row, 0, "DENOMINACION DEL ENTE: $ls_nombre ",$lo_titulo);
		$li_row++;
		$lo_hoja->write($li_row, 0, "ORGANO DE ADSCRIPCION: $ls_nombre ",$lo_titulo);
		$li_row++;
		$lo_hoja->write($li_row, 0, strtoupper($_SESSION["la_empresa"]["nomestpro1"]).': '.$as_denestpro1.' - '.$as_codestpro1,$lo_titulo);
		$li_row++;
		$lo_hoja->write($li_row, 0, "PERIODO PRESUPUESTARIO:",$lo_titulo);
		$li_row++;$li_row++;
		
		//print_r($la_data_cab_ep);
		$lo_hoja->write($li_row, 1, "Desde",$lo_titulo);
		$lo_hoja->write($li_row, 7, "Hasta",$lo_titulo);
		$li_row++;
		$lo_hoja->write($li_row, 1, $la_data_cab_ep[1]['ep_desde'],$lo_titulo);
		$lo_hoja->write($li_row, 7, $la_data_cab_ep[1]['ep_hasta'],$lo_titulo);
		$li_row++;
		$lo_hoja->write($li_row, 1, $la_data_cab_ep[2]['ep_desde'],$lo_titulo);
		$lo_hoja->write($li_row, 7, $la_data_cab_ep[2]['ep_hasta'],$lo_titulo);
		$li_row++;
		$lo_hoja->write($li_row, 1, $la_data_cab_ep[3]['ep_desde'],$lo_titulo);
		$lo_hoja->write($li_row, 7, $la_data_cab_ep[3]['ep_hasta'],$lo_titulo);
		$li_row++;$li_row++;$li_row++;

		
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------
	

	//--------------------------------------------------------------------------------------------------------------------------------	//--------------------------------------------------------------------------------------------------------------------------------
	
	function uf_print_detalle(&$li_row,&$lo_titulo,$lo_hoja,$lo_datacenter,$lo_dataleft,$lo_dataright,$la_data,$ai_numnivcuenta=4)
	{//uf_print_detalle(&$li_row,&$lo_titulo,$lo_hoja,$lo_datacenter,$lo_dataleft,$lo_dataright,$la_data);
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: 	uf_print_detalle
		//		    	Acess: private 
		//	Arguments: 	la_data // arreglo de información
		//	   		io_pdf // Objeto PDF
		//    Description: 	función que imprime el detalle
		//     Creado Por: 	Ing. Yozelin Barragán
		// Fecha Creación: 	26/06/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		$total = count($la_data);
		//print_r($la_data); 
		if($ai_numnivcuenta == 5)
		{
		  for( $i = 1; $i <= $total; $i ++)
		  {
			if($la_data[$i]['previsto'] == $la_data[$i]['modificado'])
			{
			 $la_data[$i]['modificado'] = " ";
			}
			$lo_hoja->write($li_row, 0, $la_data[$i]['ramo'],$lo_datacenter);
			$lo_hoja->write($li_row, 1, $la_data[$i]['subramo'],$lo_datacenter);
			$lo_hoja->write($li_row, 2, $la_data[$i]['especifica'],$lo_datacenter);
			$lo_hoja->write($li_row, 3, $la_data[$i]['subesp'],$lo_datacenter);
			$lo_hoja->write($li_row, 4, $la_data[$i]['interna'],$lo_datacenter);
			$lo_hoja->write($li_row, 5, $la_data[$i]['denominacion'],$lo_dataleft);
			$lo_hoja->write($li_row, 6, $la_data[$i]['previsto'],$lo_dataright);
			$lo_hoja->write($li_row, 7, $la_data[$i]['modificado'],$lo_dataright);
			$lo_hoja->write($li_row, 8, $la_data[$i]['programado'],$lo_dataright);
			$lo_hoja->write($li_row, 9, $la_data[$i]['devengado'],$lo_dataright);
			$lo_hoja->write($li_row, 10, $la_data[$i]['liquidado'],$lo_dataright);
			$lo_hoja->write($li_row, 11, $la_data[$i]['recaudado'],$lo_dataright);
			$lo_hoja->write($li_row, 12, $la_data[$i]['programado_acum'],$lo_dataright);
			$lo_hoja->write($li_row, 13, $la_data[$i]['devengado_acum'],$lo_dataright);
			$lo_hoja->write($li_row, 14, $la_data[$i]['liquidado_acum'],$lo_dataright);
			$lo_hoja->write($li_row, 15, $la_data[$i]['recaudado_acum'],$lo_dataright);
			$lo_hoja->write($li_row, 16, $la_data[$i]['ingresos_recibir'],$lo_dataright);
			$li_row++;
		  }
		}
		else
		{
		 for( $i = 1; $i <= $total; $i ++)
		 {
			if($la_data[$i]['previsto'] == $la_data[$i]['modificado'])
			{
			 $la_data[$i]['modificado'] = " ";
			}
			
			$lo_hoja->write($li_row, 0, $la_data[$i]['ramo'],$lo_datacenter);
			$lo_hoja->write($li_row, 1, $la_data[$i]['subramo'],$lo_datacenter);
			$lo_hoja->write($li_row, 2, $la_data[$i]['especifica'],$lo_datacenter);
			$lo_hoja->write($li_row, 3, $la_data[$i]['subesp'],$lo_datacenter);
			$lo_hoja->write($li_row, 4, $la_data[$i]['denominacion'],$lo_dataleft);
			$lo_hoja->write($li_row, 5, $la_data[$i]['previsto'],$lo_dataright);
			$lo_hoja->write($li_row, 6, $la_data[$i]['modificado'],$lo_dataright);
			$lo_hoja->write($li_row, 7, $la_data[$i]['programado'],$lo_dataright);
			$lo_hoja->write($li_row, 8, $la_data[$i]['devengado'],$lo_dataright);
			$lo_hoja->write($li_row, 9, $la_data[$i]['liquidado'],$lo_dataright);
			$lo_hoja->write($li_row, 10, $la_data[$i]['recaudado'],$lo_dataright);
			$lo_hoja->write($li_row, 11, $la_data[$i]['programado_acum'],$lo_dataright);
			$lo_hoja->write($li_row, 12, $la_data[$i]['devengado_acum'],$lo_dataright);
			$lo_hoja->write($li_row, 13, $la_data[$i]['liquidado_acum'],$lo_dataright);
			$lo_hoja->write($li_row, 14, $la_data[$i]['recaudado_acum'],$lo_dataright);
			$lo_hoja->write($li_row, 15, $la_data[$i]['ingresos_recibir'],$lo_dataright);
			$li_row++;
		 }
		} 
		
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_pie_cabecera(&$li_row,&$lo_titulo,$lo_hoja,$lo_datacenter,$lo_dataleft,$lo_dataright,$la_data_tot,$ai_numnivcuenta=4)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function : uf_print_pie_cabecera
		//		    Acess : private 
		//	    Arguments : ad_total // Total General
		//    Description : función que imprime el fin de la cabecera de cada página
		//	   Creado Por: Ing. Arnaldo USárez
		// Fecha Creación: 10/06/2008 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		$total = count($la_data_tot);
		if($ai_numnivcuenta == 5)
		{
		 for( $i = 1; $i <= $total; $i ++)
		 {
			if($la_data_tot[$i]['previsto'] == $la_data_tot[$i]['modificado'])
			{
			 $la_data_tot[$i]['modificado'] = " ";
			}
			
			$lo_hoja->write($li_row, 5, $la_data_tot[$i]['totales'],$lo_datacenter);
			$lo_hoja->write($li_row, 6, $la_data_tot[$i]['previsto'],$lo_dataright);
			$lo_hoja->write($li_row, 7, $la_data_tot[$i]['modificado'],$lo_dataright);
			$lo_hoja->write($li_row, 8, $la_data_tot[$i]['programado'],$lo_dataright);
			$lo_hoja->write($li_row, 9, $la_data_tot[$i]['devengado'],$lo_dataright);
			$lo_hoja->write($li_row, 10, $la_data_tot[$i]['liquidado'],$lo_dataright);
			$lo_hoja->write($li_row, 11, $la_data_tot[$i]['recaudado'],$lo_dataright);
			$lo_hoja->write($li_row, 12, $la_data_tot[$i]['programado_acum'],$lo_dataright);
			$lo_hoja->write($li_row, 13, $la_data_tot[$i]['devengado_acum'],$lo_dataright);
			$lo_hoja->write($li_row, 14, $la_data_tot[$i]['liquidado_acum'],$lo_dataright);
			$lo_hoja->write($li_row, 15, $la_data_tot[$i]['recaudado_acum'],$lo_dataright);
			$lo_hoja->write($li_row, 16, $la_data_tot[$i]['ingresos_recibir'],$lo_dataright);			
			$li_row++;
		 } 
		}
		else
		{
		 	for( $i = 1; $i <= $total; $i ++)
		    {
				if($la_data_tot[$i]['previsto'] == $la_data_tot[$i]['modificado'])
				{
				 $la_data_tot[$i]['modificado'] = " ";
				}
				
				$lo_hoja->write($li_row, 4, $la_data_tot[$i]['totales'],$lo_datacenter);
				$lo_hoja->write($li_row, 5, $la_data_tot[$i]['previsto'],$lo_dataright);
				$lo_hoja->write($li_row, 6, $la_data_tot[$i]['modificado'],$lo_dataright);
				$lo_hoja->write($li_row, 7, $la_data_tot[$i]['programado'],$lo_dataright);
				$lo_hoja->write($li_row, 8, $la_data_tot[$i]['devengado'],$lo_dataright);
				$lo_hoja->write($li_row, 9, $la_data_tot[$i]['liquidado'],$lo_dataright);
				$lo_hoja->write($li_row, 10, $la_data_tot[$i]['recaudado'],$lo_dataright);
				$lo_hoja->write($li_row, 11, $la_data_tot[$i]['programado_acum'],$lo_dataright);
				$lo_hoja->write($li_row, 12, $la_data_tot[$i]['devengado_acum'],$lo_dataright);
				$lo_hoja->write($li_row, 13, $la_data_tot[$i]['liquidado_acum'],$lo_dataright);
				$lo_hoja->write($li_row, 14, $la_data_tot[$i]['recaudado_acum'],$lo_dataright);
				$lo_hoja->write($li_row, 15, $la_data_tot[$i]['ingresos_recibir'],$lo_dataright);			
				$li_row++;
			}
		
		}
		
		unset($i);
		
		
	}// end function uf_print_pie_cabecera
//--------------------------------------------------------------------------------------------------------------------------------
//require_once("../../shared/ezpdf/class.ezpdf.php");		
require_once("../../shared/class_folder/class_funciones.php");
$io_funciones=new class_funciones();	
require_once("sigesp_spi_funciones_reportes.php");
$io_function_report=new sigesp_spi_funciones_reportes();	
require_once("../../shared/class_folder/class_fecha.php");
$io_fecha = new class_fecha();
require_once("sigesp_spi_reporte.php");
$io_spirep = new sigesp_spi_reporte();

	//-----------------------------------------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------------------------------------
// para crear el libro excel
	require_once ("../../shared/writeexcel/class.writeexcel_workbookbig.inc.php");
	require_once ("../../shared/writeexcel/class.writeexcel_worksheet.inc.php");
	$lo_archivo =  tempnam("/tmp", "spi_ejecucion_trimestral_inst_07.xls");
	$lo_libro = &new writeexcel_workbookbig($lo_archivo);
	$lo_hoja = &$lo_libro->addworksheet();
	$li_row = 0;
//---------------------------------------------------------------------------------------------------------------------------

	
	global $la_data_tot;
	require_once("sigesp_spi_class_reportes_instructivos.php");
	$io_report = new sigesp_spi_class_reportes_instructivos();
	
	$li_estpreing = $_SESSION["la_empresa"]["estpreing"];
	$li_numnivcuenta = count(explode("-",$_SESSION["la_empresa"]["formpre"]));
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ldt_periodo=$_SESSION["la_empresa"]["periodo"];
	$li_ano=substr($ldt_periodo,0,4);
	$ls_cmbmes=$_GET["cmbmes"];
	if ($li_estpreing==1)
	{
		$ls_codestpro1 = $_GET["codestpro1"];
		$ls_codestpro2 = $_GET["codestpro2"];
		$ls_codestpro3 = $_GET["codestpro3"];
		$ls_codestpro4 = $_GET["codestpro4"];
		$ls_codestpro5 = $_GET["codestpro5"];
		$ls_codestpro1h = $_GET["codestpro1h"];
		$ls_codestpro2h = $_GET["codestpro2h"];
		$ls_codestpro3h = $_GET["codestpro3h"];
		$ls_codestpro4h = $_GET["codestpro4h"];
		$ls_codestpro5h = $_GET["codestpro5h"];
		$ls_estclades   = $_GET["estclades"];
		$ls_estclahas   = $_GET["estclahas"];
	}
	else
	{
		$ls_codestpro1 = "";
		$ls_codestpro2 = "";
		$ls_codestpro3 = "";
		$ls_codestpro4 = "";
		$ls_codestpro5 = "";
		$ls_codestpro1h = "";
		$ls_codestpro2h = "";
		$ls_codestpro3h = "";
		$ls_codestpro4h = "";
		$ls_codestpro5h = "";
		$ls_estclades   = "";
		$ls_estclahas   = "";
	}
	switch($ls_cmbmes)
	{
		case '0103': $ls_trimestre = "01";
		break;
		
		case '0406': $ls_trimestre = "02";
		break;
		
		case '0709': $ls_trimestre = "03";
		break;
		
		case '1012': $ls_trimestre = "04";
		break;
	}
	if ($ls_codestpro1==='' and $ls_codestpro2==='' and $ls_codestpro3==='')
	{
		$io_spirep->uf_spg_reporte_select_estpro_blanco(&$ls_codestpro1,&$ls_codestpro2,&$ls_codestpro3,&$ls_codestpro4,&$ls_codestpro5,'TOP',&$ls_estclades);
	}
	
	if ($ls_codestpro1h==='' and $ls_codestpro2h==='' and $ls_codestpro3h==='')
	{
		$io_spirep->uf_spg_reporte_select_estpro_blanco(&$ls_codestpro1h,&$ls_codestpro2h,&$ls_codestpro3h,&$ls_codestpro4h,&$ls_codestpro5h,'BOTTOM',&$ls_estclahas);
	}
		
	$li_mesdes=substr($ls_cmbmes,0,2);
	$ldt_fecdes=$li_ano."-".$li_mesdes."-01";
	$li_meshas=substr($ls_cmbmes,2,2);
	$ldt_ult_dia=$io_fecha->uf_last_day($li_meshas,$li_ano);
	$fechas=$ldt_ult_dia;
	$ldt_fechas=$io_funciones->uf_convertirdatetobd($fechas);
	$ls_mesdes=$io_fecha->uf_load_nombre_mes($li_mesdes);
	$ls_meshas=$io_fecha->uf_load_nombre_mes($li_meshas);
	
		
//----------------------------------------------------  Parámetros del encabezado  ---------------------------------------------
	$ls_titulo="EJECUCION TRIMESTRAL DE INGRESOS Y FUENTES FINANCIERAS";       
//------------------------------------------------------------------------------------------------------------------------------
	  if($li_numnivcuenta==5)
	  { 
	   $lb_valido=$io_report->uf_spi_reportes_ejecucion_trimestral_excel($ldt_fecdes,$ldt_fechas,$ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,$ls_codestpro5,$ls_codestpro1h,$ls_codestpro2h,$ls_codestpro3h,
				                                                         $ls_codestpro4h,$ls_codestpro5h,$ls_estclades,$ls_estclahas);
	  }
	  else
	  {
	   $lb_valido=$io_report->uf_spi_reportes_ejecucion_trimestral($ldt_fecdes,$ldt_fechas,$ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,$ls_codestpro5,$ls_codestpro1h,$ls_codestpro2h,$ls_codestpro3h,
				                                                   $ls_codestpro4h,$ls_codestpro5h,$ls_estclades,$ls_estclahas);
	  }
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
		$lo_datacenter->set_text_wrap();
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
		if($li_numnivcuenta == 5)
		{
			$lo_hoja->set_column(0,0,10);
			$lo_hoja->set_column(1,1,10);
			$lo_hoja->set_column(2,2,10);
			$lo_hoja->set_column(3,3,10);
			$lo_hoja->set_column(4,4,10);
			$lo_hoja->set_column(5,5,70);
			$lo_hoja->set_column(6,6,25);
			$lo_hoja->set_column(7,7,25);
			$lo_hoja->set_column(8,8,25);
			$lo_hoja->set_column(9,9,25);
			$lo_hoja->set_column(10,10,25);
			$lo_hoja->set_column(11,11,25);
			$lo_hoja->set_column(12,12,25);
			$lo_hoja->set_column(13,13,25);
			$lo_hoja->set_column(14,14,25);
			$lo_hoja->set_column(15,15,25);
			$lo_hoja->set_column(16,16,25);
		}
		else
		{
		 	$lo_hoja->set_column(0,0,10);
			$lo_hoja->set_column(1,1,10);
			$lo_hoja->set_column(2,2,10);
			$lo_hoja->set_column(3,3,10);
			$lo_hoja->set_column(4,4,70);
			$lo_hoja->set_column(5,5,25);
			$lo_hoja->set_column(6,6,25);
			$lo_hoja->set_column(7,7,25);
			$lo_hoja->set_column(8,8,25);
			$lo_hoja->set_column(9,9,25);
			$lo_hoja->set_column(10,10,25);
			$lo_hoja->set_column(11,11,25);
			$lo_hoja->set_column(12,12,25);
			$lo_hoja->set_column(13,13,25);
			$lo_hoja->set_column(14,14,25);
			$lo_hoja->set_column(15,15,25);
		}
		$lo_hoja->write(0, 5, $ls_titulo,$lo_encabezado);
		$lo_hoja->write(1, 5, $ls_titulo1,$lo_encabezado);
		$li_row = 4;
		//--------------------------------------------------------------------------------------------------

		$li_tot=$io_report->dts_reporte->getRowCount("spi_cuenta");
		$ld_total_previsto = $li_i = 0;
		$ld_total_modificado=0;
		$ld_total_programado=0;
		$ld_total_devengado=0;
		$ld_total_liquidado=0;
		$ld_total_recaudado=0;
		$ld_total_programado_acum=0;
		$ld_total_devengado_acum=0;
		$ld_total_liquidado_acum=0;
		$ld_total_recaudado_acum=0;
		$ld_total_ingresos_recibir=0;
		
		$ld_montotpre = 0;
		$ld_montotmod = 0;
		$ld_montotpro = 0;
		$ld_montotdev = 0;
		$ld_montotliq = 0;
		$ld_montotrec = 0;
		$ld_montotpac = 0;
		$ld_montotdac = 0;
		$ld_montotlac = 0;
		$ld_montotrac = 0;
		$ld_montotire = 0;
				   		

		if ($ls_codestpro1=="")
		{
			$ls_denestpro1 = " TODAS";
			$io_spirep->uf_spg_reporte_select_denestpro_global(str_pad($ls_codestpro1,25,0,0),str_pad($ls_codestpro2,25,0,0),str_pad($ls_codestpro3,25,0,0),str_pad($ls_codestpro4,25,0,0),str_pad($ls_codestpro5,25,0,0),
									   $ls_denestpro1,$ls_denestpro2,$ls_denestpro3,$ls_denestpro4,$ls_denestpro5,$ls_estclades);
			$io_spirep->uf_spg_reporte_select_denestpro_global(str_pad($ls_codestpro1h,25,0,0),str_pad($ls_codestpro2h,25,0,0),str_pad($ls_codestpro3h,25,0,0),str_pad($ls_codestpro4h,25,0,0),str_pad($ls_codestpro5h,25,0,0),
									   $ls_denestpro1h,$ls_denestpro2h,$ls_denestpro3h,$ls_denestpro4h,$ls_denestpro5h,$ls_estclahas);
			
			$la_data_cab_ep[1]=array('ep_desde'=>$ls_codestpro1.' - '.$ls_denestpro1,'ep_hasta'=>$ls_codestpro1h.' - '.$ls_denestpro1h);
			$la_data_cab_ep[2]=array('ep_desde'=>$ls_codestpro2.' - '.$ls_denestpro2,'ep_hasta'=>$ls_codestpro2h.' - '.$ls_denestpro2h);
			$la_data_cab_ep[3]=array('ep_desde'=>$ls_codestpro3.' - '.$ls_denestpro3,'ep_hasta'=>$ls_codestpro3h.' - '.$ls_denestpro3h);			
		}
		else
		{
			$io_spirep->uf_spg_reporte_select_denestpro1(str_pad($ls_codestpro1,25,0,0),$ls_denestpro1,$ls_estclades);
			$io_spirep->uf_spg_reporte_select_denestpro_global(str_pad($ls_codestpro1,25,0,0),str_pad($ls_codestpro2,25,0,0),str_pad($ls_codestpro3,25,0,0),str_pad($ls_codestpro4,25,0,0),str_pad($ls_codestpro5,25,0,0),
									   $ls_denestpro1,$ls_denestpro2,$ls_denestpro3,$ls_denestpro4,$ls_denestpro5,$ls_estclades);
			$io_spirep->uf_spg_reporte_select_denestpro_global(str_pad($ls_codestpro1h,25,0,0),str_pad($ls_codestpro2h,25,0,0),str_pad($ls_codestpro3h,25,0,0),str_pad($ls_codestpro4h,25,0,0),str_pad($ls_codestpro5h,25,0,0),
									   $ls_denestpro1h,$ls_denestpro2h,$ls_denestpro3h,$ls_denestpro4h,$ls_denestpro5h,$ls_estclahas);
			
			$la_data_cab_ep[1]=array('ep_desde'=>$ls_codestpro1.' - '.$ls_denestpro1,'ep_hasta'=>$ls_codestpro1h.' - '.$ls_denestpro1h);
			$la_data_cab_ep[2]=array('ep_desde'=>$ls_codestpro2.' - '.$ls_denestpro2,'ep_hasta'=>$ls_codestpro2h.' - '.$ls_denestpro2h);
			$la_data_cab_ep[3]=array('ep_desde'=>$ls_codestpro3.' - '.$ls_denestpro3,'ep_hasta'=>$ls_codestpro3h.' - '.$ls_denestpro3h);
		}		
		
		uf_print_titulo_reporte(&$li_row,&$lo_titulo,$lo_hoja,$lo_encabezado,"",$li_ano,$ls_mesdes,$ls_codestpro1,$ls_denestpro1,$ls_trimestre,$la_data_cab_ep);
		
		uf_print_encabezado_pagina(&$li_row,&$lo_titulo,$lo_hoja,$ls_titulo,'(En Bolivares Fuertes)',$ls_trimestre,$li_numnivcuenta); // Imprimimos el encabezado de la página

		$ls_partida_aux="";
		
		if($li_numnivcuenta == 5)
		{
		  for ($z=1;$z<=$li_tot;$z++)
		  {		
			$ld_previsto=0;
			$ld_modificado=0;
			$ld_programado=0;
			$ld_devengado=0;
			$ld_liquidado=0;
			$ld_recaudado=0;
			$ld_programado_acum=0;
			$ld_devengado_acum=0;
			$ld_liquidado_acum=0;
			$ld_recaudado_acum=0;
			$ld_ingresos_recibir=0;
			$ls_ramo="";
			$ls_subramo="";
			$ls_especifica="";
			$ls_subesp="";
			$ls_interna="";
			$ls_status="";
			$ls_spi_cuenta       = trim($io_report->dts_reporte->data["spi_cuenta"][$z]);
			$io_function_report->uf_get_spi_cuenta($ls_spi_cuenta,$ls_ramo,$ls_subramo,$ls_especifica,$ls_subesp,$ls_interna);
			$ls_denominacion     = trim($io_report->dts_reporte->data["denominacion"][$z]);
			$ld_previsto         = $io_report->dts_reporte->data["previsto"][$z];
			$ld_modificado       = $io_report->dts_reporte->data["modificado"][$z];
			$ld_programado       = $io_report->dts_reporte->data["programado"][$z].'<br>';
			$ld_devengado        = $io_report->dts_reporte->data["devengado"][$z];
			$ld_liquidado        = $io_report->dts_reporte->data["liquidado"][$z];
			$ld_recaudado        = $io_report->dts_reporte->data["recaudado"][$z];
			$ld_programado_acum  = $io_report->dts_reporte->data["programado_acum"][$z];
			$ld_devengado_acum   = $io_report->dts_reporte->data["devengado_acum"][$z];
			$ld_liquidado_acum   = $io_report->dts_reporte->data["liquidado_acum"][$z];
			$ld_recaudado_acum   = $io_report->dts_reporte->data["recaudado_acum"][$z];
			$ld_ingresos_recibir = $io_report->dts_reporte->data["ingresos_recibir"][$z];
			$ls_status           = $io_report->dts_reporte->data["status"][$z];
			if ($ls_status=="C")
			{
				$ld_montotpre += $ld_previsto;
				$ld_montotmod += $ld_modificado;
				$ld_montotpro += $ld_programado;
				$ld_montotdev += $ld_devengado;				   
				$ld_montotliq += $ld_liquidado;
				$ld_montotrec += $ld_recaudado;
				$ld_montotpac += $ld_programado_acum;
				$ld_montotdac += $ld_devengado_acum;
				$ld_montotlac += $ld_liquidado_acum;
				$ld_montotrac += $ld_recaudado_acum;
				$ld_montotire += $ld_ingresos_recibir;
			}			  
			if ($ls_partida_aux=="")
			{
				$ls_partida_aux=$ls_ramo;
			}
			elseif($ls_partida_aux==$ls_ramo)
			{
				if ($ls_status=="C")
				{
					$ld_total_previsto         += $ld_previsto;
					$ld_total_modificado       += $ld_modificado;
					$ld_total_programado       += $ld_programado;
					$ld_total_devengado        += $ld_devengado;
					$ld_total_liquidado        += $ld_liquidado;
					$ld_total_recaudado        += $ld_recaudado;
					$ld_total_programado_acum  += $ld_programado_acum;
					$ld_total_devengado_acum   += $ld_devengado_acum;
					$ld_total_liquidado_acum   += $ld_liquidado_acum;
					$ld_total_recaudado_acum   += $ld_recaudado_acum;
					$ld_total_ingresos_recibir += $ld_ingresos_recibir;
				}  
			}
			else
			{
				$la_data_tot[1]=array('totales'=>"TOTALES ".$ls_partida_aux,
							'previsto'=>number_format($ld_total_previsto,2,",","."),
							'modificado'=>number_format($ld_total_modificado,2,",","."),
							'programado'=>number_format($ld_total_programado,2,",","."),
							'devengado'=>number_format($ld_total_devengado,2,",","."),
							'liquidado'=>number_format($ld_total_liquidado,2,",","."),
							'recaudado'=>number_format($ld_total_recaudado,2,",","."),
							'programado_acum'=>number_format($ld_total_programado_acum,2,",","."),
							'devengado_acum'=>number_format($ld_total_devengado_acum,2,",","."),
							'liquidado_acum'=>number_format($ld_total_liquidado_acum,2,",","."),
							'recaudado_acum'=>number_format($ld_total_recaudado_acum,2,",","."),
							'ingresos_recibir'=>number_format($ld_total_ingresos_recibir,2,",","."));
 
				uf_print_detalle(&$li_row,&$lo_titulo,$lo_hoja,$lo_datacenter,$lo_dataleft,$lo_dataright,$la_data,$li_numnivcuenta);
				uf_print_pie_cabecera(&$li_row,&$lo_titulo,$lo_hoja,$lo_datacenter,$lo_dataleft,$lo_dataright,$la_data_tot,$li_numnivcuenta);
				unset($la_data,$la_data_tot);
				$li_i = 0;
				$ld_total_previsto=$ld_total_modificado=$ld_total_programado=$ld_total_devengado=0;
				$ld_total_liquidado=$ld_total_recaudado=$ld_total_programado_acum=0;
				$ld_total_devengado_acum=$ld_total_liquidado_acum=$ld_total_recaudado_acum=$ld_total_ingresos_recibir=0;				   $ls_partida_aux		= $ls_ramo;
			}					  							 						   
			$ld_previsto         = number_format($ld_previsto,2,",",".");
			$ld_modificado       = number_format($ld_modificado,2,",",".");
			$ld_programado       = number_format($ld_programado,2,",",".");
			$ld_devengado        = number_format($ld_devengado,2,",",".");
			$ld_liquidado        = number_format($ld_liquidado,2,",",".");
			$ld_recaudado        = number_format($ld_recaudado,2,",",".");
			$ld_programado_acum  = number_format($ld_programado_acum,2,",",".");
			$ld_devengado_acum   = number_format($ld_devengado_acum,2,",",".");
			$ld_liquidado_acum   = number_format($ld_liquidado_acum,2,",",".");
			$ld_recaudado_acum   = number_format($ld_recaudado_acum,2,",",".");
			$ld_ingresos_recibir = number_format($ld_ingresos_recibir,2,",",".");
				   
			$li_i++;
			$la_data[$li_i]=array(	'ramo'=>$ls_ramo,
			                      	'subramo'=>" ".$ls_subramo,
									'especifica'=>" ".$ls_especifica,
									'subesp'=>" ".$ls_subesp,
									'interna'=>" ".$ls_interna,
									'denominacion'=>$ls_denominacion,
									'previsto'=>$ld_previsto,
									'modificado'=>$ld_modificado,
									'programado'=>$ld_programado,
									'devengado'=>$ld_devengado,
									'liquidado'=>$ld_liquidado,
									'recaudado'=>$ld_recaudado,
									'programado_acum'=>$ld_programado_acum,
									'devengado_acum'=>$ld_devengado_acum,
									'liquidado_acum'=>$ld_liquidado_acum,
									'recaudado_acum'=>$ld_recaudado_acum,
									'ingresos_recibir'=>$ld_ingresos_recibir);
			
			if ($z==$li_tot)
			{
				if (isset($la_data_tot))
				{
					unset($la_data_tot);
				}
				$la_data_tot[1]=array('totales'=>"TOTALES ".$ls_partida_aux,
							'previsto'=>number_format($ld_total_previsto,2,",","."),
							'modificado'=>number_format($ld_total_modificado,2,",","."),
							'programado'=>number_format($ld_total_programado,2,",","."),
							'devengado'=>number_format($ld_total_devengado,2,",","."),
							'liquidado'=>number_format($ld_total_liquidado,2,",","."),
							'recaudado'=>number_format($ld_total_recaudado,2,",","."),
							'programado_acum'=>number_format($ld_total_programado_acum,2,",","."),
							'devengado_acum'=>number_format($ld_total_devengado_acum,2,",","."),
							'liquidado_acum'=>number_format($ld_total_liquidado_acum,2,",","."),
							'recaudado_acum'=>number_format($ld_total_recaudado_acum,2,",","."),
							'ingresos_recibir'=>number_format($ld_total_ingresos_recibir,2,",","."));				   
				uf_print_detalle(&$li_row,&$lo_titulo,$lo_hoja,$lo_datacenter,$lo_dataleft,$lo_dataright,$la_data,$li_numnivcuenta);
				uf_print_pie_cabecera(&$li_row,&$lo_titulo,$lo_hoja,$lo_datacenter,$lo_dataleft,$lo_dataright,$la_data_tot,$li_numnivcuenta);
				//Impresión del Total General.
				unset($la_data_tot);
				$la_data_tot[1]=array('totales'=>"TOTAL GENERAL ",
							'previsto'=>number_format($ld_montotpre,2,",","."),
							'modificado'=>number_format($ld_montotmod,2,",","."),
							'programado'=>number_format($ld_montotpro,2,",","."),
							'devengado'=>number_format($ld_montotdev,2,",","."),
							'liquidado'=>number_format($ld_montotliq,2,",","."),
							'recaudado'=>number_format($ld_montotrec,2,",","."),
							'programado_acum'=>number_format($ld_montotpac,2,",","."),
							'devengado_acum'=>number_format($ld_montotdac,2,",","."),
							'liquidado_acum'=>number_format($ld_montotlac,2,",","."),
							'recaudado_acum'=>number_format($ld_montotrac,2,",","."),
							'ingresos_recibir'=>number_format($ld_montotire,2,",","."));
				uf_print_pie_cabecera(&$li_row,&$lo_titulo,$lo_hoja,$lo_datacenter,$lo_dataleft,$lo_dataright,$la_data_tot,$li_numnivcuenta);
				unset($la_data);
			}
		}//for 
		}
		else
		{
		  for ($z=1;$z<=$li_tot;$z++)
		  {		
			$ld_previsto=0;
			$ld_modificado=0;
			$ld_programado=0;
			$ld_devengado=0;
			$ld_liquidado=0;
			$ld_recaudado=0;
			$ld_programado_acum=0;
			$ld_devengado_acum=0;
			$ld_liquidado_acum=0;
			$ld_recaudado_acum=0;
			$ld_ingresos_recibir=0;
			$ls_ramo="";
			$ls_subramo="";
			$ls_especifica="";
			$ls_subesp="";
			$ls_status="";
			$ls_spi_cuenta       = trim($io_report->dts_reporte->data["spi_cuenta"][$z]);
			$io_function_report->uf_get_spi_cuenta($ls_spi_cuenta,$ls_ramo,$ls_subramo,$ls_especifica,$ls_subesp);
			$ls_denominacion     = trim($io_report->dts_reporte->data["denominacion"][$z]);
			$ld_previsto         = $io_report->dts_reporte->data["previsto"][$z];
			$ld_modificado       = $io_report->dts_reporte->data["modificado"][$z];
			$ld_programado       = $io_report->dts_reporte->data["programado"][$z].'<br>';
			$ld_devengado        = $io_report->dts_reporte->data["devengado"][$z];
			$ld_liquidado        = $io_report->dts_reporte->data["liquidado"][$z];
			$ld_recaudado        = $io_report->dts_reporte->data["recaudado"][$z];
			$ld_programado_acum  = $io_report->dts_reporte->data["programado_acum"][$z];
			$ld_devengado_acum   = $io_report->dts_reporte->data["devengado_acum"][$z];
			$ld_liquidado_acum   = $io_report->dts_reporte->data["liquidado_acum"][$z];
			$ld_recaudado_acum   = $io_report->dts_reporte->data["recaudado_acum"][$z];
			$ld_ingresos_recibir = $io_report->dts_reporte->data["ingresos_recibir"][$z];
			$ls_status           = $io_report->dts_reporte->data["status"][$z];
			if ($ls_status=="C")
			{
				$ld_montotpre += $ld_previsto;
				$ld_montotmod += $ld_modificado;
				$ld_montotpro += $ld_programado;
				$ld_montotdev += $ld_devengado;				   
				$ld_montotliq += $ld_liquidado;
				$ld_montotrec += $ld_recaudado;
				$ld_montotpac += $ld_programado_acum;
				$ld_montotdac += $ld_devengado_acum;
				$ld_montotlac += $ld_liquidado_acum;
				$ld_montotrac += $ld_recaudado_acum;
				$ld_montotire += $ld_ingresos_recibir;
			}			  
			if ($ls_partida_aux=="")
			{
				$ls_partida_aux=$ls_ramo;
			}
			elseif($ls_partida_aux==$ls_ramo)
			{
				if ($ls_status=="C")
				{
					$ld_total_previsto         += $ld_previsto;
					$ld_total_modificado       += $ld_modificado;
					$ld_total_programado       += $ld_programado;
					$ld_total_devengado        += $ld_devengado;
					$ld_total_liquidado        += $ld_liquidado;
					$ld_total_recaudado        += $ld_recaudado;
					$ld_total_programado_acum  += $ld_programado_acum;
					$ld_total_devengado_acum   += $ld_devengado_acum;
					$ld_total_liquidado_acum   += $ld_liquidado_acum;
					$ld_total_recaudado_acum   += $ld_recaudado_acum;
					$ld_total_ingresos_recibir += $ld_ingresos_recibir;
				}  
			}
			else
			{
				$la_data_tot[1]=array('totales'=>"TOTALES ".$ls_partida_aux,
							'previsto'=>number_format($ld_total_previsto,2,",","."),
							'modificado'=>number_format($ld_total_modificado,2,",","."),
							'programado'=>number_format($ld_total_programado,2,",","."),
							'devengado'=>number_format($ld_total_devengado,2,",","."),
							'liquidado'=>number_format($ld_total_liquidado,2,",","."),
							'recaudado'=>number_format($ld_total_recaudado,2,",","."),
							'programado_acum'=>number_format($ld_total_programado_acum,2,",","."),
							'devengado_acum'=>number_format($ld_total_devengado_acum,2,",","."),
							'liquidado_acum'=>number_format($ld_total_liquidado_acum,2,",","."),
							'recaudado_acum'=>number_format($ld_total_recaudado_acum,2,",","."),
							'ingresos_recibir'=>number_format($ld_total_ingresos_recibir,2,",","."));
 
				uf_print_detalle(&$li_row,&$lo_titulo,$lo_hoja,$lo_datacenter,$lo_dataleft,$lo_dataright,$la_data);
				uf_print_pie_cabecera(&$li_row,&$lo_titulo,$lo_hoja,$lo_datacenter,$lo_dataleft,$lo_dataright,$la_data_tot);
				unset($la_data,$la_data_tot);
				$li_i = 0;
				$ld_total_previsto=$ld_total_modificado=$ld_total_programado=$ld_total_devengado=0;
				$ld_total_liquidado=$ld_total_recaudado=$ld_total_programado_acum=0;
				$ld_total_devengado_acum=$ld_total_liquidado_acum=$ld_total_recaudado_acum=$ld_total_ingresos_recibir=0;				   $ls_partida_aux		= $ls_ramo;
			}					  							 						   
			$ld_previsto         = number_format($ld_previsto,2,",",".");
			$ld_modificado       = number_format($ld_modificado,2,",",".");
			$ld_programado       = number_format($ld_programado,2,",",".");
			$ld_devengado        = number_format($ld_devengado,2,",",".");
			$ld_liquidado        = number_format($ld_liquidado,2,",",".");
			$ld_recaudado        = number_format($ld_recaudado,2,",",".");
			$ld_programado_acum  = number_format($ld_programado_acum,2,",",".");
			$ld_devengado_acum   = number_format($ld_devengado_acum,2,",",".");
			$ld_liquidado_acum   = number_format($ld_liquidado_acum,2,",",".");
			$ld_recaudado_acum   = number_format($ld_recaudado_acum,2,",",".");
			$ld_ingresos_recibir = number_format($ld_ingresos_recibir,2,",",".");
				   
			$li_i++;
			$la_data[$li_i]=array('ramo'=>$ls_ramo,
			                      'subramo'=>$ls_subramo,
								  'especifica'=>$ls_especifica,
				                  'subesp'=>$ls_subesp,
								  'denominacion'=>$ls_denominacion,
								  'previsto'=>$ld_previsto,
								  'modificado'=>$ld_modificado,
								  'programado'=>$ld_programado,
								  'devengado'=>$ld_devengado,
								  'liquidado'=>$ld_liquidado,
								  'recaudado'=>$ld_recaudado,
								  'programado_acum'=>$ld_programado_acum,
								  'devengado_acum'=>$ld_devengado_acum,
								  'liquidado_acum'=>$ld_liquidado_acum,
								  'recaudado_acum'=>$ld_recaudado_acum,
								  'ingresos_recibir'=>$ld_ingresos_recibir);
			
			if ($z==$li_tot)
			{
				if (isset($la_data_tot))
				{
					unset($la_data_tot);
				}
				$la_data_tot[1]=array('totales'=>"TOTALES ".$ls_partida_aux,
							'previsto'=>number_format($ld_total_previsto,2,",","."),
							'modificado'=>number_format($ld_total_modificado,2,",","."),
							'programado'=>number_format($ld_total_programado,2,",","."),
							'devengado'=>number_format($ld_total_devengado,2,",","."),
							'liquidado'=>number_format($ld_total_liquidado,2,",","."),
							'recaudado'=>number_format($ld_total_recaudado,2,",","."),
							'programado_acum'=>number_format($ld_total_programado_acum,2,",","."),
							'devengado_acum'=>number_format($ld_total_devengado_acum,2,",","."),
							'liquidado_acum'=>number_format($ld_total_liquidado_acum,2,",","."),
							'recaudado_acum'=>number_format($ld_total_recaudado_acum,2,",","."),
							'ingresos_recibir'=>number_format($ld_total_ingresos_recibir,2,",","."));				   
				uf_print_detalle(&$li_row,&$lo_titulo,$lo_hoja,$lo_datacenter,$lo_dataleft,$lo_dataright,$la_data);
				uf_print_pie_cabecera(&$li_row,&$lo_titulo,$lo_hoja,$lo_datacenter,$lo_dataleft,$lo_dataright,$la_data_tot);
				//Impresión del Total General.
				unset($la_data_tot);
				$la_data_tot[1]=array('totales'=>"TOTAL GENERAL ",
							'previsto'=>number_format($ld_montotpre,2,",","."),
							'modificado'=>number_format($ld_montotmod,2,",","."),
							'programado'=>number_format($ld_montotpro,2,",","."),
							'devengado'=>number_format($ld_montotdev,2,",","."),
							'liquidado'=>number_format($ld_montotliq,2,",","."),
							'recaudado'=>number_format($ld_montotrec,2,",","."),
							'programado_acum'=>number_format($ld_montotpac,2,",","."),
							'devengado_acum'=>number_format($ld_montotdac,2,",","."),
							'liquidado_acum'=>number_format($ld_montotlac,2,",","."),
							'recaudado_acum'=>number_format($ld_montotrac,2,",","."),
							'ingresos_recibir'=>number_format($ld_montotire,2,",","."));
				uf_print_pie_cabecera(&$li_row,&$lo_titulo,$lo_hoja,$lo_datacenter,$lo_dataleft,$lo_dataright,$la_data_tot);
				unset($la_data);
			}
		}//for
		
		}
		
					
		unset($la_data,$la_data_tot);
	 	
		if (isset($d) && $d)
		{
			//nothing
	 	}
		else			
		{
			$lo_libro->close();
			header("Content-Type: application/x-msexcel; name=\"spi_ejecucion_trimestral_inst_07.xls\"");
			header("Content-Disposition: inline; filename=\"spi_ejecucion_trimestral_inst_07.xls\"");
			$fh=fopen($lo_archivo, "rb");
			fpassthru($fh);
			unlink($lo_archivo);
		}
		
	}//else
	unset($io_report,$io_funciones);
?> 