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
	ini_set('memory_limit','64M');
	ini_set('max_execution_time','0');

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_seguridad($as_titulo,$as_titulo2)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_insert_seguridad
		//		   Access: private 
		//	    Arguments: as_titulo // T�tulo del reporte
		//    Description: funci�n que guarda la seguridad de quien gener� el reporte
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 22/02/2007 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_nomina;
		$ls_descripcion="Gener� el Reporte ".$as_titulo." ".$as_titulo2;
		$lb_valido=$io_fun_nomina->uf_load_seguridad_reporte("SNR","sigesp_snorh_r_retencion_arc.php",$ls_descripcion);
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezado_pagina
		//		   Access: private 
		//	    Arguments: as_titulo // T�tulo del Reporte
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funci�n que imprime los encabezados por p�gina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 22/02/2007
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$director='<b>'.$_SESSION['director']['4'].'</b>';
		//$cargo=$_SESSION['director']['1'];
		global $ls_tiporeporte;
		if($ls_tiporeporte==1)
		{
			$ls_titulobs="Bol�vares Fuertes";
		}
		else
		{
			$ls_titulobs="Bol�vares";
		}
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->line(50,40,555,40);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],50,720,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,730,11,$as_titulo); // Agregar el t�tulo
		$li_tm=$io_pdf->getTextWidth(11,$ls_titulobs);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,720,10,$ls_titulobs); // Agregar el t�tulo

		$io_pdf->addText(512,750,8,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(518,743,7,date("h:i a")); // Agregar la Hora
		$io_pdf->line(360,110,515,110);
		$io_pdf->addText(390,100,8,"AGENTE DE RETENCI�N"); 
		$io_pdf->addText(390,90,8,$director); 
		$io_pdf->addText(367,80,8,"DIRECTOR DE RECURSOS HUMANOS");
		$io_pdf->addText(340,70,8,"Gaceta Oficial Nro. 39.365 de fecha 10 de febrero de 2010");
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezado_pagina
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera($as_nacper,$as_cedper,$as_rifper,$as_nomper,$ls_codubifis,$ls_desubifis,$as_titulo2,&$io_cabecera,&$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: private 
		//	    Arguments: as_nacper // nacionalidad del Personal
		//	    		   as_cedper // C�dula del Personal
		//	   			   as_nomper // Nombre del Personal
		//	    		   io_cabecera // objeto cabecera
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funci�n que imprime la cabecera por concepto
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 22/02/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_pdf->saveState();
		// Periodo de Retenci�n
		$io_pdf->setColor(0.9,0.9,0.9);
		$io_pdf->filledRectangle(50,700,500,$io_pdf->getFontHeight(11));
		$io_pdf->setColor(0,0,0);
		$li_tm=$io_pdf->getTextWidth(9,'RELACI�N DE INGRESOS Y RETENCIONES');
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,703,9,'<b>RELACI�N DE INGRESOS Y RETENCIONES</b>'); // Agregar el t�tulo
		$li_tm=$io_pdf->getTextWidth(8,$as_titulo2);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,690,8,$as_titulo2); // Agregar el t�tulo
		// Datos de Trabajador
		$io_pdf->setColor(0.9,0.9,0.9);
		$io_pdf->filledRectangle(50,670,500,$io_pdf->getFontHeight(12));
		$io_pdf->setColor(0,0,0);
		$li_tm=$io_pdf->getTextWidth(9,'DATOS DEL TRABAJADOR');
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,673,9,'<b>DATOS DEL TRABAJADOR</b>'); // Agregar el t�tulo
		$io_pdf->setColor(0.9,0.9,0.9);
		$io_pdf->filledRectangle(51,650,500,$io_pdf->getFontHeight(11));
		$io_pdf->setColor(0,0,0);
		$la_data[0]=array('nombre'=>'APELLIDOS Y NOMBRES','cedula'=>'C�DULA DE IDENTIDAD','rif'=>'RIF.' );
		$la_data[1]=array('nombre'=>$as_nomper,'cedula'=>number_format($as_cedper,0,'','.'),'rif'=>$as_rifper);
		$la_columnas=array('nombre'=>'',
						   'cedula'=>'',
						   'rif'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama�o de Letras
						 'titleFontSize' => 12,  // Tama�o de Letras de los t�tulos
						 'showLines'=>2, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('nombre'=>array('justification'=>'center','width'=>280), // Justificaci�n y ancho de la columna
						 			   'cedula'=>array('justification'=>'center','width'=>120), // Justificaci�n y ancho de la columna
						 			   'rif'=>array('justification'=>'center','width'=>100))); // Justificaci�n y ancho de la columna
		$io_pdf->ezSetDy(-25);
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		// Datos del Funcionario Autorizado
		$io_pdf->setColor(0.9,0.9,0.9);
		$io_pdf->filledRectangle(50,615,500,$io_pdf->getFontHeight(12));
		$io_pdf->setColor(0,0,0);
		$li_tm=$io_pdf->getTextWidth(9,'DATOS DEL FUNCIONARIO AUTORIZADO PARA HACER LA RETENCI�N');
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,618,9,'<b>DATOS DEL FUNCIONARIO AUTORIZADO PARA HACER LA RETENCI�N</b>'); // Agregar el t�tulo
		$io_pdf->setColor(0.9,0.9,0.9);
		$io_pdf->filledRectangle(51,595,500,$io_pdf->getFontHeight(11.3));
		$io_pdf->setColor(0,0,0);
		$director=$_SESSION['director']['4'];
		$cedula=number_format($_SESSION['director']['2'],0,'','.');
		$rif=$_SESSION['director']['5'];
		$la_data[0]=array('nombre'=>'APELLIDOS Y NOMBRES','cedula'=>'C�DULA DE IDENTIDAD','rif'=>'RIF.' );
		$la_data[1]=array('nombre'=>$director,'cedula'=>$cedula,'rif'=>$rif);
		$la_columnas=array('nombre'=>'',
						   'cedula'=>'',
						   'rif'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama�o de Letras
						 'titleFontSize' => 12,  // Tama�o de Letras de los t�tulos
						 'showLines'=>2, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('nombre'=>array('justification'=>'center','width'=>280), // Justificaci�n y ancho de la columna
						 			   'cedula'=>array('justification'=>'center','width'=>120), // Justificaci�n y ancho de la columna
						 			   'rif'=>array('justification'=>'center','width'=>100))); // Justificaci�n y ancho de la columna
		$io_pdf->ezSetDy(-28);
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		// Datos del Agente de Retenci�n
	    $io_pdf->setColor(0.9,0.9,0.9);
        $io_pdf->filledRectangle(50,561,500,$io_pdf->getFontHeight(12));
        $io_pdf->setColor(0,0,0);
		$li_tm=$io_pdf->getTextWidth(9,'DATOS DEL AGENTE DE RETENCI�N');
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,564,9,'<b>DATOS DEL AGENTE DE RETENCI�N</b>'); // Agregar el t�tulo
	    $io_pdf->setColor(0.9,0.9,0.9);
        $io_pdf->filledRectangle(51,541,500,$io_pdf->getFontHeight(11));
        $io_pdf->setColor(0,0,0);
		$la_data[0]=array('nombre'=>'PERSONA JUR�DICA -- NOMBRE');
		$la_data[1]=array('nombre'=>$_SESSION["la_empresa"]["nombre"]);
		$la_columnas=array('nombre'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama�o de Letras
						 'titleFontSize' => 12,  // Tama�o de Letras de los t�tulos
						 'showLines'=>2, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('nombre'=>array('justification'=>'center','width'=>500))); // Justificaci�n y ancho de la columna
		$io_pdf->ezSetDy(-28);
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
	    $io_pdf->setColor(0.9,0.9,0.9);
        $io_pdf->filledRectangle(51,514,500,$io_pdf->getFontHeight(11.3));
        $io_pdf->setColor(0,0,0);
		$la_data[0]=array('direccion'=>'DIRECCI�N DEL AGENTE DE RETENCI�N','rif'=>'RIF.');
		$la_data[1]=array('direccion'=>$_SESSION["la_empresa"]["direccion"],'rif'=>$_SESSION["la_empresa"]["rifemp"]);
		$la_columnas=array('direccion'=>'','rif'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama�o de Letras
						 'titleFontSize' => 12,  // Tama�o de Letras de los t�tulos
						 'showLines'=>2, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('direccion'=>array('justification'=>'center','width'=>430),
						 			   'rif'=>array('justification'=>'center','width'=>70))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
	    $io_pdf->setColor(0.9,0.9,0.9);
        $io_pdf->filledRectangle(51,488,500,$io_pdf->getFontHeight(11));
        $io_pdf->setColor(0,0,0);
		$la_data[0]=array('telefono'=>'TEL�FONO','zona'=>'ZONA POSTAL','ciudad'=>'CIUDAD','estado'=>'ESTADO / DISTRITO');
		$la_data[1]=array('telefono'=>$_SESSION["la_empresa"]["telemp"],'zona'=>$_SESSION["la_empresa"]["zonpos"],
		                  'ciudad'=>$_SESSION["la_empresa"]["ciuemp"],'estado'=>$_SESSION["la_empresa"]["estemp"]);
		$la_columnas=array('telefono'=>'','zona'=>'','ciudad'=>'','estado'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama�o de Letras
						 'titleFontSize' => 12,  // Tama�o de Letras de los t�tulos
						 'showLines'=>2, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('telefono'=>array('justification'=>'center','width'=>130),
						 			   'zona'=>array('justification'=>'center','width'=>100),
									   'ciudad'=>array('justification'=>'center','width'=>135),
						 			   'estado'=>array('justification'=>'center','width'=>135))); // Justificaci�n y ancho de la columna

		$io_pdf->addText(50,30,8,"Ubicaci�n F�sica: ".$ls_codubifis." - ".$ls_desubifis);//Agregar ubicaci�n f�sica

		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_cabecera,'all');
	}// end function uf_print_cabecera
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data,&$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: la_data // arreglo de informaci�n
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funci�n que imprime el detalle por concepto
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 04/08/2006 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	    $io_pdf->setColor(0.9,0.9,0.9);
        $io_pdf->filledRectangle(51,441,500,$io_pdf->getFontHeight(21));
        $io_pdf->setColor(0,0,0);
		$io_pdf->ezSetY(465);
		$la_columnas=array('mes'=>'<b>MES</b>',
						   'arc'=>'<b>DEVENGADO         </b>',
						   'arcacum'=>'<b>DEVENGADO         ACUMULADO         </b>',
						   'porcentaje'=>'<b>PORCENTAJE % </b>',
						   'retencion'=>'<b>RETENIDO         </b>',
						   'retencionacum'=>'<b>RETENIDO            ACUMULADO         </b>');
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 8, // Tama�o de Letras
						 'titleFontSize' => 12,  // Tama�o de Letras de los t�tulos
						 'showLines'=>1, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('mes'=>array('justification'=>'center','width'=>30), // Justificaci�n y ancho de la columna
						 			   'arc'=>array('justification'=>'right','width'=>100), // Justificaci�n y ancho de la columna
						 			   'arcacum'=>array('justification'=>'right','width'=>100), // Justificaci�n y ancho de la columna
						 			   'porcentaje'=>array('justification'=>'right','width'=>85), // Justificaci�n y ancho de la columna
						 			   'retencion'=>array('justification'=>'right','width'=>85), // Justificaci�n y ancho de la columna
						 			   'retencionacum'=>array('justification'=>'right','width'=>100))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
	}// end function uf_print_detalle
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle_aporte($la_data,&$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle_aporte
		//		   Access: private 
		//	    Arguments: la_data // arreglo de informaci�n
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funci�n que imprime el detalle por concepto
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 07/08/2006 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_pdf->ezSetDy(-10);
		$la_columnas=array('codigo'=>'<b>C�digo</b>',
						   'nombre'=>'<b>                                   Nombre</b>',
						   'monto'=>'<b>Monto               </b>');
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 8, // Tama�o de Letras
						 'titleFontSize' => 10,  // Tama�o de Letras de los t�tulos
						 'showLines'=>1, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>360, // Ancho de la tabla
						 'maxWidth'=>360, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('codigo'=>array('justification'=>'center','width'=>60), // Justificaci�n y ancho de la columna
						 			   'nombre'=>array('justification'=>'left','width'=>200), // Justificaci�n y ancho de la columna
						 			   'monto'=>array('justification'=>'right','width'=>100))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'Aporte Patronal',$la_config);
	}// end function uf_print_detalle_aporte
	//-----------------------------------------------------------------------------------------------------------------------------------

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
	//----------------------------------------------------  Par�metros del encabezado  -----------------------------------------------
	$ls_titulo="<b>DIRECCI�N GENERAL DE RECURSOS HUMANOS</b>";
	//--------------------------------------------------  Par�metros para Filtar el Reporte  -----------------------------------------
	$li_total=$io_fun_nomina->uf_obtenervalor_get("total","0");
	for($li_i=1;$li_i<=$li_total;$li_i++)
	{
		$la_nominas[$li_i]=$io_fun_nomina->uf_obtenervalor_get("codnom".$li_i,"");
	}
	$ls_codperdes      = $io_fun_nomina->uf_obtenervalor_get("codperdes","");
	$ls_codperhas      = $io_fun_nomina->uf_obtenervalor_get("codperhas","");
	$ls_ano=$io_fun_nomina->uf_obtenervalor_get("ano","");
	$ls_conceptoaporte=$io_fun_nomina->uf_obtenervalor_get("conceptoaporte","");
	$ls_excluir=$io_fun_nomina->uf_obtenervalor_get("excluir","");
	$ls_orden=$io_fun_nomina->uf_obtenervalor_get("orden","1");
	$ls_titulo2="PER�ODO:				 01/01/".$ls_ano." 		AL		 31/12/".$ls_ano;
	$ls_tiporeporte=$io_fun_nomina->uf_obtenervalor_get("tiporeporte",0);
	global $ls_tiporeporte;
	if($ls_tiporeporte==1)
	{
		require_once("sigesp_snorh_class_reportbsf.php");
		$io_report=new sigesp_snorh_class_reportbsf();
	}
	//--------------------------------------------------------------------------------------------------------------------------------
	$lb_valido=uf_insert_seguridad($ls_titulo,$ls_titulo2); // Seguridad de Reporte
	if($lb_valido)
	{
		$lb_valido=$io_report->uf_retencionarc_personal($la_nominas,$li_total,$ls_ano,$ls_orden,$ls_codperdes,$ls_codperhas); // Cargar el DS con los datos del reporte
	}

	if($lb_valido==false) // Existe alg�n error � no hay registros
	{
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');"); 
		print(" close();");
		print("</script>");
	}
	else  // Imprimimos el reporte
	{
		error_reporting(E_ALL);
		set_time_limit(1800);
		$io_pdf=new Cezpdf('LETTER','portrait'); // Instancia de la clase PDF
		$io_pdf->selectFont('../../shared/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
		$io_pdf->ezSetCmMargins(3.6,2.5,3,3); // Configuraci�n de los margenes en cent�metros
		uf_print_encabezado_pagina($ls_titulo,$io_pdf); // Imprimimos el encabezado de la p�gina
		$io_pdf->ezStartPageNumbers(550,50,10,'','',1); // Insertar el n�mero de p�gina
		$li_totrow=$io_report->rs_data->RecordCount();
		$lb_cabecera=false;
		$lb_print=false;
		while(!$io_report->rs_data->EOF)
		{			
			$ls_codper=$io_report->rs_data->fields["codper"];
			$ls_nomper=$io_report->rs_data->fields["apeper"].", ".$io_report->rs_data->fields["nomper"];
			$ls_nacper=$io_report->rs_data->fields["nacper"];
			$ls_cedper=$io_report->rs_data->fields["cedper"];
			$ls_codubifis=$io_report->rs_data->fields["codubifis"];
			$ls_desubifis=$io_report->rs_data->fields["desubifis"];
                        $ls_rifper=$io_report->rs_data->fields["rifper"];

			$lb_valido=$io_report->uf_retencionarc_meses($ls_codper,$la_nominas,$li_total,$ls_ano); // Obtenemos el detalle del reporte
			if($lb_valido)
			{
				$li_totrow_det=$io_report->rs_detalle->RecordCount();
				$li_arcacum=0;
				$li_islracum=0;
				$lb_arc=false;
				$li_s=0;
				while(!$io_report->rs_detalle->EOF)
				{
					$ls_codisr=$io_report->rs_detalle->fields["codisr"];
					//echo $io_report->rs_detalle->fields["porisr"];die();
					//$li_porisr=$io_fun_nomina->uf_formatonumerico($io_report->rs_detalle->fields["porisr"]*100);
					$li_porisr=$io_fun_nomina->uf_formatonumerico($io_report->rs_detalle->fields["porisr"]);
					$retenido=($io_report->rs_detalle->fields["arc"]*$io_report->rs_detalle->fields["porisr"])/100;
					$li_arcacum=$li_arcacum+abs($io_report->rs_detalle->fields["arc"]);
					$li_islracum=$retenido+$li_islracum;
					$li_arc=$io_fun_nomina->uf_formatonumerico(abs($io_report->rs_detalle->fields["arc"]));
					$li_islr=$io_fun_nomina->uf_formatonumerico(abs($io_report->rs_detalle->fields["islr"]));
					$li_arc_acumulado=$io_fun_nomina->uf_formatonumerico($li_arcacum);
					$li_isl_acumulado=$io_fun_nomina->uf_formatonumerico($li_islracum);
					$ls_mes=strtoupper(substr($io_fecha->uf_load_nombre_mes($ls_codisr),0,3));

					
					$retenido=number_format($retenido,2,',','.');
					if($li_arc<>"0,00")
					{
						$lb_arc=true;
					}
					$la_data[$li_s]=array('mes'=>$ls_mes,'arc'=>$li_arc,'arcacum'=>$li_arc_acumulado,'porcentaje'=>$li_porisr,
										  'retencion'=>$retenido,'retencionacum'=>$li_isl_acumulado);
					$li_s++;
					$io_report->rs_detalle->MoveNext();
				}
				$io_report->io_sql->free_result($io_report->rs_detalle);
				if($li_totrow_det==0)
				{
					$lb_arc=false;
					for($li_s=1;$li_s<=12;$li_s++)
					{
						$ls_codisr=str_pad($li_s,2,"0",0);
						$li_porisr=$io_fun_nomina->uf_formatonumerico(0);
						$li_arcacum=0;
						$li_islracum=0;
						$li_arc=$io_fun_nomina->uf_formatonumerico(0);
						$li_islr=$io_fun_nomina->uf_formatonumerico(0);
						$li_arc_acumulado=$io_fun_nomina->uf_formatonumerico(0);
						$li_isl_acumulado=$io_fun_nomina->uf_formatonumerico(0);
						$ls_mes=strtoupper(substr($io_fecha->uf_load_nombre_mes($ls_codisr),0,3));
						$la_data[$li_s]=array('mes'=>$ls_mes,'arc'=>$li_arc,'arcacum'=>$li_arc_acumulado,'porcentaje'=>$li_porisr,
											  'retencion'=>$li_islr,'retencionacum'=>$li_isl_acumulado);
					}
				}
				if($ls_excluir=="1")
				{
					if($lb_arc)
					{				
						$io_cabecera=$io_pdf->openObject(); // Creamos el objeto cabecera
						uf_print_cabecera($ls_nacper,$ls_cedper,$ls_rifper,$ls_nomper,$ls_codubifis,$ls_desubifis,$ls_titulo2,&$io_cabecera,&$io_pdf); // Imprimimos la cabecera del registro
						$lb_cabecera=true;
						$lb_print=true;
						uf_print_detalle($la_data,$io_pdf); // Imprimimos el detalle 
						unset($la_data);
					}
				}
				else
				{				
					$io_cabecera=$io_pdf->openObject(); // Creamos el objeto cabecera
					uf_print_cabecera($ls_nacper,$ls_cedper,$ls_rifper,$ls_nomper,$ls_codubifis,$ls_desubifis,$ls_titulo2,&$io_cabecera,&$io_pdf); // Imprimimos la cabecera del registro
					$lb_cabecera=true;
					$lb_print=true;
					uf_print_detalle($la_data,$io_pdf); // Imprimimos el detalle 
					unset($la_data);
					$lb_arc=true;
				}
				if(($lb_valido)&&($lb_arc))// Si no ocurrio ning�n error
				{
					if($ls_conceptoaporte=="1") // Si solicita que se muestren los conceptos de aporte
					{
						$lb_valido=$io_report->uf_retencionarc_aporte($ls_codper,$la_nominas,$li_total,$ls_ano);
						$li_totrow_det=$io_report->rs_detalle->RecordCount();
						$li_s=0;
						while(!$io_report->rs_detalle->EOF)
						{
							$ls_codconc=$io_report->rs_detalle->fields["codconc"];
							$ls_nomcon=$io_report->rs_detalle->fields["nomcon"];
							$li_monto=$io_fun_nomina->uf_formatonumerico(abs($io_report->rs_detalle->fields["monto"]));
							$la_data[$li_s]=array('codigo'=>$ls_codconc,'nombre'=>$ls_nomcon,'monto'=>$li_monto);
							$li_s++;
							$io_report->rs_detalle->MoveNext();
						}
						$io_report->io_sql->free_result($io_report->rs_detalle);
						if($li_totrow_det>0)
						{
							uf_print_detalle_aporte($la_data,$io_pdf);
						}
					}
				}
				if($lb_cabecera)
				{						
					$io_pdf->stopObject($io_cabecera); // Detener el objeto cabecera
					$lb_cabecera=false;
				}
				if(($li_i<$li_totrow)&&($lb_arc))
				{
					$io_pdf->ezNewPage(); // Insertar una nueva p�gina
				}
				unset($io_cabecera);
				unset($la_data);
			}
			$io_report->rs_data->MoveNext();
		}
		$io_report->io_sql->free_result($io_report->rs_data);
		if($lb_valido) // Si no ocurrio ning�n error
		{
			if($lb_print)
			{
				$io_pdf->ezStopPageNumbers(1,1); // Detenemos la impresi�n de los n�meros de p�gina
				$io_pdf->ezStream(); // Mostramos el reporte
			}
			else
			{
				print("<script language=JavaScript>");
				print(" alert('no hay nada que reportar.');"); 
				print(" close();");
				print("</script>");		
			}
		}
		else // Si hubo alg�n error
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
	unset($io_fun_nomina);
	unset($io_fecha);
?>