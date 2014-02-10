<?php
    session_start();   
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
	function uf_print_encabezado_pagina($as_titulo,$as_titulo2,&$io_pdf)
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
		$io_encabezado=$io_pdf->openObject();		
		$io_pdf->saveState();		
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],25,520,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(10,$as_titulo);
		$tm=380-($li_tm/2);
		$io_pdf->addText($tm,545,12,"<b>".$as_titulo."</b>"); // Agregar el título
		$li_tm=$io_pdf->getTextWidth(10,$as_titulo2);
		$tm=380-($li_tm/2);
		$io_pdf->addText($tm,530,12,"<b>".$as_titulo2."</b>"); // Agregar el título
		$io_pdf->addText(707,595,7,"Fecha: ".date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(714,585,7,"Hora: ".date("h:i a")); // Agregar la hora
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------	
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_listado($la_data,&$io_pdf)
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
		$la_columna=array('numero'=>'<b>No</b>',
						  'numordcom'=>'<b>O.C. No.</b>',
						  'estcondat'=>'<b>Tipo</b>',
						  'fecha'=>'<b>Fecha</b>',
						  'proveedor'=>'<b>Beneficiario</b>',
						  'fact'=>'<b>Fact.</b>',
						  'fecfac'=>'<b>Fecha</b>',
						  'obscom'=>'<b>Concepto</b>',
						  'partida'=>'<b>Partida</b>',
						  'monto'=>'<b>Monto</b>');
						  
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 10,  // Tamaño de Letras de los títulos
						 'showLines'=>2, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('numero'=>array('justification'=>'center','width'=>30), // Justificación y ancho de la columna
						 			   'numordcom'=>array('justification'=>'left','width'=>90), // Justificación y ancho de la columna
									   'estcondat'=>array('justification'=>'left','width'=>50), // Justificación y ancho de la columna
						 			   'fecha'=>array('justification'=>'center','width'=>60), // Justificación y ancho de la columna
						 			   'proveedor'=>array('justification'=>'left','width'=>120),
						 			   'fact'=>array('justification'=>'center','width'=>40),
						 			   'fecfac'=>array('justification'=>'center','width'=>65),
						 			   'obscom'=>array('justification'=>'left','width'=>200),
						 			   'partida'=>array('justification'=>'left','width'=>45),
   						 			   'monto'=>array('justification'=>'right','width'=>65))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_pie_cabecera($la_data,$ai_montot,&$io_pdf)
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
	    $io_pdf->ezSetDy(-10);
		$la_columnas=array('blanco'=>'','partida'=>'Partida','monto'=>'Monto');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 10,  // Tamaño de Letras de los títulos
						 'showLines'=>2, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('blanco'=>array('justification'=>'right','width'=>550), // Ancho Máximo de la tabla
									   'partida'=>array('justification'=>'center','width'=>95),
						 			   'monto'=>array('justification'=>'right','width'=>110)));
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);	
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
		
		$la_data[1]= array('partida'=>"TOTAL GENERAL.......................................................................",'monto'=>$ai_montot);
		$la_columnas=array('partida'=>'Partida','monto'=>'Monto');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 10,  // Tamaño de Letras de los títulos
						 'showLines'=>2, // Mostrar Líneas
						 'shaded'=>2, // Sombra entre líneas
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('partida'=>array('justification'=>'right','width'=>645), // Ancho Máximo de la tabla
						 			   'monto'=>array('justification'=>'right','width'=>110)));
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);	
	}// end function uf_print_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------


	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------
	require_once("../../shared/class_folder/sigesp_include.php");
	require_once("../../shared/class_folder/class_sql.php");	
	require_once("../../shared/ezpdf/class.ezpdf.php");
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
	$ls_bolivares="Bs.";
		
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
			error_reporting(E_ALL);
			set_time_limit(1800);
			$io_pdf=new Cezpdf('LETTER','landscape'); // Instancia de la clase PDF
			$io_pdf->selectFont('../../shared/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
			$io_pdf->ezSetCmMargins(3.5,2,2,2); // Configuración de los margenes en centímetros
			uf_print_encabezado_pagina($ls_titulo,$ls_titulo2,$io_pdf); // Imprimimos el encabezado de la página
			$io_pdf->ezStartPageNumbers(778,47,9,'','',1); // Insertar el número de página
			$ldec_monto=0;
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
					$ls_partida  = $rs_data->fields["partida"];
					$li_monto  = $rs_data->fields["monto"];
					$ls_fecha   = $io_funciones->uf_convertirfecmostrar($ls_fecordcom);	
					$ls_nombre  = $io_report->uf_select_nombre_proveedor($ls_codpro);
					if($ls_estcondat=="B") 
					{  
						$ls_estcondat="Bienes";  
					}
					if($ls_estcondat=="S")
					{
						$ls_estcondat="Servicios";
					}
					$io_ds->insertRow("partida",$ls_partida);
					$io_ds->insertRow("monto",$li_monto);
                    $li_monto = number_format($li_monto,2,",",".");
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
					$la_data[$li_i]= array('numero'=>$li_i,'numordcom'=>$ls_numordcom,'estcondat'=>$ls_estcondat,'fecha'=>$ls_fecha,
									       'proveedor'=>$ls_nombre,'fact'=>$ls_documento,'fecfac'=>$ls_fecfac,'obscom'=>$ls_obscom,
										   'partida'=>$ls_partida,'monto'=>$li_monto);
					$rs_data->MoveNext();
				}
			    uf_print_listado($la_data,$io_pdf); // Imprimimos el detalle 
				$io_ds->group_by(array('0'=>'partida'),array('0'=>'monto'),'monto');
				$li_totrow=$io_ds->getRowCount('partida');	
				$li_montot=0;
				for($li_fila=1;$li_fila<=$li_totrow;$li_fila++)
				{
					$ls_partida = $io_ds->getValue('partida',$li_fila);
					$li_montopar = $io_ds->getValue('monto',$li_fila);
					$li_montot=$li_montot+$li_montopar;
                    $li_montopar = number_format($li_montopar,2,",",".");
					$la_datapar[$li_fila]= array('blanco'=>"<b>TOTAL POR PARTIDA......................................</b>",'partida'=>$ls_partida,'monto'=>$li_montopar);
				}
                $li_montot = number_format($li_montot,2,",",".");
			    uf_print_pie_cabecera($la_datapar,$li_montot,$io_pdf); // Imprimimos el detalle 
				if($lb_valido) // Si no ocurrio ningún error
				{
					$io_pdf->ezStopPageNumbers(1,1); // Detenemos la impresión de los números de página
					$io_pdf->ezStream(); // Mostramos el reporte
				}
				else  // Si hubo algún error
				{
					print("<script language=JavaScript>");
					print("alert('Ocurrio un error al generar el reporte. Intente de Nuevo');"); 
					print("close();");
					print("</script>");		
				}
				unset($io_pdf);
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