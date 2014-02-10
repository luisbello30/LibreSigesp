<?php
	session_start(); 
	require_once("../../shared/class_folder/grid_param.php");
	$io_grid=new grid_param();
	require_once("class_funciones_scf.php");
	$io_funciones_scf=new class_funciones_scf("../../");
	require_once("sigesp_scf_c_comprobante.php");
	$io_comprobante=new sigesp_scf_c_comprobante("../../");
	require_once("../../shared/class_folder/class_datastore.php");
	$io_ds_scgcuentas=new class_datastore(); // Datastored de cuentas contables
	// proceso a ejecutar
	$ls_proceso=$io_funciones_scf->uf_obtenervalor("proceso","");
	// Número de comprobante
	$ls_comprobante=$io_funciones_scf->uf_obtenervalor("comprobante","");
	// Fecha del comprobante
	$ld_fecha=$io_funciones_scf->uf_obtenervalor("fecha","");
	// Procede del Comprobante
	$ls_procede=$io_funciones_scf->uf_obtenervalor("procede","");
	// Código de Banco
	$ls_codban=$io_funciones_scf->uf_obtenervalor("codban","---");
	// Cuenta de banco
	$ls_ctaban=$io_funciones_scf->uf_obtenervalor("ctaban","-------------------------");
	// Tipo Destino
	$ls_tipdes=$io_funciones_scf->uf_obtenervalor("tipdes","");
	// Cédula del beneficiario
	$ls_cedbene=$io_funciones_scf->uf_obtenervalor("cedbene","");
	// Código del proveedor
	$ls_codpro=$io_funciones_scf->uf_obtenervalor("codpro","");
	// total de filas de cuentas contables
	$li_totrowscg=$io_funciones_scf->uf_obtenervalor("totrowscg",1);
	// total de filas de cuentas de cargos
	switch($ls_proceso)
	{
		case "PROCESAR":
			uf_load_grid_contable($li_totrowscg);
			uf_print_cuentas_contable();
			break;

		case "LOADCOMPROBANTE":
			/*$lb_valido=$io_comprobante->uf_load_cuentas_contables($ls_procede,$ls_comprobante,$ld_fecha,$ls_codban,$ls_ctaban,
																  &$io_ds_scgcuentas);*/
			$lb_valido=$io_comprobante->uf_obtener_cuentas_contables($ls_procede,$ls_comprobante,$ld_fecha,$ls_codban,$ls_ctaban,&$rs_cuentas);
			if($lb_valido)
			{
				uf_print_cuentas_contable();
			}			
			break;
	}

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_grid_contable($ai_totrowscg)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_grid_contable
		//		   Access: private
		//	    Arguments: ai_totrowscg    // Total de filas de contabilidad
		//	  Description: Método que carga el datastored de las cuentas contables
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 27/06/2007								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_funciones_scf, $io_ds_scgcuentas;
		// Recorrido del Grid de Cuentas Contables
		for($li_fila=1;$li_fila<$ai_totrowscg;$li_fila++)
		{
			$ls_cuenta=trim($io_funciones_scf->uf_obtenervalor("txtcuenta".$li_fila,""));
			$ls_descripcion=trim($io_funciones_scf->uf_obtenervalor("txtdescripcion".$li_fila,""));
			$ls_procede=trim($io_funciones_scf->uf_obtenervalor("txtprocede".$li_fila,"SCGCMP"));
			$ls_documento=trim($io_funciones_scf->uf_obtenervalor("txtdocumento".$li_fila,""));
			$li_mondeb=trim($io_funciones_scf->uf_obtenervalor("txtmondeb".$li_fila,""));
			$li_monhab=trim($io_funciones_scf->uf_obtenervalor("txtmonhab".$li_fila,""));
			$ls_debhab=trim($io_funciones_scf->uf_obtenervalor("txtdebhab".$li_fila,""));
			$li_mondeb=str_replace(".","",$li_mondeb);
			$li_mondeb=str_replace(",",".",$li_mondeb);	
			$li_monhab=str_replace(".","",$li_monhab);
			$li_monhab=str_replace(",",".",$li_monhab);	
			$io_ds_scgcuentas->insertRow("cuenta",$ls_cuenta);			
			$io_ds_scgcuentas->insertRow("descripcion",$ls_descripcion);			
			$io_ds_scgcuentas->insertRow("procede",$ls_procede);			
			$io_ds_scgcuentas->insertRow("documento",$ls_documento);			
			$io_ds_scgcuentas->insertRow("mondeb",$li_mondeb);			
			$io_ds_scgcuentas->insertRow("monhab",$li_monhab);			
			$io_ds_scgcuentas->insertRow("debhab",$ls_debhab);			
		}
	}// end function uf_load_grid_contable
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cuentas_contable()
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print_cuentas_contable
		//		   Access: private
		//	    Arguments: 
		//	  Description: Método que imprime el grid de las cuentas de contabilidad
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 27/06/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_grid, $io_funciones_scf, $io_ds_scgcuentas, $rs_cuentas, $io_comprobante;
		global $ls_comprobante,$ld_fecha,$ls_procede,$ls_codban,$ls_ctaban;
		if($rs_cuentas == NULL)
		{
		    
			$lb_valido=$io_comprobante->uf_obtener_cuentas_contables($ls_procede,$ls_comprobante,$ld_fecha,$ls_codban,$ls_ctaban,&$rs_cuentas);
		}
				
		// Titulos el Grid
		$lo_title[1]="Cuenta";
		$lo_title[2]="Descripci&oacute;n";
		$lo_title[3]="Procede";
		$lo_title[4]="Documento";
		$lo_title[5]="Debe";
		$lo_title[6]="Haber"; 
		$lo_title[7]=" "; 
		$li_totaldebe=0;
		$li_totalhaber=0;
		$li_fila = 1;
	    while(!$rs_cuentas->EOF)
		{
		    $ls_cuenta=$rs_cuentas->fields["cuenta"];
			$ls_descripcion=utf8_encode($rs_cuentas->fields["descripcion"]);
			$ls_procede=$rs_cuentas->fields["procede"];			
			$ls_documento=$rs_cuentas->fields["documento"];
			$ls_debhab=$rs_cuentas->fields["debhab"];
			$ls_formato="";
			if($ls_debhab=="D")
			{
				$ls_formato="sin-borde";
				$li_mondeb=number_format($rs_cuentas->fields["mondeb"],2,",",".");
				$li_monhab="";
				$li_totaldebe=$li_totaldebe+$rs_cuentas->fields["mondeb"];
			}
			else
			{
				$ls_formato="celdas-azules";
				$li_mondeb="";
				$li_monhab=number_format($rs_cuentas->fields["monhab"],2,",",".");
				$li_totalhaber=$li_totalhaber+$rs_cuentas->fields["monhab"];
			}
			$lo_object[$li_fila][1]="<input name=txtcuenta".$li_fila."      type=text id=txtcuenta".$li_fila."      class=".$ls_formato." style=text-align:center size=13 value='".$ls_cuenta."'      readonly>";
			$lo_object[$li_fila][2]="<input name=txtdescripcion".$li_fila." type=text id=txtdescripcion".$li_fila." class=".$ls_formato." style=text-align:center size=25 value='".$ls_descripcion."' readonly>";
			$lo_object[$li_fila][3]="<input name=txtprocede".$li_fila."     type=text id=txtprocede".$li_fila."     class=".$ls_formato." style=text-align:center size=5 value='".$ls_procede."'     readonly>";
			$lo_object[$li_fila][4]="<input name=txtdocumento".$li_fila."   type=text id=txtdocumento".$li_fila."   class=".$ls_formato." style=text-align:center size=15 value='".$ls_documento."'   readonly>";
			$lo_object[$li_fila][5]="<input name=txtmondeb".$li_fila."      type=text id=txtmondeb".$li_fila."      class=".$ls_formato." style=text-align:right size=16  value='".$li_mondeb."'      readonly>";
			$lo_object[$li_fila][6]="<input name=txtmonhab".$li_fila."      type=text id=txtmonhab".$li_fila."      class=".$ls_formato." style=text-align:right size=16  value='".$li_monhab."'      readonly>";
			$lo_object[$li_fila][7]="<a href=javascript:ue_delete_scg_cuenta('".$li_fila."');><img src=../shared/imagebank/tools15/eliminar.gif title=Eliminar width=15 height=10 border=0></a>".
									"<input name=txtdebhab".$li_fila."      type=hidden id=txtdebhab".$li_fila."    value='".$ls_debhab."'>";
		 $li_fila++;
		 $rs_cuentas->MoveNext();
		}
		$li_diferencia=$li_totaldebe-$li_totalhaber;
		$lo_object[$li_fila][1]="<input name=txtcuenta".$li_fila."      type=text id=txtcuenta".$li_fila."      class=sin-borde style=text-align:center size=13 value='' readonly>";
		$lo_object[$li_fila][2]="<input name=txtdescripcion".$li_fila." type=text id=txtdescripcion".$li_fila." class=sin-borde style=text-align:center size=25 value='' readonly>";
		$lo_object[$li_fila][3]="<input name=txtprocede".$li_fila."     type=text id=txtprocede".$li_fila."     class=sin-borde style=text-align:center size=5 value='' readonly>";
		$lo_object[$li_fila][4]="<input name=txtdocumento".$li_fila."   type=text id=txtdocumento".$li_fila."   class=sin-borde style=text-align:center size=15 value='' readonly>";
		$lo_object[$li_fila][5]="<input name=txtmondeb".$li_fila."      type=text id=txtmondeb".$li_fila."      class=sin-borde style=text-align:right size=16  value='' readonly>";
		$lo_object[$li_fila][6]="<input name=txtmonhab".$li_fila."      type=text id=txtmonhab".$li_fila."      class=sin-borde style=text-align:right size=16  value='' readonly>";
		$lo_object[$li_fila][7]="".
								"<input name=txtdebhab".$li_fila."      type=hidden id=txtdebhab".$li_fila."    value=''>";
		print "  <table width='720' border='0' align='center' cellpadding='0' cellspacing='0' class='celdas-blancas'>";
		print "    <tr>";
		//print "		<td  align='left'><a href='javascript:ue_catalogo_cuentas_scg();'><img src='../shared/imagebank/tools/nuevo.gif' width='20' height='20' border='0' title='Agregar Cuenta'>Agregar Cuenta Contable</a>&nbsp;&nbsp;</td>";
		print "    </tr>";
		print "  </table>";
		$io_grid->makegrid($li_fila,$lo_title,$lo_object,720,"Cuentas Contables","gridcuentas");		
		print "<table width='720' border='0' align='center' cellpadding='0' cellspacing='0' class='celdas-blancas'>";
		print "        <tr>";
		print "          <td width='175' height='22' align='right'></td>";
		print "          <td width='175' height='22' align='left'></td>";
		print "          <td width='175' height='22' align='right'><div align='right'><strong>Total Debe</strong></div></td>";
		print "          <td width='175' height='22' align='left'><input name='txttotaldebe'  type='text' id='txttotaldebe' style='text-align:right' value='".number_format($li_totaldebe,2,",",".")."' size='22' maxlength='20' readonly align='right' class='letras-negrita'></td>";
		print "        </tr>";
		print "        <tr>";
		print "          <td width='175' height='22' align='right'></td>";
		print "          <td width='175' height='22' align='left'></td>";
		print "          <td width='175' height='22' align='right'><div align='right'><strong>Total Haber</strong></div></td>";
		print "          <td width='175' height='22' align='left'><input name='txttotalhaber'  type='text' id='txttotalhaber' style='text-align:right' value='".number_format($li_totalhaber,2,",",".")."' size='22' maxlength='20' readonly align='right' class='letras-negrita'></td>";
		print "        </tr>";
		print "        <tr>";
		print "          <td width='175' height='22' align='right'></td>";
		print "          <td width='175' height='22' align='left'></td>";
		print "          <td width='175' height='22' align='right'><div align='right'><strong>Diferencia</strong></div></td>";
		print "          <td width='175' height='22' align='left'><input name='txtdiferencia'  type='text' id='txtdiferencia' style='text-align:right' value='".number_format($li_diferencia,2,",",".")."' size='22' maxlength='20' readonly align='right' class='letras-negrita'></td>";
		print "        </tr>";
		print "</table>";
		unset($io_ds_scgcuentas);
		unset($rs_cuentas);
	}// end function uf_print_cuentas_contable
	//-----------------------------------------------------------------------------------------------------------------------------------
?>