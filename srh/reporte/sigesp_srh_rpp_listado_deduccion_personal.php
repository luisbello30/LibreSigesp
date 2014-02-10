<?php

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//    REPORTE: Listado de Deducciones por Personal
//  ORGANISMO: IPSFA
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//-----------------------------------------------------------------------------------------------------------------------------------
///Elaborado por: María Beatriz Unda
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
		//	   Creado Por: María Beatriz Unda
		// Fecha Creación: 12/06/2008
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_srh;
		
		$ls_descripcion="Generó el Reporte ".$as_titulo;
		$lb_valido=$io_fun_srh->uf_load_seguridad_reporte("SRH","sigesp_srh_r_listado_deducciones_personal.php",$ls_descripcion);
		return $lb_valido;
	}
	
//-----------------------------------------------------------------------------------------------------------------------------------	
	
		function uf_print_encabezado_pagina($as_titulo,$io_pdf)
	    {
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezado_pagina
		//		   Access: private 
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: Función que imprime los encabezados por página
		//	   Creado Por: María Beatriz Unda
		// Fecha Creación: 12/06/2008
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->line(15,40,585,40);
       
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],25,705,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
	
		$io_pdf->addText(540,770,7,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(546,764,6,date("h:i a")); // Agregar la Hora
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
		
		 $io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();		
	
	    $io_pdf->ezSetY(665);
	    
		$la_data=array(array('titulo1'=>'<b>'.$as_titulo.'</b>'));
					
		$la_columnas=array('titulo1'=>'');
					
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 11, // Tamaño de Letras
						 'titleFontSize' => 11,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				      	 'cols'=>array('titulo1'=>array('justification'=>'center','width'=>570))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
        unset($la_data);
		unset($la_columnas);
		unset($la_config);		
	
		$io_pdf->restoreState();
	    $io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezado_pagina	
	
//---------------------------------------------------------------------------------------------------------------------------------//
  function uf_print_encabezado_detalle($la_dataper,&$io_pdf)
	 {
		
	   $io_pdf->ezSetY(620);
	   $la_datap[1]=array(   'codper'=>'<b>Código del Personal</b>',
		                     'nombre'=>'<b>Nombre del Personal</b>',
							 'cargo'=>'<b>Cargo</b>',
							 'uniadm'=>'<b>Unidad Administrativa</b>');
							 
		$la_columnas=array(  'codper'=>'',
		                     'nombre'=>'',
							 'cargo'=>'',
							 'uniadm'=>'');
							 
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('codper'=>array('justification'=>'center','width'=>70),
						               'nombre'=>array('justification'=>'center','width'=>180), // Justificación y ancho de la columna
						               'cargo'=>array('justification'=>'center','width'=>160),
									   'uniadm'=>array('justification'=>'center','width'=>160))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_datap,$la_columnas,'',$la_config);
	
		
	    $io_pdf->ezSetY(595);
		$la_columnas=array(  'codper'=>'',
		                     'nombre'=>'',
							 'cargo'=>'',
							 'uniadm'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'cols'=>array('codper'=>array('justification'=>'center','width'=>70),
						               'nombre'=>array('justification'=>'center','width'=>180), // Justificación y ancho de la columna
						               'cargo'=>array('justification'=>'center','width'=>160),
									   'uniadm'=>array('justification'=>'center','width'=>160))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_dataper,$la_columnas,'',$la_config);	
		
	    unset($la_dataper);
		unset($la_columnas);
		unset($la_config);	
				
		}
//---------------------------------------------------------------------------------------------------------------------------------//

 function uf_print_detalle($la_data,$ai_i,$as_total_emple, $as_total_empre,&$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: la_data // arreglo de información
		//				   as_titcuentas // titulo de estructura presupuestaria
		//				   ai_i // total de registros
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: Función que imprime el detalle del reporte
		//	   Creado Por: María Beatriz Unda
		// Fecha Creación: 10/06/2007 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	    
		
		$io_pdf->ezSetY(560);
		$la_data_titulo[1]=array('coddeduc'=>'<b>Código Deducción</b>',
						         'dendeduc'=>'<b>Denominación</b>',
						         'valor'=>'<b>Valor Prima</b>',
								 'aporemple'=>'<b>Aporte Empleado</b>',
								 'aporempre'=>'<b>Aporte Patrón</b>');
		$la_columnas=array('coddeduc'=>'',
						   'dendeduc'=>'',
						   'valor'=>'',
						   'aporemple'=>'',
						   'aporempre'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('coddeduc'=>array('justification'=>'left','width'=>85), // Justificación y ancho de la columna
									   'dendeduc'=>array('justification'=>'left','width'=>225),
						 			   'valor'=>array('justification'=>'right','width'=>90),
									   'aporemple'=>array('justification'=>'right','width'=>85),
									   'aporempre'=>array('justification'=>'right','width'=>85))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data_titulo,$la_columnas,'',$la_config);
		
		$la_columnas=array('coddeduc'=>'',
						   'dendeduc'=>'',
						   'valor'=>'',
						   'aporemple'=>'',
						   'aporempre'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('coddeduc'=>array('justification'=>'center','width'=>85), // Justificación y ancho de la columna
									   'dendeduc'=>array('justification'=>'left','width'=>225),
						 			   'valor'=>array('justification'=>'right','width'=>90),
									   'aporemple'=>array('justification'=>'right','width'=>85),
									   'aporempre'=>array('justification'=>'right','width'=>85))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		
		uf_print_total($as_total_emple, $as_total_empre,$io_pdf);
					
	}// end function uf_print_detalle	
	
//----------------------------------------------------------------------------------------------------------------------------

 function uf_print_total($as_total_emple, $as_total_empre,&$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: la_data // arreglo de información
		//				   as_titcuentas // titulo de estructura presupuestaria
		//				   ai_i // total de registros
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: Función que imprime el detalle del reporte
		//	   Creado Por: María Beatriz Unda
		// Fecha Creación: 10/06/2007 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	    
		
		
		$la_data_titulo[1]=array('total'=>'<b>Total a Deducir</b>',
						         'total_emple'=>$as_total_emple,
						         'total_empre'=>$as_total_empre);
		$la_columnas=array('total'=>'',
						   'total_emple'=>'',
						   'total_empre'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('total'=>array('justification'=>'right','width'=>400), // Justificación y ancho de la columna
									   'total_emple'=>array('justification'=>'right','width'=>85),
						 			   'total_empre'=>array('justification'=>'right','width'=>85))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data_titulo,$la_columnas,'',$la_config);
		
		
					
	}// end function uf_print_detalle		
	


	//--------------------------------------------------------------------------------------------------------------------
   	function calcular_edad($fecha_nac,$fecha_hasta)
	{  	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: calcular_edad
		//	    Arguments: fecha_nac  // fecha de nacimiento
		//                 fecha_hasta	 fecha hasta 	 
		//	      Returns: anos
		//	  Description: Funcion que obtiene la edad de una persona dada una fecha de nacimiento
		//     Creado Por: Maria Beatriz Unda		
		// Fecha Creación: 29/05/2008							Fecha Última Modificación : 
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		 
		$c = date("Y",$fecha_nac);	   
		$b = date("m",$fecha_nac);	  
		$a = date("d",$fecha_nac); 	
		$anos = date("Y",$fecha_hasta)-$c; 
		if(date("m",$fecha_hasta)-$b > 0)
		{
		}
		elseif(date("m",$fecha_hasta)-$b == 0)
		{
			if(date("d",$fecha_hasta)-$a <= 0)
			{		  
				$anos = $anos-1;	        
			}
		}
		else
		{		  
			$anos = $anos-1;		          
		}  
		return $anos;	 
	}// fin de function calcular_edad($fecha_nac,$fecha_hasta)
	//-----------------------------------------------------------------------------------------------------------------------------------

    require_once("../../shared/ezpdf/class.ezpdf.php");  
	require_once("../../shared/class_folder/class_funciones.php");
	$io_funciones=new class_funciones();				
	require_once("../class_folder/utilidades/class_funciones_srh.php");
	$io_fun_srh=new class_funciones_srh('../../');
	require_once("class_folder/sigesp_srh_class_report.php");
	$io_report=new sigesp_srh_class_report();
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ls_titulo="<b>Listado de Deducciones por Personal</b>";
	
//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
    
	$ls_tiporeporte=$io_fun_srh->uf_obtenervalor_get("tiporeporte",0);
	global $ls_tiporeporte;
 
	$ls_codperdes=$io_fun_srh->uf_obtenervalor_get("codperdes","");
	$ls_codperhas=$io_fun_srh->uf_obtenervalor_get("codperhas","");
	$ls_orden=$io_fun_srh->uf_obtenervalor_get("ls_orden","");
		
//--------------------------------------------------------------------------------------------------------------------------------
	$lb_valido=uf_insert_seguridad($ls_titulo); // Seguridad de Reporte
	if($lb_valido)
	{
        $lb_valido=$io_report->uf_personal_deduccion($ls_codperdes,$ls_codperhas,$ls_orden,$rs_data);
		if (($lb_valido==false) || ($rs_data->EOF))
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
			$io_pdf=new Cezpdf('LETTER','portrait'); // Instancia de la clase PDF
			$io_pdf->selectFont('../../shared/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
			$io_pdf->ezSetCmMargins(3.6,5,3,3); // Configuración de los margenes en centímetros
			$io_pdf->ezStartPageNumbers(570,47,8,'','',1); // Insertar el número de página			
			uf_print_encabezado_pagina($ls_titulo,&$io_pdf);
			$li_aux=0;
			$lp_totrow=$rs_data->RecordCount();
			while ((!$rs_data->EOF)&&($lb_valido))
			{
				$li_aux++;
				$ls_codper=$rs_data->fields["codper"];
				$ls_nombre=$rs_data->fields["nomper"]." ".$rs_data->fields["apeper"]; 
				$ls_cargo1= trim ($rs_data->fields["denasicar"]);
				$ls_cargo2= trim ($rs_data->fields["descar"]);
				$li_sueper= trim ($rs_data->fields["sueper"]);
				$ls_hcmper= trim ($rs_data->fields["hcmper"]);
				$ld_fecnacper=$rs_data->fields["fecnacper"];
				$ls_sexper=$rs_data->fields["sexper"];
				$ld_fecact=	date("Y-m-d");
				$ls_edadper=calcular_edad(strtotime($ld_fecnacper),strtotime($ld_fecact));
				if ($ls_cargo1!="Sin Asignación de Cargo")
				{
					$ls_cargo=$ls_cargo1;
				}
				if ($ls_cargo2!="Sin Cargo")
				{
					$ls_cargo=$ls_cargo2;
				}	
				$ls_uniadm= trim ($rs_data->fields["desuniadm"]);
				$la_dataper[$li_aux]=array('codper'=>$ls_codper,'nombre'=>$ls_nombre,'cargo'=>$ls_cargo,'uniadm'=>$ls_uniadm);			 
				// Para el detalle de las deducciones
				uf_print_encabezado_detalle($la_dataper,&$io_pdf);
			 	unset($la_dataper);
				$suma_total_emple=0;
				$suma_total_empre=0;
				$lb_valido=$io_report->uf_select_deduccion_personal($ls_codper, $rs_data2);
				if($lb_valido)
				{
					$li_i=0;
					$num=$rs_data2->RecordCount();	
					while ((!$rs_data2->EOF)&&($lb_valido))
					{   						
						$ls_codtipded=trim ($rs_data2->fields["codtipded"]);
						$ls_dentipded=trim ($rs_data2->fields["dentipded"]);
						$ls_sueldo=trim ($rs_data2->fields["suelbene"]);
						$ls_sexo=$rs_data2->fields["sexbene"];   
						$ls_edadmin=$rs_data2->fields["edadmin"];
						$ls_edadmax=$rs_data2->fields["edadmax"];
						$li_aporemple=$rs_data2->fields["aporemple"];
						$li_aporempre=$rs_data2->fields["aporempre"];
						$li_prima=$rs_data2->fields["valprim"];		
		                $ls_hcm=$rs_data2->fields["hcm"];               
						if (trim($ls_hcm)=='S')   
						{   
							$ls_hcm='1';   
						}   
						elseif (trim($ls_hcm)=='N')   
						{   
							$ls_hcm='0';   
						}   
						if (($ls_hcmper!="")&&($ls_hcm=='1')) 
						{                      
							if (($li_sueper >= $ls_sueldo)&&($ls_edadper >= $ls_edadmin)&&($ls_edadper <= $ls_edadmax)&&($ls_sexper==$ls_sexo)&&($ls_hcm==$ls_hcmper))   
							{                         
								$ls_valor_emple=  round ($li_prima * $li_aporemple)/100;
							 	$ls_valor_empre=  round ($li_prima * $li_aporempre)/100;
							 	$la_data[$li_i]=array('coddeduc'=>$ls_codtipded,'dendeduc'=>$ls_dentipded,'valor'=>number_format($li_prima,2,",","."), 
												      'aporemple'=>number_format($ls_valor_emple,2,",","."),'aporempre'=>number_format($ls_valor_empre,2,",","."));
								$li_i++;
								$suma_total_emple= $suma_total_emple + $ls_valor_emple;
								$suma_total_empre= $suma_total_empre + $ls_valor_empre;
							}   
						}   
						else   
						{     
							if (($li_sueper >= $ls_sueldo)&&($ls_edadper >= $ls_edadmin)&&($ls_edadper <= $ls_edadmax)&&($ls_sexper==$ls_sexo))   
							{      
								$ls_valor_emple=  round ($li_prima * $li_aporemple)/100;
							 	$ls_valor_empre=  round ($li_prima * $li_aporempre)/100;
							 	$la_data[$li_i]=array('coddeduc'=>$ls_codtipded,'dendeduc'=>$ls_dentipded,'valor'=>number_format($li_prima,2,",","."), 
												      'aporemple'=>number_format($ls_valor_emple,2,",","."),'aporempre'=>number_format($ls_valor_empre,2,",","."));
								$li_i++;
								$suma_total_emple= $suma_total_emple + $ls_valor_emple;
								$suma_total_empre= $suma_total_empre + $ls_valor_empre;
							}   
						} 
						$rs_data2->MoveNext();
	 				} // Cierre del While			 
		  		    //Para las deducciones de los familiares
					$lb_valido=$io_report->uf_select_deduccion_personal_familiar($ls_codper, $rs_data3);
					if ($lb_valido)
					{
						while ((!$rs_data3->EOF)&&($lb_valido))
						{ 
							$ls_codtipded=$rs_data3->fields["codtipded"];
							switch ($rs_data3->fields["nexfam"]) 
							{
								case 'C' :
									$ls_nexo='CONYUGE';
								break;
								case 'H' :
									$ls_nexo='HIJO';
								break;
								case 'P' :
									$ls_nexo='PROGENITOR';
								break;
								case 'E' :
									$ls_nexo='HERMANO';
								break;
								default :
									$ls_nexo="";
							}							
							$ls_dentipded=$rs_data3->fields["dentipded"].'  (DEDUCCION FAMILIAR  '.$ls_nexo.' )';	
							$ls_sueldobene=$rs_data3->fields["suelbene"];
							$ls_edadmin=$rs_data3->fields["edadmin"];
							$ls_edadmax=$rs_data3->fields["edadmax"];
							$ls_sexoben=$rs_data3->fields["sexbene"];
							$ls_nexo=$rs_data3->fields["nexfam"];
							$ls_hcm=$rs_data3->fields["hcm"];////de la tabla dt_tipodeduccion
							$ls_valorprima=$rs_data3->fields["valprim"];
							$apor_empresa=$rs_data3->fields["aporempre"];
							$apor_empleado=$rs_data3->fields["aporemple"];
							$hc_familiar=$rs_data3->fields["hcfam"];
							$hcm_familiar=$rs_data3->fields["hcmfam"];
							$nexo_familiar=$rs_data3->fields["nexofam"];
							$sexo_familiar=$rs_data3->fields["sexofam"];
							$fechanac_familiar=$rs_data3->fields["fecnacfam"];
							$ld_fecact=	date("Y-m-d");
							$edad_familiar=calcular_edad(strtotime($fechanac_familiar),strtotime($ld_fecact));				
							if (trim($ls_hcm)=='S')
							{   
								$ls_hcm='1';   
							}   
							elseif (trim($ls_hcm)=='N')   
							{   
								$ls_hcm='0';   
							}               
							if ($hcm_familiar!="")   
							{       
								if (($li_sueper>=$ls_sueldobene)&&($edad_familiar>=$ls_edadmin)&&($edad_familiar<=$ls_edadmax)&&($ls_sexoben==$sexo_familiar)&&($ls_nexo==$nexo_familiar)&&($ls_hcm==$hcm_familiar))   
								{                         
									$ls_valor_emple=  round ($ls_valorprima * $apor_empleado)/100;
									$ls_valor_empre=  round ($ls_valorprima * $apor_empresa)/100;
									$la_data[$li_i]=array('coddeduc'=>$ls_codtipded,'dendeduc'=>$ls_dentipded,'valor'=>number_format($ls_valorprima,2,",","."), 
														  'aporemple'=>number_format($ls_valor_emple,2,",","."),'aporempre'=>number_format($ls_valor_empre,2,",","."));
									$li_i++;
									$suma_total_emple= $suma_total_emple + $ls_valor_emple;
									$suma_total_empre= $suma_total_empre + $ls_valor_empre;
								}                           
							}                   
							else                                                
							{                   
								if (($li_sueper>=$ls_sueldobene)&&($edad_familiar>=$ls_edadmin)&&($edad_familiar<=$ls_edadmax)&&($ls_sexoben==$sexo_familiar)&&($ls_nexo==$nexo_familiar))   
								{   
									$ls_valor_emple=  round ($ls_valorprima * $apor_empleado)/100;
									$ls_valor_empre=  round ($ls_valorprima * $apor_empresa)/100;
									$la_data[$li_i]=array('coddeduc'=>$ls_codtipded,'dendeduc'=>$ls_dentipded,'valor'=>number_format($ls_valorprima,2,",","."), 
														  'aporemple'=>number_format($ls_valor_emple,2,",","."),'aporempre'=>number_format($ls_valor_empre,2,",","."));
									$li_i++;
									$suma_total_emple= $suma_total_emple + $ls_valor_emple;
									$suma_total_empre= $suma_total_empre + $ls_valor_empre;
								}  
							}
							$rs_data3->MoveNext();				
						}///fin del while
			 		} 
					$suma_total_emple=number_format($suma_total_emple,2,",",".");
					$suma_total_empre=number_format($suma_total_empre,2,",",".");
					if (($suma_total_emple!='0,00') || ($suma_total_empre!='0,00'))
					{
						uf_print_detalle($la_data,$num, $suma_total_emple, $suma_total_empre,&$io_pdf);
						unset($la_data);
					}
					else
					{
						$lb_valido=false;
					}
				}				
				if($li_aux<$lp_totrow)		
				{
					$io_pdf->ezNewPage(); // Insertar una nueva página
				}
				$rs_data->MoveNext();			
			}			
		}
		if($lb_valido) // Si no ocurrio ningún error
		{
			$io_pdf->ezStopPageNumbers(1,1); // Detenemos la impresión de los números de página
			$io_pdf->ezStream(); // Mostramos el reporte
		}
		else // Si hubo algún error
		{
			print("<script language=JavaScript>");
			print(" alert('No hay nada que reportar');"); 
			print(" close();");
			print("</script>");	
		}
	}
?>