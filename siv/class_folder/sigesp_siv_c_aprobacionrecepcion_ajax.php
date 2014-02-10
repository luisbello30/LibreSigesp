<?php
	session_start();  
	require_once("../../shared/class_folder/grid_param.php");
	$io_grid=new grid_param();
	require_once("../../shared/class_folder/class_funciones.php");
	$io_funciones=new class_funciones();
	require_once("../class_funciones_inventario.php");
	$io_funciones_siv=new class_funciones_inventario();
	require_once("sigesp_siv_c_aprobacionrecepcion.php");
	$io_aprobacion=new sigesp_siv_c_aprobacionrecepcion('../../');
	// proceso a ejecutar
	$ls_proceso=$io_funciones_siv->uf_obtenervalor("proceso","");
	// numero de recepcion 
	$ls_numsol=$io_funciones_siv->uf_obtenervalor("numsol","");
	// fecha(registro) de inicio de busqueda
	$ld_fecregdes=$io_funciones_siv->uf_obtenervalor("fecregdes","");
	// fecha(registro) de fin de busqueda
	$ld_fecreghas=$io_funciones_siv->uf_obtenervalor("fecreghas","");
	// tipo de operacion aprobacion/reverso
	$ls_tipooperacion=$io_funciones_siv->uf_obtenervalor("tipooperacion","");
	// nunmero de orden de compra o factura
	$ls_numordcom=$io_funciones_siv->uf_obtenervalor("numordcom","");
	switch($ls_proceso)
	{
		case "BUSCAR":
			uf_print_recepciones($ls_numsol,$ld_fecregdes,$ld_fecreghas,$ls_numordcom,$ls_tipooperacion);
			break;
	}

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_recepciones($as_numsol,$ad_fecregdes,$ad_fecreghas,$as_numordcom,$as_tipooperacion)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print_recepciones
		//		   Access: private
		//		 Argument: as_numsol        // Numero de la solicitud de orden de Pago
		//                 ad_fecregdes     // Fecha (Registro) de inicio de la Busqueda
		//                 ad_fecreghas     // Fecha (Registro) de fin de la Busqueda
		//                 as_tipooperacion // Codigo de la Unidad Ejecutora
		//	  Description: Método que impirme el grid de las entradas de suministros de almacen
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 05/05/2007								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_grid, $io_funciones_siv, $io_funciones, $io_aprobacion, $io_mensajes;
		// Titulos del Grid de Solicitudes
		$lo_title[1]="";
		$lo_title[2]="Orden de Compra/Factura";
		$lo_title[3]="Recepcion";
		$lo_title[4]="Fecha";
		$lo_title[5]="Almacen";
		$ad_fecregdes=$io_funciones->uf_convertirdatetobd($ad_fecregdes);
		$ad_fecreghas=$io_funciones->uf_convertirdatetobd($ad_fecreghas);
		$as_numsol="%".$as_numsol."%";
		$as_numordcom="%".$as_numordcom."%";
		$rs_datasol=$io_aprobacion->uf_load_recepciones($as_numsol,$ad_fecregdes,$ad_fecreghas,$as_numordcom,$as_tipooperacion);
		$li_fila=0;
		while($row=$io_aprobacion->io_sql->fetch_row($rs_datasol))
		{
			$li_fila=$li_fila + 1;
			$ls_numordcom=$row["numordcom"];
			$ls_numconrec=$row["numconrec"];
			$ld_fecrec=$row["fecrec"];
			$ls_nomalm=$row["nomalm"];
			$ls_codalm=$row["codalm"];
			$ls_estpro=$row["estpro"];
			$ld_fecrec=$io_funciones->uf_convertirfecmostrar($ld_fecrec);
			$lo_object[$li_fila][1]="<input type=checkbox name=chkaprobacion".$li_fila.">";
			$lo_object[$li_fila][2]="<input type=text name=txtnumordcom".$li_fila." id=txtnumordcom".$li_fila." class=sin-borde style=text-align:center size=20 value='".$ls_numordcom."' readonly>";
			$lo_object[$li_fila][3]="<input type=text name=txtnumconrec".$li_fila." id=txtnumconrec".$li_fila." class=sin-borde style=text-align:center   size=20 value='".$ls_numconrec."' readonly>"; 
			$lo_object[$li_fila][4]="<input type=text name=txtfecrec".$li_fila."    id=txtfecrec".$li_fila."    class=sin-borde style=text-align:left   size=27 value='".$ld_fecrec."'    readonly>"; 
			$lo_object[$li_fila][5]="<input type=text name=txtnomalm".$li_fila."    id=txtnomalm".$li_fila."    class=sin-borde style=text-align:left   size=50 value='".$ls_nomalm."'   readonly>".
									"<input type=hidden name=txtcodalm".$li_fila."    id=txtcodalm".$li_fila."  value='".$ls_codalm."'>".
									"<input type=hidden name=txtestpro".$li_fila."    id=txtestpro".$li_fila."  value='".$ls_estpro."'>";
		}
		if($li_fila==0)
		{
			$io_aprobacion->io_mensajes->message("No se encontraron resultados");
			$li_fila=1;
			$lo_object[$li_fila][1]="<input type=checkbox name=chkaprobacion".$li_fila.">";
			$lo_object[$li_fila][2]="<input type=text name=txtnumordcom".$li_fila." id=txtnumordcom".$li_fila." class=sin-borde style=text-align:center size=20 value='' readonly>";
			$lo_object[$li_fila][3]="<input type=text name=txtnumconrec".$li_fila." id=txtnumconrec".$li_fila." class=sin-borde style=text-align:center   size=12 value='' readonly>"; 
			$lo_object[$li_fila][4]="<input type=text name=txtfecrec".$li_fila."    id=txtfecrec".$li_fila."    class=sin-borde style=text-align:left   size=35 value=''    readonly>"; 
			$lo_object[$li_fila][5]="<input type=text name=txtnomalm".$li_fila."    id=txtnomalm".$li_fila."    class=sin-borde style=text-align:left   size=50 value=''   readonly>";
		}

		$io_grid->makegrid($li_fila,$lo_title,$lo_object,700,"Entradas de Suministros al Almacen","gridsolicitudes");
	}// end function uf_print_solicitudes
	//-----------------------------------------------------------------------------------------------------------------------------------
?>