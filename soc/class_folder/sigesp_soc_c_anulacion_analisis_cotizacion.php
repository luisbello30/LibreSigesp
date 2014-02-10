<?php
class sigesp_soc_c_anulacion_analisis_cotizacion
{
  function sigesp_soc_c_anulacion_analisis_cotizacion($as_path)
  {
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//	     Function: sigesp_soc_c_anulacion_analisis_cotizacion
	//		   Access: public 
	//	  Description: Constructor de la Clase
	//	   Creado Por: Ing. Néstor Falcón.
	// Fecha Creación: 09/06/2007 								Fecha Última Modificación : 03/06/2007 
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        require_once($as_path."shared/class_folder/sigesp_include.php");
		require_once($as_path."shared/class_folder/class_sql.php");
		require_once($as_path."shared/class_folder/class_funciones.php");
		require_once($as_path."shared/class_folder/sigesp_c_seguridad.php");
		require_once($as_path."shared/class_folder/class_mensajes.php");
		$io_include			= new sigesp_include();
		$io_conexion		= $io_include->uf_conectar();
		$this->io_sql       = new class_sql($io_conexion);	
		$this->io_mensajes  = new class_mensajes();		
		$this->io_funciones = new class_funciones();	
		$this->io_seguridad = new sigesp_c_seguridad();
		$this->ls_codemp    = $_SESSION["la_empresa"]["codemp"];
  }

function uf_load_analisis_cotizacion($as_numanacot,$ad_fecdes,$ad_fechas,$as_codpro)
{
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//	     Function: uf_load_ordenes_compra
//         Access: public
//      Argumento: 
//   $as_numordcom //Número de la Orden de Compra (Bien o Servicio.)
//      $ad_fecdes //Fecha desde el cual buscaremos las Ordenes de Compra.
//      $ad_fechas //Fecha hasta el cual buscaremos las Ordenes de Compra.
//      $as_codpro //Código del Proveedor asociado a la Orden de Compra.
//	      Returns: Retorna un resulset
//    Description: Funcion que carga la Ordenes de Compra dispuestas para el proceso de Anulación. 
//	   Creado Por: Ing. Néstor Falcón.
// Fecha Creación: 06/03/2007							Fecha Última Modificación : 09/06/2007
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 
  $ls_straux = "";
  if (!empty($ad_fecdes) && !empty($ad_fechas))
     {
	   $ld_fecdes = $this->io_funciones->uf_convertirdatetobd($ad_fecdes);
       $ld_fechas = $this->io_funciones->uf_convertirdatetobd($ad_fechas);
	   $ls_straux = " AND soc_ordencompra.fecordcom BETWEEN '".$ld_fecdes."' AND '".$ld_fechas."'"; 
	 }
  $ls_sql = "SELECT soc_ordencompra.numanacot,soc_ordencompra.cod_pro,".
            "       soc_ordencompra.fecordcom,soc_analisicotizacion.obsana,soc_analisicotizacion.fecanacot,".
			"       COALESCE(soc_ordencompra.numanacot,'-') as numanacot,".
            "       soc_ordencompra.estcondat,soc_ordencompra.montot,rpc_proveedor.nompro".			
		    "  FROM soc_ordencompra , rpc_proveedor,soc_analisicotizacion".
			" WHERE soc_ordencompra.codemp='".$this->ls_codemp."'".
			"   AND soc_ordencompra.numanacot like '%".$as_numanacot."%'".
			"   AND soc_ordencompra.cod_pro like '%".$as_codpro."%'".
			"       $ls_straux".
			"   AND soc_ordencompra.numordcom <> '000000000000000'".
			"   AND soc_ordencompra.numanacot <> '-'".
			"   AND soc_ordencompra.estcom= '3'".
			"   AND soc_analisicotizacion.estana= '1'".
			"   AND soc_ordencompra.codemp=rpc_proveedor.codemp".
  	  	    "   AND soc_ordencompra.cod_pro=rpc_proveedor.cod_pro".
  	  	    "   AND soc_ordencompra.codemp=soc_analisicotizacion.codemp".
  	  	    "   AND soc_ordencompra.numanacot=soc_analisicotizacion.numanacot".
			" ORDER BY soc_ordencompra.numordcom ASC";
	//print $ls_sql."<br>";
	$rs_data=$this->io_sql->select($ls_sql);
	if($rs_data===false)
	{
		$lb_valido=false;
		$this->io_mensajes	->message("CLASE->sigesp_soc_c_anulacion_orden_compra.MÉTODO->uf_load_ordenes_compra.ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
	}
	return $rs_data;
} // end  function uf_load_ordenes_compra

function uf_update_estatus_analisis_cotizacion($ai_totrows,$aa_seguridad)
{
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//	     Function: uf_update_estatus_analisis_cotizacion
//         Access: public
//      Argumento: $ai_totrows = Total de filas dispuestas para su anulación.
//                 $aa_seguridad = Arreglo cargado con la informacion de la pantalla, usuario, entre otros.
//     $as_totrows //Total de Ordenes de Compra.
//	      Returns: Retorna un resulset
//    Description: Funcion que carga la Ordenes de Compra dispuestas para el proceso de Anulación. 
//	   Creado Por: Ing. Néstor Falcón.
// Fecha Creación: 06/03/2007							Fecha Última Modificación : 09/06/2007
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

  $lb_valido = true;
  if ($ai_totrows>0)
     {
	   $this->io_sql->begin_transaction();
	   for ($i=1;$i<=$ai_totrows;$i++)
	       {
			 if (array_key_exists("chk".$i,$_POST))
			    {
			      $ls_numanacot = str_pad($_POST["txtnumord".$i],15,0,0);
				  $ls_numanacot = trim($_POST["hidnumanacot".$i]);
				  
			      $ls_sql       = "UPDATE soc_analisicotizacion 			     ".
			                      "   SET estana='2'      			     ".
			                      " WHERE codemp='".$this->ls_codemp."'  ".
							      "   AND numanacot='".$ls_numanacot."'  ";
				  $rs_data = $this->io_sql->execute($ls_sql);
				  if ($rs_data===false)
					 {
					   $lb_valido = false;
					   $this->io_mensajes->message("CLASE->uf_update_estatus_analisis_cotizacion.php->MÉTODO->uf_update_estatus_orden_compra.ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
					   break;
					 }
				  else
					 {
						/////////////////////////////////         SEGURIDAD               /////////////////////////////////////////		
						$ls_descripcion ="Anuló el Analisis de Cotizacion Nro. $ls_numanacot asociada a la empresa ".$this->ls_codemp;
						$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
														$aa_seguridad["sistema"],"UPDATE",$aa_seguridad["logusr"],
														$aa_seguridad["ventanas"],$ls_descripcion);
						/////////////////////////////////         SEGURIDAD               //////////////////////////////////////////				   
					   $this->ls_supervisor=$_SESSION["la_empresa"]["envcorsup"];
						if($this->ls_supervisor!=0)
						{
							$ls_fromname="Anulación Analisis de Cotización";
							$ls_bodyenv="Se le envia la notificación de actualización en el modulo de SOC, se anuló el analisis de cotización N°.. ";
							$ls_nomper=$_SESSION["la_nomusu"];
							$lb_valido_3= $this->io_seguridad->uf_envio_correo_activo($ls_fromname,$ls_numanacot,$ls_bodyenv,$ls_nomper);
						}
						/////////////////////////////////         SEGURIDAD               /////////////////////////////	
					   $lb_valido = $this->uf_liberar_cotizaciones($ls_numanacot,$aa_seguridad);
					 }
				}
		   }
	    if ($lb_valido)
	       {
		     $this->io_sql->commit();
			 $this->io_mensajes->message("Operación realizada con éxito !!!");
		     $this->io_sql->close();

		   } 
	    else
		   {
		     $this->io_sql->rollback();
			 $this->io_mensajes->message("Error  en Operación !!!");
		     $this->io_sql->close();
		   }
	 }
  return $lb_valido;
}

function uf_liberar_cotizaciones($as_numanacot,$aa_seguridad)
{
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//	     Function: uf_liberar_cotizaciones
	//         Access: public
	//      Argumento: 
	//   $as_numordcom //Número de la Orden de Compra (Bien o Servicio.)
	//      $as_codpro //Código del Proveedor asociado a la Orden de Compra.
	//   $as_tipordcom //Tipo de Orden de Compra (Bien o Servicio).
	//	      Returns: Retorna un resulset
	//    Description: Funcion que carga la Ordenes de Compra dispuestas para el proceso de Anulación. 
	//	   Creado Por: Ing. Néstor Falcón.
	// Fecha Creación: 06/06/2007							Fecha Última Modificación : 09/06/2007
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$lb_valido= true;
	$ls_sql= "SELECT numcot,cod_pro ".
			 "  FROM soc_cotxanalisis ".
			 " WHERE codemp='".$this->ls_codemp."'".
			 "   AND numanacot='".$as_numanacot."'";
	$rs_datos = $this->io_sql->select($ls_sql);
	if ($rs_datos===false)
	{
		$lb_valido = false;
		$this->io_mensajes->message("CLASE->sigesp_soc_c_anulacion_orden_compra.php->MÉTODO->uf_liberar_cotizaciones.ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		echo $this->io_sql->message;
	}		    
	else
	{
		while ($row=$this->io_sql->fetch_row($rs_datos))
		{
			$ls_numcot = $row["numcot"];
			$ls_codpro = $row["cod_pro"];
			$lb_valido = $this->uf_update_estatus_cotizacion($as_numanacot,$ls_numcot,$ls_codpro,$aa_seguridad);
		}
	}
  return $lb_valido;
}

function uf_update_estatus_cotizacion($as_numanacot,$as_numcot,$as_codpro,$aa_seguridad)
{
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//	     Function: uf_update_estatus_incorporacion_item
	//         Access: public
	//      Argumento: 
	//   $as_numordcom //Número de la Orden de Compra (Bien o Servicio.)
	//      $as_codpro //Código del Proveedor asociado a la Orden de Compra.
	//   $as_tipordcom //Tipo de Orden de Compra (Bien o Servicio).
	//	      Returns: Retorna un resulset
	//    Description: Funcion que carga la Ordenes de Compra dispuestas para el proceso de Anulación. 
	//	   Creado Por: Ing. Néstor Falcón.
	// Fecha Creación: 06/06/2007							Fecha Última Modificación : 09/06/2007
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$lb_valido = true;
	$ls_sql="UPDATE soc_cotizacion SET estcot='0' ".
			" WHERE codemp='".$this->ls_codemp."'".
			"   AND numcot='".$as_numcot."'".
			"   AND cod_pro='".$as_codpro."'";
	$rs_dato = $this->io_sql->execute($ls_sql);
	if ($rs_dato===false)
	{
		$lb_valido = false;
		$this->io_mensajes->message("CLASE->sigesp_soc_c_anulacion_orden_compra.php->MÉTODO->uf_update_estatus_incorporacion_item.ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
	}
	else
	{
		/////////////////////////////////         SEGURIDAD               /////////////////////////////////////////		
		$ls_descripcion ="Liberó la Cotizacion $as_numcot asociada al Analisis de Cotizacion Nro. $as_numanacot asociada a la empresa ".$this->ls_codemp;
		$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
										$aa_seguridad["sistema"],"UPDATE",$aa_seguridad["logusr"],
										$aa_seguridad["ventanas"],$ls_descripcion);
		/////////////////////////////////         SEGURIDAD               //////////////////////////////////////////				   
	}
  return $lb_valido; 
}

//-----------------------------------------------------------------------------------------------------------------------------------	
function uf_nivel_aprobacion_usu($as_codusu,$as_codtipniv)
{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_validar_estatus_solicitud
		//		   Access: private
		//	    Arguments: as_numsol  //  Número de Solicitud
		//				   as_estsol  //  Estatus de la Solicitud
		// 	      Returns: lb_existe True si existe ó False si no existe
		//	  Description: Funcion que valida el estatus de aprobacion de la solicitud 
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 26/02/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_existe=true;
		$as_codniv="";
		$ls_sql="SELECT codasiniv ".
				"  FROM sss_niv_usuarios ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codusu='".$as_codusu."' ".
				"   AND codtipniv='".$as_codtipniv."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->sigesp_soc_c_aprobacion_analisis_cotizacion.php->uf_nivel_aprobacion_usu ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_existe=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$as_codniv=$row["codasiniv"];
			}
			$this->io_sql->free_result($rs_data);	
		}
		return $as_codniv;
	}// end function uf_validar_estatus_solicitud
//-----------------------------------------------------------------------------------------------------------------------------------
function uf_nivel_aprobacion_montohasta($as_codniv)
{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_validar_estatus_solicitud
		//		   Access: private
		//	    Arguments: as_numsol  //  Número de Solicitud
		//				   as_estsol  //  Estatus de la Solicitud
		// 	      Returns: lb_existe True si existe ó False si no existe
		//	  Description: Funcion que valida el estatus de aprobacion de la solicitud 
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 26/02/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_existe=true;
		$ai_monhas=0;
		$ls_sql="SELECT monnivhas ".
				"  FROM sigesp_nivel ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codniv='".$as_codniv."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->sigesp_soc_c_aprobacion_analisis_cotizacion.php-> ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_existe=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ai_monhas=$row["monnivhas"];
			}
			$this->io_sql->free_result($rs_data);	
		}
		return $ai_monhas;
	}// end function uf_validar_estatus_solicitud
//-----------------------------------------------------------------------------------------------------------------------------------
function uf_nivel($as_codniv)
{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_validar_estatus_solicitud
		//		   Access: private
		//	    Arguments: as_numsol  //  Número de Solicitud
		//				   as_estsol  //  Estatus de la Solicitud
		// 	      Returns: lb_existe True si existe ó False si no existe
		//	  Description: Funcion que valida el estatus de aprobacion de la solicitud 
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 26/02/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_existe=true;
		$as_nivel="";
		$ls_sql="SELECT codniv ".
				"  FROM sigesp_asig_nivel ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codasiniv='".$as_codniv."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->sigesp_soc_c_aprobacion_analisis_cotizacion.php-> ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_existe=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$as_nivel=$row["codniv"];
			}
			$this->io_sql->free_result($rs_data);	
		}
		return $as_nivel;
	}// end function uf_validar_estatus_solicitud
//-----------------------------------------------------------------------------------------------------------------------------------

}
?>
