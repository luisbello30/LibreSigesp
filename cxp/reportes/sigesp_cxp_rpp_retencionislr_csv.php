<?php
	
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//  REPORTE: Retencion de ISLR
	//  ORGANISMO: Fundación Misión Cultura
	//  Modificación: Lic. Alejandro Senges
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
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

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_seguridad($as_titulo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_insert_seguridad
		//		   Access: private
		//	    Arguments: as_titulo // Título del reporte
		//    Description: función que guarda la seguridad de quien generó el reporte
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 14/07/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_cxp;

		$ls_descripcion="Generó el Reporte ".$as_titulo;
		$lb_valido=$io_fun_cxp->uf_load_seguridad_reporte("CXP","sigesp_cxp_r_retencionesiva.php",$ls_descripcion);
		return $lb_valido;
	}// end function uf_insert_seguridad
	//-----------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezadopagina($as_titulo,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 14/07/2007
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		/*
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->line(50,40,960,40);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],47,539,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$io_pdf->addText(910,595,8,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(916,585,7,date("h:i a")); // Agregar la Hora
		$io_pdf->setStrokeColor(0,0,0);
                $io_pdf->Rectangle(150,540,800,40);
		$io_pdf->addText(240,555,13,"<b>".$as_titulo."</b>"); // Agregar el t�ulo
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
		*/
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera()
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private
		//	    Arguments: as_agenteret // Nombre del Agente de retención
		//	    		   as_rifagenteret // Rif del Agente de retención
		//	    		   as_perfiscal // Período fiscal
		//	    		   as_codsujret // Código del Sujeto a retención
		//	    		   as_nomsujret // Nombre del Sujeto a retención
		//	    		   as_diragenteret // Dirección del agente de retención
		//	    		   as_numcon // Número de Comprobante
		//	    		   ad_fecrep // Fecha del comprobante
		//	    		   ai_estcmpret // estatus del comprobante
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 14/07/2007
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		

	}// end function uf_print_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------

          //--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_firmas(&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_firmas
		//		   Access: private
		//	    Arguments: io_pdf // Instancia de objeto pdf
		//    Description: funci�n que imprime el detalle por recepci�n
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creaci�n: 05/07/2007
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		/*		
		$la_data[0]=array('firma1'=>'','firma2'=>'');
		$la_data[1]=array('firma1'=>'','firma2'=>'');
		$la_data[2]=array('firma1'=>'____________________________','firma2'=>'____________________________');
		$la_data[3]=array('firma1'=>'AGENTE DE RETENCION','firma2'=>'BENEFICIARIOS');
		$la_data[4]=array('firma1'=>'','firma2'=>'');
		$la_columna=array('firma1'=>'','firma2'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 10, // Tama�o de Letras
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'width'=>500, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
				 		 'cols'=>array('firma1'=>array('justification'=>'center','width'=>250), // Justificaci�n y ancho de la columna
						 			   'firma2'=>array('justification'=>'center','width'=>250))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);


		$io_pdf->rectangle(450,60,110,90);
		$io_pdf->addText(485,66,10,'<b>SELLO</b>');
		*/
	}// end function uf_print_firmas
	//--------------------------------------------------------------------------------------------------------------------------------
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private
		//	    Arguments: la_data // Arreglo de datos a imprimir
		//	    		   ai_totconiva // Total con iva
		//	    		   ai_totsiniva // Total sin iva
		//	    		   ai_totbasimp // Total de la base imponible
		//	    		   ai_totmonimp // Total monto imponible
		//	    		   ai_totivaret // Total iva retenido
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 14/07/2007
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		/*
		$la_data1[1]=array('titulo'=>'');
		$la_columna=array('titulo'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'showLines'=>0, // Mostrar Letras
						 'shaded'=>0, // Sombra entre lineas
						 'xOrientation'=>'center', // Orientacion de la tabla
						 'width'=>900, // Ancho de la tabla
						 'justification'=>'center', // Ancho de la tabla
						 'maxWidth'=>900,
						 'cols'=>array('titulo'=>array('justification'=>'center','width'=>900))); // Ancho Minimo de la tabla
		$io_pdf->ezTable($la_data1,$la_columna,'',$la_config);
		
		unset($la_data1);
		unset($la_columna);
		unset($la_config);
		$la_columna=array('numope'=>'<b>Oper Nro.</b>',
						  'fecfac'=>'<b>Fecha de la Factura</b>',
						  'numfac'=>'<b>Numero de Factura</b>',
  						  'numcom'=>'<b>Num. Ctrol de Factura</b>',
                                                  'baseimp'=>'<b>Base imponible</b>',
						  'desact'=>'<b>Concepto Retencion</b>',
						  'obsconret'=>'<b>% Aplicado</b>',
						  'ivaret'=>'<b>Monto Retenido</b>');
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>900, // Ancho de la tabla
						 'maxWidth'=>900, // Ancho Mínimo de la tabla
						 'xPos'=>500, // Orientación de la tabla
						 'cols'=>array('numope'=>array('justification'=>'center','width'=>60), // Justificacion y ancho de la columna
						 			   'fecfac'=>array('justification'=>'center','width'=>60), // Justificacion y ancho de la columna
						 			   'numfac'=>array('justification'=>'center','width'=>80), // Justificacion y ancho de la columna
									   'numcom'=>array('justification'=>'center','width'=>80), // Justificacion y ancho de la columna
                                                                           'baseimp'=>array('justification'=>'center','width'=>60),
									   'desact'=>array('justification'=>'center','width'=>320),
						 			   'obsconret'=>array('justification'=>'center','width'=>45),
  						 			   'ivaret'=>array('justification'=>'center','width'=>70)));
		$io_pdf->ezSetDy(-2);
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		/*$la_data[1]=array('name'=>'','name1'=>$ai_totconiva,'name2'=>$ai_totsiniva,'name3'=>$ai_totbasimp,
		                  'name4'=>'','name5'=>$ai_totmonimp,'name6'=>$ai_totivaret);
		$la_columna=array('name'=>'','name1'=>'','name2'=>'','name3'=>'','name4'=>'','name5'=>'','name6'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' =>8,    // Tamaño de Letras
						 'showLines'=>0,    // Mostrar Lineas
						 'shaded'=>0,       // Sombra entre Lineas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>482,
						 'yPos'=>734,       // Orientacion de la tabla
						 'width'=>200,
						 'xOrientation'=>'right',      // Ancho de la tabla
						 'maxWidth'=>200,
						 'cols'=>array('name'=>array('justification'=>'center','width'=>50), // Justificacion y ancho de la columna
						               'name1'=>array('justification'=>'center','width'=>90), // Justificacion y ancho de la columna
						 			   'name2'=>array('justification'=>'center','width'=>70), // Justificacion y ancho de la columna
						 			   'name3'=>array('justification'=>'center','width'=>70), // Justificacion y ancho de la columna
									   'name4'=>array('justification'=>'center','width'=>45), // Justificacion y ancho de la columna
									   'name5'=>array('justification'=>'center','width'=>70), // Justificacion y ancho de la columna
   						 			   'name6'=>array('justification'=>'center','width'=>70)));
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);*/


		
		foreach($la_data as $valor){
			foreach($valor as $columna){
				echo $columna;
			}
			echo "\n";
		}


	}// end function uf_print_detalle
	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------

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

	include("bd/conector_bd.php");
	include("bd/sql_list.php");

	if($ls_tiporeporte==1)
	{
		require_once("sigesp_cxp_class_reportbsf.php");
		$io_report=new sigesp_cxp_class_reportbsf();
	}
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	
	$ls_titulo="COMPROBANTE DE RETENCION DEL IMPUESTO SOBRE LA RENTA";
	
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_comprobantes=$io_fun_cxp->uf_obtenervalor_get("comprobantes","");
	$ls_agenteret=$_SESSION["la_empresa"]["nombre"];
	$ls_rifagenteret=$_SESSION["la_empresa"]["rifemp"];
	$ls_diragenteret=$_SESSION["la_empresa"]["direccion"];
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
			//$io_pdf=new Cezpdf('LEGAL','landscape');
			//$io_pdf->selectFont('../../shared/ezpdf/fonts/Helvetica.afm');
			//$io_pdf->ezSetCmMargins(3.5,3,3,3);
			$lb_valido=true;

			header('Content-type: application/vnd.ms-excel');
			header("Content-Disposition: attachment; filename=archivo.csv");
			header("Pragma: no-cache");
			header("Expires: 0");

			echo '"RIF","Factura","Control","Cod Conc","Base","%","Monto Retenido"';
			echo "\n";

			for ($li_z=0;($li_z<$li_totrow)&&($lb_valido);$li_z++)
			{
				//uf_print_encabezadopagina($ls_titulo,&$io_pdf);
	$ls_numcom=$la_datos[$li_z];
				$lb_valido=$io_report->uf_retencionesislr_proveedor($ls_numcom);
				if($lb_valido)
				{
					$li_total=$io_report->DS->getRowCount("numcom");
					for($li_i=1;$li_i<=$li_total;$li_i++)
					{
						$ls_numcon=$io_report->DS->data["numcom"][$li_i];
						$ls_codret=$io_report->DS->data["codret"][$li_i];
						$ls_fecrep=$io_funciones->uf_convertirfecmostrar($io_report->DS->data["fecrep"][$li_i]);
						$ls_perfiscal=$io_report->DS->data["perfiscal"][$li_i];
						$ls_codsujret=$io_report->DS->data["codsujret"][$li_i];
						$ls_nomsujret=$io_report->DS->data["nomsujret"][$li_i];
						$ls_rif_bene=$io_report->DS->data["rifben"][$li_i];
                                                $ls_rif_prov=$io_report->DS->data["rifpro"][$li_i];
						$ls_dirsujret=$io_report->DS->data["dirsujret"][$li_i];
						$li_estcmpret=$io_report->DS->data["estcmpret"][$li_i];
                                                
					}
					uf_print_cabecera($ls_agenteret,$ls_rifagenteret,$ls_perfiscal,$ls_codsujret,$ls_nomsujret,$ls_rif_bene,
					                  $ls_rif_prov,$ls_diragenteret,$ls_numcon,$ls_fecrep,$li_estcmpret,&$io_pdf);
					$lb_valido=$io_report->uf_retencionesislr_detalle($ls_numcom);
					if($lb_valido)
					{
	
						$li_totalconiva = 0;
						$li_totalsiniva = 0;
						$li_totalbaseimp = 0;
						$li_totalmontoimp = 0;
						$li_totalivaret = 0;
						$li_total=$io_report->ds_detalle->getRowCount("numfac");

						

						for($li_i=1;$li_i<=$li_total;$li_i++)
						{
							$ls_numope=$io_report->ds_detalle->data["numope"][$li_i];
							$ls_numfac=$io_report->ds_detalle->data["numfac"][$li_i];
							$ls_numref=$io_report->ds_detalle->data["numcon"][$li_i];
							$ld_fecfac=$io_funciones->uf_convertirfecmostrar($io_report->ds_detalle->data["fecfac"][$li_i]);
							$li_siniva=$io_report->ds_detalle->data["totcmp_sin_iva"][$li_i];
							$li_coniva=$io_report->ds_detalle->data["totcmp_con_iva"][$li_i];
							$li_baseimp=$io_report->ds_detalle->data["basimp"][$li_i];
							$li_porimp=$io_report->ds_detalle->data["porimp"][$li_i];
							$li_totimp=$io_report->ds_detalle->data["totimp"][$li_i];
							$li_ivaret=$io_report->ds_detalle->data["iva_ret"][$li_i];
							$ls_numdoc=$io_report->ds_detalle->data["numdoc"][$li_i];
							$ls_tiptrans=$io_report->ds_detalle->data["tiptrans"][$li_i];
							$ls_numnotdeb=$io_report->ds_detalle->data["numnd"][$li_i];
							$ls_numnotcre=$io_report->ds_detalle->data["numnc"][$li_i];
							$li_monto=$li_baseimp + $li_totimp;
							$li_totdersiniva= abs($li_coniva - $li_monto);
							$ls_numfacafec="";
							$li_totalconiva=$li_totalconiva + $li_coniva;
							$li_totalsiniva=$li_totalsiniva + $li_totdersiniva;
							$li_totalbaseimp=$li_totalbaseimp + $li_baseimp ;
							$li_totalmontoimp=$li_totalmontoimp + $li_totimp;
							$li_totalivaret=$li_totalivaret + $li_ivaret;
							$li_totdersiniva=number_format($li_totdersiniva,2,",",".");
							$li_siniva=number_format($li_siniva,2,",",".");
							$li_coniva=number_format($li_coniva,2,",",".");
							$li_baseimp=number_format($li_baseimp,2,",",".");
							$li_porimp=number_format($li_porimp,2,",",".");
							$li_totimp=number_format($li_totimp,2,",",".");
							$li_ivaret=number_format($li_ivaret,2,",",".");

							$arr_datos[0] = $ls_codsujret;
							$arr_datos[1] = $ls_numcon;
							$arr_datos[2] = $ls_numfac;

							$conexion = conectar_bd($conn_string);

							$res_rs = ejecutar_bd($conexion,$sql1,$arr_datos,NULL,1); # SQL LIST 00001
							$arr_rs = pg_fetch_array($res_rs);


							desconectar_bd($conexion);

							$la_data[$li_i]=array('numope'=>$arr_rs[0],'fecfac'=>$arr_rs[1],'numfac'=>$arr_rs[2],'numcom'=>$arr_rs[3],
										'baseimp'=>$arr_rs[4],'desact'=>$arr_rs[5],'obsconret'=>$arr_rs[6],'ivaret'=>$arr_rs[7]);


						 	}

							

						  $li_totalconiva= number_format($li_totalconiva,2,",",".");
						  $li_totalsiniva= number_format($li_totalsiniva,2,",",".");
  						  $li_totalbaseimp= number_format($li_totalbaseimp,2,",",".");
  						  $li_totalmontoimp= number_format($li_totalmontoimp,2,",",".");
						  $li_totalivaret= number_format($li_totalivaret,2,",",".");
						  uf_print_detalle($la_data,$li_totalconiva,$li_totalsiniva,$li_totalbaseimp,$li_totalmontoimp,$li_totalivaret,&$io_pdf);
						  unset($la_data);
					}
				}
				if($li_z<($li_totrow-1))
				{
					$io_pdf->ezNewPage();
				}
			}
                        uf_print_firmas($io_pdf);
			if($lb_valido) // Si no ocurrio ningún error
			{

				//$io_pdf->ezStopPageNumbers(1,1); // Detenemos la impresión de los números de página
				//$io_pdf->ezStream(); // Mostramos el reporte
			}
			else  // Si hubo algún error
			{
				print("<script language=JavaScript>");
				print(" alert('Ocurrio un error al generar el reporte. Intente de Nuevo');");
				print(" close();");
				print("</script>");
			}
			//unset($io_pdf);
		}
	}
	unset($io_report);
	unset($io_funciones);
	unset($io_fun_cxp);
?> 
