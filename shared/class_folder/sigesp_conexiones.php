<?php
class conexiones {
	
	function conexiones(){
			
			global $ruta;
			if(!isset($ruta)){$ruta="../";}
			require_once($ruta."shared/class_folder/class_mensajes.php");
			$this->obj_msj=new class_mensajes();
	
	
	}
						
	function codificacion_navegador(){
				
				global $navegador;
				$navegador = strpos($_SERVER['HTTP_USER_AGENT'],'MSIE');
				
				if($navegador === false){
					$navegador = 'FIREFOX';		
					header('Content-Type: text/html; charset=utf-8');
				}
				else{
					
					$navegador = 'INTERNET EXPLORER';				
					header('Content-Type: text/html; charset=ISO-8859-1');
				}
				
	}

	function decodificar_post(){
	
			
			foreach ($_POST as $k=>$v) {
			  $_POST[$k] = utf8_decode($v);
			} 

	
	
	}
	
	function decodificar_get(){
	
			
			foreach ($_GET as $k=>$v) {
			  $_GET[$k] = utf8_decode($v);
			} 

	
	
	}
	
	
	function covertir_getpost(){
	
			
			foreach ($_GET as $k=>$v) {
			  $_POST[$k] = $v;
			} 

	
	
	}
	
	function asignar_post(){	
			
			$resultado = array();
			
			foreach ($_POST as $k=>$v) {
			  $resultado[$k] = $v;
			}
			
			return $resultado;
	
	}
	
	function asignar_get(){	
			
			$resultado = array();
			
			foreach ($_GET as $k=>$v) {
			  $resultado[$k] = $v;
			}
			
			return $resultado;
	
	}
							
	function nombre_form(){
					
		$formulario = split('/',$_SERVER['PHP_SELF']);
		$cantidad = count($formulario);
		$formulario = $formulario[$cantidad - 1];
		return $formulario;
	}
							
							
	function conexion($query_rs,$opciones,$informacion = '',$modo='select',$campo_id = '',$base_de_datos = '',$gestor = '',$usuario=''){
			
			global $obj_sql,$msj_error_conex,$ruta,$conex_ajax;
			
			if(is_array($opciones)){
					if($opciones['modo']){$modo=$opciones['modo'];}
					if($opciones['mensaje']){$informacion=$opciones['mensaje'];}
			}
			
			
			if($_SESSION["ls_gestor"] == 'POSTGRES' and $modo=='insert'){$query_rs = $query_rs.'; SELECT lastval() AS valor_id; ';}
			
			$in=new sigesp_include();
			
			if($gestor == ''){$gestor = $_SESSION["ls_gestor"];}
			if($usuario==''){$usuario = $_SESSION["ls_login"];}
			
			if($base_de_datos==''){$con=$in->uf_conectar();}
			else{$con=$in->uf_conectar_otra_bd($_SESSION["ls_hostname"],$usuario,$_SESSION["ls_password"],$base_de_datos,$gestor);}
			
			$obj_sql=new class_sql($con);
			$this->conex =$obj_sql;				
			$rs_data = $obj_sql->select($query_rs);			
			$this->rs_datos = $rs_data;
			
			if($rs_data === false){
						
						$msjerror = '<br><b>ERROR-></b><br>'.$informacion.$obj_sql->message;
						
						if($opciones['imprimir'] == 1){
								$this->obj_msj->message($msjerror);
								return false;
						}	
						
						if($opciones['ajax'] == 1){
								$this->mensajes_ajax($msjerror);
								return false;
						}																							
						
						echo $msjerror;	
						if($tipo_mensaje=='clase_msj'){								
								$this->obj_msj->message($msjerror,2,$ruta);
						}
						return false;						
						
			}
			$cantidad = $obj_sql->num_rows($rs_data);
			$row=$obj_sql->fetch_row($rs_data);
				
			switch($modo){
  
					  case "consulta":					  				
								return array('rs'=>$rs_data, 'fila'=>$row, 'cantidad'=>$cantidad);	
							break;
					  
					  case "update":					  				
								//return array('filas_afectadas'=>$rs_data->Affected_Rows());
							break;
							
					  case "delete":
								//return array('filas_afectadas'=>$rs_data->Affected_Rows($con));
							break;
							
					  case "insert":
								if($_SESSION["ls_gestor"] == 'MYSQLT' or $_SESSION["ls_gestor"] == 'MYSQLT'){ return mysql_insert_id();}
								if($_SESSION["ls_gestor"] == 'POSTGRES'){$insert_id = $row['valor_id']; return $insert_id;}
							break;
							
					   case "select":
								if($opciones == 'arreglo' or $opciones['arreglo'] == 'arreglo'){return array('rs'=>$rs_data, 'fila'=>$row, 'cantidad'=>$cantidad);}
								elseif($opciones == 'fila' or $opciones['fila'] == 1){return $row;}		
							break;
								
			}	
								
			$obj_sql->free_result($rs_data);

	}
	
	function formatea_fecha_bd($var_fecha){// CONVIERTE LA FECHA A MYSQL o POSTGRES
			//para los días que trae un solo dígito se le agrega cero a la izquierda e igual para el mes.
			ereg( "([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $var_fecha, $lugarx);
			$dia = strlen($lugarx[1]); if($dia==1){$dia_x.="0".$lugarx[1];}else{$dia_x=$lugarx[1];}
			$mes = strlen($lugarx[2]); if($mes==1){$mes_x.="0".$lugarx[2];}else{$mes_x=$lugarx[2];}
			$year_x = $lugarx[3];
			$fechaMySql=$year_x."-".$mes_x."-".$dia_x;
			if(!$fechaMySql){$fechaMySql="0000/00/00";}			
			return $fechaMySql;
		} 
	
	function formatea_fecha_normal($fechax){// CONVIERTE LA FECHA A MYSQL A NORMAL.
		ereg( "([0-9]{2,4})-([0-9]{1,2})-([0-9]{1,2})", $fechax, $lugarx);
		$dia = strlen($lugarx[3]); if($dia==1){$dia_x.="0".$lugarx[3];}else{$dia_x=$lugarx[3];}
		$mes = strlen($lugarx[2]); if($mes==1){$mes_x.="0".$lugarx[2];}else{$mes_x=$lugarx[2];}
		$year_x = $lugarx[1];
		$fec_normal=$dia_x."/".$mes_x."/".$year_x;
		if(!$fechax){$fec_normal='';}
		return $fec_normal;
	}
	
	
	function extrae_hora($hora_x){//EXTRAE LA HORA
		$hora = strtotime($hora_x);
		$var_hora = date('h',$hora);
		return $var_hora;
	}
	function extrae_minutos($hora_x){//EXTRAE LOS MINUTOS
		$hora = strtotime($hora_x);
		$var_minuto = date('i',$hora);
		return $var_minuto;
	}
	
	function conforma_hora($hora_x, $minutos_x){//CONFORMA LA HORA
		$hora = $hora_x.':'.$minutos_x.':00';
		return $hora;
	}
	
	function mensajes_ajax($msj,$tipo_msj=''){
			
			if($tipo_msj=='error'){$msj = '<b>ERROR:</b> <br>'.$msj;}
			echo '<input type="hidden" name="txt_msj_ajax" id="txt_msj_ajax" value="'.$msj.'">';		
	
	}
	
	
	function compara_fechas($fecha1,$fecha2){
            

		  	if (preg_match("/[0-9]{1,2}\/[0-9]{1,2}\/([0-9][0-9]){1,2}/",$fecha1))
				  list($dia1,$mes1,$año1)=split("/",$fecha1);
				
	
		  	if (preg_match("/[0-9]{1,2}-[0-9]{1,2}-([0-9][0-9]){1,2}/",$fecha1))
				  list($dia1,$mes1,$año1)=split("-",$fecha1);
				  
		  	if (preg_match("/[0-9]{1,2}\/[0-9]{1,2}\/([0-9][0-9]){1,2}/",$fecha2))
				  list($dia2,$mes2,$año2)=split("/",$fecha2);
				
	
		  	if (preg_match("/[0-9]{1,2}-[0-9]{1,2}-([0-9][0-9]){1,2}/",$fecha2))
				  list($dia2,$mes2,$año2)=split("-",$fecha2);
				  
			$dif = mktime(0,0,0,$mes1,$dia1,$año1) - mktime(0,0,0, $mes2,$dia2,$año2);
			return ($dif);                         
            

	}
	
	function calcular_tiempo ($ad_fecha,$ld_fecact='')
	{
		
		////////////////////////////////////////////////////////////////////////////////////////////////////////	   
	    //    Function:   calcular_tiempo
	    //    Descripción: Calcula en tiempo en dias, meses y anos entre dos fechas
	    ////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		if($ld_fecact==''){$ld_fecact=date("d/m/Y");}
		
		$diaActual  = substr($ld_fecact, 0,2);  
		$mesActual = substr($ld_fecact,3,2);  
		$anioActual = substr($ld_fecact,6,4); 
		
		$diaInicio = substr($ad_fecha,0,2);  
		$mesInicio = substr($ad_fecha,3,2);  
		$anioInicio = substr($ad_fecha,6,4);  
		
		$b = 0;  
		$mes = $mesActual-1; 
		if($mes==2)
		{  
			if(($anioActual%4==0 && $anioActual%100!=0) || $anioActual%400==0)
			{  
				$b = 29;  
			}
			else
			{ 
				$b = 28;  
			}  
		}  
		else if($mes<=7)
		{  
			if($mes==0)
			{  
				$b = 31;  
			}  
			else if($mes%2==0)
			{  
				$b = 30;  
			}  
			else
			{  
				$b = 31;  
			}  
		}  
		else if($mes>7)
		{  
			if($mes%2==0)
			{  
				$b = 31;  
			}  
			else
			{  
				$b = 30;  
			}  
		}  
		
		if($mesInicio <= $mesActual)
		{  
			$anios = $anioActual - $anioInicio;  
			if($diaInicio <= $diaActual)
			{  
				$meses = $mesActual - $mesInicio;  
				$dies = $diaActual - $diaInicio;  
			}
			else
			{  
				if($mesActual == $mesInicio)
				{  
					$anios = $anios - 1;  
				}  
				$meses = ($mesActual - $mesInicio - 1 + 12) % 12;  
				$dies = $b-($diaInicio-$diaActual);  
			}  
		}
		else
		{  
			$anios = $anioActual - $anioInicio - 1;  
			if($diaInicio > $diaActual)
			{  
				$meses = $mesActual - $mesInicio -1 +12;  
				$dies = $b - ($diaInicio-$diaActual);  
			}
			else
			{  
				$meses = $mesActual - $mesInicio + 12;  
				$dies = $diaActual - $diaInicio;  
			}  
		} 
		
		$tiempo['dias']=$dies;
		$tiempo['meses']=$meses;
		$tiempo['anos']=$anios;
		
		return $tiempo;
		 
			
	}// end function
	
	function clacula_dias($fec_desde,$fec_hasta){					
				return $this->calcula_dias($fec_desde,$fec_hasta);
	}
	
	
	function calcula_dias($fec_desde,$fec_hasta){
					
				$dia_d  = substr($fec_desde, 0,2);  
				$mes_d = substr($fec_desde,3,2);  
				$ano_d = substr($fec_desde,6,4); 
				
				$dia_h = substr($fec_hasta,0,2);  
				$mes_h = substr($fec_hasta,3,2);  
				$ano_h = substr($fec_hasta,6,4); 
				
				$timestamp1 = mktime(0,0,0,$mes_h,$dia_h,$ano_h);
				$timestamp2 = mktime(0,0,0,$mes_d,$dia_d,$ano_d);
				
				//resto a una fecha la otra
				$segundos_diferencia = $timestamp1 - $timestamp2;
				
				
				//convierto segundos en días
				$dias_diferencia = $segundos_diferencia / (60 * 60 * 24);
				
				//obtengo el valor absoulto de los días (quito el posible signo negativo)
				$dias_diferencia = abs($dias_diferencia);
				
				//quito los decimales a los días de diferencia
				//$dias_diferencia = floor($dias_diferencia);
				
				return $dias_diferencia;
	
	
	}
	
	function generar_cadena_in($arreglo=array()){
		
		$i=0;
		$cadena = "(";
		foreach($arreglo as $elemento){
			if($elemento){		    
				if($i==0){$cadena .= "'".$elemento."'";}
				if($i>0){$cadena .= ",'".$elemento."'";}
				$i++;	
			}	
		}
		$cadena .= ") ";
		return $cadena;	
	}
	
}//fin de la clase conexiones


?>