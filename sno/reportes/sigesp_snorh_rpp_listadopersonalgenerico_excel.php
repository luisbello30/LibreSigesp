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
		//	    Arguments: as_titulo // T�tulo del Reporte
		//    Description: funci�n que guarda la seguridad de quien gener� el reporte
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 22/08/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_nomina;
		$ls_descripcion="Gener� el Reporte ".$as_titulo;
		$lb_valido=$io_fun_nomina->uf_load_seguridad_reporte("SNR","sigesp_snorh_r_listadopersonalgenerico.php",$ls_descripcion);
		return $lb_valido;
	}
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_titulos($lo_titulo,&$lo_hoja)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_titulos
		//		   Access: private 
		//	    Arguments: lo_hoja // hoja en excel
		//    Description: funci�n que los t�tulos del reporte
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 22/06/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$li_total=$_SESSION["li_total"];
         //   echo $_SESSION['li_aux_titulo'];die();
               /*echo '<pre>';
               print_r($_SESSION["la_titulos"]);
                echo '</pre>';
                die();*/
                
		for($li_i=0;($li_i<=$li_total);$li_i++)
		{


			$lo_hoja->set_column($li_i,$li_i,$_SESSION["la_titulos"][$li_i]["ancho"]);
		}
              
		for($li_i=0;($li_i<=$_SESSION['li_aux_titulo']);$li_i++)
		{

                   //echo $_SESSION["la_titulos"][$li_i]["titulo"].'<hr>';
			$lo_hoja->write(3, $li_i,$_SESSION["la_titulos"][$li_i]["titulo"],$lo_titulo);
		}//die();
	}// end function uf_print_titulos
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($lo_titulo,&$lo_hoja)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: lo_hoja // hoja en excel
		//    Description: funci�n que los t�tulos del reporte
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 22/06/2006 
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

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_validar_campo($id,&$as_campo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_validar_campo
		//		   Access: private 
		//	    Arguments: id // Identificador del campo que se desea validar
		//				   as_campo // Contenido del campo que se desea validar
		//    Description: funci�n que valida los campos 
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 22/08/2007 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_nomina, $io_funciones,$io_report;
		
             $ls_tipo=$_SESSION["la_titulos"][$id]["tipo"];
		switch($ls_tipo)
		{
			case "string":
				$as_campo=rtrim($as_campo);
				break;
			case "date":
				$as_campo=$io_funciones->uf_convertirfecmostrar($as_campo);
				break;		
			case "integer":
				$as_campo=number_format($as_campo,0,",",".");
				break;
			case "double":
				$as_campo=number_format($as_campo,2,",",".");
				break;
		}
		$ls_campo=$_SESSION["la_titulos"][$id]["campo"];
          
		switch($ls_campo)
		{
			case "sno_personal.nacper":
				switch($as_campo)
				{
					case "V":
						$as_campo="Venezolano";
						break;
					case "E":
						$as_campo="Estranjero";
						break;
				}
				break;
			case "sno_personal.sexper":
				switch($as_campo)
				{
					case "M":
						$as_campo="Masculino";
						break;
					case "F":
						$as_campo="Femenino";
						break;
				}
				break;
			case "sno_personal.edocivper":
				switch($as_campo)
				{
					case "S":
						$as_campo="Soltero";
						break;
					case "C":
						$as_campo="Casado";
						break;
					case "D":
						$as_campo="Divorciado";
						break;
					case "V":
						$as_campo="Viudo";
						break;
					case "K":
						$as_campo="Concubino";
						break;
				}
				break;
			case "sno_personal.nivacaper":
				switch($as_campo)
				{
					case "0":
						$as_campo="Ninguno";
						break;
					case "1":
						$as_campo="Primaria";
						break;
					case "2":
						$as_campo="Bachiller";
						break;
					case "3":
						$as_campo="T�cnico Superior";
						break;
					case "4":
						$as_campo="Universitario";
						break;
					case "5":
						$as_campo="Maestria";
						break;
					case "6":
						$as_campo="PostGrado";
						break;
					case "7":
						$as_campo="Doctorado";
						break;
				}
				break;
			case "sno_personal.estper":
				switch($as_campo)
				{
					case "0":
						$as_campo="Pre-Ingreso";
						break;
					case "1":
						$as_campo="Activo";
						break;
					case "2":
						$as_campo="N/A";
						break;
					case "3":
						$as_campo="Egresado";
						break;
				}
				break;
			case "sno_personalnomina.staper":
				switch($as_campo)
				{
					case "0":
						$as_campo="N/A";
						break;
					case "1":
						$as_campo="Activo";
						break;
					case "2":
						$as_campo="Vacaciones";
						break;
					case "3":
						$as_campo="Egresado";
						break;
					case "4":
						$as_campo="Suspendido";
						break;
				}
				break;
			case "sno_personal.cauegrper":
				switch($as_campo)
				{
					case "N": // Ninguna
						$as_campo="Ninguna";
						break;
					case "D": // Despido
						$as_campo="Despido";
						break;
					case "R": // Renuncia
						$as_campo="Renuncia";
						break;
					case "J": // Jubilado
						$as_campo="Jubilado";
						break;
					case "P": // Pensionado
						$as_campo="Pensionado";
						break;
					case "T": // Traslado
						$as_campo="Traslado";
						break;
					case "F": // Fallecido
						$as_campo="Fallecido";
						break;
				}
				break;
				case "sno_personalnomina.tipcuebanper":
				switch($as_campo)
				{
					case "A": // Ahorro
						$as_campo="Ahorro";
						break;
					case "C": // corriente
						$as_campo="Corriente";
						break;
					case "F": // fondo de activos liquidos
						$as_campo="Fondo de Activos L�quidos";
						break;
				}
				break;
				case "sno_personal.codorg":
					$as_campo=$io_report->uf_buscar_ubicacion_fisica($as_campo);
				break;
		}
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//---------------------------------------------------------------------------------------------------------------------------
	// para crear el libro excel
	require_once ("../../shared/writeexcel/class.writeexcel_workbookbig.inc.php");
	require_once ("../../shared/writeexcel/class.writeexcel_worksheet.inc.php");
	$lo_archivo = tempnam("/tmp", "listado_personal_generico.xls");
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

	//----------------------------------------------------  Parametros del encabezado  -----------------------------------------------
	$ls_titulo=$_SESSION["ls_titulo"];
	//--------------------------------------------------  Par�metros para Filtar el Reporte  -----------------------------------------
	//---------------------------------------------------------------------------------------------------------------------------
	//Busqueda de la data 
	$lb_valido=uf_insert_seguridad("<b>Listado de Personal en Excel</b>"); // Seguridad de Reporte
	if($lb_valido)
	{
		$lb_valido=$io_report->uf_listadogenerico(); // Obtenemos el detalle del reporte
	}
	if($lb_valido==false) // Existe alg�n error � no hay registros
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
		$lo_hoja->write(0,1,$ls_titulo,$lo_encabezado);
		uf_print_titulos($lo_titulo,&$lo_hoja);			
		$li_row=3;
                $c=0;
		while(!$io_report->rs_data->EOF)
		{
			$ls_campo0=$io_report->rs_data->fields["campo0"];
			$ls_campo1=$io_report->rs_data->fields["campo1"];
			$ls_campo2=$io_report->rs_data->fields["campo2"];
			$ls_campo3=$io_report->rs_data->fields["campo3"];
			$ls_campo4=$io_report->rs_data->fields["campo4"];
			$ls_campo5=$io_report->rs_data->fields["campo5"];
			$ls_campo6=$io_report->rs_data->fields["campo6"];
			$ls_campo7=$io_report->rs_data->fields["campo7"];
			$ls_campo8=$io_report->rs_data->fields["campo8"];
			$ls_campo9=$io_report->rs_data->fields["campo9"];
			$ls_campo10=$io_report->rs_data->fields["campo10"];
			$ls_campo11=$io_report->rs_data->fields["campo11"];
			$ls_campo12=$io_report->rs_data->fields["campo12"];
			$ls_campo13=$io_report->rs_data->fields["campo13"];
			$ls_campo14=$io_report->rs_data->fields["campo14"];
			$ls_campo15=$io_report->rs_data->fields["campo15"];
			$ls_campo16=$io_report->rs_data->fields["campo16"];
			$ls_campo17=$io_report->rs_data->fields["campo17"];
			$ls_campo18=$io_report->rs_data->fields["campo18"];
			$ls_campo19=$io_report->rs_data->fields["campo19"];
			$ls_campo20=$io_report->rs_data->fields["campo20"];
			$ls_campo21=$io_report->rs_data->fields["campo21"];
			$ls_campo22=$io_report->rs_data->fields["campo22"];
			$ls_campo23=$io_report->rs_data->fields["campo23"];
			$ls_campo24=$io_report->rs_data->fields["campo24"];
			$ls_campo25=$io_report->rs_data->fields["campo25"];
			$ls_campo26=$io_report->rs_data->fields["campo26"];
			$ls_campo27=$io_report->rs_data->fields["campo27"];
			$ls_campo28=$io_report->rs_data->fields["campo28"];
			$ls_campo29=$io_report->rs_data->fields["campo29"];
			$ls_campo30=$io_report->rs_data->fields["campo30"];
			$ls_campo31=$io_report->rs_data->fields["campo31"];
			$ls_campo32=$io_report->rs_data->fields["campo32"];
			$ls_campo33=$io_report->rs_data->fields["campo33"];
			$ls_campo34=$io_report->rs_data->fields["campo34"];
			$ls_campo35=$io_report->rs_data->fields["campo35"];
			$ls_campo36=$io_report->rs_data->fields["campo36"];
			$ls_campo37=$io_report->rs_data->fields["campo37"];
			$ls_campo38=$io_report->rs_data->fields["campo38"];
			$ls_campo39=$io_report->rs_data->fields["campo39"];
			$ls_campo40=$io_report->rs_data->fields["campo40"];
			$ls_campo41=$io_report->rs_data->fields["campo41"];

                        $c=0;
                       $v=0;
                       for($li_k1=0;($li_k1<$_SESSION["li_aux_titulo"]);$li_k1++)
			{
                           

                      

                          $ls_campo_sel =  $_SESSION["la_titulos"][$li_k1]["campo"];
                          if ($ls_campo_sel != "")
                          {
                           
                            $valor_campo=$io_report->rs_data->fields["campo".$v];
                            $campo_sueldo = $io_report->rs_data->fields["montomen"];
                            if ($ls_campo_sel=='sno_personal.nomper')
                            {
                                   $desglose_nombre=explode(' ',$valor_campo);
                                   uf_validar_campo($c,&$desglose_nombre[0]);
                                   $la_campos[$c]=rtrim($desglose_nombre[0]);
                                   $c++;
                                   uf_validar_campo($c,&$desglose_nombre[1]);
                                   $la_campos[$c]=rtrim($desglose_nombre[1]);
                                   $c++;


                            }else if ($ls_campo_sel=='sno_personal.apeper')
                            {
                                   $desglose_apellido=explode(' ',$valor_campo);
                                   uf_validar_campo($c,&$desglose_apellido[0]);
                                   $la_campos[$c]=$desglose_apellido[0];
                                   $c++;
                                   uf_validar_campo($c,&$desglose_apellido[1]);
                                   $la_campos[$c]=$desglose_apellido[1];
                                   $c++;
                            }else if ($ls_campo_sel=='sno_personal.telhabper')
                            {
                                   $desglose_tlfhab=explode('-',$valor_campo);
                                   uf_validar_campo($c,&$desglose_tlfhab[0]);
                                   $la_campos[$c]=$desglose_tlfhab[0];
                                   $c++;
                                    uf_validar_campo($c,&$desglose_tlfhab[1]);
                                   $la_campos[$c]=rtrim($desglose_tlfhab[1]);
                                   $c++;


                            }else if ($ls_campo_sel=='sno_personal.telmovper')
                            {
                                    
                                  $desglose_tlfcel=explode('-',$valor_campo);
                                  uf_validar_campo($c,&$desglose_tlfcel[0]);
                                  $la_campos[$c]=$desglose_tlfcel[0];
                                  
                                  $c++;
                                  uf_validar_campo($c,&$desglose_tlfcel[1]);
                                  $la_campos[$c]=$desglose_tlfcel[1];
                                  $c++;
                                
                            }else if ($ls_campo_sel=='sno_personalnomina.sueper')
                            {

                                  
                                  uf_validar_campo($c,&$valor_campo);
                                  $la_campos[$c]=$valor_campo;
                                
                                  $c++;
                                  uf_validar_campo($c,&$campo_sueldo);
                                  $la_campos[$c]=$campo_sueldo;
                                  $c++;

                            }else
                            {
                                  uf_validar_campo($c,&$valor_campo);
                                  $la_campos[$c]=$valor_campo;
                                  $c++;

                            }
                            $v++;
                         
                          }
                        }
                     
                        

			$li_row=$li_row+1;
			for($li_k=0;($li_k<=$_SESSION["li_aux_titulo"]);$li_k++)
			{
				switch($_SESSION["la_titulos"][$li_k]["alineacion"])
				{
					case "center":
						$lo_hoja->write($li_row, $li_k, $la_campos[$li_k]." ", $lo_datacenter);
						break;
					case "left":
						$lo_hoja->write($li_row, $li_k, $la_campos[$li_k]." ", $lo_dataleft);
						break;
					case "right":
						$lo_hoja->write($li_row, $li_k, $la_campos[$li_k], $lo_dataright);
						break;
				}
			}
			$io_report->rs_data->MoveNext();
		}

		$lo_libro->close();
		header("Content-Type: application/x-msexcel; name=\"listado_personal_generico.xls\"");
		header("Content-Disposition: inline; filename=\"listado_personal_generico.xls\"");
		$fh=fopen($lo_archivo, "rb");
		fpassthru($fh);
		unlink($lo_archivo);
		print("<script language=JavaScript>");
		//print(" close();");
		print("</script>");
		unset($io_pdf);
	}
	unset($io_report);
	unset($io_funciones);
	unset($io_fun_nomina);
?> 