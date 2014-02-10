<?php
class sigesp_snorh_c_guarderia
{
	var $io_sql;
	var $io_mensajes;
	var $io_funciones;
	var $io_seguridad;
	var $ls_codemp;
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function sigesp_snorh_c_guarderia()
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: sigesp_snorh_c_diaferiado
		//		   Access: public (sigesp_snorh_d_familiar)
		//	  Description: Constructor de la Clase
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		require_once("../shared/class_folder/sigesp_include.php");
		$io_include=new sigesp_include();
		$io_conexion=$io_include->uf_conectar();
		require_once("../shared/class_folder/class_sql.php");
		$this->io_sql=new class_sql($io_conexion);	
		require_once("../shared/class_folder/class_mensajes.php");
		$this->io_mensajes=new class_mensajes();		
		require_once("../shared/class_folder/class_funciones.php");
		$this->io_funciones=new class_funciones();		
		require_once("../shared/class_folder/sigesp_c_seguridad.php");
		$this->io_seguridad= new sigesp_c_seguridad();
        $this->ls_codemp=$_SESSION["la_empresa"]["codemp"];
	}// end function sigesp_snorh_c_guarderia
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_destructor()
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_destructor
		//		   Access: public (sigesp_snorh_d_familiar)
		//	  Description: Destructor de la Clase
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		unset($io_include);
		unset($io_conexion);
		unset($this->io_sql);	
		unset($this->io_mensajes);		
		unset($this->io_funciones);		
		unset($this->io_seguridad);
        unset($this->ls_codemp);
	}// end function uf_destructor
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_guarderia($as_codper,$as_codguar)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_familiar
		//		   Access: private
		//	    Arguments: as_codper  // Código de Personal
		//				   as_cedfam  // Cédula del Familiar
		// 	      Returns: lb_existe True si existe ó False si no existe
		//	  Description: Funcion que verifica si el familiar está registrado
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_existe=true;
		$ls_sql="SELECT codper ".
		        "  FROM sno_guarderias ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codper='".$as_codper."'".
				"   AND codguar='".$as_codguar."'";
				
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
        	$this->io_mensajes->message("CLASE->Guarderia MÉTODO->uf_select_guarderia ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_existe=false;
		}
		else
		{
			if(!$row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_existe=false;
			}
			$this->io_sql->free_result($rs_data);
		}
		return $lb_existe;
	}// end function uf_select_guarderia
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_guarderia($as_codper,$as_nomper,$as_codguar,$as_benef,$ai_montoguar,$as_denbene,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_familiar
		//		   Access: private
		//	    Arguments: as_codper  // Código del Personal
		//				   as_cedfam  // Cedula
		//				   as_nomfam  // Nombre
		//				   as_apefam  // Apellido
		//				   as_sexfam  // Sexo
		//				   ad_fecnacfam  // Fecha Nacimiento
		//				   as_nexfam  // Nexo 
		//				   ai_estfam  // Estudio del familiar
		//				   ai_hcfam  // si el familiar tiene hc
		//				   ai_hcmfam //  si el personal tiene hcm
		//                 ai_hijesp // indica si es un hijo especial
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Funcion que inserta el familiar
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="INSERT INTO sno_guarderias".
				"(codemp,codper,codguar,nomper,monto,cedbene,nombene)VALUES".
				"('".$this->ls_codemp."','".$as_codper."','".$as_codguar."','".$as_nomper."','".$ai_montoguar."',".
				"'".$as_benef."','".$as_denbene."')";
		$this->io_sql->begin_transaction()	;
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
 			$lb_valido=false;
        	$this->io_mensajes->message("CLASE->Guarderia MÉTODO->uf_insert_guarderia ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$this->io_sql->rollback();
			print ($this->io_sql->message);
		}
		else
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="INSERT";
			$ls_descripcion ="Insertó la Guarderia ".$as_codguar." asociado al personal ".$as_codper;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////	
			if($lb_valido)
			{	
				$this->io_mensajes->message("La Guarderia fue Registrada.");
				$this->io_sql->commit();
			}
			else
			{
				$lb_valido=false;
        		$this->io_mensajes->message("CLASE->Guarderia MÉTODO->uf_insert_guarderia ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
				$this->io_sql->rollback();
			}
		}
		return $lb_valido;
	}// end function uf_insert_guarderia
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_update_guarderia($as_codper,$as_nomper,$as_codguar,$as_benef,$ai_montoguar,$as_denbene,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_update_guarderia
		//		   Access: private
		//	    Arguments: as_codper  // Código del Personal
		//				   as_cedfam  // Cedula
		//				   as_nomfam  // Nombre
		//				   as_apefam  // Apellido
		//				   as_sexfam  // Sexo
		//				   ad_fecnacfam  // Fecha Nacimiento
		//				   as_nexfam  // Nexo 
		//				   ai_estfam  // Estudio del familiar
		//				   ai_hcfam  // si el familiar tiene hc
		//				   ai_hcmfam //  si el personal tiene hcm
		//                 ai_hijesp // indica si es un hijo especial
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el update ó False si hubo error en el update
		//	  Description: Funcion que actualiza el familiar
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="UPDATE sno_guarderias ".
				"   SET cedbene='".$as_benef."', ".
				"		nombene='".$as_denbene."', ".
				"		monto='".$ai_montoguar."' ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codper='".$as_codper."'".
				"   AND codguar='".$as_codguar."'";
				
		$this->io_sql->begin_transaction();
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$lb_valido=false;
        	$this->io_mensajes->message("CLASE->Guarderia MÉTODO->uf_update_guarderia ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$this->io_sql->rollback();
		}
		else
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="UPDATE";
			$ls_descripcion ="Actualizó la Guarderia ".$as_codguar." asociado al personal ".$as_codper;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////	
			if($lb_valido)
			{	
				$this->io_mensajes->message("La Guarderia fue Actualizado.");
				$this->io_sql->commit();
			}
			else
			{
				$lb_valido=false;
	        	$this->io_mensajes->message("CLASE->Guarderia MÉTODO->uf_update_guarderia ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
				$this->io_sql->rollback();
			}
		}
		return $lb_valido;
	}// end function uf_update_familiar
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_guardar($as_existe,$as_codper,$as_nomper,$as_codguar,$as_benef,$ai_montoguar,$as_denbene,$aa_seguridad)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_guardar
		//		   Access: public (sigesp_snorh_d_familiar)
		//	    Arguments: as_codper  // Código del Personal
		//				   as_cedfam  // Cedula
		//				   as_nomfam  // Nombre
		//				   as_apefam  // Apellido
		//				   as_sexfam  // Sexo
		//				   ad_fecnacfam  // Fecha Nacimiento
		//				   as_nexfam  // Nexo 
		//				   ai_estfam // Estudio del familiar 
		//				   ai_hcfam  // si el familiar tiene hc
		//				   ai_hcmfam //  si el personal tiene hcm
		//                 ai_hijesp // indica si es un hijo especial
		//                 ai_bonjug // indica si el hijo recibe bono de juguete
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el update ó False si hubo error en el update
		//	  Description: Funcion que actualiza el familiar
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ai_montoguar=str_replace(".","",$ai_montoguar);
		$ai_montoguar=str_replace(",",".",$ai_montoguar);	
		$lb_valido=false;		
		switch ($as_existe)
		{
			case "FALSE":
				if($this->uf_select_guarderia($as_codper,$as_codguar)===false)
				{
					$lb_valido=$this->uf_insert_guarderia($as_codper,$as_nomper,$as_codguar,$as_benef,$ai_montoguar,$as_denbene,$aa_seguridad);
				}
				else
				{
					$this->io_mensajes->message("La Guarderia ya existe, no la puede incluir.");
				}
				break;
							
			case "TRUE":
				if(($this->uf_select_guarderia($as_codper,$as_codguar)))
				{
					$lb_valido=$this->uf_update_guarderia($as_codper,$as_nomper,$as_codguar,$as_benef,$ai_montoguar,$as_denbene,$aa_seguridad);
				}
				else
				{
					$this->io_mensajes->message("La Guarderia no existe, no la puede actualizar.");
				}
				break;
		}		
		return $lb_valido;
	}// end function uf_guardar
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_delete_guarderia($as_codper,$as_codguar,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_delete_familiar
		//		   Access: public (sigesp_snorh_d_familiar)
		//	    Arguments: as_codper  // Código del Personal
		//				   as_cedfam  // Cedula
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el delete ó False si hubo error en el delete
		//	  Description: Funcion que elimina el familiar
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="DELETE ".
				"  FROM sno_guarderias ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codper='".$as_codper."'".
				"   AND codguar='".$as_codguar."'";
				
       	$this->io_sql->begin_transaction();
	   	$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$lb_valido=false;
        	$this->io_mensajes->message("CLASE->Guarderia MÉTODO->uf_delete_guarderia ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$this->io_sql->rollback();
		}
		else
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="DELETE";
			$ls_descripcion ="Elimino la Guarderia ".$as_codguar." asociado al personal ".$as_codper;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////	
			if($lb_valido)
			{	
				$this->io_mensajes->message("La Guarderia fue Eliminada.");
				$this->io_sql->commit();
			}
			else
			{
				$lb_valido=false;
 		       	$this->io_mensajes->message("CLASE->Guarderia MÉTODO->uf_delete_guarderia ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
				$this->io_sql->rollback();
			}
		}
		return $lb_valido;
    }// end function uf_delete_familiar
	//-----------------------------------------------------------------------------------------------------------------------------------
}
?>