<?php
class sigesp_spi_c_comprobante
{
	var $is_msg_error;
	var $io_sql;
	var $io_include;
	var $io_int_scg;
	var $io_int_spi;
	var $io_msg;
	var $io_function;

function sigesp_spi_c_comprobante()
{
	require_once("../shared/class_folder/class_sql.php");
	require_once("../shared/class_folder/sigesp_include.php");
	require_once("../shared/class_folder/class_mensajes.php");
	require_once("../shared/class_folder/class_fecha.php");	
	require_once("../shared/class_folder/class_funciones.php");
	require_once("../shared/class_folder/class_sigesp_int.php");	
	require_once("../shared/class_folder/class_sigesp_int_scg.php");
	require_once("../shared/class_folder/class_sigesp_int_spi.php");
	require_once("../shared/class_folder/class_mensajes.php");
	require_once("../shared/class_folder/class_funciones.php");
	require_once("../shared/class_folder/sigesp_c_reconvertir_monedabsf.php");
    require_once("class_folder/class_funciones_spi.php");

	$this->io_function=new class_funciones();	
	$this->sig_int=new class_sigesp_int();
    $this->io_fecha=new class_fecha();
	$this->io_include=new sigesp_include();	
	$this->io_connect=$this->io_include->uf_conectar();
	$this->io_sql=new class_sql($this->io_connect);
	$this->io_msg = new class_mensajes();
	$this->io_int_spi=new class_sigesp_int_spi();	
	$this->io_int_scg=new class_sigesp_int_scg();	
	$this->is_msg_error="";
	// Agregado para la conversi�n
	$this->io_class_spi = new class_funciones_spi();
	$this->io_rcbsf		= new sigesp_c_reconvertir_monedabsf();
	$this->li_candeccon =$_SESSION["la_empresa"]["candeccon"];
	$this->li_tipconmon =$_SESSION["la_empresa"]["tipconmon"];
	$this->li_redconmon =$_SESSION["la_empresa"]["redconmon"];
	//Agregado para la conversi�n
}

function uf_generar_num_cmp($as_codemp,$as_procede)
{
    //$ls_sql="SELECT comprobante FROM sigesp_cmp WHERE codemp='".$as_codemp."' AND procede='".$as_procede."' ORDER BY comprobante DESC";		
      $ls_sql = "SELECT max(comprobante) as comprobante ".
            "   FROM sigesp_cmp ".
            "      WHERE      codemp='".$as_codemp."' ".
            "                     AND procede='".$as_procede."' " .
            "                     AND (comprobante not like '%A%' AND comprobante not like '%a%')".
            "                     AND (comprobante not like '%B%' AND comprobante not like '%b%')".
            "                     AND (comprobante not like '%C%' AND comprobante not like '%c%')".
            "                     AND (comprobante not like '%D%' AND comprobante not like '%d%')".
            "                     AND (comprobante not like '%E%' AND comprobante not like '%e%')".
            "                     AND (comprobante not like '%F%' AND comprobante not like '%f%')".
            "                     AND (comprobante not like '%G%' AND comprobante not like '%g%')".
            "                     AND (comprobante not like '%H%' AND comprobante not like '%h%')".
            "                     AND (comprobante not like '%I%' AND comprobante not like '%i%')".
            "                     AND (comprobante not like '%J%' AND comprobante not like '%j%')".
            "                     AND (comprobante not like '%K%' AND comprobante not like '%k%')".
            "                     AND (comprobante not like '%L%' AND comprobante not like '%l%')".
            "                     AND (comprobante not like '%M%' AND comprobante not like '%m%')".
            "                     AND (comprobante not like '%N%' AND comprobante not like '%n%')".
            "                     AND (comprobante not like '%O%' AND comprobante not like '%o%')".
            "                     AND (comprobante not like '%P%' AND comprobante not like '%p%')".
            "                     AND (comprobante not like '%Q%' AND comprobante not like '%q%')".
            "                     AND (comprobante not like '%R%' AND comprobante not like '%r%')".
            "                     AND (comprobante not like '%S%' AND comprobante not like '%s%')".
            "                     AND (comprobante not like '%T%' AND comprobante not like '%t%')".
            "                     AND (comprobante not like '%U%' AND comprobante not like '%u%')".
            "                     AND (comprobante not like '%V%' AND comprobante not like '%v%')".
            "                     AND (comprobante not like '%W%' AND comprobante not like '%w%')".
            "                     AND (comprobante not like '%X%' AND comprobante not like '%x%')".
            "                     AND (comprobante not like '%Y%' AND comprobante not like '%y%')".
            "                     AND (comprobante not like '%Z%' AND comprobante not like '%z%')".
            "                      ORDER BY comprobante DESC";
    
        $rs_funciondb=$this->io_sql->select($ls_sql);
      //print "$ls_sql <br>";
	  if ($row=$this->io_sql->fetch_row($rs_funciondb))
	  { 
		  $codigo=$row["comprobante"];
		  settype($codigo,'int');                             // Asigna el tipo a la variable.
		  $codigo = $codigo + 1;                              // Le sumo uno al entero.
		  settype($codigo,'string');                          // Lo convierto a varchar nuevamente.
		  $ls_codigo=$this->io_function->uf_cerosizquierda($codigo,15);
	  }
	  else
	  {
		  $codigo="1";
		  $ls_codigo=$this->io_function->uf_cerosizquierda($codigo,15);
	  }
	return $ls_codigo;
}

function uf_guardar_automatico($as_comprobante,$ad_fecha,$as_proccomp,$as_desccomp,$as_prov,$as_bene,$as_tipo,$ai_tipo_comp,$as_codban,$as_ctaban)
{
	$lb_valido=false;
	$dat=$_SESSION["la_empresa"];
	$_SESSION["fechacomprobante"]=$ad_fecha;////////modificado el 05/12/2007
    $ls_codemp=$_SESSION["la_empresa"]["codemp"];
    
    $ls_existe_comprobante_fecha = $this->uf_verificar_comprobante_fecha($ls_codemp,$as_proccomp,$as_comprobante,$ad_fecha);    
    if ($ls_existe_comprobante_fecha)
    {
        $this->io_msg->message("Existen comprobantes con el mismo numero y diferente fecha ");
        return $lb_valido;
    }
    
	if($this->uf_valida_datos_cmp($as_comprobante,$ad_fecha,$as_proccomp,$as_desccomp,&$as_prov,&$as_bene,$as_tipo))
	{	
	   $lb_valido=$this->io_int_spi->uf_sigesp_comprobante($dat["codemp"],$as_proccomp,$as_comprobante,$ad_fecha,$ai_tipo_comp,$as_desccomp,$as_tipo,$as_prov,$as_bene,0,$as_codban,$as_ctaban);
	   if (!$lb_valido)
	   {
	      $this->io_msg->message("Error al procesar el comprobante Presupuestario  ".$this->io_int_spi->is_msg_error);
	   }  
	   else  {   $this->io_msg->message("El Movimiento fue registrado."); }
	   
	   $ib_valido = $lb_valido;
	   
	   if($lb_valido)
	   {
		  $ib_new = $this->io_int_spi->ib_new_comprobante;
	   }	
	   else  {  $lb_valido=true;  } 	
	}
	else { $this->io_msg->message("Error en valida datos comprobante"); }
	return $lb_valido;
}
 //---------------------------------------------------------------------------------------------------------------------------------
    function uf_verificar_comprobante_fecha($as_codemp,$as_procedencia,$as_comprobante,$ad_fecha)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////
    //     Function:  uf_verificar_comprobante_fecha()
    //       Access:  public
    //    Arguments:  $as_codemp-> empresa,$as_procedencia->procedencia,$as_comprobante->comprobante,$as_fecha
    //      Returns:    booleano lb_existe
    //Description:  M�todo que verifica si existe o no el comprobante con fechas distintas
    /////////////////////////////////////////////////////////////////////////////////////////////////////////
       $ad_fecha = $this->io_function->uf_convertirdatetobd($ad_fecha);    
       $lb_existe=false;
       
       $ls_sql =   " SELECT comprobante ".
                   " FROM   sigesp_cmp ".
                   " WHERE codemp='".$as_codemp."' AND procede='".$as_procedencia."' AND comprobante='".$as_comprobante."' AND fecha <> '".$ad_fecha."' ";
       
       $lr_result = $this->io_sql->select($ls_sql);
       if($lr_result===false)
       {
          $this->is_msg_error="Error en uf_verificar_comprobante ".$this->io_function->uf_convertirmsg($this->io_sql->message);
          return false;
       }
       else  
       { 
          if($row=$this->io_sql->fetch_row($lr_result)) 
          { 
             $lb_existe=true;
          }  
      }
      return $lb_existe;
    } // end function uf_select_comprobante
 //---------------------------------------------------------------------------------------------------------------------------------    
function uf_cargar_dt_comprobante($as_codemp,$as_procede,$as_comprobante,$adt_fecha,$as_codban,$as_ctaban)
{

	$ld_fecha=$this->io_function->uf_convertirdatetobd($adt_fecha);

	$ls_sql="SELECT DISTINCT DT.spi_cuenta as spi_cuenta,C.denominacion as denominacion,
			 DT.procede_doc as procede_doc,P.desproc as desproc,DT.documento as documento,DT.operacion as operacion,
			 DT.descripcion as descripcion,DT.monto as monto,DT.orden as orden,DT.codestpro1 as codestpro1,DT.codestpro2 as codestpro2,
			 DT.codestpro3 as codestpro3, DT.codestpro4 as codestpro4, DT.codestpro5 as codestpro5, DT.estcla as estcla,
			 OP.denominacion as denominacion
			 FROM spi_dt_cmp DT,spi_cuentas C, sigesp_procedencias P,spi_operaciones OP
			 WHERE DT.procede=P.procede AND DT.codemp=C.codemp AND DT.spi_cuenta=C.spi_cuenta AND OP.operacion = DT.operacion  
			 AND DT.codemp='".$as_codemp."' AND DT.procede='".$as_procede."' AND DT.comprobante='".$as_comprobante."' AND DT.fecha='".$ld_fecha."' 
			 AND DT.codban='".$as_codban."' AND DT.ctaban='".$as_ctaban."' 
			 ORDER BY DT.orden "; 

	$rs_dt_cmp=$this->io_sql->select($ls_sql);
	
	if($rs_dt_cmp===false)
	{
		$this->io_msg->message($this->io_function->uf_convertirmsg($this->io_sql->message));
	}
	return $rs_dt_cmp;
}

function uf_cargar_dt_contable_cmp($as_codemp,$as_procede,$as_comprobante,$adt_fecha,$as_codban,$as_ctaban)
{

	$ld_fecha=$this->io_function->uf_convertirdatetobd($adt_fecha);
	$rs_dt_scg=$this->io_int_scg->uf_scg_cargar_detalle_comprobante( $as_codemp, $as_procede,$as_comprobante, $ld_fecha,&$lds_detalle_cmp,$as_codban,$as_ctaban);
	if($rs_dt_scg===false)
	{
		$this->io_msg->message($this->io_function->uf_convertirmsg($this->io_int_scg->io_sql->message));
	}
	return $rs_dt_scg;
}

function uf_valida_datos_cmp($as_comprobante,$ad_fecha,$as_procedencia,$as_desccomp,$as_cod_prov,$as_ced_bene,$as_tipo)
{
	$ls_desproc ="";
	if(!$this->io_int_spi->uf_valida_procedencia($as_procedencia,&$ls_desproc ) )
	{
	   $this->io_msg->message("Procedencia invalida.",$ls_desproc);
	   return false	;
	} 

	if(trim($as_comprobante)=="")
	{
		$this->io_msg->message("Debe registrar el comprobante contable.");
		return false;
	}

	if(trim($as_comprobante)=="000000000000000")
	{
		$this->io_msg->message("Debe registrar el comprobante contable.");
		return false;
	}
	
	
	if((trim($as_cod_prov)=="----------")&&($as_tipo=="P"))
	{
		$this->io_msg->message("Debe registrar el codigo del proveedor.");
		return false;
	}
	if((trim($as_cod_prov)=="")&&($as_tipo=="P"))
	{
		$this->io_msg->message("Debe registrar el codigo del proveedor.");
		return false;
	}
	
	if((trim($as_cod_prov)!="----------" )&&($as_tipo=="B"))
	{
		$as_cod_prov = "----------";
	}
		
	if((trim($as_ced_bene)=="----------")&&($as_tipo=="B"))
	{
		$this->io_msg->message("Debe registrar la c�dula del beneficiario1.");
		return false;
	}
	if((trim($as_ced_bene)=="")&&($as_tipo=="B"))
	{
		$this->io_msg->message("Debe registrar la c�dula del beneficiario.2");
		return false;	
	}
	
	if((trim($as_ced_bene)!="----------" )&&($as_tipo=="P"))
	{
		$as_ced_bene="----------";
	}
	if($as_tipo=="-")
	{
		$as_ced_bene="----------";
		$as_cod_prov="----------";
	}

  return true;
}

function uf_guardar_movimientos($arr_cmp,$ls_cuenta,$ls_procede_doc,$ls_descripcion,$ls_documento,$ls_operacionpre,
                                $ldec_monto_ant,$ldec_monto_act,$ls_tipocomp,$as_codban,$as_ctaban,
								$ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,$ls_codestpro5,$ls_estcla)
{
	$lb_valido=false; 
	$ls_mensaje = $this->io_int_spi->uf_operacion_codigo_mensaje($ls_operacionpre);
	if($ls_mensaje!="")
	{   ///print "entro1<br>";
		if(!$this->uf_spi_valida_datos_movimiento($ls_cuenta,$ls_descripcion,$ls_documento,&$ldec_monto))
		{ 
		   $this->io_msg->message("Error 1".$this->is_msg_error);
		   return false;
		}
		$this->io_int_spi->is_codemp=$arr_cmp["codemp"];
		$this->io_int_spi->is_comprobante=$arr_cmp["comprobante"];
		$this->io_int_spi->id_fecha=$arr_cmp["fecha"];
		$this->io_int_spi->is_procedencia=$arr_cmp["procedencia"];
		$this->io_int_spi->is_cod_prov=$arr_cmp["proveedor"];
		$this->io_int_spi->is_ced_bene=$arr_cmp["beneficiario"];
		$this->io_int_spi->is_tipo=$arr_cmp["tipo"];
		$this->io_int_spi->is_codban = $as_codban;
		$this->io_int_spi->is_ctaban = $as_ctaban;
		$lb_valido=$this->io_int_spi->uf_spi_comprobante_actualizar($ldec_monto_ant, $ldec_monto_act, $ls_tipocomp);
		if($lb_valido)
		{  ///print "entro2<br>";
                         $ls_sc_cuenta="";
			if ($arr_cmp["tipo"]=="B")  
				{ $ls_fuente = $arr_cmp["beneficiario"]; }	
			else
			{ 
				if ($arr_cmp["tipo"]=="P")
				 {  
					$ls_fuente = $arr_cmp["proveedor"]; 
				 }	
				 else 
				 {  
					$ls_fuente = "----------"; 
				 } 
			}
			$ls_status="";$ls_denominacion="";$ls_sc_cuenta="";
			if(!$this->io_int_spi->uf_spi_select_cuenta($arr_cmp["codemp"],$ls_cuenta,&$ls_status,&$ls_denominacion,&$ls_sc_cuenta))
			{  
			  return false;
			}
			 $this->io_int_spi->ib_AutoConta=true;
                         //  var_dump($lb_valido); die();
            $lb_valido = $this->io_int_spi->uf_int_spi_insert_movimiento($arr_cmp["codemp"],$arr_cmp["procedencia"],$arr_cmp["comprobante"],$arr_cmp["fecha"],
										                                 $arr_cmp["tipo"],$ls_fuente,$arr_cmp["proveedor"],$arr_cmp["beneficiario"],
																		 $ls_cuenta,$ls_procede_doc,$ls_documento,$ls_descripcion,
																		 $ls_mensaje,$ldec_monto_act,$ls_sc_cuenta,true,
																		 $as_codban,$as_ctaban,$ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,
																		 $ls_codestpro5,$ls_estcla);
            
            //

			
			if(!$lb_valido)
			{
				$this->io_msg->message("**No se registraron los detalles presupuestario".$this->io_int_spi->is_msg_error);
                $lb_valido=false;
				
			}
		}
		else
		{
		  $lb_valido=false;
		}
   }
   $ldec_monto = 0;
 return $lb_valido;
}


function uf_spi_valida_datos_movimiento($as_cuenta,$as_descripcion,$as_documento,$adec_monto)
{

	if (trim($as_cuenta)=="")
	{
		$this->is_msg_error = "Registre la Cuenta de Ingreso." ;
		return false;	
	}
	if(trim($as_descripcion)=="")
	{
		$this->is_msg_error = "Registre la Descripci�n del Movimiento." ;
		return false;
	}
	
	if(trim($as_documento) =="") 
	{
		$this->is_msg_error = "Registre el N� de documento.";
		return false;	
	}

 return true ;
}

function uf_guardar_movimientos_contable($arr_cmp,$as_cuenta,$as_procede_doc,$as_descripcion,$as_documento,
                                         $as_operacioncon,$adec_monto,$as_codban,$as_ctaban)
{
	$lb_valido=false;

	if(!$this->uf_scg_valida_datos_mov_contable($as_cuenta,$as_descripcion,$as_documento,$adec_monto))
	{ 
		$this->io_msg->message($this->is_msg_error);
	   return false;
	}
	$lb_valido = $this->io_int_scg->uf_scg_procesar_movimiento_cmp($arr_cmp["codemp"],$arr_cmp["procedencia"],$arr_cmp["comprobante"],$arr_cmp["fecha"],
                                                          $arr_cmp["proveedor"],$arr_cmp["beneficiario"],$arr_cmp["tipo"],$arr_cmp["tipo_comp"],
                                                          $as_cuenta,$as_procede_doc,$as_documento,$as_operacioncon,
														  $as_descripcion,$adec_monto,$as_codban,$as_ctaban);
										  
		
	if(!$lb_valido)
	{
		$this->io_msg->message("Error al registrar movimiento contable".$this->is_msg_error);
	}
	$ldec_monto = 0;
    return $lb_valido;
 }

	function uf_scg_valida_datos_mov_contable($as_cuenta,$as_descripcion,$as_documento,$adec_monto)
	{
		if (trim($as_cuenta)=="")
		{
			$this->is_msg_error = "Registre la Cuenta Gasto." ;
			return false;	
		}
		
		if(trim($as_descripcion)=="")
		{
			$this->is_msg_error = "Registre la Descripci�n del Movimiento." ;
			return false;
		}
		
		if(trim($as_documento) =="") 
		{
			$this->is_msg_error = "Registre el N� de documento." 	;
			return false;	
		}
		
		if($adec_monto == 0)
		{
			$this->is_msg_error = "Registre el Monto." ;	
			return false;
		} 
	
	   return true ;
	}
	//////////////////////////////////////////////////////////////////////////////////////////////////////////
	//	 Function:  uf_verificar_comprobante()
	//	   Access:  public
	//	Arguments:  $as_codemp-> empresa,$as_procedencia->procedencia,$as_comprobante->comprobante,$as_fecha
	//	  Returns:	booleano lb_existe
	//Description:  M�todo que verifica si existe o no el comprobante
	/////////////////////////////////////////////////////////////////////////////////////////////////////////
	function uf_verificar_comprobante($as_codemp,$as_procedencia,$as_comprobante)
	{
	   $lb_existe=false;
	   $ls_sql =   " SELECT comprobante ".
	               " FROM   sigesp_cmp ".
				   " WHERE codemp='".$as_codemp."' AND procede='".$as_procedencia."' AND comprobante='".$as_comprobante."' ";
	   $lr_result = $this->io_sql->select($ls_sql);
	   if($lr_result===false)
	   {
		  $this->is_msg_error="Error en delete Comprobante".$this->io_function->uf_convertirmsg($this->io_sql->message);
		  return false;
	   }
	   else  
	   { 
	      if($row=$this->io_sql->fetch_row($lr_result)) 
		  { 
		     $lb_existe=true;
		  }  
	  }
	  return $lb_existe;
	} // end function uf_select_comprobante
	
}//fin de la clase
?>