<?php
class sigesp_spg_c_consolidacion_empresas
{
	var $is_msg_error;
	var $io_sql;
	var $io_sql_destino;
	var $io_include;
	var $io_int_scg;
	var $io_int_spg;
	var $io_msg;
	var $io_function;
	var $is_codemp;
	var $is_procedencia;
	var $is_comprobante;
	var $is_cod_prov;
	var $is_ced_ben;
	var $id_fecha;
	var $ii_tipo_comp;
	var $is_descripcion;
	var $is_tipo;
	var $ib_contabilizar;
	var $ib_spg_enlace_contable;
	var $io_connect;
	var $as_codban;
	var $as_ctaban;
	var $io_comprobante;
function sigesp_spg_c_consolidacion_empresas()
{
	require_once("../shared/class_folder/class_sql.php");
	require_once("../shared/class_folder/sigesp_include.php");
	require_once("../shared/class_folder/class_mensajes.php");
	require_once("../shared/class_folder/class_fecha.php");	
	require_once("../shared/class_folder/class_funciones.php");
	require_once("../shared/class_folder/sigesp_c_seguridad.php");
	require_once("../mis/class_folder/class_funciones_mis.php");
	require_once("sigesp_spg_c_comprobante.php");
	require_once("class_funciones_gasto.php");
    $this->io_include=new sigesp_include();
	$this->io_function=new class_funciones();
	$this->io_fecha=new class_fecha();	
	$this->io_connect=$this->io_include->uf_conectar();
    $this->io_sql=new class_sql($this->io_connect);
	$this->io_msg = new class_mensajes();
	$this->is_msg_error="";
	$this->io_seguridad= new sigesp_c_seguridad;
	$this->io_comprobante=new sigesp_spg_c_comprobante();
	$this->in_class_mis=new class_funciones_mis();
	$this->dts_empresa=$_SESSION["la_empresa"];
	$this->ls_codemp=$_SESSION["la_empresa"]["codemp"];
	$this->ls_loncodestpro1 = $_SESSION["la_empresa"]["loncodestpro1"];
	$this->ls_loncodestpro2 = $_SESSION["la_empresa"]["loncodestpro2"];
	$this->ls_loncodestpro3 = $_SESSION["la_empresa"]["loncodestpro3"];
	$this->ls_loncodestpro4 = $_SESSION["la_empresa"]["loncodestpro4"];
	$this->ls_loncodestpro5 = $_SESSION["la_empresa"]["loncodestpro5"];
}
/**********************************************************************************************************************************/


function uf_conectar_destino($as_hostname, $as_login, $as_password,$as_database,$as_gestor)
{
 /*******************************************************************************************************************************
  *	     Function: uf_conectar_destino
  *		 Access: public 
  *      Argument: 
  *               $as_hostname       // Direccion Ip del equipo que contiene la Base de Datos Destino
  *               $as_login          // Login del Gesto de la Base de Datos Destino
  *               $as_password       // Password de la Base de Datos Destino
  *               $as_database       // Nombre de la Base de Datos Destino
  *               $as_gestor         // Gestor de la Base de Datos Destino
  *	     Description: Este método genera la conexion a la Base de Datos Destino
  *	     Returns: datos de los detalles
  *      Fecha: 18/09/2009
  *	     Creado Por: Ing. Arnaldo Suárez
 *********************************************************************************************************************************/
 $this->io_connect_destino=$this->io_include->uf_conectar_otra_bd($as_hostname, $as_login, $as_password,$as_database,$as_gestor);	
 $this->io_sql_destino=new class_sql($this->io_connect_destino);
}

function uf_liberar_conexion_destino()
{
  /*******************************************************************************************************************************
  *	     Function: uf_liberar_conexion_destino
  *		 Access: public 
  *      Argument:
  *	     Description: Libera la conexión a la Base de Datos Destino
  *	     Returns: 
  *      Fecha: 18/09/2009
  *	     Creado Por: Ing. Arnaldo Suárez
 *********************************************************************************************************************************/
  unset($this->io_connect_destino);
  unset($this->io_sql_destino);
}

function uf_obtener_detalles_gasto_consolidado($ad_feccomprobante,$as_estructura)
{
 /*******************************************************************************************************************************
  *	     Function: uf_obtener_detalles_gasto_consolidado
  *		 Access: public 
  *      Argument: 
  *               $ad_feccomprobante   // Fecha del Comprobante de Consolidación
  *               $as_estructura       // Conjunto de la(s) Estructura de Gasto a buscar
  *	     Description: Este método retorna un consolidado de todos los movimientos presupuesatarios hechos en la base de datos destino
  *	     Returns: datos de los detalles
  *      Fecha: 17/09/2009
  *	     Creado Por: Ing. Arnaldo Suárez
 *********************************************************************************************************************************/
  
  $ls_cadena = "";
  switch(strtoupper($_SESSION["ls_gestor"]))
  {
   case "MYSQLT":
   					$ls_cadena = " AND CONCAT(spg_dt_cmp.codestpro1,spg_dt_cmp.codestpro2,spg_dt_cmp.codestpro3,spg_dt_cmp.codestpro4,spg_dt_cmp.codestpro5,spg_dt_cmp.estcla) IN (".$as_estructura.")";
					break;
   default:         $ls_cadena = " AND spg_dt_cmp.codestpro1||spg_dt_cmp.codestpro2||spg_dt_cmp.codestpro3||spg_dt_cmp.codestpro4||spg_dt_cmp.codestpro5||spg_dt_cmp.estcla IN (".$as_estructura.")";
  }
  
  
  
  $ls_sql = "SELECT                                                                               ".
			"	  spg_dt_cmp.codestpro1,                                                          ".
			"	  spg_dt_cmp.codestpro2,                                                          ". 
			"	  spg_dt_cmp.codestpro3,                                                          ". 
			"	  spg_dt_cmp.codestpro4,                                                          ".
			"	  spg_dt_cmp.codestpro5,                                                          ". 
			"	  spg_dt_cmp.estcla,                                                              ". 
			"	  spg_dt_cmp.spg_cuenta,                                                          ". 
			"	  spg_dt_cmp.operacion,                                                           ".
			"	  SUM(spg_dt_cmp.monto) as monto                                                  ".
			"	FROM                                                                              ".
			"	  sigesp_cmp,                                                                     ". 
			"	  spg_dt_cmp                                                                      ".
			"	WHERE                                                                             ". 
		    "	  spg_dt_cmp.codemp = sigesp_cmp.codemp AND                                       ".
			"	  spg_dt_cmp.procede = sigesp_cmp.procede AND                                     ".
			"	  spg_dt_cmp.comprobante = sigesp_cmp.comprobante AND                             ".
			"	  spg_dt_cmp.fecha = sigesp_cmp.fecha AND                                         ".
			"	  spg_dt_cmp.codban = sigesp_cmp.codban AND                                       ".
			"	  spg_dt_cmp.ctaban = sigesp_cmp.ctaban AND                                       ". 
			"	  sigesp_cmp.procede NOT IN ('SPGAPR','SPGREC','SPGTRA','SPGCRA', 'SPGINS') AND   ".
			"     sigesp_cmp.fecha <= '".$ad_feccomprobante."' ".$ls_cadena."                     ".
			"	GROUP BY 1,2,3,4,5,6,7,8                                                          ".
			"	ORDER BY 1,2,3,4,5,6,7,8                                                          ";
			
 $rs_detalles=$this->io_sql_destino->select($ls_sql);
 return $rs_detalles;
}

function uf_obtener_codaltempresa()
{
 /*******************************************************************************************************************************
  *	     Function: uf_obtener_codaltempresa
  *		 Access: public 
  *      Argument: 
  *               $as_hostname       // Direccion Ip del equipo que contiene la Base de Datos Destino
  *               $as_login          // Login del Gesto de la Base de Datos Destino
  *               $as_password       // Password de la Base de Datos Destino
  *               $as_database       // Nombre de la Base de Datos Destino
  *               $as_gestor         // Gestor de la Base de Datos Destino
  *	     Description: Este método retorna el código alterno de la empresa destino
  *	     Returns:    codigo alterno de la emprea
  *      Fecha: 18/09/2009
  *	     Creado Por: Ing. Arnaldo Suárez
 *********************************************************************************************************************************/
  $ls_codaltemp="";
  $ls_sql = "SELECT                    ".
			"	  sigesp_empresa.codaltemp ".
			"	FROM                   ".
			"	  sigesp_empresa       "; 
  $resultado=$this->io_sql_destino->select($ls_sql);
 
  while(!$resultado->EOF)
  { 
   $ls_codaltemp = $resultado->fields["codaltemp"];
   $resultado->MoveNext();
  }
  
  $this->io_sql_destino->free_result($resultado);
  
  return str_pad($ls_codaltemp,4,'0',STR_PAD_LEFT);
}

function uf_obtener_estructura_base_datos($as_base_datos)
{
 /*******************************************************************************************************************************
  *	     Function: uf_obtener_estructura_base_datos
  *		 Access: public 
  *      Argument:  
  *               $as_base_datos // Nombre de la Base de Datos
  *	     Description: Este método retorna las estructaura presupuestarias asociadas a una base de datos que consolida
  *	     Returns: datos de las estructuras
  *      Fecha: 18/09/2009
  *	     Creado Por: Ing. Arnaldo Suárez
 *********************************************************************************************************************************/
 $ls_estructuras="";
 $i=1;
 $ls_sql = "SELECT                                ".
		   "     sigesp_consolidacion.codestpro1, ". 
		   "     sigesp_consolidacion.codestpro2, ".  
		   "	 sigesp_consolidacion.codestpro3, ".  
		   "	 sigesp_consolidacion.codestpro4, ". 
		   "	 sigesp_consolidacion.codestpro5, ". 
		   "	 sigesp_consolidacion.estcla      ".
		   "   FROM                               ". 
		   "	 sigesp_consolidacion             ".
		   "   WHERE                              ".
		   "	 sigesp_consolidacion.codemp = '".$this->dts_empresa['codemp']."' AND ".
		   "	 sigesp_consolidacion.nombasdat = '".$as_base_datos."'";	    
 $rs_estructuras=$this->io_sql->select($ls_sql);
 
 while(!$rs_estructuras->EOF)
 {
   if($i==1)
   {
    $ls_estructuras .= "'".$rs_estructuras->fields["codestpro1"].$rs_estructuras->fields["codestpro2"].$rs_estructuras->fields["codestpro3"].$rs_estructuras->fields["codestpro4"].$rs_estructuras->fields["codestpro5"].$rs_estructuras->fields["estcla"]."'";
   }
   else
   {
    $ls_estructuras .= ",'".$rs_estructuras->fields["codestpro1"].$rs_estructuras->fields["codestpro2"].$rs_estructuras->fields["codestpro3"].$rs_estructuras->fields["codestpro4"].$rs_estructuras->fields["codestpro5"].$rs_estructuras->fields["estcla"]."'";
   }
   $rs_estructuras->MoveNext();
   $i++;
 }
 
 $this->io_sql->free_result($rs_estructuras);
 
 return $ls_estructuras;
}

function uf_generar_comprobante_consolidacion($ad_fecha,$aa_seguridad)
{
 /*******************************************************************************************************************************
  *	     Function: uf_generar_comprobante_consolidacion
  *		 Access: public 
  *      Argument: 
  *               $ad_fecha          // Fecha en la que se va a generar el comprobante
  *	     Description: Este método genera un comprobante de gasto con el consolidado de la base de datos de consolidacion
  *	     Returns: datos de las estructuras
  *      Fecha: 18/09/2009
  *	     Creado Por: Ing. Arnaldo Suárez
  *********************************************************************************************************************************/
    
	$ls_codemp=$this->dts_empresa["codemp"];
	$lb_valido=$this->io_fecha->uf_valida_fecha_periodo($ad_fecha,$ls_codemp);
	
	if(!($lb_valido))
	{
		$this->io_msg->message($this->io_fecha->is_msg_error);
	}
	else
	{
		$ls_codaltemp= $this->uf_obtener_codaltempresa();
		if($ls_codaltemp!="0000")
		{
		 	$ls_comprobante="CONEJEFINGAS-".substr($ls_codaltemp,2,2);
		 	$lb_existe = $this->uf_verificar_existencia_comprobante_consolidacion($ls_comprobante);
			if(!$lb_existe)
			{
				$ls_operacion=$_POST["operacion"];
				$_SESSION["fechacomprobante"]=$ad_fecha;
				$ls_procedencia="SPGCMP";
				$ls_descripcion="COMPROBANTE DE CONSOLIDACIÓN DE LA EJECUCIÓN PRESUPUESTARIA DE GASTO  DE LA BASE DE DATOS ".$this->uf_generar_descripcion_bd($_SESSION["ls_database_destino"])." AL ".$ad_fecha;
				$ls_tipo="-";
				$this->io_comprobante->io_int_int->is_tipo           = "-";
				$this->io_comprobante->io_int_int->is_cod_prov       = "----------";
				$this->io_comprobante->io_int_int->is_ced_ben        = "----------";
				$this->io_comprobante->io_int_int->ib_procesando_cmp = false;
				$this->io_comprobante->io_int_int->id_fecha          = $this->io_function->uf_convertirdatetobd($ad_fecha);
				$ls_codban     = "---";
				$ls_ctaban     = "-------------------------";
				$ls_rendfon    = 0;
				$ls_existe     = "N"; 
				$ls_fuentefin="--";
				$ls_comprobante2=$ls_comprobante;
				$ls_comprobante3=$ls_comprobante;
				$this->io_comprobante->io_int_spg->io_sql->begin_transaction();
				
				$ls_estructuras = $this->uf_obtener_estructura_base_datos($_SESSION["ls_database_destino"]);
				
				$rs_detconsolidacion =$this->uf_obtener_detalles_gasto_consolidado($this->io_function->uf_convertirdatetobd($ad_fecha),$ls_estructuras);	
				
				$li_numrows = $this->io_sql_destino->num_rows($rs_detconsolidacion);
				
				if($li_numrows > 0)
				{
				
					$lb_valido=$this->io_comprobante->uf_guardar_automatico($ls_comprobante,$ad_fecha,$ls_procedencia,$ls_descripcion,
																			$this->io_comprobante->io_int_int->is_cod_prov,
																			$this->io_comprobante->io_int_int->is_ced_ben,$ls_tipo,1,
																			$ls_codban,$ls_ctaban, $ls_rendfon, $ls_fuentefin, $ls_existe);
					if ($lb_valido)
					{ 
						$la_comprobante["comprobante"]   = $ls_comprobante;
						$ld_fecdb                        = $this->io_function->uf_convertirdatetobd($ad_fecha);
						$la_comprobante["fecha"]      	 = $ld_fecdb;
						$la_comprobante["procedencia"]	 = $ls_procedencia;
						$la_comprobante["descripcion"]	 = $ls_descripcion;
						$la_comprobante["proveedor"]  	 = $this->io_comprobante->io_int_int->is_cod_prov;
						$la_comprobante["beneficiario"]  = $this->io_comprobante->io_int_int->is_ced_ben;
						$la_comprobante["tipo"]          = $ls_tipo;
						$la_comprobante["codemp"]        = $ls_codemp;
						$la_comprobante["tipo_comp"]     = 1;
						
						while(!$rs_detconsolidacion->EOF)
						{
							$ld_disponible = 0;
							$ls_cuenta	   = $rs_detconsolidacion->fields["spg_cuenta"]; 
							$ls_codestpro1 = $rs_detconsolidacion->fields["codestpro1"]; 
							$ls_codestpro2 = $rs_detconsolidacion->fields["codestpro2"];
							$ls_codestpro3 = $rs_detconsolidacion->fields["codestpro3"];
							$ls_codestpro4 = $rs_detconsolidacion->fields["codestpro4"];
							$ls_codestpro5 = $rs_detconsolidacion->fields["codestpro5"];
							$ls_estcla	   = $rs_detconsolidacion->fields["estcla"];
							$ld_disponible = 0;
							$ls_operacion  = $rs_detconsolidacion->fields["operacion"];
							$ld_monto      = $rs_detconsolidacion->fields["monto"];
							
							$ls_codestpro=$ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5;
							$ls_formato_estructura="";
							$this->in_class_mis->uf_formatoprogramatica($ls_codestpro,&$ls_formato_estructura);    
										
							$lb_valido=$this->io_comprobante->uf_spg_select_disponibilidad($ls_cuenta,$ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,$ls_codestpro5,
																				  $ld_disponible,$ls_operacion,$ld_monto,$ls_estcla);
							if(($ld_disponible<$ld_monto)&&($lb_valido))
							{
							   $this->io_msg->message("La Cuenta  ".trim($ls_cuenta)."  en la Estructura Presupuestaria ".$ls_formato_estructura." con operación ".$ls_operacion." y monto ".$ld_monto." proveniente de la Base de Datos ".strtoupper($_SESSION["ls_database_destino"])." no tiene disponibilidad " ); 
							}
							else
							{
								$lb_valido=$this->io_comprobante->uf_guardar_movimientos($la_comprobante,$ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,$ls_codestpro5,
																				$ls_cuenta,$ls_procedencia,$ls_descripcion,$ls_comprobante,
																				$ls_operacion,0,$ld_monto,"C",$ls_codban,$ls_ctaban,
																				$ls_estcla);
								if (!$lb_valido)
								{
									$lb_valido=false;						
									//return false;
								}			
							} 
							
						 $rs_detconsolidacion->MoveNext();
						}
						$this->io_sql_destino->free_result($rs_detconsolidacion);		
					}	
					
				
					if ($lb_valido)
					{  
						$this->io_comprobante->io_int_spg->io_sql->commit();			
						$this->io_msg->message("El Comprobante ".$ls_comprobante." asociado a la Base de Datos ".$this->uf_generar_descripcion_bd($_SESSION["ls_database_destino"])." se registro Exitosamente..." );
						/////////////////////////////////         SEGURIDAD               /////////////////////////////
						$ls_evento="INSERT";
						$ls_desc_event="Generó el comprobante presupuestario de consolidación ".$as_comprobante." asociado a la Base de Datos ".strtoupper($_SESSION["ls_database_destino"])." de fecha ".$ad_fecha;
						$as_variable= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],$aa_seguridad["ventanas"],$ls_desc_event);
						////////////////////////////////         SEGURIDAD               ////////////////////////////// 
					}
					else
					{
						$this->io_comprobante->io_int_spg->io_sql->rollback();			
						$this->io_msg->message($this->io_comprobante->is_msg_error);			
						$this->io_msg->message(" Error al guardar Comprobante ".$ls_comprobante." asociado a la Base de Datos ".$this->uf_generar_descripcion_bd($_SESSION["ls_database_destino"])); 			
					}		
				}
				else
				{
				 $this->io_msg->message("No existen detalles presupuestarios para Consolidación de la Base de Datos ".$this->uf_generar_descripcion_bd($_SESSION["ls_database_destino"]).", este comprobante no se generará" ); 
				}
			}
			else
			{
			 $this->io_msg->message("Ya existe un Comprobante de Consolidación con Número ".$ls_comprobante." para la Base de Datos ".$this->uf_generar_descripcion_bd($_SESSION["ls_database_destino"]).", este comprobante no se generará" ); 
			}
		}
		else
		{
		 $this->io_msg->message("La Base de Datos de Consolidación ".$_SESSION["ls_database_destino"]." no tiene configurado código alterno de empresa, el comprobante de consolidación no se generará");
		}
		
	}// fin del else
 
 return $lb_valido;
}


function uf_generar_descripcion_bd($as_nombasdat)
{
 /*******************************************************************************************************************************
  *	     Function: uf_generar_descripcion_bd
  *		 Access: public 
  *      Argument: 
  *               $as_nombasdat          // Nombre físico de la Base de Datos
  *	     Description: Este genera la descripción de la Base de Datos de la que se está creando la consolidación
  *	     Returns: descripción de la Base de Datos que está consolidando
  *      Fecha: 21/09/2009
  *	     Creado Por: Ing. Arnaldo Suárez
  *********************************************************************************************************************************/
 
    $ls_basedatos="";
	$arreglo= explode("_",$as_nombasdat);
	$total=count($arreglo);
	for($i=1;$i<$total;$i++)
	{
	 if($i==1)
	 {
	  $ls_basedatos .= strtoupper($arreglo[$i]); 
	 }
	 else
	 {
	  $ls_basedatos .= " ".strtoupper($arreglo[$i]);  
	 }
	}
 return $ls_basedatos;
}

function uf_verificar_existencia_comprobante_consolidacion($as_comprobante)
{
 /*******************************************************************************************************************************
  *	     Function: uf_verificar_existencia_comprobante_consolidacion
  *		 Access: public 
  *      Argument: 
  *               $as_comprobante          // Codigo del Comprobare de Consolidacion
  *	     Description: Verifica que no exista ya el comprobante de consolidacion para la Base de Datos que está consolidando,
  *                   ya que sólo puede existir un comprobante de consolidación por Base de Datos
  *	     Returns: True o False
  *      Fecha: 21/09/2009
  *	     Creado Por: Ing. Arnaldo Suárez
  *********************************************************************************************************************************/

 $lb_existe = false;
 $li_cantidad = 0;
 $ls_sql = "SELECT                                           ".
		   "     COUNT(sigesp_cmp.comprobante) as existe    ".
		   "   FROM                                         ". 
		   "	 sigesp_cmp                                 ".
		   "   WHERE                                        ".
		   "	 sigesp_cmp.codemp = '".$this->dts_empresa['codemp']."' AND ".
		   "	 sigesp_cmp.comprobante = '".$as_comprobante."'"; 
		   
 $resultado=$this->io_sql->select($ls_sql);
 
 while(!$resultado->EOF)
  { 
   $li_cantidad = $resultado->fields["existe"];
   $resultado->MoveNext();
  }
  
  if($li_cantidad > 0)
  {
   $lb_existe = true;
  }

 return $lb_existe;
}

function uf_select_comprobantes_consolidacion(&$ao_object,&$ai_totrows)
{
 /*******************************************************************************************************************************
  * 	 Function: uf_select_comprobantes_consolidacion
  *      Access: public
  *      Arguments: 
  *		           $ao_object  // Arreglo de objetos
  * 		       $ai_totrows  // total del Filas
  *	     Returns: 
  *	     Description: Verifica que no exista ya el comprobante de consolidacion para la Base de Datos que está consolidando,
  *                   ya que sólo puede existir un comprobante de consolidación por Base de Datos
  *	     Returns: True o False
  *      Fecha: 21/09/2009
  *	     Creado Por: Ing. Arnaldo Suárez
  *******************************************************************************************************************************/
		$ai_totrows=0;
		$lb_valido=true;
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$ls_sql = "SELECT                                  ".
				  "   DISTINCT sigesp_cmp.comprobante,     ".
				  "   sigesp_cmp.procede,                  ". 
				  "	  sigesp_cmp.fecha,                    ".
				  "   sigesp_cmp.codban,                   ". 
				  "	  sigesp_cmp.ctaban,                   ".
				  "	  sigesp_cmp.descripcion               ".    
				  " FROM                                   ".
				  "	  sigesp_cmp,                          ". 
				  "	  spg_dt_cmp                           ".
				  "	WHERE                                  ". 
				  "	  spg_dt_cmp.codemp = sigesp_cmp.codemp AND           ".
				  "	  spg_dt_cmp.procede = sigesp_cmp.procede AND         ".
				  "	  spg_dt_cmp.comprobante = sigesp_cmp.comprobante AND ".
				  "	  spg_dt_cmp.fecha = sigesp_cmp.fecha AND             ".
				  "	  spg_dt_cmp.codban = sigesp_cmp.codban AND           ".
				  "	  spg_dt_cmp.ctaban = sigesp_cmp.ctaban AND           ".
				  "   sigesp_cmp.comprobante like 'CONEJEFINGAS-%'        ".
				  " ORDER BY sigesp_cmp.comprobante,sigesp_cmp.fecha";
		$rs_data = $this->io_sql->select($ls_sql);
		
		if($rs_data===false)
		{
			$lb_valido=false;
            $this->io_msg->message("CLASE->Consolidacion_Empresas MÉTODO->uf_select_comprobantes_consolidacion ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
		}
		else
		{
		   $li_numrows=$this->io_sql->num_rows($rs_data);
		   if($li_numrows > 0)
		   {
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$ai_totrows++;
				$ls_comprobante=rtrim($row["comprobante"]);
				$ld_fecha=$this->io_function->uf_formatovalidofecha($row["fecha"]);
				$ld_fecha=$this->io_function->uf_convertirfecmostrar($ld_fecha);
				$ls_procede=rtrim($row["procede"]);
				$ls_descripcion=rtrim($row["descripcion"]);				
				$ls_codban = $row["codban"];
				$ls_ctaban = $row["ctaban"];				
				$ao_object[$ai_totrows][1] = "<input type=checkbox name=selcmp".$ai_totrows." 		  id=selcmp".$ai_totrows." onChange='javascript: cambiar_valor(".$ai_totrows.");' ><input name=txtselcmp".$ai_totrows." type=hidden id=txtselcmp".$ai_totrows." readonly>";
				$ao_object[$ai_totrows][2] = "<input type=text     name=txtcomprobante".$ai_totrows." id=txtcomprobante".$ai_totrows." value='".$ls_comprobante."' class=sin-borde readonly style=text-align:center size=17 maxlength=15><input name=hidcodban".$ai_totrows." id=hidcodban".$ai_totrows." type=hidden  value='".$ls_codban."' readonly>";
				$ao_object[$ai_totrows][3] ="<input  type=text 	   name=txtprocede".$ai_totrows."     id=txtprocede".$ai_totrows."     value='".$ls_procede."'     class=sin-borde readonly style=text-align:center size=15 maxlength=12><input name=hidctaban".$ai_totrows." id=hidctaban".$ai_totrows." type=hidden  value='".$ls_ctaban."' readonly>";
				$ao_object[$ai_totrows][4] = "<input type=text 	   name=txtfecha".$ai_totrows."       id=txtfecha".$ai_totrows."       value='".$ld_fecha."'       class=sin-borde readonly style=text-align:center size=20 maxlength=10>";
				$ao_object[$ai_totrows][5] = "<input type=text 	   name=txtconcepto".$ai_totrows."    id=txtconcepto".$ai_totrows."    value='".$ls_descripcion."' class=sin-borde readonly style=text-align:left   size=80 maxlength=250 title='".$ls_descripcion."'>";			
				$ao_object[$ai_totrows][6] = "<div align='center'><a href=javascript:uf_verdetalle('".str_replace(" ","___",$ls_comprobante)."','".$ls_procede."');><img src=../shared/imagebank/mas.gif alt=Detalle width=12 height=24 border=0></a></div>";
											 
			}
			$this->io_sql->free_result($rs_data);
		   }
		   else
		   {
		    $ai_totrows=$ai_totrows+1;
		    $ao_object[$ai_totrows][1]=  "<input type=checkbox name=selcmp".$ai_totrows." 		  id=selcmp".$ai_totrows." onChange='javascript: cambiar_valor(".$ai_totrows.");' ><input name=txtselcmp".$ai_totrows." type=hidden id=txtselcmp".$ai_totrows." readonly>";
			$ao_object[$ai_totrows][2] = "<input type=text     name=txtcomprobante".$ai_totrows." id=txtcomprobante".$ai_totrows." value=''  class=sin-borde readonly style=text-align:center size=17 maxlength=15><input name=hidcodban".$ai_totrows." id=hidcodban".$ai_totrows." type=hidden  value='' readonly>";
			$ao_object[$ai_totrows][3] = "<input type=hidden   name=txtprocede".$ai_totrows."     id=txtprocede".$ai_totrows."     value=''  class=sin-borde readonly style=text-align:center size=15 maxlength=12><input name=hidctaban".$ai_totrows." id=hidctaban".$ai_totrows." type=hidden  value='' readonly>";
			$ao_object[$ai_totrows][4] = "<input type=text 	   name=txtfecha".$ai_totrows."       id=txtfecha".$ai_totrows."       value=''  class=sin-borde readonly style=text-align:center size=20 maxlength=10>";
			$ao_object[$ai_totrows][5] = "<input type=text     name=txtconcepto".$ai_totrows."    id=txtconcepto".$ai_totrows."	   value=''  class=sin-borde readonly style=text-align:left   size=80 maxlength=250>";			
			$ao_object[$ai_totrows][6] = "<div align='center'><img src=../shared/imagebank/mas.gif alt=Detalle width=12 height=24 border=0></div>";
			
		   }
		}		
		return $lb_valido;
	}// end function uf_select_comprobantes_consolidacion

function uf_cargar_bdconsolidacion()
{
 /*******************************************************************************************************************************
  * 	 Function: uf_cargar_bdconsolidacion
  *      Access: public
  *      Arguments: 
  *	     Returns: Bases de datos consolidadoras
  *	     Description: Funcion que retorna las Bases de Datos consolidadoras configuradas en CONFIGURACION
  *	     Returns: True o False
  *      Fecha: 21/09/2009
  *	     Creado Por: Ing. Arnaldo Suárez
  *******************************************************************************************************************************/
	  $ls_sql = " SELECT                                     ".
	            "    DISTINCT sigesp_consolidacion.nombasdat ". 
				"   FROM sigesp_consolidacion                ".
				   " ORDER BY sigesp_consolidacion.nombasdat ";		   
	   $rs_data = $this->io_sql->select($ls_sql);
	   if($rs_data===false)
	   {
		  $this->is_msg_error="Error en Select de Base de Datos para Consolidacion".$this->io_function->uf_convertirmsg($this->io_sql->message);
	   }

	  return $rs_data;
} // fin de uf_cargar_bdconsolidacion

function uf_revertir_comprobante_consolidacion($as_codemp,$as_procedencia,$as_comprobante, $ad_fecha,$as_tipo,$as_ced_ben,$as_cod_prov,$as_codban,$as_ctaban,$aa_seguridad)
{
 /*******************************************************************************************************************************
  * 	 Function: uf_revertir_comprobante_consolidacion
  *      Access: public
  *      Arguments: 
  *	     Returns: Bases de datos consolidadoras
  *	     Description: Funcion que revierte el comprobante de consolidación
  *	     Returns: True o False
  *      Fecha: 22/09/2009
  *	     Creado Por: Ing. Arnaldo Suárez
  *******************************************************************************************************************************/
	
	
	$lb_valido=false;
	
	$this->io_comprobante->io_int_int->is_tipo          = $as_tipo;
	$this->io_comprobante->io_int_int->is_cod_prov      = $as_ced_ben;
	$this->io_comprobante->io_int_int->is_ced_ben       = $as_cod_prov;
	$this->io_comprobante->io_int_int->ib_procesando_cmp= false;
	$this->io_comprobante->io_int_int->id_fecha=$this->io_function->uf_convertirdatetobd($ad_fecha);

	$lb_valido=$this->io_comprobante->io_int_int->uf_init_delete($as_codemp,$as_procedencia,$as_comprobante, $this->io_comprobante->io_int_int->id_fecha,$as_tipo,$this->io_comprobante->io_int_int->is_ced_ben,$this->io_comprobante->io_int_int->is_cod_prov,false,$as_codban,$as_ctaban);

	if (!$lb_valido) 
    {	
	   $this->io_msg->message("El Comprobante Nro. ".$as_comprobante." no existe");	
	}	
	else
	{
	    $lb_valido = $this->io_comprobante->io_int_int->uf_int_init_transaction_begin();
		if(!$lb_valido)
		{
			$this->io_msg->message($this->io_comprobante->io_int_int->is_msg_error);
		}	
		if($lb_valido)
		{	
			$lb_valido = $this->io_comprobante->io_int_int->uf_init_end_transaccion_integracion($aa_seguridad);
			if (!$lb_valido)
			{
				$this->io_msg->message("Error".$this->io_comprobante->io_int_int->is_msg_error);
				$this->io_comprobante->io_int_int->io_sql->rollback();
			}
			else
			{
				$this->io_msg->message("El Comprobante de Consolidación Nro. ".$as_comprobante." ha sido eliminado satisfactoriamente");		
				/////////////////////////////////         SEGURIDAD               /////////////////////////////
				$ls_evento="DELETE";
				$ls_desc_event="Eliminó el comprobante presupuestario de consolidación ".$as_comprobante." de fecha ".$ad_fecha." y procedencia ".$as_procedencia;
				$as_variable= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],$aa_seguridad["ventanas"],$ls_desc_event);
				////////////////////////////////         SEGURIDAD               //////////////////////////////
				$lb_valido=$this->io_comprobante->io_int_int->io_sql->commit();
			}
		}
    }		
}

}// Fin de la Clase
?>