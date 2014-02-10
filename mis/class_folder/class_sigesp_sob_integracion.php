<?php
  ////////////////////////////////////////////////////////////////////////////////////////////////////////
  //       Class : class_sigesp_sob_integracion_php                                                     //    
  // Description : Esta clase tiene todos los metodos necesario para el manejo de la rutina integradora //
  //               con el sistema de presupuesto de  gasto y el sistema de obras.                       //               
  ////////////////////////////////////////////////////////////////////////////////////////////////////////
class class_sigesp_sob_integracion
{
	var $sqlca;   
    var $is_msg_error;
	var $dts_empresa; 
    var $dts_data_contrato;
	var $dts_data;
	var $obj="";
	var $io_sql;
	var $io_siginc;
	var $io_conect;
	var $io_function;	
    var $io_sigesp_int;
	var $io_fecha;
	var $io_msg;
	var $io_codemp;
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function class_sigesp_sob_integracion()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: class_sigesp_sob_integracion
		//		   Access: public 
		//	  Description: Constructor de la Clase
		//	   Creado Por: Ing. Wilmer Briceño
		// Modificado Por: Ing. Yesenia Moreno								Fecha Última Modificación : 25/04/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		require_once("../shared/class_folder/class_sql.php");  
		require_once("../shared/class_folder/class_datastore.php");
		require_once("../shared/class_folder/sigesp_include.php");
		require_once("../shared/class_folder/class_funciones.php");
		require_once("../shared/class_folder/class_sigesp_int.php");
		require_once("../shared/class_folder/class_sigesp_int_int.php");
		require_once("../shared/class_folder/class_sigesp_int_spg.php");
		require_once("../shared/class_folder/class_sigesp_int_scg.php");
		require_once("../shared/class_folder/class_sigesp_int_spi.php");
		require_once("../shared/class_folder/class_fecha.php");
		require_once("../shared/class_folder/class_mensajes.php");
		require_once("../shared/class_folder/sigesp_c_seguridad.php");
		require_once("class_funciones_mis.php");
	    $this->io_fun_mis=new class_funciones_mis();
	    $this->io_fecha= new class_fecha();
        $this->io_sigesp_int=new class_sigesp_int_int();
		$this->io_sigesp_int_spg=new class_sigesp_int_spg();
		$this->io_sigesp_int_scg=new class_sigesp_int_scg();
		$this->io_function= new class_funciones() ;
		$this->io_siginc= new sigesp_include();
		$this->io_connect=$this->io_siginc->uf_conectar();
		$this->io_sql=new class_sql($this->io_connect);		
		$this->obj=new class_datastore();
		$this->dts_empresa=$_SESSION["la_empresa"];
		$this->io_codemp=$this->dts_empresa["codemp"];		
		$this->dts_data=new class_datastore();
        $this->dts_data_contrato=new class_datastore();		
        $this->dts_data_variacion=new class_datastore();
		$this->io_ds_spgcuentas=new class_datastore();
		$this->io_ds_scgcuentas=new class_datastore();
		$this->io_msg=new class_mensajes();		
		$this->io_seguridad=new sigesp_c_seguridad();		
		$this->as_procede="";
		$this->as_comprobante="";
		$this->ad_fecha="";
		$this->as_codban="";
		$this->as_ctaban="";
		$this->as_procedeaux="";
		$this->as_comprobanteaux="";
		$this->ad_fechaaux="";
		$this->as_codbanaux="";
		$this->as_ctabanaux="";
	}// end function class_sigesp_sob_integracion
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_destroy_objects()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_destroy_objects
		//		   Access: public 
		//	  Description: Destructor de los objectos de la Clase
		//	   Creado Por: Ing. Wilmer Briceño
		// Modificado Por: Ing. Yesenia Moreno								Fecha Última Modificación : 25/04/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
       if( is_object($this->io_fecha) ) { unset($this->io_fecha);  }
       if( is_object($this->io_sigesp_int) ) { unset($this->io_sigesp_int);  }
	   if( is_object($this->io_function) ) { unset($this->io_function);  }
	   if( is_object($this->io_siginc) ) { unset($this->io_siginc);  }
	   if( is_object($this->io_connect) ) { unset($this->io_connect);  }
	   if( is_object($this->io_sql) ) { unset($this->io_sql);  }	   
	   if( is_object($this->obj) ) { unset($this->obj);  }	   
	   if( is_object($this->dts_empresa) ) { unset($this->dts_empresa);  }	   
	   if( is_object($this->dts_data) ) { unset($this->dts_data);  }	   
	   if( is_object($this->dts_data_contrato) ) { unset($this->dts_data_contrato);  }	   	   
	   if( is_object($this->io_msg) ) { unset($this->io_msg);  }	   
	}// end function uf_destroy_objects
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_procesar_contabilizacion_asignacion($as_codasi,$adt_fecha,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_procesar_contabilizacion_asignacion
		//		   Access: public (sigesp_mis_p_contabiliza_asignacion_sob.php)
		//	    Arguments: as_codasi  // Código de Asignacióna
		//				   adt_fecha  // Fecha de contabilización
		//				   aa_seguridad  // Arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto la contabilización correctamente
		//	  Description: Este metodo tiene como fin contabilizar en presupuesto la asignacion
		//	   Creado Por: Ing. Wilmer Briceño
		// Modificado Por: Ing. Yesenia Moreno								Fecha Última Modificación : 26/04/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	    $ls_codemp=$this->dts_empresa["codemp"];
        $ls_comprobante=$this->io_sigesp_int->uf_fill_comprobante($as_codasi);		
        $this->dts_data->resetds("codasi"); // inicializa el datastore en 0 registro.
		if(!$this->uf_select_asignacion($as_codasi))
		{
			$this->io_msg->message(" No existe la Asignación N° ".$as_codasi);
			return false;
		}		
		$ls_fecasi=$this->io_function->uf_convertirfecmostrar($this->dts_data->getValue("fecasi",1));
		$ldt_fecha=$this->io_function->uf_convertirfecmostrar($adt_fecha); 
		$ls_estspgscg=$this->dts_data->getValue("estspgscg",1);
		$ls_estasi=$this->dts_data->getValue("estasi",1);
		$ls_descripcion=$this->dts_data->getValue("desobr",1); 
		$ls_codigo_destino=$this->dts_data->getValue("cod_pro",1);	
        $ls_mensaje=$this->io_sigesp_int_spg->uf_operacion_codigo_mensaje("PC");	
        $ls_tipo_destino="P" ;		
        $ls_procede="SOBASI";
		if(($ls_estasi!=1)&&($ls_estasi!=6))
		{
			$this->io_msg->message(" La Asignación ".$as_codasi." debe estar en estatus EMITIDA ó MODIFICADA para su contabilización.");
			return false;
		}
        if(!$this->io_fecha->uf_comparar_fecha($ls_fecasi,$ldt_fecha))
		{
			$this->io_msg->message(" La Fecha de Contabilizacion es menor que la fecha de Emision de la Asignación Nº ".$as_codasi);
			return false;
		}
        // obtengo el monto de la Asignacion y la comparo con el monto de gasto acumulado		
        $ldec_sum_gasto= round($this->uf_sumar_total_cuentas_gasto_asignacion($as_codasi),2);
		$ldec_monto_asignacion = round($this->dts_data->getValue("montotasi",1),2);		
		if($ldec_monto_asignacion!=$ldec_sum_gasto)
        {
			$this->io_msg->message("La Asignación no esta cuadrado con el resumen presupuestario");
			return false;
        }       
        $this->io_sigesp_int->uf_int_init_transaction_begin();	
		$ls_codban="---";
		$ls_ctaban="-------------------------";
		$li_tipo_comp=1; // comprobante Normal
		$this->as_procede=$ls_procede;
		$this->as_comprobante=$ls_comprobante;
		$this->ad_fecha=$this->io_function->uf_convertirdatetobd($adt_fecha);
		$this->as_codban=$ls_codban;
		$this->as_ctaban=$ls_ctaban;
		$lb_valido=$this->io_sigesp_int->uf_int_init($ls_codemp,$ls_procede,$ls_comprobante,$adt_fecha,$ls_descripcion,
													 $ls_tipo_destino,$ls_codigo_destino,false,$ls_codban,$ls_ctaban,
													 $li_tipo_comp);
		if(!$lb_valido)
		{   
			$this->io_msg->message($this->io_sigesp_int->is_msg_error);
			$this->io_sigesp_int->uf_sql_transaction($lb_valido);
			return false;		   		   
		}
		$lb_valido=$this->uf_procesar_detalles_gastos_asignacion($as_codasi,$ls_mensaje,$ls_procede,$ls_descripcion,"PC");
		if($lb_valido) 
		{
			if($lb_valido)
			{
				$lb_valido=$this->io_sigesp_int->uf_init_end_transaccion_integracion($aa_seguridad); 
				if(!$lb_valido)
				{
					$this->io_msg->message($this->io_sigesp_int->is_msg_error);
				}
			}
		}
		if($lb_valido)
		{
			$lb_valido=$this->uf_update_estatus_contabilizado_asignacion($as_codasi,1);		
		}
		if($lb_valido)
		{
			$adt_fecha=$this->io_function->uf_convertirdatetobd($adt_fecha); 
			$lb_valido=$this->uf_update_fecha_contabilizado_sob_asignacion($ls_codemp,$as_codasi,$adt_fecha,'1900-01-01');
		}
	    if($lb_valido)
	    {
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="PROCESS";
			$ls_descripcion="Contabilizó la Asignación <b>".$as_codasi."</b>, Fecha de Contabilización <b>".$ldt_fecha."</b>";
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////
		}		
		$this->io_sigesp_int->uf_sql_transaction($lb_valido);
		return  $lb_valido;
	}// end function uf_procesar_contabilizacion_asignacion
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_asignacion($as_codasi)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_asignacion
		//		   Access: private
		//	    Arguments: as_codasi  // Código de Asignacióna
		//	      Returns: Retorna estructura de datos datastrore con la data de la asignación
		//	  Description: Este metodo realiza una busqueda de la asignación y la almacewna en un datastore
		//	   Creado Por: Ing. Wilmer Briceño
		// Modificado Por: Ing. Yesenia Moreno								Fecha Última Modificación : 25/04/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;		
		$ls_codemp=$this->io_codemp;
		$ls_sql="SELECT sob_asignacion.*, sob_obra.desobr ".
                "  FROM sob_asignacion, sob_obra ".
                " WHERE sob_asignacion.codemp='".$ls_codemp."' ".
				"   AND sob_asignacion.codasi='".$as_codasi."' ".
				"   AND sob_obra.codobr=sob_asignacion.codobr ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
            $this->io_msg->message("CLASE->Integración SOB MÉTODO->uf_select_asignacion ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
			$lb_valido=false;
		}
		else
		{                 
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_valido=true;
                $this->dts_data->data=$this->io_sql->obtener_datos($rs_data);
			}
			$this->io_sql->free_result($rs_data);
		}
		return $lb_valido;
	}// end function uf_select_asignacion
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_sumar_total_cuentas_gasto_asignacion($as_codasi)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_sumar_total_cuentas_gasto_asignacion
		//		   Access: private
		//	    Arguments: as_codasi  // Código de Asignacióna
		//	      Returns: Retorna un decimal valor monto
		//	  Description: Este método suma los detalles de gasto ASIGNACION.
		//	   Creado Por: Ing. Wilmer Briceño
		// Modificado Por: Ing. Yesenia Moreno								Fecha Última Modificación : 25/04/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $ldec_monto=0;
		$ls_sql="SELECT COALESCE(SUM(monto),0) As monto ".
                "  FROM sob_cuentasasignacion ".
                " WHERE codemp='".$this->io_codemp."' ".
				"   AND codasi='".$as_codasi."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
            $this->io_msg->message("CLASE->Integración SOB MÉTODO->uf_sumar_total_cuentas_gasto_asignacion ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
	        $ldec_monto=0;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ldec_monto=$row["monto"];
			}
			$this->io_sql->free_result($rs_data);
		}
		return $ldec_monto;
	}// end function uf_sumar_total_cuentas_gasto_asignacion
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
    function uf_procesar_detalles_gastos_asignacion($as_codasi,$as_mensaje,$as_procede_doc,$as_descripcion,$as_process)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_procesar_detalles_gastos_asignacion
		//		   Access: private
		//	    Arguments: as_codasi  // Código de Asignacióna
		//	    		   as_mensaje  // Mensaje del precompromiso
		//	    		   as_procede_doc  // Procede del Documento
		//	    		   as_descripcion  // Descripcioón de la obre
		//	    		   as_process  // proceso si se va a precomprometer o se va a hacer el reverso del precompromiso
		//	      Returns: Retorna un boolean valido
		//	  Description: método que procesa los detalles de gastos de una asignación
		//	   Creado Por: Ing. Wilmer Briceño
		// Modificado Por: Ing. Yesenia Moreno								Fecha Última Modificación : 25/04/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	    $lb_valido=true;		
		$ls_sql="SELECT * ".
                "  FROM sob_cuentasasignacion ".
                " WHERE codemp='".$this->io_codemp."' ".
				"   AND codasi='".$as_codasi."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
            $this->io_msg->message("CLASE->Integración SOB MÉTODO->uf_procesar_detalles_gastos_asignacion ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
			return false;
		}
		else
		{                 
			while($row=$this->io_sql->fetch_row($rs_data) and ($lb_valido))
		    {
				$ls_codestpro1=$row["codestpro1"];
				$ls_codestpro2=$row["codestpro2"];
				$ls_codestpro3=$row["codestpro3"];
				$ls_codestpro4=$row["codestpro4"];
				$ls_codestpro5=$row["codestpro5"];
				$ls_estcla=$row["estcla"];
				$ls_spg_cuenta=$row["spg_cuenta"];
				$ls_documento=$this->io_sigesp_int->uf_fill_comprobante($as_codasi);		
				$ldec_monto=$row["monto"];
                if($as_process=="PC")
				{// Se genera el precompromiso de la asignación	
					$ldec_monto=$ldec_monto;
				}
				else //"CO" Reverso del precompromiso
				{
  	 	 	 	   $ldec_monto=$ldec_monto*(-1);
				}
				$lb_valido = $this->io_sigesp_int->uf_spg_insert_datastore($this->io_codemp,$ls_codestpro1,$ls_codestpro2,$ls_codestpro3,
									                                       $ls_codestpro4,$ls_codestpro5,$ls_estcla,$ls_spg_cuenta,$as_mensaje,
									                                       $ldec_monto,$ls_documento,$as_procede_doc,$as_descripcion);
				if ($lb_valido===false)
				{  
				   $this->io_msg->message($this->io_sigesp_int->is_msg_error);
				   break;
				}
			} 
			$this->io_sql->free_result($rs_data);
		}
		return $lb_valido; 
	}// end function uf_procesar_detalles_gastos_asignacion
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_update_estatus_contabilizado_asignacion($as_codasi,$ai_estasi)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_procesar_detalles_gastos_asignacion
		//		   Access: private
		//	    Arguments: as_codasi  // Código de Asignacióna
		//	    		   ai_estasi  // Estatus de la Asignación
		//	      Returns: Retorna un boolean valido
		//	  Description: método que procesa los detalles de gastos de una asignación
		//	   Creado Por: Ing. Wilmer Briceño
		// Modificado Por: Ing. Yesenia Moreno								Fecha Última Modificación : 25/04/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $lb_valido=true;
		$ls_sql="UPDATE sob_asignacion ".
		        "   SET estspgscg=".$ai_estasi.
                " WHERE codemp='".$this->io_codemp."' ".
				"   AND codasi='".$as_codasi."'";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
            $this->io_msg->message("CLASE->Integración SOB MÉTODO->uf_update_estatus_contabilizado_asignacion ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
			return false;
		}
		return $lb_valido;
	}// end function uf_update_estatus_contabilizado_asignacion
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_procesar_reverso_asignacion($as_codasi,$ad_fechaconta,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_procesar_reverso_asignacion
		//		   Access: private
		//	    Arguments: as_codasi  // Código de Asignacióna
		//	    		   ad_fechaconta  // Fecha de Contabilización
		//	    		   aa_seguridad  // Arreglo de seguridad
		//	      Returns: Retorna un boolean valido
		//	  Description: Este metodo tiene como fin reversar la contabilizacion en presupuesto la asignacion
		//	   Creado Por: Ing. Wilmer Briceño
		// Modificado Por: Ing. Yesenia Moreno								Fecha Última Modificación : 25/04/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $lb_valido=false;
		$ldt_fecha=$ad_fechaconta;
	    $ls_procede="SOBASI"; // reverso de asignación.
        $ls_comprobante=$this->io_sigesp_int->uf_fill_comprobante($as_codasi);		
        $this->dts_data->resetds("codasi"); // inicializa el datastore en 0 registro.
		if(!$this->uf_select_asignacion($as_codasi))
		{
			$this->io_msg->message(" No existe la Asignación N° ".$as_codasi);
			return false;
		}		
		$ls_tipo_destino="P";				
		$ls_estspgscg=$this->dts_data->getValue("estspgscg",1);
		$ls_cod_pro=$this->dts_data->getValue("cod_pro",1);	
	    $ls_ced_bene="----------";
		if($ls_estspgscg!=1) 
		{
			$this->io_msg->message(" La Asignación ".$as_codasi." debe estar en estatus CONTABILIZADA para reversarla.");
			return false;
		}        
		$ls_codban="---";
		$ls_ctaban="-------------------------";
	    $lb_valido=$this->io_sigesp_int->uf_obtener_comprobante($this->io_codemp,$ls_procede,$ls_comprobante,$ldt_fecha,
		                                                        $ls_codban,$ls_ctaban,$ls_tipo_destino,$ls_ced_bene,$ls_cod_pro);
		if (!$lb_valido) 
		{ 
			$this->io_msg->message("ERROR-> No existe el comprobante Nº ".$ls_comprobante."-".$ls_procede.".");
			return false;
		}
        $this->io_sigesp_int->uf_int_init_transaction_begin();
		$lb_valido = $this->io_sigesp_int->uf_init_delete($this->io_codemp,$ls_procede,$ls_comprobante,$ldt_fecha,
		                                                  $ls_tipo_destino,$ls_ced_bene,$ls_cod_pro,false,$ls_codban,$ls_ctaban);
		if(!$lb_valido)
		{ 
			$this->io_msg->message("ERROR-> ".$this->io_sigesp_int->is_msg_error);
			$this->io_sigesp_int->uf_sql_transaction($lb_valido);
			return false; 
		}		
	    if($lb_valido)
		{
			$lb_valido = $this->io_sigesp_int->uf_init_end_transaccion_integracion($aa_seguridad); 
			if(!$lb_valido)
			{
				$this->io_msg->message($this->io_sigesp_int->is_msg_error);
			}		   
		}
	    if ($lb_valido)
		{
	        $lb_valido=$this->uf_update_estatus_contabilizado_asignacion($as_codasi,0);
		}
		if($lb_valido)
		{
			$lb_valido=$this->uf_update_fecha_contabilizado_sob_asignacion($this->io_codemp,$as_codasi,'1900-01-01','1900-01-01');
		}
	    if($lb_valido)
	    {
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="PROCESS";
			$ls_descripcion="Reverso la Contabilización de la Asignación <b>".$as_codasi."</b>";
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////
		}		
		$this->io_sigesp_int->uf_sql_transaction($lb_valido);
		return  $lb_valido;
	}// end function uf_procesar_reverso_asignacion
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_procesar_anulacion_asignacion($as_codasi,$adt_fecha,$ad_fechaconta,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_procesar_anulacion_asignacion
		//		   Access: public
		//	    Arguments: as_codasi  // Código de Asignacióna
		//	    		   adt_fecha  // Fecha de Anulación
		//	    		   ad_fechaconta  // Fecha de Contabilización
		//	    		   aa_seguridad  // Arreglo de seguridad
		//	      Returns: Retorna un boolean valido
		//	  Description: Este metodo tiene como fin anular una asignación contabilizada	
		//	   Creado Por: Ing. Wilmer Briceño
		// Modificado Por: Ing. Yesenia Moreno								Fecha Última Modificación : 30/04/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	    $ls_codemp=$this->io_codemp;
        $ls_comprobante=$this->io_sigesp_int->uf_fill_comprobante($as_codasi);		
        $this->dts_data->resetds("codasi"); // inicializa el datastore en 0 registro.
		if(!$this->uf_select_asignacion($as_codasi))
		{
			$this->io_msg->message(" No existe la Asignación N° ".$as_codasi);
			return false;
		}		
		$ldt_fecasi=$this->io_function->uf_convertirfecmostrar($this->dts_data->getValue("fecasi",1));
		$ldt_fecha_anula=$this->io_function->uf_convertirfecmostrar($adt_fecha);
		$ls_descripcion=$this->dts_data->getValue("desobr",1); 
		$ls_codigo_destino=$this->dts_data->getValue("cod_pro",1);	
        $ls_mensaje=$this->io_sigesp_int_spg->uf_operacion_codigo_mensaje("PC");	
        $ls_tipo_destino="P";		
        $ls_procede="SOBASI";
        $ls_procede_anula="SOBRAS";
		$ls_codban="---";
		$ls_ctaban="-------------------------";
		$li_tipo_comp=1; // comprobante Normal
		$this->as_procede=$ls_procede_anula;
		$this->as_comprobante=$ls_comprobante;
		$this->ad_fecha=$this->io_function->uf_convertirdatetobd($ldt_fecha_anula);
		$this->as_codban=$ls_codban;
		$this->as_ctaban=$ls_ctaban;
		$lb_valido=$this->io_sigesp_int->uf_int_anular($ls_codemp,$ls_procede,$ls_comprobante,$ad_fechaconta,$ls_procede_anula,
		                                               $ldt_fecha_anula,$ls_descripcion,$ls_codban,$ls_ctaban,$li_tipo_comp);
		if(!$lb_valido)	
		{ 
			$this->io_msg->message("Error->".$this->io_sigesp_int->is_msg_error);
			return false; 
		}
		 // inicia transacción SQL
		$this->io_sigesp_int->uf_int_init_transaction_begin();
		if($lb_valido)
		{
			if ($lb_valido)
			{
				$lb_valido=$this->io_sigesp_int->uf_init_end_transaccion_integracion($aa_seguridad); 
				if(!$lb_valido)
				{
					$this->io_msg->message($this->io_sigesp_int->is_msg_error);
				}
			}
		}
		if($lb_valido)
		{
	        $lb_valido=$this->uf_update_estatus_contabilizado_asignacion($as_codasi,2);
		}
		if($lb_valido)
		{
			$adt_fecha=$this->io_function->uf_convertirdatetobd($adt_fecha);
			$lb_valido=$this->uf_update_fecha_contabilizado_sob_asignacion($this->io_codemp,$as_codasi,'',$adt_fecha);
		}
	    if($lb_valido)
	    {
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="PROCESS";
			$ls_descripcion="Contabilizó la Anulación de la Asignación <b>".$as_codasi."</b>, Fecha de Anulación <b>".$ldt_fecha_anula."</b>";
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////
		}		
		$this->io_sigesp_int->uf_sql_transaction($lb_valido);
		return $lb_valido;
    }// end function uf_procesar_anulacion_asignacion
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_procesar_reverso_anulacion_asignacion($as_codasi,$ad_fechaanula,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_procesar_reverso_anulacion_asignacion
		//		   Access: public
		//	    Arguments: as_codasi  // Código de Asignacióna
		//	    		   ad_fechaanula  // Fecha de Anulación
		//	    		   aa_seguridad  // Arreglo de seguridad
		//	      Returns: Retorna un boolean valido
		//	  Description: Este metodo tiene como fin reversar al anulacion una asignación contabilizada
		//	   Creado Por: Ing. Wilmer Briceño
		// Modificado Por: Ing. Yesenia Moreno								Fecha Última Modificación : 30/04/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $lb_valido=false;
		$ls_tipo_destino="P";		
		$ls_ced_bene="";
		$ls_cod_pro="";
		$ldt_fecha=$ad_fechaanula;
		$ls_procede="SOBRAS"; // reverso de anulación asignación.		
        $ls_comprobante=$this->io_sigesp_int->uf_fill_comprobante($as_codasi);		
        $this->dts_data->resetds("codasi"); // inicializa el datastore en 0 registro.
		if(!$this->uf_select_asignacion($as_codasi))
		{
			$this->io_msg->message("ERROR-> No existe la Asignación N° ".$as_codasi);
			return false;
		}		
		$ls_codban="---";
		$ls_ctaban="-------------------------";
	    $lb_valido = $this->io_sigesp_int->uf_obtener_comprobante($this->io_codemp,$ls_procede,$ls_comprobante,&$ldt_fecha,
		                                                          $ls_codban,$ls_ctaban,$ls_tipo_destino,&$ls_ced_bene,
																  &$ls_cod_pro);
		if(!$lb_valido) 
		{ 
			$this->io_msg->message("ERROR-> No existe el comprobante Nº ".$ls_comprobante."-".$ls_procede.".");
			return false;
		}
		$lb_valido=$this->io_sigesp_int->uf_init_delete($this->io_codemp,$ls_procede,$ls_comprobante,$ldt_fecha,$ls_tipo_destino,
														$ls_ced_bene,$ls_cod_pro,false,$ls_codban,$ls_ctaban);
		if(!$lb_valido)
		{ 
			$this->io_msg->message("ERROR-> ".$this->io_sigesp_int->is_msg_error);
			return false; 
		}
        $this->io_sigesp_int->uf_int_init_transaction_begin();
	    if ($lb_valido)
		{
			$lb_valido=$this->io_sigesp_int->uf_init_end_transaccion_integracion($aa_seguridad); 
			if(!$lb_valido)
			{
				$this->io_msg->message($this->io_sigesp_int->is_msg_error);
			}
		}
		if($lb_valido)
		{
	        $lb_valido=$this->uf_update_estatus_contabilizado_asignacion($as_codasi,1);		
		}
		if($lb_valido)
		{
			$lb_valido=$this->uf_update_fecha_contabilizado_sob_asignacion($this->io_codemp,$as_codasi,'','1900-01-01');
		}
	    if($lb_valido)
	    {
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="PROCESS";
			$ls_descripcion="Reverso la Anulación de la Asignación <b>".$as_codasi."</b>";
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////
		}		
		$this->io_sigesp_int->uf_sql_transaction($lb_valido);
        return $lb_valido;		
    }// end function uf_procesar_reverso_anulacion_asignacion
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
    function uf_procesar_contabilizacion_contrato($as_codcon,$as_codasi,$adt_fecha,$ad_fechacontaasig,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_procesar_contabilizacion_contrato
		//		   Access: public
		//	    Arguments: as_codcon  // Código de Contrato
		//	    		   as_codasi  // Código de Asignación
		//	    		   adt_fecha  // Fecha del Contrato
		//	    		   ad_fechacontaasig  // Fecha de Contabilización de la Asignación
		//	    		   aa_seguridad  // Arreglo de seguridad
		//	      Returns: Retorna un boolean valido
		//	  Description: Este metodo tiene como fin contabilizar en presupuesto el compromiso del contrato
		//	   Creado Por: Ing. Wilmer Briceño
		// Modificado Por: Ing. Yesenia Moreno								Fecha Última Modificación : 30/04/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	    $ls_codemp= $this->dts_empresa["codemp"];
        $ls_comprobante=$this->io_sigesp_int->uf_fill_comprobante($as_codcon);		
		$ldt_fecha=$this->io_function->uf_convertirfecmostrar($adt_fecha); 		
        $this->dts_data_contrato->resetds("codcon");
		if(!$this->uf_select_contrato($as_codcon,$as_codasi))
		{
			$this->io_msg->message(" No existe el Contrato N° ".$as_codcon);
			return false;
		}		
        // obtengo el monto de la Asignacion y la comparo con el monto de gasto acumulado		
        $ldec_sum_gasto=round($this->uf_sumar_total_cuentas_gasto_asignacion($as_codasi),2);
		$ldec_monto_asignacion=round($this->dts_data_contrato->getValue("montotasi",1),2);
		if($ldec_monto_asignacion!=$ldec_sum_gasto)
        {
			$this->io_msg->message("La Asignación del Contrato no esta cuadrado con el resumen presupuestario");
			return false;
        }       
		$ldt_feccon=$this->io_function->uf_convertirfecmostrar($this->dts_data_contrato->getValue("feccon",1));
		$ls_descripcion=$this->dts_data_contrato->getValue("desobr",1); 
		$ls_codigo_destino=$this->dts_data_contrato->getValue("cod_pro",1);	
        $ls_mensaje="O"; // Compromete
        $ls_tipo_destino="P";		
        $ls_procede="SOBCON"; // Procedencia Contrato Obras
        if(!$this->io_fecha->uf_comparar_fecha($ldt_feccon,$ldt_fecha))
		{
			$this->io_msg->message("La Fecha de Contabilizacion es menor que la fecha de Emision del Contrato Nº ".$as_codcon);
			return false;
		}
        if(!$this->io_fecha->uf_comparar_fecha($ad_fechacontaasig,$ldt_fecha))
		{
			$this->io_msg->message("La Fecha de Contabilizacion del Contrato es Menor que la Fecha de Contabilización de la Asignación ");
			return false;
		}
        $this->io_sigesp_int->uf_int_init_transaction_begin();
        $lb_valido=$this->uf_reversar_precomprometido_asignacion_contrato($as_codcon,$as_codasi,$ldt_fecha,$aa_seguridad);	
		if(!$lb_valido)
		{   
			$this->io_msg->message($this->io_sigesp_int->is_msg_error);
			$this->io_sigesp_int->uf_sql_transaction($lb_valido);
			return false;		   		   
		}
		$ls_codban="---";
		$ls_ctaban="-------------------------";
		$li_tipo_comp=1; // comprobante Normal
		$this->as_procede=$ls_procede;
		$this->as_comprobante=$ls_comprobante;
		$this->ad_fecha=$this->io_function->uf_convertirdatetobd($ldt_fecha);
		$this->as_codban=$ls_codban;
		$this->as_ctaban=$ls_ctaban;
		$lb_valido=$this->io_sigesp_int->uf_int_init($ls_codemp,$ls_procede,$ls_comprobante,$ldt_fecha,$ls_descripcion,
													 $ls_tipo_destino,$ls_codigo_destino,false,$ls_codban,$ls_ctaban,
													 $li_tipo_comp);
		if (!$lb_valido)
		{   
			$this->io_msg->message($this->io_sigesp_int->is_msg_error);
			$this->io_sigesp_int->uf_sql_transaction($lb_valido);
			return false;		   		   
		}
		$lb_valido=$this->uf_procesar_detalles_gastos_asignacion($as_codasi,$ls_mensaje,$ls_procede,$ls_descripcion,"PC");
		if($lb_valido) 
		{
			if($lb_valido)
			{
				$lb_valido=$this->io_sigesp_int->uf_init_end_transaccion_integracion($aa_seguridad); 
				if(!$lb_valido)
				{
					$this->io_msg->message($this->io_sigesp_int->is_msg_error);
				}
			}
		}
		if($lb_valido) 
		{
			$lb_valido=$this->uf_update_estatus_contabilizado_contrato($as_codcon,1);
		}
		if($lb_valido)
		{
			$adt_fecha=$this->io_function->uf_convertirdatetobd($adt_fecha); 		
			$lb_valido=$this->uf_update_fecha_contabilizado_sob_contrato($ls_codemp,$as_codcon,$as_codasi,$adt_fecha,'1900-01-01');
		}
	    if($lb_valido)
	    {
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="PROCESS";
			$ls_descripcion="Contabilizó el Contrato <b>".$as_codcon."</b>, Asignación <b>".$as_codasi."</b>, Fecha de Contabilización <b>".$adt_fecha."</b>";
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////
		}		
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		/// PARA LA CONVERSIÓN MONETARIA
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		/*if($lb_valido)
		{
			$lb_valido=$this->io_fun_mis->uf_convertir_sigespcmp($this->as_procede,$this->as_comprobante,$this->ad_fecha,
																 $this->as_codban,$this->as_ctaban,$aa_seguridad);
		}
		if($lb_valido)
		{
			$lb_valido=$this->io_fun_mis->uf_convertir_spgdtcmp($this->as_procede,$this->as_comprobante,$this->ad_fecha,
																$this->as_codban,$this->as_ctaban,$aa_seguridad);
		}*/
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$this->io_sigesp_int->uf_sql_transaction($lb_valido);
		return  $lb_valido;
	}// end function uf_procesar_contabilizacion_contrato
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_contrato($as_codcon,$as_codasi)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_contrato
		//		   Access: public
		//	    Arguments: as_codcon  // Código de Contrato
		//	      Returns: Retorna un boolean valido
		//	  Description: Este metodo realiza una busqueda del contrato y la almacena en un datastore
		//	   Creado Por: Ing. Wilmer Briceño
		// Modificado Por: Ing. Yesenia Moreno								Fecha Última Modificación : 30/04/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_codemp=$this->io_codemp;
		$ls_sql="SELECT sob_contrato.*, sob_asignacion.cod_pro, sob_asignacion.montotasi, sob_obra.desobr ".
                "  FROM sob_contrato, sob_asignacion, sob_obra ".
                " WHERE sob_contrato.codemp='".$ls_codemp."' ".
				"   AND sob_contrato.codcon='".$as_codcon."' ".
				"   AND sob_contrato.codasi='".$as_codasi."' ".
				"   AND sob_contrato.codasi=sob_asignacion.codasi ".
				"   AND sob_obra.codobr=sob_asignacion.codobr ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
            $this->io_msg->message("CLASE->Integración SOB MÉTODO->uf_select_contrato ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
			$lb_valido=false;
		}
		else
		{                 
			if($row=$this->io_sql->fetch_row($rs_data))
			{
                $this->dts_data_contrato->data=$this->io_sql->obtener_datos($rs_data);
			}
			$this->io_sql->free_result($rs_data);
		}
		return $lb_valido;
	}// end function uf_select_contrato
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
    function uf_reversar_precomprometido_asignacion_contrato($as_codcon,$as_codasi,$adt_fecha,$aa_seguridad)	
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_reversar_precomprometido_asignacion_contrato
		//		   Access: public
		//	    Arguments: as_codcon  // Código de Contrato
		//	    		   as_codasi  // Código de Asignación
		//	    		   adt_fecha  // Fecha de Reverso
		//	    		   aa_seguridad  // Arreglo de seguridad
		//	      Returns: Retorna un boolean valido
		//	  Description: Este método se encarga de preparar los datos básicos del comprobante de gasto 
		//                  y los detalles de gastos pero reverso (en negativo )
		//	   Creado Por: Ing. Wilmer Briceño
		// Modificado Por: Ing. Yesenia Moreno								Fecha Última Modificación : 30/04/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $ls_codemp=$this->io_codemp;
		$ls_mensaje="R";
        $ls_tipo_destino="P";		
        $ls_procede="SOBRPC"; // REVERSO DE PRECOMPROMISO
        $ls_comprobante=$this->io_sigesp_int->uf_fill_comprobante($as_codasi);				
		$ldt_fecha=$this->io_function->uf_convertirdatetobd($adt_fecha);		
        $this->dts_data->resetds("codasi"); // inicializa el datastore en 0 registro.
		if(!$this->uf_select_asignacion($as_codasi))
		{
			$this->io_msg->message(" No existe la Asignación N° ".$as_codasi. " asociada al contrato Nº ".$as_codcon);
			return false;
		}		
		$ls_descripcion=$this->dts_data->getValue("desobr",1); 
		$ls_codigo_destino = $this->dts_data->getValue("cod_pro",1);	
		if(empty($ls_descripcion))
		{
			$ls_descripcion="ninguno";
		}
		$this->io_sigesp_int->uf_int_config(true,false); 
		$ls_codban="---";
		$ls_ctaban="-------------------------";
		$li_tipo_comp=1; // comprobante Normal
		$this->as_procedeaux=$ls_procede;
		$this->as_comprobanteaux=$ls_comprobante;
		$this->ad_fechaaux=$ldt_fecha;
		$this->as_codbanaux=$ls_codban;
		$this->as_ctabanaux=$ls_ctaban;
		$lb_valido=$this->io_sigesp_int->uf_int_init($ls_codemp,$ls_procede,$ls_comprobante,$ldt_fecha,$ls_descripcion,
												     $ls_tipo_destino,$ls_codigo_destino,true,$ls_codban,$ls_ctaban,
													 $li_tipo_comp);
		if(!$lb_valido)
		{   
			$this->io_msg->message($this->io_sigesp_int->is_msg_error); 
			return false;		   		   
		}
		$lb_valido = $this->uf_procesar_detalles_gastos_asignacion($as_codasi,$ls_mensaje,$ls_procede,$ls_descripcion,"CO");        		
	    if($lb_valido)
		{ 
			$lb_valido=$this->io_sigesp_int->uf_init_end_transaccion_integracion($aa_seguridad);
  		}
	    if(!$lb_valido)
		{
			$this->io_msg->message("ERROR->".$this->io_sigesp_int->is_msg_error);
		}
		if($lb_valido)
		{
			$lb_valido=$this->uf_update_fecha_contabilizado_sob_asignacion($this->io_codemp,$as_codasi,'',$ldt_fecha);
		}
	    if($lb_valido)
	    {
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="PROCESS";
			$ls_descripcion="Reverso el Precompromiso de la Asignación <b>".$as_codasi."</b>, Fecha de Reverso <b>".$adt_fecha."</b>";
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////
		}		
		return $lb_valido;
	}// end function uf_reversar_precomprometido_asignacion_contrato
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_update_estatus_contabilizado_contrato($as_codcon,$ai_estspgscg)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_update_estatus_contabilizado_contrato
		//		   Access: private
		//	    Arguments: as_codcon  // Código de Contrato
		//	    		   ai_estspgscg  // Estatus de Contabilización
		//	      Returns: Retorna un boolean valido
		//	  Description: Método que actualiza el estatus de contabilizacion de un contrato
		//	   Creado Por: Ing. Wilmer Briceño
		// Modificado Por: Ing. Yesenia Moreno								Fecha Última Modificación : 30/04/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $lb_valido=true;
		$ls_sql="UPDATE sob_contrato ".
		        "   SET estspgscg=".$ai_estspgscg.
                " WHERE codemp='".$this->io_codemp."' ".
				"   AND codcon='".$as_codcon."'";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
            $this->io_msg->message("CLASE->Integración SOB MÉTODO->uf_update_estatus_contabilizado_contrato ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_update_estatus_contabilizado_contrato
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
    function uf_procesar_reverso_contrato($as_codcon,$as_codasi,$ad_fechaconta,$aa_seguridad)
	{ 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_procesar_reverso_contrato
		//		   Access: public
		//	    Arguments: as_codcon  // Código de Contrato
		//	    		   as_codasi  // Código de Asignación
		//	    		   ad_fechaconta  // Fecha de Contabilización
		//	    		   aa_seguridad  // Arreglo de Seguridad
		//	      Returns: Retorna un boolean valido
		//	  Description: Este metodo tiene como fin reversar la contabilizacion del contrato y restaurar el precompromiso de la asignación
		//	   Creado Por: Ing. Wilmer Briceño
		// Modificado Por: Ing. Yesenia Moreno								Fecha Última Modificación : 30/04/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $this->io_sigesp_int->uf_int_init_transaction_begin();
		$lb_valido=$this->uf_reverso_contrato_sob($as_codcon,$as_codasi,$ad_fechaconta,$aa_seguridad);
		if($lb_valido)
		{
			$lb_valido=$this->uf_delete_reverso_asignacion($as_codasi,$ad_fechaconta,$aa_seguridad);
		}
	    if($lb_valido)
	    {
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="PROCESS";
			$ls_descripcion="Reverso el Contrato <b>".$as_codcon."</b>, Asignación <b>".$as_codasi."</b>";
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////
		}		
		$this->io_sigesp_int->uf_sql_transaction($lb_valido);
		return $lb_valido;
    }// end function uf_procesar_reverso_contrato
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
    function uf_reverso_contrato_sob($as_codcon,$as_codasi,$ad_fechaconta,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_procesar_reverso_contrato
		//		   Access: public
		//	    Arguments: as_codcon  // Código de Contrato
		//	    		   as_codasi  // Código de Asignación
		//	    		   ad_fechaconta  // Fecha de Contabilización
		//	    		   aa_seguridad  // Arreglo de Seguridad
		//	      Returns: Retorna un boolean valido
		//	  Description: Este metodo tiene como fin reversar la contabilizacion del contrato y restaurar el precompromiso de la asignación
		//	   Creado Por: Ing. Wilmer Briceño
		// Modificado Por: Ing. Yesenia Moreno								Fecha Última Modificación : 30/04/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;				
	    $ls_codemp=$this->dts_empresa["codemp"];
        $ls_procede="SOBCON"; 
		$ls_tipo_destino="P";						
        $ls_comprobante=$this->io_sigesp_int->uf_fill_comprobante($as_codcon);		
		$this->dts_data_contrato->resetds("codcon"); // inicializa el datastore en 0 registro.		
		if(!$this->uf_select_contrato($as_codcon,$as_codasi))
		{
			$this->io_msg->message(" No existe el Contrato N° ".$as_codcon);
			return false;
		}		
		$ls_estspgscg=$this->dts_data_contrato->getValue("estspgscg",1);
		$ls_cod_pro=$this->dts_data_contrato->getValue("cod_pro",1);	
	    $ls_ced_bene="----------";
		if($ls_estspgscg!=1) 
		{
			$this->io_msg->message(" El Contrato ".$as_codcon." debe estar en estatus CONTABILIZADO para reversarlo.");
			return false;
		}
		$ldt_fecha=$ad_fechaconta;
		$ls_codban="---";
		$ls_ctaban="-------------------------";
	    $lb_valido=$this->io_sigesp_int->uf_obtener_comprobante($this->io_codemp,$ls_procede,$ls_comprobante,$ldt_fecha,
																$ls_codban,$ls_ctaban,$ls_tipo_destino,$ls_ced_bene,$ls_cod_pro);
		if(!$lb_valido) 
		{ 
			$this->io_msg->message("ERROR-> No existe el comprobante Nº ".$ls_comprobante."-".$ls_procede.".");
			return false;
		}
		$lb_valido=$this->io_sigesp_int->uf_init_delete($this->io_codemp,$ls_procede,$ls_comprobante,$ldt_fecha,$ls_tipo_destino,
														$ls_ced_bene,$ls_cod_pro,false,$ls_codban,$ls_ctaban);
		if(!$lb_valido)	
		{ 
			$this->io_msg->message("".$this->io_sigesp_int->is_msg_error);
			return false; 
		}
	    if($lb_valido)
		{
			$lb_valido=$this->io_sigesp_int->uf_init_end_transaccion_integracion($aa_seguridad); 
			if(!$lb_valido)
			{
				$this->io_msg->message($this->io_sigesp_int->is_msg_error);
			}
		}
	    if($lb_valido)
		{
	        $lb_valido=$this->uf_update_estatus_contabilizado_contrato($as_codcon,0);
		}
		if($lb_valido)
		{
			$lb_valido=$this->uf_update_fecha_contabilizado_sob_contrato($this->io_codemp,$as_codcon,$as_codasi,'1900-01-01','1900-01-01');
		}
		return  $lb_valido;
	}// end function uf_reverso_contrato_sob
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
    function uf_delete_reverso_asignacion($as_codasi,$ad_fechaconta,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_delete_reverso_asignacion
		//		   Access: private
		//	    Arguments: as_codasi  // Código de Asignación
		//	    		   ad_fechaconta  // Fecha de Contabilización
		//	    		   aa_seguridad  // Arreglo de Seguridad
		//	      Returns: Retorna un boolean valido
		//	  Description: Método que elimina el reverso del precompromiso de asignación.
		//	   Creado Por: Ing. Wilmer Briceño
		// Modificado Por: Ing. Yesenia Moreno								Fecha Última Modificación : 30/04/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
        $ls_procede="SOBRPC"; 
		$ls_tipo_destino="P";		
		$ls_ced_bene="";
		$ls_cod_pro="";
		$ldt_fecha=$ad_fechaconta;
		$lb_check_close=false;		
		$ls_codban="---";
		$ls_ctaban="-------------------------";
		$ls_comprobante=$this->io_sigesp_int->uf_fill_comprobante($as_codasi);
		$lb_valido = $this->io_sigesp_int->uf_obtener_comprobante($this->io_codemp,$ls_procede,$ls_comprobante,$ldt_fecha,
																  $ls_codban,$ls_ctaban,$ls_tipo_destino,&$ls_ced_bene,
																  &$ls_cod_pro);
		if(!$lb_valido) 
		{ 
			$this->io_msg->message("ERROR-> No existe el comprobante Nº ".$ls_comprobante."-".$ls_procede.".");
			return false;
		}
		$lb_valido = $this->io_sigesp_int->uf_init_delete($this->io_codemp,$ls_procede,$ls_comprobante,$ldt_fecha,$ls_tipo_destino,
														  $ls_ced_bene,$ls_cod_pro,$lb_check_close,$ls_codban,$ls_ctaban);
		
		if(!$lb_valido)	
		{
			$this->io_msg->message("ERROR->".$this->io_sigesp_int->is_msg_error);
			return false; 
		}
	    $lb_valido=$this->io_sigesp_int->uf_init_end_transaccion_integracion($aa_seguridad); 
	    if(!$lb_valido)
		{
			$this->io_msg->message("ERROR->".$this->io_sigesp_int->is_msg_error);
		}
		if($lb_valido)
		{
			$lb_valido=$this->uf_update_fecha_contabilizado_sob_asignacion($this->io_codemp,$as_codasi,'','1900-01-01');
		}
	    if($lb_valido)
	    {
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="PROCESS";
			$ls_descripcion="Reverso la Asignación <b>".$as_codasi."</b>";
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////
		}		
		return $lb_valido;
	}// end function uf_delete_reverso_asignacion
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
    function uf_procesar_anular_contabilizacion_contrato($as_codcon,$as_codasi,$adt_fecha_anula,$ad_fechaconta,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_procesar_anular_contabilizacion_contrato
		//		   Access: public
		//	    Arguments: as_codcon  // Código de Contrato
		//	    		   as_codasi  // Código de Asignación
		//	    		   adt_fecha_anula  // Fecha de Anulación
		//	    		   aa_seguridad  // Arreglo de Seguridad
		//	      Returns: Retorna un boolean valido
		//	  Description: Este metodo tiene como fin anular la contabilizacion del contrato
		//	   Creado Por: Ing. Wilmer Briceño
		// Modificado Por: Ing. Yesenia Moreno								Fecha Última Modificación : 30/04/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	    $ls_codemp=$this->io_codemp;
		$ls_comprobante=$this->io_sigesp_int->uf_fill_comprobante($as_codcon);
		$ls_procede="SOBCON";
		$ls_procede_anula="SOBACO";
        $ldt_fecha_cmp=$ad_fechaconta;
		$ls_tipo_destino="";
		$ls_ced_bene="";
		$ls_cod_pro="";
		$ls_descripcion="Anulación del Nº Contrato :".$as_codcon;
		$ldt_fecha_anula=$this->io_function->uf_convertirdatetobd($adt_fecha_anula);		 				

		$this->dts_data_contrato->resetds("codcon"); // inicializa el datastore en 0 registro.
		if(!$this->uf_select_contrato($as_codcon,$as_codasi))
		{
			$this->io_msg->message(" No existe el Contrato N° ".$as_codcon);
			return false;
		}		
		$ls_estspgscg=$this->dts_data_contrato->getValue("estspgscg",1);
		if($ls_estspgscg!=1) 
		{
			$this->io_msg->message(" El Contrato Nº ".$as_codcon." debe estar en estatus CONTABILIZADO.");
			return false;
		}
		$ls_codban="---";
		$ls_ctaban="-------------------------";
		$li_tipo_comp=1; // comprobante Normal
	    $lb_valido=$this->io_sigesp_int->uf_obtener_comprobante($ls_codemp,$ls_procede,$ls_comprobante,$ldt_fecha_cmp,$ls_codban,
																$ls_ctaban,$ls_tipo_destino,&$ls_ced_bene,&$ls_cod_pro);
		if(!$lb_valido) 
		{ 
			$this->io_msg->message("ERROR-> No existe el comprobante Nº ".$ls_comprobante."-".$ls_procede.".");
			return false;
		}
		$this->as_procede=$ls_procede_anula;
		$this->as_comprobante=$ls_comprobante;
		$this->ad_fecha=$this->io_function->uf_convertirdatetobd($ldt_fecha_anula);
		$this->as_codban=$ls_codban;
		$this->as_ctaban=$ls_ctaban;
		$lb_valido = $this->io_sigesp_int->uf_int_anular($ls_codemp,$ls_procede,$ls_comprobante,$ldt_fecha_cmp,$ls_procede_anula,
														 $ldt_fecha_anula,$ls_descripcion,$ls_codban,$ls_ctaban,$li_tipo_comp);
		if(!$lb_valido)	
		{ 
			$this->io_msg->message("ERROR->".$this->io_sigesp_int->is_msg_error);
			return false; 
		}
        $this->io_sigesp_int->uf_int_init_transaction_begin();
	    if($lb_valido)
	    {
	        $lb_valido = $this->io_sigesp_int->uf_init_end_transaccion_integracion($aa_seguridad); 
	        if(!$lb_valido)
		    {
				$this->io_msg->message("".$this->io_sigesp_int->is_msg_error);
		    }		   
		}
		if($lb_valido)
		{
			$lb_valido=$this->uf_delete_reverso_asignacion($as_codasi,$ad_fechaconta,$aa_seguridad);
		}
	    if($lb_valido)
		{
	        $lb_valido=$this->uf_update_estatus_contabilizado_contrato($as_codcon,2);
		}
		if($lb_valido)
		{
			$lb_valido=$this->uf_update_fecha_contabilizado_sob_contrato($this->io_codemp,$as_codcon,$as_codasi,'',$ldt_fecha_anula);
		}
	    if($lb_valido)
	    {
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="PROCESS";
			$ls_descripcion="Anuló el Contrato <b>".$as_codcon."</b>, Asignación <b>".$as_codasi."</b>, Fecha de Anulación <b>".$ldt_fecha_anula."</b>";
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////
		}		
		$this->io_sigesp_int->uf_sql_transaction($lb_valido);
		return $lb_valido;
	}// end function uf_procesar_anular_contabilizacion_contrato
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
    function uf_procesar_reverso_anulacion_contrato($as_codcon,$as_codasi,$ad_fechaconta,$ad_fechaanula,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_procesar_reverso_anulacion_contrato
		//		   Access: public
		//	    Arguments: as_codcon  // Código de Contrato
		//	    		   as_codasi  // Código de Asignación
		//	    		   ad_fechaconta  // Fecha en que fue contabilizado el contrato
		//	    		   ad_fechaanula  // Fecha en que fue anulado el contrato
		//	    		   aa_seguridad  // Arreglo de Seguridad
		//	      Returns: Retorna un boolean valido
		//	  Description: Este metodo tiene como fin anular la contabilizacion del contrato
		//	   Creado Por: Ing. Wilmer Briceño
		// Modificado Por: Ing. Yesenia Moreno								Fecha Última Modificación : 02/05/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	    $ls_codemp=$this->io_codemp;
		$ls_comprobante=$this->io_sigesp_int->uf_fill_comprobante($as_codcon);
		$ls_procede="SOBACO";
		$ls_tipo_destino="";
		$ls_ced_bene="";
		$ls_cod_pro="";
        $lb_check_close=false;
		$ldt_fecha=$ad_fechaanula;		 						
		$this->dts_data_contrato->resetds("codcon"); // inicializa el datastore en 0 registro.
		if(!$this->uf_select_contrato($as_codcon,$as_codasi))
		{
			$this->io_msg->message(" No existe el Contrato N° ".$as_codcon);
			return false;
		}		
		$ls_codban="---";
		$ls_ctaban="-------------------------";
	    $lb_valido=$this->io_sigesp_int->uf_obtener_comprobante($ls_codemp,$ls_procede,$ls_comprobante,$ldt_fecha,$ls_codban,
																$ls_ctaban,$ls_tipo_destino,&$ls_ced_bene,&$ls_cod_pro);
		if(!$lb_valido)
		{ 
			$this->io_msg->message("ERROR-> No existe el comprobante Nº ".$ls_comprobante."-".$ls_procede.".");
			return false;
		}
		$lb_valido=$this->io_sigesp_int->uf_init_delete($ls_codemp,$ls_procede,$ls_comprobante,$ldt_fecha,$ls_tipo_destino,
														$ls_ced_bene,$ls_cod_pro,$lb_check_close,$ls_codban,$ls_ctaban);
		if(!$lb_valido)	
		{ 
			$this->io_msg->message("ERROR->".$this->io_sigesp_int->is_msg_error);
			return false; 
		}
        $this->io_sigesp_int->uf_int_init_transaction_begin();
	    if($lb_valido)
	    {
			$lb_valido = $this->io_sigesp_int->uf_init_end_transaccion_integracion($aa_seguridad); 
			if (!$lb_valido)
			{
				$this->io_msg->message("".$this->io_sigesp_int->is_msg_error);
			}		   
		}
	    if($lb_valido)
	    {
			if($lb_valido)
			{
		        $lb_valido=$this->uf_reversar_precomprometido_asignacion_contrato($as_codcon,$as_codasi,$ad_fechaconta,$aa_seguridad);
			}
		}	
	    if($lb_valido)
		{
	        $lb_valido=$this->uf_update_estatus_contabilizado_contrato($as_codcon,1);
		}
		if($lb_valido)
		{
			$lb_valido=$this->uf_update_fecha_contabilizado_sob_contrato($ls_codemp,$as_codcon,$as_codasi,'','1900-01-01');
		}
	    if($lb_valido)
	    {
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="PROCESS";
			$ls_descripcion="Reverso la Anulación del Contrato <b>".$as_codcon."</b>, Asignación <b>".$as_codasi."</b>";
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////
		}		
		$this->io_sigesp_int->uf_sql_transaction($lb_valido);
		return $lb_valido;
	}// end function uf_procesar_reverso_anulacion_contrato
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_update_fecha_contabilizado_sob_asignacion($as_codemp,$as_codasi,$ad_fechaconta,$ad_fechaanula)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_update_fecha_contabilizado_sob_asignacion
		//		   Access: private
		//	    Arguments: as_codemp  // Código
		//                 as_codasi  // Código de la Asignación
		//                 ad_fecha  // Fecha de contabilización ó de Anulación
		//	      Returns: lb_valido True si se ejecuto la contabilización correctamente
		//	  Description: Método que actualiza la solicitud en estatus contabilizado
		//	   Creado Por: Ing. Wilmer Briceño
		// Modificado Por: Ing. Yesenia Moreno								Fecha Última Modificación : 07/11/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $lb_valido=true;
		$ls_campo1="";
		$ls_campo2="";
		if($ad_fechaconta!="")
		{
			$ls_campo1=" fechaconta='".$ad_fechaconta."' ";
		}
		if($ad_fechaanula!="")
		{
			$ls_campo2=" fechaanula='".$ad_fechaanula."' ";
		}
		if($ls_campo1!="")
		{
			if($ls_campo2!="")
			{
				$ls_campos=$ls_campo1.", ".$ls_campo2;
			}
			else
			{
				$ls_campos=$ls_campo1;
			}
		}
		else
		{
			$ls_campos=$ls_campo2;
		}
		$ls_sql="UPDATE sob_asignacion ".
		        "   SET ".$ls_campos.
                " WHERE codemp='".$as_codemp."' ".
				"   AND codasi='".$as_codasi."'";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
            $this->io_msg->message("CLASE->Integración SOB MÉTODO->uf_update_fecha_contabilizado_sob_asignacion ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_update_fecha_contabilizado_sob_asignacion
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_update_fecha_contabilizado_sob_contrato($as_codemp,$as_codcon,$as_codasi,$ad_fechaconta,$ad_fechaanula)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_update_fecha_contabilizado_sob_contrato
		//		   Access: private
		//	    Arguments: as_codemp  // Código
		//                 as_codcon  // Código del Contrato
		//                 as_codasi  // Código de la Asignación
		//                 ad_fecha  // Fecha de contabilización ó de Anulación
		//	      Returns: lb_valido True si se ejecuto la contabilización correctamente
		//	  Description: Método que actualiza la solicitud en estatus contabilizado
		//	   Creado Por: Ing. Wilmer Briceño
		// Modificado Por: Ing. Yesenia Moreno								Fecha Última Modificación : 07/11/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $lb_valido=true;
		$ls_campo1="";
		$ls_campo2="";
		if($ad_fechaconta!="")
		{
			$ls_campo1=" fechaconta='".$ad_fechaconta."' ";
		}
		if($ad_fechaanula!="")
		{
			$ls_campo2=" fechaanula='".$ad_fechaanula."' ";
		}
		if($ls_campo1!="")
		{
			if($ls_campo2!="")
			{
				$ls_campos=$ls_campo1.", ".$ls_campo2;
			}
			else
			{
				$ls_campos=$ls_campo1;
			}
		}
		else
		{
			$ls_campos=$ls_campo2;
		}
		$ls_sql="UPDATE sob_contrato ".
		        "   SET ".$ls_campos.
                " WHERE codemp='".$as_codemp."' ".
				"   AND codasi='".$as_codasi."' ".
				"   AND codcon='".$as_codcon."' ";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
            $this->io_msg->message("CLASE->Integración SOB MÉTODO->uf_update_fecha_contabilizado_sob_asignacion ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_update_fecha_contabilizado_sob_contrato
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_procesar_contabilizacion_anticipo($as_codcon,$as_codant,$adt_fecha,$adt_fechacontacontrato,$as_codtipdoc,$as_codpro,$ai_monto,
												  $aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_procesar_contabilizacion_anticipo
		//		   Access: private
		//	    Arguments: as_codcon  // Código del contrato
		//				   as_codant  // Código del anticipo
		//				   adt_fecha  // Fecha de contabilización
		//				   adt_fechacontacontrato  // Fecha de contabilización del contrato
		//				   aa_seguridad  // Arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se genero la recepción de documento correctamente
		//	  Description: Método que registra la contabilizacion del anticipo
		//	   Creado Por: Ing. Yesenia Moreno	
		// Modificado Por: 																Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido = true;
		$as_comprobante=substr($as_codcon.$as_codant,0,15);
		$as_comprobante=str_pad($as_comprobante,15,"0",0);
		$ls_ced_bene="----------";
		$ls_descripcion="ANTICIPO CONTRATO ".$as_codcon;
		$ls_tipo_destino="P";
		$ai_monto=str_replace(".","",$ai_monto);
		$ai_monto=str_replace(",",".",$ai_monto);
		$adt_fecha=$this->io_function->uf_convertirdatetobd($adt_fecha);
		// inicia transacción SQL
		$this->io_sigesp_int->uf_int_init_transaction_begin(); 
		// Insertamos la Cabecera
		$ls_sql="INSERT INTO cxp_rd (codemp,numrecdoc,codtipdoc,ced_bene,cod_pro,dencondoc,fecemidoc, fecregdoc, fecvendoc,".
 		        "montotdoc, mondeddoc,moncardoc,tipproben,numref,estprodoc,procede,estlibcom,estaprord,fecaprord,usuaprord,".
				"estimpmun,codcla) VALUES ('".$this->io_codemp."','".$as_comprobante."','".$as_codtipdoc."','".$ls_ced_bene."',".
				"'".$as_codpro."','".$ls_descripcion."','".$adt_fecha."','".$adt_fecha."','".$adt_fecha."',".$ai_monto.
				",0,0,'".$ls_tipo_destino."','".$as_comprobante."','R','SOBANT',0,0,'1900-01-01','',0,'--')";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{  
           	$this->io_msg->message("CLASE->Integración SOB MÉTODO->uf_procesar_contabilizacion_anticipo ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		if($lb_valido)
		{	// Insertar los detalles Contables
			$lb_valido=$this->uf_insert_recepcion_documento_contable($as_codcon,$as_codant,$as_comprobante,$as_codtipdoc,$ls_ced_bene,$as_codpro);
		}
	    if($lb_valido)
		{	// Actualizar el estatus en la nómina
			$lb_valido=$this->uf_update_contabilizado_sob_anticipo($as_codcon,$as_codant,$adt_fecha,'1900-01-01',1);
		}		
		if($lb_valido)
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="PROCESS";
			$ls_descripcion="Generó la Recepción de Documento  <b>".$as_comprobante."</b>";
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////
		}
		$this->io_sigesp_int->uf_sql_transaction($lb_valido);		
		return $lb_valido;
	}  // end function uf_procesar_contabilizacion_anticipo
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_recepcion_documento_contable($as_codcon,$as_codant,$as_comprobante,$as_codtipdoc,$as_ced_bene,$as_cod_pro)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_recepcion_documento_contable
		//		   Access: private
		//	    Arguments: as_codcon  // Código del contrato
		//				   as_codant  // Código del anticipo
		//	               as_comprobante  // Código de Comprobante
		//				   as_codtipdoc  // Tipo de Documento
		//				   as_ced_bene  // Cédula del Beneficiario
		//				   as_cod_pro  // Código del Proveedor
		//				   as_codcomapo  // Arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se inserto los detalles contables en la recepción de documento correctamente
		//	  Description: Método que inserta los movimientos contables en la tabla de detalle de contable de la recepcion de documento
		//	   Creado Por: Ing. Wilmer Briceño
		// Modificado Por: Ing. Yesenia Moreno								Fecha Última Modificación : 25/10/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	    $lb_valido=true;		
		$ls_procede="SOBANT";
		$ls_sql="SELECT sob_anticipo.sc_cuenta AS cuentaanticipo, rpc_proveedor.sc_cuenta AS cuentaproveedor, sob_anticipo.monto ".
				"  FROM sob_anticipo, sob_contrato, sob_asignacion, rpc_proveedor  ".
				" WHERE sob_anticipo.codemp='".$this->io_codemp."' ".
				"   AND sob_anticipo.codant='".$as_codant."'".
				"   AND sob_anticipo.codcon='".$as_codcon."'".
				"   AND sob_anticipo.codemp=sob_contrato.codemp ".
				"   AND sob_anticipo.codcon=sob_contrato.codcon ".
				"   AND sob_asignacion.codemp=sob_contrato.codemp ".
				"   AND sob_asignacion.codasi=sob_contrato.codasi ".
				"   AND rpc_proveedor.codemp=sob_asignacion.codemp ".
				"   AND rpc_proveedor.cod_pro=sob_asignacion.cod_pro ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{   
           	$this->io_msg->message("CLASE->Integración SOB MÉTODO->uf_insert_recepcion_documento_contable ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
			return false;
		}
		else
		{           
			while($row=$this->io_sql->fetch_row($rs_data) and ($lb_valido))
			{
				$ls_scg_cuenta = $row["cuentaanticipo"];
				$ldec_monto = $row["monto"];				
				$ls_debhab = "D";				
				$ls_documento = $as_comprobante;								 
				$ls_status="";
				$ls_denominacion="";
				if(!$this->io_sigesp_int_scg->uf_scg_select_cuenta($this->io_codemp,$ls_scg_cuenta,$ls_status,$ls_denominacion))
				{
					$this->io_msg->message("La cuenta contable ".trim($ls_scg_cuenta)." no exite en el plan de cuenta.");			
				}
				if($lb_valido)
				{
					$ls_sql="INSERT INTO cxp_rd_scg (codemp,numrecdoc,codtipdoc,ced_bene,cod_pro,procede_doc,numdoccom,debhab,".
							"sc_cuenta,monto) VALUES ('".$this->io_codemp."','".$as_comprobante."','".$as_codtipdoc."','".$as_ced_bene."',".
							"'".$as_cod_pro."','".$ls_procede."','".$ls_documento."','".$ls_debhab."','".$ls_scg_cuenta."',".$ldec_monto.")";
					$li_row=$this->io_sql->execute($ls_sql);
					if($li_row===false)
					{
						$this->io_msg->message("CLASE->Integración SOB MÉTODO->uf_insert_recepcion_documento_contable ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
						$lb_valido=false;
						break;
					}
				}
				if ($lb_valido)
				{
					$ls_scg_cuenta = $row["cuentaproveedor"];
					$ls_debhab = "H";				
					$ls_status="";
					$ls_denominacion="";
					if(!$this->io_sigesp_int_scg->uf_scg_select_cuenta($this->io_codemp,$ls_scg_cuenta,$ls_status,$ls_denominacion))
					{
						$this->io_msg->message("La cuenta contable ".trim($ls_scg_cuenta)." no exite en el plan de cuenta.");			
					}
					if($lb_valido)
					{
						$ls_sql="INSERT INTO cxp_rd_scg (codemp,numrecdoc,codtipdoc,ced_bene,cod_pro,procede_doc,numdoccom,debhab,".
								"sc_cuenta,monto) VALUES ('".$this->io_codemp."','".$as_comprobante."','".$as_codtipdoc."','".$as_ced_bene."',".
								"'".$as_cod_pro."','".$ls_procede."','".$ls_documento."','".$ls_debhab."','".$ls_scg_cuenta."',".$ldec_monto.")";
						$li_row=$this->io_sql->execute($ls_sql);
						if($li_row===false)
						{
							$this->io_msg->message("CLASE->Integración SOB MÉTODO->uf_insert_recepcion_documento_contable ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
							$lb_valido=false;
							break;
						}
					}
				}
			} // end while
		}
		$this->io_sql->free_result($rs_data);	 
		return $lb_valido;
    } // end function uf_insert_recepcion_documento_contable
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_update_contabilizado_sob_anticipo($as_codcon,$as_codant,$ad_fechaconta,$ad_fechaanula,$as_estatus)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_update_fecha_contabilizado_sob_anticipo
		//		   Access: private
		//	    Arguments: as_codcon  // Código del Contrato
		//                 as_codant  // Código del anticipo
		//                 ad_fecha  // Fecha de contabilización ó de Anulación
		//	      Returns: lb_valido True si se ejecuto la contabilización correctamente
		//	  Description: Método que actualiza la solicitud en estatus contabilizado
		//	   Creado Por: Ing. Wilmer Briceño
		// Modificado Por: Ing. Yesenia Moreno								Fecha Última Modificación : 07/11/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $lb_valido=true;
		$ls_campo1="";
		$ls_campo2="";
		if($ad_fechaconta!="")
		{
			$ls_campo1=" fechaconta='".$ad_fechaconta."' ";
		}
		if($ad_fechaanula!="")
		{
			$ls_campo2=" fechaanula='".$ad_fechaanula."' ";
		}
		if($ls_campo1!="")
		{
			if($ls_campo2!="")
			{
				$ls_campos=$ls_campo1.", ".$ls_campo2;
			}
			else
			{
				$ls_campos=$ls_campo1;
			}
		}
		else
		{
			$ls_campos=$ls_campo2;
		}
		$ls_campos="estspgscg=".$as_estatus.", ".$ls_campos."";
		$ls_sql="UPDATE sob_anticipo ".
		        "   SET ".$ls_campos.
                " WHERE codemp='".$this->io_codemp."' ".
				"   AND codant='".$as_codant."' ".
				"   AND codcon='".$as_codcon."' ";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
            $this->io_msg->message("CLASE->Integración SOB MÉTODO->uf_update_fecha_contabilizado_sob_asignacion ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_update_fecha_contabilizado_sob_anticipo
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_procesar_reverso_anticipo($as_codcon,$as_codant,$as_cod_pro,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_procesar_reverso_anticipo
		//		   Access: private
		//	    Arguments: as_comprobante  // Código de Comprobante
		//				   ad_fechaconta  // Fecha en que fue contabilizado el Documento
		//				   aa_seguridad  // Arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el reverso correctamente
		//	  Description: Este metodo elimina la recepción de documento de una nómina
		//	   Creado Por: Ing. Wilmer Briceño
		// Modificado Por: Ing. Yesenia Moreno								Fecha Última Modificación : 25/10/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $lb_valido=true;
		$as_comprobante=substr($as_codcon.$as_codant,0,15);
		$as_comprobante=str_pad($as_comprobante,15,"0",0);
		$ls_ced_bene="----------";
		$ls_procede="SOBANT";
        $this->io_sigesp_int->uf_int_init_transaction_begin();
		$ls_sql="SELECT numsol ".
				"  FROM cxp_dt_solicitudes  ".
				" WHERE codemp='".$this->io_codemp."' ".
				"   AND numrecdoc='".$as_comprobante."' ".
				"   AND cod_pro='".$as_cod_pro."' ".
				"   AND ced_bene='".$ls_ced_bene."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{   
           	$this->io_msg->message("CLASE->Integración SOB MÉTODO->uf_procesar_reverso_anticipo ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
			print $this->io_sql->message;			
			$lb_valido=false;
		}
		else
		{        
			if (!$rs_data->EOF)   
			{
				$lb_valido=false;
           		$this->io_msg->message("La recepción ".$as_comprobante." se encuentra en la solicitud de pago ".$rs_data->fields["numsol"]." por lo tanto no puede ser reversada.");			
			}
		}
		if ($lb_valido)
		{
			// Eliminamos los Detalles Contables
			$ls_sql="DELETE ".
					"  FROM cxp_rd_scg ".
					" WHERE codemp='".$this->io_codemp."' ".
					"   AND numrecdoc='".$as_comprobante."' ".
					"   AND cod_pro='".$as_cod_pro."' ".
					"   AND ced_bene='".$ls_ced_bene."'".
					"   AND procede_doc='".$ls_procede."' ";
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$this->io_msg->message("CLASE->Integración SOB MÉTODO->uf_procesar_reverso_anticipo ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
				$lb_valido=false;
			}
		}
		if ($lb_valido)
		{
			// Eliminamos los Históricos de La Recepción de Documento
			$ls_sql="DELETE ".
					"  FROM cxp_historico_rd ".
					" WHERE codemp='".$this->io_codemp."' ".
					"   AND numrecdoc='".$as_comprobante."' ".
					"   AND cod_pro='".$as_cod_pro."' ".
					"   AND ced_bene='".$ls_ced_bene."'";
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$this->io_msg->message("CLASE->Integración SOB MÉTODO->uf_procesar_reverso_anticipo ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
				$lb_valido=false;
			}
		}
		if ($lb_valido)
		{
			// Eliminamos La Recepción de Documento
			$ls_sql="DELETE ".
					"  FROM cxp_rd ".
					" WHERE codemp='".$this->io_codemp."' ".
					"   AND numrecdoc='".$as_comprobante."' ".
					"   AND cod_pro='".$as_cod_pro."' ".
					"   AND ced_bene='".$ls_ced_bene."'".
					"   AND procede='".$ls_procede."' ";
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$this->io_msg->message("CLASE->Integración SOB MÉTODO->uf_procesar_reverso_anticipo ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
				$lb_valido=false;
			}
		}
	    if($lb_valido)
		{	// Actualizar el estatus en la nómina
			$lb_valido=$this->uf_update_contabilizado_sob_anticipo($as_codcon,$as_codant,'1900-01-01','1900-01-01',0);
		}		
		if($lb_valido)
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="PROCESS";
			$ls_descripcion="Reverso la Recepción de Documento  <b>".$as_comprobante."</b>";
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////
		}
		$this->io_sigesp_int->uf_sql_transaction($lb_valido);
		return $lb_valido;
	}  // end function uf_procesar_reverso_anticipo
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
    function uf_procesar_contabilizacion_variacion($as_codcon,$as_codvar,$adt_fecha,$ad_fechacontacontrato,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_procesar_contabilizacion_variacion
		//		   Access: public
		//	    Arguments: as_codcon  // Código de Contrato
		//	    		   as_codvar  // Código de Variacion
		//	    		   adt_fecha  // Fecha del Contrato
		//	    		   fechacontacontrato  // Fecha de Contabilización del contrato
		//	    		   aa_seguridad  // Arreglo de seguridad
		//	      Returns: Retorna un boolean valido
		//	  Description: Este metodo tiene como fin contabilizar en presupuesto el compromiso de la variacion
		//	   Creado Por: Ing. Yesenia Moreno
		// Modificado Por:												Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	    $ls_codemp= $this->dts_empresa["codemp"];
        $ls_comprobante=$this->io_sigesp_int->uf_fill_comprobante($as_codcon.$as_codvar);		
		$ldt_fecha=$this->io_function->uf_convertirfecmostrar($adt_fecha); 		
        $this->dts_data_variacion->resetds("codvar");
		if(!$this->uf_select_variacion($as_codcon,$as_codvar))
		{
			$this->io_msg->message(" No existe la variacion ".$as_codvar." del Contrato N° ".$as_codcon." para contabilizar.");
			return false;
		}		
        // obtengo el monto de la Asignacion y la comparo con el monto de gasto acumulado		
        $ldec_sum_gasto=round($this->uf_sumar_total_cuentas_gasto_variacion($as_codcon,$as_codvar),2);
		$ldec_monto_asignacion=round($this->dts_data_variacion->getValue("monto",1),2);
		if($ldec_monto_asignacion!=$ldec_sum_gasto)
        {
			$this->io_msg->message("La Variacion del Contrato no esta cuadrado con el resumen presupuestario");
			return false;
        }   
		$ldt_feccon=$this->io_function->uf_convertirfecmostrar($ad_fechacontacontrato);
		$ls_descripcion=$this->dts_data_variacion->getValue("motvar",1); 
		$ls_codigo_destino=$this->dts_data_variacion->getValue("cod_pro",1);	
        $ls_mensaje="O"; // Compromete
        $ls_tipo_destino="P";		
        $ls_procede="SOBVAR"; // Procedencia Variacion de contrato de Obras
        if(!$this->io_fecha->uf_comparar_fecha($ldt_feccon,$ldt_fecha))
		{
			$this->io_msg->message("La Fecha de Contabilizacion es menor que la fecha de Emision del Contrato Nº ".$as_codcon);
			return false;
		}
        $this->io_sigesp_int->uf_int_init_transaction_begin();
		$ls_codban="---";
		$ls_ctaban="-------------------------";
		$li_tipo_comp=1; // comprobante Normal
		$this->as_procede=$ls_procede;
		$this->as_comprobante=$ls_comprobante;
		$this->ad_fecha=$this->io_function->uf_convertirdatetobd($ldt_fecha);
		$this->as_codban=$ls_codban;
		$this->as_ctaban=$ls_ctaban;
		$lb_valido=$this->io_sigesp_int->uf_int_init($ls_codemp,$ls_procede,$ls_comprobante,$ldt_fecha,$ls_descripcion,
													 $ls_tipo_destino,$ls_codigo_destino,false,$ls_codban,$ls_ctaban,
													 $li_tipo_comp);
		if (!$lb_valido)
		{   
			$this->io_msg->message($this->io_sigesp_int->is_msg_error);
			$this->io_sigesp_int->uf_sql_transaction($lb_valido);
			return false;		   		   
		}
		$lb_valido=$this->uf_procesar_detalles_gastos_variacion($as_codcon,$as_codvar,$ls_mensaje,$ls_procede,$ls_descripcion);
		if($lb_valido) 
		{
			if($lb_valido)
			{
				$lb_valido=$this->io_sigesp_int->uf_init_end_transaccion_integracion($aa_seguridad); 
				if(!$lb_valido)
				{
					$this->io_msg->message($this->io_sigesp_int->is_msg_error);
				}
			}
		}
		if($lb_valido) 
		{
			$adt_fecha=$this->io_function->uf_convertirdatetobd($adt_fecha); 		
			$lb_valido=$this->uf_update_estatus_contabilizado_variacion($as_codcon,$as_codvar,1,$adt_fecha,'1900-01-01');
		}
	    if($lb_valido)
	    {
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="PROCESS";
			$ls_descripcion="Contabilizó la variacion ".$as_codvar." del Contrato <b>".$as_codcon."</b>  Fecha de Contabilización <b>".$adt_fecha."</b>";
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////
		}		
		$this->io_sigesp_int->uf_sql_transaction($lb_valido);
		return  $lb_valido;
	}// end function uf_procesar_contabilizacion_variacion
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_variacion($as_codcon,$as_codvar)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_variacion
		//		   Access: private
		//	    Arguments: as_codcon  // Código de Contrato
		//	               as_codvar  // Código de Variacion
		//	      Returns: Retorna estructura de datos datastrore con la data de la variacion
		//	  Description: Este metodo realiza una busqueda de la variacion y la almacewna en un datastore
		//	   Creado Por: Ing. Yesenia Moreno		
		// Modificado Por:   													Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;		
		$ls_sql="SELECT sob_variacioncontrato.*, sob_asignacion.cod_pro  ".
                "  FROM sob_variacioncontrato, sob_contrato, sob_asignacion  ".
                " WHERE sob_variacioncontrato.codemp='".$this->io_codemp."' ".
				"   AND sob_variacioncontrato.estvar=1 ".
				"   AND sob_variacioncontrato.estapr=1 ".
				"   AND sob_variacioncontrato.codcon='".$as_codcon."' ".
				"   AND sob_variacioncontrato.codvar='".$as_codvar."' ".
				"   AND sob_variacioncontrato.codemp = sob_contrato.codemp ".
				"   AND sob_variacioncontrato.codcon = sob_contrato.codcon ";
				"   AND sob_contrato.codemp = sob_asignacion.codemp ".
				"   AND sob_contrato.codasi = sob_asignacion.codasi ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
            $this->io_msg->message("CLASE->Integración SOB MÉTODO->uf_select_variacion ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
			$lb_valido=false;
		}
		else
		{                 
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_valido=true;
                $this->dts_data_variacion->data=$this->io_sql->obtener_datos($rs_data);
			}
			$this->io_sql->free_result($rs_data);
		}
		return $lb_valido;
	}// end function uf_select_variacion
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_sumar_total_cuentas_gasto_variacion($as_codcon,$as_codvar)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_sumar_total_cuentas_gasto_variacion
		//		   Access: private
		//	    Arguments: as_codcon  // Código de Contrato
		//	    	       as_codvar  // Código de Variacion
		//	      Returns: Retorna un decimal valor monto
		//	  Description: Este método suma los detalles de gasto variacion
		//	   Creado Por: Ing. Yesenia Moreno	
		// Modificado Por: 												Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $ldec_monto=0;
		$ls_sql="SELECT COALESCE(SUM(monto),0) As monto ".
                "  FROM sob_cuentavariacion ".
                " WHERE codemp='".$this->io_codemp."' ".
				"   AND codcon='".$as_codcon."'";
				"   AND codvar='".$as_codvar."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
            $this->io_msg->message("CLASE->Integración SOB MÉTODO->uf_sumar_total_cuentas_gasto_variacion ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
	        $ldec_monto=0;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ldec_monto=$row["monto"];
			}
			$this->io_sql->free_result($rs_data);
		}
		return $ldec_monto;
	}// end function uf_sumar_total_cuentas_gasto_variacion
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
    function uf_procesar_detalles_gastos_variacion($as_codcon,$as_codvar,$as_mensaje,$as_procede_doc,$as_descripcion)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_procesar_detalles_gastos_variacion
		//		   Access: private
		//	    Arguments: as_codcon  // Código de Contrato
		//	    		   as_codvar  // Código de Variacion
		//	    		   as_mensaje  // Mensaje del compromiso
		//	    		   as_procede_doc  // Procede del Documento
		//	    		   as_descripcion  // Descripcioón de la obre
		//	      Returns: Retorna un boolean valido
		//	  Description: método que procesa los detalles de gastos de una asignación
		//	   Creado Por: Ing. Wilmer Briceño
		// Modificado Por: Ing. Yesenia Moreno								Fecha Última Modificación : 25/04/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	    $lb_valido=true;		
		$ls_sql="SELECT * ".
                "  FROM sob_cuentavariacion ".
                " WHERE codemp='".$this->io_codemp."' ".
				"   AND codcon='".$as_codcon."'".
				"   AND codvar='".$as_codvar."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
            $this->io_msg->message("CLASE->Integración SOB MÉTODO->uf_procesar_detalles_gastos_variacion ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
			return false;
		}
		else
		{                 
			while($row=$this->io_sql->fetch_row($rs_data) and ($lb_valido))
		    {
				$ls_codestpro1=$row["codestpro1"];
				$ls_codestpro2=$row["codestpro2"];
				$ls_codestpro3=$row["codestpro3"];
				$ls_codestpro4=$row["codestpro4"];
				$ls_codestpro5=$row["codestpro5"];
				$ls_estcla=$row["estcla"];
				$ls_spg_cuenta=$row["spg_cuenta"];
				$ls_documento=$this->io_sigesp_int->uf_fill_comprobante($as_codcon);		
				$ldec_monto=$row["monto"];
				$lb_valido = $this->io_sigesp_int->uf_spg_insert_datastore($this->io_codemp,$ls_codestpro1,$ls_codestpro2,$ls_codestpro3,
									                                       $ls_codestpro4,$ls_codestpro5,$ls_estcla,$ls_spg_cuenta,$as_mensaje,
									                                       $ldec_monto,$ls_documento,$as_procede_doc,$as_descripcion);
				if ($lb_valido===false)
				{  
				   $this->io_msg->message($this->io_sigesp_int->is_msg_error);
				   break;
				}
			} 
			$this->io_sql->free_result($rs_data);
		}
		return $lb_valido; 
	}// end function uf_procesar_detalles_gastos_variacion
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_update_estatus_contabilizado_variacion($as_codcon,$as_codvar,$ai_estspgscg,$ad_fechaconta,$ad_fechaanula)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_update_estatus_contabilizado_variacion
		//		   Access: private
		//	    Arguments: as_codcon  // Código de Contrato
		//	    		   as_codvar  // Còdigo de Variacion
		//	    		   ai_estspgscg  // Estatus de Contabilización
		//	      Returns: Retorna un boolean valido
		//	  Description: Método que actualiza el estatus de contabilizacion de una variacion
		//	   Creado Por: Ing. Yesenia Moreno	
		// Modificado Por: 															Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $lb_valido=true;
		$ls_campo1="";
		$ls_campo2="";
		if($ad_fechaconta!="")
		{
			$ls_campo1=" fechaconta='".$ad_fechaconta."' ";
		}
		if($ad_fechaanula!="")
		{
			$ls_campo2=" fechaanula='".$ad_fechaanula."' ";
		}
		if($ls_campo1!="")
		{
			if($ls_campo2!="")
			{
				$ls_campos=$ls_campo1.", ".$ls_campo2;
			}
			else
			{
				$ls_campos=$ls_campo1;
			}
		}
		else
		{
			$ls_campos=$ls_campo2;
		}
		$ls_sql="UPDATE sob_variacioncontrato ".
		        "   SET estspgscg=".$ai_estspgscg.",".
				$ls_campos.
                " WHERE codemp='".$this->io_codemp."' ".
				"   AND codcon='".$as_codcon."'".
				"   AND codvar='".$as_codvar."'";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
            $this->io_msg->message("CLASE->Integración SOB MÉTODO->uf_update_estatus_contabilizado_variacion ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_update_estatus_contabilizado_variacion
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
    function uf_procesar_reverso_variacion($as_codcon,$as_codvar,$ad_fechaconta,$aa_seguridad)
	{ 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_procesar_reverso_variacion
		//		   Access: public
		//	    Arguments: as_codcon  // Código de Contrato
		//	    		   as_codvar  // Código de Variacion
		//	    		   ad_fechaconta  // Fecha de Contabilización
		//	    		   aa_seguridad  // Arreglo de Seguridad
		//	      Returns: Retorna un boolean valido
		//	  Description: Este metodo tiene como fin reversar la contabilizacion de la variacion
		//	   Creado Por: Ing. Yesenia Moreno	
		// Modificado Por: 												Fecha Última Modificación : 30/04/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;				
	    $ls_codemp=$this->dts_empresa["codemp"];
        $ls_procede="SOBVAR"; 
		$ls_tipo_destino="P";						
        $ls_comprobante=$this->io_sigesp_int->uf_fill_comprobante($as_codcon.$as_codvar);		
		$this->dts_data_variacion->resetds("codvar");
		if(!$this->uf_select_variacion($as_codcon,$as_codvar))
		{
			$this->io_msg->message(" No existe la variacion ".$as_codvar." del Contrato N° ".$as_codcon." para contabilizar.");
			return false;
		}		
		$ls_estspgscg=$this->dts_data_variacion->getValue("estspgscg",1);
		$ls_cod_pro=$this->dts_data_variacion->getValue("cod_pro",1);	
	    $ls_ced_bene="----------";
		if($ls_estspgscg!=1) 
		{
			$this->io_msg->message(" El Contrato ".$as_codcon." debe estar en estatus CONTABILIZADO para reversarlo.");
			return false;
		}
		$ldt_fecha=$ad_fechaconta;
		$ls_codban="---";
		$ls_ctaban="-------------------------";
	    $lb_valido=$this->io_sigesp_int->uf_obtener_comprobante($this->io_codemp,$ls_procede,$ls_comprobante,$ldt_fecha,
																$ls_codban,$ls_ctaban,$ls_tipo_destino,$ls_ced_bene,$ls_cod_pro);
		if(!$lb_valido) 
		{ 
			$this->io_msg->message("ERROR-> No existe el comprobante Nº ".$ls_comprobante."-".$ls_procede.".");
			return false;
		}
		$lb_valido=$this->io_sigesp_int->uf_init_delete($this->io_codemp,$ls_procede,$ls_comprobante,$ldt_fecha,$ls_tipo_destino,
														$ls_ced_bene,$ls_cod_pro,false,$ls_codban,$ls_ctaban);
		if(!$lb_valido)	
		{ 
			$this->io_msg->message("".$this->io_sigesp_int->is_msg_error);
			return false; 
		}
	    if($lb_valido)
		{
			$lb_valido=$this->io_sigesp_int->uf_init_end_transaccion_integracion($aa_seguridad); 
			if(!$lb_valido)
			{
				$this->io_msg->message($this->io_sigesp_int->is_msg_error);
			}
		}
	    if($lb_valido)
		{
			$adt_fecha=$this->io_function->uf_convertirdatetobd($ldt_fecha); 		
			$lb_valido=$this->uf_update_estatus_contabilizado_variacion($as_codcon,$as_codvar,0,$adt_fecha,'1900-01-01');
		}
	    if($lb_valido)
	    {
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="PROCESS";
			$ls_descripcion="Reverso de la variacion ".$as_codvar." del Contrato <b>".$as_codcon."</b>";
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////
		}		
		$this->io_sigesp_int->uf_sql_transaction($lb_valido);
		return $lb_valido;
    }// end function uf_procesar_reverso_variacion
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//----------------------------------------------------------------------------------------------------------------------------------------------------------------
	function uf_procesar_recepcion_documentos($as_numrecdoc,$as_codtipdoc,$as_conval,$ad_fecval,$ai_montotval,$ai_totreten,$ai_totconcar,$as_codcon,
											  $ai_basimpval,$as_codasi,$as_codval,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_procesar_recepcion_documentos
		//		   Access: private
		//	    Arguments: $as_numrecdoc    // Numero de Recepcion de documentos
		//				   $as_codtipdoc 	// Codigo de tipo de documento
		//				   $as_codtipdoc	// codigo de tipo de documento
		//				   $as_conval	    // Codigo de Valuacion
		//				   $ad_fecval  		// Fecha de Valuacion
		//				   $ai_montotval  	// Monto total de valuacion
		//				   $ai_totretten    // Monto total de retenciones
		//				   $ai_totcargos    // Monto total de cargos
		//				   $as_codcon       // Codigo del contrato
		//				   $ai_basimpval    // Base Imponible Valuacion
		//				   $as_codasi       // Codigo de Asignacion
		//				   $aa_seguridad    // Arreglo de las variables de seguridad
		//	      Returns: $lb_valido True si se genero la recepción de documento correctamente
		//	  Description: Retorna un Booleano
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 29/04/2008 								Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido= true;
        $ls_tipodestino= "P";			
		$ls_cedbene= "----------";	
		$ls_codpro=$this->uf_select_contratista($as_codcon);
		$li_totcargos=($ai_totconcar-$ai_basimpval);
		$lb_existe=$this->uf_select_recepcion($as_numrecdoc,$as_codtipdoc,$ls_cedbene,$ls_codpro);
		if(!$lb_existe)
		{
			$ad_fecval= $this->io_function->uf_convertirdatetobd($ad_fecval);
			$this->io_sql->begin_transaction();	
			$ls_sql="INSERT INTO cxp_rd (codemp,numrecdoc,codtipdoc,ced_bene,cod_pro,dencondoc,fecemidoc, fecregdoc, fecvendoc,".
					"                    montotdoc, mondeddoc,moncardoc,tipproben,numref,estprodoc,procede,estlibcom,estaprord,".
					"                    fecaprord,usuaprord,estimpmun,codcla)".
					"     VALUES ('".$this->io_codemp."','".$as_numrecdoc."','".$as_codtipdoc."','".$ls_cedbene."',".
					"             '".$ls_codpro."','".$as_conval."','".$ad_fecval."','".$ad_fecval."','".$ad_fecval."',
					"               .$ai_montotval.",".$ai_totreten.",".$li_totcargos.",'".$ls_tipodestino."','".$as_numrecdoc."','R','SOBCON',0,0,'1900-01-01','OBRAS',0,'--')";
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$this->io_msg->message("CLASE->Valuacion MÉTODO->uf_procesar_recepcion_documentos ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
				$lb_valido=false;
			}
			if($lb_valido)
			{
				$lb_valido=$this->uf_insert_dt_recepcion_documento($as_numrecdoc,$as_codtipdoc,$ls_cedbene,$ls_codpro,$ai_basimpval,$as_codcon,$as_codasi,$as_codval);
				if($lb_valido)
				{
					$lb_valido=$this->uf_update_estatus_generacion_rd($as_codcon,$as_conval,$aa_seguridad);
				}
				if($lb_valido)
				{
					/////////////////////////////////         SEGURIDAD               /////////////////////////////		
					$ls_evento="PROCESS";
					$ls_descripcion="Generó la Recepción de Documento de la llave contrato-valuacion <b>".$as_numrecdoc."</b>";
					$lb_valido= $this->seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
													  $aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
													  $aa_seguridad["ventanas"],$ls_descripcion);
					/////////////////////////////////         SEGURIDAD               /////////////////////////////
				}
			}
			if($lb_valido)
			{
				$this->io_sql->commit();	
				$this->io_msg->message("La Recepcion de Documentos se Genero con Exito.");
			}
			else
			{
				$this->io_sql->rollback();	
				$this->io_msg->message("No se Genero la Recepcion de Documentos");
			}
		}
		else
		{
			$this->io_msg->message("La Recepcion de Documentos ya Existe.");
			$lb_valido=false;
		}
		return $lb_valido;
	}  // end function uf_procesar_recepcion_documentos
	//----------------------------------------------------------------------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------------------------------------------------------------------------
	function uf_update_estatus_generacion_rd($as_codcon,$as_conval,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_procesar_recepcion_documentos
		//		   Access: private
		//	    Arguments: $as_conant	    // descripcion del documento
		//				   $as_codcon       // Codigo del contrato
		//				   $aa_seguridad    // Arreglo de las variables de seguridad
		//	      Returns: $lb_valido True si se genero la recepción de documento correctamente
		//	  Description: Retorna un Booleano
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 05/05/2008 								Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql="UPDATE sob_valuacion".
				"   SET estgenrd='1'".
				" WHERE codemp='".$this->io_codemp."'".
				"   AND codcon='".$as_codcon."'".
				"   AND codval='".$as_conval."'";		
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{			
           	$this->io_msg->message("CLASE->Valuacion MÉTODO->uf_update_estatus_generacion_rd ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
			$this->io_sql->rollback();
			
		}
		else
		{
				/////////////////////////////////         SEGURIDAD               /////////////////////////////		
				$ls_evento="UPDATE";
				$ls_descripcion ="Actualizó el estatus de generacion de R.D. de la Valuacion ".$as_conval." del Contrato ".$as_codcon." Asociado a la Empresa ".$this->io_codemp;
				$lb_variable= $this->seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
												$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
												$aa_seguridad["ventanas"],$ls_descripcion);
				/////////////////////////////////         SEGURIDAD               /////////////////////////////	
				$lb_valido=true;
		}		
		return $lb_valido;
	}
	//----------------------------------------------------------------------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------------------------------------------------------------------------
	function uf_select_contratista($as_codcon)
	{
	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_contratista
		//		   Access: private
		//	    Arguments: $as_codcon    // codigo de contrato
		//	      Returns: $ls_codpro Codigo de Proveedor
		//	  Description: Obtiene el codigo del proveedor relacionado con el contrato
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 29/04/2008 								Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;		
		$ls_sql="SELECT sob_asignacion.cod_pro". 
				"  FROM sob_contrato , sob_asignacion". 
				" WHERE sob_contrato.codemp='".$this->io_codemp."'".
				"   AND sob_contrato.codcon='".$as_codcon."'".
				"   AND sob_contrato.codemp=sob_asignacion.codemp".
				"   AND sob_contrato.codasi=sob_asignacion.codasi";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->Valuacion MÉTODO->uf_select_contratista ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				//$la_data=$this->io_sql->obtener_datos($rs_data);
				$ls_codpro=$row["cod_pro"];
			}			
		}	
		return $ls_codpro;
	}
	//----------------------------------------------------------------------------------------------------------------------------------------------------------------
	//----------------------------------------------------------------------------------------------------------------------------------------------------------------
	function uf_select_recepcion($as_numrecdoc,$as_codtipdoc,$as_cedbene,$as_codpro)
	{
	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_recepcion
		//		   Access: private
		//	    Arguments: $as_numrecdoc // Numero de Recepcion de Documentos
		//                 $as_codtipdoc // Codigo de Tipo de Documento
		//                 $as_cedbene   // Cedula de Beneficiario
		//                 $as_codpro    // Codigo de Proveedor
 		//	      Returns: $ls_codpro Codigo de Proveedor
		//	  Description: Verifica la existencia de una Recepcion de Documentos
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 29/04/2008 								Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_existe=false;		
		$ls_codemp=$this->la_empresa["codemp"];
		$ls_sql="SELECT numrecdoc". 
				"  FROM cxp_rd". 
				" WHERE cxp_rd.codemp='".$ls_codemp."'".
				"   AND cxp_rd.numrecdoc='".$as_numrecdoc."'".
				"   AND cxp_rd.codtipdoc='".$as_codtipdoc."'".
				"   AND cxp_rd.cod_pro='".$as_codpro."'".
				"   AND cxp_rd.ced_bene='".$as_cedbene."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->Valuacion MÉTODO->uf_select_recepcion ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
			return false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_existe=true;		
			}			
		}	
		return $lb_existe;
	}
	//----------------------------------------------------------------------------------------------------------------------------------------------------------------
	//----------------------------------------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_dt_recepcion_documento($as_numrecdoc,$as_codtipdoc,$as_cedbene,$as_codpro,$ai_basimpval,$as_codcon,$as_codval)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_procesar_recepcion_documentos
		//		   Access: private
		//	    Arguments: $as_numrecdoc    // Numero de Recepcion de documentos
		//				   $as_codtipdoc 	// Codigo de tipo de documento
		//				   $as_cedbene   	// Cedula de Beneficiario
		//				   $as_codpro   	// Codigo de proveedor
		//				   $as_conval	    // Codigo de Valuacion
		//				   $ad_fecval  		// Fecha de Valuacion
		//				   $ai_montotval  	// Monto total de valuacion
		//				   $ai_totretten    // Monto total de retenciones
		//				   $ai_totcargos    // Monto total de cargos
		//				   $as_codcon       // Codigo del contrato
		//				   $ai_basimpval    // Base Imponible Valuacion
		//				   $as_codasi       // Codigo de Asignacion
		//				   $aa_seguridad    // Arreglo de las variables de seguridad
		//	      Returns: $lb_valido True 
		//	  Description: Retorna un Booleano
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 29/04/2008 								Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$lb_valido=$this->uf_select_cuentaspresupuestarias($as_codval,$as_codcon,$rs_datacuentas,$ai_rows);
		if($ai_rows>0)
		{
			while(!$rs_datacuentas->EOF)
			{
				$ls_codestpro1=$rs_datacuentas->fields["codestpro1"];
				$ls_codestpro2=$rs_datacuentas->fields["codestpro2"];
				$ls_codestpro3=$rs_datacuentas->fields["codestpro3"];
				$ls_codestpro4=$rs_datacuentas->fields["codestpro4"];
				$ls_codestpro5=$rs_datacuentas->fields["codestpro5"];
				$ls_estcla=$rs_datacuentas->fields["estcla"];
				$ls_spgcuenta=$rs_datacuentas->fields["spg_cuenta"];
				$li_monto=$rs_datacuentas->fields["monto"];
				$ls_codestpro=$ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5;
				
				$ls_sql="INSERT INTO cxp_rd_spg (codemp, numrecdoc, codtipdoc, ced_bene, cod_pro, procede_doc, numdoccom, codestpro,".
						"                        spg_cuenta, monto, codfuefin,estcla)".
						"     VALUES ('".$this->io_codemp."','".$as_numrecdoc."','".$as_codtipdoc."','".$as_cedbene."',".
						"             '".$as_codpro."','SOBRPC','".$as_codcon."','".$ls_codestpro."','".$ls_spgcuenta."',".
						"               ".$li_monto.",'--','".$ls_estcla."')";
				$li_row=$this->io_sql->execute($ls_sql);
				if($li_row===false)
				{ 
					$this->io_msg->message("CLASE->Valuacion MÉTODO->uf_procesar_recepcion_documentos ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
					$lb_valido=false;
				}
				else
				{
					$lb_valido=true;
					$ls_sccuenta=$this->uf_select_cuentacontable($ls_spgcuenta,$ls_codestpro,$ls_estcla);
					$this->io_ds_scgcuentas->insertRow("sccuenta",$ls_sccuenta);
					$this->io_ds_scgcuentas->insertRow("debhab","D");
					$this->io_ds_scgcuentas->insertRow("monto",$ai_basimpval);
				}
				$rs_datacuentas->MoveNext();
			}
		}
		
		if($lb_valido)
		{
			if($lb_valido)
			{
				$lb_valido=$this->uf_insert_cargos($as_numrecdoc,$as_codtipdoc,$as_cedbene,$as_codpro,$as_codval,$as_codcon);
			}
			if($lb_valido)
			{
				$lb_valido=$this->uf_insert_deducciones($as_numrecdoc,$as_codtipdoc,$as_cedbene,$as_codpro,$as_codval,$as_codcon);
			}
		}
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------	
    function uf_select_cuentaspresupuestarias($as_codval,$as_codcon,&$rs_data,&$ai_rows)
	{
		/***************************************************************************************/
		/*	Function:	    uf_change_estatus_asi                                              */    
		/* Access:			public                                                             */ 
		/*	Returns:		Boolean, Retorna true si realizo el cambio de estatus al registro  */ 
		/*	Description:	Funcion que se encarga de cambiar el estatus a la asignacion       */    
		/*  Fecha:          25/03/2006                                                         */        
		/*	Autor:          GERARDO CORDERO		                                               */     
		/***************************************************************************************/
		$lb_valido=false;
		$rs_data="";
		$ai_rows=0;
		switch ($_SESSION["ls_gestor"])
		{
			case "MYSQLT":
				$ls_cadena="CONCAT(sob_cuentasasignacion.codemp,sob_cuentasasignacion.codasi,sob_cuentasasignacion.spg_cuenta,sob_cuentasasignacion.monto)";
				$ls_cadena2="CONCAT(sob_cargoasignacion.codemp,sob_cargoasignacion.codasi,sob_cargoasignacion.spg_cuenta,sob_cargoasignacion.monto)";
				break;
			case "POSTGRES":
				$ls_cadena="sob_cuentasasignacion.codemp||sob_cuentasasignacion.codasi||sob_cuentasasignacion.spg_cuenta||sob_cuentasasignacion.monto";
				$ls_cadena2="sob_cargoasignacion.codemp||sob_cargoasignacion.codasi||sob_cargoasignacion.spg_cuenta||sob_cargoasignacion.monto";
				break;
		}
		$ls_sql="SELECT codestpro1,codestpro2,codestpro3,codestpro4,codestpro5,estcla,spg_cuenta,monto".
				"  FROM sob_cuentavaluacion".
				" WHERE sob_cuentavaluacion.codemp='".$this->io_codemp."'".
				"   AND sob_cuentavaluacion.codcon='".$as_codcon."'".
				"   AND sob_cuentavaluacion.codval='".$as_codval."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->is_msg_error="Error en select".$this->io_function->uf_convertirmsg($this->io_sql->message);
			print $this->is_msg_error;
		}
		else
		{
			$lb_valido=true;
			$ai_rows=$this->io_sql->num_rows($rs_data);
		}		
		return $lb_valido;
	}	
	//----------------------------------------------------------------------------------------------------------------------------------------------------------------
	function uf_obtener_estructura($as_codasi,&$as_codestpro,&$as_spgcuenta,&$as_estcla)
	{
	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_obtener_estructura
		//		   Access: private
		//	    Arguments: $as_codasi    // codigo de asignacion
		//	      Returns: $ls_codpro Codigo de Proveedor
		//	  Description: Obtiene la estructura de la asignacion
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 29/04/2008 								Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$as_codestpro="";		
		$as_spgcuenta="";
		$lb_valido=false;		
		$ls_sql="SELECT codestpro1,codestpro2,codestpro3,codestpro4,codestpro5,spg_cuenta,estcla". 
				"  FROM sob_cuentasasignacion". 
				" WHERE codemp='".$this->io_codemp."'".
				"   AND codasi='".$as_codasi."'".
				"   AND spg_cuenta NOT IN (SELECT spg_cuenta FROM sigesp_cargos GROUP BY spg_cuenta);";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->Valuacion MÉTODO->uf_obtener_estructura ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));	
			return false;		
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$as_codestpro=$row["codestpro1"].$row["codestpro2"].$row["codestpro3"].$row["codestpro4"].$row["codestpro5"];
				$as_spgcuenta=$row["spg_cuenta"];
				$as_estcla=$row["estcla"];
				$lb_valido=true;
			}			
		}	
		return $lb_valido;
	}
	//----------------------------------------------------------------------------------------------------------------------------------------------------------------
	//----------------------------------------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_cargos($as_numrecdoc,$as_codtipdoc,$as_cedbene,$as_codpro,$as_codval,$as_codcon)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_obtener_estructura
		//		   Access: private
		//	    Arguments: $as_numrecdoc    // Numero de Recepcion de documentos
		//				   $as_codtipdoc 	// Codigo de tipo de documento
		//				   $as_cedbene   	// Cedula de Beneficiario
		//				   $as_codval   	// Codigo de valuacion
		//				   $as_codcon   	// Codigo de proveedor
		//	      Returns: $ls_codpro Codigo de Proveedor
		//	  Description: Obtiene la estructura de la asignacion
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 29/04/2008 								Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;		
		$ls_sql="SELECT codcar,basimp,monto,formula,codestprog,spg_cuenta,estcla". 
				"  FROM sob_cargovaluacion". 
				" WHERE codemp='".$this->io_codemp."'".
				"   AND codval='".$as_codval."'".
				"   AND codcon='".$as_codcon."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->Valuacion MÉTODO->uf_obtener_estructura ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));	
			return false;		
		}
		else
		{
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_codcar=$row["codcar"];
				$li_basimp=$row["basimp"];
				$li_monto=$row["monto"];
				$ls_formula=$row["formula"];
				$ls_codestpro=$row["codestprog"];
				$ls_spgcuenta=$row["spg_cuenta"];
				$ls_estcla=$row["estcla"];
				$ls_porcar=$this->uf_select_porcar($ls_codcar);
				$ls_sql="INSERT INTO cxp_rd_cargos (codemp, numrecdoc, codtipdoc, ced_bene, cod_pro, codcar, procede_doc, numdoccom,".
						"                           monobjret, monret, codestpro1, codestpro2, codestpro3, codestpro4, codestpro5,".
						"							spg_cuenta, porcar, formula,estcla)".
						"     VALUES ('".$this->io_codemp."','".$as_numrecdoc."','".$as_codtipdoc."','".$as_cedbene."',".
						"             '".$as_codpro."','".$ls_codcar."','SOBCON','".$as_codcon."',".$li_basimp.",".$li_monto.",".
						"             '".substr($ls_codestpro,0,25)."','".substr($ls_codestpro,25,25)."','".substr($ls_codestpro,50,25)."',".
						"             '".substr($ls_codestpro,75,25)."','".substr($ls_codestpro,100,25)."','".$ls_spgcuenta."','".$ls_porcar."',".
						"             '".$ls_formula."','".$ls_estcla."')";
				$li_row=$this->io_sql->execute($ls_sql);
				if($li_row===false)
				{print $this->io_sql->message;
					$this->io_msg->message("CLASE->Valuacion MÉTODO->uf_insert_cargos_I ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
					return false;
				}
				else
				{
					$this->io_ds_spgcuentas->insertRow("spgcuenta",$ls_spgcuenta);
					$this->io_ds_spgcuentas->insertRow("codestpro",$ls_codestpro);
					$this->io_ds_spgcuentas->insertRow("monto",$li_monto);
					$this->io_ds_spgcuentas->insertRow("basimp",$li_basimp);
					$this->io_ds_spgcuentas->insertRow("estcla",$ls_estcla);
				}
				$lb_valido=true;
			}	
			$this->io_ds_spgcuentas->group_by(array('0'=>'spgcuenta','1'=>'codestpro','2'=>'estcla'),array('0'=>'monto','1'=>'basimp'),'monto');
			$li_totrow=$this->io_ds_spgcuentas->getRowCount('spgcuenta');	
			for($li_fila=1;$li_fila<=$li_totrow;$li_fila++)
			{
				$ls_spgcuenta=$this->io_ds_spgcuentas->getValue('spgcuenta',$li_fila);
				$ls_codestpro=$this->io_ds_spgcuentas->getValue('codestpro',$li_fila);
				$ls_estcla=$this->io_ds_spgcuentas->getValue('estcla',$li_fila);
				$li_monto=$this->io_ds_spgcuentas->getValue('monto',$li_fila);
				$li_basimp=$this->io_ds_spgcuentas->getValue('basimp',$li_fila);
				
				$ls_sql="INSERT INTO cxp_rd_spg (codemp, numrecdoc, codtipdoc, ced_bene, cod_pro, procede_doc, numdoccom, codestpro,".
						"                        spg_cuenta, monto, codfuefin,estcla)".
						"     VALUES ('".$this->io_codemp."','".$as_numrecdoc."','".$as_codtipdoc."','".$as_cedbene."',".
						"             '".$as_codpro."','SOBCON','".$as_codcon."','".$ls_codestpro."','".$ls_spgcuenta."',".
						"               ".$li_monto.",'--','".$ls_estcla."')";
				$li_row=$this->io_sql->execute($ls_sql);
				if($li_row===false)
				{ 
					$this->io_msg->message("CLASE->Valuacion MÉTODO->uf_insert_cargos_II ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
					return false;
				} 
				else
				{
					$ls_sccuenta=$this->uf_select_cuentacontable($ls_spgcuenta,$ls_codestpro,$ls_estcla);
					$this->io_ds_scgcuentas->insertRow("sccuenta",$ls_sccuenta);
					$this->io_ds_scgcuentas->insertRow("debhab","D");
					$this->io_ds_scgcuentas->insertRow("monto",$li_monto);
					$lb_valido=true;
				}
			}
		}	
		return $lb_valido;
	}
	//----------------------------------------------------------------------------------------------------------------------------------------------------------------
	//----------------------------------------------------------------------------------------------------------------------------------------------------------------
	function uf_select_porcar($as_codcar)
	{
	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_porcar
		//		   Access: private
		//	    Arguments: $as_codcar    // codigo de cargo
		//	      Returns: $ls_codpro Codigo de Proveedor
		//	  Description: Obtiene el codigo del proveedor relacionado con el contrato
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 29/04/2008 								Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_porcar="";		
		$ls_codemp=$this->la_empresa["codemp"];
		$ls_sql="SELECT porcar". 
				"  FROM sigesp_cargos". 
				" WHERE codemp='".$this->io_codemp."'".
				"   AND codcar='".$as_codcar."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->Valuacion MÉTODO->uf_select_porcar ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_porcar=$row["porcar"];
			}			
		}	
		return $ls_porcar;
	}
	//----------------------------------------------------------------------------------------------------------------------------------------------------------------
	//----------------------------------------------------------------------------------------------------------------------------------------------------------------
	function uf_select_porded($as_codded,&$as_sccuenta)
	{
	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_porded
		//		   Access: private
		//	    Arguments: $as_codcar    // codigo de cargo
		//	      Returns: $ls_codpro Codigo de Proveedor
		//	  Description: Obtiene el codigo del proveedor relacionado con el contrato
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 29/04/2008 								Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_porded="";		
		$ls_codemp=$this->la_empresa["codemp"];
		$ls_sql="SELECT porded,sc_cuenta". 
				"  FROM sigesp_deducciones". 
				" WHERE codemp='".$this->io_codemp."'".
				"   AND codded='".$as_codded."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->Valuacion MÉTODO->uf_select_porded ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_porded=$row["porded"];
				$as_sccuenta=$row["sc_cuenta"];
			}			
		}	
		return $ls_porded;
	}
	//----------------------------------------------------------------------------------------------------------------------------------------------------------------
	//----------------------------------------------------------------------------------------------------------------------------------------------------------------
	function uf_select_cuentacontable($as_spgcuenta,$as_codestpro,$as_estcla)
	{
	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_cuentacontable
		//		   Access: private
		//	    Arguments: $as_spgcuenta // Cuenta Presupuestaria
		//				   $as_codestpro // Codigo de estructura programatica
		//	      Returns: $ls_codpro Codigo de Proveedor
		//	  Description: Obtiene el codigo del proveedor relacionado con el contrato
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 29/04/2008 								Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_sccuenta="";		
		$ls_sql="SELECT sc_cuenta". 
				"  FROM spg_cuentas". 
				" WHERE codemp='".$this->io_codemp."'".
				"   AND spg_cuenta='".$as_spgcuenta."'".
				"   AND estcla='".$as_estcla."'".
				"   AND codestpro1='".substr($as_codestpro,0,25)."'".
				"   AND codestpro2='".substr($as_codestpro,25,25)."'".
				"   AND codestpro3='".substr($as_codestpro,50,25)."'".
				"   AND codestpro4='".substr($as_codestpro,75,25)."'".
				"   AND codestpro5='".substr($as_codestpro,100,25)."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->Valuacion MÉTODO->uf_select_cuentacontable ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_sccuenta=$row["sc_cuenta"];
			}			
		}	
		return $ls_sccuenta;
	}
	//----------------------------------------------------------------------------------------------------------------------------------------------------------------
	//----------------------------------------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_deducciones($as_numrecdoc,$as_codtipdoc,$as_cedbene,$as_codpro,$as_codval,$as_codcon)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_deducciones
		//		   Access: private
		//	    Arguments: $as_numrecdoc    // Numero de Recepcion de documentos
		//				   $as_codtipdoc 	// Codigo de tipo de documento
		//				   $as_cedbene   	// Cedula de Beneficiario
		//				   $as_codval   	// Codigo de valuacion
		//				   $as_codcon   	// Codigo de proveedor
		//	      Returns: $ls_codpro Codigo de Proveedor
		//	  Description: Obtiene la estructura de la asignacion
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 29/04/2008 								Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;		
		$ls_sql="SELECT codded,monret,montotret". 
				"  FROM sob_retencionvaluacioncontrato". 
				" WHERE codemp='".$this->io_codemp."'".
				"   AND codval='".$as_codval."'".
				"   AND codcon='".$as_codcon."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->Valuacion MÉTODO->uf_insert_deducciones ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));	
			return false;		
		}
		else
		{
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_codded=$row["codded"];
				$li_monret=$row["monret"];
				$li_montotret=$row["montotret"];
				$ls_porded=$this->uf_select_porded($ls_codded,$ls_sccuenta);
				$ls_sql="INSERT INTO cxp_rd_deducciones (codemp, numrecdoc, codtipdoc, ced_bene, cod_pro, codded, procede_doc, numdoccom, monobjret,".
						" 								 monret, sc_cuenta, porded, estcmp)".
						"     VALUES ('".$this->io_codemp."','".$as_numrecdoc."','".$as_codtipdoc."','".$as_cedbene."',".
						"             '".$as_codpro."','".$ls_codded."','SOBRPC','".$as_codcon."',".$li_monret.",".$li_montotret.",".
						"             '".$ls_sccuenta."',".$ls_porded.",'0')";
				$li_row=$this->io_sql->execute($ls_sql);
				if($li_row===false)
				{ print $this->io_sql->message;
					$this->io_msg->message("CLASE->Valuacion MÉTODO->uf_insert_deducciones ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
					return false;
				}
				else
				{
					if($li_montotret>0)
					{
						$this->io_ds_scgcuentas->insertRow("sccuenta",$ls_sccuenta);
						$this->io_ds_scgcuentas->insertRow("debhab","H");
						$this->io_ds_scgcuentas->insertRow("monto",$li_montotret);
					}
				}
				$lb_valido=true;
			}
//			print_r($this->io_ds_scgcuentas->data);
			$this->io_ds_scgcuentas->group_by(array('0'=>'sccuenta','1'=>'debhab'),array('0'=>'monto'),'monto');
			$li_totrow=$this->io_ds_scgcuentas->getRowCount('sccuenta');	
//			print "<br />";
//			print_r($this->io_ds_scgcuentas->data);
			for($li_fila=1;$li_fila<=$li_totrow;$li_fila++)
			{
				$ls_sccuenta=$this->io_ds_scgcuentas->getValue('sccuenta',$li_fila);
				$ls_debhab=$this->io_ds_scgcuentas->getValue('debhab',$li_fila);
				$li_monto=$this->io_ds_scgcuentas->getValue('monto',$li_fila);
				
				$ls_sql="INSERT INTO cxp_rd_scg (codemp, numrecdoc, codtipdoc, ced_bene, cod_pro, procede_doc, numdoccom, debhab, sc_cuenta,".
						"				 		 monto, estgenasi, estasicon)".
						"     VALUES ('".$this->io_codemp."','".$as_numrecdoc."','".$as_codtipdoc."','".$as_cedbene."',".
						"             '".$as_codpro."','SOBRPC','".$as_codcon."','".$ls_debhab."','".$ls_sccuenta."',".
						"               ".$li_monto.",0,'A')";
				$li_row=$this->io_sql->execute($ls_sql);
				if($li_row===false)
				{ print $this->io_sql->message;
					$this->io_msg->message("CLASE->Valuacion MÉTODO->uf_insert_deducciones ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
					return false;
				} 
			}
			$this->io_ds_scgcuentas->group_by(array('0'=>'debhab'),array('0'=>'monto'),'monto');
			$li_totdebhab=$this->io_ds_scgcuentas->getRowCount('sccuenta');	
			$li_totdeb=0;
			$li_tothab=0;
			for($li_fildebhab=1;$li_fildebhab<=$li_totdebhab;$li_fildebhab++)
			{
				$ls_debhab=$this->io_ds_scgcuentas->getValue('debhab',$li_fildebhab);
				$li_monto=$this->io_ds_scgcuentas->getValue('monto',$li_fildebhab);
				if($ls_debhab=="D")
				{$li_totdeb=$li_totdeb+$li_monto;}
				else
				{$li_tothab=$li_tothab+$li_monto;}
				
			}
			$li_totpro=($li_totdeb-$li_tothab);
			$lb_valido=$this->uf_select_cuenta_proveedor($as_codpro,$as_sccuentapro);
			if($lb_valido)
			{
				$ls_sql="INSERT INTO cxp_rd_scg (codemp, numrecdoc, codtipdoc, ced_bene, cod_pro, procede_doc, numdoccom, debhab, sc_cuenta,".
						"				 		 monto, estgenasi, estasicon)".
						"     VALUES ('".$this->io_codemp."','".$as_numrecdoc."','".$as_codtipdoc."','".$as_cedbene."',".
						"             '".$as_codpro."','SOBRPC','".$as_codcon."','H','".$as_sccuentapro."',".
						"               ".$li_totpro.",0,'A')";
				$li_row=$this->io_sql->execute($ls_sql);
				if($li_row===false)
				{ 
					$this->io_msg->message("CLASE->Valuacion MÉTODO->uf_insert_deducciones ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
					return false;
				} 
				else
				{
					$lb_valido=true;
				}
			}
		}	
		return $lb_valido;
	}
	//----------------------------------------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_procesar_contabilizacion_valuacion($as_codcon,$as_codval,$adt_fecha,$as_codtipdoc,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_procesar_contabilizacion_anticipo
		//		   Access: private
		//	    Arguments: as_codcon  // Código del contrato
		//				   as_codant  // Código del anticipo
		//				   adt_fecha  // Fecha de contabilización
		//				   adt_fechacontacontrato  // Fecha de contabilización del contrato
		//				   aa_seguridad  // Arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se genero la recepción de documento correctamente
		//	  Description: Método que registra la contabilizacion del anticipo
		//	   Creado Por: Ing. Yesenia Moreno	
		// Modificado Por: 																Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido = true;
		$as_comprobante=substr($as_codcon.$as_codval,0,15);
		$as_comprobante=str_pad($as_comprobante,15,"0",0);
		$ls_ced_bene="----------";
		$ls_descripcion="VALUACION CONTRATO ".$as_codcon;
		$ls_tipo_destino="P";
		$ls_codpro=$this-> uf_select_contratista($as_codcon);
		$rs_data=$this->uf_select_valuacion($as_codcon,$as_codval);
		while(!$rs_data->EOF)
		{
			$li_monto=$rs_data->fields["montotval"];
			$rs_data->MoveNext();
		}

		
		$adt_fecha=$this->io_function->uf_convertirdatetobd($adt_fecha);
		// inicia transacción SQL
		$this->io_sigesp_int->uf_int_init_transaction_begin(); 
		// Insertamos la Cabecera
		$ls_sql="INSERT INTO cxp_rd (codemp,numrecdoc,codtipdoc,ced_bene,cod_pro,dencondoc,fecemidoc, fecregdoc, fecvendoc,".
 		        "montotdoc, mondeddoc,moncardoc,tipproben,numref,estprodoc,procede,estlibcom,estaprord,fecaprord,usuaprord,".
				"estimpmun,codcla) VALUES ('".$this->io_codemp."','".$as_comprobante."','".$as_codtipdoc."','".$ls_ced_bene."',".
				"'".$ls_codpro."','".$ls_descripcion."','".$adt_fecha."','".$adt_fecha."','".$adt_fecha."',".$li_monto.
				",0,0,'".$ls_tipo_destino."','".$as_comprobante."','R','SOBRPC',0,0,'1900-01-01','',0,'--')";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{  
           	$this->io_msg->message("CLASE->Integración SOB MÉTODO->uf_procesar_contabilizacion_anticipo ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		if($lb_valido)
		{	// Insertar los detalles Contables
			$lb_valido=$this->uf_insert_dt_recepcion_documento($as_comprobante,$as_codtipdoc,$ls_ced_bene,$ls_codpro,$li_monto,$as_codcon,$as_codval);
		}
		if($lb_valido)
		{	// Insertar los detalles Contables
			$lb_valido=$this->uf_update_estatus_recepcion_documento($as_codcon,$as_codval);
		}
		if($lb_valido)
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="PROCESS";
			$ls_descripcion="Generó la Recepción de Documento  <b>".$as_comprobante."</b>";
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////
		}
		$this->io_sigesp_int->uf_sql_transaction($lb_valido);		
		return $lb_valido;
	}  // end function uf_procesar_contabilizacion_anticipo
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_valuacion($as_codcon,$as_codval)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_verificar_anticipoeliminar
		//		   Access: private
		//	    Arguments: as_numsol  //  Número de Solicitud
		// 	      Returns: lb_existe True si existe ó False si no existe
		//	  Description: Funcion que verifica si el numero de contrato es el ultimo que esta registrado
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 26/04/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql="SELECT * ".
				"  FROM sob_valuacion ".
				" WHERE codemp='".$this->io_codemp."' ".
				"   AND codcon='".$as_codcon."' ".
				"   AND codval='".$as_codval."' ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Solicitud MÉTODO->uf_select_valuacion ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		return $rs_data;
	}// end function uf_verificar_solicitudeliminar
	//-----------------------------------------------------------------------------------------------------------------------------------	
	function uf_select_cuenta_proveedor($as_codpro,&$as_sccuenta)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_cuenta_proveedor
		//		   Access: private
		//	    Arguments: $as_codcon    // codigo de contrato
		//                 $as_sccuenta  // Cuenta de contratista
		//                 $as_ctaant    // Cuenta de anticipo de contratista
		//	      Returns: $lb_valido Devuelve un booleano
		//	  Description: Obtiene las cuentas contables para el asiento del anticipo
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 30/04/2008 								Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;		
		$ls_sql="SELECT sc_cuenta,sc_ctaant". 
				"  FROM rpc_proveedor". 
				" WHERE codemp='".$this->io_codemp."'".
				"   AND cod_pro='".$as_codpro."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
				$this->io_msg->message("CLASE->Anticipo MÉTODO->uf_select_cuenta_proveedor ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				//$la_data=$this->io_sql->obtener_datos($rs_data);
				$as_sccuenta=$row["sc_cuenta"];
				if($as_sccuenta!="")
				{
					$lb_valido=true;
				}
				else
				{
					$this->io_msg->message("Falta por configurar la cuenta contable del proveedor");
				}
			}			
		}	
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_update_estatus_recepcion_documento($as_codcon,$as_codval)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_update_estatus_recepcion_documento
		//		   Access: private
		//	    Arguments: as_codasi  // Código de Asignacióna
		//	    		   ai_estasi  // Estatus de la Asignación
		//	      Returns: Retorna un boolean valido
		//	  Description: método que procesa los detalles de gastos de una asignación
		//	   Creado Por: Ing. Wilmer Briceño
		// Modificado Por: Ing. Yesenia Moreno								Fecha Última Modificación : 25/04/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $lb_valido=true;
		$ls_sql="UPDATE sob_valuacion ".
		        "   SET estgenrd='1'";
                " WHERE codemp='".$this->io_codemp."' ".
				"   AND codcon='".$as_codcon."'".
				"   AND codval='".$as_codval."'";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
            $this->io_msg->message("CLASE->Integración SOB MÉTODO->uf_update_estatus_recepcion_documento ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
			return false;
		}
		return $lb_valido;
	}// end function uf_update_estatus_contabilizado_asignacion
	//-----------------------------------------------------------------------------------------------------------------------------------
	

}
?>