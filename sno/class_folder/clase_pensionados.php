<?php 
//session_start();
class pensionados
{
	

	function pensionados()
	{	
		global $ruta;
		if(!$ruta){$ruta = '../';}
		require_once($ruta."shared/class_folder/sigesp_include.php");
		$io_include=new sigesp_include();
		$io_conexion=$io_include->uf_conectar();
		//$io_conexion_saman=$io_include->uf_conectar_otra_bd('localhost', 'postgres', 'adminsigesp','db_saman_2009','POSTGRES');
		require_once($ruta."shared/class_folder/class_sql.php");
		$this->io_sql=new class_sql($io_conexion);
		//$this->io_sql_saman=new class_sql($io_conexion_saman);	
		require_once($ruta."shared/class_folder/class_mensajes.php");
		$this->io_mensajes=new class_mensajes();
		require_once($ruta."shared/class_folder/sigesp_conexiones.php");
		$this->io_conexiones=new conexiones();	
		require_once($ruta."shared/class_folder/class_funciones.php");
		$this->io_funciones=new class_funciones();		
		require_once($ruta."shared/class_folder/sigesp_c_seguridad.php");
		$this->io_seguridad= new sigesp_c_seguridad();				
        $this->ls_codemp=$_SESSION["la_empresa"]["codemp"];
		if(array_key_exists("la_nomina",$_SESSION))
		{
        	$this->ls_codnom=$_SESSION["la_nomina"]["codnom"];
		}
		else
		{
			$this->ls_codnom="0000";
		}
		
	}


	function busca_calculados(){
				
				$query_rs = "SELECT codper FROM sno_resumen WHERE monnetres>0 ORDER BY codper";					
				$resultado = $this->io_conexiones->conexion($query_rs,'arreglo','<b>CLASE:</b> pensionados <br><b>METODO:</b> busca_calculados');
				
				
				return $resultado;
								
	}//end busca_calculados
	
	function busca_calculados2(){
				
				$lb_valido=true;
				$ls_sql = "SELECT codper FROM sno_resumen WHERE monnetres>0";			
				$this->rs_data_pensionados=$this->io_sql->select($ls_sql);
				if($rs_data===false)
				{
					$this->io_mensajes->message("CLASE->pensionados MÉTODO->busca_calculados2 ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
					$lb_valido=false;
				}
	
				return $lb_valido;
								
	}//end busca_calculados
	
	function busca_neto($param){
				
				$lb_valido=true;
				$ls_sql = "SELECT codper FROM sno_resumen WHERE monnetres>0 AND codper = '".$param['codper']."'";			
				$this->rs_data_pensionados=$this->io_sql->select($ls_sql);
				if($rs_data===false)
				{
					$this->io_mensajes->message("CLASE->pensionados MÉTODO->busca_calculados2 ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
					$lb_valido=false;
				}
	
				return $lb_valido;
								
	}//end busca_calculados
	
	
	function busca_medidas_persona($datos=''){
				
					$codper_medida = explode(',',$datos);
					
					if(sizeof($codper_medida)<2){							
							
							return $this->busca_nro_medida_judicial($codper_medida);
					}	
					else{					
						$medidas = '';
						foreach($codper_medida as $codperm) {
							  if($medidas == ''){$medidas = $this->busca_nro_medida_judicial($codperm);}							
							  else{$medidas = $this->busca_nro_medida_judicial($codperm).', '.$medidas;}
							  $i ++;
						}
						return $medidas;								
					}		
								
	}//end busca_calculados
	
	function busca_nro_medida_judicial($codper_medida){
				
				$ls_sql = "SELECT medjudnro FROM sno_personal WHERE codper='".$codper_medida."' ";					
				$rs_data=$this->io_sql->select($ls_sql);			
				if($rs_data===false)
				{
					$this->io_mensajes->message("CLASE->pensionados MÉTODO-> busca_nro_medida_judicial ERROR->".$this->io_sql->message);
					$lb_valido=false;
					return;
				}				
				return $rs_data->fields["medjudnro"];;
	}
		
	
	
	function uf_monto_cero($opciones=array()){
		
		$lb_valido=true;
		$ls_criterio="";
		
		
		$ls_sql="SELECT p.codper,p.nomper,p.apeper,p.cedper,p.cedmil,
					   p.parfam,p.parentesco,monnetres,desc_tippen,
					   codban,codcueban,tipcuebanper,pagbanper,pagefeper				 
				FROM sno_resumen r 				
				INNER JOIN sno_personal p ON p.codper = r.codper
				INNER JOIN sno_personalnomina pn ON pn.codper = r.codper
				INNER JOIN sno_tipo_pensionado tp ON tp.tippensionado = p.tippensionado			 
				WHERE monnetres = 0";
		
		$this->rs_data_cero=$this->io_sql->select($ls_sql);
		
		if($this->rs_data_cero==false)
		{
			$this->io_mensajes->message("CLASE->pensionados MÉTODO->uf_monto_cero ERROR->".$this->io_sql->message);
			$lb_valido=false;
		}
		
		return $lb_valido;
	
	}
	
	
	function uf_monto_negativo($opciones=array()){
		
		$lb_valido=true;
		$ls_criterio="";
		
		
		$ls_sql="SELECT p.codper,p.nomper,p.apeper,p.cedper,p.cedmil,
					   p.parfam,p.parentesco,monnetres,desc_tippen,
					   codban,codcueban,tipcuebanper,pagbanper,pagefeper				 
				FROM sno_resumen r 				
				INNER JOIN sno_personal p ON p.codper = r.codper
				INNER JOIN sno_personalnomina pn ON pn.codper = r.codper
				INNER JOIN sno_tipo_pensionado tp ON tp.tippensionado = p.tippensionado			 
				WHERE monnetres < 0";
		
		$this->rs_data_cero=$this->io_sql->select($ls_sql);
		
		if($this->rs_data_cero==false)
		{
			$this->io_mensajes->message("CLASE->pensionados MÉTODO->uf_monto_negativo ERROR->".$this->io_sql->message);
			$lb_valido=false;
		}
		
		return $lb_valido;
	
	}
	
	
	function uf_errores_banco($opciones=array()){
		
		$lb_valido=true;
		$ls_criterio="";
		
		
		$ls_sql="SELECT p.codper,p.nomper,p.apeper,p.cedper,p.cedmil,
					   p.parfam,p.parentesco,monnetres,desc_tippen,
					   codban,codcueban,tipcuebanper,pagbanper,pagefeper				 
				FROM sno_resumen r 				
				INNER JOIN sno_personal p ON p.codper = r.codper
				INNER JOIN sno_personalnomina pn ON pn.codper = r.codper
				INNER JOIN sno_tipo_pensionado tp ON tp.tippensionado = p.tippensionado			 
				WHERE staper = 9";
		
		$this->rs_data=$this->io_sql->select($ls_sql);
		
		if($this->rs_data==false)
		{
			$this->io_mensajes->message("CLASE->pensiones MÉTODO->uf_errores_banco ERROR->".$this->io_sql->message);
			$lb_valido=false;
		}
		
		return $lb_valido;
	
	}
	
	function uf_cheques_sin_banco($opciones=array()){
		
		$lb_valido=true;
		$ls_criterio="";
		
		
		$ls_sql="SELECT p.codper,p.nomper,p.apeper,p.cedper,p.cedmil,
					   p.parfam,p.parentesco,monnetres,desc_tippen,
					   codban,codcueban,tipcuebanper,pagbanper,pagefeper			 
				FROM sno_resumen r 				
				INNER JOIN sno_personal p ON p.codper = r.codper
				INNER JOIN sno_personalnomina pn ON pn.codper = r.codper
				INNER JOIN sno_tipo_pensionado tp ON tp.tippensionado = p.tippensionado			 
				WHERE (pagefeper = 1 AND monnetres>0 AND codban = '')  OR (pagefeper = 1 AND monnetres>0 AND codban != '002')";
		
		$this->rs_data=$this->io_sql->select($ls_sql);
		
		if($this->rs_data==false)
		{
			$this->io_mensajes->message("CLASE->pensionados MÉTODO->uf_cheques_sin_banco ERROR->".$this->io_sql->message);
			$lb_valido=false;
		}
		
		return $lb_valido;
	
	}
	
	function uf_sin_categoria($opciones=array()){
		
		$lb_valido=true;
		$ls_criterio="";
		
		
		$ls_sql="SELECT p.codper,p.nomper,p.apeper,p.cedper,p.cedmil,
					    p.codcom,descom,p.codran, desran,cat.codcat,descat,
					    p.parfam,p.parentesco,monnetres,desc_tippen,
						codban,codcueban,tipcuebanper,pagbanper,pagefeper				 
				FROM sno_resumen r 				
				INNER JOIN sno_personal p ON p.codper = r.codper
				INNER JOIN sno_personalnomina pn ON pn.codper = r.codper
				INNER JOIN sno_tipo_pensionado tp ON tp.tippensionado = p.tippensionado	
				LEFT JOIN sno_rango ran ON (ran.codemp = p.codemp AND ran.codcom= p.codcom AND ran.codran = p.codran) 
				LEFT JOIN sno_categoria_rango cat  ON (cat.codemp = p.codemp AND ran.codcat = cat.codcat) 
				LEFT JOIN sno_componente com ON (com.codemp = p.codemp AND com.codcom = p.codcom) 		 
				WHERE  cat.codcat is null OR cat.codcat='0000000000' OR cat.codcat=''";
		
		$this->rs_data=$this->io_sql->select($ls_sql);
		
		if($this->rs_data==false)
		{
			$this->io_mensajes->message("CLASE->pensionados MÉTODO->uf_sin_categoria ERROR->".$this->io_sql->message);
			$lb_valido=false;
		}
		
		return $lb_valido;
	
	}
	
	
	function uf_busca_descuentos_medjudiciales($opciones=array()){
		
		$lb_valido=true;
		$ls_criterio="";
		
		
		$ls_sql="SELECT p.codper,p.nomper,p.apeper,p.cedper,p.cedmil,
					   p.parfam,p.parentesco,monnetres,desc_tippen,abs(valsal) AS monto,codconc,
					   codban,codcueban,tipcuebanper,pagbanper,pagefeper				 
				FROM sno_resumen r 				
				INNER JOIN sno_personal p ON p.codper = r.codper AND p.codemp = r.codemp
				INNER JOIN sno_personalnomina pn ON pn.codper = r.codper AND p.codemp = pn.codemp
				INNER JOIN sno_tipo_pensionado tp ON tp.tippensionado = p.tippensionado
				INNER JOIN sno_salida s ON s.codper = p.codper AND s.codconc = '0000000411' AND p.codemp = s.codemp				 
				WHERE abs(valsal)>0";
		
		$this->rs_data=$this->io_sql->select($ls_sql);
		
		if($this->rs_data==false)
		{
			$this->io_mensajes->message("CLASE->sigesp_sno_class_report MÉTODO->uf_busca_descuentos_medjudiciales ERROR->".$this->io_sql->message);
			$lb_valido=false;
		}
		
		return $lb_valido;
	
	}
	
	
	function uf_montos_medidas($cedmil){
	
				$lb_valido=true;
				$ls_sql = "SELECT p.codper,p.nomper,p.apeper,p.cedper,p.cedmil,
								  p.parfam,p.parentesco,monnetres,desc_tippen,abs(valsal) AS monto,codconc,
								  codban,codcueban,tipcuebanper,pagbanper,pagefeper			 
							FROM sno_resumen r 				
							INNER JOIN sno_personal p ON p.codper = r.codper AND p.codemp = r.codemp
							INNER JOIN sno_personalnomina pn ON pn.codper = r.codper AND p.codemp = pn.codemp
							INNER JOIN sno_tipo_pensionado tp ON tp.tippensionado = p.tippensionado
							INNER JOIN sno_salida s ON s.codper = p.codper AND s.codconc = '0000000211'	AND p.codemp = s.codemp			 
							WHERE abs(valsal)>0 AND p.cedmil='".$cedmil."'";			
				$this->rs_data_medidas=$this->io_sql->select($ls_sql);
				if($rs_data===false)
				{
					$this->io_mensajes->message("CLASE->pensionados MÉTODO->uf_montos_medidas ERROR->".$this->io_sql->message);
					$lb_valido=false;
				}
				
				$monto = 0;
				if($this->rs_data_medidas->RecordCount()>1){					
						while($this->rs_data_medidas->EOF){
								$monto += $this->rs_data_medidas->fields['monto'];  
								$this->rs_data_medidas->MoveNext();
						}						
										
				}
				if($this->rs_data_medidas->RecordCount()==1){				
							$monto = $this->rs_data_medidas->fields['monto'];			
				}
							
				return $monto;

	}
	
	
	function uf_error_banco($opciones=array()){
		
		$lb_valido=true;
		$ls_criterio="";
		
		
		$ls_sql="SELECT p.codper,p.nomper,p.apeper,p.cedper,p.cedmil,
					   p.parfam,p.parentesco,desc_tippen,codban,codcueban,tipcuebanper,pagbanper,pagefeper				 
				FROM sno_personal p 			
				INNER JOIN sno_personalnomina pn ON pn.codper = p.codper
				INNER JOIN sno_tipo_pensionado tp ON tp.tippensionado = p.tippensionado			 
				WHERE staper = 9";
		
		$this->rs_data=$this->io_sql->select($ls_sql);
		
		if($this->rs_data==false)
		{
			$this->io_mensajes->message("CLASE->pensiones MÉTODO->uf_errores_banco ERROR->".$this->io_sql->message);
			$lb_valido=false;
		}
		
		return $lb_valido;
	
	}
	
	
	function uf_sin_categoria_prenomina($opciones=array()){
		
		$lb_valido=true;
		$ls_criterio="";
		
		
		$ls_sql="   SELECT p.codper,p.nomper,p.apeper,p.cedper,p.cedmil,
						   p.codcom,descom,p.codran, desran,cat.codcat,descat,
						   p.parfam,p.parentesco,desc_tippen,
						   codban,codcueban,tipcuebanper,pagbanper,pagefeper				 
					FROM sno_personal p			
					INNER JOIN sno_personalnomina pn ON pn.codper = p.codper
					INNER JOIN sno_tipo_pensionado tp ON tp.tippensionado = p.tippensionado	
					LEFT JOIN sno_rango ran ON (ran.codemp = p.codemp AND ran.codcom= p.codcom AND ran.codran = p.codran) 
					LEFT JOIN sno_categoria_rango cat  ON (cat.codemp = p.codemp AND ran.codcat = cat.codcat) 
					LEFT JOIN sno_componente com ON (com.codemp = p.codemp AND com.codcom = p.codcom) 		 
					WHERE  (cat.codcat is null OR cat.codcat='0000000000' OR cat.codcat='') AND (staper=1 OR staper=9) ";
		
		$this->rs_data=$this->io_sql->select($ls_sql);
		
		if($this->rs_data==false)
		{
			$this->io_mensajes->message("CLASE->pensionados MÉTODO->uf_sin_categoria_prenomin ERROR->".$this->io_sql->message);
			$lb_valido=false;
		}
		
		return $lb_valido;
	
	}
	
	
	
	function uf_sin_autorizado($opciones=array()){
		
		$lb_valido=true;
		$ls_criterio="";
		
		
		$ls_sql="SELECT p.codper,p.nomper,p.apeper,p.cedper,p.cedmil,p.cedaut,p.tipnip,
					   p.codcom,descom,p.codran, desran,cat.codcat,descat,
					   p.parfam,p.parentesco,monnetres,desc_tippen,
					   pn.codban,nomban,codcueban,tipcuebanper,pagbanper,pagefeper				 
				FROM sno_resumen r 				
				INNER JOIN sno_personal p ON p.codper = r.codper
				INNER JOIN sno_personalnomina pn ON pn.codper = r.codper
				INNER JOIN scb_banco b ON b.codban = pn.codban
				INNER JOIN sno_tipo_pensionado tp ON tp.tippensionado = p.tippensionado	
				LEFT JOIN sno_rango ran ON (ran.codemp = p.codemp AND ran.codcom= p.codcom AND ran.codran = p.codran) 
				LEFT JOIN sno_categoria_rango cat  ON (cat.codemp = p.codemp AND ran.codcat = cat.codcat) 
				LEFT JOIN sno_componente com ON (com.codemp = p.codemp AND com.codcom = p.codcom) 		 
				WHERE  p.tipnip = 'M' AND cedaut=0 AND pagbanper=1 ORDER BY pn.codban,cedper";
		
		$this->rs_data=$this->io_sql->select($ls_sql);
		
		if($this->rs_data==false)
		{
			$this->io_mensajes->message("CLASE->pensionados MÉTODO->uf_sin_autorizado ERROR->".$this->io_sql->message);
			$lb_valido=false;
		}
		
		return $lb_valido;
	
	}
	
	
	
	
	function uf_sin_autorizado_prenomina($opciones=array()){
		
		$lb_valido=true;
		$ls_criterio="";
		
		
		$ls_sql="SELECT p.codper,p.nomper,p.apeper,p.cedper,p.cedmil,p.cedaut,p.tipnip,
					   p.codcom,descom,p.codran, desran,cat.codcat,descat,
					   p.parfam,p.parentesco,desc_tippen,
					   pn.codban,nomban,codcueban,tipcuebanper,pagbanper,pagefeper				 
				FROM sno_personal p				
				INNER JOIN sno_personalnomina pn ON pn.codper = p.codper
				INNER JOIN scb_banco b ON b.codban = pn.codban
				INNER JOIN sno_tipo_pensionado tp ON tp.tippensionado = p.tippensionado	
				LEFT JOIN sno_rango ran ON (ran.codemp = p.codemp AND ran.codcom= p.codcom AND ran.codran = p.codran) 
				LEFT JOIN sno_categoria_rango cat  ON (cat.codemp = p.codemp AND ran.codcat = cat.codcat) 
				LEFT JOIN sno_componente com ON (com.codemp = p.codemp AND com.codcom = p.codcom) 		 
				WHERE  p.tipnip = 'M' AND cedaut=0 AND pagbanper=1 AND staper=1 ORDER BY pn.codban,cedper";
		
		$this->rs_data=$this->io_sql->select($ls_sql);
		
		if($this->rs_data==false)
		{
			$this->io_mensajes->message("CLASE->pensionados MÉTODO->uf_sin_autorizado ERROR->".$this->io_sql->message);
			$lb_valido=false;
		}
		
		return $lb_valido;
	
	}
	
	
	function uf_busca_descuentos_medjudiciales_prenomina($opciones=array()){
		
		$lb_valido=true;
		$ls_criterio="";
		
		
		$ls_sql=" SELECT p.codper,moncon,codper_medidas,p.nomper,p.apeper,p.cedper,
					p.tipnip, p.codcom,descom,p.codran, desran,cat.codcat,descat,
					desc_tippen 
				FROM sno_constantepersonal c
				INNER JOIN sno_personal p ON p.codper = c.codper
				INNER JOIN sno_personalnomina pn ON pn.codper = c.codper
				INNER JOIN sno_tipo_pensionado tp ON tp.tippensionado = p.tippensionado	
				LEFT JOIN sno_rango ran ON (ran.codemp = p.codemp AND ran.codcom= p.codcom AND ran.codran = p.codran) 
				LEFT JOIN sno_categoria_rango cat  ON (cat.codemp = p.codemp AND ran.codcat = cat.codcat) 
				LEFT JOIN sno_componente com ON (com.codemp = p.codemp AND com.codcom = p.codcom) 	
				WHERE codcons='0000000411' AND staper=1 AND moncon>0 ORDER BY codcom,codcat,codran,codper";
		
		$this->rs_data=$this->io_sql->select($ls_sql);
		
		if($this->rs_data==false)
		{
			$this->io_mensajes->message("CLASE->sigesp_sno_class_report MÉTODO->uf_busca_descuentos_medjudiciales_prenomina ERROR->".$this->io_sql->message);
			$lb_valido=false;
		}
		
		return $lb_valido;
	
	}
	
	function uf_busca_medjudiciales_prenomina($opciones=array()){
		
		$lb_valido=true;
		$ls_criterio="";
		
		
		$ls_sql=" SELECT p.codper,moncon,codper_medidas,p.nomper,p.apeper,p.cedper,
					p.tipnip, p.codcom,descom,p.codran, desran,cat.codcat,descat,
					desc_tippen,staper 
				FROM sno_constantepersonal c
				INNER JOIN sno_personal p ON p.codper = c.codper
				INNER JOIN sno_personalnomina pn ON pn.codper = c.codper
				INNER JOIN sno_tipo_pensionado tp ON tp.tippensionado = p.tippensionado	
				LEFT JOIN sno_rango ran ON (ran.codemp = p.codemp AND ran.codcom= p.codcom AND ran.codran = p.codran) 
				LEFT JOIN sno_categoria_rango cat  ON (cat.codemp = p.codemp AND ran.codcat = cat.codcat) 
				LEFT JOIN sno_componente com ON (com.codemp = p.codemp AND com.codcom = p.codcom) 	
				WHERE codcons='0000000211' AND c.codper = '".$opciones."'";
		
		$this->rs_data_medida=$this->io_sql->select($ls_sql);
		
		if($this->rs_data_medida==false)
		{
			$this->io_mensajes->message("CLASE->sigesp_sno_class_report MÉTODO->uf_busca_medjudiciales_prenomina ERROR->".$this->io_sql->message);
			$lb_valido=false;
		}
		
		return $lb_valido;
	
	}

	function buscar_personal($opciones=array()){
	
			if(!$opciones['criterio_consulta']){$opciones['criterio_consulta']="por_cedula";}
			
			switch($opciones['criterio_consulta']){
				  
				case "por_codper":
					$sql_criterio = " WHERE p.codper='".$opciones['codper']."' AND p.codemp ='".$this->ls_codemp."'";
					break;
			  case "por_cedula":
					$sql_criterio = " WHERE p.cedper='".$opciones['cedper']."' AND p.codemp ='".$this->ls_codemp."'";
					break;
			   case "por_autorizado":
					$sql_criterio = " WHERE p.cedaut='".$opciones['cedaut']."' AND p.codemp ='".$this->ls_codemp."'";
					break;
			   case "por_cedmil":
					$sql_criterio = " WHERE p.cedmil='".$opciones['cedmil']."' AND p.codemp ='".$this->ls_codemp."'";
					break;
			}
			
			$query_rs = "SELECT desc_tippen,p.codper,p.nomper,p.apeper,cedper,cedmil,cedaut,
								   p.tippensionado, tipnip, tipmedjudcod, staper, sueper, 
								   suebenef, parfam, nropersona,
								   pn.codban,codcueban,tipcuebanper,nomban 
							FROM sno_personalnomina pn
							INNER JOIN sno_personal p ON p.codper = pn.codper AND p.codemp = pn.codemp
							INNER JOIN sno_tipo_pensionado tp ON tp.tippensionado = p.tippensionado
							INNER JOIN scb_banco b ON b.codban = pn.codban  ".$sql_criterio;	
			
			$clase = get_class($this);
			$metodo = 'busca_personal';
			$opcionesx['arreglo'] = 'arreglo';
			$opcionesx['ajax'] = '1';			
			$msj = '<b>CLASE:</b> '.$clase.' <br><b>METODO:</b> '.$metodo;		
			$respuesta=$this->io_conexiones->conexion($query_rs,$opcionesx,$msj);						
			return $respuesta;	
	
	}
	
	function existe_personal($opciones=array()){
	
			$query_rs = "SELECT p.codper FROM sno_personal p
						 INNER JOIN sno_personalnomina pn ON p.codper = pn.codper AND p.codemp = pn.codemp
						 WHERE p.cedper='".$opciones['cedper']."'";
			
			$clase = get_class($this);
			$metodo = 'existe_personal';
			$opcionesx['arreglo'] = 'arreglo';
			$opcionesx['ajax'] = '1';			
			$msj = '<b>CLASE:</b> '.$clase.' <br><b>METODO:</b> '.$metodo;		
			$respuesta=$this->io_conexiones->conexion($query_rs,$opcionesx,$msj);						
			return $respuesta;	
	
	}
	
	function busca_constante_personal($opciones=array()){
	
			$query_rs = "SELECT p.codper,aplcon,moncon FROM sno_personal p
						 INNER JOIN sno_personalnomina pn ON p.codper = pn.codper AND p.codemp = pn.codemp
						 INNER JOIN sno_conceptopersonal cp ON p.codper = cp.codper AND p.codemp = cp.codemp
						 INNER JOIN sno_constantepersonal csp ON p.codper = csp.codper AND p.codemp = csp.codemp AND codcons = codconc
						 WHERE p.cedper='".$opciones['cedper']."' AND cp.codconc='".$opciones['codconc']."'  AND cp.codemp ='".$this->ls_codemp."' AND cp.codnom='".$this->ls_codnom."'; ";
			
			$clase = get_class($this);
			$metodo = 'busca_constante_personal';
			$opcionesx['arreglo'] = 'arreglo';
			$opcionesx['ajax'] = '1';			
			$msj = '<b>CLASE:</b> '.$clase.' <br><b>METODO:</b> '.$metodo;		
			$respuesta=$this->io_conexiones->conexion($query_rs,$opcionesx,$msj);						
			return $respuesta;	
	
	}
	
	function busca_monto_total_medidas($opciones=array()){
	
			$query_rs = "SELECT sum(moncon) AS monto FROM sno_constantepersonal WHERE codcons IN ('0000000410','0000000411'); ";			
			$clase = get_class($this);
			$metodo = 'busca_monto_total_medidas';
			$opcionesx['arreglo'] = 'arreglo';
			$opcionesx['ajax'] = '1';			
			$msj = '<b>CLASE:</b> '.$clase.' <br><b>METODO:</b> '.$metodo;		
			$respuesta=$this->io_conexiones->conexion($query_rs,$opcionesx,$msj);						
			return $respuesta['fila']['monto'];	
	
	}
	
	function insertar_conceptos_personal($opciones=array()){
	
				
			$query_conc = "INSERT INTO sno_conceptopersonal( codemp, codnom, codper, codconc, aplcon, valcon, acuemp, acuiniemp, 
															acupat, acuinipat )
						   VALUES ('".$this->ls_codemp."', '".$this->ls_codnom."', '".$opciones['codper']."', '".$opciones['codconc']."', 
						            '1', '0', '0', '0','0', '0');";
			
		    $query_cons = "INSERT INTO sno_constantepersonal (codemp,codnom,codper,codcons,moncon) 
			               VALUES ('".$this->ls_codemp."', '".$this->ls_codnom."', '".$opciones['codper']."', '".$opciones['codconc']."', '".$opciones['moncon']."');";
			
			$query_rs = $query_conc.$query_cons;
			
			$this->rs_data=$this->io_sql->select($query_rs);			
				
			if($this->rs_data==false)
			{				
				$metodo = 'insertar_conceptos_personal';
				$mensaje = '<b>CLASE:</b> '.get_class($this).' <br><b>METODO:</b> '.$metodo.' <br><b>ERROR:</b><br>'.$this->io_sql->message;					
				$this->io_conexiones->mensajes_ajax($mensaje);
				return false;
				
			}
			
			return true;
	
	}
	
	function actualizar_monto_constante($opciones=array()){
	
				
			$query_rs = "UPDATE sno_constantepersonal SET moncon = '".$opciones['moncon']."' WHERE codper='".$opciones['codper']."' AND codcons='".$opciones['codconc']."'  AND codemp ='".$this->ls_codemp."' AND codnom='".$this->ls_codnom."'; ";
			 					
			$this->rs_data=$this->io_sql->select($query_rs);			
				
			if($this->rs_data==false)
			{				
				$metodo = 'actualizar_monto_constante';
				$mensaje = '<b>CLASE:</b> '.get_class($this).' <br><b>METODO:</b> '.$metodo.' <br><b>ERROR:</b><br>'.$this->io_sql->message;					
				$this->io_conexiones->mensajes_ajax($mensaje);
				return false;
				
			}
			
			return true;
	
	}
	
	
	
	function inicializar_conceptos_sisa($opciones=array()){	
			$this->eliminar_conceptos_sisa();
			$this->eliminar_constantes_sisa();	
	
	}
	
	function eliminar_conceptos_personal($opciones=array()){
	
				$ls_sql = "DELETE FROM sno_conceptopersonal  WHERE codconc='".$opciones['codconc']."' AND codemp ='".$this->ls_codemp."' AND codnom='".$this->ls_codnom."'; ";
				$ls_sql = $ls_sql." DELETE FROM sno_constantepersonal  WHERE codcons='".$opciones['codconc']."' AND codemp ='".$this->ls_codemp."' AND codnom='".$this->ls_codnom."'; ";
				
				$this->rs_data=$this->io_sql->select($ls_sql);			
				
				if($this->rs_data==false)
				{						
					$metodo = 'eliminar_conceptos_personal';
					$mensaje = '<b>CLASE:</b> '.get_class($this).' <br><b>METODO:</b> '.$metodo.' <br><b>ERROR:</b><br>'.$this->io_sql->message;					
					$this->io_conexiones->mensajes_ajax($mensaje);
					return false;
					
				}
				
				return $this->io_sql->conn->Affected_Rows();
				
	}
	
	function eliminar_conceptos_sisa($opciones=array()){
	
				$ls_sql = "DELETE FROM sno_conceptopersonal  WHERE codconc IN ('0000000661','0000000660','0000000536') AND codemp ='".$this->ls_codemp."' AND codnom='".$this->ls_codnom."'; ";
				
				$this->rs_data=$this->io_sql->select($ls_sql);			
				
				if($this->rs_data==false)
				{						
					$metodo = 'eliminar_conceptos_personal';
					$mensaje = '<b>CLASE:</b> '.get_class($this).' <br><b>METODO:</b> '.$metodo.' <br><b>ERROR:</b><br>'.$this->io_sql->message;					
					$this->io_conexiones->mensajes_ajax($mensaje);
					return false;
					
				}
				
				return $this->io_sql->conn->Affected_Rows();
				
	}
	
	function eliminar_constantes_sisa($opciones=array()){
	
				$ls_sql = "DELETE FROM sno_constantepersonal  WHERE codcons IN ('0000000661','0000000660','0000000536') AND codemp ='".$this->ls_codemp."' AND codnom='".$this->ls_codnom."';";
				
				$this->rs_data=$this->io_sql->select($ls_sql);			
				
				if($this->rs_data==false)
				{						
					$metodo = 'eliminar_constantes_sisa';
					$mensaje = '<b>CLASE:</b> '.get_class($this).' <br><b>METODO:</b> '.$metodo.' <br><b>ERROR:</b><br>'.$this->io_sql->message;					
					$this->io_conexiones->mensajes_ajax($mensaje);
					return false;
					
				}
				
				return $this->io_sql->conn->Affected_Rows();
				
	}
	
	function eliminar_conceptos_medidas($opciones=array()){
	
				$query = "DELETE FROM sno_conceptopersonal  WHERE codconc IN ('0000000411','0000000410') AND codemp ='".$this->ls_codemp."' AND codnom='".$this->ls_codnom."'; ";
				$query2 = "DELETE FROM sno_constantepersonal  WHERE codcons IN ('0000000411','0000000410') AND codemp ='".$this->ls_codemp."' AND codnom='".$this->ls_codnom."'; ";
				
				$ls_sql = $query.$query2;
				
				$this->rs_data=$this->io_sql->select($ls_sql);			
				
				if($this->rs_data==false)
				{						
					$metodo = 'eliminar_conceptos_medidas';
					$mensaje = '<b>CLASE:</b> '.get_class($this).' <br><b>METODO:</b> '.$metodo.' <br><b>ERROR:</b><br>'.$this->io_sql->message;					
					$this->io_conexiones->mensajes_ajax($mensaje);
					return false;
					
				}
				
				return $this->io_sql->conn->Affected_Rows();
				
	}
	
	function buscar_medidas($opciones=array()){
	
			if(!$opciones['criterio_consulta']){$opciones['criterio_consulta']="pagadas";}
			$campos = " s.codper,codconc,abs(valsal) AS monto,cedmil,monnetres,cedper,tipnip,desc_tippen,
					    nomper,apeper,pn.codban,nomban,codcueban,tipcuebanper,pagbanper,pagefeper ";
							   
			switch($opciones['criterio_consulta']){
				  
			  case "pagadas":
					$sql_criterio = " AND abs(valsal)>0 AND staper=1 AND monnetres > 0 ";
					
					break;
			  case "neto_negativo":
					$sql_criterio = " AND abs(valsal)>0 AND staper=1 AND monnetres < 0 ";
					
					break;
			   case "suma_pagadas":
					$sql_criterio = " AND abs(valsal)>0 AND staper=1 AND monnetres > 0 ";
					$campos = " sum(abs(valsal)) AS total ";
					break;
			   case "suma_negativo":
					$sql_criterio = " AND abs(valsal)>0 AND staper=1 AND monnetres < 0 ";
					$campos = " sum(abs(valsal)) AS total  ";
					break;			
				
			}
			
			$query_rs = "   SELECT ".$campos." 
							FROM sno_salida s
							INNER JOIN sno_resumen r ON r.codper = s.codper
							INNER JOIN sno_personal p ON  s.codper = p.codper
							INNER JOIN sno_personalnomina pn ON  pn.codper = p.codper
							INNER JOIN sno_tipo_pensionado tp ON tp.tippensionado = p.tippensionado
							INNER JOIN scb_banco b ON b.codban = pn.codban  
							WHERE codconc IN ('0000000411','0000000410')  ".$sql_criterio;	
			
			$this->rs_data=$this->io_sql->select($query_rs);
		
			if($this->rs_data===false)
			{
				$this->io_mensajes->message("CLASE->sigesp_sno_class_report MÉTODO->buscar_medidas ERROR->".$this->io_sql->message);
				return false;
			}
			
			return true;
	
	}
	
	
	function buscar_medidas_error_banco($opciones=array()){
	
			if(!$opciones['criterio_consulta']){$opciones['criterio_consulta']="error_banco";}
			$campos = " cp.codper,codconc,abs(moncon) AS monto,cedmil,cedper,tipnip,desc_tippen,
					    nomper,apeper,pn.codban,nomban,codcueban,tipcuebanper,pagbanper,pagefeper,staper ";
							   
			switch($opciones['criterio_consulta']){
				  
			  case "error_banco":
					$sql_criterio = " AND moncon<>0 AND (staper = 9 OR staper=4) ";
					
					break;
			 
			   case "suma_error_banco":
					$sql_criterio = "  AND moncon<>0 AND (staper = 9 OR staper=4) ";
					$campos = " sum(abs(moncon)) AS total ";
					break;
			}
			
			$query_rs = "   SELECT ".$campos." 
							FROM sno_conceptopersonal cp
							INNER JOIN sno_constantepersonal csp ON csp.codper = cp.codper AND csp.codcons = cp.codconc AND csp.codemp = cp.codemp
							INNER JOIN sno_personal p ON  cp.codper = p.codper
							INNER JOIN sno_personalnomina pn ON  pn.codper = p.codper
							INNER JOIN sno_tipo_pensionado tp ON tp.tippensionado = p.tippensionado
							INNER JOIN scb_banco b ON b.codban = pn.codban  
							WHERE cp.codconc IN ('0000000411','0000000410')  ".$sql_criterio;	
			
			$this->rs_data=$this->io_sql->select($query_rs);
		
			if($this->rs_data===false)
			{
				$this->io_mensajes->message("CLASE->sigesp_sno_class_report MÉTODO->buscar_medidas ERROR->".$this->io_sql->message);
				return false;
			}
			
			return true;	
	}
	
	function buscar_pagos_terceros($opciones=array()){
	
			$campo_conc = '';		
			switch($opciones['criterio_consulta']){
				  
			  case "por_concepto":
					$campo_conc = 'c.codconc,nomcon,';				
					break;	 
			
			}
			
			$query_rs = "   SELECT ".$campo_conc." codente,descripcion_ente,sum(abs(valsal)) AS monto,
			                       (porcentaje_ente::double precision*sum(abs(valsal))/100) AS porcentaje
							FROM sno_salida s
							INNER JOIN sno_resumen r ON r.codper = s.codper
							INNER JOIN sno_personal p ON  s.codper = p.codper
							INNER JOIN sno_personalnomina pn ON  pn.codper = p.codper
							INNER JOIN sno_tipo_pensionado tp ON tp.tippensionado = p.tippensionado
							INNER JOIN scb_banco b ON b.codban = pn.codban 
							INNER JOIN sno_concepto c ON c.codconc = s.codconc AND c.codemp = s.codemp
							INNER JOIN sno_entes e ON c.codente = e.codigo_ente
							WHERE monnetres>0 AND staper=1 AND codente!='0000000000' AND codente!='0' AND valsal<>0 ".$sql_criterio." 
							GROUP BY ".$campo_conc." c.codente,descripcion_ente,porcentaje_ente  ORDER BY codente ";	
			
			$this->rs_data=$this->io_sql->select($query_rs);
		
			if($this->rs_data===false)
			{
				$this->io_mensajes->message("CLASE->sigesp_sno_class_report MÉTODO->buscar_pagos_terceros ERROR->".$this->io_sql->message);
				return false;
			}
			
			return true;	
	}
	
	
	function buscar_pagos_cheques($opciones=array()){
	
					
			$query_rs = "  SELECT p.codper,monnetres,p.nomper,p.apeper,p.cedper,p.cedmil,p.cedaut AS ced_bene ,p.nomaut AS nombene,
								   titular.cedper AS cedtit, causante.cedper AS cedcau, causante.tipnip AS tipnipcau,
								   titular.nomper AS nomtit, titular.apeper AS apetit, titular.tipnip AS tipniptit,
								   causante.nomper AS nomcau, causante.apeper AS apecau,
								   titular.tipnip AS tipniptit,causante.tipnip AS tipnipcau, 
								   p.codcom,descom,p.codran, desran,cat.codcat,descat,
								   p.parfam,p.parentesco,desc_tippen,
								   codban,codcueban,tipcuebanper,pagbanper,pagefeper				 
							FROM sno_personal p			
							INNER JOIN sno_personalnomina pn ON pn.codper = p.codper
							INNER JOIN sno_resumen r ON pn.codper = r.codper
							LEFT JOIN sno_personal titular ON titular.codper = p.codper
							LEFT JOIN sno_personal causante ON causante.cedper = titular.cedmil
							INNER JOIN sno_tipo_pensionado tp ON tp.tippensionado = p.tippensionado	
							LEFT JOIN sno_rango ran ON (ran.codemp = p.codemp AND ran.codcom= p.codcom AND ran.codran = p.codran) 
							LEFT JOIN sno_categoria_rango cat  ON (cat.codemp = p.codemp AND ran.codcat = cat.codcat) 
							LEFT JOIN sno_componente com ON (com.codemp = p.codemp AND com.codcom = p.codcom) 		 
							WHERE  pagefeper = 1 AND monnetres>0 ORDER BY lpad(p.cedper, 10,'0')";	
			
			$this->rs_data=$this->io_sql->select($query_rs);
		
			if($this->rs_data===false)
			{
				$this->io_mensajes->message("CLASE->sigesp_sno_class_report MÉTODO->buscar_pagos_terceros ERROR->".$this->io_sql->message);
				return false;
			}
			
			return true;	
	}
	
	function total_pagos_cheques($opciones=array()){
	
					
			$query_rs = "   SELECT SUM(monnetres) AS total				 
							FROM sno_personal p			
							INNER JOIN sno_personalnomina pn ON pn.codper = p.codper
							INNER JOIN sno_resumen r ON pn.codper = r.codper								 
							WHERE  pagefeper = 1 AND monnetres>0 ";	
			
			$this->rs_data=$this->io_sql->select($query_rs);
		
			if($this->rs_data===false)
			{
				$this->io_mensajes->message("CLASE->sigesp_sno_class_report MÉTODO->buscar_pagos_terceros ERROR->".$this->io_sql->message);
				return false;
			}
			
			return $this->rs_data->fields['total'];	
	}
	
	
	
	
	function buscar_concepto_sueldo($opciones=array()){
	
					
			$query_rs = "   SELECT * 		 
							FROM sno_salida s		
							INNER JOIN sno_concepto c ON c.codconc = s.codconc AND c.codemp = s.codemp														 
							WHERE  valsal<>0 AND codper = '".$opciones['codper']."' AND sigcon='A'";	
			
			$this->rs_data=$this->io_sql->select($query_rs);
			
			foreach($this->rs_data as $filas){
					switch($filas['codconc']){
								
								case '0000000211':
										return $filas['nomcon'];
										break;
								
								case '0000000210':
										return $filas['nomcon'];
										break;
								
								case '0000000003':
										return $filas['nomcon'];
										break;
								
								case '0000000001':
										return $filas['nomcon'];
										break;
						}
			}
			
			if($this->rs_data===false)
			{
				$this->io_mensajes->message("CLASE->sigesp_sno_class_report MÉTODO->buscar_pagos_terceros ERROR->".$this->io_sql->message);
				return false;
			}
			
			return false;	
	}
	
	
	function consulta_tipnip($opciones=array()){	
										   
		$query_rs = "SELECT DISTINCT tipnip FROM sno_personal WHERE tipnip!='' OR tipnip!=NULL";
		
		$clase = get_class($this);
		$metodo = 'consulta_tipnip';
		$param['arreglo'] = 'arreglo';
		$param['ajax'] = '0';
		$param['imprimir'] = '1';	
		$msj = '<b>CLASE:</b> '.$clase.' <br><b>METODO:</b> '.$metodo;		
		return $respuesta=$this->io_conexiones->conexion($query_rs,$param,$msj);
	}
	
	
	function combo_tipnip($opciones=array()){

				if(!$opciones['nombre_combo']){$nombre_combo = 'cmb_tipnip';}else{$nombre_combo = $opciones['nombre_combo'];}
				if(!$opciones['tipnip']){$carga = 'V'; $id_carga = 'V';}
				else{$carga = $opciones['tipnip'];  $id_carga = $opciones['tipnip'];}
				
				global $obj_sql;
				
				$resultado = $this->consulta_tipnip();
				
				$combo = '<select name="'.$nombre_combo.'" id="'.$nombre_combo.'" onChange="'.$opciones['funcion_js'].'">
				          <option value="'.$id_carga.'">- '.$carga.' -</option>';
				
				do { 				
					$combo .= '<option value="'.$resultado['fila']["tipnip"].'">'.$resultado['fila']["tipnip"].'</option>';								
				} while ($resultado['fila'] = $obj_sql->fetch_row($resultado['rs'])); 
				$combo .= '</select>';
																							
				return $combo;

	}

	
	
	
	
}







?>
