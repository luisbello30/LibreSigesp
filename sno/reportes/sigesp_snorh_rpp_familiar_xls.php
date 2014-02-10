<?php
	session_start();   
	ini_set('memory_limit','512M');
	ini_set('max_execution_time','0');

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
		$lb_valido=$io_fun_nomina->uf_load_seguridad_reporte("SNR","sigesp_snorh_r_listadopersonal.php",$ls_descripcion);
		return $lb_valido;
	}
	//--------------------------------------------------------------------------------------------------------------------------------

	//---------------------------------------------------------------------------------------------------------------------------
	// para crear el libro excel
	require_once ("../../shared/writeexcel/class.writeexcel_workbookbig.inc.php");
	require_once ("../../shared/writeexcel/class.writeexcel_worksheet.inc.php");
	$lo_archivo = tempnam("/tmp", "listado_familiar.xls");
	$lo_libro = &new writeexcel_workbookbig($lo_archivo);
	$lo_hoja = &$lo_libro->addworksheet();
	//---------------------------------------------------------------------------------------------------------------------------
	// para crear la data necesaria del reporte
	require_once("../../shared/ezpdf/class.ezpdf.php");
	require_once("sigesp_snorh_class_report.php");
	$io_report=new sigesp_snorh_class_report();
	require_once("../../shared/class_folder/class_funciones.php");
	$io_funciones=new class_funciones();				
	require_once("../class_folder/class_funciones_nomina.php");
	$io_fun_nomina=new class_funciones_nomina();
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ls_titulo="Reporte de Familiares";
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------

	//echo "radxddk"; die();
	//var_dump($_GET);die();
	$ls_codnomdes=$io_fun_nomina->uf_obtenervalor_get("codnomdes","");
	$ls_codnomhas=$io_fun_nomina->uf_obtenervalor_get("codnomhas","");
	$ls_codperdes=$io_fun_nomina->uf_obtenervalor_get("codperdes","");
	$ls_codperhas=$io_fun_nomina->uf_obtenervalor_get("codperhas","");
	$ls_conyuge=$io_fun_nomina->uf_obtenervalor_get("conyuge","");
	$ls_progenitor=$io_fun_nomina->uf_obtenervalor_get("progenitor","");
	$ls_hijo=$io_fun_nomina->uf_obtenervalor_get("hijo","");
	$ls_hermano=$io_fun_nomina->uf_obtenervalor_get("hermano","");
	$ls_masculino=$io_fun_nomina->uf_obtenervalor_get("masculino","");
	$ls_femenino=$io_fun_nomina->uf_obtenervalor_get("femenino","");
	$li_edaddesde=$io_fun_nomina->uf_obtenervalor_get("edaddesde","");
	$li_edadhasta=$io_fun_nomina->uf_obtenervalor_get("edadhasta","");
	$ls_activo=$io_fun_nomina->uf_obtenervalor_get("activo","");
	$ls_egresado=$io_fun_nomina->uf_obtenervalor_get("egresado","");
	$ls_orden=$io_fun_nomina->uf_obtenervalor_get("orden","1");
	$ls_activono=$io_fun_nomina->uf_obtenervalor_get("activono","");
	$ls_vacacionesno=$io_fun_nomina->uf_obtenervalor_get("vacacionesno","");
	$ls_suspendidono=$io_fun_nomina->uf_obtenervalor_get("suspendidono","");
	$ls_egresadono=$io_fun_nomina->uf_obtenervalor_get("egresadono","");
	$ls_personalmasculino=$io_fun_nomina->uf_obtenervalor_get("personalmasculino","");
	$ls_personalfemenino=$io_fun_nomina->uf_obtenervalor_get("personalfemenino","");



	//---------------------------------------------------------------------------------------------------------------------------
	//Busqueda de la data 

	//--------------------------------------------------------------------------------------------------------------------------------



	$lb_valido=uf_insert_seguridad($ls_titulo); // Seguridad de Reporte
	if($lb_valido)
	{
			$lb_valido=$io_report->uf_familiar_personal($ls_codperdes,$ls_codperhas,$ls_conyuge,$ls_progenitor,$ls_hijo, $ls_hermano, $ls_masculino, $ls_femenino, $li_edaddesde, $li_edadhasta, $ls_codnomdes,$ls_codnomhas, $ls_activo, $ls_egresado,$ls_activono, $ls_vacacionesno, $ls_suspendidono, $ls_egresadono,$ls_personalmasculino, $ls_personalfemenino,$ls_orden); // Cargar el DS con los datos de la cabecera del reporte
			//  die();
		
	}
	if($lb_valido==false) // Existe algún error ó no hay registros
	{
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');"); 
		print(" close();");
		print("</script>");
	}
	else // Imprimimos el reporte
	{
		$lo_encabezado= &$lo_libro->addformat();
		$lo_encabezado->set_bold();
		$lo_encabezado->set_font("Verdana");
		$lo_encabezado->set_align('center');
		$lo_encabezado->set_size('11');
		$lo_titulo= &$lo_libro->addformat();
		$lo_titulo->set_text_wrap();
		$lo_titulo->set_bold();
		$lo_titulo->set_font("Verdana");
		$lo_titulo->set_align('center');
		$lo_titulo->set_size('9');		
		$lo_datacenter= &$lo_libro->addformat();
		$lo_datacenter->set_font("Verdana");
		$lo_datacenter->set_align('center');
		$lo_datacenter->set_size('9');
		$lo_dataleft= &$lo_libro->addformat();
		$lo_dataleft->set_text_wrap();
		$lo_dataleft->set_font("Verdana");
		$lo_dataleft->set_align('left');
		$lo_dataleft->set_size('9');
		$lo_dataright= &$lo_libro->addformat(array(num_format => '#,##0.00'));
		$lo_dataright->set_font("Verdana");
		$lo_dataright->set_align('right');
		$lo_dataright->set_size('9');
		$lo_hoja->set_column(0,0,20);
		$lo_hoja->set_column(1,1,60);
		$lo_hoja->set_column(2,2,15);
		$lo_hoja->set_column(3,3,20);
		$lo_hoja->set_column(4,4,50);
		$lo_hoja->set_column(5,5,20);
		$lo_hoja->set_column(6,6,15);
		$lo_hoja->set_column(7,7,20);
		$lo_hoja->set_column(8,9,50);
		$lo_hoja->set_column(10,10,15);
		$lo_hoja->set_column(11,12,50);
		$lo_hoja->set_column(13,14,20);
		$lo_hoja->set_column(15,18,50);
		$lo_hoja->write(0,1,$ls_titulo,$lo_encabezado);
		$lo_hoja->write(1,1,$ls_rango,$lo_encabezado);			
		



	    

		$li_row=3;
		$li_totrow=$io_report->DS->getRowCount("codper");
		
		for($li_i=1;(($li_i<=$li_totrow)&&($lb_valido));$li_i++)
		{
			
			  $ls_codper="";
			  $ls_nomper="";
			  $ld_fecingper="";
			  $ls_codper=$io_report->DS->data["codper"][$li_i];
			  $ls_nomper=$io_report->DS->data["apeper"][$li_i].", ".$io_report->DS->data["nomper"][$li_i];
			  $ld_fecingper=$io_funciones->uf_convertirfecmostrar($io_report->DS->data["fecnacper"][$li_i]);
			  $lo_hoja->write($li_row, 0, "Personal ",$lo_titulo);
			  $lo_hoja->write($li_row, 1, $ls_codper." - ".$ls_nomper, $lo_dataleft);
			
			  $lb_valido=$io_report->uf_familiar_familiar($ls_codper,$ls_conyuge,$ls_progenitor,$ls_hijo,$ls_hermano,$ls_masculino,$ls_femenino,$li_edaddesde,$li_edadhasta); // Obtenemos el detalle del reporte
			 
			  $li_row = $li_row+1;
			  $lo_hoja->write($li_row, 0, "Cedula",$lo_titulo);
			  $lo_hoja->write($li_row, 1, "Apellidos y Nombres",$lo_titulo);
			  $lo_hoja->write($li_row, 2, "Genero",$lo_titulo);
			  $lo_hoja->write($li_row, 3, "Nexo",$lo_titulo);
			  $lo_hoja->write($li_row, 4, "Fecha de Nacimiento",$lo_titulo);
			  $lo_hoja->write($li_row, 5, "Edad",$lo_titulo);
			  if($lb_valido)
			  {
				$li_totrow_fam=$io_report->DS_detalle->getRowCount("cedfam"); 
				
				$li_row = $li_row+1;
			      for($li_tot_fam=1;(($li_tot_fam<=$li_totrow_fam)&&($lb_valido));$li_tot_fam++)
			      {
				    $ls_codper_fam="";
				    $ls_nomper_fam="";
				    $ls_sexo_fam="";
				    $ls_nexo_fam="";
				    $ld_fecnac_fam="";
				    $li_edad="";
				  

				  $ls_codper_fam=$io_report->DS_detalle->data["cedfam"][$li_tot_fam];
				  $ls_cedula=trim($io_report->DS_detalle->data["cedula"][$li_tot_fam]);
				  if ($ls_cedula!='')
				  {
					  $ls_codper_fam=$ls_cedula;
				  }
				  $ls_sexo_fam=$io_report->DS_detalle->data["sexfam"][$li_tot_fam];
				  switch($ls_sexo_fam)
				  {
					  case "M":
						  $ls_sexfam="Masculino";
						  break;
					  case "F":
						  $ls_sexfam="Femenino";
						  break;
				  }

				  $ls_nexo_fam=$io_report->DS_detalle->data["nexfam"][$li_tot_fam];
				  switch($ls_nexo_fam)
				  {
					  case "C":
						  $ls_nexo_fam="Conyuge";
						  break;
					  case "H":
						  $ls_nexo_fam="Hijo";
						  break;
					  case "P":
						  $ls_nexo_fam="Progenitor";
						  break;
					  case "E":
						  $ls_nexo_fam="Hermano";
						  break;
				  }
				  $ld_fecnac_fam=$io_report->DS_detalle->data["fecnacfam"][$li_tot_fam];
				  $ld_hoy=date('Y');
				  $ld_fecha=substr($ld_fecnac_fam,0,4);
				  $li_edad=$ld_hoy-$ld_fecha;					
				      if(intval(date('m'))<intval(substr($ld_fecnac_fam,5,2)))
				      {
					      $li_edad=$li_edad-1;
				      }
				      else
				      {
					      if(intval(date('m'))==intval(substr($ld_fecnac_fam,5,2)))
					      {
						      if(intval(date('d'))<intval(substr($ld_fecnac_fam,8,2)))
						      {
							      $li_edad=$li_edad-1;
						      }
					      }
				      }
				  $ld_fecnacfam=$io_funciones->uf_convertirfecmostrar($ld_fecnacfam);
				  $ls_nomper_fam=$io_report->DS_detalle->data["apefam"][$li_tot_fam].", ".$io_report->DS_detalle->data["nomfam"][$li_tot_fam];
				  $ld_fecnac_fam=$io_funciones->uf_convertirfecmostrar($io_report->DS_detalle->data["fecnacfam"][$li_tot_fam]);
			      

				  $lo_hoja->write($li_row, 0, $ls_codper_fam, $lo_dataleft);
				  $lo_hoja->write($li_row, 1, $ls_nomper_fam, $lo_datacenter);
				  $lo_hoja->write($li_row, 2, $ls_sexfam, $lo_datacenter);
				  $lo_hoja->write($li_row, 3, $ls_nexo_fam, $lo_datacenter);
				  $lo_hoja->write($li_row, 4, $ld_fecnac_fam, $lo_datacenter);
				  $lo_hoja->write($li_row, 5, $li_edad, $lo_datacenter);
				    

				    $li_row=$li_row+1;
			      }
				      $li_row=$li_row+1;
			          
			}
			
		      }
			
		
		$io_report->DS->resetds("codper");
		$lo_libro->close();
		header("Content-Type: application/x-msexcel; name=\"listado_familiar.xls\"");
		header("Content-Disposition: inline; filename=\"listado_familiar.xls\"");
		$fh=fopen($lo_archivo, "rb");
		fpassthru($fh);
		unlink($lo_archivo);
		print("<script language=JavaScript>");
		print(" close();");
		print("</script>");
		unset($io_pdf);
	}
	unset($io_report);
	unset($io_funciones);
	unset($io_fun_nomina);

?> 