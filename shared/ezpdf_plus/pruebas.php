<?php 
session_start();   
header("Pragma: public");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: private",false);
ini_set('memory_limit','256M');
ini_set('max_execution_time','0');
$ruta="../../";
require_once("class.ezpdf.php");
require_once("class.covensol_pdf.php");
error_reporting(E_ALL);
set_time_limit(1800);

$io_pdf=new covensol_pdf('LETTER','portrait'); // Instancia de la clase PDF	
$io_pdf->selectFont('../../shared/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
$io_pdf->ezSetCmMargins(1,1,1.6,1); // Configuración de los margenes en centímetros ezSetCmMargins(top,bottom,left,right)

$texto = '<b><i>Los ministros de Defensa de los doce países que forman  América del</i></b> Sur se reunirán en. Chile este martes  para lanzar el <c:covensol_color:FFCC00>Consejo de Defensa Sudamericano</c:covensol_color> (CDS), un organismo que busca <c:covensol_color:A7FFA6>fomentar </c:covensol_color>la cooperación <c:covensol_color:770000>y el diálogo </c:covensol_color>entre las fuerzas <c:covensol_color:0000FF>armadas </c:covensol_color>de la región.<c:covensol_color:FF0000>   <b>El CDS formaparte de la Unión de Naciones Sudamericanas (Unasur) </c:covensol_color> , creada en mayo de 2008 para agrupar a  Argentina, Bolivia, Brasil,Chile, Colombia, Ecuador, Guyana, Paraguay, Perú, Surinam, Uruguay y Venezuela. </b>';

$color = $io_pdf->decodifica_color("#59BB66");
$io_pdf->ezText($color['R'].','.$color['G'].','.$color['B'],12,array('justification' => 'full'));

$io_pdf->ezText($texto,12,array('justification' => 'full'));
$io_pdf->ezText('',12,array('justification' => 'full'));
$io_pdf->ezText('',12,array('justification' => 'full'));

$texto2 = 'xxxxxxxxx'; 

$parametros = array();//INICIALIZA LOS PARAMETROS
$parametros['color_fondo'] = array(0.9,0.94,0.98);
$parametros['color_texto_columna1'] = array(0,0,0.4);

$io_pdf->dibuja_tabla('<b><c:covensol_color:FFFF00>Ente:</c:covensol_color></b>', $texto2,$parametros);
$io_pdf->dibuja_tabla('<b>Dependencia:</b>',$texto2,$parametros);
$io_pdf->dibuja_tabla('<b>Persona o Funcionario:</b>',$texto2,$parametros);
$io_pdf->dibuja_tabla('<b>Cargo:</b>',$texto2,$parametros);
$parametros = array();//INICIALIZA LOS PARAMETROS
$io_pdf->ezText('',12,array('justification' => 'full'));
$parametros['texto_titulo1'] = '<c:covensol_color:FFFF00>ACCIONES A TOMAR</c:covensol_color>';
$parametros['texto_titulo2'] = 'REMITIR  A';
$parametros['tamano_letra'] = 7;
$io_pdf->dibuja_tabla_2_columnas($parametros);
$io_pdf->ezText('',12,array('justification' => 'full'));
$io_pdf->dibuja_tabla_con_encabezado($parametros);
$parametros['relleno'] = 'si';
$desplazamineto = $io_pdf->dibuja_cuadrado($parametros);
$io_pdf->y -=  2*$desplazamineto;
$io_pdf->dibuja_cuadrado($parametros);
$io_pdf->y -=  2*$desplazamineto;	
$io_pdf->dibuja_cuadrado($parametros);
$io_pdf->y -=  2*$desplazamineto;	
$io_pdf->dibuja_cuadrado($parametros);
$io_pdf->y -=  2*$desplazamineto;	
$io_pdf->dibuja_cuadrado($parametros);
$io_pdf->y -=  2*$desplazamineto;

$opciones['numero_columnas'] = 2;
$opciones['texto_titulo'][1] = 'Nombre';
$opciones['ancho'][2] = 100;	
$io_pdf->tabla($opciones);
$io_pdf->ezText('',12,array('justification' => 'full'));
$opciones = array();
$opciones['numero_columnas'] = 3;	
$io_pdf->tabla($opciones);
$io_pdf->ezText('',12,array('justification' => 'full'));
$opciones = array();
$opciones['numero_columnas'] = 4;	
$io_pdf->tabla($opciones);
$io_pdf->ezText('',12,array('justification' => 'full'));
$opciones = array();
$opciones['numero_columnas'] = 5;
$opciones['texto_titulo'][3] = 'Nombre';
$opciones['ancho'][4] = 50;
$io_pdf->tabla($opciones);
$io_pdf->ezText('',12,array('justification' => 'full'));
$opciones = array();
$opciones['numero_columnas'] = 6;	
$io_pdf->tabla($opciones);
$io_pdf->ezText('',12,array('justification' => 'full'));
$opciones = array();
$opciones['numero_columnas'] = 7;	
$io_pdf->tabla($opciones);
$io_pdf->ezText('',12,array('justification' => 'full'));
$opciones = array();
$opciones['numero_columnas'] = 8;	
$io_pdf->tabla($opciones);
$io_pdf->ezText('',12,array('justification' => 'full'));
$opciones = array();
$opciones['numero_columnas'] = 9;	
$io_pdf->tabla($opciones);
$io_pdf->ezText('',12,array('justification' => 'full'));
$opciones = array();
$opciones['numero_columnas'] = 10;	
$io_pdf->tabla($opciones);

$io_pdf->ezStream(); 

































?>