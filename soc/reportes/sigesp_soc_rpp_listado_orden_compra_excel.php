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
	function uf_print_encabezado_pagina($lo_libro,$lo_hoja,&$li_fila)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   as_desnom // Descripción de la nómina
		//	    		   as_periodo // Descripción del período
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Yozelin Barragan
		// Fecha Creación: 16/07/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
		$lo_hoja->write($li_fila, 0, 'Código',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
		$lo_hoja->write($li_fila, 1, 'Nombre',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
		$lo_hoja->write($li_fila, 2, 'Tipo',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
		$lo_hoja->write($li_fila, 3, 'Fecha',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
		$lo_hoja->write($li_fila, 4, 'Estatus',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
		$lo_hoja->write($li_fila, 5, 'Unidad Adm.',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
		$lo_hoja->write($li_fila, 6, 'Monto',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------
	require_once("../../shared/class_folder/sigesp_include.php");
	require_once("../../shared/class_folder/class_sql.php");	
	require_once("../../shared/ezpdf/class.ezpdf.php");
	require_once("../../shared/class_folder/class_funciones.php");
	require_once("sigesp_soc_class_report.php");	
	require_once("../class_folder/class_funciones_soc.php");
	$in           = new sigesp_include();
	$con          = $in->uf_conectar();
	$io_sql       = new class_sql($con);	
	$io_funciones = new class_funciones();	
	$io_fun_soc   = new class_funciones_soc();
	$io_report    = new sigesp_soc_class_report($con);
	$ls_tiporeporte=$io_fun_soc->uf_obtenervalor_get("tiporeporte",0);
	$ls_bolivares="Bs.";
	require_once ("../../shared/writeexcel/class.writeexcel_workbookbig.inc.php");
	require_once ("../../shared/writeexcel/class.writeexcel_worksheet.inc.php");
	$lo_archivo =  tempnam("/tmp", "ordenes.xls");
	$lo_libro = &new writeexcel_workbookbig($lo_archivo);
	$lo_hoja = &$lo_libro->addworksheet();
	if($ls_tiporeporte==1)
	{
		require_once("sigesp_soc_class_reportbsf.php");
		$io_report=new sigesp_soc_class_reportbsf();
		$ls_bolivares="Bs.F.";
	}
		
	//----------------------------------------------------  Inicializacion de variables  -----------------------------------------------
	$lb_valido=true;
	//----------------------------------------------------  Parámetros del encabezado    -----------------------------------------------
	$ls_titulo ="LISTADO DE LAS ORDENES DE COMPRAS";	
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	
	$ls_numordcomdes=$io_fun_soc->uf_obtenervalor_get("txtnumordcomdes","");
	$ls_numordcomhas=$io_fun_soc->uf_obtenervalor_get("txtnumordcomhas","");
	$ls_codprodes=$io_fun_soc->uf_obtenervalor_get("txtcodprodes","");
	$ls_codprohas=$io_fun_soc->uf_obtenervalor_get("txtcodprohas","");
	$ls_fecordcomdes=$io_fun_soc->uf_obtenervalor_get("txtfecordcomdes","");
	$ls_fecordcomhas=$io_fun_soc->uf_obtenervalor_get("txtfecordcomhas","");
	$ls_coduniadmdes=$io_fun_soc->uf_obtenervalor_get("txtcoduniejedes","");
	$ls_coduniadmhas=$io_fun_soc->uf_obtenervalor_get("txtcoduniejehas","");
	$ls_codartdes=$io_fun_soc->uf_obtenervalor_get("txtcodartdes","");
	$ls_codarthas=$io_fun_soc->uf_obtenervalor_get("txtcodarthas","");
	$ls_codserdes=$io_fun_soc->uf_obtenervalor_get("txtcodserdes","");
	$ls_codserhas=$io_fun_soc->uf_obtenervalor_get("txtcodserhas","");
	
	$ls_rdanucom="0";
	$ls_rdemi=$io_fun_soc->uf_obtenervalor_get("rdemi","");
	$ls_rdpre=$io_fun_soc->uf_obtenervalor_get("rdpre","");	
	$ls_rdcon=$io_fun_soc->uf_obtenervalor_get("rdcon","");
	$ls_rdanu=$io_fun_soc->uf_obtenervalor_get("rdanu","");
	$ls_rdinv=$io_fun_soc->uf_obtenervalor_get("rdinv","");
	
	$ls_estcondat=$io_fun_soc->uf_obtenervalor_get("rdtipo","");
	$ls_tipo=$io_fun_soc->uf_obtenervalor_get("esttip","");
	$ls_ordenes=$io_fun_soc->uf_obtenervalor_get("ordenes","");
    $lr_ordenes= split('>>',$ls_ordenes);
    $lr_datos= array_unique($lr_ordenes);

	//--------------------------------------------------------------------------------------------------------------------------------
	if($ls_tiporeporte=="NORMAL")
	{
		$rs_data = $io_report->uf_select_listado_orden_compra($ls_numordcomdes,$ls_numordcomhas,$ls_codprodes,
																$ls_codprohas,$ls_fecordcomdes,$ls_fecordcomhas,$ls_coduniadmdes,
																$ls_coduniadmhas,$ls_rdanucom,$ls_rdemi,$ls_rdpre,$ls_rdcon,$ls_rdanu,
																$ls_rdinv,$ls_codartdes,$ls_codarthas,$ls_codserdes,
																$ls_codserhas,$ls_estcondat,$ls_tipo,&$lb_valido);
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
		$ls_descripcion="Generó el Reporte de Listado de Orden de Compra";
		$lb_valido=$io_fun_soc->uf_load_seguridad_reporte("SOC","sigesp_soc_r_orden_compra.php",$ls_descripcion);
		if($lb_valido)	
		{
			set_time_limit(1800);

			$lo_encabezado= &$lo_libro->addformat();
			$lo_encabezado->set_bold();
			$lo_encabezado->set_font("Verdana");
			$lo_encabezado->set_align('center');
			$lo_encabezado->set_size('11');
			$lo_titulo= &$lo_libro->addformat();
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
			$lo_hoja->set_column(0,0,15);
			$lo_hoja->set_column(1,1,20);
			$lo_hoja->set_column(2,2,30);
			$lo_hoja->set_column(3,3,20);
			$lo_hoja->set_column(4,4,30);
			$lo_hoja->set_column(5,5,30);
			$lo_hoja->set_column(6,6,30);

			$lo_hoja->write(0, 3, $ls_titulo,$lo_encabezado);

			$li_fila=2;
			uf_print_encabezado_pagina($lo_libro,$lo_hoja,&$li_fila); // Imprimimos el encabezado de la página
			$ldec_monto=0;
			$li_i=0;
			if($ls_tiporeporte=="SELECTIVO")
			{
   				$li_row= count($lr_datos);
			}
			else
			{
				$li_row=$io_sql->num_rows($rs_data);
			}
			if ($li_row>0)
			{     
				$li_fila=$li_fila+1;
				if($ls_tiporeporte=="SELECTIVO")
				{
					for($li_i=0;$li_i<$li_row;$li_i++)
					{
						$li_fila++;
						$ls_ordcom=$lr_datos[$li_i];
						$lr_ordenesdet= split('>',$ls_ordcom);
						$lr_detalle= array_unique($lr_ordenesdet);
						$ls_numordcom=$lr_detalle[0];
						$ls_estcondat=$lr_detalle[1];
						$rs_data=$io_report->uf_select_orden_compra($ls_numordcom,$ls_estcondat,&$lb_valido);
						while($row=$io_sql->fetch_row($rs_data))
						{
							  $li_i=$li_i+1;
							  $ls_nompro     ="";
							  $ls_estcon     ="";
							  $ls_estcondat  =""; 
							  $ls_numord  = $row["numordcom"]; 
							  $ls_estcon  = $row["estcondat"];
							  $ls_codpro  = $row["cod_pro"];
							  $ls_estatus = $row["estcom"];
							  $ls_estapro = $row["estapro"];
							  $ls_fecord  = $row["fecordcom"];
							  $ld_monto   = $row["montot"];
				  			  $ls_unidades = $io_report->uf_unidades_administrativas($ls_numord); 
							  $ldec_monto= $ldec_monto+$ld_monto;
							  $ls_nombre  = $io_report->uf_select_nombre_proveedor($ls_codpro);
							  $ls_fecha   = $io_funciones->uf_convertirfecmostrar($ls_fecord);	
							  
							  if($ls_estcon=="B") {  $ls_estcondat="Bienes";  }
							  
			
							  if($ls_estcon=="S") {  $ls_estcondat="Servicios";	  }
			
							  if( ($ls_estcon=="-") || ($ls_estcon=="") )  {   $ls_estcondat="";  }
			
							  if($ls_estatus==0){
								 $status="Registro";			 
							   }
							   
							  if($ls_estatus==1){
								 $status="Emitida";	
								 if($ls_estapro==1)
								 {
									$status="Emitida (Aprobada)";	
								 }		 
							   }
						
							  if($ls_estatus==2){
								 $status="Comprometida (Procesada)";			 
							   }
						
							  if($ls_estatus==3){
								 $status="Anulada";			 
							   }
						
							  if($ls_estatus==4){
								 $status="Ent. Compra";			 
							   }
							   
							  if($ls_estatus==5){
								 $status="Pre-Comprometida";			 
							   }
						
							  if($ls_estatus==6){
								 $status="Pre-Comp.Anulada";			 
							   }
							   
							  if($ls_estatus==7){
								 $status="Servicio Recibido";			 
							   }
							$lo_hoja->write($li_fila, 0, $ls_numord, $lo_datacenter);
							$lo_hoja->write($li_fila, 1, $ls_nombre, $lo_dataleft);
							$lo_hoja->write($li_fila, 2, $ls_estcondat, $lo_datacenter);
							$lo_hoja->write($li_fila, 3, $ls_fecha, $lo_datacenter);
							$lo_hoja->write($li_fila, 4, $status, $lo_dataleft);
							$lo_hoja->write($li_fila, 5, $ls_unidades, $lo_dataleft);
							$lo_hoja->write($li_fila, 6, $ld_monto, $lo_dataright);
												 
						}
						$li_i=$li_i-1;
					}
				}
				else
				{
					while($row=$io_sql->fetch_row($rs_data))
					{
					  $li_fila++;
					  $li_i=$li_i+1;
					  $ls_nompro     ="";
					  $ls_estcon     ="";
					  $ls_estcondat  =""; 
					  $ls_numord  = $row["numordcom"];
					  $ls_estcon  = $row["estcondat"];
					  $ls_codpro  = $row["cod_pro"];
					  $ls_estatus = $row["estcom"];
					  $ls_estapro = $row["estapro"];
					  $ls_fecord  = $row["fecordcom"];
					  $ld_monto   = $row["montot"];
					  $ldec_monto= $ldec_monto+$ld_monto;
		  			  $ls_unidades = $io_report->uf_unidades_administrativas($ls_numord); 
					  $ls_nombre  = $io_report->uf_select_nombre_proveedor($ls_codpro);
					  $ls_fecha   = $io_funciones->uf_convertirfecmostrar($ls_fecord);	
					  
					  if($ls_estcon=="B") {  $ls_estcondat="Bienes";  }
					  
	
					  if($ls_estcon=="S") {  $ls_estcondat="Servicios";	  }
	
					  if( ($ls_estcon=="-") || ($ls_estcon=="") )  {   $ls_estcondat="";  }
	
					  if($ls_estatus==0){
						 $status="Registro";			 
					   }
					   
					  if($ls_estatus==1){
						 $status="Emitida";	
						 if($ls_estapro==1)
						 {
							$status="Emitida (Aprobada)";	
						 }		 
					   }
				
					  if($ls_estatus==2){
						 $status="Comprometida (Procesada)";			 
					   }
				
					  if($ls_estatus==3){
						 $status="Anulada";			 
					   }
				
					  if($ls_estatus==4){
						 $status="Ent. Compra";			 
					   }
					   
					  if($ls_estatus==5){
						 $status="Pre-Comprometida";			 
					   }
				
					  if($ls_estatus==6){
						 $status="Pre-Comp.Anulada";			 
					   }
					   
					  if($ls_estatus==7){
						 $status="Servicio Recibido";			 
					   }
						$lo_hoja->write($li_fila, 0, $ls_numord, $lo_datacenter);
						$lo_hoja->write($li_fila, 1, $ls_nombre, $lo_dataleft);
						$lo_hoja->write($li_fila, 2, $ls_estcondat, $lo_datacenter);
						$lo_hoja->write($li_fila, 3, $ls_fecha, $lo_datacenter);
						$lo_hoja->write($li_fila, 4, $status, $lo_dataleft);
						$lo_hoja->write($li_fila, 5, $ls_unidades, $lo_dataleft);
						$lo_hoja->write($li_fila, 6, $ld_monto, $lo_dataright);

					}
				}
				$lo_libro->close();
				header("Content-Type: application/x-msexcel; name=\"solicitudes.xls\"");
				header("Content-Disposition: inline; filename=\"solicitudes.xls\"");
				$fh=fopen($lo_archivo, "rb");
				fpassthru($fh);
				unlink($lo_archivo);
				print("<script language=JavaScript>");
				print(" close();");
				print("</script>");		
			}
			else
			{
				print("<script language=JavaScript>");
				print("alert('No hay nada que reportar');"); 
				print("close();");
				print("</script>");		
			}				
		}	
		unset($io_report);
		unset($io_funciones);
	}	
?> 