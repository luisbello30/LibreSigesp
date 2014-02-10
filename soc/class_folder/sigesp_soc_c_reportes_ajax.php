<?php
	session_start();  
	require_once("../../shared/class_folder/grid_param.php");
	$io_grid=new grid_param();
	require_once("../../shared/class_folder/class_funciones.php");
	$io_funciones=new class_funciones();
	require_once("class_funciones_soc.php");
	$io_funciones_soc=new class_funciones_soc();
	require_once("../../shared/class_folder/sigesp_include.php");
	$io_include=new sigesp_include();
	$io_conexion=$io_include->uf_conectar();
	require_once("../../shared/class_folder/class_sql.php");
	$io_sql=new class_sql($io_conexion);	
	require_once("../../shared/class_folder/class_mensajes.php");
	$io_mensajes=new class_mensajes();		
    $ls_codemp=$_SESSION["la_empresa"]["codemp"];
	// proceso a ejecutar
	$ls_proceso=$io_funciones_soc->uf_obtenervalor("proceso","");
	$ls_numordcomdes=$io_funciones_soc->uf_obtenervalor("txtnumordcomdes","");
	$ls_numordcomhas=$io_funciones_soc->uf_obtenervalor("txtnumordcomhas","");
	$ls_codprodes=$io_funciones_soc->uf_obtenervalor("txtcodprodes","");
	$ls_codprohas=$io_funciones_soc->uf_obtenervalor("txtcodprohas","");
	$ls_fecordcomdes=$io_funciones_soc->uf_obtenervalor("txtfecordcomdes","");
	$ls_fecordcomhas=$io_funciones_soc->uf_obtenervalor("txtfecordcomhas","");
	$ls_coduniadmdes=$io_funciones_soc->uf_obtenervalor("txtcoduniejedes","");
	$ls_coduniadmhas=$io_funciones_soc->uf_obtenervalor("txtcoduniejehas","");
	$ls_codartdes=$io_funciones_soc->uf_obtenervalor("txtcodartdes","");
	$ls_codarthas=$io_funciones_soc->uf_obtenervalor("txtcodarthas","");
	$ls_codserdes=$io_funciones_soc->uf_obtenervalor("txtcodserdes","");
	$ls_codserhas=$io_funciones_soc->uf_obtenervalor("txtcodserhas","");
	$ls_rdemi=$io_funciones_soc->uf_obtenervalor("rdemi","");
	$ls_rdpre=$io_funciones_soc->uf_obtenervalor("rdpre","");	
	$ls_rdcon=$io_funciones_soc->uf_obtenervalor("rdcon","");
	$ls_rdanu=$io_funciones_soc->uf_obtenervalor("rdanu","");
	$ls_rdinv=$io_funciones_soc->uf_obtenervalor("rdinv","");
	
	$ls_estcondat=$io_funciones_soc->uf_obtenervalor("rdtipo","");
	$ls_tipo=$io_funciones_soc->uf_obtenervalor("esttip","");
	switch($ls_proceso)
	{
		case "ORDEN":
			uf_print_ordenes($ls_numordcomdes,$ls_numordcomhas,$ls_codprodes,$ls_codprohas,$ls_fecordcomdes,$ls_fecordcomhas,
							 $ls_coduniadmdes,$ls_coduniadmhas,$ls_rdanu,$ls_rdemi,$ls_rdpre,$ls_rdcon,$ls_rdanu,
                             $ls_rdinv,$ls_codartdes,$ls_codarthas,$ls_codserdes,$ls_codserhas,$ls_estcondat,$ls_tipo);
			break;
	}

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_ordenes($ls_numordcomdes,$ls_numordcomhas,$ls_codprodes,$ls_codprohas,$ls_fecordcomdes,$ls_fecordcomhas,
							 $ls_coduniadmdes,$ls_coduniadmhas,$ls_rdanucom,$ls_rdemi,$ls_rdpre,$ls_rdcon,$ls_rdanu,
                             $ls_rdinv,$ls_codartdes,$ls_codarthas,$ls_codserdes,$ls_codserhas,$ls_estcondat,$ls_tipo)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print_solicitudes
		//		   Access: private
		//	    Arguments: as_numsoldes     // Numero de solicitud de inicio del parametro de Busqueda
		//	  			   as_numsolhas     // Numero de solicitud de fin del parametro de Busqueda
		//	  			   as_tipproben     // Indica si es proveedor o beneficiario
		//	  			   as_codprobendes  // Código del proveedor/beneficiario de inicio del parametro de Busqueda
		//	  			   as_codprobenhas  // Código del proveedor/beneficiario de fin del parametro de Busqueda
		//	  			   ad_fegregdes     // Fecha de registgro de la solicitud de inicio del parametro de Busqueda
		//	  			   ad_fegregdes     // Fecha de registgro de la solicitud de fin del parametro de Busqueda
		//	  			   as_codunides     // Codigo de unidad ejecutora de inicio del parametro de Busqueda
		//	  			   as_codunihas     // Codigo de unidad ejecutora de fin del parametro de Busqueda
		//	  			   as_tipsol        // Indica el tipo de solicitud (Bienes, Servicios, Conceptos)
		//	  			   ai_registrada    // Indica si se desea filtrar por este estatus de solicitud
		//	  			   ai_emitida       // Indica si se desea filtrar por este estatus de solicitud
		//	  			   ai_registrada    // Indica si se desea filtrar por este estatus de solicitud
		//	  			   ai_contabilizada // Indica si se desea filtrar por este estatus de solicitud
		//	  			   ai_procesada     // Indica si se desea filtrar por este estatus de solicitud
		//	  			   ai_anulada       // Indica si se desea filtrar por este estatus de solicitud
		//	  			   ai_despachada    // Indica si se desea filtrar por este estatus de solicitud
		//	  			   as_codusudes    // Indica si se desea filtrar por el código de usuario
		//	  			   as_codusuhas    // Indica si se desea filtrar por el código de usuario
		//                 ai_aprobada		// Indica si se desea filtrar por este estatus de solicitud
		//                 ai_pagada		// Indica si se desea filtrar por este estatus de solicitud
		//	  Description: Método que impirme el grid de las solicitudes de pago a imprimir en el reporte
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 16/06/2007								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_grid, $io_funciones_cxp, $io_funciones, $io_sql, $io_mensajes;
		// Titulos del Grid de Solicitudes
		$lo_title[1]="";
		$lo_title[2]="Numero de Orden";
		$lo_title[3]="Tipo";
		$lo_title[4]="Proveedor / Beneficiario";
		$lo_title[5]="Concepto";
		$lo_title[6]="Fecha";
		$lo_title[7]="Monto";
		/*$ld_fegregdes=$io_funciones->uf_convertirdatetobd($ld_fegregdes);
		$ld_fegreghas=$io_funciones->uf_convertirdatetobd($ld_fegreghas);*/
		$rs_data=uf_select_listado_orden_compra($ls_numordcomdes,$ls_numordcomhas,$ls_codprodes,$ls_codprohas,$ls_fecordcomdes,$ls_fecordcomhas,
												$ls_coduniadmdes,$ls_coduniadmhas,$ls_rdanucom,$ls_rdemi,$ls_rdpre,$ls_rdcon,$ls_rdanu,
                             					$ls_rdinv,$ls_codartdes,$ls_codarthas,$ls_codserdes,$ls_codserhas,$ls_estcondat,$ls_tipo,&$lb_valido);
		$li_fila=0;
		while(!$rs_data->EOF)
		{
			$li_fila=$li_fila + 1;
			$ls_numordcom=$rs_data->fields["numordcom"]; 
			$ls_estcondat=$rs_data->fields["estcondat"]; 
			$ld_fecordcom=$rs_data->fields["fecordcom"];
			$ld_fecordcom=$io_funciones->uf_formatovalidofecha($ld_fecordcom);
			$ls_codpro=utf8_encode($rs_data->fields["cod_pro"]);
			$ls_nompro=uf_select_nombre_proveedor($ls_codpro);
			$ls_obscon=utf8_encode($rs_data->fields["obscom"]);
			$li_monto=number_format($rs_data->fields["montot"],2,',','.');
			$ld_fecordcom=$io_funciones->uf_convertirfecmostrar($ld_fecordcom);
			if($ls_estcondat=="B")
			{
				$ls_tipo="Bienes";
			}
			else
			{
				$ls_tipo="Servicios";
			}$li_fila=trim($li_fila);
			$lo_object[$li_fila][1]="<input type=checkbox name=chkimprimir".$li_fila.">";
			$lo_object[$li_fila][2]="<input type=text name=txtnumsol".$li_fila." id=txtnumsol".$li_fila." class=sin-borde style=text-align:center size=20 value='".$ls_numordcom."' readonly>";
			$lo_object[$li_fila][3]="<input type=text name=txtproben".$li_fila."    id=txtproben".$li_fila."    class=sin-borde style=text-align:left   size=35 value='".$ls_nompro."'    readonly>"; 
			$lo_object[$li_fila][4]="<input type=text name=txttipo".$li_fila."    id=txttipo".$li_fila."    class=sin-borde style=text-align:left   size=15 value='".$ls_tipo."'    readonly>"; 
			$lo_object[$li_fila][5]="<input type=text name=txtconsol".$li_fila."    id=txtconsol".$li_fila."    class=sin-borde style=text-align:left   size=27 value='".$ls_obscon."'   readonly>";
			$lo_object[$li_fila][6]="<input type=text name=txtfecemisol".$li_fila." id=txtfecemisol".$li_fila." class=sin-borde style=text-align:center   size=10 value='".$ld_fecordcom."' readonly>"; 
			$lo_object[$li_fila][7]="<input type=text name=txtmonsol".$li_fila." id=txtmonsol".$li_fila." class=sin-borde style=text-align:right  size=12 value='".$li_monto."' readonly>";
			$rs_data->MoveNext();
		}
		if($li_fila==0)
		{
			$io_mensajes->message("No se encontraron resultados");
			$li_fila=1;
			$lo_object[$li_fila][1]="<input type=checkbox name=chkimprimir value=1 disabled/>";
			$lo_object[$li_fila][2]="<input type=text name=txtnumsol".$li_fila." class=sin-borde style=text-align:center size=20 readonly>";
			$lo_object[$li_fila][3]="<input type=text name=txtproben".$li_fila."    class=sin-borde style=text-align:left   size=35 readonly>"; 
			$lo_object[$li_fila][4]="<input type=text name=txtproben".$li_fila."    class=sin-borde style=text-align:left   size=35 readonly>"; 
			$lo_object[$li_fila][5]="<input type=text name=txtconsol".$li_fila."    class=sin-borde style=text-align:left   size=27 readonly>";
			$lo_object[$li_fila][6]="<input type=text name=txtfecemisol".$li_fila." class=sin-borde style=text-align:left   size=13 readonly>"; 
			$lo_object[$li_fila][7]="<input type=text name=txtmonsol".$li_fila." class=sin-borde style=text-align:right  size=15 readonly>";
		}

		$io_grid->makegrid($li_fila,$lo_title,$lo_object,700,"Ordenes de Compra","gridsolicitudes");
	}// end function uf_print_solicitudes
	//-----------------------------------------------------------------------------------------------------------------------------------

	//---------------------------------------------------------------------------------------------------------------------------------------
	function uf_select_listado_orden_compra($as_numordcomdes,$as_numordcomhas,$as_codprodes,$as_codprohas,
                                            $as_fecordcomdes,$as_fecordcomhas,$as_coduniadmdes,
                                            $as_coduniadmhas,$as_rdanucom,$as_rdemi,$as_rdpre,$as_rdcon,
                                            $as_rdanu,$as_rdinv,$as_artdes,$as_arthas,$as_serdes,$as_serhas,
								            $as_tipord,$as_tipo,&$lb_valido)
	{
		///////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_listado_orden_compra
		//         Access: public
		//	    Arguments: as_numordcom   ---> Orden de Compra a imprimir
		//                 $as_tipord  ---> tipo de la orden de compra bienes o servicios
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca los detalles de la  orden de compra para imprimir
		//	   Creado Por: Ing. Yozelin Barragan
		// Fecha Creación: 16/07/2007									Fecha Última Modificación :
		//////////////////////////////////////////////////////////////////////////////////////////////////////
		//$ab_valido = true;
		global $io_grid, $io_funciones_cxp, $io_funciones, $io_sql, $io_mensajes,$ls_codemp ;
		$lb_valido = true;
		$ls_criterio_a = "";
		$ls_criterio_b = "";
		$ls_criterio_c = "";
		$ls_criterio_d = "";
		$ls_criterio_e = "";
		$ls_criterio_f = "";
		$ls_criterio_g = "";
		$ls_criterio_h = "";
		$ls_cad        = "";
		$ls_cadena     = "";
		$ls_sql        = "";
		$ls_parentesis = "";
		if(  (($as_numordcomdes!="") && ($as_numordcomhas=="")) || (($as_numordcomdes=="") && ($as_numordcomhas!=""))  )
		{
		   $lb_valido = false;
		   $io_mensajes->message("Debe Completar el Rango de Busqueda por Número !!!");
		}
		else
		{
			if( ($as_numordcomdes!="") && ($as_numordcomhas!="") )
			{
			   $ls_criterio_a = "   numordcom >='".$as_numordcomdes."'  AND  numordcom <='".$as_numordcomhas."'    ";
			}
			else
			{
			   $ls_criterio_a ="";
			}
		}

		if(  (($as_codprodes!="") && ($as_codprohas=="")) || (($as_codprodes=="") && ($as_codprohas!=""))  )
		{
		   $lb_valido = false;
		   $io_mensajes->message("Debe Completar el Rango de Busqueda por Proveedor !!!");
		}
		else
		{
			if( ($as_codprodes!="") && ($as_codprohas!="") )
			{
			   if($ls_criterio_a=="")
			   {
					 $CA_AND="";   //CA = Criterio A
			   }
			   else
			   {
					 $CA_AND="  AND  ";
			   }
			   $ls_criterio_b  =  $ls_criterio_a.$CA_AND."  cod_pro   >='".$as_codprodes."'  AND  cod_pro   <='".$as_codprohas."'  ";
			}
			else
			{
			   $ls_criterio_b = $ls_criterio_a;
			}
		}


		if(  (($as_fecordcomdes!="") && ($as_fecordcomhas=="")) || (($as_fecordcomdes=="") && ($as_fecordcomhas!=""))  )
		{
		   $lb_valido = false;
		   $io_mensajes->message("Debe Completar el Rango de Busqueda por Fechas !!!");
		}
		else
		{
		   if( ($as_fecordcomdes!="") && ($as_fecordcomhas!="") )
		   {
			   $ls_fecha  = $io_funciones->uf_convertirdatetobd($as_fecordcomdes);
			   $as_fecordcomdes = $ls_fecha;

			   $ls_fechas  = $io_funciones->uf_convertirdatetobd($as_fecordcomhas);
			   $as_fecordcomhas  = $ls_fechas;

			   if($ls_criterio_b=="")
			   {
					 $CB_AND="";  //CB = Criterio B
			   }
			   else
			   {
					 $CB_AND="  AND  ";
			   }
			   $ls_criterio_c = $ls_criterio_b.$CB_AND."  fecordcom >='".$as_fecordcomdes."'  AND  fecordcom <='".$as_fecordcomhas."'  ";
			 }
		   else
		   {
				$ls_criterio_c = $ls_criterio_b;
		   }
		}

		if( ($as_rdanucom==0) && ($as_rdemi==0) && ($as_rdpre==0) && ($as_rdcon==0) && ($as_rdanu==0) && ($as_rdinv==0))
		{
			$ls_criterio_d = $ls_criterio_c;
		}
		else
		{
		   if($as_rdanucom!=0)
		   {
			  $ls_cadena=" (  estcom = 6 ";
		   }
		   else
		   {
			 $ls_cadena="";
		   }

		   if($as_rdemi!=0)
		   {
		   	  if($as_rdpre!=0)
			  {
				  if($ls_cadena!="")
				  {
					 $ls_cad=" OR   estcom = 1  ";
					 $ls_cadena=$ls_cadena.$ls_cad;
				  }
				  else
				  {
					  $ls_cadena=" (  estcom = 1 ";
				  }
			  }
			  else
			  {
				  if($ls_cadena!="")
				  {
					 $ls_cad=" OR   (estcom = 1 AND estapro=0) ";
					 $ls_cadena=$ls_cadena.$ls_cad;
				  }
				  else
				  {
					  $ls_cadena=" (  (estcom = 1 AND  estapro=0) ";
				  }
			  }
		   }
		   else
		   {
			 $ls_cadena=$ls_cadena;
		   }

		   if($as_rdpre!=0)
		   {
			  if($ls_cadena!="")
			  {
				 $ls_cad=" OR   (estcom = 1 AND estapro=1) ";
				 $ls_cadena=$ls_cadena.$ls_cad;
			  }
			  else
			  {
				 $ls_cadena=" (  (estcom = 1 AND  estapro=1)";
			  }
		   }
		   else
		   {
			 $ls_cadena=$ls_cadena;
		   }

		   if($as_rdcon!=0)
		   {
			  if($ls_cadena!="")
			  {
				 $ls_cad=" OR   estcom = 2  ";
				 $ls_cadena=$ls_cadena.$ls_cad;
			  }
			  else
			  {
				 $ls_cadena=" (  estcom = 2 ";
			  }
		   }
		   else
		   {
			 $ls_cadena=$ls_cadena;
		   }

		   if($as_rdanu!=0)
		   {
			  if($ls_cadena!="")
			  {
				 $ls_cad=" OR   estcom = 3  ";
				 $ls_cadena=$ls_cadena.$ls_cad;
			  }
			  else
			  {
				 $ls_cadena=" (  estcom = 3 ";
			  }
		   }
		   else
		   {
			 $ls_cadena=$ls_cadena;
		   }

		   if($as_rdinv!=0)
		   {
			  if($ls_cadena!="")
			  {
				 $ls_cad=" OR   estcom = 4  ";
				 $ls_cadena=$ls_cadena.$ls_cad;
			  }
			  else
			  {
				 $ls_cadena=" (  estcom = 4 ";
			  }
		   }
		   else
		   {
			   $ls_cadena=$ls_cadena;
		   }

		   $ls_parentesis="   )   ";

		   if(empty($ls_criterio_c))
		   {
			  $CC_AND=""; //CC = Criterio C
		   }
		   else
		   {
			  $CC_AND="   AND   ";
		   }
		   $ls_criterio_d=$ls_criterio_c.$CC_AND.$ls_cadena.$ls_parentesis;
	   }

		if(  (($as_coduniadmdes!="") && ($as_coduniadmhas=="")) || (($as_coduniadmdes=="") && ($as_coduniadmhas!=""))  )
		{
		   $lb_valido = false;
		   $io_mensajes->message("Debe Completar el Rango de Busqueda por Departamento !!!");
		}
		else
		{
			if(empty($ls_criterio_d))
			 {
				$CD_AND="";  //CD = Criterio D
			 }
			else
			 {
				$CD_AND="  AND  ";
			 }

			if( (($as_coduniadmdes!="") && ($as_coduniadmhas!="")) && (($as_numordcomdes!="") && ($as_numordcomhas!="")) )
			{
			   /*$ls_criterio_e  =  $ls_criterio_d.$CD_AND."  numordcom in (SELECT numordcom FROM soc_enlace_sep   ".
														 "                WHERE  numordcom >='".$as_numordcomdes."' AND numordcom<='".$as_numordcomhas."' AND ".
														 "                numordcom in (SELECT S.numsol FROM sep_solicitud S               ".
														 "                              WHERE  S.coduniadm >='".$as_coduniadmdes."'  AND  S.coduniadm <='".$as_coduniadmhas."' ".
														 "                              ) ".
														 "               )                ";
			*/
				$ls_criterio_e  =  $ls_criterio_d.$CD_AND." soc_ordencompra.coduniadm >='".$as_coduniadmdes."'  AND  soc_ordencompra.coduniadm <='".$as_coduniadmhas."'";
			}
			else
			{
			   if( (($as_coduniadmdes!="") && ($as_coduniadmhas!="")) && (($as_numordcomdes=="") && ($as_numordcomhas=="")) )
			   {
				 /* $ls_criterio_e  =  $ls_criterio_d.$CD_AND."  numordcom in (SELECT numordcom FROM soc_enlace_sep ".
															"                WHERE  numordcom in (SELECT S.numsol FROM sep_solicitud S  ".
															"                                     WHERE  S.coduniadm >='".$as_coduniadmdes."' AND S.coduniadm <='".$as_coduniadmhas."'".
															"                                    ) ".
															"               )                      ";*/
				$ls_criterio_e  =  $ls_criterio_d.$CD_AND." soc_ordencompra.coduniadm >='".$as_coduniadmdes."'  AND  soc_ordencompra.coduniadm <='".$as_coduniadmhas."'";
			   }
			   else
			   {
					if( ($as_coduniadmdes=="") && ($as_coduniadmhas=="") )
					{
						$ls_criterio_e = $ls_criterio_d;
					}
			   }
			}
		}

		if( ($as_tipo=="T") || ($as_tipo=="A") )
		{
			   //************************        Busqueda por Artículo  ******************************
			   if(  (($as_artdes!="") && ($as_arthas=="")) || (($as_artdes=="") && ($as_arthas!=""))  )
				{
				   $lb_valido = false;
				   $io_mensajes->message("Debe Completar el Rango de Busqueda por Artículo !!!");
				}
				else
				{
					if(empty($ls_criterio_e))
					 {
						$CE_AND="";  //CD = Criterio D
					 }
					else
					 {
						$CE_AND="  AND  ";
					 }
					 if(  ($as_artdes!="") && ($as_arthas!="")  )
					 {
						 $ls_criterio_f = $ls_criterio_e.$CE_AND."  numordcom in (SELECT numordcom                                             ".
																 "                FROM soc_dt_bienes                                           ".
																 "                WHERE codart >='".$as_artdes."' AND codart<='".$as_arthas."' ".
																 "                )                                                            ";
					 }
					 else
					 {
						 $ls_criterio_f = $ls_criterio_e;
					 }
				}
		}
		else
		{
		  $ls_criterio_f = $ls_criterio_e;
		}

		if( ($as_tipo=="T") || ($as_tipo=="S") )
		{
			   //************************        Busqueda por Servicios  ******************************
			   if(  (($as_serdes!="") && ($as_serhas=="")) || (($as_serdes=="") && ($as_serhas!=""))  )
				{
				   $lb_valido = false;
				   $io_mensajes->message("Debe Completar el Rango de Busqueda por Servicios !!!");
				}
				else
				{
					if(empty($ls_criterio_f))
					 {
						$CF_AND="";  //CD = Criterio D
					 }
					else
					 {
						$CF_AND="  AND  ";
					 }
					 if(  ($as_serdes!="") && ($as_serhas!="")  )
					 {
						 $ls_criterio_g = $ls_criterio_f.$CF_AND."  numordcom in (SELECT numordcom                                             ".
																 "                FROM soc_dt_servicio                                           ".
																 "                WHERE codser >='".$as_serdes."' AND codser<='".$as_serhas."' ".
																 "                )                                                            ";
					}
					else
					{
						$ls_criterio_g = $ls_criterio_f;
					}
				}
		}
		else
		{
		   $ls_criterio_g = $ls_criterio_f;
		}
		if( ($as_tipord=="A")  ||  ($as_tipord=="") )
		{
			 $ls_criterio_h = $ls_criterio_g;
		}
		else
		{
			 if(empty($ls_criterio_g))
			 {
				 $CG_AND=""; //CC = Criterio C
			 }
			 else
			 {
				$CG_AND="   AND   ";
			 }
			 if($as_tipord=="B")
			 {
				 $ls_criterio_h = $ls_criterio_g.$CG_AND." estcondat='B' ";
			 }
			 else
			 {
				 if($as_tipord=="S")
				 {
					 $ls_criterio_h = $ls_criterio_g.$CG_AND." estcondat='S' ";
				 }
			 }
		}
		if($ls_criterio_h!="")
		{
		   $ls_sql=" SELECT * FROM soc_ordencompra ".
				   " WHERE codemp='".$ls_codemp."'  AND ".$ls_criterio_h." ".
				   " ORDER BY numordcom ASC";
		}
		else
		{
		   $ls_sql=" SELECT * FROM soc_ordencompra ".
				   " WHERE codemp='".ls_codemp."' ".
				   " ORDER BY numordcom ASC";
		}
		//print $ls_sql;
		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$io_mensajes->message("ERROR->uf_select_listado_orden_compra".$io_funciones->uf_convertirmsg($io_sql->message));
			$lb_valido=false;
		}
        return $rs_data;
    }//fin de uf_select_listado_orden_compra
   //---------------------------------------------------------------------------------------------------------------------------------------
	
   //---------------------------------------------------------------------------------------------------------------------------------
	function uf_select_nombre_proveedor($as_codpro)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_nombre_proveedor
		//		   Access: private
		//	    Arguments: as_codpro //codigo del proveedor
		//    Description: Function que devuelve la denominacion de la cuenta presupuestaria
		//	   Creado Por: Ing. Yozelin Barragan.
		// Fecha Creación: 10/04/2007
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_grid, $io_funciones_cxp, $io_funciones, $io_sql, $io_mensajes,$ls_codemp ;
		 $lb_valido=false;
		 $ls_sql=" SELECT   nompro ".
				 " FROM     rpc_proveedor ".
				 " WHERE    codemp='".$ls_codemp."'  AND  cod_pro ='".$as_codpro."' ";
		 $rs=$io_sql->select($ls_sql);
		 if ($rs===false)
		 {
			$lb_valido=false;
			$io_mensajes->message("CLASE->Report MÉTODO->uf_select_nombre_proveedor ERROR->".$io_funciones->uf_convertirmsg($io_sql->message));
		 }
		 else
		 {
			 if($row=$io_sql->fetch_row($rs))
			 {
				$as_nompro=$row["nompro"];
				$lb_valido=true;
			 }
			$io_sql->free_result($rs);
		 }
		 return $as_nompro;
	}//fin 	uf_select_nombre_proveedor
   //---------------------------------------------------------------------------------------------------------------------------------
	
?>