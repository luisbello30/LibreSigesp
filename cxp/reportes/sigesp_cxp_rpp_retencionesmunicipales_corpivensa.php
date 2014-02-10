<?php
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//    REPORTE: Retencion de ISLR
	//  ORGANISMO: Ninguno en particular
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
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

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_seguridad($as_titulo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_insert_seguridad
		//		   Access: private 
		//	    Arguments: as_titulo // Título del reporte
		//    Description: función que guarda la seguridad de quien generó el reporte
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 03/07/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_cxp;
		
		$ls_descripcion="Generó el Reporte ".$as_titulo;
		$lb_valido=$io_fun_cxp->uf_load_seguridad_reporte("CXP","sigesp_cxp_r_retencionesmunicipales.php",$ls_descripcion);
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,$as_numcom,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 04/07/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->setStrokeColor(0,0,0);
//		$io_pdf->rectangle(20,650,558,30);
		$io_pdf->rectangle(20,20,730,568);
		$io_pdf->addJpegFromFile('../../shared/imagebank/gobierno_capital.jpg',40,500,110,80); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(13,"COMPROBANTE DE RETENCIÓN DEL IMPUESTO");
		$tm=396-($li_tm/2);
		$io_pdf->addText($tm,480,13,"COMPROBANTE DE RETENCIÓN DEL IMPUESTO"); // Agregar el título
		$li_tm=$io_pdf->getTextWidth(13,"DEL UNO POR MIL (1 X 1000)");
		$tm=396-($li_tm/2);
		$io_pdf->addText($tm,463,13,"DEL UNO POR MIL (1 X 1000)"); // Agregar el título

		$li_tm=$io_pdf->getTextWidth(11,"Republica Bolivariana de Venezuela");
		$tm=396-($li_tm/2);
		$io_pdf->addText($tm,550,11,"República Bolivariana de Venezuela"); // Agregar el título
		$li_tm=$io_pdf->getTextWidth(11,"Gobierno del Distrito Capital");
		$tm=396-($li_tm/2);
		$io_pdf->addText($tm,535,11,"Gobierno del Distrito Capital"); // Agregar el título
		$li_tm=$io_pdf->getTextWidth(11,"Servicio de Administracion Tributaria del Distrito Capital");
		$tm=396-($li_tm/2);
		$io_pdf->addText($tm,520,11,"Servicio de Administracion Tributaria del Distrito Capital"); // Agregar el título
		$io_pdf->addText(550,500,11,"N° Correlativo: ".$as_numcom); // Agregar el título
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado($as_agente,$as_nomsujret,$as_rif,$as_nit,$as_direccion,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezado
		//		   Access: private 
		//	    Arguments: as_agente // Nombre del agente de retención
		//	    		   as_nombre // Nombre del proveedor ó beneficiario
		//	    		   as_rif // Rif del proveedor ó beneficiario
		//	    		   as_nit // nit del proveedor ó beneficiario
		//	    		   as_telefono // Telefono del proveedor ó beneficiario
		//	    		   as_direccion // Dirección del proveedor ó beneficiario
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime el detalle por recepción
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 05/07/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_pdf->ezSetY(430);
		$la_data=array(array('name'=>'Agente de Retención:'."  ".'<b>'.$as_agente.'</b>'),
					   array('name'=>'N° de RIF:'."  ".'<b>'.$_SESSION["la_empresa"]["rifemp"].'</b>'),
					   array('name'=>'Domicilio Fiscal:'."  ".$_SESSION["la_empresa"]["direccion"]),
					   array('name'=>'Telefono:'."  ".'<b>'.$_SESSION["la_empresa"]["telemp"].'</b>'));
		
		 
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras						 
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas						 
						 'xOrientation'=>'center', // Orientación de la tabla
						 'width'=>720, // Ancho de la tabla
						 'maxWidth'=>720, // Orientación de la tabla
				      	 'cols'=>array('name'=>array('justification'=>'lef','width'=>720))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		
		
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
		$io_pdf->setStrokeColor(0,0,0);
		$io_pdf->line(20,350,750,350);
		$io_pdf->ezSetY(350);
		$la_data=array(array('name'=>'Contribuyente: '."  ".'<b>'.$as_nomsujret.'</b>'),
					   array('name'=>'Persona Natural:______  Persona Jurídica:________         Cédula de Identidad ó RIF N°:'."  ".$as_rif),
					   array('name'=>'Por Concepto de:   Prestación de Servicio______       Adquisición de Bienes o Suministros:________       Ejecución de Obras:_______'),
					   array('name'=>'Descripción:_______________________________________________________________________________________________________________________'));
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras						 
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas						 
						 'xOrientation'=>'center', // Orientación de la tabla
						 'width'=>720, // Ancho de la tabla
						 'maxWidth'=>720, // Orientación de la tabla
				      	 'cols'=>array('name'=>array('justification'=>'lef','width'=>720))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
	}// end function uf_print_encabezado
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($ai_monto,$ai_cantidad,$ai_porcentaje,$as_tiporet, $ai_totalret, $as_numche,$ad_fecha,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: as_numsol // Número de recepción
		//	    		   as_concepto // Concepto de la solicitud
		//	    		   as_fechapago // Fecha de la recepción
		//	    		   ad_monto // monto de la recepción
		//	    		   ad_monret // monto retenido
		//	    		   ad_porcentaje // porcentaje de retención
		//	    		   as_numcon // numero de referencia
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime el detalle por recepción
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 05/07/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_pdf->ezSetDy(-10);
		$la_data=array(array('name'=>'Monto Bruto de la Operacion: Bs.'.$ai_monto."                     Monto del Impuesto: Bs.".$ai_cantidad),
					   array('name'=>'Monto Retenido:'."  ".$ai_totalret."                 Fecha de la Retencion:  ".$ad_fecha),
					   array('name'=>''),
					   array('name'=>'<b>Llenar solo en caso de pagos efectuados directamente en las cuentas receptoras de Fondos del Distrito Capital:</b>'),
					   array('name'=>'Banco:_________________________ N° de Planilla:_____________________________'),
					   array('name'=>'Monto Pagado: Bs. _________________________ Fecha de Pago:_____________________________'));
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras						 
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas						 
						 'xOrientation'=>'center', // Orientación de la tabla
						 'width'=>720, // Ancho de la tabla
						 'maxWidth'=>720, // Orientación de la tabla
				      	 'cols'=>array('name'=>array('justification'=>'lef','width'=>720))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
		
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_firmas($as_agente,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_firmas
		//		   Access: private 
		//	    Arguments: io_pdf // Instancia de objeto pdf
		//    Description: función que imprime el detalle por recepción
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 05/07/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_pdf->setStrokeColor(0,0,0);
		$io_pdf->line(20,170,750,170);
		$io_pdf->ezSetY(150);
		$la_data=array(array('name'=>'    Agente de Retención:                                                                             Firma:                                                 Sello:'),
					   array('name'=>'   _____________________________________________________________________________________________________________________________'),
					   array('name'=>'      (Responsable)'));
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras						 
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas						 
						 'xOrientation'=>'center', // Orientación de la tabla
						 'width'=>720, // Ancho de la tabla
						 'maxWidth'=>720, // Orientación de la tabla
				      	 'cols'=>array('name'=>array('justification'=>'lef','width'=>720))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
	}// end function uf_print_firmas
	//--------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------

	require_once("../../shared/ezpdf/class.ezpdf.php");
	require_once("sigesp_cxp_class_report.php");
	$io_report=new sigesp_cxp_class_report();
	require_once("../../shared/class_folder/class_funciones.php");
	$io_funciones=new class_funciones();				
	require_once("../class_folder/class_funciones_cxp.php");
	$io_fun_cxp=new class_funciones_cxp();
	$ls_tiporeporte=$io_fun_cxp->uf_obtenervalor_get("tiporeporte",0);
	global $ls_tiporeporte;
	if($ls_tiporeporte==1)
	{
		require_once("sigesp_cxp_class_reportbsf.php");
		$io_report=new sigesp_cxp_class_reportbsf();
	}
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	
	$ls_titulo="<b>RETENCION 1 X 1.000</b>";
	
    $ls_agente=$_SESSION["la_empresa"]["nombre"];
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_comprobantes=$io_fun_cxp->uf_obtenervalor_get("comprobantes","");
	$ls_mes=$io_fun_cxp->uf_obtenervalor_get("mes","");
	$ls_anio=$io_fun_cxp->uf_obtenervalor_get("anio","");
	$ls_agenteret=$_SESSION["la_empresa"]["nombre"];
	$ls_rifagenteret=$_SESSION["la_empresa"]["rifemp"];
	$ls_diragenteret=$_SESSION["la_empresa"]["direccion"];
	$ls_licagenteret=$_SESSION["la_empresa"]["numlicemp"];
	//--------------------------------------------------------------------------------------------------------------------------------
	$lb_valido=uf_insert_seguridad($ls_titulo); // Seguridad de Reporte
	if($lb_valido)
	{
		$la_comprobantes=split('-',$ls_comprobantes);
		$la_datos=array_unique($la_comprobantes);
		$li_totrow=count($la_datos);
		sort($la_datos,SORT_STRING);
		if($li_totrow<=0)
		{
			print("<script language=JavaScript>");
			print(" alert('No hay nada que Reportar');"); 
			print(" close();");
			print("</script>");
		}
		else
		{
			error_reporting(E_ALL);
			set_time_limit(1800);
			$io_pdf=new Cezpdf('LETTER','landscape');
			$io_pdf->selectFont('../../shared/ezpdf/fonts/Helvetica.afm');
			$io_pdf->ezSetCmMargins(7,2,3,3);
			$lb_valido=true;
			for ($li_z=0;($li_z<$li_totrow)&&($lb_valido);$li_z++)
			{
				$ls_numcom=$la_datos[$li_z];
				uf_print_encabezado_pagina($ls_titulo,$ls_numcom,$io_pdf);
				$lb_valido=$io_report->uf_retencionesmunicipales_proveedor($ls_numcom,$ls_mes,$ls_anio);
				if($lb_valido)
				{
					$li_total=$io_report->DS->getRowCount("numcom");
					for($li_i=1;$li_i<=$li_total;$li_i++)
					{
						$ls_numcon=$io_report->DS->data["numcom"][$li_i];		 								
						$ls_codret=$io_report->DS->data["codret"][$li_i];			   
						$ls_nomsujret=$io_report->DS->data["nomsujret"][$li_i];	
						$ls_rif=$io_report->DS->data["rif"][$li_i];	
						$ls_nit=$io_report->DS->data["nit"][$li_i];	
						$ls_dirsujret=$io_report->DS->data["dirsujret"][$li_i];		
						$li_estcmpret=$io_report->DS->data["estcmpret"][$li_i];	
						$ls_numlic=$io_report->DS->data["numlic"][$li_i];									
					}											
					uf_print_encabezado($ls_agente,$ls_nomsujret,$ls_rif,$ls_nit,$ls_dirsujret,&$io_pdf);
					$lb_valido=$io_report->uf_retencionesmunicipales_detalles($ls_numcom);
					if($lb_valido)
					{
						$li_totalbaseimp=0;
						$li_totalmontoimp=0;
						$li_total=$io_report->ds_detalle->getRowCount("numfac");			   
						for($li_i=1;($li_i<=$li_total)&&($lb_valido);$li_i++)
						{
							$ls_numsop=$io_report->ds_detalle->data["numsop"][$li_i];					
							$li_baseimp=$io_report->ds_detalle->data["basimp"][$li_i];	
							$li_porimp='RETENCION 1 X 1.000';	
							$li_porcentaje='0,001';
							$li_totimp=$io_report->ds_detalle->data["iva_ret"][$li_i];	
							$lb_valido=$io_report->uf_select_datos_cheque_retencion($ls_numsop,$ls_nummov,$ld_fecmov,$li_monto);
							$li_totalbaseimp=$li_totalbaseimp + $li_baseimp ;	
							$li_totalmontoimp=$li_totalmontoimp + $li_totimp;	
							$li_baseimp=number_format($li_baseimp,2,",",".");			
							$li_totimp=number_format($li_totimp,2,",",".");							
							$li_monto=number_format($li_monto,2,",",".");		
							$ld_fecmov=$io_funciones->uf_convertirfecmostrar($ld_fecmov);
																					
						  }																		 																						  						  if($lb_valido) // Si no ocurrio ningún error
						  {
							  $li_totalbaseimp= number_format($li_totalbaseimp,2,",","."); 
							  $li_totalmontoimp= number_format($li_totalmontoimp,2,",","."); 
							  uf_print_detalle($li_monto,$li_baseimp,$li_porcentaje,$li_porimp, $li_totimp, $ls_nummov,
											   $ld_fecmov,&$io_pdf);
							  uf_print_firmas($ls_agente,&$io_pdf);
						  }
						  							 
					}
				}
				$io_report->DS->reset_ds();
				if($li_z<($li_totrow-1))
				{
					$io_pdf->ezNewPage(); 					  
				}		

			}
			if($lb_valido) // Si no ocurrio ningún error
			{
				$io_pdf->ezStopPageNumbers(1,1); // Detenemos la impresión de los números de página
				$io_pdf->ezStream(); // Mostramos el reporte
			}
			else  // Si hubo algún error
			{
				print("<script language=JavaScript>");
				print(" alert('Ocurrio un error al generar el reporte. Intente de Nuevo');"); 
				//print(" close();");
				print("</script>");		
			}
			unset($io_pdf);
		}
	}
	unset($io_report);
	unset($io_funciones);
	unset($io_fun_cxp);
?> 