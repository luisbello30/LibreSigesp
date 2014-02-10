<?php
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//    REPORTE: Retencion de ISLR
	//  ORGANISMO: FUDECO
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
		$lb_valido=$io_fun_cxp->uf_load_seguridad_reporte("CXP","sigesp_cxp_r_retencionesislr.php",$ls_descripcion);
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,&$io_pdf)
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
		$io_pdf->line(20,40,578,40);
		$io_pdf->setStrokeColor(0,0,0);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],30,700,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,680,11,$as_titulo); // Agregar el título
		$io_pdf->addText(500,750,8,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(506,743,7,date("h:i a")); // Agregar la Hora
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado($as_sigemp,$as_nomsujret,$as_rifageret,$as_rifsujret,$as_nitsujret,$as_telsujret,$as_dirageret,$as_dirsujret,$as_tipper,&$io_pdf)
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
		//    Description: Función que imprime el detalle por recepción
		//	   Creado Por: Ing. Néstor Falcón.
		// Fecha Creación: 04/12/2008.
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		$la_data[1]=array('column1'=>'<b>DATOS DEL AGENTE DE RETENCIÓN</b>','column2'=>'<b>DATOS DEL SUJETO RETENIDO</b>');
		$la_columna=array('column1'=>'','column2'=>'');
		$la_config=array('showHeadings'=>0,// Mostrar encabezados
					     'fontSize'=>10,// Tamaño de Letras
					     'showLines'=>1,// Mostrar Líneas
					     'shaded'=>2,// Sombra entre líneas
					     'shadeCol'=>array(0.95,0.95,0.95),
					     'shadeCol2'=>array(0.95,0.95,0.95),
					     'width'=>580,// Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('column1'=>array('justification'=>'center','width'=>290),
						 			   'column2'=>array('justification'=>'center','width'=>290))); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);		       
		unset($la_data,$la_columna,$la_config);

		$la_data[1]=array('column1'=>"<b>Organismo: </b>".$as_sigemp,'column2'=>"<b>Razón:</b> ".$as_nomsujret);
		$la_columna=array('column1'=>'','column2'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
					     'fontSize'=>10,  // Tamaño de Letras
					     'showLines'=>1,    // Mostrar Líneas
					     'shaded'=>0,       // Sombra entre líneas
					     'width'=>580,     // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('column1'=>array('justification'=>'left','width'=>290),
						 			   'column2'=>array('justification'=>'left','width'=>290))); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);		       
		unset($la_data,$la_columna,$la_config);

		if ($as_tipper=='J')
		   {
			 $io_pdf->addText(555,635,8,"X");			 
		   }
		elseif($as_tipper=='N')
		   {
		     $io_pdf->addText(455,635,8,"X");
		   }

		$la_data[1]=array('column1'=>"<b>RIF: </b>".$as_rifageret,'column2'=>"<b>Persona:</b>",'column3'=>'<b>Natural:</b>','column4'=>'<b>Jurídica:</b>');
		$la_columna=array('column1'=>'','column2'=>'','column3'=>'','column4'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
					     'fontSize'=>10,  // Tamaño de Letras
					     'showLines'=>1,    // Mostrar Líneas
					     'shaded'=>0,       // Sombra entre líneas
					     'width'=>580,     // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('column1'=>array('justification'=>'left','width'=>290),
						 			   'column2'=>array('justification'=>'left','width'=>100),
									   'column3'=>array('justification'=>'left','width'=>100),
									   'column4'=>array('justification'=>'left','width'=>90),)); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);		       
		unset($la_data,$la_columna,$la_config);

		$la_data[1]=array('column1'=>"<b>Funcionario Autorizado: </b>",'column2'=>"<b>N° RIF: </b>",'column3'=>"<b>N° NIT: </b>");
		$la_data[2]=array('column1'=>"Lcda. Betyali M. Lucena Freitez.",'column2'=>$as_rifsujret,'column3'=>$as_nitsujret);
		$la_columna=array('column1'=>'','column2'=>'','column3'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
					     'fontSize'=>10,  // Tamaño de Letras
					     'showLines'=>1,    // Mostrar Líneas
					     'shaded'=>0,       // Sombra entre líneas
					     'width'=>580,     // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('column1'=>array('justification'=>'left','width'=>290),
						 			   'column2'=>array('justification'=>'left','width'=>145),
									   'column3'=>array('justification'=>'left','width'=>145))); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);		       
		unset($la_data,$la_columna,$la_config);
		
		$ls_faxageret = $_SESSION["la_empresa"]["faxemp"];
		$ls_telageret = $_SESSION["la_empresa"]["telemp"];
		$la_data[1]=array('column1'=>"<b>Dirección: </b>".$as_dirageret.". Apartado 523. <b>Teléfono:</b> ".$ls_telageret." - <b>(Máster) Fax: </b>".$ls_faxageret,'column2'=>"<b>Dirección: </b>".$as_dirsujret.'. <b>Teléfono:</b> '.$as_telsujret);
		$la_columna=array('column1'=>'','column2'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
					     'fontSize'=>10,  // Tamaño de Letras
					     'showLines'=>1,    // Mostrar Líneas
					     'shaded'=>0,       // Sombra entre líneas
					     'width'=>580,     // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('column1'=>array('justification'=>'left','width'=>290),
						 			   'column2'=>array('justification'=>'left','width'=>290))); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);		       
		unset($la_data,$la_columna,$la_config);

		$la_data[1]=array('column1'=>"<b>INFORMACIÓN DEL IMPUESTO RETENIDO</b>");
		$la_columna=array('column1'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
					     'fontSize'=>10,  // Tamaño de Letras
					     'showLines'=>1,    // Mostrar Líneas
					     'shaded'=>0,       // Sombra entre líneas
					     'width'=>580,     // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('column1'=>array('justification'=>'center','width'=>580))); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);		       
		unset($la_data,$la_columna,$la_config);
		
		$la_data[1]=array('column1'=>'<b>Fecha</b>','column2'=>'<b>No. Fact./Control</b>','column3'=>'<b>Fecha Emisión Factura</b>','column4'=>'<b>Total a Pagar</b>','column5'=>'<b>Base Imponible</b>','column6'=>'<b>Importe Retenido</b>');
		$la_columna=array('column1'=>'','column2'=>'','column3'=>'','column4'=>'','column5'=>'','column6'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
					     'fontSize'=>9,  // Tamaño de Letras
					     'showLines'=>1,    // Mostrar Líneas
					     'width'=>580,     // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
 					     'shaded'=>2, // Sombra entre líneas
					     'shadeCol'=>array(0.95,0.95,0.95),
					     'shadeCol2'=>array(0.95,0.95,0.95),
						 'cols'=>array('column1'=>array('justification'=>'center','width'=>90),
						 			   'column2'=>array('justification'=>'center','width'=>100),
									   'column3'=>array('justification'=>'center','width'=>100),
									   'column4'=>array('justification'=>'center','width'=>100),
									   'column5'=>array('justification'=>'center','width'=>100),
									   'column6'=>array('justification'=>'center','width'=>90))); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);		       
		unset($la_data,$la_columna,$la_config);
	}// end function uf_print_encabezado
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($as_numrecdoc,$as_concepto,$ad_fecregdoc,$as_fechapago,$ad_monrecdoc,$ad_monto,$ad_monret,$ad_porcentaje,$as_numcon,&$io_pdf)
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
		//	   Creado Por: Ing. Néstor Falcón.
		// Fecha Creación: 04/12/2008 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$la_data[1]=array('column1'=>$ad_fecregdoc,'column2'=>$as_numrecdoc,'column3'=>$as_fechapago,'column4'=>$ad_monrecdoc,'column5'=>$ad_monto,'column6'=>$ad_monret);
		$la_columna=array('column1'=>'','column2'=>'','column3'=>'','column4'=>'','column5'=>'','column6'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
					     'fontSize'=>9,  // Tamaño de Letras
					     'showLines'=>1,    // Mostrar Líneas
					     'width'=>580,     // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
 					     'shaded'=>0, // Sombra entre líneas
						 'cols'=>array('column1'=>array('justification'=>'center','width'=>90),
						 			   'column2'=>array('justification'=>'center','width'=>100),
									   'column3'=>array('justification'=>'center','width'=>100),
									   'column4'=>array('justification'=>'right','width'=>100),
									   'column5'=>array('justification'=>'right','width'=>100),
									   'column6'=>array('justification'=>'right','width'=>90))); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);		       
		unset($la_data,$la_columna,$la_config);
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_totales($ad_montotpag,$ad_totbasimp,$ad_totmonret,&$io_pdf)
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
		//	   Creado Por: Ing. Néstor Falcón.
		// Fecha Creación: 04/12/2008 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		$ad_montotpag = number_format($ad_montotpag,2,',','.');
		$ad_totbasimp = number_format($ad_totbasimp,2,',','.');
		$ad_totmonret = number_format($ad_totmonret,2,',','.');
		
		$la_data[1]=array('column1'=>'<b>TOTALES</b>','column2'=>"<b>".$ad_montotpag."</b>",'column3'=>"<b>".$ad_totbasimp."</b>",'column4'=>"<b>".$ad_totmonret."</b>");
		$la_columna=array('column1'=>'','column2'=>'','column3'=>'','column4'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
					     'fontSize'=>9,  // Tamaño de Letras
					     'showLines'=>1,    // Mostrar Líneas
					     'width'=>390,     // Ancho Máximo de la tabla
						 'xPos'=>401, // Orientación de la tabla
 					     'shaded'=>0, // Sombra entre líneas
						 'cols'=>array('column1'=>array('justification'=>'center','width'=>100),
									   'column2'=>array('justification'=>'right','width'=>100),
									   'column3'=>array('justification'=>'right','width'=>100),
									   'column4'=>array('justification'=>'right','width'=>90))); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);		       
		unset($la_data,$la_columna,$la_config);
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	require_once("../../shared/ezpdf/class.ezpdf.php");
	require_once("sigesp_cxp_class_report.php");
	$io_report=new sigesp_cxp_class_report();
	require_once("../../shared/class_folder/class_funciones.php");
	$io_funciones=new class_funciones();				
	require_once("../class_folder/class_funciones_cxp.php");
	$io_fun_cxp=new class_funciones_cxp();
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ls_titulo    = "<b>COMPROBANTE RETENCIÓN IMPUESTO SOBRE LA RENTA</b>";
    $ls_agente    = $_SESSION["la_empresa"]["sigemp"];
	$ls_rifageret = $_SESSION["la_empresa"]["rifemp"];
	$ls_dirageret = $_SESSION["la_empresa"]["direccion"];
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_comprobantes=$io_fun_cxp->uf_obtenervalor_get("comprobantes","");
	$ls_procedencias=$io_fun_cxp->uf_obtenervalor_get("procedencias","");
	$ls_tiporeporte=$io_fun_cxp->uf_obtenervalor_get("tiporeporte",0);
	global $ls_tiporeporte;
	if($ls_tiporeporte==1)
	{
		require_once("sigesp_cxp_class_reportbsf.php");
		$io_report=new sigesp_cxp_class_reportbsf();
	}
	//--------------------------------------------------------------------------------------------------------------------------------
	$lb_valido=uf_insert_seguridad($ls_titulo); // Seguridad de Reporte
	if($lb_valido)
	{
		$la_procedencias=split('<<<',$ls_procedencias);
		$la_comprobantes=split('<<<',$ls_comprobantes);
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
			$io_pdf=new Cezpdf('LETTER','portrait');
			$io_pdf->selectFont('../../shared/ezpdf/fonts/Helvetica.afm');
			$io_pdf->ezSetCmMargins(4,4,3,3);
			$lb_valido=true;
			$ls_codigoant="";
			for ($li_z=0;($li_z<$li_totrow)&&($lb_valido);$li_z++)
			{
				$ld_montotpag=0;
				$ld_totbasimp=0;
				$ld_totmonret=0;
				uf_print_encabezado_pagina($ls_titulo,$io_pdf);
				$ls_numsol=$la_datos[$li_z];
				$ls_procede=$la_procedencias[$li_z]; 
				if($ls_procede=="SCBBCH")
				{
					$lb_valido=$io_report->uf_retencionesislr_scb($ls_numsol);  
				}
				else
				{
					$lb_valido=$io_report->uf_retencionesislr_cxp($ls_numsol);
				}
				if($lb_valido)
				{
					$ld_montotpag = $ld_totbasimp = $ld_totmonret = 0;
					$li_total=$io_report->DS->getRowCount("numdoc");
					for($li_i=1;($li_i<=$li_total);$li_i++)
					{
						$ls_tipproben=$io_report->DS->data["tipproben"][$li_i];
						if ($ls_tipproben=="P")
						   {
						     $ls_codigo    = $io_report->DS->data["cod_pro"][$li_i];
							 $ls_nombre    = $io_report->DS->data["proveedor"][$li_i];
							 $ls_telefono  = $io_report->DS->data["telpro"][$li_i];
							 $ls_direccion = $io_report->DS->data["dirpro"][$li_i];
							 $ls_rif	   = $io_report->DS->data["rifpro"][$li_i];
							 $ls_tipper    = substr(trim(strtoupper($io_report->DS->data["tipper"][$li_i])),0,1);
						   }
						else
						   {
							 $ls_codigo    = $io_report->DS->data["ced_bene"][$li_i];
							 $ls_nombre    = $io_report->DS->data["beneficiario"][$li_i];
							 $ls_telefono  = $io_report->DS->data["telbene"][$li_i];
							 $ls_direccion = $io_report->DS->data["dirbene"][$li_i];
							 $ls_rif       = $io_report->DS->data["rifben"][$li_i];
							 $ls_tipper    = "--";
						   } 						 
						$ls_nit       = $io_report->DS->data["nit"][$li_i];
						$ls_consol    = $io_report->DS->data["consol"][$li_i];
						$ls_numdoc    = $io_report->DS->data["numdoc"][$li_i];
						$ls_numref    = $io_report->DS->data["numref"][$li_i];
						$ld_fecemidoc = $io_funciones->uf_convertirfecmostrar($io_report->DS->data["fecemidoc"][$li_i]);
						$ld_fecregdoc = $io_funciones->uf_convertirfecmostrar($io_report->DS->data["fecregdoc"][$li_i]);
						$li_montotdoc = $io_report->DS->data["montotdoc"][$li_i];  
						$li_monobjret = $io_report->DS->data["monobjret"][$li_i];    
						$li_retenido  = $io_report->DS->data["retenido"][$li_i];  
						$ld_montotpag += $li_montotdoc;
						$ld_totbasimp += $li_monobjret;
						$ld_totmonret += $li_retenido;
						$li_montotdoc = number_format($li_montotdoc,2,',','.');  
						$li_monobjret = number_format($li_monobjret,2,',','.');    
						$li_retenido  = number_format($li_retenido,2,',','.');
						$li_porcentaje= number_format($io_report->DS->data["porcentaje"][$li_i],2,',','.');
										
						if($ls_codigo!=$ls_codigoant)
						{
							if($li_z>=1)
							{
								$io_pdf->ezNewPage();  
							}
							uf_print_encabezado($ls_agente,$ls_nombre,$ls_rifageret,$ls_rif,$ls_nit,$ls_telefono,$ls_dirageret,$ls_direccion,$ls_tipper,$io_pdf);
							$ls_codigoant=$ls_codigo;
						}
						uf_print_detalle($ls_numdoc,$ls_consol,$ld_fecregdoc,$ld_fecemidoc,$li_montotdoc,$li_monobjret,$li_retenido,$li_porcentaje,$ls_numref,$io_pdf);
					}
				}	
			  uf_print_totales($ld_montotpag,$ld_totbasimp,$ld_totmonret,$io_pdf);
			}			
			if($lb_valido) // Si no ocurrio ningún error
			{
				$io_pdf->ezStopPageNumbers(1,1); // Detenemos la impresión de los números de página
				$io_pdf->ezStream(); // Mostramos el reporte
			}
			else  // Si hubo algún error
			{
				print("<script language=JavaScript>");
			//	print(" alert('Ocurrio un error al generar el reporte. Intente de Nuevo');"); 
			//	print(" close();");
				print("</script>");		
			}
			unset($io_pdf);
		}
	}
	unset($io_report);
	unset($io_funciones);
	unset($io_fun_cxp);
?> 