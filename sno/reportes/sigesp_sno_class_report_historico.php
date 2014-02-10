<?php
class sigesp_sno_class_report_historico
{
	//-----------------------------------------------------------------------------------------------------------------------------------
	function sigesp_sno_class_report_historico()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: sigesp_sno_class_report_historico
		//		   Access: public 
		//	  Description: Constructor de la Clase
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 02/02/2006 								Fecha �ltima Modificaci�n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		require_once("../../shared/class_folder/sigesp_include.php");
		$io_include=new sigesp_include();
		$this->io_conexion=$io_include->uf_conectar();
		require_once("../../shared/class_folder/class_sql.php");
		$this->io_sql=new class_sql($this->io_conexion);	
		$this->DS=new class_datastore();
		$this->DS_detalle=new class_datastore();
		$this->DS_detalle2=new class_datastore();
		$this->DS_asigna=new class_datastore();
		$this->DS_pension=new class_datastore();
		$this->DS_pension2=new class_datastore();	
		require_once("../../shared/class_folder/class_mensajes.php");
		$this->io_mensajes=new class_mensajes();		
		require_once("../../shared/class_folder/class_funciones.php");
		$this->io_funciones=new class_funciones();		
                $this->ls_codemp=$_SESSION["la_empresa"]["codemp"];
                $this->ls_codnom=$_SESSION["la_nomina"]["codnom"];
                $this->ls_peractnom=$_SESSION["la_nomina"]["peractnom"];
                $this->ls_anocurnom=$_SESSION["la_nomina"]["anocurnom"];
		$this->li_rac=$_SESSION["la_nomina"]["racnom"];
		$this->rs_data="";
		$this->rs_data_detalle="";
		$this->rs_data_detalle2="";
	}// end function sigesp_sno_class_report_historico
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_config($as_sistema, $as_seccion, $as_variable, $as_valor, $as_tipo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_config
		//		   Access: public
		//	    Arguments: as_sistema  // Sistema al que pertenece la variable
		//				   as_seccion  // Secci�n a la que pertenece la variable
		//				   as_variable  // Variable nombre de la variable a buscar
		//				   as_valor  // valor por defecto que debe tener la variable
		//				   as_tipo  // tipo de la variable
		//	      Returns: $ls_resultado variable buscado
		//	  Description: Funci�n que obtiene una variable de la tabla config
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 01/01/2006 								Fecha �ltima Modificaci�n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_valor="";
		$ls_sql="SELECT value ".
				"  FROM sigesp_config ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codsis='".$as_sistema."' ".
				"   AND seccion='".$as_seccion."' ".
				"   AND entry='".$as_variable."' ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report Contable M�TODO->uf_select_config ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		}
		else
		{
			$li_i=0;
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_valor=$row["value"];
				$li_i=$li_i+1;
			}
			if($li_i==0)
			{
				$lb_valido=$this->uf_insert_config($as_sistema, $as_seccion, $as_variable, $as_valor, $as_tipo);
				if ($lb_valido)
				{
					$ls_valor=$this->uf_select_config($as_sistema, $as_seccion, $as_variable, $as_valor, $as_tipo);
				}
			}
			$this->io_sql->free_result($rs_data);		
		}
		return rtrim($ls_valor);
	}// end function uf_select_config
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_config($as_sistema, $as_seccion, $as_variable, $as_valor, $as_tipo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_config
		//		   Access: public
		//	    Arguments: as_sistema  // Sistema al que pertenece la variable
		//				   as_seccion  // Secci�n a la que pertenece la variable
		//				   as_variable  // Variable a buscar
		//				   as_valor  // valor por defecto que debe tener la variable
		//				   as_tipo  // tipo de la variable
		//	      Returns: $lb_valido True si se ejecuto el insert � False si hubo error en el insert
		//	  Description: Funci�n que inserta la variable de configuraci�n
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 01/01/2006 								Fecha �ltima Modificaci�n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$this->io_sql->begin_transaction();		
		$ls_sql="DELETE ".
				"  FROM sigesp_config ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codsis='".$as_sistema."' ".
				"   AND seccion='".$as_seccion."' ".
				"   AND entry='".$as_variable."' ";		
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
 			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Report Contable M�TODO->uf_insert_config ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$this->io_sql->rollback();
		}
		else
		{
			switch ($as_tipo)
			{
				case "C"://Caracter
					$valor = $as_valor;
					break;

				case "D"://Double
					$as_valor=str_replace(".","",$as_valor);
					$as_valor=str_replace(",",".",$as_valor);
					$valor = $as_valor;
					break;

				case "B"://Boolean
					$valor = $as_valor;
					break;

				case "I"://Integer
					$valor = intval($as_valor);
					break;
			}
			$ls_sql="INSERT INTO sigesp_config(codemp, codsis, seccion, entry, value, type)VALUES ".
					"('".$this->ls_codemp."','".$as_sistema."','".$as_seccion."','".$as_variable."','".$valor."','".$as_tipo."')";
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$lb_valido=false;
				$this->io_mensajes->message("CLASE->Report Contable M�TODO->uf_insert_config ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
				$this->io_sql->rollback();
			}
			else
			{
				$this->io_sql->commit();
			}
		}
		return $lb_valido;
	}// end function uf_insert_config	
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_prenomina_personal($as_codperdes,$as_codperhas,$as_subnomdes,$as_subnomhas,$as_orden)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_prenomina_personal
		//         Access: public (desde la clase sigesp_sno_rpp_prenomina)  
		//	    Arguments: as_codperdes // C�digo del personal donde se empieza a filtrar
		//	  			   as_codperhas // C�digo del personal donde se termina de filtrar		  
		//	  			   as_orden // Orde a mostrar en el reporte		  
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: funci�n que busca la informaci�n del personal que se le calcul� la pren�mina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 26/04/2006 								Fecha �ltima Modificaci�n :  
		// SE MODIFICARON LAS TABLAS TH POR H
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		if(!empty($as_codperdes))
		{
			$ls_criterio= "AND sno_hpersonalnomina.codper>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codper<='".$as_codperhas."'";
		}
		if(!empty($as_subnomdes))
		{
			$ls_criterio= $ls_criterio."AND sno_hpersonalnomina.codsubnom>='".$as_subnomdes."'";
		}
		if(!empty($as_subnomhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codsubnom<='".$as_subnomhas."'";
		}
		switch($as_orden)
		{
			case "1": // Ordena por C�digo de personal
				$ls_orden="ORDER BY sno_personal.codper ";
				break;

			case "2": // Ordena por Apellido de personal
				$ls_orden="ORDER BY sno_personal.apeper ";
				break;

			case "3": // Ordena por Nombre de personal
				$ls_orden="ORDER BY sno_personal.nomper ";
				break;
		}
		$ls_sql="SELECT sno_personal.codper,sno_personal.nomper, sno_personal.apeper ".
				"  FROM sno_personal, sno_hpersonalnomina, sno_hprenomina, sno_hconcepto ".
				" WHERE sno_hprenomina.codemp='".$this->ls_codemp."' ".
				"   AND sno_hprenomina.codnom='".$this->ls_codnom."' ".
				"   AND sno_hprenomina.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hprenomina.codperi='".$this->ls_peractnom."' ".
				"   ".$ls_criterio." ".
				"   AND sno_hpersonalnomina.codemp = sno_hprenomina.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hprenomina.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hprenomina.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hprenomina.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hprenomina.codper ".
				"   AND sno_hpersonalnomina.codemp = sno_personal.codemp ".
				"   AND sno_hpersonalnomina.codper = sno_personal.codper ".
				"   AND sno_hprenomina.codemp = sno_hconcepto.codemp ".
				"   AND sno_hprenomina.codnom = sno_hconcepto.codnom ".
				"   AND sno_hprenomina.anocur = sno_hconcepto.anocur ".
				"   AND sno_hprenomina.codperi = sno_hconcepto.codperi ".
				"   AND sno_hprenomina.codconc = sno_hconcepto.codconc ".
				" GROUP BY sno_personal.codper,sno_personal.nomper, sno_personal.apeper ".
				"   ".$ls_orden;
		$this->rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_prenomina_personal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_prenomina_personal
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_prenomina_conceptopersonal($as_codper,$as_conceptocero,$as_conceptop2)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_prenomina_conceptopersonal
		//         Access: public (desde la clase sigesp_sno_rpp_prenomina)  
		//	    Arguments: as_codper // C�digo de Personal
		//	  			   as_conceptocero // criterio que me indica si se desea quitar los conceptos cuyo valor es cero
		//	  			   as_conceptop2 // criterio que me indica si se desea mostrar los conceptos de tipo aporte patronal
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: funci�n que busca la informaci�n de los conceptos asociados al personal que se le calcul� la pren�mina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 26/04/2006 								Fecha �ltima Modificaci�n :
		// SE MODIFICARON LAS TABLAS TH POR H
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		if(!empty($as_conceptocero))
		{
			$ls_criterio = "AND sno_hprenomina.valprenom<>0 ";
		}
		if(empty($as_conceptop2))
		{
			$ls_criterio = $ls_criterio." AND (sno_hprenomina.tipprenom<>'P2' AND sno_hprenomina.tipprenom<>'V4' AND sno_hprenomina.tipprenom<>'W4')";
		}
		$ls_sql="SELECT sno_hprenomina.codconc, sno_hconcepto.nomcon, sno_hprenomina.tipprenom, sno_hprenomina.valprenom, sno_hprenomina.valhis ".
				"  FROM sno_hprenomina, sno_hconcepto ".
				" WHERE sno_hprenomina.codemp='".$this->ls_codemp."' ".
				"   AND sno_hprenomina.codnom='".$this->ls_codnom."' ".
				"   AND sno_hprenomina.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hprenomina.codperi='".$this->ls_peractnom."' ".
				"   AND sno_hprenomina.codper='".$as_codper."' ".
				"     ".$ls_criterio.
				"   AND sno_hprenomina.codemp = sno_hconcepto.codemp ".
				"   AND sno_hprenomina.codnom = sno_hconcepto.codnom ".
				"   AND sno_hprenomina.anocur = sno_hconcepto.anocur ".
				"   AND sno_hprenomina.codperi = sno_hconcepto.codperi ".
				"   AND sno_hprenomina.codconc = sno_hconcepto.codconc ".
				" ORDER BY sno_hprenomina.codconc ";
		$this->rs_data_detalle=$this->io_sql->select($ls_sql);
		if($this->rs_data_detalle===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_prenomina_conceptopersonal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_prenomina_conceptopersonal
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	  function uf_pagonomina_personal($as_codperdes,$as_codperhas,$as_conceptocero,$as_conceptoreporte,$as_conceptop2,$as_codubifis,
										$as_codpai,$as_codest,$as_codmun,$as_codpar,$as_subnomdes,$as_subnomhas,$as_orden)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_pagonomina_personal
		//         Access: public (desde la clase sigesp_sno_rpp_pagonomina)  
		//	    Arguments: as_codperdes // C�digo del personal donde se empieza a filtrar
		//	  			   as_codperhas // C�digo del personal donde se termina de filtrar		  
		//	  			   as_conceptocero // criterio que me indica si se desea quitar los conceptos cuyo valor es cero
		//	  			   as_conceptoreporte // criterio que me indica si se desea mostrar los conceptos tipo reporte
		//	  			   as_conceptop2 // criterio que me indica si se desea mostrar los conceptos de tipo aporte patronal
		//	  			   as_orden // orden por medio del cual se desea que salga el reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: funci�n que busca la informaci�n del personal que se le calcul� la n�mina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 01/02/2006								Fecha �ltima Modificaci�n : 
		// SE MODIFICARON LAS TABLAS TH POR H
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		$ls_criteriounion="";
		if(!empty($as_codperdes))
		{
			$ls_criterio= " AND sno_hpersonalnomina.codper>='".$as_codperdes."'";
			$ls_criteriounion=" AND sno_hpersonalnomina.codper>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codper<='".$as_codperhas."'";
			$ls_criteriounion = $ls_criteriounion."   AND sno_hpersonalnomina.codper<='".$as_codperhas."'";
		}
		if(!empty($as_subnomdes))
		{
			$ls_criterio= $ls_criterio."    AND sno_hpersonalnomina.codsubnom>='".$as_subnomdes."'";
			$ls_criteriounion= $ls_criteriounion."    AND sno_hpersonalnomina.codsubnom>='".$as_subnomdes."'";
		}
		if(!empty($as_subnomhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codsubnom<='".$as_subnomhas."'";
			$ls_criteriounion= $ls_criteriounion."   AND sno_hpersonalnomina.codsubnom<='".$as_subnomhas."'";
		}
		if(!empty($as_conceptocero))
		{
			$ls_criterio = $ls_criterio."   AND sno_hsalida.valsal<>0 ";
		}
		if(!empty($as_codubifis))
		{
			$ls_criterio= $ls_criterio." AND sno_hpersonalnomina.codubifis='".$as_codubifis."'";
			$ls_criteriounion = $ls_criteriounion." AND sno_hpersonalnomina.codubifis='".$as_codubifis."'";
		}
		else
		{
			if(!empty($as_codest))
			{
				$ls_criterio= $ls_criterio." AND sno_ubicacionfisica.codpai='".$as_codpai."'";
				$ls_criterio= $ls_criterio." AND sno_ubicacionfisica.codest='".$as_codest."'";
				$ls_criteriounion = $ls_criteriounion." AND sno_ubicacionfisica.codpai='".$as_codpai."'";
				$ls_criteriounion = $ls_criteriounion." AND sno_ubicacionfisica.codest='".$as_codest."'";
			}
			if(!empty($as_codmun))
			{
				$ls_criterio= $ls_criterio." AND sno_ubicacionfisica.codmun='".$as_codmun."'";
				$ls_criteriounion = $ls_criteriounion." AND sno_ubicacionfisica.codmun='".$as_codmun."'";
			}
			if(!empty($as_codpar))
			{
				$ls_criterio= $ls_criterio." AND sno_ubicacionfisica.codpar='".$as_codpar."'";
				$ls_criteriounion = $ls_criteriounion." AND sno_ubicacionfisica.codpar='".$as_codpar."'";
			}
		}
		if(!empty($as_conceptoreporte))
		{
			if(!empty($as_conceptop2))
			{
				$ls_criterio = $ls_criterio." AND (sno_hsalida.tipsal='A' OR sno_hsalida.tipsal='V1' OR sno_hsalida.tipsal='W1' OR ".
											"	   sno_hsalida.tipsal='D' OR sno_hsalida.tipsal='V2' OR sno_hsalida.tipsal='W2' OR ".
							   				"      sno_hsalida.tipsal='P1' OR sno_hsalida.tipsal='V3' OR sno_hsalida.tipsal='W3' OR ".
											"	   sno_hsalida.tipsal='P2' OR sno_hsalida.tipsal='V4' OR sno_hsalida.tipsal='W4' OR ".
											"	   sno_hsalida.tipsal='R')";
			}
			else
			{
				$ls_criterio = $ls_criterio." AND (sno_hsalida.tipsal='A' OR sno_hsalida.tipsal='V1' OR sno_hsalida.tipsal='W1' OR ".
											"	   sno_hsalida.tipsal='D' OR sno_hsalida.tipsal='V2' OR sno_hsalida.tipsal='W2' OR ".
							   				"      sno_hsalida.tipsal='P1' OR sno_hsalida.tipsal='V3' OR sno_hsalida.tipsal='W3' OR ".
											"	   sno_hsalida.tipsal='R')";
			}
		}
		else
		{
			if(!empty($as_conceptop2))
			{
				$ls_criterio = $ls_criterio." AND (sno_hsalida.tipsal='A' OR sno_hsalida.tipsal='V1' OR sno_hsalida.tipsal='W1' OR ".
											"	   sno_hsalida.tipsal='D' OR sno_hsalida.tipsal='V2' OR sno_hsalida.tipsal='W2' OR ".
							   				"      sno_hsalida.tipsal='P1' OR sno_hsalida.tipsal='V3' OR sno_hsalida.tipsal='W3' OR ".
											"	   sno_hsalida.tipsal='P2' OR sno_hsalida.tipsal='V4' OR sno_hsalida.tipsal='W4') ";
			}
			else
			{
				$ls_criterio = $ls_criterio." AND (sno_hsalida.tipsal='A' OR sno_hsalida.tipsal='V1' OR sno_hsalida.tipsal='W1' OR ".
											"	   sno_hsalida.tipsal='D' OR sno_hsalida.tipsal='V2' OR sno_hsalida.tipsal='W2' OR ".
							   				"      sno_hsalida.tipsal='P1' OR sno_hsalida.tipsal='V3' OR sno_hsalida.tipsal='W3')";
			}
		}
		if(empty($as_orden))
		{
			$ls_orden=" ORDER BY sno_personal.codper ";
		}
		else
		{
			switch($as_orden)
			{
				case "1": // Ordena por unidad administrativa
					$ls_orden=" ORDER BY minorguniadm, ofiuniadm, uniuniadm, depuniadm, prouniadm, codper ";
					break;

				case "2": // Ordena por C�digo de personal
					$ls_orden=" ORDER BY sno_personal.codper ";
					break;

				case "3": // Ordena por Apellido de personal
					$ls_orden=" ORDER BY sno_personal.apeper ";
					break;

				case "4": // Ordena por Nombre de personal
					$ls_orden=" ORDER BY sno_personal.nomper ";
					break;
			}
		}
		if($this->li_rac=="1") // Utiliza RAC
		{
			$ls_descar="       (SELECT denasicar FROM sno_hasignacioncargo ".
					   "   	     WHERE sno_hpersonalnomina.codemp = sno_hasignacioncargo.codemp ".
					   "		   AND sno_hpersonalnomina.codnom = sno_hasignacioncargo.codnom ".
					   "		   AND sno_hpersonalnomina.anocur = sno_hasignacioncargo.anocur ".
					   "		   AND sno_hpersonalnomina.codperi = sno_hasignacioncargo.codperi ".
				       "           AND sno_hpersonalnomina.codasicar = sno_hasignacioncargo.codasicar) as descar ";
		}
		else // No utiliza RAC
		{
			$ls_descar="       (SELECT descar FROM sno_hcargo ".
					   "   	     WHERE sno_hpersonalnomina.codemp = sno_hcargo.codemp ".
					   "		   AND sno_hpersonalnomina.codnom = sno_hcargo.codnom ".
					   "		   AND sno_hpersonalnomina.anocur = sno_hcargo.anocur ".
					   "		   AND sno_hpersonalnomina.codperi = sno_hcargo.codperi ".
				       "           AND sno_hpersonalnomina.codcar = sno_hcargo.codcar) as descar ";
		}
		$ls_union="";
		$li_vac_reportar=trim($this->uf_select_config("SNO","NOMINA","MOSTRAR VACACION","0","C"));
		$ls_vac_codconvac=trim($this->uf_select_config("SNO","NOMINA","COD CONCEPTO VACACION","","C"));
		if(($li_vac_reportar==1)&&($ls_vac_codconvac!=""))
		{
			$ls_union="UNION ".
					  "SELECT sno_personal.codper, sno_personal.cedper, sno_personal.nomper, sno_personal.apeper, sno_personal.fecingper, ".
					  "		sno_hpersonalnomina.codcueban, sno_hunidadadmin.desuniadm, sno_hunidadadmin.codprouniadm, MAX(sno_hpersonalnomina.sueper) AS sueper, ".
					  "		sno_hunidadadmin.minorguniadm, sno_hunidadadmin.ofiuniadm, sno_hunidadadmin.uniuniadm, sno_hunidadadmin.depuniadm, sno_hunidadadmin.prouniadm, ".
					  "		MAX(sno_hpersonalnomina.codgra) AS codgra, MAX(sno_personal.nacper) AS nacper, MAX(sno_ubicacionfisica.desubifis) AS desubifis, MAX(sno_hpersonalnomina.descasicar) AS descasicar, ".
					  "		MAX(sno_hpersonalnomina.obsrecper) As obsrecper, ".
					  "		  (SELECT desest FROM sigesp_estados".
					  "			WHERE sigesp_estados.codpai = sno_ubicacionfisica.codpai ".
					  "			 AND sigesp_estados.codest = sno_ubicacionfisica.codest) AS desest, ".
					  "		  (SELECT denmun FROM sigesp_municipio ".
					  "			WHERE sigesp_municipio.codpai = sno_ubicacionfisica.codpai ".
					  "			 AND sigesp_municipio.codest = sno_ubicacionfisica.codest ".
					  "			 AND sigesp_municipio.codmun = sno_ubicacionfisica.codmun) AS denmun, ".
					  "		  (SELECT denpar FROM sigesp_parroquia  ".
					  "			WHERE sigesp_parroquia.codpai = sno_ubicacionfisica.codpai ".
					  "			 AND sigesp_parroquia.codest = sno_ubicacionfisica.codest ".
					  "			 AND sigesp_parroquia.codmun = sno_ubicacionfisica.codmun ".
					  "			 AND sigesp_parroquia.codpar = sno_ubicacionfisica.codpar) AS denpar, ".
					  "		  (SELECT SUM(asires) FROM sno_hresumen ".
					  "			WHERE sno_hresumen.codemp = sno_hsalida.codemp ".
					  "			 AND sno_hresumen.codnom = sno_hsalida.codnom ".
					  "			 AND sno_hresumen.anocur = sno_hsalida.anocur ".
					  "			 AND sno_hresumen.codperi = sno_hsalida.codperi) AS totalasignacion, ".
					  "		  (SELECT SUM(dedres + apoempres) FROM sno_hresumen ".
					  "			WHERE sno_hresumen.codemp = sno_hsalida.codemp ".
					  "			 AND sno_hresumen.codnom = sno_hsalida.codnom ".
					  "			 AND sno_hresumen.anocur = sno_hsalida.anocur ".
					  "			 AND sno_hresumen.codperi = sno_hsalida.codperi) AS totaldeduccion, ".
					  "		  (SELECT SUM(apopatres) FROM sno_hresumen ".
					  "			WHERE sno_hresumen.codemp = sno_hsalida.codemp ".
					  "			 AND sno_hresumen.codnom = sno_hsalida.codnom ".
					  "			 AND sno_hresumen.anocur = sno_hsalida.anocur ".
					  "			 AND sno_hresumen.codperi = sno_hsalida.codperi) AS totalaporte, ".
					  "".$ls_descar.
					  "  FROM sno_personal, sno_hpersonalnomina, sno_hsalida, sno_hunidadadmin, sno_ubicacionfisica  ".
					  " WHERE sno_hpersonalnomina.codemp='".$this->ls_codemp."' ".
					  "   AND sno_hpersonalnomina.codnom='".$this->ls_codnom."' ".
					  "   AND sno_hpersonalnomina.anocur='".$this->ls_anocurnom."' ".
					  "   AND sno_hpersonalnomina.codperi='".$this->ls_peractnom."' ".
					  "   AND sno_hsalida.codconc='".$ls_vac_codconvac."' ".
					  "   ".$ls_criteriounion.
					  "   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
					  "   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
					  "   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
					  "   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
					  "   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
					  "   AND sno_personal.codemp = sno_hpersonalnomina.codemp ".
					  "   AND sno_personal.codper = sno_hpersonalnomina.codper ".
					  "   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
					  "   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
					  "   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
					  "   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
					  "   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
					  "   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
					  "   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
					  "   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
					  "   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
					  "   AND sno_ubicacionfisica.codemp = sno_hpersonalnomina.codemp ".
					  "	  AND sno_ubicacionfisica.codubifis = sno_hpersonalnomina.codubifis ".
					  " GROUP BY sno_hpersonalnomina.codemp, sno_hsalida.codemp, sno_hpersonalnomina.codnom, sno_hsalida.codnom,  sno_hpersonalnomina.anocur, sno_hsalida.anocur, sno_hpersonalnomina.codperi, sno_hsalida.codperi,".
					  "		   sno_personal.codper, sno_personal.cedper, sno_personal.nomper, sno_personal.apeper, ".
				  	  "		   sno_personal.fecingper, sno_hpersonalnomina.codcar, sno_hpersonalnomina.codasicar, ".
					  "		   sno_hpersonalnomina.codcueban, sno_hunidadadmin.desuniadm, sno_hunidadadmin.codprouniadm, ".

					  "        sno_hunidadadmin.minorguniadm, sno_hunidadadmin.ofiuniadm, sno_hunidadadmin.uniuniadm, ".
					  "    	   sno_hunidadadmin.depuniadm, sno_hunidadadmin.prouniadm, sno_ubicacionfisica.codpai, ".
					  "        sno_ubicacionfisica.codest,sno_ubicacionfisica.codmun,sno_ubicacionfisica.codpar  ";
		}
		$ls_sql="SELECT sno_personal.codper, sno_personal.cedper, sno_personal.nomper, sno_personal.apeper, sno_personal.fecingper, ".
				"		sno_hpersonalnomina.codcueban, sno_hunidadadmin.desuniadm, sno_hunidadadmin.codprouniadm, MAX(sno_hpersonalnomina.sueper) AS sueper, ".
			    "		sno_hunidadadmin.minorguniadm, sno_hunidadadmin.ofiuniadm, sno_hunidadadmin.uniuniadm, sno_hunidadadmin.depuniadm, sno_hunidadadmin.prouniadm, ".
				"		MAX(sno_hpersonalnomina.codgra) AS codgra, MAX(sno_personal.nacper) AS nacper, MAX(sno_ubicacionfisica.desubifis) AS desubifis, MAX(sno_hpersonalnomina.descasicar) AS descasicar, ".
			    "		MAX(sno_hpersonalnomina.obsrecper) As obsrecper, ".
				"		  (SELECT desest FROM sigesp_estados  ".
				"			WHERE sigesp_estados.codpai = sno_ubicacionfisica.codpai ".
				"			 AND sigesp_estados.codest = sno_ubicacionfisica.codest) AS desest, ".
				"		  (SELECT denmun FROM sigesp_municipio  ".
				"			WHERE sigesp_municipio.codpai = sno_ubicacionfisica.codpai ".
				"			 AND sigesp_municipio.codest = sno_ubicacionfisica.codest ".
				"			 AND sigesp_municipio.codmun = sno_ubicacionfisica.codmun) AS denmun, ".
				"		  (SELECT denpar FROM sigesp_parroquia  ".
				"			WHERE sigesp_parroquia.codpai = sno_ubicacionfisica.codpai ".
				"			 AND sigesp_parroquia.codest = sno_ubicacionfisica.codest ".
				"			 AND sigesp_parroquia.codmun = sno_ubicacionfisica.codmun ".
				"			 AND sigesp_parroquia.codpar = sno_ubicacionfisica.codpar) AS denpar, ".
			    "		  (SELECT SUM(asires) FROM sno_hresumen ".
			    "			WHERE sno_hresumen.codemp = sno_hsalida.codemp ".
			    "			 AND sno_hresumen.codnom = sno_hsalida.codnom ".
			    "			 AND sno_hresumen.anocur = sno_hsalida.anocur ".
			    " 		 AND sno_hresumen.codperi = sno_hsalida.codperi) AS totalasignacion, ".
			    "		  (SELECT SUM(dedres + apoempres) FROM sno_hresumen ".
			    "			WHERE sno_hresumen.codemp = sno_hsalida.codemp ".
			    "			 AND sno_hresumen.codnom = sno_hsalida.codnom ".
			    "			 AND sno_hresumen.anocur = sno_hsalida.anocur ".
			    "			 AND sno_hresumen.codperi = sno_hsalida.codperi) AS totaldeduccion, ".
			    "		  (SELECT SUM(apopatres) FROM sno_hresumen ".
			    "			WHERE sno_hresumen.codemp = sno_hsalida.codemp ".
			    "			 AND sno_hresumen.codnom = sno_hsalida.codnom ".
			    "			 AND sno_hresumen.anocur = sno_hsalida.anocur ".
			    "			 AND sno_hresumen.codperi = sno_hsalida.codperi) AS totalaporte, ".
				"  ".$ls_descar.
				"  FROM sno_personal, sno_hpersonalnomina, sno_hsalida, sno_hunidadadmin, sno_ubicacionfisica ".
				" WHERE sno_hpersonalnomina.codemp='".$this->ls_codemp."' ".
				"   AND sno_hpersonalnomina.codnom='".$this->ls_codnom."' ".
				"   AND sno_hpersonalnomina.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hpersonalnomina.codperi='".$this->ls_peractnom."' ".
				"   ".$ls_criterio." ".
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"   AND sno_personal.codemp = sno_hpersonalnomina.codemp ".
				"   AND sno_personal.codper = sno_hpersonalnomina.codper ".
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
			    "   AND sno_ubicacionfisica.codemp = sno_hpersonalnomina.codemp ".
				"   AND sno_ubicacionfisica.codubifis = sno_hpersonalnomina.codubifis ".
				" GROUP BY sno_hpersonalnomina.codemp, sno_hsalida.codemp, sno_hpersonalnomina.codnom, sno_hsalida.codnom,  sno_hpersonalnomina.anocur, sno_hsalida.anocur, sno_hpersonalnomina.codperi, sno_hsalida.codperi,".
				"		   sno_personal.codper, sno_personal.cedper, sno_personal.nomper, sno_personal.apeper, ".
				"		   sno_personal.fecingper, sno_hpersonalnomina.codcar, sno_hpersonalnomina.codasicar, ".
				"		   sno_hpersonalnomina.codcueban, sno_hunidadadmin.desuniadm, sno_hunidadadmin.codprouniadm, ".
				"          sno_hunidadadmin.minorguniadm, sno_hunidadadmin.ofiuniadm, sno_hunidadadmin.uniuniadm, ".
				"    	   sno_hunidadadmin.depuniadm, sno_hunidadadmin.prouniadm, sno_ubicacionfisica.codpai, ".
			    "        sno_ubicacionfisica.codest,sno_ubicacionfisica.codmun,sno_ubicacionfisica.codpar  ".
				"   ".$ls_union.
				"   ".$ls_orden;
		$this->rs_data=$this->io_sql->select($ls_sql);
		if($this->rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_pagonomina_personal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_pagonomina_personal
	//-----------------------------------------------------------------------------------------------------------------------------------

        function uf_pagonomina_personal2($codnom,$as_codperdes,$as_codperhas,$as_conceptocero,$as_conceptoreporte,$as_conceptop2,$as_codubifis,
										$as_codpai,$as_codest,$as_codmun,$as_codpar,$as_subnomdes,$as_subnomhas,$as_orden,$ls_codperidesde,$ls_codperihasta,$ls_anocurnom)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_pagonomina_personal
		//         Access: public (desde la clase sigesp_sno_rpp_pagonomina)
		//	    Arguments: as_codperdes // C�digo del personal donde se empieza a filtrar
		//	  			   as_codperhas // C�digo del personal donde se termina de filtrar
		//	  			   as_conceptocero // criterio que me indica si se desea quitar los conceptos cuyo valor es cero
		//	  			   as_conceptoreporte // criterio que me indica si se desea mostrar los conceptos tipo reporte
		//	  			   as_conceptop2 // criterio que me indica si se desea mostrar los conceptos de tipo aporte patronal
		//	  			   as_orden // orden por medio del cual se desea que salga el reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: funci�n que busca la informaci�n del personal que se le calcul� la n�mina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 01/02/2006								Fecha �ltima Modificaci�n :
		// SE MODIFICARON LAS TABLAS TH POR H
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		$ls_criteriounion="";
                $anio = date('Y');
		if(!empty($as_codperdes))
		{
			$ls_criterio= " AND sno_hpersonalnomina.codper>='".$as_codperdes."'";
			$ls_criteriounion=" AND sno_hpersonalnomina.codper>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codper<='".$as_codperhas."'";
			$ls_criteriounion = $ls_criteriounion."   AND sno_hpersonalnomina.codper<='".$as_codperhas."'";
		}
		if(!empty($as_subnomdes))
		{
			$ls_criterio= $ls_criterio."    AND sno_hpersonalnomina.codsubnom>='".$as_subnomdes."'";
			$ls_criteriounion= $ls_criteriounion."    AND sno_hpersonalnomina.codsubnom>='".$as_subnomdes."'";
		}
		if(!empty($as_subnomhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codsubnom<='".$as_subnomhas."'";
			$ls_criteriounion= $ls_criteriounion."   AND sno_hpersonalnomina.codsubnom<='".$as_subnomhas."'";
		}
		if(!empty($as_conceptocero))
		{
			$ls_criterio = $ls_criterio."   AND sno_hsalida.valsal<>0 ";
		}
		if(!empty($as_codubifis))
		{
			$ls_criterio= $ls_criterio." AND sno_hpersonalnomina.codubifis='".$as_codubifis."'";
			$ls_criteriounion = $ls_criteriounion." AND sno_hpersonalnomina.codubifis='".$as_codubifis."'";
		}
		else
		{
			if(!empty($as_codest))
			{
				$ls_criterio= $ls_criterio." AND sno_ubicacionfisica.codpai='".$as_codpai."'";
				$ls_criterio= $ls_criterio." AND sno_ubicacionfisica.codest='".$as_codest."'";
				$ls_criteriounion = $ls_criteriounion." AND sno_ubicacionfisica.codpai='".$as_codpai."'";
				$ls_criteriounion = $ls_criteriounion." AND sno_ubicacionfisica.codest='".$as_codest."'";
			}
			if(!empty($as_codmun))
			{
				$ls_criterio= $ls_criterio." AND sno_ubicacionfisica.codmun='".$as_codmun."'";
				$ls_criteriounion = $ls_criteriounion." AND sno_ubicacionfisica.codmun='".$as_codmun."'";
			}
			if(!empty($as_codpar))
			{
				$ls_criterio= $ls_criterio." AND sno_ubicacionfisica.codpar='".$as_codpar."'";
				$ls_criteriounion = $ls_criteriounion." AND sno_ubicacionfisica.codpar='".$as_codpar."'";
			}
		}
		if(!empty($as_conceptoreporte))
		{
			if(!empty($as_conceptop2))
			{
				$ls_criterio = $ls_criterio." AND (sno_hsalida.tipsal='A' OR sno_hsalida.tipsal='V1' OR sno_hsalida.tipsal='W1' OR ".
											"	   sno_hsalida.tipsal='D' OR sno_hsalida.tipsal='V2' OR sno_hsalida.tipsal='W2' OR ".
							   				"      sno_hsalida.tipsal='P1' OR sno_hsalida.tipsal='V3' OR sno_hsalida.tipsal='W3' OR ".
											"	   sno_hsalida.tipsal='P2' OR sno_hsalida.tipsal='V4' OR sno_hsalida.tipsal='W4' OR ".
											"	   sno_hsalida.tipsal='R')";
			}
			else
			{
				$ls_criterio = $ls_criterio." AND (sno_hsalida.tipsal='A' OR sno_hsalida.tipsal='V1' OR sno_hsalida.tipsal='W1' OR ".
											"	   sno_hsalida.tipsal='D' OR sno_hsalida.tipsal='V2' OR sno_hsalida.tipsal='W2' OR ".
							   				"      sno_hsalida.tipsal='P1' OR sno_hsalida.tipsal='V3' OR sno_hsalida.tipsal='W3' OR ".
											"	   sno_hsalida.tipsal='R')";
			}
		}
		else
		{
			if(!empty($as_conceptop2))
			{
				$ls_criterio = $ls_criterio." AND (sno_hsalida.tipsal='A' OR sno_hsalida.tipsal='V1' OR sno_hsalida.tipsal='W1' OR ".
											"	   sno_hsalida.tipsal='D' OR sno_hsalida.tipsal='V2' OR sno_hsalida.tipsal='W2' OR ".
							   				"      sno_hsalida.tipsal='P1' OR sno_hsalida.tipsal='V3' OR sno_hsalida.tipsal='W3' OR ".
											"	   sno_hsalida.tipsal='P2' OR sno_hsalida.tipsal='V4' OR sno_hsalida.tipsal='W4') ";
			}
			else
			{
				$ls_criterio = $ls_criterio." AND (sno_hsalida.tipsal='A' OR sno_hsalida.tipsal='V1' OR sno_hsalida.tipsal='W1' OR ".
											"	   sno_hsalida.tipsal='D' OR sno_hsalida.tipsal='V2' OR sno_hsalida.tipsal='W2' OR ".
							   				"      sno_hsalida.tipsal='P1' OR sno_hsalida.tipsal='V3' OR sno_hsalida.tipsal='W3')";
			}
		}
		if(empty($as_orden))
		{
			$ls_orden=" ORDER BY sno_personal.codper ";
		}
		else
		{
			switch($as_orden)
			{
				case "1": // Ordena por unidad administrativa
					$ls_orden=" ORDER BY minorguniadm, ofiuniadm, uniuniadm, depuniadm, prouniadm, codper ";
					break;

				case "2": // Ordena por C�digo de personal
					$ls_orden=" ORDER BY sno_personal.codper ";
					break;

				case "3": // Ordena por Apellido de personal
					$ls_orden=" ORDER BY sno_personal.apeper ";
					break;

				case "4": // Ordena por Nombre de personal
					$ls_orden=" ORDER BY sno_personal.nomper ";
					break;
			}
		}
		if($this->li_rac=="1") // Utiliza RAC
		{
			$ls_descar="       (SELECT denasicar FROM sno_hasignacioncargo ".
					   "   	     WHERE sno_hpersonalnomina.codemp = sno_hasignacioncargo.codemp ".
					   "		   AND sno_hpersonalnomina.codnom = sno_hasignacioncargo.codnom ".
					   "		   AND sno_hpersonalnomina.anocur = sno_hasignacioncargo.anocur ".
					   "		   AND sno_hpersonalnomina.codperi = sno_hasignacioncargo.codperi ".
				       "           AND sno_hpersonalnomina.codasicar = sno_hasignacioncargo.codasicar) as descar ";
		}
		else // No utiliza RAC
		{
			$ls_descar="       (SELECT descar FROM sno_hcargo ".
					   "   	     WHERE sno_hpersonalnomina.codemp = sno_hcargo.codemp ".
					   "		   AND sno_hpersonalnomina.codnom = sno_hcargo.codnom ".
					   "		   AND sno_hpersonalnomina.anocur = sno_hcargo.anocur ".
					   "		   AND sno_hpersonalnomina.codperi = sno_hcargo.codperi ".
				       "           AND sno_hpersonalnomina.codcar = sno_hcargo.codcar) as descar ";
		}
		$ls_union="";
		$li_vac_reportar=trim($this->uf_select_config("SNO","NOMINA","MOSTRAR VACACION","0","C"));
		$ls_vac_codconvac=trim($this->uf_select_config("SNO","NOMINA","COD CONCEPTO VACACION","","C"));
		if(($li_vac_reportar==1)&&($ls_vac_codconvac!=""))
		{
			$ls_union="UNION ".
					  "SELECT sno_personal.codper, sno_personal.cedper, sno_personal.nomper, sno_personal.apeper, sno_personal.fecingper, ".
					  "		sno_hpersonalnomina.codcueban, sno_hunidadadmin.desuniadm, sno_hunidadadmin.codprouniadm, MAX(sno_hpersonalnomina.sueper) AS sueper, ".
					  "		sno_hunidadadmin.minorguniadm, sno_hunidadadmin.ofiuniadm, sno_hunidadadmin.uniuniadm, sno_hunidadadmin.depuniadm, sno_hunidadadmin.prouniadm, ".
					  "		MAX(sno_hpersonalnomina.codgra) AS codgra, MAX(sno_personal.nacper) AS nacper, MAX(sno_ubicacionfisica.desubifis) AS desubifis, MAX(sno_hpersonalnomina.descasicar) AS descasicar, ".
					  "		MAX(sno_hpersonalnomina.obsrecper) As obsrecper, ".
					  "		  (SELECT desest FROM sigesp_estados".
					  "			WHERE sigesp_estados.codpai = sno_ubicacionfisica.codpai ".
					  "			 AND sigesp_estados.codest = sno_ubicacionfisica.codest) AS desest, ".
					  "		  (SELECT denmun FROM sigesp_municipio ".
					  "			WHERE sigesp_municipio.codpai = sno_ubicacionfisica.codpai ".
					  "			 AND sigesp_municipio.codest = sno_ubicacionfisica.codest ".
					  "			 AND sigesp_municipio.codmun = sno_ubicacionfisica.codmun) AS denmun, ".
					  "		  (SELECT denpar FROM sigesp_parroquia  ".
					  "			WHERE sigesp_parroquia.codpai = sno_ubicacionfisica.codpai ".
					  "			 AND sigesp_parroquia.codest = sno_ubicacionfisica.codest ".
					  "			 AND sigesp_parroquia.codmun = sno_ubicacionfisica.codmun ".
					  "			 AND sigesp_parroquia.codpar = sno_ubicacionfisica.codpar) AS denpar, ".
					  "		  (SELECT SUM(asires) FROM sno_hresumen ".
					  "			WHERE sno_hresumen.codemp = sno_hsalida.codemp ".
					  "			 AND sno_hresumen.codnom = sno_hsalida.codnom ".
					  "			 AND sno_hresumen.anocur = sno_hsalida.anocur ".
					  "			 AND sno_hresumen.codperi = sno_hsalida.codperi) AS totalasignacion, ".
					  "		  (SELECT SUM(dedres + apoempres) FROM sno_hresumen ".
					  "			WHERE sno_hresumen.codemp = sno_hsalida.codemp ".
					  "			 AND sno_hresumen.codnom = sno_hsalida.codnom ".
					  "			 AND sno_hresumen.anocur = sno_hsalida.anocur ".
					  "			 AND sno_hresumen.codperi = sno_hsalida.codperi) AS totaldeduccion, ".
					  "		  (SELECT SUM(apopatres) FROM sno_hresumen ".
					  "			WHERE sno_hresumen.codemp = sno_hsalida.codemp ".
					  "			 AND sno_hresumen.codnom = sno_hsalida.codnom ".
					  "			 AND sno_hresumen.anocur = sno_hsalida.anocur ".
					  "			 AND sno_hresumen.codperi = sno_hsalida.codperi) AS totalaporte, ".
					  "".$ls_descar.
					  "  FROM sno_personal, sno_hpersonalnomina, sno_hsalida, sno_hunidadadmin, sno_ubicacionfisica  ".
					  " WHERE sno_hpersonalnomina.codemp='".$this->ls_codemp."' ".
					  "   AND sno_hpersonalnomina.codnom='".$codnom."' ".
					  "   AND sno_hpersonalnomina.anocur='".$ls_anocurnom."' ".
					  "   AND sno_hpersonalnomina.codperi BETWEEN '".$ls_codperidesde."' and  '".$ls_codperihasta."'".
					  "   AND sno_hsalida.codconc='".$ls_vac_codconvac."' ".
					  "   ".$ls_criteriounion.
					  "   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
					  "   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
					  "   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
					  "   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
					  "   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
					  "   AND sno_personal.codemp = sno_hpersonalnomina.codemp ".
					  "   AND sno_personal.codper = sno_hpersonalnomina.codper ".
					  "   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
					  "   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
					  "   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
					  "   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
					  "   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
					  "   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
					  "   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
					  "   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
					  "   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
					  "   AND sno_ubicacionfisica.codemp = sno_hpersonalnomina.codemp ".
					  "	  AND sno_ubicacionfisica.codubifis = sno_hpersonalnomina.codubifis ".
					  " GROUP BY sno_hpersonalnomina.codemp, sno_hsalida.codemp, sno_hpersonalnomina.codnom, sno_hsalida.codnom,  sno_hpersonalnomina.anocur, sno_hsalida.anocur, sno_hpersonalnomina.codperi, sno_hsalida.codperi,".
					  "		   sno_personal.codper, sno_personal.cedper, sno_personal.nomper, sno_personal.apeper, ".
				  	  "		   sno_personal.fecingper, sno_hpersonalnomina.codcar, sno_hpersonalnomina.codasicar, ".
					  "		   sno_hpersonalnomina.codcueban, sno_hunidadadmin.desuniadm, sno_hunidadadmin.codprouniadm, ".

					  "        sno_hunidadadmin.minorguniadm, sno_hunidadadmin.ofiuniadm, sno_hunidadadmin.uniuniadm, ".
					  "    	   sno_hunidadadmin.depuniadm, sno_hunidadadmin.prouniadm, sno_ubicacionfisica.codpai, ".
					  "        sno_ubicacionfisica.codest,sno_ubicacionfisica.codmun,sno_ubicacionfisica.codpar  ";
		}
		$ls_sql="SELECT sno_personal.codper, sno_personal.cedper, sno_personal.nomper, sno_personal.apeper, sno_personal.fecingper, ".
				"		sno_hpersonalnomina.codcueban, sno_hunidadadmin.desuniadm, sno_hunidadadmin.codprouniadm, MAX(sno_hpersonalnomina.sueper) AS sueper, ".
			    "		sno_hunidadadmin.minorguniadm, sno_hunidadadmin.ofiuniadm, sno_hunidadadmin.uniuniadm, sno_hunidadadmin.depuniadm, sno_hunidadadmin.prouniadm, ".
				"		MAX(sno_hpersonalnomina.codgra) AS codgra, MAX(sno_personal.nacper) AS nacper, MAX(sno_ubicacionfisica.desubifis) AS desubifis, MAX(sno_hpersonalnomina.descasicar) AS descasicar, ".
			    "		MAX(sno_hpersonalnomina.obsrecper) As obsrecper, ".
				"		  (SELECT desest FROM sigesp_estados  ".
				"			WHERE sigesp_estados.codpai = sno_ubicacionfisica.codpai ".
				"			 AND sigesp_estados.codest = sno_ubicacionfisica.codest) AS desest, ".
				"		  (SELECT denmun FROM sigesp_municipio  ".
				"			WHERE sigesp_municipio.codpai = sno_ubicacionfisica.codpai ".
				"			 AND sigesp_municipio.codest = sno_ubicacionfisica.codest ".
				"			 AND sigesp_municipio.codmun = sno_ubicacionfisica.codmun) AS denmun, ".
				"		  (SELECT denpar FROM sigesp_parroquia  ".
				"			WHERE sigesp_parroquia.codpai = sno_ubicacionfisica.codpai ".
				"			 AND sigesp_parroquia.codest = sno_ubicacionfisica.codest ".
				"			 AND sigesp_parroquia.codmun = sno_ubicacionfisica.codmun ".
				"			 AND sigesp_parroquia.codpar = sno_ubicacionfisica.codpar) AS denpar, ".
			    "		  (SELECT SUM(asires) FROM sno_hresumen ".
			    "			WHERE sno_hresumen.codemp = sno_hsalida.codemp ".
			    "			 AND sno_hresumen.codnom = sno_hsalida.codnom ".
			    "			 AND sno_hresumen.anocur = sno_hsalida.anocur ".
			    " 		 AND sno_hresumen.codperi = sno_hsalida.codperi) AS totalasignacion, ".
			    "		  (SELECT SUM(dedres + apoempres) FROM sno_hresumen ".
			    "			WHERE sno_hresumen.codemp = sno_hsalida.codemp ".
			    "			 AND sno_hresumen.codnom = sno_hsalida.codnom ".
			    "			 AND sno_hresumen.anocur = sno_hsalida.anocur ".
			    "			 AND sno_hresumen.codperi = sno_hsalida.codperi) AS totaldeduccion, ".
			    "		  (SELECT SUM(apopatres) FROM sno_hresumen ".
			    "			WHERE sno_hresumen.codemp = sno_hsalida.codemp ".
			    "			 AND sno_hresumen.codnom = sno_hsalida.codnom ".
			    "			 AND sno_hresumen.anocur = sno_hsalida.anocur ".
			    "			 AND sno_hresumen.codperi = sno_hsalida.codperi) AS totalaporte, ".
				"  ".$ls_descar.
				"  FROM sno_personal, sno_hpersonalnomina, sno_hsalida, sno_hunidadadmin, sno_ubicacionfisica ".
				" WHERE sno_hpersonalnomina.codemp='".$this->ls_codemp."' ".
				"   AND sno_hpersonalnomina.codnom='".$codnom."' ".
				"   AND sno_hpersonalnomina.anocur='".$anio."' ".
				"   AND sno_hpersonalnomina.codperi BETWEEN '".$ls_codperidesde."' and  '".$ls_codperihasta."'".
				"   ".$ls_criterio." ".
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"   AND sno_personal.codemp = sno_hpersonalnomina.codemp ".
				"   AND sno_personal.codper = sno_hpersonalnomina.codper ".
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
			    "   AND sno_ubicacionfisica.codemp = sno_hpersonalnomina.codemp ".
				"   AND sno_ubicacionfisica.codubifis = sno_hpersonalnomina.codubifis ".
				" GROUP BY sno_hpersonalnomina.codemp, sno_hsalida.codemp, sno_hpersonalnomina.codnom, sno_hsalida.codnom,  sno_hpersonalnomina.anocur, sno_hsalida.anocur, sno_hpersonalnomina.codperi, sno_hsalida.codperi,".
				"		   sno_personal.codper, sno_personal.cedper, sno_personal.nomper, sno_personal.apeper, ".
				"		   sno_personal.fecingper, sno_hpersonalnomina.codcar, sno_hpersonalnomina.codasicar, ".
				"		   sno_hpersonalnomina.codcueban, sno_hunidadadmin.desuniadm, sno_hunidadadmin.codprouniadm, ".
				"          sno_hunidadadmin.minorguniadm, sno_hunidadadmin.ofiuniadm, sno_hunidadadmin.uniuniadm, ".
				"    	   sno_hunidadadmin.depuniadm, sno_hunidadadmin.prouniadm, sno_ubicacionfisica.codpai, ".
			    "        sno_ubicacionfisica.codest,sno_ubicacionfisica.codmun,sno_ubicacionfisica.codpar  ".
				"   ".$ls_union.
				"   ".$ls_orden;

		$this->rs_data=$this->io_sql->select($ls_sql);
		if($this->rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_pagonomina_personal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_pagonomina_personal
	//-----------------------------------------------------------------------------------------------------------------------------------
        
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_pagonomina_personal_pensionado($as_codperdes,$as_codperhas,$as_conceptocero,$as_conceptoreporte,
	                                           $as_conceptop2,$as_codubifis,
									           $as_codpai,$as_codest,$as_codmun,$as_codpar,$as_subnomdes,$as_subnomhas,$as_orden)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_pagonomina_personal_pensionado
		//         Access: public (desde la clase sigesp_sno_rpp_pagonomina)  
		//	    Arguments: as_codperdes // C�digo del personal donde se empieza a filtrar
		//	  			   as_codperhas // C�digo del personal donde se termina de filtrar		  
		//	  			   as_conceptocero // criterio que me indica si se desea quitar los conceptos cuyo valor es cero
		//	  			   as_conceptoreporte // criterio que me indica si se desea mostrar los conceptos tipo reporte
		//	  			   as_conceptop2 // criterio que me indica si se desea mostrar los conceptos de tipo aporte patronal
		//	  			   as_orden // orden por medio del cual se desea que salga el reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: funci�n que busca la informaci�n del personal que se le calcul� la n�mina
		//	   Creado Por: Ing. Mar�a Beatriz Unda
		// Fecha Creaci�n: 29/09/2008 							Fecha �ltima Modificaci�n :		
		// SE MODIFICARON LAS TABLAS TH POR H
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		$ls_criteriounion="";
		if(!empty($as_codperdes))
		{
			$ls_criterio= " AND sno_hpersonalnomina.codper>='".$as_codperdes."'";
			$ls_criteriounion=" AND sno_hpersonalnomina.codper>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codper<='".$as_codperhas."'";
			$ls_criteriounion = $ls_criteriounion."   AND sno_hpersonalnomina.codper<='".$as_codperhas."'";
		}
		if(!empty($as_subnomdes))
		{
			$ls_criterio= $ls_criterio."    AND sno_hpersonalnomina.codsubnom>='".$as_subnomdes."'";
			$ls_criteriounion= $ls_criteriounion."    AND sno_hpersonalnomina.codsubnom>='".$as_subnomdes."'";
		}
		if(!empty($as_subnomhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codsubnom<='".$as_subnomhas."'";
			$ls_criteriounion= $ls_criteriounion."   AND sno_hpersonalnomina.codsubnom<='".$as_subnomhas."'";
		}
		if(!empty($as_conceptocero))
		{
			$ls_criterio = $ls_criterio."   AND sno_hsalida.valsal<>0 ";
		}
		if(!empty($as_codubifis))
		{
			$ls_criterio= $ls_criterio." AND sno_hpersonalnomina.codubifis='".$as_codubifis."'";
			$ls_criteriounion = $ls_criteriounion." AND sno_hpersonalnomina.codubifis='".$as_codubifis."'";
		}
		else
		{
			if(!empty($as_codest))
			{
				$ls_criterio= $ls_criterio." AND sno_ubicacionfisica.codpai='".$as_codpai."'";
				$ls_criterio= $ls_criterio." AND sno_ubicacionfisica.codest='".$as_codest."'";
				$ls_criteriounion = $ls_criteriounion." AND sno_ubicacionfisica.codpai='".$as_codpai."'";
				$ls_criteriounion = $ls_criteriounion." AND sno_ubicacionfisica.codest='".$as_codest."'";
			}
			if(!empty($as_codmun))
			{
				$ls_criterio= $ls_criterio." AND sno_ubicacionfisica.codmun='".$as_codmun."'";
				$ls_criteriounion = $ls_criteriounion." AND sno_ubicacionfisica.codmun='".$as_codmun."'";
			}
			if(!empty($as_codpar))
			{
				$ls_criterio= $ls_criterio." AND sno_ubicacionfisica.codpar='".$as_codpar."'";
				$ls_criteriounion = $ls_criteriounion." AND sno_ubicacionfisica.codpar='".$as_codpar."'";
			}
		}
		if(!empty($as_conceptoreporte))
		{
			if(!empty($as_conceptop2))
			{
				$ls_criterio = $ls_criterio." AND (sno_hsalida.tipsal='A' OR sno_hsalida.tipsal='V1' OR sno_hsalida.tipsal='W1' OR ".
											"	   sno_hsalida.tipsal='D' OR sno_hsalida.tipsal='V2' OR sno_hsalida.tipsal='W2' OR ".
							   				"      sno_hsalida.tipsal='P1' OR sno_hsalida.tipsal='V3' OR sno_hsalida.tipsal='W3' OR ".
											"	   sno_hsalida.tipsal='P2' OR sno_hsalida.tipsal='V4' OR sno_hsalida.tipsal='W4' OR ".
											"	   sno_hsalida.tipsal='R')";
			}
			else
			{
				$ls_criterio = $ls_criterio." AND (sno_hsalida.tipsal='A' OR sno_hsalida.tipsal='V1' OR sno_hsalida.tipsal='W1' OR ".
											"	   sno_hsalida.tipsal='D' OR sno_hsalida.tipsal='V2' OR sno_hsalida.tipsal='W2' OR ".
							   				"      sno_hsalida.tipsal='P1' OR sno_hsalida.tipsal='V3' OR sno_hsalida.tipsal='W3' OR ".
											"	   sno_hsalida.tipsal='R')";
			}
		}
		else
		{
			if(!empty($as_conceptop2))
			{
				$ls_criterio = $ls_criterio." AND (sno_hsalida.tipsal='A' OR sno_hsalida.tipsal='V1' OR sno_hsalida.tipsal='W1' OR ".
											"	   sno_hsalida.tipsal='D' OR sno_hsalida.tipsal='V2' OR sno_hsalida.tipsal='W2' OR ".
							   				"      sno_hsalida.tipsal='P1' OR sno_hsalida.tipsal='V3' OR sno_hsalida.tipsal='W3' OR ".
											"	   sno_hsalida.tipsal='P2' OR sno_hsalida.tipsal='V4' OR sno_hsalida.tipsal='W4') ";
			}
			else
			{
				$ls_criterio = $ls_criterio." AND (sno_hsalida.tipsal='A' OR sno_hsalida.tipsal='V1' OR sno_hsalida.tipsal='W1' OR ".
											"	   sno_hsalida.tipsal='D' OR sno_hsalida.tipsal='V2' OR sno_hsalida.tipsal='W2' OR ".
							   				"      sno_hsalida.tipsal='P1' OR sno_hsalida.tipsal='V3' OR sno_hsalida.tipsal='W3')";
			}
		}
		if(empty($as_orden))
		{
			$ls_orden=" ORDER BY codper ";
		}
		else
		{
			switch($as_orden)
			{
				case "1": // Ordena por unidad administrativa
					$ls_orden=" ORDER BY minorguniadm, ofiuniadm, uniuniadm, ".
							  "    	     depuniadm, prouniadm, codper ";
					break;

				case "2": // Ordena por C�digo de personal
					$ls_orden=" ORDER BY codper ";
					break;

				case "3": // Ordena por Apellido de personal
					$ls_orden=" ORDER BY apeper ";
					break;

				case "4": // Ordena por Nombre de personal
					$ls_orden=" ORDER BY nomper ";
					break;
			}
		}
		if($this->li_rac=="1") // Utiliza RAC
		{
			$ls_descar="       (SELECT denasicar FROM sno_asignacioncargo ".
					   "   	     WHERE sno_hpersonalnomina.codemp = sno_asignacioncargo.codemp ".
					   "		   AND sno_hpersonalnomina.codnom = sno_asignacioncargo.codnom ".
				       "           AND sno_hpersonalnomina.codasicar = sno_asignacioncargo.codasicar) as descar ";
		}
		else // No utiliza RAC
		{
			$ls_descar="       (SELECT descar FROM sno_cargo ".
					   "   	     WHERE sno_hpersonalnomina.codemp = sno_cargo.codemp ".
					   "		   AND sno_hpersonalnomina.codnom = sno_cargo.codnom ".
				       "           AND sno_hpersonalnomina.codcar = sno_cargo.codcar) as descar ";
		}
		$ls_union="";
		$li_vac_reportar=trim($this->uf_select_config("SNO","NOMINA","MOSTRAR VACACION","0","C"));
		$ls_vac_codconvac=trim($this->uf_select_config("SNO","NOMINA","COD CONCEPTO VACACION","","C"));
		if(($li_vac_reportar==1)&&($ls_vac_codconvac!=""))
		{
			$ls_union="UNION ".
					  "SELECT sno_hpersonalnomina.codper, sno_personal.cedper, sno_personal.nomper, sno_personal.apeper, ".
					  "		  sno_personal.fecingper, sno_hpersonalnomina.fecculcontr, sno_hpersonalnomina.fecingper as fecingnom,".
					  "       sno_hpersonalnomina.codcueban, sno_hunidadadmin.desuniadm, sno_personal.fecegrper, ".
					  "       sno_personal.fecsitu, sno_personal.fecnacper, ".
					  "		  sno_hunidadadmin.codprouniadm, MAX(sno_hpersonalnomina.sueper) AS sueper, ".
					  "		  sno_hunidadadmin.minorguniadm, sno_hunidadadmin.ofiuniadm, sno_hunidadadmin.uniuniadm, ".
					  "       sno_hunidadadmin.depuniadm, sno_hunidadadmin.prouniadm, MAX(sno_hpersonalnomina.codgra) AS codgra, ".
					  "       MAX(sno_personal.nacper) AS nacper,  ".
					  "       MAX(sno_ubicacionfisica.desubifis) AS desubifis,".
					  "		  (SELECT desest FROM sigesp_estados ".
					  "			WHERE sigesp_estados.codpai = sno_ubicacionfisica.codpai ".
					  "			 AND sigesp_estados.codest = sno_ubicacionfisica.codest) AS desest, ".
					  "		  (SELECT denmun FROM sigesp_municipio ".
					  "			WHERE sigesp_municipio.codpai = sno_ubicacionfisica.codpai ".
					  "			 AND sigesp_municipio.codest = sno_ubicacionfisica.codest ".
					  "			 AND sigesp_municipio.codmun = sno_ubicacionfisica.codmun) AS denmun, ".
					  "		  (SELECT denpar FROM sigesp_parroquia ".
					  "			WHERE sigesp_parroquia.codpai = sno_ubicacionfisica.codpai ".
					  "			 AND sigesp_parroquia.codest = sno_ubicacionfisica.codest ".
					  "			 AND sigesp_parroquia.codmun = sno_ubicacionfisica.codmun ".
					  "			 AND sigesp_parroquia.codpar = sno_ubicacionfisica.codpar) AS denpar, ".
					  "		  (SELECT SUM(asires) FROM sno_resumen ".
					  "			WHERE sno_resumen.codemp = sno_hsalida.codemp ".
					  "			 AND sno_resumen.codnom = sno_hsalida.codnom ".
					  "			 AND sno_resumen.codperi = sno_hsalida.codperi) AS totalasignacion, ".
					  "		  (SELECT SUM(dedres + apoempres) FROM sno_resumen ".
					  "			WHERE sno_resumen.codemp = sno_hsalida.codemp ".
					  "			 AND sno_resumen.codnom = sno_hsalida.codnom ".
					  "			 AND sno_resumen.codperi = sno_hsalida.codperi) AS totaldeduccion, ".
					  "		  (SELECT SUM(apopatres) FROM sno_resumen ".
					  "			WHERE sno_resumen.codemp = sno_hsalida.codemp ".
					  "			 AND sno_resumen.codnom = sno_hsalida.codnom ".
					  "			 AND sno_resumen.codperi = sno_hsalida.codperi) AS totalaporte, ".
					  "		 (SELECT sno_componente.descom FROM sno_componente ".
					  "        WHERE sno_componente.codemp='".$this->ls_codemp."'".
					  "          AND sno_componente.codcom=sno_personal.codcom) AS dencom, ".
					  "		 (SELECT sno_rango.desran FROM sno_rango ".
					  "        WHERE sno_rango.codemp='".$this->ls_codemp."'".
					  "          AND sno_rango.codcom=sno_personal.codcom".
					  "          AND sno_rango.codran=sno_personal.codran) AS denran, sno_personal.situacion, ".
					  "        (SELECT sno_causales.dencausa FROM sno_causales WHERE sno_causales.codemp='".$this->ls_codemp."'".
				      "            AND sno_causales.codcausa=sno_personal.codcausa) AS dencausa, ".
  					  $ls_descar.
					  "  FROM sno_personal, sno_hpersonalnomina, sno_hsalida, sno_hunidadadmin, sno_ubicacionfisica ".
					  " WHERE sno_hpersonalnomina.codemp='".$this->ls_codemp."' ".
					  "   AND sno_hpersonalnomina.codnom='".$this->ls_codnom."' ".
					  "	  AND sno_hpersonalnomina.staper = '2' ".
					  "   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
					  "   AND sno_hsalida.codconc='".$ls_vac_codconvac."' ".
					  "   ".$ls_criteriounion.
					  "   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
					  "   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
					  "   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
					  "   AND sno_personal.codemp = sno_hpersonalnomina.codemp ".
					  "   AND sno_personal.codper = sno_hpersonalnomina.codper ".
					  "   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
					  "   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
					  "   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
					  "   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
					  "   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
					  "   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
					  "   AND sno_ubicacionfisica.codemp = sno_hpersonalnomina.codemp ".
					  "	  AND sno_ubicacionfisica.codubifis = sno_hpersonalnomina.codubifis ".
					  " GROUP BY sno_hpersonalnomina.codemp, sno_hsalida.codemp, sno_hpersonalnomina.codnom, sno_hsalida.codnom, sno_hsalida.codperi, sno_hpersonalnomina.codper, ".
					  "		   sno_personal.cedper, sno_personal.nomper, sno_personal.apeper, sno_personal.fecingper, ".
					  "        sno_hpersonalnomina.fecculcontr, sno_hpersonalnomina.fecingper, ".
					  "		   sno_hpersonalnomina.codcueban, sno_hunidadadmin.desuniadm, sno_hunidadadmin.desuniadm, ".
					  "		   sno_hunidadadmin.codprouniadm, sno_hpersonalnomina.codcar, sno_hpersonalnomina.codasicar, ".
					  "		   sno_hunidadadmin.minorguniadm, sno_hunidadadmin.ofiuniadm, sno_hunidadadmin.uniuniadm, ".
					  "    	   sno_hunidadadmin.depuniadm, sno_hunidadadmin.prouniadm, sno_ubicacionfisica.codpai, ".
					  "        sno_ubicacionfisica.codest,sno_ubicacionfisica.codmun,sno_ubicacionfisica.codpar, ".
					  "        sno_personal.codcom,sno_personal.codran, sno_personal.cauegrper, sno_personal.codcausa,".
					  "        sno_personal.fecegrper, sno_personal.fecsitu, sno_personal.fecnacper ";
		}
		$ls_sql="SELECT sno_hpersonalnomina.codper, sno_personal.cedper, sno_personal.nomper, sno_personal.apeper, ".
				"		sno_personal.fecingper, sno_hpersonalnomina.fecculcontr, sno_hpersonalnomina.fecingper as fecingnom, ".
				"       sno_hpersonalnomina.codcueban, sno_hunidadadmin.desuniadm, sno_personal.fecegrper, sno_personal.fecsitu, ".
				"       sno_personal.fecnacper, ".
				"		sno_hunidadadmin.codprouniadm, MAX(sno_hpersonalnomina.sueper) AS sueper, sno_hunidadadmin.minorguniadm, ".
				"		sno_hunidadadmin.ofiuniadm, sno_hunidadadmin.uniuniadm, sno_hunidadadmin.depuniadm, sno_hunidadadmin.prouniadm, ".
				"       MAX(sno_hpersonalnomina.codgra) AS codgra, MAX(sno_personal.nacper) AS nacper, ".
				"       MAX(sno_ubicacionfisica.desubifis) AS desubifis, ".
				"		  (SELECT desest FROM sigesp_estados ".
				"			WHERE sigesp_estados.codpai = sno_ubicacionfisica.codpai ".
				"			 AND sigesp_estados.codest = sno_ubicacionfisica.codest) AS desest, ".
				"		  (SELECT denmun FROM sigesp_municipio ".
				"			WHERE sigesp_municipio.codpai = sno_ubicacionfisica.codpai ".
				"			 AND sigesp_municipio.codest = sno_ubicacionfisica.codest ".
				"			 AND sigesp_municipio.codmun = sno_ubicacionfisica.codmun) AS denmun, ".
				"		  (SELECT denpar FROM sigesp_parroquia ".
				"			WHERE sigesp_parroquia.codpai = sno_ubicacionfisica.codpai ".
				"			 AND sigesp_parroquia.codest = sno_ubicacionfisica.codest ".
				"			 AND sigesp_parroquia.codmun = sno_ubicacionfisica.codmun ".
				"			 AND sigesp_parroquia.codpar = sno_ubicacionfisica.codpar) AS denpar, ".
				"		  (SELECT SUM(asires) FROM sno_resumen ".
				"			WHERE sno_resumen.codemp = sno_hsalida.codemp ".
				"			 AND sno_resumen.codnom = sno_hsalida.codnom ".
				"			 AND sno_resumen.codperi = sno_hsalida.codperi) AS totalasignacion, ".
				"		  (SELECT SUM(dedres + apoempres) FROM sno_resumen ".
				"			WHERE sno_resumen.codemp = sno_hsalida.codemp ".
				"			 AND sno_resumen.codnom = sno_hsalida.codnom ".
				"			 AND sno_resumen.codperi = sno_hsalida.codperi) AS totaldeduccion, ".
				"		  (SELECT SUM(apopatres) FROM sno_resumen ".
				"			WHERE sno_resumen.codemp = sno_hsalida.codemp ".
				"			 AND sno_resumen.codnom = sno_hsalida.codnom ".
				"			 AND sno_resumen.codperi = sno_hsalida.codperi) AS totalaporte, ".
			    "		 (SELECT sno_componente.descom FROM sno_componente ".
				"          WHERE sno_componente.codemp='".$this->ls_codemp."'".
				"            AND sno_componente.codcom=sno_personal.codcom) AS dencom, ".
				"		 (SELECT sno_rango.desran FROM sno_rango ".
			    "        WHERE sno_rango.codemp='".$this->ls_codemp."'".
				"          AND sno_rango.codcom=sno_personal.codcom".
				"          AND sno_rango.codran=sno_personal.codran) AS denran, sno_personal.situacion, ".
				"        (SELECT sno_causales.dencausa FROM sno_causales WHERE sno_causales.codemp='".$this->ls_codemp."'".
				"            AND sno_causales.codcausa=sno_personal.codcausa) AS dencausa, ".
				$ls_descar.
				"  FROM sno_personal, sno_hpersonalnomina, sno_hsalida, sno_hunidadadmin, sno_ubicacionfisica ".
				" WHERE sno_hpersonalnomina.codemp='".$this->ls_codemp."' ".
				"   AND sno_hpersonalnomina.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   ".$ls_criterio.
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"   AND sno_personal.codemp = sno_hpersonalnomina.codemp ".
				"   AND sno_personal.codper = sno_hpersonalnomina.codper ".
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				"   AND sno_ubicacionfisica.codemp = sno_hpersonalnomina.codemp ".				
				"	AND sno_ubicacionfisica.codubifis = sno_hpersonalnomina.codubifis ".
				"   AND sno_personal.cedper NOT IN (SELECT sno_beneficiario.cedben FROM sno_beneficiario ".
				"                                    WHERE sno_beneficiario.codemp='".$this->ls_codemp."')".
				" GROUP BY sno_hpersonalnomina.codemp, sno_hsalida.codemp, sno_hpersonalnomina.codnom, sno_hsalida.codnom, sno_hsalida.codperi, sno_hpersonalnomina.codper, ".
				"		   sno_personal.cedper, sno_personal.nomper, sno_personal.apeper, sno_personal.fecingper, ".
				"          sno_hpersonalnomina.fecculcontr, sno_hpersonalnomina.fecingper, ".
				"		   sno_hpersonalnomina.codcueban, sno_hunidadadmin.desuniadm, sno_hunidadadmin.desuniadm, ".
				"		   sno_hunidadadmin.codprouniadm, sno_hpersonalnomina.codcar, sno_hpersonalnomina.codasicar, ".
				"		   sno_hunidadadmin.minorguniadm, sno_hunidadadmin.ofiuniadm, sno_hunidadadmin.uniuniadm, ".
			    "    	   sno_hunidadadmin.depuniadm, sno_hunidadadmin.prouniadm, sno_ubicacionfisica.codpai,  ".
				"          sno_ubicacionfisica.codest,sno_ubicacionfisica.codmun,sno_ubicacionfisica.codpar,".
				"          sno_personal.codcom,sno_personal.codran, sno_personal.codcausa, ".
				"          sno_personal.fecegrper, sno_personal.situacion, sno_personal.fecsitu, sno_personal.fecnacper ".
				"   ".$ls_union.
				"   ".$ls_orden;  
		$this->rs_data=$this->io_sql->select($ls_sql);
		
		if($this->rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_pagonomina_personal_pensionado ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_pagonomina_personal_pensionado
	//-----------------------------------------------------------------------------------------------------------------------------------//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_pagonomina_conceptopersonal($as_codper,$as_conceptocero,$as_tituloconcepto,$as_conceptoreporte,$as_conceptop2)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_pagonomina_conceptopersonal
		//         Access: public (desde la clase sigesp_sno_rpp_pagonomina)  
		//	    Arguments: as_codper // C�digo del personal que se desea buscar la salida
		//	  			   as_conceptocero // criterio que me indica si se desea quitar los conceptos en cero
		//	  			   as_tituloconcepto // criterio que me indica si se desea mostrar el t�tulo del concepto � el nombre
		//	  			   as_conceptoreporte // criterio que me indica si se desea mostrar los conceptos tipo reporte
		//	  			   as_conceptop2 // criterio que me indica si se desea mostrar los conceptos de tipo aporte patronal
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: funci�n que busca la informaci�n de los conceptos asociados al personal que se le calcul� la n�mina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 01/02/2006 								Fecha �ltima Modificaci�n :  
		// SE MODIFICARON LAS TABLAS TH POR H
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_campo="sno_hconcepto.nomcon";
		if(!empty($as_tituloconcepto))
		{
			$ls_campo = "sno_hconcepto.titcon";
		}
		if(!empty($as_conceptocero))
		{
			$ls_criterio = "AND sno_hsalida.valsal<>0 ";
		}
		if(!empty($as_conceptoreporte))
		{
			if(!empty($as_conceptop2))
			{
				$ls_criterio = $ls_criterio." AND (sno_hsalida.tipsal='A' OR sno_hsalida.tipsal='V1' OR sno_hsalida.tipsal='W1' OR ".
											"	   sno_hsalida.tipsal='D' OR sno_hsalida.tipsal='V2' OR sno_hsalida.tipsal='W2' OR ".
							   				"      sno_hsalida.tipsal='P1' OR sno_hsalida.tipsal='V3' OR sno_hsalida.tipsal='W3' OR ".
											"	   sno_hsalida.tipsal='P2' OR sno_hsalida.tipsal='V4' OR sno_hsalida.tipsal='W4' OR ".
											"	   sno_hsalida.tipsal='R')";
			}
			else
			{
				$ls_criterio = $ls_criterio." AND (sno_hsalida.tipsal='A' OR sno_hsalida.tipsal='V1' OR sno_hsalida.tipsal='W1' OR ".
											"	   sno_hsalida.tipsal='D' OR sno_hsalida.tipsal='V2' OR sno_hsalida.tipsal='W2' OR ".
							   				"      sno_hsalida.tipsal='P1' OR sno_hsalida.tipsal='V3' OR sno_hsalida.tipsal='W3' OR ".
											"	   sno_hsalida.tipsal='R')";
			}
		}
		else
		{
			if(!empty($as_conceptop2))
			{
				$ls_criterio = $ls_criterio." AND (sno_hsalida.tipsal='A' OR sno_hsalida.tipsal='V1' OR sno_hsalida.tipsal='W1' OR ".
											"	   sno_hsalida.tipsal='D' OR sno_hsalida.tipsal='V2' OR sno_hsalida.tipsal='W2' OR ".
							   				"      sno_hsalida.tipsal='P1' OR sno_hsalida.tipsal='V3' OR sno_hsalida.tipsal='W3' OR ".
											"	   sno_hsalida.tipsal='P2' OR sno_hsalida.tipsal='V4' OR sno_hsalida.tipsal='W4') ";
			}
			else
			{
				$ls_criterio = $ls_criterio." AND (sno_hsalida.tipsal='A' OR sno_hsalida.tipsal='V1' OR sno_hsalida.tipsal='W1' OR ".
											"	   sno_hsalida.tipsal='D' OR sno_hsalida.tipsal='V2' OR sno_hsalida.tipsal='W2' OR ".
							   				"      sno_hsalida.tipsal='P1' OR sno_hsalida.tipsal='V3' OR sno_hsalida.tipsal='W3')";
			}
		}
		$ls_union="";
		$li_vac_reportar=trim($this->uf_select_config("SNO","NOMINA","MOSTRAR VACACION","0","C"));
		$ls_vac_codconvac=trim($this->uf_select_config("SNO","NOMINA","COD CONCEPTO VACACION","","C"));
		if(($li_vac_reportar==1)&&($ls_vac_codconvac!=""))
		{
			$ls_union="UNION ".					  
					  "SELECT sno_hconcepto.codconc, ".$ls_campo." as nomcon, sno_hsalida.valsal, sno_hsalida.tipsal, sno_hconcepto.frevarcon, sno_hconcepto.repconsunicon,sno_hconcepto.consunicon ".
				      "  FROM sno_hsalida, sno_hconcepto, sno_hpersonalnomina ".
	 	 		      " WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				      "   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				      "   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				      "   AND sno_hsalida.codperi='".$this->ls_peractnom."'".
				      "   AND sno_hsalida.codper='".$as_codper."'".
				      "   AND sno_hsalida.codconc='".$ls_vac_codconvac."'".
				      "   AND sno_hpersonalnomina.staper = '2' ".
				      "   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				      "   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				      "   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				      "   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				      "   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
					  "   AND sno_hsalida.codemp = sno_hpersonalnomina.codemp ".
					  "   AND sno_hsalida.codnom = sno_hpersonalnomina.codnom ".
					  "   AND sno_hsalida.codper = sno_hpersonalnomina.codper ";
		}
		$ls_sql="SELECT sno_hconcepto.codconc, ".$ls_campo." as nomcon, sno_hsalida.valsal, sno_hsalida.tipsal, sno_hconcepto.frevarcon, sno_hconcepto.repconsunicon,sno_hconcepto.consunicon ".
				"  FROM sno_hsalida, sno_hconcepto ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."'".
				"   AND sno_hsalida.codper='".$as_codper."'".
				"   ".$ls_criterio.
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   ".$ls_union.
				" ORDER BY codconc, tipsal ";
		$this->rs_data_detalle=$this->io_sql->select($ls_sql);
		if($this->rs_data_detalle===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_pagonomina_conceptopersonal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_pagonomina_conceptopersonal
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_pagonomina_concepto_excel($as_tituloconcepto,$as_sigcon)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_pagonomina_concepto_excel
		//         Access: public (desde la clase sigesp_sno_rpp_pagonomina)  
		//	    Arguments: as_codper // C�digo del personal que se desea buscar la salida
		//	  			   as_tituloconcepto // criterio que me indica si se desea mostrar el t�tulo del concepto � el nombre
		//	  			   as_tipsal // Tipo de salida que voy a reportar
		//	  			   as_conceptop2 // criterio que me indica si se desea mostrar los conceptos de tipo aporte patronal
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: funci�n que busca la informaci�n de los conceptos asociados al personal que se le calcul� la n�mina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 01/02/2006 								Fecha �ltima Modificaci�n : 
		// SE MODIFICARON LAS TABLAS TH POR H
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_campo="nomcon";
		if(!empty($as_tituloconcepto))
		{
			$ls_campo = "titcon";
		}
		$ls_sql="SELECT codconc, ".$ls_campo." as nomcon ".
				"  FROM sno_hconcepto ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codnom='".$this->ls_codnom."' ".
				"   ".$as_sigcon." ".
				"   AND codconc IN (SELECT codconc FROM sno_hsalida WHERE codemp='".$this->ls_codemp."' AND codnom='".$this->ls_codnom."' AND codperi='".$this->ls_peractnom."')".
				"   AND codperi='".$this->ls_peractnom."'".
				" ORDER BY codconc ";
		$this->rs_data_detalle=$this->io_sql->select($ls_sql);
		if($this->rs_data_detalle===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_pagonomina_concepto_excel ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_pagonomina_conceptopersonal_excel
	//-----------------------------------------------------------------------------------------------------------------------------------




        function uf_pagonomina_concepto_excel2($as_tituloconcepto,$as_sigcon,$codnom)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_pagonomina_concepto_excel
		//         Access: public (desde la clase sigesp_sno_rpp_pagonomina)
		//	    Arguments: as_codper // C�digo del personal que se desea buscar la salida
		//	  			   as_tituloconcepto // criterio que me indica si se desea mostrar el t�tulo del concepto � el nombre
		//	  			   as_tipsal // Tipo de salida que voy a reportar
		//	  			   as_conceptop2 // criterio que me indica si se desea mostrar los conceptos de tipo aporte patronal
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: funci�n que busca la informaci�n de los conceptos asociados al personal que se le calcul� la n�mina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 01/02/2006 								Fecha �ltima Modificaci�n :
		// SE MODIFICARON LAS TABLAS TH POR H
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_campo="nomcon";
		if(!empty($as_tituloconcepto))
		{
			$ls_campo = "titcon";
		}
		$ls_sql="SELECT codconc, ".$ls_campo." as nomcon ".
				"  FROM sno_hconcepto ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codnom='".$codnom."' ".
				"   ".$as_sigcon." ".
				"   AND codconc IN (SELECT codconc FROM sno_hsalida WHERE codemp='".$this->ls_codemp."' AND codnom='".$codnom."' AND codperi='".$this->ls_peractnom."')".
				" ORDER BY codconc ";
		$this->rs_data_detalle=$this->io_sql->select($ls_sql);
		if($this->rs_data_detalle===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_pagonomina_concepto_excel ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_pagonomina_conceptopersonal_excel
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_pagonomina_conceptopersonal_excel($as_codper,$as_tituloconcepto,$as_tipsal)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_pagonomina_conceptopersonal_excel
		//         Access: public (desde la clase sigesp_sno_rpp_pagonomina)  
		//	    Arguments: as_codper // C�digo del personal que se desea buscar la salida
		//	  			   as_tituloconcepto // criterio que me indica si se desea mostrar el t�tulo del concepto � el nombre
		//	  			   as_tipsal // Tipo de salida que voy a reportar
		//	  			   as_conceptop2 // criterio que me indica si se desea mostrar los conceptos de tipo aporte patronal
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: funci�n que busca la informaci�n de los conceptos asociados al personal que se le calcul� la n�mina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 01/02/2006 								Fecha �ltima Modificaci�n :  
		// SE MODIFICARON LAS TABLAS TH POR H
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$this->DS_detalle->reset_ds();
		$lb_valido=true;
		$ls_criterio="";
		$ls_campo="sno_hconcepto.nomcon";
		if(!empty($as_tituloconcepto))
		{
			$ls_campo = "sno_hconcepto.titcon";
		}
		$ls_union="";
		$li_vac_reportar=trim($this->uf_select_config("SNO","NOMINA","MOSTRAR VACACION","0","C"));
		$ls_vac_codconvac=trim($this->uf_select_config("SNO","NOMINA","COD CONCEPTO VACACION","","C"));
		if(($li_vac_reportar==1)&&($ls_vac_codconvac!=""))
		{
			$ls_union="UNION ".
					  "SELECT sno_hconcepto.codconc, MAX(".$ls_campo.") as nomcon, SUM(sno_hsalida.valsal) as valsal, MAX(sno_hsalida.tipsal) AS tipsal ".
					  "  FROM sno_hsalida, sno_hconcepto, sno_hpersonalnomina ".
					  " WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
					  "   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
					  "   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
					  "   AND sno_hsalida.codperi='".$this->ls_peractnom."'".
					  "   AND sno_hsalida.codper='".$as_codper."'".
					  "   AND sno_hsalida.codconc='".$ls_vac_codconvac."'".
					  "   AND sno_hpersonalnomina.staper = '2' ".
					  "   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
					  "   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
					  "   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
					  "   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
					  "   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
					  "   AND sno_hsalida.codemp = sno_hpersonalnomina.codemp ".
					  "   AND sno_hsalida.codnom = sno_hpersonalnomina.codnom ".
					  "   AND sno_hsalida.anocur = sno_hpersonalnomina.anocur ".
					  "   AND sno_hsalida.codperi = sno_hpersonalnomina.codperi ".
					  "   AND sno_hsalida.codper = sno_hpersonalnomina.codper ".
					  " GROUP BY sno_hconcepto.codconc ";
		}
		$ls_sql="SELECT sno_hconcepto.codconc, MAX(".$ls_campo.") as nomcon, SUM(sno_hsalida.valsal) as valsal, MAX(sno_hsalida.tipsal) AS tipsal ".
				"  FROM sno_hconcepto, sno_hsalida ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."'".
				"   AND sno_hsalida.codper='".$as_codper."'".
				"   ".$as_tipsal.
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				" GROUP BY sno_hconcepto.codconc ".
				"   ".$ls_union.
				" ORDER BY codconc, tipsal ";
		$this->rs_data_detalle=$this->io_sql->select($ls_sql);
		if($this->rs_data_detalle===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_pagonomina_conceptopersonal_excel ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_pagonomina_conceptopersonal_excel
	//-----------------------------------------------------------------------------------------------------------------------------------


        function uf_pagonomina_conceptopersonal_excel2($as_codper,$as_tituloconcepto,$as_tipsal,$codnom,$ls_codperidesde,$ls_codperihasta,$ls_anocurnom)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_pagonomina_conceptopersonal_excel
		//         Access: public (desde la clase sigesp_sno_rpp_pagonomina)
		//	    Arguments: as_codper // C�digo del personal que se desea buscar la salida
		//	  			   as_tituloconcepto // criterio que me indica si se desea mostrar el t�tulo del concepto � el nombre
		//	  			   as_tipsal // Tipo de salida que voy a reportar
		//	  			   as_conceptop2 // criterio que me indica si se desea mostrar los conceptos de tipo aporte patronal
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: funci�n que busca la informaci�n de los conceptos asociados al personal que se le calcul� la n�mina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 01/02/2006 								Fecha �ltima Modificaci�n :
		// SE MODIFICARON LAS TABLAS TH POR H
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$this->DS_detalle->reset_ds();
		$lb_valido=true;
		$ls_criterio="";
		$ls_campo="sno_hconcepto.nomcon";
		if(!empty($as_tituloconcepto))
		{
			$ls_campo = "sno_hconcepto.titcon";
		}
		$ls_union="";
               // $anio_curso = date('Y');
		$li_vac_reportar=trim($this->uf_select_config("SNO","NOMINA","MOSTRAR VACACION","0","C"));
		$ls_vac_codconvac=trim($this->uf_select_config("SNO","NOMINA","COD CONCEPTO VACACION","","C"));
		if(($li_vac_reportar==1)&&($ls_vac_codconvac!=""))
		{
			$ls_union="UNION ".
					  "SELECT sno_hconcepto.codconc, MAX(".$ls_campo.") as nomcon, SUM(sno_hsalida.valsal) as valsal, MAX(sno_hsalida.tipsal) AS tipsal ".
					  "  FROM sno_hsalida, sno_hconcepto, sno_hpersonalnomina ".
					  " WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
					  "   AND sno_hsalida.codnom='".$codnom."' ".
					  "   AND sno_hsalida.anocur='".$ls_anocurnom."' ".
					  "   AND sno_hsalida.codperi BETWEEN '".$ls_codperidesde."' AND '".$ls_codperihasta."'".
					  "   AND sno_hsalida.codper='".$as_codper."'".
					  "   AND sno_hsalida.codconc='".$ls_vac_codconvac."'".
					  "   AND sno_hpersonalnomina.staper = '2' ".
					  "   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
					  "   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
					  "   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
					  "   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
					  "   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
					  "   AND sno_hsalida.codemp = sno_hpersonalnomina.codemp ".
					  "   AND sno_hsalida.codnom = sno_hpersonalnomina.codnom ".
					  "   AND sno_hsalida.anocur = sno_hpersonalnomina.anocur ".
					  "   AND sno_hsalida.codperi = sno_hpersonalnomina.codperi ".
					  "   AND sno_hsalida.codper = sno_hpersonalnomina.codper ".
					  " GROUP BY sno_hconcepto.codconc ";
		}
		$ls_sql="SELECT sno_hconcepto.codconc, MAX(".$ls_campo.") as nomcon, SUM(sno_hsalida.valsal) as valsal, MAX(sno_hsalida.tipsal) AS tipsal ".
				"  FROM sno_hconcepto, sno_hsalida ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$codnom."' ".
				"   AND sno_hsalida.anocur='".$ls_anocurnom."' ".
				"   AND sno_hsalida.codperi BETWEEN '".$ls_codperidesde."' AND '".$ls_codperihasta."'".
				"   AND sno_hsalida.codper='".$as_codper."'".
				"   ".$as_tipsal.
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				" GROUP BY sno_hconcepto.codconc ".
				"   ".$ls_union.
				" ORDER BY codconc, tipsal ";
		$this->rs_data_detalle=$this->io_sql->select($ls_sql);
		if($this->rs_data_detalle===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_pagonomina_conceptopersonal_excel ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_pagonomina_conceptopersonal_excel
	//-----------------------------------------------------------------------------------------------------------------------------------

        function uf_buscar_nomina($cod_nomina)
        {
               
           $ls_sql ="SELECT * FROM sno_nomina where codnom='$cod_nomina';";
           $this->rs_data_detalle=$this->io_sql->select($ls_sql);
            if($this->rs_data_detalle===false)
            {
                    $this->io_mensajes->message("CLASE->Report M�TODO->uf_pagonomina_conceptopersonal_excel ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
                    $lb_valido=false;
            }
            return $lb_valido;
        

        }
        
	function uf_pagonomina_prestamoamortizado($as_codper,$as_concepto,&$as_valor)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_pagonomina_prestamoamortizado
		//         Access: public (desde la clase sigesp_sno_rpp_pagonomina)  
		//	    Arguments: as_codper // C�digo del personal que se desea buscar el prestamo
		//	  			   as_concepto // c�digo del concepto 
		//	  			   as_valor // Valor del Amortizado
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: funci�n que busca la informaci�n de los prestamos asociados a estas personas
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 01/02/2006 								Fecha �ltima Modificaci�n : 
		// SE MODIFICARON LAS TABLAS TH POR H
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$as_valor="";
		$lb_valido=true;
		$ls_sql="SELECT monamopre ".
				"  FROM sno_hprestamos ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codnom='".$this->ls_codnom."' ".
				"   AND anocur='".$this->ls_anocurnom."' ".
				"   AND codperi='".$this->ls_peractnom."'".
				"   AND codconc='".$as_concepto."' ".				
				"   AND codper='".$as_codper."'".
				"   AND stapre=1";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_pagonomina_conceptopersonal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			$ls_total=0;
			$lb_entro=false;
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_total=$ls_total+$row["monamopre"];
				$lb_entro=true;
			}
			if($lb_entro)
			{
				$as_valor=number_format($ls_total,2,",",".");
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_pagonomina_prestamoamortizado
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_recibopago_personal($as_codperdes,$as_codperhas,$as_coduniadm,$as_conceptocero,$as_conceptop2,$as_conceptoreporte,
									$as_codubifis,$as_codpai,$as_codest,$as_codmun,$as_codpar,$as_subnomdes,$as_subnomhas,$as_orden)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_recibopago_personal
		//         Access: public (desde la clase sigesp_sno_r_recibopago)  
		//	    Arguments: as_codperdes // C�digo del personal donde se empieza a filtrar
		//	  			   as_codperhas // C�digo del personal donde se termina de filtrar		  
		//	  			   as_coduniadm // C�digo de la unidad administrativa	  
		//	  			   as_conceptop2 // criterio que me indica si se desea mostrar los conceptos de tipo aporte patronal
		//	  			   as_conceptoreporte // criterio que me indica si se desea mostrar los conceptos de tipo reporte
		//	  			   as_orden // Orde a mostrar en el reporte		  
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: funci�n que busca la informaci�n del personal que se le calcul� la n�mina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 05/05/2006 								Fecha �ltima Modificaci�n :  
		// SE MODIFICARON LAS TABLAS TH POR H
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		if(!empty($as_codperdes))
		{
			$ls_criterio= "AND sno_hpersonalnomina.codper>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codper<='".$as_codperhas."'";
		}
		if(!empty($as_subnomdes))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codsubnom>='".$as_subnomdes."'";
		}
		if(!empty($as_subnomhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codsubnom<='".$as_subnomhas."'";
		}
		if(!empty($as_conceptocero))
		{
			$ls_criterio = $ls_criterio."AND sno_hsalida.valsal<>0 ";
		}
		if(!empty($as_coduniadm))
		{
			$ls_criterio=$ls_criterio."   AND sno_hpersonalnomina.minorguniadm='".substr($as_coduniadm,0,4)."' ";
			$ls_criterio=$ls_criterio."   AND sno_hpersonalnomina.ofiuniadm='".substr($as_coduniadm,5,2)."' ";
			$ls_criterio=$ls_criterio."   AND sno_hpersonalnomina.uniuniadm='".substr($as_coduniadm,8,2)."' ";
			$ls_criterio=$ls_criterio."   AND sno_hpersonalnomina.depuniadm='".substr($as_coduniadm,11,2)."' ";
			$ls_criterio=$ls_criterio."   AND sno_hpersonalnomina.prouniadm='".substr($as_coduniadm,14,2)."' ";
		}
		if(!empty($as_conceptop2))
		{
			if(!empty($as_conceptoreporte))
			{
				$ls_criterio = $ls_criterio." AND (sno_hsalida.tipsal='A' OR sno_hsalida.tipsal='V1' OR sno_hsalida.tipsal='W1' OR ".
											"      sno_hsalida.tipsal='D' OR sno_hsalida.tipsal='V2' OR sno_hsalida.tipsal='W2' OR ".
											"      sno_hsalida.tipsal='P1' OR sno_hsalida.tipsal='V3' OR sno_hsalida.tipsal='W3' OR ".
											"	   sno_hsalida.tipsal='P2' OR sno_hsalida.tipsal='V4' OR sno_hsalida.tipsal='W4' OR ".
											"  	   sno_hsalida.tipsal='R')";
			}
			else
			{
				$ls_criterio = $ls_criterio." AND (sno_hsalida.tipsal='A' OR sno_hsalida.tipsal='V1' OR sno_hsalida.tipsal='W1' OR ".
											"      sno_hsalida.tipsal='D' OR sno_hsalida.tipsal='V2' OR sno_hsalida.tipsal='W2' OR ".
											"      sno_hsalida.tipsal='P1' OR sno_hsalida.tipsal='V3' OR sno_hsalida.tipsal='W3' OR ".
											"	   sno_hsalida.tipsal='P2' OR sno_hsalida.tipsal='V4' OR sno_hsalida.tipsal='W4') ";
			}
		}
		else
		{
			if(!empty($as_conceptoreporte))
			{
				$ls_criterio = $ls_criterio." AND (sno_hsalida.tipsal='A' OR sno_hsalida.tipsal='V1' OR sno_hsalida.tipsal='W1' OR ".
											"      sno_hsalida.tipsal='D' OR sno_hsalida.tipsal='V2' OR sno_hsalida.tipsal='W2' OR ".
											"      sno_hsalida.tipsal='P1' OR sno_hsalida.tipsal='V3' OR sno_hsalida.tipsal='W3' OR ".
											"  	   sno_hsalida.tipsal='R')";
			}
			else
			{
				$ls_criterio = $ls_criterio." AND (sno_hsalida.tipsal='A' OR sno_hsalida.tipsal='V1' OR sno_hsalida.tipsal='W1' OR ".
											"      sno_hsalida.tipsal='D' OR sno_hsalida.tipsal='V2' OR sno_hsalida.tipsal='W2' OR ".
											"      sno_hsalida.tipsal='P1' OR sno_hsalida.tipsal='V3' OR sno_hsalida.tipsal='W3')";
			}
		}
		if(!empty($as_codubifis))
		{
			$ls_criterio= $ls_criterio." AND sno_hpersonalnomina.codubifis='".$as_codubifis."'";
			$ls_criteriounion = $ls_criteriounion." AND sno_hpersonalnomina.codubifis='".$as_codubifis."'";
		}
		else
		{
			if(!empty($as_codest))
			{
				$ls_criterio= $ls_criterio." AND sno_ubicacionfisica.codpai='".$as_codpai."'";
				$ls_criterio= $ls_criterio." AND sno_ubicacionfisica.codest='".$as_codest."'";
				$ls_criteriounion = $ls_criteriounion." AND sno_ubicacionfisica.codpai='".$as_codpai."'";
				$ls_criteriounion = $ls_criteriounion." AND sno_ubicacionfisica.codest='".$as_codest."'";
			}
			if(!empty($as_codmun))
			{
				$ls_criterio= $ls_criterio." AND sno_ubicacionfisica.codmun='".$as_codmun."'";
				$ls_criteriounion = $ls_criteriounion." AND sno_ubicacionfisica.codmun='".$as_codmun."'";
			}
			if(!empty($as_codpar))
			{
				$ls_criterio= $ls_criterio." AND sno_ubicacionfisica.codpar='".$as_codpar."'";
				$ls_criteriounion = $ls_criteriounion." AND sno_ubicacionfisica.codpar='".$as_codpar."'";
			}
		}
		switch($as_orden)
		{
			case "1": // Ordena por C�digo de personal
				$ls_orden="ORDER BY sno_personal.codper ";
				break;

			case "2": // Ordena por Apellido de personal
				$ls_orden="ORDER BY sno_personal.apeper ";
				break;

			case "3": // Ordena por Nombre de personal
				$ls_orden="ORDER BY sno_personal.nomper ";
				break;
		}
		if($this->li_rac=="1")// Utiliza RAC
		{
			$ls_descar="       (SELECT denasicar FROM sno_hasignacioncargo ".
					   "   	     WHERE sno_hpersonalnomina.codemp = sno_hasignacioncargo.codemp ".
					   "		   AND sno_hpersonalnomina.codnom = sno_hasignacioncargo.codnom ".
					   "		   AND sno_hpersonalnomina.anocur = sno_hasignacioncargo.anocur ".
					   "		   AND sno_hpersonalnomina.codperi = sno_hasignacioncargo.codperi ".
				       "           AND sno_hpersonalnomina.codasicar = sno_hasignacioncargo.codasicar) as descar ";
					   
			$ls_codcar="       (SELECT codasicar FROM sno_hasignacioncargo ".
					   "   	     WHERE sno_hpersonalnomina.codemp = sno_hasignacioncargo.codemp ".
					   "		   AND sno_hpersonalnomina.codnom = sno_hasignacioncargo.codnom ".
					   "		   AND sno_hpersonalnomina.anocur = sno_hasignacioncargo.anocur ".
					   "		   AND sno_hpersonalnomina.codperi = sno_hasignacioncargo.codperi ".
				       "           AND sno_hpersonalnomina.codasicar = sno_hasignacioncargo.codasicar) as codcar, ";
		}
		else// No utiliza RAC
		{
			$ls_descar="       (SELECT descar FROM sno_hcargo ".
					   "   	     WHERE sno_hpersonalnomina.codemp = sno_hcargo.codemp ".
					   "		   AND sno_hpersonalnomina.codnom = sno_hcargo.codnom ".
					   "		   AND sno_hpersonalnomina.anocur = sno_hcargo.anocur ".
					   "		   AND sno_hpersonalnomina.codperi = sno_hcargo.codperi ".
				       "           AND sno_hpersonalnomina.codcar = sno_hcargo.codcar) as descar ";
					   
			$ls_codcar="       (SELECT codcar FROM sno_hcargo ".
					   "   	     WHERE sno_hpersonalnomina.codemp = sno_hcargo.codemp ".
					   "		   AND sno_hpersonalnomina.codnom = sno_hcargo.codnom ".
					   "		   AND sno_hpersonalnomina.anocur = sno_hcargo.anocur ".
					   "		   AND sno_hpersonalnomina.codperi = sno_hcargo.codperi ".
				       "           AND sno_hpersonalnomina.codcar = sno_hcargo.codcar) as codcar, ";
		}
		$ls_sql="SELECT sno_personal.codper,sno_personal.coreleper, sno_personal.cedper, sno_personal.nomper, sno_personal.apeper,     ".
		        "  sno_personal.nacper, sno_personal.fecegrper, sno_personal.fecleypen,sno_personal.codorg, sno_hpersonalnomina.obsrecper, ".
				"		sno_hpersonalnomina.codcueban, sno_hpersonalnomina.tipcuebanper, sno_personal.fecingper, sum(sno_hsalida.valsal) as total, sno_hunidadadmin.desuniadm,".
				"		sno_hunidadadmin.minorguniadm,sno_hunidadadmin.ofiuniadm,sno_hunidadadmin.uniuniadm,sno_hunidadadmin.depuniadm,".
				"		sno_hunidadadmin.prouniadm, MAX(sno_hpersonalnomina.sueper) AS sueper,  MAX(sno_hpersonalnomina.pagbanper) AS pagbanper, ".
				"		MAX(sno_hpersonalnomina.pagefeper) AS pagefeper, MAX(sno_ubicacionfisica.desubifis) AS desubifis,  ".
				"		MAX(sno_hpersonalnomina.descasicar) AS descasicar, ".
				"		  (SELECT tipnom FROM sno_hnomina ".
				"			WHERE sno_hpersonalnomina.codemp = sno_hnomina.codemp ".
				"			 AND sno_hpersonalnomina.codnom = sno_hnomina.codnom  ".
				"			 AND sno_hpersonalnomina.anocur = sno_hnomina.anocurnom  ".
				"			 AND sno_hpersonalnomina.codperi = sno_hnomina.peractnom) AS tiponom, ".
                                "                 (SELECT racnom FROM sno_hnomina ".
				"			WHERE sno_hpersonalnomina.codemp = sno_hnomina.codemp ".
				"			 AND sno_hpersonalnomina.codnom = sno_hnomina.codnom  ".
				"			 AND sno_hpersonalnomina.anocur = sno_hnomina.anocurnom  ".
				"			 AND sno_hpersonalnomina.codperi = sno_hnomina.peractnom) AS racnom, ".
				"		  (SELECT suemin FROM sno_hclasificacionobrero ".
				"			WHERE sno_hclasificacionobrero.codemp = sno_hpersonalnomina.codemp ".
				"			 AND sno_hclasificacionobrero.codnom = sno_hpersonalnomina.codnom  ".
				"			 AND sno_hclasificacionobrero.anocur = sno_hpersonalnomina.anocur  ".
				"			 AND sno_hclasificacionobrero.codperi = sno_hpersonalnomina.codperi  ".
				"			 AND sno_hclasificacionobrero.grado = sno_hpersonalnomina.grado) AS sueobr, ".
				"		  (SELECT desest FROM sigesp_estados ".
				"			WHERE sigesp_estados.codpai = sno_ubicacionfisica.codpai ".
				"			 AND sigesp_estados.codest = sno_ubicacionfisica.codest) AS desest, ".
				"		  (SELECT denmun FROM sigesp_municipio ".
				"			WHERE sigesp_municipio.codpai = sno_ubicacionfisica.codpai ".
				"			 AND sigesp_municipio.codest = sno_ubicacionfisica.codest ".
				"			 AND sigesp_municipio.codmun = sno_ubicacionfisica.codmun) AS denmun, ".
				"		  (SELECT denpar FROM sigesp_parroquia ".
				"			WHERE sigesp_parroquia.codpai = sno_ubicacionfisica.codpai ".
				"			 AND sigesp_parroquia.codest = sno_ubicacionfisica.codest ".
				"			 AND sigesp_parroquia.codmun = sno_ubicacionfisica.codmun ".
				"			 AND sigesp_parroquia.codpar = sno_ubicacionfisica.codpar) AS denpar, ".
				"		(SELECT nomban FROM scb_banco ".
				"		   WHERE scb_banco.codemp = sno_hpersonalnomina.codemp ".
				" 			 AND scb_banco.codban = sno_hpersonalnomina.codban) AS banco,".
				"		(SELECT  nomage FROM scb_agencias ".
				"		   WHERE scb_agencias.codemp = sno_hpersonalnomina.codemp ".
				" 			 AND scb_agencias.codban = sno_hpersonalnomina.codban ".
				"            AND scb_agencias.codage = sno_hpersonalnomina.codage) AS agencia,".
				"       (SELECT sno_categoria_rango.descat FROM sno_rango, sno_categoria_rango   ".
                "         WHERE sno_rango.codemp=sno_personal.codemp                             ".
                "           AND sno_rango.codcom=sno_personal.codcom                             ".
                "     AND sno_rango.codran=sno_personal.codran                                   ".
                "     AND sno_categoria_rango.codcat=sno_rango.codcat) AS descat,                ".
				$ls_codcar.$ls_descar." ,sno_hpersonalnomina.codgra ,sno_hpersonalnomina.codpas".
				"  FROM sno_personal, sno_hpersonalnomina, sno_hsalida, sno_hunidadadmin, sno_ubicacionfisica ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal<>'P2' AND  sno_hsalida.tipsal<>'V4' AND sno_hsalida.tipsal<>'W4') ".
				"   ".$ls_criterio." ".
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"   AND sno_hpersonalnomina.codemp = sno_ubicacionfisica.codemp ".
				"   AND sno_hpersonalnomina.codubifis = sno_ubicacionfisica.codubifis ".
				"   AND sno_hpersonalnomina.codemp = sno_personal.codemp ".
				"   AND sno_hpersonalnomina.codper = sno_personal.codper ".
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				" GROUP BY sno_hpersonalnomina.codemp, sno_hpersonalnomina.codnom, sno_hpersonalnomina.anocur, sno_hpersonalnomina.codperi, sno_personal.codemp,sno_personal.codcom, sno_personal.codran, ".
				"		   sno_personal.codper, sno_personal.cedper, sno_personal.nomper, sno_personal.apeper, ".
				"		   sno_personal.nacper,sno_personal.fecingper, sno_personal.fecegrper, sno_personal.fecleypen, sno_hpersonalnomina.codcueban, sno_hpersonalnomina.tipcuebanper, sno_personal.fecingper, ".
				"		   sno_hunidadadmin.desuniadm, sno_hpersonalnomina.codasicar, sno_hpersonalnomina.codcar, ".
				"		   sno_hpersonalnomina.codban, sno_hunidadadmin.minorguniadm,sno_hunidadadmin.ofiuniadm, ".
				"		   sno_hunidadadmin.uniuniadm,sno_hunidadadmin.depuniadm, sno_hunidadadmin.prouniadm, sno_ubicacionfisica.codpai,  ".
				"          sno_ubicacionfisica.codest,sno_ubicacionfisica.codmun,sno_ubicacionfisica.codpar,sno_hpersonalnomina.codage,sno_personal.codorg,sno_hpersonalnomina.grado, sno_hpersonalnomina.obsrecper, sno_personal.coreleper,
                                           sno_hpersonalnomina.codgra ,sno_hpersonalnomina.codpas".
				"   ".$ls_orden;
		$this->rs_data=$this->io_sql->select($ls_sql);		
		if($this->rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_recibopago_personal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_recibopago_personal
	//-----------------------------------------------------------------------------------------------------------------------------------
  	function uf_buscar_datos_correo(&$as_serv,&$as_port,&$as_remitente)
  	{ 	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_buscar_datos_correo
		//		   Access: public
		//	  Description: Funci�n que busca la informacion para enviar los recibos por correo electronico
		//	   Creado Por: Ing. Mar�a Beatriz Unda
		// Fecha Creaci�n: 05/01/2009 								Fecha �ltima Modificaci�n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$as_serv="";
		$as_port="";
		$as_remitente="";	
		$lb_valido=true;
		$ls_sql="SELECT msjservidor,msjpuerto,msjremitente ".				
				"  FROM sigesp_correo ".
				" WHERE sigesp_correo.codemp='".$ls_codemp."' ";

		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			  $this->io_mensajes->message("CLASE->Report M�TODO->uf_buscar_datos_correo ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			  $lb_valido=false;
		}
		else
		{			
			while(!$rs_data->EOF)
			{
				
				$as_serv=$rs_data->fields["msjservidor"];
				$as_port=$rs_data->fields["msjpuerto"];
				$as_remitente=$rs_data->fields["msjremitente"];					
				$rs_data->MoveNext();
			}
			
		}
		return $lb_valido;
   	}// fin uf_buscar_datos_correo

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_recibopago_conceptopersonal($as_codper,$as_conceptocero,$as_conceptop2,$as_conceptoreporte,$as_tituloconcepto,$as_quincena)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_recibopago_conceptopersonal
		//         Access: public (desde la clase sigesp_sno_rpp_recibopago)  
		//	    Arguments: as_codper // C�digo del personal que se desea buscar la salida
		//	  			   as_conceptocero // criterio que me indica si se desea quitar los conceptos cuyo valor es cero
		//	  			   as_conceptop2 // criterio que me indica si se desea mostrar los conceptos de tipo aporte patronal
		//	  			   as_conceptoreporte // criterio que me indica si se desea mostrar los conceptos de tipo reporte
		//	  			   as_tituloconcepto // criterio que me indica si se desea mostrar los t�tulos de los conceptos
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: funci�n que busca la informaci�n de los conceptos asociados al personal que se le calcul� la n�mina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 05/05/2006 								Fecha �ltima Modificaci�n :  
		// SE MODIFICARON LAS TABLAS TH POR H
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_campo="sno_hconcepto.nomcon";
		$ls_campomonto=" sno_hsalida.valsal ";
		if(($_SESSION["la_nomina"]["divcon"]==1)&&($_SESSION["la_nomina"]["tippernom"]==2))
		{
			if($as_quincena!="3")
			{
				$ls_criterio = $ls_criterio."   AND (sno_hconcepto.quirepcon = '".$as_quincena."' ".
											"	 OR  sno_hconcepto.quirepcon = '3')";
				switch($as_quincena)
				{
					case "1":
						$ls_campomonto=" sno_hsalida.priquisal as valsal ";
						break;
					case "2":
						$ls_campomonto=" sno_hsalida.segquisal as valsal ";
						break;
				}
			}
		}
		if(!empty($as_tituloconcepto))
		{
			$ls_campo = "sno_hconcepto.titcon";
		}
		if(!empty($as_conceptocero))
		{
			$ls_criterio = "   AND sno_hsalida.valsal<>0 ";
		}
		if(!empty($as_conceptop2))
		{
			if(!empty($as_conceptoreporte))
			{
				$ls_criterio = $ls_criterio." AND (sno_hsalida.tipsal='A' OR sno_hsalida.tipsal='V1' OR sno_hsalida.tipsal='W1' OR ".
											"      sno_hsalida.tipsal='D' OR sno_hsalida.tipsal='V2' OR sno_hsalida.tipsal='W2' OR ".
											"      sno_hsalida.tipsal='P1' OR sno_hsalida.tipsal='V3' OR sno_hsalida.tipsal='W3' OR ".
											"	   sno_hsalida.tipsal='P2' OR sno_hsalida.tipsal='V4' OR sno_hsalida.tipsal='W4' OR ".
											"  	   sno_hsalida.tipsal='R')";
			}
			else
			{
				$ls_criterio = $ls_criterio." AND (sno_hsalida.tipsal='A' OR sno_hsalida.tipsal='V1' OR sno_hsalida.tipsal='W1' OR ".
											"      sno_hsalida.tipsal='D' OR sno_hsalida.tipsal='V2' OR sno_hsalida.tipsal='W2' OR ".
											"      sno_hsalida.tipsal='P1' OR sno_hsalida.tipsal='V3' OR sno_hsalida.tipsal='W3' OR ".
											"	   sno_hsalida.tipsal='P2' OR sno_hsalida.tipsal='V4' OR sno_hsalida.tipsal='W4') ";
			}
		}
		else
		{
			if(!empty($as_conceptoreporte))
			{
				$ls_criterio = $ls_criterio." AND (sno_hsalida.tipsal='A' OR sno_hsalida.tipsal='V1' OR sno_hsalida.tipsal='W1' OR ".
											"      sno_hsalida.tipsal='D' OR sno_hsalida.tipsal='V2' OR sno_hsalida.tipsal='W2' OR ".
											"      sno_hsalida.tipsal='P1' OR sno_hsalida.tipsal='V3' OR sno_hsalida.tipsal='W3' OR ".
											"  	   sno_hsalida.tipsal='R')";
			}
			else
			{
				$ls_criterio = $ls_criterio." AND (sno_hsalida.tipsal='A' OR sno_hsalida.tipsal='V1' OR sno_hsalida.tipsal='W1' OR ".
											"      sno_hsalida.tipsal='D' OR sno_hsalida.tipsal='V2' OR sno_hsalida.tipsal='W2' OR ".
											"      sno_hsalida.tipsal='P1' OR sno_hsalida.tipsal='V3' OR sno_hsalida.tipsal='W3')";
			}
		}
		$ls_sql="SELECT sno_hconcepto.codconc, ".$ls_campo." as nomcon, ".$ls_campomonto.", sno_hsalida.tipsal, abs(sno_hconceptopersonal.acuemp) AS acuemp, ".
				"		abs(sno_hconceptopersonal.acupat) AS acupat , sno_hconcepto.repacucon,  sno_hconcepto.repconsunicon, sno_hconcepto.consunicon, ".
				"		(SELECT moncon FROM sno_hconstantepersonal ".
				"		  WHERE sno_hconcepto.repconsunicon='1' ".
				"			AND sno_hconstantepersonal.codper = '".$as_codper."' ".
				"			AND sno_hconstantepersonal.codemp = sno_hconcepto.codemp ".
				"			AND sno_hconstantepersonal.codnom = sno_hconcepto.codnom ".
				"			AND sno_hconstantepersonal.anocur = sno_hconcepto.anocur ".
				"			AND sno_hconstantepersonal.codperi = sno_hconcepto.codperi ".
				"			AND sno_hconstantepersonal.codcons = sno_hconcepto.consunicon ) AS unidad ".
				"  FROM sno_hsalida, sno_hconcepto, sno_hconceptopersonal ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."'".
				"   AND sno_hsalida.codper='".$as_codper."'".
				"   ".$ls_criterio.
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hsalida.codemp = sno_hconceptopersonal.codemp ".
				"   AND sno_hsalida.codnom = sno_hconceptopersonal.codnom ".
				"   AND sno_hsalida.anocur = sno_hconceptopersonal.anocur ".
				"   AND sno_hsalida.codperi = sno_hconceptopersonal.codperi ".
				"   AND sno_hsalida.codconc = sno_hconceptopersonal.codconc ".
				"   AND sno_hsalida.codper = sno_hconceptopersonal.codper ".
				" ORDER BY sno_hsalida.tipsal ";
		$this->rs_data_detalle=$this->io_sql->select($ls_sql);
		if($this->rs_data_detalle===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_recibopago_conceptopersonal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_recibopago_conceptopersonal
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_listadoconcepto_conceptos($as_codconcdes,$as_codconchas,$as_codperdes,$as_codperhas,$as_coduniadm,$as_conceptocero,
										  $as_subnomdes,$as_subnomhas,$as_codente)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_listadoconcepto_conceptos
		//         Access: public (desde la clase sigesp_sno_rpp_listadoconceptos)  
		//	    Arguments: as_codconcdes // C�digo del concepto donde se empieza a filtrar
		//				   as_codconchas // C�digo del concepto donde se termina de filtrar
		//				   as_codperdes // C�digo del personal donde se empieza a filtrar
		//	  			   as_codperhas // C�digo del personal donde se termina de filtrar		  
		//	  			   as_coduniadm // C�digo de la unidad administrativa que se desea filtrar
		//	  			   as_conceptocero // criterio que me indica si se desea quitar los conceptos que tienen monto cero
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: funci�n que busca la informaci�n de los conceptos que se calcularon en la n�mina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 03/02/2006 								Fecha �ltima Modificaci�n : 
		// SE MODIFICARON LAS TABLAS TH POR H
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		if(!empty($as_codconcdes))
		{
			$ls_criterio= "AND sno_hconcepto.codconc>='".$as_codconcdes."'";
		}
		if(!empty($as_codconchas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hconcepto.codconc<='".$as_codconchas."'";
		}
		if(!empty($as_codperdes))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codper>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codper<='".$as_codperhas."'";
		}
		if(!empty($as_conceptocero))
		{
			$ls_criterio = $ls_criterio."   AND sno_hsalida.valsal<>0 ";
		}
		if(!empty($as_subnomdes))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codsubnom>='".$as_subnomdes."'";
		}
		if(!empty($as_subnomhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codsubnom<='".$as_subnomhas."'";
		}
		if(!empty($as_coduniadm))
		{
			$ls_criterio = $ls_criterio."   AND sno_hpersonalnomina.minorguniadm='".substr($as_coduniadm,0,4)."' ";
			$ls_criterio = $ls_criterio."   AND sno_hpersonalnomina.ofiuniadm='".substr($as_coduniadm,5,2)."' ";
			$ls_criterio = $ls_criterio."   AND sno_hpersonalnomina.uniuniadm='".substr($as_coduniadm,8,2)."' ";
			$ls_criterio = $ls_criterio."   AND sno_hpersonalnomina.depuniadm='".substr($as_coduniadm,11,2)."' ";
			$ls_criterio = $ls_criterio."   AND sno_hpersonalnomina.prouniadm='".substr($as_coduniadm,14,2)."' ";
		}
		if(!empty($as_codente))
		{
			$ls_criterio= $ls_criterio." AND sno_hconcepto.codente='".$as_codente."'";
		}
		$ls_sql="SELECT sno_hconcepto.codconc, sno_hconcepto.nomcon, count(sno_hsalida.codper) as total, sum(sno_hsalida.valsal) as monto ".
				"  FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, sno_hresumen ".
				" WHERE sno_hpersonalnomina.codemp='".$this->ls_codemp."' ".
				"   AND sno_hpersonalnomina.codnom='".$this->ls_codnom."' ".
				"   AND sno_hpersonalnomina.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hpersonalnomina.codperi='".$this->ls_peractnom."' ".
				"   AND sno_hresumen.monnetres > 0 ".
				"   AND (sno_hsalida.tipsal='A' OR sno_hsalida.tipsal='V1' OR sno_hsalida.tipsal='W1' OR ".
				"		 sno_hsalida.tipsal='D' OR sno_hsalida.tipsal='V2' OR sno_hsalida.tipsal='W2' OR ".
				"		 sno_hsalida.tipsal='P1' OR sno_hsalida.tipsal='V3' OR sno_hsalida.tipsal='W3') ".
				"   ".$ls_criterio." ".
				"   AND sno_hsalida.codemp = sno_hresumen.codemp ".
				"   AND sno_hsalida.codnom = sno_hresumen.codnom ".
				"   AND sno_hsalida.anocur = sno_hresumen.anocur ".
				"   AND sno_hsalida.codperi = sno_hresumen.codperi ".
				"   AND sno_hsalida.codper = sno_hresumen.codper ".
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				" GROUP BY sno_hconcepto.codconc, sno_hconcepto.nomcon ".
				" ORDER BY sno_hconcepto.codconc ";
		$this->rs_data=$this->io_sql->select($ls_sql);
		if($this->rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_listadoconcepto_conceptos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_listadoconcepto_conceptos
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_listadoconcepto_personalconcepto($as_codconc,$as_codperdes,$as_codperhas,$as_conceptocero,$as_coduniadm,$as_subnomdes,$as_subnomhas,$as_orden)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_listadoconcepto_personalconcepto
		//		   Access: public (desde la clase sigesp_sno_rpp_listadonomina)  
		//	    Arguments: as_codconc // C�digo del concepto del que se desea busca el personal
		//				   as_codperdes // C�digo del personal donde se empieza a filtrar
		//	  			   as_codperhas // C�digo del personal donde se termina de filtrar		  
		//	  			   as_conceptocero // criterio que me indica si se desea quitar los conceptos que tienen monto cero
		//	  			   as_orden // orden por medio del cual se desea que salga el reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: funci�n que busca la informaci�n del personal asociado al concepto que se le calcul� la n�mina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 03/02/2006 								Fecha �ltima Modificaci�n : 
		// SE MODIFICARON LAS TABLAS TH POR H
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		if(!empty($as_codperdes))
		{
			$ls_criterio= "AND sno_hpersonalnomina.codper>='".$as_codperdes."'";
		}
		if(!empty($as_subnomdes))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codsubnom>='".$as_subnomdes."'";
		}
		if(!empty($as_subnomhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codsubnom<='".$as_subnomhas."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codper<='".$as_codperhas."'";
		}
		if(!empty($as_conceptocero))
		{
			$ls_criterio = $ls_criterio."   AND sno_hsalida.valsal<>0 ";
		}
		if(!empty($as_coduniadm))
		{
			$ls_criterio = $ls_criterio."   AND sno_hpersonalnomina.minorguniadm='".substr($as_coduniadm,0,4)."' ";
			$ls_criterio = $ls_criterio."   AND sno_hpersonalnomina.ofiuniadm='".substr($as_coduniadm,5,2)."' ";
			$ls_criterio = $ls_criterio."   AND sno_hpersonalnomina.uniuniadm='".substr($as_coduniadm,8,2)."' ";
			$ls_criterio = $ls_criterio."   AND sno_hpersonalnomina.depuniadm='".substr($as_coduniadm,11,2)."' ";
			$ls_criterio = $ls_criterio."   AND sno_hpersonalnomina.prouniadm='".substr($as_coduniadm,14,2)."' ";
		}
		switch($as_orden)
		{
			case "1": // Ordena por C�digo de personal
				$ls_orden="ORDER BY sno_componente.codcom, sno_rango.codran, sno_personal.codper ";
				break;

			case "2": // Ordena por Apellido de personal
				$ls_orden="ORDER BY sno_componente.codcom, sno_rango.codran, sno_personal.apeper ";
				break;

			case "3": // Ordena por Nombre de personal
				$ls_orden="ORDER BY sno_componente.codcom, sno_rango.codran, sno_personal.nomper ";
				break;

			case "4": // Ordena por C�dula de personal
				$ls_orden="ORDER BY sno_componente.codcom, sno_rango.codran, sno_personal.cedper ";
				break;
		}
		if($this->li_rac=="1")// Utiliza RAC
		{
			$ls_descar="       (SELECT denasicar FROM sno_hasignacioncargo ".
					   "   	     WHERE sno_hpersonalnomina.codemp = sno_hasignacioncargo.codemp ".
					   "		   AND sno_hpersonalnomina.codnom = sno_hasignacioncargo.codnom ".
					   "		   AND sno_hpersonalnomina.anocur = sno_hasignacioncargo.anocur ".
					   "		   AND sno_hpersonalnomina.codperi = sno_hasignacioncargo.codperi ".
				       "           AND sno_hpersonalnomina.codasicar = sno_hasignacioncargo.codasicar) as descar, ";
		}
		else// No utiliza RAC
		{
			$ls_descar="       (SELECT descar FROM sno_hcargo ".
					   "   	     WHERE sno_hpersonalnomina.codemp = sno_hcargo.codemp ".
					   "		   AND sno_hpersonalnomina.codnom = sno_hcargo.codnom ".
					   "		   AND sno_hpersonalnomina.anocur = sno_hcargo.anocur ".
					   "		   AND sno_hpersonalnomina.codperi = sno_hcargo.codperi ".
				       "           AND sno_hpersonalnomina.codcar = sno_hcargo.codcar) as descar, ";
		}
		$ls_sql="SELECT sno_personal.cedper, sno_personal.apeper, sno_personal.nomper, sno_hsalida.valsal, ".$ls_descar.
				"       sno_componente.descom, sno_rango.desran                                             ".
				"   FROM sno_personal                                                                       ".
				"   JOIN sno_hpersonalnomina ON (sno_hpersonalnomina.codemp=sno_personal.codemp           ".
				"							 AND  sno_hpersonalnomina.codper=sno_personal.codper)          ".
				"   JOIN sno_hsalida ON (sno_hpersonalnomina.codemp = sno_hsalida.codemp                 ".        
				"				     AND sno_hpersonalnomina.codnom = sno_hsalida.codnom                  ".
				"			         AND sno_hpersonalnomina.anocur = sno_hsalida.anocur                  ".
				"			         AND sno_hpersonalnomina.codperi = sno_hsalida.codperi                ".
				"			         AND sno_hpersonalnomina.codper = sno_hsalida.codper)                 ".
				"   LEFT JOIN sno_componente ON (sno_componente.codemp=sno_personal.codemp                  ".
				"						    AND  sno_componente.codcom=sno_personal.codcom)                 ".
				"   LEFT JOIN sno_rango ON (sno_rango.codemp=sno_personal.codemp                            ".
				"					   AND sno_rango.codcom=sno_personal.codcom                             ".
				"					   AND sno_rango.codran=sno_personal.codran)                            ".
				" WHERE sno_hpersonalnomina.codemp='".$this->ls_codemp."' ".
				"   AND sno_hpersonalnomina.codnom='".$this->ls_codnom."' ".
				"   AND sno_hpersonalnomina.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hpersonalnomina.codperi='".$this->ls_peractnom."' ".
				"   AND sno_hsalida.codconc='".$as_codconc."' ".
				"   AND (sno_hsalida.tipsal='A' OR sno_hsalida.tipsal='V1' OR sno_hsalida.tipsal='W1' OR ".
				"		 sno_hsalida.tipsal='D' OR sno_hsalida.tipsal='V2' OR sno_hsalida.tipsal='W2' OR ".
				"		 sno_hsalida.tipsal='P1' OR sno_hsalida.tipsal='V3' OR sno_hsalida.tipsal='W3') ".
				"   ".$ls_criterio.$ls_orden;
		$this->rs_data_detalle=$this->io_sql->select($ls_sql);
		if($this->rs_data_detalle===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_listadoconcepto_personalconcepto ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;

	}// end function uf_listadoconcepto_personalconcepto
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_listadopersonalcheque_unidad($as_codban,$as_suspendidos,$as_subnomdes,$as_subnomhas)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_listadopersonalcheque_unidad
		//		   Access: public (desde la clase sigesp_sno_rpp_listadopersonalcheque)  
		//	    Arguments: as_codban // C�digo del banco del que se desea busca el personal
		//	    		   as_suspendidos // si se busca a toto del personal � solo los activos
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: funci�n que busca la informaci�n de las personas que cobran con cheque
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 02/05/2006 								Fecha �ltima Modificaci�n :  
		// SE MODIFICARON LAS TABLAS TH POR H
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		if(!empty($as_codban))
		{
			$ls_criterio = $ls_criterio." AND sno_hpersonalnomina.codban='".$as_codban."' ";
		}
		if($as_suspendidos=="1") // Mostrar solo el personal suspendido
		{
			$ls_criterio = $ls_criterio." AND (sno_hpersonalnomina.staper='1' OR sno_hpersonalnomina.staper='2')";
		}
		if(!empty($as_subnomdes))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codsubnom>='".$as_subnomdes."'";
		}
		if(!empty($as_subnomhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codsubnom<='".$as_subnomhas."'";
		}
		$ls_sql="SELECT sno_hunidadadmin.minorguniadm, sno_hunidadadmin.ofiuniadm, sno_hunidadadmin.uniuniadm, ".
				"   	sno_hunidadadmin.depuniadm, sno_hunidadadmin.prouniadm, sno_hunidadadmin.desuniadm ".
				"  FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hresumen ".
				" WHERE sno_hpersonalnomina.codemp='".$this->ls_codemp."' ".
				"   AND sno_hpersonalnomina.codnom='".$this->ls_codnom."' ".
				"   AND sno_hpersonalnomina.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hpersonalnomina.codperi='".$this->ls_peractnom."' ".
				"   AND sno_hpersonalnomina.pagefeper=1 ".
				"   AND sno_hpersonalnomina.pagbanper=0 ".
				"   AND sno_hpersonalnomina.pagtaqper=0 ".
				"   AND sno_hresumen.monnetres > 0 ".
				"     ".$ls_criterio.
				"	AND sno_hpersonalnomina.codemp = sno_hresumen.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hresumen.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hresumen.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hresumen.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hresumen.codper ".
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				" GROUP BY sno_hunidadadmin.minorguniadm, sno_hunidadadmin.ofiuniadm, sno_hunidadadmin.uniuniadm, ".
				"   	    sno_hunidadadmin.depuniadm, sno_hunidadadmin.prouniadm, sno_hunidadadmin.desuniadm ".
				" ORDER BY sno_hunidadadmin.minorguniadm, sno_hunidadadmin.ofiuniadm, sno_hunidadadmin.uniuniadm, ".
				"   	    sno_hunidadadmin.depuniadm, sno_hunidadadmin.prouniadm ";
		$this->rs_data=$this->io_sql->select($ls_sql);
		if($this->rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_listadopersonalcheque_unidad ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_listadopersonalcheque_unidad
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_listadopersonalcheque_personal($as_codban,$as_minorguniadm,$as_ofiuniadm,$as_uniuniadm,$as_depuniadm,
											   $as_prouniadm,$as_suspendidos,$as_quincena,$as_subnomdes,$as_subnomhas,$as_orden)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_listadopersonalcheque_personal
		//		   Access: public (desde la clase sigesp_sno_rpp_listadopersonalcheque)  
		//	    Arguments: as_codban // C�digo del banco del que se desea busca el personal
		//	    		   as_minorguniadm // C�digo del Ministerio � Organismo
		//	    		   as_ofiuniadm // C�digo de la Oficina
		//	    		   as_uniuniadm // C�digo de la Unidad
		//	    		   as_depuniadm // C�digo del departamento
		//	    		   as_prouniadm // C�digo del programa
		//	    		   as_suspendidos // si se busca a toto del personal � solo los activos
		//	    		   as_quincena // quincena que se quiere mostrar
		//	  			   as_orden // Orden del reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: funci�n que busca la informaci�n de las personas que tienen asociado el banco y la unidad administrativa
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 02/05/2006 								Fecha �ltima Modificaci�n :  
		// SE MODIFICARON LAS TABLAS TH POR H
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		$ls_monto="";
		switch($as_quincena)
		{
			case 1: // Primera Quincena
				$ls_monto="sno_hresumen.priquires as monnetres";
				break;

			case 2: // Segunda Quincena
				$ls_monto="sno_hresumen.segquires as monnetres";
				break;

			case 3: // Mes Completo
				$ls_monto="sno_hresumen.monnetres as monnetres";
				break;
		}
		if(!empty($as_subnomdes))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codsubnom>='".$as_subnomdes."'";
		}
		if(!empty($as_subnomhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codsubnom<='".$as_subnomhas."'";
		}
		if(!empty($as_codban))
		{
			$ls_criterio = $ls_criterio." AND sno_hpersonalnomina.codban='".$as_codban."' ";
		}
		if($as_suspendidos=="1") // Mostrar solo el personal suspendido
		{
			$ls_criterio = $ls_criterio." AND (sno_hpersonalnomina.staper='1' OR sno_hpersonalnomina.staper='2')";
		}
		switch($as_orden)
		{
			case "1": // Ordena por C�digo del Personal
				$ls_orden="ORDER BY sno_personal.codper ";
				break;

			case "2": // Ordena por Apellido del Personal
				$ls_orden="ORDER BY sno_personal.apeper ";
				break;

			case "3": // Ordena por Nombre del Personal
				$ls_orden="ORDER BY sno_personal.nomper ";
				break;

			case "4": // Ordena por C�dula del Personal
				$ls_orden="ORDER BY sno_personal.cedper ";
				break;
		}
		$ls_sql="SELECT sno_personal.cedper, sno_personal.nomper, sno_personal.apeper, ".$ls_monto." ".
				"  FROM sno_personal, sno_hpersonalnomina, sno_hresumen ".
				" WHERE sno_hpersonalnomina.codemp='".$this->ls_codemp."' ".
				"   AND sno_hpersonalnomina.codnom='".$this->ls_codnom."' ".
				"   AND sno_hpersonalnomina.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hpersonalnomina.codperi='".$this->ls_peractnom."' ".
				"   AND sno_hpersonalnomina.pagefeper=1 ".
				"   AND sno_hpersonalnomina.pagbanper=0 ".
				"   AND sno_hpersonalnomina.pagtaqper=0 ".
				"   AND sno_hresumen.monnetres > 0 ".
				"	AND sno_hpersonalnomina.minorguniadm = '".$as_minorguniadm."' ".
				"   AND sno_hpersonalnomina.ofiuniadm = '".$as_ofiuniadm."' ".
				"   AND sno_hpersonalnomina.uniuniadm = '".$as_uniuniadm."' ".
				"   AND sno_hpersonalnomina.depuniadm = '".$as_depuniadm."' ".
				"   AND sno_hpersonalnomina.prouniadm = '".$as_prouniadm."' ".
				"	".$ls_criterio.
				"	AND sno_hpersonalnomina.codemp = sno_hresumen.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hresumen.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hresumen.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hresumen.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hresumen.codper ".
				"   AND sno_personal.codemp = sno_hpersonalnomina.codemp ".
				"	AND sno_personal.codper = sno_hpersonalnomina.codper ".
				$ls_orden;
		$this->rs_data_detalle=$this->io_sql->select($ls_sql);
		if($this->rs_data_detalle===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_listadopersonalcheque_personal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_listadopersonalcheque_personal
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_listadobanco_banco($as_codban,$as_suspendidos,$as_sc_cuenta,$as_ctaban,$as_subnomdes,$as_subnomhas,$as_codperdes,$as_codperhas,$pago_otros_bancos='')
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_listadobanco_banco
		//		   Access: public (desde la clase sigesp_sno_rpp_listadobanco)  
		//	    Arguments: as_codban // C�digo del banco del que se desea busca el personal
		//	    		   as_suspendidos // si se busca a toto del personal � solo los activos
		//	    		   as_sc_cuenta // cuenta contable del banco
		//	    		   as_ctaban // cuenta del banco
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: funci�n que busca la informaci�n del banco seleccionado
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 03/05/2006 								Fecha �ltima Modificaci�n :  
		// SE MODIFICARON LAS TABLAS TH POR H
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		if(!empty($as_codban) && (empty($pago_otros_bancos) || $pago_otros_bancos===false))
		{
			$ls_criterio = $ls_criterio." AND sno_hpersonalnomina.codban='".$as_codban."' ";
		}
		if($as_suspendidos=="1") // Mostrar solo el personal suspendido
		{
			$ls_criterio = $ls_criterio." AND (sno_hpersonalnomina.staper='1' OR sno_hpersonalnomina.staper='2')";
		}
		if(!empty($as_subnomdes))
		{
			$ls_criterio= $ls_criterio."    AND sno_hpersonalnomina.codsubnom>='".$as_subnomdes."'";
		}
		if(!empty($as_subnomhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codsubnom<='".$as_subnomhas."'";
		}
		if(!empty($as_codperdes))
		{
			$ls_criterio= $ls_criterio."    AND sno_hpersonalnomina.codper>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codper<='".$as_codperhas."'";
		}
		$ls_sql="SELECT scb_banco.codban, scb_banco.nomban ".
				"  FROM sno_hpersonalnomina, sno_hresumen, scb_banco  ".
				" WHERE sno_hpersonalnomina.codemp='".$this->ls_codemp."' ".
				"   AND sno_hpersonalnomina.codnom='".$this->ls_codnom."' ".
				"   AND sno_hpersonalnomina.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hpersonalnomina.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hpersonalnomina.pagbanper=1 OR sno_hpersonalnomina.pagtaqper=1) ".
				"   AND sno_hresumen.monnetres > 0".
				"   ".$ls_criterio.
				"   AND sno_hpersonalnomina.codemp = sno_hresumen.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hresumen.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hresumen.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hresumen.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hresumen.codper ".
				"   AND sno_hpersonalnomina.codemp = scb_banco.codemp ".
				"   AND sno_hpersonalnomina.codban = scb_banco.codban ".
				" GROUP BY scb_banco.codban, scb_banco.nomban ".
				" ORDER BY scb_banco.nomban ";
		$this->rs_data=$this->io_sql->select($ls_sql);
		if($this->rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_listadobanco_banco ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if(!$this->rs_data->EOF)
			{
				$lb_valido=$this->uf_update_banco($as_codban,$as_sc_cuenta,$as_ctaban);	
			}
		}		
		return $lb_valido;
	}// end function uf_listadobanco_banco
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_update_banco($as_codban,$as_sc_cuenta,$as_ctaban)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_update_banco
		//		   Access: private
		//	    Arguments: as_codban  // c�digo de cargo
		//	    		   as_sc_cuenta // cuenta contable del banco
		//	    		   as_ctaban // cuenta del banco
		//	      Returns: lb_valido True si se ejecuto el update � False si hubo error en el update
		//	  Description: Funcion que actualiza si se gener� el listado al banco
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 11/05/2006 								Fecha �ltima Modificaci�n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="DELETE ".
				"  FROM sno_banco ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codnom='".$this->ls_codnom."' ".
				"   AND codperi='".$this->ls_peractnom."' ".
				"   AND codban='".$as_codban."'";
		$this->io_sql->begin_transaction();
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
 			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_update_banco ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$this->io_sql->rollback();
		}
		else
		{
			$ls_sql="INSERT INTO sno_banco(codemp,codnom,codperi,codban,codcueban,codcuecon) VALUES ('".$this->ls_codemp."',".
					"'".$this->ls_codnom."','".$this->ls_peractnom."','".$as_codban."','".$as_ctaban."','".$as_sc_cuenta."')";
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$lb_valido=false;
				$this->io_mensajes->message("CLASE->Report M�TODO->uf_update_banco ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
				$this->io_sql->rollback();
			}
			else
			{
				$this->io_sql->commit();
			}
		}
		return $lb_valido;
	}// end function uf_update_banco
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_listadobanco_personal($as_codban,$as_suspendidos,$as_tipcueban,$as_quincena,$as_subnomdes,$as_subnomhas,$as_codperdes,$as_codperhas,$as_orden)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_listadobanco_personal
		//		   Access: public (desde la clase sigesp_sno_rpp_listadobanco)  
		//	    Arguments: as_codban // C�digo del banco del que se desea busca el personal
		//	    		   as_suspendidos // si se busca a toto del personal � solo los activos
		//	    		   as_tipcueban // tipo de cuenta bancaria (Ahorro,  Corriente, Activos liquidos)
		//	  			   as_quincena // Quincena para el cual se quiere filtrar
		//	  			   as_orden // Orden del reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: funci�n que busca la informaci�n de las personas que tienen asociado el banco 
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 03/05/2006 								Fecha �ltima Modificaci�n :  
		// SE MODIFICARON LAS TABLAS TH POR H
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		$ls_monto="";
		switch($as_quincena)
		{
			case 1: // Primera Quincena
				$ls_monto="sno_hresumen.priquires as monnetres";
				break;

			case 2: // Segunda Quincena
				$ls_monto="sno_hresumen.segquires as monnetres";
				break;

			case 3: // Mes Completo
				$ls_monto="sno_hresumen.monnetres as monnetres";
				break;
		}
		if(!empty($as_codban))
		{
			$ls_criterio = $ls_criterio." AND sno_hpersonalnomina.codban='".$as_codban."' ";
		}
		if($as_suspendidos=="1") // Mostrar solo el personal suspendido
		{
			$ls_criterio = $ls_criterio." AND (sno_hpersonalnomina.staper='1' OR sno_hpersonalnomina.staper='2')";
		}
		if(!empty($as_subnomdes))
		{
			$ls_criterio= $ls_criterio."    AND sno_hpersonalnomina.codsubnom>='".$as_subnomdes."'";
		}
		if(!empty($as_subnomhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codsubnom<='".$as_subnomhas."'";
		}
		switch($as_tipcueban)
		{
			case "A": // Cuenta de Ahorro
				$ls_criterio = $ls_criterio." AND sno_hpersonalnomina.tipcuebanper='A' ";
				break;
				
			case "C": // Cuenta corriente
				$ls_criterio = $ls_criterio." AND sno_hpersonalnomina.tipcuebanper='C' ";
				break;

			case "L": // Cuenta Activos L�quidos
				$ls_criterio = $ls_criterio." AND sno_hpersonalnomina.tipcuebanper='L' ";
				break;
		}
		if(!empty($as_codperdes))
		{
			$ls_criterio= $ls_criterio."    AND sno_hpersonalnomina.codper>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codper<='".$as_codperhas."'";
		}
		switch($as_orden)
		{
			case "1": // Ordena por C�digo del Personal
				$ls_orden="ORDER BY sno_personal.codper ";
				break;

			case "2": // Ordena por Apellido del Personal
				$ls_orden="ORDER BY sno_personal.apeper ";
				break;

			case "3": // Ordena por Nombre del Personal
				$ls_orden="ORDER BY sno_personal.nomper ";
				break;

			case "4": // Ordena por C�dula del Personal
				$ls_orden="ORDER BY sno_personal.cedper ";
				break;
				
			case "5": // Ordena por Rango del Personal
				$ls_orden="ORDER BY  sno_personal.codran, sno_personal.codcom DESC";
				break;
		}
		$ls_sql="SELECT sno_personal.cedper, sno_personal.nomper, sno_personal.apeper, ".$ls_monto.", sno_hpersonalnomina.codcueban, sno_personal.codran, ".
				 "		 (SELECT sno_rango.desran FROM sno_rango ".
				 "        WHERE sno_rango.codemp='".$this->ls_codemp."'".
				 "          AND sno_rango.codcom=sno_personal.codcom".
				 "          AND sno_rango.codran=sno_personal.codran) AS denran ".
				"  FROM sno_personal, sno_hpersonalnomina, sno_hresumen  ".
				" WHERE sno_hpersonalnomina.codemp='".$this->ls_codemp."' ".
				"   AND sno_hpersonalnomina.codnom='".$this->ls_codnom."' ".
				"   AND sno_hpersonalnomina.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hpersonalnomina.codperi='".$this->ls_peractnom."' ".
				"   AND sno_hpersonalnomina.pagbanper=1 ".
				"   AND sno_hpersonalnomina.pagefeper=0 ".
				"   AND sno_hpersonalnomina.pagtaqper=0 ".
				"   AND sno_hresumen.monnetres > 0 ".
				"	".$ls_criterio.
				"   AND sno_personal.codemp = sno_hpersonalnomina.codemp ".
				"	AND sno_personal.codper = sno_hpersonalnomina.codper ".
				"	AND sno_hpersonalnomina.codemp = sno_hresumen.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hresumen.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hresumen.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hresumen.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hresumen.codper ".
				$ls_orden;
		$this->rs_data_detalle=$this->io_sql->select($ls_sql);
		if($this->rs_data_detalle===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_listadobanco_personal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_listadobanco_personal
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_listadobancotaquilla_personal($as_codban,$as_suspendidos,$as_quincena,$as_subnomdes,$as_subnomhas,$as_codperdes,$as_codperhas,$as_orden)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_listadobancotaquilla_personal
		//		   Access: public (desde la clase sigesp_sno_rpp_listadobanco)  
		//	    Arguments: as_codban // C�digo del banco del que se desea busca el personal
		//	    		   as_suspendidos // si se busca a toto del personal � solo los activos
		//	    		   as_tipcueban // tipo de cuenta bancaria (Ahorro,  Corriente, Activos liquidos)
		//	  			   as_quincena // Quincena para el cual se quiere filtrar
		//	  			   as_orden // Orden del reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: funci�n que busca la informaci�n de las personas que tienen asociado el banco 
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 03/05/2006 								Fecha �ltima Modificaci�n :  
		// SE MODIFICARON LAS TABLAS TH POR H
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		$ls_monto="";
		switch($as_quincena)
		{
			case 1: // Primera Quincena
				$ls_monto="sno_hresumen.priquires as monnetres";
				break;

			case 2: // Segunda Quincena
				$ls_monto="sno_hresumen.segquires as monnetres";
				break;

			case 3: // Mes Completo
				$ls_monto="sno_hresumen.monnetres as monnetres";
				break;
		}
		if(!empty($as_codban))
		{
			$ls_criterio = $ls_criterio." AND sno_hpersonalnomina.codban='".$as_codban."' ";
		}
		if($as_suspendidos=="1") // Mostrar solo el personal suspendido
		{
			$ls_criterio = $ls_criterio." AND (sno_hpersonalnomina.staper='1' OR sno_hpersonalnomina.staper='2')";
		}
		if(!empty($as_subnomdes))
		{
			$ls_criterio= $ls_criterio."    AND sno_hpersonalnomina.codsubnom>='".$as_subnomdes."'";
		}
		if(!empty($as_subnomhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codsubnom<='".$as_subnomhas."'";
		}
		if(!empty($as_codperdes))
		{
			$ls_criterio= $ls_criterio."    AND sno_hpersonalnomina.codper>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codper<='".$as_codperhas."'";
		}
		switch($as_orden)
		{
			case "1": // Ordena por C�digo del Personal
				$ls_orden="ORDER BY sno_personal.codper ";
				break;

			case "2": // Ordena por Apellido del Personal
				$ls_orden="ORDER BY sno_personal.apeper ";
				break;

			case "3": // Ordena por Nombre del Personal
				$ls_orden="ORDER BY sno_personal.nomper ";
				break;

			case "4": // Ordena por C�dula del Personal
				$ls_orden="ORDER BY sno_personal.cedper ";
				break;
				
			case "5": // Ordena por Rango del Personal
				$ls_orden="ORDER BY  sno_personal.codran, sno_personal.codcom DESC";
				break;
		}
		$ls_sql="SELECT sno_personal.cedper, sno_personal.nomper, sno_personal.apeper, ".$ls_monto.", sno_hpersonalnomina.codcueban  , sno_personal.codran, ".
				 "		 (SELECT sno_rango.desran FROM sno_rango ".
				 "        WHERE sno_rango.codemp='".$this->ls_codemp."'".
				 "          AND sno_rango.codcom=sno_personal.codcom".
				 "          AND sno_rango.codran=sno_personal.codran) AS denran ".
				"  FROM sno_personal, sno_hpersonalnomina, sno_hresumen  ".
				" WHERE sno_hpersonalnomina.codemp='".$this->ls_codemp."' ".
				"   AND sno_hpersonalnomina.codnom='".$this->ls_codnom."' ".
				"   AND sno_hpersonalnomina.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hpersonalnomina.codperi='".$this->ls_peractnom."' ".
				"   AND sno_hpersonalnomina.pagbanper=0 ".
				"   AND sno_hpersonalnomina.pagefeper=0 ".
				"   AND sno_hpersonalnomina.pagtaqper=1 ".
				"   AND sno_hresumen.monnetres > 0 ".
				"	".$ls_criterio.
				"   AND sno_personal.codemp = sno_hpersonalnomina.codemp ".
				"	AND sno_personal.codper = sno_hpersonalnomina.codper ".
				"	AND sno_hpersonalnomina.codemp = sno_hresumen.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hresumen.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hresumen.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hresumen.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hresumen.codper ".
				$ls_orden;
		$this->rs_data_detalle=$this->io_sql->select($ls_sql);
		if($this->rs_data_detalle===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_listadobancotaquilla_personal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_listadobancotaquilla_personal
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_aportepatronal_personal($as_codconc,$as_conceptocero,$as_subnomdes,$as_subnomhas,$as_orden)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_aportepatronal_personal
		//		   Access: public (desde la clase sigesp_sno_rpp_listadonomina)  
		//	    Arguments: as_codconc // C�digo del concepto del que se desea busca el personal
		//	  			   as_conceptocero // concepto cero
		//	  			   as_orden // orden por medio del cual se desea que salga el reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: funci�n que busca la informaci�n de las personas que tienen asociado el concepto	de tipo aporte patronal 
		//				   y se calcul� en la n�mina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 19/04/2006 								Fecha �ltima Modificaci�n : 
		// SE MODIFICARON LAS TABLAS TH POR H
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		$ls_group=",";
		if(!empty($as_codconc))
		{
			$ls_criterio = $ls_criterio." AND sno_hsalida.codconc='".$as_codconc."' ";
		}
		if(!empty($as_conceptocero))
		{
			$ls_criterio = $ls_criterio." AND sno_hsalida.valsal<>0 ";
		}
		if(!empty($as_subnomdes))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codsubnom>='".$as_subnomdes."'";
			$ls_group=",sno_hpersonalnomina.codsubnom,";
		}
		if(!empty($as_subnomhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codsubnom<='".$as_subnomhas."'";
			$ls_group=",sno_hpersonalnomina.codsubnom,";
		}
		switch($as_orden)
		{
			case "1": // Ordena por C�digo de personal
				$ls_orden="ORDER BY sno_hpersonalnomina.codper ";
				break;

			case "2": // Ordena por Apellido de personal
				$ls_orden="ORDER BY sno_personal.apeper ";
				break;

			case "3": // Ordena por Nombre de personal
				$ls_orden="ORDER BY sno_personal.nomper ";
				break;

			case "4": // Ordena por C�dula de personal
				$ls_orden="ORDER BY sno_personal.cedper ";
				break;
		}
		$ls_sql="SELECT sno_personal.cedper, sno_personal.apeper, sno_personal.nomper, count(sno_personal.cedper) as total, ".
				"       (SELECT SUM(valsal) ".
				"		   FROM sno_hsalida ".
				"   	  WHERE (sno_hsalida.tipsal='P1' OR sno_hsalida.tipsal='V3' OR sno_hsalida.tipsal='W3' OR sno_hsalida.tipsal='Q1') ".
				$ls_criterio.
				"           AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   		AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   		AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   		AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   		AND sno_hpersonalnomina.codper = sno_hsalida.codper) as personal, ".
				"       (SELECT SUM(valsal) ".
				"		   FROM sno_hsalida ".
				"   	  WHERE (sno_hsalida.tipsal='P2' OR sno_hsalida.tipsal='V4' OR sno_hsalida.tipsal='W4' OR sno_hsalida.tipsal='Q2') ".
				$ls_criterio.
				"           AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   		AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   		AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   		AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   		AND sno_hpersonalnomina.codper = sno_hsalida.codper) as patron ".
				"  FROM sno_personal, sno_hpersonalnomina, sno_hsalida ".
				" WHERE sno_hpersonalnomina.codemp='".$this->ls_codemp."' ".
				"   AND sno_hpersonalnomina.codnom='".$this->ls_codnom."' ".
				"   AND sno_hpersonalnomina.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hpersonalnomina.codperi='".$this->ls_peractnom."' ".
				$ls_criterio.
				"   AND sno_personal.codemp = sno_hpersonalnomina.codemp ".
				"   AND sno_personal.codper = sno_hpersonalnomina.codper ".
				"	AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"	AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"	AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"	AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"	AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				" GROUP BY sno_hpersonalnomina.codemp, sno_hpersonalnomina.codnom, sno_hpersonalnomina.anocur, sno_hpersonalnomina.codperi ".$ls_group." ".
				"		   sno_hpersonalnomina.codper, sno_personal.cedper, sno_personal.apeper, ".
				"		   sno_personal.nomper, sno_hsalida.codemp, sno_hsalida.codnom, sno_hsalida.codperi, sno_hsalida.codper   ".
				"   ".$ls_orden;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_aportepatronal_personal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);			
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_aportepatronal_personal
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_resumenconcepto_conceptos($as_codconcdes,$as_codconchas,$as_aportepatronal,$as_conceptocero,$as_subnomdes,$as_subnomhas,&$rs_data)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_resumenconcepto_conceptos
		//         Access: public (desde la clase sigesp_sno_rpp_resumenconceptos)  
		//	    Arguments: as_codconcdes // C�digo del concepto donde se empieza a filtrar
		//				   as_codconchas // C�digo del concepto donde se termina de filtrar
		//				   as_aportepatronal // criterio que me indica si se quiere mostrar el aporte patronal
		//	  			   as_conceptocero // criterio que me indica si se desea quitar los conceptos que tienen monto cero
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: funci�n que busca la informaci�n de los conceptos que se calcularon en la n�mina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 27/04/2006 								Fecha �ltima Modificaci�n : 
		// SE MODIFICARON LAS TABLAS TH POR H
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		if(!empty($as_codconcdes))
		{
			$ls_criterio= "AND sno_hconcepto.codconc>='".$as_codconcdes."'";
		}
		if(!empty($as_codconchas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hconcepto.codconc<='".$as_codconchas."'";
		}
		if(!empty($as_conceptocero))
		{
			$ls_criterio = $ls_criterio."   AND sno_hsalida.valsal<>0 ";
		}
		if(!empty($as_subnomdes))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codsubnom>='".$as_subnomdes."'";
		}
		if(!empty($as_subnomhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codsubnom<='".$as_subnomhas."'";
		}
		if(!empty($as_aportepatronal))
		{
			$ls_criterio = $ls_criterio." AND (sno_hsalida.tipsal='A' OR sno_hsalida.tipsal='V1' OR sno_hsalida.tipsal='W1' OR ".
										"      sno_hsalida.tipsal='D' OR sno_hsalida.tipsal='V2' OR sno_hsalida.tipsal='W2' OR ".
										"      sno_hsalida.tipsal='P1' OR sno_hsalida.tipsal='V3' OR sno_hsalida.tipsal='W3' OR ".
										"	   sno_hsalida.tipsal='P2' OR sno_hsalida.tipsal='V4' OR sno_hsalida.tipsal='W4')";
		}
		else
		{
			$ls_criterio = $ls_criterio." AND (sno_hsalida.tipsal='A' OR sno_hsalida.tipsal='V1' OR sno_hsalida.tipsal='W1' OR ".
										"      sno_hsalida.tipsal='D' OR sno_hsalida.tipsal='V2' OR sno_hsalida.tipsal='W2' OR ".
										"      sno_hsalida.tipsal='P1' OR sno_hsalida.tipsal='V3' OR sno_hsalida.tipsal='W3')";
		}
		$ls_sql="SELECT sno_hconcepto.codconc, MAX(sno_hconcepto.nomcon) AS nomcon, sno_hsalida.tipsal, sum(sno_hsalida.valsal) as monto, ".
				"		COUNT(sno_hsalida.codper) AS total, MAX(sno_hconcepto.cueprecon) AS cueprecon, MAX(sno_hconcepto.cueprepatcon) AS cueprepatcon  ".
				"  FROM sno_hsalida, sno_hconcepto, sno_hpersonalnomina ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   ".$ls_criterio." ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hsalida.codemp = sno_hpersonalnomina.codemp ".
				"   AND sno_hsalida.codnom = sno_hpersonalnomina.codnom ".
				"   AND sno_hsalida.anocur = sno_hpersonalnomina.anocur ".
				"   AND sno_hsalida.codperi = sno_hpersonalnomina.codperi ".
				"   AND sno_hsalida.codper = sno_hpersonalnomina.codper ".
				" GROUP BY sno_hconcepto.codconc, sno_hsalida.tipsal ".
				" ORDER BY sno_hconcepto.codconc, sno_hsalida.tipsal ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_resumenconcepto_conceptos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_resumenconcepto_conceptos
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_resumenconceptounidad_unidad($as_codconcdes,$as_codconchas,$as_coduniadm,$as_conceptocero,$as_subnomdes,$as_subnomhas)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_resumenconceptounidad_unidad
		//         Access: public (desde la clase sigesp_sno_r_resumenconceptounidad)  
		//	    Arguments: as_codconcdes // C�digo del concepto donde se empieza a filtrar
		//				   as_codconchas // C�digo del concepto donde se termina de filtrar
		//				   as_coduniadm // C�digo de la unidad administrativa 
		//	  			   as_conceptocero // criterio que me indica si se desea quitar los conceptos que tienen monto cero
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: funci�n que busca la informaci�n de las unidades administrativas asociadas a los conceptos	
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 27/04/2006 								Fecha �ltima Modificaci�n :  
		// SE MODIFICARON LAS TABLAS TH POR H
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		if(!empty($as_codconcdes))
		{
			$ls_criterio= "AND sno_hsalida.codconc>='".$as_codconcdes."'";
		}
		if(!empty($as_codconchas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hsalida.codconc<='".$as_codconchas."'";
		}
		if(!empty($as_subnomdes))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codsubnom>='".$as_subnomdes."'";
		}
		if(!empty($as_subnomhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codsubnom<='".$as_subnomhas."'";
		}
		if(!empty($as_coduniadm))
		{
			$ls_minorguniadm=substr($as_coduniadm,0,4);
			$ls_ofiuniadm=substr($as_coduniadm,5,2);
			$ls_uniuniadm=substr($as_coduniadm,8,2);
			$ls_depuniadm=substr($as_coduniadm,11,2);
			$ls_prouniadm=substr($as_coduniadm,14,2);
			$ls_criterio = $ls_criterio."   AND sno_hpersonalnomina.minorguniadm = '".$ls_minorguniadm."' ".
										"   AND sno_hpersonalnomina.ofiuniadm = '".$ls_ofiuniadm."' ".
										"   AND sno_hpersonalnomina.uniuniadm = '".$ls_uniuniadm."' ".
										"   AND sno_hpersonalnomina.depuniadm = '".$ls_depuniadm."' ".
										"   AND sno_hpersonalnomina.prouniadm = '".$ls_prouniadm."' ";
		}
		if(!empty($as_conceptocero))
		{
			$ls_criterio = $ls_criterio."   AND sno_hsalida.valsal<>0 ";
		}
		$ls_sql="SELECT sno_hunidadadmin.minorguniadm, sno_hunidadadmin.ofiuniadm, sno_hunidadadmin.uniuniadm, sno_hunidadadmin.depuniadm, ".
				"		sno_hunidadadmin.prouniadm, sno_hunidadadmin.desuniadm ".
				"  FROM sno_hsalida, sno_hpersonalnomina, sno_hunidadadmin ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal='A' OR sno_hsalida.tipsal='V1' OR sno_hsalida.tipsal='W1' OR ".
				"        sno_hsalida.tipsal='D' OR sno_hsalida.tipsal='V2' OR sno_hsalida.tipsal='W2' OR ".
				"        sno_hsalida.tipsal='P1' OR sno_hsalida.tipsal='V3' OR sno_hsalida.tipsal='W3' OR ".
				"	     sno_hsalida.tipsal='P2' OR sno_hsalida.tipsal='V4' OR sno_hsalida.tipsal='W4') ".
				"   ".$ls_criterio." ".
				"   AND sno_hsalida.codemp = sno_hpersonalnomina.codemp ".
				"   AND sno_hsalida.codnom = sno_hpersonalnomina.codnom ".
				"   AND sno_hsalida.anocur = sno_hpersonalnomina.anocur ".
				"   AND sno_hsalida.codperi = sno_hpersonalnomina.codperi ".
				"   AND sno_hsalida.codper = sno_hpersonalnomina.codper ".
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				" GROUP BY sno_hunidadadmin.minorguniadm, sno_hunidadadmin.ofiuniadm, sno_hunidadadmin.uniuniadm, sno_hunidadadmin.depuniadm, ".
				"		sno_hunidadadmin.prouniadm, sno_hunidadadmin.desuniadm ".
				" ORDER BY sno_hunidadadmin.minorguniadm, sno_hunidadadmin.ofiuniadm, sno_hunidadadmin.uniuniadm, sno_hunidadadmin.depuniadm, ".
				"		sno_hunidadadmin.prouniadm";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_resumenconceptounidad_unidad ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);		
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_resumenconceptounidad_unidad
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_resumenconceptounidad_concepto($as_codconcdes,$as_codconchas,$as_coduniadm,$as_conceptocero,$as_orden)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_resumenconceptounidad_concepto
		//         Access: public (desde la clase sigesp_sno_r_resumenconceptounidad)  
		//	    Arguments: as_codconcdes // C�digo del concepto donde se empieza a filtrar
		//				   as_codconchas // C�digo del concepto donde se termina de filtrar
		//				   as_coduniadm // C�digo de la unidad administrativa 
		//	  			   as_conceptocero // criterio que me indica si se desea quitar los conceptos que tienen monto cero
		//	  			   as_orden // Orden del reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: funci�n que busca la informaci�n de los conceptos asociados a la unidad administrativa
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 28/04/2006 								Fecha �ltima Modificaci�n :  
		// SE MODIFICARON LAS TABLAS TH POR H
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		$ls_minorguniadm=substr($as_coduniadm,0,4);
		$ls_ofiuniadm=substr($as_coduniadm,5,2);
		$ls_uniuniadm=substr($as_coduniadm,8,2);
		$ls_depuniadm=substr($as_coduniadm,11,2);
		$ls_prouniadm=substr($as_coduniadm,14,2);
		if(!empty($as_codconcdes))
		{
			$ls_criterio= "AND sno_hsalida.codconc>='".$as_codconcdes."'";
		}
		if(!empty($as_codconchas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hsalida.codconc<='".$as_codconchas."'";
		}
		if(!empty($as_conceptocero))
		{
			$ls_criterio = $ls_criterio."   AND sno_hsalida.valsal<>0 ";
		}
		switch($as_orden)
		{
			case "1": // Ordena por Tipo de Salida y C�digo del Concepto
				$ls_orden="ORDER BY sno_hsalida.tipsal, sno_hconcepto.codconc ";
				break;

			case "2": // Ordena por Tipo de Salida y descripci�n del Concepto
				$ls_orden="ORDER BY sno_hsalida.tipsal,  sno_hconcepto.nomcon ";
				break;
		}
		$ls_sql="SELECT sno_hconcepto.codconc, MAX(sno_hconcepto.nomcon) AS nomcon, sno_hsalida.tipsal, sum(sno_hsalida.valsal) as monto, ".
				"		COUNT(sno_hsalida.codper) AS total, MAX(sno_hconcepto.cueprecon) AS cueprecon, MAX(sno_hconcepto.cueprepatcon) AS cueprepatcon  ".
				"  FROM sno_hsalida, sno_hpersonalnomina, sno_hconcepto ".
				" WHERE sno_hpersonalnomina.minorguniadm = '".$ls_minorguniadm."' ".
				"   AND sno_hpersonalnomina.ofiuniadm = '".$ls_ofiuniadm."' ".
				"   AND sno_hpersonalnomina.uniuniadm = '".$ls_uniuniadm."' ".
				"   AND sno_hpersonalnomina.depuniadm = '".$ls_depuniadm."' ".
				"   AND sno_hpersonalnomina.prouniadm = '".$ls_prouniadm."' ".
				"   AND sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal='A' OR sno_hsalida.tipsal='V1' OR sno_hsalida.tipsal='W1' OR ".
				"        sno_hsalida.tipsal='D' OR sno_hsalida.tipsal='V2' OR sno_hsalida.tipsal='W2' OR ".
				"        sno_hsalida.tipsal='P1' OR sno_hsalida.tipsal='V3' OR sno_hsalida.tipsal='W3' OR ".
				"	     sno_hsalida.tipsal='P2' OR sno_hsalida.tipsal='V4' OR sno_hsalida.tipsal='W4') ".
				"   ".$ls_criterio." ".
				"   AND sno_hsalida.codemp = sno_hpersonalnomina.codemp ".
				"   AND sno_hsalida.codnom = sno_hpersonalnomina.codnom ".
				"   AND sno_hsalida.anocur = sno_hpersonalnomina.anocur ".
				"   AND sno_hsalida.codperi = sno_hpersonalnomina.codperi ".
				"   AND sno_hsalida.codper = sno_hpersonalnomina.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				" GROUP BY sno_hconcepto.codconc, sno_hsalida.tipsal ".
				$ls_orden;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_resumenconceptounidad_concepto ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS_detalle->data=$this->io_sql->obtener_datos($rs_data);		
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_resumenconceptounidad_concepto
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_cuadrenomina_periodo_previo(&$ai_anoprev,&$ai_periprev)
    {
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_cuadrenomina_periodo_previo
		//		   Access: public
		//	    Arguments: ai_anoprev // A�o Previo
		//                 ai_periprev // periodo previo          
		//	      Returns: lb_valido True si se ejecuto correctamente la funaci�n y false si hubo error
		//	  Description: funci�n que busca la informaci�n del per�odo previo a la n�mina actual
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 02/05/2006 								Fecha �ltima Modificaci�n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ai_anoprev=$_SESSION["la_nomina"]["anocurnom"];
		$ai_periprev=(intval($_SESSION["la_nomina"]["peractnom"])-1);
		if($ai_periprev<1)
		{
			$ai_anoprev=(intval($ai_anoprev)-1);
			$ls_sql="SELECT numpernom ".
					"  FROM sno_hnomina ".
					" WHERE codemp='".$this->ls_codemp."' ".
					"   AND codnom='".$this->ls_codnom."' ".
					"   AND anocurnom='".$ai_anoprev."' ";
			$rs_data=$this->io_sql->select($ls_sql);
			if($rs_data===false)
			{
				$this->io_mensajes->message("CLASE->SNO M�TODO->uf_cuadrenomina_periodo_previo ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
				$lb_valido=false;
			}
			else
			{
				while($row=$this->io_sql->fetch_row($rs_data))
				{
					$ai_periprev=$row["numpernom"];
				}
				if($ai_periprev<1)
				{
					$ai_periprev="0";
				}
				$this->io_sql->free_result($rs_data);
			}
		}
		$ai_periprev=str_pad($ai_periprev,3,"0",0);
      	return ($lb_valido);  
    }// end function uf_cuadrenomina_periodo_previo	
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_cuadrenomina_concepto($as_codconcdes,$as_codconchas,$as_conceptocero,$as_subnomdes,$as_subnomhas)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_cuadrenomina_concepto
		//         Access: public (desde la clase sigesp_sno_r_cuadrenomina)  
		//	    Arguments: as_codconcdes // C�digo del concepto donde se empieza a filtrar
		//				   as_codconchas // C�digo del concepto donde se termina de filtrar
		//	  			   as_conceptocero // criterio que me indica si se desea quitar los conceptos que tienen monto cero
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: funci�n que busca la informaci�n de los conceptos que se calcul� la n�mina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 02/05/2006 								Fecha �ltima Modificaci�n : 
		// SE MODIFICARON LAS TABLAS TH POR H
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_hcriterio="";
		$li_anoprev="";
		$li_periprev="";
		if(!empty($as_codconcdes))
		{
			//$ls_criterio= "AND sno_thsalida.codconc>='".$as_codconcdes."'";
			$ls_hcriterio= "AND sno_hsalida.codconc>='".$as_codconcdes."'";
		}
		if(!empty($as_codconchas))
		{
			//$ls_criterio= $ls_criterio."   AND sno_thsalida.codconc<='".$as_codconchas."'";
			$ls_hcriterio= $ls_hcriterio."   AND sno_hsalida.codconc<='".$as_codconchas."'";
		}
		if(!empty($as_conceptocero))
		{
			//$ls_criterio = $ls_criterio."   AND sno_thsalida.valsal<>0 ";
			$ls_hcriterio = $ls_hcriterio."   AND sno_hsalida.valsal<>0 ";
		}
		if(!empty($as_subnomdes))
		{
			//$ls_criterio= $ls_criterio."   AND sno_thpersonalnomina.codsubnom>='".$as_subnomdes."'";
			$ls_hcriterio= $ls_hcriterio."   AND sno_hpersonalnomina.codsubnom>='".$as_subnomdes."'";
		}
		if(!empty($as_subnomhas))
		{
			//$ls_criterio= $ls_criterio."   AND sno_thpersonalnomina.codsubnom<='".$as_subnomhas."'";
			$ls_hcriterio= $ls_hcriterio."   AND sno_hpersonalnomina.codsubnom<='".$as_subnomhas."'";
		}
		$lb_valido=$this->uf_cuadrenomina_periodo_previo($li_anoprev,$li_periprev);
		$ls_sql=" SELECT codconc,nomcon,actual,COALESCE(previo,0) as previo".
			" FROM (SELECT sno_hsalida.codconc, sno_hconcepto.nomcon, sno_hsalida.tipsal, sum(COALESCE(sno_hsalida.valsal,0)) as actual ".
				"  FROM sno_hsalida, sno_hconcepto, sno_hpersonalnomina ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal='A' OR  sno_hsalida.tipsal='V1' OR sno_hsalida.tipsal='W1')".
				"   ".$ls_hcriterio." ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hsalida.codemp = sno_hpersonalnomina.codemp ".
				"   AND sno_hsalida.codnom = sno_hpersonalnomina.codnom ".
				"   AND sno_hsalida.anocur = sno_hpersonalnomina.anocur ".
				"   AND sno_hsalida.codperi = sno_hpersonalnomina.codperi ".
				"   AND sno_hsalida.codper = sno_hpersonalnomina.codper ".
				" GROUP BY sno_hsalida.codconc, sno_hsalida.tipsal, sno_hconcepto.nomcon ) as actual".
			" LEFT JOIN ".
			"	(SELECT sum(COALESCE(sno_hsalida.valsal,0)) as previo, sno_hsalida.codconc as cod".
				"		   			FROM sno_hsalida,sno_hpersonalnomina ".
				"		 		   WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"					 AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"					 AND sno_hsalida.anocur='".$li_anoprev."' ".
				"					 AND sno_hsalida.codperi='".$li_periprev."' ".
				"   				 AND (sno_hsalida.tipsal='A' OR  sno_hsalida.tipsal='V1' OR sno_hsalida.tipsal='W1')".
				"					 ".$ls_hcriterio.
				"   				 AND sno_hsalida.codconc=sno_hsalida.codconc ".
				"   				 AND sno_hsalida.tipsal=sno_hsalida.tipsal ".
				"   				 AND sno_hsalida.codemp = sno_hpersonalnomina.codemp ".
				"  					 AND sno_hsalida.codnom = sno_hpersonalnomina.codnom ".
				"  					 AND sno_hsalida.anocur = sno_hpersonalnomina.anocur ".
				"  					 AND sno_hsalida.codperi = sno_hpersonalnomina.codperi ".
				"   				 AND sno_hsalida.codper = sno_hpersonalnomina.codper ".
				" 				   GROUP BY sno_hsalida.codconc, sno_hsalida.tipsal) as previo ".
			"ON cod=codconc";
		$rs_data=$this->io_sql->select($ls_sql);//echo $ls_sql;die();
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_cuadrenomina_concepto ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);		
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_cuadrenomina_concepto
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_monejetipocargo_programado()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_monejetipocargo_programado
		//         Access: public (desde la clase sigesp_snorh_rpp_monejetipocargo)  
		//	    Arguments: as_rango // rango de meses a sumar
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: funci�n que busca la informaci�n de la programaci�n de reporte
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 30/06/2006 								Fecha �ltima Modificaci�n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;

		$ls_sql="SELECT sno_programacionreporte.codrep, sno_programacionreporte.codded, sno_programacionreporte.codtipper, ".
				"		(SELECT desded FROM  sno_dedicacion ".
				"	 	  WHERE sno_programacionreporte.codemp = sno_dedicacion.codemp ".
				"			AND sno_programacionreporte.codded = sno_dedicacion.codded) as desded, ".
				"		(SELECT destipper FROM  sno_tipopersonal ".
				"	 	  WHERE sno_programacionreporte.codemp = sno_tipopersonal.codemp ".
				"			AND sno_programacionreporte.codded = sno_tipopersonal.codded ".
				"			AND sno_programacionreporte.codtipper = sno_tipopersonal.codtipper) as destipper ".
				"  FROM sno_programacionreporte ".
				" WHERE sno_programacionreporte.codemp = '".$this->ls_codemp."'".
				"   AND sno_programacionreporte.codrep = '0711'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_monejetipocargo_programado ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);		
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_monejetipocargo_programado
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_monejetipocargo_real($as_codded,$as_codtipper,&$ai_cargoreal,&$ai_montoreal)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_monejetipocargo_real
		//         Access: public (desde la clase sigesp_snorh_rpp_comparado0711)  
		//	    Arguments: as_codded // c�digo de dedicaci�n
		//	   			   as_codtipper // c�digo de tipo de personal
		//	   			   ai_cargoreal // Cargo Real
		//	   			   ai_montoreal // Monto Real
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: funci�n que busca la informaci�n de la programaci�n de reporte
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 30/06/2006 								Fecha �ltima Modificaci�n :  
		// SE MODIFICARON LAS TABLAS TH POR H
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_groupcargos="";
		$ls_groupmontos="";
		if($as_codtipper=="0000")
		{
			$ls_criterio=" AND sno_hpersonalnomina.codded='".$as_codded."'";
			$ls_groupcargos=" GROUP BY sno_hpersonalnomina.codper, sno_hpersonalnomina.codded, sno_hpersonalnomina.codtipper ";
			$ls_groupmontos=" GROUP BY sno_hpersonalnomina.codded ";
		}
		else
		{
			$ls_criterio=" AND sno_hpersonalnomina.codded='".$as_codded."'".
						 " AND sno_hpersonalnomina.codtipper='".$as_codtipper."'";
			$ls_groupcargos=" GROUP BY sno_hpersonalnomina.codper, sno_hpersonalnomina.codded, sno_hpersonalnomina.codtipper ";
			$ls_groupmontos=" GROUP BY sno_hpersonalnomina.codded, sno_hpersonalnomina.codtipper ";
		}

		$ls_sql="SELECT sno_hpersonalnomina.codper ".
				"  FROM sno_hpersonalnomina, sno_hperiodo, sno_hnomina ".
				" WHERE sno_hpersonalnomina.codemp = '".$this->ls_codemp."'".
				"   AND sno_hpersonalnomina.codnom = '".$this->ls_codnom."'".
				"   AND sno_hpersonalnomina.anocur = '".substr($_SESSION["la_empresa"]["periodo"],0,4)."'".
				"   AND sno_hpersonalnomina.codperi = '".$this->ls_peractnom."'".
				"   ".$ls_criterio.
				"   AND sno_hnomina.tipnom <> 7 ".
				"   AND sno_hnomina.espnom = '0' ".
				"   AND sno_hnomina.ctnom = '0' ".
				"   AND sno_hnomina.codemp = sno_hperiodo.codemp ".
				"   AND sno_hnomina.codnom = sno_hperiodo.codnom ".
				"	AND sno_hnomina.anocurnom = sno_hperiodo.anocur ".
				"   AND sno_hnomina.peractnom = sno_hperiodo.codperi ".
				"   AND sno_hpersonalnomina.codemp = sno_hperiodo.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hperiodo.codnom ".
				"	AND sno_hpersonalnomina.anocur = sno_hperiodo.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hperiodo.codperi ".
				$ls_groupcargos;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_comparado0711_real ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$ai_cargoreal=$ai_cargoreal+1;
			}
			$this->io_sql->free_result($rs_data);
		}
		if($lb_valido)
		{
			$ls_sql="SELECT sum(sno_hsalida.valsal) as monto ".
					"  FROM sno_hpersonalnomina, sno_hsalida, sno_hperiodo, sno_hnomina ".
					" WHERE sno_hpersonalnomina.codemp = '".$this->ls_codemp."'".
					"   AND sno_hpersonalnomina.codnom = '".$this->ls_codnom."'".
					"   AND sno_hpersonalnomina.anocur = '".substr($_SESSION["la_empresa"]["periodo"],0,4)."'".
					"   AND sno_hpersonalnomina.codperi = '".$this->ls_peractnom."'".
					$ls_criterio.
					"   AND sno_hsalida.tipsal = 'A' ".
					"   AND sno_hnomina.tipnom <> 7 ".
					"   AND sno_hnomina.codemp = sno_hperiodo.codemp ".
					"   AND sno_hnomina.codnom = sno_hperiodo.codnom ".
					"	AND sno_hnomina.anocurnom = sno_hperiodo.anocur ".
					"   AND sno_hnomina.peractnom = sno_hperiodo.codperi ".
					"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
					"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
					"	AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
					"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
					"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
					"   AND sno_hpersonalnomina.codemp = sno_hperiodo.codemp ".
					"   AND sno_hpersonalnomina.codnom = sno_hperiodo.codnom ".
					"	AND sno_hpersonalnomina.anocur = sno_hperiodo.anocur ".
					"   AND sno_hpersonalnomina.codperi = sno_hperiodo.codperi ".
					$ls_groupmontos;
			$rs_data=$this->io_sql->select($ls_sql);
			if($rs_data===false)
			{
				$this->io_mensajes->message("CLASE->Report M�TODO->uf_comparado0711_real ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
				$lb_valido=false;
			}
			else
			{
				if($row=$this->io_sql->fetch_row($rs_data))
				{
					$ai_montoreal=$row["monto"];
				}
				$this->io_sql->free_result($rs_data);
			}
		}		
		return $lb_valido;
	}// end function uf_monejetipocargo_real
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_monejepensionado_programado()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_monejepensionado_programado
		//         Access: public (desde la clase sigesp_snorh_rpp_monejepensionado)  
		//	    Arguments: as_rango // rango de meses a sumar
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: funci�n que busca la informaci�n de la programaci�n de reporte
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 29/06/2006 								Fecha �ltima Modificaci�n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;

		$ls_sql="SELECT sno_programacionreporte.codrep, sno_programacionreporte.codded, sno_programacionreporte.codtipper ".
				"  FROM sno_programacionreporte ".
				" WHERE sno_programacionreporte.codemp = '".$this->ls_codemp."'".
				"   AND sno_programacionreporte.codrep = '0712'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_monejepensionado_programado ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);		
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_monejepensionado_programado
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_monejepensionado_real($as_catjub,$as_conjub,&$ai_cargoreal,&$ai_montoreal)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_monejepensionado_real
		//         Access: public (desde la clase sigesp_snorh_rpp_monejepensionado)  
		//	    Arguments: as_catjub // Categor�a de Jubilaci�n
		//	   			   as_conjub // Condici�n de Jubilaci�n
		//	   			   ai_cargoreal // Cargo Real
		//	   			   ai_montoreal // Monto Real
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: funci�n que busca la informaci�n de la programaci�n de reporte
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 29/06/2006 								Fecha �ltima Modificaci�n :  
		// SE MODIFICARON LAS TABLAS TH POR H
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_groupcargos="";
		$ls_groupmontos="";
		if($as_conjub=="0000")
		{
			$ls_criterio=" AND sno_hpersonalnomina.catjub='".$as_catjub."'";
			$ls_groupcargos=" GROUP BY sno_hpersonalnomina.codper, sno_hpersonalnomina.catjub, sno_hpersonalnomina.conjub ";
			$ls_groupmontos=" GROUP BY sno_hpersonalnomina.catjub ";
		}
		else
		{
			$ls_criterio=" AND sno_hpersonalnomina.catjub='".$as_catjub."'".
						 " AND sno_hpersonalnomina.conjub='".$as_conjub."'";
			$ls_groupcargos=" GROUP BY sno_hpersonalnomina.codper, sno_hpersonalnomina.catjub, sno_hpersonalnomina.conjub ";
			$ls_groupmontos=" GROUP BY sno_hpersonalnomina.catjub, sno_hpersonalnomina.conjub ";
		}
		$ls_sql="SELECT sno_hpersonalnomina.codper ".
				"  FROM sno_hpersonalnomina, sno_hperiodo, sno_hnomina ".
				" WHERE sno_hpersonalnomina.codemp = '".$this->ls_codemp."'".
				"   AND sno_hpersonalnomina.codnom = '".$this->ls_codnom."'".
				"   AND sno_hpersonalnomina.anocur = '".substr($_SESSION["la_empresa"]["periodo"],0,4)."'".
				"   AND sno_hpersonalnomina.codperi = '".$this->ls_peractnom."'".
				"   AND sno_hnomina.tipnom = 7 ".
				"   AND sno_hnomina.espnom = '0' ".
				"   AND sno_hnomina.ctnom = '0' ".
				$ls_criterio.
				"   AND sno_hnomina.codemp = sno_hperiodo.codemp ".
				"   AND sno_hnomina.codnom = sno_hperiodo.codnom ".
				"	AND sno_hnomina.anocurnom = sno_hperiodo.anocur ".
				"   AND sno_hnomina.peractnom = sno_hperiodo.codperi ".
				"   AND sno_hpersonalnomina.codemp = sno_hperiodo.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hperiodo.codnom ".
				"	AND sno_hpersonalnomina.anocur = sno_hperiodo.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hperiodo.codperi ".
				$ls_groupcargos;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_monejepensionado_real ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$ai_cargoreal=$ai_cargoreal+1;
			}
			$this->io_sql->free_result($rs_data);
		}
		if($lb_valido)
		{
			$ls_sql="SELECT sum(sno_hsalida.valsal) as monto ".
					"  FROM sno_hpersonalnomina, sno_hsalida, sno_hperiodo, sno_hnomina ".
					" WHERE sno_hpersonalnomina.codemp = '".$this->ls_codemp."'".
					"   AND sno_hpersonalnomina.codnom = '".$this->ls_codnom."'".
					"   AND sno_hpersonalnomina.anocur = '".substr($_SESSION["la_empresa"]["periodo"],0,4)."'".
					"   AND sno_hperiodo.codperi = '".$this->ls_peractnom."'".
					$ls_criterio.
					"   AND sno_hnomina.tipnom = 7 ".
					"   AND sno_hnomina.espnom = '0' ".
					"   AND sno_hnomina.ctnom = '0' ".
					"   AND sno_hsalida.tipsal = 'A' ".
					"   AND sno_hnomina.codemp = sno_hperiodo.codemp ".
					"   AND sno_hnomina.codnom = sno_hperiodo.codnom ".
					"	AND sno_hnomina.anocurnom = sno_hperiodo.anocur ".
					"   AND sno_hnomina.peractnom = sno_hperiodo.codperi ".
					"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
					"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
					"	AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
					"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
					"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
					"   AND sno_hpersonalnomina.codemp = sno_hperiodo.codemp ".
					"   AND sno_hpersonalnomina.codnom = sno_hperiodo.codnom ".
					"	AND sno_hpersonalnomina.anocur = sno_hperiodo.anocur ".
					"   AND sno_hpersonalnomina.codperi = sno_hperiodo.codperi ".
					$ls_groupmontos;
			$rs_data=$this->io_sql->select($ls_sql);
			if($rs_data===false)
			{
				$this->io_mensajes->message("CLASE->Report M�TODO->uf_monejepensionado_real ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
				$lb_valido=false;
			}
			else
			{
				if($row=$this->io_sql->fetch_row($rs_data))
				{
					$ai_montoreal=$row["monto"];
				}
				$this->io_sql->free_result($rs_data);
			}
		}		
		return $lb_valido;
	}// end function uf_monejepensionado_real
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_relacionvacacion_personal($as_codper,$as_codvac,$as_conceptocero,&$rs_data)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_relacionvacacion_personal
		//         Access: public (desde la clase sigesp_sno_rpp_relacionvacacion)  
		//	    Arguments: as_codper // C�digo del personal 
		//	  			   as_codvac // C�digo de la vacaci�n 
		//	  			   as_conceptocero // si se desean mostrar los conceptos en cero
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: funci�n que busca la informaci�n del personal que sale de vacaciones
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 03/07/2006 								Fecha �ltima Modificaci�n :  
		// SE MODIFICARON LAS TABLAS TH POR H
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		if(!empty($as_conceptocero))
		{
			$ls_criterio = "AND sno_hsalida.valsal<>0 ";
		}
		if($this->li_rac=="1")// Utiliza RAC
		{
			$ls_descar="       (SELECT denasicar FROM sno_hasignacioncargo ".
					   "   	     WHERE sno_hpersonalnomina.codemp = sno_hasignacioncargo.codemp ".
					   "		   AND sno_hpersonalnomina.codnom = sno_hasignacioncargo.codnom ".
					   "		   AND sno_hpersonalnomina.anocur = sno_hasignacioncargo.anocur ".
					   "		   AND sno_hpersonalnomina.codperi = sno_hasignacioncargo.codperi ".
				       "           AND sno_hpersonalnomina.codasicar = sno_hasignacioncargo.codasicar) as descar ";
		}
		else// No utiliza RAC
		{
			$ls_descar="       (SELECT descar FROM sno_hcargo ".
					   "   	     WHERE sno_hpersonalnomina.codemp = sno_hcargo.codemp ".
					   "		   AND sno_hpersonalnomina.codnom = sno_hcargo.codnom ".
					   "		   AND sno_hpersonalnomina.anocur = sno_hcargo.anocur ".
					   "		   AND sno_hpersonalnomina.codperi = sno_hcargo.codperi ".
				       "           AND sno_hpersonalnomina.codcar = sno_hcargo.codcar) as descar ";
		}
		$ls_sql="SELECT sno_hpersonalnomina.codemp, sno_personal.codper, sno_personal.cedper, sno_personal.nomper, sno_personal.apeper, sno_personal.fecingper, ".
				"		sno_hunidadadmin.desuniadm, sno_hvacacpersonal.sueintvac, sno_hvacacpersonal.fecdisvac, sno_hvacacpersonal.fecvenvac, ".
				"		sno_hvacacpersonal.fecreivac, sno_hvacacpersonal.diavac, sno_hvacacpersonal.codvac, ".$ls_descar.
				"       ,sno_hvacacpersonal.dianorvac, sno_hvacacpersonal.persalvac, sno_hvacacpersonal.peringvac, ".
				"       sno_hvacacpersonal.quisalvac, sno_hvacacpersonal.quireivac, sno_hvacacpersonal.diabonvac, ".
				"       sno_hvacacpersonal.sabdom, sno_hvacacpersonal.diafer,sno_hvacacpersonal.obsvac, sno_hvacacpersonal.diaadibon,".
				"       sno_hvacacpersonal.diapenvac, sno_hvacacpersonal.diapervac,sno_hvacacpersonal.diaadivac, MAX(sno_dedicacion.desded) as desded,  ".				
				"		MAX(sno_personal.anoservpreper) as anoservpreper ".
				"  FROM sno_personal ".
				" INNER JOIN (sno_hpersonalnomina  ".
				"		INNER JOIN sno_hunidadadmin ".
				"          ON sno_hpersonalnomina.codemp='".$this->ls_codemp."' ".
				"         AND sno_hpersonalnomina.codnom='".$this->ls_codnom."' ".
				"         AND sno_hpersonalnomina.anocur='".$this->ls_anocurnom."' ".
				"         AND sno_hpersonalnomina.codperi='".$this->ls_peractnom."' ".
				"         AND sno_hpersonalnomina.codper='".$as_codper."' ".
				"         AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"         AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
				"         AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
				"         AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
				"         AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"         AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"         AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"         AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"         AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				"		INNER JOIN sno_dedicacion ".
				"          ON sno_hpersonalnomina.codemp='".$this->ls_codemp."' ".
				"         AND sno_hpersonalnomina.codnom='".$this->ls_codnom."' ".
				"         AND sno_hpersonalnomina.anocur='".$this->ls_anocurnom."' ".
				"         AND sno_hpersonalnomina.codperi='".$this->ls_peractnom."' ".
				"         AND sno_hpersonalnomina.codper='".$as_codper."' ".
				"         AND sno_hpersonalnomina.codemp = sno_dedicacion.codemp ".
				"         AND sno_hpersonalnomina.codded = sno_dedicacion.codded ".
				"		INNER JOIN sno_hvacacpersonal ".
				"          ON sno_hpersonalnomina.codemp='".$this->ls_codemp."' ".
				"         AND sno_hpersonalnomina.codnom='".$this->ls_codnom."' ".
				"         AND sno_hpersonalnomina.anocur='".$this->ls_anocurnom."' ".
				"         AND sno_hpersonalnomina.codperi='".$this->ls_peractnom."' ".
				"         AND sno_hpersonalnomina.codper='".$as_codper."' ".
				"  		  AND sno_hvacacpersonal.codvac='".$as_codvac."' ".
				"         AND sno_hpersonalnomina.codemp = sno_hvacacpersonal.codemp ".
				"         AND sno_hpersonalnomina.anocur = sno_hvacacpersonal.anocur ".
				"         AND sno_hpersonalnomina.codperi = sno_hvacacpersonal.codperi ".
				"         AND sno_hpersonalnomina.codper = sno_hvacacpersonal.codper ".
				"		INNER JOIN sno_hsalida ".
				"          ON sno_hpersonalnomina.codemp='".$this->ls_codemp."' ".
				"         AND sno_hpersonalnomina.codnom='".$this->ls_codnom."' ".
				"         AND sno_hpersonalnomina.anocur='".$this->ls_anocurnom."' ".
				"         AND sno_hpersonalnomina.codperi='".$this->ls_peractnom."' ".
				"         AND sno_hpersonalnomina.codper='".$as_codper."' ".
				"         AND ((sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'V3' OR sno_hsalida.tipsal = 'V4') ".
				"          OR (sno_hsalida.tipsal = 'W1' OR sno_hsalida.tipsal = 'W2' OR sno_hsalida.tipsal = 'W3' OR sno_hsalida.tipsal = 'W4')) ".
				$ls_criterio.
				"         AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"         AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"         AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"         AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"         AND sno_hpersonalnomina.codper = sno_hsalida.codper) ".
				"    ON sno_hpersonalnomina.codemp = sno_personal.codemp ".
				"   AND sno_hpersonalnomina.codper = sno_personal.codper ".
				" WHERE sno_personal.codemp='".$this->ls_codemp."' ".
				"   AND sno_personal.codper='".$as_codper."' ".
				" GROUP BY sno_hpersonalnomina.codemp, sno_hpersonalnomina.anocur, sno_hpersonalnomina.codperi, sno_personal.codper, ".
				"  sno_hvacacpersonal.codvac, sno_personal.cedper, sno_personal.nomper, sno_personal.apeper, sno_personal.fecingper, ".
				"  sno_hunidadadmin.desuniadm, sno_hvacacpersonal.sueintvac, sno_hvacacpersonal.fecdisvac, sno_hvacacpersonal.fecreivac, ".
				"  sno_hvacacpersonal.diavac, sno_hvacacpersonal.dianorvac, sno_hvacacpersonal.persalvac, sno_hvacacpersonal.peringvac, ".
				"  sno_hvacacpersonal.quisalvac, sno_hvacacpersonal.quireivac, sno_hvacacpersonal.diabonvac, sno_hvacacpersonal.sabdom, ".
				"  sno_hvacacpersonal.diafer,sno_hvacacpersonal.obsvac, sno_hvacacpersonal.diaadibon, sno_hvacacpersonal.diapenvac, ".
				"  sno_hvacacpersonal.diapervac,sno_hvacacpersonal.diaadivac,sno_hpersonalnomina.codnom,sno_hpersonalnomina.codcar,sno_hpersonalnomina.codasicar,sno_hvacacpersonal.fecvenvac ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_relacionvacacion_personal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			print $this->io_sql->message;
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_relacionvacacion_personal
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_relacionvacacion_concepto($as_codper,$as_codvac,$as_conceptocero,$as_tituloconcepto)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_relacionvacacion_concepto
		//         Access: public (desde la clase sigesp_sno_rpp_relacionvacacion)  
		//	    Arguments: as_codper // C�digo del personal 
		//	  			   as_codvac // C�digo de vacaci�n
		//	  			   as_conceptocero // si se desean mostrar los conceptos en cero
		//	  			   as_tituloconcepto // si se desea mostrar el nombre del concepto � el t�tulo
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: funci�n que busca la informaci�n del personal que sale de vacaciones
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 03/07/2006 								Fecha �ltima Modificaci�n :  
		// SE MODIFICARON LAS TABLAS TH POR H
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_campo="sno_hconcepto.nomcon";
		if(!empty($as_tituloconcepto))
		{
			$ls_campo = "sno_hconcepto.titcon";
		}
		if(!empty($as_conceptocero))
		{
			$ls_criterio = "AND sno_hsalida.valsal<>0 ";
		}
		$ls_sql="SELECT sno_hconcepto.codconc, ".$ls_campo." as nomcon, sno_hsalida.valsal, ".
				"		sno_hsalida.tipsal, sno_hvacacpersonal.persalvac, sno_hvacacpersonal.peringvac ".
				"  FROM sno_hpersonalnomina, sno_hconcepto, sno_hsalida, sno_hvacacpersonal ".
				" WHERE sno_hpersonalnomina.codemp='".$this->ls_codemp."' ".
				"   AND sno_hpersonalnomina.codnom='".$this->ls_codnom."' ".
				"   AND sno_hpersonalnomina.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hpersonalnomina.codperi='".$this->ls_peractnom."' ".
				"   AND sno_hpersonalnomina.codper='".$as_codper."' ".
				"   AND sno_hvacacpersonal.codvac='".$as_codvac."' ".
				$ls_criterio.
				"   AND ((sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'V3' OR sno_hsalida.tipsal = 'V4') ".
				"    OR (sno_hsalida.tipsal = 'W1' OR sno_hsalida.tipsal = 'W2' OR sno_hsalida.tipsal = 'W3' OR sno_hsalida.tipsal = 'W4')) ".
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"   AND sno_hpersonalnomina.codemp = sno_hvacacpersonal.codemp ".
				"   AND sno_hpersonalnomina.anocur = sno_hvacacpersonal.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hvacacpersonal.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hvacacpersonal.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_relacionvacacion_concepto ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS_detalle->data=$this->io_sql->obtener_datos($rs_data);		
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_relacionvacacion_concepto
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_programacionvacaciones_personal($as_estvac,$ad_fecdisdes,$ad_fecdishas,$as_subnomdes,$as_subnomhas,$as_orden)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_programacionvacaciones_personal
		//         Access: public (desde la clase sigesp_sno_rpp_resumenconceptos)  
		//	    Arguments: as_estvac // Estatus de las vacaciones
		//				   ad_fecdisdes // Fecha de Disfrute Desde
		//				   ad_fecdishas // Fecha de Disfrute Hasta
		//	  			   as_orden // Orden de la salida
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: funci�n que busca la informaci�n de las vacaciones programadas del personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 23/08/2006 								Fecha �ltima Modificaci�n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		if(!empty($as_estvac))
		{
			$ls_criterio= "AND sno_hvacacpersonal.stavac = ".$as_estvac."";
		}
		else
		{
			$ls_criterio= "AND (sno_hvacacpersonal.stavac = 1 OR sno_hvacacpersonal.stavac = 2) ";
		}
		if(!empty($as_subnomdes))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codsubnom>='".$as_subnomdes."'";
		}
		if(!empty($as_subnomhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codsubnom<='".$as_subnomhas."'";
		}
		if(!empty($ad_fecdisdes))
		{
			$ls_criterio= $ls_criterio."   AND sno_hvacacpersonal.fecdisvac>='".$this->io_funciones->uf_convertirdatetobd($ad_fecdisdes)."'";
		}
		if(!empty($ad_fecdishas))
		{
			$ls_criterio = $ls_criterio."   AND sno_hvacacpersonal.fecdisvac<='".$this->io_funciones->uf_convertirdatetobd($ad_fecdishas)."' ";
		}
		switch($as_orden)
		{
			case "1": // Ordena por C�digo de Personal 
				$ls_orden="ORDER BY sno_personal.codper, sno_hvacacpersonal.codvac ";
				break;

			case "2": // Ordena por Apellido de Personal
				$ls_orden="ORDER BY sno_personal.apeper, sno_hvacacpersonal.codvac ";
				break;

			case "3": // Ordena por Nombre de Personal
				$ls_orden="ORDER BY sno_personal.nomper, sno_hvacacpersonal.codvac ";
				break;

			case "4": // Ordena por Fecha de Vencimiento
				$ls_orden="ORDER BY sno_hvacacpersonal.fecvenvac, sno_hvacacpersonal.codvac ";
				break;

			case "5": // Ordena por Fecha de Disfrute
				$ls_orden="ORDER BY sno_hvacacpersonal.fecdisvac, sno_hvacacpersonal.codvac ";
				break;
		}
		$ls_sql="SELECT sno_personal.codper, sno_personal.apeper, sno_personal.nomper, sno_hvacacpersonal.codvac, ".
		        "		sno_hvacacpersonal.fecvenvac, sno_hvacacpersonal.fecdisvac, sno_hvacacpersonal.stavac ".
 				"  FROM sno_personal, sno_hpersonalnomina, sno_hvacacpersonal ".
				" WHERE sno_hpersonalnomina.codemp='".$this->ls_codemp."' ".
				"   AND sno_hpersonalnomina.codnom='".$this->ls_codnom."' ".
				"   AND sno_hpersonalnomina.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hpersonalnomina.codperi='".$this->ls_peractnom."' ".
				"   ".$ls_criterio." ".
				"   AND sno_personal.codemp = sno_hpersonalnomina.codemp ".
				"   AND sno_personal.codper = sno_hpersonalnomina.codper ".
				"   AND sno_personal.codemp = sno_hvacacpersonal.codemp ".
				"   AND sno_personal.codper = sno_hvacacpersonal.codper ".
				"   AND sno_hvacacpersonal.codperi='".$this->ls_peractnom."' ".
				"   ".$ls_orden;
		$rs_data=$this->io_sql->select($ls_sql);//echo $ls_sql;die();
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_programacionvacaciones_personal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);		
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_programacionvacaciones_personal
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_listadofirmas($as_codperdes,$as_codperhas,$as_personalcero,$as_quincena,$as_tipopago,$as_coduniadm,$as_subnomdes,$as_subnomhas,$as_orden)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_listadofirmas
		//		   Access: public (desde la clase sigesp_sno_rpp_listadofirmas)  
		//	    Arguments: as_codperdes // C�digo del personal Desde
		//	    		   as_codperhas // c�digo del personal Hasta
		//	    		   as_personalcero // Si se quiere filtrar por el personal con monto cero
		//	    		   as_quincena // si se busca a toto del personal � solo los activos
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: funci�n que busca la informaci�n de las personas para que firmen lo que se les pago
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 22/11/2006 								Fecha �ltima Modificaci�n :  
		// SE MODIFICARON LAS TABLAS TH POR H
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		if(!empty($as_codperdes))
		{
			$ls_criterio= "AND sno_hpersonalnomina.codper>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codper<='".$as_codperhas."'";
		}
		if(!empty($as_subnomdes))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codsubnom>='".$as_subnomdes."'";
		}
		if(!empty($as_subnomhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codsubnom<='".$as_subnomhas."'";
		}
		if(!empty($as_coduniadm))
		{
			$ls_criterio = $ls_criterio."   AND sno_hpersonalnomina.minorguniadm='".substr($as_coduniadm,0,4)."' ";
			$ls_criterio = $ls_criterio."   AND sno_hpersonalnomina.ofiuniadm='".substr($as_coduniadm,5,2)."' ";
			$ls_criterio = $ls_criterio."   AND sno_hpersonalnomina.uniuniadm='".substr($as_coduniadm,8,2)."' ";
			$ls_criterio = $ls_criterio."   AND sno_hpersonalnomina.depuniadm='".substr($as_coduniadm,11,2)."' ";
			$ls_criterio = $ls_criterio."   AND sno_hpersonalnomina.prouniadm='".substr($as_coduniadm,14,2)."' ";
		}
		switch($as_tipopago)
		{
			case "1": // Pago en efectivo
				$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.pagefeper=1 ";
				$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.pagbanper=0 ";
				$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.pagtaqper=0 ";
				break;
				
			case "2": // Pago en banco
				$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.pagefeper=0 ";
				$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.pagbanper=1 ";
				$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.pagtaqper=0 ";
				break;
				
			case "3": // Pago por taquilla
				$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.pagefeper=0 ";
				$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.pagbanper=0 ";
				$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.pagtaqper=1 ";
				break;
		}
		switch($as_quincena)
		{
			case 1: // Primera Quincena
				$ls_monto="sno_hresumen.priquires as monnetres";
				if(!empty($as_personalcero))
				{
					$ls_criterio = $ls_criterio."AND sno_hresumen.priquires<>0 ";
				}
				break;

			case 2: // Segunda Quincena
				$ls_monto="sno_hresumen.segquires as monnetres";
				if(!empty($as_personalcero))
				{
					$ls_criterio = $ls_criterio."AND sno_hresumen.segquires<>0 ";
				}
				break;

			case 3: // Mes Completo
				$ls_monto="sno_hresumen.monnetres as monnetres";
				if(!empty($as_personalcero))
				{
					$ls_criterio = $ls_criterio."AND sno_hresumen.monnetres<>0 ";
				}
				break;
		}
		switch($as_orden)
		{
			case "1": // Ordena por C�digo del Personal
				$ls_orden="ORDER BY sno_personal.codper ";
				break;

			case "2": // Ordena por Apellido del Personal
				$ls_orden="ORDER BY sno_personal.apeper ";
				break;

			case "3": // Ordena por Nombre del Personal
				$ls_orden="ORDER BY sno_personal.nomper ";
				break;
		}
		$ls_sql="SELECT sno_personal.codper, sno_personal.cedper, sno_personal.apeper, sno_personal.nomper, ".$ls_monto.
				"  FROM sno_personal, sno_hpersonalnomina,  sno_hresumen ".
				" WHERE sno_hpersonalnomina.codemp='".$this->ls_codemp."' ".
				"   AND sno_hpersonalnomina.codnom='".$this->ls_codnom."' ".
				"   AND sno_hpersonalnomina.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hpersonalnomina.codperi='".$this->ls_peractnom."' ".
				$ls_criterio. 
				"   AND sno_personal.codemp = sno_hpersonalnomina.codemp ".
				"   AND sno_personal.codper = sno_hpersonalnomina.codper ".
				"	AND sno_hpersonalnomina.codemp = sno_hresumen.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hresumen.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hresumen.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hresumen.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hresumen.codper ".
				$ls_orden;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_listadofirmas ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_listadofirmas
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_listadoprestamo_conceptos($as_codconcdes,$as_codconchas,$as_codperdes,$as_codperhas,
										  $as_codtippredes,$as_codtipprehas,$as_subnomdes,$as_subnomhas,$as_estatus)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_listadoprestamo_conceptos
		//         Access: public (desde la clase sigesp_sno_rpp_listadoprestamo)  
		//	    Arguments: as_codconcdes // C�digo del concepto donde se empieza a filtrar
		//				   as_codconchas // C�digo del concepto donde se termina de filtrar
		//				   as_codperdes // C�digo del personal donde se empieza a filtrar
		//	  			   as_codperhas // C�digo del personal donde se termina de filtrar		  
		//	  			   as_codtippredes // C�digo del tipo de prestamo desde
		//	  			   as_codtipprehas // C�digo del tipo de prestamo hasta
		//	  			   as_estatus // Estatus del prestamo
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: funci�n que busca la informaci�n de los conceptos que se tienen asociados prestamos
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 04/12/2006 								Fecha �ltima Modificaci�n :  
		// SE MODIFICARON LAS TABLAS TH POR H
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		if(!empty($as_codconcdes))
		{
			$ls_criterio= "AND sno_hprestamos.codconc>='".$as_codconcdes."'";
		}
		if(!empty($as_codconchas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hprestamos.codconc<='".$as_codconchas."'";
		}
		if(!empty($as_codperdes))
		{
			$ls_criterio= $ls_criterio."   AND sno_hprestamos.codper>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hprestamos.codper<='".$as_codperhas."'";
		}
		if(!empty($as_codtippredes))
		{
			$ls_criterio= $ls_criterio."   AND sno_hprestamos.codtippre>='".$as_codtippredes."'";
		}
		if(!empty($as_codtipprehas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hprestamos.codtippre<='".$as_codtipprehas."'";
		}
		if(!empty($as_estatus))
		{
			$ls_criterio = $ls_criterio."   AND sno_hprestamos.stapre='".$as_estatus."' ";
		}
		if(!empty($as_subnomdes))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codsubnom>='".$as_subnomdes."'";
		}
		if(!empty($as_subnomhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codsubnom<='".$as_subnomhas."'";
		}
		$ls_sql="SELECT sno_hprestamos.codconc, sno_hconcepto.nomcon ".
				"  FROM sno_hprestamos, sno_hconcepto, sno_hpersonalnomina ".
				" WHERE sno_hprestamos.codemp='".$this->ls_codemp."' ".
				"   AND sno_hprestamos.codnom='".$this->ls_codnom."' ".
				"   AND sno_hprestamos.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hprestamos.codperi='".$this->ls_peractnom."' ".
				$ls_criterio.
				"   AND sno_hprestamos.codemp = sno_hconcepto.codemp ".
				"   AND sno_hprestamos.codnom = sno_hconcepto.codnom ".
				"   AND sno_hprestamos.anocur = sno_hconcepto.anocur ".
				"   AND sno_hprestamos.codperi = sno_hconcepto.codperi ".
				"   AND sno_hprestamos.codconc = sno_hconcepto.codconc ".
				"   AND sno_hprestamos.codemp = sno_hpersonalnomina.codemp ".
				"   AND sno_hprestamos.codnom = sno_hpersonalnomina.codnom ".
				"   AND sno_hprestamos.anocur = sno_hpersonalnomina.anocur ".
				"   AND sno_hprestamos.codperi = sno_hpersonalnomina.codperi ".
				"   AND sno_hprestamos.codper = sno_hpersonalnomina.codper ".
				" GROUP BY sno_hprestamos.codconc, sno_hconcepto.nomcon";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_listadoprestamo_conceptos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);		
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_listadoprestamo_conceptos
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_listadoprestamo_personalconcepto($as_codconc,$as_codperdes,$as_codperhas,
										         $as_codtippredes,$as_codtipprehas,$as_estatus,$as_subnomdes,$as_subnomhas,$as_orden)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_listadoprestamo_personalconcepto
		//		   Access: public (desde la clase sigesp_sno_rpp_listadoprestamo)  
		//	    Arguments: as_codconc // C�digo del concepto del que se desea busca el personal
		//				   as_codperdes // C�digo del personal donde se empieza a filtrar
		//	  			   as_codperhas // C�digo del personal donde se termina de filtrar		  
		//	  			   as_codtippredes // C�digo del tipo de prestamo desde
		//	  			   as_codtipprehas // C�digo del tipo de prestamo hasta
		//	  			   as_estatus // Estatus del prestamo
		//	  			   as_orden // orden por medio del cual se desea que salga el reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: funci�n que busca la informaci�n del personal asociado al concepto que se le calcul� la n�mina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 04/12/2006 								Fecha �ltima Modificaci�n :  
		// SE MODIFICARON LAS TABLAS TH POR H
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$this->io_sql=new class_sql($this->io_conexion);	
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		if(!empty($as_codperdes))
		{
			$ls_criterio= $ls_criterio."   AND sno_hprestamos.codper>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hprestamos.codper<='".$as_codperhas."'";
		}
		if(!empty($as_codtippredes))
		{
			$ls_criterio= $ls_criterio."   AND sno_hprestamos.codtippre>='".$as_codtippredes."'";
		}
		if(!empty($as_codtipprehas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hprestamos.codtippre<='".$as_codtipprehas."'";
		}
		if(!empty($as_estatus))
		{
			$ls_criterio = $ls_criterio."   AND sno_hprestamos.stapre='".$as_estatus."' ";
		}
		if(!empty($as_subnomdes))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codsubnom>='".$as_subnomdes."'";
		}
		if(!empty($as_subnomhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codsubnom<='".$as_subnomhas."'";
		}
		switch($as_orden)
		{
			case "1": // Ordena por C�digo de personal
				$ls_orden="ORDER BY sno_personal.codper ";
				break;

			case "2": // Ordena por Apellido de personal
				$ls_orden="ORDER BY sno_personal.apeper ";
				break;

			case "3": // Ordena por Nombre de personal
				$ls_orden="ORDER BY sno_personal.nomper ";
				break;

			case "4": // Ordena por C�dula de personal
				$ls_orden="ORDER BY sno_personal.cedper ";
				break;
		}
		$ls_sql="SELECT sno_hprestamos.codper, sno_personal.nomper, sno_personal.apeper, sno_htipoprestamo.destippre, ".
			    "		sno_hprestamos.fecpre, sno_hprestamos.monpre,  sno_hprestamos.monamopre, sno_hprestamos.stapre, ".
				"		(SELECT COUNT(codper) FROM sno_hprestamosperiodo ".
				"         WHERE sno_hprestamosperiodo.estcuo = 0 ".
				"			AND sno_hprestamos.codemp = sno_hprestamosperiodo.codemp ".
				" 			AND sno_hprestamos.codnom = sno_hprestamosperiodo.codnom ".
				"			AND sno_hprestamos.anocur = sno_hprestamosperiodo.anocur ".
				"			AND sno_hprestamos.codperi = sno_hprestamosperiodo.codperi ".
				"			AND sno_hprestamos.codper = sno_hprestamosperiodo.codper ".
				"			AND sno_hprestamos.numpre = sno_hprestamosperiodo.numpre ".
				"			AND sno_hprestamos.codtippre = sno_hprestamosperiodo.codtippre) AS numcuopre ".
			    "  FROM sno_hprestamos, sno_personal, sno_htipoprestamo, sno_hpersonalnomina ".
			    " WHERE sno_hprestamos.codemp='".$this->ls_codemp."' ".
			    "   AND sno_hprestamos.codnom='".$this->ls_codnom."' ".
			    "   AND sno_hprestamos.anocur='".$this->ls_anocurnom."' ".
			    "   AND sno_hprestamos.codperi='".$this->ls_peractnom."' ".
				"	AND sno_hprestamos.codconc='".$as_codconc."' ".
				$ls_criterio.
			    "   AND sno_hprestamos.codemp = sno_personal.codemp ".
			    "   AND sno_hprestamos.codper = sno_personal.codper ".
				"   AND sno_hprestamos.codemp = sno_hpersonalnomina.codemp ".
				"   AND sno_hprestamos.codnom = sno_hpersonalnomina.codnom ".
				"   AND sno_hprestamos.anocur = sno_hpersonalnomina.anocur ".
				"   AND sno_hprestamos.codperi = sno_hpersonalnomina.codperi ".
				"   AND sno_hprestamos.codper = sno_hpersonalnomina.codper ".
			    "   AND sno_hprestamos.codemp = sno_htipoprestamo.codemp ".
			    "   AND sno_hprestamos.codnom = sno_htipoprestamo.codnom ".
			    "   AND sno_hprestamos.anocur = sno_htipoprestamo.anocur ".
			    "   AND sno_hprestamos.codperi = sno_htipoprestamo.codperi ".
			    "   AND sno_hprestamos.codtippre = sno_htipoprestamo.codtippre ".
				"   ".$ls_orden;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_listadoprestamo_personalconcepto ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS_detalle->data=$this->io_sql->obtener_datos($rs_data);	
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_listadoprestamo_personalconcepto
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_detalleprestamo_personal($as_codconcdes,$as_codconchas,$as_codperdes,$as_codperhas,
										  $as_codtippredes,$as_codtipprehas,$as_estatus,$as_subnomdes,$as_subnomhas,$as_orden)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_detalleprestamo_personal
		//         Access: public (desde la clase sigesp_sno_rpp_detalleoprestamo)  
		//	    Arguments: as_codconcdes // C�digo del concepto donde se empieza a filtrar
		//				   as_codconchas // C�digo del concepto donde se termina de filtrar
		//				   as_codperdes // C�digo del personal donde se empieza a filtrar
		//	  			   as_codperhas // C�digo del personal donde se termina de filtrar		  
		//	  			   as_codtippredes // C�digo del tipo de prestamo desde
		//	  			   as_codtipprehas // C�digo del tipo de prestamo hasta
		//	  			   as_estatus // Estatus del prestamo
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: funci�n que busca la informaci�n de las personas que se tienen asociados prestamos
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 04/12/2006 								Fecha �ltima Modificaci�n :  
		// SE MODIFICARON LAS TABLAS TH POR H
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		if(!empty($as_codconcdes))
		{
			$ls_criterio= "AND sno_hprestamos.codconc>='".$as_codconcdes."'";
		}
		if(!empty($as_codconchas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hprestamos.codconc<='".$as_codconchas."'";
		}
		if(!empty($as_codperdes))
		{
			$ls_criterio= $ls_criterio."   AND sno_hprestamos.codper>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hprestamos.codper<='".$as_codperhas."'";
		}
		if(!empty($as_codtippredes))
		{
			$ls_criterio= $ls_criterio."   AND sno_hprestamos.codtippre>='".$as_codtippredes."'";
		}
		if(!empty($as_codtipprehas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hprestamos.codtippre<='".$as_codtipprehas."'";
		}
		if(!empty($as_estatus))
		{
			$ls_criterio = $ls_criterio."   AND sno_hprestamos.stapre='".$as_estatus."' ";
		}
		if(!empty($as_subnomdes))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codsubnom>='".$as_subnomdes."'";
		}
		if(!empty($as_subnomhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codsubnom<='".$as_subnomhas."'";
		}
		switch($as_orden)
		{
			case "1": // Ordena por C�digo de personal
				$ls_orden=" ORDER BY sno_personal.codper ";
				break;

			case "2": // Ordena por Apellido de personal
				$ls_orden=" ORDER BY sno_personal.apeper ";
				break;

			case "3": // Ordena por Nombre de personal
				$ls_orden=" ORDER BY sno_personal.nomper ";
				break;

			case "4": // Ordena por C�dula de personal
				$ls_orden=" ORDER BY sno_personal.cedper ";
				break;
		}
		$ls_sql="SELECT sno_hprestamos.codper, sno_hprestamos.numpre, sno_hprestamos.codtippre, sno_hprestamos.codconc, ".
				"		sno_hprestamos.monpre, sno_hprestamos.numcuopre, sno_hprestamos.monamopre, sno_hprestamos.stapre, ".
				"		sno_hprestamos.fecpre, sno_hprestamos.perinipre, sno_personal.nomper, sno_personal.apeper, ".
				"		sno_hconcepto.nomcon, sno_htipoprestamo.destippre, sno_personal.cedper, sno_personal.fecingper ".
				"  FROM sno_hprestamos, sno_personal, sno_hconcepto, sno_htipoprestamo, sno_hpersonalnomina ".
				" WHERE sno_hprestamos.codemp='".$this->ls_codemp."' ".
				"   AND sno_hprestamos.codnom='".$this->ls_codnom."' ".
				"   AND sno_hprestamos.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hprestamos.codperi='".$this->ls_peractnom."' ".
				$ls_criterio.
				"   AND sno_hprestamos.codemp = sno_personal.codemp ".
				"   AND sno_hprestamos.codper = sno_personal.codper ".
				"   AND sno_hprestamos.codemp = sno_hpersonalnomina.codemp ".
				"   AND sno_hprestamos.codnom = sno_hpersonalnomina.codnom ".
				"   AND sno_hprestamos.anocur = sno_hpersonalnomina.anocur ".
				"   AND sno_hprestamos.codperi = sno_hpersonalnomina.codperi ".
				"   AND sno_hprestamos.codper = sno_hpersonalnomina.codper ".
				"   AND sno_hprestamos.codemp = sno_hconcepto.codemp ".
				"   AND sno_hprestamos.codnom = sno_hconcepto.codnom ".
				"   AND sno_hprestamos.anocur = sno_hconcepto.anocur ".
				"   AND sno_hprestamos.codperi = sno_hconcepto.codperi ".
				"   AND sno_hprestamos.codconc = sno_hconcepto.codconc ".
				"   AND sno_hprestamos.codemp = sno_htipoprestamo.codemp ".
				"   AND sno_hprestamos.codnom = sno_htipoprestamo.codnom ".
				"   AND sno_hprestamos.anocur = sno_htipoprestamo.anocur ".
				"   AND sno_hprestamos.codperi = sno_htipoprestamo.codperi ".
				"   AND sno_hprestamos.codtippre = sno_htipoprestamo.codtippre ".
				$ls_orden;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_detalleprestamo_personal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);		
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_detalleprestamo_personal
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_detalleprestamo_cuotas($as_codper,$ai_numpre,$as_codtippre)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_detalleprestamo_cuotas
		//         Access: public (desde la clase sigesp_sno_rpp_detalleoprestamo)  
		//	    Arguments: as_codper // C�digo del personal
		//				   ai_numpre // N�mero del Prestamo
		//				   as_codtippre // C�digo del tipo de prestamo
		//				   as_codconc // C�digo de concepto
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: funci�n que busca la informaci�n de las personas que se tienen asociados prestamos
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 04/12/2006 								Fecha �ltima Modificaci�n :  
		// SE MODIFICARON LAS TABLAS TH POR H
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT numcuo, percob, feciniper, fecfinper, moncuo, estcuo ".
				"  FROM sno_hprestamosperiodo ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codnom='".$this->ls_codnom."' ".
				"   AND anocur='".$this->ls_anocurnom."' ".
				"   AND codperi='".$this->ls_peractnom."' ".
				"   AND codper='".$as_codper."' ".
				"   AND numpre='".$ai_numpre."' ".
				"   AND codtippre='".$as_codtippre."' ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_detalleprestamo_cuotas ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS_detalle->reset_ds();
				$this->DS_detalle->data=$this->io_sql->obtener_datos($rs_data);		
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_detalleprestamo_cuotas
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_detalleprestamo_amortizado($as_codper,$ai_numpre,$as_codtippre)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_detalleprestamo_amortizado
		//         Access: public (desde la clase sigesp_sno_rpp_detalleoprestamo)  
		//	    Arguments: as_codper // C�digo del personal
		//				   ai_numpre // N�mero del Prestamo
		//				   as_codtippre // C�digo del tipo de prestamo
		//				   as_codconc // C�digo de concepto
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: funci�n que busca la informaci�n de las personas que se tienen asociados prestamos
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 04/12/2006 								Fecha �ltima Modificaci�n :  
		// SE MODIFICARON LAS TABLAS TH POR H
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT numamo, peramo, fecamo, monamo, desamo ".
				"  FROM sno_hprestamosamortizado ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codnom='".$this->ls_codnom."' ".
				"   AND anocur='".$this->ls_anocurnom."' ".
				"   AND codperi='".$this->ls_peractnom."' ".
				"   AND codper='".$as_codper."' ".
				"   AND numpre='".$ai_numpre."' ".
				"   AND codtippre='".$as_codtippre."' ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_detalleprestamo_amortizado ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS_detalle->reset_ds();
				$this->DS_detalle->data=$this->io_sql->obtener_datos($rs_data);		
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_detalleprestamo_amortizado
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_listadoproyecto_proyectos($as_codproydes,$as_codproyhas)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_listadoproyecto_proyectos
		//         Access: public (desde la clase sigesp_sno_rpp_listadoproyecto)  
		//	    Arguments: as_codproydes // C�digo del proyecto donde se empieza a filtrar
		//				   as_codproyhas // C�digo del proyecto donde se termina de filtrar
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: funci�n que busca la informaci�n de los conceptos que se calcularon en la n�mina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 01/08/2007 								Fecha �ltima Modificaci�n :  
		// SE MODIFICARON LAS TABLAS TH POR H
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		if(!empty($as_codproydes))
		{
			$ls_criterio= "AND sno_hproyecto.codproy>='".$as_codproydes."'";
		}
		if(!empty($as_codproyhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hproyecto.codproy<='".$as_codproyhas."'";
		}
		$ls_sql="SELECT sno_hproyecto.codproy, MAX(sno_hproyecto.nomproy) AS nomproy, count(sno_tproyectopersonal.codper) as total, ".
				"		sum(sno_hproyectopersonal.pordiames*100) as monto ".
				"  FROM sno_hproyectopersonal, sno_hproyecto ".
				" WHERE sno_hproyectopersonal.codemp='".$this->ls_codemp."' ".
				"   AND sno_hproyectopersonal.codnom='".$this->ls_codnom."' ".
				"   AND sno_hproyectopersonal.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hproyectopersonal.codperi='".$this->ls_peractnom."' ".
				"   ".$ls_criterio.
				"   AND sno_hproyectopersonal.codemp = sno_hproyecto.codemp ".
				"   AND sno_hproyectopersonal.anocur = sno_hproyecto.anocur ".
				"   AND sno_hproyectopersonal.codperi = sno_hproyecto.codperi ".
				"   AND sno_hproyectopersonal.codproy = sno_hproyecto.codproy ".
				" GROUP BY sno_hproyecto.codproy  ".
				" ORDER BY sno_hproyecto.codproy ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_listadoproyecto_proyectos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);		
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_listadoproyecto_proyectos
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_listadoproyecto_proyectospersonal($as_codproy,$as_orden)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_listadoproyecto_proyectospersonal
		//		   Access: public (desde la clase sigesp_sno_rpp_listadoproyecto)  
		//	    Arguments: as_codproy // C�digo del proyecto del que se desea busca el personal
		//	  			   as_orden // orden por medio del cual se desea que salga el reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: funci�n que busca la informaci�n del personal asociado al proyecto
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 01/08/2007 								Fecha �ltima Modificaci�n : 
		// SE MODIFICARON LAS TABLAS TH POR H
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$this->io_sql=new class_sql($this->io_conexion);	
		$lb_valido=true;
		$ls_orden="";
		switch($as_orden)
		{
			case "1": // Ordena por C�digo de personal
				$ls_orden="ORDER BY sno_personal.codper ";
				break;

			case "2": // Ordena por Apellido de personal
				$ls_orden="ORDER BY sno_personal.apeper ";
				break;

			case "3": // Ordena por Nombre de personal
				$ls_orden="ORDER BY sno_personal.nomper ";
				break;

			case "4": // Ordena por C�dula de personal
				$ls_orden="ORDER BY sno_personal.cedper ";
				break;
		}
		if($this->li_rac=="1")// Utiliza RAC
		{
			$ls_descar="       (SELECT denasicar FROM sno_hasignacioncargo ".
					   "   	     WHERE sno_hpersonalnomina.codemp='".$this->ls_codemp."' ".
					   "           AND sno_hpersonalnomina.codnom='".$this->ls_codnom."' ".
					   "		   AND sno_hpersonalnomina.codemp = sno_hasignacioncargo.codemp ".
					   "		   AND sno_hpersonalnomina.codnom = sno_hasignacioncargo.codnom ".
				       "           AND sno_hpersonalnomina.codasicar = sno_hasignacioncargo.codasicar) as descar ";
		}
		else// No utiliza RAC
		{
			$ls_descar="       (SELECT descar FROM sno_hcargo ".
					   "   	     WHERE sno_hpersonalnomina.codemp='".$this->ls_codemp."' ".
					   "           AND sno_hpersonalnomina.codnom='".$this->ls_codnom."' ".
					   "		   AND sno_hpersonalnomina.codemp = sno_hcargo.codemp ".
					   "		   AND sno_hpersonalnomina.codnom = sno_hcargo.codnom ".
				       "           AND sno_hpersonalnomina.codcar = sno_hcargo.codcar) as descar ";
		}
		$ls_sql="SELECT sno_personal.cedper, sno_personal.apeper, sno_personal.nomper, (sno_hproyectopersonal.pordiames*100) AS pordiames, ".$ls_descar.
				"  FROM sno_personal, sno_hpersonalnomina, sno_hproyectopersonal ".
				" WHERE sno_hproyectopersonal.codemp='".$this->ls_codemp."' ".
				"   AND sno_hproyectopersonal.codnom='".$this->ls_codnom."' ".
				"   AND sno_hproyectopersonal.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hproyectopersonal.codperi='".$this->ls_peractnom."' ".
				"   AND sno_hproyectopersonal.codproy='".$as_codproy."' ".
				"   AND sno_hpersonalnomina.codemp = sno_hproyectopersonal.codemp ".
				"   AND sno_hpersonalnomina.anocur = sno_hproyectopersonal.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hproyectopersonal.codperi ".
				"   AND sno_hpersonalnomina.codnom = sno_hproyectopersonal.codnom ".
				"   AND sno_hpersonalnomina.codper = sno_hproyectopersonal.codper ".
				"   AND sno_hpersonalnomina.codemp = sno_personal.codemp ".
				"   AND sno_hpersonalnomina.codper = sno_personal.codper ".
				"   ".$ls_orden;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_listadoproyecto_proyectospersonal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS_detalle->data=$this->io_sql->obtener_datos($rs_data);	
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;

	}// end function uf_listadoproyecto_proyectospersonal
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_listadoproyectopersonal_personal($as_codperdes,$as_codperhas,$as_subnomdes,$as_subnomhas,$as_orden)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_listadoproyectopersonal_personal
		//         Access: public (desde la clase sigesp_sno_rpp_listadoproyecto)  
		//	    Arguments: as_codperdes // C�digo del personal donde se empieza a filtrar
		//				   as_codperhas // C�digo del personal donde se termina de filtrar
		//	  			   as_orden // orden por medio del cual se desea que salga el reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: funci�n que busca la informaci�n del personal que tiene asociado proyectos
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 01/08/2007 								Fecha �ltima Modificaci�n :  
		// SE MODIFICARON LAS TABLAS TH POR H
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		switch($as_orden)
		{
			case "1": // Ordena por C�digo de personal
				$ls_orden="ORDER BY sno_personal.codper ";
				break;

			case "2": // Ordena por Apellido de personal
				$ls_orden="ORDER BY sno_personal.apeper ";
				break;

			case "3": // Ordena por Nombre de personal
				$ls_orden="ORDER BY sno_personal.nomper ";
				break;

			case "4": // Ordena por C�dula de personal
				$ls_orden="ORDER BY sno_personal.cedper ";
				break;
		}
		if(!empty($as_codperdes))
		{
			$ls_criterio= "AND sno_hproyectopersonal.codper>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hproyectopersonal.codper<='".$as_codperhas."'";
		}
		if(!empty($as_subnomdes))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codsubnom>='".$as_subnomdes."'";
		}
		if(!empty($as_subnomhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codsubnom<='".$as_subnomhas."'";
		}
		$ls_sql="SELECT sno_personal.codper, MAX(sno_personal.nomper) AS nomper, MAX(sno_personal.apeper) AS apeper, ".
				"		count(sno_hproyectopersonal.codproy) as total, sum(sno_hproyectopersonal.pordiames*100) as monto ".
				"  FROM sno_hproyectopersonal, sno_hproyecto, sno_personal, sno_hpersonalnomina ".
				" WHERE sno_hproyectopersonal.codemp='".$this->ls_codemp."' ".
				"   AND sno_hproyectopersonal.codnom='".$this->ls_codnom."' ".
				"   AND sno_hproyectopersonal.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hproyectopersonal.codperi='".$this->ls_peractnom."' ".
				"   ".$ls_criterio.
				"   AND sno_hproyectopersonal.codemp = sno_hproyecto.codemp ".
				"   AND sno_hproyectopersonal.codnom = sno_hproyecto.codnom ".
				"   AND sno_hproyectopersonal.anocur = sno_hproyecto.anocur ".
				"   AND sno_hproyectopersonal.codperi = sno_hproyecto.codperi ".
				"   AND sno_hproyectopersonal.codproy = sno_hproyecto.codproy ".
				"   AND sno_hproyectopersonal.codemp = sno_hpersonalnomina.codemp ".
				"   AND sno_hproyectopersonal.codnom = sno_hpersonalnomina.codnom ".
				"   AND sno_hproyectopersonal.anocur = sno_hpersonalnomina.anocur ".
				"   AND sno_hproyectopersonal.codperi = sno_hpersonalnomina.codperi ".
				"   AND sno_hproyectopersonal.codper = sno_hpersonalnomina.codper ".
				"   AND sno_hpersonalnomina.codemp = sno_personal.codemp ".
				"   AND sno_hpersonalnomina.codper = sno_personal.codper ".
				" GROUP BY sno_personal.codper  ".
				$ls_orden;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_listadoproyectopersonal_personal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);		
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_listadoproyectopersonal_personal
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_listadoproyectopersonal_proyecto($as_codper)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_listadoproyectopersonal_proyecto
		//         Access: public (desde la clase sigesp_sno_rpp_listadoproyecto)  
		//	    Arguments: as_codper // C�digo del personal
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: funci�n que busca la informaci�n de los proyectos asociados al personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 01/08/2007 								Fecha �ltima Modificaci�n : 
		// SE MODIFICARON LAS TABLAS TH POR H
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_sql="SELECT sno_hproyecto.codproy, sno_hproyecto.nomproy, (sno_hproyectopersonal.pordiames*100) AS pordiames ".
				"  FROM sno_hproyectopersonal, sno_hproyecto ".
				" WHERE sno_hproyectopersonal.codemp='".$this->ls_codemp."' ".
				"   AND sno_hproyectopersonal.codnom='".$this->ls_codnom."' ".
				"   AND sno_hproyectopersonal.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hproyectopersonal.codperi='".$this->ls_peractnom."' ".
				"   AND sno_hproyectopersonal.codper='".$as_codper."' ".
				"   AND sno_hproyectopersonal.codemp = sno_hproyecto.codemp ".
				"   AND sno_hproyectopersonal.codnom = sno_hproyecto.codnom ".
				"   AND sno_hproyectopersonal.anocur = sno_hproyecto.anocur ".
				"   AND sno_hproyectopersonal.codperi = sno_hproyecto.codperi ".
				"   AND sno_hproyectopersonal.codproy = sno_hproyecto.codproy ".
				" ORDER BY sno_hproyecto.codproy ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_listadoproyectopersonal_proyecto ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS_detalle->data=$this->io_sql->obtener_datos($rs_data);		
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_listadoproyectopersonal_proyecto
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_pagonominaunidad_unidad($as_codperdes,$as_codperhas,$as_conceptocero,$as_conceptoreporte,$as_conceptop2,
										  $as_coduniadmdes,$as_coduniadmhas,$as_subnomdes,$as_subnomhas)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_pagonominaunidad_unidad
		//         Access: public (desde la clase sigesp_sno_rpp_pagonominaunidadadmin)  
		//	    Arguments: as_codperdes // C�digo del personal donde se empieza a filtrar
		//	  			   as_codperhas // C�digo del personal donde se termina de filtrar		  
		//	  			   as_conceptocero // criterio que me indica si se desea quitar los conceptos cuyo valor es cero
		//	  			   as_conceptoreporte // criterio que me indica si se desea mostrar los conceptos tipo reporte
		//	  			   as_conceptop2 // criterio que me indica si se desea mostrar los conceptos de tipo aporte patronal
		//	    		   as_coduniadmdes // C�digo de Unidad Administrativa donde se empieza a filtrar
		//	  			   as_coduniadmhas // C�digo de Unidad Administrativa donde se termina de filtrar		  
		//	  			   as_orden // orden por medio del cual se desea que salga el reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: funci�n que busca la informaci�n de las unidades administrativas del personal que se le calcul� la n�mina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 07/08/2007								Fecha �ltima Modificaci�n : 
		// SE MODIFICARON LAS TABLAS TH POR H
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_criteriounion="";
		if(!empty($as_codperdes))
		{
			$ls_criterio= " AND sno_hpersonalnomina.codper>='".$as_codperdes."'";
			$ls_criteriounion=" AND sno_hpersonalnomina.codper>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codper<='".$as_codperhas."'";
			$ls_criteriounion = $ls_criteriounion."   AND sno_hpersonalnomina.codper<='".$as_codperhas."'";
		}
		if(!empty($as_subnomdes))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codsubnom>='".$as_subnomdes."'";
			$ls_criteriounion= $ls_criteriounion."   AND sno_hpersonalnomina.codsubnom>='".$as_subnomdes."'";
		}
		if(!empty($as_subnomhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codsubnom<='".$as_subnomhas."'";
			$ls_criteriounion= $ls_criteriounion."   AND sno_hpersonalnomina.codsubnom<='".$as_subnomhas."'";
		}
		if(!empty($as_coduniadmdes))
		{
			$ls_criterio= $ls_criterio." AND sno_hpersonalnomina.minorguniadm>='".substr($as_coduniadmdes,0,4)."'".
						  			   " AND sno_hpersonalnomina.ofiuniadm>='".substr($as_coduniadmdes,5,2)."' ".
						               " AND sno_hpersonalnomina.uniuniadm>='".substr($as_coduniadmdes,8,2)."' ".
						               " AND sno_hpersonalnomina.depuniadm>='".substr($as_coduniadmdes,11,2)."' ".
						               " AND sno_hpersonalnomina.prouniadm>='".substr($as_coduniadmdes,14,2)."' ";
			$ls_criteriounion= $ls_criteriounion." AND sno_hpersonalnomina.minorguniadm>='".substr($as_coduniadmdes,0,4)."'".
						  	   					 " AND sno_hpersonalnomina.ofiuniadm>='".substr($as_coduniadmdes,5,2)."' ".
						       					 " AND sno_hpersonalnomina.uniuniadm>='".substr($as_coduniadmdes,8,2)."' ".
						       					 " AND sno_hpersonalnomina.depuniadm>='".substr($as_coduniadmdes,11,2)."' ".
						       					 " AND sno_hpersonalnomina.prouniadm>='".substr($as_coduniadmdes,14,2)."' ";
		}
		if(!empty($as_coduniadmhas))
		{
			$ls_criterio= $ls_criterio." AND sno_hpersonalnomina.minorguniadm<='".substr($as_coduniadmhas,0,4)."'".
						  			   " AND sno_hpersonalnomina.ofiuniadm<='".substr($as_coduniadmhas,5,2)."' ".
						               " AND sno_hpersonalnomina.uniuniadm<='".substr($as_coduniadmdes,8,2)."' ".
						               " AND sno_hpersonalnomina.depuniadm<='".substr($as_coduniadmhas,11,2)."' ".
						               " AND sno_hpersonalnomina.prouniadm<='".substr($as_coduniadmhas,14,2)."' ";
			$ls_criteriounion= $ls_criteriounion." AND sno_hpersonalnomina.minorguniadm<='".substr($as_coduniadmhas,0,4)."'".
						  	   					 " AND sno_hpersonalnomina.ofiuniadm<='".substr($as_coduniadmhas,5,2)."' ".
						       					 " AND sno_hpersonalnomina.uniuniadm<='".substr($as_coduniadmhas,8,2)."' ".
						       					 " AND sno_hpersonalnomina.depuniadm<='".substr($as_coduniadmhas,11,2)."' ".
						       					 " AND sno_hpersonalnomina.prouniadm<='".substr($as_coduniadmhas,14,2)."' ";
		}
		if(!empty($as_conceptocero))
		{
			$ls_criterio = $ls_criterio."   AND sno_hsalida.valsal<>0 ";
		}
		if(!empty($as_conceptoreporte))
		{
			if(!empty($as_conceptop2))
			{
				$ls_criterio = $ls_criterio." AND (sno_hsalida.tipsal='A' OR sno_hsalida.tipsal='V1' OR sno_hsalida.tipsal='W1' OR ".
											"	   sno_hsalida.tipsal='D' OR sno_hsalida.tipsal='V2' OR sno_hsalida.tipsal='W2' OR ".
							   				"      sno_hsalida.tipsal='P1' OR sno_hsalida.tipsal='V3' OR sno_hsalida.tipsal='W3' OR ".
											"	   sno_hsalida.tipsal='P2' OR sno_hsalida.tipsal='V4' OR sno_hsalida.tipsal='W4' OR ".
											"	   sno_hsalida.tipsal='R')";
			}
			else
			{
				$ls_criterio = $ls_criterio." AND (sno_hsalida.tipsal='A' OR sno_hsalida.tipsal='V1' OR sno_hsalida.tipsal='W1' OR ".
											"	   sno_hsalida.tipsal='D' OR sno_hsalida.tipsal='V2' OR sno_hsalida.tipsal='W2' OR ".
							   				"      sno_hsalida.tipsal='P1' OR sno_hsalida.tipsal='V3' OR sno_hsalida.tipsal='W3' OR ".
											"	   sno_hsalida.tipsal='R')";
			}
		}
		else
		{
			if(!empty($as_conceptop2))
			{
				$ls_criterio = $ls_criterio." AND (sno_hsalida.tipsal='A' OR sno_hsalida.tipsal='V1' OR sno_hsalida.tipsal='W1' OR ".
											"	   sno_hsalida.tipsal='D' OR sno_hsalida.tipsal='V2' OR sno_hsalida.tipsal='W2' OR ".
							   				"      sno_hsalida.tipsal='P1' OR sno_hsalida.tipsal='V3' OR sno_hsalida.tipsal='W3' OR ".
											"	   sno_hsalida.tipsal='P2' OR sno_hsalida.tipsal='V4' OR sno_hsalida.tipsal='W4') ";
			}
			else
			{
				$ls_criterio = $ls_criterio." AND (sno_hsalida.tipsal='A' OR sno_hsalida.tipsal='V1' OR sno_hsalida.tipsal='W1' OR ".
											"	   sno_hsalida.tipsal='D' OR sno_hsalida.tipsal='V2' OR sno_hsalida.tipsal='W2' OR ".
							   				"      sno_hsalida.tipsal='P1' OR sno_hsalida.tipsal='V3' OR sno_hsalida.tipsal='W3')";
			}
		}
		$ls_union="";
		$li_vac_reportar=trim($this->uf_select_config("SNO","NOMINA","MOSTRAR VACACION","0","C"));
		$ls_vac_codconvac=trim($this->uf_select_config("SNO","NOMINA","COD CONCEPTO VACACION","","C"));
		if(($li_vac_reportar==1)&&($ls_vac_codconvac!=""))
		{
			$ls_union="UNION ".
					  "SELECT sno_hunidadadmin.minorguniadm, sno_hunidadadmin.ofiuniadm, sno_hunidadadmin.uniuniadm, ".
					  "    	  sno_hunidadadmin.depuniadm, sno_hunidadadmin.prouniadm, MAX(sno_hunidadadmin.desuniadm) AS desuniadm ".
					  "  FROM sno_hpersonalnomina, sno_hsalida, sno_hunidadadmin ".
					  " WHERE sno_hpersonalnomina.codemp='".$this->ls_codemp."' ".
					  "   AND sno_hpersonalnomina.codnom='".$this->ls_codnom."' ".
					  "   AND sno_hpersonalnomina.anocur='".$this->ls_anocurnom."' ".
					  "   AND sno_hpersonalnomina.codperi='".$this->ls_peractnom."' ".
					  "	  AND sno_hpersonalnomina.staper = '2' ".
					  "   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
					  "   AND sno_hsalida.codconc='".$ls_vac_codconvac."' ".
					  "   ".$ls_criteriounion.
					  "   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
					  "   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
					  "   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
					  "   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
					  "   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
					  "   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
					  "   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
					  "   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
					  "   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
					  "   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
					  "   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
					  "   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
					  "   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
					  " GROUP BY sno_hunidadadmin.minorguniadm, sno_hunidadadmin.ofiuniadm, sno_hunidadadmin.uniuniadm, ".
					  "		   sno_hunidadadmin.depuniadm, sno_hunidadadmin.prouniadm ";
		}
		$ls_sql="SELECT sno_hunidadadmin.minorguniadm, sno_hunidadadmin.ofiuniadm, sno_hunidadadmin.uniuniadm,sno_hunidadadmin.depuniadm,  ".
				"    	sno_hunidadadmin.prouniadm, MAX(sno_hunidadadmin.desuniadm) AS desuniadm   ".
				"  FROM sno_hpersonalnomina, sno_hsalida, sno_hunidadadmin ".
				" WHERE sno_hpersonalnomina.codemp='".$this->ls_codemp."' ".
				"   AND sno_hpersonalnomina.codnom='".$this->ls_codnom."' ".
				"   AND sno_hpersonalnomina.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hpersonalnomina.codperi='".$this->ls_peractnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   ".$ls_criterio.
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				" GROUP BY sno_hunidadadmin.minorguniadm, sno_hunidadadmin.ofiuniadm, sno_hunidadadmin.uniuniadm, ".
				"		   sno_hunidadadmin.depuniadm, sno_hunidadadmin.prouniadm  ".
				"   ".$ls_union.
				" ORDER BY minorguniadm, ofiuniadm, uniuniadm, depuniadm, prouniadm "; 
		$this->rs_data=$this->io_sql->select($ls_sql);
		if($this->rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_pagonominaunidad_unidad ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_pagonominaunidad_unidad
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_pagonominaunidad_personal($as_codperdes,$as_codperhas,$as_conceptocero,$as_conceptoreporte,$as_conceptop2,
										  $as_minorguniadm,$as_ofiuniadm,$as_uniuniadm,$as_depuniadm,$as_prouniadm,$as_orden)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_pagonominaunidad_personal
		//         Access: public (desde la clase sigesp_sno_rpp_pagonomina)  
		//	    Arguments: as_codperdes // C�digo del personal donde se empieza a filtrar
		//	  			   as_codperhas // C�digo del personal donde se termina de filtrar		  
		//	  			   as_conceptocero // criterio que me indica si se desea quitar los conceptos cuyo valor es cero
		//	  			   as_conceptoreporte // criterio que me indica si se desea mostrar los conceptos tipo reporte
		//	  			   as_conceptop2 // criterio que me indica si se desea mostrar los conceptos de tipo aporte patronal
		//	    		   as_minorguniadm // C�digo de la unidad
		//	   			   as_ofiuniadm // C�digo de la unidad
		//	   			   as_uniuniadm // C�digo de la unidad
		//	   			   as_depuniadm // C�digo de la unidad
		//	   			   as_prouniadm // C�digo de la unidad
		//	   			   as_desuniadm // Descripci�n de la unidad
		//	  			   as_orden // orden por medio del cual se desea que salga el reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: funci�n que busca la informaci�n del personal que se le calcul� la n�mina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 07/08/2007								Fecha �ltima Modificaci�n : 
		// SE MODIFICARON LAS TABLAS TH POR H
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		$ls_criteriounion="";
		if(!empty($as_codperdes))
		{
			$ls_criterio= " AND sno_hpersonalnomina.codper>='".$as_codperdes."'";
			$ls_criteriounion=" AND sno_hpersonalnomina.codper>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codper<='".$as_codperhas."'";
			$ls_criteriounion = $ls_criteriounion."   AND sno_hpersonalnomina.codper<='".$as_codperhas."'";
		}
		$ls_criterio= $ls_criterio." AND sno_hpersonalnomina.minorguniadm='".$as_minorguniadm."'".
								   " AND sno_hpersonalnomina.ofiuniadm='".$as_ofiuniadm."' ".
								   " AND sno_hpersonalnomina.uniuniadm='".$as_uniuniadm."' ".
								   " AND sno_hpersonalnomina.depuniadm='".$as_depuniadm."' ".
								   " AND sno_hpersonalnomina.prouniadm='".$as_prouniadm."' ";
		$ls_criteriounion= $ls_criteriounion." AND sno_hpersonalnomina.minorguniadm='".$as_minorguniadm."'".
											 " AND sno_hpersonalnomina.ofiuniadm='".$as_ofiuniadm."' ".
											 " AND sno_hpersonalnomina.uniuniadm='".$as_uniuniadm."' ".
											 " AND sno_hpersonalnomina.depuniadm='".$as_depuniadm."' ".
											 " AND sno_hpersonalnomina.prouniadm='".$as_prouniadm."' ";
		if(!empty($as_conceptocero))
		{
			$ls_criterio = $ls_criterio."   AND sno_hsalida.valsal<>0 ";
		}
		if(!empty($as_conceptoreporte))
		{
			if(!empty($as_conceptop2))
			{
				$ls_criterio = $ls_criterio." AND (sno_hsalida.tipsal='A' OR sno_hsalida.tipsal='V1' OR sno_hsalida.tipsal='W1' OR ".
											"	   sno_hsalida.tipsal='D' OR sno_hsalida.tipsal='V2' OR sno_hsalida.tipsal='W2' OR ".
							   				"      sno_hsalida.tipsal='P1' OR sno_hsalida.tipsal='V3' OR sno_hsalida.tipsal='W3' OR ".
											"	   sno_hsalida.tipsal='P2' OR sno_hsalida.tipsal='V4' OR sno_hsalida.tipsal='W4' OR ".
											"	   sno_hsalida.tipsal='R')";
			}
			else
			{
				$ls_criterio = $ls_criterio." AND (sno_hsalida.tipsal='A' OR sno_hsalida.tipsal='V1' OR sno_hsalida.tipsal='W1' OR ".
											"	   sno_hsalida.tipsal='D' OR sno_hsalida.tipsal='V2' OR sno_hsalida.tipsal='W2' OR ".
							   				"      sno_hsalida.tipsal='P1' OR sno_hsalida.tipsal='V3' OR sno_hsalida.tipsal='W3' OR ".
											"	   sno_hsalida.tipsal='R')";
			}
		}
		else
		{
			if(!empty($as_conceptop2))
			{
				$ls_criterio = $ls_criterio." AND (sno_hsalida.tipsal='A' OR sno_hsalida.tipsal='V1' OR sno_hsalida.tipsal='W1' OR ".
											"	   sno_hsalida.tipsal='D' OR sno_hsalida.tipsal='V2' OR sno_hsalida.tipsal='W2' OR ".
							   				"      sno_hsalida.tipsal='P1' OR sno_hsalida.tipsal='V3' OR sno_hsalida.tipsal='W3' OR ".
											"	   sno_hsalida.tipsal='P2' OR sno_hsalida.tipsal='V4' OR sno_hsalida.tipsal='W4') ";
			}
			else
			{
				$ls_criterio = $ls_criterio." AND (sno_hsalida.tipsal='A' OR sno_hsalida.tipsal='V1' OR sno_hsalida.tipsal='W1' OR ".
											"	   sno_hsalida.tipsal='D' OR sno_hsalida.tipsal='V2' OR sno_hsalida.tipsal='W2' OR ".
							   				"      sno_hsalida.tipsal='P1' OR sno_hsalida.tipsal='V3' OR sno_hsalida.tipsal='W3')";
			}
		}
		if(empty($as_orden))
		{
			$ls_orden=" ORDER BY codper ";
		}
		else
		{
			switch($as_orden)
			{
				case "1": // Ordena por C�digo de personal
					$ls_orden=" ORDER BY codper ";
					break;

				case "2": // Ordena por Apellido de personal
					$ls_orden=" ORDER BY apeper ";
					break;

				case "3": // Ordena por Nombre de personal
					$ls_orden=" ORDER BY nomper ";
					break;
			}
		}
		if($this->li_rac=="1") // Utiliza RAC
		{
			$ls_descar="       MAX((SELECT denasicar FROM sno_hasignacioncargo ".
					   "   	     WHERE sno_hpersonalnomina.codemp='".$this->ls_codemp."' ".
					   "           AND sno_hpersonalnomina.codnom='".$this->ls_codnom."' ".
					   "		   AND sno_hpersonalnomina.codemp = sno_hasignacioncargo.codemp ".
					   "		   AND sno_hpersonalnomina.codnom = sno_hasignacioncargo.codnom ".
					   "		   AND sno_hpersonalnomina.anocur = sno_hasignacioncargo.anocur ".
					   "		   AND sno_hpersonalnomina.codperi = sno_hasignacioncargo.codperi ".
				       "           AND sno_hpersonalnomina.codasicar = sno_hasignacioncargo.codasicar)) as descar ";
		}
		else // No utiliza RAC
		{
			$ls_descar="      MAX((SELECT descar FROM sno_hcargo ".
					   "   	     WHERE sno_hpersonalnomina.codemp='".$this->ls_codemp."' ".
					   "           AND sno_hpersonalnomina.codnom='".$this->ls_codnom."' ".
					   "		   AND sno_hpersonalnomina.codemp = sno_hcargo.codemp ".
					   "		   AND sno_hpersonalnomina.codnom = sno_hcargo.codnom ".
					   "		   AND sno_hpersonalnomina.anocur = sno_hcargo.anocur ".
					   "		   AND sno_hpersonalnomina.codperi = sno_hcargo.codperi ".
				       "           AND sno_hpersonalnomina.codcar = sno_hcargo.codcar)) as descar ";
		}
		$ls_union="";
		$li_vac_reportar=trim($this->uf_select_config("SNO","NOMINA","MOSTRAR VACACION","0","C"));
		$ls_vac_codconvac=trim($this->uf_select_config("SNO","NOMINA","COD CONCEPTO VACACION","","C"));
		if(($li_vac_reportar==1)&&($ls_vac_codconvac!=""))
		{
			$ls_union="UNION ".
					  "SELECT sno_hpersonalnomina.codper, sno_personal.cedper, sno_personal.nomper, sno_personal.apeper, sno_personal.fecingper, ".
					  "   	  sno_hpersonalnomina.codcueban, sno_hunidadadmin.desuniadm, sno_hunidadadmin.codprouniadm, MAX(sno_hpersonalnomina.sueper) AS sueper, ".
					  "		  sno_hunidadadmin.minorguniadm, sno_hunidadadmin.ofiuniadm, sno_hunidadadmin.uniuniadm, sno_hunidadadmin.depuniadm, sno_hunidadadmin.prouniadm, ".
					  "		  MAX(sno_hpersonalnomina.codgra) AS codgra, ".
  					  $ls_descar.
					  "  FROM sno_personal, sno_hpersonalnomina, sno_hsalida, sno_hunidadadmin ".
					  " WHERE sno_hpersonalnomina.codemp='".$this->ls_codemp."' ".
					  "   AND sno_hpersonalnomina.codnom='".$this->ls_codnom."' ".
					  "   AND sno_hpersonalnomina.anocur='".$this->ls_anocurnom."' ".
					  "   AND sno_hpersonalnomina.codperi='".$this->ls_peractnom."' ".
					  "	  AND sno_hpersonalnomina.staper = '2' ".
					  "   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
					  "   AND sno_hsalida.codconc='".$ls_vac_codconvac."' ".
					  "   ".$ls_criteriounion.
					  "   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
					  "   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
					  "   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
					  "   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
					  "   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
					  "   AND sno_personal.codemp = sno_hpersonalnomina.codemp ".
					  "   AND sno_personal.codper = sno_hpersonalnomina.codper ".
					  "   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
					  "   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
					  "   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
					  "   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
					  "   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
					  "   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
					  "   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
					  "   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
					  " GROUP BY sno_hpersonalnomina.codemp, sno_hpersonalnomina.codnom, sno_hpersonalnomina.codper, ".
					  "		   sno_personal.cedper, sno_personal.nomper, sno_personal.apeper, sno_personal.fecingper, ".
					  "		   sno_hpersonalnomina.codcueban, sno_hunidadadmin.desuniadm, sno_hunidadadmin.desuniadm, ".
					  "		   sno_hunidadadmin.codprouniadm, sno_hpersonalnomina.codcar, sno_hpersonalnomina.codasicar, ".
					  "		   sno_hunidadadmin.minorguniadm, sno_hunidadadmin.ofiuniadm, sno_hunidadadmin.uniuniadm, ".
					  "    	   sno_hunidadadmin.depuniadm, sno_hunidadadmin.prouniadm ";
		}
		$ls_sql="SELECT sno_hpersonalnomina.codper, sno_personal.cedper, sno_personal.nomper, sno_personal.apeper, sno_personal.fecingper, ".
				"		sno_hpersonalnomina.codcueban, sno_hunidadadmin.desuniadm, sno_hunidadadmin.codprouniadm, MAX(sno_hpersonalnomina.sueper) AS sueper, ".
				"		sno_hunidadadmin.minorguniadm, sno_hunidadadmin.ofiuniadm, sno_hunidadadmin.uniuniadm, sno_hunidadadmin.depuniadm, sno_hunidadadmin.prouniadm, ".
			    "		  MAX(sno_hpersonalnomina.codgra) AS codgra, ".
				$ls_descar.
				"  FROM sno_personal, sno_hpersonalnomina, sno_hsalida, sno_hunidadadmin ".
				" WHERE sno_hpersonalnomina.codemp='".$this->ls_codemp."' ".
				"   AND sno_hpersonalnomina.codnom='".$this->ls_codnom."' ".
				"   AND sno_hpersonalnomina.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hpersonalnomina.codperi='".$this->ls_peractnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   ".$ls_criterio.
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"   AND sno_personal.codemp = sno_hpersonalnomina.codemp ".
				"   AND sno_personal.codper = sno_hpersonalnomina.codper ".
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				" GROUP BY sno_hpersonalnomina.codemp, sno_hpersonalnomina.codnom, sno_hpersonalnomina.codper, ".
				"		   sno_personal.cedper, sno_personal.nomper, sno_personal.apeper, sno_personal.fecingper, ".
				"		   sno_hpersonalnomina.codcueban, sno_hunidadadmin.desuniadm, sno_hunidadadmin.desuniadm, ".
				"		   sno_hunidadadmin.codprouniadm, sno_hpersonalnomina.codcar, sno_hpersonalnomina.codasicar, ".
				"		   sno_hunidadadmin.minorguniadm, sno_hunidadadmin.ofiuniadm, sno_hunidadadmin.uniuniadm, ".
			    "    	   sno_hunidadadmin.depuniadm, sno_hunidadadmin.prouniadm, sno_personal.codper ".
				"   ".$ls_union.
				"   ".$ls_orden;
		$this->rs_data_detalle=$this->io_sql->select($ls_sql);
		if($this->rs_data_detalle===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_pagonominaunidad_personal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_pagonominaunidad_personal
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_pagonominaunidad_conceptopersonal($as_codper,$as_conceptocero,$as_tituloconcepto,$as_conceptoreporte,$as_conceptop2)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_pagonominaunidad_conceptopersonal
		//         Access: public (desde la clase sigesp_sno_rpp_pagonominaunidadadmin)  
		//	    Arguments: as_codper // C�digo del personal que se desea buscar la salida
		//	  			   as_conceptocero // criterio que me indica si se desea quitar los conceptos en cero
		//	  			   as_tituloconcepto // criterio que me indica si se desea mostrar el t�tulo del concepto � el nombre
		//	  			   as_conceptoreporte // criterio que me indica si se desea mostrar los conceptos tipo reporte
		//	  			   as_conceptop2 // criterio que me indica si se desea mostrar los conceptos de tipo aporte patronal
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: funci�n que busca la informaci�n de los conceptos asociados al personal que se le calcul� la n�mina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 08/08/2007 								Fecha �ltima Modificaci�n : 
		// SE MODIFICARON LAS TABLAS TH POR H
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_campo="sno_hconcepto.nomcon";
		if(!empty($as_tituloconcepto))
		{
			$ls_campo = "sno_hconcepto.titcon";
		}
		if(!empty($as_conceptocero))
		{
			$ls_criterio = "AND sno_hsalida.valsal<>0 ";
		}
		if(!empty($as_conceptoreporte))
		{
			if(!empty($as_conceptop2))
			{
				$ls_criterio = $ls_criterio." AND (sno_hsalida.tipsal='A' OR sno_hsalida.tipsal='V1' OR sno_hsalida.tipsal='W1' OR ".
											"	   sno_hsalida.tipsal='D' OR sno_hsalida.tipsal='V2' OR sno_hsalida.tipsal='W2' OR ".
											"      sno_hsalida.tipsal='P1' OR sno_hsalida.tipsal='V3' OR sno_hsalida.tipsal='W3' OR ".
											"	   sno_hsalida.tipsal='P2' OR sno_hsalida.tipsal='V4' OR sno_hsalida.tipsal='W4' OR ".
											"	   sno_hsalida.tipsal='R')";
			}
			else
			{
				$ls_criterio = $ls_criterio." AND (sno_hsalida.tipsal='A' OR sno_hsalida.tipsal='V1' OR sno_hsalida.tipsal='W1' OR ".
											"	   sno_hsalida.tipsal='D' OR sno_hsalida.tipsal='V2' OR sno_hsalida.tipsal='W2' OR ".
											"      sno_hsalida.tipsal='P1' OR sno_hsalida.tipsal='V3' OR sno_hsalida.tipsal='W3' OR ".
											"	   sno_hsalida.tipsal='R')";
			}
		}
		else
		{
			if(!empty($as_conceptop2))
			{
				$ls_criterio = $ls_criterio." AND (sno_hsalida.tipsal='A' OR sno_hsalida.tipsal='V1' OR sno_hsalida.tipsal='W1' OR ".
											"	   sno_hsalida.tipsal='D' OR sno_hsalida.tipsal='V2' OR sno_hsalida.tipsal='W2' OR ".
											"      sno_hsalida.tipsal='P1' OR sno_hsalida.tipsal='V3' OR sno_hsalida.tipsal='W3' OR ".
											"	   sno_hsalida.tipsal='P2' OR sno_hsalida.tipsal='V4' OR sno_hsalida.tipsal='W4') ";
			}
			else
			{
				$ls_criterio = $ls_criterio." AND (sno_hsalida.tipsal='A' OR sno_hsalida.tipsal='V1' OR sno_hsalida.tipsal='W1' OR ".
											"	   sno_hsalida.tipsal='D' OR sno_hsalida.tipsal='V2' OR sno_hsalida.tipsal='W2' OR ".
											"      sno_hsalida.tipsal='P1' OR sno_hsalida.tipsal='V3' OR sno_hsalida.tipsal='W3')";
			}
		}
		$ls_union="";
		$li_vac_reportar=trim($this->uf_select_config("SNO","NOMINA","MOSTRAR VACACION","0","C"));
		$ls_vac_codconvac=trim($this->uf_select_config("SNO","NOMINA","COD CONCEPTO VACACION","","C"));
		if(($li_vac_reportar==1)&&($ls_vac_codconvac!=""))
		{
			$ls_union="UNION ".
					  "SELECT sno_hconcepto.codconc, ".$ls_campo." as nomcon, sno_hsalida.valsal, sno_hsalida.tipsal ".
					  "  FROM sno_hsalida, sno_hconcepto, sno_hpersonalnomina ".
					  " WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
					  "   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
					  "   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
					  "   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
					  "   AND sno_hsalida.codper='".$as_codper."'".
					  "   AND sno_hsalida.codconc='".$ls_vac_codconvac."'".
					  "   AND sno_hpersonalnomina.staper = '2' ".
					  "   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
					  "   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
					  "   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
					  "   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
					  "   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
					  "   AND sno_hsalida.codemp = sno_hpersonalnomina.codemp ".
					  "   AND sno_hsalida.codnom = sno_hpersonalnomina.codnom ".
					  "   AND sno_hsalida.anocur = sno_hpersonalnomina.anocur ".
					  "   AND sno_hsalida.codperi = sno_hpersonalnomina.codperi ".
					  "   AND sno_hsalida.codper = sno_hpersonalnomina.codper ";
		}
		$ls_sql="SELECT sno_hconcepto.codconc, ".$ls_campo." as nomcon, sno_hsalida.valsal, sno_hsalida.tipsal ".
				"  FROM sno_hsalida, sno_hconcepto ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND sno_hsalida.codper='".$as_codper."'".
				"   ".$ls_criterio.
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   ".$ls_union.
				" ORDER BY codconc ";
		$this->rs_data_detalle2=$this->io_sql->select($ls_sql);
		if($this->rs_data_detalle2===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_pagonominaunidad_conceptopersonal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_pagonominaunidad_conceptopersonal
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_listado_asignaciocargo($as_coddes,$as_codhas,$as_orden)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_listado_asignaciocargo
		//         Access: public (desde la clase sigesp_sno_rpp_prenomina)  
		//	    Arguments: 
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: funci�n que busca la informaci�n de los cargos asigandos por n�mina
		//	   Creado Por: Ing. Jennifer Rivero
		// Fecha Creaci�n: 29/04/2008 								Fecha �ltima Modificaci�n :  
		// SE MODIFICARON LAS TABLAS TH POR H
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_codnom=$_SESSION["la_nomina"]["codnom"];
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];
			
		$ls_peractnom=$_SESSION["la_nomina"]["peractnom"];		
		if(!empty($as_coddes))
		{
		  if (!empty($as_codhas))
		   {
		     	$ls_criterio = " and sno_hasignacioncargo.codasicar BETWEEN '".$as_coddes."' and '".$as_codhas."'";
		   }
		}		
		
		switch($as_orden)
			{
				case "1": // Ordena por C�digo de Asignaci�n de Cargo
					$ls_orden=" ORDER BY sno_hasignacioncargo.codasicar ";
					break;

				case "2": // Ordena por el Nombre de la Asignaci�n de Cargo
					$ls_orden=" ORDER BY sno_hasignacioncargo.denasicar ";
					break;

				
			}
			
				$ls_sql=" SELECT sno_hasignacioncargo.codasicar, sno_hasignacioncargo.codnom,sno_hasignacioncargo.denasicar, ".
		        		" sno_hasignacioncargo.codtab, ".
       					" sno_hasignacioncargo.codgra, sno_hasignacioncargo.codpas, sno_hasignacioncargo.grado, ". 
       					" sno_hunidadadmin.minorguniadm,sno_hunidadadmin.ofiuniadm,sno_hunidadadmin.uniuniadm, ".
						" sno_hunidadadmin.depuniadm, ".
               		    " sno_hunidadadmin.prouniadm, sno_hunidadadmin.desuniadm, ".
               		    " sno_htabulador.destab,sno_hasignacioncargo.numvacasicar, ".
                		" (SELECT count (sno_hpersonalnomina.codasicar) from sno_hpersonalnomina ".  
                		"         WHERE sno_hpersonalnomina.codasicar=sno_hasignacioncargo.codasicar ".
                		"         AND sno_hpersonalnomina.codnom=sno_hasignacioncargo.codnom ".
                		"         AND sno_hpersonalnomina.codemp=sno_hasignacioncargo.codemp) as ocupado ".
                		"  FROM sno_hasignacioncargo   ".
                		"  JOIN sno_hunidadadmin on (sno_hasignacioncargo.codemp=sno_hunidadadmin.codemp  ".
                        "        AND sno_hasignacioncargo.codnom=sno_hunidadadmin.codnom  ".
                        "        AND sno_hasignacioncargo.anocur=sno_hunidadadmin.anocur   ".
                        "        AND sno_hasignacioncargo.uniuniadm=sno_hunidadadmin.uniuniadm  ".
                        "        AND sno_hasignacioncargo.minorguniadm=sno_hunidadadmin.minorguniadm  ".
                        "        AND sno_hasignacioncargo.ofiuniadm=sno_hunidadadmin.ofiuniadm  ".
                        "        AND sno_hasignacioncargo.depuniadm=sno_hunidadadmin.depuniadm  ".
                        "        AND sno_hasignacioncargo.prouniadm=sno_hunidadadmin.prouniadm)  ".
         				" JOIN sno_htabulador on (sno_hasignacioncargo.codtab=sno_htabulador.codtab  ".
                        "      AND sno_hasignacioncargo.codemp=sno_htabulador.codemp     ".
                        "      AND sno_hasignacioncargo.codnom=sno_htabulador.codnom     ".
                        "      AND sno_hasignacioncargo.codperi=sno_htabulador.codperi   ".
                        "      and sno_hasignacioncargo.anocur=sno_htabulador.anocur)    ".
   						" WHERE sno_hasignacioncargo.codnom='".$ls_codnom."'". 
						"   and  sno_hasignacioncargo.codemp='".$ls_codemp."'".
						"   and  sno_hasignacioncargo.anocur='".$this->ls_anocurnom."' ".
						"   and  sno_hasignacioncargo.codperi='".$this->ls_peractnom."' ".$ls_criterio.$ls_orden; 
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_listado_asignaciocargo ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS_asigna->data=$this->io_sql->obtener_datos($rs_data);		
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_prenomina_conceptopersonal
	
//--------------------------------------------------------------------------------------------------------------------------------	
function uf_seleccionar_quincenas($as_codper,&$as_priqui,&$as_segqui)
{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_recibo_nomina_oficiales
		//         Access: public (desde la clase sigesp_sno_rpp_recibopago_ipsfa)  
		//	    Arguments: 
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: funci�n que busca la informaci�n de la primera y segunda quincena de la nomina de una persona
		//	   Creado Por: Ing. Jennifer Rivero
		// Fecha Creaci�n: 21/05/2008 								Fecha �ltima Modificaci�n :  
		// SE MODIFICARON LAS TABLAS TH POR H
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_codnom=$_SESSION["la_nomina"]["codnom"];
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];			
		$ls_peractnom=$_SESSION["la_nomina"]["peractnom"]; 	
		
				$ls_sql=" SELECT priquires, segquires         ".
				        " FROM sno_hresumen                    ".
						" WHERE sno_hresumen.codemp='".$ls_codemp."'         ". 
						" AND sno_hresumen.codper='".$as_codper."'  ".
						" AND sno_hresumen.codperi='".$ls_peractnom."'       ".
						" AND sno_hresumen.codnom='".$ls_codnom."'       ";  
       
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_seleccionar_quincenas ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$as_priqui=$row["priquires"];
				$as_segqui=$row["segquires"];		
			}
			else
			{
				$lb_valido=false;
				$as_priqui="";
				$as_priqui="";	
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_seleccionar_quincenas
//--------------------------------------------------------------------------------------------------------------------------------
     function uf_obtener_valor_concepto($as_codper,$as_concepto,&$as_valor)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_obtener_valor_concepto
		//         Access: public (desde la clase sigesp_sno_rpp_recibopago_ipsfa)  
		//	    Arguments: 
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: funci�n que busca la informaci�n de la primera y segunda quincena de la nomina de una persona
		//	   Creado Por: Ing. Jennifer Rivero
		// Fecha Creaci�n: 21/05/2008 								Fecha �ltima Modificaci�n :  
		// SE MODIFICARON LAS TABLAS TH POR H
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_codnom=$_SESSION["la_nomina"]["codnom"];
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];			
		$ls_peractnom=$_SESSION["la_nomina"]["peractnom"]; 	
		
				$ls_sql=" SELECT sno_hconcepto.codconc, sno_hconcepto.titcon as nomcon, sno_hsalida.valsal  ".
						"	FROM sno_hsalida, sno_hconcepto ".
						"		WHERE sno_hsalida.codemp='".$ls_codemp."' ". 
						"		AND sno_hsalida.codnom='".$ls_codnom."'  ". 
						"		AND sno_hsalida.codperi='".$ls_peractnom."' ". 
						"		AND sno_hconcepto.codconc='".$as_concepto."' ".
						"		AND sno_hsalida.codper='".$as_codper."' ". 
						"		AND sno_hsalida.valsal<>0 ".
						"		AND sno_hsalida.codemp = sno_hconcepto.codemp ".
						"		AND sno_hsalida.codnom = sno_hconcepto.codnom ".
						"		AND sno_hsalida.codconc = sno_hconcepto.codconc ".
						"		ORDER BY sno_hconcepto.codconc   ";  
       
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_obtener_valor_concepto ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$as_valor=$row["valsal"];
						
			}
			else
			{
				$lb_valido=false;
				$as_valor="";				
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_obtener_valor_concepto
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
    function uf_recibo_nomina_oficiales($as_codper)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_recibo_nomina_oficiales
		//         Access: public (desde la clase sigesp_sno_rpp_prenomina)  
		//	    Arguments: 
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: funci�n que busca la informaci�n del personal oficial
		//	   Creado Por: Ing. Jennifer Rivero
		// Fecha Creaci�n: 14/05/2008 								Fecha �ltima Modificaci�n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_codnom=$_SESSION["la_nomina"]["codnom"];
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];			
			
			    $ls_sql=" SELECT sno_personalpension.codemp, sno_personalpension.codnom, sno_personalpension.codper, ".
						"	     sno_personalpension.suebasper, sno_personalpension.pritraper, sno_personalpension.pridesper, ". 
						"	     sno_personalpension.prianoserper, sno_personalpension.prinoascper, ".
						"	     sno_personalpension.priespper, sno_personalpension.priproper, sno_personalpension.subtotper, ".
						"	     sno_personalpension.porpenper, sno_personalpension.monpenper, ".
						"	   (select sno_personal.nomper from sno_personal where codper=sno_personalpension.codper) as nomper,".
						"	   (select sno_personal.apeper from sno_personal where ".
						" sno_personal.codper=sno_personalpension.codper)  as apeper, ".
						"	   (select sno_personal.cedper from sno_personal  ".
						"      where sno_personal.codper=sno_personalpension.codper) as cedper, ".
						"	   (select sno_personal.fecingper from sno_personal ".
						"	   where sno_personal.codper=sno_personalpension.codper) as fecingper, ".
						"	   (select sno_personalnomina.fecingper from sno_personalnomina ".
						"       where sno_personalnomina.codper=sno_personalpension.codper ".
						"       and sno_personalnomina.codnom='".$ls_codnom."') as fecingnom, ".
						"	    sno_componente.descom, sno_rango.desran ".
						"  FROM sno_personalpension ".
						"  JOIN sno_personal ON (sno_personal.codemp=sno_personalpension.codemp ".
						"				   AND  sno_personal.codper=sno_personalpension.codper) ".
						"  LEFT JOIN sno_componente ON (sno_componente.codemp= sno_personal.codemp ".
						"						   AND sno_componente.codcom= sno_personal.codcom) ".
						"  LEFT JOIN sno_rango ON (sno_rango.codemp=sno_personal.codemp ".
						"					 AND  sno_rango.codcom=sno_personal.codcom  ".
						"					 AND  sno_rango.codran=sno_personal.codran) ".
						" WHERE sno_personalpension.codemp='".$ls_codemp."'".
						" AND	sno_personalpension.codper='".$as_codper."'".
						" AND	sno_personalpension.codnom='".$ls_codnom."'";       
		$this->rs_data_detalle=$this->io_sql->select($ls_sql);
		if($this->rs_data_detalle===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_recibo_nomina_oficiales ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_recibo_nomina_oficiales

	//--------------------------------------------------------------------------------------------------------------------------------	
	function uf_recibo_nomina_oficiales_2($as_codper)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_recibo_nomina_oficiales_2
		//         Access: public (desde la clase sigesp_sno_rpp_prenomina)  
		//	    Arguments: 
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: funci�n que busca la informaci�n del personal oficial
		//	   Creado Por: Ing. Jennifer Rivero
		// Fecha Creaci�n: 14/05/2008 								Fecha �ltima Modificaci�n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_codnom=$_SESSION["la_nomina"]["codnom"];
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];			
			
			    $ls_sql=" SELECT sno_personalpension.codemp, sno_personalpension.codnom, sno_personalpension.codper, ".
						"	     sno_personalpension.suebasper, sno_personalpension.pritraper, sno_personalpension.pridesper, ". 
						"	     sno_personalpension.prianoserper, sno_personalpension.prinoascper, ".
						"	     sno_personalpension.priespper, sno_personalpension.priproper, sno_personalpension.subtotper, ".
						"	     sno_personalpension.porpenper, sno_personalpension.monpenper, ".
						"	   (select sno_personal.nomper from sno_personal where codper=sno_personalpension.codper) as nomper,".
						"	   (select sno_personal.apeper from sno_personal where ".
						" sno_personal.codper=sno_personalpension.codper)  as apeper, ".
						"	   (select sno_personal.cedper from sno_personal  ".
						"      where sno_personal.codper=sno_personalpension.codper) as cedper, ".
						"	   (select sno_personal.fecingper from sno_personal ".
						"	   where sno_personal.codper=sno_personalpension.codper) as fecingper, ".
						"	   (select sno_personalnomina.fecingper from sno_personalnomina ".
						"       where sno_personalnomina.codper=sno_personalpension.codper ".
						"       and sno_personalnomina.codnom='".$ls_codnom."') as fecingnom, ".
						"	    sno_componente.descom, sno_rango.desran, ".
						"      (SELECT sno_categoria_rango.descat FROM sno_categoria_rango    ".
						"        WHERE sno_categoria_rango.codemp=sno_rango.codemp            ".
						"          AND sno_categoria_rango.codcat=sno_rango.codcat) as descat ".
						"  FROM sno_personalpension ".
						"  JOIN sno_personal ON (sno_personal.codemp=sno_personalpension.codemp ".
						"				   AND  sno_personal.codper=sno_personalpension.codper) ".
						"  LEFT JOIN sno_componente ON (sno_componente.codemp= sno_personal.codemp ".
						"						   AND sno_componente.codcom= sno_personal.codcom) ".
						"  LEFT JOIN sno_rango ON (sno_rango.codemp=sno_personal.codemp ".
						"					 AND  sno_rango.codcom=sno_personal.codcom  ".
						"					 AND  sno_rango.codran=sno_personal.codran) ".
						" WHERE sno_personalpension.codemp='".$ls_codemp."'".
						" AND	sno_personalpension.codper='".$as_codper."'".
						" AND	sno_personalpension.codnom='".$ls_codnom."'";       
		$this->rs_data_detalle=$this->io_sql->select($ls_sql);
		if($this->rs_data_detalle===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_recibo_nomina_oficiales_2 ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_recibo_nomina_oficiales

	 //-----------------------------------------------------------------------------------------------------------------------------------
    function uf_buscar_beneficiarios($as_codbendes, $as_codbenhas, $as_codperdes, $as_codperhas)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_buscar_beneficiarios
		//         Access: public (desde la clase sigesp_sno_rpp_recibopago_beneficiario)  
		//	    Arguments: 
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: funci�n que busca la informaci�n de los beneficiarios
		//	   Creado Por: Ing. Jennifer Rivero
		// Fecha Creaci�n: 26/06/2008								Fecha �ltima Modificaci�n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		if (($as_codperdes!="")&&($as_codperhas!=""))		
		{
			$ls_criterio="   AND codper BETWEEN '".$as_codperdes."' AND '".$as_codperhas."'";
		}
		if (($as_codbendes!="")&&($as_codbenhas!=""))		
		{
			$ls_criterio=$ls_criterio. "   AND codben BETWEEN '".$as_codbendes."' AND '".$as_codbenhas."'";  
		}
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];			
		$ls_sql=" SELECT sno_beneficiario.codper, sno_beneficiario.codben,  sno_beneficiario.cedben,         ".
                "        sno_beneficiario.nomben, sno_beneficiario.apeben,  sno_beneficiario.porpagben,      ".
                "        sno_beneficiario.codban, sno_beneficiario.ctaban,  sno_beneficiario.tipcueben,      ".
				"        sno_beneficiario.nexben, sno_beneficiario.nomcheben, sno_beneficiario.cedaut,       ".
				"        (SELECT sno_personal.fecnacper FROM sno_personal ".
				"          WHERE sno_personal.codemp='".$ls_codemp."'".
				"            AND sno_personal.cedper=sno_beneficiario.cedben) as fecnacben,        ".
				"        (SELECT scb_banco.nomban FROM scb_banco WHERE scb_banco.codemp='".$ls_codemp."'     ".
				"            AND scb_banco.codban=sno_beneficiario.codban) AS banco                          ".
                " FROM sno_beneficiario                                                                      ".
                " WHERE sno_beneficiario.codemp='".$ls_codemp."'".$ls_criterio.
				" ORDER BY sno_beneficiario.codper, sno_beneficiario.codben";           
       
		$this->rs_data_detalle2=$this->io_sql->select($ls_sql);
		if($this->rs_data_detalle2===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_buscar_beneficiarios ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_buscar_beneficiarios
	//----------------------------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------------------------------
    function uf_cuadre_concepto_pensiones($as_codconcdes,$as_codconchas,$as_conceptocero,$as_subnomdes,$as_subnomhas,$fecha,$criteriodefecha)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_cuadre_concepto_pensiones
		//         Access: public (desde la clase sigesp_sno_r_cuadrenomina_pensiones)  
		//	    Arguments: as_codconcdes // C�digo del concepto donde se empieza a filtrar
		//				   as_codconchas // C�digo del concepto donde se termina de filtrar
		//	  			   as_conceptocero // criterio que me indica si se desea quitar los conceptos que tienen monto cero
		//	      Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
		//    Description: funci�n que busca la informaci�n de los conceptos que se calcul� la n�mina
		//	   Creado Por: Ing. Jennifer Rivero
		// Fecha Creaci�n: 18/07/2008 								Fecha �ltima Modificaci�n :  
		// SE MODIFICARON LAS TABLAS TH POR H
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";		
		$ls_criteriopersonalnomina="";		
		$ls_criterio= $ls_criterio."	     ON sno_hsalida.codemp='".$this->ls_codemp."'  ".
								   "		AND sno_hsalida.codnom='".$this->ls_codnom."'  ".
								   "        AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
								   "		AND sno_hsalida.codperi='".$this->ls_peractnom."'  ";
		if(!empty($as_codconcdes))
		{
			$ls_criterio= $ls_criterio."   AND sno_hsalida.codconc>='".$as_codconcdes."'";			
		}
		if(!empty($as_codconchas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hsalida.codconc<='".$as_codconchas."'";			
		}
		if(!empty($as_conceptocero))
		{
			$ls_criterio = $ls_criterio."   AND sno_hsalida.valsal<>0 ";			
		}
		if(!empty($as_aportepatronal))
		{
			$ls_criterio = $ls_criterio." AND (sno_hsalida.tipsal='A' OR sno_hsalida.tipsal='V1' OR sno_hsalida.tipsal='W1' OR ".
										"      sno_hsalida.tipsal='D' OR sno_hsalida.tipsal='V2' OR sno_hsalida.tipsal='W2' OR ".		
										"	   sno_hsalida.tipsal='P2' OR sno_hsalida.tipsal='V4' OR sno_hsalida.tipsal='W4')";
		}
		else
		{
			$ls_criterio = $ls_criterio." AND (sno_hsalida.tipsal='A' OR sno_hsalida.tipsal='V1' OR sno_hsalida.tipsal='W1')";
		}		
		if(!empty($as_subnomdes))
		{
			$ls_criteriopersonalnomina= $ls_criteriopersonalnomina."   AND sno_hpersonalnomina.codsubnom>='".$as_subnomdes."'";			
		}
		if(!empty($as_subnomhas))
		{
			$ls_criteriopersonalnomina= $ls_criteriopersonalnomina."   AND sno_hpersonalnomina.codsubnom<='".$as_subnomhas."'";			
		}
		$ls_sql="SELECT  sno_hconcepto.codconc, MAX(sno_hconcepto.nomcon) AS nomcon, sno_hsalida.tipsal, sum(sno_hsalida.valsal) as monto, COUNT(sno_hsalida.codper) AS total						    ".	
				"  FROM sno_hsalida ".
				" INNER JOIN sno_hconcepto ".
				"  ".$ls_criterio.
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp                     ".
				"	AND sno_hsalida.codnom = sno_hconcepto.codnom                     ".
				"	AND sno_hsalida.anocur = sno_hconcepto.anocur                     ".
				"	AND sno_hsalida.codnom = sno_hconcepto.codnom                     ".
				"	AND sno_hsalida.codperi = sno_hconcepto.codperi                   ".
				" INNER JOIN (sno_hpersonalnomina ".
				"           INNER JOIN sno_personal  ". 
				"		       ON  ".$criteriodefecha.
				"             AND sno_personal.codemp = sno_hpersonalnomina.codemp   ".
				"			  AND sno_personal.codper = sno_hpersonalnomina.codper)  ".
				"	".$ls_criterio.
				"        AND sno_hsalida.codemp = sno_hpersonalnomina.codemp               ".
				"		 AND sno_hsalida.codnom = sno_hpersonalnomina.codnom               ".
				"	     AND sno_hsalida.anocur = sno_hpersonalnomina.anocur                     ".
				"	     AND sno_hsalida.codnom = sno_hpersonalnomina.codnom                     ".
				"		 AND sno_hsalida.codper = sno_hpersonalnomina.codper               ".
				$ls_criteriopersonalnomina.
				" GROUP BY sno_hconcepto.codconc, sno_hsalida.tipsal  ".
				" ORDER BY sno_hconcepto.codconc, sno_hsalida.tipsal                "; 
		$this->rs_data=$this->io_sql->select($ls_sql);
		if($this->rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M�TODO->uf_cuadre_concepto_pensiones ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	}// end uf_cuadrenomina_concepto_pensiones	
	//---------------------------------------------------------------------------------------------------------------------
	
	//---------------------------------------------------------------------------------------------------------------------
	function uf_buscar_codigos_unico_rac($as_codasicar,&$rs_data)
    {  
	    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //       Function: uf_buscar_codigos_unico_rac
        //        Arguments: 
        //          Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
        //    Description: funci�n que busca la informaci�n de las c�digos unicos asociados a una asignaci�n de cargo
        //       Creado Por: Ing. Mar�a Beatriz Unda
        // Fecha Creaci�n: 03/11/2008                                 Fecha �ltima Modificaci�n :          
	// SE MODIFICARON LAS TABLAS TH POR H
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $lb_valido=true;            
        $ls_codemp=$_SESSION["la_empresa"]["codemp"];            
        $ls_sql="SELECT codunirac, estcodunirac    ".                
                "  FROM sno_hcodigounicorac ".                
                " WHERE sno_hcodigounicorac.codemp='".$ls_codemp."'  ".
				"   AND sno_hcodigounicorac.codnom='".$this->ls_codnom."' ".
				"   AND sno_hcodigounicorac.codperi='".$this->ls_peractnom."'  ". 
				"   AND sno_hcodigounicorac.codasicar='".$as_codasicar."' ".
                " ORDER BY codunirac";  
        $rs_data=$this->io_sql->select($ls_sql);
        if($rs_data===false)
        {
            $this->io_mensajes->message("CLASE->Report M�TODO->uf_buscar_codigos_unico_rac ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
            $lb_valido=false;
        }
        return $lb_valido;
    }// end function uf_buscar_codigos_unico_rac
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
      function uf_buscar_cuotas ($as_codcon,$as_codper,&$as_cuota)
      {   
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //       Function: uf_buscar_cuotas
        //        Arguments: 
        //          Returns: lb_valido True si se creo el Data stored correctamente � False si no se creo
        //    Description: funci�n que busca la informaci�n de las c�digos unicos asociados a una asignaci�n de cargo
        //       Creado Por: Ing. Mar�a Beatriz Unda
        // Fecha Creaci�n: 08/12/2008                                 Fecha �ltima Modificaci�n :          
	// SE MODIFICARON LAS TABLAS TH POR H
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $lb_valido=true;
		$as_cuota="";            
        $ls_codemp=$_SESSION["la_empresa"]["codemp"];            
                    
        $ls_sql=" SELECT moncon, montopcon   ".                
                "  FROM sno_hconstantepersonal ".                
                "  WHERE sno_hconstantepersonal.codemp='".$ls_codemp."'  ".
				"	  AND sno_hconstantepersonal.codnom='".$this->ls_codnom."' ".
				"	  AND sno_hconstantepersonal.codperi='".$this->ls_peractnom."'  ". 
				"	  AND sno_hconstantepersonal.codcons='".$as_codcon."' ".
				"	  AND sno_hconstantepersonal.codper='".$as_codper."' ";  
        $rs_data=$this->io_sql->select($ls_sql);
        if($rs_data===false)
        {
            $this->io_mensajes->message("CLASE->Report M�TODO->uf_buscar_cuotas ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
            $lb_valido=false;
        }
		else
		{
			if(!$rs_data->EOF)
			{
				 $as_cuota=$rs_data->fields["moncon"]."/".$rs_data->fields["montopcon"];
				 
				 $rs_data->MoveNext();
			}
		}
        return $lb_valido;
    }// end function uf_buscar_cuotas
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_buscar_ubicacion_fisica($as_codorg)
   	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_buscar_ubicacion_fisica
		//		   Access: public
		//	  Description: Funci�n que obtiene ela ubicacion f�sica del personal seg�n el organigrama
		//	   Creado Por: Ing. Mar�a Beatriz Unda
		// Fecha Creaci�n: 09/01/2009 								Fecha �ltima Modificaci�n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_ubifis="";
		$lb_valido=true;
		
		$ls_sql="SELECT codorg, desorg, nivorg, padorg ".				
				"  FROM srh_organigrama ".
				" WHERE srh_organigrama.codemp='".$this->ls_codemp."' ".
				"   AND srh_organigrama.codorg='".$as_codorg."' ".
				"   AND srh_organigrama.codorg <> '----------' ";	
											
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("ERROR->".$io_funciones->uf_convertirmsg($io_sql->message)); 
		}
		else
		{
			$lb_hay=$rs_data->RecordCount();
			$li_i=1;
			while(!$rs_data->EOF)
			{
				$ls_codorg=$rs_data->fields["codorg"];
				$ls_desorg=$rs_data->fields["desorg"];
				$ls_nivorg=$rs_data->fields["nivorg"];					
				$ls_padorg=$rs_data->fields["padorg"];
				$la_data[$li_i]=array('cod'=>$ls_codorg,'des'=>$ls_desorg);				
				if ($ls_nivorg<>0)
				{
					for($i=$ls_nivorg;($i>0);$i--)
					{
						$ls_codorgsup=$ls_padorg;
						$this->uf_buscar_padre($ls_codorgsup,$ls_despadorg,$ls_nivpadorg,$ls_padorg);
						$li_i=$li_i+1;
						$la_data[$li_i]=array('cod'=>$ls_codorgsup,'des'=>$ls_despadorg);
					}
				}							
				for($j=$li_i;$j>0;$j--)
				{
					if ($j==$li_i)
					{
						$ls_ubifis=$la_data[$j]['des'];
					}
					else
					{						
						$ls_ubifis=$ls_ubifis.' - '.$la_data[$j]['des'];
					}
				}	
				$rs_data->MoveNext();
			}
		}
		return $ls_ubifis;
   }
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
 	function uf_buscar_padre($as_codorg,&$as_desorg,&$as_nivorg,&$as_padorg)
	{
  		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function:uf_buscar_padre
		//		   Access: public
		//	  Description: Funci�n que obtiene e imprime los conceptos a pagar por encargadur�a
		//	   Creado Por: Ing. Mar�a Beatriz Unda
		// Fecha Creaci�n: 05/01/2009 								Fecha �ltima Modificaci�n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$lb_valido=true;
		$ls_sql="SELECT codorg, desorg, nivorg, padorg ".				
				"  FROM srh_organigrama ".
				" WHERE srh_organigrama.codemp='".$ls_codemp."' ".
				"   AND srh_organigrama.codorg='".$as_codorg."' ".
				"   AND srh_organigrama.codorg <> '----------' ";	
		$rs_data2=$this->io_sql->select($ls_sql);
		if($rs_data2===false)
		{
			$this->io_mensajes->message("ERROR->".$io_funciones->uf_convertirmsg($io_sql->message)); 
		}
		else
		{
			while(!$rs_data2->EOF)
			{
				$ls_codorg=$rs_data2->fields["codorg"];
				$as_desorg=$rs_data2->fields["desorg"];
				$as_nivorg=$rs_data2->fields["nivorg"];					
				$as_padorg=$rs_data2->fields["padorg"];
				$rs_data2->MoveNext();
			}
		}
	}
	//-----------------------------------------------------------------------------------------------------------------------------------
}
?>
