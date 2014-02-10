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
	function uf_print_encabezado_pagina($as_titulo,$as_numsolcot,$as_fecsolcot,$as_dentipsolcot,$as_obssolcot,&$io_pdf)
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
		$io_pdf->setStrokeColor(0,0,0);
		$io_pdf->saveState();
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
		$io_pdf->addText(555,770,7,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(560,760,7,date("h:i a")); // Agregar la hora
		
		$io_pdf->ezSetY(695);
		$la_data=array(array('name'=>'<b>                  TIPO:  </b>'.$as_dentipsolcot),
					   array('name'=>'<b>OBSERVACI�N: </b> '.$as_obssolcot));				
		$la_columna=array('name'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
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

		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_datos_proveedor($as_codpro,$as_nompro,$as_dirpro,$as_telpro,$as_email,$as_rifpro,&$io_pdf)
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
		
		$io_pdf->saveState();
		$la_data=array(array('name'=>'<b>Nombre o Raz�n Social: </b>'.$as_codpro.'  -  '.$as_nompro),
 		               array('name'=>'<b>Direcci�n: </b>'.$as_dirpro),
					   array('name'=>'<b>Tel�fono: </b> '.$as_telpro.'  -                                                 <b>E-Mail</b>: '.$as_email.'                                                                         <b>RIF: </b>'.$as_rifpro));				
		
		$la_data1=array(array('name'=>''));				
		$la_columna=array('name'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 11, // Tama�o de Letras
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>320, // Orientaci�n de la tabla
						 'width'=>548, // Ancho de la tabla						 
						 'maxWidth'=>548); // Ancho M�ximo de la tabla
		$io_pdf->ezTable($la_data1,$la_columna,'',$la_config);	
		
		unset($la_data1);
		unset($la_columna);
		unset($la_config);
		
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
	function uf_print_texto(&$io_pdf)
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

		$la_data1=array(array('name'=>'
				      <b>CONDICIONES DE LA OFERTA:</b>
				      '),
				array('name'=>'<b>Validez de la Oferta:</b> M�nimo de _________ (______) d�as h�biles contados a partir de la recepci�n de la invitaci�n al proceso. Dicha validez deber� venir sin falta reflejada en la oferta comercial o en la carta oferta.'),
				array('name'=>'<b>Condiciones de Entrega:</b> Especificar (dependiendo lo que se pretende adquirir, el servicio o la obra).'),
				array('name'=>'<b>Condiciones de Pago:</b> Contra Entrega a entera satisfacci�n del Ministerio del Poder Popular para la Cultura.
				      '),
				array('name'=>'Asimismo, deber� consignar, los documentos que a continuaci�n se detallan:'),
				array('name'=>'<b>Instrucciones para la presentaci�n de la oferta comercial:</b> '),
				array('name'=>'- Entregar oferta comercial impresa, con logo de la empresa, direcci�n, tel�fono, Registro de Informaci�n Fiscal <b>(RIF)</b>, debidamente firmada por el Representante Legal de la Empresa o Cooperativa.'),
				array('name'=>'- Planilla emanada del Registro Nacional de Contratista "Informaci�n de la Empresa Registrada".'),
				array('name'=>'- Dichas <b>ofertas deber�n estar dirigidas al Ministerio del Poder Popular para la Cultura. Direcci�n General de Administraci�n y Servicios. Unidad de Compras</b>; y deben contener los siguientes datos:
						'),
				array('name'=>'        <b>R.I.F: G-20004640-6'),
				array('name'=>'        Direcci�n:</b> Avenida Pante�n, Foro Libertador, Edificio Archivo General de la Naci�n, Planta Baja.'),
				array('name'=>'        <b>Tel�fonos:</b> 0212-509.55.17 <b>Fax:</b> 0212-564.32.77'),
				array('name'=>'        <b>Persona Contacto:</b> Evelin Girott'),
				array('name'=>'        <b>Correo Electr�nico:</b> comprasmincultura1@gmail.com
				      '),
				array('name'=>'<b>En caso de no estar inscritas en el Registro Nacional de Contratistas:</b>'),
				array('name'=>'- Documento Constitutivo y sus �ltimas modificaciones.'),
				array('name'=>'- Registro de Informaci�n Fiscal (RIF) actualizado.'),
				array('name'=>'- Fotocopia de la c�dula de identidad del Responsable Legal de la Empresa o Cooperativa.
  				      '),
				array('name'=>'<b>Condiciones de entrega de la oferta:</b>'),
				array('name'=>'- La oferta presentada deber� cumplir con las caracter�sticas t�cnicas antes se�aladas.'),
				array('name'=>'- Incluir en la oferta los precios Unitarios y Totales.'),
				array('name'=>'- Incluir el IVA separado del precio.
				      '),
				array('name'=>'<b>Lapso de entrega de las ofertas y evaluaci�n:</b>'),
				array('name'=>'La oferta deber� ser entregada 3 (tres) d�as h�biles despu�s de recibida la invitaci�n, desde las 8:30 a.m, hasta las 4:30 p.m, v�a correo eletr�nico, fax o a la direcci�n se�alada.
				      '),
				array('name'=>'La adjudicaci�n se realizar� entre aquellas ofertas que cumplan los requisitos exigidos en la presente comunicaci�n y haya ofrecido la mejor opci�n para el Ministerio del Poder Popular para la Cultura.
				      '),
				array('name'=>'Agradeciendo de antemano su participaci�n en el referido proceso, se despide.
				      '));				
		$la_columna=array('name'=>'');	
		//$la_columna=array('des'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 11, // Tama�o de Letras
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>320, // Orientaci�n de la tabla
						 'width'=>548, // Ancho de la tabla						 
						 'maxWidth'=>548); // Ancho M�ximo de la tabla
					//	 'cols'=>array('des'=>array('justification'=>'center','width'=>125))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data1,$la_columna,'',$la_config);	

		unset($la_data1);
		unset($la_columna);
		unset($la_config);

		$la_data1=array(array('name'=>'                      					  Atentamente,




					                      <b>Evelin Girott
					                     Jefe de Compras
				 Ministerio del Poder Popular para la Cultura</b>'));				
		$la_columna=array('name'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 11, // Tama�o de Letras
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>330, // Orientaci�n de la tabla
						  'justification'=>'center', // Ancho de la tabla	
						 'width'=>250, // Ancho de la tabla						 
						 'maxWidth'=>250); // Ancho M�ximo de la tabla
		$io_pdf->ezTable($la_data1,$la_columna,'',$la_config);	

	}

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_saludo($la_data,&$io_pdf)
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
		$la_data1=array(array('name'=>'
				      Me dirijo a usted, en la oportunidad de notificarle que su empresa ha sido seleccionada para participar en la Consulta de Precios, de las siguientes especificaciones que se detallan a continuaci�n:
				      '));				
		$la_columna=array('name'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 11, // Tama�o de Letras
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>320, // Orientaci�n de la tabla
						 'width'=>548, // Ancho de la tabla						 
						 'maxWidth'=>548); // Ancho M�ximo de la tabla
		$io_pdf->ezTable($la_data1,$la_columna,'',$la_config);	
	}

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
		$la_data1=array(array('name'=>''));				
		$la_columna=array('name'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 11, // Tama�o de Letras
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>320, // Orientaci�n de la tabla
						 'width'=>548, // Ancho de la tabla						 
						 'maxWidth'=>548); // Ancho M�ximo de la tabla
		$io_pdf->ezTable($la_data1,$la_columna,'',$la_config);	
		
		unset($la_data1);
		unset($la_columna);
		unset($la_config);
		
		$la_columna=array('codigo'=>'<b>C�digo</b>',
						  'denominacion'=>'<b>Denominaci�n</b>',
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
						 			   'denominacion'=>array('justification'=>'left','width'=>335), // Justificaci�n y ancho de la columna
						 			   'cantidad'=>array('justification'=>'right','width'=>90))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'<b>DETALLE DE LOS MATERIALES, SUMINISTROS O SERVICIOS REQUERIDOS</b>',$la_config);
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
	$ls_codprov = $_GET["codprov"];
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
	     $rs_data = $io_report->uf_load_cabecera_formato_solicitud_cotizacion($ls_numsolcot,$ls_tipsolcot,$ls_fecsolcot,$ls_tabla,&$lb_valido,$ls_codprov);
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
				   $io_pdf->ezSetCmMargins(5,3,3,3); // Configuraci�n de los margenes en cent�metros
				   $io_pdf->ezStartPageNumbers(550,30,10,'','',1); // Insertar el n�mero de p�gina
				   $li_count = 0; 
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
										    $ld_canite       = number_format($row["canite"],2,',','.');
									        $la_datos[$li_i] = array('codigo'=>$ls_codigo,'denominacion'=>$ls_denite,'cantidad'=>$ld_canite);
									      }
				    		       }
						        else
							       {
							         $lb_valido = false;
							       }
						      }
					       uf_print_encabezado_pagina($ls_titulo,$ls_numsolcot,$ls_fecsolcot,$ls_tipo,$ls_obssolcot,$io_pdf);
					       uf_print_datos_proveedor($ls_codpro,$ls_nompro,$ls_dirpro,$ls_telpro,$ls_mailpro,$ls_rifpro,$io_pdf);
					      uf_print_saludo($la_datos,$io_pdf);
					       uf_print_detalle($la_datos,$io_pdf);
					      uf_print_texto($io_pdf);
					      $io_pdf->setStrokeColor(0,0,0);
					       $io_pdf->line(20,50,580,50);
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