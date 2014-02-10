<?php
class sigesp_cfg_c_sucursales
 {
  	var $ls_sql="";
	var $io_msg_error;
	var $la_seguridad;
	
	
function sigesp_cfg_c_sucursales()//Constructor de la Clase.
{
	require_once("../shared/class_folder/class_sql.php");
	require_once("../shared/class_folder/sigesp_include.php");
	require_once("../shared/class_folder/class_mensajes.php");
	require_once("../shared/class_folder/class_funciones.php");
	require_once("../shared/class_folder/sigesp_c_seguridad.php");
	
	$this->io_seguridad= new sigesp_c_seguridad();		  
	$this->io_funciones= new class_funciones();
	$io_conect= new sigesp_include();
	$conn= $io_conect->uf_conectar();
	$this->ls_codemp= $_SESSION["la_empresa"]["codemp"];
	$this->io_sql= new class_sql($conn); //Instanciando  la clase sql
	$this->io_mensajes= new class_mensajes();
}

	//---------------------------------------------------------------------------------------------------------------------------
	function uf_select_sucursal($as_codsuc)
	{	
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_sucursal
		//		   Access: private
		//	    Arguments: as_codsuc  // Código de Sucursal
		//	      Returns: lb_valido True existe el registro ó False no si existe
		//	  Description: Funcion que velifica la existencia de la sucursal
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 11/12/2009 								Fecha Última Modificación : 
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql="SELECT codsuc ".
				"  FROM sigesp_sucursales  ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codsuc='".$as_codsuc."' ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Sucursales MÉTODO->uf_select_sucursal ERROR->".
										$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_valido=true;
			}
			$this->io_sql->free_result($rs_data);	
		}
		return $lb_valido;
	}
	//---------------------------------------------------------------------------------------------------------------------------

	//---------------------------------------------------------------------------------------------------------------------------
	function uf_select_estructura_sucursal($as_codsuc,$as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,
										   $as_codestpro5,$as_estcla)
	{	
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_estructura_sucursal
		//		   Access: private
		//	    Arguments: as_codsuc  // Código de Sucursal
		//                 as_codestpro1 // Código de Estructura Presupuestaria nivel 1
		//                 as_codestpro2 // Código de Estructura Presupuestaria nivel 2
		//                 as_codestpro3 // Código de Estructura Presupuestaria nivel 3
		//                 as_codestpro4 // Código de Estructura Presupuestaria nivel 4
		//                 as_codestpro5 // Código de Estructura Presupuestaria nivel 5
		//                 as_estcla // Estatus de Clasificacion
		//	      Returns: lb_valido True existe el registro ó False no si existe
		//	  Description: Funcion que velifica si la estructura presupuestaria indicada esta registrada para otra sucursal.
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 11/12/2009 								Fecha Última Modificación : 
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$as_codestpro1=str_pad($as_codestpro1,25,"0",STR_PAD_LEFT);  
		$as_codestpro2=str_pad($as_codestpro2,25,"0",STR_PAD_LEFT);  
		$as_codestpro3=str_pad($as_codestpro3,25,"0",STR_PAD_LEFT);  
		$as_codestpro4=str_pad($as_codestpro4,25,"0",STR_PAD_LEFT);  
		$as_codestpro5=str_pad($as_codestpro5,25,"0",STR_PAD_LEFT);  
		$ls_sql="SELECT codsuc ".
				"  FROM sigesp_sucursales  ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codestpro1='".$as_codestpro1."' ".
				"   AND codestpro2='".$as_codestpro2."' ".
				"   AND codestpro3='".$as_codestpro3."' ".
				"   AND codestpro4='".$as_codestpro4."' ".
				"   AND codestpro5='".$as_codestpro5."' ".
				"   AND estcla='".$as_estcla."' ".
				"   AND codsuc <> '".$as_codsuc."' ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Sucursales MÉTODO->uf_select_estructura_sucursal ERROR->".
										$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_valido=true;
			}
			$this->io_sql->free_result($rs_data);	
		}
		return $lb_valido;
	}
	//---------------------------------------------------------------------------------------------------------------------------

	//---------------------------------------------------------------------------------------------------------------------------
	function uf_insert_sucursal($as_codsuc,$as_nomsuc,$as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,
								 $as_estcla,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_sucursal
		//		   Access: private
		//	    Arguments: as_codsuc  // Código de Sucursal
		//                 as_nomsuc // Nombre de la Sucursal
		//                 as_codestpro1 // Código de Estructura Presupuestaria nivel 1
		//                 as_codestpro2 // Código de Estructura Presupuestaria nivel 2
		//                 as_codestpro3 // Código de Estructura Presupuestaria nivel 3
		//                 as_codestpro4 // Código de Estructura Presupuestaria nivel 4
		//                 as_codestpro5 // Código de Estructura Presupuestaria nivel 5
		//                 as_estcla // Estatus de Clasificacion
		//	      Returns: lb_valido True existe el registro ó False no si existe
		//	  Description: Funcion que velifica si la estructura presupuestaria indicada esta registrada para otra sucursal.
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 11/12/2009 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$as_codestpro1=str_pad($as_codestpro1,25,"0",STR_PAD_LEFT);  
		$as_codestpro2=str_pad($as_codestpro2,25,"0",STR_PAD_LEFT);  
		$as_codestpro3=str_pad($as_codestpro3,25,"0",STR_PAD_LEFT);  
		$as_codestpro4=str_pad($as_codestpro4,25,"0",STR_PAD_LEFT);  
		$as_codestpro5=str_pad($as_codestpro5,25,"0",STR_PAD_LEFT);  
		$ls_sql="INSERT INTO sigesp_sucursales (codemp, codsuc, nomsuc, codestpro1, codestpro2, codestpro3, codestpro4, ".
				"                               codestpro5,estcla)".
				"	  VALUES ('".$this->ls_codemp."','".$as_codsuc."','".$as_nomsuc."','".$as_codestpro1."',".
				" 			  '".$as_codestpro2."','".$as_codestpro3."','".$as_codestpro4."','".$as_codestpro5."',".
				"             '".$as_estcla."')";
		$this->io_sql->begin_transaction();				
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_sql->rollback();
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Solicitud MÉTODO->uf_insert_sucursal ERROR->".
										$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		}
		else
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="INSERT";
			$ls_descripcion ="Insertó la sucursal ".$as_codsuc." Con la estructura presupuestaria ".$as_codestpro1." - ".
							  $as_codestpro2." - ".$as_codestpro3." - ".$as_codestpro4." - ".$as_codestpro5." - ".
							  $as_estcla.". Asociado a la empresa ".$this->ls_codemp;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$this->io_sql->commit();
		}
		return $lb_valido;
	}// end function uf_insert_solicitud
	//---------------------------------------------------------------------------------------------------------------------------

	//---------------------------------------------------------------------------------------------------------------------------
	function uf_update_sucursal($as_codsuc,$as_nomsuc,$as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,
								 $as_estcla,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_update_sucursal
		//		   Access: private
		//	    Arguments: as_codsuc  // Código de Sucursal
		//                 as_nomsuc // Nombre de la Sucursal
		//                 as_codestpro1 // Código de Estructura Presupuestaria nivel 1
		//                 as_codestpro2 // Código de Estructura Presupuestaria nivel 2
		//                 as_codestpro3 // Código de Estructura Presupuestaria nivel 3
		//                 as_codestpro4 // Código de Estructura Presupuestaria nivel 4
		//                 as_codestpro5 // Código de Estructura Presupuestaria nivel 5
		//                 as_estcla // Estatus de Clasificacion
		//	      Returns: lb_valido True existe el registro ó False no si existe
		//	  Description: Funcion que velifica si la estructura presupuestaria indicada esta registrada para otra sucursal.
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 11/12/2009 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$as_codestpro1=str_pad($as_codestpro1,25,"0",STR_PAD_LEFT);  
		$as_codestpro2=str_pad($as_codestpro2,25,"0",STR_PAD_LEFT);  
		$as_codestpro3=str_pad($as_codestpro3,25,"0",STR_PAD_LEFT);  
		$as_codestpro4=str_pad($as_codestpro4,25,"0",STR_PAD_LEFT);  
		$as_codestpro5=str_pad($as_codestpro5,25,"0",STR_PAD_LEFT);  
		$ls_sql="UPDATE sigesp_sucursales ".
				"   SET nomsuc = '".$as_nomsuc."',".
				"       codestpro1 = '".$as_codestpro1."',".
				"       codestpro2 = '".$as_codestpro2."',".
				"       codestpro3 = '".$as_codestpro3."',".
				"       codestpro4 = '".$as_codestpro4."',".
				"       codestpro5 = '".$as_codestpro5."',".
				"       estcla = '".$as_estcla."' ".
				" WHERE codemp = '".$this->ls_codemp."'".
				"	AND codsuc = '".$as_codsuc."' ";
		$this->io_sql->begin_transaction();				
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_sql->rollback();
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Sucursales MÉTODO->uf_update_sucursal ERROR->".
										$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		}
		else
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="UPDATE";
			$ls_descripcion ="Modificó la sucursal ".$as_codsuc." Con la estructura presupuestaria ".$as_codestpro1." - ".
							  $as_codestpro2." - ".$as_codestpro3." - ".$as_codestpro4." - ".$as_codestpro5." - ".
							  $as_estcla.". Asociado a la empresa ".$this->ls_codemp;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$this->io_sql->commit();
		}
		return $lb_valido;
	}// end function uf_insert_solicitud
	//---------------------------------------------------------------------------------------------------------------------------

	//---------------------------------------------------------------------------------------------------------------------------
	function uf_delete_sucursales($as_codsuc,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_delete_sucursales
		//		   Access: private
		//	    Arguments: as_codsuc  // Código de Sucursal
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Funcion que elimina la sucursal indicada
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 11/12/2009 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="DELETE FROM sigesp_sucursales ".
				" WHERE codemp = '".$this->ls_codemp."' ".
				"   AND codsuc = '".$as_codsuc."' ";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Sucursales MÉTODO->uf_delete_sucursales ERROR->".
									    $this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		}
		if($lb_valido)
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="DELETE";
			$ls_descripcion ="Eliminó la sucursal ".$as_codsuc." Asociado a la empresa ".$this->ls_codemp;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////	
		}
		return $lb_valido;
	}// end function uf_delete_detalles
	//---------------------------------------------------------------------------------------------------------------------------

}//Fin de la Clase.
?>