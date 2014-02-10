<?php
    session_start();   
	ini_set('memory_limit','2048M');
	ini_set('max_execution_time ','0');
	header("Pragma: public");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private",false);
	if(!array_key_exists("la_logusr",$_SESSION))
	{
		print "<script language=JavaScript>";
		print "close();";
		print "opener.document.form1.submit();";		
		print "</script>";		
	}
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,$as_titulo2)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   as_desnom // Descripción de la nómina
		//	    		   as_periodo // Descripción del período
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Yozelin Barragan
		// Fecha Creación: 16/07/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------	
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_listado($li_i,$lo_libro,$lo_hoja,$la_data,&$li_fila)
	{	 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Objeto PDF
		//    Description: función que imprime el detalle
		//	   Creado Por: Ing. Yozelin Barragan
		// Fecha Creación: 16/07/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////				
		$lo_datacenter= &$lo_libro->addformat();
		$lo_datacenter->set_font("Verdana");
		$lo_datacenter->set_align('center');
		$lo_datacenter->set_size('9');
		$lo_dataleft= &$lo_libro->addformat();
		$lo_dataleft->set_text_wrap();
		$lo_dataleft->set_font("Verdana");
		$lo_dataleft->set_align('left');
		$lo_dataleft->set_size('9');
		$lo_datadate= &$lo_libro->addformat(array(num_format => 'dd/mm/yyyy'));
		$lo_datadate->set_text_wrap();
		$lo_datadate->set_font("Verdana");
		$lo_datadate->set_align('center');
		$lo_datadate->set_size('8');
		$lo_dataright= &$lo_libro->addformat(array(num_format => '#,##0.00'));
		$lo_dataright->set_font("Verdana");
		$lo_dataright->set_align('right');
		$lo_dataright->set_size('8');
		$lo_hoja->write($li_fila, 0, 'N',$lo_datacenter);
		$lo_hoja->write($li_fila, 1, 'O.C. No.',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
		$lo_hoja->write($li_fila, 2, 'TIPO',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
		$lo_hoja->write($li_fila, 3, 'FECHA',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
		$lo_hoja->write($li_fila, 4, 'BENEFICIARIO',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
		$lo_hoja->write($li_fila, 5, 'FACT',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
		$lo_hoja->write($li_fila, 6, 'FECHA',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
		$lo_hoja->write($li_fila, 7, 'CONCEPTO',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
		$lo_hoja->write($li_fila, 8, 'PARTIDA',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
		$lo_hoja->write($li_fila, 9, 'MONTO',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
		for($i=1;$i<=$li_i;$i++)
		{
			$li_fila++;
			$lo_hoja->write($li_fila, 0, " ".$la_data[$i]['numero'],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'8')));
			$lo_hoja->write($li_fila, 1, " ".$la_data[$i]['numordcom'],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'8')));
			$lo_hoja->write($li_fila, 2, " ".$la_data[$i]['estcondat'],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'8')));
			$lo_hoja->write($li_fila, 3, " ".$la_data[$i]['fecha'],$lo_datadate);
			$lo_hoja->write($li_fila, 4, " ".$la_data[$i]['proveedor'],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'8')));
			$lo_hoja->write($li_fila, 5, " ".$la_data[$i]['fact'],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'8')));
			$lo_hoja->write($li_fila, 6, " ".$la_data[$i]['fecfac'],$lo_datadate);
			$lo_hoja->write($li_fila, 7, " ".$la_data[$i]['obscom'],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'8')));
			$lo_hoja->write($li_fila, 8, " ".$la_data[$i]['partida'],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'8')));
			$lo_hoja->write($li_fila, 9, $la_data[$i]['monto'],$lo_dataright);
		}
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function a($li_totrow,$lo_libro,$lo_hoja,&$li_fila)
	{
		$lo_hoja->write($li_fila, 0, 'N',$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'8')));
	
	}
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_pie_cabecera($li_totrow,$lo_libro,$lo_hoja,$la_data,$ai_montot,&$li_fila)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: private 
		//	    Arguments: as_codper // total de registros que va a tener el reporte
		//	    		   as_nomper // total de registros que va a tener el reporte
		//	    		   io_pdf // total de registros que va a tener el reporte
		//    Description: función que imprime la cabecera de cada página
		//	   Creado Por: Ing. Yozelin Barragan
		// Fecha Creación: 16/07/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lo_dataright= &$lo_libro->addformat(array(num_format => '#,##0.00'));
		$lo_dataright->set_font("Verdana");
		$lo_dataright->set_align('right');
		$lo_dataright->set_size('8');
		for($i=1;$i<=$li_totrow;$i++)
		{
			$li_fila++;
			$lo_hoja->write($li_fila, 1, " ".$la_data[$i]['blanco'],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'8')));
			$lo_hoja->write($li_fila, 2, " ".$la_data[$i]['partida'],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'8')));
			$lo_hoja->write($li_fila, 3, $la_data[$i]['monto'],$lo_dataright);
		}
		$li_fila++;
		$lo_hoja->write($li_fila, 2, " TOTAL GENERAL.....",$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'8')));
		$lo_hoja->write($li_fila, 3, $ai_montot,$lo_dataright);
	}// end function uf_print_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------
	//--------------------------------------------  Llamada a clases de gneracion de excel  ------------------------------------------
	require_once ("../../shared/writeexcel/class.writeexcel_workbookbig.inc.php");
	require_once ("../../shared/writeexcel/class.writeexcel_worksheet.inc.php");
	$lo_archivo =  tempnam("/tmp", "relacion_mensual.xls");
	$lo_libro = &new writeexcel_workbookbig($lo_archivo);
	$lo_hoja = &$lo_libro->addworksheet();

	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------
	require_once("../../shared/class_folder/sigesp_include.php");
	require_once("../../shared/class_folder/class_sql.php");	
	require_once("../../shared/class_folder/class_funciones.php");
	require_once("sigesp_soc_class_report.php");	
	require_once("../class_folder/class_funciones_soc.php");
    require_once("../../shared/class_folder/class_datastore.php");
	$in= new sigesp_include();
	$con= $in->uf_conectar();
	$io_sql= new class_sql($con);	
	$io_funciones = new class_funciones();	
	$io_fun_soc= new class_funciones_soc();
	$io_report= new sigesp_soc_class_report($con);
	$io_ds = new class_datastore();
		
	//----------------------------------------------------  Inicializacion de variables  -----------------------------------------------
	$lb_valido=false;
	//----------------------------------------------------  Parámetros del encabezado    -----------------------------------------------
	$ls_titulo ="RELACION MENSUAL DE ORDENES DE COMPRA";	
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	
	$ls_numordcomdes=$io_fun_soc->uf_obtenervalor_get("txtnumordcomdes","");
	$ls_numordcomhas=$io_fun_soc->uf_obtenervalor_get("txtnumordcomhas","");
	$ls_fecdes=$io_fun_soc->uf_obtenervalor_get("txtfecdes","");
	$ls_fechas=$io_fun_soc->uf_obtenervalor_get("txtfechas","");
	$ls_estcondat=$io_fun_soc->uf_obtenervalor_get("rdtipo","");
	$ls_titulo2 ="DEL ".$ls_fecdes." AL ".$ls_fechas;	
	 
	//--------------------------------------------------------------------------------------------------------------------------------
	$rs_data = $io_report->uf_select_partidas($ls_numordcomdes,$ls_numordcomhas,$ls_fecdes,$ls_fechas,$ls_estcondat,&$lb_valido);
	if($lb_valido==false) // Existe algún error ó no hay registros
	{
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');"); 
		print(" close();");
		print("</script>");
	}
	else // Imprimimos el reporte
	{
		$ls_descripcion="Generó el Reporte de Ubicacion de Orden de Compra";
		//$lb_valido=$io_fun_soc->uf_load_seguridad_reporte("SOC","sigesp_soc_r_orden_ubicacioncompra.php",$ls_descripcion);
		if($lb_valido)	
		{
			$lo_encabezado= &$lo_libro->addformat();
			$lo_encabezado->set_bold();
			$lo_encabezado->set_font("Verdana");
			$lo_encabezado->set_align('center');
			$lo_encabezado->set_size('11');
			$lo_titulo= &$lo_libro->addformat();
			$lo_titulo->set_bold();
			$lo_titulo->set_font("Verdana");
			$lo_titulo->set_align('center');
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
			$lo_hoja->set_column(0,0,15);
			$lo_hoja->set_column(1,1,20);
			$lo_hoja->set_column(2,2,50);
			$lo_hoja->set_column(3,3,20);
			$lo_hoja->set_column(4,4,30);
			$lo_hoja->set_column(5,5,30);
			$lo_hoja->set_column(6,6,30);
	
			$lo_hoja->write(0, 3, $ls_titulo,$lo_encabezado);
			$lo_hoja->write(1, 3, $ls_titulo2,$lo_encabezado);
			
			$li_fila=3;
			$li_i=0;
			$li_row=$io_sql->num_rows($rs_data);
			if ($li_row>0)
			{     
				while((!$rs_data->EOF))
				{
					$li_i++;
					$ls_numordcom  = $rs_data->fields["numordcom"];
					$ls_estcondat  = $rs_data->fields["estcondat"];
					$ls_fecordcom  = $rs_data->fields["fecordcom"];
					$ls_codpro  = $rs_data->fields["cod_pro"];
					$ls_obscom = $rs_data->fields["obscom"];
					$ls_partida = $rs_data->fields["partida"];
					$li_monto = $rs_data->fields["monto"];
					$ls_fecha   = $io_funciones->uf_convertirfecmostrar($ls_fecordcom);	
					$ls_nombre  = $io_report->uf_select_nombre_proveedor($ls_codpro);
					if($ls_estcondat=="B") 
					{  
						$ls_estcondataux="Bienes";  
					}
					if($ls_estcondat=="S")
					{
						$ls_estcondataux="Servicios";
					}
					$io_ds->insertRow("partida",$ls_partida);
					$io_ds->insertRow("monto",$li_monto);
					$rs_facturas=$io_report->uf_load_facturas($ls_numordcom,$ls_codpro,&$lb_valido);
					$ls_documento  = "";
					$ls_fecfac  = "";
					while((!$rs_facturas->EOF))
					{
						$ls_documento  = $rs_facturas->fields["documento"];
						$ls_fecfac  = $rs_facturas->fields["fecha"];
						$rs_facturas->MoveNext();
					}
					$ls_fecfac   = $io_funciones->uf_convertirfecmostrar($ls_fecfac);	
                 //   $li_monto = number_format($li_monto,2,",",".");
					$la_data[$li_i]= array('numero'=>$li_i,'numordcom'=>$ls_numordcom,'estcondat'=>$ls_estcondataux,'fecha'=>$ls_fecha,
									       'proveedor'=>$ls_nombre,'fact'=>$ls_documento,'fecfac'=>$ls_fecfac,'obscom'=>$ls_obscom,'partida'=>$ls_partida,'monto'=>$li_monto);
					$rs_data->MoveNext();
				}
				$io_ds->group_by(array('0'=>'partida'),array('0'=>'monto'),'monto');
				$li_totrow=$io_ds->getRowCount('partida');	
				$li_montot=0;
				for($li_j=1;$li_j<=$li_totrow;$li_j++)
				{
					$ls_partida = $io_ds->getValue('partida',$li_j);
					$li_montopar = $io_ds->getValue('monto',$li_j);
					$li_montot=$li_montot+$li_montopar;
                   // $li_montopar = number_format($li_montopar,2,",",".");
					$la_datapar[$li_j]= array('blanco'=>"TOTAL POR PARTIDA",'partida'=>$ls_partida,'monto'=>$li_montopar);
				}
              //  $li_montot = number_format($li_montot,2,",",".");
				uf_print_listado($li_i,$lo_libro,$lo_hoja,$la_data,&$li_fila);
			    uf_print_pie_cabecera($li_totrow,$lo_libro,$lo_hoja,$la_datapar,$li_montot,&$li_fila); // Imprimimos el detalle 
				if($lb_valido)
				{
					unset($io_report);
					$lo_libro->close();
					header("Content-Type: application/x-msexcel; name=\"relacion_mensual.xls\"");
					header("Content-Disposition: inline; filename=\"relacion_mensual.xls\"");
					$fh=fopen($lo_archivo, "rb");
					fpassthru($fh);
					unlink($lo_archivo);
					print("<script language=JavaScript>");
					print(" close();");
					print("</script>");
				}
				else
				{
					print("<script language=JavaScript>");
					print(" alert('Ocurrio un error al generarse el Reporte');");
					print(" close();");
					print("</script>");
				}
			}
			else
			{
				print("<script language=JavaScript>");
				print("alert('No hay nada que reportar');"); 
				print("close();");
				print("</script>");		
			}				
		}	
		unset($io_report);
		unset($io_funciones);
	}	
?> 