<?php
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//    REPORTE: Listado de Movimiento de personal
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
		$lb_valido=$io_fun_srh->uf_load_seguridad_reporte("SRH","sigesp_srh_p_movimiento_personal.php",$ls_descripcion);
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------	
	
	//-----------------------------------------------------------------------------------------------------------------------------------	
	function uf_print_encabezado_pagina($as_titulo,$as_nummov,$as_fecha_r,$as_para,$as_de,$as_asunto,$io_pdf)
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
		$io_pdf->line(15,40,585,40);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],25,705,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();		
	    $io_pdf->ezSetY(670);
		$la_data=array(array('titulo1'=>'<b>'.$as_titulo.'									</b>'));
		$la_columnas=array('titulo1'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 10, // Tamaño de Letras
						 'titleFontSize' => 14,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				      	 'cols'=>array('titulo1'=>array('justification'=>'center','width'=>500))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
        unset($la_data);
		unset($la_columnas);
		unset($la_config);		
		
		$io_pdf->setStrokeColor(0,0,0);
		$io_pdf->Rectangle(51,630,500,60);
		$io_pdf->addText(465,683,6,"<b>No. </b>"); // Agregar el título
		$io_pdf->addText(472,670,8,$as_nummov); // Agregar el título
		$io_pdf->addText(465,653,6,"<b>FECHA: </b>"); // Agregar el título
		$io_pdf->addText(485,640,8,$as_fecha_r); // Agregar el título
		$io_pdf->line(460,660,550,660);	//HORIZONTAL
		$io_pdf->line(460,630,460,690);	//VERTICAL	
		// cuadro inferior
		$io_pdf->setStrokeColor(0,0,0);
        $io_pdf->Rectangle(50,60,500,60); 
		$io_pdf->addText(60,112,7,"ELABORADO POR:"); // Agregar el título
		$io_pdf->line(210,60,210,120);	//VERTICAL		
		$io_pdf->addText(220,112,7,"REVISADO POR:"); // Agregar el título
		$io_pdf->line(380,60,380,120);	//VERTICAL		
		$io_pdf->addText(390,112,7,"APROBADO POR:"); // Agregar el título

		$io_pdf->ezSetY(632);
		$la_data[1]=array('titulo1'=>'<b>PARA: </b>', 'titulo2'=>$as_para);
 		$la_data[2]=array('titulo1'=>'<b>DE: </b>', 'titulo2'=>$as_de);
		$la_data[3]=array('titulo1'=>'<b>ASUNTO: </b>', 'titulo2'=>$as_asunto);	
		$la_columnas=array('titulo1'=>'','titulo2'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'showLines'=>2, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'rowGap' => 6,
						 'width'=>500, // Ancho de la tabla						 
						 'maxWidth'=>500,
						 'cols'=>array('titulo1'=>array('justification'=>'left','width'=>100),
						  			   'titulo2'=>array('justification'=>'left','width'=>400))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);	
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
		$la_data[1]=array('titulo1'=>'<b>DATOS DEL EMPLEADO Ó CIUDADANO:</b>');
		$la_columnas=array('titulo1'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'showLines'=>2, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'rowGap' => 6,
						 'width'=>500, // Ancho de la tabla						 
						 'maxWidth'=>500,
						 'cols'=>array('titulo1'=>array('justification'=>'left','width'=>500))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);	
		unset($la_data);
		unset($la_columnas);
		unset($la_config);

		$io_pdf->restoreState();
	    $io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	
	}// end function uf_print_encabezado_pagina	
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------	
	function uf_print_detalle($as_apeper,$as_nomper,$as_cedper,$as_descaract,$as_codgraact,$as_codpasact,$ai_suebasact,$ai_comact,$ai_otringact,
							  $ai_suetotact,$as_desuniadmact,$as_desuniadm,$as_descar,$as_codgra,$as_codpas,$ai_suebaspro,$ai_compro,$ai_otringpro,
							  $ai_suetotpro,$as_fecinimov,$as_tipnom,$as_dengrumov,$as_motivo,$as_observacion,&$io_pdf)
   	{
		$la_data[1]=array('columna1'=>'<b>APELLIDOS: </b>'.$as_apeper,
		                  'columna2'=>'<b>NOMBRE: </b>'.$as_nomper,
						  'columna3'=>'<b>C.I: </b>'.$as_cedper);
		$la_columna=array('columna1'=>'','columna2'=>'','columna3'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho Máximo de la tabla
						 'rowGap' => 6,
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('columna1'=>array('justification'=>'left','width'=>210), // Justificación y ancho de la columna
						 			   'columna2'=>array('justification'=>'left','width'=>210),
									   'columna3'=>array('justification'=>'left','width'=>80))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		$la_data[1]=array('columna1'=>'<b>UNIDAD ADMINISTRATIVA ACTUAL: </b>'.$as_desuniadmact);
		$la_data[2]=array('columna1'=>'<b>CARGO ACTUAL: </b>'.$as_descaract);
		$la_columna=array('columna1'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>2, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho Máximo de la tabla
						 'rowGap' => 6,
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('columna1'=>array('justification'=>'left','width'=>500))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$la_data[1]=array('columna2'=>'<b>GRADO: </b>           '.$as_codgraact,
						  'columna3'=>'<b>PASO: </b>           '.$as_codpasact,
						  'columna4'=>'<b>SUELDO BASICO: </b>	'.$ai_suebasact,
						  'columna5'=>'<b>COMPENSACIÓN: </b>           '.$ai_comact,
						  'columna6'=>'<b>OTROS INGRESOS: </b>	'.$ai_otringact,
						  'columna7'=>'<b>SUELDO TOTAL: </b>	'.$ai_suetotact);
		$la_columna=array('columna2'=>'','columna3'=>'','columna4'=>'','columna5'=>'','columna6'=>'','columna7'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'titleFontSize' => 7,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho Máximo de la tabla
						 'rowGap' => 6,
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('columna2'=>array('justification'=>'left','width'=>60),
									   'columna3'=>array('justification'=>'left','width'=>60),
									   'columna4'=>array('justification'=>'left','width'=>95),
									   'columna5'=>array('justification'=>'left','width'=>95),
									   'columna6'=>array('justification'=>'left','width'=>95),
									   'columna7'=>array('justification'=>'left','width'=>95))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$la_data[1]=array('columna1'=>'<b>UNIDAD ADMINISTRATIVA PROPUESTA: </b>'.$as_desuniadm);
		$la_data[2]=array('columna1'=>'<b>CARGO PROPUESTO: </b>'.$as_descar);
		$la_columna=array('columna1'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>2, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho Máximo de la tabla
						 'rowGap' => 6,
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('columna1'=>array('justification'=>'left','width'=>500))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$la_data[1]=array('columna2'=>'<b>GRADO: </b>           '.$as_codgra,
						  'columna3'=>'<b>PASO: </b>           '.$as_codpas,
						  'columna4'=>'<b>SUELDO BASICO: </b>	'.$ai_suebaspro,
						  'columna5'=>'<b>COMPENSACIÓN: </b>           '.$ai_compro,
						  'columna6'=>'<b>OTROS INGRESOS: </b>	'.$ai_otringpro,
						  'columna7'=>'<b>SUELDO TOTAL: </b>	'.$ai_suetotpro);
		$la_columna=array('columna2'=>'','columna3'=>'','columna4'=>'','columna5'=>'','columna6'=>'','columna7'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'titleFontSize' => 7,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho Máximo de la tabla
						 'rowGap' => 6,
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('columna2'=>array('justification'=>'left','width'=>60),
									   'columna3'=>array('justification'=>'left','width'=>60),
									   'columna4'=>array('justification'=>'left','width'=>95),
									   'columna5'=>array('justification'=>'left','width'=>95),
									   'columna6'=>array('justification'=>'left','width'=>95),
									   'columna7'=>array('justification'=>'left','width'=>95))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$la_data[1]=array('columna1'=>'<b>TIPO DE NOMBRAMIENTO: </b>'.$as_tipnom);
		$la_data[2]=array('columna1'=>'<b>GRUPO DE MOVIMIENTO: </b>'.$as_dengrumov);
		$la_data[3]=array('columna1'=>'<b>MOTIVO DEL MOVIMIENTO: </b>'.$as_motivo);
		$la_data[4]=array('columna1'=>'<b>RECOMENDACIÓN: </b>PROCEDER AL MOVIMIENTO SEÑALADO A PARTIR DE LA FECHA '.$as_fecinimov);
		$la_data[5]=array('columna1'=>'<b>OBSERVACIÓN: </b>'.$as_observacion);
		$la_columna=array('columna1'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>2, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho Máximo de la tabla
						 'rowGap' => 6,
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('columna1'=>array('justification'=>'left','width'=>500))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
	}
	//-----------------------------------------------------------------------------------------------------------------------------------
    require_once("../../shared/ezpdf/class.ezpdf.php");  
	require_once("../../shared/class_folder/class_funciones.php");
	$io_funciones=new class_funciones();				
	require_once("../class_folder/utilidades/class_funciones_srh.php");
	$io_fun_srh=new class_funciones_srh('../../');
	require_once("class_folder/sigesp_srh_class_report.php");
	$io_report=new sigesp_srh_class_report();
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	
	$ls_titulo="<b>CUENTA MOVIMIENTO DE PERSONAL</b>";
	$ls_nroreg = $io_fun_srh->uf_obtenervalor_get("nroreg","");
	$ls_codper = $io_fun_srh->uf_obtenervalor_get("codper","");

    $lb_valido=uf_insert_seguridad($ls_titulo); // Seguridad de Reporte
	if ($lb_valido)
	{
		$lb_valido=$io_report->uf_listado_movimiento($ls_nroreg, $ls_codper);		
		if ($lb_valido==false)
		{
			print("<script language=JavaScript>");
			print(" alert('No hay nada que reportar');"); 
			print(" close();");
			print("</script>");
		}
		else  // Imprimimos el reporte
		{	
			error_reporting(E_ALL);
			set_time_limit(1800);
			$io_pdf=new Cezpdf('LETTER','PORTRAIT'); // Instancia de la clase PDF
			$io_pdf->selectFont('../../shared/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
			$io_pdf->ezSetCmMargins(4,5,3,3); // Configuración de los margenes en centímetros
			$io_pdf->ezStartPageNumbers(720,47,8,'','',1); // Insertar el número de página			
			while(!$io_report->rs_data->EOF)
			{
				$ls_nummov=$io_report->rs_data->fields["nummov"];
				$ls_fecha_r=$io_report->rs_data->fields["fecreg"];			 
				$ls_fecha_r=$io_funciones->uf_convertirfecmostrar($ls_fecha_r);
				$ls_fecinimov=$io_funciones->uf_convertirfecmostrar($io_report->rs_data->fields["fecinimov"]);
				$ls_de="PRESIDENTE DE LA JUNTA ADMINISTRADORA";			 
				$ls_para="GERENTE DE RECURSOS HUMANOS";
				$ls_asunto="MOVIMIENTO DE PERSONAL";
				$ls_nomper=$io_report->rs_data->fields["nomper"];
				$ls_apeper=$io_report->rs_data->fields["apeper"];
				$ls_cedper=$io_report->rs_data->fields["cedper"];
				$ls_tipnom=$io_report->rs_data->fields["tipnom"];
				$ls_dengrumov=$io_report->rs_data->fields["dengrumov"];
				$ls_motivo=$io_report->rs_data->fields["motivo"];
				$ls_observacion=$io_report->rs_data->fields["observacion"];
				$ls_descar1=$io_report->rs_data->fields["descaract"];
				$ls_descar2=$io_report->rs_data->fields["denasicaract"];
				$ls_codgraact=$io_report->rs_data->fields["codgraact"];
				$ls_codpasact=$io_report->rs_data->fields["codpasact"];
				if ($ls_descar1!="")
				{
					$ls_descaract=$ls_descar1;
					$ls_codgraact="";
					$ls_codpasact="";
				}
				if ($ls_descar2!="")
				{
					$ls_descaract=$ls_descar2;
				}
				$li_suebasact=$io_report->rs_data->fields["suebasact"];
				$li_comact=$io_report->rs_data->fields["comact"];
				$li_otringact=$io_report->rs_data->fields["otringact"];
				$li_suetotact=$li_suebasact+$li_comact+$li_otringact;
				$li_suebasact=number_format($li_suebasact,2,",",".");
				$li_comact=number_format($li_comact,2,",",".");
				$li_otringact=number_format($li_otringact,2,",",".");
				$li_suetotact=number_format($li_suetotact,2,",",".");
				$ls_desuniadmact=$io_report->rs_data->fields["desuniadmact"];
				$ls_uniadmact=$io_report->rs_data->fields["minorguniadmact"]."-".$io_report->rs_data->fields["ofiuniadmact"]."-".$io_report->rs_data->fields["uniuniadmact"]."-".$io_report->rs_data->fields["depuniadmact"]."-".$io_report->rs_data->fields["prouniadmact"];
				$ls_desuniadmact =$ls_uniadmact."  ".$ls_desuniadmact;
				$ls_desuniadm=$io_report->rs_data->fields["desuniadm"];
				$ls_uniadm=$io_report->rs_data->fields["minorguniadm"]."-".$io_report->rs_data->fields["ofiuniadm"]."-".$io_report->rs_data->fields["uniuniadm"]."-".$io_report->rs_data->fields["depuniadm"]."-".$io_report->rs_data->fields["prouniadm"];
				$ls_desuniadm =$ls_uniadm."  ".$ls_desuniadm;
				$ls_descar1=$io_report->rs_data->fields["descar"];
				$ls_descar2=$io_report->rs_data->fields["denasicar"];
				$ls_codgra=$io_report->rs_data->fields["codgra"];
				$ls_codpas=$io_report->rs_data->fields["codpas"];
				if ($ls_descar1!="")
				{
					$ls_descar=$ls_descar1;
					$ls_codgra="";
					$ls_codpas="";
				}
				if ($ls_descar2!="")
				{
					$ls_descar=$ls_descar2;
				}
				$li_suebaspro=number_format($io_report->rs_data->fields["suebaspro"],2,",",".");
				$li_compro=number_format($io_report->rs_data->fields["compro"],2,",",".");
				$li_otringpro=number_format($io_report->rs_data->fields["otringpro"],2,",",".");
				$li_suetotpro=number_format($io_report->rs_data->fields["suetotpro"],2,",",".");

				uf_print_encabezado_pagina($ls_titulo,$ls_nummov,$ls_fecha_r,$ls_de,$ls_para,$ls_asunto,&$io_pdf);	
				uf_print_detalle($ls_apeper,$ls_nomper,$ls_cedper,$ls_descaract,$ls_codgraact,$ls_codpasact,$li_suebasact,
								 $li_comact,$li_otringact,$li_suetotact,$ls_desuniadmact,$ls_desuniadm,$ls_descar,$ls_codgra,$ls_codpas,
								 $li_suebaspro,$li_compro,$li_otringpro,$li_suetotpro,$ls_fecinimov,$ls_tipnom,$ls_dengrumov,$ls_motivo,
								 $ls_observacion,&$io_pdf);	
				
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
				$io_report->rs_data->MoveNext();
			}
		}
	}
?>	