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

	function uf_print_encabezado_pagina($as_numcmp,$as_feccmp,$as_codbenpre,$as_nombenpre,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   as_cmpmov // numero de comprobante de movimiento
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
		$li_tm=$io_pdf->getTextWidth(11,"RETORNO DE ACTIVOS");
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,730,12,"<b>RETORNO DE ACTIVOS</b>"); // Agregar el título
		$io_pdf->addText(423,735,11,"<b>No.:</b>");      // Agregar texto
		$io_pdf->addText(456,735,11,$as_numcmp); // Agregar Numero de la solicitud
		$io_pdf->addText(423,715,10,"<b>Fecha:</b>"); // Agregar texto
		$io_pdf->addText(456,715,10,$as_feccmp); // Agregar la Fecha
		$io_pdf->addText(510,759,8,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(516,752,7,date("h:i a")); // Agregar la Hora

		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
		
		$la_data[1] = array('codbenpre'=>'<b>Cédula/Código</b>','nombenpre'=>'<b>Nombre</b>');
		$la_data[2] = array('codbenpre'=>$as_codbenpre,'nombenpre'=>$as_nombenpre);
		$la_columna = array('codbenpre'=>'','nombenpre'=>'');
		$la_config  = array('showHeadings'=>0, // Mostrar encabezados
						    'showLines'=>2, // Mostrar Líneas
						    'shaded'=>0, // Sombra entre líneas
						    'xOrientation'=>'center', // Orientación de la tabla
						    'width'=>500, // Ancho de la tabla
						    'maxWidth'=>500,
						    'cols'=>array('codbenpre'=>array('justification'=>'center','width'=>110),
							              'nombenpre'=>array('justification'=>'left','width'=>390))); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'<b>Beneficiario del Préstamo</b>',$la_config);
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------

	function uf_print_detalles($as_codact,$as_denact,$as_ideact,&$io_pdf)
	{
		$io_pdf->ezSetDy(-20);
		$la_data[1] = array('codact'=>'<b>Activo</b>','denact'=>'<b>Denominación</b>','ideact'=>'<b>Identificador</b>');
		$la_data[2] = array('codact'=>$as_codact,'denact'=>$as_denact,'ideact'=>$as_ideact);
		$la_columna = array('codact'=>'','denact'=>'','ideact'=>'');
		$la_config  = array('showHeadings'=>0, // Mostrar encabezados
						    'showLines'=>2, // Mostrar Líneas
						    'shaded'=>0, // Sombra entre líneas
						    'xOrientation'=>'center', // Orientación de la tabla
						    'width'=>500, // Ancho de la tabla
						    'maxWidth'=>500,
						    'cols'=>array('codact'=>array('justification'=>'center','width'=>110),
							              'denact'=>array('justification'=>'left','width'=>280),
										  'ideact'=>array('justification'=>'center','width'=>110))); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'<b>Detalle Activo(s) Retornado(s)</b>',$la_config);
	}

	require_once("sigesp_saf_class_report.php");
	require_once("../../shared/ezpdf/class.ezpdf.php");	
	require_once("../../shared/class_folder/sigesp_include.php");
	require_once("../../shared/class_folder/class_sql.php");	
	require_once("../../shared/class_folder/class_funciones.php");
	
	$io_include = new sigesp_include();
	$ls_conect  = $io_include->uf_conectar();
	$io_sql     = new class_sql($ls_conect);	
	$io_report  = new sigesp_saf_class_report($ls_conect);
	$io_funcion = new class_funciones();
    
	 $ls_codact = $_GET["actcod"];
	 $la_codact = split('-',$ls_codact);
	 $ls_ideact = $_GET["actide"];
	 $la_ideact = split('-',$ls_ideact);
	 $ls_numcmp = $_GET["numcmp"];
	 $ls_feccmp = $_GET["feccmp"];
	 $ls_tipcmp = $_GET["tipcmp"];
	 
	 $ls_codbenpre = $_GET["codbenpre"];
	 $ls_nombenpre = $_GET["nombenpre"];
	 if (empty($ls_codact) || empty($ls_ideact))
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
		   $io_pdf = new Cezpdf('LETTER','portrait'); // Instancia de la clase PDF
		   $io_pdf->selectFont('../../shared/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
		   $io_pdf->ezSetCmMargins(5,3,3,3); // Configuración de los margenes en centímetros
		   $io_pdf->ezStartPageNumbers(550,30,10,'','',1); // Insertar el número de página
		   $li_count = 0; 
		   uf_print_encabezado_pagina($ls_numcmp,$ls_feccmp,$ls_codbenpre,$ls_nombenpre,$io_pdf);
		   $li_count = count($la_codact);
		   if ($li_count>0)
		      {
				for ($li_i=0;$li_i<$li_count;$li_i++)
				    {
				      $ls_codact = $la_codact[$li_i];
					  $ls_ideact = $la_ideact[$li_i];
					  $ls_denact = $io_report->uf_load_nombre_activo($ls_codact);
				 	  $la_data   = array('codact'=>$ls_codact,'denact'=>$ls_denact,'ideact'=>$ls_ideact);
					}
			    uf_print_detalles($ls_codact,$ls_denact,$ls_ideact,$io_pdf);  
			  }
		   $io_pdf->setStrokeColor(0,0,0);
		   $io_pdf->line(20,50,580,50);
		   $io_pdf->ezStopPageNumbers(1,1);
		   $io_pdf->ezStream();
		} 
?>