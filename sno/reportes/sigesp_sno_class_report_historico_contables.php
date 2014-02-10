<?php
class sigesp_sno_class_report_historico_contables
{
	//-----------------------------------------------------------------------------------------------------------------------------------
	function sigesp_sno_class_report_historico_contables()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: sigesp_sno_class_report_historico_contables
		//		   Access: public 
		//	  Description: Constructor de la Clase
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 22/05/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		require_once("../../shared/class_folder/sigesp_include.php");
		$io_include=new sigesp_include();
		$this->io_conexion=$io_include->uf_conectar();
		require_once("../../shared/class_folder/class_sql.php");
		$this->io_sql=new class_sql($this->io_conexion);	
		$this->DS=new class_datastore();
		$this->DS_detalle=new class_datastore();
		$this->DS_detalle_2=new class_datastore();
		require_once("../../shared/class_folder/class_mensajes.php");
		$this->io_mensajes=new class_mensajes();		
		require_once("../../shared/class_folder/class_funciones.php");
		$this->io_funciones=new class_funciones();		
        $this->ls_codemp=$_SESSION["la_empresa"]["codemp"];
        $this->ls_codnom=$_SESSION["la_nomina"]["codnom"];
        $this->ls_peractnom=$_SESSION["la_nomina"]["peractnom"];
		$this->ls_anocurnom=$_SESSION["la_nomina"]["anocurnom"];
		$this->li_rac=$_SESSION["la_nomina"]["racnom"];
	}// end function sigesp_sno_class_report_historico_contables
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_config($as_sistema, $as_seccion, $as_variable, $as_valor, $as_tipo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_config
		//		   Access: public
		//	    Arguments: as_sistema  // Sistema al que pertenece la variable
		//				   as_seccion  // Sección a la que pertenece la variable
		//				   as_variable  // Variable nombre de la variable a buscar
		//				   as_valor  // valor por defecto que debe tener la variable
		//				   as_tipo  // tipo de la variable
		//	      Returns: $ls_resultado variable buscado
		//	  Description: Función que obtiene una variable de la tabla config
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
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
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_config ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
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
    function uf_select_provbene_ctacestatik($as_codprovben_cest,$as_benprov)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_provbene_ctacestatik
		//		   Access: public
		//	    Arguments: as_codprovben_cest  // Codigo del proveedor o beneficiario
		//				   as_benprov  // Estatus para consultar en tabla de proveedores o en tabla de beneficiarios
		//	      Returns: $ls_resultado variable buscado
		//	  Description: Función que obtiene una variable de la tabla rpc_proveedor o rpc_beneficiario
		//	   Creado Por: Ing. Carlos Zambrano
		// Fecha Creación: 27/04/2010 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_valor="";
		if($as_benprov=='P')
		{
			$ls_sql=" SELECT sc_cuenta ".
					" FROM rpc_proveedor ".
					" WHERE codemp='".$this->ls_codemp."' ".
					" AND cod_pro='".$as_codprovben_cest."' ";
		}
		else
		{
			$ls_sql=" SELECT sc_cuenta ".
					" FROM rpc_beneficiario ".
					" WHERE codemp='".$this->ls_codemp."' ".
					" AND ced_bene='".$as_codprovben_cest."' ";
		}
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_provbene_ctacestatik ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		}
		else
		{
			$li_i=0;
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_valor=$row["sc_cuenta"];
				$li_i=$li_i+1;
			}
			$this->io_sql->free_result($rs_data);		
		}
		return $ls_valor;
	}// end function uf_select_provbene_ctacestatik
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_provbene_denctacestatik($as_ctaprovben_cest)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_provbene_denctacestatik
		//		   Access: public
		//	    Arguments: as_ctaprovben_cest  // Codigo de la cuenta
		//	      Returns: $ls_resultado variable buscado
		//	  Description: Función que obtiene una variable de la tabla config
		//	   Creado Por: Ing. Carlos Zambrano
		// Fecha Creación: 27/04/2010 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_valor="";
		$ls_sql=" SELECT denominacion ".
				" FROM scg_cuentas ".
				" WHERE codemp='".$this->ls_codemp."' ".
				" AND sc_cuenta='".$as_ctaprovben_cest."' ";
		
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_provbene_denctacestatik ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		}
		else
		{
			$li_i=0;
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_valor=$row["denominacion"];
				$li_i=$li_i+1;
			}
			$this->io_sql->free_result($rs_data);		
		}
		return $ls_valor;
	}// end function uf_select_provbene_denctacestatik
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_config($as_sistema, $as_seccion, $as_variable, $as_valor, $as_tipo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_config
		//		   Access: public
		//	    Arguments: as_sistema  // Sistema al que pertenece la variable
		//				   as_seccion  // Sección a la que pertenece la variable
		//				   as_variable  // Variable a buscar
		//				   as_valor  // valor por defecto que debe tener la variable
		//				   as_tipo  // tipo de la variable
		//	      Returns: $lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Función que inserta la variable de configuración
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
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
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_insert_config ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
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
				$this->io_mensajes->message("CLASE->Report MÉTODO->uf_insert_config ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
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
	function uf_contableaportes_presupuesto()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_contableaportes_presupuesto
		//         Access: public (desde la clase sigesp_sno_r_contableaportes)  
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de las cuentas presupuestarias que afectan los conceptos de tipo P2
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 11/05/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos que se integran directamente con presupuesto
		$ls_sql="SELECT sno_hconcepto.codpro as programatica, sno_hconcepto.estcla, spg_cuentas.spg_cuenta AS cueprepatcon, ".
				"		MAX(spg_cuentas.denominacion) AS denominacion, sum(sno_hsalida.valsal) as total ".
				"  FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, spg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'P2' OR sno_hsalida.tipsal = 'V4' OR sno_hsalida.tipsal = 'W4')".
				"   AND sno_hconcepto.intprocon = '1'".
				"   AND spg_cuentas.status = 'C' ".
				"   AND sno_hsalida.valsal <> 0 ".
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
				"   AND sno_hconcepto.codemp = spg_cuentas.codemp ".
				"   AND sno_hconcepto.cueprepatcon = spg_cuentas.spg_cuenta ".
				"   AND substr(sno_hconcepto.codpro,1,25) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hconcepto.codpro,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hconcepto.codpro,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hconcepto.codpro,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hconcepto.codpro,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_hconcepto.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_hconcepto.codpro, sno_hconcepto.estcla, spg_cuentas.spg_cuenta ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos que no se integran directamente con presupuesto
		// entonces las buscamos según la estructura de la unidad administrativa a la que pertenece el personal
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_hunidadadmin.codprouniadm as programatica,sno_hunidadadmin.estcla, spg_cuentas.spg_cuenta AS cueprepatcon, ".
				"		MAX(spg_cuentas.denominacion) AS denominacion, sum(sno_hsalida.valsal) as total ".
				"  FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto, spg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'P2' OR sno_hsalida.tipsal = 'V4' OR sno_hsalida.tipsal = 'W4')".
				"   AND sno_hconcepto.intprocon = '0'".
				"   AND spg_cuentas.status = 'C' ".
				"   AND sno_hsalida.valsal <> 0 ".
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
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				"   AND sno_hconcepto.codemp = spg_cuentas.codemp ".
				"   AND sno_hconcepto.cueprepatcon = spg_cuentas.spg_cuenta ".
				"   AND substr(sno_hunidadadmin.codprouniadm,1,25) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_hunidadadmin.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_hunidadadmin.codprouniadm, sno_hunidadadmin.estcla, spg_cuentas.spg_cuenta ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_contableaportes_presupuesto ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_contableaportes_presupuesto
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_contableaportes_contable()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_contableaportes_contable
		//         Access: public (desde la clase sigesp_sno_r_contableaportes)  
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de las cuentas contables que afectan los conceptos de tipo P2
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 11/05/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$this->io_sql=new class_sql($this->io_conexion);	
		$li_parametros=trim($this->uf_select_config("SNO","CONFIG","CONTA GLOBAL","0","I"));
		switch($li_parametros)
		{
			case 0: // La contabilización es global
				$ls_modoaporte=$this->uf_select_config("SNO","NOMINA","CONTABILIZACION APORTES","OCP","C");
				$li_genrecapo=str_pad($this->uf_select_config("SNO","CONFIG","GENERAR RECEPCION DOCUMENTO APORTE","0","I"),1,"0");
				$ls_estctaalt=trim($this->uf_select_config("SNO","CONFIG","UTILIZAR_CTA.CONTA_REC_DOC_PROV_BEN","0","I"));
				break;
			
			case 1: // La contabilización es por nómina
				$ls_modoaporte=trim($_SESSION["la_nomina"]["conaponom"]);
				$li_genrecapo=str_pad(trim($_SESSION["la_nomina"]["recdocapo"]),1,"0");
				$ls_estctaalt=trim($_SESSION["la_nomina"]["estctaalt"]);
				break;
		}
		if ($ls_estctaalt=='1')
		{
			$ls_scctaprov='rpc_proveedor.sc_cuentarecdoc';
			$ls_scctaben='rpc_beneficiario.sc_cuentarecdoc';
		}
		else
		{
			$ls_scctaprov='rpc_proveedor.sc_cuenta';
			$ls_scctaben='rpc_beneficiario.sc_cuenta';
		}
		$ls_group=" GROUP BY spg_cuentas.sc_cuenta ";
		
			$ls_sql="  SELECT cuenta,  denoconta, operacion,  total ".
					"    FROM contableaporte_contable_historico     ".
					"	WHERE codemp='".$this->ls_codemp."'         ".
					"	  AND codnom='".$this->ls_codnom."'         ".
					"	  AND anocur='".$this->ls_anocurnom."'      ".
					"	  AND codperi='".$this->ls_peractnom."'                         ".
					"	UNION                                       ".
					"  SELECT cuenta,  denoconta, operacion,  total ".
					"	 FROM contableaporte_contable_historico_int ".
					"	WHERE codemp='".$this->ls_codemp."'         ".
					"	  AND codnom='".$this->ls_codnom."'         ".
					"	  AND anocur='".$this->ls_anocurnom."'      ".
					"	  AND codperi='".$this->ls_peractnom."'                         ";
					
		if(($ls_modoaporte=="OC")&&($li_genrecapo=="1"))
		{
			// Buscamos todas aquellas cuentas contables de los conceptos, estas van por el haber de contabilidad
			$ls_sql=$ls_sql." UNION ".
					"SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) as denoconta, 'H' as operacion, sum(sno_hsalida.valsal) as total ".
					"  FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, scg_cuentas, rpc_proveedor ".
					" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
					"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
					"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
					"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
					"   AND sno_hsalida.valsal <> 0 ".
					"   AND (sno_hsalida.tipsal = 'P2' OR sno_hsalida.tipsal = 'V4' OR sno_hsalida.tipsal = 'W4')".
					"   AND scg_cuentas.status = 'C' ".
					"   AND sno_hconcepto.codprov <> '----------' ".
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
					"	AND sno_hconcepto.codemp = rpc_proveedor.codemp ".
					"	AND sno_hconcepto.codprov = rpc_proveedor.cod_pro ".
					"   AND scg_cuentas.codemp = rpc_proveedor.codemp ".
					"   AND scg_cuentas.sc_cuenta = ".$ls_scctaprov." ".
					" GROUP BY scg_cuentas.sc_cuenta ";
			// Buscamos todas aquellas cuentas contables de los conceptos, estas van por el haber de contabilidad
			$ls_sql=$ls_sql." UNION ".
					"SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) as denoconta, 'H' as operacion, sum(sno_hsalida.valsal) as total ".
					"  FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, scg_cuentas, rpc_beneficiario ".
					" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
					"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
					"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
					"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
					"   AND sno_hsalida.valsal <> 0 ".
					"   AND (sno_hsalida.tipsal = 'P2' OR sno_hsalida.tipsal = 'V4' OR sno_hsalida.tipsal = 'W4')".
					"   AND scg_cuentas.status = 'C' ".
					"   AND sno_hconcepto.cedben <> '----------' ".
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
					"	AND sno_hconcepto.codemp = rpc_beneficiario.codemp ".
					"	AND sno_hconcepto.cedben = rpc_beneficiario.ced_bene ".
					"   AND scg_cuentas.codemp = rpc_beneficiario.codemp ".
					"   AND scg_cuentas.sc_cuenta = ".$ls_scctaben." ".
					" GROUP BY scg_cuentas.sc_cuenta ";
		}
		else
		{
			// Buscamos todas aquellas cuentas contables de los conceptos, estas van por el haber de contabilidad
			$ls_sql=$ls_sql." UNION ".
					"SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) as denoconta, 'H' as operacion, sum(sno_hsalida.valsal) as total ".
					"  FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, scg_cuentas ".
					" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
					"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
					"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
					"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
					"   AND scg_cuentas.status = 'C'".
					"   AND (sno_hsalida.tipsal = 'P2' OR sno_hsalida.tipsal = 'V4' OR sno_hsalida.tipsal = 'W4')".
					"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
					"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
					"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
					"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
					"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
					"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
					"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
					"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
					"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
					"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
					"   AND scg_cuentas.codemp = sno_hconcepto.codemp ".
					"   AND scg_cuentas.sc_cuenta = sno_hconcepto.cueconpatcon ".
					" GROUP BY scg_cuentas.sc_cuenta ";
		}
		$ls_sql=$ls_sql." ORDER BY operacion, cuenta"; 
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_contableaportes_contable ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS_detalle->data=$this->io_sql->obtener_datos($rs_data);
				$this->DS_detalle->group_by(array('0'=>'cuenta','1'=>'operacion'),array('0'=>'total'),array('0'=>'cuenta','1'=>'operacion'));
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_contableaportes_contable
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_contableconceptos_presupuesto()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_contableconceptos_presupuesto
		//         Access: public (desde la clase sigesp_sno_r_contableconceptos)  
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de las cuentas presupuestarias que afectan los conceptos de tipo A, D, P1
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 22/05/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A y D, que se integran directamente con presupuesto
		$ls_sql="SELECT sno_hconcepto.codpro as programatica, sno_hconcepto.estcla, sno_hconcepto.cueprecon, ".
				"		MAX(spg_cuentas.denominacion) AS denominacion, sum(sno_hsalida.valsal) as total ".
				"  FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, spg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1') ".
				"   AND sno_hsalida.valsal <> 0".
				"   AND sno_hconcepto.intprocon = '1'".
				"   AND spg_cuentas.status = 'C'".
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
				"   AND sno_hconcepto.codemp = spg_cuentas.codemp ".
				"   AND sno_hconcepto.cueprecon = spg_cuentas.spg_cuenta ".
				"   AND substr(sno_hconcepto.codpro,1,25) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hconcepto.codpro,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hconcepto.codpro,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hconcepto.codpro,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hconcepto.codpro,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_hconcepto.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_hconcepto.codpro, sno_hconcepto.estcla, sno_hconcepto.cueprecon ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A y D, que no se integran directamente con presupuesto
		// entonces las buscamos según la estructura de la unidad administrativa a la que pertenece el personal
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_hunidadadmin.codprouniadm as programatica, sno_hunidadadmin.estcla, sno_hconcepto.cueprecon, ".
				"		MAX(spg_cuentas.denominacion) AS denominacion, sum(sno_hsalida.valsal) as total ".
				"  FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto, spg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1') ".
				"   AND sno_hsalida.valsal <> 0".
				"   AND sno_hconcepto.intprocon = '0'".
				"   AND spg_cuentas.status = 'C'".
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
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprecon ".
				"   AND substr(sno_hunidadadmin.codprouniadm,1,25) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_hunidadadmin.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_hunidadadmin.codprouniadm,sno_hunidadadmin.estcla, sno_hconcepto.cueprecon ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A y D, que se integran directamente con presupuesto
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_hconcepto.codpro as programatica, sno_hconcepto.estcla, sno_hconcepto.cueprecon, ".
				"		MAX(spg_cuentas.denominacion) AS denominacion, sum(sno_hsalida.valsal) as total ".
				"  FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, spg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'D' OR sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2') ".
				"   AND sno_hconcepto.sigcon = 'E'".
				"   AND sno_hsalida.valsal <> 0".
				"   AND sno_hconcepto.intprocon = '1'".
				"   AND spg_cuentas.status = 'C'".
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
				"   AND sno_hconcepto.codemp = spg_cuentas.codemp ".
				"   AND sno_hconcepto.cueprecon = spg_cuentas.spg_cuenta ".
				"   AND substr(sno_hconcepto.codpro,1,25) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hconcepto.codpro,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hconcepto.codpro,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hconcepto.codpro,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hconcepto.codpro,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_hconcepto.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_hconcepto.codpro,sno_hconcepto.estcla, sno_hconcepto.cueprecon ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A y D, que no se integran directamente con presupuesto
		// entonces las buscamos según la estructura de la unidad administrativa a la que pertenece el personal
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_hunidadadmin.codprouniadm as programatica, sno_hunidadadmin.estcla, sno_hconcepto.cueprecon, ".
				"		MAX(spg_cuentas.denominacion) AS denominacion, sum(sno_hsalida.valsal) as total ".
				"  FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto, spg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'D' OR sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2') ".
				"   AND sno_hconcepto.sigcon = 'E'".
				"   AND sno_hsalida.valsal <> 0".
				"   AND sno_hconcepto.intprocon = '0'".
				"   AND spg_cuentas.status = 'C'".
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
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprecon ".
				"   AND sno_hunidadadmin.codemp = spg_cuentas.codemp ".
				"   AND substr(sno_hunidadadmin.codprouniadm,1,25) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_hunidadadmin.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_hunidadadmin.codprouniadm,sno_hunidadadmin.estcla, sno_hconcepto.cueprecon ".
				" ORDER BY programatica, cueprecon";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_contableconceptos_presupuesto ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_contableconceptos_presupuesto
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_contableconceptos_contable()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_contableconceptos_contable 
		//	    Arguments: 
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Función que se encarga de procesar la data para la contabilización de los conceptos
	    //     Creado por: Ing. Yesenia Moreno
	    // Fecha Creación: 08/11/2006
		///////////////////////////////////////////////////////////////////////////////////////////////////
	   	$lb_valido=true;
		$this->io_sql=new class_sql($this->io_conexion);
		$ls_group="  GROUP BY scg_cuentas.sc_cuenta ";
		$li_parametros=$this->uf_select_config("SNO","CONFIG","CONTA GLOBAL","0","I");
		$ls_codpronom=$_SESSION["la_nomina"]["codpronom"];
		$ls_codbennom=$_SESSION["la_nomina"]["codbennom"];
		$ls_espnom=$_SESSION["la_nomina"]["espnom"];
		$ls_ctnom=$_SESSION["la_nomina"]["ctnom"];
		switch($li_parametros)
		{
			case 0: // La contabilización es global
				$ls_cuentapasivo=trim($this->uf_select_config("SNO","CONFIG","CTA.CONTA","-------------------------","C"));
				$ls_modo=trim($this->uf_select_config("SNO","NOMINA","CONTABILIZACION","OCP","C"));
				$li_genrecdoc=str_pad($this->uf_select_config("SNO","CONFIG","GENERAR RECEPCION DOCUMENTO","0","I"),1,"0");
				$ls_estctaalt=trim($this->uf_select_config("SNO","CONFIG","UTILIZAR_CTA.CONTA_REC_DOC_PROV_BEN","0","I"));
				break;
				
			case 1: // La contabilización es por nómina
				$ls_cuentapasivo=trim($_SESSION["la_nomina"]["cueconnom"]);
				$ls_modo=trim($_SESSION["la_nomina"]["consulnom"]);
				$li_genrecdoc=str_pad(trim($_SESSION["la_nomina"]["recdocnom"]),1,"0");
				$ls_estctaalt=trim($_SESSION["la_nomina"]["estctaalt"]);
				break;
		}
		if ($ls_estctaalt=='1')
		{
			$ls_scctaprov='rpc_proveedor.sc_cuentarecdoc';
			$ls_scctaben='rpc_beneficiario.sc_cuentarecdoc';
		}
		else
		{
			$ls_scctaprov='rpc_proveedor.sc_cuenta';
			$ls_scctaben='rpc_beneficiario.sc_cuenta';
		}
		
		// Buscamos todas aquellas cuentas contables que estan ligadas a las presupuestarias de los conceptos A y D, que se 
		// integran directamente con presupuesto, estas van por el debe de contabilidad
		
				$ls_sql=" SELECT cuenta, denominacion,  operacion,  total ".
						"    FROM contableconceptos_contable_historico    ".
						"	WHERE codemp='".$this->ls_codemp."'           ".
						"     AND codnom='".$this->ls_codnom."'           ".
						"	  AND anocur='".$this->ls_anocurnom."'        ".
						"	  AND codperi='".$this->ls_peractnom."'       ".
						"	UNION                                         ".
						" SELECT cuenta, denominacion,  operacion,  total ".
						" 	FROM contableconceptos_contable_historico_int ".
						"  WHERE codemp='".$this->ls_codemp."'            ".
						"	 AND codnom='".$this->ls_codnom."'            ".
						"	 AND anocur='".$this->ls_anocurnom."'         ".   
						"	 AND codperi='".$this->ls_peractnom."'        ";
		if($ls_modo=="OC") // Si el modo de contabilizar la nómina es Compromete y Causa tomamos la cuenta pasivo de la nómina.
		{
			if($li_genrecdoc=="0") // No se genera Recepción de Documentos
			{
				
				/*if(($ls_espnom==1)&&($ls_ctnom==1))
				{
					$ls_sql=$ls_sql." UNION ".
						"SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) AS denominacion,  CAST('H' AS char(1)) as operacion, -sum(sno_thsalida.valsal) as total ".
						"  FROM sno_thpersonalnomina, sno_thsalida, scg_cuentas, sno_thnomina, rpc_proveedor ".
						" WHERE sno_thsalida.codemp = '".$this->ls_codemp."' ".
						"   AND sno_thsalida.codnom = '".$this->ls_codnom."' ".
						"   AND sno_thsalida.anocur='".$this->ls_anocurnom."' ".
						"   AND sno_thsalida.codperi = '".$this->ls_peractnom."' ".
						"   AND (sno_thsalida.tipsal = 'A' OR sno_thsalida.tipsal = 'V1' OR sno_thsalida.tipsal = 'W1' OR sno_thsalida.tipsal = 'D' ".
						"    OR  sno_thsalida.tipsal = 'V2' OR sno_thsalida.tipsal = 'W2' OR sno_thsalida.tipsal = 'P1' OR sno_thsalida.tipsal = 'V3' OR sno_thsalida.tipsal = 'W3' )".
						"   AND sno_thsalida.valsal <> 0 ".
						"   AND (sno_thpersonalnomina.pagbanper = 1 OR sno_thpersonalnomina.pagtaqper = 1) ".
						"   AND sno_thpersonalnomina.pagefeper = 0 ".
						"   AND scg_cuentas.status = 'C'".
						"   AND sno_thnomina.descomnom = 'P'".
						"   AND sno_thnomina.codemp = sno_thsalida.codemp ".
						"   AND sno_thnomina.codnom = sno_thsalida.codnom ".
						"   AND sno_thnomina.anocurnom = sno_thsalida.anocur ".
						"   AND sno_thnomina.peractnom = sno_thsalida.codperi ".
						"   AND sno_thpersonalnomina.codemp = sno_thsalida.codemp ".
						"   AND sno_thpersonalnomina.codnom = sno_thsalida.codnom ".
						"   AND sno_thpersonalnomina.anocur = sno_thsalida.anocur ".
						"   AND sno_thpersonalnomina.codperi = sno_thsalida.codperi ".
						"   AND sno_thpersonalnomina.codper = sno_thsalida.codper ".
						"   AND sno_thnomina.codemp = rpc_proveedor.codemp ".
						"   AND sno_thnomina.codpronom = rpc_proveedor.cod_pro ".
						"   AND rpc_proveedor.codemp = scg_cuentas.codemp ".
						"   AND ".$ls_scctaprov." = scg_cuentas.sc_cuenta ".
						" GROUP BY scg_cuentas.sc_cuenta ";
				}*/
				// Buscamos todas aquellas cuentas contables de los conceptos A y D, estas van por el haber de contabilidad
				switch($_SESSION["ls_gestor"])
				{
					case "MYSQLT":
						$ls_cadena="CONVERT('".$ls_cuentapasivo."' USING utf8) as cuenta";
						break;
					case "POSTGRES":
						$ls_cadena="CAST('".$ls_cuentapasivo."' AS char(25)) as cuenta";
						break;					
					case "INFORMIX":
						$ls_cadena="CAST('".$ls_cuentapasivo."' AS char(25)) as cuenta";
						break;					
				}
				$ls_sql=$ls_sql." UNION ".
						"SELECT ".$ls_cadena.", MAX(scg_cuentas.denominacion) AS denominacion,  CAST('H' AS char(1)) as operacion, -sum(sno_hsalida.valsal) as total ".
						"  FROM sno_hpersonalnomina, sno_hsalida, sno_banco, scg_cuentas ".
						" WHERE sno_hsalida.codemp = '".$this->ls_codemp."' ".
						"   AND sno_hsalida.codnom = '".$this->ls_codnom."' ".
						"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
						"   AND sno_hsalida.codperi = '".$this->ls_peractnom."' ".
						"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1' OR sno_hsalida.tipsal = 'D' ".
						"    OR  sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2' OR sno_hsalida.tipsal = 'P1' OR sno_hsalida.tipsal = 'V3' OR sno_hsalida.tipsal = 'W3' )".
						"   AND sno_hsalida.valsal <> 0 ".
						"   AND (sno_hpersonalnomina.pagbanper = 1 OR sno_hpersonalnomina.pagtaqper = 1) ".
						"   AND sno_hpersonalnomina.pagefeper = 0 ".
						"   AND scg_cuentas.status = 'C'".
						"   AND scg_cuentas.sc_cuenta = '".$ls_cuentapasivo."' ".
						"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
						"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
						"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
						"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
						"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
						"   AND sno_hsalida.codemp = sno_banco.codemp ".
						"   AND sno_hsalida.codnom = sno_banco.codnom ".
						"   AND sno_hsalida.codperi = sno_banco.codperi ".
						"   AND sno_hpersonalnomina.codemp = sno_banco.codemp ".
						"   AND sno_hpersonalnomina.codban = sno_banco.codban ".
						"   AND scg_cuentas.codemp = sno_banco.codemp ".
						" GROUP BY scg_cuentas.sc_cuenta ";
			}
			else // Se genera Recepción de documentos
			{
				$ls_sql=$ls_sql." UNION ".
						"SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) AS denominacion,  CAST('H' AS char(1)) as operacion, -sum(sno_hsalida.valsal) as total ".
						"  FROM sno_hpersonalnomina, sno_hsalida, scg_cuentas, sno_hnomina, rpc_proveedor ".
						" WHERE sno_hsalida.codemp = '".$this->ls_codemp."' ".
						"   AND sno_hsalida.codnom = '".$this->ls_codnom."' ".
						"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
						"   AND sno_hsalida.codperi = '".$this->ls_peractnom."' ".
						"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1' OR sno_hsalida.tipsal = 'D' ".
						"    OR  sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2' OR sno_hsalida.tipsal = 'P1' OR sno_hsalida.tipsal = 'V3' OR sno_hsalida.tipsal = 'W3' )".
						"   AND sno_hsalida.valsal <> 0 ".
						"   AND (sno_hpersonalnomina.pagbanper = 1 OR sno_hpersonalnomina.pagtaqper = 1) ".
						"   AND sno_hpersonalnomina.pagefeper = 0 ".
						"   AND scg_cuentas.status = 'C'".
						"   AND sno_hnomina.descomnom = 'P'".
						"   AND sno_hnomina.codemp = sno_hsalida.codemp ".
						"   AND sno_hnomina.codnom = sno_hsalida.codnom ".
						"   AND sno_hnomina.anocurnom = sno_hsalida.anocur ".
						"   AND sno_hnomina.peractnom = sno_hsalida.codperi ".
						"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
						"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
						"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
						"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
						"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
						"   AND sno_hnomina.codemp = rpc_proveedor.codemp ".
						"   AND sno_hnomina.codpronom = rpc_proveedor.cod_pro ".
						"   AND rpc_proveedor.codemp = scg_cuentas.codemp ".
						"   AND ".$ls_scctaprov." = scg_cuentas.sc_cuenta ".
						" GROUP BY scg_cuentas.sc_cuenta ";
				$ls_sql=$ls_sql." UNION ".
						"SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) AS denominacion,  CAST('H' AS char(1)) as operacion, -sum(sno_hsalida.valsal) as total ".
						"  FROM sno_hpersonalnomina, sno_hsalida, scg_cuentas, sno_hnomina, rpc_beneficiario ".
						" WHERE sno_hsalida.codemp = '".$this->ls_codemp."' ".
						"   AND sno_hsalida.codnom = '".$this->ls_codnom."' ".
						"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
						"   AND sno_hsalida.codperi = '".$this->ls_peractnom."' ".
						"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1' OR sno_hsalida.tipsal = 'D' ".
						"    OR  sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2' OR sno_hsalida.tipsal = 'P1' OR sno_hsalida.tipsal = 'V3' OR sno_hsalida.tipsal = 'W3' )".
						"   AND sno_hsalida.valsal <> 0 ".
						"   AND (sno_hpersonalnomina.pagbanper = 1 OR sno_hpersonalnomina.pagtaqper = 1) ".
						"   AND sno_hpersonalnomina.pagefeper = 0 ".
						"   AND scg_cuentas.status = 'C'".
						"   AND sno_hnomina.descomnom = 'B'".
						"   AND sno_hnomina.codemp = sno_hsalida.codemp ".
						"   AND sno_hnomina.codnom = sno_hsalida.codnom ".
						"   AND sno_hnomina.anocurnom = sno_hsalida.anocur ".
						"   AND sno_hnomina.peractnom = sno_hsalida.codperi ".
						"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
						"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
						"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
						"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
						"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
						"   AND sno_hnomina.codemp = rpc_beneficiario.codemp ".
						"   AND sno_hnomina.codbennom = rpc_beneficiario.ced_bene ".
						"   AND rpc_beneficiario.codemp = scg_cuentas.codemp ".
						"   AND ".$ls_scctaben." = scg_cuentas.sc_cuenta ".
						" GROUP BY scg_cuentas.sc_cuenta ";
			}
			if(($ls_espnom==1)&&($ls_ctnom==1))
			{
					if($ls_codpronom!='----------')
					{
						$ls_sql=$ls_sql." UNION ".
								" SELECT rpc_proveedor.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) as denominacion, CAST('H' AS char(1)) as operacion, -sum(sno_hsalida.valsal) as total ".
								" FROM sno_hpersonalnomina, sno_hsalida, rpc_proveedor, scg_cuentas ".
								" WHERE sno_hsalida.codemp = '".$this->ls_codemp."' ".
								" AND sno_hsalida.codnom = '".$this->ls_codnom."' ".
								" AND sno_hsalida.codperi = '".$this->ls_peractnom."' ".
								" AND sno_hsalida.anocur = '".$this->ls_anocurnom."' ".
								" AND rpc_proveedor.cod_pro = '".$ls_codpronom."' ".
								" AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1' OR sno_hsalida.tipsal = 'D' OR sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2' OR sno_hsalida.tipsal = 'P1' OR sno_hsalida.tipsal = 'V3' OR sno_hsalida.tipsal = 'W3') ".
								" AND sno_hsalida.valsal <> 0 ".
								" AND sno_hpersonalnomina.pagbanper = 0 ".
								" AND sno_hpersonalnomina.pagtaqper = 0 ".
								" AND sno_hpersonalnomina.pagefeper = 1 ".
								" AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
								" AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
								" AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
								" AND rpc_proveedor.codemp = sno_hpersonalnomina.codemp ".
								" AND scg_cuentas.codemp = rpc_proveedor.codemp ".
								" AND rpc_proveedor.sc_cuenta = scg_cuentas.sc_cuenta ".
								" GROUP BY scg_cuentas.sc_cuenta,rpc_proveedor.sc_cuenta ";
					}
					else
					{
						$ls_sql=$ls_sql." UNION ".
								" SELECT rpc_beneficiario.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) as denominacion, CAST('H' AS char(1)) as operacion, -sum(sno_hsalida.valsal) as total ".
								" FROM sno_hpersonalnomina, sno_hsalida, rpc_beneficiario, scg_cuentas ".
								" WHERE sno_hsalida.codemp = '".$this->ls_codemp."' ".
								" AND sno_hsalida.codnom = '".$this->ls_codnom."' ".
								" AND sno_hsalida.codperi = '".$this->ls_peractnom."' ".
								" AND sno_hsalida.anocur = '".$this->ls_anocurnom."' ".
								" AND rpc_beneficiario.ced_bene = '".$ls_codbennom."' ".
								" AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1' OR sno_hsalida.tipsal = 'D' OR sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2' OR sno_hsalida.tipsal = 'P1' OR sno_hsalida.tipsal = 'V3' OR sno_hsalida.tipsal = 'W3') ".
								" AND sno_hsalida.valsal <> 0 ".
								" AND sno_hpersonalnomina.pagbanper = 0 ".
								" AND sno_hpersonalnomina.pagtaqper = 0 ".
								" AND sno_hpersonalnomina.pagefeper = 1 ".
								" AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
								" AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
								" AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
								" AND rpc_beneficiario.codemp = sno_hpersonalnomina.codemp ".
								" AND scg_cuentas.codemp = rpc_beneficiario.codemp ".
								" AND rpc_beneficiario.sc_cuenta = scg_cuentas.sc_cuenta ".
								" GROUP BY scg_cuentas.sc_cuenta,rpc_beneficiario.sc_cuenta ";
					}
			}
			else
			{
				$ls_sql=$ls_sql." UNION ".
						"SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) AS denominacion,  CAST('H' AS char(1)) as operacion, -sum(sno_hsalida.valsal) as total ".
						"  FROM sno_hpersonalnomina, sno_hsalida, scg_cuentas ".
						" WHERE sno_hsalida.codemp = '".$this->ls_codemp."' ".
						"   AND sno_hsalida.codnom = '".$this->ls_codnom."' ".
						"   AND sno_hsalida.anocur = '".$this->ls_anocurnom."' ".
						"   AND sno_hsalida.codperi = '".$this->ls_peractnom."' ".
						"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1' OR sno_hsalida.tipsal = 'D' ".
						"    OR  sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2' OR sno_hsalida.tipsal = 'P1' OR sno_hsalida.tipsal = 'V3' OR sno_hsalida.tipsal = 'W3')".
						"   AND sno_hsalida.valsal <> 0".
						"   AND sno_hpersonalnomina.pagbanper = 0 ".
						"   AND sno_hpersonalnomina.pagtaqper = 0 ".
						"   AND sno_hpersonalnomina.pagefeper = 1 ".
						"   AND scg_cuentas.status = 'C'".
						"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
						"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
						"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
						"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
						"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
						"   AND scg_cuentas.codemp = sno_hpersonalnomina.codemp ".
						"   AND scg_cuentas.sc_cuenta = sno_hpersonalnomina.cueaboper ".
						" GROUP BY scg_cuentas.sc_cuenta ";
			}
		}
		else
		{
			// Buscamos todas aquellas cuentas contables de los conceptos A y D, estas van por el haber de contabilidad
			$ls_sql=$ls_sql." UNION ".
					"SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) AS denominacion,  CAST('H' AS char(1)) as operacion, -sum(sno_hsalida.valsal) as total ".
					"  FROM sno_hpersonalnomina, sno_hsalida, sno_banco, scg_cuentas ".
					" WHERE sno_hsalida.codemp = '".$this->ls_codemp."' ".
					"   AND sno_hsalida.codnom = '".$this->ls_codnom."' ".
					"   AND sno_hsalida.anocur = '".$this->ls_anocurnom."' ".
					"   AND sno_hsalida.codperi = '".$this->ls_peractnom."' ".
					"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1' OR sno_hsalida.tipsal = 'D' ".
					"    OR  sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2' OR sno_hsalida.tipsal = 'P1' OR sno_hsalida.tipsal = 'V3' OR sno_hsalida.tipsal = 'W3')".
					"   AND sno_hsalida.valsal <> 0".
					"   AND (sno_hpersonalnomina.pagbanper = 1  OR sno_hpersonalnomina.pagtaqper = 1) ".
					"   AND sno_hpersonalnomina.pagefeper = 0 ".
					"   AND scg_cuentas.status = 'C'".
					"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
					"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
					"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
					"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
					"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
					"   AND sno_hsalida.codemp = sno_banco.codemp ".
					"   AND sno_hsalida.codnom = sno_banco.codnom ".
					"   AND sno_hsalida.codperi = sno_banco.codperi ".
					"   AND sno_hpersonalnomina.codemp = sno_banco.codemp ".
					"   AND sno_hpersonalnomina.codban = sno_banco.codban ".
					"   AND scg_cuentas.codemp = sno_banco.codemp ".
					"   AND scg_cuentas.sc_cuenta = sno_banco.codcuecon ".
					" GROUP BY scg_cuentas.sc_cuenta ";
			if(($ls_espnom==1)&&($ls_ctnom==1))
			{
					if($ls_codpronom!='----------')
					{
						$ls_sql=$ls_sql." UNION ".
								" SELECT rpc_proveedor.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) as denominacion, CAST('H' AS char(1)) as operacion, -sum(sno_hsalida.valsal) as total ".
								" FROM sno_hpersonalnomina, sno_hsalida, rpc_proveedor, scg_cuentas ".
								" WHERE sno_hsalida.codemp = '".$this->ls_codemp."' ".
								" AND sno_hsalida.codnom = '".$this->ls_codnom."' ".
								" AND sno_hsalida.codperi = '".$this->ls_peractnom."' ".
								" AND sno_hsalida.anocur = '".$this->ls_anocurnom."' ".
								" AND rpc_proveedor.cod_pro = '".$ls_codpronom."' ".
								" AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1' OR sno_hsalida.tipsal = 'D' OR sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2' OR sno_hsalida.tipsal = 'P1' OR sno_hsalida.tipsal = 'V3' OR sno_hsalida.tipsal = 'W3') ".
								" AND sno_hsalida.valsal <> 0 ".
								" AND sno_hpersonalnomina.pagbanper = 0 ".
								" AND sno_hpersonalnomina.pagtaqper = 0 ".
								" AND sno_hpersonalnomina.pagefeper = 1 ".
								" AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
								" AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
								" AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
								" AND rpc_proveedor.codemp = sno_hpersonalnomina.codemp ".
								" AND scg_cuentas.codemp = rpc_proveedor.codemp ".
								" AND rpc_proveedor.sc_cuenta = scg_cuentas.sc_cuenta ".
								" GROUP BY scg_cuentas.sc_cuenta,rpc_proveedor.sc_cuenta ";
					}
					else
					{
						$ls_sql=$ls_sql." UNION ".
								" SELECT rpc_beneficiario.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) as denominacion, CAST('H' AS char(1)) as operacion, -sum(sno_hsalida.valsal) as total ".
								" FROM sno_hpersonalnomina, sno_hsalida, rpc_beneficiario, scg_cuentas ".
								" WHERE sno_hsalida.codemp = '".$this->ls_codemp."' ".
								" AND sno_hsalida.codnom = '".$this->ls_codnom."' ".
								" AND sno_hsalida.codperi = '".$this->ls_peractnom."' ".
								" AND sno_hsalida.anocur = '".$this->ls_anocurnom."' ".
								" AND rpc_beneficiario.ced_bene = '".$ls_codbennom."' ".
								" AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1' OR sno_hsalida.tipsal = 'D' OR sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2' OR sno_hsalida.tipsal = 'P1' OR sno_hsalida.tipsal = 'V3' OR sno_hsalida.tipsal = 'W3') ".
								" AND sno_hsalida.valsal <> 0 ".
								" AND sno_hpersonalnomina.pagbanper = 0 ".
								" AND sno_hpersonalnomina.pagtaqper = 0 ".
								" AND sno_hpersonalnomina.pagefeper = 1 ".
								" AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
								" AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
								" AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
								" AND rpc_beneficiario.codemp = sno_hpersonalnomina.codemp ".
								" AND scg_cuentas.codemp = rpc_beneficiario.codemp ".
								" AND rpc_beneficiario.sc_cuenta = scg_cuentas.sc_cuenta ".
								" GROUP BY scg_cuentas.sc_cuenta,rpc_beneficiario.sc_cuenta ";
					}
			}
			else
			{
				$ls_sql=$ls_sql." UNION ".
						"SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) AS denominacion, CAST('H' AS char(1)) as operacion, -sum(sno_hsalida.valsal) as total ".
						"  FROM sno_hpersonalnomina, sno_hsalida, scg_cuentas ".
						" WHERE sno_hsalida.codemp = '".$this->ls_codemp."' ".
						"   AND sno_hsalida.codnom = '".$this->ls_codnom."' ".
						"   AND sno_hsalida.anocur = '".$this->ls_anocurnom."' ".
						"   AND sno_hsalida.codperi = '".$this->ls_peractnom."' ".
						"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1' OR sno_hsalida.tipsal = 'D' ".
						"    OR  sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2' OR sno_hsalida.tipsal = 'P1' OR sno_hsalida.tipsal = 'V3' OR sno_hsalida.tipsal = 'W3')".
						"   AND sno_hsalida.valsal <> 0".
						"   AND sno_hpersonalnomina.pagbanper = 0 ".
						"   AND sno_hpersonalnomina.pagtaqper = 0 ".
						"   AND sno_hpersonalnomina.pagefeper = 1 ".
						"   AND scg_cuentas.status = 'C'".
						"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
						"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
						"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
						"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
						"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
						"   AND scg_cuentas.codemp = sno_hpersonalnomina.codemp ".
						"   AND scg_cuentas.sc_cuenta = sno_hpersonalnomina.cueaboper ".
						" GROUP BY scg_cuentas.sc_cuenta ";
			}
		}
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_contableconceptos_contable ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS_detalle->data=$this->io_sql->obtener_datos($rs_data);
				$this->DS_detalle->group_by(array('0'=>'cuenta','1'=>'operacion'),array('0'=>'total'),'total');
			}
			$this->io_sql->free_result($rs_data);
		}		
		return  $lb_valido;    
	}// end function uf_contableconceptos_contable
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_contableconceptos_presupuesto_enmohca()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_contableconceptos_presupuesto
		//         Access: public (desde la clase sigesp_sno_r_contableconceptos)  
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de las cuentas presupuestarias que afectan los conceptos de tipo A, D, P1
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 22/05/2006 								Fecha Última Modificación :  
		// SE MODIFICARON LAS TABLAS TH POR H
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A y D, que se integran directamente con presupuesto
		$ls_sql="SELECT sno_hconcepto.codpro as programatica, sno_hconcepto.cueprecon, spg_cuentas.denominacion, sum(sno_hsalida.valsal) as total ".
				"  FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, spg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1') ".
				"   AND sno_hsalida.valsal <> 0".
				"   AND sno_hconcepto.intprocon = '1'".
				"   AND spg_cuentas.status = 'C'".
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
				"   AND sno_hconcepto.codemp = spg_cuentas.codemp ".
				"   AND sno_hconcepto.cueprecon = spg_cuentas.spg_cuenta ".
				"   AND substr(sno_hconcepto.codpro,1,25) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hconcepto.codpro,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hconcepto.codpro,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hconcepto.codpro,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hconcepto.codpro,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_hconcepto.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_hconcepto.codpro, sno_hconcepto.estcla, sno_hconcepto.cueprecon, spg_cuentas.denominacion ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A y D, que no se integran directamente con presupuesto
		// entonces las buscamos según la estructura de la unidad administrativa a la que pertenece el personal
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_hunidadadmin.codprouniadm as programatica, sno_hconcepto.cueprecon, spg_cuentas.denominacion, sum(sno_hsalida.valsal) as total ".
				"  FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto, spg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1') ".
				"   AND sno_hsalida.valsal <> 0".
				"   AND sno_hconcepto.intprocon = '0'".
				"   AND spg_cuentas.status = 'C'".
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
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprecon ".
				"   AND sno_hunidadadmin.codemp = spg_cuentas.codemp ".
				"   AND substr(sno_hunidadadmin.codprouniadm,1,25) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_hunidadadmin.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_hunidadadmin.codprouniadm, sno_hunidadadmin.estcla, sno_hconcepto.cueprecon, spg_cuentas.denominacion ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A y D, que se integran directamente con presupuesto
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_hconcepto.codpro as programatica, sno_hconcepto.cueprecon, spg_cuentas.denominacion, sum(sno_hsalida.valsal) as total ".
				"  FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, spg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'D' OR sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2') ".
				"   AND sno_hconcepto.sigcon = 'E'".
				"   AND sno_hsalida.valsal <> 0".
				"   AND sno_hconcepto.intprocon = '1'".
				"   AND spg_cuentas.status = 'C'".
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
				"   AND sno_hconcepto.codemp = spg_cuentas.codemp ".
				"   AND sno_hconcepto.cueprecon = spg_cuentas.spg_cuenta ".
				"   AND substr(sno_hconcepto.codpro,1,25) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hconcepto.codpro,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hconcepto.codpro,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hconcepto.codpro,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hconcepto.codpro,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_hconcepto.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_hconcepto.codpro, no_thconcepto.estcla, sno_hconcepto.cueprecon, spg_cuentas.denominacion ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A y D, que no se integran directamente con presupuesto
		// entonces las buscamos según la estructura de la unidad administrativa a la que pertenece el personal
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_hunidadadmin.codprouniadm as programatica, sno_hconcepto.cueprecon, spg_cuentas.denominacion, sum(sno_hsalida.valsal) as total ".
				"  FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto, spg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'D' OR sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2') ".
				"   AND sno_hconcepto.sigcon = 'E'".
				"   AND sno_hsalida.valsal <> 0".
				"   AND sno_hconcepto.intprocon = '0'".
				"   AND spg_cuentas.status = 'C'".
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
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprecon ".
				"   AND substr(sno_hunidadadmin.codprouniadm,1,25) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_hunidadadmin.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_hunidadadmin.codprouniadm, sno_hunidadadmin.estcla, sno_hconcepto.cueprecon, spg_cuentas.denominacion ".
				" ORDER BY programatica, cueprecon";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_contableconceptos_presupuesto ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);
				$this->DS->group_by(array('0'=>'programatica','1'=>'cueprecon'),array('0'=>'total'),array('0'=>'programatica','1'=>'cueprecon'));
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_contableconceptos_presupuesto_enmohca
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_contableaportes_presupuesto_proyecto()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_contableaportes_presupuesto_proyecto
		//         Access: public (desde la clase sigesp_sno_r_contableaportes)  
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de las cuentas presupuestarias que afectan los conceptos de tipo P2
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 19/07/2007 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos que se integran directamente con presupuesto
		$ls_sql="SELECT sno_hconcepto.codpro as programatica, sno_hconcepto.estcla, spg_cuentas.spg_cuenta AS cueprepatcon, MAX(spg_cuentas.denominacion) AS denominacion, ".
				"		SUM(sno_hsalida.valsal) as total, ".
				"       SUBSTR(sno_hconcepto.codpro,1,25) AS proy1, ".
       			"		SUBSTR(sno_hconcepto.codpro,26,25) AS proy2, ".
				"		SUBSTR(sno_hconcepto.codpro,51,25) AS proy3, ".
				"		SUBSTR(sno_hconcepto.codpro,76,25) AS proy4, ".
				"		SUBSTR(sno_hconcepto.codpro,101,25) AS proy5 ".
				"  FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, spg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'P2' OR sno_hsalida.tipsal = 'V4' OR sno_hsalida.tipsal = 'W4')".
				"   AND sno_hconcepto.intprocon = '1'".
				"   AND sno_hconcepto.conprocon = '0' ".
				"   AND spg_cuentas.status = 'C' ".
				"   AND sno_hsalida.valsal <> 0 ".
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
				"   AND sno_hconcepto.codemp = spg_cuentas.codemp ".
				"   AND sno_hconcepto.cueprepatcon = spg_cuentas.spg_cuenta ".
				"   AND substr(sno_hconcepto.codpro,1,25) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hconcepto.codpro,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hconcepto.codpro,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hconcepto.codpro,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hconcepto.codpro,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_hconcepto.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_hconcepto.codpro, sno_hconcepto.estcla, spg_cuentas.spg_cuenta ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos que no se integran directamente con presupuesto
		// entonces las buscamos según la estructura de la unidad administrativa a la que pertenece el personal
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_hunidadadmin.codprouniadm as programatica, sno_hunidadadmin.estcla, spg_cuentas.spg_cuenta AS cueprepatcon, MAX(spg_cuentas.denominacion) AS denominacion, ".
				"		SUM(sno_hsalida.valsal) as total, ".
				"		SUBSTR(sno_hunidadadmin.codprouniadm,1,25) AS proy1, ".
       			"		SUBSTR(sno_hunidadadmin.codprouniadm,26,25) AS proy2, ".
       			"		SUBSTR(sno_hunidadadmin.codprouniadm,51,25) AS proy3, ".
       			"		SUBSTR(sno_hunidadadmin.codprouniadm,76,25) AS proy4, ".
       			"		SUBSTR(sno_hunidadadmin.codprouniadm,101,25) AS proy5 ".
				"  FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto, spg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'P2' OR sno_hsalida.tipsal = 'V4' OR sno_hsalida.tipsal = 'W4')".
				"   AND sno_hconcepto.intprocon = '0'".
				"   AND sno_hconcepto.conprocon = '0' ".
				"   AND spg_cuentas.status = 'C' ".
				"   AND sno_hsalida.valsal <> 0 ".
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
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprepatcon ".
				"   AND sno_hunidadadmin.codemp = spg_cuentas.codemp ".
				"   AND substr(sno_hunidadadmin.codprouniadm,1,25) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_hunidadadmin.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_hunidadadmin.codprouniadm, sno_hunidadadmin.estcla, spg_cuentas.spg_cuenta ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_contableaportes_presupuesto_proyecto ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);
			}
			$this->io_sql->free_result($rs_data);
		}		
		if($lb_valido)
		{
			$lb_valido=$this->uf_contableaportes_presupuesto_proyecto_dt();
			$this->DS->group_by(array('0'=>'proy1','1'=>'proy2','2'=>'proy3','3'=>'proy4','4'=>'proy5','5'=>'cueprepatcon'),array('0'=>'total'),'total');
			$this->DS->sortData('programatica');
		}
		return $lb_valido;
	}// end function uf_contableaportes_presupuesto_proyecto
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_contableaportes_presupuesto_proyecto_dt()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_contableaportes_presupuesto_proyecto_dt
		//         Access: public (desde la clase sigesp_sno_r_contableaportes)  
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de las cuentas presupuestarias que afectan los conceptos de tipo P2
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 19/07/2007 								Fecha Última Modificación : 
		// SE MODIFICARON LAS TABLAS TH POR H
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		switch($_SESSION["ls_gestor"])
		{
			case "MYSQLT":
				$ls_cadena=" ROUND((SUM(sno_hsalida.valsal)*MAX(sno_hproyectopersonal.pordiames)),3) ";
				break;
			case "POSTGRES":
				$ls_cadena=" ROUND(CAST((sum(sno_hsalida.valsal)*MAX(sno_hproyectopersonal.pordiames)) AS NUMERIC),3) ";
				break;					
			case "INFORMIX":
				$ls_cadena=" ROUND(CAST((sum(sno_hsalida.valsal)*MAX(sno_hproyectopersonal.pordiames)) AS FLOAT),3) ";
				break;					
		}
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos que se integran directamente con presupuesto
		$ls_sql="SELECT MAX(sno_hproyecto.estproproy) AS estproproy, sno_hproyecto.estcla, spg_cuentas.spg_cuenta, ".
				"		sum(sno_hsalida.valsal) AS total, MAX(sno_hconcepto.codprov) AS codprov, ".$ls_cadena." AS montoparcial, ".
				"		MAX(sno_hconcepto.cedben) AS cedben, sno_hconcepto.codconc, sno_hproyecto.codproy, sno_hproyectopersonal.codper, ".
				"		MAX(spg_cuentas.denominacion) AS denominacion, MAX(sno_hproyectopersonal.pordiames) as pordiames, ".
				"		MAX(SUBSTR(sno_hproyecto.estproproy,1,25)) AS proy1, ".
          		"		MAX(SUBSTR(sno_hproyecto.estproproy,26,25)) AS proy2, ".
          		"		MAX(SUBSTR(sno_hproyecto.estproproy,51,25)) AS proy3, ".
          		"		MAX(SUBSTR(sno_hproyecto.estproproy,76,25)) AS proy4, ".
          		"		MAX(SUBSTR(sno_hproyecto.estproproy,101,25)) AS proy5 ".
				"  FROM sno_hproyectopersonal, sno_hproyecto, sno_hsalida, sno_hconcepto, spg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'P2' OR sno_hsalida.tipsal = 'V4' OR sno_hsalida.tipsal = 'W4')".
				"   AND sno_hconcepto.conprocon = '1' ".
				"   AND spg_cuentas.status = 'C' ".
				"   AND sno_hsalida.valsal <> 0 ".
				"   AND sno_hproyectopersonal.codemp = sno_hsalida.codemp ".
				"   AND sno_hproyectopersonal.codnom = sno_hsalida.codnom ".
				"   AND sno_hproyectopersonal.anocur = sno_hsalida.anocur ".
				"   AND sno_hproyectopersonal.codperi = sno_hsalida.codperi ".
				"   AND sno_hproyectopersonal.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hproyectopersonal.codemp = sno_hproyecto.codemp ".
				"   AND sno_hproyectopersonal.codnom = sno_hproyecto.codnom ".
				"   AND sno_hproyectopersonal.anocur = sno_hproyecto.anocur ".
				"   AND sno_hproyectopersonal.codperi = sno_hproyecto.codperi ".
				"   AND sno_hproyectopersonal.codproy = sno_hproyecto.codproy ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprepatcon ".
				"   AND substr(sno_hproyecto.estproproy,1,25) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hproyecto.estproproy,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hproyecto.estproproy,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hproyecto.estproproy,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hproyecto.estproproy,101,25) = spg_cuentas.codestpro5 ".
				" GROUP BY sno_hproyectopersonal.codper, sno_hproyecto.codproy, spg_cuentas.spg_cuenta, sno_hconcepto.codconc ".
				" ORDER BY sno_hproyectopersonal.codper, sno_hproyecto.codproy, spg_cuentas.spg_cuenta, sno_hconcepto.codconc ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_contableaportes_presupuesto_proyecto_dt ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			$ls_codant="";
			$li_acumulado=0;
			$li_totalant=0;
			$ls_programaticaant="";
			$ls_estclaproyant="";
			$ls_cuentaant="";
			$ls_denominacionant="";
			$ls_conceptoant="";
			$ls_conta=0;
			$ls_conta2=0;
			while(!$rs_data->EOF)
			{
				$ls_codper=$rs_data->fields["codper"];
				$ls_codconc=$rs_data->fields["codconc"];
				$li_cedben=$rs_data->fields["cedben"];
				$ls_codproy=$rs_data->fields["codproy"];
				$li_montoparcial=round($rs_data->fields["montoparcial"],3);
				$li_total=round($rs_data->fields["total"],3);
				$ls_estproproy=$rs_data->fields["estproproy"];
				$ls_estclaproy=$rs_data->fields["estcla"];
				$ls_spgcuenta=$rs_data->fields["spg_cuenta"];
				$ls_denominacion=$rs_data->fields["denominacion"];
				$li_pordiames=$rs_data->fields["pordiames"];
				$ls_proy1=$rs_data->fields["proy1"];
				$ls_proy2=$rs_data->fields["proy2"];
				$ls_proy3=$rs_data->fields["proy3"];
				$ls_proy4=$rs_data->fields["proy4"];
				$ls_proy5=$rs_data->fields["proy5"];
				if(($ls_codper!=$ls_codant)||(($ls_spgcuenta!=$ls_cuentaant)&&($ls_codconc!=$ls_conceptoant)))
				{
					if($li_acumulado!=0)
					{
						
						if((round($li_acumulado,3)!=round($li_totalant,3))&&($li_pordiames<1))
						{							
							$ls_conta++;
							$li_montoparcial=round($rs_data->fields["montoparcial"],3);
							$this->DS->insertRow("programatica",$ls_estproproy);
							$this->DS->insertRow("estcla",$ls_estclaproy);
							$this->DS->insertRow("cueprepatcon",$ls_spgcuenta);
							$this->DS->insertRow("total",$li_montoparcial);
							$this->DS->insertRow("denominacion",$ls_denominacion);
							$this->DS->insertRow("proy1",$ls_proy1);
							$this->DS->insertRow("proy2",$ls_proy2);
							$this->DS->insertRow("proy3",$ls_proy3);
							$this->DS->insertRow("proy4",$ls_proy4);
							$this->DS->insertRow("proy5",$ls_proy5);
							$this->DS->insertRow("cedben",$li_cedben);
							$this->DS->insertRow("codper",$ls_codper);
							$this->DS->insertRow("codconc",$ls_codconc);
							$this->DS->insertRow("codproy",$ls_codproy);
						}
					}
					$li_montoparcial=round($rs_data->fields["montoparcial"],3);
					$ls_programaticaant=$ls_estproproy;
					$ls_estclaproyant=$ls_estclaproy;
					$ls_cuentaant=$ls_spgcuenta;
					$ls_codant=$ls_codper;
					$ls_denominacionant=$ls_denominacion;
					$li_pordiamesant=$li_pordiames;
					$ls_conceptoant=$ls_codconc;
					$li_totalant=$li_total;
				}
				if(($li_acumulado==0)||($li_pordiames==1))
				{
					$ls_conta2++;
					$this->DS->insertRow("programatica",$ls_estproproy);
					$this->DS->insertRow("estcla",$ls_estclaproy);
					$this->DS->insertRow("cueprepatcon",$ls_spgcuenta);
					$this->DS->insertRow("total",$li_montoparcial);
					$this->DS->insertRow("denominacion",$ls_denominacion);
					$this->DS->insertRow("proy1",$ls_proy1);
					$this->DS->insertRow("proy2",$ls_proy2);
					$this->DS->insertRow("proy3",$ls_proy3);
					$this->DS->insertRow("proy4",$ls_proy4);
					$this->DS->insertRow("proy5",$ls_proy5);
					$this->DS->insertRow("cedben",$li_cedben);
					$this->DS->insertRow("codper",$ls_codper);
					$this->DS->insertRow("codconc",$ls_codconc);
					$this->DS->insertRow("codproy",$ls_codproy);
				}
				$li_acumulado=round($rs_data->fields["montoparcial"],3);
				$rs_data->MoveNext();
			}
			if((number_format($li_acumulado,3,".","")!=number_format($li_totalant,3,".",""))&&($li_pordiames<1))
			{
				$li_montoparcial=round(($li_totalant-$li_acumulado),3);
				$this->DS->insertRow("programatica",$ls_programaticaant);
				$this->DS->insertRow("estcla",$ls_estclaproyant);
				$this->DS->insertRow("cueprepatcon",$ls_cuentaant);
				$this->DS->insertRow("total",$li_montoparcial);
				$this->DS->insertRow("denominacion",$ls_denominacionant);
				$this->DS->insertRow("proy1",$ls_proy1);
				$this->DS->insertRow("proy2",$ls_proy2);
				$this->DS->insertRow("proy3",$ls_proy3);
				$this->DS->insertRow("proy4",$ls_proy4);
				$this->DS->insertRow("proy5",$ls_proy5);
				$this->DS->insertRow("cedben",$li_cedben);
				$this->DS->insertRow("codper",$ls_codper);
				$this->DS->insertRow("codconc",$ls_codconc);
				$this->DS->insertRow("codproy",$ls_codproy);
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_contableaportes_presupuesto_proyecto_dt
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_contableaportes_contable_proyecto()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_contableaportes_contable_proyecto
		//         Access: public (desde la clase sigesp_sno_r_contableaportes)  
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de las cuentas contables que afectan los conceptos de tipo P2
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 19/07/2007 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$this->io_sql=new class_sql($this->io_conexion);	
		$li_parametros=trim($this->uf_select_config("SNO","CONFIG","CONTA GLOBAL","0","I"));
		switch($li_parametros)
		{
			case 0: // La contabilización es global
				$ls_modoaporte=$this->uf_select_config("SNO","NOMINA","CONTABILIZACION APORTES","OCP","C");
				$li_genrecapo=str_pad($this->uf_select_config("SNO","CONFIG","GENERAR RECEPCION DOCUMENTO APORTE","0","I"),1,"0");
				$ls_estctaalt=trim($this->uf_select_config("SNO","CONFIG","UTILIZAR_CTA.CONTA_REC_DOC_PROV_BEN","0","I"));
				break;
			
			case 1: // La contabilización es por nómina
				$ls_modoaporte=trim($_SESSION["la_nomina"]["conaponom"]);
				$li_genrecapo=str_pad(trim($_SESSION["la_nomina"]["recdocapo"]),1,"0");
				$ls_estctaalt=trim($_SESSION["la_nomina"]["estctaalt"]);
				break;
		}
		if ($ls_estctaalt=='1')
		{
			$ls_scctaprov='rpc_proveedor.sc_cuentarecdoc';
			$ls_scctaben='rpc_beneficiario.sc_cuentarecdoc';
		}
		else
		{
			$ls_scctaprov='rpc_proveedor.sc_cuenta';
			$ls_scctaben='rpc_beneficiario.sc_cuenta';
		}
				
		$ls_group=" GROUP BY spg_cuentas.sc_cuenta ";
		
			$ls_sql="SELECT spg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) as denoconta, 'D' as operacion, sum(sno_hsalida.valsal) as total ".
				"  FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, spg_cuentas, scg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'P2' OR sno_hsalida.tipsal = 'V4' OR sno_hsalida.tipsal = 'W4')".
				"   AND sno_hconcepto.intprocon = '1' ".
				"   AND sno_hconcepto.conprocon = '0' ".
				"   AND spg_cuentas.status = 'C'".
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
				"   AND sno_hconcepto.codemp = spg_cuentas.codemp ".
				"   AND sno_hconcepto.cueprepatcon = spg_cuentas.spg_cuenta ".
				"   AND substr(sno_hconcepto.codpro,1,25) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hconcepto.codpro,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hconcepto.codpro,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hconcepto.codpro,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hconcepto.codpro,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_hconcepto.estcla = spg_cuentas.estcla ".
				"   AND spg_cuentas.codemp = scg_cuentas.codemp ".
				"   AND spg_cuentas.sc_cuenta = scg_cuentas.sc_cuenta".
				$ls_group;
		// Buscamos todas aquellas cuentas contables que estan ligadas a las presupuestarias de los conceptos que NO se 
		// integran directamente con presupuesto entonces las buscamos según la estructura de la unidad administrativa a 
		// la que pertenece el personal, estas van por el debe de contabilidad
		$ls_sql=$ls_sql." UNION ".
				"SELECT spg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) as denoconta, 'D' as operacion, sum(sno_hsalida.valsal) as total ".
				"  FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto, spg_cuentas, scg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'P2' OR sno_hsalida.tipsal = 'V4' OR sno_hsalida.tipsal = 'W4')".
				"   AND sno_hconcepto.intprocon = '0'".
				"   AND sno_hconcepto.conprocon = '0' ".
				"   AND spg_cuentas.status = 'C'".
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
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprepatcon ".
				"   AND spg_cuentas.codemp = scg_cuentas.codemp ".
				"   AND spg_cuentas.sc_cuenta = scg_cuentas.sc_cuenta".
				"   AND sno_hunidadadmin.codemp = spg_cuentas.codemp ".
				"   AND substr(sno_hunidadadmin.codprouniadm,1,25) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_hunidadadmin.estcla = spg_cuentas.estcla ".
				$ls_group;
		if(($ls_modoaporte=="OC")&&($li_genrecapo=="1"))
		{
			// Buscamos todas aquellas cuentas contables de los conceptos, estas van por el haber de contabilidad
			$ls_sql=$ls_sql." UNION ".
					"SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) as denoconta, 'H' as operacion, sum(sno_hsalida.valsal) as total ".
					"  FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, scg_cuentas, rpc_proveedor ".
					" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
					"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
					"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
					"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
					"   AND sno_hsalida.valsal <> 0 ".
					"   AND (sno_hsalida.tipsal = 'P2' OR sno_hsalida.tipsal = 'V4' OR sno_hsalida.tipsal = 'W4')".
					"   AND scg_cuentas.status = 'C' ".
					"   AND sno_hconcepto.codprov <> '----------' ".
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
					"	AND sno_hconcepto.codemp = rpc_proveedor.codemp ".
					"	AND sno_hconcepto.codprov = rpc_proveedor.cod_pro ".
					"   AND scg_cuentas.codemp = rpc_proveedor.codemp ".
					"   AND scg_cuentas.sc_cuenta = rpc_proveedor.sc_cuenta ".
					" GROUP BY scg_cuentas.sc_cuenta ";
			// Buscamos todas aquellas cuentas contables de los conceptos, estas van por el haber de contabilidad
			$ls_sql=$ls_sql." UNION ".
					"SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) as denoconta, 'H' as operacion, sum(sno_hsalida.valsal) as total ".
					"  FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, scg_cuentas, rpc_beneficiario ".
					" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
					"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
					"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
					"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
					"   AND sno_hsalida.valsal <> 0 ".
					"   AND (sno_hsalida.tipsal = 'P2' OR sno_hsalida.tipsal = 'V4' OR sno_hsalida.tipsal = 'W4')".
					"   AND scg_cuentas.status = 'C' ".
					"   AND sno_hconcepto.cedben <> '----------' ".
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
					"	AND sno_hconcepto.codemp = rpc_beneficiario.codemp ".
					"	AND sno_hconcepto.cedben = rpc_beneficiario.ced_bene ".
					"   AND scg_cuentas.codemp = rpc_beneficiario.codemp ".
					"   AND scg_cuentas.sc_cuenta = rpc_beneficiario.sc_cuenta ".
					" GROUP BY scg_cuentas.sc_cuenta ";
		}
		else
		{
			// Buscamos todas aquellas cuentas contables de los conceptos, estas van por el haber de contabilidad
			$ls_sql=$ls_sql." UNION ".
					"SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) as denoconta, 'H' as operacion, sum(sno_hsalida.valsal) as total ".
					"  FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, scg_cuentas ".
					" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
					"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
					"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
					"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
					"   AND scg_cuentas.status = 'C'".
					"   AND (sno_hsalida.tipsal = 'P2' OR sno_hsalida.tipsal = 'V4' OR sno_hsalida.tipsal = 'W4')".
					"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
					"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
					"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
					"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
					"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
					"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
					"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
					"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
					"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
					"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
					"   AND scg_cuentas.codemp = sno_hconcepto.codemp ".
					"   AND scg_cuentas.sc_cuenta = sno_hconcepto.cueconpatcon ".
					" GROUP BY scg_cuentas.sc_cuenta ".
					" ORDER BY operacion, cuenta ";
		}
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_contableaportes_contable_proyecto ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS_detalle->data=$this->io_sql->obtener_datos($rs_data);
			}
			$this->io_sql->free_result($rs_data);
		}		
		if($lb_valido)
		{
			$lb_valido=$this->uf_contableaportes_contable_proyecto_dt();
			$this->DS_detalle->group_by(array('0'=>'cuenta','1'=>'operacion'),array('0'=>'total'),'total');		
			$this->DS_detalle->sortData('operacion');
		}
		return $lb_valido;
	}// end function uf_contableaportes_contable_proyecto
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_contableaportes_contable_proyecto_dt()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_contableaportes_contable_proyecto_dt
		//         Access: public (desde la clase sigesp_sno_r_contableaportes)  
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de las cuentas contables que afectan los conceptos de tipo P2
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 19/07/2007 								Fecha Última Modificación : 
		// SE MODIFICARON LAS TABLAS TH POR H
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$this->io_sql=new class_sql($this->io_conexion);	
		switch($_SESSION["ls_gestor"])
		{
			case "MYSQLT":
				$ls_cadena=" ROUND((SUM(abs(sno_hsalida.valsal))*MAX(sno_hproyectopersonal.pordiames)),3) ";
				break;
			case "POSTGRES":
				$ls_cadena=" ROUND(CAST((sum(abs(sno_hsalida.valsal))*MAX(sno_hproyectopersonal.pordiames)) AS NUMERIC),3) ";
				break;					
			case "INFORMIX":
				$ls_cadena=" ROUND(CAST((sum(abs(sno_hsalida.valsal))*MAX(sno_hproyectopersonal.pordiames)) AS FLOAT),3) ";
				break;					
		}
		// Buscamos todas aquellas cuentas contables que estan ligadas a las presupuestarias de los conceptos que se 
		// integran directamente con presupuesto estas van por el debe de contabilidad
		$ls_sql="SELECT scg_cuentas.sc_cuenta, CAST('D' AS char(1)) as operacion, sum(abs(sno_hsalida.valsal)) as total, ".
				"		".$ls_cadena." AS montoparcial, MAX(sno_hconcepto.codprov) as codprov, MAX(sno_hconcepto.cedben) as cedben, ".
				"		sno_hconcepto.codconc, sno_hproyectopersonal.codper, sno_hproyecto.codproy, MAX(scg_cuentas.denominacion) as denoconta, ".
				"		MAX(sno_hproyectopersonal.pordiames) as pordiames ".
				"  FROM sno_hproyectopersonal, sno_hproyecto, sno_hsalida, sno_hconcepto, spg_cuentas, scg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'P2' OR sno_hsalida.tipsal = 'V4' OR sno_hsalida.tipsal = 'W4')".
				"   AND sno_hconcepto.conprocon = '1' ".
				"   AND spg_cuentas.status = 'C' ".
				"   AND sno_hproyectopersonal.codemp = sno_hsalida.codemp ".
				"   AND sno_hproyectopersonal.codnom = sno_hsalida.codnom ".
				"   AND sno_hproyectopersonal.anocur = sno_hsalida.anocur ".
				"   AND sno_hproyectopersonal.codperi = sno_hsalida.codperi ".
				"   AND sno_hproyectopersonal.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hproyectopersonal.codemp = sno_hproyecto.codemp ".
				"   AND sno_hproyectopersonal.codnom = sno_hproyecto.codnom ".
				"   AND sno_hproyectopersonal.anocur = sno_hproyecto.anocur ".
				"   AND sno_hproyectopersonal.codperi = sno_hproyecto.codperi ".
				"   AND sno_hproyectopersonal.codproy = sno_hproyecto.codproy ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprepatcon ".
				"   AND spg_cuentas.sc_cuenta = scg_cuentas.sc_cuenta".
				"   AND substr(sno_hproyecto.estproproy,1,25) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hproyecto.estproproy,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hproyecto.estproproy,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hproyecto.estproproy,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hproyecto.estproproy,101,25) = spg_cuentas.codestpro5 ".
				" GROUP BY sno_hproyectopersonal.codper, sno_hconcepto.codconc, sno_hproyecto.codproy, scg_cuentas.sc_cuenta ".
				" ORDER BY codper, codconc, codproy, sc_cuenta ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_contableaportes_contable_proyecto_DT ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			$ls_codant="";
			$li_acumulado=0;
			$li_totalant=0;
			$ls_cuentaant="";
			$ls_operacionant="";
			$ls_denominacionant="";
			$ls_codconcant="";
			while(!$rs_data->EOF)
			{
				$ls_codper=$rs_data->fields["codper"];
				$ls_codconc=$rs_data->fields["codconc"];
				$li_montoparcial=$rs_data->fields["montoparcial"];
				$li_total=$rs_data->fields["total"];
				$ls_cuenta=$rs_data->fields["sc_cuenta"];
				$ls_operacion=$rs_data->fields["operacion"];
				$ls_denominacion=$rs_data->fields["denoconta"];
				$li_pordiames=$rs_data->fields["pordiames"];
				if(($ls_codper!=$ls_codant)||($ls_codconc!=$ls_codconcant))
				{
					if($li_acumulado!=0)
					{
						if((round($li_acumulado,3)!=round($li_totalant,3))&&($li_pordiamesant<1))
						{
							$li_montoparcial=round(($li_totalant-$li_acumulado),3);
							$this->DS_detalle->insertRow("operacion",$ls_operacionant);
							$this->DS_detalle->insertRow("cuenta",$ls_cuentaant);
							$this->DS_detalle->insertRow("total",$li_montoparcial);
							$this->DS_detalle->insertRow("denoconta",$ls_denominacionant);
						}
					}
					$li_acumulado=$rs_data->fields["montoparcial"];
					$li_montoparcial=round($rs_data->fields["montoparcial"],3);
					$ls_operacionant=$ls_operacion;
					$ls_cuentaant=$ls_cuenta;
					$ls_codconcant=$ls_codconc;
					$ls_codant=$ls_codper;
					$ls_denominacionant=$ls_denominacion;
					$li_pordiamesant=$li_pordiames;
					$li_totalant=$li_total;
				}
				else
				{
					$li_acumulado=$li_acumulado+$li_montoparcial;
					$ls_operacionant=$ls_operacion;
					$ls_cuentaant=$ls_cuenta;
					$ls_codconcant=$ls_codconc;
					$li_totalant=$li_total;
					$ls_denominacionant=$ls_denominacion;
				}
				$this->DS_detalle->insertRow("operacion",$ls_operacion);
				$this->DS_detalle->insertRow("cuenta",$ls_cuenta);
				$this->DS_detalle->insertRow("total",$li_montoparcial);
				$this->DS_detalle->insertRow("denoconta",$ls_denominacion);
				$rs_data->MoveNext();
			}
			if((number_format($li_acumulado,3,".","")!=number_format($li_totalant,3,".",""))&&($li_pordiamesant<1))
			{
				$li_montoparcial=round(($li_totalant-$li_acumulado),3);
				$this->DS_detalle->insertRow("operacion",$ls_operacionant);
				$this->DS_detalle->insertRow("cuenta",$ls_cuentaant);
				$this->DS_detalle->insertRow("total",$li_montoparcial);
				$this->DS_detalle->insertRow("denoconta",$ls_denominacionant);
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_contableaportes_contable_proyecto_dt
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_contableconceptos_presupuesto_proyecto()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_contableconceptos_presupuesto_proyecto
		//         Access: public (desde la clase sigesp_sno_r_contableconceptos)  
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de las cuentas presupuestarias que afectan los conceptos de tipo A, D, P1
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 19/07/2007 								Fecha Última Modificación :  
		// SE MODIFICARON LAS TABLAS TH POR H
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A y D, que se integran directamente con presupuesto
		$ls_sql="SELECT sno_hconcepto.codpro as programatica, sno_hconcepto.estcla, sno_hconcepto.cueprecon, ".
				"		MAX(spg_cuentas.denominacion) AS denominacion, sum(sno_hsalida.valsal) as total, ".
				" 		SUBSTR(sno_hconcepto.codpro,1,25) AS proy1, ".
       			"		SUBSTR(sno_hconcepto.codpro,26,25) AS proy2, ".
       			"		SUBSTR(sno_hconcepto.codpro,51,25) AS proy3, ".
       			"		SUBSTR(sno_hconcepto.codpro,76,25) AS proy4, ".
       			"		SUBSTR(sno_hconcepto.codpro,101,25) AS proy5 ".
				"  FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, spg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1') ".
				"   AND sno_hsalida.valsal <> 0".
				"   AND sno_hconcepto.intprocon = '1' ".
				"   AND sno_hconcepto.conprocon = '0' ".
				"   AND spg_cuentas.status = 'C'".
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
				"   AND sno_hconcepto.codemp = spg_cuentas.codemp ".
				"   AND sno_hconcepto.cueprecon = spg_cuentas.spg_cuenta ".
				"   AND substr(sno_hconcepto.codpro,1,25) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hconcepto.codpro,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hconcepto.codpro,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hconcepto.codpro,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hconcepto.codpro,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_hconcepto.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_hconcepto.codpro, sno_hconcepto.estcla, sno_hconcepto.cueprecon ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A y D, que no se integran directamente con presupuesto
		// entonces las buscamos según la estructura de la unidad administrativa a la que pertenece el personal
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_hunidadadmin.codprouniadm as programatica, sno_hunidadadmin.estcla, sno_hconcepto.cueprecon, ".
				"		MAX(spg_cuentas.denominacion) AS denominacion, sum(sno_hsalida.valsal) as total, ".
				" 		SUBSTR(sno_hconcepto.codpro,1,25) AS proy1, ".
       			"		SUBSTR(sno_hconcepto.codpro,26,25) AS proy2, ".
       			"		SUBSTR(sno_hconcepto.codpro,51,25) AS proy3, ".
       			"		SUBSTR(sno_hconcepto.codpro,76,25) AS proy4, ".
       			"		SUBSTR(sno_hconcepto.codpro,101,25) AS proy5 ".
				"  FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto, spg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1') ".
				"   AND sno_hsalida.valsal <> 0".
				"   AND sno_hconcepto.intprocon = '0'".
				"   AND sno_hconcepto.conprocon = '0' ".
				"   AND spg_cuentas.status = 'C'".
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
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprecon ".
				"   AND substr(sno_hunidadadmin.codprouniadm,1,25) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_hunidadadmin.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_hunidadadmin.codprouniadm, sno_hunidadadmin.estcla, sno_hconcepto.cueprecon ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A y D, que se integran directamente con presupuesto
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_hconcepto.codpro as programatica, sno_hconcepto.estcla, sno_hconcepto.cueprecon, ".
				"		MAX(spg_cuentas.denominacion) AS denominacion, sum(sno_hsalida.valsal) as total, ".
				" 		SUBSTR(sno_hconcepto.codpro,1,25) AS proy1, ".
       			"		SUBSTR(sno_hconcepto.codpro,26,25) AS proy2, ".
       			"		SUBSTR(sno_hconcepto.codpro,51,25) AS proy3, ".
       			"		SUBSTR(sno_hconcepto.codpro,76,25) AS proy4, ".
       			"		SUBSTR(sno_hconcepto.codpro,101,25) AS proy5 ".
				"  FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, spg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'D' OR sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2') ".
				"   AND sno_hconcepto.sigcon = 'E'".
				"   AND sno_hsalida.valsal <> 0".
				"   AND sno_hconcepto.intprocon = '1'".
				"   AND sno_hconcepto.conprocon = '0' ".
				"   AND spg_cuentas.status = 'C'".
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
				"   AND sno_hconcepto.codemp = spg_cuentas.codemp ".
				"   AND sno_hconcepto.cueprecon = spg_cuentas.spg_cuenta ".
				"   AND substr(sno_hconcepto.codpro,1,25) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hconcepto.codpro,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hconcepto.codpro,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hconcepto.codpro,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hconcepto.codpro,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_hconcepto.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_hconcepto.codpro, sno_hconcepto.estcla, sno_hconcepto.cueprecon ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A y D, que no se integran directamente con presupuesto
		// entonces las buscamos según la estructura de la unidad administrativa a la que pertenece el personal
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_hunidadadmin.codprouniadm as programatica, sno_hunidadadmin.estcla, sno_hconcepto.cueprecon, ".
				"		MAX(spg_cuentas.denominacion) AS denominacion, sum(sno_hsalida.valsal) as total, ".
				" 		SUBSTR(sno_hconcepto.codpro,1,25) AS proy1, ".
       			"		SUBSTR(sno_hconcepto.codpro,26,25) AS proy2, ".
       			"		SUBSTR(sno_hconcepto.codpro,51,25) AS proy3, ".
       			"		SUBSTR(sno_hconcepto.codpro,76,25) AS proy4, ".
       			"		SUBSTR(sno_hconcepto.codpro,101,25) AS proy5 ".
				"  FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto, spg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'D' OR sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2') ".
				"   AND sno_hconcepto.sigcon = 'E'".
				"   AND sno_hsalida.valsal <> 0".
				"   AND sno_hconcepto.intprocon = '0'".
				"   AND sno_hconcepto.conprocon = '0' ".
				"   AND spg_cuentas.status = 'C'".
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
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprecon ".
				"   AND sno_hunidadadmin.codemp = spg_cuentas.codemp ".
				"   AND substr(sno_hunidadadmin.codprouniadm,1,25) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_hunidadadmin.estcla = spg_cuentas.estcla ".
			    " GROUP BY sno_hunidadadmin.codprouniadm, sno_hunidadadmin.estcla, sno_hconcepto.cueprecon ".
				" ORDER BY programatica, cueprecon"; 
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_contableconceptos_presupuesto ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);
			}
			$this->io_sql->free_result($rs_data);
		}		
		if($lb_valido)
		{
			$lb_valido=$this->uf_contableconceptos_presupuesto_proyecto_dt();
			$this->DS->group_by(array('0'=>'proy1','1'=>'proy2','2'=>'proy3','3'=>'proy4','4'=>'proy5','5'=>'estcla','6'=>'cueprecon'),array('0'=>'total'),'total');		
			$ls_contar=$this->DS->getRowCount("programatica");
			if ($ls_contar>0)
			{
				$this->DS->sortData('programatica');
			}
		}
		return $lb_valido;
	}// end function uf_contableconceptos_presupuesto_proyecto
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_contableconceptos_presupuesto_proyecto_dt()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_contableconceptos_presupuesto_proyecto_dt
		//         Access: public (desde la clase sigesp_sno_r_contableconceptos)  
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de las cuentas presupuestarias que afectan los conceptos de tipo A, D, P1
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 19/07/2007 								Fecha Última Modificación :  
		// SE MODIFICARON LAS TABLAS TH POR H
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		switch($_SESSION["ls_gestor"])
		{
			case "MYSQLT":
				$ls_cadena=" ROUND((SUM(sno_hsalida.valsal)*MAX(sno_hproyectopersonal.pordiames)),3) ";
				break;
			case "POSTGRES":
				$ls_cadena=" ROUND(CAST((sum(sno_hsalida.valsal)*MAX(sno_hproyectopersonal.pordiames)) AS NUMERIC),3) ";
				break;					
			case "INFORMIX":
				$ls_cadena=" ROUND(CAST((sum(sno_hsalida.valsal)*MAX(sno_hproyectopersonal.pordiames)) AS FLOAT),3) ";
				break;					
		}
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A y D, que se integran directamente con presupuesto
		$ls_sql="SELECT sno_hproyectopersonal.codper, sno_hproyectopersonal.codproy, MAX(sno_hproyecto.estproproy) AS estproproy, ".
				"		MAX(sno_hproyecto.estcla) AS estcla , spg_cuentas.spg_cuenta, ".
				"		".$ls_cadena." as montoparcial, sum(sno_hsalida.valsal) AS total, MAX(spg_cuentas.denominacion) AS denominacion, ".
				"       MAX(sno_hproyectopersonal.pordiames) AS pordiames, ".
				" 		MAX(SUBSTR(sno_hproyecto.estproproy,1,25)) AS proy1, ".
       			"		MAX(SUBSTR(sno_hproyecto.estproproy,26,25)) AS proy2, ".
       			"		MAX(SUBSTR(sno_hproyecto.estproproy,51,25)) AS proy3, ".
       			"		MAX(SUBSTR(sno_hproyecto.estproproy,76,25)) AS proy4, ".
       			"		MAX(SUBSTR(sno_hproyecto.estproproy,101,25)) AS proy5 ".
				"  FROM sno_hproyectopersonal, sno_hproyecto, sno_hsalida, sno_hconcepto, spg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1') ".
				"   AND sno_hsalida.valsal <> 0".
				"   AND sno_hconcepto.conprocon = '1' ".
				"   AND spg_cuentas.status = 'C'".
				"   AND sno_hproyectopersonal.codemp = sno_hsalida.codemp ".
				"   AND sno_hproyectopersonal.codnom = sno_hsalida.codnom ".
				"   AND sno_hproyectopersonal.anocur = sno_hsalida.anocur ".
				"   AND sno_hproyectopersonal.codperi = sno_hsalida.codperi ".
				"   AND sno_hproyectopersonal.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hproyectopersonal.codemp = sno_hproyecto.codemp ".
				"   AND sno_hproyectopersonal.codnom = sno_hproyecto.codnom ".
				"   AND sno_hproyectopersonal.anocur = sno_hproyecto.anocur ".
				"   AND sno_hproyectopersonal.codperi = sno_hproyecto.codperi ".
				"   AND sno_hproyectopersonal.codproy = sno_hproyecto.codproy ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprecon ".
				"   AND substr(sno_hproyecto.estproproy,1,25) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hproyecto.estproproy,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hproyecto.estproproy,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hproyecto.estproproy,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hproyecto.estproproy,101,25) = spg_cuentas.codestpro5 ".
				" GROUP BY sno_hproyectopersonal.codper, sno_hproyectopersonal.codproy, spg_cuentas.spg_cuenta ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A y D, que se integran directamente con presupuesto
		$ls_sql=$ls_sql." UNION ".
		$ls_sql="SELECT sno_hproyectopersonal.codper, sno_hproyectopersonal.codproy, MAX(sno_hproyecto.estproproy) AS estproproy, ".
				"		MAX(sno_hproyecto.estcla) AS estcla , spg_cuentas.spg_cuenta, ".
				"		".$ls_cadena." as montoparcial, sum(sno_hsalida.valsal) AS total, MAX(spg_cuentas.denominacion) AS denominacion, ".
				"       MAX(sno_hproyectopersonal.pordiames) AS pordiames, ".
				" 		MAX(SUBSTR(sno_hproyecto.estproproy,1,25)) AS proy1, ".
       			"		MAX(SUBSTR(sno_hproyecto.estproproy,26,25)) AS proy2, ".
       			"		MAX(SUBSTR(sno_hproyecto.estproproy,51,25)) AS proy3, ".
       			"		MAX(SUBSTR(sno_hproyecto.estproproy,76,25)) AS proy4, ".
       			"		MAX(SUBSTR(sno_hproyecto.estproproy,101,25)) AS proy5 ".
				"  FROM sno_hproyectopersonal, sno_hproyecto, sno_hsalida, sno_hconcepto, spg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'D' OR sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2') ".
				"   AND sno_hconcepto.sigcon = 'E'".
				"   AND sno_hsalida.valsal <> 0".
				"   AND sno_hconcepto.conprocon = '1' ".
				"   AND spg_cuentas.status = 'C' ".
				"   AND sno_hproyectopersonal.codemp = sno_hsalida.codemp ".
				"   AND sno_hproyectopersonal.codnom = sno_hsalida.codnom ".
				"   AND sno_hproyectopersonal.anocur = sno_hsalida.anocur ".
				"   AND sno_hproyectopersonal.codperi = sno_hsalida.codperi ".
				"   AND sno_hproyectopersonal.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hproyectopersonal.codemp = sno_hproyecto.codemp ".
				"   AND sno_hproyectopersonal.codnom = sno_hproyecto.codnom ".
				"   AND sno_hproyectopersonal.anocur = sno_hproyecto.anocur ".
				"   AND sno_hproyectopersonal.codperi = sno_hproyecto.codperi ".
				"   AND sno_hproyectopersonal.codproy = sno_hproyecto.codproy ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprecon ".
				"   AND substr(sno_hproyecto.estproproy,1,25) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hproyecto.estproproy,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hproyecto.estproproy,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hproyecto.estproproy,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hproyecto.estproproy,101,25) = spg_cuentas.codestpro5 ".
				" GROUP BY sno_hproyectopersonal.codper, sno_hproyectopersonal.codproy, spg_cuentas.spg_cuenta ".
				" ORDER BY codper, spg_cuenta, codproy "; 
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_contableconceptos_presupuesto_proyecto_dt ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			$ls_codant="";
			$li_acumulado=0;
			$li_totalant=0;
			$ls_programaticaant="";
			$ls_estclaproyant="";
			$ls_cuentaant="";
			$ls_denominacionant="";
			$ls_codproyant="";
			while(!$rs_data->EOF)
			{
				$ls_codper=$rs_data->fields["codper"];
				$li_montoparcial=round($rs_data->fields["montoparcial"],3);
				$li_total=round($rs_data->fields["total"],3);
				$ls_estproproy=$rs_data->fields["estproproy"];
				$ls_estclaproy=$rs_data->fields["estcla"];
				$ls_spgcuenta=$rs_data->fields["spg_cuenta"];
				$ls_denominacion=$rs_data->fields["denominacion"];
				$li_pordiames=$rs_data->fields["pordiames"];
				$ls_codproy=$rs_data->fields["codproy"];
				$ls_proy1=$rs_data->fields["proy1"];
				$ls_proy2=$rs_data->fields["proy2"];
				$ls_proy3=$rs_data->fields["proy3"];
				$ls_proy4=$rs_data->fields["proy4"];
				$ls_proy5=$rs_data->fields["proy5"];
				if(($ls_codper!=$ls_codant)||($ls_spgcuenta!=$ls_cuentaant))
				{
					if($li_acumulado!=0)
					{
						if((round($li_acumulado,3)!=round($li_totalant,3))&&($li_pordiamesant<1))
						{
							$li_montoparcial=round(($li_totalant-$li_acumulado),3);
							$this->DS->insertRow("programatica",$ls_programaticaant);
							$this->DS->insertRow("estcla",$ls_estclaproyant);
							$this->DS->insertRow("cueprecon",$ls_cuentaant);
							$this->DS->insertRow("total",$li_montoparcial);
							$this->DS->insertRow("denominacion",$ls_denominacionant);
							$this->DS->insertRow("proy1",$ls_proy1);
							$this->DS->insertRow("proy2",$ls_proy2);
							$this->DS->insertRow("proy3",$ls_proy3);
							$this->DS->insertRow("proy4",$ls_proy4);
							$this->DS->insertRow("proy5",$ls_proy5);
						}
					}
					$li_montoparcial=round($rs_data->fields["montoparcial"],3);
					$li_acumulado=$li_montoparcial;
					$ls_programaticaant=$ls_estproproy;
					$ls_estclaproyant=$ls_estclaproy;
					$ls_cuentaant=$ls_spgcuenta;
					$li_pordiamesant=$li_pordiames;
					$ls_codant=$ls_codper;
					$ls_codproyant=$ls_codproy;
					$ls_denominacionant=$ls_denominacion;
					$li_totalant=$li_total;
				}
				else
				{
					$li_acumulado=$li_acumulado+$li_montoparcial;
					$ls_programaticaant=$ls_estproproy;
					$ls_estclaproyant=$ls_estclaproy;
					$ls_cuentaant=$ls_spgcuenta;
					$li_totalant=$li_total;
					$ls_denominacionant=$ls_denominacion;
				}
				$this->DS->insertRow("programatica",$ls_estproproy);
				$this->DS->insertRow("estcla",$ls_estclaproy);
				$this->DS->insertRow("cueprecon",$ls_spgcuenta);
				$this->DS->insertRow("total",$li_montoparcial);
				$this->DS->insertRow("denominacion",$ls_denominacion);
				$this->DS->insertRow("proy1",$ls_proy1);
				$this->DS->insertRow("proy2",$ls_proy2);
				$this->DS->insertRow("proy3",$ls_proy3);
				$this->DS->insertRow("proy4",$ls_proy4);
				$this->DS->insertRow("proy5",$ls_proy5);
				$rs_data->MoveNext();
			}
			if((number_format($li_acumulado,3,".","")!=number_format($li_totalant,3,".",""))&&($li_pordiamesant<1))
			{
				$li_montoparcial=round(($li_totalant-$li_acumulado),3);
				$this->DS->insertRow("programatica",$ls_programaticaant);
				$this->DS->insertRow("estcla",$ls_estclaproyant);
				$this->DS->insertRow("cueprecon",$ls_cuentaant);
				$this->DS->insertRow("total",$li_montoparcial);
				$this->DS->insertRow("denominacion",$ls_denominacionant);
				$this->DS->insertRow("proy1",$ls_proy1);
				$this->DS->insertRow("proy2",$ls_proy2);
				$this->DS->insertRow("proy3",$ls_proy3);
				$this->DS->insertRow("proy4",$ls_proy4);
				$this->DS->insertRow("proy5",$ls_proy5);
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_contableconceptos_presupuesto_proyecto_dt
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_contableconceptos_contable_proyecto()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_contableconceptos_contable_proyecto 
		//	    Arguments: 
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Función que se encarga de procesar la data para la contabilización de los conceptos
	    //     Creado por: Ing. Yesenia Moreno
	    // Fecha Creación: 19/07/2007
		///////////////////////////////////////////////////////////////////////////////////////////////////
	   	$lb_valido=true;
		$this->io_sql=new class_sql($this->io_conexion);
		$ls_group="  GROUP BY scg_cuentas.sc_cuenta ";
		$li_parametros=$this->uf_select_config("SNO","CONFIG","CONTA GLOBAL","0","I");
		switch($li_parametros)
		{
			case 0: // La contabilización es global
				$ls_cuentapasivo=trim($this->uf_select_config("SNO","CONFIG","CTA.CONTA","-------------------------","C"));
				$ls_modo=trim($this->uf_select_config("SNO","NOMINA","CONTABILIZACION","OCP","C"));
				$li_genrecdoc=str_pad($this->uf_select_config("SNO","CONFIG","GENERAR RECEPCION DOCUMENTO","0","I"),1,"0");
				$ls_estctaalt=trim($this->uf_select_config("SNO","CONFIG","UTILIZAR_CTA.CONTA_REC_DOC_PROV_BEN","0","I"));
				break;
				
			case 1: // La contabilización es por nómina
				$ls_cuentapasivo=trim($_SESSION["la_nomina"]["cueconnom"]);
				$ls_modo=trim($_SESSION["la_nomina"]["consulnom"]);
				$li_genrecdoc=str_pad(trim($_SESSION["la_nomina"]["recdocnom"]),1,"0");
				$ls_estctaalt=trim($_SESSION["la_nomina"]["estctaalt"]);
				break;
		}
		if ($ls_estctaalt=='1')
		{
			$ls_scctaprov='rpc_proveedor.sc_cuentarecdoc';
			$ls_scctaben='rpc_beneficiario.sc_cuentarecdoc';
		}
		else
		{
			$ls_scctaprov='rpc_proveedor.sc_cuenta';
			$ls_scctaben='rpc_beneficiario.sc_cuenta';
		}
		
// Buscamos todas aquellas cuentas contables que estan ligadas a las presupuestarias de los conceptos A y D, que se 
		// integran directamente con presupuesto, estas van por el debe de contabilidad
		$ls_sql="SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) AS denominacion, CAST('D' AS char(1)) as operacion, sum(sno_hsalida.valsal) as total ".
				"  FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, spg_cuentas, scg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1') ".
				"   AND sno_hsalida.valsal <> 0 ".
				"   AND sno_hconcepto.intprocon = '1' ".
				"   AND sno_hconcepto.conprocon = '0' ".
				"   AND spg_cuentas.status = 'C'".
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
				"   AND sno_hconcepto.codemp = spg_cuentas.codemp ".
				"   AND sno_hconcepto.cueprecon = spg_cuentas.spg_cuenta ".
				"   AND substr(sno_hconcepto.codpro,1,25) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hconcepto.codpro,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hconcepto.codpro,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hconcepto.codpro,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hconcepto.codpro,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_hconcepto.estcla = spg_cuentas.estcla ".
				"   AND spg_cuentas.codemp = scg_cuentas.codemp".
				"   AND spg_cuentas.sc_cuenta = scg_cuentas.sc_cuenta".
				$ls_group;
		// Buscamos todas aquellas cuentas contables que estan ligadas a las presupuestarias de los conceptos A y D que NO se 
		// integran directamente con presupuesto entonces las buscamos según la estructura de la unidad administrativa a 
		// la que pertenece el personal, estas van por el debe de contabilidad
		$ls_sql=$ls_sql." UNION ".
				"SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) AS denominacion, CAST('D' AS char(1)) as operacion, sum(sno_hsalida.valsal) as total ".
				"  FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto, spg_cuentas, scg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1') ".
				"   AND sno_hsalida.valsal <> 0".
				"   AND sno_hconcepto.intprocon = '0'".
				"   AND sno_hconcepto.conprocon = '0' ".
				"   AND spg_cuentas.status = 'C'".
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
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprecon ".
				"   AND spg_cuentas.codemp = scg_cuentas.codemp".
				"   AND spg_cuentas.sc_cuenta = scg_cuentas.sc_cuenta".
				"   AND sno_hunidadadmin.codemp = spg_cuentas.codemp ".
				"   AND substr(sno_hunidadadmin.codprouniadm,1,25) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_hunidadadmin.estcla = spg_cuentas.estcla ".
				$ls_group;
		// Buscamos todas aquellas cuentas contables que estan ligadas a las presupuestarias de los conceptos A y D, que se 
		// integran directamente con presupuesto, estas van por el debe de contabilidad
		// Buscamos todas aquellas cuentas contables que estan ligadas a las presupuestarias de los conceptos A y D, que se 
		// integran directamente con presupuesto, estas van por el debe de contabilidad
		$ls_sql=$ls_sql." UNION ".
				"SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) AS denominacion, CAST('H' AS char(1)) as operacion, sum(sno_hsalida.valsal) as total ".
				"  FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, spg_cuentas, scg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'D' OR sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2') ".
				"   AND sno_hsalida.valsal <> 0 ".
				"   AND sno_hconcepto.sigcon = 'E' ".
				"   AND sno_hconcepto.intprocon = '1' ".
				"   AND sno_hconcepto.conprocon = '0' ".
				"   AND spg_cuentas.status = 'C'".
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
				"   AND sno_hconcepto.codemp = spg_cuentas.codemp ".
				"   AND sno_hconcepto.cueprecon = spg_cuentas.spg_cuenta ".
				"   AND substr(sno_hconcepto.codpro,1,25) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hconcepto.codpro,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hconcepto.codpro,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hconcepto.codpro,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hconcepto.codpro,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_hconcepto.estcla = spg_cuentas.estcla ".
				"   AND spg_cuentas.codemp = scg_cuentas.codemp".
				"   AND spg_cuentas.sc_cuenta = scg_cuentas.sc_cuenta".
				$ls_group;
		// Buscamos todas aquellas cuentas contables que estan ligadas a las presupuestarias de los conceptos A y D que NO se 
		// integran directamente con presupuesto entonces las buscamos según la estructura de la unidad administrativa a 
		// la que pertenece el personal, estas van por el debe de contabilidad
		$ls_sql=$ls_sql." UNION ".
				"SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) AS denominacion, CAST('H' AS char(1)) as operacion, sum(sno_hsalida.valsal) as total ".
				"  FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto, spg_cuentas, scg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'D' OR sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2') ".
				"   AND sno_hsalida.valsal <> 0 ".
				"   AND sno_hconcepto.sigcon = 'E' ".
				"   AND sno_hconcepto.intprocon = '0'".
				"   AND sno_hconcepto.conprocon = '0' ".
				"   AND spg_cuentas.status = 'C'".
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
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprecon ".
				"   AND spg_cuentas.codemp = scg_cuentas.codemp".
				"   AND spg_cuentas.sc_cuenta = scg_cuentas.sc_cuenta".
				"   AND sno_hunidadadmin.codemp = spg_cuentas.codemp ".
				"   AND substr(sno_hunidadadmin.codprouniadm,1,25) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_hunidadadmin.estcla = spg_cuentas.estcla ".
				$ls_group;
		$ls_sql=$ls_sql." UNION ".
				"SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) AS denominacion, CAST('D' AS char(1)) as operacion, sum(sno_hsalida.valsal) as total ".
				"  FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, scg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1') ".
				"   AND sno_hsalida.valsal <> 0 ".
				"   AND sno_hconcepto.sigcon = 'B' ".
				"   AND scg_cuentas.status = 'C'".
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
				"   AND scg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND scg_cuentas.sc_cuenta = sno_hconcepto.cueconcon ".
				$ls_group;
		$ls_sql=$ls_sql." UNION ".
				"SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) AS denominacion, CAST('H' AS char(1)) as operacion, sum(sno_hsalida.valsal) as total ".
				"  FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, scg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'D' OR sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2' OR sno_hsalida.tipsal = 'P1' OR sno_hsalida.tipsal = 'V3' OR sno_hsalida.tipsal = 'W3' )".
				"   AND sno_hsalida.valsal <> 0 ".
				"   AND scg_cuentas.status = 'C'".
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
				"   AND scg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND scg_cuentas.sc_cuenta = sno_hconcepto.cueconcon ".
				$ls_group;
		if($ls_modo=="OC") // Si el modo de contabilizar la nómina es Compromete y Causa tomamos la cuenta pasivo de la nómina.
		{
			if($li_genrecdoc=="0") // No se genera Recepción de Documentos
			{
				// Buscamos todas aquellas cuentas contables de los conceptos A y D, estas van por el haber de contabilidad
				switch($_SESSION["ls_gestor"])
				{
					case "MYSQLT":
						$ls_cadena="CONVERT('".$ls_cuentapasivo."' USING utf8) as cuenta";
						break;
					case "POSTGRES":
						$ls_cadena="CAST('".$ls_cuentapasivo."' AS char(25)) as cuenta";
						break;					
					case "INFORMIX":
						$ls_cadena="CAST('".$ls_cuentapasivo."' AS char(25)) as cuenta";
						break;					
				}
				$ls_sql=$ls_sql." UNION ".
						"SELECT ".$ls_cadena.", MAX(scg_cuentas.denominacion) AS denominacion,  CAST('H' AS char(1)) as operacion, -sum(sno_hsalida.valsal) as total ".
						"  FROM sno_hpersonalnomina, sno_hsalida, sno_banco, scg_cuentas ".
						" WHERE sno_hsalida.codemp = '".$this->ls_codemp."' ".
						"   AND sno_hsalida.codnom = '".$this->ls_codnom."' ".
						"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
						"   AND sno_hsalida.codperi = '".$this->ls_peractnom."' ".
						"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1' OR sno_hsalida.tipsal = 'D' ".
						"    OR  sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2' OR sno_hsalida.tipsal = 'P1' OR sno_hsalida.tipsal = 'V3' OR sno_hsalida.tipsal = 'W3' )".
						"   AND sno_hsalida.valsal <> 0 ".
						"   AND (sno_hpersonalnomina.pagbanper = 1  OR sno_hpersonalnomina.pagtaqper = 1) ".
						"   AND sno_hpersonalnomina.pagefeper = 0 ".
						"   AND scg_cuentas.status = 'C'".
						"   AND scg_cuentas.sc_cuenta = '".$ls_cuentapasivo."' ".
						"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
						"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
						"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
						"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
						"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
						"   AND sno_hsalida.codemp = sno_banco.codemp ".
						"   AND sno_hsalida.codnom = sno_banco.codnom ".
						"   AND sno_hsalida.codperi = sno_banco.codperi ".
						"   AND sno_hpersonalnomina.codemp = sno_banco.codemp ".
						"   AND sno_hpersonalnomina.codban = sno_banco.codban ".
						"   AND scg_cuentas.codemp = sno_banco.codemp ".
						" GROUP BY scg_cuentas.sc_cuenta ";
			}
			else // Se genera Recepción de documentos
			{
				$ls_sql=$ls_sql." UNION ".
						"SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) AS denominacion,  CAST('H' AS char(1)) as operacion, -sum(sno_hsalida.valsal) as total ".
						"  FROM sno_hpersonalnomina, sno_hsalida, scg_cuentas, sno_hnomina, rpc_proveedor ".
						" WHERE sno_hsalida.codemp = '".$this->ls_codemp."' ".
						"   AND sno_hsalida.codnom = '".$this->ls_codnom."' ".
						"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
						"   AND sno_hsalida.codperi = '".$this->ls_peractnom."' ".
						"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1' OR sno_hsalida.tipsal = 'D' ".
						"    OR  sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2' OR sno_hsalida.tipsal = 'P1' OR sno_hsalida.tipsal = 'V3' OR sno_hsalida.tipsal = 'W3' )".
						"   AND sno_hsalida.valsal <> 0 ".
						"   AND (sno_hpersonalnomina.pagbanper = 1 OR sno_hpersonalnomina.pagtaqper = 1) ".
						"   AND sno_hpersonalnomina.pagefeper = 0 ".
						"   AND scg_cuentas.status = 'C'".
						"   AND sno_hnomina.descomnom = 'P'".
						"   AND sno_hnomina.codemp = sno_hsalida.codemp ".
						"   AND sno_hnomina.codnom = sno_hsalida.codnom ".
						"   AND sno_hnomina.anocurnom = sno_hsalida.anocur ".
						"   AND sno_hnomina.peractnom = sno_hsalida.codperi ".
						"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
						"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
						"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
						"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
						"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
						"   AND sno_hnomina.codemp = rpc_proveedor.codemp ".
						"   AND sno_hnomina.codpronom = rpc_proveedor.cod_pro ".
						"   AND rpc_proveedor.codemp = scg_cuentas.codemp ".
						"   AND rpc_proveedor.sc_cuenta = scg_cuentas.sc_cuenta ".
						" GROUP BY scg_cuentas.sc_cuenta ";
				$ls_sql=$ls_sql." UNION ".
						"SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) AS denominacion,  CAST('H' AS char(1)) as operacion, -sum(sno_hsalida.valsal) as total ".
						"  FROM sno_hpersonalnomina, sno_hsalida, scg_cuentas, sno_hnomina, rpc_beneficiario ".
						" WHERE sno_hsalida.codemp = '".$this->ls_codemp."' ".
						"   AND sno_hsalida.codnom = '".$this->ls_codnom."' ".
						"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
						"   AND sno_hsalida.codperi = '".$this->ls_peractnom."' ".
						"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1' OR sno_hsalida.tipsal = 'D' ".
						"    OR  sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2' OR sno_hsalida.tipsal = 'P1' OR sno_hsalida.tipsal = 'V3' OR sno_hsalida.tipsal = 'W3' )".
						"   AND sno_hsalida.valsal <> 0 ".
						"   AND (sno_hpersonalnomina.pagbanper = 1 OR sno_hpersonalnomina.pagtaqper = 1) ".
						"   AND sno_hpersonalnomina.pagefeper = 0 ".
						"   AND scg_cuentas.status = 'C'".
						"   AND sno_hnomina.descomnom = 'B'".
						"   AND sno_hnomina.codemp = sno_hsalida.codemp ".
						"   AND sno_hnomina.codnom = sno_hsalida.codnom ".
						"   AND sno_hnomina.anocurnom = sno_hsalida.anocur ".
						"   AND sno_hnomina.peractnom = sno_hsalida.codperi ".
						"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
						"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
						"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
						"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
						"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
						"   AND sno_hnomina.codemp = rpc_beneficiario.codemp ".
						"   AND sno_hnomina.codbennom = rpc_beneficiario.ced_bene ".
						"   AND rpc_beneficiario.codemp = scg_cuentas.codemp ".
						"   AND rpc_beneficiario.sc_cuenta = scg_cuentas.sc_cuenta ".
						" GROUP BY scg_cuentas.sc_cuenta ";
			}
			$ls_sql=$ls_sql." UNION ".
					"SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) AS denominacion,  CAST('H' AS char(1)) as operacion, -sum(sno_hsalida.valsal) as total ".
					"  FROM sno_hpersonalnomina, sno_hsalida, scg_cuentas ".
					" WHERE sno_hsalida.codemp = '".$this->ls_codemp."' ".
					"   AND sno_hsalida.codnom = '".$this->ls_codnom."' ".
					"   AND sno_hsalida.anocur = '".$this->ls_anocurnom."' ".
					"   AND sno_hsalida.codperi = '".$this->ls_peractnom."' ".
					"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1' OR sno_hsalida.tipsal = 'D' ".
					"    OR  sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2' OR sno_hsalida.tipsal = 'P1' OR sno_hsalida.tipsal = 'V3' OR sno_hsalida.tipsal = 'W3')".
					"   AND sno_hsalida.valsal <> 0".
					"   AND sno_hpersonalnomina.pagbanper = 0 ".
					"   AND sno_hpersonalnomina.pagtaqper = 0 ".
					"   AND sno_hpersonalnomina.pagefeper = 1 ".
					"   AND scg_cuentas.status = 'C'".
					"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
					"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
					"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
					"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
					"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
					"   AND scg_cuentas.codemp = sno_hpersonalnomina.codemp ".
					"   AND scg_cuentas.sc_cuenta = sno_hpersonalnomina.cueaboper ".
					" GROUP BY scg_cuentas.sc_cuenta ";
		}
		else
		{
			// Buscamos todas aquellas cuentas contables de los conceptos A y D, estas van por el haber de contabilidad
			$ls_sql=$ls_sql." UNION ".
					"SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) AS denominacion,  CAST('H' AS char(1)) as operacion, -sum(sno_hsalida.valsal) as total ".
					"  FROM sno_hpersonalnomina, sno_hsalida, sno_banco, scg_cuentas ".
					" WHERE sno_hsalida.codemp = '".$this->ls_codemp."' ".
					"   AND sno_hsalida.codnom = '".$this->ls_codnom."' ".
					"   AND sno_hsalida.anocur = '".$this->ls_anocurnom."' ".
					"   AND sno_hsalida.codperi = '".$this->ls_peractnom."' ".
					"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1' OR sno_hsalida.tipsal = 'D' ".
					"    OR  sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2' OR sno_hsalida.tipsal = 'P1' OR sno_hsalida.tipsal = 'V3' OR sno_hsalida.tipsal = 'W3')".
					"   AND sno_hsalida.valsal <> 0".
					"   AND (sno_hpersonalnomina.pagbanper = 1  OR sno_hpersonalnomina.pagtaqper = 1) ".
					"   AND sno_hpersonalnomina.pagefeper = 0 ".
					"   AND scg_cuentas.status = 'C'".
					"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
					"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
					"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
					"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
					"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
					"   AND sno_hsalida.codemp = sno_banco.codemp ".
					"   AND sno_hsalida.codnom = sno_banco.codnom ".
					"   AND sno_hsalida.codperi = sno_banco.codperi ".
					"   AND sno_hpersonalnomina.codemp = sno_banco.codemp ".
					"   AND sno_hpersonalnomina.codban = sno_banco.codban ".
					"   AND scg_cuentas.codemp = sno_banco.codemp ".
					"   AND scg_cuentas.sc_cuenta = sno_banco.codcuecon ".
					" GROUP BY scg_cuentas.sc_cuenta ";
			$ls_sql=$ls_sql." UNION ".
					"SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) AS denominacion, CAST('H' AS char(1)) as operacion, -sum(sno_hsalida.valsal) as total ".
					"  FROM sno_hpersonalnomina, sno_hsalida, scg_cuentas ".
					" WHERE sno_hsalida.codemp = '".$this->ls_codemp."' ".
					"   AND sno_hsalida.codnom = '".$this->ls_codnom."' ".
					"   AND sno_hsalida.anocur = '".$this->ls_anocurnom."' ".
					"   AND sno_hsalida.codperi = '".$this->ls_peractnom."' ".
					"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1' OR sno_hsalida.tipsal = 'D' ".
					"    OR  sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2' OR sno_hsalida.tipsal = 'P1' OR sno_hsalida.tipsal = 'V3' OR sno_hsalida.tipsal = 'W3')".
					"   AND sno_hsalida.valsal <> 0".
					"   AND sno_hpersonalnomina.pagbanper = 0 ".
					"   AND sno_hpersonalnomina.pagtaqper = 0 ".
					"   AND sno_hpersonalnomina.pagefeper = 1 ".
					"   AND scg_cuentas.status = 'C'".
					"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
					"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
					"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
					"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
					"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
					"   AND scg_cuentas.codemp = sno_hpersonalnomina.codemp ".
					"   AND scg_cuentas.sc_cuenta = sno_hpersonalnomina.cueaboper ".
					" GROUP BY scg_cuentas.sc_cuenta ";
		}
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_contableconceptos_contable_proyecto ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS_detalle->data=$this->io_sql->obtener_datos($rs_data);
			}
			$this->io_sql->free_result($rs_data);
		}		
		if($lb_valido)
		{
			$lb_valido=$this->uf_contableconceptos_contable_proyecto_dt();
			$this->DS_detalle->group_by(array('0'=>'cuenta','1'=>'operacion'),array('0'=>'total'),'total');		
			$this->DS_detalle->sortData('operacion');
		}
		return $lb_valido;    
	}// end function uf_contableconceptos_contable_proyecto
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_contableconceptos_contable_proyecto_dt()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_contableconceptos_contable_proyecto_dt 
		//	    Arguments: 
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Función que se encarga de procesar la data para la contabilización de los conceptos
		//     Creado por: Ing. Yesenia Moreno
		// Fecha Creación: 19/07/2007
		// SE MODIFICARON LAS TABLAS TH POR H
		///////////////////////////////////////////////////////////////////////////////////////////////////
	   	$lb_valido=true;
		$this->io_sql=new class_sql($this->io_conexion);		 
		switch($_SESSION["ls_gestor"])
		{
			case "MYSQLT":
				$ls_cadena=" ROUND((SUM(sno_hsalida.valsal)*MAX(sno_hproyectopersonal.pordiames)),3) ";
				break;
			case "POSTGRES":
				$ls_cadena=" ROUND(CAST((sum(sno_hsalida.valsal)*MAX(sno_hproyectopersonal.pordiames)) AS NUMERIC),3) ";
				break;					
		}
		// Buscamos todas aquellas cuentas contables que estan ligadas a las presupuestarias de los conceptos A y D, que se 
		// integran directamente con presupuesto, estas van por el debe de contabilidad
		$ls_sql="SELECT scg_cuentas.sc_cuenta as cuenta, CAST('D' AS char(1)) as operacion, sum(sno_hsalida.valsal) as total, ".
				"		".$ls_cadena." as montoparcial, sno_hproyectopersonal.codper, sno_hproyectopersonal.codproy, ".
				"		MAX(scg_cuentas.denominacion) AS denominacion, MAX(sno_hproyectopersonal.pordiames) as pordiames, sno_hconcepto.codconc ".
				"  FROM sno_hproyectopersonal, sno_hproyecto, sno_hsalida, sno_hconcepto, spg_cuentas, scg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1') ".
				"   AND sno_hsalida.valsal <> 0 ".
				"   AND sno_hconcepto.conprocon = '1' ".
				"   AND spg_cuentas.status = 'C'".
				"   AND sno_hproyectopersonal.codemp = sno_hsalida.codemp ".
				"   AND sno_hproyectopersonal.codnom = sno_hsalida.codnom ".
				"   AND sno_hproyectopersonal.anocur = sno_hsalida.anocur ".
				"   AND sno_hproyectopersonal.codperi = sno_hsalida.codperi ".
				"   AND sno_hproyectopersonal.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hproyectopersonal.codemp = sno_hproyecto.codemp ".
				"   AND sno_hproyectopersonal.codnom = sno_hproyecto.codnom ".
				"   AND sno_hproyectopersonal.anocur = sno_hproyecto.anocur ".
				"   AND sno_hproyectopersonal.codperi = sno_hproyecto.codperi ".
				"   AND sno_hproyectopersonal.codproy = sno_hproyecto.codproy ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprecon ".
				"   AND spg_cuentas.sc_cuenta = scg_cuentas.sc_cuenta".
				"   AND substr(sno_hproyecto.estproproy,1,25) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hproyecto.estproproy,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hproyecto.estproproy,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hproyecto.estproproy,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hproyecto.estproproy,101,25) = spg_cuentas.codestpro5 ".
				" GROUP BY sno_hproyectopersonal.codper, sno_hconcepto.codconc, sno_hproyectopersonal.codproy, scg_cuentas.sc_cuenta ";
		// Buscamos todas aquellas cuentas contables que estan ligadas a las presupuestarias de los conceptos A y D, que se 
		// integran directamente con presupuesto, estas van por el debe de contabilidad
		// Buscamos todas aquellas cuentas contables que estan ligadas a las presupuestarias de los conceptos A y D, que se 
		// integran directamente con presupuesto, estas van por el debe de contabilidad
		$ls_sql=$ls_sql." UNION ".
		$ls_sql="SELECT scg_cuentas.sc_cuenta as cuenta, CAST('D' AS char(1)) as operacion, sum(sno_hsalida.valsal) as total, ".
				"		".$ls_cadena." as montoparcial, sno_hproyectopersonal.codper, sno_hproyectopersonal.codproy, ".
				"		MAX(scg_cuentas.denominacion) AS denominacion, MAX(sno_hproyectopersonal.pordiames) as pordiames, sno_hconcepto.codconc ".
				"  FROM sno_hproyectopersonal, sno_hproyecto, sno_hsalida, sno_hconcepto, spg_cuentas, scg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'D' OR sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2') ".
				"   AND sno_hsalida.valsal <> 0 ".
				"   AND sno_hconcepto.sigcon = 'E' ".
				"   AND sno_hconcepto.conprocon = '1' ".
				"   AND spg_cuentas.status = 'C'".
				"   AND sno_hproyectopersonal.codemp = sno_hsalida.codemp ".
				"   AND sno_hproyectopersonal.codnom = sno_hsalida.codnom ".
				"   AND sno_hproyectopersonal.anocur = sno_hsalida.anocur ".
				"   AND sno_hproyectopersonal.codperi = sno_hsalida.codperi ".
				"   AND sno_hproyectopersonal.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hproyectopersonal.codemp = sno_hproyecto.codemp ".
				"   AND sno_hproyectopersonal.codnom = sno_hproyecto.codnom ".
				"   AND sno_hproyectopersonal.anocur = sno_hproyecto.anocur ".
				"   AND sno_hproyectopersonal.codperi = sno_hproyecto.codperi ".
				"   AND sno_hproyectopersonal.codproy = sno_hproyecto.codproy ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprecon ".
				"   AND spg_cuentas.sc_cuenta = scg_cuentas.sc_cuenta".
				"   AND substr(sno_hproyecto.estproproy,1,25) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hproyecto.estproproy,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hproyecto.estproproy,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hproyecto.estproproy,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hproyecto.estproproy,101,25) = spg_cuentas.codestpro5 ".
				" GROUP BY sno_hproyectopersonal.codper, sno_hconcepto.codconc, sno_hproyectopersonal.codproy, scg_cuentas.sc_cuenta ".
				" ORDER BY codper, codconc, codproy,  cuenta ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_contableconceptos_contable_proyecto_dt ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			$ls_codant="";
			$li_acumulado=0;
			$li_totalant=0;
			$ls_cuentaant="";
			$ls_codconcant="";
			$ls_operacionant="";
			$ls_denominacionant="";
			while(!$rs_data->EOF)
			{
				$ls_codper=$rs_data->fields["codper"];
				$ls_codconc=$rs_data->fields["codconc"];
				$li_montoparcial=$rs_data->fields["montoparcial"];
				$li_total=$rs_data->fields["total"];
				$ls_cuenta=$rs_data->fields["cuenta"];
				$ls_operacion=$rs_data->fields["operacion"];
				$ls_denominacion=$rs_data->fields["denominacion"];
				$li_pordiames=$rs_data->fields["pordiames"];
				if(($ls_codper!=$ls_codant)||($ls_codconc!=$ls_codconcant))
				{
					if($li_acumulado!=0)
					{
						if((round($li_acumulado,3)!=round($li_totalant,3))&&($li_pordiamesant<1))
						{
							$li_montoparcial=round(($li_totalant-$li_acumulado),3);
							$this->DS_detalle->insertRow("operacion",$ls_operacionant);
							$this->DS_detalle->insertRow("cuenta",$ls_cuentaant);
							$this->DS_detalle->insertRow("total",$li_montoparcial);
							$this->DS_detalle->insertRow("denominacion",$ls_denominacionant);
						}
					}
					$li_acumulado=$rs_data->fields["montoparcial"];
					$li_montoparcial=round($rs_data->fields["montoparcial"],3);
					$ls_operacionant=$ls_operacion;
					$ls_cuentaant=$ls_cuenta;
					$ls_codconcant=$ls_codconc;
					$ls_codant=$ls_codper;
					$ls_denominacionant=$ls_denominacion;
					$li_pordiamesant=$li_pordiames;
					$li_totalant=$li_total;
				}
				else
				{
					$li_acumulado=$li_acumulado+$li_montoparcial;
					$ls_operacionant=$ls_operacion;
					$ls_cuentaant=$ls_cuenta;
					$ls_codconcant=$ls_codconc;
					$li_totalant=$li_total;
					$ls_denominacionant=$ls_denominacion;
				}
				$this->DS_detalle->insertRow("operacion",$ls_operacion);
				$this->DS_detalle->insertRow("cuenta",$ls_cuenta);
				$this->DS_detalle->insertRow("total",$li_montoparcial);
				$this->DS_detalle->insertRow("denominacion",$ls_denominacion);
				$rs_data->MoveNext();
			}
			if((number_format($li_acumulado,3,".","")!=number_format($li_totalant,3,".",""))&&($li_pordiamesant<1))
			{
				$li_montoparcial=round(($li_totalant-$li_acumulado),3);
				$this->DS_detalle->insertRow("operacion",$ls_operacionant);
				$this->DS_detalle->insertRow("cuenta",$ls_cuentaant);
				$this->DS_detalle->insertRow("total",$li_montoparcial);
				$this->DS_detalle->insertRow("denominacion",$ls_denominacionant);
			}
			$this->io_sql->free_result($rs_data);
			$this->io_sql->free_result($rs_data);
		}		
		return  $lb_valido;    
	}// end function uf_contableconceptos_contable_proyecto_dt
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_contableingresos_ingreso()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_contableingresos_ingreso
		//         Access: public (desde la clase sigesp_sno_r_contableingresos)  
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de las cuentas de ingresos
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 25/03/2008 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$this->io_sql=new class_sql($this->io_conexion);
		$ls_estpreing=$_SESSION["la_empresa"]["estpreing"];
		if ($ls_estpreing==0)// no maneja las estructuras con las cuenats de ingresos
		{
			// Buscamos todas aquellas cuentas presupuestarias de los conceptos que se integran directamente con presupuesto
			$ls_sql="SELECT spi_cuentas.spi_cuenta AS cuenta, MAX(spi_cuentas.denominacion) AS denominacion, ".
					"		sum((sno_hsalida.valsal*sno_hconcepto.poringcon)/100) as total ".
					"  FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, spi_cuentas ".
					" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
					"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
					"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
					"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
					"   AND sno_hsalida.valsal <> 0 ".
					"   AND (sno_hsalida.tipsal = 'D' OR sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2' OR sno_hsalida.tipsal = 'P1' OR sno_hsalida.tipsal = 'V3' OR sno_hsalida.tipsal = 'W3' )".
					"   AND sno_hconcepto.intingcon = '1'".
					"   AND spi_cuentas.status = 'C' ".
					"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
					"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
					"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
					"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
					"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
					"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
					"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
					"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
					"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
					"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
					"   AND spi_cuentas.codemp = sno_hconcepto.codemp ".
					"   AND spi_cuentas.spi_cuenta = sno_hconcepto.spi_cuenta ".
					" GROUP BY spi_cuentas.spi_cuenta ";
		}
		else
		{
			$ls_sql="  SELECT  spi_cuentas.spi_cuenta AS cuenta, MAX(spi_cuentas.denominacion) AS denominacion, ". 
					"		   sum((sno_hsalida.valsal*sno_hconcepto.poringcon)/100) as total, ".
					"		   spi_cuentas_estructuras.codestpro1, ".
					"		   spi_cuentas_estructuras.codestpro2, ".
					"		   spi_cuentas_estructuras.codestpro3, ".
					"		   spi_cuentas_estructuras.codestpro4, ".
					"		   spi_cuentas_estructuras.codestpro5, ".
					"		   spi_cuentas_estructuras.estcla ".
					"   FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, spi_cuentas, ".
					"        spi_cuentas_estructuras, sno_hunidadadmin  ".
					"  WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
					"    AND sno_hsalida.codnom='".$this->ls_codnom."' ".
					"    AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
					"    AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
					"    AND sno_hsalida.valsal <> 0 ".
					"    AND (sno_hsalida.tipsal = 'D' ".
					"         OR sno_hsalida.tipsal = 'V2' ".
					"         OR sno_hsalida.tipsal = 'W2' ".
					"         OR sno_hsalida.tipsal = 'P1' ".
					"         OR sno_hsalida.tipsal = 'V3' ".
					"         OR sno_hsalida.tipsal = 'W3') ".
					"   AND sno_hconcepto.intingcon = '1' ".
					"   AND spi_cuentas.status = 'C' ".
					"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
					"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
					"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
					"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
					"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
					"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
					"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
					"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
					"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
					"   AND sno_hsalida.codconc = sno_hconcepto.codconc   ". 
					"   AND spi_cuentas.codemp = sno_hconcepto.codemp ".
					"   AND spi_cuentas.spi_cuenta = sno_hconcepto.spi_cuenta  ".
					"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ". 
					"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
					"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
					"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
					"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
					"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
					"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
					"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
					"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
					"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm   ". 
					"   AND spi_cuentas_estructuras.codemp=spi_cuentas.codemp ".
					"   AND spi_cuentas_estructuras.spi_cuenta= spi_cuentas.spi_cuenta    ".
					"   AND substr(sno_hconcepto.codpro,1,25)  = spi_cuentas_estructuras.codestpro1 ".
					"   AND substr(sno_hconcepto.codpro,26,25) = spi_cuentas_estructuras.codestpro2 ".
					"   AND substr(sno_hconcepto.codpro,51,25) = spi_cuentas_estructuras.codestpro3 ".
					"   AND substr(sno_hconcepto.codpro,76,25) = spi_cuentas_estructuras.codestpro4 ".
					"   AND substr(sno_hconcepto.codpro,101,25) = spi_cuentas_estructuras.codestpro5 ".
					"   AND sno_hconcepto.estcla = spi_cuentas_estructuras.estcla ".
					" GROUP BY spi_cuentas.spi_cuenta, spi_cuentas_estructuras.codestpro1, spi_cuentas_estructuras.codestpro2,".
					"		   spi_cuentas_estructuras.codestpro3,  spi_cuentas_estructuras.codestpro4,".
					"		   spi_cuentas_estructuras.codestpro5,  spi_cuentas_estructuras.estcla ";
			$ls_sql=$ls_sql."   UNION  ".
					" SELECT spi_cuentas.spi_cuenta AS cuenta, MAX(spi_cuentas.denominacion) AS denominacion, ".
					"		   sum((sno_hsalida.valsal*sno_hconcepto.poringcon)/100) as total, ".
					"		   spi_cuentas_estructuras.codestpro1,  spi_cuentas_estructuras.codestpro2, ".
					"		   spi_cuentas_estructuras.codestpro3,  spi_cuentas_estructuras.codestpro4, ".
					"		   spi_cuentas_estructuras.codestpro5,  spi_cuentas_estructuras.estcla ".
					"  FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, spi_cuentas, ".
					"       spi_cuentas_estructuras, sno_hunidadadmin  ".
					" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
					"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
					"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
					"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ". 
					"   AND sno_hsalida.valsal <> 0 ".
					"   AND (sno_hsalida.tipsal = 'D' ".
					"        OR sno_hsalida.tipsal = 'V2' ".
					"        OR sno_hsalida.tipsal = 'W2' ".
					"        OR sno_hsalida.tipsal = 'P1' ".
					"        OR sno_hsalida.tipsal = 'V3' ".
					"        OR sno_hsalida.tipsal = 'W3') ".
					"   AND sno_hconcepto.intingcon = '1' ".
					"   AND spi_cuentas.status = 'C' ".
					"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
					"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
					"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
					"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
					"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
					"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
					"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
					"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
					"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
					"   AND sno_hsalida.codconc = sno_hconcepto.codconc   ". 
					"   AND spi_cuentas.codemp = sno_hconcepto.codemp ".
					"   AND spi_cuentas.spi_cuenta = sno_hconcepto.spi_cuenta  ".
					"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ". 
					"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
					"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
					"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
					"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
					"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
					"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
					"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
					"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
					"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm   ". 
					"   AND spi_cuentas_estructuras.codemp=spi_cuentas.codemp ".
					"   AND spi_cuentas_estructuras.spi_cuenta= spi_cuentas.spi_cuenta    ".
					"   AND substr(sno_hunidadadmin.codprouniadm,1,25) =  spi_cuentas_estructuras.codestpro1 ".
					"   AND substr(sno_hunidadadmin.codprouniadm,26,25) = spi_cuentas_estructuras.codestpro2 ".
					"   AND substr(sno_hunidadadmin.codprouniadm,51,25) = spi_cuentas_estructuras.codestpro3 ".
					"   AND substr(sno_hunidadadmin.codprouniadm,76,25) = spi_cuentas_estructuras.codestpro4 ".
					"   AND substr(sno_hunidadadmin.codprouniadm,101,25) = spi_cuentas_estructuras.codestpro5 ".
					"   AND sno_hunidadadmin.estcla = spi_cuentas_estructuras.estcla   ".
					" GROUP BY spi_cuentas.spi_cuenta, spi_cuentas_estructuras.codestpro1,".
					"		   spi_cuentas_estructuras.codestpro2, spi_cuentas_estructuras.codestpro3,".
					"		   spi_cuentas_estructuras.codestpro4, spi_cuentas_estructuras.codestpro5,".
					"		   spi_cuentas_estructuras.estcla ";
		}	
		
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_contableingresos_ingreso ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_contableingresos_ingreso
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_contableingresos_contable()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_contableingresos_contable
		//         Access: public (desde la clase sigesp_sno_r_contableingresos)  
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de las cuentas contables que afectan los conceptos de tipo P2
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 11/05/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$this->io_sql=new class_sql($this->io_conexion);
		$ls_estpreing=$_SESSION["la_empresa"]["estpreing"];
		if ($ls_estpreing==0)// no maneja las estructuras con las cuenats de ingresos
		{
			// Buscamos todas aquellas cuentas contables que estan ligadas a las presupuestarias de los conceptos que se 
			// integran directamente con presupuesto estas van por el debe de contabilidad
			$ls_sql="SELECT spi_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) as denominacion, 'H' as operacion, ".
					"		sum((sno_hsalida.valsal*sno_hconcepto.poringcon)/100) as total ".
					"  FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, spi_cuentas, scg_cuentas ".
					" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
					"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
					"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
					"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
					"   AND sno_hsalida.valsal <> 0 ".
					"   AND (sno_hsalida.tipsal = 'D' OR sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2' OR sno_hsalida.tipsal = 'P1' OR sno_hsalida.tipsal = 'V3' OR sno_hsalida.tipsal = 'W3' )".
					"   AND sno_hconcepto.intingcon = '1'".
					"   AND spi_cuentas.status = 'C'".
					"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
					"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
					"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
					"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
					"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
					"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
					"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
					"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
					"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
					"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
					"   AND spi_cuentas.codemp = sno_hconcepto.codemp ".
					"   AND spi_cuentas.spi_cuenta = sno_hconcepto.spi_cuenta ".
					"   AND spi_cuentas.sc_cuenta = scg_cuentas.sc_cuenta".
					"   GROUP BY spi_cuentas.sc_cuenta ";
			// Buscamos todas aquellas cuentas contables que estan ligadas a las presupuestarias de los conceptos que NO se 
			// integran directamente con presupuesto entonces las buscamos según la estructura de la unidad administrativa a 
			// la que pertenece el personal, estas van por el debe de contabilidad
			$ls_sql=$ls_sql." UNION ".
					"SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) as denoconta, 'D' as operacion, ".
					"		sum((sno_hsalida.valsal*sno_hconcepto.poringcon)/100) as total ".
					"  FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, scg_cuentas ".
					" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
					"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
					"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
					"   AND sno_hsalida.valsal <> 0 ".
					"   AND (sno_hsalida.tipsal = 'D' OR sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2' OR sno_hsalida.tipsal = 'P1' OR sno_hsalida.tipsal = 'V3' OR sno_hsalida.tipsal = 'W3' )".
					"   AND sno_hconcepto.intingcon = '1'".
					"   AND scg_cuentas.status = 'C'".
					"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
					"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
					"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
					"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
					"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
					"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
					"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
					"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
					"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
					"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
					"   AND scg_cuentas.codemp = sno_hconcepto.codemp ".
					"   AND scg_cuentas.sc_cuenta = sno_hconcepto.cueconcon  ".
					"   GROUP BY scg_cuentas.sc_cuenta ";
		}
		else
		{  
			$ls_sql="   SELECT spi_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) as denominacion, ".
					"		   'H' as operacion, sum((sno_hsalida.valsal*sno_hconcepto.poringcon)/100) as total, ".
					"		   spi_cuentas_estructuras.codestpro1, spi_cuentas_estructuras.codestpro2,".
					"		   spi_cuentas_estructuras.codestpro3, spi_cuentas_estructuras.codestpro4,".
					"		   spi_cuentas_estructuras.codestpro5, spi_cuentas_estructuras.estcla ".
					"    FROM  sno_hpersonalnomina, sno_hsalida, sno_hconcepto, spi_cuentas, ".
					"          scg_cuentas, spi_cuentas_estructuras, sno_hunidadadmin ".
				    "   WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
					"     AND sno_hsalida.codnom='".$this->ls_codnom."' ".
					"     AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
					"     AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
					"	  AND sno_hsalida.valsal <> 0 ".
					"	  AND (sno_hsalida.tipsal = 'D' ".
					"         OR sno_hsalida.tipsal = 'V2'". 
					"         OR sno_hsalida.tipsal = 'W2' ".
					"         OR sno_hsalida.tipsal = 'P1' ".
					"         OR sno_hsalida.tipsal = 'V3' ".
					"         OR sno_hsalida.tipsal = 'W3') ". 
					" 	 AND sno_hconcepto.intingcon = '1' ".
					"	 AND spi_cuentas.status = 'C' ".
					"	 AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
					"	 AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
					"	 AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
					"	 AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
					"	 AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
					"	 AND sno_hsalida.codemp = sno_hconcepto.codemp ".
					"	 AND sno_hsalida.codnom = sno_hconcepto.codnom ".
					"	 AND sno_hsalida.anocur = sno_hconcepto.anocur ".
					"	 AND sno_hsalida.codperi = sno_hconcepto.codperi ".
					"	 AND sno_hsalida.codconc = sno_hconcepto.codconc ".
					"	 AND spi_cuentas.codemp = sno_hconcepto.codemp ".
					"	 AND spi_cuentas.spi_cuenta = sno_hconcepto.spi_cuenta ".
					"	 AND spi_cuentas.sc_cuenta = scg_cuentas.sc_cuenta ".
					"	 AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
					"	 AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
					"	 AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
					"	 AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
					"	 AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
					"	 AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
					"	 AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
					"	 AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
					"	 AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
					"	 AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm   ". 
					"	 AND spi_cuentas_estructuras.codemp=spi_cuentas.codemp ".
					"	 AND spi_cuentas_estructuras.spi_cuenta= spi_cuentas.spi_cuenta      ".
					"	 AND substr(sno_hconcepto.codpro,1,25)  = spi_cuentas_estructuras.codestpro1 ".
					"	 AND substr(sno_hconcepto.codpro,26,25) = spi_cuentas_estructuras.codestpro2 ".
					"	 AND substr(sno_hconcepto.codpro,51,25) = spi_cuentas_estructuras.codestpro3 ".
					"	 AND substr(sno_hconcepto.codpro,76,25) = spi_cuentas_estructuras.codestpro4 ".
					"	 AND substr(sno_hconcepto.codpro,101,25) = spi_cuentas_estructuras.codestpro5 ".
					"	 AND sno_hconcepto.estcla = spi_cuentas_estructuras.estcla ".
					"	GROUP BY spi_cuentas.sc_cuenta,spi_cuentas_estructuras.codestpro1, spi_cuentas_estructuras.codestpro2,".
					"		   spi_cuentas_estructuras.codestpro3, spi_cuentas_estructuras.codestpro4,".
					"		   spi_cuentas_estructuras.codestpro5, spi_cuentas_estructuras.estcla ";
			$ls_sql=$ls_sql."		UNION   ".
					"  SELECT spi_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) as denominacion, ".
					"		   'H' as operacion, sum((sno_hsalida.valsal*sno_hconcepto.poringcon)/100) as total, ".
					"		   spi_cuentas_estructuras.codestpro1, spi_cuentas_estructuras.codestpro2,".
					"		   spi_cuentas_estructuras.codestpro3, spi_cuentas_estructuras.codestpro4, ".
					"		   spi_cuentas_estructuras.codestpro5, spi_cuentas_estructuras.estcla ".
					"   FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, spi_cuentas, ".
					"        scg_cuentas, spi_cuentas_estructuras, sno_hunidadadmin ".
					" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
					"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
					"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
					"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
					"	AND sno_hsalida.valsal <> 0 ".
					"	AND (sno_hsalida.tipsal = 'D' ".
					"        OR sno_hsalida.tipsal = 'V2' ".
					"        OR sno_hsalida.tipsal = 'W2' ".
					"        OR sno_hsalida.tipsal = 'P1' ".
					"        OR sno_hsalida.tipsal = 'V3' ".
					"        OR sno_hsalida.tipsal = 'W3') ". 
					"	AND sno_hconcepto.intingcon = '1' ".
					"	AND spi_cuentas.status = 'C' ".
					"	AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
					"	AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
					"	AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ". 
					"	AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
					"	AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
					"	AND sno_hsalida.codemp = sno_hconcepto.codemp ".
					"	AND sno_hsalida.codnom = sno_hconcepto.codnom ".
					"	AND sno_hsalida.anocur = sno_hconcepto.anocur ".
					"	AND sno_hsalida.codperi = sno_hconcepto.codperi ".
					"	AND sno_hsalida.codconc = sno_hconcepto.codconc ".
					"	AND spi_cuentas.codemp = sno_hconcepto.codemp ".
					"	AND spi_cuentas.spi_cuenta = sno_hconcepto.spi_cuenta ".
					"	AND spi_cuentas.sc_cuenta = scg_cuentas.sc_cuenta ".
					"	AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
					"	AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
					"	AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
					"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
					"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
					"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
					"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
					"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
					"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
					"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm   ". 
					"   AND spi_cuentas_estructuras.codemp=spi_cuentas.codemp ".
					"   AND spi_cuentas_estructuras.spi_cuenta= spi_cuentas.spi_cuenta      ".
					"   AND substr(sno_hunidadadmin.codprouniadm,1,25) =  spi_cuentas_estructuras.codestpro1 ".
					"   AND substr(sno_hunidadadmin.codprouniadm,26,25) = spi_cuentas_estructuras.codestpro2 ".
					"   AND substr(sno_hunidadadmin.codprouniadm,51,25) = spi_cuentas_estructuras.codestpro3 ".
					"   AND substr(sno_hunidadadmin.codprouniadm,76,25) = spi_cuentas_estructuras.codestpro4 ".
					"   AND substr(sno_hunidadadmin.codprouniadm,101,25) = spi_cuentas_estructuras.codestpro5 ".
					"   AND sno_hunidadadmin.estcla = spi_cuentas_estructuras.estcla   ".
					" GROUP BY spi_cuentas.sc_cuenta,spi_cuentas_estructuras.codestpro1, spi_cuentas_estructuras.codestpro2,".
					"		   spi_cuentas_estructuras.codestpro3, spi_cuentas_estructuras.codestpro4,".
					"		   spi_cuentas_estructuras.codestpro5, spi_cuentas_estructuras.estcla ";
			$ls_sql=$ls_sql."		UNION   ".
					"	SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) as denoconta, 'D' as operacion, ".
					"		   sum((sno_hsalida.valsal*sno_hconcepto.poringcon)/100) as total, ".
					"		   spi_cuentas_estructuras.codestpro1, spi_cuentas_estructuras.codestpro2, ".
					"		   spi_cuentas_estructuras.codestpro3, spi_cuentas_estructuras.codestpro4, ".
					"		   spi_cuentas_estructuras.codestpro5, spi_cuentas_estructuras.estcla  ".
					"    FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, scg_cuentas, spi_cuentas, ".
					"         spi_cuentas_estructuras, sno_hunidadadmin ".
					" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
					"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
					"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
					"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
					"   AND sno_hsalida.valsal <> 0 ".
					"   AND (sno_hsalida.tipsal = 'D' ".
					"        OR sno_hsalida.tipsal = 'V2' ". 
					"        OR sno_hsalida.tipsal = 'W2' ".
					"        OR sno_hsalida.tipsal = 'P1' ".
					"        OR sno_hsalida.tipsal = 'V3' ".
					"        OR sno_hsalida.tipsal = 'W3') ". 
					"   AND sno_hconcepto.intingcon = '1' ".
					"   AND scg_cuentas.status = 'C' ".
					"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
					"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
					"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
					"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
					"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
					"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
					"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
					"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
					"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
					"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
					"   AND scg_cuentas.codemp = sno_hconcepto.codemp ".
					"   AND scg_cuentas.sc_cuenta = sno_hconcepto.cueconcon ".
					"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
					"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
					"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
					"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
					"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
					"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
					"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
					"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
					"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
					"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm   ". 
					"   AND spi_cuentas_estructuras.codemp=spi_cuentas.codemp ".
					"   AND spi_cuentas_estructuras.spi_cuenta= spi_cuentas.spi_cuenta      ".
					"   AND substr(sno_hconcepto.codpro,1,25)  = spi_cuentas_estructuras.codestpro1 ".
					"   AND substr(sno_hconcepto.codpro,26,25) = spi_cuentas_estructuras.codestpro2 ".
					"   AND substr(sno_hconcepto.codpro,51,25) = spi_cuentas_estructuras.codestpro3 ".
					"   AND substr(sno_hconcepto.codpro,76,25) = spi_cuentas_estructuras.codestpro4 ".
					"   AND substr(sno_hconcepto.codpro,101,25) = spi_cuentas_estructuras.codestpro5 ".
					"   AND sno_hconcepto.estcla = spi_cuentas_estructuras.estcla ".
					" GROUP BY scg_cuentas.sc_cuenta, spi_cuentas_estructuras.codestpro1, spi_cuentas_estructuras.codestpro2,".
					"		   spi_cuentas_estructuras.codestpro3, spi_cuentas_estructuras.codestpro4,".
					"		   spi_cuentas_estructuras.codestpro5, spi_cuentas_estructuras.estcla ";
						
		    $ls_sql=$ls_sql."		UNION   ".						
			   	    "   SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) as denoconta, 'D' as operacion, ".
				    "		   sum((sno_hsalida.valsal*sno_hconcepto.poringcon)/100) as total,".
					"		   spi_cuentas_estructuras.codestpro1, spi_cuentas_estructuras.codestpro2, ".
					"		   spi_cuentas_estructuras.codestpro3, spi_cuentas_estructuras.codestpro4, ".
					"		   spi_cuentas_estructuras.codestpro5, spi_cuentas_estructuras.estcla  ".
					"    FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, scg_cuentas, spi_cuentas, ".
					"         spi_cuentas_estructuras, sno_hunidadadmin ".
				    " WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
					"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
					"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
					"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
					"   AND sno_hsalida.valsal <> 0 ".
					"   AND (sno_hsalida.tipsal = 'D' ".
					"        OR sno_hsalida.tipsal = 'V2' ".
					"        OR sno_hsalida.tipsal = 'W2' ".
					"        OR sno_hsalida.tipsal = 'P1' ".
					"        OR sno_hsalida.tipsal = 'V3' ".
					"        OR sno_hsalida.tipsal = 'W3') ".
					"   AND sno_hconcepto.intingcon = '1' ".
					"   AND scg_cuentas.status = 'C' ".
					"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
					"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
					"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
					"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
					"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
					"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
					"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
					"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
					"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
					"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
					"   AND scg_cuentas.codemp = sno_hconcepto.codemp ".
					"   AND scg_cuentas.sc_cuenta = sno_hconcepto.cueconcon ".
					"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
					"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
					"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
					"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
					"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
					"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
					"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
					"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
					"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
					"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm   ". 
					"   AND spi_cuentas_estructuras.codemp=spi_cuentas.codemp ".
					"   AND spi_cuentas_estructuras.spi_cuenta= spi_cuentas.spi_cuenta      ".
					"   AND substr(sno_hunidadadmin.codprouniadm,1,25) =  spi_cuentas_estructuras.codestpro1 ".
					"   AND substr(sno_hunidadadmin.codprouniadm,26,25) = spi_cuentas_estructuras.codestpro2 ".
					"   AND substr(sno_hunidadadmin.codprouniadm,51,25) = spi_cuentas_estructuras.codestpro3 ".
					"   AND substr(sno_hunidadadmin.codprouniadm,76,25) = spi_cuentas_estructuras.codestpro4 ".
					"   AND substr(sno_hunidadadmin.codprouniadm,101,25) = spi_cuentas_estructuras.codestpro5 ".
					"   AND sno_hunidadadmin.estcla = spi_cuentas_estructuras.estcla   ".
					" GROUP BY scg_cuentas.sc_cuenta, spi_cuentas_estructuras.codestpro1, spi_cuentas_estructuras.codestpro2,".
					"		   spi_cuentas_estructuras.codestpro3, spi_cuentas_estructuras.codestpro4,".
					"		   spi_cuentas_estructuras.codestpro5, spi_cuentas_estructuras.estcla ";
		
		}
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_contableaportes_contable ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS_detalle->data=$this->io_sql->obtener_datos($rs_data);
				$this->DS_detalle->group_by(array('0'=>'cuenta','1'=>'operacion'),array('0'=>'total'),array('0'=>'cuenta','1'=>'operacion'));
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_contableingresos_contable
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_contableconceptos_especifico_presupuesto($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,$as_estcla,
														 $as_subnomdes,$as_subnomhas)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_contableconceptos_especifico_presupuesto
		//         Access: public (desde la clase sigesp_sno_r_contableconceptos)  
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de las cuentas presupuestarias que afectan los conceptos de tipo A, D, P1
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 22/05/2006 								Fecha Última Modificación :  
		// SE MODIFICARON LAS TABLAS TH POR H
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		if(!empty($as_estcla))
		{
			$ls_criterio="   AND spg_cuentas.estcla = '".$as_estcla."'".
						 "   AND spg_cuentas.codestpro1 = '".str_pad($as_codestpro1,25,"0","0")."'".
						 "   AND spg_cuentas.codestpro2 = '".str_pad($as_codestpro2,25,"0","0")."'".
						 "   AND spg_cuentas.codestpro3 = '".str_pad($as_codestpro3,25,"0","0")."'".
						 "   AND spg_cuentas.codestpro4 = '".str_pad($as_codestpro4,25,"0","0")."'".
						 "   AND spg_cuentas.codestpro5 = '".str_pad($as_codestpro5,25,"0","0")."'";
		}
		if(!empty($as_subnomdes))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codsubnom>='".$as_subnomdes."'";
		}
		if(!empty($as_subnomhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codsubnom<='".$as_subnomhas."'";
		}
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A y D, que se integran directamente con presupuesto
		$ls_sql="SELECT sno_hconcepto.codpro as programatica, sno_hconcepto.estcla, sno_hconcepto.cueprecon, ".
				"		MAX(spg_cuentas.denominacion) AS denominacion, sum(sno_hsalida.valsal) as total, sno_hpersonalnomina.codded, sno_hpersonalnomina.codtipper, COUNT(sno_hpersonalnomina.codper) AS totalpersonal, ".
				"		MAX(sno_dedicacion.desded) AS desded, MAX(sno_tipopersonal.destipper) AS destipper  ".
				"  FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, spg_cuentas, sno_dedicacion, sno_tipopersonal  ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1') ".
				"   AND sno_hsalida.valsal <> 0".
				"   AND sno_hconcepto.intprocon = '1'".
				"   AND spg_cuentas.status = 'C'".
				$ls_criterio.
				"   AND sno_hpersonalnomina.codemp = sno_dedicacion.codemp ".
				"   AND sno_hpersonalnomina.codded = sno_dedicacion.codded ".
				"   AND sno_hpersonalnomina.codemp = sno_tipopersonal.codemp ".
				"   AND sno_hpersonalnomina.codded = sno_tipopersonal.codded ".
				"   AND sno_hpersonalnomina.codtipper = sno_tipopersonal.codtipper ".
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
				"   AND sno_hconcepto.codemp = spg_cuentas.codemp ".
				"   AND sno_hconcepto.cueprecon = spg_cuentas.spg_cuenta ".
				"   AND substr(sno_hconcepto.codpro,1,25) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hconcepto.codpro,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hconcepto.codpro,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hconcepto.codpro,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hconcepto.codpro,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_hconcepto.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_hconcepto.codpro, sno_hconcepto.estcla, sno_hconcepto.cueprecon, sno_hpersonalnomina.codded, sno_hpersonalnomina.codtipper ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A y D, que no se integran directamente con presupuesto
		// entonces las buscamos según la estructura de la unidad administrativa a la que pertenece el personal
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_hunidadadmin.codprouniadm as programatica, sno_hunidadadmin.estcla, sno_hconcepto.cueprecon, ".
				"		MAX(spg_cuentas.denominacion) AS denominacion, sum(sno_hsalida.valsal) as total, sno_hpersonalnomina.codded, sno_hpersonalnomina.codtipper, COUNT(sno_hpersonalnomina.codper) AS totalpersonal, ".
				"		MAX(sno_dedicacion.desded) AS desded, MAX(sno_tipopersonal.destipper) AS destipper  ".
				"  FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto, spg_cuentas, sno_dedicacion, sno_tipopersonal  ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1') ".
				"   AND sno_hsalida.valsal <> 0".
				"   AND sno_hconcepto.intprocon = '0'".
				"   AND spg_cuentas.status = 'C'".
				$ls_criterio.
				"   AND sno_hpersonalnomina.codemp = sno_dedicacion.codemp ".
				"   AND sno_hpersonalnomina.codded = sno_dedicacion.codded ".
				"   AND sno_hpersonalnomina.codemp = sno_tipopersonal.codemp ".
				"   AND sno_hpersonalnomina.codded = sno_tipopersonal.codded ".
				"   AND sno_hpersonalnomina.codtipper = sno_tipopersonal.codtipper ".
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
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprecon ".
				"   AND sno_hunidadadmin.codemp = spg_cuentas.codemp ".
				"   AND substr(sno_hunidadadmin.codprouniadm,1,25) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_hunidadadmin.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_hunidadadmin.codprouniadm,sno_hunidadadmin.estcla, sno_hconcepto.cueprecon, sno_hpersonalnomina.codded, sno_hpersonalnomina.codtipper ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A y D, que se integran directamente con presupuesto
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_hconcepto.codpro as programatica, sno_hconcepto.estcla, sno_hconcepto.cueprecon, ".
				"		MAX(spg_cuentas.denominacion) AS denominacion, sum(sno_hsalida.valsal) as total, sno_hpersonalnomina.codded, sno_hpersonalnomina.codtipper, COUNT(sno_hpersonalnomina.codper) AS totalpersonal, ".
				"		MAX(sno_dedicacion.desded) AS desded, MAX(sno_tipopersonal.destipper) AS destipper  ".
				"  FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, spg_cuentas, sno_dedicacion, sno_tipopersonal  ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'D' OR sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2') ".
				"   AND sno_hconcepto.sigcon = 'E'".
				"   AND sno_hsalida.valsal <> 0".
				"   AND sno_hconcepto.intprocon = '1'".
				"   AND spg_cuentas.status = 'C'".
				$ls_criterio.
				"   AND sno_hpersonalnomina.codemp = sno_dedicacion.codemp ".
				"   AND sno_hpersonalnomina.codded = sno_dedicacion.codded ".
				"   AND sno_hpersonalnomina.codemp = sno_tipopersonal.codemp ".
				"   AND sno_hpersonalnomina.codded = sno_tipopersonal.codded ".
				"   AND sno_hpersonalnomina.codtipper = sno_tipopersonal.codtipper ".
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
				"   AND sno_hconcepto.codemp = spg_cuentas.codemp ".
				"   AND sno_hconcepto.cueprecon = spg_cuentas.spg_cuenta ".
				"   AND substr(sno_hconcepto.codpro,1,25) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hconcepto.codpro,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hconcepto.codpro,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hconcepto.codpro,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hconcepto.codpro,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_hconcepto.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_hconcepto.codpro,sno_hconcepto.estcla, sno_hconcepto.cueprecon, sno_hpersonalnomina.codded, sno_hpersonalnomina.codtipper ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A y D, que no se integran directamente con presupuesto
		// entonces las buscamos según la estructura de la unidad administrativa a la que pertenece el personal
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_hunidadadmin.codprouniadm as programatica, sno_hunidadadmin.estcla, sno_hconcepto.cueprecon, ".
				"		MAX(spg_cuentas.denominacion) AS denominacion, sum(sno_hsalida.valsal) as total, sno_hpersonalnomina.codded, sno_hpersonalnomina.codtipper, COUNT(sno_hpersonalnomina.codper) AS totalpersonal, ".
				"		MAX(sno_dedicacion.desded) AS desded, MAX(sno_tipopersonal.destipper) AS destipper  ".
				"  FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto, spg_cuentas, sno_dedicacion, sno_tipopersonal  ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'D' OR sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2') ".
				"   AND sno_hconcepto.sigcon = 'E'".
				"   AND sno_hsalida.valsal <> 0".
				"   AND sno_hconcepto.intprocon = '0'".
				"   AND spg_cuentas.status = 'C'".
				$ls_criterio.
				"   AND sno_hpersonalnomina.codemp = sno_dedicacion.codemp ".
				"   AND sno_hpersonalnomina.codded = sno_dedicacion.codded ".
				"   AND sno_hpersonalnomina.codemp = sno_tipopersonal.codemp ".
				"   AND sno_hpersonalnomina.codded = sno_tipopersonal.codded ".
				"   AND sno_hpersonalnomina.codtipper = sno_tipopersonal.codtipper ".
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
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprecon ".
				"   AND sno_hunidadadmin.codemp = spg_cuentas.codemp ".
				"   AND substr(sno_hunidadadmin.codprouniadm,1,25) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_hunidadadmin.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_hunidadadmin.codprouniadm,sno_hunidadadmin.estcla, sno_hconcepto.cueprecon, sno_hpersonalnomina.codded, sno_hpersonalnomina.codtipper ".
				" ORDER BY programatica, cueprecon";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_contableconceptos_presupuesto ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_contableconceptos_especifico_presupuesto
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_contableconceptos_especifico_presupuesto_proyecto($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,$as_estcla,
																  $as_subnomdes,$as_subnomhas)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_contableconceptos_presupuesto_proyecto
		//         Access: public (desde la clase sigesp_sno_r_contableconceptos)  
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de las cuentas presupuestarias que afectan los conceptos de tipo A, D, P1
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 19/07/2007 								Fecha Última Modificación :  
		// SE MODIFICARON LAS TABLAS TH POR H
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		if(!empty($as_estcla))
		{
			$ls_criterio="   AND spg_cuentas.estcla = '".$as_estcla."'".
						 "   AND spg_cuentas.codestpro1 = '".str_pad($as_codestpro1,25,"0","0")."'".
						 "   AND spg_cuentas.codestpro2 = '".str_pad($as_codestpro2,25,"0","0")."'".
						 "   AND spg_cuentas.codestpro3 = '".str_pad($as_codestpro3,25,"0","0")."'".
						 "   AND spg_cuentas.codestpro4 = '".str_pad($as_codestpro4,25,"0","0")."'".
						 "   AND spg_cuentas.codestpro5 = '".str_pad($as_codestpro5,25,"0","0")."'";
		}
		if(!empty($as_subnomdes))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codsubnom>='".$as_subnomdes."'";
		}
		if(!empty($as_subnomhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codsubnom<='".$as_subnomhas."'";
		}
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A y D, que se integran directamente con presupuesto
		$ls_sql="SELECT sno_hconcepto.codpro as programatica, sno_hconcepto.estcla, sno_hconcepto.cueprecon, ".
				"		MAX(spg_cuentas.denominacion) AS denominacion, sum(sno_hsalida.valsal) as total, sno_hpersonalnomina.codded, sno_hpersonalnomina.codtipper, COUNT(sno_hpersonalnomina.codper) AS totalpersonal, ".
				"		MAX(sno_dedicacion.desded) AS desded, MAX(sno_tipopersonal.destipper) AS destipper  ".
				"  FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, spg_cuentas, sno_dedicacion, sno_tipopersonal ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1') ".
				"   AND sno_hsalida.valsal <> 0".
				"   AND sno_hconcepto.intprocon = '1' ".
				"   AND sno_hconcepto.conprocon = '0' ".
				"   AND spg_cuentas.status = 'C'".
				$ls_criterio.
				"   AND sno_hpersonalnomina.codemp = sno_dedicacion.codemp ".
				"   AND sno_hpersonalnomina.codded = sno_dedicacion.codded ".
				"   AND sno_hpersonalnomina.codemp = sno_tipopersonal.codemp ".
				"   AND sno_hpersonalnomina.codded = sno_tipopersonal.codded ".
				"   AND sno_hpersonalnomina.codtipper = sno_tipopersonal.codtipper ".
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
				"   AND sno_hconcepto.codemp = spg_cuentas.codemp ".
				"   AND sno_hconcepto.cueprecon = spg_cuentas.spg_cuenta ".
				"   AND substr(sno_hconcepto.codpro,1,25) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hconcepto.codpro,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hconcepto.codpro,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hconcepto.codpro,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hconcepto.codpro,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_hconcepto.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_hconcepto.codpro, sno_hconcepto.estcla, sno_hconcepto.cueprecon, sno_hpersonalnomina.codded, sno_hpersonalnomina.codtipper ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A y D, que no se integran directamente con presupuesto
		// entonces las buscamos según la estructura de la unidad administrativa a la que pertenece el personal
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_hunidadadmin.codprouniadm as programatica, sno_hunidadadmin.estcla, sno_hconcepto.cueprecon, ".
				"		MAX(spg_cuentas.denominacion) AS denominacion, sum(sno_hsalida.valsal) as total, sno_hpersonalnomina.codded, sno_hpersonalnomina.codtipper, COUNT(sno_hpersonalnomina.codper) AS totalpersonal, ".
				"		MAX(sno_dedicacion.desded) AS desded, MAX(sno_tipopersonal.destipper) AS destipper  ".
				"  FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto, spg_cuentas, sno_dedicacion, sno_tipopersonal ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1') ".
				"   AND sno_hsalida.valsal <> 0".
				"   AND sno_hconcepto.intprocon = '0'".
				"   AND sno_hconcepto.conprocon = '0' ".
				"   AND spg_cuentas.status = 'C'".
				$ls_criterio.
				"   AND sno_hpersonalnomina.codemp = sno_dedicacion.codemp ".
				"   AND sno_hpersonalnomina.codded = sno_dedicacion.codded ".
				"   AND sno_hpersonalnomina.codemp = sno_tipopersonal.codemp ".
				"   AND sno_hpersonalnomina.codded = sno_tipopersonal.codded ".
				"   AND sno_hpersonalnomina.codtipper = sno_tipopersonal.codtipper ".
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
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprecon ".
				"   AND sno_hunidadadmin.codemp = spg_cuentas.codemp ".
				"   AND substr(sno_hunidadadmin.codprouniadm,1,25) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_hunidadadmin.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_hunidadadmin.codprouniadm, sno_hunidadadmin.estcla, sno_hconcepto.cueprecon, sno_hpersonalnomina.codded, sno_hpersonalnomina.codtipper ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A y D, que se integran directamente con presupuesto
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_hconcepto.codpro as programatica, sno_hconcepto.estcla, sno_hconcepto.cueprecon, ".
				"		MAX(spg_cuentas.denominacion) AS denominacion, sum(sno_hsalida.valsal) as total, sno_hpersonalnomina.codded, sno_hpersonalnomina.codtipper, COUNT(sno_hpersonalnomina.codper) AS totalpersonal, ".
				"		MAX(sno_dedicacion.desded) AS desded, MAX(sno_tipopersonal.destipper) AS destipper  ".
				"  FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, spg_cuentas, sno_dedicacion, sno_tipopersonal ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'D' OR sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2') ".
				"   AND sno_hconcepto.sigcon = 'E'".
				"   AND sno_hsalida.valsal <> 0".
				"   AND sno_hconcepto.intprocon = '1'".
				"   AND sno_hconcepto.conprocon = '0' ".
				"   AND spg_cuentas.status = 'C'".
				$ls_criterio.
				"   AND sno_hpersonalnomina.codemp = sno_dedicacion.codemp ".
				"   AND sno_hpersonalnomina.codded = sno_dedicacion.codded ".
				"   AND sno_hpersonalnomina.codemp = sno_tipopersonal.codemp ".
				"   AND sno_hpersonalnomina.codded = sno_tipopersonal.codded ".
				"   AND sno_hpersonalnomina.codtipper = sno_tipopersonal.codtipper ".
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
				"   AND sno_hconcepto.codemp = spg_cuentas.codemp ".
				"   AND sno_hconcepto.cueprecon = spg_cuentas.spg_cuenta ".
				"   AND substr(sno_hconcepto.codpro,1,25) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hconcepto.codpro,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hconcepto.codpro,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hconcepto.codpro,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hconcepto.codpro,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_hconcepto.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_hconcepto.codpro, sno_hconcepto.estcla, sno_hconcepto.cueprecon, sno_hpersonalnomina.codded, sno_hpersonalnomina.codtipper ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A y D, que no se integran directamente con presupuesto
		// entonces las buscamos según la estructura de la unidad administrativa a la que pertenece el personal
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_hunidadadmin.codprouniadm as programatica, sno_hunidadadmin.estcla, sno_hconcepto.cueprecon, ".
				"		MAX(spg_cuentas.denominacion) AS denominacion, sum(sno_hsalida.valsal) as total, sno_hpersonalnomina.codded, sno_hpersonalnomina.codtipper, COUNT(sno_hpersonalnomina.codper) AS totalpersonal, ".
				"		MAX(sno_dedicacion.desded) AS desded, MAX(sno_tipopersonal.destipper) AS destipper  ".
				"  FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto, spg_cuentas, sno_dedicacion, sno_tipopersonal ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'D' OR sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2') ".
				"   AND sno_hconcepto.sigcon = 'E'".
				"   AND sno_hsalida.valsal <> 0".
				"   AND sno_hconcepto.intprocon = '0'".
				"   AND sno_hconcepto.conprocon = '0' ".
				"   AND spg_cuentas.status = 'C'".
				$ls_criterio.
				"   AND sno_hpersonalnomina.codemp = sno_dedicacion.codemp ".
				"   AND sno_hpersonalnomina.codded = sno_dedicacion.codded ".
				"   AND sno_hpersonalnomina.codemp = sno_tipopersonal.codemp ".
				"   AND sno_hpersonalnomina.codded = sno_tipopersonal.codded ".
				"   AND sno_hpersonalnomina.codtipper = sno_tipopersonal.codtipper ".
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
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprecon ".
				"   AND sno_hunidadadmin.codemp = spg_cuentas.codemp ".
				"   AND substr(sno_hunidadadmin.codprouniadm,1,25) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_hunidadadmin.estcla = spg_cuentas.estcla ".
			    " GROUP BY sno_hunidadadmin.codprouniadm, sno_hunidadadmin.estcla, sno_hconcepto.cueprecon, sno_hpersonalnomina.codded, sno_hpersonalnomina.codtipper ".
				" ORDER BY programatica, cueprecon";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_contableconceptos_especifico_presupuesto_proyecto ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);
			}
			$this->io_sql->free_result($rs_data);
		}		
		if($lb_valido)
		{
			$lb_valido=$this->uf_contableconceptos_especifico_presupuesto_proyecto_dt($ls_criterio);
			$this->DS->group_by(array('0'=>'programatica','1'=>'cueprecon','2'=>'codded','3'=>'codtipper'),array('0'=>'total'),array('0'=>'programatica','1'=>'cueprecon','2'=>'codded','3'=>'codtipper'));		
			$this->DS->sortData('programatica');
		}
		return $lb_valido;
	}// end function uf_contableconceptos_especifico_presupuesto_proyecto
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_contableconceptos_especifico_presupuesto_proyecto_dt($as_criterio)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_contableconceptos_presupuesto_proyecto_dt
		//         Access: public (desde la clase sigesp_sno_r_contableconceptos)  
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de las cuentas presupuestarias que afectan los conceptos de tipo A, D, P1
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 19/07/2007 								Fecha Última Modificación :  
		// SE MODIFICARON LAS TABLAS TH POR H
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		switch($_SESSION["ls_gestor"])
		{
			case "MYSQLT":
				$ls_cadena=" ROUND((SUM(sno_hsalida.valsal)*MAX(sno_hproyectopersonal.pordiames)),3) ";
				break;
			case "POSTGRES":
				$ls_cadena=" ROUND(CAST((sum(sno_hsalida.valsal)*MAX(sno_hproyectopersonal.pordiames)) AS NUMERIC),3) ";
				break;					
			case "INFORMIX":
				$ls_cadena=" ROUND(CAST((sum(sno_hsalida.valsal)*MAX(sno_hproyectopersonal.pordiames)) AS FLOAT),3) ";
				break;					
		}
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A y D, que se integran directamente con presupuesto
		$ls_sql="SELECT sno_hproyectopersonal.codper, sno_hproyectopersonal.codproy, sno_hproyecto.estproproy, sno_hproyecto.estcla, spg_cuentas.spg_cuenta,".
				"		".$ls_cadena." as montoparcial, ".$ls_cadena." AS total, MAX(spg_cuentas.denominacion) AS denominacion, ".
				"       MAX(sno_hproyectopersonal.pordiames) AS pordiames, sno_hpersonalnomina.codded, sno_hpersonalnomina.codtipper, COUNT(sno_hpersonalnomina.codper) AS totalpersonal, ".
				"		MAX(sno_dedicacion.desded) AS desded, MAX(sno_tipopersonal.destipper) AS destipper  ".
				"  FROM sno_hproyectopersonal, sno_hproyecto, sno_hsalida, sno_hconcepto, spg_cuentas, sno_hpersonalnomina, sno_dedicacion, sno_tipopersonal ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1') ".
				"   AND sno_hsalida.valsal <> 0".
				"   AND sno_hconcepto.conprocon = '1' ".
				"   AND spg_cuentas.status = 'C'".
				$as_criterio.
				"   AND sno_hproyectopersonal.codemp = sno_hpersonalnomina.codemp ".
				"   AND sno_hproyectopersonal.codnom = sno_hpersonalnomina.codnom ".
				"   AND sno_hproyectopersonal.anocur = sno_hpersonalnomina.anocur ".
				"   AND sno_hproyectopersonal.codperi = sno_hpersonalnomina.codperi ".
				"   AND sno_hproyectopersonal.codper = sno_hpersonalnomina.codper ".
				"   AND sno_hpersonalnomina.codemp = sno_dedicacion.codemp ".
				"   AND sno_hpersonalnomina.codded = sno_dedicacion.codded ".
				"   AND sno_hpersonalnomina.codemp = sno_tipopersonal.codemp ".
				"   AND sno_hpersonalnomina.codded = sno_tipopersonal.codded ".
				"   AND sno_hpersonalnomina.codtipper = sno_tipopersonal.codtipper ".
				"   AND sno_hproyectopersonal.codemp = sno_hsalida.codemp ".
				"   AND sno_hproyectopersonal.codnom = sno_hsalida.codnom ".
				"   AND sno_hproyectopersonal.anocur = sno_hsalida.anocur ".
				"   AND sno_hproyectopersonal.codperi = sno_hsalida.codperi ".
				"   AND sno_hproyectopersonal.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hproyectopersonal.codemp = sno_hproyecto.codemp ".
				"   AND sno_hproyectopersonal.codnom = sno_hproyecto.codnom ".
				"   AND sno_hproyectopersonal.anocur = sno_hproyecto.anocur ".
				"   AND sno_hproyectopersonal.codperi = sno_hproyecto.codperi ".
				"   AND sno_hproyectopersonal.codproy = sno_hproyecto.codproy ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprecon ".
				"   AND substr(sno_hproyecto.estproproy,1,25) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hproyecto.estproproy,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hproyecto.estproproy,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hproyecto.estproproy,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hproyecto.estproproy,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_hproyecto.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_hproyectopersonal.codper, sno_hproyectopersonal.codproy, sno_hproyecto.estproproy, sno_hproyecto.estcla, spg_cuentas.spg_cuenta, sno_hpersonalnomina.codded, sno_hpersonalnomina.codtipper  ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A y D, que se integran directamente con presupuesto
		$ls_sql=$ls_sql." UNION ".
		$ls_sql="SELECT sno_hproyectopersonal.codper, sno_hproyectopersonal.codproy, sno_hproyecto.estproproy, sno_hproyecto.estcla, spg_cuentas.spg_cuenta,".
				"		".$ls_cadena." as montoparcial, ".$ls_cadena." AS total, MAX(spg_cuentas.denominacion) AS denominacion, ".
				"       MAX(sno_hproyectopersonal.pordiames) As pordiames, sno_hpersonalnomina.codded, sno_hpersonalnomina.codtipper, COUNT(sno_hpersonalnomina.codper) AS totalpersonal, ".
				"		MAX(sno_dedicacion.desded) AS desded, MAX(sno_tipopersonal.destipper) AS destipper  ".
				"  FROM sno_hproyectopersonal, sno_hproyecto, sno_hsalida, sno_hconcepto, spg_cuentas, sno_hpersonalnomina, sno_dedicacion, sno_tipopersonal  ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'D' OR sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2') ".
				"   AND sno_hconcepto.sigcon = 'E'".
				"   AND sno_hsalida.valsal <> 0".
				"   AND sno_hconcepto.conprocon = '1' ".
				"   AND spg_cuentas.status = 'C' ".
				$as_criterio.
				"   AND sno_hproyectopersonal.codemp = sno_hpersonalnomina.codemp ".
				"   AND sno_hproyectopersonal.codnom = sno_hpersonalnomina.codnom ".
				"   AND sno_hproyectopersonal.anocur = sno_hpersonalnomina.anocur ".
				"   AND sno_hproyectopersonal.codperi = sno_hpersonalnomina.codperi ".
				"   AND sno_hproyectopersonal.codper = sno_hpersonalnomina.codper ".
				"   AND sno_hpersonalnomina.codemp = sno_dedicacion.codemp ".
				"   AND sno_hpersonalnomina.codded = sno_dedicacion.codded ".
				"   AND sno_hpersonalnomina.codemp = sno_tipopersonal.codemp ".
				"   AND sno_hpersonalnomina.codded = sno_tipopersonal.codded ".
				"   AND sno_hpersonalnomina.codtipper = sno_tipopersonal.codtipper ".
				"   AND sno_hproyectopersonal.codemp = sno_hsalida.codemp ".
				"   AND sno_hproyectopersonal.codnom = sno_hsalida.codnom ".
				"   AND sno_hproyectopersonal.anocur = sno_hsalida.anocur ".
				"   AND sno_hproyectopersonal.codperi = sno_hsalida.codperi ".
				"   AND sno_hproyectopersonal.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hproyectopersonal.codemp = sno_hproyecto.codemp ".
				"   AND sno_hproyectopersonal.codnom = sno_hproyecto.codnom ".
				"   AND sno_hproyectopersonal.anocur = sno_hproyecto.anocur ".
				"   AND sno_hproyectopersonal.codperi = sno_hproyecto.codperi ".
				"   AND sno_hproyectopersonal.codproy = sno_hproyecto.codproy ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprecon ".
				"   AND substr(sno_hproyecto.estproproy,1,25) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hproyecto.estproproy,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hproyecto.estproproy,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hproyecto.estproproy,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hproyecto.estproproy,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_hproyecto.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_hproyectopersonal.codper, sno_hproyectopersonal.codproy, sno_hproyecto.estproproy, sno_hproyecto.estcla, spg_cuentas.spg_cuenta, sno_hpersonalnomina.codded, sno_hpersonalnomina.codtipper  ".
				" ORDER BY codper, spg_cuenta, codproy ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_contableconceptos_presupuesto_proyecto_dt ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			$ls_codant="";
			$li_acumulado=0;
			$li_totalant=0;
			$ls_programaticaant="";
			$ls_estclaproyant="";
			$ls_cuentaant="";
			$ls_denominacionant="";
			$ls_coddedant="";
			$ls_codtipperant="";
			$ls_desdedant="";
			$ls_destipperant="";
			$li_totalpersonalant=0;
			while(!$rs_data->EOF)
			{
				$ls_codper=$rs_data->fields["codper"];
				$ls_codded=$rs_data->fields["codded"];
				$ls_codtipper=$rs_data->fields["codtipper"];
				$ls_desded=$rs_data->fields["desded"];
				$ls_destipper=$rs_data->fields["destipper"];
				$li_totalpersonal=$rs_data->fields["totalpersonal"];
				$li_montoparcial=round($rs_data->fields["montoparcial"],3);
				$li_total=round($rs_data->fields["total"],3);
				$ls_estproproy=$rs_data->fields["estproproy"];
				$ls_estclaproy=$rs_data->fields["estcla"];
				$ls_spgcuenta=$rs_data->fields["spg_cuenta"];
				$ls_denominacion=$rs_data->fields["denominacion"];
				$li_pordiames=$rs_data->fields["pordiames"];
				if(($ls_codper!=$ls_codant)||($ls_spgcuenta!=$ls_cuentaant))
				{
					if($li_acumulado!=0)
					{
						if((round($li_acumulado,3)!=round($li_totalant,3))&&($li_pordiamesant<1))
						{
							$li_montoparcial=round(($li_totalant-$li_acumulado),3);
							$this->DS->insertRow("programatica",$ls_programaticaant);
							$this->DS->insertRow("estcla",$ls_estclaproyant);
							$this->DS->insertRow("cueprecon",$ls_cuentaant);
							$this->DS->insertRow("total",$li_montoparcial);
							$this->DS->insertRow("denominacion",$ls_denominacionant);
							$this->DS->insertRow("codded",$ls_coddedant);
							$this->DS->insertRow("codtipper",$ls_codtipperant);
							$this->DS->insertRow("desded",$ls_desdedant);
							$this->DS->insertRow("destipper",$ls_destipperant);
							$this->DS->insertRow("totalpersonal",$li_totalpersonalant);
						}
					}
					$li_acumulado=$rs_data->fields["montoparcial"];
					$li_montoparcial=round($rs_data->fields["montoparcial"],3);
					$ls_programaticaant=$ls_estproproy;
					$ls_estclaproyant=$ls_estclaproy;
					$ls_cuentaant=$ls_spgcuenta;
					$li_totalant=$li_total;
					$ls_codant=$ls_codper;
					$ls_denominacionant=$ls_denominacion;
					$li_pordiamesant=$li_pordiames;
					$ls_coddedant=$ls_codded;
					$ls_codtipperant=$ls_codtipper;
					$ls_desdedant=$ls_desded;
					$ls_destipperant=$ls_destipper;
					$li_totalpersonalant=$li_totalpersonal;
				}
				else
				{
					$li_acumulado=$li_acumulado+$li_montoparcial;
					$ls_programaticaant=$ls_estproproy;
					$ls_estclaproyant=$ls_estclaproy;
					$ls_cuentaant=$ls_spgcuenta;
					$li_totalant=$li_total;
					$ls_denominacionant=$ls_denominacion;
					$ls_coddedant=$ls_codded;
					$ls_codtipperant=$ls_codtipper;
					$ls_desdedant=$ls_desded;
					$ls_destipperant=$ls_destipper;
					$li_totalpersonalant=$li_totalpersonal;
				}
				$this->DS->insertRow("programatica",$ls_estproproy);
				$this->DS->insertRow("estcla",$ls_estclaproy);
				$this->DS->insertRow("cueprecon",$ls_spgcuenta);
				$this->DS->insertRow("total",$li_montoparcial);
				$this->DS->insertRow("denominacion",$ls_denominacion);
				$this->DS->insertRow("codded",$ls_codded);
				$this->DS->insertRow("codtipper",$ls_codtipper);
				$this->DS->insertRow("desded",$ls_desded);
				$this->DS->insertRow("destipper",$ls_destipper);
				$this->DS->insertRow("totalpersonal",$li_totalpersonal);
				$rs_data->MoveNext();
			}
			if((number_format($li_acumulado,3,".","")!=number_format($li_totalant,3,".",""))&&($li_pordiamesant<1))
			{
				$li_montoparcial=round(($li_totalant-$li_acumulado),3);
				$this->DS->insertRow("programatica",$ls_programaticaant);
				$this->DS->insertRow("estcla",$ls_estclaproyant);
				$this->DS->insertRow("cueprecon",$ls_cuentaant);
				$this->DS->insertRow("total",$li_montoparcial);
				$this->DS->insertRow("denominacion",$ls_denominacionant);
				$this->DS->insertRow("codded",$ls_coddedant);
				$this->DS->insertRow("codtipper",$ls_codtipperant);
				$this->DS->insertRow("desded",$ls_desdedant);
				$this->DS->insertRow("destipper",$ls_destipperant);
				$this->DS->insertRow("totalpersonal",$li_totalpersonalant);
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_contableconceptos_especifico_presupuesto_proyecto_dt
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_contableaportes_especifico_presupuesto($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,$as_estcla,
													   $as_subnomdes,$as_subnomhas)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_contableaportes_presupuesto
		//         Access: public (desde la clase sigesp_sno_r_contableaportes)  
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de las cuentas presupuestarias que afectan los conceptos de tipo P2
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 11/05/2006 								Fecha Última Modificación :  
		// SE MODIFICARON LAS TABLAS TH POR H
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		if(!empty($as_estcla))
		{
			$ls_criterio="   AND spg_cuentas.estcla = '".$as_estcla."'".
						 "   AND spg_cuentas.codestpro1 = '".str_pad($as_codestpro1,25,"0","0")."'".
						 "   AND spg_cuentas.codestpro2 = '".str_pad($as_codestpro2,25,"0","0")."'".
						 "   AND spg_cuentas.codestpro3 = '".str_pad($as_codestpro3,25,"0","0")."'".
						 "   AND spg_cuentas.codestpro4 = '".str_pad($as_codestpro4,25,"0","0")."'".
						 "   AND spg_cuentas.codestpro5 = '".str_pad($as_codestpro5,25,"0","0")."'";
		}
		if(!empty($as_subnomdes))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codsubnom>='".$as_subnomdes."'";
		}
		if(!empty($as_subnomhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codsubnom<='".$as_subnomhas."'";
		}
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos que se integran directamente con presupuesto
		$ls_sql="SELECT sno_hconcepto.codpro as programatica, sno_hconcepto.estcla, spg_cuentas.spg_cuenta AS cueprepatcon, ".
				"		MAX(spg_cuentas.denominacion) AS denominacion, sum(sno_hsalida.valsal) as total, sno_hpersonalnomina.codded, sno_hpersonalnomina.codtipper, COUNT(sno_hpersonalnomina.codper) AS totalpersonal, ".
				"		MAX(sno_dedicacion.desded) AS desded, MAX(sno_tipopersonal.destipper) AS destipper  ".
				"  FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, spg_cuentas, sno_dedicacion, sno_tipopersonal ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'P2' OR sno_hsalida.tipsal = 'V4' OR sno_hsalida.tipsal = 'W4')".
				"   AND sno_hconcepto.intprocon = '1'".
				"   AND spg_cuentas.status = 'C' ".
				"   AND sno_hsalida.valsal <> 0 ".
				$ls_criterio.
				"   AND sno_hpersonalnomina.codemp = sno_dedicacion.codemp ".
				"   AND sno_hpersonalnomina.codded = sno_dedicacion.codded ".
				"   AND sno_hpersonalnomina.codemp = sno_tipopersonal.codemp ".
				"   AND sno_hpersonalnomina.codded = sno_tipopersonal.codded ".
				"   AND sno_hpersonalnomina.codtipper = sno_tipopersonal.codtipper ".
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
				"   AND sno_hconcepto.codemp = spg_cuentas.codemp ".
				"   AND sno_hconcepto.cueprepatcon = spg_cuentas.spg_cuenta ".
				"   AND substr(sno_hconcepto.codpro,1,25) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hconcepto.codpro,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hconcepto.codpro,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hconcepto.codpro,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hconcepto.codpro,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_hconcepto.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_hconcepto.codpro, sno_hconcepto.estcla, spg_cuentas.spg_cuenta, sno_hpersonalnomina.codded, sno_hpersonalnomina.codtipper   ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos que no se integran directamente con presupuesto
		// entonces las buscamos según la estructura de la unidad administrativa a la que pertenece el personal
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_hunidadadmin.codprouniadm as programatica,sno_hunidadadmin.estcla, spg_cuentas.spg_cuenta AS cueprepatcon, ".
				"		MAX(spg_cuentas.denominacion) AS denominacion, sum(sno_hsalida.valsal) as total, sno_hpersonalnomina.codded, sno_hpersonalnomina.codtipper, COUNT(sno_hpersonalnomina.codper) AS totalpersonal, ".
				"		MAX(sno_dedicacion.desded) AS desded, MAX(sno_tipopersonal.destipper) AS destipper  ".
				"  FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto, spg_cuentas, sno_dedicacion, sno_tipopersonal  ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'P2' OR sno_hsalida.tipsal = 'V4' OR sno_hsalida.tipsal = 'W4')".
				"   AND sno_hconcepto.intprocon = '0'".
				"   AND spg_cuentas.status = 'C' ".
				"   AND sno_hsalida.valsal <> 0 ".
				$ls_criterio.
				"   AND sno_hpersonalnomina.codemp = sno_dedicacion.codemp ".
				"   AND sno_hpersonalnomina.codded = sno_dedicacion.codded ".
				"   AND sno_hpersonalnomina.codemp = sno_tipopersonal.codemp ".
				"   AND sno_hpersonalnomina.codded = sno_tipopersonal.codded ".
				"   AND sno_hpersonalnomina.codtipper = sno_tipopersonal.codtipper ".
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
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprepatcon ".
				"   AND sno_hunidadadmin.codemp = spg_cuentas.codemp ".
				"   AND substr(sno_hunidadadmin.codprouniadm,1,25) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_hunidadadmin.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_hunidadadmin.codprouniadm, sno_hunidadadmin.estcla, spg_cuentas.spg_cuenta, sno_hpersonalnomina.codded, sno_hpersonalnomina.codtipper   ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_contableaportes_presupuesto ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_contableaportes_especifico_presupuesto
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_contableaportes_especifico_presupuesto_proyecto($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,
																$as_estcla,$as_subnomdes,$as_subnomhas)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_contableaportes_presupuesto_proyecto
		//         Access: public (desde la clase sigesp_sno_r_contableaportes)  
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de las cuentas presupuestarias que afectan los conceptos de tipo P2
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 19/07/2007 								Fecha Última Modificación :  
		// SE MODIFICARON LAS TABLAS TH POR H
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos que se integran directamente con presupuesto
		$ls_criterio="";
		if(!empty($as_estcla))
		{
			$ls_criterio="   AND spg_cuentas.estcla = '".$as_estcla."'".
						 "   AND spg_cuentas.codestpro1 = '".str_pad($as_codestpro1,25,"0","0")."'".
						 "   AND spg_cuentas.codestpro2 = '".str_pad($as_codestpro2,25,"0","0")."'".
						 "   AND spg_cuentas.codestpro3 = '".str_pad($as_codestpro3,25,"0","0")."'".
						 "   AND spg_cuentas.codestpro4 = '".str_pad($as_codestpro4,25,"0","0")."'".
						 "   AND spg_cuentas.codestpro5 = '".str_pad($as_codestpro5,25,"0","0")."'";
		}
		if(!empty($as_subnomdes))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codsubnom>='".$as_subnomdes."'";
		}
		if(!empty($as_subnomhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codsubnom<='".$as_subnomhas."'";
		}
		$ls_sql="SELECT sno_hconcepto.codpro as programatica, sno_hconcepto.estcla, spg_cuentas.spg_cuenta AS cueprepatcon, ".
				"		MAX(spg_cuentas.denominacion) AS denominacion, sum(sno_hsalida.valsal) as total, sno_hpersonalnomina.codded,  ".
				"		sno_hpersonalnomina.codtipper, COUNT(sno_hpersonalnomina.codper) AS totalpersonal, MAX(sno_dedicacion.desded) AS desded, ".
				"       MAX(sno_tipopersonal.destipper) AS destipper  ".
				"  FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, spg_cuentas, sno_dedicacion, sno_tipopersonal  ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'P2' OR sno_hsalida.tipsal = 'V4' OR sno_hsalida.tipsal = 'W4')".
				"   AND sno_hconcepto.intprocon = '1'".
				"   AND sno_hconcepto.conprocon = '0' ".
				"   AND spg_cuentas.status = 'C' ".
				"   AND sno_hsalida.valsal <> 0 ".
				$ls_criterio.
				"   AND sno_hpersonalnomina.codemp = sno_dedicacion.codemp ".
				"   AND sno_hpersonalnomina.codded = sno_dedicacion.codded ".
				"   AND sno_hpersonalnomina.codemp = sno_tipopersonal.codemp ".
				"   AND sno_hpersonalnomina.codded = sno_tipopersonal.codded ".
				"   AND sno_hpersonalnomina.codtipper = sno_tipopersonal.codtipper ".
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
				"   AND sno_hconcepto.codemp = spg_cuentas.codemp ".
				"   AND sno_hconcepto.cueprepatcon = spg_cuentas.spg_cuenta ".
				"   AND substr(sno_hconcepto.codpro,1,25) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hconcepto.codpro,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hconcepto.codpro,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hconcepto.codpro,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hconcepto.codpro,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_hconcepto.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_hconcepto.codpro, sno_hconcepto.estcla, spg_cuentas.spg_cuenta, sno_hpersonalnomina.codded, sno_hpersonalnomina.codtipper ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos que no se integran directamente con presupuesto
		// entonces las buscamos según la estructura de la unidad administrativa a la que pertenece el personal
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_hunidadadmin.codprouniadm as programatica, sno_hunidadadmin.estcla, spg_cuentas.spg_cuenta AS cueprepatcon, ".
				"		MAX(spg_cuentas.denominacion) AS denominacion, sum(sno_hsalida.valsal) as total, sno_hpersonalnomina.codded,  ".
				"		sno_hpersonalnomina.codtipper, COUNT(sno_hpersonalnomina.codper) AS totalpersonal, MAX(sno_dedicacion.desded) AS desded, ".
				"       MAX(sno_tipopersonal.destipper) AS destipper  ".
				"  FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto, spg_cuentas, sno_dedicacion, sno_tipopersonal    ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'P2' OR sno_hsalida.tipsal = 'V4' OR sno_hsalida.tipsal = 'W4')".
				"   AND sno_hconcepto.intprocon = '0'".
				"   AND sno_hconcepto.conprocon = '0' ".
				"   AND spg_cuentas.status = 'C' ".
				"   AND sno_hsalida.valsal <> 0 ".
				$ls_criterio.
				"   AND sno_hpersonalnomina.codemp = sno_dedicacion.codemp ".
				"   AND sno_hpersonalnomina.codded = sno_dedicacion.codded ".
				"   AND sno_hpersonalnomina.codemp = sno_tipopersonal.codemp ".
				"   AND sno_hpersonalnomina.codded = sno_tipopersonal.codded ".
				"   AND sno_hpersonalnomina.codtipper = sno_tipopersonal.codtipper ".
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
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprepatcon ".
				"   AND sno_hunidadadmin.codemp = spg_cuentas.codemp ".
				"   AND substr(sno_hunidadadmin.codprouniadm,1,25) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hunidadadmin.codprouniadm,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_hunidadadmin.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_hunidadadmin.codprouniadm, sno_hunidadadmin.estcla, spg_cuentas.spg_cuenta, sno_hpersonalnomina.codded, sno_hpersonalnomina.codtipper ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_contableaportes_especifico_presupuesto_proyecto ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);
			}
			$this->io_sql->free_result($rs_data);
		}		
		if($lb_valido)
		{
			$lb_valido=$this->uf_contableaportes_especifico_presupuesto_proyecto_dt($ls_criterio);
			$this->DS->group_by(array('0'=>'programatica','1'=>'cueprepatcon','2'=>'codded','3'=>'codtipper'),array('0'=>'total'),array('0'=>'programatica','1'=>'cueprepatcon','2'=>'codded','3'=>'codtipper'));		
			$this->DS->sortData('programatica');
		}
		return $lb_valido;
	}// end function uf_contableaportes_especifico_presupuesto_proyecto
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_contableaportes_especifico_presupuesto_proyecto_dt($as_criterio)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_contableaportes_presupuesto_proyecto_dt
		//         Access: public (desde la clase sigesp_sno_r_contableaportes)  
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de las cuentas presupuestarias que afectan los conceptos de tipo P2
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 19/07/2007 								Fecha Última Modificación :  
		// SE MODIFICARON LAS TABLAS TH POR H
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		switch($_SESSION["ls_gestor"])
		{
			case "MYSQLT":
				$ls_cadena=" ROUND((SUM(sno_hsalida.valsal)*MAX(sno_hproyectopersonal.pordiames)),3) ";
				break;
			case "POSTGRES":
				$ls_cadena=" ROUND(CAST((sum(sno_hsalida.valsal)*MAX(sno_hproyectopersonal.pordiames)) AS NUMERIC),3) ";
				break;					
			case "INFORMIX":
				$ls_cadena=" ROUND(CAST((sum(sno_hsalida.valsal)*MAX(sno_hproyectopersonal.pordiames)) AS FLOAT),3) ";
				break;					
		}
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos que se integran directamente con presupuesto
		$ls_sql="SELECT MAX(sno_hproyecto.estproproy) AS estproproy,sno_hproyecto.estcla, MAX(spg_cuentas.spg_cuenta) AS spg_cuenta, ".
				"		".$ls_cadena." AS total, MAX(sno_hconcepto.codprov) AS codprov, ".$ls_cadena." AS montoparcial, ".
				"		MAX(sno_hconcepto.cedben) AS cedben, sno_hconcepto.codconc, sno_hproyecto.codproy, sno_hproyectopersonal.codper, ".
				"		MAX(spg_cuentas.denominacion) AS denominacion, MAX(sno_hproyectopersonal.pordiames) AS pordiames, sno_hpersonalnomina.codded, ".
				"		sno_hpersonalnomina.codtipper, COUNT(sno_hpersonalnomina.codper) AS totalpersonal, ".
				"		MAX(sno_dedicacion.desded) AS desded, MAX(sno_tipopersonal.destipper) AS destipper  ".
				"  FROM sno_hproyectopersonal, sno_hproyecto, sno_hsalida, sno_hconcepto, spg_cuentas, sno_hpersonalnomina, sno_dedicacion, sno_tipopersonal  ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'P2' OR sno_hsalida.tipsal = 'V4' OR sno_hsalida.tipsal = 'W4')".
				"   AND sno_hconcepto.conprocon = '1' ".
				"   AND spg_cuentas.status = 'C' ".
				"   AND sno_hsalida.valsal <> 0 ".
				$as_criterio.
				"   AND sno_hproyectopersonal.codemp = sno_hpersonalnomina.codemp ".
				"   AND sno_hproyectopersonal.codnom = sno_hpersonalnomina.codnom ".
				"   AND sno_hproyectopersonal.anocur = sno_hpersonalnomina.anocur ".
				"   AND sno_hproyectopersonal.codperi = sno_hpersonalnomina.codperi ".
				"   AND sno_hproyectopersonal.codper = sno_hpersonalnomina.codper ".
				"   AND sno_hpersonalnomina.codemp = sno_dedicacion.codemp ".
				"   AND sno_hpersonalnomina.codded = sno_dedicacion.codded ".
				"   AND sno_hpersonalnomina.codemp = sno_tipopersonal.codemp ".
				"   AND sno_hpersonalnomina.codded = sno_tipopersonal.codded ".
				"   AND sno_hpersonalnomina.codtipper = sno_tipopersonal.codtipper ".
				"   AND sno_hproyectopersonal.codemp = sno_hsalida.codemp ".
				"   AND sno_hproyectopersonal.codnom = sno_hsalida.codnom ".
				"   AND sno_hproyectopersonal.anocur = sno_hsalida.anocur ".
				"   AND sno_hproyectopersonal.codperi = sno_hsalida.codperi ".
				"   AND sno_hproyectopersonal.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hproyectopersonal.codemp = sno_hproyecto.codemp ".
				"   AND sno_hproyectopersonal.codnom = sno_hproyecto.codnom ".
				"   AND sno_hproyectopersonal.anocur = sno_hproyecto.anocur ".
				"   AND sno_hproyectopersonal.codperi = sno_hproyecto.codperi ".
				"   AND sno_hproyectopersonal.codproy = sno_hproyecto.codproy ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprepatcon ".
				"   AND substr(sno_hproyecto.estproproy,1,25) = spg_cuentas.codestpro1 ".
				"   AND substr(sno_hproyecto.estproproy,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(sno_hproyecto.estproproy,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(sno_hproyecto.estproproy,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(sno_hproyecto.estproproy,101,25) = spg_cuentas.codestpro5 ".
				"   AND sno_hproyecto.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_hproyectopersonal.codper, sno_hproyecto.codproy, spg_cuentas.spg_cuenta, sno_hconcepto.codconc, sno_hpersonalnomina.codded, sno_hpersonalnomina.codtipper ".
				" ORDER BY sno_hproyectopersonal.codper, sno_hproyecto.codproy, spg_cuentas.spg_cuenta, sno_hconcepto.codconc, sno_hpersonalnomina.codded, sno_hpersonalnomina.codtipper ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_contableaportes_especifico_presupuesto_proyecto_dt ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			$ls_codant="";
			$li_acumulado=0;
			$li_totalant=0;
			$ls_programaticaant="";
			$ls_estclaproyant="";
			$ls_cuentaant="";
			$ls_denominacionant="";
			$ls_coddedant="";
			$ls_codtipperant="";
			$ls_desdedant="";
			$ls_destipperant="";
			$li_totalpersonalant=0;
			while(!$rs_data->EOF)
			{
				$ls_codper=$rs_data->fields["codper"];
				$ls_codded=$rs_data->fields["codded"];
				$ls_codtipper=$rs_data->fields["codtipper"];
				$ls_desded=$rs_data->fields["desded"];
				$ls_destipper=$rs_data->fields["destipper"];
				$li_totalpersonal=$rs_data->fields["totalpersonal"];
				$li_montoparcial=$rs_data->fields["montoparcial"];
				$li_total=$rs_data->fields["total"];
				$ls_estproproy=$rs_data->fields["estproproy"];
				$ls_estclaproy=$rs_data->fields["estcla"];
				$ls_spgcuenta=$rs_data->fields["spg_cuenta"];
				$ls_denominacion=$rs_data->fields["denominacion"];
				$li_pordiames=$rs_data->fields["pordiames"];
				if(($ls_codper!=$ls_codant)||($ls_spgcuenta!=$ls_cuentaant))
				{
					if($li_acumulado!=0)
					{
						if((round($li_acumulado,3)!=round($li_totalant,3))&&($li_pordiames<1))
						{
							$li_montoparcial=round(($li_totalant-$li_acumulado),3);
							$this->DS->insertRow("programatica",$ls_programaticaant);
							$this->DS->insertRow("estcla",$ls_estclaproyant);
							$this->DS->insertRow("cueprepatcon",$ls_cuentaant);
							$this->DS->insertRow("total",$li_montoparcial);
							$this->DS->insertRow("denominacion",$ls_denominacionant);
							$this->DS->insertRow("codded",$ls_coddedant);
							$this->DS->insertRow("codtipper",$ls_codtipperant);
							$this->DS->insertRow("desded",$ls_desdedant);
							$this->DS->insertRow("destipper",$ls_destipperant);
							$this->DS->insertRow("totalpersonal",$li_totalpersonalant);
						}
					}
					$li_acumulado=$rs_data->fields["montoparcial"];
					$li_montoparcial=round($rs_data->fields["montoparcial"],3);
					$ls_programaticaant=$ls_estproproy;
					$ls_estclaproyant=$ls_estclaproy;
					$ls_cuentaant=$ls_spgcuenta;
					$ls_codant=$ls_codper;
					$ls_denominacionant=$ls_denominacion;
					$li_pordiamesant=$li_pordiames;
					$li_totalant=$li_total;
					$ls_coddedant=$ls_codded;
					$ls_codtipperant=$ls_codtipper;
					$ls_desdedant=$ls_desded;
					$ls_destipperant=$ls_destipper;
					$li_totalpersonalant=$li_totalpersonal;
				}
				else
				{
					$li_acumulado=$li_acumulado+$li_montoparcial;
					$ls_programaticaant=$ls_estproproy;
					$ls_estclaproyant=$ls_estclaproy;
					$ls_cuentaant=$ls_spgcuenta;
					$li_totalant=$li_total;
					$ls_denominacionant=$ls_denominacion;
					$ls_coddedant=$ls_codded;
					$ls_codtipperant=$ls_codtipper;
					$ls_desdedant=$ls_desded;
					$ls_destipperant=$ls_destipper;
					$li_totalpersonalant=$li_totalpersonal;
				}
				$this->DS->insertRow("programatica",$ls_estproproy);
				$this->DS->insertRow("estcla",$ls_estclaproy);
				$this->DS->insertRow("cueprepatcon",$ls_spgcuenta);
				$this->DS->insertRow("total",$li_montoparcial);
				$this->DS->insertRow("denominacion",$ls_denominacion);
				$this->DS->insertRow("codded",$ls_codded);
				$this->DS->insertRow("codtipper",$ls_codtipper);
				$this->DS->insertRow("desded",$ls_desded);
				$this->DS->insertRow("destipper",$ls_destipper);
				$this->DS->insertRow("totalpersonal",$li_totalpersonal);
				$rs_data->MoveNext();
			}
			if((number_format($li_acumulado,3,".","")!=number_format($li_totalant,3,".",""))&&($li_pordiames<1))
			{
				$li_montoparcial=round(($li_totalant-$li_acumulado),3);
				$this->DS->insertRow("programatica",$ls_programaticaant);
				$this->DS->insertRow("estcla",$ls_estclaproyant);
				$this->DS->insertRow("cueprepatcon",$ls_cuentaant);
				$this->DS->insertRow("total",$li_montoparcial);
				$this->DS->insertRow("denominacion",$ls_denominacionant);
				$this->DS->insertRow("codded",$ls_coddedant);
				$this->DS->insertRow("codtipper",$ls_codtipperant);
				$this->DS->insertRow("desded",$ls_desdedant);
				$this->DS->insertRow("destipper",$ls_destipperant);
				$this->DS->insertRow("totalpersonal",$li_totalpersonalant);
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_contableaportes_especifico_presupuesto_proyecto_dt
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_cuadreconceptoaporte_aportes()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_cuadreconceptoaporte_aportes
		//         Access: public (desde la clase sigesp_sno_r_cuadreconceptoaporte)  
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de las cuentas presupuestarias que afectan los conceptos de tipo P2
		//	   Creado Por: Ing. María Beatriz Unda
		// Fecha Creación: 09/02/2009								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$this->io_sql=new class_sql($this->io_conexion);
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos que se integran directamente con presupuesto
		$ls_sql="SELECT sno_hconcepto.codpro as programatica,sno_hconcepto.estcla, sno_hconcepto.cueprepatcon, sum(sno_hsalida.valsal) as total ".
				"  FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.valsal <> 0 ".
				"   AND (sno_hsalida.tipsal = 'P2' OR sno_hsalida.tipsal = 'V4' OR sno_hsalida.tipsal = 'W4')".
				"   AND sno_hconcepto.intprocon = '1'".
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.codperi=sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.anocur=sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hconcepto.codperi=sno_hsalida.codperi ".
				"   AND sno_hconcepto.anocur=sno_hsalida.anocur ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				" GROUP BY sno_hconcepto.codconc, sno_hconcepto.codpro,sno_hconcepto.estcla, sno_hconcepto.cueprepatcon  ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos que no se integran directamente con presupuesto
		// entonces las buscamos según la estructura de la unidad administrativa a la que pertenece el personal
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_hunidadadmin.codprouniadm as programatica, sno_hunidadadmin.estcla, sno_hconcepto.cueprepatcon, sum(sno_hsalida.valsal) as total".
				"  FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.valsal <> 0 ".
				"   AND (sno_hsalida.tipsal = 'P2' OR sno_hsalida.tipsal = 'V4' OR sno_hsalida.tipsal = 'W4')".
				"   AND sno_hconcepto.intprocon = '0'".
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.codperi=sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.anocur=sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".			
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hconcepto.codperi=sno_hsalida.codperi ".
				"   AND sno_hconcepto.anocur=sno_hsalida.anocur ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
				"   AND sno_hpersonalnomina.codperi=sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.anocur=sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				" GROUP BY sno_hconcepto.codconc, sno_hunidadadmin.codprouniadm,sno_hunidadadmin.estcla, sno_hconcepto.cueprepatcon ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_cuadreconceptoaporte_aportes ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			while(!$rs_data->EOF)
			{
				$ls_programatica=$rs_data->fields["programatica"];
				$ls_estcla=$rs_data->fields["estcla"];
				$ls_cuentapresupuesto=$rs_data->fields["cueprepatcon"];
				$li_total=$rs_data->fields["total"];
				$ls_sql="SELECT denominacion ".
						"  FROM spg_cuentas ".
						" WHERE codemp='".$this->ls_codemp."' ".
						"   AND status = 'C'".
						"   AND codestpro1 = '".substr($ls_programatica,0,25)."'".
						"   AND codestpro2 = '".substr($ls_programatica,25,25)."'".
						"   AND codestpro3 = '".substr($ls_programatica,50,25)."'".
						"   AND codestpro4 = '".substr($ls_programatica,75,25)."'".
						"   AND codestpro5 = '".substr($ls_programatica,100,25)."'".
						"   AND estcla='".$ls_estcla."'".
						"   AND spg_cuenta = '".$ls_cuentapresupuesto."'";
				$rs_data2=$this->io_sql->select($ls_sql);
				if($rs_data2===false)
				{
					$this->io_mensajes->message("CLASE->Report MÉTODO->uf_cuadreconceptoaporte_aportes ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
					$lb_valido=false;
				}
				else
				{
					if(!$row=$this->io_sql->fetch_row($rs_data2))
					{
						$this->DS->insertRow("programatica",$ls_programatica);
						$this->DS->insertRow("cueprepatcon",$ls_cuentapresupuesto);
						$this->DS->insertRow("denominacion","No Existe la cuenta en la Estructura.");
						$this->DS->insertRow("total",$li_total);
					}
					$this->io_sql->free_result($rs_data2);
				}
				$rs_data->MoveNext();
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_cuadreconceptoaporte_aportes
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_cuadreconceptoaporte_conceptos()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_cuadreconceptoaporte_conceptos
		//         Access: public (desde la clase sigesp_sno_r_cuadreconceptoaporte)  
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de las cuentas presupuestarias que afectan los 
		//                 conceptos de tipo A, D, P1
		//	   Creado Por: Ing. María Beatriz Unda
		// Fecha Creación: 09/02/2009 								Fecha Última Modificación :  
		// SE MODIFICARON LAS TABLAS TH POR H
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$this->io_sql=new class_sql($this->io_conexion);
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A y D, que se integran directamente con presupuesto
		$ls_sql="SELECT sno_hconcepto.codpro as programatica,sno_hconcepto.estcla, sno_hconcepto.cueprecon, sum(sno_hsalida.valsal) as total ".
				"  FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1') ".
				"   AND sno_hconcepto.sigcon = 'A' ".
				"   AND sno_hsalida.valsal <> 0".
				"   AND sno_hconcepto.intprocon = '1'".
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.codperi=sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.anocur=sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				" GROUP BY sno_hconcepto.codpro,sno_hconcepto.estcla, sno_hconcepto.cueprecon ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A y D, que no se integran directamente con presupuesto
		// entonces las buscamos según la estructura de la unidad administrativa a la que pertenece el personal
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_hunidadadmin.codprouniadm as programatica, sno_hunidadadmin.estcla, sno_hconcepto.cueprecon, sum(sno_hsalida.valsal) as total ".
				"  FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1') ".
				"   AND sno_hconcepto.sigcon = 'A' ".
				"   AND sno_hsalida.valsal <> 0".
				"   AND sno_hconcepto.intprocon = '0'".
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.codperi=sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.anocur=sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hconcepto.codperi=sno_hsalida.codperi ".
				"   AND sno_hconcepto.anocur=sno_hsalida.anocur ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
				"   AND sno_hpersonalnomina.codperi=sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.anocur=sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				" GROUP BY sno_hunidadadmin.codprouniadm, sno_hunidadadmin.estcla, sno_hconcepto.cueprecon ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos D , que se integran directamente con presupuesto
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_hconcepto.codpro as programatica, sno_hconcepto.estcla, sno_hconcepto.cueprecon, sum(sno_hsalida.valsal) as total ".
				"  FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND (sno_hsalida.tipsal = 'D' OR sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2' OR sno_hsalida.tipsal = 'P1' OR sno_hsalida.tipsal = 'V3' OR sno_hsalida.tipsal = 'W3')".
				"   AND sno_hsalida.valsal <> 0 ".
				"   AND sno_hconcepto.sigcon = 'E' ".
				"   AND sno_hconcepto.intprocon = '1' ".
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.codperi=sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.anocur=sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hconcepto.codperi=sno_hsalida.codperi ".
				"   AND sno_hconcepto.anocur=sno_hsalida.anocur ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				" GROUP BY sno_hconcepto.codpro,sno_hconcepto.estcla, sno_hconcepto.cueprecon ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos  D, que no se integran directamente con presupuesto
		// entonces las buscamos según la estructura de la unidad administrativa a la que pertenece el personal
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_hunidadadmin.codprouniadm as programatica, sno_hunidadadmin.estcla, sno_hconcepto.cueprecon, sum(sno_hsalida.valsal) as total ".
				"  FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND (sno_hsalida.tipsal = 'D' OR sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2' OR sno_hsalida.tipsal = 'P1' OR sno_hsalida.tipsal = 'V3' OR sno_hsalida.tipsal = 'W3')".
				"   AND sno_hsalida.valsal <> 0 ".
				"   AND sno_hconcepto.sigcon = 'E' ".
				"   AND sno_hconcepto.intprocon = '0' ".
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.codperi=sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.anocur=sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
				"   AND sno_hpersonalnomina.codperi=sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.anocur=sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				" GROUP BY sno_hunidadadmin.codprouniadm, sno_hunidadadmin.estcla, sno_hconcepto.cueprecon ".
				" ORDER BY programatica, cueprecon";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_cuadreconceptoaporte_conceptos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			while(!$rs_data->EOF)
			{
				$ls_programatica=$rs_data->fields["programatica"];
				$ls_estcla=$rs_data->fields["estcla"];
				$ls_cuentapresupuesto=$rs_data->fields["cueprecon"];
				$li_total=$rs_data->fields["total"];
				$ls_sql="SELECT denominacion ".
						"  FROM spg_cuentas ".
						" WHERE codemp='".$this->ls_codemp."' ".
						"   AND status = 'C'".
						"   AND codestpro1 = '".substr($ls_programatica,0,25)."'".
						"   AND codestpro2 = '".substr($ls_programatica,25,25)."'".
						"   AND codestpro3 = '".substr($ls_programatica,50,25)."'".
						"   AND codestpro4 = '".substr($ls_programatica,75,25)."'".
						"   AND codestpro5 = '".substr($ls_programatica,100,25)."'".
						"   AND estcla = '".$ls_estcla."'".
						"   AND spg_cuenta = '".$ls_cuentapresupuesto."'";
				$rs_data2=$this->io_sql->select($ls_sql);
				if($rs_data2===false)
				{
					$this->io_mensajes->message("CLASE->Report MÉTODO->uf_cuadreconceptoaporte_conceptos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
					$lb_valido=false;
				}
				else
				{
					if(!$row=$this->io_sql->fetch_row($rs_data2))
					{
						$this->DS_detalle->insertRow("programatica",$ls_programatica);
						$this->DS_detalle->insertRow("cueprecon",$ls_cuentapresupuesto);
						$this->DS_detalle->insertRow("denominacion","No Existe la cuenta en la Estructura.");
						$this->DS_detalle->insertRow("total",$li_total);
					}
					$this->io_sql->free_result($rs_data2);
				}
				$rs_data->MoveNext();
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_cuadreconceptoaporte_conceptos
	//-----------------------------------------------------------------------------------------------------------------------------------

}
?>
