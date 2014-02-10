<?php 



//$this->setColor($this->ColorTextoActual[$this->nStackColor]['r'],$this->ColorTextoActual[$this->nStackColor]['g'],$this->ColorTextoActual[$this->nStackColor]['b']);
//$this->objects[$this->currentContents]['c'].="\n".sprintf('%.3f',$this->ColorTextoActual[$this->nStackColor]['r']).' '.sprintf('%.3f',$this->ColorTextoActual[$this->nStackColor]['g']).' '.sprintf('%.3f',$this->ColorTextoActual[$this->nStackColor]['b']).' rg';												
//echo 'cierre '.$this->nStackColor.'<br>';
array_pop($this->ColorTextoActual); 
$this->nStackColor--;



if($this->ProcesandoTexto == 1){
												
$this->nStackColor++;
if($this->currentColour['r'] == -1){$this->currentColour['r'] = 0;}
if($this->currentColour['g'] == -1){$this->currentColour['g'] = 0;}
if($this->currentColour['b'] == -1){$this->currentColour['b'] = 0;}
$this->ColorTextoActual[$this->nStackColor]['r'] = $this->currentColour['r'];
$this->ColorTextoActual[$this->nStackColor]['g'] = $this->currentColour['g'];
$this->ColorTextoActual[$this->nStackColor]['b'] = $this->currentColour['b'];			
//echo $this->nStackColor.'<br>';
$this->objects[$this->currentContents]['c'].="\n".sprintf('%.3f',$resultado['valor']['R']).' '.sprintf('%.3f',$resultado['valor']['G']).' '.sprintf('%.3f',$resultado['valor']['B']).' rg';												
}
//$this->setColor($resultado['valor']['R'],$resultado['valor']['G'],$resultado['valor']['B']);
//$this->currentTextState = $resultado['funcion'];	


var $ColorTextoActual = array('r'=>-1,'g'=>-1,'b'=>-1);
var $nStackColor = 0;
var $FuncionTexto = array();


if(isset($this->FuncionTexto['funcion'])){
			  			
						if($this->FuncionTexto['funcion'] == 'color' and $this->FuncionTexto['tag'] == 'abrir'){
								
								$this->nStackColor++;
								if($this->currentColour['r'] == -1){$this->currentColour['r'] = 0;}
								if($this->currentColour['g'] == -1){$this->currentColour['g'] = 0;}
								if($this->currentColour['b'] == -1){$this->currentColour['b'] = 0;}
								$this->ColorTextoActual[$this->nStackColor]['r'] = $this->currentColour['r'];
								$this->ColorTextoActual[$this->nStackColor]['g'] = $this->currentColour['g'];
								$this->ColorTextoActual[$this->nStackColor]['b'] = $this->currentColour['b'];
								
								$this->objects[$this->currentContents]['c'].="\n".sprintf('%.3f',$this->FuncionTexto['valor']['R']).' '.sprintf('%.3f',$this->FuncionTexto['valor']['G']).' '.sprintf('%.3f',$this->FuncionTexto['valor']['B']).' rg';												
								//$this->FuncionTexto['tag'] = '';
						
						}
						if($this->FuncionTexto['funcion'] == 'color' and $this->FuncionTexto['tag'] == 'cerrar'){
						
								$this->objects[$this->currentContents]['c'].="\n".sprintf('%.3f',$this->ColorTextoActual[$this->nStackColor]['r']).' '.sprintf('%.3f',$this->ColorTextoActual[$this->nStackColor]['g']).' '.sprintf('%.3f',$this->ColorTextoActual[$this->nStackColor]['b']).' rg';												
								array_pop($this->ColorTextoActual); 
								$this->nStackColor--;
								
								//$this->FuncionTexto['tag'] = '';
						
						}
						
						$this->FuncionTexto = array();
						
			  
			  } 	  
			
		  }


function extrae_etiqueta($texto){
		//FUNCIÓN QUE EXTRAE LOS DATOS DE LAS ETIQUETAS DE TIPO <%pdf 
		
		$longitud = strlen($texto);
		$bandera = 0;
		$parametros = array();
		
		$funcion[] = 'color';
		$funcion[] = 'resaltado';
		$funcion[] = 'color_celda';	
		
		for($i=0;$i<$longitud;$i++){
		
					if($texto[$i] =='<'){
					
							$i++;
							if($texto[$i] == '%'){
									
									$tag_pdf = substr($texto, $i+1 , 3);
									
									if($tag_pdf == 'pdf'){
									
													//echo 'El tag es: '.$tag_pdf.'<br>';
													$i = $i-1;	
													//echo 'El comienzo es: '.$i.'<br>';
													$pos_inicio = $i;
													$parametros['pos_inicio'] = $i;
													$bandera = 1;
										}
							}
					
					}
		
					
					
					if($texto[$i] == '>' and $bandera == 1){
							
							$pos_final = $i;
							$parametros['pos_final'] = $i;
							$bandera = 1;
							//echo 'El final es: '.$i.'<br>';
							$logitud = ($pos_final - $pos_inicio )+ 1;
							$parametros['longitud_etiqueta'] = $logitud;
							//echo 'La longitud es: '.$logitud.'<br>';
							$etiqueta = substr($texto,$pos_inicio,$logitud);
							$parametros['etiqueta'] = $etiqueta;							
							//echo 'La etiqueta es: '.$etiqueta.'<br>';														
													
							$etiqueta_low = strtolower($etiqueta);
							
							foreach($funcion as $k=>$V){
							
									$posicion = strpos($etiqueta_low, $funcion[$k]);
									
									if ($posicion === false) {
										//echo "No se encontro '$funcion[$k]' en la cadena '$etiqueta' <br>";										
									} else {
										//echo "Se encontro '$funcion[$k]' en la cadena '$etiqueta'";
										//echo " en la posicion $posicion <br>";
										$funcion = $funcion[$k];
										$parametros['funcion'] = $funcion;
										break;
									}
							
							}					
							
							
						if($posicion === false){
							
								return false;
							  
						 }
						 else{
						 
						 	  $reemplazo = array("", "", "", "", "","","","");
							  $texto_no_deseado = array("=", "#", " ", "<%pdf", ">",'"',"'",$funcion);				  
							  $valor=str_replace($texto_no_deseado,$reemplazo,$etiqueta);
							  //echo $color.'<br>';
							  if($parametros['funcion'] == 'color'){$parametros['valor'] = $this->decodifica_color($valor);}
							  if($parametros['funcion'] == 'resaltado'){$parametros['valor'] = $this->decodifica_color($valor);}
							  $parametros['tag'] = 'abrir'; 
							  return $parametros;
						 
						 } 
						  //echo 'color = ('.$color_decimal['R'].','.$color_decimal['G'].','.$color_decimal['B'].')';										
					}
					
		
		}
		
		
}

function extrae_etiqueta_cierre($texto){
		//FUNCIÓN QUE EXTRAE LOS DATOS DE LAS ETIQUETAS DE TIPO <%pdf 
		
		$longitud = strlen($texto);
		$bandera = 0;
		$parametros = array();
		
		$funcion = array();
		$funcion[] = 'color';
		$funcion[] = 'resaltado';
		$funcion[] = 'color_celda';	
		
		for($i=0;$i<$longitud;$i++){
		
					if($texto[$i] =='<'){
					
							$i++;
							if($texto[$i] == '%'){
									
									$i++;									
									if($texto[$i] == '/'){
									
												$i = $i-2;	
												//echo 'El comienzo es: '.$i.'<br>';
												$pos_inicio = $i;
												$parametros['pos_inicio'] = $i;
												$bandera = 1;
										
									}
									
									
							}
							
					 }
		
					
					
					if($texto[$i] == '>' and $bandera == 1){
							
							$pos_final = $i;
							$parametros['pos_final'] = $i;
							$bandera = 1;
							//echo 'El final es: '.$i.'<br>';
							$logitud = ($pos_final - $pos_inicio )+ 1;
							$parametros['longitud_etiqueta'] = $logitud;
							//echo 'La longitud es: '.$logitud.'<br>';
							$etiqueta = substr($texto,$pos_inicio,$logitud);
							$parametros['etiqueta'] = $etiqueta;							
							//echo 'La etiqueta es: '.$etiqueta.'<br>';														
													
							$etiqueta_low = strtolower($etiqueta);
							
							foreach($funcion as $k=>$V){
							
									$posicion = strpos($etiqueta_low, $funcion[$k]);
									
									if ($posicion === false) {
										//echo "No se encontro '$funcion[$k]' en la cadena '$etiqueta' <br>";										
									} else {
										//echo "Se encontro '$funcion[$k]' en la cadena '$etiqueta'";
										//echo " en la posicion $posicion <br>";
										$funcion = $funcion[$k];
										$parametros['funcion'] = $funcion;
										$parametros['tag'] = 'cerrar';
										return $parametros;
									}
							
							}//fin foreach					
					
					}//fin if
				
				
		}// for
		
		
}



function decodifica_color($color1) {
        
        $color1 = str_replace("#", '', $color1);
		       
       	$valor = 255;
        $r = hexdec(substr($color1, 0, 2))/$valor;
        $g = hexdec(substr($color1, 2, 2))/$valor;
        $b = hexdec(substr($color1, 4, 2))/$valor;
			
        $color = array();
		$color['R'] = $r;
		$color['G'] = $g;
		$color['B'] = $b;
		
        return $color;
       
}

?>