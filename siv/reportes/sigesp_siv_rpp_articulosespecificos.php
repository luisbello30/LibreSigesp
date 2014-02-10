<?php
	session_start();   
	header("Pragma: public");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private",false);
	if(!array_key_exists("la_logusr",$_SESSION))
	{
		print "<script language=JavaScript>";
		print "close();";
		print "</script>";		
	}
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,$as_fecha,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   as_desnom // Descripción de la nómina
		//	    		   as_fecha // Fecha 
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 26/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->line(20,40,578,40);
		$io_pdf->setStrokeColor(0,0,0);
		//$io_pdf->rectangle(200,710,350,40);
		//$io_pdf->line(400,750,400,710);
		//$io_pdf->line(400,730,550 ,730);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],50,720,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,720,11,$as_titulo); // Agregar el título
		$li_tm=$io_pdf->getTextWidth(11,$as_fecha);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,780,10,$as_fecha); // Agregar el título
		$io_pdf->addText(510,750,8,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(516,743,7,date("h:i a")); // Agregar la Hora
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera($as_codart,$as_denart,$ai_exiart,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: private 
		//	    Arguments: as_codart    // codigo de articulo
		//	    		   as_denart // denominacion de articulo
		//	    		   ai_exiart // existencia
		//	    		   io_pdf       // total de registros que va a tener el reporte
		//    Description: función que imprime la cabecera de cada página
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 12/05/10 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_pdf->ezSetDy(-5);
		$la_data    = array(array('articulo'=>'<b>'.$as_denart.'</b>','existencia'=>'<b>Total Existencia:</b>','cantidad'=>number_format($ai_exiart,2,',','.')));
		$la_columna = array('articulo'=>'','existencia'=>'','cantidad'=>'');
		$la_config  = array('showHeadings'=>0, // Mostrar encabezados
							'fontSize' => 10,  // Tamaño de Letras
							'showLines'=>0,    // Mostrar Líneas
							'shaded'=>0,       // Sombra entre líneas
							'colGap'=>1,
							'width'=>530,
							'cols'=>array('articulo'=>array('justification'=>'left','width'=>250),
										  'existencia'=>array('justification'=>'right','width'=>120),
										  'cantidad'=>array('justification'=>'left','width'=>100))); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Objeto PDF
		//    Description: función que imprime el detalle
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 21/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_pdf->ezSetDy(-2.5);
		$la_datatit    = array(array('articulo'=>'Detalle:'));
		$la_columna = array('articulo'=>'');
		$la_config  = array('showHeadings'=>0, // Mostrar encabezados
							'fontSize' => 10,  // Tamaño de Letras
							'showLines'=>0,    // Mostrar Líneas
							'shaded'=>0,       // Sombra entre líneas
							'colGap'=>1,
							'width'=>530,
							'cols'=>array('articulo'=>array('justification'=>'left','width'=>450))); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_datatit,$la_columna,'',$la_config);


		$la_columna=array('codigo'=>'<b>Código</b>',
						  'articulo'=>'<b>Denominación</b>',
						  'existencia'=>'<b>Existencia</b>');
		$la_config  = array('showHeadings'=>1, // Mostrar encabezados
							'fontSize' => 10,  // Tamaño de Letras
							'showLines'=>1,    // Mostrar Líneas
							'shaded'=>0,       // Sombra entre líneas
							'colGap'=>1,
							'width'=>530,
						 'cols'=>array('codigo'=>array('justification'=>'center','width'=>115), // Justificación y ancho de la columna
						 			   'articulo'=>array('justification'=>'left','width'=>265), // Justificación y ancho de la columna
						 			   'existencia'=>array('justification'=>'right','width'=>70))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_totales($la_data,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_totales
		//		   Access: private 
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime el detalle por personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 06/07/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$la_columna=array('total'=>'',
						  'sueldointegral'=>'',
						  'bonovacacional'=>'',
						  'bonofin'=>'',
						  'aporte'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 11,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('total'=>array('justification'=>'right','width'=>220), // Justificación y ancho de la columna
						 			   'sueldointegral'=>array('justification'=>'right','width'=>70), // Justificación y ancho de la columna
						 			   'bonovacacional'=>array('justification'=>'right','width'=>70), // Justificación y ancho de la columna
						 			   'bonofin'=>array('justification'=>'right','width'=>70), // Justificación y ancho de la columna
						 			   'aporte'=>array('justification'=>'right','width'=>70))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_totales
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_pie_cabecera($ai_totprenom,$ai_totant,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_pie_cabecera
		//		   Access: private 
		//	    Arguments: ai_totprenom // Total Prenómina
		//	   			   ai_totant // Total Anterior
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime el fin de la cabecera de cada página
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 26/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$la_data=array(array('name'=>''));
		//$la_data=array(array('name'=>'_________________________________________________________________________________________'));
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 10, // Tamaño de Letras
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'xOrientation'=>'center', // Orientación de la tabla
						 'width'=>510); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		$la_data=array(array('total'=>''));
		$la_columna=array('total'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>510, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				 		 'cols'=>array('total'=>array('justification'=>'right','width'=>300), // Justificación y ancho de la columna
						 			   'prenomina'=>array('justification'=>'right','width'=>100), // Justificación y ancho de la columna
						 			   'anterior'=>array('justification'=>'right','width'=>100))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		$la_data=array(array('name'=>''));
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>510, // Ancho Máximo de la tabla
						 'xOrientation'=>'center'); // Orientación de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_pie_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------


	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------
	require_once("../../shared/ezpdf/class.ezpdf.php");
	require_once("sigesp_siv_class_report.php");
	$io_report=new sigesp_siv_class_report();
	require_once("../../shared/class_folder/class_funciones.php");
	$io_funciones=new class_funciones();				
	require_once("../class_funciones_inventario.php");
	$io_fun_inventario=new class_funciones_inventario();
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ls_fecrec=$io_fun_inventario->uf_obtenervalor_get("fecrec","");

	$ls_titulo="<b> Listado Detallado de Artículos </b>";
	$ls_fecha="";
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_codemp=$_SESSION["la_empresa"]["codemp"];
	$ls_nomemp=$_SESSION["la_empresa"]["nombre"];
	$ls_codart="";
	$ls_articulo=$io_fun_inventario->uf_obtenervalor_get("codart","");
	$ls_existencia=$io_fun_inventario->uf_obtenervalor_get("existencia","");
	$li_ordenart=$io_fun_inventario->uf_obtenervalor_get("ordenart",0);
	//--------------------------------------------------------------------------------------------------------------------------------
	$lb_valido=$io_report->uf_select_articulosespecificos($ls_articulo,$ls_existencia,$li_ordenart); // Cargar el DS con los datos de la cabecera del reporte
	if($lb_valido==false) // Existe algún error ó no hay registros
	{
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');"); 
		print(" close();");
		print("</script>");
	}
	else // Imprimimos el reporte
	{
		/////////////////////////////////         SEGURIDAD               ////////////////////////////////////////////////////////////////////
		$ls_desc_event="Generó el reporte de Listado Detallado de Artículos";
		$io_fun_inventario->uf_load_seguridad_reporte("SIV","sigesp_siv_r_articulosespecificos.php",$ls_desc_event);
		////////////////////////////////         SEGURIDAD               ////////////////////////////////////////////////////////////////////
		error_reporting(E_ALL);
		set_time_limit(1800);
		$io_pdf=new Cezpdf('LETTER','portrait'); // Instancia de la clase PDF
		$io_pdf->selectFont('../../shared/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
		$io_pdf->ezSetCmMargins(3.5,3,3,3); // Configuración de los margenes en centímetros
		uf_print_encabezado_pagina($ls_titulo,$ls_fecha,$io_pdf); // Imprimimos el encabezado de la página
		$io_pdf->ezStartPageNumbers(550,50,10,'','',1); // Insertar el número de página
		while (!$io_report->rs_data->EOF)
		{
			$li_s=0;
			$la_data="";
			$ls_codart= $io_report->rs_data->fields["codart"];
			$ls_denart= $io_report->rs_data->fields["denart"];
			$li_exiart= $io_report->rs_data->fields["existencia"];
			$li_adicionales=$io_report->uf_select_articulosrelacionados($ls_codart);
			$li_exiart=$li_exiart+$li_adicionales;
			if(($ls_existencia==1)&&($li_exiart>0))
			{
				$lb_valido=$io_report->uf_select_detallearticulo($ls_codart); // Obtenemos el detalle del reporte
				while (!$io_report->rs_detalle->EOF)
				{
					$ls_codartdet= $io_report->rs_detalle->fields["codart"];
					$ls_denartdet= $io_report->rs_detalle->fields["denart"];
					$li_existencia= $io_report->rs_detalle->fields["existencia"];
					if($li_existencia>0)
					{
						$li_existencia=number_format($li_existencia,2,',','.');
						$la_data[$li_s]=array('codigo'=>$ls_codartdet,'articulo'=>$ls_denartdet,'existencia'=>$li_existencia);
						$li_s++;
					}
					$io_report->rs_detalle->MoveNext();
				}	
				if($la_data!="")
				{		
					uf_print_cabecera($ls_codart,$ls_denart,$li_exiart,$io_pdf); // Imprimimos la cabecera del registro
					uf_print_detalle($la_data,$io_pdf); // Imprimimos el detalle 
				}
				unset($io_report->rs_detalle);
				unset($la_data);
			}
			else
			{
				if($ls_existencia==0)
				{
					$lb_valido=$io_report->uf_select_detallearticulo($ls_codart); // Obtenemos el detalle del reporte
					while (!$io_report->rs_detalle->EOF)
					{
						$ls_codartdet= $io_report->rs_detalle->fields["codart"];
						$ls_denartdet= $io_report->rs_detalle->fields["denart"];
						$li_existencia= $io_report->rs_detalle->fields["existencia"];
						$li_existencia=number_format($li_existencia,2,',','.');
						$la_data[$li_s]=array('codigo'=>$ls_codartdet,'articulo'=>$ls_denartdet,'existencia'=>$li_existencia);
						$li_s++;
						$io_report->rs_detalle->MoveNext();
					}	
					if($la_data!="")
					{		
						uf_print_cabecera($ls_codart,$ls_denart,$li_exiart,$io_pdf); // Imprimimos la cabecera del registro
						uf_print_detalle($la_data,$io_pdf); // Imprimimos el detalle 
					}
					unset($io_report->rs_detalle);
					unset($la_data);
				}
			}
			$io_report->rs_data->MoveNext();
		}
		if($lb_valido) // Si no ocurrio ningún error
		{
			$io_pdf->ezStopPageNumbers(1,1); // Detenemos la impresión de los números de página
			$io_pdf->ezStream(); // Mostramos el reporte
		}
		else // Si hubo algún error
		{
			print("<script language=JavaScript>");
			print(" alert('Ocurrio un error al generar el reporte. Intente de Nuevo');"); 
			print(" close();");
			print("</script>");		
		}
		unset($io_pdf);
	}
	unset($io_report);
	unset($io_funciones);
?> 