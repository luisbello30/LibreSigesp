<?php
    session_start();   
	header("Pragma: public");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private",false);
	if (!array_key_exists("la_logusr",$_SESSION))
	   {
		 print "<script language=JavaScript>";
		 print "opener.document.form1.submit();";
		 print "close();";
		 print "</script>";		
	   }
	ini_set('memory_limit','2048M');
	ini_set('max_execution_time ','0');		//--------------------------------------------------------------------------------------------------------------------------------
	
	function uf_insert_seguridad()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_insert_seguridad
		//		   Access: private 
		//	    Arguments: as_titulo // Título del reporte
		//    Description: función que guarda la seguridad de quien generó el reporte
		//	   Creado Por: Ing. Néstor Falcón.
		// Fecha Creación: 06/07/2009
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_saf;
		
		$ls_descripcion = "Generó el Reporte de Formato de salida de Entrega de Unidad.";
		$lb_valido		= $io_fun_saf->uf_load_seguridad_reporte("SAF","sigesp_saf_p_entre_unidad.php",$ls_descripcion);
		return $lb_valido;
	}
	
	
	function uf_print_encabezado_pagina($as_cmpent,$as_fecentuni,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   as_cmpent // numero de comprobante de movimiento
		//	    		   ad_fecha // Fecha 
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Néstor Falcón.
		// Fecha Creación: 06/07/2009. 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->setStrokeColor(0,0,0);
		$io_pdf->rectangle(420,710,130,40);
		$io_pdf->line(420,730,550 ,730);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],50,710,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(11,"ENTREGA DE UNIDAD");
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,730,11,"<b>ENTREGA DE UNIDAD</b>"); // Agregar el título
		$io_pdf->addText(423,735,11,"<b>No.:</b>");      // Agregar texto
		$io_pdf->addText(456,735,11,$as_cmpent); // Agregar Numero de la solicitud
		$io_pdf->addText(423,715,10,"<b>Fecha:</b>"); // Agregar texto
		$io_pdf->addText(456,715,10,$as_fecentuni); // Agregar la Fecha
		$io_pdf->addText(510,759,8,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(516,752,7,date("h:i a")); // Agregar la Hora

		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------

	function uf_print_detalles($as_coduniadm,$as_denuniadm,$as_codres,$as_nomres,$as_codnewres,$as_nomnewres,$as_obsentuni,&$io_pdf)
	{
		$la_data[1] = array('coduniadm'=>'<b>Código</b>','denuniadm'=>'<b>Denominación</b>');
		$la_data[2] = array('coduniadm'=>$as_coduniadm,'denuniadm'=>$as_denuniadm);
		$la_columna = array('coduniadm'=>'','denuniadm'=>'');
		$la_config  = array('showHeadings'=>0, // Mostrar encabezados
						    'showLines'=>2, // Mostrar Líneas
						    'shaded'=>0, // Sombra entre líneas
						    'xOrientation'=>'center', // Orientación de la tabla
						    'width'=>500, // Ancho de la tabla
						    'maxWidth'=>500,
						    'cols'=>array('coduniadm'=>array('justification'=>'center','width'=>150),
							              'denuniadm'=>array('justification'=>'left','width'=>350))); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'<b>Datos de la Unidad</b>',$la_config);

		$io_pdf->ezSetDy(-20);
		$la_data[1] = array('codresant'=>'<b>Responsable Anterior</b>','nomresant'=>'','codnewres'=>'<b>Nuevo Responsable</b>','nomnewres'=>'');
		$la_data[2] = array('codresant'=>$as_codres,'nomresant'=>$as_nomres,'codnewres'=>$as_codnewres,'nomnewres'=>$as_nomnewres);
		$la_columna = array('codresant'=>'','nomresant'=>'','codnewres'=>'','nomnewres'=>'');
		$la_config  = array('showHeadings'=>0, // Mostrar encabezados
						    'showLines'=>2, // Mostrar Líneas
							'fontSize'=>8, // Tamaño de Letras
						    'colGap'=>0,
							'shaded'=>0, // Sombra entre líneas
						    'xOrientation'=>'center', // Orientación de la tabla
						    'width'=>500, // Ancho de la tabla
						    'maxWidth'=>500,
						    'cols'=>array('codresant'=>array('justification'=>'center','width'=>90),
							              'nomresant'=>array('justification'=>'left','width'=>175),
										  'codnewres'=>array('justification'=>'center','width'=>90),
										  'nomnewres'=>array('justification'=>'center','width'=>145))); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'<b>Datos Responsables</b>',$la_config);
	    unset($la_data,$la_columna,$la_config);
	 
		$io_pdf->ezSetDy(-20);
		$la_data[1] = array('obsentuni'=>'<b>OBSERVACIÓN:</b> '.$as_obsentuni);
		$la_columna = array('obsentuni'=>'');
		$la_config  = array('showHeadings'=>0, // Mostrar encabezados
						    'showLines'=>2, // Mostrar Líneas
							'fontSize'=>8, // Tamaño de Letras
						    'colGap'=>0,
							'shaded'=>0, // Sombra entre líneas
						    'xOrientation'=>'center', // Orientación de la tabla
						    'width'=>500, // Ancho de la tabla
						    'maxWidth'=>500,
						    'cols'=>array('obsentuni'=>array('justification'=>'left','width'=>500))); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}
    
	require_once("sigesp_saf_class_report.php");
	require_once("../class_funciones_activos.php");	
	require_once("../../shared/ezpdf/class.ezpdf.php");	
	require_once("../../shared/class_folder/sigesp_include.php");
	require_once("../../shared/class_folder/class_sql.php");	
	require_once("../../shared/class_folder/class_funciones.php");
	
	$io_include = new sigesp_include();
	$ls_conect  = $io_include->uf_conectar();
	$io_sql     = new class_sql($ls_conect);	
	$io_report  = new sigesp_saf_class_report($ls_conect);
	$io_funcion = new class_funciones();
	$io_fun_saf	= new class_funciones_activos();
	
	$ls_codemp = $_SESSION["la_empresa"]["codemp"];
	//$lb_valido = uf_insert_seguridad(); // Seguridad de Reporte
	$lb_valido = true;
	if ($lb_valido)
	   {
		 $ls_cmpent = $_GET["cmpent"];
		 $rs_data   = $io_report->uf_load_entrega_unidad($ls_cmpent,&$lb_valido);
		 if (!$lb_valido)
		    {
			  print("<script language=JavaScript>");
			  print(" alert('No hay nada que Reportar');"); 
			  print(" close();");
			  print("</script>");
		    }
	 	 else
		    {
	          if ($row=$io_sql->fetch_row($rs_data))
		         {
				   error_reporting(E_ALL);
				   set_time_limit(1800);
				   $io_pdf = new Cezpdf('LETTER','portrait'); // Instancia de la clase PDF
				   $io_pdf->selectFont('../../shared/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
				   $io_pdf->ezSetCmMargins(5,3,3,3); // Configuración de los margenes en centímetros
				   $io_pdf->ezStartPageNumbers(550,30,10,'','',1); // Insertar el número de página
				   $ls_cmpent 	 = $rs_data->fields["cmpent"];
				   $ls_fecentuni = $io_funcion->uf_convertirfecmostrar($rs_data->fields["fecentuni"]);
				   $ls_codusureg = $rs_data->fields["codusureg"];
				   $ls_coduniadm = $rs_data->fields["coduniadm"];
				   $ls_denuniadm = $rs_data->fields["denuniadm"];
				   $ls_codres 	 = $rs_data->fields["codres"];
				   $ls_obsentuni = $rs_data->fields["obsentuni"];
				   $ls_codnewres = $rs_data->fields["codresnew"];
				   $ls_nomres    = $rs_data->fields["nomres1"];
				   if (empty($ls_nomres))
					  {
						$ls_nomres = $rs_data->fields["nomres2"];
					  }
				   $ls_nomnewres = $rs_data->fields["nomresnew1"];
				   if (empty($ls_nomnewres))
					  {
						$ls_nomnewres = $rs_data->fields["nomresnew2"];
					  }
				   uf_print_encabezado_pagina($ls_cmpent,$ls_fecentuni,$io_pdf);
				   uf_print_detalles($ls_coduniadm,$ls_denuniadm,$ls_codres,$ls_nomres,$ls_codnewres,$ls_nomnewres,$ls_obsentuni,$io_pdf);
				   $io_pdf->setStrokeColor(0,0,0);
				   $io_pdf->line(20,50,580,50);
				   $io_pdf->ezStopPageNumbers(1,1);
				   $io_pdf->ezStream();
				 }
			  else
				 {
				   print("<script language=JavaScript>");
				   print(" alert('No hay nada que Reportar !!!');"); 
				   print(" close();");
				   print("</script>");
				 }
	        } 
	   }			   

?>