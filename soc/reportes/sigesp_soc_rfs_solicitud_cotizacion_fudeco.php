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

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_seguridad($as_titulo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_insert_seguridad
		//		   Access: private 
		//	    Arguments: as_titulo // T�tulo del reporte
		//    Description: funci�n que guarda la seguridad de quien gener� el reporte
		//	   Creado Por: Ing. N�stor Falc�n.
		// Fecha Creaci�n: 11/03/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_soc;
		
		$ls_descripcion="Gener� el Reporte de Formato de salida de ".$as_titulo;
		$lb_valido=$io_fun_soc->uf_load_seguridad_reporte("SOC","sigesp_soc_p_solicitud_cotizacion.php",$ls_descripcion);
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,$as_numsolcot,$as_fecsolcot,$as_dentipsolcot,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: as_titulo // T�tulo del Reporte
		//	    		   hidnumero // N�mero de solicitud
		//	    		   ls_fecsolcot // N�mero de solicitud
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funci�n que imprime los encabezados por p�gina
		//	   Creado Por: Ing. N�stor Falc�n.
		// Fecha Creaci�n: 17/05/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->setStrokeColor(0,0,0);
		$io_pdf->line(41,50,589,50);
		$io_pdf->rectangle(140,705,450,40);
		$io_pdf->line(450,705,450,745);
		$io_pdf->line(450,725,590,725);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],40,705,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(10,$as_titulo);		
		$io_pdf->addText(200,720,14,"<b>".$as_titulo."</b>"); // Agregar el t�tulo
		$io_pdf->addText(460,730,10,"<b>   No.:</b>");      // Agregar texto
		$io_pdf->addText(495,730,10,$as_numsolcot); // Agregar Numero de la solicitud
		$io_pdf->addText(450,710,10,"<b>  Fecha:</b>"); // Agregar texto
		$io_pdf->addText(495,710,10,$as_fecsolcot); // Agregar la Fecha
		$io_pdf->addText(45,698,8,"<b>RIF: ".$_SESSION["la_empresa"]["rifemp"]."</b>"); 
		$io_pdf->addText(40,40,7,"<b>DIRECCI�N: ".strtoupper($_SESSION["la_empresa"]["direccion"])."</b>"); 		

		$io_pdf->Rectangle(41,60,548,70); 
		$io_pdf->line(41,117,589,117);	//HORIZONTAL
		$io_pdf->line(41,73,589,73);	//HORIZONTAL
		$io_pdf->addText(120,120,8,"ELABORADO POR"); 
		$io_pdf->addText(130,63,8,"FIRMA / SELLO"); 
		$io_pdf->line(300,60,300,130);	//VERTICAL	
		$io_pdf->addText(400,120,8,"CONFORMADO POR"); 
		$io_pdf->addText(408,63,8,"FIRMA / SELLO");  
		
		$io_pdf->ezSetY(180);
	    $la_data1=array(array('name'=>'<b>OBSERVACIONES: </b>'));
		$la_columna=array('name'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize'=>10, // Tama�o de Letras
						 'showLines'=>2, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>320, // Orientaci�n de la tabla
						 'width'=>548, // Ancho de la tabla						 
						 'maxWidth'=>548); // Ancho M�ximo de la tabla
		$io_pdf->ezTable($la_data1,$la_columna,'',$la_config);	
		
		unset($la_data1);
		unset($la_columna);
		unset($la_config);
		
		$la_data    = array(array('name'=>'INDICAR EN COTIZACI�N CONDICIONES DE COMPRA Y/O SERVICIOS: TIEMPO DE ENTREGA Y/O EJECUCI�N, LUGAR DE ENTREGA, CONDICIONES DE PAGO Y GARANT�AS'));
		$la_columna = array('name'=>'');		
		$la_config  = array('showHeadings'=>0, // Mostrar encabezados
						    'fontSize'=>10, // Tama�o de Letras
						    'showLines'=>0, // Mostrar L�neas
						    'titleFontSize'=>10,
						    'shaded'=>0, // Sombra entre l�neas
						    'xPos'=>320, // Orientaci�n de la tabla
						    'width'=>548, // Ancho de la tabla						 
						    'justification'=>'left', // Ancho de la tabla						 
						    'maxWidth'=>548); // Ancho M�ximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_datos_proveedor($as_codpro,$as_nompro,$as_dirpro,$as_telpro,$as_email,$as_rifpro,$as_faxpro,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_datos_proveedor
		//		   Access: private 
		//	    Arguments: as_numsolcot // N�mero
		//	    		   as_fecsolcot // Fecha
		//	    		   as_obssolcot // Observaci�n
		//	    		   as_codpro // C�digo de Proveedor
		//	    		   as_nompro // Nombre de Proveedor
		//	    		   as_dirpro // Direcci�n de Proveedor
		//	    		   as_telpro // Tel�fono de Proveedor
		//	    		   io_pdf // total de registros que va a tener el reporte
		//    Description: funci�n que imprime la cabecera de cada p�gina
		//	   Creado Por: Ing. N�stor Falc�n.
		// Fecha Creaci�n: 19/06/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		$io_pdf->ezSetY(690);
		$la_data=array(array('name'=>'<b>Nombre o Raz�n Social: </b>'.$as_codpro.'  -  '.$as_nompro),
 		               array('name'=>'<b>Direcci�n: </b>'.$as_dirpro),
					   array('name'=>'<b>Tel�fono: </b> '.$as_telpro.'  -      <b>FAX: </b>'.$as_faxpro      .'                    <b>E-Mail</b>: '.$as_email.'                                                                         <b>RIF: </b>'.$as_rifpro));				
		
		$la_columna=array('name'=>'<b>DATOS DEL PROVEEDOR</b>');		
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 9, // Tama�o de Letras
						 'showLines'=>1, // Mostrar L�neas
						 'titleFontSize' => 9,
						 'shaded'=>0, // Sombra entre l�neas
						 'xPos'=>320, // Orientaci�n de la tabla
						 'width'=>548, // Ancho de la tabla						 
						 'justification'=>'center', // Ancho de la tabla						 
						 'maxWidth'=>548); // Ancho M�ximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
	}// end function uf_print_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------
   	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data,&$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: la_data // arreglo de informaci�n
		//	   			   io_pdf // Objeto PDF
		//    Description: funci�n que imprime el detalle
		//	   Creado Por: Ing. N�stor Falc�n.
		// Fecha Creaci�n: 17/05/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////				
		
		$io_pdf->ezSetDy(-5);
		$la_columna=array('codigo'=>'<b>C�digo</b>',
						  'denominacion'=>'<b>Denominaci�n</b>',
						  'unidad'=>'<b>Unidad</b>',
  						  'cantidad'=>'<b>Cantidad</b>');
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 9, // Tama�o de Letras
						 'titleFontSize' => 9,  // Tama�o de Letras de los t�tulos
						 'showLines'=>1, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M�ximo de la tabla
						 'xPos'=>320, // Orientaci�n de la tabla
						 'cols'=>array('codigo'=>array('justification'=>'center','width'=>125),      // Justificaci�n y ancho de la columna
						 			   'denominacion'=>array('justification'=>'left','width'=>250), // Justificaci�n y ancho de la columna
									   'unidad'=>array('justification'=>'center','width'=>95),
						 			   'cantidad'=>array('justification'=>'right','width'=>80))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'<b>DETALLE DE LOS MATERIALES, SUMINISTROS O SERVICIOS REQUERIDOS</b>',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);		
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------
	
	require_once("sigesp_soc_class_report.php");	
	require_once("../../shared/ezpdf/class.ezpdf.php");
	require_once("../../shared/class_folder/class_sql.php");	
	require_once("../class_folder/class_funciones_soc.php");
	require_once("../../shared/class_folder/sigesp_include.php");
	require_once("../../shared/class_folder/class_funciones.php");
	
	$in           = new sigesp_include();
	$con          = $in->uf_conectar();
	$io_sql       = new class_sql($con);	
	$io_report    = new sigesp_soc_class_report($con);
	$io_funciones = new class_funciones();
	$io_fun_soc	  = new class_funciones_soc();
	
	$ls_numsolcot = $_GET["numsolcot"];
	$ls_tipsolcot = $_GET["tipsolcot"];
	$ls_fecsolcot = $_GET["fecsolcot"];
	if ($ls_tipsolcot=='B')
	   {
	     $ls_tabla = "soc_dtsc_bienes";
	     $ls_campo = "codart";
	     $ls_table = "siv_articulo"; 
	     $ls_tipo  = "Bienes"; 
	   }
	elseif($ls_tipsolcot=='S')
	   {
	     $ls_tabla = "soc_dtsc_servicios";
	     $ls_campo = "codser";
	     $ls_table = "soc_servicios";
	     $ls_tipo  = "Servicios"; 
	   }
	$ls_codemp = $_SESSION["la_empresa"]["codemp"];
	$ls_titulo = "SOLICITUD DE COTIZACI�N";

	$lb_valido = uf_insert_seguridad($ls_titulo); // Seguridad de Reporte
	if ($lb_valido)
	   {
	     $rs_data = $io_report->uf_load_cabecera_formato_solicitud_cotizacion($ls_numsolcot,$ls_tipsolcot,$ls_fecsolcot,$ls_tabla,&$lb_valido);
	     if (!$lb_valido)
		    {
			  print("<script language=JavaScript>");
			  print(" alert('No hay nada que Reportar !!!');"); 
			  print(" close();");
			  print("</script>");
		    }
	     else
	        {
	          $li_numrows = $io_sql->num_rows($rs_data);
		      if ($li_numrows>0)
		         {
				   error_reporting(E_ALL);
				   set_time_limit(1800);
				   $io_pdf = new Cezpdf('LETTER','portrait'); // Instancia de la clase PDF
				   $io_pdf->selectFont('../../shared/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
				   $io_pdf->ezSetCmMargins(5,4,3,3); // Configuraci�n de los margenes en cent�metros
				   $io_pdf->ezStartPageNumbers(575,30,10,'','',1); // Insertar el n�mero de p�gina
				   $li_count = 0; 
				   uf_print_encabezado_pagina($ls_titulo,$ls_numsolcot,$ls_fecsolcot,$ls_tipo,$io_pdf);
				   while (($row=$io_sql->fetch_row($rs_data)) && $lb_valido)
						 { 
                           $li_count++; 
					       if ($li_count>1)
					          {
						        $io_pdf->ezNewPage(); 					  
						      }
 					  	   $ls_codpro    = $row["cod_pro"];
					  	   $ls_nompro    = $row["nompro"];
						   $ls_dirpro    = $row["dirpro"];
						   $ls_telpro    = $row["telpro"];
						   $ls_obssolcot = $row["obssol"];
						   $ls_fecsolcot = $row["fecsol"];
						   $ls_mailpro   = $row["email"];
						   $ls_rifpro    = $row["rifpro"];
						   $ls_faxpro    = $row["faxpro"];
						   $ls_fecsolcot = $io_funciones->uf_convertirfecmostrar($ls_fecsolcot);
						   $rs_datos     = $io_report->uf_load_dt_solicitud_cotizacion($ls_numsolcot,$ls_codpro,$ls_tabla,$ls_table,$ls_campo,&$lb_valido);
						   if ($lb_valido)
					          {
					     	    $li_totrows = $io_sql->num_rows($rs_datos);
							    if ($li_totrows>0)
							       { 
							         $li_i = 0;
								     while($row=$io_sql->fetch_row($rs_datos))
								          {
									        $li_i++;
										    $ls_codigo       = $row["codite"];
										    $ls_denite       = $row["denite"];
											if ($ls_tipsolcot == "B")
						                       {
							                     $ls_unidad=$row["denunimed"];
						                       }
						                    else
						                       {
							                     $ls_unidad="Servicio";
						                       }
											$ld_canite       = number_format($row["canite"],2,',','.');
									        $la_datos[$li_i] = array('codigo'=>$ls_codigo,'denominacion'=>$ls_denite,'cantidad'=>$ld_canite,'unidad'=>$ls_unidad);
									      }
				    		       }
						        else
							       {
							         $lb_valido = false;
							       }
						      }						   
					       uf_print_datos_proveedor($ls_codpro,$ls_nompro,$ls_dirpro,$ls_telpro,$ls_mailpro,$ls_rifpro,$ls_faxpro,$io_pdf);
						   $io_pdf->ezSetCmMargins(5,6.5,3,3); // Configuraci�n de los margenes en cent�metros
						   uf_print_detalle($la_datos,$io_pdf);
	           		       $io_pdf->setStrokeColor(0,0,0);					       
					     }					
			        $io_pdf->ezStopPageNumbers(1,1);
			        $io_pdf->ezStream();
			     }
		      else
		         {
			       print("<script language=JavaScript>");
			       print(" alert('No hay nada que Reportar');"); 
			       print(" close();");
			       print("</script>");
			     }
	        } 
	   }			
?>