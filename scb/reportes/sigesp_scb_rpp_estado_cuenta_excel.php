<?php
    session_start();   
	ini_set('memory_limit','2048M');
	ini_set('max_execution_time ','0');	

	//---------------------------------------------------------------------------------------------------------------------------
	// para crear el libro excel
		require_once ("../../shared/writeexcel/class.writeexcel_workbookbig.inc.php");
		require_once ("../../shared/writeexcel/class.writeexcel_worksheet.inc.php");
		$lo_archivo = tempnam("/tmp", "relacion_selectiva_cheques.xls");
		$lo_libro = &new writeexcel_workbookbig($lo_archivo);
		$lo_hoja = &$lo_libro->addworksheet();
	//---------------------------------------------------------------------------------------------------------------------------
	// para crear la data necesaria del reporte
		require_once("sigesp_scb_report.php");
		require_once("../../shared/class_folder/class_fecha.php");
		require_once("../../shared/class_folder/class_funciones.php");
		require_once("../../shared/class_folder/sigesp_include.php");
                require_once("../../shared/class_folder/class_sql.php");
		require_once("../../shared/class_folder/class_datastore.php");    
		$io_conect  = new sigesp_include();
		$con        = $io_conect->uf_conectar();
		$io_report    = new sigesp_scb_report($con);
		$io_funcion = new class_funciones();			
		$io_fecha   = new class_fecha();
                $io_sql     = new class_sql($con);
                $ds_edocta    = new class_datastore();	
                $ls_codemp      = $_SESSION["la_empresa"]["codemp"];
                $ls_codban      = $_GET["codban"];
                $ls_ctaban      = $_GET["ctaban"];
                $ld_fecdesde    = $_GET["fecdes"];		
                $ld_fechasta    = $_GET["fechas"];
                $ls_orden       = $_GET["orden"];
                $ls_tipbol      = 'Bs.';
                $ls_tiporeporte = 0;
                $ls_tiporeporte = $_GET["tiporeporte"];
	//---------------------------------------------------------------------------------------------------------------------------
       	//---------------------------------------------------------------------------------------------------------------------------
             
        global $ls_tiporeporte;
	if ($ls_tiporeporte==1)
	   {
		 require_once("sigesp_scb_reportbsf.php");
		 $io_report = new sigesp_scb_reportbsf($con);
		 $ls_tipbol = 'Bs.F.';
	   }
       // $io_report->uf_cargar_documentos_relacion($arr_documentos,$arr_fecmov,$arr_operaciones,$ls_codban,$ls_ctaban);
	$data=$io_report->uf_generar_estado_cuenta($ls_codemp,$ls_codban,$ls_ctaban,$ls_orden,$ld_fecdesde,$ld_fechasta,&$ldec_saldoant,&$ldec_total_debe,&$ldec_total_haber,false,'C','---');
	$ds_edocta->data=$data;
	$li_totrow=$ds_edocta->getRowCount("numdoc");

        $ls_titulo      = "Estado de Cuenta $ls_tipbol";
        $ldt_fecha=$ld_fecdesde."-".$ld_fechasta;
	$ldec_saldoactual = $ldec_saldoant+$ldec_total_debe-$ldec_total_haber;
	$ldec_saldo       = $ldec_saldoant;
	$ldec_saldoant    = number_format($ldec_saldoant,2,",",".");
	$ls_nomban        = $io_report->uf_select_data($io_sql,"SELECT nomban FROM scb_banco WHERE codban ='".$ls_codban."' AND codemp='".$ls_codemp."'","nomban");
	$ls_nomtipcta     = $io_report->uf_select_data($io_sql,"SELECT nomtipcta FROM scb_tipocuenta t, scb_ctabanco c WHERE c.codemp='".$ls_codemp."' AND c.codtipcta=t.codtipcta AND c.ctaban='".$ls_ctaban."'","nomtipcta");
	
        $li_row     = 0;
        $li_pos=0;
	if ($li_totrow<=0)
	   {
		 print("<script language=JavaScript>");
		 print(" alert('No hay nada que Reportar !!!');"); 
		 print(" close();");
		 print("</script>");
	   }
        else
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
	     $lo_hoja->set_column(0,0,30);
	     $lo_hoja->set_column(1,1,45);
	     $lo_hoja->set_column(2,5,30);

	     $lo_hoja->write(0, 2, $ls_titulo,$lo_encabezado);
	     $lo_hoja->write(1, 2, $ldt_fecha,$lo_encabezado);
	     $lo_hoja->write(3, 0, "Banco  :",$lo_titulo);
	     $lo_hoja->write(3, 1, $ls_denban, $lo_datacenter);
             $lo_hoja->write(3, 0, "Tipo Cuenta  :",$lo_titulo);
             $lo_hoja->write(3, 1, $ls_nomtipcta, $lo_datacenter);
	     $lo_hoja->write(3, 2, "Cuenta :",$lo_titulo);
	     $lo_hoja->write(3, 3, $ls_ctaban, $lo_datacenter);
             $lo_hoja->write(3, 4, "Saldo Anterior :",$lo_titulo);
             $lo_hoja->write(3, 5, $ldec_saldoant, $lo_datacenter);
		 $li_row = 5;
		 $lo_hoja->write(4, 0, "Fecha",$lo_titulo);
		 $lo_hoja->write(4, 1, "Operacion",$lo_titulo);
                 $lo_hoja->write(4, 2, "Nro.Documento",$lo_titulo);
		 $lo_hoja->write(4, 3, "Beneficiario",$lo_titulo);
		 $lo_hoja->write(4, 4, "Debitos",$lo_titulo);
		 $lo_hoja->write(4, 5, "Creditos",$lo_titulo);
                for($li_i=1;$li_i<=$li_totrow;$li_i++)
                    {
			
			$li_totprenom = 0;
			$ldec_mondeb  = 0;
			$ldec_monhab  = 0;
			$li_totant    = 0;
			$li_pos       = $li_pos+1;
			$ls_numdoc        = $ds_edocta->getValue("numdoc",$li_i);
			$ls_codban        = $ds_edocta->getValue("codban",$li_i);
			$ls_nomban        = $ds_edocta->getValue("nomban",$li_i);
			$ls_ctaban        = $ds_edocta->getValue("ctaban",$li_i);
			$ls_nomproben     = $ds_edocta->getValue("beneficiario",$li_i);		
			$ls_conmov        = $ds_edocta->getValue("conmov",$li_i);
			$ldec_monret      = $ds_edocta->getValue("monret",$li_i);
			$ldec_monto       = $ds_edocta->getValue("monto",$li_i);
			$ls_nomtipcta     = $ds_edocta->getValue("nomtipcta",$li_i);
			$ls_operacion     = $ds_edocta->getValue("operacion",$li_i);
			$ld_fecmov        = $io_funcion->uf_formatovalidofecha($ds_edocta->getValue("fecmov",$li_i));
			$ld_fecmov        = $io_funcion->uf_convertirfecmostrar($ld_fecmov);
			$ldec_total       = $ldec_monto-$ldec_monret;
			$ldec_debitos     = $ds_edocta->getValue("debitos",$li_i);
			$ldec_creditos    = $ds_edocta->getValue("creditos",$li_i);
			$ldec_saldo       = $ldec_saldo+$ldec_debitos-$ldec_creditos;
			$ldec_mondeb      = number_format($ldec_debitos,2,",",".");
			$ldec_monhab      = number_format($ldec_creditos,2,",",".");
                        
                         $li_row=$li_row+1;
			     $lo_hoja->write($li_row, 0, $ld_fecmov, $lo_datacenter);
			     $lo_hoja->write($li_row, 1, " ".$ls_operacion, $lo_datacenter);
			     $lo_hoja->write($li_row, 2, $ls_numdoc, $lo_datacenter);
                             $lo_hoja->write($li_row, 3, $ls_nomproben, $lo_dataleft);
			     $lo_hoja->write($li_row, 4, $ldec_mondeb, $lo_dataright);
			     $lo_hoja->write($li_row, 5, $ldec_monhab, $lo_dataright);
                        
			}
		   $li_row=$li_row+1;
		   $lo_hoja->write($li_row, 4, "Total Creditos",$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'10')));
		   $lo_hoja->write($li_row, 5, $ldec_total_haber, $lo_dataright);
                   $li_row=$li_row+1;
		   $lo_hoja->write($li_row, 4, "Total Debitos",$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'10')));
		   $lo_hoja->write($li_row, 5, $ldec_total_debe, $lo_dataright);
	
 		   $li_row=$li_row+1;			
		   $lo_hoja->write($li_row, 4, "Total Saldo",$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'10')));
		   $lo_hoja->write($li_row, 5, $ldec_saldoactual, $lo_dataright);

		   $lo_libro->close();
		   header("Content-Type: application/x-msexcel; name=\"estados_cuentas.xls\"");
		   header("Content-Disposition: inline; filename=\"estados_cuentas.xls\"");
		   $fh=fopen($lo_archivo, "rb");
		   fpassthru($fh);
		   unlink($lo_archivo);
		   print("<script language=JavaScript>");
		   print(" close();");
		   print("</script>");
	   } 
           
?>