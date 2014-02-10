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
	ini_set('memory_limit','2048M');
	ini_set('max_execution_time','0');

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_seguridad($as_titulo,$as_desnom,$as_periodo,$ai_tipo)
	{
		
		global $io_fun_nomina;
		
		$ls_descripcion="Generó el Reporte ".$as_titulo;
		$lb_valido=$io_fun_nomina->uf_load_seguridad_reporte("SNR","sigesp_snorh_r_listadocumpleano.php",$ls_descripcion);
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	  function calcular_anos($fecha_ingreso,$fecha_egreso)
	  {  
		  $c = date("Y",$fecha_ingreso);	   
		  $b = date("m",$fecha_ingreso);	  
		  $a = date("d",$fecha_ingreso); 	  
		  $anos = date("Y",$fecha_egreso)-$c; 
	   
			  if(date("m",$fecha_egreso)-$b > 0){
		  
			  }elseif(date("m",$fecha_egreso)-$b == 0){
		 
			  if(date("d",$fecha_egreso)-$a <= 0)
			  {		  
			     $anos = $anos-1;	  
			  }
		  
			  }else{		  
			         $anos = $anos-1;		  
			       }  
		  return $anos;	 
      } //FIN DE calcular_anos_servicioas
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------
	
	$ls_tiporeporte="0";
	$ruta = '../../';
	require_once("sigesp_snorh_class_report.php");
	$io_report=new sigesp_snorh_class_report();
	require_once("../../shared/class_folder/class_funciones.php");
	$io_funciones=new class_funciones();				
	require_once("../class_folder/class_funciones_nomina.php");
	$io_fun_nomina=new class_funciones_nomina();
	
	$param = $io_report->io_conexiones->asignar_get();
	
	$ls_bolivares ="Bs.";
	
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ls_desnom=$_SESSION["la_nomina"]["desnom"];
	$ls_peractnom=$_SESSION["la_nomina"]["peractnom"];
	$ld_fecdesper=$io_funciones->uf_convertirfecmostrar($_SESSION["la_nomina"]["fecdesper"]);
	$ld_fechasper=$io_funciones->uf_convertirfecmostrar($_SESSION["la_nomina"]["fechasper"]);
	$ls_titulo="<b>REPORTE DE CUMPLEAÑEROS</b>";
	
	$lb_valido=uf_insert_seguridad($ls_titulo,$ls_desnom,$ls_periodo,$li_tipo); // Seguridad de Reporte
	
	require_once('../../shared/tcpdf/config/lang/ita.php');
	require_once('../../shared/tcpdf/tcpdf.php');  
	//error_reporting(E_ALL);
	//set_time_limit(18000);
	
	$pdf = new TCPDF('PORTRAIT', PDF_UNIT, 'LETTER', true, 'UTF-8', true); 
	$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
	$pdf->SetMargins(PDF_MARGIN_LEFT, 10, PDF_MARGIN_RIGHT);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
	$pdf->setPrintHeader(false);
		//$pdf->setTextoFooter(utf8_encode(''));
		
	$pdf->AddPage();

	$periodo = '  PERÍODO: '.$ls_peractnom.", ".$ld_fecdesper." - ".$ld_fechasper;
	$personal = '  PERSONAL: '.$ls_codperdes.'-'.$ls_codperhas;
	
	$margenes = $pdf->getMargins();
	$pdf->Image('../../shared/imagebank/'.$_SESSION["ls_logo"],$margenes['left'],$margenes['top'], 15, 15);
	$pdf->SetFont('helvetica', '', 12);
	$tit_rep = '<p  style="text-align:center;"><b> '.$ls_titulo.'</b></p>';
	$pdf->writeHTML($tit_rep, true, false, false, false, '');
	$pdf->Ln();
	$pdf->Ln();
	//$periodo = '<p  style="text-align:center;"><b> '.$ls_periodo.'</b></p>';
	//$pdf->writeHTML($periodo, true, false, false, false, '');
	//$pdf->Ln();
	
	//$nomina = '<p  style="text-align:center;"><b> '.$ls_desnom.'</b></p>';
	//$pdf->writeHTML($nomina, true, false, false, false, '');
	//$pdf->Ln();
	
	$pdf->SetFont('helvetica', '', 6);
	
	$pdf->SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(200, 200, 200))); 
	
	$ancho[0] = 14;
	$ancho[1] = 80;
	$ancho[2] = 15;
	$ancho[3] = 0;		// columna para centrar tabla	
	$ancho[4] = 15;
	$ancho[5] = 10;
	$ancho_total = array_sum($ancho);
	$margen_tabla=25;	
	$mensaje="";
	
	function encabezado_oficina($opciones=array()){		
													
						global $pdf,$margen_tabla,$ancho_total,$io_report;
						$pdf->SetFillColor(255,255,255);
						$pdf->Cell($margen_tabla, 3,"", 0,0,'C',1);
						$pdf->SetFont('helvetica', 'B', 7);
						$pdf->SetTextColor(0,0,150);
						$pdf->SetFillColor(200, 200, 150);
						
						//Cell( $w, $h, $txt, $border,$ln,$align, $fill,$link,$stretch,$ignore_min_height)
						$pdf->Cell($ancho_total, 3,$opciones['desuniadm'], 1,0,'C',1);
						$pdf->Ln();
						$pdf->SetFillColor(255, 255, 255);
						$pdf->SetTextColor(0);
						$pdf->SetFont('helvetica', '', 7);
	}
		
	function encabezado($opciones=array()){		
													
						global $pdf,$margen_tabla,$ancho,$io_report;
						$pdf->SetFillColor(255,255,255);
						$pdf->Cell($margen_tabla, 3,"", 0,0,'C',1);
						$pdf->SetFont('helvetica', 'B', 6);
						$pdf->SetTextColor(255,255,255);
						$pdf->SetFillColor(0, 0, 150);
						//Cell( $w, $h, $txt, $border,$ln,$align, $fill,$link,$stretch,$ignore_min_height)
						$pdf->Cell($ancho[0], 3,'CÉDULA.', 1,0,'C',1);
						$pdf->Cell($ancho[1], 3,"NOMBRE ", 1,0,'C',1);
						$pdf->Cell($ancho[2], 3,"GÉNERO", 1,0,'C',1);						
						$pdf->Cell($ancho[4], 3,'FEC. NAC.', 1,0,'C',1);
						$pdf->Cell($ancho[5], 3,'EDAD', 1,0,'C',1);													
						$pdf->Ln();
						$pdf->SetFillColor(255, 255, 255);
						$pdf->SetTextColor(0);
						$pdf->SetFont('helvetica', '', 5);
	}
	
	function fila($datos=array()){		
													
						global $pdf,$io_fun_nomina,$margen_tabla,$ancho,$io_report;
						$pdf->SetFillColor(255,255,255);
						$pdf->Cell($margen_tabla, 3,"", 0,0,'C',1);
						$pdf->SetFont('helvetica', 'B', 7);
						$pdf->SetFillColor(255, 255, 255);
						$pdf->SetTextColor(0);
												
						$anocomp=date('Y');
						$mescump = substr($datos['fecnacper'],5,2);
						$diacump = substr($datos['fecnacper'],8,2);
						$messig = (integer)$mescump+1;
						if($messig==13){$messig=1; $anocomp=date('Y')+1;}
						$messig = str_pad($messig,2,'0',STR_PAD_LEFT);					
						$feccomp = $anocomp.'/'.$messig.'/'.$diacump;
											
						$nombre = substr(($datos['nomper'].' '.$datos['apeper']),0,40); 	
						$pdf->Cell($ancho[0], 3,trim($datos['cedper']), 1,0,'R',1);
						$pdf->Cell($ancho[1], 3,$nombre, 1,0,'L',1); 
						$pdf->Cell($ancho[2], 3,$io_report->desc_sexo($datos['sexper']), 1,0,'C',1);						
						$pdf->Cell($ancho[4], 3,$io_report->io_conexiones->formatea_fecha_normal($datos['fecnacper']), 1,0,'C',1);
						$pdf->Cell($ancho[5], 3,calcular_anos(strtotime($datos['fecnacper']),strtotime($feccomp)), 1,0,'C',1);
											
						$pdf->Ln();
						
						
	}
	
	function titulo_oficina($datos=array()){	
	    global $pdf,$io_fun_nomina,$margen_tabla,$ancho,$io_report;
		$pdf->SetFont('helvetica', '', 10);
		$tit_rep = '<p  style="text-align:center;"><b> '.$datos['desuniadm'].'</b></p>';
		$pdf->writeHTML(utf8_encode($tit_rep), true, false, false, false, '');
	
	}
	
	function cambios_parametros($param=array(),$datos=array()){
			
			global $pdf,$io_fun_nomina,$margen_tabla,$ancho,$io_report;
			
			$param['oficprinact'] = $datos['minorguniadm'].$datos['ofiuniadm'];
			if($param['oficprinact']!=$param['oficprinant']){
			    if($datos['nro']>1){$pdf->AddPage();}
				$param['oficprinant'] = $datos['minorguniadm'].$datos['ofiuniadm']; 
				$ofic = $io_report->consulta_oficinas($datos);
				$param['datos_oficina'] = $ofic->fields;			 			
				$param['cambiooficprin']=1;
			}			
			
			$param['codoficact'] = $datos['minorguniadm'].$datos['ofiuniadm'].$datos['uniuniadm'].$datos['depuniadm'].$datos['prouniadm'];					
			if($param['codoficact']!=$param['codoficant']){
				$param['codoficant'] = $param['minorguniadm'].$datos['ofiuniadm'].$datos['uniuniadm'].$datos['depuniadm'].$datos['prouniadm'];
				$param['cambioofic']=1;
			}
			
			$param['codperact'] = $datos['codper'];
			if($param['codperact']!=$param['codperant']){
				$param['codperant'] = $datos['codper'];
				$param['cambioper']=1;	
			}			
			return $param;
	
	}
		
	$resp = $io_report->consulta_cumpleanos($param);
		
	$li_nro=0;
	$cambio = array();
	
	foreach($resp as $datosfam){
				
			$li_nro++;							
			$datos=array();
			$datos=$datosfam;
			
			$datos['nro']=$li_nro;
			
			$cambio = cambios_parametros($cambio,$datos);
				
			$pdf->startTransaction();
			$pagina = $pdf->getPage();
			
			if($cambio['cambiooficprin']){$pdf->Ln();titulo_oficina($cambio['datos_oficina']);$pdf->Bookmark($cambio['datos_oficina']['desuniadm'], 0, 0);}
			if($cambio['cambioofic']){$pdf->Ln();encabezado_oficina($datos);encabezado();}													
			fila($datos);
						
			$pagina2 = $pdf->getPage();			    
			
			if($pagina!=$pagina2){
					$pdf = $pdf->rollbackTransaction();									
					$pdf->AddPage();
					$pdf->Ln();titulo_oficina($cambio['datos_oficina']);$pdf->Ln();
					if($cambiooficprin){$pdf->Bookmark($cambio['datos_oficina']['desuniadm'], 0, 0);}
					$pdf->Ln();encabezado_oficina($datos);
					encabezado();
					fila($datos);													
			}	
			else{ $pdf->commitTransaction(); } 
					
			$cambio['cambioofic']=0;	
			$cambio['cambioper']=0;	
			$cambio['cambiooficprin']=0;				
	}
	
	$pdf->Ln();
	$pdf->Ln();
	
	$pdf->Output('listado_cupleano.pdf', 'I');


?>