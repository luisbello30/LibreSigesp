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
  	ini_set('memory_limit','1024M');
	ini_set('max_execution_time ','0');
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,$as_procede,$ad_fecha,$as_fuentefin="",$as_denuac='',&$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezado_pagina
		//		    Acess: private
		//	    Arguments: as_titulo // T�tulo del Reporte
		//	    		   io_pdf    // Instancia de objeto pdf
		//    Description: Funci�n que imprime los encabezados por p�gina
		//	   Creado Por: Ing. N�stor Falcon
		// Fecha Creaci�n: 18/05/2007.
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		$tm=306-($li_tm/2);
		$io_pdf->rectangle(20,690,570,90);
		$io_pdf->addText(30,760,8,"ORGANO:{$_SESSION['la_empresa']['nombre']}");
		$io_pdf->addText(383,750,8,utf8_decode("<b>PÁGINA No.</b>"));
		$io_pdf->addText(510,745,8,"COD.");
		$io_pdf->addText(25,720,7,"FECHA:".$ad_fecha);
		$io_pdf->addText(25,710,7,"FUENTE DE FINANCIAMIENTO: ".$as_fuentefin);
                $io_pdf->addText(25,700,7,"UNIDAD ADMINISTRADORA: ".$as_denuac);
		//$io_pdf->addText(180,730,10,$as_titulo);
		$io_pdf->addText($tm,730,10,$as_titulo);
		$io_pdf->rectangle(20,625,570,60);
		$io_pdf->addText(30,660,7,"INSUBSISTENCIA");$io_pdf->rectangle(95,658,10,10);
		$io_pdf->addText(130,660,7,utf8_decode("REDUCCIÓN"));$io_pdf->rectangle(180,658,10,10);
		$io_pdf->addText(220,675,7,"<b>RECURSOS ADICIONALES</b>");
		$io_pdf->addText(223,660,7,utf8_decode("CRÉDITO ADICIONAL"));$io_pdf->rectangle(300,658,10,10);
		$io_pdf->addText(238,638,7,utf8_decode("RECTIFICACIÓN"));$io_pdf->rectangle(300,635,10,10);
		$io_pdf->addText(470,675,7,"<b>TRASPASO</b>");
		$io_pdf->addText(450,660,7,"GASTOS CORRIENTES");$io_pdf->rectangle(540,658,10,10);
		$io_pdf->addText(460,638,7,"GASTOS DE CAPITAL");$io_pdf->rectangle(540,635,10,10);
		//Impresi�n de las X para el Marcado de Operacion.
		
		switch ($as_procede){
		  case 'SPGINS':
		    $io_pdf->addText(97.5,660.5,7,"<b>X</b>");//Insubsistencia  
		  break;
		  case 'SPGCRA':
		    $io_pdf->addText(302.5,660.5,7,"<b>X</b>");//Cr�dito Adicional.
		  break;
		  case 'SPGREC':
		  	$io_pdf->addText(302.5,637.5,7,"<b>X</b>");//Rectificacion.
		  break;
		  case 'SPGTRA':
		  	$io_pdf->addText(542.5,660.5,7,"<b>X</b>");//Traspaso.
		  break;		
		}
		
		//Gastos Corrientes.
        /*$io_pdf->addText(542.5,660.5,7,"<b>X</b>");
		//Gastos de Capital.
		$io_pdf->addText(542.5,637.5,7,"<b>X</b>");*/

		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezado_pagina
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data,&$io_pdf)
	{
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		    Acess: private
		//	    Arguments: la_data // arreglo de informaci�n
		//	   			   io_pdf // Objeto PDF
		//    Description: funci�n que imprime el detalle
		//	   Creado Por: Ing.Yozelin Barrag�n
		// Fecha Creaci�n: 13/09/2006
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $io_pdf->setStrokeColor(1,1,1);
		$io_pdf->ezSetY(615);
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 7, // Tama�o de Letras
						 'titleFontSize' => 7,  // Tama�o de Letras de los t�tulos
						 'showLines'=>1, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'colGap'=>1, // separacion entre tablas
						 'width'=>580, // Ancho de la tabla
						 'maxWidth'=>580, // Ancho M�ximo de la tabla
						 'xPos'=>305, // Orientaci�n de la tabla
						 'cols'=>array('proyecto'=>array('justification'=>'center','width'=>65), // Justificaci�n y ancho de la 
						 			   'accion'=>array('justification'=>'center','width'=>45),
									   'ejecutora'=>array('justification'=>'center','width'=>25),
									   'partida'=>array('justification'=>'center','width'=>25),
									   'generica'=>array('justification'=>'center','width'=>25),
									   'especifica'=>array('justification'=>'center','width'=>25), 
									   'subespecifica'=>array('justification'=>'center','width'=>25),
									   'denominacion'=>array('justification'=>'left','width'=>255),
									   'monto'=>array('justification'=>'right','width'=>80))); // Justificaci�n y ancho 

		$la_columnas = array('proyecto'=>'<b>PROYECTO O ACCION CENTRALIZADA</b>',
		                     'accion'=>'<b>ACCI�N ESPEC�FICA</b>',
							 'ejecutora'=>'<b>UEL</b>',
							 'partida'=>'<b>PART</b>',
							 'generica'=>'<b>GEN</b>',
			                 'especifica'=>'<b>ESP</b>',
							 'subespecifica'=>'<b>SUB</b>',
							 'denominacion'=>'<b>DENOMINACI�N</b>',
							 'monto'=>'<b>BOL�VARES</b>');
		
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle_mensual($la_data,&$io_pdf)
	{
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle_mensual
		//		    Acess: private
		//	    Arguments: la_data // arreglo de informaci�n
		//	   			   io_pdf // Objeto PDF
		//    Description: funci�n que imprime el detalle
		//	   Creado Por: Ing.Luis Anibal Lang
		// Fecha Creaci�n: 19/05/2009
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $io_pdf->setStrokeColor(1,1,1);
		$data[0] = array('cuenta'=>"CUENTA",'programatica'=>"ESTRUCTURA",'operacion'=>"OPER",'enero'=>"ENE",
								'febrero'=>"FEB",'marzo'=>"MAR",'abril'=>"ABR",
								'mayo'=>"MAY",'junio'=>"JUN",'julio'=>"JUL",'agosto'=>"AGO",
								'septiembre'=>"SEP",'octubre'=>"OCT",'noviembre'=>"NOV",'diciembre'=>"DIC");
		$la_columnas = array('cuenta'=>"",'programatica'=>"",'operacion'=>"",'enero'=>"",'febrero'=>"",'marzo'=>"",'abril'=>"",
							 'mayo'=>"",'junio'=>"",'julio'=>"",'agosto'=>"",'septiembre'=>"",'octubre'=>"",'noviembre'=>"",'diciembre'=>"");
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama�o de Letras
						 'titleFontSize' => 7,  // Tama�o de Letras de los t�tulos
						 'showLines'=>1, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'colGap'=>1, // separacion entre tablas
						 'width'=>580, // Ancho de la tabla
						 'maxWidth'=>580, // Ancho M�ximo de la tabla
						 'xPos'=>305, // Orientaci�n de la tabla
						 'cols'=>array('cuenta'=>array('justification'=>'center','width'=>55), // Justificaci�n y ancho de la 
						 			   'programatica'=>array('justification'=>'center','width'=>75),
									   'operacion'=>array('justification'=>'center','width'=>25),
									   'enero'=>array('justification'=>'center','width'=>35),
									   'febrero'=>array('justification'=>'center','width'=>35),
									   'marzo'=>array('justification'=>'center','width'=>35), 
									   'abril'=>array('justification'=>'center','width'=>35),
									   'mayo'=>array('justification'=>'center','width'=>35),
									   'junio'=>array('justification'=>'center','width'=>35),
									   'julio'=>array('justification'=>'center','width'=>35),
									   'agosto'=>array('justification'=>'center','width'=>35),
									   'septiembre'=>array('justification'=>'center','width'=>35),
									   'octubre'=>array('justification'=>'center','width'=>35),
									   'noviembre'=>array('justification'=>'center','width'=>35),
									   'diciembre'=>array('justification'=>'center','width'=>35))); // Justificaci�n y ancho 

		$io_pdf->ezTable($data,$la_columnas,'DISTRIBUCION MENSUAL',$la_config);
		unset($la_columnas);
		unset($la_config);
		
		$la_columnas = array('cuenta'=>"",'programatica'=>"",'operacion'=>"",'enero'=>"",'febrero'=>"",'marzo'=>"",'abril'=>"",
							 'mayo'=>"",'junio'=>"",'julio'=>"",'agosto'=>"",'septiembre'=>"",'octubre'=>"",'noviembre'=>"",'diciembre'=>"");
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 6, // Tama�o de Letras
						 'titleFontSize' => 7,  // Tama�o de Letras de los t�tulos
						 'showLines'=>1, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'colGap'=>1, // separacion entre tablas
						 'width'=>580, // Ancho de la tabla
						 'maxWidth'=>580, // Ancho M�ximo de la tabla
						 'xPos'=>305, // Orientaci�n de la tabla
						 'cols'=>array('cuenta'=>array('justification'=>'center','width'=>55), // Justificaci�n y ancho de la 
						 			   'programatica'=>array('justification'=>'center','width'=>75),
									   'operacion'=>array('justification'=>'center','width'=>25),
									   'enero'=>array('justification'=>'right','width'=>35),
									   'febrero'=>array('justification'=>'right','width'=>35),
									   'marzo'=>array('justification'=>'right','width'=>35), 
									   'abril'=>array('justification'=>'right','width'=>35),
									   'mayo'=>array('justification'=>'right','width'=>35),
									   'junio'=>array('justification'=>'right','width'=>35),
									   'julio'=>array('justification'=>'right','width'=>35),
									   'agosto'=>array('justification'=>'right','width'=>35),
									   'septiembre'=>array('justification'=>'right','width'=>35),
									   'octubre'=>array('justification'=>'right','width'=>35),
									   'noviembre'=>array('justification'=>'right','width'=>35),
									   'diciembre'=>array('justification'=>'right','width'=>35))); // Justificaci�n y ancho 

		
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------
    //-----------------------------------------------------------------------------------------------------------------------------------
	function uf_init_niveles()
	{	///////////////////////////////////////////////////////////////////////////////////////////////////////
		//	   Function: uf_init_niveles
		//	     Access: public
		//	    Returns: vacio	 
		//	Description: Este m�todo realiza una consulta a los formatos de las cuentas
		//               para conocer los niveles de la escalera de las cuentas contables  
		//////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_funcion,$ia_niveles_scg;
		
		$ls_formato  = ""; $li_posicion=0; $li_indice=0;
		$ls_formato  = trim($_SESSION["la_empresa"]["formpre"])."-";
		$li_posicion = 1 ;
		$li_indice   = 1 ;
		$li_posicion = $io_funcion->uf_posocurrencia($ls_formato, "-" , $li_indice ) - $li_indice;
		do
		{
			$ia_niveles_scg[$li_indice] = $li_posicion;
			$li_indice   = $li_indice+1;
			$li_posicion = $io_funcion->uf_posocurrencia($ls_formato, "-" , $li_indice ) - $li_indice;
		} while ($li_posicion>=0);
	}// end function uf_init_niveles
	//-----------------------------------------------------------------------------------------------------------------------------------
    
	function uf_print_pie_de_pagina(&$io_pdf)
	{
	///////////////////////////////////////////////////////////////////////////////////////////////////////
	//	   Function: uf_print_pie_de_pagina
	//	     Access: public
	//	    Returns: vacio	 
	//	Description: M�todo que imprime el pie de pagina de Forma 0301 De Modificaciones Presupuestarias. 
	//////////////////////////////////////////////////////////////////////////////////////////////////////
   
        $io_pdf->Rectangle(19,80,570,30);

        $io_pdf->Rectangle(19,40,570,60);
		$io_pdf->line(19,80,590,80);		
		$io_pdf->line(95,40,95,100);		
		$io_pdf->line(180,40,180,100);		
		$io_pdf->line(290,40,290,100);	
		$io_pdf->line(400,40,400,110);	
		$io_pdf->line(480,40,480,100);
		
		$io_pdf->addText(200,102,8,"<b>INSTITUCION</b>"); // Agregar el t�tulo
		$io_pdf->addText(410,102,8,"<b>OFICINA NACIONAL DE PRESUPUESTO</b>"); // Agregar el t�tulo

			
		$io_pdf->addText(25,90,7,"ELABORADO POR:"); // Agregar el t�tulo
		$io_pdf->addText(110,90,7,"REVISADO POR:"); // Agregar el t�tulo
		$io_pdf->addText(200,90,7,"JEFE DE OFICINA DE"); // Agregar el t�tulo
		$io_pdf->addText(210,83,7,"PLANIFICACION"); // Agregar el t�tulo
		$io_pdf->addText(298,90,7,"APROBADO POR GERENTE"); // Agregar el t�tulo
		$io_pdf->addText(300,83,7,"GENERAL O PRESIDENTE"); // Agregar el t�tulo
		$io_pdf->addText(415,90,7,"JEFE SECTOR"); // Agregar el t�tulo
		$io_pdf->addText(490,90,7,"DIRECTOR G. SECTORIAL"); // Agregar el t�tulo
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------
	  require_once("../../shared/ezpdf/class.ezpdf.php");
	  require_once("../../shared/class_folder/class_fecha.php");
	  require_once("../../shared/class_folder/class_funciones.php");
	  require_once("../../shared/class_folder/sigesp_include.php"); 
	  require_once("../../shared/class_folder/class_datastore.php");      
	  require_once("../../shared/class_folder/class_sql.php");    
	  require_once("sigesp_spg_funciones_reportes.php");
	  require_once("sigesp_spg_reportes_class.php");
	 
	  $io_report      = new sigesp_spg_reportes_class();  
	  $io_funrep      = new sigesp_spg_funciones_reportes();
	  $io_funcion     = new class_funciones();
	  $io_fecha       = new class_fecha();
	  $io_conect      = new sigesp_include();
	  $con            = $io_conect-> uf_conectar ();
	  $io_msg         = new class_mensajes(); //Instanciando la clase mensajes 
	  $io_sql         = new class_sql($con); //Instanciando  la clase sql
	  $lb_valido      = true;
	  $io_dsreport    = new class_datastore();
	  $ls_codemp      = $_SESSION["la_empresa"]["codemp"];
	  $ls_forpre      = $_SESSION["la_empresa"]["formpre"];
	  $ls_estmodprog=	$_SESSION["la_empresa"]["estmodprog"];
	 
	  $ls_procede     = $_GET["procede"];
	  $ls_comprobante = $_GET["comprobante"];
	  $ld_fecha       = $_GET["fecha"];
	  $io_report->uf_init_niveles(&$ia_niveles_scg,&$li_posicion);
  
	  if ($lb_valido==false)
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
			$io_pdf=new Cezpdf('LETTER','portrait'); // Instancia de la clase PDF
			$io_pdf->selectFont('../../shared/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
			$io_pdf->ezSetCmMargins(6.2,4,3,3); // Configuraci�n de los margenes en cent�metros
			$ls_titulo = utf8_decode("<b>SOLICITUD DE MODIFICACIÓN PRESUPUESTARIA No. ").$ls_comprobante."</b>";
			$ls_fuentefin = $io_report->uf_obtener_fuente_financiamiento_comprobante($ls_comprobante,$ls_procede,$ld_fecha);
			
			$li_total   = count($ia_niveles_scg);
			$li_numrows = 0;
                        $ls_denuac1='';
			$lb_ok= $io_report->uf_select_dt_comprobante($ls_codemp,$ls_procede,$ls_comprobante,$ld_fecha,&$li_numrows,$rs_dat,$ls_denuac1);
                        
                        uf_print_encabezado_pagina($ls_titulo,$ls_procede,$ld_fecha,$ls_fuentefin,$ls_denuac1,$io_pdf); // Imprimimos el encabezado de la p�gina
			if ($li_numrows==0)
			   {
				 print("<script language=JavaScript>");
				 print(" alert('No hay nada que Reportar');"); 
				 print(" close();");
				 print("</script>");
			   }
			else
			  {
				 $li_pos = 0;
				 $lb_impreso = false;
				 $ld_totced  = 0;
				 $ld_totrec  = 0; 
				 $li_filas   = 0;
				 
				 $io_report->uf_select_dt_comprobante_r($ls_codemp,$ls_procede,$ls_comprobante,$ld_fecha,$li_total,$la_data,$ia_niveles_scg,$li_posicion,$li_numrows);
				 uf_print_detalle($la_data,&$io_pdf);
				 uf_print_pie_de_pagina(&$io_pdf);
			   }
			if($ls_estmodprog==1)
			{
				$rs_mensual=$io_report->uf_select_distmensual($ls_codemp,$ls_procede,$ls_comprobante,$ld_fecha);
				$li_i=0;
				while((!$rs_mensual->EOF))
				{
					$li_i++;
					$ls_cuenta= trim($rs_mensual->fields["spg_cuenta"]);
					$ls_codestpro1= $rs_mensual->fields["codestpro1"];
					$ls_codestpro2= $rs_mensual->fields["codestpro2"];
					$ls_codestpro3= $rs_mensual->fields["codestpro3"];
					$ls_codestpro4= $rs_mensual->fields["codestpro4"];
					$ls_codestpro5= $rs_mensual->fields["codestpro5"];
					$ls_operacion= trim($rs_mensual->fields["operacion"]);
					$li_enero= number_format($rs_mensual->fields["enero"],2,',','.');
					$li_febrero= number_format($rs_mensual->fields["febrero"],2,',','.');
					$li_marzo= number_format($rs_mensual->fields["marzo"],2,',','.');
					$li_abril= number_format($rs_mensual->fields["abril"],2,',','.');
					$li_mayo= number_format($rs_mensual->fields["mayo"],2,',','.');
					$li_junio= number_format($rs_mensual->fields["junio"],2,',','.');
					$li_julio= number_format($rs_mensual->fields["julio"],2,',','.');
					$li_agosto= number_format($rs_mensual->fields["agosto"],2,',','.');
					$li_septiembre= number_format($rs_mensual->fields["septiembre"],2,',','.');
					$li_octubre= number_format($rs_mensual->fields["octubre"],2,',','.');
					$li_noviembre= number_format($rs_mensual->fields["noviembre"],2,',','.');
					$li_diciembre= number_format($rs_mensual->fields["diciembre"],2,',','.');
					$ls_codestpro=$ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5;
					$io_report->uf_formatoprogramatica($ls_codestpro,&$ls_programatica);
                                      
					if($ls_operacion=="AU")
					{
						$ls_operacion="AUM";
					}
					else
					{
						$ls_operacion="DIS";
					}
					$la_data1[$li_i] = array('cuenta'=>$ls_cuenta,'programatica'=>$ls_programatica,'operacion'=>$ls_operacion,'enero'=>$li_enero,
											'febrero'=>$li_febrero,'marzo'=>$li_marzo,'abril'=>$li_abril,
											'mayo'=>$li_mayo,'junio'=>$li_junio,'julio'=>$li_julio,'agosto'=>$li_agosto,
											'septiembre'=>$li_septiembre,'octubre'=>$li_octubre,'noviembre'=>$li_noviembre,'diciembre'=>$li_diciembre);
					$rs_mensual->MoveNext();
				}
				if($li_i>0)
				{
					uf_print_detalle_mensual($la_data1,&$io_pdf);
				}
			}
			$io_pdf->ezStopPageNumbers(1,1);
			if (isset($d) && $d)
			{
				$ls_pdfcode = $io_pdf->ezOutput(1);
				$ls_pdfcode = str_replace("\n","\n<br>",htmlspecialchars($ls_pdfcode));
				echo '<html><body>';
				echo trim($ls_pdfcode);
				echo '</body></html>';
			}
			else
			{
				$io_pdf->ezStream();
			}
		 }
		unset($io_pdf);
		unset($io_report);
		unset($io_funciones);
		unset($io_function_report);
		unset($io_fecha);
?>	