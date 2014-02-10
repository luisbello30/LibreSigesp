<?php

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//    REPORTE: Listado de Evaluaciones por Meta
//  ORGANISMO: IPSFA
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//-----------------------------------------------------------------------------------------------------------------------------------
///Elaborado por: Ing. Gusmary Balza
//-----------------------------------------------------------------------------------------------------------------------------------
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
		// Fecha Creación: 11/03/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_srh;
		
		$ls_descripcion="Generó el Reporte ".$as_titulo;
		$lb_valido=$io_fun_srh->uf_load_seguridad_reporte("SRH","sigesp_cxp_r_listadoevaluacionpsicologica.php",$ls_descripcion);
		return $lb_valido;
	}
	
//-----------------------------------------------------------------------------------------------------------------------------------	
	
		function uf_print_encabezado_pagina($as_titulo,$as_titulo_2,$as_titulo_3,$io_pdf)
	    {
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezado_pagina
		//		   Access: private 
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: Función que imprime los encabezados por página
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 11/03/2007
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],40,705,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		
		
		$io_pdf->addText(570,755,7,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(576,745,6,date("h:i a")); // Agregar la Hora
		$io_pdf->addText(30,648,9,"<b>Participante:</b>");
		$io_pdf->addText(330,648,9,"<b>Fecha:</b>");
		$io_pdf->addText(95,665,9,"__________________________________________");
		$io_pdf->addText(367,665,9,"_______________________");
		$io_pdf->addText(367,647,9,"_________________________");
		$io_pdf->addText(30,60,7,"(*) Estudios cursados no mayor a tres años proximo pasado a la fecha de concurso");
		$io_pdf->addText(30,50,7,"(**) Relacionados al cargo vacante");
		$io_pdf->setStrokeColor(0,0,0);
		$io_pdf->line(585,625,585,99); //VERTICAL
		$io_pdf->line(41,124.5,585,124.5);//HORIZONTAL
		$io_pdf->line(41,99,585,99);//HORIZONTAL
		$io_pdf->line(41,111,585,111);//HORIZONTAL
		$io_pdf->line(41,178,585,178);//HORIZONTAL
		$io_pdf->line(41,230.5,585,230.5);//HORIZONTAL
		$io_pdf->line(41,284,585,284);//HORIZONTAL
		$io_pdf->line(41,336.5,585,336.5);//HORIZONTAL
		$io_pdf->line(41,415.5,585,415.5);//HORIZONTAL
		$io_pdf->line(41,496,585,496);//HORIZONTAL
		$io_pdf->line(41,575,585,575);//HORIZONTAL
		$io_pdf->line(41,602,585,602);//HORIZONTAL

		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
		
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();		
	
	    $io_pdf->ezSetY(755);
	    
		$la_data[1]=array('titulo1'=>'<b>'.$as_titulo.'</b>');
		$la_data[2]=array('titulo1'=>'<b>'.$as_titulo_2.'</b>');
		$la_data[3]=array('titulo1'=>'<b>'.$as_titulo_3.'</b>');
					
		$la_columnas=array('titulo1'=>'');
					
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 14,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				      	 'cols'=>array('titulo1'=>array('justification'=>'center','width'=>670))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
        unset($la_data);
		unset($la_columnas);
		unset($la_config);		
	
		$io_pdf->restoreState();
	    $io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezado_pagina	
	
//---------------------------------------------------------------------------------------------------------------------------------//
  function uf_print_encabezado_detalle($la_data,&$io_pdf)
	 {
		
	   $io_pdf->ezSetY(680);
	   $la_data_t[1]=array(  'ascenso'=>'<b>Dependencia :</b>', 
		                     'caract'=>'<b>Cargo :</b>',
							 'fecha'=>'<b>Analista Responsable</b>');
							 
		$la_columnas=array(  'ascenso'=>'', 
		                     'caract'=>'',
							 'fecha'=>'');
							 
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xPos'=>310, // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('ascenso'=>array('justification'=>'left','width'=>230),
									   'caract'=>array('justification'=>'center','width'=>180),
									   'fecha'=>array('justification'=>'right','width'=>150))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data_t,$la_columnas,'',$la_config);
	
		
	    $io_pdf->ezSetY(680);
		$la_columnas=array(	 'desuniadm'=>'',
		                     'caract'=>'');
							 
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 10,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xPos'=>330, // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('desuniadm'=>array('justification'=>'left','width'=>200), // Justificación y ancho de la columna
									   'caract'=>array('justification'=>'center','width'=>270)));
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		
		 $io_pdf->ezSetY(660);
		$la_columnas=array(	 'nombre'=>'',		                    
		                     'fecha'=>'');
							 
							 
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 10,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xPos'=>315, // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('nombre'=>array('justification'=>'left','width'=>300),									   
									   'fecha'=>array('justification'=>'left','width'=>150)));
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);	
		
	    unset($la_data);
		unset($la_columnas);
		unset($la_config);	
				
		}
//---------------------------------------------------------------------------------------------------------------------------------//
	function uf_print_detalle($la_data,$as_fecha_eval,$aa_puntos,&$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: la_data // arreglo de información
		//				   as_titcuentas // titulo de estructura presupuestaria
		//				   ai_i // total de registros
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: Función que imprime el detalle del reporte
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 10/06/2007 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	    
		$io_pdf->ezSetY(620);
		
		/*$la_data_fecha[1]=array('fecha'=>'Fecha de la Evaluación: '.$as_fecha_eval);
		$la_columnas=array('fecha'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 10, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xPos'=>360, // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('fecha'=>array('justification'=>'left','width'=>670))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data_fecha,$la_columnas,'',$la_config);*/
		$io_pdf->ezSetY(630);
		$la_data_titulo[1]=array('tit1'=>'<b>TOTAL</b>');
		$la_columnas=array('tit1'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>2, // Mostrar Líneas
						 'shaded'=>2, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xPos'=>555, // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('tit1'=>array('justification'=>'center','width'=>70))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data_titulo,$la_columnas,'',$la_config);
		
		$la_data_titulo[1]=array('tit1'=>'<b>1   Evaluación de Requisitos Mínimos</b>',
						         'tit2'=>'<b>Puntaje            20</b>',
						         'tit3'=>'<b>Puntaje Asignado</b>');
		$la_columnas=array('tit1'=>'',
						   'tit2'=>'',
						   'tit3'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>2, // Mostrar Líneas
						 'shaded'=>2, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xPos'=>275, // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('tit1'=>array('justification'=>'left','width'=>280), // Justificación y ancho de la columna
									   'tit2'=>array('justification'=>'center','width'=>105),
						 			   'tit3'=>array('justification'=>'center','width'=>105))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data_titulo,$la_columnas,'',$la_config);
		
		$la_data_titulo[1]=array('tit1'=>'<b>2   Capacitación y Desarrollo (Adicional)(*)</b>',
						         'tit2'=>'<b>Puntaje Máximo 15</b>',
						         'tit3'=>''.$aa_puntos[1]);
		$la_columnas=array('tit1'=>'',
						   'tit2'=>'',
						   'tit3'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>2, // Mostrar Líneas
						 'shaded'=>2, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xPos'=>275, // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('tit1'=>array('justification'=>'left','width'=>280), // Justificación y ancho de la columna
									   'tit2'=>array('justification'=>'center','width'=>105),
						 			   'tit3'=>array('justification'=>'center','width'=>105))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data_titulo,$la_columnas,'',$la_config);
		
		$la_data_titulo[1]=array('tit1'=>'<b>      2.1   Formal (No Acumulativo)</b>',
						         'tit2'=>'<b>Puntaje Máximo 10</b>',
						         'tit3'=>''.$aa_puntos[2]);
		$la_columnas=array('tit1'=>'',
						   'tit2'=>'',
						   'tit3'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>2, // Mostrar Líneas
						 'shaded'=>2, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xPos'=>275, // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('tit1'=>array('justification'=>'left','width'=>280), // Justificación y ancho de la columna
									   'tit2'=>array('justification'=>'center','width'=>105),
						 			   'tit3'=>array('justification'=>'center','width'=>105))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data_titulo,$la_columnas,'',$la_config);
		
		$la_data_titulo[1]=array('tit1'=>'   Profesional Universitario con Doctorado',
						         'tit2'=>'10',
						         'tit3'=>''.$aa_puntos[3]);
		$la_columnas=array('tit1'=>'',
						   'tit2'=>'',
						   'tit3'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xPos'=>275, // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('tit1'=>array('justification'=>'left','width'=>280), // Justificación y ancho de la columna
									   'tit2'=>array('justification'=>'center','width'=>105),
						 			   'tit3'=>array('justification'=>'center','width'=>105))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data_titulo,$la_columnas,'',$la_config);
		
		$la_data_titulo[1]=array('tit1'=>'   Profesional Universitario con Maestria',
						         'tit2'=>'8',
						         'tit3'=>''.$aa_puntos[4]);
		$la_columnas=array('tit1'=>'',
						   'tit2'=>'',
						   'tit3'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xPos'=>275, // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('tit1'=>array('justification'=>'left','width'=>280), // Justificación y ancho de la columna
									   'tit2'=>array('justification'=>'center','width'=>105),
						 			   'tit3'=>array('justification'=>'center','width'=>105))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data_titulo,$la_columnas,'',$la_config);
		
		$la_data_titulo[1]=array('tit1'=>'   Profesional Universitario con Especialización',
						         'tit2'=>'6',
						         'tit3'=>''.$aa_puntos[5]);
		$la_columnas=array('tit1'=>'',
						   'tit2'=>'',
						   'tit3'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xPos'=>275, // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('tit1'=>array('justification'=>'left','width'=>280), // Justificación y ancho de la columna
									   'tit2'=>array('justification'=>'center','width'=>105),
						 			   'tit3'=>array('justification'=>'center','width'=>105))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data_titulo,$la_columnas,'',$la_config);
		
		$la_data_titulo[1]=array('tit1'=>'   Pre Grado aprobado',
						         'tit2'=>'4',
						         'tit3'=>''.$aa_puntos[6]);
		$la_columnas=array('tit1'=>'',
						   'tit2'=>'',
						   'tit3'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xPos'=>275, // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('tit1'=>array('justification'=>'left','width'=>280), // Justificación y ancho de la columna
									   'tit2'=>array('justification'=>'center','width'=>105),
						 			   'tit3'=>array('justification'=>'center','width'=>105))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data_titulo,$la_columnas,'',$la_config);
		
		$la_data_titulo[1]=array('tit1'=>'   T.S.U con Estudios de Especialización Aprobado',
						         'tit2'=>'3',
						         'tit3'=>''.$aa_puntos[7]);
		$la_columnas=array('tit1'=>'',
						   'tit2'=>'',
						   'tit3'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xPos'=>275, // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('tit1'=>array('justification'=>'left','width'=>280), // Justificación y ancho de la columna
									   'tit2'=>array('justification'=>'center','width'=>105),
						 			   'tit3'=>array('justification'=>'center','width'=>105))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data_titulo,$la_columnas,'',$la_config);
		
		$la_data_titulo[1]=array('tit1'=>'   T.S.U con Estudios univ. optar a Lic. (1 Año Aprob)',
						         'tit2'=>'2',
						         'tit3'=>''.$aa_puntos[8]);
		$la_columnas=array('tit1'=>'',
						   'tit2'=>'',
						   'tit3'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xPos'=>275, // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('tit1'=>array('justification'=>'left','width'=>280), // Justificación y ancho de la columna
									   'tit2'=>array('justification'=>'center','width'=>105),
						 			   'tit3'=>array('justification'=>'center','width'=>105))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data_titulo,$la_columnas,'',$la_config);
		$li_total1=$aa_puntos[1]+$aa_puntos[2]+$aa_puntos[3]+$aa_puntos[4]+$aa_puntos[5]+$aa_puntos[6]+$aa_puntos[7]+$aa_puntos[8];
		$io_pdf->addText(548,535,9,$li_total1);
		$ls_especial=20;
		$io_pdf->addText(548,606,9,$ls_especial);
		
		
		$la_data_titulo[1]=array('tit1'=>'<b>      2.2   Informal (No Acumulativo)(*)</b>',
						         'tit2'=>'<b>Puntaje Máximo 05</b>',
						         'tit3'=>'<b>Puntaje Asignado</b>');
		$la_columnas=array('tit1'=>'',
						   'tit2'=>'',
						   'tit3'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>2, // Mostrar Líneas
						 'shaded'=>2, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xPos'=>275, // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('tit1'=>array('justification'=>'left','width'=>280), // Justificación y ancho de la columna
									   'tit2'=>array('justification'=>'center','width'=>105),
						 			   'tit3'=>array('justification'=>'center','width'=>105))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data_titulo,$la_columnas,'',$la_config);
		
		$la_data_titulo[1]=array('tit1'=>'   201 en adelante',
						         'tit2'=>'5',
						         'tit3'=>''.$aa_puntos[9]);
		$la_columnas=array('tit1'=>'',
						   'tit2'=>'',
						   'tit3'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xPos'=>275, // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('tit1'=>array('justification'=>'left','width'=>280), // Justificación y ancho de la columna
									   'tit2'=>array('justification'=>'center','width'=>105),
						 			   'tit3'=>array('justification'=>'center','width'=>105))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data_titulo,$la_columnas,'',$la_config);
		
		$la_data_titulo[1]=array('tit1'=>'   151 a 200 Horas',
						         'tit2'=>'4',
						         'tit3'=>''.$aa_puntos[10]);
		$la_columnas=array('tit1'=>'',
						   'tit2'=>'',
						   'tit3'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xPos'=>275, // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('tit1'=>array('justification'=>'left','width'=>280), // Justificación y ancho de la columna
									   'tit2'=>array('justification'=>'center','width'=>105),
						 			   'tit3'=>array('justification'=>'center','width'=>105))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data_titulo,$la_columnas,'',$la_config);
		
		$la_data_titulo[1]=array('tit1'=>'   101 a 150 Horas',
						         'tit2'=>'3',
						         'tit3'=>''.$aa_puntos[11]);
		$la_columnas=array('tit1'=>'',
						   'tit2'=>'',
						   'tit3'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xPos'=>275, // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('tit1'=>array('justification'=>'left','width'=>280), // Justificación y ancho de la columna
									   'tit2'=>array('justification'=>'center','width'=>105),
						 			   'tit3'=>array('justification'=>'center','width'=>105))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data_titulo,$la_columnas,'',$la_config);
		
		$la_data_titulo[1]=array('tit1'=>'   51 a 100 Horas',
						         'tit2'=>'2',
						         'tit3'=>''.$aa_puntos[12]);
		$la_columnas=array('tit1'=>'',
						   'tit2'=>'',
						   'tit3'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xPos'=>275, // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('tit1'=>array('justification'=>'left','width'=>280), // Justificación y ancho de la columna
									   'tit2'=>array('justification'=>'center','width'=>105),
						 			   'tit3'=>array('justification'=>'center','width'=>105))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data_titulo,$la_columnas,'',$la_config);
		
		$la_data_titulo[1]=array('tit1'=>'   08 a 50 Horas',
						         'tit2'=>'1',
						         'tit3'=>''.$aa_puntos[13]);
		$la_columnas=array('tit1'=>'',
						   'tit2'=>'',
						   'tit3'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xPos'=>275, // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('tit1'=>array('justification'=>'left','width'=>280), // Justificación y ancho de la columna
									   'tit2'=>array('justification'=>'center','width'=>105),
						 			   'tit3'=>array('justification'=>'center','width'=>105))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data_titulo,$la_columnas,'',$la_config);
		
		$li_total2=$aa_puntos[9]+$aa_puntos[10]+$aa_puntos[11]+$aa_puntos[12]+$aa_puntos[13];
		$io_pdf->addText(548,455,9,$li_total2);
		
		$la_data_titulo[1]=array('tit1'=>'<b>3   Experiencia Laboral (Acumulativo)</b>',
						         'tit2'=>'<b>Puntaje Máximo 15</b>',
						         'tit3'=>'<b>Puntaje Asignado</b>');
		$la_columnas=array('tit1'=>'',
						   'tit2'=>'',
						   'tit3'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>2, // Mostrar Líneas
						 'shaded'=>2, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xPos'=>275, // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('tit1'=>array('justification'=>'left','width'=>280), // Justificación y ancho de la columna
									   'tit2'=>array('justification'=>'center','width'=>105),
						 			   'tit3'=>array('justification'=>'center','width'=>105))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data_titulo,$la_columnas,'',$la_config);
		
		$la_data_titulo[1]=array('tit1'=>'<b>      3.1   En el área y en el instituto (IPSFA)</b>',
						         'tit2'=>'En el área',
						         'tit3'=>'En el IPSFA',
								 'tit4'=>'En el área',
						         'tit5'=>'En el IPSFA');
		$la_columnas=array('tit1'=>'',
						   'tit2'=>'',
						   'tit3'=>'',
						   'tit4'=>'',
						   'tit5'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>2, // Mostrar Líneas
						 'shaded'=>2, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xPos'=>275, // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('tit1'=>array('justification'=>'left','width'=>280), // Justificación y ancho de la columna
									   'tit2'=>array('justification'=>'center','width'=>50.5),
									   'tit3'=>array('justification'=>'center','width'=>54.5),
						 			   'tit4'=>array('justification'=>'center','width'=>50.5), // Justificación y ancho de la columna
									   'tit5'=>array('justification'=>'center','width'=>54.5))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data_titulo,$la_columnas,'',$la_config);
		
		$la_data_titulo[1]=array('tit1'=>'   De 15 años en adelante',
						         'tit2'=>'8',
						         'tit3'=>'4',
								 'tit4'=>''.$aa_puntos[14],
						         'tit5'=>''.$aa_puntos[15]);
		$la_columnas=array('tit1'=>'',
						   'tit2'=>'',
						   'tit3'=>'',
						   'tit4'=>'',
						   'tit5'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xPos'=>275, // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('tit1'=>array('justification'=>'left','width'=>280), // Justificación y ancho de la columna
									   'tit2'=>array('justification'=>'center','width'=>50.5),
									   'tit3'=>array('justification'=>'center','width'=>54.5),
						 			   'tit4'=>array('justification'=>'center','width'=>50.5), // Justificación y ancho de la columna
									   'tit5'=>array('justification'=>'center','width'=>54.5))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data_titulo,$la_columnas,'',$la_config);
		
		$la_data_titulo[1]=array('tit1'=>'   14 a 10 Años',
						         'tit2'=>'7',
						         'tit3'=>'3',
								 'tit4'=>''.$aa_puntos[16],
						         'tit5'=>''.$aa_puntos[17]);
		$la_columnas=array('tit1'=>'',
						   'tit2'=>'',
						   'tit3'=>'',
						   'tit4'=>'',
						   'tit5'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xPos'=>275, // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('tit1'=>array('justification'=>'left','width'=>280), // Justificación y ancho de la columna
									   'tit2'=>array('justification'=>'center','width'=>50.5),
									   'tit3'=>array('justification'=>'center','width'=>54.5),
						 			   'tit4'=>array('justification'=>'center','width'=>50.5), // Justificación y ancho de la columna
									   'tit5'=>array('justification'=>'center','width'=>54.5))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data_titulo,$la_columnas,'',$la_config);
		
		$la_data_titulo[1]=array('tit1'=>'   9 a 5 Años',
						         'tit2'=>'5',
						         'tit3'=>'2',
								 'tit4'=>''.$aa_puntos[18],
						         'tit5'=>''.$aa_puntos[19]);
		$la_columnas=array('tit1'=>'',
						   'tit2'=>'',
						   'tit3'=>'',
						   'tit4'=>'',
						   'tit5'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xPos'=>275, // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('tit1'=>array('justification'=>'left','width'=>280), // Justificación y ancho de la columna
									   'tit2'=>array('justification'=>'center','width'=>50.5),
									   'tit3'=>array('justification'=>'center','width'=>54.5),
						 			   'tit4'=>array('justification'=>'center','width'=>50.5), // Justificación y ancho de la columna
									   'tit5'=>array('justification'=>'center','width'=>54.5))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data_titulo,$la_columnas,'',$la_config);
		
		$la_data_titulo[1]=array('tit1'=>'   4 a 2 Años',
						         'tit2'=>'3',
						         'tit3'=>'1',
								 'tit4'=>''.$aa_puntos[20],
						         'tit5'=>''.$aa_puntos[21]);
		$la_columnas=array('tit1'=>'',
						   'tit2'=>'',
						   'tit3'=>'',
						   'tit4'=>'',
						   'tit5'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xPos'=>275, // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('tit1'=>array('justification'=>'left','width'=>280), // Justificación y ancho de la columna
									   'tit2'=>array('justification'=>'center','width'=>50.5),
									   'tit3'=>array('justification'=>'center','width'=>54.5),
						 			   'tit4'=>array('justification'=>'center','width'=>50.5), // Justificación y ancho de la columna
									   'tit5'=>array('justification'=>'center','width'=>54.5))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data_titulo,$la_columnas,'',$la_config);
		$li_total3=$aa_puntos[14]+$aa_puntos[15]+$aa_puntos[16]+$aa_puntos[17]+$aa_puntos[18]+$aa_puntos[19]+$aa_puntos[20]+$aa_puntos[21];
		$io_pdf->addText(547,375,9,$li_total3);
		
		
		$la_data_titulo[1]=array('tit1'=>'<b>      3.2   Experiencia en Funciones Supervisora</b>',
						         'tit2'=>'<b>Puntaje Máximo 06</b>',
						         'tit3'=>'');
		$la_columnas=array('tit1'=>'',
						   'tit2'=>'',
						   'tit3'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>2, // Mostrar Líneas
						 'shaded'=>2, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xPos'=>275, // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('tit1'=>array('justification'=>'left','width'=>280), // Justificación y ancho de la columna
									   'tit2'=>array('justification'=>'center','width'=>105),
						 			   'tit3'=>array('justification'=>'center','width'=>105))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data_titulo,$la_columnas,'',$la_config);
		
		$la_data_titulo[1]=array('tit1'=>'   3 años y un mes en adelante',
						         'tit2'=>'3',
						         'tit3'=>''.$aa_puntos[22]);
		$la_columnas=array('tit1'=>'',
						   'tit2'=>'',
						   'tit3'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xPos'=>275, // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('tit1'=>array('justification'=>'left','width'=>280), // Justificación y ancho de la columna
									   'tit2'=>array('justification'=>'center','width'=>105),
						 			   'tit3'=>array('justification'=>'center','width'=>105))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data_titulo,$la_columnas,'',$la_config);
		
		$la_data_titulo[1]=array('tit1'=>'   1 año y un mes a 3 años',
						         'tit2'=>'2',
						         'tit3'=>''.$aa_puntos[23]);
		$la_columnas=array('tit1'=>'',
						   'tit2'=>'',
						   'tit3'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xPos'=>275, // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('tit1'=>array('justification'=>'left','width'=>280), // Justificación y ancho de la columna
									   'tit2'=>array('justification'=>'center','width'=>105),
						 			   'tit3'=>array('justification'=>'center','width'=>105))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data_titulo,$la_columnas,'',$la_config);
		
		$la_data_titulo[1]=array('tit1'=>'   6 meses a 1 año',
						         'tit2'=>'1',
						         'tit3'=>''.$aa_puntos[24]);
		$la_columnas=array('tit1'=>'',
						   'tit2'=>'',
						   'tit3'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xPos'=>275, // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('tit1'=>array('justification'=>'left','width'=>280), // Justificación y ancho de la columna
									   'tit2'=>array('justification'=>'center','width'=>105),
						 			   'tit3'=>array('justification'=>'center','width'=>105))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data_titulo,$la_columnas,'',$la_config);
		$li_total4=$aa_puntos[22]+$aa_puntos[23]+$aa_puntos[24];
		$io_pdf->addText(548,305,9,$li_total4);
		
		$la_data_titulo[1]=array('tit1'=>'<b>4   Evaluaciones de Desempeño</b>',
						         'tit2'=>'<b>Puntaje Maximo           10</b>',
						         'tit3'=>'<b>Puntaje Asignado</b>');
		$la_columnas=array('tit1'=>'',
						   'tit2'=>'',
						   'tit3'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>2, // Mostrar Líneas
						 'shaded'=>2, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xPos'=>275, // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('tit1'=>array('justification'=>'left','width'=>280), // Justificación y ancho de la columna
									   'tit2'=>array('justification'=>'center','width'=>105),
						 			   'tit3'=>array('justification'=>'center','width'=>105))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data_titulo,$la_columnas,'',$la_config);
		
		$la_data_titulo[1]=array('tit1'=>'   Excepcional',
						         'tit2'=>'10',
						         'tit3'=>''.$aa_puntos[25]);
		$la_columnas=array('tit1'=>'',
						   'tit2'=>'',
						   'tit3'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xPos'=>275, // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('tit1'=>array('justification'=>'left','width'=>280), // Justificación y ancho de la columna
									   'tit2'=>array('justification'=>'center','width'=>105),
						 			   'tit3'=>array('justification'=>'center','width'=>105))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data_titulo,$la_columnas,'',$la_config);
		
		$la_data_titulo[1]=array('tit1'=>'   Sobre lo Esperado',
						         'tit2'=>'8',
						         'tit3'=>''.$aa_puntos[26]);
		$la_columnas=array('tit1'=>'',
						   'tit2'=>'',
						   'tit3'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xPos'=>275, // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('tit1'=>array('justification'=>'left','width'=>280), // Justificación y ancho de la columna
									   'tit2'=>array('justification'=>'center','width'=>105),
						 			   'tit3'=>array('justification'=>'center','width'=>105))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data_titulo,$la_columnas,'',$la_config);
		
		$la_data_titulo[1]=array('tit1'=>'   Dentro de lo esperado',
						         'tit2'=>'6',
						         'tit3'=>''.$aa_puntos[27]);
		$la_columnas=array('tit1'=>'',
						   'tit2'=>'',
						   'tit3'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xPos'=>275, // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('tit1'=>array('justification'=>'left','width'=>280), // Justificación y ancho de la columna
									   'tit2'=>array('justification'=>'center','width'=>105),
						 			   'tit3'=>array('justification'=>'center','width'=>105))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data_titulo,$la_columnas,'',$la_config);
		$li_total5=$aa_puntos[25]+$aa_puntos[26]+$aa_puntos[27];
		$io_pdf->addText(547,252,9,$li_total5);
				
		$la_data_titulo[1]=array('tit1'=>'<b>5   Evaluación Psicológica</b>',
						         'tit2'=>'<b>Puntaje Maximo          20</b>',
						         'tit3'=>'<b>Puntaje Asignado</b>');
		$la_columnas=array('tit1'=>'',
						   'tit2'=>'',
						   'tit3'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>2, // Mostrar Líneas
						 'shaded'=>2, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xPos'=>275, // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('tit1'=>array('justification'=>'left','width'=>280), // Justificación y ancho de la columna
									   'tit2'=>array('justification'=>'center','width'=>105),
						 			   'tit3'=>array('justification'=>'center','width'=>105))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data_titulo,$la_columnas,'',$la_config);
		
		$la_data_titulo[1]=array('tit1'=>'   Prueba Psicológicas',
						         'tit2'=>'10',
						         'tit3'=>''.$aa_puntos[28]);
		$la_columnas=array('tit1'=>'',
						   'tit2'=>'',
						   'tit3'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xPos'=>275, // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('tit1'=>array('justification'=>'left','width'=>280), // Justificación y ancho de la columna
									   'tit2'=>array('justification'=>'center','width'=>105),
						 			   'tit3'=>array('justification'=>'center','width'=>105))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data_titulo,$la_columnas,'',$la_config);
		
		$la_data_titulo[1]=array('tit1'=>'   Entrevista Psicológica',
						         'tit2'=>'10',
						         'tit3'=>''.$aa_puntos[29]);
		$la_columnas=array('tit1'=>'',
						   'tit2'=>'',
						   'tit3'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xPos'=>275, // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('tit1'=>array('justification'=>'left','width'=>280), // Justificación y ancho de la columna
									   'tit2'=>array('justification'=>'center','width'=>105),
						 			   'tit3'=>array('justification'=>'center','width'=>105))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data_titulo,$la_columnas,'',$la_config);
		$li_total6=$aa_puntos[28]+$aa_puntos[29];
		$io_pdf->addText(547,204,9,$li_total6);
		
		
		$la_data_titulo[1]=array('tit1'=>'<b>6   Entrevista con el Jefe de la Unidad(**)</b>',
						         'tit2'=>'<b>Puntaje Maximo          16</b>',
						         'tit3'=>'<b>Puntaje Asignado</b>');
		$la_columnas=array('tit1'=>'',
						   'tit2'=>'',
						   'tit3'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>2, // Mostrar Líneas
						 'shaded'=>2, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xPos'=>275, // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('tit1'=>array('justification'=>'left','width'=>280), // Justificación y ancho de la columna
									   'tit2'=>array('justification'=>'center','width'=>105),
						 			   'tit3'=>array('justification'=>'center','width'=>105))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data_titulo,$la_columnas,'',$la_config);
		
		$la_data_titulo[1]=array('tit1'=>'   Dominio del área de trabajo',
						         'tit2'=>'4',
						         'tit3'=>''.$aa_puntos[30]);
		$la_columnas=array('tit1'=>'',
						   'tit2'=>'',
						   'tit3'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xPos'=>275, // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('tit1'=>array('justification'=>'left','width'=>280), // Justificación y ancho de la columna
									   'tit2'=>array('justification'=>'center','width'=>105),
						 			   'tit3'=>array('justification'=>'center','width'=>105))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data_titulo,$la_columnas,'',$la_config);
		
		$la_data_titulo[1]=array('tit1'=>'   Logros Obtenidos',
						         'tit2'=>'4',
						         'tit3'=>''.$aa_puntos[31]);
		$la_columnas=array('tit1'=>'',
						   'tit2'=>'',
						   'tit3'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xPos'=>275, // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('tit1'=>array('justification'=>'left','width'=>280), // Justificación y ancho de la columna
									   'tit2'=>array('justification'=>'center','width'=>105),
						 			   'tit3'=>array('justification'=>'center','width'=>105))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data_titulo,$la_columnas,'',$la_config);
		
		$la_data_titulo[1]=array('tit1'=>'   Motivación, interes e iniciativa',
						         'tit2'=>'4',
						         'tit3'=>''.$aa_puntos[32]);
		$la_columnas=array('tit1'=>'',
						   'tit2'=>'',
						   'tit3'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xPos'=>275, // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('tit1'=>array('justification'=>'left','width'=>280), // Justificación y ancho de la columna
									   'tit2'=>array('justification'=>'center','width'=>105),
						 			   'tit3'=>array('justification'=>'center','width'=>105))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data_titulo,$la_columnas,'',$la_config);
		
		$la_data_titulo[1]=array('tit1'=>'   Habilidades potenciales',
						         'tit2'=>'4',
						         'tit3'=>''.$aa_puntos[33]);
		$la_columnas=array('tit1'=>'',
						   'tit2'=>'',
						   'tit3'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xPos'=>275, // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('tit1'=>array('justification'=>'left','width'=>280), // Justificación y ancho de la columna
									   'tit2'=>array('justification'=>'center','width'=>105),
						 			   'tit3'=>array('justification'=>'center','width'=>105))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data_titulo,$la_columnas,'',$la_config);
		$li_total7=$aa_puntos[30]+$aa_puntos[31]+$aa_puntos[32]+$aa_puntos[33];
		$io_pdf->addText(547,152,9,$li_total7);
		
		
		$la_data_titulo[1]=array('tit1'=>'<b>7   Trayectoria Disciplinaria</b>',
						         'tit2'=>'<b>Puntaje Maximo        04</b>',
						         'tit3'=>'<b>Puntaje Asignado   </b>');
		$la_columnas=array('tit1'=>'',
						   'tit2'=>'',
						   'tit3'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>2, // Mostrar Líneas
						 'shaded'=>2, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xPos'=>275, // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('tit1'=>array('justification'=>'left','width'=>280), // Justificación y ancho de la columna
									   'tit2'=>array('justification'=>'center','width'=>105),
						 			   'tit3'=>array('justification'=>'center','width'=>105))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data_titulo,$la_columnas,'',$la_config);
		$li_total8=$aa_puntos[34];
		$io_pdf->addText(548,114,9,$li_total8);
		
		$as_total1=$aa_puntos[1]+$aa_puntos[2]+$aa_puntos[3]+$aa_puntos[4]+$aa_puntos[5]+$aa_puntos[6]+$aa_puntos[7]+$aa_puntos[8]+$aa_puntos[9]+$aa_puntos[10];
		$as_total2=$aa_puntos[11]+$aa_puntos[12]+$aa_puntos[13]+$aa_puntos[14]+$aa_puntos[15]+$aa_puntos[16]+$aa_puntos[17]+$aa_puntos[18]+$aa_puntos[19]+$aa_puntos[20];
		$as_total3=$aa_puntos[21]+$aa_puntos[22]+$aa_puntos[23]+$aa_puntos[24]+$aa_puntos[25]+$aa_puntos[26]+$aa_puntos[27]+$aa_puntos[28]+$aa_puntos[29]+$aa_puntos[30];
		$as_total4=$aa_puntos[31]+$aa_puntos[32]+$aa_puntos[33]+$aa_puntos[34];
		$as_total=$as_total1+$as_total2+$as_total3+$as_total4;
		$io_pdf->addText(546,101,9,$as_total);
		$la_data_total[1]=array('tit1'=>'<b>PUNTAJE TOTAL OBTENIDOS</b>',
						         'tit2'=>'<b>100</b>');
		$la_columnas=array('tit1'=>'',
						   'tit2'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>2, // Mostrar Líneas
						 'shaded'=>2, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xPos'=>275, // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('tit1'=>array('justification'=>'right','width'=>385), // Justificación y ancho de la columna
									   'tit2'=>array('justification'=>'center','width'=>105))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data_total,$la_columnas,'',$la_config);
		/*$la_columnas=array('numero'=>'',
						   'denite'=>'',
						   'puntos'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>2, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xPos'=>310, // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('numero'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
									   'denite'=>array('justification'=>'left','width'=>450),
						 			   'puntos'=>array('justification'=>'center','width'=>60))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);*/					
	}// end function uf_print_detalle		
//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_total($as_total,&$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: la_data // arreglo de información
		//				   as_titcuentas // titulo de estructura presupuestaria
		//				   ai_i // total de registros
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: Función que imprime el detalle del reporte
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 10/06/2007 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	    
		$la_data_total[1]=array('tit1'=>'<b>PUNTAJE TOTAL OBTENIDOS</b>',
						         'tit2'=>''.$as_total,
								 'tit4'=>'');
		$la_columnas=array('tit1'=>'',
						   'tit2'=>'',
						   'tit4'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>2, // Mostrar Líneas
						 'shaded'=>2, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xPos'=>310, // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('tit1'=>array('justification'=>'right','width'=>385), // Justificación y ancho de la columna
									   'tit2'=>array('justification'=>'center','width'=>105),
									   'tit4'=>array('justification'=>'center','width'=>70))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data_total,$la_columnas,'',$la_config);
		
		
		/*$la_data_total[1]=array('columna'=>'<b>TOTAL</b>','total'=>"      ".$as_total);
		$la_columnas=array('columna'=>'','total'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 10, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>2, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xPos'=>310, // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('columna'=>array('justification'=>'right','width'=>500),
						               'total'=>array('justification'=>'left','width'=>60))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data_total,$la_columnas,'',$la_config);				*/
	}// end function uf_print_detalle		
//-----------------------------------------------------------------------------------------------------------------------------------

    require_once("../../shared/ezpdf/class.ezpdf.php");  
	require_once("../../shared/class_folder/class_funciones.php");
	$io_funciones=new class_funciones();				
	require_once("../class_folder/utilidades/class_funciones_srh.php");
	$io_fun_srh=new class_funciones_srh('../../');
	require_once("class_folder/sigesp_srh_class_report.php");
	$io_report=new sigesp_srh_class_report();
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ls_titulo="<b>SISTEMA DE MERITOS</b>";
	$ls_titulo2="<b>BAREMO APLICABLE AL PERSONAL TECNICO SUPERIOR</b>";
	$ls_titulo3="<b>PROFESIONAL Y SUPERVISORIO</b>";
	
//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
    
	 $ls_tiporeporte=$io_fun_srh->uf_obtenervalor_get("tiporeporte",0);
	 global $ls_tiporeporte;
 	 $ls_fechades=$_GET["fechades"]; 
	 $ls_fechahas=$_GET["fechahas"];
	 $ls_codperdes=$_GET["codperdes"];
	 $ls_codperhas=$_GET["codperhas"];
	 $ls_orden=$_GET["ls_orden"];
		
//--------------------------------------------------------------------------------------------------------------------------------
	$lb_valido=uf_insert_seguridad($ls_titulo); // Seguridad de Reporte
	if($lb_valido)
	{
        $lb_valido=$io_report->uf_listado_ascenso($ls_codperdes,$ls_codperhas,$ls_fechades,$ls_fechahas, $ls_orden);
      
		if ($lb_valido==false)
		{
		    print("<script language=JavaScript>");
			print(" alert('No hay nada que reportar');"); 
			//print(" close();");
			print("</script>");
		}
		   
		else  // Imprimimos el reporte
		{
     		
		 	error_reporting(E_ALL);
			set_time_limit(1800);
			$io_pdf=new Cezpdf('LETTER','portrait'); // Instancia de la clase PDF
			$io_pdf->selectFont('../../shared/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
			$io_pdf->ezSetCmMargins(3,1,1,2); // Configuración de los margenes en centímetros
			$io_pdf->ezStartPageNumbers(720,47,8,'','',1); // Insertar el número de página			
			uf_print_encabezado_pagina($ls_titulo,$ls_titulo2,$ls_titulo3,&$io_pdf);
			
			$lp_totrow=$io_report->det_ascenso->getRowCount("nroreg");			
			$li_aux=0;
			for($lp_i=1;$lp_i<=$lp_totrow;$lp_i++)
			{
			 $li_aux++;
			 $ls_codper=$io_report->det_ascenso->data["codper"][$lp_i];
			 $ls_cedula=$io_report->det_ascenso->data["cedper"][$lp_i];
			 $ls_cedula=number_format($ls_cedula,0,",",".");
			 $ls_nombre=$io_report->det_ascenso->data["nomper"][$lp_i]; 
			 $ls_apellido=$io_report->det_ascenso->data["apeper"][$lp_i];
			 $ls_ascenso=$io_report->det_ascenso->data["nroreg"][$lp_i];
			 $ls_fecha_asc=$io_report->det_ascenso->data["fecreg"][$lp_i];			 
			 $ls_fecha_asc=$io_funciones->uf_convertirfecmostrar($ls_fecha_asc);
			 //$ls_puntaje=$io_report->det_ascenso->data["reseval"][$lp_i];
			 $ls_caract1=$io_report->det_ascenso->data["caract1"][$lp_i];
		 	 $ls_caract2=$io_report->det_ascenso->data["caract2"][$lp_i];
			 $ls_dependencia=$io_report->det_ascenso->data["desuniadm"][$lp_i];
			
			 if ($ls_caract2=="")
			 {
				$ls_caract=$ls_caract1;
			
		 	 }
			 else
			 {
				 $ls_caract=$ls_caract2;
			 }
			
			
			
			 $ls_carasc1=$io_report->det_ascenso->data["descar"][$lp_i];
			 $ls_carasc2=$io_report->det_ascenso->data["denasicar"][$lp_i];
			 
			 if ($ls_carasc2=="")
			 { 
				$ls_carasc=$ls_carasc1;			
			 }
			 else
			 {
		 		 $ls_carasc=$ls_carasc2;
			 }
			 
			 $la_data[$lp_i]=array('codper'=>$ls_codper,'cedula'=>$ls_cedula,'nombre'=>$ls_nombre." ".$ls_apellido,
			                       'ascenso'=>$ls_ascenso,'fecha'=>$ls_fecha_asc,/*'puntaje'=>$ls_puntaje,*/
			                       'caract'=>$ls_caract,'carasc'=>$ls_carasc,'desuniadm'=>$ls_dependencia); 
			 uf_print_encabezado_detalle($la_data,&$io_pdf); 
			 unset($la_data); 
			 
			 $lb_valido=$io_report->uf_items_eval_ascenso($ls_ascenso);
			
			 if ($lb_valido)
		     {
				 $li_totrow=$io_report->det_item_asc->getRowCount("nroreg"); 
				 $chk1=$chk2=$chk3=$chk4=$chk5=$chk6=$chk7=$chk8=$chk9=$chk10=$chk11=$chk12=$chk13=$chk14=$chk15=0;			 
				 $chk16=$chk17=$chk18=$chk19=$chk20=$chk21=$chk22=$chk23=$chk24=$chk25=$chk26=$chk27=$chk28=$chk29=$chk30=0;
				 $chk31=$chk32=$chk33=$chk34=0;
				 for($li_i=1;$li_i<=$li_totrow;$li_i++)
			  		{      
					 $ls_denominacion=$io_report->det_item_asc->data["denite"][$li_i];
					 $ls_puntos=$io_report->det_item_asc->data["puntos"][$li_i];
					 $ls_fecha_eval=$io_report->det_item_asc->data["fecha"][$li_i];			 
			         $ls_fecha_eval=$io_funciones->uf_convertirfecmostrar($ls_fecha_eval);
					 $la_data_item[$li_i]=array('numero'=>$li_i,'denite'=>$ls_denominacion,'puntos'=>$ls_puntos);
					 if (($ls_denominacion=='CAPACITACION Y DESARROLLO (ADICIONAL)(*)')||($ls_denominacion=='Capacitación y Desarrollo (Adicional)(*)'))
					 {
					 	$la_puntos[1]=$ls_puntos;
						$chk1=1;
					 }
					 elseif(($ls_denominacion=='FORMAL (NO ACUMULATIVO)')||($ls_denominacion=='Formal (No Acumulativo)'))
					 {
					 	$la_puntos[2]=$ls_puntos;
						$chk2=1;
					 }
					 elseif(($ls_denominacion=='PROFESIONAL UNIVERSITARIO CON DOCTORADO')||($ls_denominacion=='Profesional Universitario con Doctorado'))
					 {
					 	$la_puntos[3]=$ls_puntos;
						$chk3=1;
					 }
					 elseif(($ls_denominacion=='PROFESIONAL UNIVERSITARIO CON MAESTRIA')||($ls_denominacion=='Profesional Universitario con Maestría'))
					 {
					 	$la_puntos[4]=$ls_puntos;
						$chk4=1;
					 }
					 elseif(($ls_denominacion=='PROFESIONAL UNIVERSITARIO CON ESPECIALIZACION')||($ls_denominacion=='Profesional Universitario con Especialización'))
					 {
					 	$la_puntos[5]=$ls_puntos;
						$chk5=1;
					 }
					 elseif(($ls_denominacion=='PRE GRADO APROBADO')||(($ls_denominacion=='Pre Grado aprobado')))
					 {
					 	$la_puntos[6]=$ls_puntos;
						$chk6=1;
					 }
					 elseif(($ls_denominacion=='T.S.U. CON ESTUDIOS DE ESPECIALIZACION APROBADO')||(($ls_denominacion=='T.S.U. con estudios de Especialización aprobado')))
					 {
					 	$la_puntos[7]=$ls_puntos;
						$chk7=1;
					 }
					 elseif(($ls_denominacion=='T.S.U. CON ESTUDIOS UNIV. OPTAR A LA LIC. (1 AÑO APROB)')||($ls_denominacion=='T.S.U. con estudios univ. optar a la Lic. (1 año aprob)'))
					 {
					 	$la_puntos[8]=$ls_puntos;
						$chk8=1;
					 }
					 elseif(($ls_denominacion=='201 EN ADELANTE')||($ls_denominacion=='201 en adelante'))
					 {
					 	$la_puntos[9]=$ls_puntos;
						$chk9=1;
					 }
					 elseif(($ls_denominacion=='151 A 200 HORAS')||($ls_denominacion=='151 a 200 Horas'))
					 {
					 	$la_puntos[10]=$ls_puntos;
						$chk10=1;
					 }
					 elseif(($ls_denominacion=='101 A 150 HORAS')||($ls_denominacion=='101 a 150 Horas'))
					 {
					 	$la_puntos[11]=$ls_puntos;
						$chk11=1;
					 }
					 elseif(($ls_denominacion=='51 A 100 HORAS')||($ls_denominacion=='51 a 100 Horas'))
					 {
					 	$la_puntos[12]=$ls_puntos;
						$chk12=1;
					 }
					 elseif(($ls_denominacion=='08 A 50 HORAS')||($ls_denominacion=='08 a 50 Horas'))
					 {
					 	$la_puntos[13]=$ls_puntos;
						$chk13=1;
					 }
					 elseif(($ls_denominacion=='DE 15 AÑOS EN ADELANTE')||($ls_denominacion=='De 15 años en adelante En el area'))
					 {
					 	$la_puntos[14]=$ls_puntos;
						$chk14=1;
					 }
					 elseif(($ls_denominacion=='DE 15 AÑOS EN ADELANTE EN EL IPSFA')||($ls_denominacion=='De 15 años en adelante En el IPSFA'))
					 {
					 	$la_puntos[15]=$ls_puntos;
						$chk15=1;
					 }
					 elseif(($ls_denominacion=='14 A 10 AÑOS')||($ls_denominacion=='14 a 10 años En el area'))
					 {
					 	$la_puntos[16]=$ls_puntos;
						$chk16=1;
					 }
					 elseif(($ls_denominacion=='14 A 10 AÑOS IPSFA')||($ls_denominacion=='14 a 10 años En el IPSFA'))
					 {
					 	$la_puntos[17]=$ls_puntos;
						$chk17=1;
					 }
					 elseif(($ls_denominacion=='9 A 5 AÑOS')||($ls_denominacion=='9 a 5 años En el area'))
					 {
					 	$la_puntos[18]=$ls_puntos;
						$chk18=1;
					 }
					 elseif(($ls_denominacion=='9 A 5 AÑOS IPSFA')||($ls_denominacion=='9 a 5 años En el IPSFA'))
					 {
					 	$la_puntos[19]=$ls_puntos;
						$chk19=1;
					 }
					 elseif(($ls_denominacion=='4 A 2 AÑOS')||($ls_denominacion=='4 a 2 años En el area'))
					 {
					 	$la_puntos[20]=$ls_puntos;
						$chk20=1;
					 }
					  elseif(($ls_denominacion=='4 A 2 AÑOS IPSFA')||($ls_denominacion=='4 a 2 años En el IPSFA'))
					 {
					 	$la_puntos[21]=$ls_puntos;
						$chk21=1;
					 }
					 elseif(($ls_denominacion=='3 AÑOS Y UN MES EN ADELANTE')||($ls_denominacion=='3 años y un mes en adelante'))
					 {
					 	$la_puntos[22]=$ls_puntos;
						$chk22=1;
					 }
					 elseif(($ls_denominacion=='1 AÑO Y UN MES A 3 AÑOS')||($ls_denominacion=='1 año y un mes a 3 años'))
					 {
					 	$la_puntos[23]=$ls_puntos;
						$chk23=1;
					 }
					 elseif(($ls_denominacion=='6 MESES A 1 AÑO')||($ls_denominacion=='6 meses a 1 año'))
					 {
					 	$la_puntos[24]=$ls_puntos;
						$chk24=1;
					 }
					 elseif(($ls_denominacion=='EXCEPCIONAL')||($ls_denominacion=='Excepcional'))
					 {
					 	$la_puntos[25]=$ls_puntos;
						$chk25=1;
					 }
					 elseif(($ls_denominacion=='SOBRE LO ESPERADO')||($ls_denominacion=='Sobre lo Esperado'))
					 {
					 	$la_puntos[26]=$ls_puntos;
						$chk26=1;
					 }
					 elseif(($ls_denominacion=='DENTRO DE LO ESPERADO')||($ls_denominacion=='Dentro de lo Esperado'))
					 {
					 	$la_puntos[27]=$ls_puntos;
						$chk27=1;
					 }
					 elseif(($ls_denominacion=='PRUEBA PSICOLOGICAS')||($ls_denominacion=='Pruebas Psicologicas'))
					 {
					 	$la_puntos[28]=$ls_puntos;
						$chk28=1;
					 }
					 elseif(($ls_denominacion=='ENTREVISTA PSICOLOGICA')||($ls_denominacion=='Entrevistas Psicologicas'))
					 {
					 	$la_puntos[29]=$ls_puntos;
						$chk29=1;
					 }
					 elseif(($ls_denominacion=='DOMINIO DEL AREA DE TRABAJO')||($ls_denominacion=='Dominio del area de trabajo'))
					 {
					 	$la_puntos[30]=$ls_puntos;
						$chk30=1;
					 }
					 elseif(($ls_denominacion=='LOGROS OBTENIDOS')||($ls_denominacion=='Logros obtenidos'))
					 {
					 	$la_puntos[31]=$ls_puntos;
						$chk31=1;
					 }
					 elseif(($ls_denominacion=='MOTIVACION, INTERES E INICIATIVA')||($ls_denominacion=='Motivacion, interes e iniciativa'))
					 {
					 	$la_puntos[32]=$ls_puntos;
						$chk32=1;
					 }
					 elseif(($ls_denominacion=='HABILIDADES POTENCIALES')||($ls_denominacion=='Habilidades potenciales'))
					 {
					 	$la_puntos[33]=$ls_puntos;
						$chk33=1;
					 }
					 elseif(($ls_denominacion=='TRAYECTORIA DISCIPLINARIA')||($ls_denominacion=='Trayectoria Disciplinaria'))
					 {
					 	$la_puntos[34]=$ls_puntos;
						$chk34=1;
					 }
					 if($chk1==0)
					 {$la_puntos[1]="";}
					 if($chk2==0)
					 {$la_puntos[2]="";}
					 if($chk3==0)
					 {$la_puntos[3]="";}
					 if($chk4==0)
					 {$la_puntos[4]="";}
					 if($chk5==0)
					 {$la_puntos[5]="";}
					 if($chk6==0)
					 {$la_puntos[6]="";}
					 if($chk7==0)
					 {$la_puntos[7]="";}
					 if($chk8==0)
					 {$la_puntos[8]="";}
					 if($chk9==0)
					 {$la_puntos[9]="";}
					 if($chk10==0)
					 {$la_puntos[10]="";}
					 if($chk11==0)
					 {$la_puntos[11]="";}
					 if($chk12==0)
					 {$la_puntos[12]="";}
					 if($chk13==0)
					 {$la_puntos[13]="";}
					 if($chk14==0)
					 {$la_puntos[14]="";}
					 if($chk15==0)
					 {$la_puntos[15]="";}
					 if($chk16==0)
					 {$la_puntos[16]="";}
					 if($chk17==0)
					 {$la_puntos[17]="";}
					 if($chk18==0)
					 {$la_puntos[18]="";}
					 if($chk19==0)
					 {$la_puntos[19]="";}
					 if($chk20==0)
					 {$la_puntos[20]="";}
					 if($chk21==0)
					 {$la_puntos[21]="";}
					 if($chk22==0)
					 {$la_puntos[22]="";}
					 if($chk23==0)
					 {$la_puntos[23]="";}
					 if($chk24==0)
					 {$la_puntos[24]="";}
					 if($chk25==0)
					 {$la_puntos[25]="";}
					 if($chk26==0)
					 {$la_puntos[26]="";}
					 if($chk27==0)
					 {$la_puntos[27]="";}
					 if($chk28==0)
					 {$la_puntos[28]="";}
					 if($chk29==0)
					 {$la_puntos[29]="";}
					 if($chk30==0)
					 {$la_puntos[30]="";}
					 if($chk31==0)
					 {$la_puntos[31]="";}
					 if($chk32==0)
					 {$la_puntos[32]="";}
					 if($chk33==0)
					 {$la_puntos[33]="";}
					 if($chk34==0)
					 {$la_puntos[34]="";}
						 			  			  
			       }
				   uf_print_detalle($la_data_item,$ls_fecha_eval,$la_puntos,&$io_pdf);
			       unset($la_data_item);
				   //uf_print_total($ls_puntaje,&$io_pdf);
			     
		     }
			 if($li_aux<$lp_totrow)		
			 {
			 	$io_pdf->ezNewPage(); // Insertar una nueva página
			 } 
						
		   }//end del for*/
		   
		  
					
		}	//end del else
		   
		      
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

}

?>	

	
	