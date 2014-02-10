<?php
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//    REPORTE: Reporte de Cuentas por Pagar Resumido
//  ORGANISMO: Ninguno en particular
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    session_start();
	ini_set('memory_limit','512M');
	ini_set('max_execution_time ','0');
	if(!array_key_exists("la_logusr",$_SESSION))
	{
		print "<script language=JavaScript>";
		print "close();";
		print "</script>";
	}
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_seguridad($as_titulo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_insert_seguridad
		//		   Access: private
		//	    Arguments: as_titulo // Título del reporte
		//    Description: función que guarda la seguridad de quien generó el reporte
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 11/03/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_cxp;

		$ls_descripcion="Generó el Reporte ".$as_titulo;
		$lb_valido=$io_fun_cxp->uf_load_seguridad_reporte("CXP","sigesp_cxp_r_solicitudes.php",$ls_descripcion);
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera($lo_libro,$lo_hoja,$ad_fecemides,$ad_fecemihas,$as_titulo,&$li_fila)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: private
		//	    Arguments: as_codigo         // Codigo del Proveedor/Beneficiario
		//	    		   as_nombre         // Nombre del Proveedor/Beneficiario
		//	    		   ad_saldo_anterior // Saldo hasta la fecha de inicio del Intervalo
		//	    		   io_pdf            // Instancia de objeto pdf
		//    Description: función que imprime la cabecera de cada página
		//	   Creado Por: Ing. Yesenia Moreno Ing. Luis Lang
		// Fecha Creación: 21/06/2007
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	//	$lo_hoja->write($li_fila, 4, $as_titulo,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
		$lo_hoja->write($li_fila, 3, "Del: ".$ad_fecemides." Al ".$ad_fecemihas,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
	}// end function uf_print_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle_proveedores($la_data,$li_totsalantp,$li_totmondebp,$li_totmonhabp,$li_totsalactp,$lo_libro,$lo_hoja,&$li_fila)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle_proveedores
		//		   Access: private
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime el detalle
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 21/06/2007
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lo_hoja->write(4, 3, 'PROVEEDORES',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'center','size'=>'10')));
		$lo_hoja->write(5, 0, 'Codigo',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'center','size'=>'9')));
		$lo_hoja->write(5, 1, 'Proveedor',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
		$lo_hoja->write(5, 2, 'Saldo Anterior',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
		$lo_hoja->write(5, 3, 'Debitos',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
		$lo_hoja->write(5, 4, 'Creditos',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
		$lo_hoja->write(5, 5, 'Saldo Actual',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
		$li_total=count($la_data);
		$li_fila=6;

		for($li_j=1;$li_j<=$li_total;$li_j++)
		{
			$lo_hoja->write($li_fila, 0, " ".$la_data[$li_j]['codigo'],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'9')));
			$lo_hoja->write($li_fila, 1, $la_data[$li_j]['nombre'],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'9')));
			$lo_hoja->write($li_fila, 2, $la_data[$li_j]['saldoant'],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'right','size'=>'9')));
			$lo_hoja->write($li_fila, 3, $la_data[$li_j]['mondeb'],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'right','size'=>'9')));
			$lo_hoja->write($li_fila, 4, $la_data[$li_j]['monhab'],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'right','size'=>'9')));
			$lo_hoja->write($li_fila, 5, $la_data[$li_j]['saldo'],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'right','size'=>'9')));
			$li_fila++;
		}
		$li_fila++;
		$lo_hoja->write($li_fila, 1, 'TOTALES',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'9')));
		$lo_hoja->write($li_fila, 2, $li_totsalantp,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'9')));
		$lo_hoja->write($li_fila, 3, $li_totmondebp,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'9')));
		$lo_hoja->write($li_fila, 4, $li_totmonhabp,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'9')));
		$lo_hoja->write($li_fila, 5, $li_totsalactp,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'9')));
	}// end uf_print_detalle_solicitudes_actuales
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle_beneficiarios($la_data,$li_totsalantb,$li_totmondebb,$li_totmonhabb,$li_totsalactb,$lo_libro,$lo_hoja,&$li_fila)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle_beneficiarios
		//		   Access: private
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime el detalle
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 21/06/2007
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$li_fila=$li_fila+2;
		$lo_hoja->write($li_fila, 3, 'BENEFICIARIOS',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'center','size'=>'10')));
		$li_fila++;
		$lo_hoja->write($li_fila, 0, 'Codigo',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'center','size'=>'9')));
		$lo_hoja->write($li_fila, 1, 'Beneficiario',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
		$lo_hoja->write($li_fila, 2, 'Saldo Anterior',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
		$lo_hoja->write($li_fila, 3, 'Debitos',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
		$lo_hoja->write($li_fila, 4, 'Creditos',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
		$lo_hoja->write($li_fila, 5, 'Saldo Actual',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
		$li_fila++;
		$li_total=count($la_data);
		for($li_j=1;$li_j<=$li_total;$li_j++)
		{
			$lo_hoja->write($li_fila, 0, " ".$la_data[$li_j]['codigo'],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'9')));
			$lo_hoja->write($li_fila, 1, $la_data[$li_j]['nombre'],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'9')));
			$lo_hoja->write($li_fila, 2, $la_data[$li_j]['saldoant'],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'right','size'=>'9')));
			$lo_hoja->write($li_fila, 3, $la_data[$li_j]['mondeb'],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'right','size'=>'9')));
			$lo_hoja->write($li_fila, 4, $la_data[$li_j]['monhab'],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'right','size'=>'9')));
			$lo_hoja->write($li_fila, 5, $la_data[$li_j]['saldo'],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'right','size'=>'9')));
			$li_fila++;
		}
		$li_fila++;
		$lo_hoja->write($li_fila, 1, 'TOTALES',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'9')));
		$lo_hoja->write($li_fila, 2, $li_totsalantb,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'9')));
		$lo_hoja->write($li_fila, 3, $li_totmondebb,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'9')));
		$lo_hoja->write($li_fila, 4, $li_totmonhabb,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'9')));
		$lo_hoja->write($li_fila, 5, $li_totsalactb,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'9')));
	}// end function uf_print_detalle_ndnc_actuales
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------  Llamada a clases de gneracion de excel  ------------------------------------------
	require_once ("../../shared/writeexcel/class.writeexcel_workbookbig.inc.php");
	require_once ("../../shared/writeexcel/class.writeexcel_worksheet.inc.php");
	$lo_archivo =  tempnam("/tmp", "cxpresumido.xls");
	$lo_libro = &new writeexcel_workbookbig($lo_archivo);
	$lo_hoja = &$lo_libro->addworksheet();
	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------
	require_once("../../shared/class_folder/class_datastore.php");
	require_once("sigesp_cxp_class_report.php");
	$io_report=new sigesp_cxp_class_report();
	require_once("../../shared/class_folder/class_funciones.php");
	$io_funciones=new class_funciones();
	require_once("../class_folder/class_funciones_cxp.php");
	$io_fun_cxp=new class_funciones_cxp();
	$io_dsctasxpagar= new class_datastore();
	$io_dsctasxpagar = new class_datastore();
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ls_titulo= "Reporte de Cuentas Por Pagar Resumido";
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$li_excluir=$io_fun_cxp->uf_obtenervalor_get("excluir",0);
	$ld_fecemides=$io_fun_cxp->uf_obtenervalor_get("fecemides","");
	$ld_fecemihas=$io_fun_cxp->uf_obtenervalor_get("fecemihas","");
	//--------------------------------------------------------------------------------------------------------------------------------
	$ls_estretiva=$_SESSION["la_empresa"]["estretiva"];
	$lb_valido= $io_report->uf_select_solicitudes("","","",$ld_fecemides,$ld_fecemihas);
	if (!$lb_valido) // Existe algún error ó no hay registros.
	{
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar ');");
		print(" close();");
		print("</script>");
	}
	else // Imprimimos el reporte
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
		$lo_hoja->set_column(0,0,15);
		$lo_hoja->set_column(1,1,20);
		$lo_hoja->set_column(2,2,20);
		$lo_hoja->set_column(3,3,20);
		$lo_hoja->set_column(4,4,20);
		$lo_hoja->set_column(5,5,20);
		$lo_hoja->set_column(6,6,20);

		$lo_hoja->write(0, 3, $ls_titulo,$lo_encabezado);

		$li_fila=2;

		$li_totrow= $io_report->rs_data->RecordCount();
		$li_p=0;
		$li_q=0;
		$li_totgendeb=0;
		$li_totgenhab=0;
		$li_totgensal=0;
		$li_totmondebp=0;
		$li_totmonhabp=0;
		$li_totmondebb=0;
		$li_totmonhabb=0;
		$li_totsalantp=0;
		$li_totsalactp=0;
		$li_totsalantb=0;
		$li_totsalactb=0;
		$la_databen="";
		$la_datapro="";
		uf_print_cabecera($lo_libro,$lo_hoja,$ld_fecemides,$ld_fecemihas,$ls_titulo,&$li_fila);
		while(!$io_report->rs_data->EOF)
		{
			$li_salsol=0;
			$li_totaldebe=0;
			$li_totalhaber=0;
			$li_totalsaldo=0;
			$ls_tipproben=$io_report->rs_data->fields['tipproben'];
			$ls_cedbene= $io_report->rs_data->fields['ced_bene'];
			$ls_codpro= $io_report->rs_data->fields['cod_pro'];
			$ls_nombre= $io_report->rs_data->fields['nombre'];
			if($ls_tipproben=="B")
			{
				$ls_codigo=$ls_cedbene;
			}
			else
			{
				$ls_codigo=$ls_codpro;
			}
			if($lb_valido)
			{
				$li_monsolpre=0;
				//////////////////////////////////        SALDO PREVIO        //////////////////////////////////
				$lb_valido= $io_report->uf_select_solicitudes_previas($ls_tipproben,$ls_codpro,$ls_cedbene,$ld_fecemides,$ld_fecemihas);
				if($lb_valido)
				{
					$li_solcont=0;
					$li_solanul=0;
					while(!$io_report->rs_solprevias->EOF)
					{
						$ls_estatus= $io_report->rs_solprevias->fields['estatus'];
						$li_monsol= $io_report->rs_solprevias->fields['monsol'];
						$ls_numsolaux= $io_report->rs_solprevias->fields['numsol'];
						if($ls_estretiva=="B")
						{
							$li_monretiva=$io_report->uf_select_det_deducciones_solpag($ls_numsolaux);
							$li_monsol=$li_monsol+$li_monretiva;
						}
						switch ($ls_estatus)
						{
							case "C":
								$li_solcont=($li_solcont+$li_monsol);
							break;
							case "A":
								$li_solanul=($li_solanul+$li_monsol);
							break;
						}
						$io_report->rs_solprevias->MoveNext();
					}
					$li_monsolpre=($li_solcont-$li_solanul);
				}
				$io_report->io_sql->free_result($io_report->rs_solprevias);
				$li_monpagpre=0;
				$lb_valido= $io_report->uf_select_pagosprevios($ls_tipproben,$ls_codpro,$ls_cedbene,$ld_fecemides,$ld_fecemihas,
															   $li_monpagpre,$li_monretpre);
																			   
				if($ls_estretiva=="B")
				{
					$li_monpagpre=$li_monpagpre+$li_monretpre;
				}
				$li_monsalant=($li_monsolpre-$li_monpagpre);
				if($li_monsalant>0)
				{
					$ls_saldoanterior= number_format($li_monsalant,2,',','.');
					$ls_saldoanterior="(".$ls_saldoanterior.")";
				}
				else
				{
					$ls_saldoanterior= abs($li_monsalant);
					$ls_saldoanterior= number_format($ls_saldoanterior,2,',','.');
				}
				//////////////////////////////////        SALDO PREVIO        //////////////////////////////////
				
				//////////////////////////////////    SOLICITUDES ACTUALES    //////////////////////////////////
				$lb_valido= $io_report->uf_select_solicitudesactualescxp($ls_tipproben,$ls_cedbene,$ls_codpro,$ld_fecemides,$ld_fecemihas);
				if($lb_valido)
				{
					$li_salsol=$li_monsalant;
					while(!$io_report->rs_solicitudes->EOF)
					{
						$ls_numsol= $io_report->rs_solicitudes->fields['numsol'];
						$ls_estprodoc= $io_report->rs_solicitudes->fields['estprodoc'];
						$ls_consol= $io_report->rs_solicitudes->fields['consol'];
						$li_monsol= $io_report->rs_solicitudes->fields['monsol']; //Monto de la Solicitudes de Pago actuales.
						$ld_fecsol= $io_report->rs_solicitudes->fields['fecha'];
						if($ls_estretiva=="B")
						{
							$li_monretiva=$io_report->uf_select_det_deducciones_solpag($ls_numsol);
							$li_monsol=$li_monsol+$li_monretiva;
						}
						$li_salsol= $li_salsol+$li_monsol;
						$li_totalhaber=$li_totalhaber+$li_monsol;
						$ld_fecsol=$io_funciones->uf_convertirfecmostrar($ld_fecsol);
						$ls_monto= number_format($li_monsol,2,',','.');
						$ls_salsol= "(".number_format($li_salsol,2,',','.').")";
						$io_report->rs_solicitudes->MoveNext();
					}
				}
				$io_report->io_sql->free_result($io_report->rs_solicitudes);
				//////////////////////////////////    SOLICITUDES ACTUALES    //////////////////////////////////
				//////////////////////////////////    NOTAS DEBITO/CREDITO    //////////////////////////////////
				$lb_valido=$io_report->uf_select_informacionndnc($ls_tipproben,$ls_codigo,$ld_fecemides,$ld_fecemihas,"");
				if($lb_valido)
				{
					while(!$io_report->rs_ndnc->EOF)
					{
						$ls_numdc= $io_report->rs_ndnc->fields['numdc'];
						$ls_codope= $io_report->rs_ndnc->fields['codope'];
						$ls_desope= $io_report->rs_ndnc->fields['desope'];
						$li_monto=  $io_report->rs_ndnc->fields['monto']; //Monto de la Solicitudes de Pago actuales.
						$ld_fecope= $io_report->rs_ndnc->fields['fecope'];
						if($ls_codope=="ND")
						{
							$li_salsol= $li_salsol+$li_monto;
							$li_debe=0;
							$li_haber=$li_monto;
							$ls_procedencia="Debito";
							$li_totalhaber=$li_totalhaber+$li_monto;
						}
						else
						{
							$li_salsol= $li_salsol-$li_monto;
							$li_debe=$li_monto;
							$li_haber=0;
							$ls_procedencia="Credito";
							$li_totaldebe=$li_totaldebe+$li_monto;
						}
						$ld_fecope=$io_funciones->uf_convertirfecmostrar($ld_fecope);
						$li_debe= number_format($li_debe,2,',','.');
						$li_haber= number_format($li_haber,2,',','.');
						$li_salsol=round($li_salsol,2);
						if(doubleval($li_salsol)>0)
						{
							$ls_salsol= "(".number_format($li_salsol,2,',','.').")";
						}
						else
						{
							$ls_salsol= abs($li_salsol);
							$ls_salsol= number_format($ls_salsol,2,',','.');
						}
						$io_report->rs_ndnc->MoveNext();
					}
				}
				$io_report->io_sql->free_result($io_report->rs_ndnc);
				//////////////////////////////////    NOTAS DEBITO/CREDITO    //////////////////////////////////

				//////////////////////////////////       PAGOS ACTUALES       //////////////////////////////////
				$lb_valido=$io_report->uf_select_informacionpagoscxp($ls_tipproben,$ls_cedbene,$ls_codpro,$ld_fecemides,$ld_fecemihas,"");
				if($lb_valido)
				{
					while(!$io_report->rs_pagactuales->EOF)
					{
						$ls_salsol="";
						$ls_numsol= $io_report->rs_pagactuales->fields['numsol'];
						$ls_codope= $io_report->rs_pagactuales->fields['codope'];
						$ls_conmov= $io_report->rs_pagactuales->fields['conmov'];
						$li_monto= $io_report->rs_pagactuales->fields['monto']; //Monto de la Solicitudes de Pago actuales.
						$ld_fecmov= $io_report->rs_pagactuales->fields['fecmov'];
						$ls_estmov= $io_report->rs_pagactuales->fields['estmov'];
						if($ls_estretiva=="B")
						{
							$li_monretiva=$io_report->uf_select_det_deducciones_solpag($ls_numsol);
							$li_monto=$li_monto+$li_monretiva;
						}
						if ($ls_estmov=='O' || $ls_estmov=='C')
						{
							$li_salsol= $li_salsol-$li_monto;
							$li_totaldebe=$li_totaldebe+$li_monto;
							$ld_debe=number_format($li_monto,2,',','.');
							$ld_haber="0,00";
							$ls_anulado ="";
						}
						else 
						{
							$li_salsol= $li_salsol+$li_monto;
							$li_totalhaber=$li_totalhaber+$li_monto;
							$ld_debe="0,00";
							$ld_haber=number_format($li_monto,2,',','.');
							$ls_anulado =" Anulado";
						}
						$ld_fecmov=$io_funciones->uf_convertirfecmostrar($ld_fecmov);
						$li_salsol=round($li_salsol,2);
						if(doubleval($li_salsol)>0)
						{
							$ls_salsol= "(".number_format($li_salsol,2,',','.').")";
						}
						else
						{
							$ls_salsol= abs($li_salsol);
							$ls_salsol= number_format($ls_salsol,2,',','.');
						}
						switch ($ls_codope)
						{
							case "CH":
								$ls_procedencia="Cheque".$ls_anulado;
							break;
							case "ND":
								$ls_procedencia="Nota de Debito";
							break;
						}
						$ls_monto= number_format($li_monto,2,',','.');
						$io_report->rs_pagactuales->MoveNext();
					}
				}
				$io_report->io_sql->free_result($io_report->rs_pagactuales);
				//////////////////////////////////       PAGOS ACTUALES       //////////////////////////////////
			}
			else
			{
				break;
			}
			$li_totalsaldo=$li_salsol;
			/*$li_totgendeb=$li_totgendeb+$li_totaldebe;
			$li_totgenhab=$li_totgenhab+$li_totalhaber;*/
			$li_totgensal=$li_totgensal+$li_totalsaldo;
			if(doubleval($li_totalsaldo)>0)
			{
				$li_totalsaldo= "(".number_format($li_totalsaldo,2,',','.').")";
			}
			else
			{
				$li_totalsaldo= abs($li_totalsaldo);
				$li_totalsaldo= number_format($li_totalsaldo,2,',','.');
			}
/*			$li_totalhaber= number_format($li_totalhaber,2,',','.');
			$li_totaldebe= number_format($li_totaldebe,2,',','.');
*/			if(($li_excluir==0)||($li_salsol>0))
			{
					if($ls_tipproben=="P")
					{
						$li_p++;
						$li_totsalantp= $li_totsalantp + $li_monsalant;
						$li_totsalactp= $li_totsalactp + $li_salsol;
						$li_totmondebp= $li_totmondebp + $li_totaldebe;
						$li_totmonhabp= $li_totmonhabp + $li_totalhaber;
						$li_montodebe= number_format($li_totaldebe,2,',','.');
						$li_montohaber= number_format($li_totalhaber,2,',','.');
						$la_datapro[$li_p]= array('codigo'=>$ls_codigo,'nombre'=>$ls_nombre,'saldoant'=>$ls_saldoanterior,'mondeb'=>$li_montodebe,'monhab'=>$li_montohaber,'saldo'=>$li_totalsaldo);
					}
					else
					{
						$li_q++;
						$li_totsalantb= $li_totsalantb + $li_monsalant;
						$li_totsalactb= $li_totsalactb + $li_salsol;
						$li_totmondebb= $li_totmondebb + $li_totaldebe;
						$li_totmonhabb= $li_totmonhabb + $li_totalhaber;
						$li_montodebe= number_format($li_totaldebe,2,',','.');
						$li_montohaber= number_format($li_totalhaber,2,',','.');
						$la_databen[$li_q]= array('codigo'=>$ls_codigo,'nombre'=>$ls_nombre,'saldoant'=>$ls_saldoanterior,'mondeb'=>$li_montodebe,'monhab'=>$li_montohaber,'saldo'=>$li_totalsaldo);
					}

			}
			if(!$lb_valido)
			{break;}
			$io_report->rs_data->MoveNext();
		}// fin for uf_select_solicitudes
		$io_report->io_sql->free_result($io_report->rs_data);
		$li_totsalantp= number_format($li_totsalantp,2,',','.');
		$li_totmondebp= number_format($li_totmondebp,2,',','.');
		$li_totmonhabp= number_format($li_totmonhabp,2,',','.');
		$li_totsalactp= number_format($li_totsalactp,2,',','.');
		$li_totsalantb= number_format($li_totsalantb,2,',','.');
		$li_totmondebb= number_format($li_totmondebb,2,',','.');
		$li_totmonhabb= number_format($li_totmonhabb,2,',','.');
		$li_totsalactb= number_format($li_totsalactb,2,',','.');
		$li_fila=5;
		if($la_datapro!="")
		{
			uf_print_detalle_proveedores($la_datapro,$li_totsalantp,$li_totmondebp,$li_totmonhabp,$li_totsalactp,$lo_libro,$lo_hoja,$li_fila); // Imprimimos el detalle  
		}
		if($la_databen!="")
		{
			uf_print_detalle_beneficiarios($la_databen,$li_totsalantb,$li_totmondebb,$li_totmonhabb,$li_totsalactb,$lo_libro,$lo_hoja,$li_fila); // Imprimimos el detalle  
		}
		if($lb_valido)
		{
			unset($io_report);
			$lo_libro->close();
			header("Content-Type: application/x-msexcel; name=\"cuentas_x_pagar.xls\"");
			header("Content-Disposition: inline; filename=\"cuentas_x_pagar.xls\"");
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
			print(" alert('Ocurrio un error al generarse el Reporte');");
			print(" close();");
			print("</script>");
		}
	}
?>