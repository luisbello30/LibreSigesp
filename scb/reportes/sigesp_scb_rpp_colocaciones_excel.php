<?php
    session_start();   
	ini_set('memory_limit','256M');
	ini_set('max_execution_time ','0');

	//---------------------------------------------------------------------------------------------------------------------------
	// para crear el libro excel
		require_once ("../../shared/writeexcel/class.writeexcel_workbookbig.inc.php");
		require_once ("../../shared/writeexcel/class.writeexcel_worksheet.inc.php");
		$lo_archivo = tempnam("/tmp", "Colocaciones.xls");
		$lo_libro = &new writeexcel_workbookbig($lo_archivo);
		$lo_hoja = &$lo_libro->addworksheet();
	//---------------------------------------------------------------------------------------------------------------------------
	// para crear la data necesaria del reporte
		require_once("sigesp_scb_report.php");
		require_once("../../shared/ezpdf/class.ezpdf.php");
		require_once("../../shared/class_folder/class_fecha.php");
		require_once("../../shared/class_folder/class_sql.php");
		require_once("../../shared/class_folder/sigesp_include.php");
		require_once("../../shared/class_folder/class_funciones.php");
		require_once("../../shared/class_folder/class_datastore.php");
		
		$io_conect       = new sigesp_include();
		$con             = $io_conect->uf_conectar();
		$io_report       = new sigesp_scb_report($con);
		$io_funciones    = new class_funciones();
		$ds_colocacion   = new class_datastore();	
		$io_sql          = new class_sql($con);
		$io_fecha        = new class_fecha();
		//$ls_titulo = "Colocaciones Bancarias ";				

	//---------------------------------------------------------------------------------------------------------------------------
	//Parámetros para Filtar el Reporte
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$ls_titemp=$_SESSION["la_empresa"]["titulo"];
		$ls_codbandes=$_GET["codbandes"];
		$ls_codbanhas=$_GET["codbanhas"];
		$ls_ctabandes=$_GET["ctabandes"];
		$ls_ctabanhas=$_GET["ctabanhas"];
		$ld_fecdesde=$_GET["fecdes"];		
		$ld_fechasta=$_GET["fechas"];
		$ls_orden=$_GET["orden"];	
		$ls_tipbol      = 'Bsf.';
	//---------------------------------------------------------------------------------------------------------------------------
	//Parámetros del encabezado
		$ldt_fecha="Desde  ".$ld_fecdesde."  al ".$ld_fechasta."";
		$ls_titulo="INVENTARIOS DE COLOCACIONES BANCARIAS ";       
	//---------------------------------------------------------------------------------------------------------------------------
	//Busqueda de la data 
	$lb_valido            = true;
	$data=$io_report->uf_generar_estado_cuenta_colocacion($ls_codemp,$ls_codbandes,$ls_ctabandes,$ls_codbanhas,$ls_ctabanhas,$ld_fecdesde,$ld_fechasta,$ls_orden);
	$ds_colocacion->data  = $data;
	$li_totrow        	  = $ds_colocacion->getRowCount("numcol");
	//---------------------------------------------------------------------------------------------------------------------------
	function restaFechas($dFecIni, $dFecFin)
	{
		$dFecIni = str_replace("-","",$dFecIni);
		$dFecIni = str_replace("/","",$dFecIni);
		$dFecFin = str_replace("-","",$dFecFin);
		$dFecFin = str_replace("/","",$dFecFin);
		ereg( "([0-9]{1,2})([0-9]{1,2})([0-9]{2,4})", $dFecIni, $aFecIni);
		ereg( "([0-9]{1,2})([0-9]{1,2})([0-9]{2,4})", $dFecFin, $aFecFin);
		$date1 = mktime(0,0,0,$aFecIni[2], $aFecIni[1], $aFecIni[3]);
		$date2 = mktime(0,0,0,$aFecFin[2], $aFecFin[1], $aFecFin[3]);
		return round(($date2 - $date1) / (60 * 60 * 24));
	}
	//---------------------------------------------------------------------------------------------------------------------------
  	// Impresión de la información encontrada en caso de que exista
	if(empty($ds_colocacion->data)) // Existe algún error ó no hay registros
	{
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar !!!');"); 
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
		$lo_hoja->set_column(1,1,20);
		$lo_hoja->set_column(2,2,20);
		$lo_hoja->set_column(3,5,30);
		$lo_hoja->set_column(6,6,40);
		$lo_hoja->set_column(7,9,30);
		
		$lo_hoja->set_column(9,9,40);
		$lo_hoja->set_column(10,10,40);
		$lo_hoja->set_column(11,11,50);
		$lo_hoja->set_column(12,12,20);
		$lo_hoja->set_column(7,7,20);
		$lo_hoja->set_column(5,5,30);
		$lo_hoja->set_column(6,6,20);
		$lo_hoja->set_column(8,8,20);
		$lo_hoja->set_column(13,13,20);
		
		$lo_hoja->write(0, 3, $ls_titulo,$lo_encabezado);
		$lo_hoja->write(1, 3, $ldt_fecha,$lo_encabezado);
		
		$li_row = 5;
		$lo_hoja->write(5, 0, "Proyecto",$lo_titulo);
		$lo_hoja->write(5, 1, "Capital",$lo_titulo);
		$lo_hoja->write(5, 2, "Fecha Emisión",$lo_titulo);
		$lo_hoja->write(5, 3, "Fecha Vencimiento",$lo_titulo);
		$lo_hoja->write(5, 4, "Días",$lo_titulo);
		$lo_hoja->write(5, 5, "Tasa",$lo_titulo);
		$lo_hoja->write(5, 6, "Rendimiento",$lo_titulo);
		$lo_hoja->write(5, 7, "Conjunto",$lo_titulo);
		$lo_hoja->write(5, 8, "N° Expediente",$lo_titulo);
		$lo_hoja->write(5, 9, "Cuenta Cedente",$lo_titulo);
		$lo_hoja->write(5, 10, "Concepto",$lo_titulo);
		$lo_hoja->write(5, 11, "Entidad",$lo_titulo);
		
        for ($li_i=1;$li_i<=$li_totrow;$li_i++)
		    {
			 	$ls_banco		= $ds_colocacion->getValue("nomban",$li_i);
				$ls_colocacion	= $ds_colocacion->getValue("numcol",$li_i);
				$ls_fecinicol   = $ds_colocacion->getValue("feccol",$li_i);
			    $ls_fecinicol   = $io_funciones->uf_convertirfecmostrar($ls_fecinicol);
			    $ls_fecfincol   = $ds_colocacion->getValue("fecvencol",$li_i);
			    $ls_fecfincol   = $io_funciones->uf_convertirfecmostrar($ls_fecfincol);
				$ls_diacol      = $ds_colocacion->getValue("diacol",$li_i);
				$ls_tascol   	= $ds_colocacion->getValue("tascol",$li_i);
				$ls_tascol   	= number_format($ls_tascol,2,",",".")." %";
				$ls_rendimiento = $ds_colocacion->getValue("monint",$li_i);
				$ls_rendimiento = number_format($ls_rendimiento,2,",",".");
				$ls_monto       = $ds_colocacion->getValue("monto",$li_i);
				$ls_conjunto    = $ls_rendimiento+$ls_monto;
				$ls_conjunto    = number_format($ls_conjunto,2,",",".");
				$ls_ctabancedente = $ds_colocacion->getValue("ctaban",$li_i);
				$ls_concepto	= $ds_colocacion->getValue("dencta",$li_i);
				$ls_tipocol  	= $ds_colocacion->getValue("denominacion",$li_i);
			 $li_row=$li_row+1;

			  $lo_hoja->write($li_row, 0, $ls_banco, $lo_datacenter);
			  $lo_hoja->write($li_row, 1, $ls_monto, $lo_dataright);
			  $lo_hoja->write($li_row, 2, $ls_fecinicol, $lo_datacenter);
			  $lo_hoja->write($li_row, 3, $ls_fecfincol, $lo_datacenter);
			  $lo_hoja->write($li_row, 4, $ls_diacol, $lo_datacenter);
			  $lo_hoja->write($li_row, 5, $ls_tascol, $lo_datacenter);
			  $lo_hoja->write($li_row, 6, $ls_rendimiento, $lo_dataright);
			  $lo_hoja->write($li_row, 7, $ls_conjunto, $lo_dataright);
			  $lo_hoja->write($li_row, 8, $ls_colocacion, $lo_datacenter);
			  $lo_hoja->write($li_row, 9, " ".$ls_ctabancedente, $lo_datacenter);
			  $lo_hoja->write($li_row, 10, $ls_concepto,$lo_datacenter);
			  $lo_hoja->write($li_row, 11, $ls_tipocol,$lo_datacenter);
			  
		    }
		$lo_libro->close();
		header("Content-Type: application/x-msexcel; name=\"Colocaciones.xls\"");
		header("Content-Disposition: inline; filename=\"Colocaciones.xls\"");
		$fh=fopen($lo_archivo, "rb");
		fpassthru($fh);
		unlink($lo_archivo);
		unset($io_funciones);
		print("<script language=JavaScript>");
		print(" close();");
		print("</script>");
    }
?> 