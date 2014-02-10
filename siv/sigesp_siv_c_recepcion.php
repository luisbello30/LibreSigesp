<?php
require_once("../shared/class_folder/class_sql.php");
class sigesp_siv_c_recepcion
{
	var $obj="";
	var $io_sql;
	var $siginc;
	var $con;

	function sigesp_siv_c_recepcion()
	{
		require_once("../shared/class_folder/class_datastore.php");
		require_once("../shared/class_folder/class_mensajes.php");
		require_once("../shared/class_folder/sigesp_include.php");
		require_once("../shared/class_folder/sigesp_c_seguridad.php");
		require_once("../shared/class_folder/class_funciones_db.php");
		require_once("../shared/class_folder/sigesp_c_reconvertir_monedabsf.php");
		
		$in=               new sigesp_include();
		$this->con=        $in->uf_conectar();
		$this->io_sql=     new class_sql($this->con);
		$this->seguridad=  new sigesp_c_seguridad();
		$this->fun=        new class_funciones_db($this->con);
		$this->io_msg =    new class_mensajes();
		$this->DS=         new class_datastore();
		$this->io_funcion= new class_funciones();
		$this->ls_codemp=$_SESSION["la_empresa"]["codemp"];

	}

	function uf_siv_select_recepcion($as_codemp,$as_numordcom)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_select_recepcion
		//         Access: public (sigesp_siv_p_recepcion)
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $as_numordcom // numero de la orden de compra/factura
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que verifica que exista una entrada de suministo a almacen en la tabla de  siv_recepcion
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 10/02/2006							Fecha Última Modificación : 10/02/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql = "SELECT * FROM siv_recepcion  ".
				  " WHERE codemp='".$as_codemp."'".
				  "   AND numordcom='".$as_numordcom."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->recepcion MÉTODO->uf_siv_select_recepcion ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_valido=true;
				$this->io_sql->free_result($rs_data);
			}
			else
			{
				$lb_valido=false;
			}
		}
		return $lb_valido;
	} // end function uf_siv_select_recepcion

	function uf_siv_insert_recepcion($as_codemp,$as_numordcom,$as_codpro,$as_codalm,$ad_fecrec,$as_obsrec,$as_codusu,$as_estpro,$as_estrec,&$as_numconrec,$aa_seguridad) 
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_insert_recepcion
		//         Access: public (sigesp_siv_p_recepcion)
		//      Argumento: $as_codemp    // codigo de empresa               $as_numordcom // numero de la orden de compra/factura
		// 				   $as_codpro    // codigo de proveedor			    $as_codalm    // codigo de almacen
		//				   $ad_fecrec    // fecha de recepcion              $as_obsrec    // observacion de la recepcion
		//				   $as_codusu    // codigo del usuario	 			$aa_seguridad // arreglo de registro de seguridad
		//				   $as_estpro    // estatus de la procedencia: 0--> Factura, 1--> Orden de compra
		//				   $as_estrec    // estatus de la recepcion:   0--> Parcial, 1--> Completa
		//				   $as_numconrec // comprobante (numero concecutivo para hacer unica la recepcion)
		//	      Returns: Retorna un Booleano
		//    Description: Funcion  que inserta  los  datos  maestros  de  una  entrada  de  suministros a almacen  y genera
		//				   el numero  de  comprobante  de  la  recepcion  de manera que puedan existir varias recepciones para una
		//				   misma orden de compra, en la tabla siv_recepcion
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 10/02/2006							Fecha Última Modificación : 10/02/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$io_fun=  new class_funciones_db($this->con);
		$ls_emp="";
		$ls_tabla="siv_recepcion";
		$ls_columna="numconrec";
		$ls_estrevrec="1";
		$as_numconrec=$io_fun->uf_generar_codigo($ls_emp,$as_codemp,$ls_tabla,$ls_columna);
		$ls_sql="INSERT INTO siv_recepcion (codemp,numordcom,numconrec,cod_pro,codalm,fecrec,obsrec,codusu,estpro,estrec,estrevrec,estapr)".
				" VALUES ('".$as_codemp."','".$as_numordcom."','".$as_numconrec."','".$as_codpro."','".$as_codalm."','".$ad_fecrec."','".$as_obsrec."',".
				"         '".$as_codusu."','".$as_estpro."','".$as_estrec."','".$ls_estrevrec."','0')";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_msg->message("CLASE->recepcion MÉTODO->uf_siv_insert_recepcion ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;

		}
		else
		{
				$lb_valido=true;
				/////////////////////////////////         SEGURIDAD               /////////////////////////////		
				$ls_evento="INSERT";
				$ls_descripcion ="Insertó una Entrada de Suminisros a almacen proveniente del Documento ".$as_numordcom.", y fue enviado al Almacen ".$as_codalm.
								 " Asociado a la Empresa ".$as_codemp;
				$ls_variable= $this->seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
												$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
												$aa_seguridad["ventanas"],$ls_descripcion);
				/////////////////////////////////         SEGURIDAD               /////////////////////////////		
		}
		if(($lb_valido)&&($as_estrec==1)&&($as_estpro==0))
		{
			$lb_valido=$this->uf_siv_update_ordencompra($as_codemp,$as_numordcom,$aa_seguridad);
		}
		return $lb_valido;
	} // end function uf_siv_insert_recepcion

	function uf_siv_select_dt_recepcion($as_codemp,$as_numordcom)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_select_dt_recepcion
		//         Access: public (sigesp_siv_p_recepcion)
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $as_numordcom // numero de la orden de compra/factura
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que verifica si existe detalles asociados a un maestro de recepcion de suministros a almacen
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 10/02/2006							Fecha Última Modificación : 10/02/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT * FROM siv_dt_recepcion".
				" WHERE codemp='". $as_codemp ."'".
				"   AND numordcom NOT IN (SELECT numordcom FROM siv_recepcion WHERE estrevrec='0')".
				"   AND numordcom='". $as_numordcom ."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->recepcion MÉTODO->uf_siv_select_dt_recepcion ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_valido=true;
				$this->io_sql->free_result($rs_data);
			}
			else
			{
				$lb_valido=false;
			}
		}
		return $lb_valido;
	} // end function uf_siv_select_dt_recepcion
	
	function uf_siv_insert_dt_recepcion($as_codemp,$as_numordcom,$as_codart,$as_unidad,$ai_canart,$ai_penart,
	                                    $ai_preuniart,$ai_monsubart,$ai_montotart,$ai_orden,$ai_canoriart,
										$as_numconrec,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_insert_dt_recepcion
		//         Access: public (sigesp_siv_p_recepcion)
		//      Argumento: $as_codemp    // codigo de empresa               $as_numordcom // numero de la orden de compra/factura
		// 				   $as_codart    // codigo de articulo			    $as_unidad    // codigo de unidad M-->Mayor D->Detal
		//				   $ai_canart    // cantidad recibida de articulos  $ai_penart    // cantidad pendiente de articulos por recibir
		//				   $ai_preuniart // precio unitario del articulo	$ai_monsubart // monto sub-total por articulo
		//				   $ai_montotart // monto total de articulo			$ai_orden     // orden consecutivo de registro
		//				   $as_estrec    // estatus de la recepcion:   0--> Parcial, 1--> Completa
		//				   $ai_canoriart // codigo de procedencia del documento
		//				   $as_numconrec // comprobante (numero concecutivo para hacer unica la recepcion)
		//				   $aa_seguridad // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que inserta un detalle de recepcion de articulos a almacen sociado a su respectivo
		//				   maestro en la tabla de  siv_dt_recepcion
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 10/02/2006							Fecha Última Modificación : 10/02/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
			$ls_sql="INSERT INTO siv_dt_recepcion (codemp,numordcom,numconrec,codart,unidad,canart,penart,preuniart,monsubart,montotart,".
					"                              orden,canoriart)".
					" VALUES ('".$as_codemp."','".$as_numordcom."','".$as_numconrec."','".$as_codart."','".$as_unidad."','".$ai_canart."',".
					"         '".$ai_penart."','".$ai_preuniart."','".$ai_monsubart."','".$ai_montotart."','".$ai_orden."',".
					"         '".$ai_canoriart."')"; 
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{print $this->io_sql->message;
				$this->io_msg->message("CLASE->recepcion MÉTODO->uf_siv_insert_dt_recepcion ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
				$lb_valido=false;
			}
			else
			{
				$lb_valido=true;
				/////////////////////////////////         SEGURIDAD               /////////////////////////////		
				$ls_evento="INSERT";
				$ls_descripcion ="Permitió una entrada a ".$ai_canart." Articulos ".$as_codart." Asociado a la Orden de Compra ".$as_numordcom." de la Empresa ".$as_codemp;
				$ls_variable= $this->seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
												$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
												$aa_seguridad["ventanas"],$ls_descripcion);
				/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			}
			if($lb_valido)
			{
				$lb_valido=$this->uf_siv_update_ultimocosto($as_codemp,$as_codart,$ai_preuniart,$aa_seguridad);
				if($lb_valido)
				{
					$lb_valido=$this->uf_siv_actualizar_costo_promedio($as_codemp,$as_codart,$aa_seguridad);
				}
			}
		    return $lb_valido;
	}  // end   function uf_siv_insert_dt_recepcion

	function uf_siv_obtener_dt_pendiente($as_codemp,$as_numordcom,&$ai_totrows,&$ao_object)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_obtener_dt_pendiente
		//         Access: private
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $as_numordcom // numero de la orden de compra/factura
		//  			   $ai_totrows   // total de filas encontradas
		//  			   $ao_object    // arreglo de objetos para pintar el grid
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que busca los articulos asociados a una orden de compra ordenados por el campo "orden" en la
		//				   tabla de soc_dt_bienes, y por articulo busca en la tabla siv_dt_recepcion los pendientes asociados a esos 
		//				   articulos para luego imprimirlos en el grid de la pagina exepto aquellos que ya se recibieron por completo.
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 10/02/2006							Fecha Última Modificación : 10/02/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT codart,MIN(orden) as orden".
				" FROM soc_dt_bienes".
				" WHERE codemp='". $as_codemp ."'".
				"   AND numordcom='". $as_numordcom ."'".
				" GROUP BY codemp, numordcom, estcondat, codart".
				" ORDER BY orden";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$lb_valido=false;
			$this->io_msg->message("CLASE->recepcion MÉTODO->uf_siv_obtener_dt_pendiente ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			return false;
		}
		else
		{
			$ls_gestor=$_SESSION["ls_gestor"];
			$ai_totrows=0;
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_aux="";
				$ls_codart=$row["codart"];
				$la_alternos=$this->uf_obtener_alternos($ls_codart);
				if(!empty($la_alternos))
				{
					$li_total=count($la_alternos);
					if($li_total>0)
					{
						for($li_i=1;$li_i<=$li_total;$li_i++)
						{
							$ls_aux=$ls_aux."OR siv_dt_recepcion.codart='".$la_alternos[$li_i]."'";
						}
					}
				}
				$ls_aux=$ls_aux. ")";
			  $ls_sql="SELECT siv_dt_recepcion.*,siv_articulo.codunimed,siv_articulo.denart, ".
					  "     (SELECT unidad FROM siv_unidadmedida ".
					  "	      WHERE siv_unidadmedida.codunimed = siv_articulo.codunimed) AS unidades,".
					  "     (SELECT denart FROM siv_articulo as padre ".
					  "       WHERE padre.codart=siv_articulo.codartpri) AS denartpri,".
					  "     (SELECT unidad FROM siv_articulo as padre,siv_unidadmedida ".
					  "       WHERE padre.codart=siv_articulo.codartpri".
					  "         AND padre.codunimed=siv_unidadmedida.codunimed) AS unidadespri".
					  "  FROM siv_dt_recepcion, siv_recepcion, siv_articulo ".
					  " WHERE  siv_dt_recepcion.codemp=siv_recepcion.codemp".
					  "   AND siv_dt_recepcion.codart=siv_articulo.codart".
					  "   AND siv_dt_recepcion.numordcom=siv_recepcion.numordcom".
					  "   AND siv_dt_recepcion.numconrec=siv_recepcion.numconrec ".
					  "   AND siv_dt_recepcion.codemp='". $as_codemp ."'".
					  "   AND siv_dt_recepcion.numordcom='". $as_numordcom ."'".
					  "   AND siv_recepcion.estrec=0".
					  "   AND (siv_dt_recepcion.codart='". $ls_codart ."'".
					  $ls_aux.
					  " ORDER BY siv_dt_recepcion.numconrec DESC LIMIT  1";
				$rs_data1=$this->io_sql->select($ls_sql);
				if($rs_data1===false)
				{
					$lb_valido=false;
					$this->io_msg->message("CLASE->recepcion MÉTODO->uf_siv_obtener_dt_pendiente ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
				}
				else
				{
					while($row=$this->io_sql->fetch_row($rs_data1))
					{
						$ls_codartaux=    $row["codart"];
						$ls_denart=    $row["denartpri"];
						if($ls_denart=="")
						{
							$li_penart=    $row["penart"];
							$ls_denart=    $row["denart"];
						}
						else
						{
							$li_penart=    $row["canoriart"];
						}
						$ls_unidad=    $row["unidad"];
						$li_unidad=    $row["unidadespri"];
						if($li_unidad=="")
						{
							$li_unidad=    $row["unidades"];
						}
						$li_preuniart= $row["preuniart"];
						$li_canoriart= $row["canoriart"];
						$li_canart=    "";
						$li_montotart= "";
						$lb_valido=$this->uf_select_articulosrelacionados($as_codemp,$as_numordcom,$ls_codart,$ls_codartaux,&$li_adicionales);
						$li_penart=$li_penart-$li_adicionales;
						switch ($ls_unidad) 
						{
							case "M":
								$ls_unidadaux="Mayor";
								break;
							case "D":
								$ls_unidadaux="Detal";
								break;
						}
						if($li_penart!=0.00)
						{
							$ai_totrows=$ai_totrows+1;
							$ao_object[$ai_totrows][1]="<input name=txtdenart".$ai_totrows."    type=text id=txtdenart".$ai_totrows."    class=sin-borde size=15 maxlength=50 value='".$ls_denart."' readonly>".
													   "<input name=txtcodart".$ai_totrows." type=hidden id=txtcodart".$ai_totrows." class=sin-borde size=20 maxlength=20 value='".$ls_codart."' readonly>".
												       "<input name=txtcodartpri".$ai_totrows." type=hidden id=txtcodartpri".$ai_totrows." class=sin-borde size=20 readonly>";
							$ao_object[$ai_totrows][2]="<input name=txtunidad".$ai_totrows."    type=text id=txtunidad".$ai_totrows."    class=sin-borde size=12 maxlength=12 value='".$ls_unidadaux."' readonly><input name='hidunidad".$ai_totrows."' type='hidden' id='hidunidad".$ai_totrows."' value='".$li_unidad."'>";
							$ao_object[$ai_totrows][3]="<input name=txtcanoriart".$ai_totrows." type=text id=txtcanoriart".$ai_totrows." class=sin-borde size=12 maxlength=12 value='".number_format ($li_canoriart,2,",",".")."'  readonly>";
							$ao_object[$ai_totrows][4]="<input name=txtcanart".$ai_totrows."    type=text id=txtcanart".$ai_totrows."    class=sin-borde size=10 maxlength=12 value='".$li_canart."'  onKeyPress=return(ue_formatonumero(this,'.',',',event)); onBlur='javascript: ue_calcularpendiente(".$ai_totrows.");'>";
							$ao_object[$ai_totrows][5]="<input name=txtpenart".$ai_totrows."    type=text id=txtpenart".$ai_totrows."    class=sin-borde size=10 maxlength=12 value='".number_format ($li_penart,2,",",".")."'  onKeyPress=return(ue_formatonumero(this,'.',',',event)); readonly><input name='hidpendiente".$ai_totrows."' type='hidden' id='hidpendiente".$ai_totrows."' value='".$li_penart."'>";
							$ao_object[$ai_totrows][6]="<input name=txtpreuniart".$ai_totrows." type=text id=txtpreuniart".$ai_totrows." class=sin-borde size=14 maxlength=15 value='".number_format ($li_preuniart,2,",",".")."' readonly>";
							$ao_object[$ai_totrows][7]="<input name=txtmontotart".$ai_totrows." type=text id=txtmontotart".$ai_totrows." class=sin-borde size=14 maxlength=15 value='".$li_montotart."' readonly>";
							$ao_object[$ai_totrows][8]="";

						}
					}//while
				}//else
			}//while($row=$this->io_sql->fetch_row($li_exec))
		}
		if ($ai_totrows==0)
		{$lb_valido=false;}
		$this->io_sql->free_result($rs_data);
		return $lb_valido;
	} // end function uf_siv_obtener_dt_pendiente
  //-----------------------------------------------------------------------------------------------------------------------------------
   	function uf_obtener_alternos($as_codart)
   	{
		//////////////////////////////////////////////////////////////////////////////
		//	Function:     uf_formatonumerico
		//	Arguments:    as_codart  // Codigo de articulo
		//	Returns:	  $la_alternos arreglo que contiene codigos alternos
		//	Description:  Función que obtiene los codigos alternos relacionados con determinado articulo
		//////////////////////////////////////////////////////////////////////////////
		global $io_sql;
		$la_alternos="";
		$li_i=0;
		$ls_sql="SELECT codart".
				"  FROM siv_articulo".
				" WHERE codemp='".$_SESSION["la_empresa"]["codemp"]."'".
				"   AND codartpri='".$as_codart."' ";
		$rs_data=$io_sql->select($ls_sql);
		while(!$rs_data->EOF)
		{
			$li_i++;
			$la_alternos[$li_i]= $rs_data->fields["codart"];
			$rs_data->fields["codart"];
			$rs_data->MoveNext();
		}
		return $la_alternos;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------
	function uf_select_articulosrelacionados($as_codemp,$as_numordcom,$ls_codart,$ls_codartaux,&$ai_adicionales)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_articulosrelacionados
		//         Access: public (sigesp_siv_d_articulo)
		//      Argumento: $as_codart //codigo de articulo
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que verifica si existe un determinado articulo en la tabla siv_articulo
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 05/04/2010 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ai_adicionales=0;
		$ls_sql="SELECT SUM(siv_dt_recepcion.canart) AS canart".
			  	"  FROM siv_dt_recepcion, siv_recepcion,siv_articulo".
			  	" WHERE  siv_dt_recepcion.codemp=siv_recepcion.codemp".
			  	"   AND siv_dt_recepcion.codart=siv_articulo.codart".
			  	"   AND siv_dt_recepcion.numordcom=siv_recepcion.numordcom".
			  	"   AND siv_dt_recepcion.numconrec=siv_recepcion.numconrec ".
			  	"   AND siv_dt_recepcion.codemp='". $as_codemp ."'".
			  	"   AND siv_dt_recepcion.numordcom='". $as_numordcom ."'".
			  	"   AND siv_recepcion.estrec=0".
				"   AND siv_articulo.codartpri='". $ls_codart ."'";
//				"   AND siv_articulo.codart<>'".$ls_codartaux."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->articulo MÉTODO->uf_siv_select_articulo ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_valido=true;
				$ai_adicionales=$row["canart"];
				$this->io_sql->free_result($rs_data);
			}
		}
		return $lb_valido;
	}// end function uf_siv_select_articulo
	//-----------------------------------------------------------------------------------------------------------------------------

	function uf_siv_obtener_dt_bienes($as_codemp,$as_numordcom,&$ai_totrows,&$ao_object)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_obtener_dt_bienes
		//         Access: private
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $as_numordcom // numero de la orden de compra/factura
		//  			   $ai_totrows   // total de filas encontradas
		//  			   $ao_object    // arreglo de objetos para pintar el grid
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que busca los articulos asociados a una nueva orden de compra ordenados por el campo "orden" en la
		//				   tabla de  soc_dt_bienes e imprime los resultados obtenidos en el grid de la pagina.
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 10/02/2006							Fecha Última Modificación : 10/02/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql=" SELECT soc_dt_bienes.codemp,soc_dt_bienes.numordcom,soc_dt_bienes.estcondat,soc_dt_bienes.codart,".
				"   MAX(soc_dt_bienes.numsol) as numsol,MAX(soc_dt_bienes.unidad) as unidad,SUM(soc_dt_bienes.canart) as canart,".
				"   MAX(soc_dt_bienes.penart) as penart,MAX(soc_dt_bienes.preuniart) as preuniart,".
				"   MAX(soc_dt_bienes.monsubart) as monsubart,".
				"   MAX(soc_dt_bienes.montotart) as montotart,".
				"   MAX(soc_dt_bienes.orden) as orden,MAX(soc_dt_bienes.coduniadm) as coduniadm,MAX(soc_dt_bienes.codestpro1) as codestpro1,".
				"   MAX(soc_dt_bienes.codestpro2) as codestpro2,MAX(soc_dt_bienes.codestpro3) as codestpro3,MAX(soc_dt_bienes.codestpro4) as codestpro4,".
				"   MAX(soc_dt_bienes.codestpro5) as codestpro5,MAX(soc_dt_bienes.estcla) as estcla,".
				"   siv_articulo.codunimed, ".
				"   (SELECT denart FROM siv_articulo WHERE soc_dt_bienes.codart=siv_articulo.codart) AS denart, ".
				"   (SELECT unidad FROM siv_unidadmedida WHERE siv_unidadmedida.codunimed = siv_articulo.codunimed) AS unidades ".
				"	FROM soc_dt_bienes,siv_articulo ".
				"	WHERE soc_dt_bienes.codemp='". $as_codemp ."' ".
				"	AND soc_dt_bienes.codart=siv_articulo.codart ".
				"	AND numordcom='". $as_numordcom ."' ".
				"	GROUP BY soc_dt_bienes.codemp,soc_dt_bienes.numordcom,soc_dt_bienes.estcondat,soc_dt_bienes.codart,siv_articulo.codunimed".
				"	ORDER BY orden ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->recepcion MÉTODO->uf_siv_obtener_dt_bienes ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			$ai_totrows=0;
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_codart=    $row["codart"];
				$ls_denart=    $row["denart"];
				$ls_unidad=    $row["unidad"];
				$li_unidad=    $row["unidades"];
				$li_preuniart= $row["preuniart"];
				$li_canoriart= $row["canart"];
				$li_canart=    "";
				$li_montotart= "";
				$li_penart=    "";
				switch ($ls_unidad) 
				{
					case "M":
						$ls_unidadaux="Mayor";
						break;
					case "D":
						$ls_unidadaux="Detal";
						break;
				}
				$ai_totrows=$ai_totrows+1;
				$ao_object[$ai_totrows][1]="<input name=txtdenart".$ai_totrows."    type=text id=txtdenart".$ai_totrows."    class=sin-borde size=15 maxlength=50 value='".$ls_denart."' readonly>".
										   "<input name=txtcodart".$ai_totrows." type=hidden id=txtcodart".$ai_totrows." class=sin-borde size=20 maxlength=20 value='".$ls_codart."' readonly>".
										   "<input name=txtcodartpri".$ai_totrows." type=hidden id=txtcodartpri".$ai_totrows." class=sin-borde size=20 readonly>";
				$ao_object[$ai_totrows][2]="<input name=txtunidad".$ai_totrows."    type=text id=txtunidad".$ai_totrows."    class=sin-borde size=12 maxlength=12 value='".$ls_unidadaux."' readonly><input name='hidunidad".$ai_totrows."' type='hidden' id='hidunidad".$ai_totrows."' value='". $li_unidad ."'>";
				$ao_object[$ai_totrows][3]="<input name=txtcanoriart".$ai_totrows." type=text id=txtcanoriart".$ai_totrows." class=sin-borde size=12 maxlength=12 value='".number_format ($li_canoriart,2,",",".")."' readonly>";
				$ao_object[$ai_totrows][4]="<input name=txtcanart".$ai_totrows."    type=text id=txtcanart".$ai_totrows."    class=sin-borde size=10 maxlength=12 value='".$li_canart."'  onKeyPress=return(ue_formatonumero(this,'.',',',event));  onBlur='javascript: ue_calcularpendiente(".$ai_totrows.");'>";
				$ao_object[$ai_totrows][5]="<input name=txtpenart".$ai_totrows."    type=text id=txtpenart".$ai_totrows."    class=sin-borde size=10 maxlength=12 value='".number_format ($li_penart,2,",",".")."' readonly><input name='hidpendiente".$ai_totrows."' type='hidden' id='hidpendiente".$ai_totrows."' value='".$li_penart."'>";
				$ao_object[$ai_totrows][6]="<input name=txtpreuniart".$ai_totrows." type=text id=txtpreuniart".$ai_totrows." class=sin-borde size=14 maxlength=15 value='".number_format ($li_preuniart,2,",",".")."' readonly>";
				$ao_object[$ai_totrows][7]="<input name=txtmontotart".$ai_totrows." type=text id=txtmontotart".$ai_totrows." class=sin-borde size=14 maxlength=15 value='".$li_montotart."' readonly>";
				$ao_object[$ai_totrows][8]="";

			}//while
		}//else
		$this->io_sql->free_result($rs_data);
		return $lb_valido;
	} // end function uf_siv_obtener_dt_bienes

	function uf_siv_obtener_dt_orden($as_codemp,$as_numordcom,&$ai_totrows,&$ao_object)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_obtener_dt_orden
		//         Access: public (sigesp_siv_p_recepcion)
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $as_numordcom // numero de la orden de compra/factura
		//  			   $ai_totrows   // total de filas encontradas
		//  			   $ao_object    // arreglo de objetos para pintar el grid
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que deacuerdo a que si una orden de compra es nueva  ó no, procesa la busqueda
		//				   e los articulos de forma diferente.
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 10/02/2006							Fecha Última Modificación : 10/02/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_existe= $this->uf_siv_select_dt_recepcion($as_codemp,$as_numordcom);
		if($lb_existe)
		{
			$lb_valido=$this->uf_siv_obtener_dt_pendiente($as_codemp,$as_numordcom,&$ai_totrows,&$ao_object);
		}
		else
		{
			$lb_valido=$this->uf_siv_obtener_dt_bienes($as_codemp,$as_numordcom,&$ai_totrows,&$ao_object);
		}
		return $lb_valido;
	} // end  function uf_siv_obtener_dt_orden
	
	function uf_siv_update_ordencompra($as_codemp,$as_numordcom,$aa_seguridad) 
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_update_ordencompra
		//         Access: public (sigesp_siv_p_recepcion)
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $as_numordcom // numero de la orden de compra/factura
		//  			   $aa_seguridad // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que actualiza el estatus de la orden de compra estpenalm que indica si una orden de compra
		//				   ha sido completa o no. En la tabla soc_ordencompra.
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 10/02/2006							Fecha Última Modificación : 10/02/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		 $lb_valido=true;
		 $ls_sql = "UPDATE soc_ordencompra".
		 		   "   SET estpenalm=1".
				   " WHERE codemp='" . $as_codemp ."' ".
				   "   AND numordcom='" . $as_numordcom ."' ".
				   "   AND estcondat='B'";
		$li_row = $this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_msg->message("CLASE->recepcion MÉTODO->uf_siv_update_ordencompra ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			$lb_valido=true;
		}
	  return $lb_valido;
	} // end function uf_siv_update_ordencompra

	function uf_siv_obtener_dt_recepcion($as_codemp,$as_numordcom,$as_numconrec,&$ai_totrows,&$ao_object,&$ai_totentsum)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_obtener_dt_recepcion
		//         Access: public (sigesp_siv_p_recepcion)
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $as_numordcom // numero de la orden de compra/factura
		//  			   $as_numconrec // numero consecutivo de recepcion
		//  			   $ai_totrows   // total de filas encontradas
		//  			   $ao_object    // arreglo de objetos para pintar el grid
		//	      Returns: Retorna un Booleano
		//    Description: Funcion  que busca los articulos asociados a recepcion en la tabla siv_dt_recepcion para luego 
		//                 imprimirlos en el grid de  la pagina exepto que ya se recibieron por completo.
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 10/02/2006							Fecha Última Modificación : 10/02/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql= "SELECT siv_dt_recepcion.*,siv_articulo.codunimed,".
				  "      (SELECT unidad FROM siv_unidadmedida ".
				  "	       WHERE siv_unidadmedida.codunimed = siv_articulo.codunimed) AS unidades,".
				  "      (SELECT denart FROM siv_articulo".
				  "        WHERE siv_dt_recepcion.codart=siv_articulo.codart) AS denart,".
				  "		 (SELECT tipart FROM siv_tipoarticulo".
				  "        WHERE siv_tipoarticulo.codtipart = siv_articulo.codtipart) AS tipart".
				  "  FROM siv_dt_recepcion, siv_recepcion,siv_articulo".
				  " WHERE  siv_dt_recepcion.codemp=siv_recepcion.codemp".
				  "   AND siv_dt_recepcion.codart=siv_articulo.codart".
				  "   AND siv_dt_recepcion.numordcom=siv_recepcion.numordcom".
				  "   AND siv_dt_recepcion.numconrec=siv_recepcion.numconrec ".
				  "   AND siv_dt_recepcion.codemp='".$as_codemp."'".
				  "   AND siv_dt_recepcion.numordcom='".$as_numordcom."'".
				  "   AND siv_dt_recepcion.numconrec='".$as_numconrec."'".
				  " ORDER BY siv_dt_recepcion.numconrec";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{ print $this->io_sql->message;
			$lb_valido=false;
			$this->io_msg->message("CLASE->recepcion MÉTODO->uf_siv_obtener_dt_recepcion ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			return false;
		}
		else
		{
			$ai_totrows=0;
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_codart=    $row["codart"];
				$ls_denart=    $row["denart"];
				$ls_unidad=    $row["unidad"];
				$li_unidad=    $row["unidades"];
				$li_preuniart= $row["preuniart"];
				$li_penart=    $row["penart"];
				$li_canoriart= $row["canoriart"];
				$li_canart=    $row["canart"];
				$li_montotart= $row["montotart"];
				$ai_totentsum=($ai_totentsum+$li_montotart);
				$ls_clasi=$row["tipart"]; 
				switch ($ls_unidad) 
				{
					case "M":
						$ls_unidadaux="Mayor";
						$li_canart= ($li_canart/$li_unidad);
						break;
					case "D":
						$ls_unidadaux="Detal";
						break;
				}
				$ai_totrows=$ai_totrows+1;
				$ao_object[$ai_totrows][1]="<input name=txtdenart".$ai_totrows."    type=text id=txtdenart".$ai_totrows."    class=sin-borde size=15 maxlength=50 value='".$ls_denart."' readonly><input name=txtcodart".$ai_totrows." type=hidden id=txtcodart".$ai_totrows." class=sin-borde size=20 maxlength=20 value='".$ls_codart."' readonly>";
				$ao_object[$ai_totrows][2]="<input name=txtunidad".$ai_totrows."    type=text id=txtunidad".$ai_totrows."    class=sin-borde size=12 maxlength=12 value='".$ls_unidadaux."' readonly><input name='hidunidad".$ai_totrows."' type='hidden' id='hidunidad".$ai_totrows."' value='".$li_unidad."'>";
				$ao_object[$ai_totrows][3]="<input name=txtcanoriart".$ai_totrows." type=text id=txtcanoriart".$ai_totrows." class=sin-borde size=12 maxlength=12 value='".number_format ($li_canoriart,2,",",".")."'  readonly>";
				$ao_object[$ai_totrows][4]="<input name=txtcanart".$ai_totrows."    type=text id=txtcanart".$ai_totrows."    class=sin-borde size=10 maxlength=12 value='".number_format ($li_canart,2,",",".")."' readonly>";
				$ao_object[$ai_totrows][5]="<input name=txtpenart".$ai_totrows."    type=text id=txtpenart".$ai_totrows."    class=sin-borde size=10 maxlength=12 value='".number_format ($li_penart,2,",",".")."' readonly>";
				$ao_object[$ai_totrows][6]="<input name=txtpreuniart".$ai_totrows." type=text id=txtpreuniart".$ai_totrows." class=sin-borde size=14 maxlength=15 value='".number_format ($li_preuniart,2,",",".")."' readonly>";
				$ao_object[$ai_totrows][7]="<input name=txtmontotart".$ai_totrows." type=text id=txtmontotart".$ai_totrows." class=sin-borde size=14 maxlength=15 value='".number_format ($li_montotart,2,",",".")."' readonly>";
				$ao_object[$ai_totrows][8]="";
				$ao_object[$ai_totrows][9]="<a href=javascript:uf_delete_dt(".$ai_totrows.");><img src=../shared/imagebank/tools15/eliminar.gif alt=Aceptar width=15 height=15 border=0></a>";			
				$ao_object[$ai_totrows][10]="<a href=javascript:uf_dt_activo(".$ai_totrows.");><img src=../shared/imagebank/mas.gif alt=Agregar Seriales width=15 height=15 border=0></a><input name=hclasi".$ai_totrows." type=hidden id=hclasi".$ai_totrows." class=sin-borde size=20 maxlength=20  value='".$ls_clasi."' readonly>";	

			}//while
		}//else
		if ($ai_totrows==0)
		{$lb_valido=false;}
		$this->io_sql->free_result($rs_data);
		$ai_totrows=$ai_totrows+1;
		return $lb_valido;
	} // end function uf_siv_obtener_dt_recepcion
	
	function uf_siv_update_ultimocosto($as_codemp,$as_codart,$ai_preuniart,$aa_seguridad) 
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_update_ultimocosto
		//         Access: private
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $as_codart     // numero de orden de compra
		//  			   $ai_preuniart  // precio unitario del articulo
		//                 $aa_seguridad  // 
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que actualiza el monto del ultimo costo con el cual el articulo ha ingresado a la empresa
		//				   en la tabla siv_articulo
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 10/02/2006							Fecha Última Modificación : 10/02/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		 $lb_valido=true;
		 $ls_sql = "UPDATE siv_articulo ".
		 		   "   SET ultcosart='".$ai_preuniart."' ".
				   " WHERE codemp='".$as_codemp."' ".
				   "   AND codart='".$as_codart."' ";
		$li_row = $this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_msg->message("CLASE->recepcion MÉTODO->uf_siv_update_ultimocosto ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
	    return $lb_valido;
	} // end function uf_siv_update_ultimocosto

	function uf_siv_actualizar_costo_promedio($as_codemp,$as_codart,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_actualizar_costo_promedio
		//         Access: private
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $as_codart     // numero de orden de compra
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que se encarga de calcular el costo promedio por articulo para luego actualizar
		//				   dicho monto en la tabla de siv_articulo
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 10/02/2006							Fecha Última Modificación : 10/02/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$li_cosproart=0;
		$li_montot=0;		
		$li_conart=0;		
		$ls_sql = "SELECT * FROM siv_dt_recepcion  ".
				  " WHERE codemp='".$as_codemp."'".
				  "   AND codart='".$as_codart."'" ;
			
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->recepcion MÉTODO->uf_siv_actualizar_costo_promedio ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		while($row=$this->io_sql->fetch_row($rs_data))
		{
			$li_preuniart=$row["preuniart"];
			$li_canart=$row["canart"];
			$li_montot=$li_montot + ($li_preuniart * $li_canart);
			$li_conart=$li_conart + $li_canart;
		}
		if($li_conart!=0)
		{$li_cosproart=($li_montot / $li_conart);}
		else
		{$li_cosproart=0.0000;}
//		$this->io_sql->free_result($li_exec);
		$lb_valido=$this->uf_siv_update_costo_promedio($as_codemp,$as_codart,$li_cosproart,$aa_seguridad);		
		return $lb_valido;

	}  // end function uf_siv_actualizar_costo_promedio

	function uf_siv_update_costo_promedio($as_codemp,$as_codart,$ai_cosproart,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_update_costo_promedio
		//         Access: private
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $as_codart     // numero de orden de compra
		//  			   $as_codalm     // codigo de almacen
		//  			   $ai_cosproart  // costo promedio por articulo
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que actualiza el costo promedio en un determinado articulo en la tabla siv_articulo
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 10/02/2006							Fecha Última Modificación : 10/02/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		 $lb_valido=true;
		 $li_exce=-1;
		 $lb_existe=true;
		//$lb_existe=$this->uf_siv_select_articulo($as_codemp,$as_codart);
		if($lb_existe)
		{
				$ls_sql = "UPDATE siv_articulo".
						  "   SET cosproart='".$ai_cosproart."' ".
						  " WHERE codemp='".$as_codemp."' ".
						  "   AND codart='".$as_codart."' ";
				$li_row = $this->io_sql->execute($ls_sql);
				if($li_row===false)
				{
					$this->io_msg->message("CLASE->recepcion MÉTODO->uf_siv_update_costo_promedio ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
					$lb_valido=false;
				}
		} 
		return $lb_valido;
	} // end  function uf_siv_update_costo_promedio
	
	function uf_siv_update_recepcion($as_codemp,$as_numordcom,$as_codpro,$as_codalm,$as_obsrec,$as_estpro,$as_estrec,
									 $as_numconrec,$aa_seguridad) 
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_update_recepcion
		//         Access: public (sigesp_siv_p_recepcion)
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $as_numordcom // numero de la orden de compra/factura
		//  			   $aa_seguridad // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que actualiza el estatus de la orden de compra estpenalm que indica si una orden de compra
		//				   ha sido completa o no. En la tabla soc_ordencompra.
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 10/02/2006							Fecha Última Modificación : 10/02/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		 $lb_valido=true;
		 $ls_sql = "UPDATE siv_recepcion".
		 		   "   SET cod_pro='".$as_codpro."',".
				   "       codalm='".$as_codalm."',".
				   "       obsrec='".$as_obsrec."',".
				   "       estpro='".$as_estpro."',".
				   "       estrec='".$as_estrec."'".
				   " WHERE codemp='" . $as_codemp ."' ".
				   "   AND numordcom='" . $as_numordcom ."'".
				   "   AND numconrec='".$as_numconrec."' ";
		$li_row = $this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_msg->message("CLASE->recepcion MÉTODO->uf_siv_update_recepcion ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			$lb_valido=true;
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="UPDATE";
			$ls_descripcion ="Modificó una la entrada de suministros ".$as_numconrec." Asociado a la Orden de Compra ".$as_numordcom." de la Empresa ".$as_codemp;
			$ls_variable= $this->seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
		}
	  return $lb_valido;
	} // end function uf_siv_update_recepcion
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_delete_detalles($as_codemp,$as_numordcom,$as_numconrec,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_delete_detalles
		//		   Access: private
		//	    Arguments: 
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Funcion que elimina los detalles de una solicitud de pago
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 30/04/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="DELETE FROM siv_dt_recepcion ".
				" WHERE codemp = '".$as_codemp."' ".
				"   AND numordcom = '".$as_numordcom."'".
				"   AND numconrec='".$as_numconrec."' ";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			print $this->io_sql->message;
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Solicitud MÉTODO->uf_delete_detalles ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		}
		if($lb_valido)
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="DELETE";
			$ls_descripcion ="Eliminó todos los detalles de la entrada de suministros ".$as_numconrec." Asociado a la empresa ".$as_codemp;
			$lb_valido= $this->seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////	
		}
		return $lb_valido;
	}// end function uf_delete_detalles
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------
	function uf_siv_select_catalogo(&$ai_estnum,&$ai_estcmp)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_select_catalogo
		//         Access: public (sigesp_siv_d_articulo)
		//      Argumento: $ai_estnum //estatus que indica si la codificion es numerica o alfanumerica
		//				   $ai_estcmp // Estatus que indica si se van a agregar ceros a la izq. del codigo de articulo
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que obtiene la configuracion del inventario
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación: 08/10/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql="SELECT metodo, estcatsig, estnum, estcmp".
				"  FROM siv_config".
				" WHERE id=1 ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->articulo MÉTODO->uf_siv_select_catalogo ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$li_estcatsig= $row["estcatsig"];
				$ai_estnum= $row["estnum"];
				$ai_estcmp= $row["estcmp"];
				if($li_estcatsig==1)
				{$lb_valido=true;}
			}
			$this->io_sql->free_result($rs_data);
		}
		return $lb_valido;
	}// end function uf_siv_select_catalogo
	//-----------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------
	function uf_select_articulo($as_codart,$as_origen,&$ls_codartpri,&$as_denart,&$ai_unidad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_articulo
		//         Access: public (sigesp_siv_d_articulo)
		//      Argumento: $as_codart //codigo de articulo
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que verifica si existe un determinado articulo en la tabla siv_articulo
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 05/04/2010 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$as_denart="";
		$ai_unidad=0;
		$ls_sql="SELECT codart,denart,unidad,codartpri".
				"  FROM siv_articulo,siv_unidadmedida  ".
				" WHERE siv_articulo.codemp='".$this->ls_codemp."'".
				"   AND siv_articulo.codart='".$as_codart."'".
				"   AND siv_articulo.codunimed=siv_unidadmedida.codunimed" ;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->articulo MÉTODO->uf_siv_select_articulo ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_valido=true;
				$ls_codartpri=$row["codartpri"];
/*				if($as_origen=="orden")
				{
					if($ls_codartpri==$as_codartori)
					{
						$as_denart=$row["denart"];
						$ai_unidad=$row["unidad"];
					}
					else
					{
						$this->io_msg->message("El articulo existe pero no corresponde a su generico");
					}
				}
				else
				{
*/					$as_denart=$row["denart"];
					$ai_unidad=$row["unidad"];
//				}
				$this->io_sql->free_result($rs_data);
			}
		}
		return $lb_valido;
	}// end function uf_siv_select_articulo
	//-----------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------
	function  uf_siv_insert_articulo($as_codart,$as_denart,$as_codtipart,$as_codunimed,$as_spg_cuenta,$as_codcatsig,$as_codartpri,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_insert_articulo
		//         Access: public (sigesp_siv_d_articulo)
		//     Argumentos: $as_codemp     //codigo de empresa                 $as_codart    // codigo de articulo
		//				   $as_denart     // denominacion del articulo        $as_codtipart // codigo de tipo de articulo
		//			       $as_codunimed  // codigo de unidad de medida       $ad_feccreart // fecha de creacion del articulo
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que inserta un articulo en la tabla de  siv_articulo
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 30/08/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$li_exiart=0;
		$li_exiiniart=0;
		$li_prearta=0;
		$li_preartb=0;
		$li_preartc=0;
		$li_preartd=0;
		$li_pesart=0;
		$li_altart=0;
		$li_ancart=0;
		$li_proart=0;
		$li_minart=0;
		$li_maxart=0;
		$ld_fecvenart="1900-01-01";
		$ld_feccreart=date("Y-m-d");
		$this->io_sql->begin_transaction();
		$ls_sql="INSERT INTO siv_articulo (codemp,codart,denart,codtipart,codunimed,feccreart,exiart,exiiniart, ".
				"                          minart,maxart,prearta,preartb,preartc,preartd,fecvenart,spg_cuenta,pesart,altart,".
				"                          ancart, proart,codcatsig,estartgen,codartpri,obsart)".
				" VALUES ('".$this->ls_codemp."','".$as_codart."','".$as_denart."','".$as_codtipart."','".$as_codunimed."',".
				"         '".$ld_feccreart."',".$li_exiart.",".$li_exiiniart.",".$li_minart.",".$li_maxart.",".
				"          ".$li_prearta.",".$li_preartb.",".$li_preartc.",".$li_preartd.",'".$ld_fecvenart."','".$as_spg_cuenta."',".
				"          ".$li_pesart.",".$li_altart.",".$li_ancart.",".$li_proart.",'".$as_codcatsig."',".
				"         '1','".$as_codartpri."','NINGUNA')";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_msg->message("CLASE->articulo MÉTODO->uf_siv_insert_articulo ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
			$this->io_sql->rollback();
		}
		else
		{
				/////////////////////////////////         SEGURIDAD               /////////////////////////////		
				$ls_evento="INSERT";
				$ls_descripcion ="Insertó el Articulo ".$as_codart." Asociado a la Empresa ".$as_codemp;
				$lb_valido= $this->seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
												$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
												$aa_seguridad["ventanas"],$ls_descripcion);
				/////////////////////////////////         SEGURIDAD               /////////////////////////////	
				if($lb_valido)
				{
					$lb_valido=true;
					$this->io_sql->commit();
				}
				else
				{
					$lb_valido=false;
					$this->io_sql->rollback();
				}	
		}
		return $lb_valido;
	} // end  function  uf_siv_insert_articulo
	//-----------------------------------------------------------------------------------------------------------------------------
	function uf_siv_validar_pendientes($as_codemp,$as_numordcom,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_validar_pendientes
		//         Access: private
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $as_numordcom // numero de la orden de compra/factura
		//  			   $ai_totrows   // total de filas encontradas
		//  			   $ao_object    // arreglo de objetos para pintar el grid
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que busca los articulos asociados a una orden de compra ordenados por el campo "orden" en la
		//				   tabla de soc_dt_bienes, y por articulo busca en la tabla siv_dt_recepcion los pendientes asociados a esos 
		//				   articulos para luego imprimirlos en el grid de la pagina exepto aquellos que ya se recibieron por completo.
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 10/02/2006							Fecha Última Modificación : 10/02/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$lb_pendientes=false;
		$ls_sql="SELECT codart,MIN(orden) as orden".
				" FROM soc_dt_bienes".
				" WHERE codemp='". $as_codemp ."'".
				"   AND numordcom='". $as_numordcom ."'".
				" GROUP BY codemp, numordcom, estcondat, codart".
				" ORDER BY orden";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$lb_valido=false;
			$this->io_msg->message("CLASE->recepcion MÉTODO->uf_siv_validar_pendientes ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			return false;
		}
		else
		{
			$ls_gestor=$_SESSION["ls_gestor"];
			$ai_totrows=0;
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_aux="";
				$ls_codart=$row["codart"];
				$la_alternos=$this->uf_obtener_alternos($ls_codart);
				if(!empty($la_alternos))
				{
					$li_total=count($la_alternos);
					if($li_total>0)
					{
						for($li_i=1;$li_i<=$li_total;$li_i++)
						{
							$ls_aux=$ls_aux."OR siv_dt_recepcion.codart='".$la_alternos[$li_i]."'";
						}
					}
				}
				$ls_aux=$ls_aux. ")";
			  $ls_sql="SELECT siv_dt_recepcion.*,siv_articulo.codunimed,siv_articulo.denart, ".
					  "     (SELECT unidad FROM siv_unidadmedida ".
					  "	      WHERE siv_unidadmedida.codunimed = siv_articulo.codunimed) AS unidades,".
					  "     (SELECT denart FROM siv_articulo as padre ".
					  "       WHERE padre.codart=siv_articulo.codartpri) AS denartpri,".
					  "     (SELECT unidad FROM siv_articulo as padre,siv_unidadmedida ".
					  "       WHERE padre.codart=siv_articulo.codartpri".
					  "         AND padre.codunimed=siv_unidadmedida.codunimed) AS unidadespri".
					  "  FROM siv_dt_recepcion, siv_recepcion, siv_articulo ".
					  " WHERE  siv_dt_recepcion.codemp=siv_recepcion.codemp".
					  "   AND siv_dt_recepcion.codart=siv_articulo.codart".
					  "   AND siv_dt_recepcion.numordcom=siv_recepcion.numordcom".
					  "   AND siv_dt_recepcion.numconrec=siv_recepcion.numconrec ".
					  "   AND siv_dt_recepcion.codemp='". $as_codemp ."'".
					  "   AND siv_dt_recepcion.numordcom='". $as_numordcom ."'".
					  "   AND siv_recepcion.estrec=0".
					  "   AND (siv_dt_recepcion.codart='". $ls_codart ."'".
					  $ls_aux.
					  " ORDER BY siv_dt_recepcion.numconrec DESC LIMIT  1";
				$rs_data1=$this->io_sql->select($ls_sql);
				if($rs_data1===false)
				{
					$lb_valido=false;
					$this->io_msg->message("CLASE->recepcion MÉTODO->uf_siv_validar_pendientes ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
				}
				else
				{
					while($row=$this->io_sql->fetch_row($rs_data1))
					{
						$ls_codartaux=    $row["codart"];
						$ls_denart=    $row["denartpri"];
						if($ls_denart=="")
						{
							$li_penart=    $row["penart"];
							$ls_denart=    $row["denart"];
						}
						else
						{
							$li_penart=    $row["canoriart"];
						}
						$ls_unidad=    $row["unidad"];
						$li_unidad=    $row["unidadespri"];
						if($li_unidad=="")
						{
							$li_unidad=    $row["unidades"];
						}
						$li_preuniart= $row["preuniart"];
						$li_canoriart= $row["canoriart"];
						$li_canart=    "";
						$li_montotart= "";
						$lb_valido=$this->uf_select_articulosrelacionados($as_codemp,$as_numordcom,$ls_codart,$ls_codartaux,&$li_adicionales);
						$li_penart=$li_penart-$li_adicionales;
						switch ($ls_unidad) 
						{
							case "M":
								$ls_unidadaux="Mayor";
								break;
							case "D":
								$ls_unidadaux="Detal";
								break;
						}
						if($li_penart!=0.00)
						{
							$lb_pendientes=true;
						}
					}//while
				}//else
			}//while($row=$this->io_sql->fetch_row($li_exec))
		}
		if(!$lb_pendientes)
		{
			$lb_valido=$this->uf_siv_update_ordencompra($as_codemp,$as_numordcom,$aa_seguridad);
		}
		$this->io_sql->free_result($rs_data);
		return $lb_valido;
	} // end function uf_siv_obtener_dt_pendiente
  //-----------------------------------------------------------------------------------------------------------------------------------

}//fin  class sigesp_siv_c_recepcion
?>