<?php
    session_start();   
	ini_set('memory_limit','512M');
	ini_set('max_execution_time','0');

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_seguridad($as_titulo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_insert_seguridad
		//		   Access: private 
		//	    Arguments: as_titulo // Título del Reporte
		//    Description: función que guarda la seguridad de quien generó el reporte
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 22/09/2006 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_scg;
		
		$ls_descripcion="Generó el Reporte ".$as_titulo;
		$lb_valido=$io_fun_scg->uf_load_seguridad_reporte("SCG","sigesp_scg_r_comprobante_formato1.php",$ls_descripcion);
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_init_niveles()
	{	///////////////////////////////////////////////////////////////////////////////////////////////////////
		//	   Function: uf_init_niveles
		//	     Access: public
		//	    Returns: vacio	 
		//	Description: Este método realiza una consulta a los formatos de las cuentas
		//               para conocer los niveles de la escalera de las cuentas contables  
		//////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_funciones,$ia_niveles_scg;
		
		$ls_formato=""; $li_posicion=0; $li_indice=0;
		$dat_emp=$_SESSION["la_empresa"];
		//contable
		$ls_formato = trim($dat_emp["formcont"])."-";
		$li_posicion = 1 ;
		$li_indice   = 1 ;
		$li_posicion = $io_funciones->uf_posocurrencia($ls_formato, "-" , $li_indice ) - $li_indice;
		do
		{
			$ia_niveles_scg[$li_indice] = $li_posicion;
			$li_indice   = $li_indice+1;
			$li_posicion = $io_funciones->uf_posocurrencia($ls_formato, "-" , $li_indice ) - $li_indice;
		} while ($li_posicion>=0);
	}// end function uf_init_niveles
	//-----------------------------------------------------------------------------------------------------------------------------------

	//---------------------------------------------------------------------------------------------------------------------------
	// para crear el libro excel
		require_once ("../../shared/writeexcel/class.writeexcel_workbookbig.inc.php");
		require_once ("../../shared/writeexcel/class.writeexcel_worksheet.inc.php");
		$lo_archivo = tempnam("/tmp", "comprobante_formato1.xls");
		$lo_libro = &new writeexcel_workbookbig($lo_archivo);
		$lo_hoja = &$lo_libro->addworksheet();
	//---------------------------------------------------------------------------------------------------------------------------
	// para crear la data necesaria del reporte
		require_once("../../shared/class_folder/class_funciones.php");
		$io_funciones=new class_funciones();	
		require_once("../class_funciones_scg.php");
		$io_fun_scg=new class_funciones_scg();
		$ls_tiporeporte="0";
		$ls_bolivares="";
		if (array_key_exists("tiporeporte",$_GET))
		{
			$ls_tiporeporte=$_GET["tiporeporte"];
		}
		switch($ls_tiporeporte)
		{
			case "0":
				require_once("sigesp_scg_reporte.php");
				$io_report  = new sigesp_scg_reporte();
				$ls_bolivares ="Bs.";
				break;
	
			case "1":
				require_once("sigesp_scg_reportebsf.php");
				$io_report  = new sigesp_scg_reportebsf();
				$ls_bolivares ="Bs.F.";
				break;
		}
		$ia_niveles_scg[0]="";			
		uf_init_niveles();
		$li_total=count($ia_niveles_scg)-1;
	//---------------------------------------------------------------------------------------------------------------------------
	//Parámetros para Filtar el Reporte
		$ls_compdes=$_GET["txtcompdes"];
		$ls_comphas=$_GET["txtcomphas"];
		$ls_procdes=$_GET["txtprocdes"];
		$ls_prochas=$_GET["txtprochas"];
		$fecdes=$_GET["txtfecdes"];
		if(!empty($fecdes))
		{
			$ldt_fecdes=$io_funciones->uf_convertirdatetobd($fecdes);
		}
		$fechas=$_GET["txtfechas"];
		if(!empty($fechas))
		{
			$ldt_fechas=$io_funciones->uf_convertirdatetobd($fechas);
		}
		$ls_orden=$_GET["rborden"];
	//---------------------------------------------------------------------------------------------------------------------------
	//Parámetros del encabezado
		$ldt_periodo=$_SESSION["la_empresa"]["periodo"];
		$li_ano=substr($ldt_periodo,0,4);
		
		$ldt_fecdes=substr($ldt_fecdes,0,10);
		$ldt_fecdes_cab=$io_funciones->uf_convertirfecmostrar($ldt_fecdes);
		$ldt_fechas=substr($ldt_fechas,0,10);
		$ldt_fechas_cab=$io_funciones->uf_convertirfecmostrar($ldt_fechas);
		$ldt_fecha_cab="Desde  ".$ldt_fecdes_cab."  al  ".$ldt_fechas_cab."";
		$ls_titulo="COMPROBANTES  CONTABLE  FORMATO 1";
	//---------------------------------------------------------------------------------------------------------------------------
	//Busqueda de la data
	$lb_valido=uf_insert_seguridad("<b>Comprobante Formato 1 en Excel</b>"); // Seguridad de Reporte
	if($lb_valido)
	{
		 $lb_valido=$io_report->uf_scg_reporte_select_comprobante_formato1($ls_procdes,$ls_prochas,$ls_compdes,$ls_comphas,$ldt_fecdes,
																		   $ldt_fechas,$ls_orden);
	}																		 
	//---------------------------------------------------------------------------------------------------------------------------
	// Impresión de la información encontrada en caso de que exista
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
		$lo_hoja->set_column(1,1,50);
		$lo_hoja->set_column(2,2,25);
		$lo_hoja->set_column(3,4,20);
		$lo_hoja->write(0, 2, $ls_titulo,$lo_encabezado);
		$lo_hoja->write(1, 2, $ldt_fecha_cab,$lo_encabezado);
		$li_row=2;
		$li_tot=$io_report->rs_data->RowCount();
		$ld_totald=0;
		$ld_totalh=0;
		 $li_i=0;
		while($row=$io_report->SQL->fetch_row($io_report->rs_data))
		{
			$$li_i++;
			$ld_totaldebe=0;
			$ld_totalhaber=0;
			$ls_comprobante=$row["comprobante"];
			$ldt_fecha=$row["fecha"];
			$ls_procede=$row["procede"];
			$ls_ced_bene=$row["ced_bene"];
			$ls_cod_pro=$row["cod_pro"];
			$ls_nomproben=$row["nombre"];
			$ls_tipo_destino=$row["tipo_destino"];
			$ls_destino="Beneficiario";
			if($ls_tipo_destino=="P")
			{
				$ls_destino="Proveedor";
			}
			$ldt_fec=$io_funciones->uf_convertirfecmostrar($ldt_fecha);
			$li_row=$li_row+1;
			$lo_hoja->write($li_row, 0, "Comprobante",$lo_titulo);
			$lo_hoja->write($li_row, 1, $ls_procede."---".$ls_comprobante,$lo_dataleft);
			$lo_hoja->write($li_row, 2, $ldt_fec,$lo_datacenter);
			$li_row=$li_row+1;
			$lo_hoja->write($li_row, 0, $ls_destino,$lo_titulo);
			$lo_hoja->write($li_row, 1, $ls_nomproben,$lo_dataleft);
			$li_row=$li_row+1;
			$lo_hoja->write($li_row, 0, "Descripción",$lo_titulo);
			$lo_hoja->write($li_row, 1, $ls_descripcion,$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'9')));
			$lb_valido=$io_report->uf_scg_reporte_comprobante_formato1($ls_procede,$ls_comprobante,$ldt_fecha,$ls_orden);
			if($lb_valido)
			{
				$li_row=$li_row+1;
				$lo_hoja->write($li_row, 0, "Cuenta", $lo_titulo);
				$lo_hoja->write($li_row, 1, "Denominación", $lo_titulo);
				$lo_hoja->write($li_row, 2, "Documento", $lo_titulo);
				$lo_hoja->write($li_row, 3, "Debe", $lo_titulo);
				$lo_hoja->write($li_row, 4, "Haber", $lo_titulo);
				$li_totrow_det=$io_report->rs_data_comp->RowCount();
				while($row_comp=$io_report->SQL->fetch_row($io_report->rs_data_comp))
				{
					$ls_comprobante=$row_comp["comprobante"];
					$ls_sc_cuenta=trim($row_comp["sc_cuenta"]);
					$li_totfil=0;
					$as_cuenta="";
					for($li=$li_total;$li>1;$li--)
					{
						$li_ant=$ia_niveles_scg[$li-1];
						$li_act=$ia_niveles_scg[$li];
						$li_fila=$li_act-$li_ant;
						$li_len=strlen($ls_sc_cuenta);
						$li_totfil=$li_totfil+$li_fila;
						$li_inicio=$li_len-$li_totfil;
						if($li==$li_total)
						{
							$as_cuenta=substr($ls_sc_cuenta,$li_inicio,$li_fila);
						}
						else
						{
							$as_cuenta=substr($ls_sc_cuenta,$li_inicio,$li_fila)."-".$as_cuenta;
						}
					}
					$li_fila=$ia_niveles_scg[1]+1;
					$as_cuenta=substr($ls_sc_cuenta,0,$li_fila)."-".$as_cuenta;
					$ls_procede_doc=$row_comp["procede_doc"];
					$ls_debhab=$row_comp["debhab"];
					$ld_monto=$row_comp["monto"];
					$ls_denominacion=$row_comp["denominacion"];
					$ls_CMP_descripcion=$row_comp["cmp_descripcion"];
					if($ls_debhab=='D')
					{
					   $ld_debe=$ld_monto;
					   $ld_totaldebe=$ld_totaldebe+$ld_monto;
					   $ld_haber="";
					}
					if($ls_debhab=='H')
					{
					   $ld_haber=$ld_monto;
					   $ld_totalhaber=$ld_totalhaber+$ld_monto;
					   $ld_debe="";
					}
					$ls_documentoproc=$ls_procede_doc."-".$ls_comprobante;
					$li_row=$li_row+1;
					$lo_hoja->write($li_row, 0, $as_cuenta, $lo_datacenter);
					$lo_hoja->write($li_row, 1, $ls_denominacion, $lo_dataleft);
					$lo_hoja->write($li_row, 2, $ls_documentoproc, $lo_datacenter);
					$lo_hoja->write($li_row, 3, $ld_debe, $lo_dataright);
					$lo_hoja->write($li_row, 4, $ld_haber, $lo_dataright);
				}
				$li_row=$li_row+1;
				$ld_totald=$ld_totald+$ld_totaldebe;
				$ld_totalh=$ld_totalh+$ld_totalhaber;
				$lo_hoja->write($li_row, 2, "Total Comprobante ".$ls_bolivares,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'10')));
				$lo_hoja->write($li_row, 3, $ld_totaldebe, $lo_dataright);
				$lo_hoja->write($li_row, 4, $ld_totalhaber, $lo_dataright);
				$li_row=$li_row+1;
			}
		}
		$li_row=$li_row+1;
		$lo_hoja->write($li_row, 2, "Total ".$ls_bolivares,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'10')));
		$lo_hoja->write($li_row, 3, $ld_totald, $lo_dataright);
		$lo_hoja->write($li_row, 4, $ld_totalh, $lo_dataright);
		$lo_libro->close();
		header("Content-Type: application/x-msexcel; name=\"comprobante_formato1.xls\"");
		header("Content-Disposition: inline; filename=\"comprobante_formato1.xls\"");
		$fh=fopen($lo_archivo, "rb");
		fpassthru($fh);
		unlink($lo_archivo);
		print("<script language=JavaScript>");
		print(" close();");
		print("</script>");
	}
?> 
