<?php 
include_once('class.ezpdf.php');

class covensol_pdf extends Cezpdf{

			function convensol_pdf($tamaño='LETTER',$orientacion='portrait'){
			
			
					$this->Cezpdf($tamaño,$orientacion);
			
			
			}
			
			function parametros_por_defecto($defaults,$opciones){
			
			
					if (!is_array($opciones)){
						$opciones=array();
					  }
					
					if (!is_array($defaults)){
						$defaults=array();
					  }
					
					
					  foreach($defaults as $key=>$value){
						if (is_array($value)){
						 	foreach($value as $key2=>$value2){
								if (!isset($opciones[$key][$key2])){								
									$opciones[$key][$key2]=$value2;
								}	
							}													 
						} else {
						  if (!isset($opciones[$key])){
							$opciones[$key]=$value;
						  }
						}
					  }
			
					return $opciones;
			
			}
			
			
			function dibuja_tabla_con_encabezado($opciones=array())
				{
					$columna[1] = 'columna1';
					$defaults = array(
										'tamano_titulo'=>10,'tamano_letra'=>10,'color_fondo'=>array(1,1,1),'ancho_tabla'=>530,
										'orientacion'=>'center','justificacion_texto'=>'left','mostrar_lineas'=>1,
										'datos'=>array('0'=>array($columna[1]=>'')),'texto_titulo'=>' ',
										'color_fondo_titulo'=>array(0.1,0.4,0.6),'color_letra_titulo'=>array(1,1,1)	
									  );
								
					$opciones = $this->parametros_por_defecto($defaults,$opciones);
					
					
					$la_columnas=array($columna[1]=>$opciones['texto_titulo']);										   
					
					$la_config=array('showHeadings'=>1, // Mostrar encabezados
									 'fontSize' => $opciones['tamano_letra'], // Tamaño de Letras
									 'titleFontSize' => $opciones['tamano_titulo'],  // Tamaño de Letras de los títulos
									 'showLines'=>$opciones['mostrar_lineas'], // Mostrar Líneas
									 'shaded'=>2, // Sombra entre líneas
									 'shadeCol' => $opciones['color_fondo'],
									 'shadeCol2' => $opciones['color_fondo'],									 
									 'width'=>$opciones['ancho_tabla'], // Ancho de la tabla
									 'maxWidth'=>$opciones['ancho_tabla'], // Ancho Máximo de la tabla
									 'extraer_datos'=>'si',
									 'xOrientation'=>$opciones['orientacion'], // Orientación de la tabla
									 'outerLineThickness'=>0.5,
									 'innerLineThickness' =>0.5,
									 'Titulo_Color'  => 'si', // Para poner color de fondo y de letra al titulo
									 'TituloCol' => $opciones['color_fondo_titulo'], //Color de fondo del titulo
									 'Letra_Titulo' => $opciones['color_letra_titulo'],  //Color de letra del titulo
									 'cols'=>array($columna[1]=>array('justification'=>$opciones['justificacion_texto'],'width'=>$opciones['ancho_tabla'])),
									 'cabecera_cols'=>array($columna[1]=>array('justification'=>'center'))		   
												   ); // Justificación y ancho de la columna
					
					$this->ezTable($opciones['datos'],$la_columnas,'',$la_config);
				}// fin método dibuja_tabla_con_encabezado
			
			
			function dibuja_tabla($data1='',$data2='',$opciones=array()){
				
				$defaults = array(
										'tamano_titulo'=>10,'tamano_letra'=>8,'color_fondo'=>array(1,1,0.8),'ancho_tabla'=>300,
										'orientacion'=>'center','justificacion_texto'=>'left','mostrar_lineas'=>1,
										'color_linea'=>array(0.2,0.2,0.2),'ancho_1'=>'180','ancho_2'=>'350',
										'justificacion_texto1'=>'right','justificacion_texto2'=>'left',
										'datos_columna1'=>'','datos_columna2'=>'',
										'color_texto_columna1'=>array(0,0,0),'color_texto_columna2'=>array(0,0,0),
										'color_fondo_titulo'=>array(0,0,0.5),'color_letra_titulo'=>array(1,1,1)	
									  );
								
				$opciones = $this->parametros_por_defecto($defaults,$opciones);
				
				$columnax[1] = '1';
				$columnax[2] = '2';
				$la_data[0]=array($columnax[1]=>$data1,$columnax[2]=>$data2);	
					
					$la_columnas=array($columnax[1]=>'1',$columnax[2]=>'2');					   
					$la_config=array('showHeadings'=>0, // Mostrar encabezados
									 'fontSize' =>$opciones['tamano_letra'], // Tamaño de Letras
									 'titleFontSize' => $opciones['tamano_titulo'],  // Tamaño de Letras de los títulos
									 'showLines'=>0, // Mostrar Líneas
									 'lineCol' => $opciones['color_linea'],
									 'shaded'=>2, // Sombra entre líneas
									 'shadeCol' => $opciones['color_fondo'],
									 'shadeCol2' => $opciones['color_fondo'],
									 'extraer_datos'=>'si',
									 'width'=>$opciones['ancho_tabla'], // Ancho de la tabla
									 'maxWidth'=>$opciones['ancho_tabla'], // Ancho Máximo de la tabla
									 'xOrientation'=>'center', // Orientación de la tabla
									 'outerLineThickness'=>0.3,
									 'innerLineThickness' =>0.3,
									 'Titulo_Color'  => 'no', // Para poner color de fondo y de letra al titulo
									 'TituloCol' => $opciones['color_fondo_titulo'], //Color de fondo del titulo
									 'Letra_Titulo' => $opciones['color_letra_titulo'],  //Color de letra del titulo
									 'cols'=>array($columnax[1]=>array('justification'=>$opciones['justificacion_texto1'],'width'=>$opciones['ancho_1'],'color'=>$opciones['color_texto_columna1']),
												   $columnax[2]=>array('justification'=>$opciones['justificacion_texto2'],'width'=>$opciones['ancho_2'],'color'=>$opciones['color_texto_columna2'])
									  )
									  ); // Justificación y ancho de la columna
					
					$this->ezTable($la_data,$la_columnas,'',$la_config);
			}
			
			function dibuja_tabla_2_columnas($opciones){
					
					$columnax[1] = 'columna1';
					$columnax[2] = 'columna2';	
					
					$defaults = array(
										'tamano_titulo'=>10,'tamano_letra'=>10,'color_fondo'=>array(1,1,1),'ancho_tabla'=>530,
										'orientacion'=>'center','justificacion_texto'=>'left','mostrar_lineas'=>1,
										'ancho_1'=>'265','ancho_2'=>'265','texto_titulo1'=>'','texto_titulo2'=>'',
										'datos'=>array('0'=>array($columnax[1]=>'',$columnax[2]=>' ')),
										'color_fondo_titulo'=>array(0.1,0.4,0.6),'color_letra_titulo'=>array(1,1,1)	
									  );
								
					$opciones = $this->parametros_por_defecto($defaults,$opciones);
					
								
					$la_columnas=array($columnax[1]=>$opciones['texto_titulo1'],$columnax[2]=>$opciones['texto_titulo2']);
										   
					$la_config=array('showHeadings'=>1, // Mostrar encabezados
									 'fontSize' => $opciones['tamano_letra'], // Tamaño de Letras
									 'titleFontSize' => $opciones['tamano_titulo'],  // Tamaño de Letras de los títulos
									 'showLines'=>$opciones['mostrar_lineas'], // Mostrar Líneas
									 'lineCol' => array(0.2,0.2,0.2),
									 'shaded'=>2, // Sombra entre líneas
									 'shadeCol' => $opciones['color_fondo'],
									 'shadeCol2' => $opciones['color_fondo'],
									 'width'=>$opciones['ancho_tabla'], // Ancho de la tabla
									 'extraer_datos'=>'si',
									 'maxWidth'=>300, // Ancho Máximo de la tabla
									 'xOrientation'=>$opciones['orientacion'], // Orientación de la tabla
									 'outerLineThickness'=>0.3,
									 'innerLineThickness' =>0.3,
									 'Titulo_Color'  => 'si', // Para poner color de fondo y de letra al titulo
									 'TituloCol' => $opciones['color_fondo_titulo'], //Color de fondo del titulo
									 'Letra_Titulo' => $opciones['color_letra_titulo'],  //Color de letra del titulo
									 'cols'=>array($columnax[1]=>array('justification'=>$opciones['justificacion_texto'],'width'=>$opciones['ancho_1']),
												   $columnax[2]=>array('justification'=>$opciones['justificacion_texto'],'width'=>$opciones['ancho_2'])
									  ),
									  'cabecera_cols'=>array($columnax[1]=>array('justification'=>'center'),
															 $columnax[2]=>array('justification'=>'center'))
									  ); // Justificación y ancho de la columna
					
					$this->ezTable($opciones['datos'],$la_columnas,'',$la_config);
			}
			
			
			function tabla($opciones=array()){
					
					$columnax[1]  =  'columna1';
					$columnax[2]  =  'columna2';	
					$columnax[3]  =  'columna3';
					$columnax[4]  =  'columna4';
					$columnax[5]  =  'columna5';
					$columnax[6]  =  'columna6';
					$columnax[7]  =  'columna7';
					$columnax[8]  =  'columna8';
					$columnax[9]  =  'columna9';
					$columnax[10] =  'columna10';				
					
					$defaults = array(
										'tamano_titulo'=>6,'tamano_letra'=>6,'color_fondo'=>array(1,1,1),'ancho_tabla'=>530,
										'orientacion'=>'center','justificacion_texto'=>'left','mostrar_lineas'=>1,
										'numero_columnas'=>2,										
										'color_fondo_titulo'=>array(0.1,0.4,0.6),'color_letra_titulo'=>array(1,1,1)	
									  );
								
					$opciones = $this->parametros_por_defecto($defaults,$opciones);
					
					
					
					
					
					switch($opciones['numero_columnas']){					
					
						case '1':
						
						break;
						
						
						case '2':
								$defaults = array('ancho'=>array(1=>'265',2=>'265'),								                  
												  'texto_titulo'=>array(1=>'',2=>''),
												  'justificacion_texto'=>array(1=>'left',2=>'left'),												  
												  'datos'=>array('0'=>array($columnax[1]=>' ',$columnax[2]=>' '))
												  );
								$opciones = $this->parametros_por_defecto($defaults,$opciones);
								$la_columnas=array($columnax[1]=>$opciones['texto_titulo'][1],
												   $columnax[2]=>$opciones['texto_titulo'][2]);
								$parametros_cols = array(
														 $columnax[1]=>array('justification'=>$opciones['justificacion_texto'][1],'width'=>$opciones['ancho'][1]),
														 $columnax[1]=>array('justification'=>$opciones['justificacion_texto'][2],'width'=>$opciones['ancho'][2])
									  				     );
								$parametros_cols_cabecera = array(
																	$columnax[1]=>array('justification'=>'center'),
																	$columnax[2]=>array('justification'=>'center')
																 );
						break;
						
						
						case '3':
								$defaults = array('ancho'=>array(1=>'176.6',2=>'176.6',3=>'176.6'),								                  
												  'texto_titulo'=>array(1=>'',2=>'',3=>''),
												  'justificacion_texto'=>array(1=>'left',2=>'left',3=>'left'),												  
												  'datos'=>array(	
												  				  '0'=>array($columnax[1]=>' ',$columnax[2]=>' ',$columnax[3]=>' ')
																 )
												  );
								$opciones = $this->parametros_por_defecto($defaults,$opciones);
								$la_columnas=array($columnax[1]=>$opciones['texto_titulo'][1],
												   $columnax[2]=>$opciones['texto_titulo'][2],
												   $columnax[3]=>$opciones['texto_titulo'][3]);
								$parametros_cols = array(
														 $columnax[1]=>array('justification'=>$opciones['justificacion_texto'][1],'width'=>$opciones['ancho'][1]),
														 $columnax[1]=>array('justification'=>$opciones['justificacion_texto'][2],'width'=>$opciones['ancho'][2]),
												   		 $columnax[2]=>array('justification'=>$opciones['justificacion_texto'][3],'width'=>$opciones['ancho'][3])
									  				     );
								$parametros_cols_cabecera = array(
																	$columnax[1]=>array('justification'=>'center'),
																	$columnax[2]=>array('justification'=>'center'),
															 		$columnax[3]=>array('justification'=>'center')
																 );
						break;
						
						
						case '4':
								$defaults = array('ancho'=>array(1=>'132.5',2=>'132.5',3=>'132.5',4=>'132.5'),								                  
												  'texto_titulo'=>array(1=>'',2=>'',3=>'',4=>''),
												  'justificacion_texto'=>array(1=>'left',2=>'left',3=>'left',4=>'left'),												  
												  'datos'=>array(	
												  				  '0'=>array($columnax[1]=>' ',$columnax[2]=>' ',$columnax[3]=>' ',$columnax[4]=>' ')
																 )
												  );
								$opciones = $this->parametros_por_defecto($defaults,$opciones);
								$la_columnas=array($columnax[1]=>$opciones['texto_titulo'][1],
												   $columnax[2]=>$opciones['texto_titulo'][2],
												   $columnax[3]=>$opciones['texto_titulo'][3],
												   $columnax[4]=>$opciones['texto_titulo'][4]);
								$parametros_cols = array(
														 $columnax[1]=>array('justification'=>$opciones['justificacion_texto'][1],'width'=>$opciones['ancho'][1]),
														 $columnax[2]=>array('justification'=>$opciones['justificacion_texto'][2],'width'=>$opciones['ancho'][2]),
														 $columnax[3]=>array('justification'=>$opciones['justificacion_texto'][3],'width'=>$opciones['ancho'][3]),
												   		 $columnax[4]=>array('justification'=>$opciones['justificacion_texto'][4],'width'=>$opciones['ancho'][4])
									  				     );
								$parametros_cols_cabecera = array(
																	$columnax[1]=>array('justification'=>'center'),
																	$columnax[2]=>array('justification'=>'center'),
															 		$columnax[3]=>array('justification'=>'center'),
																	$columnax[4]=>array('justification'=>'center')
																 );
						break;
						
						
						case '5':
								$defaults = array('ancho'=>array(1=>'106',2=>'106',3=>'106',4=>'106',5=>'106'),								                  
												  'texto_titulo'=>array(1=>'',2=>'',3=>'',4=>'',5=>''),
												  'justificacion_texto'=>array(1=>'left',2=>'left',3=>'left',4=>'left',5=>'left'),												  
												  'datos'=>array(	
												  				  '0'=>array($columnax[1]=>' ',$columnax[2]=>' ',$columnax[3]=>' ',$columnax[4]=>' ',$columnax[5]=>' ')
																 )
												  );
								$opciones = $this->parametros_por_defecto($defaults,$opciones);
								$la_columnas=array($columnax[1]=>$opciones['texto_titulo'][1],
												   $columnax[2]=>$opciones['texto_titulo'][2],
												   $columnax[3]=>$opciones['texto_titulo'][3],
												   $columnax[4]=>$opciones['texto_titulo'][4],
												   $columnax[5]=>$opciones['texto_titulo'][5]);
								$parametros_cols = array(
														 $columnax[1]=>array('justification'=>$opciones['justificacion_texto'][1],'width'=>$opciones['ancho'][1]),
														 $columnax[2]=>array('justification'=>$opciones['justificacion_texto'][2],'width'=>$opciones['ancho'][2]),
														 $columnax[3]=>array('justification'=>$opciones['justificacion_texto'][3],'width'=>$opciones['ancho'][3]),
												   		 $columnax[4]=>array('justification'=>$opciones['justificacion_texto'][4],'width'=>$opciones['ancho'][4]),
														 $columnax[5]=>array('justification'=>$opciones['justificacion_texto'][5],'width'=>$opciones['ancho'][5])
									  				     );
								$parametros_cols_cabecera = array(
																	$columnax[1]=>array('justification'=>'center'),
																	$columnax[2]=>array('justification'=>'center'),
															 		$columnax[3]=>array('justification'=>'center'),
																	$columnax[4]=>array('justification'=>'center'),
																	$columnax[5]=>array('justification'=>'center')
																);
						break;
						
						case '6':
								$defaults = array('ancho'=>array(1=>'88.33',2=>'88.33',3=>'88.33',4=>'88.33',5=>'88.33',6=>'88.33'),								                  
												  'texto_titulo'=>array(1=>'',2=>'',3=>'',4=>'',5=>'',6=>''),
												  'justificacion_texto'=>array(1=>'left',2=>'left',3=>'left',4=>'left',5=>'left',6=>'left'),												  
												  'datos'=>array(	
												  				  '0'=>array($columnax[1]=>' ',$columnax[2]=>' ',$columnax[3]=>' ',$columnax[4]=>' ',$columnax[5]=>' ',$columnax[6]=>' ')
																 )
												  );
								$opciones = $this->parametros_por_defecto($defaults,$opciones);
								$la_columnas=array(
												   $columnax[1]=>$opciones['texto_titulo'][1],
												   $columnax[2]=>$opciones['texto_titulo'][2],
												   $columnax[3]=>$opciones['texto_titulo'][3],
												   $columnax[4]=>$opciones['texto_titulo'][4],
												   $columnax[5]=>$opciones['texto_titulo'][5],
												   $columnax[6]=>$opciones['texto_titulo'][6]
												   );
								$parametros_cols = array(
														 $columnax[1]=>array('justification'=>$opciones['justificacion_texto'][1],'width'=>$opciones['ancho'][1]),
														 $columnax[2]=>array('justification'=>$opciones['justificacion_texto'][2],'width'=>$opciones['ancho'][2]),
														 $columnax[3]=>array('justification'=>$opciones['justificacion_texto'][3],'width'=>$opciones['ancho'][3]),
												   		 $columnax[4]=>array('justification'=>$opciones['justificacion_texto'][4],'width'=>$opciones['ancho'][4]),
														 $columnax[5]=>array('justification'=>$opciones['justificacion_texto'][5],'width'=>$opciones['ancho'][5]),
														 $columnax[6]=>array('justification'=>$opciones['justificacion_texto'][6],'width'=>$opciones['ancho'][6])
									  				     );
								$parametros_cols_cabecera = array(
																	$columnax[1]=>array('justification'=>'center'),
																	$columnax[2]=>array('justification'=>'center'),
															 		$columnax[3]=>array('justification'=>'center'),
																	$columnax[4]=>array('justification'=>'center'),
																	$columnax[5]=>array('justification'=>'center'),
																	$columnax[6]=>array('justification'=>'center')
																);
						break;
						
						
						case '7':
								$defaults = array('ancho'=>array(1=>'75.71',2=>'75.71',3=>'75.71',4=>'75.71',5=>'75.71',6=>'75.71',7=>'75.71'),								                  
												  'texto_titulo'=>array(1=>'',2=>'',3=>'',4=>'',5=>'',6=>'',7=>''),
												  'justificacion_texto'=>array(1=>'left',2=>'left',3=>'left',4=>'left',5=>'left',6=>'left',7=>'left'),												  
												  'datos'=>array(	
												  				  '0'=>array($columnax[1]=>' ',$columnax[2]=>' ',$columnax[3]=>' ',$columnax[4]=>' ',$columnax[5]=>' ',$columnax[6]=>' ',$columnax[7]=>' ')
																 )
												  );
								$opciones = $this->parametros_por_defecto($defaults,$opciones);
								$la_columnas=array(
												   $columnax[1]=>$opciones['texto_titulo'][1],
												   $columnax[2]=>$opciones['texto_titulo'][2],
												   $columnax[3]=>$opciones['texto_titulo'][3],
												   $columnax[4]=>$opciones['texto_titulo'][4],
												   $columnax[5]=>$opciones['texto_titulo'][5],
												   $columnax[6]=>$opciones['texto_titulo'][6],
												   $columnax[7]=>$opciones['texto_titulo'][7]
												   );
								$parametros_cols = array(
														 $columnax[1]=>array('justification'=>$opciones['justificacion_texto'][1],'width'=>$opciones['ancho'][1]),
														 $columnax[2]=>array('justification'=>$opciones['justificacion_texto'][2],'width'=>$opciones['ancho'][2]),
														 $columnax[3]=>array('justification'=>$opciones['justificacion_texto'][3],'width'=>$opciones['ancho'][3]),
												   		 $columnax[4]=>array('justification'=>$opciones['justificacion_texto'][4],'width'=>$opciones['ancho'][4]),
														 $columnax[5]=>array('justification'=>$opciones['justificacion_texto'][5],'width'=>$opciones['ancho'][5]),
														 $columnax[6]=>array('justification'=>$opciones['justificacion_texto'][6],'width'=>$opciones['ancho'][6]),
														 $columnax[7]=>array('justification'=>$opciones['justificacion_texto'][7],'width'=>$opciones['ancho'][7])
									  				     );
								$parametros_cols_cabecera = array(
																	$columnax[1]=>array('justification'=>'center'),
																	$columnax[2]=>array('justification'=>'center'),
															 		$columnax[3]=>array('justification'=>'center'),
																	$columnax[4]=>array('justification'=>'center'),
																	$columnax[5]=>array('justification'=>'center'),
																	$columnax[6]=>array('justification'=>'center'),
																	$columnax[7]=>array('justification'=>'center')
																);
								
						break;
						
						case '8':
								$defaults = array('ancho'=>array(1=>'66.25',2=>'66.25',3=>'66.25',4=>'66.25',5=>'66.25',6=>'66.25',7=>'66.25',8=>'66.25'),								                  
												  'texto_titulo'=>array(1=>'',2=>'',3=>'',4=>'',5=>'',6=>'',7=>'',8=>''),
												  'justificacion_texto'=>array(1=>'left',2=>'left',3=>'left',4=>'left',5=>'left',6=>'left',7=>'left',8=>'left'),												  
												  'datos'=>array(	
												  				  '0'=>array($columnax[1]=>' ',$columnax[2]=>' ',$columnax[3]=>' ',$columnax[4]=>' ',$columnax[5]=>' ',$columnax[6]=>' ',$columnax[7]=>' ',$columnax[8]=>' ')
																 )
												  );
								$opciones = $this->parametros_por_defecto($defaults,$opciones);
								$la_columnas=array(
												   $columnax[1]=>$opciones['texto_titulo'][1],
												   $columnax[2]=>$opciones['texto_titulo'][2],
												   $columnax[3]=>$opciones['texto_titulo'][3],
												   $columnax[4]=>$opciones['texto_titulo'][4],
												   $columnax[5]=>$opciones['texto_titulo'][5],
												   $columnax[6]=>$opciones['texto_titulo'][6],
												   $columnax[7]=>$opciones['texto_titulo'][7],
												   $columnax[8]=>$opciones['texto_titulo'][8]
												   );
								$parametros_cols = array(
														 $columnax[1]=>array('justification'=>$opciones['justificacion_texto'][1],'width'=>$opciones['ancho'][1]),
														 $columnax[2]=>array('justification'=>$opciones['justificacion_texto'][2],'width'=>$opciones['ancho'][2]),
														 $columnax[3]=>array('justification'=>$opciones['justificacion_texto'][3],'width'=>$opciones['ancho'][3]),
												   		 $columnax[4]=>array('justification'=>$opciones['justificacion_texto'][4],'width'=>$opciones['ancho'][4]),
														 $columnax[5]=>array('justification'=>$opciones['justificacion_texto'][5],'width'=>$opciones['ancho'][5]),
														 $columnax[6]=>array('justification'=>$opciones['justificacion_texto'][6],'width'=>$opciones['ancho'][6]),
														 $columnax[7]=>array('justification'=>$opciones['justificacion_texto'][7],'width'=>$opciones['ancho'][7]),
														 $columnax[8]=>array('justification'=>$opciones['justificacion_texto'][8],'width'=>$opciones['ancho'][8])
									  				     );
								$parametros_cols_cabecera = array(
																	$columnax[1]=>array('justification'=>'center'),
																	$columnax[2]=>array('justification'=>'center'),
															 		$columnax[3]=>array('justification'=>'center'),
																	$columnax[4]=>array('justification'=>'center'),
																	$columnax[5]=>array('justification'=>'center'),
																	$columnax[6]=>array('justification'=>'center'),
																	$columnax[7]=>array('justification'=>'center'),
																	$columnax[8]=>array('justification'=>'center')
																);
								
						break;
						
						
						case '9':
								$defaults = array('ancho'=>array(1=>'58.89',2=>'58.89',3=>'58.89',4=>'58.89',5=>'58.89',6=>'58.89',7=>'58.89',8=>'58.89',9=>'58.89'),								                  
												  'texto_titulo'=>array(1=>'',2=>'',3=>'',4=>'',5=>'',6=>'',7=>'',8=>'',9=>''),
												  'justificacion_texto'=>array(1=>'left',2=>'left',3=>'left',4=>'left',5=>'left',6=>'left',7=>'left',8=>'left',9=>'left'),												  
												  'datos'=>array(	
												  				  '0'=>array($columnax[1]=>' ',$columnax[2]=>' ',$columnax[3]=>' ',$columnax[4]=>' ',$columnax[5]=>' ',$columnax[6]=>' ',$columnax[7]=>' ',$columnax[8]=>' ',$columnax[9]=>' ')
																 )
												  );
								$opciones = $this->parametros_por_defecto($defaults,$opciones);
								$la_columnas=array(
												   $columnax[1]=>$opciones['texto_titulo'][1],
												   $columnax[2]=>$opciones['texto_titulo'][2],
												   $columnax[3]=>$opciones['texto_titulo'][3],
												   $columnax[4]=>$opciones['texto_titulo'][4],
												   $columnax[5]=>$opciones['texto_titulo'][5],
												   $columnax[6]=>$opciones['texto_titulo'][6],
												   $columnax[7]=>$opciones['texto_titulo'][7],
												   $columnax[8]=>$opciones['texto_titulo'][8],
												   $columnax[9]=>$opciones['texto_titulo'][9]
												   );
								$parametros_cols = array(
														 $columnax[1]=>array('justification'=>$opciones['justificacion_texto'][1],'width'=>$opciones['ancho'][1]),
														 $columnax[2]=>array('justification'=>$opciones['justificacion_texto'][2],'width'=>$opciones['ancho'][2]),
														 $columnax[3]=>array('justification'=>$opciones['justificacion_texto'][3],'width'=>$opciones['ancho'][3]),
												   		 $columnax[4]=>array('justification'=>$opciones['justificacion_texto'][4],'width'=>$opciones['ancho'][4]),
														 $columnax[5]=>array('justification'=>$opciones['justificacion_texto'][5],'width'=>$opciones['ancho'][5]),
														 $columnax[6]=>array('justification'=>$opciones['justificacion_texto'][6],'width'=>$opciones['ancho'][6]),
														 $columnax[7]=>array('justification'=>$opciones['justificacion_texto'][7],'width'=>$opciones['ancho'][7]),
														 $columnax[8]=>array('justification'=>$opciones['justificacion_texto'][8],'width'=>$opciones['ancho'][8]),
														 $columnax[9]=>array('justification'=>$opciones['justificacion_texto'][9],'width'=>$opciones['ancho'][9])
									  				     );
								$parametros_cols_cabecera = array(
																	$columnax[1]=>array('justification'=>'center'),
																	$columnax[2]=>array('justification'=>'center'),
															 		$columnax[3]=>array('justification'=>'center'),
																	$columnax[4]=>array('justification'=>'center'),
																	$columnax[5]=>array('justification'=>'center'),
																	$columnax[6]=>array('justification'=>'center'),
																	$columnax[7]=>array('justification'=>'center'),
																	$columnax[8]=>array('justification'=>'center'),
																	$columnax[9]=>array('justification'=>'center')
																);
								
						break;
						
						
						case '10':
								$defaults = array('ancho'=>array(1=>'53',2=>'53',3=>'53',4=>'53',5=>'53',6=>'53',7=>'53',8=>'53',9=>'53',10=>'53'),								                  
												  'texto_titulo'=>array(1=>'',2=>'',3=>'',4=>'',5=>'',6=>'',7=>'',8=>'',9=>'',10=>''),
												  'justificacion_texto'=>array(1=>'left',2=>'left',3=>'left',4=>'left',5=>'left',6=>'left',7=>'left',8=>'left',9=>'left',10=>'left'),												  
												  'datos'=>array(	
												  				  '0'=>array($columnax[1]=>' ',$columnax[2]=>' ',$columnax[3]=>' ',$columnax[4]=>' ',$columnax[5]=>' ',$columnax[6]=>' ',$columnax[7]=>' ',$columnax[8]=>' ',$columnax[9]=>' ',$columnax[10]=>' ')
																 )
												  );
								$opciones = $this->parametros_por_defecto($defaults,$opciones);
								$la_columnas=array(
												   $columnax[1]=>$opciones['texto_titulo'][1],
												   $columnax[2]=>$opciones['texto_titulo'][2],
												   $columnax[3]=>$opciones['texto_titulo'][3],
												   $columnax[4]=>$opciones['texto_titulo'][4],
												   $columnax[5]=>$opciones['texto_titulo'][5],
												   $columnax[6]=>$opciones['texto_titulo'][6],
												   $columnax[7]=>$opciones['texto_titulo'][7],
												   $columnax[8]=>$opciones['texto_titulo'][8],
												   $columnax[9]=>$opciones['texto_titulo'][9],
												   $columnax[10]=>$opciones['texto_titulo'][10]
												   );
								$parametros_cols = array(
														 $columnax[1]=>array('justification'=>$opciones['justificacion_texto'][1],'width'=>$opciones['ancho'][1]),
														 $columnax[2]=>array('justification'=>$opciones['justificacion_texto'][2],'width'=>$opciones['ancho'][2]),
														 $columnax[3]=>array('justification'=>$opciones['justificacion_texto'][3],'width'=>$opciones['ancho'][3]),
												   		 $columnax[4]=>array('justification'=>$opciones['justificacion_texto'][4],'width'=>$opciones['ancho'][4]),
														 $columnax[5]=>array('justification'=>$opciones['justificacion_texto'][5],'width'=>$opciones['ancho'][5]),
														 $columnax[6]=>array('justification'=>$opciones['justificacion_texto'][6],'width'=>$opciones['ancho'][6]),
														 $columnax[7]=>array('justification'=>$opciones['justificacion_texto'][7],'width'=>$opciones['ancho'][7]),
														 $columnax[8]=>array('justification'=>$opciones['justificacion_texto'][8],'width'=>$opciones['ancho'][8]),
														 $columnax[9]=>array('justification'=>$opciones['justificacion_texto'][9],'width'=>$opciones['ancho'][9]),
														 $columnax[10]=>array('justification'=>$opciones['justificacion_texto'][10],'width'=>$opciones['ancho'][10])
									  				     );
								$parametros_cols_cabecera = array(
																	$columnax[1]=>array('justification'=>'center'),
																	$columnax[2]=>array('justification'=>'center'),
															 		$columnax[3]=>array('justification'=>'center'),
																	$columnax[4]=>array('justification'=>'center'),
																	$columnax[5]=>array('justification'=>'center'),
																	$columnax[6]=>array('justification'=>'center'),
																	$columnax[7]=>array('justification'=>'center'),
																	$columnax[8]=>array('justification'=>'center'),
																	$columnax[9]=>array('justification'=>'center'),
																	$columnax[10]=>array('justification'=>'center')
																);
						break;
						

					}
					
							
					
										   
					$la_config=array('showHeadings'=>1, // Mostrar encabezados
									 'fontSize' => $opciones['tamano_letra'], // Tamaño de Letras
									 'titleFontSize' => $opciones['tamano_titulo'],  // Tamaño de Letras de los títulos
									 'showLines'=>$opciones['mostrar_lineas'], // Mostrar Líneas
									 'lineCol' => array(0.2,0.2,0.2),
									 'shaded'=>2, // Sombra entre líneas
									 'shadeCol' => $opciones['color_fondo'],
									 'shadeCol2' => $opciones['color_fondo'],
									 'width'=>$opciones['ancho_tabla'], // Ancho de la tabla
									 'extraer_datos'=>'si',
									 'maxWidth'=>300, // Ancho Máximo de la tabla
									 'xOrientation'=>$opciones['orientacion'], // Orientación de la tabla
									 'outerLineThickness'=>0.3,
									 'innerLineThickness' =>0.3,
									 'rowGap' => 1,
									 'colGap' => 3,
									 'Titulo_Color'  => 'si', // Para poner color de fondo y de letra al titulo
									 'TituloCol' => $opciones['color_fondo_titulo'], //Color de fondo del titulo
									 'Letra_Titulo' => $opciones['color_letra_titulo'],  //Color de letra del titulo
									 'cols'=>$parametros_cols,
									 'cabecera_cols'=>$parametros_cols_cabecera
									  ); // Justificación y ancho de la columna
					
					$this->ezTable($opciones['datos'],$la_columnas,'',$la_config);
			}
			
			
			function coloca_marco(){
					
					
					$io_encabezado=$this->openObject();
					$this->saveState();
					
					$this->marco();
					
					$this->restoreState();
					$this->closeObject();
					$this->addObject($io_encabezado,'all');
			
			}
			
			
			function marco()
				{
					/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
					//       Function: marco
					//		   Access: private
					//	    Arguments: 
					//    Description: función que imprime un marco a la página
					//	   Creado Por: Lic. Edgar A. Quintero U.
					// Fecha Creación: 19/02/2009
					/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
					global $columna;
					
					$datos = "\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n
							  \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n
							  \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n";
					
					
					$la_columnas=array($columna[1]=>'');
					$la_data[0]=array($columna[1]=>$datos);				   
					$la_config=array('showHeadings'=>0, // Mostrar encabezados
									 'fontSize' => 6, // Tamaño de Letras
									 'titleFontSize' => 6,  // Tamaño de Letras de los títulos
									 'showLines'=>1, // Mostrar Líneas
									 'lineCol' => array(0.2,0.2,0.2),
									 'shaded'=>0, // Sombra entre líneas
									 'shadeCol' => array(1,1,0.8),
									 'shadeCol2' => array(1,1,0.8),
									 'width'=>700, // Ancho de la tabla
									 'maxWidth'=>700, // Ancho Máximo de la tabla
									 'xOrientation'=>'center', // Orientación de la tabla
									 'outerLineThickness'=>0.5,
									 'innerLineThickness' =>0.5,
									 'Titulo_Color'  => 'no', // Para poner color de fondo y de letra al titulo
									 'TituloCol' => array(0,0,0.5), //Color de fondo del titulo
									 'Letra_Titulo' => array(1,1,1),  //Color de letra del titulo
									 'cols'=>array($columna[1]=>array('justification'=>'left','width'=>580))
									  ); // Justificación y ancho de la columna
					
					$this->ezTable($la_data,$la_columnas,'',$la_config);
			}// end function imprime_correspondencia

			
			
			function dibuja_cuadrado($opciones=array()){

					/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
					//       Function: dibuja_cuadrado
					//		   Access: public
					//	    Arguments: 
					//    Description: función que dibuja un cuadrado
					//	   Creado Por: Lic. Edgar A. Quintero U.
					// Fecha Creación: 09/03/2009
					/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
					
					
					$defaults = array(
										'x0'=>50,'y0'=>$this->y,
										'ancho'=>10,'relleno'=>'no',
										'color_relleno'=>array(0.9,0.94,0.98),
										'color_borde'=>array(0,0,0),
										'ancho_linea'=>0.5										
									  );
					
					$opciones = $this->parametros_por_defecto($defaults,$opciones);
					
					$x0 = $opciones['x0'];
					$y0 = $opciones['y0'];
					$ancho = $opciones['ancho'];
					if($opciones['relleno']=='si'){$relleno=1;}else{$relleno=0;}					
					
					$x1 = $x0 + $ancho;
					$y1 = $y0;
					$x2 = $x0 + $ancho;
					$y2 = $y0 + $ancho;
					$x3 = $x0;
					$y3 = $y0 + $ancho;
					$x4 = $x0;
					$y4 = $y0;
					$datos = array($x0,$y0,$x1,$y1,$x2,$y2,$x3,$y3,$x4,$y4);
					
					$this->saveState();
					
					if($opciones['relleno']=='si'){
							$this->saveState();						
							$this->setColor($opciones['color_relleno'][0],$opciones['color_relleno'][1],$opciones['color_relleno'][2]);
							$this->polygon($datos,5,1);
							$this->restoreState();
					}
					
					$this->setStrokeColor($opciones['color_borde'][0],$opciones['color_borde'][1],$opciones['color_borde'][2]);
					
					$this->setLineStyle($opciones['ancho_linea']);				
					$this->polygon($datos,5);
					
					$this->restoreState();
					return $ancho;
					
					
			}
			
			
			function dibuja_rectangulo($opciones=array()){

					/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
					//       Function: dibuja_rectangulo
					//		   Access: public
					//	    Arguments: 
					//    Description: función que dibuja un cuadrado
					//	   Creado Por: Lic. Edgar A. Quintero U.
					// Fecha Creación: 09/03/2009
					/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
					
					
					$defaults = array(
										'x0'=>$this->x,'y0'=>$this->y,
										'ancho'=>40,'alto'=>20,'relleno'=>'no',
										'color_relleno'=>array(0.9,0.94,0.98),
										'color_borde'=>array(0,0,0),
										'ancho_linea'=>0.5										
									  );
					
					$opciones = $this->parametros_por_defecto($defaults,$opciones);
					
					$x0 = $opciones['x0'];
					$y0 = $opciones['y0'];
					$ancho = $opciones['ancho'];
					$alto = $opciones['alto'];
					if($opciones['relleno']=='si'){$relleno=1;}else{$relleno=0;}					
					
					$x1 = $x0 + $ancho;
					$y1 = $y0;
					$x2 = $x0 + $ancho;
					$y2 = $y0 + $alto;
					$x3 = $x0;
					$y3 = $y0 + $alto;
					$x4 = $x0;
					$y4 = $y0;
					$datos = array($x0,$y0,$x1,$y1,$x2,$y2,$x3,$y3,$x4,$y4);
					
					$this->saveState();
					
					if($opciones['relleno']=='si'){
							$this->saveState();						
							$this->setColor($opciones['color_relleno'][0],$opciones['color_relleno'][1],$opciones['color_relleno'][2]);
							$this->polygon($datos,5,1);
							$this->restoreState();
					}
					
					$this->setStrokeColor($opciones['color_borde'][0],$opciones['color_borde'][1],$opciones['color_borde'][2]);
					
					$this->setLineStyle($opciones['ancho_linea']);				
					$this->polygon($datos,5);
					
					$this->restoreState();
					
					
					
			}
			


}//fin clase covensol_pdf


























?>