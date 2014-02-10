<?php
    session_start();   
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
		// Fecha Creación: 22/08/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_nomina;
		$ls_descripcion="Generó el Reporte ".$as_titulo;
		$lb_valido=$io_fun_nomina->uf_load_seguridad_reporte("SNR","sigesp_snorh_r_listadopersonalipsfa.php",$ls_descripcion);
		return $lb_valido;
	}
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($lo_titulo,&$lo_hoja)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: lo_hoja // hoja en excel
		//    Description: función que los títulos del reporte
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 22/06/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$li_total=$_SESSION["li_total"];
		for($li_i=0;($li_i<=$li_total);$li_i++)
		{
			$lo_hoja->set_column($li_i,$li_i,$_SESSION["la_titulos"][$li_i]["ancho"]);
		}
		for($li_i=0;($li_i<=$li_total);$li_i++)
		{
			$lo_hoja->write(3, $li_i, $_SESSION["la_titulos"][$li_i]["titulo"],$lo_titulo);
		}
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	//---------------------------------------------------------------------------------------------------------------------------
	// para crear el libro excel
	require_once ("../../shared/writeexcel/class.writeexcel_workbookbig.inc.php");
	require_once ("../../shared/writeexcel/class.writeexcel_worksheet.inc.php");
	$lo_archivo = tempnam("/tmp", "listado_personal_generico_ipsfa.xls");
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
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_codnomdes=$io_fun_nomina->uf_obtenervalor_get("codnomdes","");
	$ls_codnomhas=$io_fun_nomina->uf_obtenervalor_get("codnomhas","");
	$ls_codperdes=$io_fun_nomina->uf_obtenervalor_get("codperdes","");
	$ls_codperhas=$io_fun_nomina->uf_obtenervalor_get("codperhas","");
	$ls_anio=$io_fun_nomina->uf_obtenervalor_get("ano","");
	$ls_mes=$io_fun_nomina->uf_obtenervalor_get("mes","");
	$ls_titmes=strtoupper($io_report->io_fecha->uf_load_nombre_mes($ls_mes));
	$ls_codperi=$io_fun_nomina->uf_obtenervalor_get("codperi","");
	$ls_orden=$io_fun_nomina->uf_obtenervalor_get("orden","1");
	$ls_sueldobasico=$io_fun_nomina->uf_obtenervalor_get("sueldobasico","");
	$ls_sueldobasicocargo=$io_fun_nomina->uf_obtenervalor_get("sueldobasicocargo","");
	$ls_difsueldobasico=$io_fun_nomina->uf_obtenervalor_get("difsueldobasico","");
	$ls_cestaticket=$io_fun_nomina->uf_obtenervalor_get("cestaticket","");
	$ls_primas=$io_fun_nomina->uf_obtenervalor_get("primas","");
	$ls_bonos=$io_fun_nomina->uf_obtenervalor_get("bonos","");
	$ls_complementos=$io_fun_nomina->uf_obtenervalor_get("complementos","");
	$ls_compensacion=$io_fun_nomina->uf_obtenervalor_get("compensacion","");
	$ls_deducciones=$io_fun_nomina->uf_obtenervalor_get("deducciones","");
	$ls_bonovacacional=$io_fun_nomina->uf_obtenervalor_get("bonovacacional","");
	$ls_bonofin=$io_fun_nomina->uf_obtenervalor_get("bonofin","");
	$ls_sueldobasico="'".$ls_sueldobasico."'";
	$ls_sueldobasico=str_replace("-","','",$ls_sueldobasico);
	$ls_sueldobasicocargo="'".$ls_sueldobasicocargo."'";
	$ls_sueldobasicocargo=str_replace("-","','",$ls_sueldobasicocargo);
	$ls_difsueldobasico="'".$ls_difsueldobasico."'";
	$ls_difsueldobasico=str_replace("-","','",$ls_difsueldobasico);
	$ls_cestaticket="'".$ls_cestaticket."'";
	$ls_cestaticket=str_replace("-","','",$ls_cestaticket);
	$ls_bonovacacional="'".$ls_bonovacacional."'";
	$ls_bonovacacional=str_replace("-","','",$ls_bonovacacional);
	$ls_bonofin="'".$ls_bonofin."'";
	$ls_bonofin=str_replace("-","','",$ls_bonofin);
	$ls_primas="'".$ls_primas."'";
	$ls_primas=str_replace("-","','",$ls_primas);
	$ls_bonos="'".$ls_bonos."'";
	$ls_bonos=str_replace("-","','",$ls_bonos);
	$ls_complementos="'".$ls_complementos."'";
	$ls_complementos=str_replace("-","','",$ls_complementos);
	$ls_compensacion="'".$ls_compensacion."'";
	$ls_compensacion=str_replace("-","','",$ls_compensacion).",".$ls_bonovacacional.",".$ls_bonofin;
	$ls_deducciones="'".$ls_deducciones."'";
	$ls_deducciones=str_replace("-","','",$ls_deducciones);
	$ls_conceptos=$ls_primas.",".$ls_bonos.",".$ls_complementos.",".$ls_compensacion.",".$ls_deducciones;

	//---------------------------------------------------------------------------------------------------------------------------
	//Busqueda de la data 
	$lb_valido=uf_insert_seguridad("<b>Listado de Personal para Ministerio de la Defensa</b>"); // Seguridad de Reporte
	if($lb_valido)
	{
		$lb_valido=$io_report->uf_listadogenerico_ipsfa($ls_codnomdes,$ls_codnomhas,$ls_anio,$ls_mes,$ls_codperi,$ls_codperdes,$ls_codperhas,$ls_orden,$rs_data); // Obtenemos el detalle del reporte
	}
	if(($lb_valido==false)||($rs_data->RecordCount()==0)) // Existe algún error ó no hay registros
	{
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');"); 
		print(" close();");
		print("</script>");
	}
	else // Imprimimos el reporte
	{
		$lo_encabezado =&$lo_libro->addformat();
		$lo_encabezado->set_size('10');		
		$lo_encabezado->set_bold();
		$lo_encabezado->set_font("Verdana");
		$lo_encabezado->set_align('left');
		$lo_titulo= &$lo_libro->addformat();
		$lo_titulo->set_text_wrap();
		$lo_titulo->set_bold();
		$lo_titulo->set_font("Verdana");
		$lo_titulo->set_align('center');
		$lo_titulo->set_size('8');		
		$lo_datacenter= &$lo_libro->addformat();
		$lo_datacenter->set_font("Verdana");
		$lo_datacenter->set_align('center');
		$lo_datacenter->set_size('8');
		$lo_dataleft= &$lo_libro->addformat();
		$lo_dataleft->set_text_wrap();
		$lo_dataleft->set_font("Verdana");
		$lo_dataleft->set_align('left');
		$lo_dataleft->set_size('8');
		$lo_dataright= &$lo_libro->addformat(array(num_format => '#,##0.00'));
		$lo_dataright->set_font("Verdana");
		$lo_dataright->set_align('right');
		$lo_dataright->set_size('8');
		$lo_dataright2= &$lo_libro->addformat();
		$lo_dataright2->set_font("Verdana");
		$lo_dataright2->set_align('right');
		$lo_dataright2->set_size('8');

		$lo_hoja->set_column(0,0,10);
		$lo_hoja->set_column(1,1,20);
		$lo_hoja->set_column(2,2,15);
		$lo_hoja->set_column(3,3,27);
		$lo_hoja->set_column(4,4,27);
		$lo_hoja->set_column(5,5,8);
		$lo_hoja->set_column(6,6,32);
		$lo_hoja->set_column(7,7,122);
		$lo_hoja->set_column(8,8,82);
		$lo_hoja->set_column(9,9,18);
		$lo_hoja->set_column(10,10,15);
		$lo_hoja->set_column(11,11,15);
		$lo_hoja->set_column(12,12,20);
		$lo_hoja->set_column(13,13,15);
		$lo_hoja->set_column(14,14,20);
		$lo_hoja->set_column(15,15,15);
		$lo_hoja->set_column(16,16,21);
		$lo_hoja->set_column(17,17,21);
		$lo_hoja->set_column(18,18,22);
		$lo_hoja->set_column(19,19,32);
		$lo_hoja->set_column(20,20,25);
		$lo_hoja->set_column(21,21,25);
		$lo_hoja->set_column(22,22,25);
		$lo_hoja->set_column(23,23,25);
		$lo_hoja->set_column(24,24,28);
		$lo_hoja->set_column(25,25,28);
		$lo_hoja->set_column(26,26,28);

		$ls_empresa = strtoupper($_SESSION["la_empresa"]["nombre"]);
		$lo_hoja->write(0, 0, "REPUBLICA BOLIVARIANA DE VENEZUELA",$lo_encabezado);
		$lo_hoja->write(1, 0, "MINISTERIO DEL PODER POPULAR PARA LA DEFENSA",$lo_encabezado);
		$lo_hoja->write(2, 0, "DIRECCION GENERAL DE CONTROL DE GESTION DE EMPRESAS Y SERVICIOS",$lo_encabezado);
		$lo_hoja->write(3, 0, $ls_empresa,$lo_encabezado);

		$lo_hoja->write(8, 0, "NOMINA DE SUELDOS Y SALARIOS DEL PERSONAL MILITAR, DE EMPLEADOS Y OBREROS FIJOS, CONTRATADOS Y DE COMISIÓN DE SERVICIOS.",$lo_encabezado);
		$lo_hoja->write(9, 0, "01/".$ls_mes."/".$ls_anio,$lo_encabezado);

		$lo_hoja->write(12, 0, "CEDULA",$lo_titulo);
		$lo_hoja->write(12, 1, "GRADO_JERAR_EMPL",$lo_titulo);
		$lo_hoja->write(12, 2, "COMPONENTE",$lo_titulo);
		$lo_hoja->write(12, 3, "NOMBRES",$lo_titulo);
		$lo_hoja->write(12, 4, "APELLIDOS",$lo_titulo);
		$lo_hoja->write(12, 5, "SEXO",$lo_titulo);
		$lo_hoja->write(12, 6, "ABREVIATURA",$lo_titulo);
		$lo_hoja->write(12, 7, "NOMBRE EMPRESA",$lo_titulo);
		$lo_hoja->write(12, 8, "CARGO",$lo_titulo);
		$lo_hoja->write(12, 9, "FECHA_INGRESO",$lo_titulo);
		$lo_hoja->write(12, 10, "AÑOS SERVICIOS",$lo_titulo);
		$lo_hoja->write(12, 11, "CANT_HIJOS",$lo_titulo);
		$lo_hoja->write(12, 12, "GRADO_INSTRUCCION",$lo_titulo);
		$lo_hoja->write(12, 13, "NOMINA",$lo_titulo);
		$lo_hoja->write(12, 14, "TIPO_NOMINA",$lo_titulo);
		$lo_hoja->write(12, 15, "FECHA_INICIO",$lo_titulo);
		$lo_hoja->write(12, 16, "FECHA_VENCIMIENTO",$lo_titulo);
		$lo_hoja->write(12, 17, "CONT_CONSECUTIVOS",$lo_titulo);
		$lo_hoja->write(12, 18, "CONT_INTERRUMPIDAS",$lo_titulo);
		$lo_hoja->write(12, 19, "CONDIC_CONTRACTUAL_LABORAL",$lo_titulo);
		$lo_hoja->write(12, 20, "SUELDO_SALARIO_BASICO",$lo_titulo);
		$lo_hoja->write(12, 21, "SUELDO_BASICO_CARGO",$lo_titulo);
		$lo_hoja->write(12, 22, "DIF_SUELDO_BASICO",$lo_titulo);
		$lo_hoja->write(12, 23, "DIAS_CESTA_TICKET",$lo_titulo);
		$lo_hoja->write(12, 24, "MONTO_DIAS_CESTA_TICKET",$lo_titulo);
		$lo_hoja->write(12, 25, "DIAS_ANUAL_CESTA_TICKET",$lo_titulo);
		$lo_hoja->write(12, 26, "MONTO_ANUAL_CESTA_TICKET",$lo_titulo);
		$li_col=26;
		$li_row=12;

		// Buscamos los conceptos de prinas
		$lb_valido=$io_report->uf_buscar_titulo_concepto($ls_codnomdes,$ls_codnomhas,$ls_anio,$ls_mes,$ls_primas); 
		if($lb_valido)
		{
			while(!$io_report->rs_data_conceptos->EOF)
			{
				$ls_codconc=$io_report->rs_data_conceptos->fields["codconc"];
				$ls_nomcon=substr($io_report->rs_data_conceptos->fields["nomcon"],0,20);
				$ls_tippernom=$io_report->rs_data_conceptos->fields["tippernom"];
				$li_col++;
				$lo_hoja->set_column($li_col,$li_col,15);
				$lo_hoja->write(11, $li_col, $ls_nomcon,$lo_encabezado);
				$lo_hoja->write(12, $li_col, "Monto",$lo_titulo);
				$li_col++;
				$lo_hoja->set_column($li_col,$li_col,15);
				$lo_hoja->write(12, $li_col, "Frecuencia",$lo_titulo);
				$io_report->DS_detalle->insertRow("codconc",$ls_codconc);
				$io_report->DS_detalle->insertRow("columna",$li_col);
				$io_report->DS_detalle->insertRow("tippernom",$ls_tippernom);
				$io_report->DS_detalle->insertRow("tipo","A");
				$io_report->rs_data_conceptos->MoveNext();
			}
		}
		// Buscamos los conceptos de bonos
		$lb_valido=$io_report->uf_buscar_titulo_concepto($ls_codnomdes,$ls_codnomhas,$ls_anio,$ls_mes,$ls_bonos); 
		if($lb_valido)
		{
			while(!$io_report->rs_data_conceptos->EOF)
			{
				$ls_codconc=$io_report->rs_data_conceptos->fields["codconc"];
				$ls_nomcon=substr($io_report->rs_data_conceptos->fields["nomcon"],0,15);
				$ls_tippernom=$io_report->rs_data_conceptos->fields["tippernom"];
				$li_col++;
				$lo_hoja->set_column($li_col,$li_col,15);
				$lo_hoja->write(11, $li_col, $ls_nomcon,$lo_encabezado);
				$lo_hoja->write(12, $li_col, "Monto",$lo_titulo);
				$li_col++;
				$lo_hoja->set_column($li_col,$li_col,15);
				$lo_hoja->write(12, $li_col, "Frecuencia",$lo_titulo);
				$io_report->DS_detalle->insertRow("codconc",$ls_codconc);
				$io_report->DS_detalle->insertRow("columna",$li_col);
				$io_report->DS_detalle->insertRow("tippernom",$ls_tippernom);
				$io_report->DS_detalle->insertRow("tipo","A");
				$io_report->rs_data_conceptos->MoveNext();
			}
		}
		// Buscamos los conceptos de complementos
		$lb_valido=$io_report->uf_buscar_titulo_concepto($ls_codnomdes,$ls_codnomhas,$ls_anio,$ls_mes,$ls_complementos); 
		if($lb_valido)
		{
			while(!$io_report->rs_data_conceptos->EOF)
			{
				$ls_codconc=$io_report->rs_data_conceptos->fields["codconc"];
				$ls_nomcon=substr($io_report->rs_data_conceptos->fields["nomcon"],0,20);
				$ls_tippernom=$io_report->rs_data_conceptos->fields["tippernom"];
				$li_col++;
				$lo_hoja->set_column($li_col,$li_col,15);
				$lo_hoja->write(11, $li_col, $ls_nomcon,$lo_encabezado);
				$lo_hoja->write(12, $li_col, "Monto",$lo_titulo);
				$li_col++;
				$lo_hoja->set_column($li_col,$li_col,15);
				$lo_hoja->write(12, $li_col, "Frecuencia",$lo_titulo);
				$io_report->DS_detalle->insertRow("codconc",$ls_codconc);
				$io_report->DS_detalle->insertRow("columna",$li_col);
				$io_report->DS_detalle->insertRow("tippernom",$ls_tippernom);
				$io_report->DS_detalle->insertRow("tipo","A");
				$io_report->rs_data_conceptos->MoveNext();
			}
		}
		// Buscamos los conceptos de compensacion
		$lb_valido=$io_report->uf_buscar_titulo_concepto($ls_codnomdes,$ls_codnomhas,$ls_anio,$ls_mes,$ls_compensacion); 
		if($lb_valido)
		{
			while(!$io_report->rs_data_conceptos->EOF)
			{
				$ls_codconc=$io_report->rs_data_conceptos->fields["codconc"];
				$ls_nomcon=substr($io_report->rs_data_conceptos->fields["nomcon"],0,20);
				$ls_tippernom=$io_report->rs_data_conceptos->fields["tippernom"];
				$li_col++;
				$lo_hoja->set_column($li_col,$li_col,15);
				$lo_hoja->write(11, $li_col, $ls_nomcon,$lo_encabezado);
				$lo_hoja->write(12, $li_col, "Monto",$lo_titulo);
				$li_col++;
				$lo_hoja->set_column($li_col,$li_col,15);
				$lo_hoja->write(12, $li_col, "Frecuencia",$lo_titulo);
				$io_report->DS_detalle->insertRow("codconc",$ls_codconc);
				$io_report->DS_detalle->insertRow("columna",$li_col);
				$io_report->DS_detalle->insertRow("tippernom",$ls_tippernom);
				$io_report->DS_detalle->insertRow("tipo","A");
				$io_report->rs_data_conceptos->MoveNext();
			}
		}		
		// Buscamos los conceptos de deducciones
		$lb_valido=$io_report->uf_buscar_titulo_concepto($ls_codnomdes,$ls_codnomhas,$ls_anio,$ls_mes,$ls_deducciones); 
		if($lb_valido)
		{
			while(!$io_report->rs_data_conceptos->EOF)
			{
				$ls_codconc=$io_report->rs_data_conceptos->fields["codconc"];
				$ls_nomcon=substr($io_report->rs_data_conceptos->fields["nomcon"],0,20);
				$ls_tippernom=$io_report->rs_data_conceptos->fields["tippernom"];
				$li_col++;
				$lo_hoja->set_column($li_col,$li_col,15);
				$lo_hoja->write(11, $li_col, $ls_nomcon,$lo_encabezado);
				$lo_hoja->write(12, $li_col, "Monto",$lo_titulo);
				$li_col++;
				$lo_hoja->set_column($li_col,$li_col,15);
				$lo_hoja->write(12, $li_col, "Frecuencia",$lo_titulo);
				$io_report->DS_detalle->insertRow("codconc",$ls_codconc);
				$io_report->DS_detalle->insertRow("columna",$li_col);
				$io_report->DS_detalle->insertRow("tippernom",$ls_tippernom);
				$io_report->DS_detalle->insertRow("tipo","D");
				$io_report->rs_data_conceptos->MoveNext();
			}
		}
		$li_col++;
		$li_colfin=$li_col;
		$lo_hoja->set_column($li_col,$li_col,20);
		$lo_hoja->write(12, $li_col, "TOTAL_ASIG",$lo_titulo);
		$li_col++;
		$lo_hoja->set_column($li_col,$li_col,20);
		$lo_hoja->write(12, $li_col, "TOTAL_DED",$lo_titulo);
		$li_col++;
		$lo_hoja->set_column($li_col,$li_col,20);
		$lo_hoja->write(12, $li_col, "SUELDO_EMPRESA",$lo_titulo);
		$li_col++;
		$lo_hoja->set_column($li_col,$li_col,20);
		$lo_hoja->write(12, $li_col, "OBSERVACIONES",$lo_titulo);
		$li_i=0;		
		$li_totrow=$rs_data->RecordCount;
		while ((!$rs_data->EOF)&&($lb_valido))
		{
			$li_i++;
			$li_asignacion=0;
			$li_deduccion=0;
			$ls_cedper=substr($rs_data->fields["cedper"],0,8);
			$ls_nomabrrango=substr($rs_data->fields["nomabrrango"],0,10);
			$ls_tipnom=$rs_data->fields["tipnom"];
			if ($ls_nomabrrango == '')
			{
				$ls_nomabrrango='EMPL';
				if(($ls_tipnom=='3')||($ls_tipnom=='4'))
				{
					$ls_nomabrrango='OBR';
				}
			}
			$ls_nomabrcomponente=substr($rs_data->fields["nomabrcomponente"],0,5);
			$ls_apeper=strtoupper(substr($rs_data->fields["apeper"],0,25));			
			$ls_nomper=strtoupper(substr($rs_data->fields["nomper"],0,25));			
			$ls_sexper=strtoupper($rs_data->fields["sexper"]);	
			$ls_titulo=strtoupper(substr($_SESSION["la_empresa"]["titulo"],0,25));	
			$ls_nombre=substr($_SESSION["la_empresa"]["nombre"],0,120);	
			$ls_codcar=$rs_data->fields["codcar"];
			$li_sueper=number_format($rs_data->fields["sueintper"],2,",",".");
			$ls_descar="ninguno";
			if($ls_codcar=='0000000000')
			{
				$ls_codasicar=$rs_data->fields["codasicar"];
				if($ls_codasicar!='0000000')
				{
					$ls_descar=substr($rs_data->fields["denasicar"],0,80);	
				}
			}
			else
			{
				$ls_descar=substr($rs_data->fields["descar"],0,80);	
			}
			$ld_fecingper=$io_funciones->uf_convertirfecmostrar($rs_data->fields["fecingper"]);
			$li_anoservpreper=$rs_data->fields["anoservpreper"];
			$li_antiguedad=(date('Y')-substr($ld_fecingper,6,4))+$li_anoservpreper;
			$ld_fechasper=$ls_anio."-".$ls_mes."-31";
			if(intval(substr($ld_fechasper,5,2))<intval(substr($ld_fecingper,3,2)))
			{
				$li_antiguedad=$li_antiguedad-1;
			}
			else
			{
				if(intval(substr($ld_fechasper,5,2))==intval(substr($ld_fecingper,3,2)))
				{
					if(intval(substr($ld_fechasper,8,2))<intval(substr($ld_fecingper,0,2)))
					{
						$li_antiguedad=$li_antiguedad-1;
					}
				}
			}
			$li_numhijper=$rs_data->fields["numhijper"];
			$li_nivacaper=$rs_data->fields["nivacaper"];
			switch($li_nivacaper)
			{
				case "0":
					$ls_nivacaper="Ninguno";
					break;
				case "1":
					$ls_nivacaper="Primaria";
					break;
				case "2":
					$ls_nivacaper="Secundaria";
					break;
				case "3":
					$ls_nivacaper="Técnico Superior";
					break;
				case "4":
					$ls_nivacaper="Universitario";
					break;
				case "5":
					$ls_nivacaper="Maestria";
					break;
				case "6":
					$ls_nivacaper="PostGrado";
					break;
				case "7":
					$ls_nivacaper="Doctorado";
					break;
			}
			$ls_codded=$rs_data->fields["codded"];
			$ls_codtipper=$rs_data->fields["codtipper"];
			$ls_nomina='';
			$ls_tipnomina='';
			$ld_fecingpernom='';
			$ld_fecculcontr='';
			$ls_contconsecutivos='';
			$ls_continterrumpidos='';
			if($ls_tipnom=='7')//JUBILADO
			{
				$ls_nomina='6';
				$ls_tipnomina='JUBILADO';
			}
			if($ls_tipnom=='10')//MILITAR
			{
				$ls_nomina='5';
				$ls_tipnomina='MILITAR';
			}
			if($ls_tipnom=='8')//COMISION DE SERVICIO
			{
				$ls_nomina='4';
				$ls_tipnomina='COMISION DE SERVICIO';
			}
			if(($ls_tipnom=='2')||($ls_tipnom=='4')||($ls_tipnom=='6')||($ls_tipnom=='14'))//CONTRATADOS
			{
				$ls_nomina='3';
				$ls_tipnomina='CONTRATADO';
				$ld_fecingpernom=$io_funciones->uf_convertirfecmostrar($rs_data->fields["fecingpernom"]);
				$ld_fecculcontr=$io_funciones->uf_convertirfecmostrar($rs_data->fields["fecculcontr"]);
				$ls_contconsecutivos='1';
				$ls_continterrumpidos='0';
			}
			if(($ls_tipnom=='1')||($ls_tipnom=='3')||($ls_tipnom=='5'))//FIJOS
			{
				$ls_nomina='2'; // TIEMPO PARCIAL
				$ls_tipnomina='FIJO TIEMPO PARCIAL';
				if($ls_codded=='100')
				{
					$ls_nomina='1'; // TIEMPO COMPLETO
					$ls_tipnomina='FIJO TIEMPO COMPLETO';
					if($ls_codtipper=='0101')
					{
						$ls_nomina='7'; // ALTO NIVEL
						$ls_tipnomina='ALTO NIVEL';
					}
				}
			}
			$ls_codper=$rs_data->fields["codper"];
			$ls_codnom=$rs_data->fields["codnom"];
			$li_sueldobasico=0;
			$li_sueldobasicocargo=0;
			$li_difsueldobasico=0;
			$li_cestaticket=0;
			$li_nrocestaticket=0;
			$li_cestaticketanual=0;
			$li_nrocestaticketanual=0;	
			$lb_valido=$io_report->uf_buscar_valor_concepto_personal($ls_codnom,$ls_codper,$ls_anio,$ls_mes,$ls_sueldobasico,&$li_sueldobasico);
			if($lb_valido)
			{
				$lb_valido=$io_report->uf_buscar_valor_concepto_personal($ls_codnom,$ls_codper,$ls_anio,$ls_mes,$ls_sueldobasicocargo,&$li_sueldobasicocargo);
			}
			if($lb_valido)
			{
				$lb_valido=$io_report->uf_buscar_valor_concepto_personal($ls_codnom,$ls_codper,$ls_anio,$ls_mes,$ls_difsueldobasico,&$li_difsueldobasico);
			}
				if($lb_valido)
			{
				$lb_valido=$io_report->uf_buscar_cestaticket_personal($ls_codper,$ls_anio,$ls_mes,$ls_cestaticket,&$li_cestaticket,&$li_nrocestaticket,&$li_cestaticketanual,&$li_nrocestaticketanual);
			}
			$li_asignacion=$li_asignacion+$li_sueldobasico;
			$li_asignacion=$li_asignacion+$li_sueldobasicocargo;
			$li_asignacion=$li_asignacion+$li_difsueldobasico;
			$li_row++;
			$lo_hoja->write($li_row, 0,$ls_cedper,$lo_datacenter);
			$lo_hoja->write($li_row, 1, $ls_nomabrrango,$lo_datacenter);
			$lo_hoja->write($li_row, 2, $ls_nomabrcomponente,$lo_datacenter);
			$lo_hoja->write($li_row, 3, $ls_nomper,$lo_dataleft);
			$lo_hoja->write($li_row, 4, $ls_apeper,$lo_dataleft);
			$lo_hoja->write($li_row, 5, $ls_sexper,$lo_datacenter);
			$lo_hoja->write($li_row, 6, $ls_titulo,$lo_datacenter);
			$lo_hoja->write($li_row, 7, $ls_nombre,$lo_dataleft);
			$lo_hoja->write($li_row, 8, $ls_descar,$lo_dataleft);
			$lo_hoja->write($li_row, 9, $ld_fecingper,$lo_datacenter);
			$lo_hoja->write($li_row, 10, $li_antiguedad,$lo_dataright2);
			$lo_hoja->write($li_row, 11, $li_numhijper,$lo_dataright2);
			$lo_hoja->write($li_row, 12, $ls_nivacaper,$lo_dataleft);			
			$lo_hoja->write($li_row, 13, $ls_nomina,$lo_datacenter);			
			$lo_hoja->write($li_row, 14, $ls_tipnomina,$lo_datacenter);
			$lo_hoja->write($li_row, 15, $ld_fecingpernom,$lo_datacenter);
			$lo_hoja->write($li_row, 16, $ld_fecculcontr,$lo_datacenter);
			$lo_hoja->write($li_row, 17, $ls_contconsecutivos,$lo_dataright2);
			$lo_hoja->write($li_row, 18, $ls_continterrumpidos,$lo_dataright2);
			$lo_hoja->write($li_row, 19, '',$lo_dataright);
			$lo_hoja->write($li_row, 20, $li_sueldobasico,$lo_dataright);
			$lo_hoja->write($li_row, 21, $li_sueldobasicocargo,$lo_dataright);
			$lo_hoja->write($li_row, 22, $li_difsueldobasico,$lo_dataright);
			$lo_hoja->write($li_row, 23, $li_nrocestaticket,$lo_dataright);
			$lo_hoja->write($li_row, 24, $li_cestaticket,$lo_dataright);
			$lo_hoja->write($li_row, 25, $li_nrocestaticketanual,$lo_dataright);
			$lo_hoja->write($li_row, 26, $li_cestaticketanual,$lo_dataright);
			$la_conceptos=split(",",$ls_conceptos);
			$li_totconcepto=count($la_conceptos);
			for($li_cont=0;($li_cont<$li_totconcepto)&&($lb_valido);$li_cont++)
			{
				$ls_concepto=$la_conceptos[$li_cont];
				if($ls_concepto<>"''")
				{
					$lb_valido=$io_report->uf_buscar_valor_concepto_personal($ls_codnom,$ls_codper,$ls_anio,$ls_mes,$ls_concepto,&$ld_valor);
					if($lb_valido)
					{
						$ls_concepto=str_replace("'","",$ls_concepto);
						$li_find=$io_report->DS_detalle->find("codconc",$ls_concepto);
						$li_col=$io_report->DS_detalle->getValue("columna",$li_find);
						$li_tippernom=$io_report->DS_detalle->getValue("tippernom",$li_find);
						$ls_tipo=$io_report->DS_detalle->getValue("tipo",$li_find);
						if ($ls_tipo=="A")
						{
							$li_asignacion=$li_asignacion+$ld_valor;
						}
						else
						{
							$li_deduccion=$li_deduccion+$ld_valor;
						}
						$ls_frecuencia="";
						switch($li_tippernom)
						{
							case "0":
								$ls_frecuencia="Semanal";
								break;
							case "1":
								$ls_frecuencia="Quincenal";
								break;
							case "2":
								$ls_frecuencia="Mensual";
								break;
							case "3":
								$ls_frecuencia="Anual";
								break;
						}
						$lo_hoja->write($li_row, $li_col-1, $ld_valor, $lo_dataright);
						$lo_hoja->write($li_row, $li_col, $ls_frecuencia, $lo_datacenter);
					}
				}
			}
			$lo_hoja->write($li_row, $li_colfin, $li_asignacion, $lo_dataright);
			$lo_hoja->write($li_row, $li_colfin+1, $li_deduccion, $lo_dataright);
			$lo_hoja->write($li_row, $li_colfin+2, $li_sueper, $lo_dataright);
			$rs_data->MoveNext();
		}
		$lo_libro->close();
		header("Content-Type: application/x-msexcel; name=\"listado_personal_generico_ipsfa.xls\"");
		header("Content-Disposition: inline; filename=\"listado_personal_generico_ipsfa.xls\"");
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