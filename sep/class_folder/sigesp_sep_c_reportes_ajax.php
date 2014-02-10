<?php
	session_start();  
	require_once("../../shared/class_folder/grid_param.php");
	$io_grid=new grid_param();
	require_once("../../shared/class_folder/class_funciones.php");
	$io_funciones=new class_funciones();
	require_once("class_funciones_sep.php");
	$io_funciones_sep=new class_funciones_sep();
	require_once("../../shared/class_folder/sigesp_include.php");
	$io_include=new sigesp_include();
	$io_conexion=$io_include->uf_conectar();
	require_once("../../shared/class_folder/class_sql.php");
	$io_sql=new class_sql($io_conexion);	
	require_once("../../shared/class_folder/class_mensajes.php");
	$io_mensajes=new class_mensajes();		
    $ls_codemp=$_SESSION["la_empresa"]["codemp"];
	// proceso a ejecutar
	$ls_proceso=$io_funciones_sep->uf_obtenervalor("proceso","");
	// Numero de solicitud de inicio de busqueda
	$ls_numsoldes=$io_funciones_sep->uf_obtenervalor("numsoldes","");
	// Numero de solicitud de fin de busqueda
	$ls_numsolhas=$io_funciones_sep->uf_obtenervalor("numsolhas","");
	// Tipo de Proveedor/Beneficiario
	$ls_tipproben=$io_funciones_sep->uf_obtenervalor("tipproben","");
	// Codigo de Proveedor/Beneficiario de inicio de busqueda
	$ls_codprobendes=$io_funciones_sep->uf_obtenervalor("codprobendes","");
	// Codigo de Proveedor/Beneficiario de fin de busqueda
	$ls_codprobenhas=$io_funciones_sep->uf_obtenervalor("codprobenhas","");
	// fecha(registro) de inicio de busqueda
	$ld_fegregdes=$io_funciones_sep->uf_obtenervalor("fegregdes","");
	// fecha(registro) de fin de busqueda
	$ld_fegreghas=$io_funciones_sep->uf_obtenervalor("fegreghas","");
	// unidad administrativa de inicio de busqueda
	$ls_codunides=$io_funciones_sep->uf_obtenervalor("codunides","");
	// unidad administrativa de inicio de busqueda
	$ls_codunihas=$io_funciones_sep->uf_obtenervalor("codunihas","");
	// Tipo de solicitud
	$ls_tipsol=$io_funciones_sep->uf_obtenervalor("tipsol","");
	// Estatus de Registro
	$li_registrada=$io_funciones_sep->uf_obtenervalor("registrada",0);
	// Estatus de Emitida
	$li_emitida=$io_funciones_sep->uf_obtenervalor("emitida",0);
	// Estatus de Contabilizada
	$li_contabilizada=$io_funciones_sep->uf_obtenervalor("contabilizada",0);
	// Estatus de Procesada
	$li_procesada=$io_funciones_sep->uf_obtenervalor("procesada",0);
	// Estatus de Anulada
	$li_anulada=$io_funciones_sep->uf_obtenervalor("anulada",0);
	// Estatus de Despachada
	$li_despachada=$io_funciones_sep->uf_obtenervalor("despachada",0);
	// Codigo de Usuario de inicio de busqueda
	$ls_codusudes=$io_funciones_sep->uf_obtenervalor("codusudes","");
	//  Codigo de Usuario de fin de busqueda
	$ls_codusuhas=$io_funciones_sep->uf_obtenervalor("codusuhas","");
	// Estatus de Aprobada
	$li_aprobada=$io_funciones_sep->uf_obtenervalor("aprobada",0);
	// Estatus de Pagada
	$li_pagada=$io_funciones_sep->uf_obtenervalor("pagada",0);
	switch($ls_proceso)
	{
		case "SOLICITUDES":
			uf_print_solicitudes($ls_numsoldes,$ls_numsolhas,$ls_tipproben,$ls_codprobendes,$ls_codprobenhas,$ld_fegregdes,
								 $ld_fegreghas,$ls_codunides,$ls_codunihas,$ls_tipsol,$li_registrada,$li_emitida,$li_contabilizada,
								 $li_procesada,$li_anulada,$li_despachada,$ls_codusudes,$ls_codusuhas,$li_aprobada,$li_pagada);
			break;
	}

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_solicitudes($ls_numsoldes,$ls_numsolhas,$ls_tipproben,$ls_codprobendes,$ls_codprobenhas,$ld_fegregdes,
								 $ld_fegreghas,$ls_codunides,$ls_codunihas,$ls_tipsol,$li_registrada,$li_emitida,$li_contabilizada,
								 $li_procesada,$li_anulada,$li_despachada,$ls_codusudes,$ls_codusuhas,$li_aprobada,$li_pagada)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print_solicitudes
		//		   Access: private
		//	    Arguments: as_numsoldes     // Numero de solicitud de inicio del parametro de Busqueda
		//	  			   as_numsolhas     // Numero de solicitud de fin del parametro de Busqueda
		//	  			   as_tipproben     // Indica si es proveedor o beneficiario
		//	  			   as_codprobendes  // Código del proveedor/beneficiario de inicio del parametro de Busqueda
		//	  			   as_codprobenhas  // Código del proveedor/beneficiario de fin del parametro de Busqueda
		//	  			   ad_fegregdes     // Fecha de registgro de la solicitud de inicio del parametro de Busqueda
		//	  			   ad_fegregdes     // Fecha de registgro de la solicitud de fin del parametro de Busqueda
		//	  			   as_codunides     // Codigo de unidad ejecutora de inicio del parametro de Busqueda
		//	  			   as_codunihas     // Codigo de unidad ejecutora de fin del parametro de Busqueda
		//	  			   as_tipsol        // Indica el tipo de solicitud (Bienes, Servicios, Conceptos)
		//	  			   ai_registrada    // Indica si se desea filtrar por este estatus de solicitud
		//	  			   ai_emitida       // Indica si se desea filtrar por este estatus de solicitud
		//	  			   ai_registrada    // Indica si se desea filtrar por este estatus de solicitud
		//	  			   ai_contabilizada // Indica si se desea filtrar por este estatus de solicitud
		//	  			   ai_procesada     // Indica si se desea filtrar por este estatus de solicitud
		//	  			   ai_anulada       // Indica si se desea filtrar por este estatus de solicitud
		//	  			   ai_despachada    // Indica si se desea filtrar por este estatus de solicitud
		//	  			   as_codusudes    // Indica si se desea filtrar por el código de usuario
		//	  			   as_codusuhas    // Indica si se desea filtrar por el código de usuario
		//                 ai_aprobada		// Indica si se desea filtrar por este estatus de solicitud
		//                 ai_pagada		// Indica si se desea filtrar por este estatus de solicitud
		//	  Description: Método que impirme el grid de las solicitudes de pago a imprimir en el reporte
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 16/06/2007								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_grid, $io_funciones_cxp, $io_funciones, $io_sql, $io_mensajes;
		// Titulos del Grid de Solicitudes
		$lo_title[1]="";
		$lo_title[2]="Solicitud";
		$lo_title[3]="Proveedor / Beneficiario";
		$lo_title[4]="Concepto";
		$lo_title[5]="Fecha";
		$lo_title[6]="Monto";
		/*$ld_fegregdes=$io_funciones->uf_convertirdatetobd($ld_fegregdes);
		$ld_fegreghas=$io_funciones->uf_convertirdatetobd($ld_fegreghas);*/
		$rs_datasol=uf_load_solicitudes($ls_numsoldes,$ls_numsolhas,$ls_tipproben,$ls_codprobendes,$ls_codprobenhas,$ld_fegregdes,
										$ld_fegreghas,$ls_codunides,$ls_codunihas,$ls_tipsol,$li_registrada,$li_emitida,$li_contabilizada,
										$li_procesada,$li_anulada,$li_despachada,$ls_codusudes,$ls_codusuhas,$li_aprobada,$li_pagada);
		$li_fila=0;
		while(!$rs_datasol->EOF)
		{
			$li_fila=$li_fila + 1;
			$ls_numsol=$rs_datasol->fields["numsol"]; 
			$ld_fecregsol=$rs_datasol->fields["fecregsol"];
			$ld_fecregsol=$io_funciones->uf_formatovalidofecha($ld_fecregsol);
			$ls_proben=utf8_encode($rs_datasol->fields["nombre"]);
			$ls_consol=utf8_encode($rs_datasol->fields["consol"]);
			$li_monsol=number_format($rs_datasol->fields["monto"],2,',','.');
			$ld_fecregsol=$io_funciones->uf_convertirfecmostrar($ld_fecregsol);
			$lo_object[$li_fila][1]="<input type=checkbox name=chkimprimir".$li_fila.">";
			$lo_object[$li_fila][2]="<input type=text name=txtnumsol".$li_fila." id=txtnumsol".$li_fila." class=sin-borde style=text-align:center size=20 value='".$ls_numsol."' readonly>";
			$lo_object[$li_fila][3]="<input type=text name=txtproben".$li_fila."    id=txtproben".$li_fila."    class=sin-borde style=text-align:left   size=35 value='".$ls_proben."'    readonly>"; 
			$lo_object[$li_fila][4]="<input type=text name=txtconsol".$li_fila."    id=txtconsol".$li_fila."    class=sin-borde style=text-align:left   size=27 value='".$ls_consol."'   readonly>";
			$lo_object[$li_fila][5]="<input type=text name=txtfecemisol".$li_fila." id=txtfecemisol".$li_fila." class=sin-borde style=text-align:left   size=13 value='".$ld_fecregsol."' readonly>"; 
			$lo_object[$li_fila][6]="<input type=text name=txtmonsol".$li_fila." id=txtmonsol".$li_fila." class=sin-borde style=text-align:right  size=15 value='".$li_monsol."' readonly>";
			$rs_datasol->MoveNext();
		}
		if($li_fila==0)
		{
			$io_mensajes->message("No se encontraron resultados");
			$li_fila=1;
			$lo_object[$li_fila][1]="<input type=checkbox name=chkimprimir value=1 disabled/>";
			$lo_object[$li_fila][2]="<input type=text name=txtnumsol".$li_fila." class=sin-borde style=text-align:center size=20 readonly>";
			$lo_object[$li_fila][3]="<input type=text name=txtproben".$li_fila."    class=sin-borde style=text-align:left   size=35 readonly>"; 
			$lo_object[$li_fila][4]="<input type=text name=txtconsol".$li_fila."    class=sin-borde style=text-align:left   size=27 readonly>";
			$lo_object[$li_fila][5]="<input type=text name=txtfecemisol".$li_fila." class=sin-borde style=text-align:left   size=13 readonly>"; 
			$lo_object[$li_fila][6]="<input type=text name=txtmonsol".$li_fila." class=sin-borde style=text-align:right  size=15 readonly>";
		}

		$io_grid->makegrid($li_fila,$lo_title,$lo_object,700,"Solicitudes de Ejecucion Presupuestaria","gridsolicitudes");
	}// end function uf_print_solicitudes
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_solicitudes($as_numsoldes,$as_numsolhas,$as_tipproben,$as_codprobendes,$as_codprobenhas,$ad_fegregdes,
								 $ad_fegreghas,$as_codunides,$as_codunihas,$as_tipsol,$ai_registrada,$ai_emitida,$ai_contabilizada,
								 $ai_procesada,$ai_anulada,$ai_despachada,$as_codusudes,$as_codusuhas,$ai_aprobada,$ai_pagada)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_load_solicitudes
		//         Access: public  
		//	    Arguments: as_numsoldes     // Numero de solicitud de inicio del parametro de Busqueda
		//	  			   as_numsolhas     // Numero de solicitud de fin del parametro de Busqueda
		//	  			   as_tipproben     // Indica si es proveedor o beneficiario
		//	  			   as_codprobendes  // Código del proveedor/beneficiario de inicio del parametro de Busqueda
		//	  			   as_codprobenhas  // Código del proveedor/beneficiario de fin del parametro de Busqueda
		//	  			   ad_fegregdes     // Fecha de registgro de la solicitud de inicio del parametro de Busqueda
		//	  			   ad_fegregdes     // Fecha de registgro de la solicitud de fin del parametro de Busqueda
		//	  			   as_codunides     // Codigo de unidad ejecutora de inicio del parametro de Busqueda
		//	  			   as_codunihas     // Codigo de unidad ejecutora de fin del parametro de Busqueda
		//	  			   as_tipsol        // Indica el tipo de solicitud (Bienes, Servicios, Conceptos)
		//	  			   ai_registrada    // Indica si se desea filtrar por este estatus de solicitud
		//	  			   ai_emitida       // Indica si se desea filtrar por este estatus de solicitud
		//	  			   ai_registrada    // Indica si se desea filtrar por este estatus de solicitud
		//	  			   ai_contabilizada // Indica si se desea filtrar por este estatus de solicitud
		//	  			   ai_procesada     // Indica si se desea filtrar por este estatus de solicitud
		//	  			   ai_anulada       // Indica si se desea filtrar por este estatus de solicitud
		//	  			   ai_despachada    // Indica si se desea filtrar por este estatus de solicitud
		//	  			   as_codusudes    // Indica si se desea filtrar por el código de usuario
		//	  			   as_codusuhas    // Indica si se desea filtrar por el código de usuario
		//                 ai_aprobada		// Indica si se desea filtrar por este estatus de solicitud
		//                 ai_pagada		// Indica si se desea filtrar por este estatus de solicitud
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de las solicitudes de pago en el intervalo indicado
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 16/06/2007									Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $ls_codemp, $io_sql, $io_funciones,$io_mensajes;
		$lb_valido=true;
		$ls_criterio="";
		$ls_criteusu="";
		if(!empty($as_numsoldes))
		{
			$ls_criterio=$ls_criterio. "  AND sep_solicitud.numsol>='".$as_numsoldes."'";
		}
		if(!empty($as_numsolhas))
		{
			$ls_criterio=$ls_criterio. "  AND sep_solicitud.numsol<='".$as_numsolhas."'";
		}
		if(!empty($as_tipproben))
		{
			$ls_criterio= $ls_criterio."   AND sep_solicitud.tipo_destino='".$as_tipproben."'";
		}
		if(!empty($as_codprobendes))
		{
			if($as_tipproben=="P")
			{
				$ls_criterio= $ls_criterio."   AND sep_solicitud.cod_pro>='".$as_codprobendes."'";
			}
			else
			{
				$ls_criterio= $ls_criterio."   AND sep_solicitud.ced_bene>='".$as_codprobendes."'";
			}
		}
		if(!empty($as_codprobenhas))
		{
			if($as_tipproben=="P")
			{
				$ls_criterio= $ls_criterio."   AND sep_solicitud.cod_pro<='".$as_codprobenhas."'";
			}
			else
			{
				$ls_criterio= $ls_criterio."   AND sep_solicitud.ced_bene<='".$as_codprobenhas."'";
			}
		}
		if(!empty($ad_fegregdes))
		{
			$ad_fegregdes=$io_funciones->uf_convertirdatetobd($ad_fegregdes);
			$ls_criterio=$ls_criterio. "  AND sep_solicitud.fecregsol>='".$ad_fegregdes."'";
		}
		if(!empty($ad_fegreghas))
		{
			$ad_fegreghas=$io_funciones->uf_convertirdatetobd($ad_fegreghas);
			$ls_criterio=$ls_criterio. "  AND sep_solicitud.fecregsol<='".$ad_fegreghas."'";
		}
		if(!empty($as_codunides))
		{
			$ls_criterio=$ls_criterio. "  AND sep_solicitud.coduniadm>='".$as_codunides."'";
		}
		if(!empty($as_codunihas))
		{
			$ls_criterio=$ls_criterio. "  AND sep_solicitud.coduniadm<='".$as_codunihas."'";
		}
		if(!empty($as_tipsol))
		{
			$ls_criterio=$ls_criterio. " AND sep_solicitud.codtipsol=sep_tiposolicitud.codtipsol".
									   " AND sep_tiposolicitud.modsep='".$as_tipsol."'";
		}
		if(($as_codusudes!="")&&($as_codusuhas!=""))
        {
		   $ls_criteusu=$ls_criteusu."  AND sep_solicitud.codaprusu=sss_usuarios.codusu   ".
		                             "  AND sss_usuarios.codusu>='".$as_codusudes."'AND sss_usuarios.codusu<='".$as_codusuhas."' AND";
		}
		else
		{
		  $ls_criteusu=$ls_criteusu."AND ";
		}
		
		if(($ai_registrada==1)or($ai_emitida==1)or($ai_contabilizada==1)or($ai_procesada==1)or($ai_anulada==1)or($ai_despachada==1)or($ai_aprobada==1))
		{
			$lb_anterior=false;
			if($ai_registrada==1)
			{
				if(!$lb_anterior)
				{
					$ls_criterio=$ls_criterio."  AND (sep_solicitud.estsol='R'";
					$lb_anterior=true;
				}
			}
			if($ai_emitida==1)
			{
				if(!$lb_anterior)
				{
					$ls_criterio=$ls_criterio."  AND (sep_solicitud.estsol='E' AND sep_solicitud.estapro='0'";
					$lb_anterior=true;
				}
				else
				{
					$ls_criterio=$ls_criterio."  OR sep_solicitud.estsol='E' AND sep_solicitud.estapro='0'";
				}
			}
			if($ai_contabilizada==1)
			{
				if(!$lb_anterior)
				{
					$ls_criterio=$ls_criterio."  AND (sep_solicitud.estsol='C'";
					$lb_anterior=true;
				}
				else
				{
					$ls_criterio=$ls_criterio."  OR sep_solicitud.estsol='C'";
				}
			}
			if($ai_procesada==1)
			{
				if(!$lb_anterior)
				{
					$ls_criterio=$ls_criterio."  AND (sep_solicitud.estsol='P'";
					$lb_anterior=true;
				}
				else
				{
					$ls_criterio=$ls_criterio."  OR sep_solicitud.estsol='P'";
				}
			}
			if($ai_anulada==1)
			{
				if(!$lb_anterior)
				{
					$ls_criterio=$ls_criterio."  AND (sep_solicitud.estsol='A'";
					$lb_anterior=true;
				}
				else
				{
					$ls_criterio=$ls_criterio."  OR sep_solicitud.estsol='A'";
				}
			}
			if($ai_despachada==1)
			{
				if(!$lb_anterior)
				{
					$ls_criterio=$ls_criterio."  AND (sep_solicitud.estsol='D'";
					$lb_anterior=true;
				}
				else
				{
					$ls_criterio=$ls_criterio."  OR sep_solicitud.estsol='D'";
				}
			}
			
			if($ai_aprobada==1)
			{
				if(!$lb_anterior)
				{
					$ls_criterio=$ls_criterio."  AND (sep_solicitud.estsol='E' AND sep_solicitud.estapro='1' ";
					$lb_anterior=true;
				}
				else
				{
					$ls_criterio=$ls_criterio."  OR sep_solicitud.estsol='E' AND sep_solicitud.estapro='1' ";
				}
			}
			if($lb_anterior)
			{
				$ls_criterio=$ls_criterio.")";
			}
		}
		 
		switch ($_SESSION["ls_gestor"])
		{
			case "MYSQLT":
				$ls_cadena="CONCAT(rpc_beneficiario.nombene,' ',rpc_beneficiario.apebene)";
				break;
			case "POSTGRES":
				$ls_cadena="rpc_beneficiario.nombene||' '||rpc_beneficiario.apebene";
				break;
			case "INFORMIX":
				$ls_cadena="rpc_beneficiario.nombene||' '||rpc_beneficiario.apebene";
				break;
		}
			$ls_sql2="";
		    if ($ai_pagada==1)
			{
				$ls_sql2=" SELECT sep_solicitud.numsol,sep_solicitud.codtipsol,sep_solicitud.coduniadm,
								 sep_solicitud.codfuefin,cast('PA' AS char(2)) as estsol,
							     sep_solicitud.estapro,sep_solicitud.consol, sep_solicitud.monto, 
								 sep_solicitud.monbasinm, sep_solicitud.montotcar, 
							     sep_solicitud.tipo_destino, sep_solicitud.cod_pro, 
								 sep_solicitud.ced_bene,spg_unidadadministrativa.denuniadm AS denuniadm,
							     sep_solicitud.fecregsol, sep_solicitud.codaprusu,
       							(CASE WHEN sep_solicitud.tipo_destino='B' 
								      THEN (SELECT ".$ls_cadena."
				                              FROM rpc_beneficiario
				                             WHERE sep_solicitud.ced_bene=rpc_beneficiario.ced_bene
				                          group by sep_solicitud.ced_bene,  rpc_beneficiario.nombene, rpc_beneficiario.apebene)
	                                  WHEN sep_solicitud.tipo_destino='P' 
									 THEN (SELECT rpc_proveedor.nompro
				                             FROM rpc_proveedor
				                            WHERE sep_solicitud.cod_pro=rpc_proveedor.cod_pro
				                         group by sep_solicitud.cod_pro, rpc_proveedor.nompro) ELSE 'NINGUNO' END) AS nombre,
	                             sep_solicitud.codestpro1, sep_solicitud.codestpro2,sep_solicitud.codestpro3,
								 sep_solicitud.codestpro4,sep_solicitud.codestpro5				                         				
  							from sep_solicitud
  							join spg_unidadadministrativa on (spg_unidadadministrativa.codemp=sep_solicitud.codemp
														and sep_solicitud.coduniadm=spg_unidadadministrativa.coduniadm)
						    join soc_enlace_sep on (soc_enlace_sep.codemp=sep_solicitud.codemp
									  and soc_enlace_sep.numsol=sep_solicitud.numsol)
						    join soc_ordencompra on (soc_ordencompra.codemp=soc_enlace_sep.codemp
											  and  soc_ordencompra.numordcom=soc_enlace_sep.numordcom)
						    join cxp_rd on (cxp_rd.codemp=soc_enlace_sep.codemp
									 and  cxp_rd.cod_pro=soc_ordencompra.cod_pro
									 and  cxp_rd.codtipdoc='00001')
						    join cxp_solicitudes on (cxp_solicitudes.codemp=cxp_rd.codemp
									  and  cxp_solicitudes.cod_pro=cxp_rd.cod_pro
									  and  cxp_solicitudes.ced_bene=cxp_rd.ced_bene)
						    join cxp_sol_banco on (cxp_sol_banco.codemp=cxp_solicitudes.codemp
									and  cxp_sol_banco.numsol=cxp_solicitudes.numsol
									and  cxp_sol_banco.codope='CH')
						    join scb_movbco on (scb_movbco.codemp=cxp_sol_banco.codemp
								  and scb_movbco.numdoc=cxp_sol_banco.numdoc
								  and scb_movbco.estmov='C'
								  and scb_movbco.codope='CH'
								  and scb_movbco.cod_pro=cxp_rd.cod_pro
								  and scb_movbco.ced_bene=cxp_rd.ced_bene)
						group by sep_solicitud.numsol,sep_solicitud.codtipsol,sep_solicitud.coduniadm,
								sep_solicitud.codfuefin, estsol, sep_solicitud.estapro,sep_solicitud.consol, 
								sep_solicitud.monto, sep_solicitud.monbasinm, sep_solicitud.montotcar, 
							    sep_solicitud.tipo_destino, sep_solicitud.cod_pro, 
							    sep_solicitud.ced_bene,spg_unidadadministrativa.denuniadm,
							    sep_solicitud.fecregsol, sep_solicitud.codaprusu,sep_solicitud.codestpro1, 
								sep_solicitud.codestpro2,sep_solicitud.codestpro3, sep_solicitud.codestpro4,
								sep_solicitud.codestpro5	
						having sum(scb_movbco.monto)=sep_solicitud.monto ".
					 " UNION ".
					 " select sep_solicitud.numsol,sep_solicitud.codtipsol,sep_solicitud.coduniadm,sep_solicitud.codfuefin,
					          cast('PA' AS char(2)) as estsol,sep_solicitud.estapro,sep_solicitud.consol, sep_solicitud.monto,
							  sep_solicitud.monbasinm, sep_solicitud.montotcar, sep_solicitud.tipo_destino, sep_solicitud.cod_pro,
							  sep_solicitud.ced_bene,spg_unidadadministrativa.denuniadm AS denuniadm,sep_solicitud.fecregsol, 
							  sep_solicitud.codaprusu,
       						  (CASE WHEN sep_solicitud.tipo_destino='B' 
							        THEN (SELECT ".$ls_cadena."
				                    FROM rpc_beneficiario
				                   WHERE sep_solicitud.ced_bene=rpc_beneficiario.ced_bene
				                group by sep_solicitud.ced_bene,  rpc_beneficiario.nombene, rpc_beneficiario.apebene)
	                                WHEN sep_solicitud.tipo_destino='P' 
									THEN (SELECT rpc_proveedor.nompro
				                            FROM rpc_proveedor
				                           WHERE sep_solicitud.cod_pro=rpc_proveedor.cod_pro
				                         group by sep_solicitud.cod_pro, rpc_proveedor.nompro) ELSE 'NINGUNO' END) AS nombre,
							  sep_solicitud.codestpro1, sep_solicitud.codestpro2,sep_solicitud.codestpro3, 
							  sep_solicitud.codestpro4,sep_solicitud.codestpro5				                         				
  						 from sep_solicitud
					     join spg_unidadadministrativa on (spg_unidadadministrativa.codemp=sep_solicitud.codemp
									and sep_solicitud.coduniadm=spg_unidadadministrativa.coduniadm)
					     join cxp_rd on (cxp_rd.codemp=sep_solicitud.codemp
								 and  cxp_rd.cod_pro=sep_solicitud.cod_pro
								 and  cxp_rd.codtipdoc='00001')
					     join cxp_solicitudes on (cxp_solicitudes.codemp=cxp_rd.codemp
								  and  cxp_solicitudes.cod_pro=cxp_rd.cod_pro
								  and  cxp_solicitudes.ced_bene=cxp_rd.ced_bene)
					     join cxp_sol_banco on (cxp_sol_banco.codemp=cxp_solicitudes.codemp
								and  cxp_sol_banco.numsol=cxp_solicitudes.numsol
								and  cxp_sol_banco.codope='CH')
					     join scb_movbco on (scb_movbco.codemp=cxp_sol_banco.codemp
							  and scb_movbco.numdoc=cxp_sol_banco.numdoc
							  and scb_movbco.estmov='C'
							  and scb_movbco.codope='CH'
							  and scb_movbco.cod_pro=cxp_rd.cod_pro
							  and scb_movbco.ced_bene=cxp_rd.ced_bene)
					  group by sep_solicitud.numsol,sep_solicitud.codtipsol,sep_solicitud.coduniadm,sep_solicitud.codfuefin, 
					  		    estsol, sep_solicitud.estapro,sep_solicitud.consol, sep_solicitud.monto, sep_solicitud.monbasinm, 
								sep_solicitud.montotcar, sep_solicitud.tipo_destino, sep_solicitud.cod_pro, 
								sep_solicitud.ced_bene,spg_unidadadministrativa.denuniadm,
						        sep_solicitud.fecregsol, sep_solicitud.codaprusu,sep_solicitud.codestpro1, 
								sep_solicitud.codestpro2,sep_solicitud.codestpro3, sep_solicitud.codestpro4,
								sep_solicitud.codestpro5	
					having sum(scb_movbco.monto)=sep_solicitud.monto ";		
			}
			
			$ls_sql="SELECT numsol, sep_solicitud.codtipsol, sep_solicitud.coduniadm, codfuefin, estsol, estapro ,".
					"       consol, monto, monbasinm, montotcar, tipo_destino, sep_solicitud.cod_pro, sep_solicitud.ced_bene,".
					"       spg_unidadadministrativa.denuniadm AS denuniadm, sep_solicitud.fecregsol, sep_solicitud.codaprusu, ".
					"       (CASE WHEN sep_solicitud.tipo_destino='B' THEN (SELECT ".$ls_cadena."".
					"                                                      FROM rpc_beneficiario".
					"                                                     WHERE sep_solicitud.codemp=rpc_beneficiario.codemp".
					"                                                       AND sep_solicitud.ced_bene=rpc_beneficiario.ced_bene)".
					"             WHEN sep_solicitud.tipo_destino='P' THEN (SELECT nompro".
					"                                                         FROM rpc_proveedor".
					"                                                        WHERE sep_solicitud.codemp=rpc_proveedor.codemp".
					"                                                          AND sep_solicitud.cod_pro=rpc_proveedor.cod_pro)".
					"                                                  ELSE 'NINGUNO'".
					"         END) AS nombre,".				
					"       sep_solicitud.codestpro1, sep_solicitud.codestpro2,sep_solicitud.codestpro3,
					        sep_solicitud.codestpro4,sep_solicitud.codestpro5 ".
					"  FROM sep_solicitud, sep_tiposolicitud,spg_unidadadministrativa, spg_dt_unidadadministrativa,sss_usuarios".
					" WHERE sep_solicitud.codemp='".$ls_codemp."'".
					"   AND sep_solicitud.codemp=spg_unidadadministrativa.codemp AND ".
					"   sep_solicitud.coduniadm=spg_unidadadministrativa.coduniadm AND".
					"   sep_solicitud.codestpro1=spg_dt_unidadadministrativa.codestpro1 AND 
					    sep_solicitud.codestpro2=spg_dt_unidadadministrativa.codestpro2 AND
						sep_solicitud.codestpro3=spg_dt_unidadadministrativa.codestpro3 AND
						sep_solicitud.codestpro4=spg_dt_unidadadministrativa.codestpro4 AND
						sep_solicitud.codestpro5=spg_dt_unidadadministrativa.codestpro5 AND
						sep_solicitud.estcla=spg_dt_unidadadministrativa.estcla ".
					"   ".$ls_criteusu." ".
					"   sep_solicitud.codemp=sss_usuarios.codemp ".
					"   ".$ls_criterio." ".
					" GROUP BY sep_solicitud.codemp, sep_solicitud.numsol, sep_solicitud.codtipsol, sep_solicitud.coduniadm, 
					        codfuefin, fecregsol,".
					"          estsol, estapro, consol, monto, monbasinm, montotcar, tipo_destino, sep_solicitud.cod_pro,".
					"          sep_solicitud.ced_bene, spg_unidadadministrativa.denuniadm ,
					           sep_solicitud.codestpro1,sep_solicitud.codestpro2,sep_solicitud.codestpro3,
							   sep_solicitud.codestpro4,sep_solicitud.codestpro5,sep_solicitud.codaprusu ".
					" ORDER BY sep_solicitud.numsol";
					
			if(($ai_registrada==1)or($ai_emitida==1)or($ai_contabilizada==1)or($ai_procesada==1)or($ai_anulada==1)or($ai_despachada==1)or($ai_aprobada==1)or($ai_pagada==1))
			{//print "entro1";
				if ($ls_sql2!="")
				{
					$ls_sql=$ls_sql2." UNION ".$ls_sql;
				}
			} 	
			elseif(($ai_registrada==1)or($ai_emitida==1)or($ai_contabilizada==1)or($ai_procesada==1)or($ai_anulada==1)or($ai_despachada==1)or($ai_aprobada==1)or($ai_pagada==0))
			{
				$ls_sql=$ls_sql;
			}
			
			if(($ai_registrada==0)&&($ai_emitida==0)&&($ai_contabilizada==0)&&($ai_procesada==0)&&($ai_anulada==0)&&($ai_despachada==0)&&($ai_aprobada==0)&&($ai_pagada==1))
			{//print "entro2";
				$ls_sql="";
				$ls_sql=$ls_sql2;
			}
		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->reportes_ajax MÉTODO->uf_load_solicitudes ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			return $rs_data;
		}		
	}// end function uf_load_solicitudes
	//-----------------------------------------------------------------------------------------------------------------------------------
	
?>