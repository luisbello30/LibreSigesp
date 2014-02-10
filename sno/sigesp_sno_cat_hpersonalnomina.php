<?php
	session_start();
	if(!array_key_exists("la_logusr",$_SESSION))
	{
		print "<script language=JavaScript>";
		print "close();";
		print "opener.document.form1.submit();";
		print "</script>";		
	}

   //--------------------------------------------------------------
   function uf_print($as_codper, $as_cedper, $as_nomper, $as_apeper, $as_tipo, $ai_subnomina)
   {
		//////////////////////////////////////////////////////////////////////////////
		//	Function:  uf_print
		//	Arguments:    as_codper  // C�digo de Personal
		//				  as_cedper  // C�dula de Pesonal
		//				  as_nomper  // Nombre de Personal
		//				  as_apeper // Apellido de Personal
		//				  as_tipo  // Tipo de Llamada del cat�logo
		//				  ai_subnomina  // si tiene sub n�mina=1 � N� =0
		//	Description:  Funci�n que obtiene e imprime los resultados de la busqueda
		//////////////////////////////////////////////////////////////////////////////
		global $io_fun_nomina;
		require_once("../shared/class_folder/sigesp_include.php");
		$io_include=new sigesp_include();
		$io_conexion=$io_include->uf_conectar();
		require_once("../shared/class_folder/class_sql.php");
		$io_sql=new class_sql($io_conexion);	
		require_once("../shared/class_folder/class_mensajes.php");
		$io_mensajes=new class_mensajes();		
		require_once("../shared/class_folder/class_funciones.php");
		$io_funciones=new class_funciones();		
                 $ls_codemp=$_SESSION["la_empresa"]["codemp"];
                $ls_codnom=$_SESSION["la_nomina"]["codnom"];
                $ls_codperi=$_SESSION["la_nomina"]["peractnom"];
                $ls_anocur=$_SESSION["la_nomina"]["anocurnom"];
		$li_tipnom=$_SESSION["la_nomina"]["tipnom"];
		print "<table width=500 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
		print "<tr class=titulo-celda>";
		print "<td width=60>C&oacute;digo</td>";
		print "<td width=40>C&eacute;dula</td>";
		print "<td width=340>Nombre y Apellido</td>";
		print "<td width=60>Estatus</td>";
		print "</tr>";
		$ls_sql="SELECT sno_hpersonalnomina.codper, sno_hpersonalnomina.codsubnom, sno_hpersonalnomina.codasicar, sno_hpersonalnomina.codtab, ".
				"		sno_hpersonalnomina.codgra, sno_hpersonalnomina.codpas, sno_hpersonalnomina.sueper, sno_hpersonalnomina.horper, ".
				"		sno_hpersonalnomina.minorguniadm, sno_hpersonalnomina.ofiuniadm, sno_hpersonalnomina.uniuniadm, ".
				"		sno_hpersonalnomina.depuniadm, sno_hpersonalnomina.prouniadm, sno_hpersonalnomina.codcar, sno_hpersonalnomina.fecingper, ".
				"		sno_hpersonalnomina.fecegrper, sno_hpersonalnomina.fecculcontr, sno_hpersonalnomina.sueintper, sno_hpersonalnomina.sueproper, ".
				"		sno_hpersonalnomina.codded, sno_hpersonalnomina.codtipper, sno_hpersonalnomina.quivacper, sno_hpersonalnomina.codtabvac, ".
				"		sno_hpersonalnomina.pagefeper, sno_hpersonalnomina.pagbanper, sno_hpersonalnomina.codban, sno_hpersonalnomina.codcueban, ".
				"		sno_hpersonalnomina.tipcuebanper, sno_hpersonalnomina.cueaboper, sno_hpersonalnomina.codage, sno_hpersonalnomina.fecsusper, ".
				"		sno_hpersonalnomina.staper, sno_hpersonalnomina.cauegrper, sno_hpersonalnomina.codescdoc, sno_hpersonalnomina.codcladoc, ".
				"		sno_hpersonalnomina.codubifis, sno_hpersonalnomina.tipcestic, sno_hpersonalnomina.conjub, sno_hpersonalnomina.catjub, ".
				"		sno_hpersonalnomina.codclavia, sno_personal.cedper, sno_personal.nomper, sno_personal.apeper, sno_hpersonalnomina.grado, sno_hpersonalnomina.descasicar, ".
				"		sno_hunidadadmin.desuniadm, sno_dedicacion.desded, sno_tipopersonal.destipper, sno_hsubnomina.dessubnom, sno_hpersonalnomina.obsrecper,".
				"		sno_tablavacacion.dentabvac, sno_escaladocente.desescdoc, sno_clasificaciondocente.descladoc, sno_ubicacionfisica.desubifis, ".
				"		sno_hpersonalnomina.codunirac, sno_hpersonalnomina.pagtaqper, sno_hpersonalnomina.fecascper, sno_hpersonalpension.suebasper, ".
				"		sno_hpersonalpension.priespper, sno_hpersonalpension.pritraper, sno_hpersonalpension.priproper, sno_hpersonalpension.prianoserper, ".
				"		sno_hpersonalpension.pridesper, sno_hpersonalpension.porpenper, sno_hpersonalpension.prinoascper, sno_hpersonalpension.monpenper, sno_hpersonalnomina.coddep, ".
				"		sno_hpersonalpension.tipjub, sno_hpersonalpension.fecvid, sno_hpersonalpension.prirem, sno_hpersonalpension.segrem, sno_hpersonalnomina.salnorper, sno_hpersonalnomina.estencper,  ".
				"       (SELECT srh_departamento.coddep FROM srh_departamento                   ".
				"         WHERE srh_departamento.codemp=sno_hpersonalnomina.codemp             ".
				"           AND srh_departamento.coddep=sno_hpersonalnomina.coddep) AS dendep, ".
				"		sno_hpersonalpension.subtotper, ".
				"		(SELECT descar FROM sno_hcargo ".
				"		   WHERE sno_hcargo.codemp = sno_hpersonalnomina.codemp ".
				"			 AND sno_hcargo.codnom = sno_hpersonalnomina.codnom ".
				"			 AND sno_hcargo.codcar = sno_hpersonalnomina.codcar AND sno_hcargo.codperi='".$ls_codperi."') as descar, ".
				"		(SELECT denasicar FROM sno_hasignacioncargo ".
				"		   WHERE sno_hasignacioncargo.codemp = sno_hpersonalnomina.codemp ".
				"			 AND sno_hasignacioncargo.codnom = sno_hpersonalnomina.codnom ".
				"			 AND sno_hasignacioncargo.codasicar = sno_hpersonalnomina.codasicar AND
                                                        sno_hasignacioncargo.codperi='".$ls_codperi."') as denasicar, ".
				"		(SELECT destab FROM sno_htabulador ".
				"		   WHERE sno_htabulador.codemp = sno_hpersonalnomina.codemp ".
				"			 AND sno_htabulador.codnom = sno_hpersonalnomina.codnom ".
				"			 AND sno_htabulador.codtab = sno_hpersonalnomina.codtab AND
                                                        sno_htabulador.codperi='".$ls_codperi."') as destab, ".
				"		(SELECT moncomgra FROM sno_hgrado ".
				"		  WHERE sno_hgrado.codemp = sno_hpersonalnomina.codemp ".
				"		    AND sno_hgrado.codnom = sno_hpersonalnomina.codnom ".
				"		    AND sno_hgrado.codtab = sno_hpersonalnomina.codtab ".
				"		    AND sno_hgrado.codpas = sno_hpersonalnomina.codpas ".
				"		    AND sno_hgrado.codgra = sno_hpersonalnomina.codgra AND
                                                    sno_hgrado.codperi='".$ls_codperi."') as compensacion, ".
				"		(SELECT denominacion FROM scg_cuentas ".
				"		   WHERE scg_cuentas.codemp = sno_hpersonalnomina.codemp ".
				"			 AND scg_cuentas.SC_cuenta = sno_hpersonalnomina.cueaboper ".
				"			 AND scg_cuentas.status = 'C') as dencueaboper, ".
				"		(SELECT nomban FROM scb_banco ".
				"		  WHERE scb_banco.codemp = sno_hpersonalnomina.codemp ".
				"			AND scb_banco.codban = sno_hpersonalnomina.codban) as nomban, ".
				"		(SELECT nomage FROM scb_agencias ".
				"		  WHERE scb_agencias.codemp = sno_hpersonalnomina.codemp ".
				"			AND scb_agencias.codban = sno_hpersonalnomina.codban ".
				"			AND scb_agencias.codage = sno_hpersonalnomina.codage) as nomage, ".
				"		(SELECT dencat FROM scv_categorias ".
				"		  WHERE scv_categorias.codemp = sno_hpersonalnomina.codemp ".
				"			AND scv_categorias.codcat = sno_hpersonalnomina.codclavia) as dencat ".
				"  FROM sno_hpersonalnomina  ".
				"  LEFT JOIN sno_hpersonalpension ".
				"	      ON sno_hpersonalnomina.codemp = sno_hpersonalpension.codemp ".
				"        AND sno_hpersonalnomina.codnom = sno_hpersonalpension.codnom ".
				"        AND sno_hpersonalnomina.anocur = sno_hpersonalpension.anocur ".
				"        AND sno_hpersonalnomina.codperi = sno_hpersonalpension.codperi ".
				"        AND sno_hpersonalnomina.codper = sno_hpersonalpension.codper,  ".
				"		sno_personal, sno_hsubnomina, sno_hunidadadmin, sno_dedicacion, sno_tipopersonal, ".
				"  		sno_tablavacacion, sno_escaladocente, sno_clasificaciondocente, sno_ubicacionfisica ".
				" WHERE sno_hpersonalnomina.codemp = '".$ls_codemp."'".
				"   AND sno_hpersonalnomina.codnom = '".$ls_codnom."' ".
				"   AND sno_hpersonalnomina.anocur = '".$ls_anocur."' ".
				"   AND sno_hpersonalnomina.codperi = '".$ls_codperi."' ".
				"   AND sno_personal.codper like '".$as_codper."' ".
				"   AND sno_personal.cedper like '".$as_cedper."'".
				"   AND sno_personal.nomper like '".$as_nomper."' ".
				"   AND sno_personal.apeper like '".$as_apeper."'".
				"   AND sno_hpersonalnomina.codemp = sno_personal.codemp ".
				"   AND sno_hpersonalnomina.codper = sno_personal.codper ".
				"   AND sno_hpersonalnomina.codemp = sno_hsubnomina.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsubnomina.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hsubnomina.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hsubnomina.codperi ".
				"	AND sno_hpersonalnomina.codsubnom = sno_hsubnomina.codsubnom ".
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				"   AND sno_hpersonalnomina.codemp = sno_dedicacion.codemp ".
				"   AND sno_hpersonalnomina.codded = sno_dedicacion.codded ".
				"   AND sno_hpersonalnomina.codemp = sno_tipopersonal.codemp ".
				"   AND sno_hpersonalnomina.codded = sno_tipopersonal.codded ".
				"   AND sno_hpersonalnomina.codtipper = sno_tipopersonal.codtipper ".
				"   AND sno_hpersonalnomina.codemp = sno_tablavacacion.codemp ".
				"	AND sno_hpersonalnomina.codtabvac = sno_tablavacacion.codtabvac ".
				"   AND sno_hpersonalnomina.codemp = sno_escaladocente.codemp ".
				"	AND sno_hpersonalnomina.codescdoc = sno_escaladocente.codescdoc ".
				"   AND sno_hpersonalnomina.codemp = sno_clasificaciondocente.codemp ".
				"	AND sno_hpersonalnomina.codescdoc = sno_clasificaciondocente.codescdoc ".
				"	AND sno_hpersonalnomina.codcladoc = sno_clasificaciondocente.codcladoc ".
				"   AND sno_hpersonalnomina.codemp = sno_ubicacionfisica.codemp ".
				"	AND sno_hpersonalnomina.codubifis = sno_ubicacionfisica.codubifis ".
				" ORDER BY sno_hpersonalnomina.codper ";
                
			
		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{
		print $io_sql->message;
        	$io_mensajes->message("ERROR->".$io_funciones->uf_convertirmsg($io_sql->message)); 
		}
		else
		{
			while($row=$io_sql->fetch_row($rs_data))
			{
				$ls_codper=$row["codper"];
				$ls_cedper=$row["cedper"];
				$ls_nomper=$row["nomper"]." ".$row["apeper"];
				$ls_estper=$row["staper"];
				$ls_codsubnom=$row["codsubnom"];
				$ls_dessubnom=$row["dessubnom"];
				$ls_codasicar=$row["codasicar"];
				$ls_denasicar=$row["denasicar"];
				$ls_codcar=$row["codcar"];
				$ls_descar=$row["descar"];
				$ls_codtab=$row["codtab"];
				$ls_destab=$row["destab"];
				$ls_codgra=$row["codgra"];
				$ls_codpas=$row["codpas"];
				$li_sueper=$row["sueper"];			
				$li_sueper=$io_fun_nomina->uf_formatonumerico($li_sueper);
				$li_compensacion=$row["compensacion"];			
				$li_compensacion=$io_fun_nomina->uf_formatonumerico($li_compensacion);
				$li_horper=$row["horper"];			
				$li_horper=$io_fun_nomina->uf_formatonumerico($li_horper);
				$li_sueintper=$row["sueintper"];			
				$li_sueintper=$io_fun_nomina->uf_formatonumerico($li_sueintper);
				$li_sueproper=$row["sueproper"];			
				$li_sueproper=$io_fun_nomina->uf_formatonumerico($li_sueproper);
				$ld_fecingper=$io_funciones->uf_convertirfecmostrar($row["fecingper"]);				
				$ld_fecculcontr=$io_funciones->uf_convertirfecmostrar($row["fecculcontr"]);				
				$ld_fecascper=$io_funciones->uf_convertirfecmostrar($row["fecascper"]);		
				$ls_coduniadm=$row["minorguniadm"]."-".$row["ofiuniadm"]."-".$row["uniuniadm"]."-".$row["depuniadm"]."-".$row["prouniadm"];			
				$ls_desuniadm=$row["desuniadm"];
				$ls_codded=$row["codded"];
				$ls_desded=$row["desded"];
				$ls_codtipper=$row["codtipper"];
				$ls_destipper=$row["destipper"];
				$ls_codtabvac=$row["codtabvac"];
				$ls_dentabvac=$row["dentabvac"];
				$li_pagefeper=$row["pagefeper"];
				$li_pagbanper=$row["pagbanper"];
				$ls_codban=$row["codban"];
				$ls_codage=$row["codage"];
				$ls_codcueban=$row["codcueban"];
				$ls_tipcuebanper=$row["tipcuebanper"];
				$ls_tipcestic=$row["tipcestic"];
				$ls_codescdoc=$row["codescdoc"];
				$ls_desescdoc=$row["desescdoc"];
				$ls_codcladoc=$row["codcladoc"];
				$ls_descladoc=$row["descladoc"];
				$ls_codubifis=$row["codubifis"];
				$ls_desubifis=$row["desubifis"];
				$ls_cueaboper=$row["cueaboper"];
				$ls_dencueaboper=$row["dencueaboper"];
				$ls_nomban=$row["nomban"];
				$ls_nomage=$row["nomage"];
				$ls_conjub=$row["conjub"];
				$ls_catjub=$row["catjub"];
				$ls_dencat=$row["dencat"];
				$ls_codclavia=$row["codclavia"];
				$li_pagtaqper=$row["pagtaqper"];
				$ls_codunirac=$row["codunirac"];
				$ls_grado=$row["grado"];
				$ls_descasicar=$row["descasicar"];
				$ls_obsrecper=$row["obsrecper"];
				$li_suebasper=$io_fun_nomina->uf_formatonumerico($row["suebasper"]);
				$li_priespper=$io_fun_nomina->uf_formatonumerico($row["priespper"]);
				$li_pritraper=$io_fun_nomina->uf_formatonumerico($row["pritraper"]);
				$li_priproper=$io_fun_nomina->uf_formatonumerico($row["priproper"]);
				$li_prianoserper=$io_fun_nomina->uf_formatonumerico($row["prianoserper"]);
				$li_pridesper=$io_fun_nomina->uf_formatonumerico($row["pridesper"]);
				$li_porpenper=$io_fun_nomina->uf_formatonumerico($row["porpenper"]);
				$li_prinoascper=$io_fun_nomina->uf_formatonumerico($row["prinoascper"]);
				$li_monpenper=$io_fun_nomina->uf_formatonumerico($row["monpenper"]);
				$li_subtotper=$io_fun_nomina->uf_formatonumerico($row["subtotper"]);
				$ls_coddep=$row["coddep"];
				$ls_dendep=$row["dendep"];
				$ls_tippen=$row["tipjub"];	
				$ls_fecvi=$io_funciones->uf_convertirfecmostrar($row["fecvid"]);
				$ls_prirem=$io_fun_nomina->uf_formatonumerico($row["prirem"]);	
				$ls_segrem=$io_fun_nomina->uf_formatonumerico($row["segrem"]);		
				$li_salnorper=$row["salnorper"];			
				$li_salnorper=$io_fun_nomina->uf_formatonumerico($li_salnorper);
				$ls_estencper=$ls_estencper;
				$ld_fecegrper=$io_funciones->uf_convertirfecmostrar($row["fecegrper"]);
				$ld_fecsusper=$io_funciones->uf_convertirfecmostrar($row["fecsusper"]);
				$ls_obsegrper=trim($row["cauegrper"]);
				switch ($ls_estper)
				{
					case "0":
						$ls_estper="N/A";
						break;
					
					case "1":
						$ls_estper="Activo";
						break;
					
					case "2":
						$ls_estper="Vacaciones";
						break;
						
					case "3":
						$ls_estper="Egresado";
						break;
	
					case "4":
						$ls_estper="Suspendido";
						break;
				}
	
				switch ($as_tipo)
				{
					case "": // el llamado se hace desde sigesp_sno_d_hpersonalnomina.php
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptar('$ls_codper','$ls_nomper','$ls_estper','$ls_codasicar','$ls_denasicar',";
						print "'$ls_codcar','$ls_descar','$ls_codtab','$ls_destab','$ls_codgra','$ls_codpas',";
						print "'$li_sueper','$li_horper','$li_sueintper','$li_sueproper','$ld_fecingper','$ld_fecculcontr','$ls_coduniadm',";
						print "'$ls_desuniadm','$ls_codded','$ls_desded','$ls_codtipper','$ls_destipper','$ls_codtabvac','$ls_dentabvac',";
						print "'$li_pagefeper','$li_pagbanper','$ls_codsubnom','$ls_dessubnom','$ls_codban','$ls_codage','$ls_codcueban',";
						print "'$ls_tipcuebanper','$ls_tipcestic','$ls_codescdoc','$ls_codcladoc','$ls_codubifis','$ls_cueaboper',";
						print "'$ls_dencueaboper','$ls_nomban','$ls_nomage','$ls_desescdoc','$ls_descladoc','$ls_desubifis','$ai_subnomina',";
						print "'$ls_conjub','$ls_catjub','$ls_codclavia','$ls_dencat','$ls_codunirac','$li_pagtaqper','$li_compensacion',";
						print "'$ld_fecascper','$ls_grado','$li_suebasper','$li_priespper','$li_pritraper','$li_priproper','$li_prianoserper',";
						print "'$li_pridesper','$li_porpenper','$li_prinoascper','$li_monpenper','$li_subtotper','$ls_descasicar','$ls_coddep',";
						print "'$ls_dendep','$li_tipnom','$ls_tippen','$ls_fecvi','$ls_prirem','$ls_segrem','$li_salnorper','$ls_estencper',";
						print "'$ld_fecegrper','$ld_fecsusper','$ls_obsegrper','$ls_obsrecper');\">".$ls_codper."</a></td>";
						print "<td>".$ls_cedper."</td>";
						print "<td>".$ls_nomper."</td>";
						print "<td>".$ls_estper."</td>";
						print "</tr>";			
						break;						

					case "repprenomdes": // el llamado se hace desde sigesp_sno_r_hprenomina.php
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptarrepprenomdes('$ls_codper');\">".$ls_codper."</a></td>";
						print "<td>".$ls_cedper."</td>";
						print "<td>".$ls_nomper."</td>";
						print "<td>".$ls_estper."</td>";
						print "</tr>";			
						break;
	
					case "repprenomhas": // el llamado se hace desde sigesp_sno_r_hprenomina.php
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptarrepprenomhas('$ls_codper');\">".$ls_codper."</a></td>";
						print "<td>".$ls_cedper."</td>";
						print "<td>".$ls_nomper."</td>";
						print "<td>".$ls_estper."</td>";
						print "</tr>";			
						break;

					case "reppagnomdes": // el llamado se hace desde sigesp_sno_r_hpagonomina.php
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptarreppagnomdes('$ls_codper');\">".$ls_codper."</a></td>";
						print "<td>".$ls_cedper."</td>";
						print "<td>".$ls_nomper."</td>";
						print "<td>".$ls_estper."</td>";
						print "</tr>";			
						break;
	
					case "reppagnomhas": // el llamado se hace desde sigesp_sno_r_hpagonomina.php
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptarreppagnomhas('$ls_codper');\">".$ls_codper."</a></td>";
						print "<td>".$ls_cedper."</td>";
						print "<td>".$ls_nomper."</td>";
						print "<td>".$ls_estper."</td>";
						print "</tr>";			
						break;	
	
					case "reprecpagdes": // el llamado se hace desde sigesp_sno_r_hrecibopago.php
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptarreprecpagdes('$ls_codper');\">".$ls_codper."</a></td>";
						print "<td>".$ls_cedper."</td>";
						print "<td>".$ls_nomper."</td>";
						print "<td>".$ls_estper."</td>";
						print "</tr>";			
						break;
	
					case "reprecpaghas": // el llamado se hace desde sigesp_sno_r_hrecibopago.php
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptarreprecpaghas('$ls_codper');\">".$ls_codper."</a></td>";
						print "<td>".$ls_cedper."</td>";
						print "<td>".$ls_nomper."</td>";
						print "<td>".$ls_estper."</td>";
						print "</tr>";			
						break;
	
					case "modpersonalhistorico": // el llamado se hace desde sigesp_sno_p_hmodificarpersonalnomina.php
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptarmodpersonalhistorico('$ls_codper','$ls_nomper','$ls_estper','$ls_codasicar','$ls_denasicar',";
						print "'$ls_codcar','$ls_descar','$ls_codtab','$ls_destab','$ls_codgra','$ls_codpas',";
						print "'$li_sueper','$li_horper','$li_sueintper','$li_sueproper','$ld_fecingper','$ld_fecculcontr','$ls_coduniadm',";
						print "'$ls_desuniadm','$ls_codded','$ls_desded','$ls_codtipper','$ls_destipper','$ls_codtabvac','$ls_dentabvac',";
						print "'$li_pagefeper','$li_pagbanper','$ls_codsubnom','$ls_dessubnom','$ls_codban','$ls_codage','$ls_codcueban',";
						print "'$ls_tipcuebanper','$ls_tipcestic','$ls_codescdoc','$ls_codcladoc','$ls_codubifis','$ls_cueaboper',";
						print "'$ls_dencueaboper','$ls_nomban','$ls_nomage','$ls_desescdoc','$ls_descladoc','$ls_desubifis','$ai_subnomina',";
						print "'$ls_conjub','$ls_catjub');\">".$ls_codper."</a></td>";
						print "<td>".$ls_cedper."</td>";
						print "<td>".$ls_nomper."</td>";
						print "<td>".$ls_estper."</td>";
						print "</tr>";			
						break;
	
					case "reppredes": // el llamado se hace desde sigesp_sno_r_listadoprestamo.php
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptarreppredes('$ls_codper');\">".$ls_codper."</a></td>";
						print "<td>".$ls_cedper."</td>";
						print "<td>".$ls_nomper."</td>";
						print "<td>".$ls_estper."</td>";
						print "</tr>";			
						break;
	
					case "repprehas": // el llamado se hace desde sigesp_sno_r_listadoprestamo.php
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptarrepprehas('$ls_codper');\">".$ls_codper."</a></td>";
						print "<td>".$ls_cedper."</td>";
						print "<td>".$ls_nomper."</td>";
						print "<td>".$ls_estper."</td>";
						print "</tr>";			
						break;
	
					case "repdetpredes": // el llamado se hace desde sigesp_sno_r_listadoprestamo.php
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptarrepdetpredes('$ls_codper');\">".$ls_codper."</a></td>";
						print "<td>".$ls_cedper."</td>";
						print "<td>".$ls_nomper."</td>";
						print "<td>".$ls_estper."</td>";
						print "</tr>";			
						break;
	
					case "repdetprehas": // el llamado se hace desde sigesp_sno_r_listadoprestamo.php
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptarrepdetprehas('$ls_codper');\">".$ls_codper."</a></td>";
						print "<td>".$ls_cedper."</td>";
						print "<td>".$ls_nomper."</td>";
						print "<td>".$ls_estper."</td>";
						print "</tr>";			
						break;

					case "replisprodes": // el llamado se hace desde sigesp_sno_r_listadoproyectospersonal.php
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptarreplisprodes('$ls_codper');\">".$ls_codper."</a></td>";
						print "<td>".$ls_cedper."</td>";
						print "<td>".$ls_nomper."</td>";
						print "<td>".$ls_estper."</td>";
						print "</tr>";			
						break;
	
					case "replisprohas": // el llamado se hace desde sigesp_sno_r_listadoproyectospersonal.php
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptarreplisprohas('$ls_codper');\">".$ls_codper."</a></td>";
						print "<td>".$ls_cedper."</td>";
						print "<td>".$ls_nomper."</td>";
						print "<td>".$ls_estper."</td>";
						print "</tr>";			
						break;
				}
			}
			$io_sql->free_result($rs_data);
		}
		print "</table>";
		unset($io_include);
		unset($io_conexion);
		unset($io_sql);
		unset($io_mensajes);
		unset($io_funciones);
		unset($ls_codemp);
		unset($ls_codnom);
   }
   //--------------------------------------------------------------
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Cat&aacute;logo de Personal N&oacute;mina</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
<!--
a:link {
	color: #006699;
}
a:visited {
	color: #006699;
}
a:active {
	color: #006699;
}
-->
</style>
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
</head>

<body>
<form name="form1" method="post" action="">
  <p align="center">
    <input name="operacion" type="hidden" id="operacion">
</p>
  <table width="500" border="0" align="center" cellpadding="1" cellspacing="1">
    <tr>
      <td width="496" height="20" colspan="2" class="titulo-ventana">Cat&aacute;logo de Personal N&oacute;mina</td>
    </tr>
  </table>
<br>
    <table width="500" border="0" cellpadding="1" cellspacing="0" class="formato-blanco" align="center">
      <tr>
        <td width="67" height="22"><div align="right">C&oacute;digo</div></td>
        <td width="431"><div align="left">
            <input name="txtcodper" type="text" id="txtcodper" size="30" maxlength="10" onKeyPress="javascript: ue_mostrar(this,event);">
        </div></td>
      </tr>
      <tr>
        <td height="22"><div align="right">C&eacute;dula</div></td>
        <td><div align="left">
          <input name="txtcedper" type="text" id="txtcedper" size="30" maxlength="10" onKeyPress="javascript: ue_mostrar(this,event);">
        </div></td>
      </tr>
      <tr>
        <td height="22"><div align="right">Nombre</div></td>
        <td><div align="left">
          <input name="txtnomper" type="text" id="txtnomper" size="30" maxlength="60" onKeyPress="javascript: ue_mostrar(this,event);">
        </div></td>
      </tr>
      <tr>
        <td height="22"><div align="right">Apellido</div></td>
        <td><div align="left">
            <input name="txtapeper" type="text" id="txtapeper" size="30" maxlength="60" onKeyPress="javascript: ue_mostrar(this,event);">
        </div></td>
      </tr>
      <tr>
        <td height="22">&nbsp;</td>
        <td><div align="right"><a href="javascript: ue_search();"><img src="../shared/imagebank/tools20/buscar.gif" title='Buscar' alt="Buscar" width="20" height="20" border="0"> Buscar</a></div></td>
      </tr>
  </table>
  <br>
<?php
	require_once("class_folder/class_funciones_nomina.php");
	$io_fun_nomina=new class_funciones_nomina();
	$ls_operacion =$io_fun_nomina->uf_obteneroperacion();
	$ls_tipo=$io_fun_nomina->uf_obtenertipo();
	$li_subnomina=$io_fun_nomina->uf_obtenervalor_get("subnom","0");
	if($ls_operacion=="BUSCAR")
	{
		$ls_codper="%".$_POST["txtcodper"]."%";
		$ls_cedper="%".$_POST["txtcedper"]."%";
		$ls_nomper="%".$_POST["txtnomper"]."%";
		$ls_apeper="%".$_POST["txtapeper"]."%";

		uf_print($ls_codper, $ls_cedper, $ls_nomper, $ls_apeper, $ls_tipo, $li_subnomina);
	}
	unset($io_fun_nomina);
?>
</div>
</form>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script language="JavaScript">
function aceptar(codper,nomper,estper,codasicar,denasicar,codcar,descar,codtab,destab,codgra,codpas,
				 sueper,horper,sueintper,sueproper,fecingper,fecculcontr,coduniadm,desuniadm,codded,desded,codtipper,
				 destipper,codtabvac,dentabvac,pagefeper,pagbanper,codsubnom,dessubnom,codban,codage,codcueban,tipcuebanper,
				 tipcestic,codescdoc,codcladoc,codubifis,cueaboper,dencueaboper,nomban,nomage,desescdoc,descladoc,desubifis,
				 subnomina,conjub,catjub,codclavia,dencat,codunirac,pagtaqper,compensacion,fecascper,grado,suebasper,priespper,
				 pritraper,priproper,prianoserper,pridesper,porpenper,prinoascper,monpenper,subtotper,descasicar, coddep, 
				 dendep, tipnom, tippen, fecvid, prirem, segrem,salnorper,estencper,fecegrper,fecsusper,obsegrper,obsrecper)
{
	opener.document.form1.txtcodper.value="";
    opener.document.form1.txtnomper.value="";
    opener.document.form1.txtestper.value="";
	if(opener.document.form1.rac.value=="0")
	{
    	opener.document.form1.txtcodcar.value="";
    	opener.document.form1.txtdescar.value="";
	}
	else
	{
		
		if ((grado=="0000")&&(tipnom!="3")&&(tipnom!="4"))
		{
			opener.document.form1.txtcodtab.value=codtab;
			opener.document.form1.txtdestab.value=destab;
			opener.document.form1.txtcodgra.value=codgra;
			opener.document.form1.txtcodpas.value=codpas;
		}
		else
		{
			opener.document.form1.txtgrado.value=grado;
		}
	}
	opener.document.form1.txtsueper.value="";
	opener.document.form1.txtcompensacion.value="";
    opener.document.form1.txthorper.value="";
    opener.document.form1.txtsueintper.value="";
    opener.document.form1.txtsueproper.value="";
    opener.document.form1.txtfecingper.value="";
    opener.document.form1.txtfecculcontr.value="";
    opener.document.form1.txtcoduniadm.value="";
    opener.document.form1.txtdesuniadm.value="";
    opener.document.form1.txtcodded.value="";
    opener.document.form1.txtdesded.value="";
    opener.document.form1.txtcodtipper.value="";
    opener.document.form1.txtdestipper.value="";
    opener.document.form1.txtcodtabvac.value="";
    opener.document.form1.txtdentabvac.value="";
	if(subnomina==1)
	{
    	opener.document.form1.txtcodsubnom.value="";
    	opener.document.form1.txtdessubnom.value="";
	}
    opener.document.form1.txtcodban.value="";
    opener.document.form1.txtcodage.value="";
    opener.document.form1.txtcodcueban.value="";
    opener.document.form1.txtcodescdoc.value="";
    opener.document.form1.txtdesescdoc.value="";
    opener.document.form1.txtcodcladoc.value="";
    opener.document.form1.txtdescladoc.value="";
    opener.document.form1.txtcodubifis.value="";
    opener.document.form1.txtdesubifis.value="";
    opener.document.form1.cmbtipcuebanper.value="";
    opener.document.form1.cmbtipcestic.value="";
    opener.document.form1.txtcuecon.value="";
    opener.document.form1.txtdencuecon.value="";
    opener.document.form1.txtnomban.value="";
    opener.document.form1.txtnomage.value="";
	opener.document.form1.chkpagefeper.checked=false;
	opener.document.form1.chkpagbanper.checked=false;
	opener.document.form1.cmbconjub.value="0000";
	opener.document.form1.cmbcatjub.value="000";
	opener.document.form1.txtcodclavia.value="";
	opener.document.form1.txtfecascper.value="";
	opener.document.form1.txtdencat.value="";
	//opener.document.form1.txtgrado.value="";
	opener.document.form1.txtsuebasper.value="0,00";
	opener.document.form1.txtpriespper.value="0,00";
	opener.document.form1.txtpritraper.value="0,00";
	opener.document.form1.txtpriproper.value="0,00";
	opener.document.form1.txtprianoserper.value="0,00";
	opener.document.form1.txtpridesper.value="0,00";
	opener.document.form1.txtporpenper.value="0,00";
	opener.document.form1.txtprinoascper.value="0,00";
	opener.document.form1.txtmonpenper.value="0,00";
	opener.document.form1.txtsubtotper.value="0,00";
	opener.document.form1.txtcodper.value=codper;
	opener.document.form1.txtcodper.readOnly=true;
    opener.document.form1.txtnomper.value=nomper;
    opener.document.form1.txtestper.value=estper;
	if(opener.document.form1.rac.value=="0")
	{
    	opener.document.form1.txtcodcar.value=codcar;
    	opener.document.form1.txtdescar.value=descar;
	}
	else
	{
		opener.document.form1.txtcodasicar.value=codasicar;
		opener.document.form1.txtdenasicar.value=denasicar;
		
		if ((grado=="0000")&&(tipnom!="3")&&(tipnom!="4"))
		{
			opener.document.form1.txtcodtab.value=codtab;
			opener.document.form1.txtdestab.value=destab;
			opener.document.form1.txtcodgra.value=codgra;
			opener.document.form1.txtcodpas.value=codpas;
		}
		else
		{ 
		   opener.document.form1.txtgrado.value=grado;
		}
	}
    opener.document.form1.txtobsrecper.value=obsrecper;
	opener.document.form1.txtsueper.value=sueper;
	opener.document.form1.txtsalnorper.value=salnorper;	
	opener.document.form1.txtcompensacion.value=compensacion;
    opener.document.form1.txthorper.value=horper;
    opener.document.form1.txtsueintper.value=sueintper;
    opener.document.form1.txtsueproper.value=sueproper;
    opener.document.form1.txtfecingper.value=fecingper;
    opener.document.form1.txtfecculcontr.value=fecculcontr;
    opener.document.form1.txtcoduniadm.value=coduniadm;
    opener.document.form1.txtdesuniadm.value=desuniadm;
	opener.document.form1.txtfecegrper.value=fecegrper;
	opener.document.form1.txtfecsusper.value=fecsusper;
	opener.document.form1.txtobsegrper.value=obsegrper;
	opener.document.form1.txtcoddep.value=coddep;
    opener.document.form1.txtdendep.value=dendep;
    opener.document.form1.txtcodded.value=codded;
    opener.document.form1.txtdesded.value=desded;
    opener.document.form1.txtcodtipper.value=codtipper;
    opener.document.form1.txtdestipper.value=destipper;
    opener.document.form1.txtcodtabvac.value=codtabvac;
    opener.document.form1.txtdentabvac.value=dentabvac;
	if(subnomina==1)
	{
    	opener.document.form1.txtcodsubnom.value=codsubnom;
    	opener.document.form1.txtdessubnom.value=dessubnom;
	}
    opener.document.form1.txtcodban.value=codban;
    opener.document.form1.txtcodage.value=codage;
    opener.document.form1.txtcodcueban.value=codcueban;
    opener.document.form1.txtcodescdoc.value=codescdoc;
    opener.document.form1.txtdesescdoc.value=desescdoc;
    opener.document.form1.txtcodcladoc.value=codcladoc;
    opener.document.form1.txtdescladoc.value=descladoc;
    opener.document.form1.txtcodubifis.value=codubifis;
    opener.document.form1.txtdesubifis.value=desubifis;
    opener.document.form1.cmbtipcuebanper.value=tipcuebanper;
    opener.document.form1.cmbtipcestic.value=tipcestic;
    opener.document.form1.txtcuecon.value=cueaboper;
    opener.document.form1.txtdencuecon.value=dencueaboper;
    opener.document.form1.txtnomban.value=nomban;
    opener.document.form1.txtnomage.value=nomage;
	opener.document.form1.cmbconjub.value=conjub;
	opener.document.form1.cmbcatjub.value=catjub;
	opener.document.form1.txtcodclavia.value=codclavia;
	opener.document.form1.txtcodclavia.readOnly=true;
	opener.document.form1.txtdencat.value=dencat;
	opener.document.form1.txtdencat.readOnly=true;
	opener.document.form1.txtfecascper.value=fecascper;
	opener.document.form1.txtsuebasper.value=suebasper;
	opener.document.form1.txtpriespper.value=priespper;
	opener.document.form1.txtpritraper.value=pritraper;
	opener.document.form1.txtpriproper.value=priproper;
	opener.document.form1.txtprianoserper.value=prianoserper;
	opener.document.form1.txtpridesper.value=pridesper;
	opener.document.form1.txtporpenper.value=porpenper;
	opener.document.form1.txtprinoascper.value=prinoascper;
	opener.document.form1.txtmonpenper.value=monpenper;
	opener.document.form1.txtsubtotper.value=subtotper;
	
	opener.document.form1.txtprimrem.value=prirem;
	opener.document.form1.txtsegrem.value=segrem;
	opener.document.form1.txtfecvid.value=fecvid;
	opener.document.form1.cmbtippen.value=tippen;
	if((opener.document.form1.rac.value=="1")&&(opener.document.form1.codunirac.value=="1"))
	{
	    opener.document.form1.txtcodunirac.value=codunirac;
	}
	if(pagefeper=="1")
	{
		opener.document.form1.chkpagefeper.checked=true;
	}
	if(pagbanper=="1")
	{
		opener.document.form1.chkpagbanper.checked=true;
		opener.document.form1.cmbtipcuebanper.disabled=false;
		opener.document.form1.txtcodcueban.readOnly=false;
	}
	if(pagtaqper=="1")
	{
		opener.document.form1.chkpagtaqper.checked=true;
	}
	if(estencper=="1")
	{
		opener.document.form1.txtestencper.value="EN ENCARGADURIA";
	}
	else
	{
		opener.document.form1.txtestencper.value="";
	}
	close();
}

function aceptarrepprenomdes(codper)
{
	opener.document.form1.txtcodperdes.value=codper;
	opener.document.form1.txtcodperdes.readOnly=true;
	opener.document.form1.txtcodperhas.value=codper;
	close();
}

function aceptarrepprenomhas(codper)
{
	if(opener.document.form1.txtcodperdes.value<=codper)
	{
		opener.document.form1.txtcodperhas.value=codper;
		opener.document.form1.txtcodperhas.readOnly=true;
		close();
	}
	else
	{
		alert("Rango del Personal inv�lido");
	}
}

function aceptarreppagnomdes(codper)
{
	opener.document.form1.txtcodperdes.value=codper;
	opener.document.form1.txtcodperdes.readOnly=true;
	opener.document.form1.txtcodperhas.value=codper;
	close();
}

function aceptarreppagnomhas(codper)
{
	if(opener.document.form1.txtcodperdes.value<=codper)
	{
		opener.document.form1.txtcodperhas.value=codper;
		opener.document.form1.txtcodperhas.readOnly=true;
		close();
	}
	else
	{
		alert("Rango del Personal inv�lido");
	}
}

function aceptarreprecpagdes(codper)
{
	opener.document.form1.txtcodperdes.value=codper;
	opener.document.form1.txtcodperdes.readOnly=true;
	opener.document.form1.txtcodperhas.value=codper;
	close();
}

function aceptarreprecpaghas(codper)
{
	if(opener.document.form1.txtcodperdes.value<=codper)
	{
		opener.document.form1.txtcodperhas.value=codper;
		opener.document.form1.txtcodperhas.readOnly=true;
		close();
	}
	else
	{
		alert("Rango del Personal inv�lido");
	}
}

function aceptarmodpersonalhistorico(codper,nomper,estper,codasicar,denasicar,codcar,descar,codtab,destab,codgra,codpas,
									 sueper,horper,sueintper,sueproper,fecingper,fecculcontr,coduniadm,desuniadm,codded,desded,codtipper,
									 destipper,codtabvac,dentabvac,pagefeper,pagbanper,codsubnom,dessubnom,codban,codage,codcueban,tipcuebanper,
									 tipcestic,codescdoc,codcladoc,codubifis,cueaboper,dencueaboper,nomban,nomage,desescdoc,descladoc,desubifis,
									 subnomina,conjub,catjub)
{
	opener.document.form1.txtcodper.value="";
    opener.document.form1.txtnomper.value="";
    opener.document.form1.txtestper.value="";
    opener.document.form1.txtcodban.value="";
    opener.document.form1.txtcodage.value="";
    opener.document.form1.txtcodcueban.value="";
    opener.document.form1.cmbtipcuebanper.value="";
    opener.document.form1.txtcuecon.value="";
    opener.document.form1.txtdencuecon.value="";
    opener.document.form1.txtnomban.value="";
    opener.document.form1.txtnomage.value="";
	opener.document.form1.chkpagefeper.checked=false;
	opener.document.form1.chkpagbanper.checked=false;
	opener.document.form1.txtcodper.value=codper;
	opener.document.form1.txtcodper.readOnly=true;
    opener.document.form1.txtnomper.value=nomper;
    opener.document.form1.txtestper.value=estper;
    opener.document.form1.txtcodban.value=codban;
    opener.document.form1.txtcodage.value=codage;
    opener.document.form1.txtcodcueban.value=codcueban;
    opener.document.form1.cmbtipcuebanper.value=tipcuebanper;
    opener.document.form1.txtcuecon.value=cueaboper;
    opener.document.form1.txtdencuecon.value=dencueaboper;
    opener.document.form1.txtnomban.value=nomban;
    opener.document.form1.txtnomage.value=nomage;
	if(pagefeper=="1")
	{
		opener.document.form1.chkpagefeper.checked=true;
		opener.document.images["cuentaabono"].style.visibility="visible";
		opener.document.images["banco"].style.visibility="hidden";
		opener.document.images["agencia"].style.visibility="hidden";
	}
	if(pagbanper=="1")
	{
		opener.document.form1.chkpagbanper.checked=true;
		opener.document.form1.cmbtipcuebanper.disabled=false;
		opener.document.form1.txtcodcueban.readOnly=false;
		opener.document.images["banco"].style.visibility="visible";
		opener.document.images["agencia"].style.visibility="visible";
		opener.document.images["cuentaabono"].style.visibility="hidden";
	}
	close();
}

function aceptarreppredes(codper)
{
	opener.document.form1.txtcodperdes.value=codper;
	opener.document.form1.txtcodperdes.readOnly=true;
	opener.document.form1.txtcodperhas.value=codper;
	close();
}

function aceptarrepprehas(codper)
{
	if(opener.document.form1.txtcodperdes.value<=codper)
	{
		opener.document.form1.txtcodperhas.value=codper;
		opener.document.form1.txtcodperhas.readOnly=true;
		close();
	}
	else
	{
		alert("Rango del Personal inv�lido");
	}
}

function aceptarrepdetpredes(codper)
{
	opener.document.form1.txtcodperdes.value=codper;
	opener.document.form1.txtcodperdes.readOnly=true;
	opener.document.form1.txtcodperhas.value=codper;
	close();
}

function aceptarrepdetprehas(codper)
{
	if(opener.document.form1.txtcodperdes.value<=codper)
	{
		opener.document.form1.txtcodperhas.value=codper;
		opener.document.form1.txtcodperhas.readOnly=true;
		close();
	}
	else
	{
		alert("Rango del Personal inv�lido");
	}
}

function aceptarreplisprodes(codper)
{
	opener.document.form1.txtcodperdes.value=codper;
	opener.document.form1.txtcodperdes.readOnly=true;
	opener.document.form1.txtcodperhas.value=codper;
	close();
}

function aceptarreplisprohas(codper)
{
	if(opener.document.form1.txtcodperdes.value<=codper)
	{
		opener.document.form1.txtcodperhas.value=codper;
		opener.document.form1.txtcodperhas.readOnly=true;
		close();
	}
	else
	{
		alert("Rango del Personal inv�lido");
	}
}

function ue_mostrar(myfield,e)
{
	var keycode;
	if (window.event) keycode = window.event.keyCode;
	else if (e) keycode = e.which;
	else return true;
	if (keycode == 13)
	{
		ue_search();
		return false;
	}
	else
		return true
}

function ue_search()
{
	f=document.form1;
  	f.operacion.value="BUSCAR";
  	f.action="sigesp_sno_cat_hpersonalnomina.php?tipo=<?PHP print $ls_tipo;?>&subnom=<?PHP print $li_subnomina;?>";
  	f.submit();
}
</script>
</html>
