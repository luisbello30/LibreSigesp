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
	function uf_insert_seguridad($as_titulo)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_insert_seguridad
		//		   Access: private 
		//	    Arguments: as_titulo // Título del Reporte
		//    Description: función que guarda la seguridad de quien generó el reporte
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 21/06/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_nomina;
		$ls_descripcion="Generó el Reporte ".$as_titulo;
		$lb_valido=$io_fun_nomina->uf_load_seguridad_reporte("SNR","sigesp_snorh_r_listadopersonalporjubilarse.php",$ls_descripcion);
		return $lb_valido;
	}
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,$as_titulo2,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 21/06/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->line(50,40,900,40);
		$io_pdf->addText(50,30,9,'LEY DEL ESTATUTO SOBRE EL RÉGIMEN DE JUBILACIONES Y PENSIONES DE LOS FUNCIONARIOS O FUNCIONARIAS O EMPLEADOS O EMPLEADAS  DE LA ADMINISTRACIÓN PÚBLICA NACIONAL,');
		$io_pdf->addText(50,20,9,'DE LOS ESTADOS Y DE LOS MUNICIPIOS. Titulo1 DISPOSICIONES GENERALES, Artículo 3');
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],50,530,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		$tm=470-($li_tm/2);
		$io_pdf->addText($tm,540,18,$as_titulo); // Agregar el título
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo2);
		$tm=512-($li_tm/2);
		$io_pdf->addText($tm,528,12,$as_titulo2); // Agregar el título
		$io_pdf->addText(912,560,8,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(918,553,7,date("h:i a")); // Agregar la Hora
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera(&$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: private 
		//	    Arguments: io_pdf // Instancia de objeto pdf
		//    Description: función que imprime el detalle por concepto
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 27/07/2006 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->ezSety(520);
        $io_pdf->setColor(0.9,0.9,0.9);
        $io_pdf->filledRectangle(40,496,915,$io_pdf->getFontHeight(19));
        $io_pdf->setColor(0,0,0);
		$la_data[1]=array('nro'=>'<b>Nº</b>',
						  'codigo'=>'<b>Código</b>',
						  'nombre'=>'<b>Apellidos y Nombres</b>',
						  'edad'=>'<b>Edad</b>',
						  'fechanac'=>'<b>Fecha de Nacimiento</b>',
						  'fecha'=>'<b>Fecha de Ingreso a la Administración P</b>',	
						  'anoser'=>'<b>Años de Serv. Prev Adm. Pub.</b>',
						  'fechaing'=>'<b>Fecha de Ingreso a la Institución</b>',		
						  'anoserinst'=>'<b>Años de Serv. en la Inst.</b>',
						  'anoobr'=>'<b>Años de Serv. Personal Obrero</b>',
						  'totalanios'=>'<b>Total Años Servicio</b>');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 11,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>700, // Ancho de la tabla
						 'maxWidth'=>700, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla				         
						 'cols'=>array('nro'=>array('justification'=>'center','width'=>25),
						 				'codigo'=>array('justification'=>'center','width'=>55), // Justificación y ancho de la columna
						 			   'nombre'=>array('justification'=>'center','width'=>175), // Justificación y ancho de la columna
									   'edad'=>array('justification'=>'center','width'=>60),
						 			   'fechanac'=>array('justification'=>'center','width'=>90), // Justificación y ancho de la columna
									   'fecha'=>array('justification'=>'center','width'=>90), // Justificación y ancho de la columna
									   'anoser'=>array('justification'=>'center','width'=>80), // Justificación y ancho de la columna						 		
									   'fechaing'=>array('justification'=>'center','width'=>90), // Justificación y ancho de la columna
									   'anoserinst'=>array('justification'=>'center','width'=>80), // Justificación y ancho de la columna
									   'anoobr'=>array('justification'=>'center','width'=>80), // Justificación y ancho de la columna
									   'totalanios'=>array('justification'=>'center','width'=>90))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,'','',$la_config);
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_cabecera
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime el detalle por personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 22/06/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 11,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>700, // Ancho de la tabla
						 'maxWidth'=>700, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla				         
						 'cols'=>array('nro'=>array('justification'=>'center','width'=>25),
						 				'codigo'=>array('justification'=>'center','width'=>55), // Justificación y ancho de la columna
						 			   'nombre'=>array('justification'=>'left','width'=>175), // Justificación y ancho de la columna
									   'edad'=>array('justification'=>'center','width'=>60),
						 			   'fechanac'=>array('justification'=>'center','width'=>90), // Justificación y ancho de la columna
									   'fecha'=>array('justification'=>'center','width'=>90), // Justificación y ancho de la columna
									   'anoser'=>array('justification'=>'center','width'=>80), // Justificación y ancho de la columna						 		
									   'fechaing'=>array('justification'=>'center','width'=>90), // Justificación y ancho de la columna
									   'anoserinst'=>array('justification'=>'center','width'=>80), // Justificación y ancho de la columna
									   'anoobr'=>array('justification'=>'center','width'=>80), // Justificación y ancho de la columna
									   'totalanios'=>array('justification'=>'center','width'=>90))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,'','',$la_config);
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------------	
	function calcular_anos($fecha_1,$fecha_2)
	{  
		$c = date("Y",$fecha_1);	   
		$b = date("m",$fecha_1);	  
		$a = date("d",$fecha_1); 	
		$anos = date("Y",$fecha_2)-$c; 
		if(date("m",$fecha_2)-$b > 0)
		{
		}
		elseif(date("m",$fecha_2)-$b == 0)
		{
			if(date("d",$fecha_2)-$a <= 0)
			{		  
				$anos = $anos-1;	        
			}
		}
		else
		{		  
			$anos = $anos-1;		          
		}  
		return $anos;	 
	} //FIN DE calcular_anos
	//---------------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------
	require_once("../../shared/ezpdf/class.ezpdf.php");
	require_once("sigesp_snorh_class_report.php");
	$io_report=new sigesp_snorh_class_report();
	require_once("../../shared/class_folder/class_funciones.php");
	$io_funciones=new class_funciones();				
	require_once("../class_folder/class_funciones_nomina.php");
	$io_fun_nomina=new class_funciones_nomina();
	require_once("../../shared/class_folder/class_fecha.php");
	$io_fecha=new class_fecha();	
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_codnomdes=$io_fun_nomina->uf_obtenervalor_get("codnomdes","");
	$ls_codnomhas=$io_fun_nomina->uf_obtenervalor_get("codnomhas","");
	$ls_codperdes=$io_fun_nomina->uf_obtenervalor_get("codperdes","");
	$ls_codperhas=$io_fun_nomina->uf_obtenervalor_get("codperhas","");
	$ls_orden=$io_fun_nomina->uf_obtenervalor_get("orden","1");		
	$ls_masculino=$io_fun_nomina->uf_obtenervalor_get("masculino","");
	$ls_femenino=$io_fun_nomina->uf_obtenervalor_get("femenino","");
	$ls_fecdes=$io_fun_nomina->uf_obtenervalor_get("fecdes","");  
	$ls_fechas=$io_fun_nomina->uf_obtenervalor_get("fechas","");
	$ls_titulo="<b>Listado de Personal Por Jubilarse</b>";
	$ls_titulo2="<b>Desde </b>".$ls_fecdes."<b> Hasta </b>".$ls_fechas;
	$lb_valido=uf_insert_seguridad($ls_titulo); // Seguridad de Reporte
	if($lb_valido)
	{  
		$lb_valido=$io_report->uf_listado_personaljub($ls_codperdes,$ls_codperhas,$ls_codnomdes,$ls_codnomhas,$ls_femenino,$ls_masculino,$ls_orden);
	}
	if(($lb_valido==false)||($io_report->rs_data->RecordCount()==0)) // Existe algún error ó no hay registros
	{
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');"); 
		print(" close();");
		print("</script>");
	}
	else // Imprimimos el reporte
	{
		error_reporting(E_ALL);
		set_time_limit(1800);		
		$io_pdf=new Cezpdf('LEGAL','landscape'); // Instancia de la clase PDF
		$io_pdf->selectFont('../../shared/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
		$io_pdf->ezSetCmMargins(4,5,3,3); // Configuración de los margenes en centímetros		
		uf_print_encabezado_pagina($ls_titulo,$ls_titulo2,$io_pdf); // Imprimimos el encabezado de la página	  
		$io_pdf->ezStartPageNumbers(900,50,10,'','',1); // Insertar el número de página
		$li_totrow=$io_report->rs_data->RecordCount();
		uf_print_cabecera($io_pdf);
		$ls_confjub="";
		$ls_confjub=$io_report->uf_select_config('SNO', 'CONFIG','CONF JUB',$ls_confjub,'I');
		$li_edadf="";
		$li_edadf=number_format($io_report->uf_select_config('SNO', 'NOMINA', 'EDADF', $li_edadf, 'C'),0,"","");
		$li_edadm="";
		$li_edadm=number_format($io_report->uf_select_config('SNO', 'NOMINA', 'EDADM', $li_edadm, 'C'),0,"","");
		$li_anomim="";
		$li_anomim=number_format($io_report->uf_select_config('SNO', 'NOMINA', 'ANOM', $li_anomim, 'C'),0,"","");
		$li_anomax="";
		$li_anomax=number_format($io_report->uf_select_config("SNO","NOMINA","ANOT", $li_anomax, 'C'),0,"","");
		$ld_fechasta=$io_fecha->uf_convert_date_to_db($ls_fechas);  
		$ls_data="";	
		$li_i=0;
		while ((!$io_report->rs_data->EOF)&&($lb_valido))	
		{
			$ls_codper=$io_report->rs_data->fields["codper"];
			$ls_nomber=$io_report->rs_data->fields["nomper"];
			$ls_sexo=$io_report->rs_data->fields["sexper"];
			$ls_apellido=$io_report->rs_data->fields["apeper"];
			$ls_fechaIng=$io_report->rs_data->fields["fecingper"];	
			$ls_desnom=$io_report->rs_data->fields["desnom"];
			$ls_descom=$io_report->rs_data->fields["descom"];	
			$ls_desran=$io_report->rs_data->fields["desran"];
			$li_anoser=number_format($io_report->rs_data->fields["anoservpreper"],0,"","");
			$li_anoserfijo=number_format($io_report->rs_data->fields["anoservprefijo"],0,"","");
			$ls_fechanac=$io_report->rs_data->fields["fecnacper"];
			$ls_fechaadminpub=$io_report->rs_data->fields["fecingadmpubper"];
			$ls_fechajub=$io_report->rs_data->fields["fecjubper"];
			$li_anoperobr=number_format(trim($io_report->rs_data->fields["anoperobr"]),0,"","");
			$li_codtippersss=$io_report->rs_data->fields["codtippersss"];
			
			// Calculo la Edad
			$li_edad=number_format(calcular_anos(strtotime($ls_fechanac),strtotime($ld_fechasta)),0,"","");
			// Calculo los años en la institución
			$li_anoserinst=calcular_anos(strtotime($ls_fechaIng),strtotime($ld_fechasta));
			$li_anoseradmpub=calcular_anos(strtotime($ls_fechaadminpub),strtotime($ld_fechasta));
			$li_anosersumtotal=number_format($li_anoser+$li_anoserinst+$li_anoperobr,0,"","");
			if ($ls_confjub=="1")
			{
				if($li_anosersumtotal>=$li_anomim)
				{
					if(($li_anomax==0)||($li_anosersumtotal<=$li_anomax))
					{
						if (((trim($ls_sexo)=="F")&&($li_edad>=$li_edadf))||((trim($ls_sexo)=="M")&&($li_edad>=$li_edadm)))
						{
							$li_i++;
							$ls_data[$li_i]=array('nro'=>$li_i,'codigo'=>$ls_codper,'nombre'=>$ls_apellido.", ".$ls_nomber,
												 'edad'=>$li_edad,
												 'fechanac'=>$io_funciones->uf_convertirfecmostrar($ls_fechanac),
												 'fecha'=>$io_funciones->uf_convertirfecmostrar($ls_fechaadminpub),
												 'anoser'=>$li_anoser,
												 'fechaing'=>$io_funciones->uf_convertirfecmostrar($ls_fechaIng),
												 'anoserinst'=>$li_anoserinst,'anoobr'=>$li_anoperobr,
												 'totalanios'=>$li_anosersumtotal);	
						}
					}
				}
			}
			else
			{
				if($li_anosersumtotal>=$li_anomax)
				{
					$li_i++;
					$ls_data[$li_i]=array('nro'=>$li_i,'codigo'=>$ls_codper,'nombre'=>$ls_apellido.", ".$ls_nomber,
										 'edad'=>$li_edad,
										 'fechanac'=>$io_funciones->uf_convertirfecmostrar($ls_fechanac),
										 'fecha'=>$io_funciones->uf_convertirfecmostrar($ls_fechaadminpub),
										 'anoser'=>$li_anoser,
										 'fechaing'=>$io_funciones->uf_convertirfecmostrar($ls_fechaIng),
										 'anoserinst'=>$li_anoserinst,'anoobr'=>$li_anoperobr,
										 'totalanios'=>$li_anosersumtotal);	
				}
			}
			$io_report->rs_data->MoveNext();
		}
		if ($ls_data!="")
		{
			uf_print_detalle($ls_data,&$io_pdf);
			unset($la_data);			
		}
		$io_report->DS->resetds("codper");
		if(($lb_valido)&&($ls_data!="")) // Si no ocurrio ningún error
		{
			$io_pdf->ezStopPageNumbers(1,1); // Detenemos la impresión de los números de página
			$io_pdf->ezStream(); // Mostramos el reporte
		}
		else  // Si hubo algún error
		{
			print("<script language=JavaScript>");
			print(" alert('No hay nada que reportar');"); 
			print(" close();");
			print("</script>");		
		}
		unset($io_pdf);
	}
	unset($io_report);
	unset($io_funciones);
	unset($io_fun_nomina);
?> 