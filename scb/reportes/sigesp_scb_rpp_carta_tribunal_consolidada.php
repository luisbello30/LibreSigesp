<?php
session_start();
$ruta = '../../';
require_once($ruta.'shared/tcpdf/config/lang/ita.php');
require_once($ruta.'shared/tcpdf/tcpdf.php');  
header("Pragma: public");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: private",false);
ini_set('memory_limit','1024M');
ini_set('max_execution_time','0');	
require_once('../../shared/class_folder/class_pdf.php');
require_once("../../shared/class_folder/class_sql.php");
require_once("../../shared/class_folder/class_funciones.php");
require_once("../../shared/class_folder/sigesp_include.php");
require_once("../../shared/class_folder/class_datastore.php");
require_once("../../shared/class_folder/class_numero_a_letra.php");
$io_include   = new sigesp_include();
$ls_conect    = $io_include->uf_conectar();
$io_sql		  = new class_sql($ls_conect);	
require_once("sigesp_scb_report.php");
$class_report = new sigesp_scb_report($ls_conect);
require_once($ruta."sno/class_folder/clase_pensionados.php");
$io_pensionados=new pensionados();


$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'LETTER', true, 'UTF-8', false); 
$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->SetMargins(PDF_MARGIN_LEFT, 10, PDF_MARGIN_RIGHT);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
$pdf->setPrintHeader(false);
$pdf->setTextoFooter(utf8_encode('I.P.S.F.A. Av Los Proceres. Edif. Sede Caracas.  email: pensionestesoreria@hotmail.com'));
$pdf->SetFont('helvetica', '', 10);




if(!$_GET["codperi"]){
	
		print "<script language=JavaScript>";
		print "alert('Debe indicarse el período.');";
		print "close();";
		print "</script>";
		exit();

}

$param['codperi'] = $_GET["codperi"];
$rs_cheques = $class_report->buscar_cheques_periodo($param);

if($rs_cheques->RecordCount()<1){
	
		print "<script language=JavaScript>";
		print "alert('No se encontraron cheques generados para el período: ".$_GET["codperi"]."');";
		print "close();";
		print "</script>";
		exit();

}

foreach($rs_cheques as $cheq_pensionado){

	for($i=1;$i<=3;$i++){
	
					$pdf->AddPage();
					
					
					$ls_codban  = $cheq_pensionado["codban"];
					$ls_ctaban  = $cheq_pensionado["ctaban"];
					$ls_numdoc  = $cheq_pensionado["numdoc"];
					$ls_chevau  = $cheq_pensionado["chevau"];
					$ls_codope  = $cheq_pensionado["codope"];
					
					$datos = $class_report->buscar_cheque($ls_numdoc,$ls_chevau,$ls_codban,$ls_ctaban,$ls_codope);
					$datos = $class_report->rs_cheque->fields;
					
					
					
					$datosx['numdoc'] = $ls_numdoc;					
					$datosx['ctaban'] = $ls_ctaban;							
					$class_report->buscar_pensionado($datosx);
					$datospen = $class_report->rs_pensinado->fields;
					
					$nombene = $datospen["nombene"].' '.$datospen["apebene"];
					$cedbene=$datospen["ced_bene"];
					if($datospen["cedaut"]){$nombene = $datospen["nomaut"]; $cedbene=$datospen["tipautor"].$datospen["cedaut"];}
					$datosben = $nombene.'  <br /><b>Cédula/Código:</b> '.$cedbene;
					$nombre_tit = $datospen["tipniptit"].'-'.$datospen["cedtit"].'   '.$datospen["nomtit"].' '.$datospen["apetit"];
					$nombre_cau = $datospen["nomcau"].' '.$datospen["apecau"].'  cédula de identidad n° '.$datospen["tipnipcau"].'-'.$datospen["cedcau"];
					
					
					$concepto = $io_pensionados->buscar_concepto_sueldo($datospen);
					
					$margenes = $pdf->getMargins();
					
					
					
					$pdf->Image($ruta.'shared/imagebank/logo_ifamil.jpg',$margenes['left'],$margenes['top'], 183, 20); 
					//$numero_com = '<p  style="text-align:right;"><b>Nº Comunicación:</b> '.$datos['formato_nrocom'].'</p>';
					$pdf->writeHTML(utf8_encode('<p>&nbsp;</p>'), true, false, false, false, '');
					$pdf->Ln();
					$pdf->Ln();
					//$numero_com = '<p  style="text-align:right;"><b>Nº Correlativo:</b> '. $datos['formato_correl'].'</p>';
					//$pdf->writeHTML(utf8_encode($numero_com), true, false, false, false, '');
					$pdf->Ln();
					$pdf->Ln();
					$pdf->SetLineStyle(array('width' => 0.2, 'color' => $pdf->decodifica_color('#DDDDDD')));
					
					$ancho[1] = 75;
					$ancho[2] = 445;
					$ancho_total = array_sum($ancho);
					$datos['fecha'] = date('d/m/Y');
					$encabezado ='<table width="'.$ancho_total.'" border="1" cellspacing="0" cellpadding="2" bordercolor="#CCCCCC">
								  <tr>
									<td width="'.$ancho_total.'" height="20" colspan="2" align="center" bgcolor="#CCCCCC"><strong><em>ENVÍO DE CHEQUE</em></strong></td>
								  </tr>
								  <tr>
									<td width="'.$ancho[1].'" valign="top"><p align="right"><strong><em>Para:</em></strong></p></td>
									<td width="'.$ancho[2].'">'.$datosben.'</td>
								  </tr>
								  <tr>
									<td width="'.$ancho[1].'" valign="top"><p align="right"><strong><em>De:</em></strong></p></td>
									<td width="'.$ancho[2].'">Junta Administradora del I.P.S.F.A.</td>
								  </tr>
								  <tr>
									<td width="'.$ancho[1].'" valign="top"><p align="right"><strong><em>Fecha:</em></strong></p></td>
									<td width="'.$ancho[2].'">'.$datos['fecha'].'</td>
								  </tr>
								  <tr>
									<td width="'.$ancho[1].'" valign="top"><p align="right"><strong><em>Asunto:</em></strong></p></td>
									<td width="'.$ancho[2].'">Envío de Cheque</td>
								  </tr>			 
								</table>';
					
					$pdf->writeHTML(utf8_encode($encabezado), true, false, false, false, '');
					
					
					$pdf->getPageWidth();
					$pdf->SetLineStyle(array('width' => 0.8, 'color' => array(0,0,0)));
					$pdf->Line($margenes['left'],$pdf->GetY()-3,($pdf->getPageWidth()-$margenes['right'])-3,$pdf->GetY()-3);
					$pdf->Ln();
					$pdf->SetLineStyle(array('width' => 0.2, 'color' => $pdf->decodifica_color('#DDDDDD')));
					$texto = '<p  style="text-align:justify;"><em>Cumpliendo Instrucciones de Ciudadano Gral. Div. Presidente de la <b>Junta Administradora de I.P.S.F.A.</b> le enviamos el (los) siguiente(s) </em></p>';
					
					$pdf->writeHTML(utf8_encode($texto), true, 0, true, 0);
					
					$pdf->Ln();
					
					$chequetxt ='<table width="'.$ancho_total.'" border="1" cellspacing="0" cellpadding="2" bordercolor="#CCCCCC">
								   <tr>
									<td width="'.$ancho[1].'" valign="top"><p align="right"><strong><em>Cheque(s):</em></strong></p></td>
									<td width="'.$ancho[2].'">1</td>
								  </tr>
								  <tr>
									<td width="'.$ancho[1].'" valign="top"><p align="right"><strong><em>Número:</em></strong></p></td>
									<td width="'.$ancho[2].'">'.$ls_numdoc.'</td>
								  </tr>
								  <tr>
									<td width="'.$ancho[1].'" valign="top"><p align="right"><strong><em>Fecha:</em></strong></p></td>
									<td width="'.$ancho[2].'">'.$io_pensionados->io_conexiones->formatea_fecha_normal($datos['fecmov']).'</td>
								  </tr>
								  <tr>
									<td width="'.$ancho[1].'" valign="top"><p align="right"><strong><em>Banco:</em></strong></p></td>
									<td width="'.$ancho[2].'">'.$datos['nomban'].'</td>
								  </tr>	
								  <tr>
									<td width="'.$ancho[1].'" valign="top"><p align="right"><strong><em>Monto:</em></strong></p></td>
									<td width="'.$ancho[2].'">'.number_format($datos['monto'],2,",",".").' Bs.</td>
								  </tr>	
								  <tr>
									<td width="'.$ancho[1].'" valign="top"><p align="right"><strong><em>Titular:</em></strong></p></td>
									<td width="'.$ancho[2].'">'.$nombre_tit.'</td>
								  </tr>			 
								</table>';
					
					$pdf->writeHTML(utf8_encode($chequetxt), true, false, false, false, '');
					$pdf->Ln();
					
					//$mes_cheque = str_pad(substr($datos['fecmov'],5,2),3,'0',STR_PAD_LEFT);
					$mes_cheque = $datos['codperi'];
					
					switch($mes_cheque){
							
							case '001': $datos['mes']='enero'; break;
							case '002': $datos['mes']='febrero'; break;
							case '003': $datos['mes']='marzo'; break;
							case '004': $datos['mes']='abril'; break;
							case '005': $datos['mes']='mayo'; break;
							case '006': $datos['mes']='junio'; break;
							case '007': $datos['mes']='julio'; break;
							case '008': $datos['mes']='agosto'; break;
							case '009': $datos['mes']='septiembre'; break;
							case '010': $datos['mes']='octubre'; break;
							case '011': $datos['mes']='noviembre'; break;
							case '012': $datos['mes']='diciembre'; break;
					}
					
					//$texto = '<p  style="text-align:justify;"><em>A la orden de los Beneficiarios , remitidos por concepto de: <b>'.$concepto.'</b>, retenido a: <b>'.$nombre_cau.'</b>, correspondientes al mes de '.$datos['mes'].'. </em></p>';
					$texto = '<p  style="text-align:justify;"><em>A la orden de los Beneficiarios , remitidos por concepto de: <b>'.$concepto.'</b>, retenido a: <b>'.$nombre_cau.'</b>. </em></p>';
					$pdf->writeHTML(utf8_encode($texto), true, 0, true, 0);
					
					$pdf->Ln();
					$texto2 = '<p  style="text-align:justify;"><em><b>Nota:</b> Se agradece remitir a esta institución lo mas pronto posible, el número de cuenta del beneficiario y titular de la misma, indicando número de cédula de identidad del mismo, aperturada en la entidad bancaria BANCO BICENTENARIO para procecder a realizar el deposito en la misma, y así dar cumplimiento a la orden emitida en la circular N° 18 de la Dirección Ejecutiva de la Magistratura (DEM) de fecha 21 de noviembre de 2005, donde se ordena aperturar todas las cuentas existentes en el Tribunal en el Banco Bicentenario. Igualmente nombres, apellidos y cédula de identidad del militar causante, copia de la libreta, cédula o carnet.</em></p>';
					$pdf->writeHTML(utf8_encode($texto2), true, 0, true, 0);
					$pdf->Ln();
					
					$texto = '<p  style="text-align:justify;"><em>Los mismos deben ser remitidos a través del fax N° (0212)-609-22-87 o (0212)-609-29-07</em></p>';
					$pdf->writeHTML(utf8_encode($texto), true, 0, true, 0);
					$pdf->Ln();
					
					$texto = '<p  style="text-align:center;"><em>Remisión que hago a usted para su conocimiento y demas fines:</em></p>';
					$pdf->writeHTML(utf8_encode($texto), true, 0, true, 0);
					$pdf->Ln();
					$texto = '<p  style="text-align:center;"><em>Dios y Federación</em></p>';
					$pdf->writeHTML(utf8_encode($texto), true, 0, true, 0);
					$pdf->Ln();
					$pdf->Ln();
					$pdf->Ln();
					
					$texto = '<p  style="text-align:center;"><em>Luis E. Contreras Contreras</em></p>';
					$pdf->writeHTML(utf8_encode($texto), true, 0, true, 0);
					$texto = '<p  style="text-align:center;"><em>ST1</em></p>';
					$pdf->writeHTML(utf8_encode($texto), true, 0, true, 0);
					$texto = '<p  style="text-align:center;"><em>Tesorero</em></p>';
					$pdf->writeHTML(utf8_encode($texto), true, 0, true, 0);
					$pdf->Ln();
					$pdf->Ln();
					$texto = '<p  style="text-align:left;"><em>Se  agradece  a  los Tribunales y Juzgados  correspondientes,  devolver el original debidamente firmado por el beneficiario al Presidente del Instituto de Prevision Social de las Fuerzas Armadas (I.P.S.F.A.) </em></p>';
					$pdf->writeHTML(utf8_encode($texto), true, 0, true, 0);
					
	}//fin del for

}//fin del foreach

$pdf->Output('envio_cheque.pdf', 'I');


?>
