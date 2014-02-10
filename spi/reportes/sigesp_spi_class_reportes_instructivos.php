<?php
class sigesp_spi_class_reportes_instructivos
{
	var $io_msg;
	var $io_fecha;
	var $io_sigesp_int_spg;
	var $io_sql;
	var $ls_codemp;
	var $ls_gestor;
	var $li_estmodest;
	var $sqlca;   
    var $is_msg_error;
	var $dts_empresa; // datastore empresa
	var $dts_reporte;
	var $dts_cab;
	var $obj="";
	var $siginc;
	var $con;
	var $fun;	
	var $sigesp_int_spi;
	var $dts_prog;
	 // Presupuesto de Caja
	var $dts_reporte_temporal;
	var $dts_ingresos_corrientes;
	var $dts_ingresos_capital;
	var $dts_ingresos_financieros;
	var $dts_egresos_consumo;
	var $dts_egresos_corrientes;
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function  sigesp_spi_class_reportes_instructivos()
    {
		require_once("../../shared/class_folder/sigesp_include.php");
		require_once("../../shared/class_folder/class_sql.php");
		require_once("../../shared/class_folder/class_fecha.php");
		require_once("../../shared/class_folder/class_funciones.php");
		require_once("../../shared/class_folder/class_mensajes.php");
		require_once("../../shared/class_folder/class_datastore.php");
		require_once("../../shared/class_folder/class_sigesp_int.php");
		require_once("../../shared/class_folder/class_sigesp_int_scg.php");
		require_once("../../shared/class_folder/class_sigesp_int_spg.php");		
		require_once("../../shared/class_folder/class_sigesp_int_spi.php");	
		
		$this->io_funciones = new class_funciones() ;
		$this->io_include = new sigesp_include();
		$this->io_conexion = $this->io_include->uf_conectar();
		$this->io_sql = new class_sql($this->io_conexion);		
		$this->dts_reporte = new class_datastore();
		$this->io_fecha = new class_fecha();
		$this->io_mensajes = new class_mensajes();
		$this->io_sigesp_int_spi = new class_sigesp_int_spi();
		$this->ls_codemp = $_SESSION["la_empresa"]["codemp"];
	    $this->ls_gestor = $_SESSION["ls_gestor"];
	    $this->li_estmodest = $_SESSION["la_empresa"]["estmodest"];
		$this->io_sigesp_int_spg = new class_sigesp_int_spg();
		
		//Presupuesto de Caja
		$this->dts_reporte_temporal        = new class_datastore();
		$this->dts_ingresos_corrientes     = new class_datastore();
		$this->dts_ingresos_capital        = new class_datastore();
		$this->dts_ingresos_financieros    = new class_datastore();
		$this->dts_incrementos_pasivos     = new class_datastore();
		$this->dts_incrementos_patrimonio  = new class_datastore();
		$this->dts_egresos_consumo         = new class_datastore();
		$this->dts_egresos_corrientes      = new class_datastore();
		
    }
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	//////////////////////////////////////////////////////////////////////////////////
	//   CLASE REPORTES SPG  INSTRUCTIVOS " CONSOLIDADO DE EJECUCION TRIMESTRAL "  //
	/////////////////////////////////////////////////////////////////////////////////
    function uf_spi_reporte_consolidado_de_ejecucion_trimestral($adt_fecdes,$adt_fechas,$as_mesdes,$as_meshas)
    {///////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spi_reporte_consolidado_de_ejecucion_trimestral
	 //         Access :	private
	 //     Argumentos :    $adt_fecdes  -----> fechas desde 
	 //                     $adt_fechas  -----> fechas hasta   
	 //                     $as_mesdes  -----> mes desde         
	 //                     $as_meshas  -----> mes hasta         
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	Reporte que genera salida  del instructivo 07 del CONSOLIDADO DE EJECUCION TRIMESTRAL
	 //     Creado por :    Ing. Yozelin Barragán.
	 // Fecha Creación :    18/05/2008          Fecha última Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $lb_valido = true;	 
	 $ls_sql=" SELECT max(codemp) as codemp, spi_cuenta, max(denominacion) as denominacion,             ".
	         "        max(status) as status, max(sc_cuenta) as sc_cuenta, sum(previsto) as previsto,    ".
             "        sum(cobrado_anticipado) as cobrado_anticipado, sum(cobrado) as cobrado,           ".
             "        sum(devengado) as devengado, sum(aumento) as aumento,                             ".
             "        sum(disminucion) as disminucion, sum(enero) as enero , sum(febrero) as febrero ,  ".
             "        sum(marzo) as marzo, sum(abril) as abril, sum(mayo) as mayo, sum(junio) as junio, ".
             "        sum(julio) as julio, sum(agosto) as agosto, sum(septiembre) as septiembre,        ".
             "        sum(octubre) as octubre, sum(noviembre) as noviembre,                             ".
			 "        sum(diciembre) as diciembre, max(nivel) as nivel, max(referencia) as referencia,  ".
			 "        sum(enero+febrero+marzo) as trimetrei, sum(abril+mayo+junio) as trimetreii,       ".
			 "        sum(julio+agosto+septiembre) as trimetreiii,                                      ".
			 "        sum(octubre+noviembre+diciembre) as trimetreiv                                    ".
             " FROM spi_cuentas                                                                         ".
             " WHERE codemp='".$this->ls_codemp."'  AND  nivel = '1'                                    ".
             " GROUP BY spi_cuenta                                                                      ".
             " ORDER BY spi_cuenta ";	
     $rs_data=$this->io_sql->select($ls_sql);
	 if($rs_data===false)
	 {   // error interno sql
			$this->io_mensajes->message("CLASE->sigesp_spi_class_reportes_instructivos ". 
			                            "MÉTODO->uf_spi_reporte_consolidado_de_ejecucion_trimestral ".
										"ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido = false;
	 }
	 else
	 {		
		//$li_numrows=$this->io_sql->num_rows($rs_data);	
		$li_numrows=$rs_data->RecordCount();
		if($li_numrows>0)
		{
		     while(!$rs_data->EOF)
			 {
			   $ls_spi_cuenta=$rs_data->fields["spi_cuenta"];
			   $ls_denominacion=$rs_data->fields["denominacion"];
			   $li_nivel=$rs_data->fields["nivel"];
			   $ld_previsto=$rs_data->fields["previsto"];
			   $ld_recaudado_total=0;
			   $ld_cobrado_total=0;
			   $ld_devengado_total=0;
			   $ld_aumento_total=0;
			   $ld_disminucion_total=0;
			   $ld_enero=$rs_data->fields["enero"];
			   $ld_febrero=$rs_data->fields["febrero"];
			   $ld_marzo=$rs_data->fields["marzo"];
			   $ld_abril=$rs_data->fields["abril"];
			   $ld_mayo=$rs_data->fields["mayo"];
			   $ld_junio=$rs_data->fields["junio"];
			   $ld_julio=$rs_data->fields["julio"];
			   $ld_agosto=$rs_data->fields["agosto"];
			   $ld_septiembre=$rs_data->fields["septiembre"];
			   $ld_octubre=$rs_data->fields["octubre"];
			   $ld_noviembre=$rs_data->fields["noviembre"];
			   $ld_diciembre=$rs_data->fields["diciembre"];
			   $ld_trimetreI=$rs_data->fields["trimetrei"]; 
			   $ld_trimetreII=$rs_data->fields["trimetreii"]; 
			   $ld_trimetreIII=$rs_data->fields["trimetreiii"]; 
			   $ld_trimetreIV=$rs_data->fields["trimetreiv"]; 
			   
			   $lb_valido=$this->uf_spi_ejecutado_trimestral($ls_spi_cuenta,$adt_fecdes,$adt_fechas,&$ld_recaudado,
			                                                 &$ld_cobrado,&$ld_devengado,&$ld_aumento,&$ld_disminucion);
			   if($lb_valido)
		       {
				   $lb_valido=$this->uf_spi_ejecutado_acumulado($ls_spi_cuenta,$adt_fechas,&$ld_recaudado_acumulado,
																&$ld_cobrado_acumulado,&$ld_devengado_acumulado,
																&$ld_aumento_acumulado,&$ld_disminucion_acumulado);
			   }//if
			   if($as_mesdes=='Enero')
		       {
				   $ld_programado_trimestral=$ld_trimetreI;
				   $ld_programado_acumulado=$ld_trimetreI;
			   }//if
			   if($as_mesdes=='Abril')
		       {
				   $ld_programado_trimestral=$ld_trimetreII;
				   $ld_programado_acumulado=$ld_trimetreI+$ld_trimetreII;
			   }//if
			   if($as_mesdes=='Julio')
		       {
				   $ld_programado_trimestral=$ld_trimetreIII;
				   $ld_programado_acumulado=$ld_trimetreI+$ld_trimetreII+$ld_trimetreIII;
			   }//if
			   if($as_mesdes=='Octubre')
		       {
				   $ld_programado_trimestral=$ld_trimetreIV;
				   $ld_programado_acumulado=$ld_trimetreI+$ld_trimetreII+$ld_trimetreIII+$ld_trimetreIV;
			   }//if
			   $ld_previsto_modificado=$ld_previsto+$ld_aumento_acumulado-$ld_disminucion_acumulado;
			   $ld_ingresosxrecibir=$ld_previsto+$ld_aumento_acumulado-$ld_disminucion_acumulado-$ld_devengado_acumulado;
			   $this->dts_reporte->insertRow("spi_cuenta",$ls_spi_cuenta);
			   $this->dts_reporte->insertRow("denominacion",$ls_denominacion);
			   $this->dts_reporte->insertRow("previsto",$ld_previsto);
			   $this->dts_reporte->insertRow("previsto_modificado",$ld_previsto_modificado);
			   $this->dts_reporte->insertRow("programado",$ld_programado_trimestral);
		  	   $this->dts_reporte->insertRow("cobrado",$ld_cobrado);					 
			   $this->dts_reporte->insertRow("devengado",$ld_devengado);
			   $this->dts_reporte->insertRow("recaudado",$ld_recaudado);					 
			   $this->dts_reporte->insertRow("programado_acumulado",$ld_programado_acumulado);
			   $this->dts_reporte->insertRow("recaudado_acumulado",$ld_recaudado_acumulado);
		  	   $this->dts_reporte->insertRow("cobrado_acumulado",$ld_cobrado_acumulado);					 
			   $this->dts_reporte->insertRow("devengado_acumulado",$ld_devengado_acumulado);	
			   $this->dts_reporte->insertRow("ingresosxrecibir",$ld_ingresosxrecibir);	
			   $lb_valido=true;
			   $rs_data->MoveNext();
		    }//while
	    }//if	
	 $this->io_sql->free_result($rs_data);
	 }//else
     return $lb_valido;
    }//fin uf_spg_reporte_consolidado_de_ejecucion_trimestral
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	function uf_spi_reporte_total_ingresos($adt_fecdes,$adt_fechas,$as_mesdes,$as_meshas,$aa_cuentas=NULL)
    {///////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spi_reporte_total_ingresos
	 //         Access :	private
	 //     Argumentos :    $adt_fecdes  -----> fechas desde 
	 //                     $adt_fechas  -----> fechas hasta   
	 //                     $as_mesdes   -----> mes desde         
	 //                     $as_meshas   -----> mes hasta 
	 //                     $aa_cuentas  -----> Arreglo opcional de cuentas para la totalizacion de los Ingresos      
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	Reporte que genera salida  del instructivo 07 del CONSOLIDADO DE EJECUCION TRIMESTRAL
	 //     Creado por :    Ing. Arnaldo Suárez
	 // Fecha Creación :    18/05/2008          Fecha última Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $lb_valido = true;
	 $ls_cadena_filtro = "";
	 if(($aa_cuentas != NULL)&&(is_array($aa_cuentas)))
	 {
	   $li_total = count($aa_cuentas);
	   for($i=0; $i<$li_total;$i++)
	   {
	    $ls_cta_aux = $this->io_sigesp_int_spi->uf_spi_cuenta_sin_cero($aa_cuentas[$i])."%";
		if($i==0)
		{
	     $ls_cadena_filtro .= " AND ( spi_cuenta LIKE '".$ls_cta_aux."' ";
		}
		else
		{
		 $ls_cadena_filtro .= " OR spi_cuenta LIKE '".$ls_cta_aux."' ";
		}
	   }
	   $ls_cadena_filtro .= ")";
	 }	 
	 $ls_sql=" SELECT spi_cuenta, sum(previsto) as previsto,    ".
             "        sum(cobrado_anticipado) as cobrado_anticipado, sum(cobrado) as cobrado,           ".
             "        sum(devengado) as devengado, sum(aumento) as aumento,                             ".
             "        sum(disminucion) as disminucion,                              ".
			 "        sum(enero+febrero+marzo) as trimestrei, sum(abril+mayo+junio) as trimestreii,       ".
			 "        sum(julio+agosto+septiembre) as trimestreiii,                                      ".
			 "        sum(octubre+noviembre+diciembre) as trimestreiv                                    ".
             " FROM spi_cuentas                                                                         ".
             " WHERE codemp='".$this->ls_codemp."'  AND spi_cuenta like '3%' AND status = 'C'           ".$ls_cadena_filtro.
             " GROUP BY spi_cuenta                                                               ".
             " ORDER BY spi_cuenta ";	 	
     $rs_data=$this->io_sql->select($ls_sql);
	 if($rs_data===false)
	 {   // error interno sql
			$this->io_mensajes->message("CLASE->sigesp_spi_class_reportes_instructivos ". 
			                            "MÉTODO->uf_spi_reporte_total_ingresos ".
										"ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido = false;
	 }
	 else
	 {		
		//$li_numrows=$this->io_sql->num_rows($rs_data);	
		$li_numrows=$rs_data->RecordCount();
		if($li_numrows>=0)
		{
		     $ld_ejecutadoacum = 0;
			 $ld_ejecutado = 0;
			 $ld_programado = 0;
			 $ld_programadoacum = 0;
			 $ld_asignado_modificado = 0;
			 $ld_asignado = 0;
			 $ld_totejeacum = 0;
			 $ld_toteje = 0;
			 while(!$rs_data->EOF)
			 {
			   $ls_spi_cuenta=$rs_data->fields["spi_cuenta"];
			   $ld_previsto=$rs_data->fields["previsto"];
			   $ld_cobrado_total=$rs_data->fields["cobrado"];
			   $ld_devengado_total=$rs_data->fields["devengado"];
			   $ld_aumento_total=$rs_data->fields["aumento"];
			   $ld_disminucion_total=$rs_data->fields["disminucion"];
			   $ld_trimetreI=$rs_data->fields["trimestrei"]; 
			   $ld_trimetreII=$rs_data->fields["trimestreii"]; 
			   $ld_trimetreIII=$rs_data->fields["trimestreiii"]; 
			   $ld_trimetreIV=$rs_data->fields["trimestreiv"]; 
			   $ld_asignado = $ld_asignado + $ld_previsto;
			   $ld_recaudado = 0;
			   $ld_ejecutado = 0;
			   $ld_devengado = 0;
			   $ld_aumento = 0;
			   $ld_disminucion = 0;
			   $ld_recaudado_acumulado = 0;
			   $ld_ejecutadoacum = 0;
			   $ld_devengado_acumulado = 0;
			   $ld_aumento_acumulado = 0;
			   $ld_disminucion_acumulado = 0;
			   $lb_valido=$this->uf_spi_ejecutado_trimestral($ls_spi_cuenta,$adt_fecdes,$adt_fechas,&$ld_recaudado,
			                                                 &$ld_ejecutado,&$ld_devengado,&$ld_aumento,&$ld_disminucion);
			   if($lb_valido)
		       {
				   $lb_valido=$this->uf_spi_ejecutado_acumulado($ls_spi_cuenta,$adt_fechas,&$ld_recaudado_acumulado,
																&$ld_ejecutadoacum,&$ld_devengado_acumulado,
																&$ld_aumento_acumulado,&$ld_disminucion_acumulado);
			   }//if
			   
			   $ld_totejeacum = $ld_totejeacum + $ld_ejecutadoacum;
			   $ld_toteje =  $ld_toteje + $ld_ejecutado;
			   if($as_mesdes=='Enero')
		       {
				   $ld_programado= $ld_programado+ $ld_trimetreI;
				   $ld_programadoacum= $ld_programadoacum +$ld_trimetreI;
			   }//if
			   if($as_mesdes=='Abril')
		       {
				   $ld_programado= $ld_programado + $ld_trimetreII;
				   $ld_programadoacum= $ld_programadoacum +=$ld_trimetreI+$ld_trimetreII;
			   }//if
			   if($as_mesdes=='Julio')
		       {
				   $ld_programado= $ld_programado + $ld_trimetreIII;
				   $ld_programadoacum= $ld_programadoacum + $ld_trimetreI+$ld_trimetreII+$ld_trimetreIII;
			   }//if
			   if($as_mesdes=='Octubre')
		       {
				   $ld_programado=$ld_programado + $ld_trimetreIV;
				   $ld_programadoacum= $ld_programadoacum + $ld_trimetreI+$ld_trimetreII+$ld_trimetreIII+$ld_trimetreIV;
			   }//if
			   $ld_asignado_modificado=$ld_asignado_modificado+$ld_previsto+$ld_aumento_acumulado-$ld_disminucion_acumulado;
			   $lb_valido=true;
			   $rs_data->MoveNext();
		    }//while
			if ($ld_programado>0)
			{
			 $ld_porcentual = ($ld_toteje/$ld_programado)*100;
			}
			else
			{
			 $ld_porcentual = 0;
			}
			$ls_formpre=$_SESSION["la_empresa"]["formpre"];
			$ls_formpre=str_replace('-','',$ls_formpre);
			$li_len=strlen($ls_formpre);
			$li_len=$li_len-9;
			$ls_ceros=$this->io_funciones->uf_cerosderecha("",$li_len);
			$this->dts_reporte->insertRow("cuenta","300000000".$ls_ceros);
			$this->dts_reporte->insertRow("denominacion",'<b>INGRESOS</b>');
			$this->dts_reporte->insertRow("asignado",$ld_asignado);
			$this->dts_reporte->insertRow("modificado",$ld_asignado_modificado);
			$this->dts_reporte->insertRow("programado",$ld_programado);
			$this->dts_reporte->insertRow("ejecutado", $ld_toteje);		
			$this->dts_reporte->insertRow("absoluto",abs($ld_ejecutado - $ld_programado));		
			$this->dts_reporte->insertRow("porcentual",$ld_porcentual);		
			$this->dts_reporte->insertRow("programado_acumulado",$ld_programadoacum);
			$this->dts_reporte->insertRow("ejecutado_acumulado",$ld_totejeacum);  
	    }//if	
	 $this->io_sql->free_result($rs_data);
	 }//else
     return $lb_valido;
    }//fin uf_spg_reporte_consolidado_de_ejecucion_trimestral
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	function uf_spg_reporte_total_egresos($adt_fecdes,$adt_fechas,$as_mesdes,$as_meshas,$aa_cuentas=NULL)
    {///////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_total_egresos
	 //         Access :	private
	 //     Argumentos :    $adt_fecdes  -----> fechas desde 
	 //                     $adt_fechas  -----> fechas hasta   
	 //                     $as_mesdes   -----> mes desde         
	 //                     $as_meshas   -----> mes hasta
	 //	                    $aa_cuentas  -----> Arreglo opcional de cuentas para filtrado de Egresos       
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	Reporte que genera salida  del instructivo 07
	 //     Creado por :    Ing. Arnaldo Suárez
	 // Fecha Creación :    18/05/2008          Fecha última Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $lb_valido = true;
	 $ls_cadena_filtro = "";
	 if(($aa_cuentas != NULL)&&(is_array($aa_cuentas)))
	 {
	   $li_total = count($aa_cuentas);
	   for($i=0; $i<$li_total;$i++)
	   {
	    $ls_cta_aux = $this->io_sigesp_int_spg->uf_spg_cuenta_sin_cero($aa_cuentas[$i])."%";
		if($i==0)
		{
	     $ls_cadena_filtro .= " AND ( spg_cuenta LIKE '".$ls_cta_aux."' ";
		}
		else
		{
		 $ls_cadena_filtro .= " OR spg_cuenta LIKE '".$ls_cta_aux."' ";
		}
	   }
	   $ls_cadena_filtro .= ")";
	 }	 
	 $ls_sql=" SELECT spg_cuenta, sum(asignado) as asignado,                                     ".
			 "        sum(enero+febrero+marzo) as trimetrei, sum(abril+mayo+junio) as trimetreii,       ".
			 "        sum(julio+agosto+septiembre) as trimetreiii,                                      ".
			 "        sum(octubre+noviembre+diciembre) as trimetreiv                                    ".
             " FROM spg_cuentas                                                                         ".
             " WHERE codemp='".$this->ls_codemp."'  AND spg_cuenta like '4%' and status = 'C'           ".$ls_cadena_filtro.
             " GROUP BY spg_cuenta                                                               ".
             " ORDER BY spg_cuenta";		 			 	
     $rs_data=$this->io_sql->select($ls_sql);
	 if($rs_data===false)
	 {   // error interno sql
			$this->io_mensajes->message("CLASE->sigesp_spi_class_reportes_instructivos ". 
			                            "MÉTODO->uf_spg_reporte_total_egresos ".
										"ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido = false;
	 }
	 else
	 {		
		//$li_numrows=$this->io_sql->num_rows($rs_data);	
		$li_numrows=$rs_data->RecordCount();
		if($li_numrows>=0)
		{
		     $ld_ejecutadoacum = 0;
			 $ld_ejecutado = 0;
			 $ld_programado = 0;
			 $ld_asignadoacum = 0;
			 $ld_programadoacum = 0;
			 $ld_asignado_modificado = 0;
			 $ld_asignado = 0;
			 $ld_totejeacum = 0;
			 $ld_toteje = 0;
			 while(!$rs_data->EOF)
			 {
			   $ls_spg_cuenta=$rs_data->fields["spg_cuenta"];
			   $ld_asignado=$rs_data->fields["asignado"];
			   $ld_trimetreI=$rs_data->fields["trimetrei"]; 
			   $ld_trimetreII=$rs_data->fields["trimetreii"]; 
			   $ld_trimetreIII=$rs_data->fields["trimetreiii"]; 
			   $ld_trimetreIV=$rs_data->fields["trimetreiv"];
			   $ld_comprometer_acumulado = 0;
			   $ld_ejecutadoacum = 0;
			   $ld_pagado_acumulado = 0;
			   $ld_aumento_acumulado = 0;
			   $ld_disminucion_acumulado =0;
			   $ld_comprometer = 0;
			   $ld_ejecutado = 0;
			   $ld_pagado = 0;
			   $ld_aumento = 0;
			   $ld_disminucion = 0;
			   $ld_asignadoacum = $ld_asignadoacum + $ld_asignado;

			   if($as_mesdes=='Enero')
		       {
				   $ld_programado= $ld_programado+ $ld_trimetreI;
				   $ld_programadoacum= $ld_programadoacum +$ld_trimetreI;
			   }//if
			   if($as_mesdes=='Abril')
		       {
				   $ld_programado= $ld_programado + $ld_trimetreII;
				   $ld_programadoacum= $ld_programadoacum +=$ld_trimetreI+$ld_trimetreII;
			   }//if
			   if($as_mesdes=='Julio')
		       {
				   $ld_programado= $ld_programado + $ld_trimetreIII;
				   $ld_programadoacum= $ld_programadoacum + $ld_trimetreI+$ld_trimetreII+$ld_trimetreIII;
			   }//if
			   if($as_mesdes=='Octubre')
		       {
				   $ld_programado=$ld_programado + $ld_trimetreIV;
				   $ld_programadoacum= $ld_programadoacum + $ld_trimetreI+$ld_trimetreII+$ld_trimetreIII+$ld_trimetreIV;
			   }//if
			   $lb_valido=$this->uf_spg_ejecutado_trimestral_estado_resultado($ls_spg_cuenta,$adt_fecdes,$adt_fechas,
			                                                                  &$ld_comprometer,&$ld_ejecutado,&$ld_pagado,
																			  &$ld_aumento,&$ld_disminucion,false,true);
				if($lb_valido)
				{
					   $lb_valido=$this->uf_spg_ejecutado_acumulado_estado_resultado($ls_spg_cuenta,$adt_fechas,&$ld_comprometer_acumulado,
																					 &$ld_ejecutadoacum,&$ld_pagado_acumulado,
																					 &$ld_aumento_acumulado,&$ld_disminucion_acumulado,
																					 false,true);
				}//if
			   
			   $ld_asignado_modificado=$ld_asignado_modificado+$ld_asignado+$ld_aumento_acumulado-$ld_disminucion_acumulado;
			   $ld_totejeacum +=  $ld_pagado_acumulado;
			   $ld_toteje += $ld_pagado;
			   $lb_valido=true;
			   $rs_data->MoveNext();
		    }//while
			if ($ld_programado>0)
			{
			 $ld_porcentual = ($ld_toteje/$ld_programado)*100;
			}
			else
			{
			 $ld_porcentual = 0;
			}
			$ls_formpre=$_SESSION["la_empresa"]["formpre"];
			$ls_formpre=str_replace('-','',$ls_formpre);
			$li_len=strlen($ls_formpre);
			$li_len=$li_len-9;
			$ls_ceros=$this->io_funciones->uf_cerosderecha("",$li_len);
			$this->dts_reporte->insertRow("cuenta",trim("400000000".$ls_ceros));
			$this->dts_reporte->insertRow("denominacion",'<b>EGRESOS</b>');
			$this->dts_reporte->insertRow("asignado",$ld_asignadoacum);
			$this->dts_reporte->insertRow("modificado",$ld_asignado_modificado);
			$this->dts_reporte->insertRow("programado",$ld_programado);
			$this->dts_reporte->insertRow("ejecutado", $ld_toteje);		
			$this->dts_reporte->insertRow("absoluto",abs($ld_programado-$ld_ejecutado));		
			$this->dts_reporte->insertRow("porcentual",$ld_porcentual);		
			$this->dts_reporte->insertRow("programado_acumulado",$ld_programadoacum);
			$this->dts_reporte->insertRow("ejecutado_acumulado",$ld_totejeacum);  
	    }//if	
	 $this->io_sql->free_result($rs_data);
	 }//else
     return $lb_valido;
    }//fin uf_spg_reporte_consolidado_de_ejecucion_trimestral
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_spi_ejecutado_trimestral($as_spi_cuenta,$adt_fecdes,$adt_fechas,&$ad_recaudado,
	                                     &$ad_cobrado,&$ad_devengado,&$ad_aumento,&$ad_disminucion,$aa_cuentas=NULL)
    {///////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spi_ejecutado_trimestral
	 //         Access :	private
	 //     Argumentos :    $as_spi_cuenta  -----> cuenta 
	 //                     $adt_fecdes     -----> fechas desde 
	 //                     $adt_fechas     -----> fechas hasta  
	 //                     $ad_recaudado   -----> monto cobrado_anticipado referencia   
	 //                     $ad_cobrado     -----> monto cobrado referencia   
	 //                     $ad_devengado   -----> monto devengado referencia   
	 //                     $ad_aumento     -----> monto aumento referencia   
	 //                     $ad_disminucion -----> monto disminucion referencia   
	 //                     $aa_cuentas     -----> arreglo con las cuentas a tomar en cuenta para los movimientos, es opcional
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	Reporte que genera los ejecutados por trimestre
	 //     Creado por :    Ing. Yozelin Barragán.
	 // Modificado por :    Ing. Arnaldo Suárez
	 // Fecha Creación :    21/05/2008          Fecha última Modificacion : 23/02/2010     Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $lb_valido = true;	 
	 $ad_recaudado = 0;
	 $ad_cobrado = 0;
	 $ad_devengado = 0;
	 $ad_aumento = 0;
	 $ad_disminucion = 0;
	 $as_spi_cuenta = $this->io_sigesp_int_spi->uf_spi_cuenta_sin_cero($as_spi_cuenta)."%";
	 $ls_cadena_filtro = "";
	 if(($aa_cuentas != NULL)&&(is_array($aa_cuentas)))
	 {
	   $li_total = count($aa_cuentas);
	   for($i=0; $i<$li_total;$i++)
	   {
	    $ls_cta_aux = $this->io_sigesp_int_spi->uf_spi_cuenta_sin_cero($aa_cuentas[$i])."%";
		if($i==0)
		{
	     $ls_cadena_filtro .= "( DT.spi_cuenta LIKE '".$ls_cta_aux."' ";
		}
		else
		{
		 $ls_cadena_filtro .= " OR DT.spi_cuenta LIKE '".$ls_cta_aux."' ";
		}
	   }
	   $ls_cadena_filtro .= ") AND ";
	 }
	 else
	 {
	  $ls_cadena_filtro = " DT.spi_cuenta like '".$as_spi_cuenta."'  AND ";
	 }
	 
	 // CALCULO DEL COBRADO
	 $ls_sql_cobrado="SELECT   COALESCE(SUM(DT.monto),0.00) as cobrado ".
					 "	FROM   spi_dt_cmp DT, spi_operaciones OP ".
					 "	WHERE  DT.codemp='".$this->ls_codemp."' AND ".
					 "		   DT.operacion = OP.operacion AND ".
					 "		   OP.cobrado = 1 AND ".$ls_cadena_filtro.
					 //"		   DT.spi_cuenta like '".$as_spi_cuenta."'  AND ".
					 "		   DT.fecha BETWEEN '".$adt_fecdes."' AND  '".$adt_fechas."'";
	 $rs_ejecutado_cob=$this->io_sql->select($ls_sql_cobrado);
	 if($rs_ejecutado_cob===false)
	 { // error interno sql
			$this->io_mensajes->message("CLASE->sigesp_spi_class_reportes_instructivos ". 
			                            "MÉTODO->uf_spi_ejecutado_trimestral ".
										"ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		$lb_valido = false;
 	 }
	 elseif(!$rs_ejecutado_cob->EOF)
	 {
	  $ad_cobrado = $rs_ejecutado_cob->fields["cobrado"];
	 }
	 
	 //echo $ls_sql_cobrado."<br><br><br>";
	 // CALCULO DEL DEVENGADO
	 $ls_sql_devengado=	 " SELECT   COALESCE(SUM(DT.monto),0.00) as devengado ".
						 "	FROM   spi_dt_cmp DT, spi_operaciones OP ".
						 "	WHERE  DT.codemp='".$this->ls_codemp."' AND ".
						 "		   DT.operacion = OP.operacion AND ".
						 "		   OP.devengado = 1 AND ".$ls_cadena_filtro.
						 //"		   DT.spi_cuenta like '".$as_spi_cuenta."'  AND ".
						 "		   DT.fecha BETWEEN '".$adt_fecdes."' AND  '".$adt_fechas."'";
	 $rs_ejecutado_dev=$this->io_sql->select($ls_sql_devengado);
	 if($rs_ejecutado_dev===false)
	 { // error interno sql
			$this->io_mensajes->message("CLASE->sigesp_spi_class_reportes_instructivos ". 
			                            "MÉTODO->uf_spi_ejecutado_trimestral ".
										"ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		$lb_valido = false;
 	 }
	 elseif(!$rs_ejecutado_dev->EOF)
	 {
	  $ad_devengado = $rs_ejecutado_dev->fields["devengado"];
	 }
	 
	 // CALCULO DEL AUMENTO
	 $ls_sql_aumento=	 " SELECT   COALESCE(SUM(DT.monto),0.00) as aumento ".
						 "	FROM   spi_dt_cmp DT, spi_operaciones OP ".
						 "	WHERE  DT.codemp='".$this->ls_codemp."' AND ".
						 "		   DT.operacion = OP.operacion AND ".
						 "		   OP.aumento = 1 AND ".$ls_cadena_filtro.
						 //"		   DT.spi_cuenta like '".$as_spi_cuenta."'  AND ".
						 "		   DT.fecha BETWEEN '".$adt_fecdes."' AND  '".$adt_fechas."'";
	 $rs_ejecutado_aum=$this->io_sql->select($ls_sql_aumento);
	 if($rs_ejecutado_aum===false)
	 { // error interno sql
			$this->io_mensajes->message("CLASE->sigesp_spi_class_reportes_instructivos ". 
			                            "MÉTODO->uf_spi_ejecutado_trimestral ".
										"ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		$lb_valido = false;
 	 }
	 elseif(!$rs_ejecutado_aum->EOF)
	 {
	  $ad_aumento = $rs_ejecutado_aum->fields["aumento"];
	 }
	 
	 // CALCULO DEL DISMINUCION
	 $ls_sql_disminucion=" SELECT   COALESCE(SUM(DT.monto),0.00) as disminucion ".
						 "	FROM   spi_dt_cmp DT, spi_operaciones OP ".
						 "	WHERE  DT.codemp='".$this->ls_codemp."' AND ".
						 "		   DT.operacion = OP.operacion AND ".
						 "		   OP.disminucion = 1 AND ".$ls_cadena_filtro.
						 //"		   DT.spi_cuenta like '".$as_spi_cuenta."'  AND ".
						 "		   DT.fecha BETWEEN '".$adt_fecdes."' AND  '".$adt_fechas."'";
	 $rs_ejecutado_dis=$this->io_sql->select($ls_sql_disminucion);
	 if($rs_ejecutado_dis===false)
	 { // error interno sql
			$this->io_mensajes->message("CLASE->sigesp_spi_class_reportes_instructivos ". 
			                            "MÉTODO->uf_spi_ejecutado_trimestral ".
										"ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		$lb_valido = false;
 	 }
	 elseif(!$rs_ejecutado_dis->EOF)
	 {
	  $ad_disminucion = $rs_ejecutado_dis->fields["disminucion"];
	 }
	 //echo $as_spi_cuenta." - RECAUDADO: ".$ad_recaudado." - COBRADO: ".$ad_cobrado." - DEVENGADO: ".$ad_devengado." - AUMENTO: ".$ad_aumento." - DOSMINUCION: ".$ad_disminucion."<br><br>";
	 $this->io_sql->free_result($rs_ejecutado_cob);
	 $this->io_sql->free_result($rs_ejecutado_dev);
	 $this->io_sql->free_result($rs_ejecutado_aum);
	 $this->io_sql->free_result($rs_ejecutado_dis);
	  return $lb_valido;	
     }//fin uf_spg_ejecutado_trimestral
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_spi_ejecutado_acumulado($as_spi_cuenta,$adt_fechas,&$ad_recaudado_acumulado,&$ad_cobrado_acumulado,
	                                    &$ad_devengado_acumulado,&$ad_aumento_acumulado,&$ad_disminucion_acumulado,$aa_cuentas=NULL)
    {///////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spi_ejecutado_trimestral
	 //         Access :	private
	 //     Argumentos :    $as_spi_cuenta             -----> cuenta 
	 //                     $adt_fechas                -----> fechas hasta    
	 //                     $ad_recaudado_acumulado    -----> monto acumulado cobrado_anticipado referencia   
	 //                     $ad_cobrado_acumulado      -----> monto acumulado cobrado referencia   
	 //                     $ad_devengado_acumulado    -----> monto acumulado devengado referencia   
	 //                     $ad_aumento_acumulado      -----> monto acumulado aumento referencia   
	 //                     $ad_disminucion_acumulado  -----> monto acumulado disminucion referencia
	 //                     $aa_cuentas                -----> arreglo de cuentas para filtrado, es opcional    
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	Reporte que genera los eejcutados por trimestre
	 //     Creado por :    Ing. Yozelin Barragán
	 // Modificado por :    Ing. Arnaldo Suárez
	 // Fecha Creación :    21/05/2008          Fecha última Modificacion : 24/02/2010  Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $lb_valido = true;
	 $ad_recaudado_acumulado = 0;
	 $ad_cobrado_acumulado = 0;
	 $ad_devengado_acumulado = 0;
	 $ad_aumento_acumulado = 0;
	 $ad_disminucion_acumulado = 0;
	 $as_spi_cuenta = $this->io_sigesp_int_spi->uf_spi_cuenta_sin_cero($as_spi_cuenta)."%";
	 $ls_cadena_filtro = "";
	 
	 if(($aa_cuentas != NULL)&&(is_array($aa_cuentas)))
	 {
	   $li_total = count($aa_cuentas);
	   for($i=0; $i<$li_total;++$i)
	   {
	    $ls_cta_aux = $this->io_sigesp_int_spi->uf_spi_cuenta_sin_cero($aa_cuentas[$i-1])."%";
		if($i==0)
		{
	     $ls_cadena_filtro .= "( DT.spi_cuenta LIKE '".$ls_cta_aux."' ";
		}
		else
		{
		 $ls_cadena_filtro .= " OR DT.spi_cuenta LIKE '".$ls_cta_aux."' ";
		}
	   }
	   $ls_cadena_filtro .= ") AND ";
	 }
	 else
	 {
	  $ls_cadena_filtro = " DT.spi_cuenta like '".$as_spi_cuenta."'  AND ";
	 }
	 
	 
	  // CALCULO DEL COBRADO
	 $ls_sql_cobrado="SELECT   COALESCE(SUM(DT.monto),0.00) as cobrado ".
					 "	FROM   spi_dt_cmp DT, spi_operaciones OP ".
					 "	WHERE  DT.codemp='".$this->ls_codemp."' AND ".
					 "		   DT.operacion = OP.operacion AND ".
					 "		   OP.cobrado = 1 AND ".$ls_cadena_filtro.
					 //"		   DT.spi_cuenta like '".$as_spi_cuenta."'  AND ".
					 "		   DT.fecha <=  '".$adt_fechas."'";
	 $rs_ejecutado_cob=$this->io_sql->select($ls_sql_cobrado);
	 if($rs_ejecutado_cob===false)
	 { // error interno sql
			$this->io_mensajes->message("CLASE->sigesp_spi_class_reportes_instructivos ". 
			                            "MÉTODO->uf_spi_ejecutado_trimestral ".
										"ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		$lb_valido = false;
 	 }
	 elseif(!$rs_ejecutado_cob->EOF)
	 {
	  $ad_cobrado_acumulado=$rs_ejecutado_cob->fields["cobrado"];
	  $ad_recaudado_acumulado=$rs_ejecutado_cob->fields["cobrado"];
	 }
	 
	 // CALCULO DEL DEVENGADO
	 $ls_sql_devengado=	 " SELECT   COALESCE(SUM(DT.monto),0.00) as devengado ".
						 "	FROM   spi_dt_cmp DT, spi_operaciones OP ".
						 "	WHERE  DT.codemp='".$this->ls_codemp."' AND ".
						 "		   DT.operacion = OP.operacion AND ".
						 "		   OP.devengado = 1 AND ". $ls_cadena_filtro.
						 //"		   DT.spi_cuenta like '".$as_spi_cuenta."'  AND ".
						 "		   DT.fecha <= '".$adt_fechas."'";
	 $rs_ejecutado_dev=$this->io_sql->select($ls_sql_devengado);
	 if($rs_ejecutado_dev===false)
	 { // error interno sql
			$this->io_mensajes->message("CLASE->sigesp_spi_class_reportes_instructivos ". 
			                            "MÉTODO->uf_spi_ejecutado_trimestral ".
										"ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		$lb_valido = false;
 	 }
	 elseif(!$rs_ejecutado_dev->EOF)
	 {
	  $ad_devengado_acumulado=$rs_ejecutado_dev->fields["devengado"];
	 }
	 
	 // CALCULO DEL AUMENTO
	 $ls_sql_aumento=	 " SELECT   COALESCE(SUM(DT.monto),0.00) as aumento ".
						 "	FROM   spi_dt_cmp DT, spi_operaciones OP ".
						 "	WHERE  DT.codemp='".$this->ls_codemp."' AND ".
						 "		   DT.operacion = OP.operacion AND ".
						 "		   OP.aumento = 1 AND ". $ls_cadena_filtro.
						 //"		   DT.spi_cuenta like '".$as_spi_cuenta."'  AND ".
						 "		   DT.fecha <='".$adt_fechas."'";
	 $rs_ejecutado_aum=$this->io_sql->select($ls_sql_aumento);
	 if($rs_ejecutado_aum===false)
	 { // error interno sql
			$this->io_mensajes->message("CLASE->sigesp_spi_class_reportes_instructivos ". 
			                            "MÉTODO->uf_spi_ejecutado_trimestral ".
										"ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		$lb_valido = false;
 	 }
	 elseif(!$rs_ejecutado_aum->EOF)
	 {
	  $ad_aumento_acumulado = $rs_ejecutado_aum->fields["aumento"];
	 }
	 
	 // CALCULO DEL DISMINUCION
	 $ls_sql_disminucion=" SELECT   COALESCE(SUM(DT.monto),0.00) as disminucion ".
						 "	FROM   spi_dt_cmp DT, spi_operaciones OP ".
						 "	WHERE  DT.codemp='".$this->ls_codemp."' AND ".
						 "		   DT.operacion = OP.operacion AND ".
						 "		   OP.disminucion = 1 AND ".$ls_cadena_filtro.
						 //"		   DT.spi_cuenta like '".$as_spi_cuenta."'  AND ".
						 "		   DT.fecha <= '".$adt_fechas."'";
	 $rs_ejecutado_dis=$this->io_sql->select($ls_sql_disminucion);
	 if($rs_ejecutado_dis===false)
	 { // error interno sql
			$this->io_mensajes->message("CLASE->sigesp_spi_class_reportes_instructivos ". 
			                            "MÉTODO->uf_spi_ejecutado_trimestral ".
										"ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		$lb_valido = false;
 	 }
	 elseif(!$rs_ejecutado_dis->EOF)
	 {
	  $ad_disminucion_acumulado = $rs_ejecutado_dis->fields["disminucion"];
	 }
	 
	 $this->io_sql->free_result($rs_ejecutado_cob);
	 $this->io_sql->free_result($rs_ejecutado_dev);
	 $this->io_sql->free_result($rs_ejecutado_aum);
	 $this->io_sql->free_result($rs_ejecutado_dis);
	  return $lb_valido;	
     }//fin uf_spi_ejecutado_trimestral
	//-----------------------------------------------------------------------------------------------------------------------------------
	
function uf_spi_reportes_ejecucion_trimestral($adt_fecdes,$adt_fechas,$as_codestpro1,$as_codestpro2,$as_codestpro3,
	 						$as_codestpro4,$as_codestpro5,$as_codestpro1h,$as_codestpro2h,$as_codestpro3h,
							$as_codestpro4h,$as_codestpro5h,$as_estclades,$as_estclahas)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	  Function :	uf_spg_reportes_ejecucion_trimestral
	 //         Access :	private
	 //     Argumentos : 
	 //                     $adt_fecdes  //  fecha desde 
	 //                     $adt_fechas  //  fecha hasta
	 //                     $ai_nivel    //  nivel 
	//	   Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //    Description :	Reporte que genera salida  de la Ejecucion Trimestral
	 //     Creado por :    Ing. Arnaldo Suárez
	 // Fecha Creación :    18/05/2008     				Modificado: 31/08/2009
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido = false;	 
		$ls_gestor = $_SESSION["ls_gestor"];
		$ls_codemp = $this->dts_empresa["codemp"]; 
		$li_mesdes=$adt_fecdes;
		$li_meshas=$adt_fechas;
		$li_trimestre = substr($li_mesdes,5,2);
		$li_trimestre = intval($li_trimestre);
		$li_estpreing = $_SESSION["la_empresa"]["estpreing"];
		$ls_sqlaux = $ls_straux = "";
		if ($li_estpreing==1)
		{
		      if (!empty($as_codestpro1) && !empty($as_codestpro2) && !empty($as_codestpro3) && !empty($as_codestpro1h)&&
			      !empty($as_codestpro2h) && !empty($as_codestpro3h))
			{
				$ls_straux      = ",spi_cuentas_estructuras";
				$ls_codestpro1  = str_pad($as_codestpro1,25,0,0);
				$ls_codestpro2  = str_pad($as_codestpro2,25,0,0);
				$ls_codestpro3  = str_pad($as_codestpro3,25,0,0);
				$ls_codestpro1h = str_pad($as_codestpro1h,25,0,0);
				$ls_codestpro2h = str_pad($as_codestpro2h,25,0,0);
				$ls_codestpro3h = str_pad($as_codestpro3h,25,0,0);
			}
			if (!empty($as_codestpro4)&&!empty($as_codestpro5)&&!empty($as_codestpro4h)&&!empty($as_codestpro5h))
			{
				$ls_codestpro4  = str_pad($as_codestpro4,25,0,0);
				$ls_codestpro5  = str_pad($as_codestpro5,25,0,0);
				$ls_codestpro4h = str_pad($as_codestpro4h,25,0,0);
				$ls_codestpro5h = str_pad($as_codestpro5h,25,0,0);					 
			}
			else
			{
				$ls_codestpro4=$ls_codestpro5=$ls_codestpro4h=$ls_codestpro5h = str_pad("",25,0,0);
			}
			if (!empty($as_codestpro1) && !empty($as_codestpro1h))
			{
				$la_codestpro_desde[0]=$ls_codestpro1;
				$la_codestpro_desde[1]=$ls_codestpro2;
				$la_codestpro_desde[2]=$ls_codestpro3;
				$la_codestpro_desde[3]=$ls_codestpro4;
				$la_codestpro_desde[4]=$ls_codestpro5;
				$la_codestpro_desde[5]=$as_estclades;
				
				$la_codestpro_hasta[0]=$ls_codestpro1h;
				$la_codestpro_hasta[1]=$ls_codestpro2h;
				$la_codestpro_hasta[2]=$ls_codestpro3h;
				$la_codestpro_hasta[3]=$ls_codestpro4h;
				$la_codestpro_hasta[4]=$ls_codestpro5h;
				$la_codestpro_hasta[5]=$as_estclahas;
				
				/*$ls_sqlaux = "  AND spi_cuentas_estructuras.estcla BETWEEN  '$as_estclades' AND '$as_estclahas'  
						AND spi_cuentas.spi_cuenta = spi_cuentas_estructuras.spi_cuenta 
						AND spi_cuentas_estructuras.codestpro1 BETWEEN '".$ls_codestpro1."' AND '".$ls_codestpro1h."'
						AND spi_cuentas_estructuras.codestpro2 BETWEEN '".$ls_codestpro2."' AND '".$ls_codestpro2h."'
						AND spi_cuentas_estructuras.codestpro3 BETWEEN '".$ls_codestpro3."' AND '".$ls_codestpro3h."'
						AND spi_cuentas_estructuras.codestpro4 BETWEEN '".$ls_codestpro4."' AND '".$ls_codestpro4h."'
						AND spi_cuentas_estructuras.codestpro5 BETWEEN '".$ls_codestpro5."' AND '".$ls_codestpro5h."'"; */
						
						switch($ls_gestor)
						{
						 case 'MYSQLT':   $ls_codestpro="CONCAT(spi_cuentas_estructuras.codestpro1,spi_cuentas_estructuras.codestpro2,spi_cuentas_estructuras.codestpro3,spi_cuentas_estructuras.codestpro4,spi_cuentas_estructuras.codestpro5,spi_cuentas_estructuras.estcla)"; 
										  break;
										
						 case 'POSTGRES': $ls_codestpro="spi_cuentas_estructuras.codestpro1||spi_cuentas_estructuras.codestpro2||spi_cuentas_estructuras.codestpro3||spi_cuentas_estructuras.codestpro4||spi_cuentas_estructuras.codestpro5||spi_cuentas_estructuras.estcla";
										  
										  break;
						
						}
		
		$ls_sqlaux = "AND $ls_codestpro BETWEEN '".$ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5.$as_estclades."' AND '".
		                                           $ls_codestpro1h.$ls_codestpro2h.$ls_codestpro3h.$ls_codestpro4h.$ls_codestpro5h.$as_estclahas."'";
			}
			
			/*$ls_sql = "SELECT spi_cuentas.spi_cuenta, max(spi_cuentas.nivel) as nivel, max(spi_cuentas.denominacion) as denominacion,
				        sum(spi_cuentas.previsto) as previsto, sum(spi_cuentas.enero) as enero, sum(spi_cuentas.febrero) as febrero,
				        sum(spi_cuentas.marzo) as marzo, sum(spi_cuentas.abril) as abril, sum(spi_cuentas.mayo) as mayo,
				        sum(spi_cuentas.junio) as junio, sum(spi_cuentas.julio) as julio, sum(spi_cuentas.agosto) as agosto,
				        sum(spi_cuentas.septiembre) as septiembre, sum(spi_cuentas.octubre) as octubre,
				        sum(spi_cuentas.noviembre) as noviembre, sum(spi_cuentas.diciembre) as diciembre, MAX(status) as status
			        FROM spi_cuentas $ls_straux
			        WHERE spi_cuentas.codemp='".$this->ls_codemp."' $ls_sqlaux
			        GROUP BY spi_cuentas.spi_cuenta
       			    ORDER BY spi_cuentas.spi_cuenta ASC";		*/
					
		$ls_sql="SELECT DISTINCT SUBSTR(spi_cuentas.spi_cuenta,1,9) as cuenta, spi_cuentas.denominacion, spi_cuentas.status  FROM spi_cuentas, (SELECT SUBSTR(spi_cuentas_estructuras.spi_cuenta,1,9) as cuenta
					FROM spi_cuentas, spi_cuentas_estructuras, spg_ep5 
					WHERE spi_cuentas_estructuras.spi_cuenta = spi_cuentas.spi_cuenta 
					AND spi_cuentas_estructuras.codestpro1 = spg_ep5.codestpro1 
					AND spi_cuentas_estructuras.codestpro2 = spg_ep5.codestpro2 
					AND spi_cuentas_estructuras.codestpro3 = spg_ep5.codestpro3 
					AND spi_cuentas_estructuras.codestpro4 = spg_ep5.codestpro4 
					AND spi_cuentas_estructuras.codestpro5 = spg_ep5.codestpro5 
					AND spi_cuentas_estructuras.codemp = spi_cuentas.codemp 
					AND spi_cuentas_estructuras.codemp = '".$this->ls_codemp."' 
					$ls_sqlaux GROUP BY 1
					ORDER BY 1) AS CUENTAESTRUCTURA
					WHERE SUBSTR(spi_cuentas.spi_cuenta,1,3) LIKE SUBSTR(CUENTAESTRUCTURA.cuenta,1,3) AND spi_cuentas.nivel = 1
					UNION
					SELECT DISTINCT SUBSTR(spi_cuentas.spi_cuenta,1,9) as cuenta, spi_cuentas.denominacion, spi_cuentas.status FROM spi_cuentas, (SELECT SUBSTR(spi_cuentas_estructuras.spi_cuenta,1,9) as cuenta
					FROM spi_cuentas, spi_cuentas_estructuras, spg_ep5 
					WHERE spi_cuentas_estructuras.spi_cuenta = spi_cuentas.spi_cuenta 
					AND spi_cuentas_estructuras.codestpro1 = spg_ep5.codestpro1 
					AND spi_cuentas_estructuras.codestpro2 = spg_ep5.codestpro2 
					AND spi_cuentas_estructuras.codestpro3 = spg_ep5.codestpro3 
					AND spi_cuentas_estructuras.codestpro4 = spg_ep5.codestpro4 
					AND spi_cuentas_estructuras.codestpro5 = spg_ep5.codestpro5 
					AND spi_cuentas_estructuras.codemp = spi_cuentas.codemp 
					AND spi_cuentas_estructuras.codemp = '".$this->ls_codemp."' 
					$ls_sqlaux GROUP BY 1
					ORDER BY 1) AS CUENTAESTRUCTURA
					WHERE SUBSTR(spi_cuentas.spi_cuenta,1,5) LIKE SUBSTR(CUENTAESTRUCTURA.cuenta,1,5) AND spi_cuentas.nivel = 2
					UNION
					SELECT DISTINCT SUBSTR(spi_cuentas.spi_cuenta,1,9) as cuenta, spi_cuentas.denominacion, spi_cuentas.status FROM spi_cuentas, (SELECT SUBSTR(spi_cuentas_estructuras.spi_cuenta,1,9) as cuenta
					FROM spi_cuentas, spi_cuentas_estructuras, spg_ep5 
					WHERE spi_cuentas_estructuras.spi_cuenta = spi_cuentas.spi_cuenta 
					AND spi_cuentas_estructuras.codestpro1 = spg_ep5.codestpro1 
					AND spi_cuentas_estructuras.codestpro2 = spg_ep5.codestpro2 
					AND spi_cuentas_estructuras.codestpro3 = spg_ep5.codestpro3 
					AND spi_cuentas_estructuras.codestpro4 = spg_ep5.codestpro4 
					AND spi_cuentas_estructuras.codestpro5 = spg_ep5.codestpro5 
					AND spi_cuentas_estructuras.codemp = spi_cuentas.codemp 
					AND spi_cuentas_estructuras.codemp = '".$this->ls_codemp."' 
					$ls_sqlaux GROUP BY 1
					ORDER BY 1) AS CUENTAESTRUCTURA
					WHERE SUBSTR(spi_cuentas.spi_cuenta,1,7) LIKE SUBSTR(CUENTAESTRUCTURA.cuenta,1,7) AND spi_cuentas.nivel = 3
					AND SUBSTR(spi_cuentas.spi_cuenta,1,9) NOT IN (SELECT SUBSTR(spi_cuentas_estructuras.spi_cuenta,1,9)
					FROM spi_cuentas, spi_cuentas_estructuras, spg_ep5 
					WHERE spi_cuentas_estructuras.spi_cuenta = spi_cuentas.spi_cuenta 
					AND spi_cuentas_estructuras.codestpro1 = spg_ep5.codestpro1 
					AND spi_cuentas_estructuras.codestpro2 = spg_ep5.codestpro2 
					AND spi_cuentas_estructuras.codestpro3 = spg_ep5.codestpro3 
					AND spi_cuentas_estructuras.codestpro4 = spg_ep5.codestpro4 
					AND spi_cuentas_estructuras.codestpro5 = spg_ep5.codestpro5 
					AND spi_cuentas_estructuras.codemp = spi_cuentas.codemp 
					AND spi_cuentas_estructuras.codemp = '".$this->ls_codemp."' 
					$ls_sqlaux 
					GROUP BY 1)
					UNION
					SELECT SUBSTR(spi_cuentas_estructuras.spi_cuenta,1,9) as cuenta, MAX(spi_cuentas.denominacion) as denominacion, 'C' as status
					FROM spi_cuentas, spi_cuentas_estructuras, spg_ep5 
					WHERE spi_cuentas_estructuras.spi_cuenta = spi_cuentas.spi_cuenta
					AND spi_cuentas_estructuras.codestpro1 = spg_ep5.codestpro1 
					AND spi_cuentas_estructuras.codestpro2 = spg_ep5.codestpro2 
					AND spi_cuentas_estructuras.codestpro3 = spg_ep5.codestpro3 
					AND spi_cuentas_estructuras.codestpro4 = spg_ep5.codestpro4 
					AND spi_cuentas_estructuras.codestpro5 = spg_ep5.codestpro5 
					AND spi_cuentas_estructuras.codemp = spi_cuentas.codemp 
					AND spi_cuentas_estructuras.codemp = '".$this->ls_codemp."'
					$ls_sqlaux 
					GROUP BY 1
					ORDER BY 1";			
					
		//echo $ls_sql."<br>";						  			
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{   // error interno sql
			$this->is_msg_error="Error en consulta metodo uf_spi_reportes_ejecucion_trimestral".$this->fun->uf_convertirmsg($this->io_sql->message);
			$lb_valido = false;
		}
		else
		{
			while(!$rs_data->EOF)
			{
			   $lb_ok=false; 
			   $ls_spi_cuenta      =$rs_data->fields["cuenta"];
			   $ls_denominacion    =$rs_data->fields["denominacion"];
			   $ls_status          =$rs_data->fields["status"];
			   $ld_programado      = 0;
			   $ld_programado_acum = 0;
			   $ld_aumdisacum    = 0;
			   $ld_pretriact     = 0; 
			   $ld_devtriact     = 0;
			   $ld_cobtriact     = 0;
			   $ld_restriact     = 0;
			   $ld_preacum       = 0; 
			   $ld_devacum       = 0;
			   $ld_cobacum       = 0;
			   $ld_resacum       = 0;
			   
			    $lb_valido=$this->uf_spi_calcular_programado_trimestre($ls_spi_cuenta,$la_codestpro_desde,$la_codestpro_hasta,$li_trimestre,$ld_programado,$ld_programado_acum);
				if($lb_valido)
				{
			     		  
				$lb_valido=$this->uf_spi_reporte_calcular_ejecutado_trimestre_estructura($ls_spi_cuenta,$la_codestpro_desde,$la_codestpro_hasta,$li_mesdes,$li_meshas,$ld_aumdisacum,
			                                                                 $ld_pretriact,$ld_devtriact,$ld_cobtriact,
			                                                                 $ld_restriact,$ld_preacum,$ld_devacum,$ld_cobacum
																			,$ld_resacum);
				}															
			   if($lb_valido)
			   {
					 $ld_ingresos_recibir=$ld_preacum+$ld_aumdisacum-$ld_devacum;
					 $ld_modificado = $ld_preacum + $ld_aumdisacum;
					 $this->dts_reporte->insertRow("spi_cuenta",$ls_spi_cuenta);
					 $this->dts_reporte->insertRow("denominacion",$ls_denominacion);
					 $this->dts_reporte->insertRow("previsto",$ld_preacum);
					 $this->dts_reporte->insertRow("modificado",$ld_modificado);
					 $this->dts_reporte->insertRow("programado",$ld_programado);
					 $this->dts_reporte->insertRow("devengado",$ld_devtriact);
					 $this->dts_reporte->insertRow("liquidado",$ld_devtriact);
					 $this->dts_reporte->insertRow("recaudado",$ld_cobtriact);
					 $this->dts_reporte->insertRow("programado_acum",$ld_programado_acum);
					 $this->dts_reporte->insertRow("devengado_acum",$ld_devacum);					 
					 $this->dts_reporte->insertRow("liquidado_acum",$ld_devacum);					 
					 $this->dts_reporte->insertRow("recaudado_acum",$ld_cobacum);
					 $this->dts_reporte->insertRow("ingresos_recibir",$ld_ingresos_recibir);
					 $this->dts_reporte->insertRow("status",$ls_status);
					 $lb_valido=true;
			    }//if
				$rs_data->MoveNext();
			 }//while
			 $this->io_sql->free_result($rs_data);
		 } //else 			
					
					
					
		}
		else
		{
		
		$ls_sql = "SELECT spi_cuentas.spi_cuenta, max(spi_cuentas.nivel) as nivel, max(spi_cuentas.denominacion) as denominacion,
				        sum(spi_cuentas.previsto) as previsto, sum(spi_cuentas.enero) as enero, sum(spi_cuentas.febrero) as febrero,
				        sum(spi_cuentas.marzo) as marzo, sum(spi_cuentas.abril) as abril, sum(spi_cuentas.mayo) as mayo,
				        sum(spi_cuentas.junio) as junio, sum(spi_cuentas.julio) as julio, sum(spi_cuentas.agosto) as agosto,
				        sum(spi_cuentas.septiembre) as septiembre, sum(spi_cuentas.octubre) as octubre,
				        sum(spi_cuentas.noviembre) as noviembre, sum(spi_cuentas.diciembre) as diciembre, MAX(status) as status
			        FROM spi_cuentas
			        WHERE spi_cuentas.codemp='".$this->ls_codemp."'
			        GROUP BY spi_cuentas.spi_cuenta
       			    ORDER BY spi_cuentas.spi_cuenta ASC";
					
		$rs_data=$this->io_sql->select($ls_sql);
		//echo $ls_sql.'<br>';
		//var_dump($rs_data);
		if($rs_data===false)
		{   // error interno sql
			$this->is_msg_error="Error en consulta metodo uf_spi_reportes_ejecucion_trimestral".$this->fun->uf_convertirmsg($this->io_sql->message);
			$lb_valido = false;
		}
		else
		{
			while(!$rs_data->EOF)
			{
			   $lb_ok=false; 
			   $ls_spi_cuenta=$rs_data->fields["spi_cuenta"];
			   $ls_denominacion=$rs_data->fields["denominacion"];
			   $li_nivel=$rs_data->fields["nivel"];
			   $ld_previsto=$rs_data->fields["previsto"];
			   $ld_enero=$rs_data->fields["enero"];
			   $ld_febrero=$rs_data->fields["febrero"];
			   $ld_marzo=$rs_data->fields["marzo"];
			   $ld_abril=$rs_data->fields["abril"];
			   $ld_mayo=$rs_data->fields["mayo"];
			   $ld_junio=$rs_data->fields["junio"];
			   $ld_julio=$rs_data->fields["julio"];
			   $ld_agosto=$rs_data->fields["agosto"];
			   $ld_septiembre=$rs_data->fields["septiembre"];
			   $ld_octubre=$rs_data->fields["octubre"];
			   $ld_noviembre=$rs_data->fields["noviembre"];
			   $ld_diciembre=$rs_data->fields["diciembre"];
			   $ls_status=$rs_data->fields["status"];
			   $ld_prog_tri_i    = 0;
			   $ld_prog_tri_ii   = 0;
			   $ld_prog_tri_iii  = 0;
			   $ld_prog_tri_iv   = 0;
			   $ld_aumdisacum    = 0;
			   $ld_pretriact     = 0; 
			   $ld_devtriact     = 0;
			   $ld_cobtriact     = 0;
			   $ld_restriact     = 0;
			   $ld_preacum       = 0; 
			   $ld_devacum       = 0;
			   $ld_cobacum       = 0;
			   $ld_resacum       = 0;

			   switch ($li_trimestre)
			   {
				case 1:
					$ld_programado      =  $ld_enero + $ld_febrero + $ld_marzo;
					$ld_programado_acum =  $ld_programado;
				break;
				
				case 4:
					$ld_programado      =  $ld_abril + $ld_mayo + $ld_junio;
					$ld_programado_acum =  $ld_enero + $ld_febrero + $ld_marzo + $ld_programado;
				break;
				
				case 7:
					$ld_programado      =  $ld_julio + $ld_agosto + $ld_septiembre;
					$ld_programado_acum =  $ld_enero + $ld_febrero + $ld_marzo + $ld_abril + $ld_mayo + $ld_junio +
					$ld_programado;
				break;	  	  
				
				case 10:
					$ld_programado      =  $ld_octubre + $ld_noviembre + $ld_diciembre;
					$ld_programado_acum =  $ld_enero + $ld_febrero + $ld_marzo +  $ld_abril + $ld_mayo + $ld_junio + $ld_julio +                                             $ld_agosto + $ld_septiembre + $ld_programado;
				break;
			   }	  		  
		$lb_valido=$this->uf_spi_reporte_calcular_ejecutado_trimestre($ls_spi_cuenta,$li_mesdes,$li_meshas,$ld_aumdisacum,
			                                                                 $ld_pretriact,$ld_devtriact,$ld_cobtriact,
			                                                                 $ld_restriact,$ld_preacum,$ld_devacum,$ld_cobacum
																			,$ld_resacum);
			   if($lb_valido)
			   {
					 $ld_ingresos_recibir=$ld_previsto+$ld_aumdisacum-$ld_devacum;
					 $ld_modificado = $ld_previsto + $ld_aumdisacum;
					 $this->dts_reporte->insertRow("spi_cuenta",$ls_spi_cuenta);
					 $this->dts_reporte->insertRow("denominacion",$ls_denominacion);
					 $this->dts_reporte->insertRow("previsto",$ld_previsto);
					 $this->dts_reporte->insertRow("modificado",$ld_modificado);
					 $this->dts_reporte->insertRow("programado",$ld_programado);
					 $this->dts_reporte->insertRow("devengado",$ld_devtriact);
					 $this->dts_reporte->insertRow("liquidado",$ld_cobtriact);
					 $this->dts_reporte->insertRow("recaudado",0);
					 $this->dts_reporte->insertRow("programado_acum",$ld_programado_acum);
					 $this->dts_reporte->insertRow("devengado_acum",$ld_devacum);					 
					 $this->dts_reporte->insertRow("liquidado_acum",$ld_cobacum);					 
					 $this->dts_reporte->insertRow("recaudado_acum",0);
					 $this->dts_reporte->insertRow("ingresos_recibir",$ld_ingresos_recibir);
					 $this->dts_reporte->insertRow("status",$ls_status);
					 $lb_valido=true;
			    }//if
				$rs_data->MoveNext();
			 }//while
			 $this->io_sql->free_result($rs_data);
		 } //else 
		 
	  } // else del Tipo de Ingreso 
     return $lb_valido;
    }//fin uf_spg_reportes_ejecucion_trimestral
/********************************************************************************************************************************/	

function uf_spi_reportes_ejecucion_trimestral_excel($adt_fecdes,$adt_fechas,$as_codestpro1,$as_codestpro2,$as_codestpro3,
	 						$as_codestpro4,$as_codestpro5,$as_codestpro1h,$as_codestpro2h,$as_codestpro3h,
							$as_codestpro4h,$as_codestpro5h,$as_estclades,$as_estclahas)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	  Function :	uf_spg_reportes_ejecucion_trimestral_excel
	 //         Access :	private
	 //     Argumentos : 
	 //                     $adt_fecdes  //  fecha desde 
	 //                     $adt_fechas  //  fecha hasta
	 //                     $ai_nivel    //  nivel 
	//	   Returns :	Retorna true o false si se realizo la consulta para el reporte en excel
	 //    Description :	Reporte que genera salida  de la Ejecucion Trimestral
	 //     Creado por :    Ing. Arnaldo Suárez
	 // Fecha Creación :    16/09/2008     				Modificado: 31/08/2009
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido = false;	 
		$ls_gestor = $_SESSION["ls_gestor"];
		$ls_codemp = $this->dts_empresa["codemp"]; 
		$li_mesdes=$adt_fecdes;
		$li_meshas=$adt_fechas;
		$li_trimestre = substr($li_mesdes,5,2);
		$li_trimestre = intval($li_trimestre);
		$li_estpreing = $_SESSION["la_empresa"]["estpreing"];
		$ls_sqlaux = $ls_straux = "";
		if ($li_estpreing==1)
		{
		      if (!empty($as_codestpro1) && !empty($as_codestpro2) && !empty($as_codestpro3) && !empty($as_codestpro1h)&&
			      !empty($as_codestpro2h) && !empty($as_codestpro3h))
			{
				$ls_straux      = ",spi_cuentas_estructuras";
				$ls_codestpro1  = str_pad($as_codestpro1,25,0,0);
				$ls_codestpro2  = str_pad($as_codestpro2,25,0,0);
				$ls_codestpro3  = str_pad($as_codestpro3,25,0,0);
				$ls_codestpro1h = str_pad($as_codestpro1h,25,0,0);
				$ls_codestpro2h = str_pad($as_codestpro2h,25,0,0);
				$ls_codestpro3h = str_pad($as_codestpro3h,25,0,0);
			}
			if (!empty($as_codestpro4)&&!empty($as_codestpro5)&&!empty($as_codestpro4h)&&!empty($as_codestpro5h))
			{
				$ls_codestpro4  = str_pad($as_codestpro4,25,0,0);
				$ls_codestpro5  = str_pad($as_codestpro5,25,0,0);
				$ls_codestpro4h = str_pad($as_codestpro4h,25,0,0);
				$ls_codestpro5h = str_pad($as_codestpro5h,25,0,0);					 
			}
			else
			{
				$ls_codestpro4=$ls_codestpro5=$ls_codestpro4h=$ls_codestpro5h = str_pad("",25,0,0);
			}
			if (!empty($as_codestpro1) && !empty($as_codestpro1h))
			{
				$la_codestpro_desde[0]=$ls_codestpro1;
				$la_codestpro_desde[1]=$ls_codestpro2;
				$la_codestpro_desde[2]=$ls_codestpro3;
				$la_codestpro_desde[3]=$ls_codestpro4;
				$la_codestpro_desde[4]=$ls_codestpro5;
				$la_codestpro_desde[5]=$as_estclades;
				
				$la_codestpro_hasta[0]=$ls_codestpro1h;
				$la_codestpro_hasta[1]=$ls_codestpro2h;
				$la_codestpro_hasta[2]=$ls_codestpro3h;
				$la_codestpro_hasta[3]=$ls_codestpro4h;
				$la_codestpro_hasta[4]=$ls_codestpro5h;
				$la_codestpro_hasta[5]=$as_estclahas;
						
						switch($ls_gestor)
						{
						 case 'MYSQLT':   $ls_codestpro="CONCAT(spi_cuentas_estructuras.codestpro1,spi_cuentas_estructuras.codestpro2,spi_cuentas_estructuras.codestpro3,spi_cuentas_estructuras.codestpro4,spi_cuentas_estructuras.codestpro5,spi_cuentas_estructuras.estcla)"; 
										  break;
										
						 case 'POSTGRES': $ls_codestpro="spi_cuentas_estructuras.codestpro1||spi_cuentas_estructuras.codestpro2||spi_cuentas_estructuras.codestpro3||spi_cuentas_estructuras.codestpro4||spi_cuentas_estructuras.codestpro5||spi_cuentas_estructuras.estcla";
										  
										  break;
						
						}
		
		$ls_sqlaux = "AND $ls_codestpro BETWEEN '".$ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5.$as_estclades."' AND '".
		                                           $ls_codestpro1h.$ls_codestpro2h.$ls_codestpro3h.$ls_codestpro4h.$ls_codestpro5h.$as_estclahas."'";
			}
			
					
		$ls_sql="SELECT DISTINCT spi_cuentas.spi_cuenta as cuenta, spi_cuentas.denominacion, spi_cuentas.status  FROM spi_cuentas, (SELECT SUBSTR(spi_cuentas_estructuras.spi_cuenta,1,9) as cuenta
					FROM spi_cuentas, spi_cuentas_estructuras, spg_ep5 
					WHERE spi_cuentas_estructuras.spi_cuenta = spi_cuentas.spi_cuenta 
					AND spi_cuentas_estructuras.codestpro1 = spg_ep5.codestpro1 
					AND spi_cuentas_estructuras.codestpro2 = spg_ep5.codestpro2 
					AND spi_cuentas_estructuras.codestpro3 = spg_ep5.codestpro3 
					AND spi_cuentas_estructuras.codestpro4 = spg_ep5.codestpro4 
					AND spi_cuentas_estructuras.codestpro5 = spg_ep5.codestpro5 
					AND spi_cuentas_estructuras.codemp = spi_cuentas.codemp 
					AND spi_cuentas_estructuras.codemp = '".$this->ls_codemp."' 
					$ls_sqlaux GROUP BY 1
					ORDER BY 1) AS CUENTAESTRUCTURA
					WHERE SUBSTR(spi_cuentas.spi_cuenta,1,3) LIKE SUBSTR(CUENTAESTRUCTURA.cuenta,1,3) AND spi_cuentas.nivel = 1
					UNION
					SELECT DISTINCT spi_cuentas.spi_cuenta as cuenta, spi_cuentas.denominacion, spi_cuentas.status FROM spi_cuentas, (SELECT spi_cuentas_estructuras.spi_cuenta as cuenta
					FROM spi_cuentas, spi_cuentas_estructuras, spg_ep5 
					WHERE spi_cuentas_estructuras.spi_cuenta = spi_cuentas.spi_cuenta 
					AND spi_cuentas_estructuras.codestpro1 = spg_ep5.codestpro1 
					AND spi_cuentas_estructuras.codestpro2 = spg_ep5.codestpro2 
					AND spi_cuentas_estructuras.codestpro3 = spg_ep5.codestpro3 
					AND spi_cuentas_estructuras.codestpro4 = spg_ep5.codestpro4 
					AND spi_cuentas_estructuras.codestpro5 = spg_ep5.codestpro5 
					AND spi_cuentas_estructuras.codemp = spi_cuentas.codemp 
					AND spi_cuentas_estructuras.codemp = '".$this->ls_codemp."' 
					$ls_sqlaux GROUP BY 1
					ORDER BY 1) AS CUENTAESTRUCTURA
					WHERE SUBSTR(spi_cuentas.spi_cuenta,1,5) LIKE SUBSTR(CUENTAESTRUCTURA.cuenta,1,5) AND spi_cuentas.nivel = 2
					UNION
					SELECT DISTINCT spi_cuentas.spi_cuenta as cuenta, spi_cuentas.denominacion, spi_cuentas.status FROM spi_cuentas, (SELECT spi_cuentas_estructuras.spi_cuenta as cuenta
					FROM spi_cuentas, spi_cuentas_estructuras, spg_ep5 
					WHERE spi_cuentas_estructuras.spi_cuenta = spi_cuentas.spi_cuenta 
					AND spi_cuentas_estructuras.codestpro1 = spg_ep5.codestpro1 
					AND spi_cuentas_estructuras.codestpro2 = spg_ep5.codestpro2 
					AND spi_cuentas_estructuras.codestpro3 = spg_ep5.codestpro3 
					AND spi_cuentas_estructuras.codestpro4 = spg_ep5.codestpro4 
					AND spi_cuentas_estructuras.codestpro5 = spg_ep5.codestpro5 
					AND spi_cuentas_estructuras.codemp = spi_cuentas.codemp 
					AND spi_cuentas_estructuras.codemp = '".$this->ls_codemp."' 
					$ls_sqlaux GROUP BY 1
					ORDER BY 1) AS CUENTAESTRUCTURA
					WHERE SUBSTR(spi_cuentas.spi_cuenta,1,7) LIKE SUBSTR(CUENTAESTRUCTURA.cuenta,1,7) AND spi_cuentas.nivel = 3
					AND spi_cuentas.spi_cuenta NOT IN (SELECT spi_cuentas_estructuras.spi_cuenta
					FROM spi_cuentas, spi_cuentas_estructuras, spg_ep5 
					WHERE spi_cuentas_estructuras.spi_cuenta = spi_cuentas.spi_cuenta 
					AND spi_cuentas_estructuras.codestpro1 = spg_ep5.codestpro1 
					AND spi_cuentas_estructuras.codestpro2 = spg_ep5.codestpro2 
					AND spi_cuentas_estructuras.codestpro3 = spg_ep5.codestpro3 
					AND spi_cuentas_estructuras.codestpro4 = spg_ep5.codestpro4 
					AND spi_cuentas_estructuras.codestpro5 = spg_ep5.codestpro5 
					AND spi_cuentas_estructuras.codemp = spi_cuentas.codemp 
					AND spi_cuentas_estructuras.codemp = '".$this->ls_codemp."' 
					$ls_sqlaux 
					GROUP BY 1)
					UNION
					SELECT DISTINCT spi_cuentas.spi_cuenta as cuenta, spi_cuentas.denominacion, spi_cuentas.status FROM spi_cuentas, (SELECT spi_cuentas_estructuras.spi_cuenta as cuenta
					FROM spi_cuentas, spi_cuentas_estructuras, spg_ep5 
					WHERE spi_cuentas_estructuras.spi_cuenta = spi_cuentas.spi_cuenta 
					AND spi_cuentas_estructuras.codestpro1 = spg_ep5.codestpro1 
					AND spi_cuentas_estructuras.codestpro2 = spg_ep5.codestpro2 
					AND spi_cuentas_estructuras.codestpro3 = spg_ep5.codestpro3 
					AND spi_cuentas_estructuras.codestpro4 = spg_ep5.codestpro4 
					AND spi_cuentas_estructuras.codestpro5 = spg_ep5.codestpro5 
					AND spi_cuentas_estructuras.codemp = spi_cuentas.codemp 
					AND spi_cuentas_estructuras.codemp = '".$this->ls_codemp."' 
					$ls_sqlaux GROUP BY 1
					ORDER BY 1) AS CUENTAESTRUCTURA
					WHERE SUBSTR(spi_cuentas.spi_cuenta,1,9) LIKE SUBSTR(CUENTAESTRUCTURA.cuenta,1,9) AND spi_cuentas.nivel = 4
					AND spi_cuentas.spi_cuenta NOT IN (SELECT spi_cuentas_estructuras.spi_cuenta
					FROM spi_cuentas, spi_cuentas_estructuras, spg_ep5 
					WHERE spi_cuentas_estructuras.spi_cuenta = spi_cuentas.spi_cuenta 
					AND spi_cuentas_estructuras.codestpro1 = spg_ep5.codestpro1 
					AND spi_cuentas_estructuras.codestpro2 = spg_ep5.codestpro2 
					AND spi_cuentas_estructuras.codestpro3 = spg_ep5.codestpro3 
					AND spi_cuentas_estructuras.codestpro4 = spg_ep5.codestpro4 
					AND spi_cuentas_estructuras.codestpro5 = spg_ep5.codestpro5 
					AND spi_cuentas_estructuras.codemp = spi_cuentas.codemp 
					AND spi_cuentas_estructuras.codemp = '".$this->ls_codemp."' 
					$ls_sqlaux 
					GROUP BY 1)
					UNION
					SELECT spi_cuentas_estructuras.spi_cuenta as cuenta, MAX(spi_cuentas.denominacion) as denominacion, MAX(spi_cuentas.status) as status
					FROM spi_cuentas, spi_cuentas_estructuras, spg_ep5 
					WHERE spi_cuentas_estructuras.spi_cuenta = spi_cuentas.spi_cuenta
					AND spi_cuentas_estructuras.codestpro1 = spg_ep5.codestpro1 
					AND spi_cuentas_estructuras.codestpro2 = spg_ep5.codestpro2 
					AND spi_cuentas_estructuras.codestpro3 = spg_ep5.codestpro3 
					AND spi_cuentas_estructuras.codestpro4 = spg_ep5.codestpro4 
					AND spi_cuentas_estructuras.codestpro5 = spg_ep5.codestpro5 
					AND spi_cuentas_estructuras.codemp = spi_cuentas.codemp 
					AND spi_cuentas_estructuras.codemp = '".$this->ls_codemp."'
					$ls_sqlaux 
					GROUP BY 1
					ORDER BY 1";			
					
		//echo $ls_sql."<br>";						  			
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{   // error interno sql
			$this->is_msg_error="Error en consulta metodo uf_spi_reportes_ejecucion_trimestral".$this->fun->uf_convertirmsg($this->io_sql->message);
			$lb_valido = false;
		}
		else
		{
			while(!$rs_data->EOF)
			{
			   $lb_ok=false; 
			   $ls_spi_cuenta      =$rs_data->fields["cuenta"];
			   $ls_denominacion    =$rs_data->fields["denominacion"];
			   $ls_status          =$rs_data->fields["status"];
			   $ld_programado      = 0;
			   $ld_programado_acum = 0;
			   $ld_aumdisacum    = 0;
			   $ld_pretriact     = 0; 
			   $ld_devtriact     = 0;
			   $ld_cobtriact     = 0;
			   $ld_restriact     = 0;
			   $ld_preacum       = 0; 
			   $ld_devacum       = 0;
			   $ld_cobacum       = 0;
			   $ld_resacum       = 0;
			   
			    $lb_valido=$this->uf_spi_calcular_programado_trimestre($ls_spi_cuenta,$la_codestpro_desde,$la_codestpro_hasta,$li_trimestre,$ld_programado,$ld_programado_acum);
				if($lb_valido)
				{
			     		  
				$lb_valido=$this->uf_spi_reporte_calcular_ejecutado_trimestre_estructura($ls_spi_cuenta,$la_codestpro_desde,$la_codestpro_hasta,$li_mesdes,$li_meshas,$ld_aumdisacum,
			                                                                 $ld_pretriact,$ld_devtriact,$ld_cobtriact,
			                                                                 $ld_restriact,$ld_preacum,$ld_devacum,$ld_cobacum
																			,$ld_resacum);
				}															
			   if($lb_valido)
			   {
					 $ld_ingresos_recibir=$ld_preacum+$ld_aumdisacum-$ld_devacum;
					 $ld_modificado = $ld_preacum + $ld_aumdisacum;
					 $this->dts_reporte->insertRow("spi_cuenta",$ls_spi_cuenta);
					 $this->dts_reporte->insertRow("denominacion",$ls_denominacion);
					 $this->dts_reporte->insertRow("previsto",$ld_preacum);
					 $this->dts_reporte->insertRow("modificado",$ld_modificado);
					 $this->dts_reporte->insertRow("programado",$ld_programado);
					 $this->dts_reporte->insertRow("devengado",$ld_devtriact);
					 $this->dts_reporte->insertRow("liquidado",$ld_devtriact);
					 $this->dts_reporte->insertRow("recaudado",$ld_cobtriact);
					 $this->dts_reporte->insertRow("programado_acum",$ld_programado_acum);
					 $this->dts_reporte->insertRow("devengado_acum",$ld_devacum);					 
					 $this->dts_reporte->insertRow("liquidado_acum",$ld_devacum);					 
					 $this->dts_reporte->insertRow("recaudado_acum",$ld_cobacum);
					 $this->dts_reporte->insertRow("ingresos_recibir",$ld_ingresos_recibir);
					 $this->dts_reporte->insertRow("status",$ls_status);
					 $lb_valido=true;
			    }//if
				$rs_data->MoveNext();
			 }//while
			 $this->io_sql->free_result($rs_data);
		 } //else 			
					
					
					
		}
		else
		{
		
		$ls_sql = "SELECT spi_cuentas.spi_cuenta, max(spi_cuentas.nivel) as nivel, max(spi_cuentas.denominacion) as denominacion,
				        sum(spi_cuentas.previsto) as previsto, sum(spi_cuentas.enero) as enero, sum(spi_cuentas.febrero) as febrero,
				        sum(spi_cuentas.marzo) as marzo, sum(spi_cuentas.abril) as abril, sum(spi_cuentas.mayo) as mayo,
				        sum(spi_cuentas.junio) as junio, sum(spi_cuentas.julio) as julio, sum(spi_cuentas.agosto) as agosto,
				        sum(spi_cuentas.septiembre) as septiembre, sum(spi_cuentas.octubre) as octubre,
				        sum(spi_cuentas.noviembre) as noviembre, sum(spi_cuentas.diciembre) as diciembre, MAX(status) as status
			        FROM spi_cuentas
			        WHERE spi_cuentas.codemp='".$this->ls_codemp."'
			        GROUP BY spi_cuentas.spi_cuenta
       			    ORDER BY spi_cuentas.spi_cuenta ASC";
					
		$rs_data=$this->io_sql->select($ls_sql);
		//echo $ls_sql.'<br>';
		//var_dump($rs_data);
		if($rs_data===false)
		{   // error interno sql
			$this->is_msg_error="Error en consulta metodo uf_spi_reportes_ejecucion_trimestral".$this->fun->uf_convertirmsg($this->io_sql->message);
			$lb_valido = false;
		}
		else
		{
			while(!$rs_data->EOF)
			{
			   $lb_ok=false; 
			   $ls_spi_cuenta=$rs_data->fields["spi_cuenta"];
			   $ls_denominacion=$rs_data->fields["denominacion"];
			   $li_nivel=$rs_data->fields["nivel"];
			   $ld_previsto=$rs_data->fields["previsto"];
			   $ld_enero=$rs_data->fields["enero"];
			   $ld_febrero=$rs_data->fields["febrero"];
			   $ld_marzo=$rs_data->fields["marzo"];
			   $ld_abril=$rs_data->fields["abril"];
			   $ld_mayo=$rs_data->fields["mayo"];
			   $ld_junio=$rs_data->fields["junio"];
			   $ld_julio=$rs_data->fields["julio"];
			   $ld_agosto=$rs_data->fields["agosto"];
			   $ld_septiembre=$rs_data->fields["septiembre"];
			   $ld_octubre=$rs_data->fields["octubre"];
			   $ld_noviembre=$rs_data->fields["noviembre"];
			   $ld_diciembre=$rs_data->fields["diciembre"];
			   $ls_status=$rs_data->fields["status"];
			   $ld_prog_tri_i    = 0;
			   $ld_prog_tri_ii   = 0;
			   $ld_prog_tri_iii  = 0;
			   $ld_prog_tri_iv   = 0;
			   $ld_aumdisacum    = 0;
			   $ld_pretriact     = 0; 
			   $ld_devtriact     = 0;
			   $ld_cobtriact     = 0;
			   $ld_restriact     = 0;
			   $ld_preacum       = 0; 
			   $ld_devacum       = 0;
			   $ld_cobacum       = 0;
			   $ld_resacum       = 0;

			   switch ($li_trimestre)
			   {
				case 1:
					$ld_programado      =  $ld_enero + $ld_febrero + $ld_marzo;
					$ld_programado_acum =  $ld_programado;
				break;
				
				case 4:
					$ld_programado      =  $ld_abril + $ld_mayo + $ld_junio;
					$ld_programado_acum =  $ld_enero + $ld_febrero + $ld_marzo + $ld_programado;
				break;
				
				case 7:
					$ld_programado      =  $ld_julio + $ld_agosto + $ld_septiembre;
					$ld_programado_acum =  $ld_enero + $ld_febrero + $ld_marzo + $ld_abril + $ld_mayo + $ld_junio +
					$ld_programado;
				break;	  	  
				
				case 10:
					$ld_programado      =  $ld_octubre + $ld_noviembre + $ld_diciembre;
					$ld_programado_acum =  $ld_enero + $ld_febrero + $ld_marzo +  $ld_abril + $ld_mayo + $ld_junio + $ld_julio +                                             $ld_agosto + $ld_septiembre + $ld_programado;
				break;
			   }	  		  
		$lb_valido=$this->uf_spi_reporte_calcular_ejecutado_trimestre($ls_spi_cuenta,$li_mesdes,$li_meshas,$ld_aumdisacum,
			                                                                 $ld_pretriact,$ld_devtriact,$ld_cobtriact,
			                                                                 $ld_restriact,$ld_preacum,$ld_devacum,$ld_cobacum
																			,$ld_resacum);
			   if($lb_valido)
			   {
					 $ld_ingresos_recibir=$ld_previsto+$ld_aumdisacum-$ld_devacum;
					 $ld_modificado = $ld_previsto + $ld_aumdisacum;
					 $this->dts_reporte->insertRow("spi_cuenta",$ls_spi_cuenta);
					 $this->dts_reporte->insertRow("denominacion",$ls_denominacion);
					 $this->dts_reporte->insertRow("previsto",$ld_previsto);
					 $this->dts_reporte->insertRow("modificado",$ld_modificado);
					 $this->dts_reporte->insertRow("programado",$ld_programado);
					 $this->dts_reporte->insertRow("devengado",$ld_devtriact);
					 $this->dts_reporte->insertRow("liquidado",$ld_cobtriact);
					 $this->dts_reporte->insertRow("recaudado",0);
					 $this->dts_reporte->insertRow("programado_acum",$ld_programado_acum);
					 $this->dts_reporte->insertRow("devengado_acum",$ld_devacum);					 
					 $this->dts_reporte->insertRow("liquidado_acum",$ld_cobacum);					 
					 $this->dts_reporte->insertRow("recaudado_acum",0);
					 $this->dts_reporte->insertRow("ingresos_recibir",$ld_ingresos_recibir);
					 $this->dts_reporte->insertRow("status",$ls_status);
					 $lb_valido=true;
			    }//if
				$rs_data->MoveNext();
			 }//while
			 $this->io_sql->free_result($rs_data);
		 } //else 
		 
	  } // else del Tipo de Ingreso 
     return $lb_valido;
    }//fin uf_spg_reportes_ejecucion_trimestral
/********************************************************************************************************************************/	     
    
function uf_spi_reporte_calcular_ejecutado_trimestre($as_spi_cuenta,$ai_mesdes,$ai_meshas,&$ad_aumdisacum,&$ad_pretriact,&
                                                     $ad_devtriact,&$ad_cobtriact,&$ad_restriact,&$ad_preacum,&
                                                     $ad_devacum,&$ad_cobacum,&$ad_resacum)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spi_reporte_calcular_ejecutado_trimestre
	 //         Access :	private
	 //     Argumentos :    $as_spi_cuenta  // cuenta
	 //                     $as_mesdes  // mes  desde
     //              	    $as_meshas  // mes hasta
	 //                     $ad_monto_ejecutado // monto ejecutado (referencia)  
	 //                     $ad_monto_acumulado // monto acumulado (referencia) 
	 //                     $ad_aumdismes // monto aumneto y disminuciones del mes (referencia)  
	 //                     $ad_aumdisacum // monto aumneto y disminuciones del mes acumulado (referencia)  
	 //                     $ad_comprometer // monto comprometer (referencia)  
	 //                     $ad_causado // monto causado (referencia)  
	 //                     $ad_pagado // monto pagado (referencia)  
     //	       Returns :	Retorna true o false si se realizo el metodo para el reporte
	 //	   Description :	Reporte que genera salida para  la ejecucucion financiera
	 //     Creado por :    Ing. Arnaldo Suárez
	 // Fecha Creación :    18/05/2008          Fecha última Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  $lb_valido = true;
	  $ld_aumento=0;
	  $ld_disminucion=0;
	  $ld_aumento_acum=0;
	  $ld_disminucion_acum=0;
	  $ldt_periodo=$_SESSION["la_empresa"]["periodo"];
	  $li_ano=substr($ldt_periodo,0,4);
	  $ls_codemp = $this->dts_empresa["codemp"];
      $li_mesdes=$ai_mesdes;
	  $li_meshas=$ai_meshas;
	  $as_spi_cuenta=$this->io_sigesp_int_spi->uf_spi_cuenta_sin_cero($as_spi_cuenta)."%";
	  $ls_sql=" SELECT DT.fecha, DT.monto, OP.previsto, OP.aumento, OP.disminucion, OP.devengado,OP.cobrado, OP.reservado     ".
              " FROM   spi_dt_cmp DT, spi_operaciones OP ".
              " WHERE  DT.codemp='".$this->ls_codemp."' AND (DT.operacion = OP.operacion) AND ".
              "        spi_cuenta like '".$as_spi_cuenta."' AND DT.fecha <= '".$ai_meshas."'".
			  " ORDER BY DT.fecha";	  
	  $rs_ejec=$this->io_sql->select($ls_sql);
	  if($rs_ejec===false)
	  { // error interno sql
		$this->is_msg_error="Error en consulta metodo uf_spi_reporte_calcular_ejecutado_trimestre".$this->fun->uf_convertirmsg($this->io_sql->message);
		$lb_valido = false;
 	  }
	  else
	  {
		while(!$rs_ejec->EOF)
		{
		  $li_previsto=$rs_ejec->fields["previsto"];
		  $li_aumento=$rs_ejec->fields["aumento"];
		  $li_disminucion=$rs_ejec->fields["disminucion"];
		  $li_devengado=$rs_ejec->fields["devengado"];
		  $li_cobrado=$rs_ejec->fields["cobrado"];
		  $li_reservado=$rs_ejec->fields["reservado"];
		  $ld_monto=$rs_ejec->fields["monto"];
		  $ldt_fecha_db=$rs_ejec->fields["fecha"];
		  $ldt_fecha=substr($ldt_fecha_db,0,10);
	      
     	  //  Comprometer, Causar, Pagar, Aumento, Disminución
		  if(($li_previsto)&&($ldt_fecha>=$li_mesdes)&&($ldt_fecha<=$li_meshas))
		  { 
		    $ad_pretriact=$ad_pretriact+$ld_monto;
		  }//if
		  if(($li_devengado)&&($ldt_fecha>=$li_mesdes)&&($ldt_fecha<=$li_meshas))
		  { 
		    $ad_devtriact=$ad_devtriact+$ld_monto;
		  }//if
		  if(($li_cobrado)&&($ldt_fecha>=$li_mesdes)&&($ldt_fecha<=$li_meshas))
		  { 
		    $ad_cobtriact=$ad_cobtriact+$ld_monto;
		  }//if
		   if(($li_reservado)&&($ldt_fecha>=$li_mesdes)&&($ldt_fecha<=$li_meshas))
		  { 
		    $ad_restriact=$ad_restriact+$ld_monto;
		  }//if
		  if(($li_previsto)&&($ldt_fecha<=$li_meshas))
		  { 
		    $ad_preacum=$ad_preacum+$ld_monto;
		  }//if
		  if(($li_devengado)&&($ldt_fecha<=$li_meshas))
		  { 
		    $ad_devacum=$ad_devacum+$ld_monto;
		  }//if
		  if(($li_cobrado)&&($ldt_fecha<=$li_meshas))
		  { 
		    $ad_cobacum=$ad_cobacum+$ld_monto;
		  }//if
		   if(($li_reservado)&&($ldt_fecha<=$li_meshas))
		  { 
		    $ad_resacum=$ad_resacum+$ld_monto;
		  }//if
		  if(($li_aumento)&&($ldt_fecha<=$li_meshas))
		  { 
		    $ld_aumento=$ld_aumento+$ld_monto;
		  }//if
		  if(($li_disminucion)&&($ldt_fecha<=$li_meshas))
		  { 
		    $ld_disminucion=$ld_disminucion+$ld_monto;
		  }//if
		  $rs_ejec->MoveNext();
		}//while
		$ad_aumdisacum=$ld_aumento-$ld_disminucion;
	   $this->io_sql->free_result($rs_ejec);
	  }//else	
	  return $lb_valido;	
   }//fin uf_spi_reporte_calcular_ejecutado_trimestre
   
	function uf_spi_reporte_calcular_ejecutado_trimestre_estructura( $as_spi_cuenta,$aa_codestpro_desde,$aa_codestpro_hasta,$ai_mesdes,$ai_meshas,&$ad_aumdisacum,&$ad_pretriact,&
																 $ad_devtriact,&$ad_cobtriact,&$ad_restriact,&$ad_preacum,&
																 $ad_devacum,&$ad_cobacum,&$ad_resacum)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spi_reporte_calcular_ejecutado_trimestre_estructura
	 //         Access :	private
	 //     Argumentos :    $as_spi_cuenta  // cuenta
	 //                     $aa_codestpro_desde // Arreglo de la Estructura Presupuestaria Desde
	 //                     $aa_codestpro_hasta // Arreglo de la Estructura Presupuesatria Hasta
	 //                     $as_mesdes  // mes  desde
     //              	    $as_meshas  // mes hasta
	 //                     $ad_monto_ejecutado // monto ejecutado (referencia)  
	 //                     $ad_monto_acumulado // monto acumulado (referencia) 
	 //                     $ad_aumdismes // monto aumneto y disminuciones del mes (referencia)  
	 //                     $ad_aumdisacum // monto aumneto y disminuciones del mes acumulado (referencia)  
	 //                     $ad_comprometer // monto comprometer (referencia)  
	 //                     $ad_causado // monto causado (referencia)  
	 //                     $ad_pagado // monto pagado (referencia)  
     //	       Returns :	Retorna true o false si se realizo el metodo para el reporte
	 //	   Description :	Reporte que genera salida para  la ejecucion financiera por rango de estrutura
	 //     Creado por :    Ing. Arnaldo Suárez
	 // Fecha Creación :    18/05/2008          Fecha última Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  $lb_valido = true;
	  $ld_aumento=0;
	  $ld_disminucion=0;
	  $ld_aumento_acum=0;
	  $ld_disminucion_acum=0;
	  $ls_gestor = $_SESSION["ls_gestor"];
	  $ldt_periodo=$_SESSION["la_empresa"]["periodo"];
	  $li_ano=substr($ldt_periodo,0,4);
	  $ls_codemp = $this->dts_empresa["codemp"];
      $li_mesdes=$ai_mesdes;
	  $li_meshas=$ai_meshas;
	  $ls_estructura="";
	  $ls_codestpro="";
	  if((!empty($aa_codestpro_desde[0]))&&(!empty($aa_codestpro_hasta[0])))
	  {
	  	$aa_codestpro_desde[0] = str_pad($aa_codestpro_desde[0],25,0,0);
		$aa_codestpro_desde[1] = str_pad($aa_codestpro_desde[1],25,0,0);
		$aa_codestpro_desde[2] = str_pad($aa_codestpro_desde[2],25,0,0);
		$aa_codestpro_desde[3] = str_pad($aa_codestpro_desde[3],25,0,0);
		$aa_codestpro_desde[4] = str_pad($aa_codestpro_desde[4],25,0,0);
		
		$aa_codestpro_hasta[0] = str_pad($aa_codestpro_hasta[0],25,0,0);
		$aa_codestpro_hasta[1] = str_pad($aa_codestpro_hasta[1],25,0,0);
		$aa_codestpro_hasta[2] = str_pad($aa_codestpro_hasta[2],25,0,0);
		$aa_codestpro_hasta[3] = str_pad($aa_codestpro_hasta[3],25,0,0);
		$aa_codestpro_hasta[4] = str_pad($aa_codestpro_hasta[4],25,0,0);
		
		
		
		switch($ls_gestor)
		{
		 case 'MYSQLT':   $ls_codestpro="CONCAT(DT.codestpro1,DT.codestpro2,DT.codestpro3,DT.codestpro4,DT.codestpro5,DT.estcla)"; 
						  break;
						
		 case 'POSTGRES': $ls_codestpro="DT.codestpro1||DT.codestpro2||DT.codestpro3||DT.codestpro4||DT.codestpro5||DT.estcla";
		                  
						  break;
		
		}
		
		$ls_estructura = "AND $ls_codestpro BETWEEN '".$aa_codestpro_desde[0].$aa_codestpro_desde[1].$aa_codestpro_desde[2].$aa_codestpro_desde[3].$aa_codestpro_desde[4].$aa_codestpro_desde[5]."' AND '".
		                  $aa_codestpro_hasta[0].$aa_codestpro_hasta[1].$aa_codestpro_hasta[2].$aa_codestpro_hasta[3].$aa_codestpro_hasta[4].$aa_codestpro_hasta[5]."'";
	  }
	  $as_spi_cuenta=$this->io_sigesp_int_spi->uf_spi_cuenta_sin_cero($as_spi_cuenta)."%";
	  $ls_sql=" SELECT DT.fecha, DT.monto, OP.previsto, OP.aumento, OP.disminucion, OP.devengado,OP.cobrado, OP.reservado     ".
              " FROM   spi_dt_cmp DT, spi_operaciones OP ".
              " WHERE  DT.codemp='".$this->ls_codemp."' AND (DT.operacion = OP.operacion) AND ".
              "        spi_cuenta like '".$as_spi_cuenta."' AND DT.fecha <= '".$ai_meshas."'".$ls_estructura.
			  " ORDER BY DT.fecha";
	  //echo	$ls_sql."<br>";	    	  
	  $rs_ejec=$this->io_sql->select($ls_sql);
	  if($rs_ejec===false)
	  { // error interno sql
		$this->is_msg_error="Error en consulta metodo uf_spi_reporte_calcular_ejecutado_trimestre".$this->io_funciones->uf_convertirmsg($this->io_sql->message);
		$lb_valido = false;
 	  }
	  else
	  {
		while(!$rs_ejec->EOF)
		{
		  $li_previsto=$rs_ejec->fields["previsto"];
		  $li_aumento=$rs_ejec->fields["aumento"];
		  $li_disminucion=$rs_ejec->fields["disminucion"];
		  $li_devengado=$rs_ejec->fields["devengado"];
		  $li_cobrado=$rs_ejec->fields["cobrado"];
		  $li_reservado=$rs_ejec->fields["reservado"];
		  $ld_monto=$rs_ejec->fields["monto"];
		  $ldt_fecha_db=$rs_ejec->fields["fecha"];
		  $ldt_fecha=substr($ldt_fecha_db,0,10);
	      
     	  //  Comprometer, Causar, Pagar, Aumento, Disminución
		  if(($li_previsto)&&($ldt_fecha>=$li_mesdes)&&($ldt_fecha<=$li_meshas))
		  { 
		    $ad_pretriact=$ad_pretriact+$ld_monto;
		  }//if
		  if(($li_devengado)&&($ldt_fecha>=$li_mesdes)&&($ldt_fecha<=$li_meshas))
		  { 
		    $ad_devtriact=$ad_devtriact+$ld_monto;
		  }//if
		  if(($li_cobrado)&&($ldt_fecha>=$li_mesdes)&&($ldt_fecha<=$li_meshas))
		  { 
		    $ad_cobtriact=$ad_cobtriact+$ld_monto;
		  }//if
		   if(($li_reservado)&&($ldt_fecha>=$li_mesdes)&&($ldt_fecha<=$li_meshas))
		  { 
		    $ad_restriact=$ad_restriact+$ld_monto;
		  }//if
		  if(($li_previsto)&&($ldt_fecha<=$li_meshas))
		  { 
		    $ad_preacum=$ad_preacum+$ld_monto;
		  }//if
		  if(($li_devengado)&&($ldt_fecha<=$li_meshas))
		  { 
		    $ad_devacum=$ad_devacum+$ld_monto;
		  }//if
		  if(($li_cobrado)&&($ldt_fecha<=$li_meshas))
		  { 
		    $ad_cobacum=$ad_cobacum+$ld_monto;
		  }//if
		   if(($li_reservado)&&($ldt_fecha<=$li_meshas))
		  { 
		    $ad_resacum=$ad_resacum+$ld_monto;
		  }//if
		  if(($li_aumento)&&($ldt_fecha<=$li_meshas))
		  { 
		    $ld_aumento=$ld_aumento+$ld_monto;
		  }//if
		  if(($li_disminucion)&&($ldt_fecha<=$li_meshas))
		  { 
		    $ld_disminucion=$ld_disminucion+$ld_monto;
		  }//if
		  $rs_ejec->MoveNext();
		}//while
		$ad_aumdisacum=$ld_aumento-$ld_disminucion;
	   $this->io_sql->free_result($rs_ejec);
	  }//else	
	  return $lb_valido;	
   }//fin uf_spi_reporte_calcular_ejecutado_trimestre_estructura
/****************************************************************************************************************************************/	

	function uf_spi_calcular_programado_trimestre($as_spi_cuenta,$aa_codestpro_desde,$aa_codestpro_hasta,$ai_trimestre,&$ad_progtrimestre,&$ad_acumtrimestre)
	{///////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spi_calcular_programado_trimestre
	 //         Access :	private
	 //     Argumentos :    $as_spi_cuenta       // Cuenta de Ingreso
	 //                     $aa_codestpro_desde  // Estructura Presupuestaria Desde
	 //                     $aa_codestpro_hasta, // Estructura Presupuestaria Hasta
	 //                     $ai_trimestre        // Trimestre a consultar
	 //                     $ad_progtrimestre,      // Total Programado en el Trimestre 
	 //                     $ad_acumtrimestre,     // Total  Acumulado del Programado en el Trimestre
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	Funcion que retorna los valores correspondientes a los programados por trimestre, para los reportes
	 //                     comparados instructivo 7 y 8 cuando el presupuesto de ingreso maneja estructura
	 //     Creado por :    Ing. Arnaldo Suarez
	 // Fecha Creación :    10/09/2009          Fecha última Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////
	  $lb_valido = true;
	  $ld_progtrimestre=0;
	  $ld_acumtrimestre=0;
	  $ls_estructura="";
	  $ls_codestpro="";
	  $ls_gestor = $_SESSION["ls_gestor"];
	  if((!empty($aa_codestpro_desde[0]))&&(!empty($aa_codestpro_hasta[0])))
	  {
	  	$aa_codestpro_desde[0] = str_pad($aa_codestpro_desde[0],25,0,0);
		$aa_codestpro_desde[1] = str_pad($aa_codestpro_desde[1],25,0,0);
		$aa_codestpro_desde[2] = str_pad($aa_codestpro_desde[2],25,0,0);
		$aa_codestpro_desde[3] = str_pad($aa_codestpro_desde[3],25,0,0);
		$aa_codestpro_desde[4] = str_pad($aa_codestpro_desde[4],25,0,0);
		
		$aa_codestpro_hasta[0] = str_pad($aa_codestpro_hasta[0],25,0,0);
		$aa_codestpro_hasta[1] = str_pad($aa_codestpro_hasta[1],25,0,0);
		$aa_codestpro_hasta[2] = str_pad($aa_codestpro_hasta[2],25,0,0);
		$aa_codestpro_hasta[3] = str_pad($aa_codestpro_hasta[3],25,0,0);
		$aa_codestpro_hasta[4] = str_pad($aa_codestpro_hasta[4],25,0,0);
		
		
		
		switch($ls_gestor)
		{
		 case 'MYSQLT':   $ls_codestpro="CONCAT(spi_cuentas_estructuras.codestpro1,spi_cuentas_estructuras.codestpro2,spi_cuentas_estructuras.codestpro3,spi_cuentas_estructuras.codestpro4,spi_cuentas_estructuras.codestpro5,spi_cuentas_estructuras.estcla)"; 
						  break;
						
		 case 'POSTGRES': $ls_codestpro="spi_cuentas_estructuras.codestpro1||spi_cuentas_estructuras.codestpro2||spi_cuentas_estructuras.codestpro3||spi_cuentas_estructuras.codestpro4||spi_cuentas_estructuras.codestpro5||spi_cuentas_estructuras.estcla";
		                  
						  break;
		
		}
		
		$ls_estructutra = "AND $ls_codestpro BETWEEN '".$aa_codestpro_desde[0].$aa_codestpro_desde[1].$aa_codestpro_desde[2].$aa_codestpro_desde[3].$aa_codestpro_desde[4].$aa_codestpro_desde[5]."' AND '".
		                  $aa_codestpro_hasta[0].$aa_codestpro_hasta[1].$aa_codestpro_hasta[2].$aa_codestpro_hasta[3].$aa_codestpro_hasta[4].$aa_codestpro_hasta[5]."'";
	  }
	  $as_spi_cuenta=$this->io_sigesp_int_spi->uf_spi_cuenta_sin_cero($as_spi_cuenta)."%";
	  $ls_sql=" SELECT sum(enero+febrero+marzo) AS trimestrei, ".
			  "        sum(abril+mayo+junio) AS trimestreii,   ".
			  "        sum(julio+agosto+septiembre) AS trimestreiii, ".
			  "        sum(octubre+noviembre+diciembre) AS trimestreiv ".
		      " FROM spi_cuentas_estructuras ".
              "  WHERE  spi_cuentas_estructuras.codemp='".$this->ls_codemp."' AND ".
              "        spi_cuentas_estructuras.spi_cuenta like '".$as_spi_cuenta."' ".$ls_estructutra;
	  //echo $ls_sql."<br>";		  	    	  
	  $rs_ejec=$this->io_sql->select($ls_sql);
	  if($rs_ejec===false)
	  { // error interno sql
		$this->is_msg_error="Error en consulta metodo uf_spi_calcular_programado_trimestre".$this->io_funciones->uf_convertirmsg($this->io_sql->message);
		$lb_valido = false;
 	  }
	  else
	  {
		if(!$rs_ejec->EOF)
		{
		  switch($ai_trimestre)
		  {
		    case 1  :$ld_progtrimestre=$ld_acumtrimestre=$rs_ejec->fields['trimestrei'];
			         break;
					 
		    case 4  :$ld_progtrimestre=$rs_ejec->fields['trimestreii'];
			         $ld_acumtrimestre=$rs_ejec->fields['trimestrei']+$rs_ejec->fields['trimestreii'];
					 break;
					 
		    case 7  :$ld_progtrimestre=$rs_ejec->fields['trimestreiii'];
			         $ld_acumtrimestre=$rs_ejec->fields['trimestrei']+$rs_ejec->fields['trimestreii']+$rs_ejec->fields['trimestreiii'];
					 break;
					 
		    case 10 :$ld_progtrimestre=$rs_ejec->fields['trimestreiv'];
			         $ld_acumtrimestre=$rs_ejec->fields['trimestrei']+$rs_ejec->fields['trimestreii']+$rs_ejec->fields['trimestreiii']+$rs_ejec->fields['trimestreiv'];
					 break;
		  
		  }
		  
		}//if
		$ad_progtrimestre = $ld_progtrimestre;
		$ad_acumtrimestre = $ld_acumtrimestre;
	   $this->io_sql->free_result($rs_ejec);
	  }//else	
	  return $lb_valido;
	}



//-----------------------------------------------------------------------------------------------------------------------------------
    function uf_spg_ejecutado_trimestral_estado_resultado($as_spg_cuenta,$adt_fecdes,$adt_fechas,&$ad_comprometer,&$ad_causado,
	                                                      &$ad_pagado,&$ad_aumento,&$ad_disminucion,$as_detallar,$ab_precaj=false,$aa_cuentas=NULL)
    {///////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_ejecutado_trimestral
	 //         Access :	private
	 //     Argumentos :    $as_spg_cuenta  -----> cuenta 
	 //                     $adt_fecdes  -----> fechas desde 
	 //                     $adt_fechas  -----> fechas hasta  
	 //                     $ad_comprometer_acumulado  -----> monto comprometer referencia   
	 //                     $ad_causado_acumulado  -----> monto causado referencia   
	 //                     $ad_pagado_acumulado  -----> monto pagado referencia   
	 //                     $ad_aumento_acumulado  -----> monto aumento referencia   
	 //                     $ad_disminucion_acumulado  -----> monto disminucion referencia   
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	Reporte que genera los eejcutados por trimestre
	 //     Creado por :    Ing. Yozelin Barragán.
	 // Fecha Creación :    18/05/2008          Fecha última Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $lb_valido = true;	 
	 $ad_comprometer=0;  
	 $ad_causado=0;  
	 $ad_pagado=0;
	 $ad_aumento=0;  
	 $ad_disminucion=0;
	 $ls_sql_compromiso = "";	
	 $ls_sql_causado = "";	
	 $ls_sql_pagado = "";
	 $ls_sql_aumento = "";
	 $ls_sql_disminucion = "";
	 
	 $ls_validacion="";
	 if($ab_precaj)
	 {
	  $ls_validacion = " AND spg_cuenta NOT LIKE '40801%'";
	 }
	 if($as_detallar==true)
	 {
	   $as_spg_cuenta = $this->io_sigesp_int_spg->uf_spg_cuenta_sin_cero($as_spg_cuenta)."%";
	   $ls_cadena="spg_cuenta like '".$as_spg_cuenta."'  AND";
	 }
	 elseif($as_detallar==false)
	 {
	   $ls_cadena="spg_cuenta = '".$as_spg_cuenta."'  AND";
	 }
     $as_spg_cuenta = $this->io_sigesp_int_spg->uf_spg_cuenta_sin_cero($as_spg_cuenta)."%";
	 $ls_cadena="spg_cuenta like '".$as_spg_cuenta."'  AND";
	 
	 $ls_cadena_filtro = "";
	 if(($aa_cuentas != NULL)&&(is_array($aa_cuentas)))
	 {
	   $li_total = count($aa_cuentas);
	   for($i=0; $i<$li_total;$i++)
	   {
	    $ls_cta_aux = $this->io_sigesp_int_spg->uf_spg_cuenta_sin_cero($aa_cuentas[$i])."%";
		if($i==0)
		{
	     $ls_cadena_filtro .= "( DT.spg_cuenta LIKE '".$ls_cta_aux."' ";
		}
		else
		{
		 $ls_cadena_filtro .= " OR DT.spg_cuenta LIKE '".$ls_cta_aux."' ";
		}
	   }
	   $ls_cadena_filtro .= ") AND ";
	 }
	 else
	 {
	   $ls_cadena_filtro = $ls_cadena;
	 }
	 
	 
	 // COMPROMISO
	 
	 $ls_sql_compromiso = "SELECT COALESCE(SUM(DT.monto),0.00) as compromiso ".
						  " FROM   spg_dt_cmp DT, spg_operaciones OP ".
						  "	WHERE  DT.codemp='".$this->ls_codemp."' AND ".
						  "		   DT.operacion = OP.operacion AND ".
						  "		   (OP.comprometer = 1 OR OP.precomprometer = 1) AND  ".
						  "        ".$ls_cadena_filtro.
						  "        DT.fecha BETWEEN '".$adt_fecdes."' AND  '".$adt_fechas."' ".$ls_validacion;
	 $rs_compromiso=$this->io_sql->select($ls_sql_compromiso);
	 if($rs_compromiso===false)
	 { // error interno sql
		$this->io_mensajes->message("CLASE->sigesp_spg_class_reportes_instructivos ". 
									"MÉTODO->uf_spg_ejecutado_acumulado_estado_resultado ".
									"ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		$lb_valido = false;
	 }
	 else
	 {
	  if(!$rs_compromiso->EOF)
	  {
	   $ad_comprometer = $rs_compromiso->fields["compromiso"];
	  }
	 }
	 
	 // CAUSADO
	 
	 $ls_sql_causado = "SELECT COALESCE(SUM(DT.monto),0.00) as causado ".
						  " FROM   spg_dt_cmp DT, spg_operaciones OP ".
						  "	WHERE  DT.codemp='".$this->ls_codemp."' AND ".
						  "		   DT.operacion = OP.operacion AND ".
						  "		   OP.causar = 1 AND  ".
						  "        ".$ls_cadena_filtro.
						  "        DT.fecha BETWEEN '".$adt_fecdes."' AND  '".$adt_fechas."' ".$ls_validacion;
	 $rs_causado=$this->io_sql->select($ls_sql_causado);
	 if($rs_causado===false)
	 { // error interno sql
		$this->io_mensajes->message("CLASE->sigesp_spg_class_reportes_instructivos ". 
									"MÉTODO->uf_spg_ejecutado_acumulado_estado_resultado ".
									"ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		$lb_valido = false;
	 }
	 else
	 {
	  if(!$rs_causado->EOF)
	  {
	   $ad_causado = $rs_causado->fields["causado"];
	  }
	 }
	 
	 // PAGADO
	 
	 $ls_sql_pagado = "SELECT COALESCE(SUM(DT.monto),0.00) as pagado ".
					  " FROM   spg_dt_cmp DT, spg_operaciones OP ".
					  "	WHERE  DT.codemp='".$this->ls_codemp."' AND ".
					  "		   DT.operacion = OP.operacion AND ".
					  "		   OP.pagar = 1 AND  ".
					  "        ".$ls_cadena_filtro.
					  "        DT.fecha BETWEEN '".$adt_fecdes."' AND  '".$adt_fechas."' ".$ls_validacion;
	 //echo $ls_sql_pagado."<br><br>";
	 $rs_pagado=$this->io_sql->select($ls_sql_pagado);
	 if($rs_pagado===false)
	 { // error interno sql
		$this->io_mensajes->message("CLASE->sigesp_spg_class_reportes_instructivos ". 
									"MÉTODO->uf_spg_ejecutado_acumulado_estado_resultado ".
									"ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		$lb_valido = false;
	 }
	 else
	 {
	  if(!$rs_pagado->EOF)
	  {
	   $ad_pagado = $rs_pagado->fields["pagado"];
	  }
	 }
	 
	  // AUMENTO
	 
	 $ls_sql_aumento = "SELECT COALESCE(SUM(DT.monto),0.00) as aumento ".
					   " FROM   spg_dt_cmp DT, spg_operaciones OP ".
					   "	WHERE  DT.codemp='".$this->ls_codemp."' AND ".
					   "		   DT.operacion = OP.operacion AND ".
					   "		   OP.aumento = 1 AND  ".
					   "        ".$ls_cadena_filtro.
					   "        DT.fecha BETWEEN '".$adt_fecdes."' AND  '".$adt_fechas."' ".$ls_validacion;
	 $rs_aumento=$this->io_sql->select($ls_sql_aumento);
	 if($rs_aumento===false)
	 { // error interno sql
		$this->io_mensajes->message("CLASE->sigesp_spg_class_reportes_instructivos ". 
									"MÉTODO->uf_spg_ejecutado_acumulado_estado_resultado ".
									"ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		$lb_valido = false;
	 }
	 else
	 {
	  if(!$rs_aumento->EOF)
	  {
	   $ad_aumento = $rs_aumento->fields["aumento"];
	  }
	 }
	 
	 // DISMINUCION
	 
	 $ls_sql_disminucion = "SELECT COALESCE(SUM(DT.monto),0.00) as disminucion ".
						   " FROM   spg_dt_cmp DT, spg_operaciones OP ".
						   "	WHERE  DT.codemp='".$this->ls_codemp."' AND ".
						   "		   DT.operacion = OP.operacion AND ".
						   "		   OP.disminucion = 1 AND  ".
						   "        ".$ls_cadena_filtro.
						   "        DT.fecha BETWEEN '".$adt_fecdes."' AND  '".$adt_fechas."' ".$ls_validacion;
	 $rs_disminucion=$this->io_sql->select($ls_sql_disminucion);
	 if($rs_disminucion===false)
	 { // error interno sql
		$this->io_mensajes->message("CLASE->sigesp_spg_class_reportes_instructivos ". 
									"MÉTODO->uf_spg_ejecutado_acumulado_estado_resultado ".
									"ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		$lb_valido = false;
	 }
	 else
	 {
	  if(!$rs_disminucion->EOF)
	  {
	   $ad_aumento = $rs_disminucion->fields["disminucion"];
	  }
	 }
	 $this->io_sql->free_result($rs_compromiso);
	 $this->io_sql->free_result($rs_causado);
	 $this->io_sql->free_result($rs_pagado);
	 $this->io_sql->free_result($rs_aumento);
	 $this->io_sql->free_result($rs_disminucion);
	 
	 
	 
	 
	 /*$ls_sql = " SELECT DT.fecha, DT.monto, OP.aumento, OP.disminucion, ".
               "        OP.precomprometer,OP.comprometer, ".
               "        OP.causar, OP.pagar ".
               " FROM   spg_dt_cmp DT, spg_operaciones OP ".
               " WHERE  DT.codemp='".$this->ls_codemp."' AND ".
               "        DT.operacion = OP.operacion AND ".
               "        ".$ls_cadena."                  ".
               "        fecha BETWEEN '".$adt_fecdes."' AND  '".$adt_fechas."' ".$ls_validacion;	      	   	   
	  $rs_ejecutado=$this->io_sql->select($ls_sql); 
	  if($rs_ejecutado===false)
	  { // error interno sql
			$this->io_mensajes->message("CLASE->sigesp_spg_class_reportes_instructivos ". 
			                            "MÉTODO->uf_spg_ejecutado_trimestral ".
										"ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		$lb_valido = false;
 	  }
	  else
	  {
		while($row=$this->io_sql->fetch_row($rs_ejecutado))
		{
		  $li_aumento=$row["aumento"];
		  $li_disminucion=$row["disminucion"];
		  $li_precomprometer=$row["precomprometer"];
		  $li_comprometer=$row["comprometer"];
		  $li_causar=$row["causar"];
		  $li_pagar=$row["pagar"];
		  $ld_monto=$row["monto"];
		  $ldt_fecha_db=$row["fecha"];
		  
		  if($li_comprometer)
		  { 
		    $ad_comprometer=$ad_comprometer+$ld_monto;
		  }//if
		  if($li_causar)
		  { 
		    $ad_causado=$ad_causado+$ld_monto;
		  }//if
		  if($li_pagar)
		  { 
		    $ad_pagado=$ad_pagado+$ld_monto;
		  }//if
		  if($li_aumento)
		  {
		    $ad_aumento=$ad_aumento+$ld_monto;
		  }//if
		  if($li_disminucion)
		  { 
		    $ad_disminucion=$ad_disminucion+$ld_monto;
		  }//if
	    }// while
	    $this->io_sql->free_result($rs_ejecutado);
	   }//else	*/
	  return $lb_valido;	
     }//fin uf_spg_ejecutado_trimestral_estado_resultado
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
    function uf_spg_ejecutado_acumulado_estado_resultado($as_spg_cuenta,$adt_fechas,&$ad_comprometer_acumulado,
	                                                     &$ad_causado_acumulado,&$ad_pagado_acumulado,&$ad_aumento_acumulado,
														 &$ad_disminucion_acumulado,$as_detallar,$ab_precaj=false,$aa_cuentas=NULL)
    {///////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_ejecutado_trimestral
	 //         Access :	private
	 //     Argumentos :    $as_spg_cuenta  -----> cuenta 
	 //                     $adt_fechas  -----> fechas hasta    
	 //                     $ad_comprometer_acumulado  -----> monto acumulado comprometer referencia   
	 //                     $ad_causado_acumulado  -----> monto acumulado causado referencia   
	 //                     $ad_pagado_acumulado  -----> monto acumulado pagado referencia   
	 //                     $ad_aumento_acumulado  -----> monto acumulado aumento referencia   
	 //                     $ad_disminucion_acumulado  -----> monto acumulado disminucion referencia   
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	Reporte que genera los eejcutados por trimestre
	 //     Creado por :    Ing. Yozelin Barragán.
	 // Fecha Creación :    18/05/2008          Fecha última Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $lb_valido = true;
	 $ad_comprometer_acumulado = 0;
	 $ad_causado_acumulado = 0;
	 $ad_pagado_acumulado = 0;
	 $ad_aumento_acumulado = 0;
	 $ad_disminucion_acumulado = 0;
	 $ls_sql_compromiso = "";	
	 $ls_sql_causado = "";	
	 $ls_sql_pagado = "";
	 $ls_sql_aumento = "";
	 $ls_sql_disminucion = "";
	 
	 	 
	 if($as_detallar==true)
	 {
	   $as_spg_cuenta = $this->io_sigesp_int_spg->uf_spg_cuenta_sin_cero($as_spg_cuenta)."%";
	   $ls_cadena="spg_cuenta like '".$as_spg_cuenta."'  AND";
	 }
	 elseif($as_detallar==false)
	 {
	   $as_spg_cuenta = $as_spg_cuenta;
	   $ls_cadena="spg_cuenta = '".$as_spg_cuenta."'  AND";
	 }
	 
	 $ls_validacion="";
	 if($ab_precaj)
	 {
	  $ls_validacion = " AND spg_cuenta NOT LIKE '40801%'";
	 }
	 $as_spg_cuenta = $this->io_sigesp_int_spg->uf_spg_cuenta_sin_cero($as_spg_cuenta)."%";
	 $ls_cadena="spg_cuenta like '".$as_spg_cuenta."'  AND";
	 
	 
	 $ls_cadena_filtro = "";
	 if(($aa_cuentas != NULL)&&(is_array($aa_cuentas)))
	 {
	   $li_total = count($aa_cuentas);
	   for($i=0; $i<$li_total;$i++)
	   {
	    $ls_cta_aux = $this->io_sigesp_int_spg->uf_spg_cuenta_sin_cero($aa_cuentas[$i])."%";
		if($i==0)
		{
	     $ls_cadena_filtro .= "( DT.spg_cuenta LIKE '".$ls_cta_aux."' ";
		}
		else
		{
		 $ls_cadena_filtro .= " OR DT.spg_cuenta LIKE '".$ls_cta_aux."' ";
		}
	   }
	   $ls_cadena_filtro .= ") AND ";
	 }
	 else
	 {
	  $ls_cadena_filtro = $ls_cadena;
	 }
	 
	 // COMPROMISO ACUMULADO
	 
	 $ls_sql_compromiso = "SELECT COALESCE(SUM(DT.monto),0.00) as compromiso ".
						  " FROM   spg_dt_cmp DT, spg_operaciones OP ".
						  "	WHERE  DT.codemp='".$this->ls_codemp."' AND ".
						  "		   DT.operacion = OP.operacion AND ".
						  "		   (OP.comprometer = 1 OR OP.precomprometer = 1) AND  ".
						  "        ".$ls_cadena_filtro.
						  "		   DT.fecha <='".$adt_fechas."' ".$ls_validacion;
	 $rs_compromiso=$this->io_sql->select($ls_sql_compromiso);
	 if($rs_compromiso===false)
	 { // error interno sql
		$this->io_mensajes->message("CLASE->sigesp_spg_class_reportes_instructivos ". 
									"MÉTODO->uf_spg_ejecutado_acumulado_estado_resultado ".
									"ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		$lb_valido = false;
	 }
	 else
	 {
	  if(!$rs_compromiso->EOF)
	  {
	   $ad_comprometer_acumulado = $rs_compromiso->fields["compromiso"];
	  }
	 }
	 
	 // CAUSADO ACUMULADO
	 
	 $ls_sql_causado = "SELECT COALESCE(SUM(DT.monto),0.00) as causado ".
						  " FROM   spg_dt_cmp DT, spg_operaciones OP ".
						  "	WHERE  DT.codemp='".$this->ls_codemp."' AND ".
						  "		   DT.operacion = OP.operacion AND ".
						  "		   OP.causar = 1 AND  ".
						  "        ".$ls_cadena_filtro.
						  "		   DT.fecha <='".$adt_fechas."' ".$ls_validacion;
	 $rs_causado=$this->io_sql->select($ls_sql_causado);
	 if($rs_causado===false)
	 { // error interno sql
		$this->io_mensajes->message("CLASE->sigesp_spg_class_reportes_instructivos ". 
									"MÉTODO->uf_spg_ejecutado_acumulado_estado_resultado ".
									"ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		$lb_valido = false;
	 }
	 else
	 {
	  if(!$rs_causado->EOF)
	  {
	   $ad_causado_acumulado = $rs_causado->fields["causado"];
	  }
	 }
	 
	 // PAGADO ACUMULADO
	 
	 $ls_sql_pagado = "SELECT COALESCE(SUM(DT.monto),0.00) as pagado ".
						  " FROM   spg_dt_cmp DT, spg_operaciones OP ".
						  "	WHERE  DT.codemp='".$this->ls_codemp."' AND ".
						  "		   DT.operacion = OP.operacion AND ".
						  "		   OP.pagar = 1 AND  ".
						  "        ".$ls_cadena_filtro.
						  "		   DT.fecha <='".$adt_fechas."' ".$ls_validacion;
	 $rs_pagado=$this->io_sql->select($ls_sql_pagado);
	 if($rs_pagado===false)
	 { // error interno sql
		$this->io_mensajes->message("CLASE->sigesp_spg_class_reportes_instructivos ". 
									"MÉTODO->uf_spg_ejecutado_acumulado_estado_resultado ".
									"ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		$lb_valido = false;
	 }
	 else
	 {
	  if(!$rs_pagado->EOF)
	  {
	   $ad_pagado_acumulado = $rs_pagado->fields["pagado"];
	  }
	 }
	 
	  // AUMENTO ACUMULADO
	 
	 $ls_sql_aumento = "SELECT COALESCE(SUM(DT.monto),0.00) as aumento ".
						  " FROM   spg_dt_cmp DT, spg_operaciones OP ".
						  "	WHERE  DT.codemp='".$this->ls_codemp."' AND ".
						  "		   DT.operacion = OP.operacion AND ".
						  "		   OP.aumento = 1 AND  ".
						  "        ".$ls_cadena_filtro.
						  "		   DT.fecha <='".$adt_fechas."' ".$ls_validacion;
	 $rs_aumento=$this->io_sql->select($ls_sql_aumento);
	 if($rs_aumento===false)
	 { // error interno sql
		$this->io_mensajes->message("CLASE->sigesp_spg_class_reportes_instructivos ". 
									"MÉTODO->uf_spg_ejecutado_acumulado_estado_resultado ".
									"ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		$lb_valido = false;
	 }
	 else
	 {
	  if(!$rs_aumento->EOF)
	  {
	   $ad_aumento_acumulado = $rs_aumento->fields["aumento"];
	  }
	 }
	 
	 // DISMINUCION ACUMULADA
	 
	 $ls_sql_disminucion = "SELECT COALESCE(SUM(DT.monto),0.00) as disminucion ".
						  " FROM   spg_dt_cmp DT, spg_operaciones OP ".
						  "	WHERE  DT.codemp='".$this->ls_codemp."' AND ".
						  "		   DT.operacion = OP.operacion AND ".
						  "		   OP.disminucion = 1 AND  ".
						  "        ".$ls_cadena_filtro.
						  "		   DT.fecha <='".$adt_fechas."' ".$ls_validacion;
	 $rs_disminucion=$this->io_sql->select($ls_sql_disminucion);
	 if($rs_disminucion===false)
	 { // error interno sql
		$this->io_mensajes->message("CLASE->sigesp_spg_class_reportes_instructivos ". 
									"MÉTODO->uf_spg_ejecutado_acumulado_estado_resultado ".
									"ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		$lb_valido = false;
	 }
	 else
	 {
	  if(!$rs_disminucion->EOF)
	  {
	   $ad_aumento_acumulado = $rs_disminucion->fields["disminucion"];
	  }
	 }
	 $this->io_sql->free_result($rs_compromiso);
	 $this->io_sql->free_result($rs_causado);
	 $this->io_sql->free_result($rs_pagado);
	 $this->io_sql->free_result($rs_aumento);
	 $this->io_sql->free_result($rs_disminucion);
	 
	 /*$ls_sql = " SELECT DT.fecha, DT.monto, OP.aumento, OP.disminucion, ".
               "        OP.precomprometer,OP.comprometer, ".
               "        OP.causar, OP.pagar ".
               " FROM   spg_dt_cmp DT, spg_operaciones OP ".
               " WHERE  DT.codemp='".$this->ls_codemp."' AND ".
               "        DT.operacion = OP.operacion AND ".
               "        ".$ls_cadena."  ".
               "        fecha <='".$adt_fechas."' ".$ls_validacion;	   
	  $rs_ejecutado=$this->io_sql->select($ls_sql);
	  if($rs_ejecutado===false)
	  { // error interno sql
		$this->io_mensajes->message("CLASE->sigesp_spg_class_reportes_instructivos ". 
			                        "MÉTODO->uf_spg_ejecutado_trimestral ".
									"ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		$lb_valido = false;
 	  }
	  else
	  {
		while($row=$this->io_sql->fetch_row($rs_ejecutado))
		{
		  $li_aumento=$row["aumento"];
		  $li_disminucion=$row["disminucion"];
		  $li_precomprometer=$row["precomprometer"];
		  $li_comprometer=$row["comprometer"];
		  $li_causar=$row["causar"];
		  $li_pagar=$row["pagar"];
		  $ld_monto=$row["monto"];
		  $ldt_fecha_db=$row["fecha"];
		  
		  if($li_comprometer)
		  { 
		    $ad_comprometer_acumulado=$ad_comprometer_acumulado+$ld_monto;
		  }//if
		  if($li_causar)
		  { 
		    $ad_causado_acumulado=$ad_causado_acumulado+$ld_monto;
		  }//if
		  if($li_pagar)
		  { 
		    $ad_pagado_acumulado=$ad_pagado_acumulado+$ld_monto;
		  }//if
		  if($li_aumento)
		  { 
		    $ad_aumento_acumulado=$ad_aumento_acumulado+$ld_monto;
		  }//if
		  if($li_disminucion)
		  { 
		    $ad_disminucion_acumulado=$ad_disminucion_acumulado+$ld_monto;
		  }//if
	    }// while
	    $this->io_sql->free_result($rs_ejecutado);
	   }//else	*/
	  return $lb_valido;	
     }//fin uf_spg_ejecutado_acumulado_estado_resultado
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
    function uf_spg_reportes_select_denominacion($as_spi_cuenta,&$as_denominacion)
    { /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  //	      Function :	uf_spg_reportes_select_denominacion
	  //        Argumentos :    
      //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	  //	   Description :	Reporte que genera salida  del Ejecucion Financiera Formato 3
	  //        Creado por :    Ing. Yozelin Barragán.
	  //    Fecha Creación :    28/06/2008                       Fecha última Modificacion :      Hora :
  	  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql = "SELECT denominacion FROM sigesp_plan_unico_re WHERE sig_cuenta LIKE '".$as_spi_cuenta."%' ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->sigesp_spg_class_reportes_instructivos ". 
										"MÉTODO->uf_spg_reportes_select_denominacion ".
										"ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 	
		}
		else
		{
		   if(!$rs_data->EOF)
		   {
			  $as_denominacion=$rs_data->fields["denominacion"];
		   }
		   $this->io_sql->free_result($rs_data);
		}
		return  $lb_valido;
     }//fin uf_spg_reportes_select_denominacion()
	//-----------------------------------------------------------------------------------------------------------------------------------
/********************************************************************************************************************************/
/*                                                        PRESUPUESTO DE CAJA INSTRUCTIVO 07                                                  */
/********************************************************************************************************************************/


	function uf_spg_reportes_presupuesto_de_caja($adt_fecdes,$adt_fechas,$adts_datastore,$as_mesdes,$as_meshas)
	{////////////////////////////////////////////////////////////////////////////////////////////////////////
		 //	      Function :  uf_spg_reportes_presupuesto_de_caja
		 //     Argumentos : adt_fecdes ... adt_fechas  // rango de fecha del reporte
		 //                  adts_datastore  // datastore que imprime el reporte
		 //	       Returns : Retorna true o false si se realizo la consulta para el reporte
		 //	   Description : Reporte que genera salida del Presupuesto de Caja
		 //     Creado por : Ing. Arnaldo Suárez
		 // Fecha Creación : 18/06/2008                       Fecha última Modificacion :      Hora :
		 ///////////////////////////////////////////////////////////////////////////////////////////////////////
		 $ld_asignado_i = 0;
		 $ld_asignado_modificado_i = 0;
		 $ld_programado_i = 0;
		 $ld_ejecutado_i = 0;
		 $ld_variacion_absoluta_i = 0;
		 $ld_variacion_porcentual_i = 0;
		 $ld_programado_acumulado_i = 0;
		 $ld_ejecutado_acumulado_i = 0;
		 
		  $lb_valido = $this->uf_spg_reporte_select_saldo_empresa();
		 if($lb_valido)
		 {
		  $lb_valido=$this->uf_spi_reporte_total_ingresos($adt_fecdes,$adt_fechas,$as_mesdes,$as_meshas);
		 }
		 if($lb_valido)
		 {
		   $lb_valido=$this->uf_spi_reportes_ingresos_corrientes($adt_fecdes,$adt_fechas,$adts_datastore,$as_mesdes,$as_meshas);
		 }
		 if($lb_valido)
		 {
			$lb_valido=$this->uf_spi_reportes_ingresos_capital($adt_fecdes,$adt_fechas,$adts_datastore,$as_mesdes,$as_meshas);
		 }
		 if($lb_valido)
		 {
			$lb_valido=$this->uf_spi_reportes_ingresos_financieros($adt_fecdes,$adt_fechas,$adts_datastore,$as_mesdes,$as_meshas);
		 }
		 if($lb_valido)
		 {
			$lb_valido=$this->uf_spi_reportes_gastos_de_consumo($adt_fecdes,$adt_fechas,$adts_datastore,$as_mesdes,$as_meshas);
		 }
		 if($lb_valido)
		 {
			$lb_valido=$this->uf_spi_reportes_gastos_corrientes($adt_fecdes,$adt_fechas,$adts_datastore,$as_mesdes,$as_meshas);
		 }	 
		 if($lb_valido)
		 {
			$la_cuenta[67]=array();
			
			$ls_formpre=$_SESSION["la_empresa"]["formpre"];
			$ls_formpre=str_replace('-','',$ls_formpre);
			$li_len=strlen($ls_formpre);
			$li_len=$li_len-9;
			$ls_ceros=$this->io_funciones->uf_cerosderecha("",$li_len);
					
			// INGRESOS CORRIENTES
			$la_cuenta[1]='305010000'.$ls_ceros;
			$la_cuenta[2]='305010100'.$ls_ceros;
			$la_cuenta[3]='305010300'.$ls_ceros;
			$la_cuenta[4]='305010301'.$ls_ceros;
			$la_cuenta[5]='305010302'.$ls_ceros;
			$la_cuenta[6]='305010304'.$ls_ceros;
			$la_cuenta[7]='305010305'.$ls_ceros;
			$la_cuenta[8]='305010306'.$ls_ceros;
			$la_cuenta[9]='305010307'.$ls_ceros;
			$la_cuenta[10]='305010308'.$ls_ceros;
			$la_cuenta[11]='305010309'.$ls_ceros;
			$la_cuenta[12]='305010500'.$ls_ceros;
			$la_cuenta[13]='305010501'.$ls_ceros;
			$la_cuenta[14]='305010502'.$ls_ceros;
			$la_cuenta[15]='305010503'.$ls_ceros;
			$la_cuenta[16]='301090000'.$ls_ceros;
			$la_cuenta[17]='301090100'.$ls_ceros;
			$la_cuenta[18]='301090200'.$ls_ceros;
			$la_cuenta[19]='301099900'.$ls_ceros;
			$la_cuenta[20]='408070000'.$ls_ceros;
			$la_cuenta[21]='301030000'.$ls_ceros;
			$la_cuenta[22]='301040000'.$ls_ceros;
			$la_cuenta[23]='301050000'.$ls_ceros;
			$la_cuenta[24]='301100000'.$ls_ceros;
			$la_cuenta[25]='301100401'.$ls_ceros;
			$la_cuenta[26]='301100400'.$ls_ceros;
			$la_cuenta[27]='301100500'.$ls_ceros;
			$la_cuenta[28]='301100800'.$ls_ceros;
			$la_cuenta[29]='301110000'.$ls_ceros;
			// INGRESOS DE CAPITAL
			$la_cuenta[30]='305020000'.$ls_ceros;
			$la_cuenta[31]='305020100'.$ls_ceros;
			$la_cuenta[32]='305020300'.$ls_ceros;
			$la_cuenta[33]='305020301'.$ls_ceros;
			$la_cuenta[34]='305020302'.$ls_ceros;
			$la_cuenta[35]='305020308'.$ls_ceros;
			$la_cuenta[36]='305020309'.$ls_ceros;
			$la_cuenta[37]='305020500'.$ls_ceros;
			$la_cuenta[38]='305020501'.$ls_ceros;
			$la_cuenta[39]='305020502'.$ls_ceros;
			$la_cuenta[40]='305020503'.$ls_ceros;
			$la_cuenta[41]='306010000'.$ls_ceros;
			$la_cuenta[42]='306020000'.$ls_ceros;
			// INGRESOS FINANCIEROS
			$la_cuenta[43]='307000000'.$ls_ceros;
			$la_cuenta[44]='308000000'.$ls_ceros;
			$la_cuenta[45]='309000000'.$ls_ceros;
			$la_cuenta[46]='311000000'.$ls_ceros;
			// INCREMENTOS DE PASIVO
			$la_cuenta[47]='312000000'.$ls_ceros;
			// INCREMENTO DE PATRIMONIO
			$la_cuenta[48]='313000000'.$ls_ceros;
			// EGRESOS
			$la_cuenta[49]='400000000'.$ls_ceros;
			// EGRESOS DE CONSUMO
			$la_cuenta[50]='401000000'.$ls_ceros;
			$la_cuenta[51]='402000000'.$ls_ceros;
			$la_cuenta[52]='403000000'.$ls_ceros;
			$la_cuenta[53]='408000000'.$ls_ceros;
			$la_cuenta[54]='408020000'.$ls_ceros;
			$la_cuenta[55]='408060000'.$ls_ceros;
			// OTROS EGRESOS CORRIENTES
			$la_cuenta[56]='407000000'.$ls_ceros;
			$la_cuenta[57]='407010100'.$ls_ceros;
			$la_cuenta[58]='407010300'.$ls_ceros;
			$la_cuenta[59]='407020000'.$ls_ceros;
			$la_cuenta[60]='407030000'.$ls_ceros;
			$la_cuenta[61]='407030100'.$ls_ceros;
			$la_cuenta[62]='407030300'.$ls_ceros;
			$la_cuenta[63]='408080000'.$ls_ceros;
			// ACTIVOS REALES
			$la_cuenta[64]='404000000'.$ls_ceros;
			// ACTIVOS FINANCIEROS
			$la_cuenta[65]='405000000'.$ls_ceros;
			// DISMINUCION DE PASIVOS
			$la_cuenta[66]='411000000'.$ls_ceros;
			// DISMINUCION DE PATRIMONIO
			$la_cuenta[67]='412000000'.$ls_ceros;
			
			$ld_asignado_vn = 0;
			$ld_asignado_modificado_vn = 0;
			$ld_programado_vn = 0;
			$ld_ejecutado_vn = 0;
			$ld_variacion_absoluta_vn = 0;
			$ld_variacion_porcentual_vn = 0;
			$ld_programado_acumulado_vn = 0;
			$ld_ejecutado_acumulado_vn = 0;
			
			$ld_total_asignado_ic=0;
			$ld_total_asignado_modificado_ic=0;
			$ld_total_programado_ic=0;
			$ld_total_ejecutado_ic=0;
			$ld_total_variacion_absoluta_ic=0;
			$ld_total_variacion_porcentual_ic=0;
			$ld_total_programado_acumulado_ic=0;
			$ld_total_ejecutado_acumulado_ic=0;
			
			$ld_total_asignado_if=0;
			$ld_total_asignado_modificado_if=0;
			$ld_total_programado_if=0;
			$ld_total_ejecutado_if=0;
			$ld_total_variacion_absoluta_if=0;
			$ld_total_variacion_porcentual_if=0;
			$ld_total_programado_acumulado_if=0;
			$ld_total_ejecutado_acumulado_if=0;
			
			
			for($i=1;$i<=67;$i++)
			{
			   switch ($i)
			   {		
				 case 1:  //INGRESOS CORRIENTES
					$ld_total_asignado_ic=0;
					$ld_total_asignado_modificado_ic=0;
					$ld_total_programado_ic=0;
					$ld_total_ejecutado_ic=0;
					$ld_total_variacion_absoluta_ic=0;
					$ld_total_variacion_porcentual_ic=0;
					$ld_total_programado_acumulado_ic=0;
					$ld_total_ejecutado_acumulado_ic=0;
					$li_total=$this->dts_ingresos_corrientes->getRowCount("cuenta");
					if($li_total>0)
					{
					  for($li=1;$li<=$li_total;$li++)
					  {
						 $ls_cuenta=$this->dts_ingresos_corrientes->getValue("cuenta",$li);
						 $ls_denominacion=$this->dts_ingresos_corrientes->getValue("denominacion",$li);
						 $ld_asignado=$this->dts_ingresos_corrientes->getValue("asignado",$li);
						 $ld_asignado_modificado=$this->dts_ingresos_corrientes->getValue("modificado",$li);
						 $ld_programado=$this->dts_ingresos_corrientes->getValue("programado",$li);
						 $ld_ejecutado=$this->dts_ingresos_corrientes->getValue("ejecutado",$li);
						 $ld_variacion_absoluta=$this->dts_ingresos_corrientes->getValue("absoluto",$li);
						 $ld_variacion_porcentual=$this->dts_ingresos_corrientes->getValue("porcentual",$li);
						 $ld_programado_acumulado=$this->dts_ingresos_corrientes->getValue("programado_acumulado",$li);
						 $ld_ejecutado_acumulado=$this->dts_ingresos_corrientes->getValue("ejecutado_acumulado",$li);
						 $ls_status=$this->dts_ingresos_corrientes->getValue("status",$li);
						 
						 if(($ls_status == "C")&&(substr($ls_cuenta,0,1) == "3"))
						 {
							 //echo $ls_cuenta." - ".$ld_asignado."<br><br>"; 
							 $ld_total_asignado_ic=$ld_total_asignado_ic + $ld_asignado;
							 $ld_total_asignado_modificado_ic=$ld_total_asignado_modificado_ic + $ld_asignado_modificado;
							 $ld_total_programado_ic=$ld_total_programado_ic + $ld_programado;
							 $ld_total_ejecutado_ic=$ld_total_ejecutado_ic + $ld_ejecutado;
							 $ld_total_variacion_absoluta_ic=$ld_total_variacion_absoluta_ic + $ld_variacion_absoluta;
							 $ld_total_variacion_porcentual_ic=$ld_total_variacion_porcentual_ic + $ld_variacion_porcentual;
							 $ld_total_programado_acumulado_ic=$ld_total_programado_acumulado_ic + $ld_programado_acumulado;
							 $ld_total_ejecutado_acumulado_ic=$ld_total_ejecutado_acumulado_ic + $ld_ejecutado_acumulado;
						 }
						 
					  }//for
					  
					  if ($ld_total_programado_ic> 0)
					{
					 $ld_porcentual = ($ld_total_ejecutado_ic/$ld_total_programado_ic)*100;
					}
					else
					{
					 $ld_porcentual = 0;
					}
						
						$this->dts_reporte->insertRow("cuenta","");
						$this->dts_reporte->insertRow("denominacion",'<b>INGRESOS CORRIENTES</b>');
						$this->dts_reporte->insertRow("asignado",$ld_total_asignado_ic);
						$this->dts_reporte->insertRow("modificado",$ld_total_asignado_modificado_ic);
						$this->dts_reporte->insertRow("programado",$ld_total_programado_ic);
						$this->dts_reporte->insertRow("ejecutado", $ld_total_ejecutado_ic);		
						$this->dts_reporte->insertRow("absoluto",abs($ld_total_ejecutado_ic - $ld_total_programado_ic));		
						$this->dts_reporte->insertRow("porcentual",$ld_porcentual);		
						$this->dts_reporte->insertRow("programado_acumulado",$ld_total_programado_acumulado_ic);
						$this->dts_reporte->insertRow("ejecutado_acumulado",$ld_total_ejecutado_acumulado_ic);  
					}  
				 break;
				 
				 case 30:  //INGRESOS CAPITAL
					$ld_total_asignado=0;
					$ld_total_asignado_modificado=0;
					$ld_total_programado=0;
					$ld_total_ejecutado=0;
					$ld_total_variacion_absoluta=0;
					$ld_total_variacion_porcentual=0;
					$ld_total_programado_acumulado=0;
					$ld_total_ejecutado_acumulado=0;
					$li_total=$this->dts_ingresos_capital->getRowCount("cuenta");
					if($li_total>0)
					{
					  for($li=1;$li<=$li_total;$li++)
					  {
						 $ls_cuenta=$this->dts_ingresos_capital->getValue("cuenta",$li);
						 $ls_denominacion=$this->dts_ingresos_capital->getValue("denominacion",$li);
						 $ld_asignado=$this->dts_ingresos_capital->getValue("asignado",$li);
						 $ld_asignado_modificado=$this->dts_ingresos_capital->getValue("modificado",$li);
						 $ld_programado=$this->dts_ingresos_capital->getValue("programado",$li);
						 $ld_ejecutado=$this->dts_ingresos_capital->getValue("ejecutado",$li);
						 $ld_variacion_absoluta=$this->dts_ingresos_capital->getValue("absoluto",$li);
						 $ld_variacion_porcentual=$this->dts_ingresos_capital->getValue("porcentual",$li);
						 $ld_programado_acumulado=$this->dts_ingresos_capital->getValue("programado_acumulado",$li);
						 $ld_ejecutado_acumulado=$this->dts_ingresos_capital->getValue("ejecutado_acumulado",$li);
						 $ls_status=$this->dts_ingresos_capital->getValue("status",$li);
						 if ($ls_status == "C")
						 {
							 $ld_total_asignado=$ld_total_asignado + $ld_asignado;
							 $ld_total_asignado_modificado=$ld_total_asignado_modificado + $ld_asignado_modificado;
							 $ld_total_programado=$ld_total_programado + $ld_programado;
							 $ld_total_ejecutado=$ld_total_ejecutado + $ld_ejecutado;
							 $ld_total_variacion_absoluta=$ld_total_variacion_absoluta + $ld_variacion_absoluta;
							 $ld_total_variacion_porcentual=$ld_total_variacion_porcentual + $ld_variacion_porcentual;
							 $ld_total_programado_acumulado=$ld_total_programado_acumulado + $ld_programado_acumulado;
							 $ld_total_ejecutado_acumulado=$ld_total_ejecutado_acumulado + $ld_ejecutado_acumulado;
						 }
						 
					  }//for
					  
						
					}
					if ($ld_total_programado> 0)
					{
					 $ld_porcentual = ($ld_total_ejecutado/$ld_total_programado)*100;
					}
					else
					{
					 $ld_porcentual = 0;
					}
						
						$this->dts_reporte->insertRow("cuenta","");
						$this->dts_reporte->insertRow("denominacion",'<b>INGRESOS DE CAPITAL</b>');
						$this->dts_reporte->insertRow("asignado",$ld_total_asignado);
						$this->dts_reporte->insertRow("modificado",$ld_total_asignado_modificado);
						$this->dts_reporte->insertRow("programado",$ld_total_programado);
						$this->dts_reporte->insertRow("ejecutado", $ld_total_ejecutado);		
						$this->dts_reporte->insertRow("absoluto",abs($ld_total_ejecutado - $ld_total_programado));		
						$this->dts_reporte->insertRow("porcentual",$ld_porcentual);		
						$this->dts_reporte->insertRow("programado_acumulado",$ld_total_programado_acumulado);
						$this->dts_reporte->insertRow("ejecutado_acumulado",$ld_total_ejecutado_acumulado);  
				 break;
				 
				 case 43:  //INGRESOS FINANCIEROS
					$li_total=$this->dts_ingresos_financieros->getRowCount("cuenta");
					if($li_total>0)
					{
					  for($li=1;$li<=$li_total;$li++)
					  {
						 $ls_cuenta=$this->dts_ingresos_financieros->getValue("cuenta",$li);
						 $ls_denominacion=$this->dts_ingresos_financieros->getValue("denominacion",$li);
						 $ld_asignado=$this->dts_ingresos_financieros->getValue("asignado",$li);
						 $ld_asignado_modificado=$this->dts_ingresos_financieros->getValue("modificado",$li);
						 $ld_programado=$this->dts_ingresos_financieros->getValue("programado",$li);
						 $ld_ejecutado=$this->dts_ingresos_financieros->getValue("ejecutado",$li);
						 $ld_variacion_absoluta=$this->dts_ingresos_financieros->getValue("absoluto",$li);
						 $ld_variacion_porcentual=$this->dts_ingresos_financieros->getValue("porcentual",$li);
						 $ld_programado_acumulado=$this->dts_ingresos_financieros->getValue("programado_acumulado",$li);
						 $ld_ejecutado_acumulado=$this->dts_ingresos_financieros->getValue("ejecutado_acumulado",$li);
						 $ls_status=$this->dts_ingresos_financieros->getValue("status",$li);
			
						 if($ls_status == "C")
						 {
						 $ld_total_asignado_if=$ld_total_asignado_if + $ld_asignado;
						 $ld_total_asignado_modificado_if=$ld_total_asignado_modificado_if + $ld_asignado_modificado;
						 $ld_total_programado_if=$ld_total_programado_if + $ld_programado;
						 $ld_total_ejecutado_if=$ld_total_ejecutado_if + $ld_ejecutado;
						 $ld_total_variacion_absoluta_if=$ld_total_variacion_absoluta_if + $ld_variacion_absoluta;
						 $ld_total_variacion_porcentual_if=$ld_total_variacion_porcentual_if + $ld_variacion_porcentual;
						 $ld_total_programado_acumulado_if=$ld_total_programado_acumulado_if + $ld_programado_acumulado;
						 $ld_total_ejecutado_acumulado_if=$ld_total_ejecutado_acumulado_if + $ld_ejecutado_acumulado;
						 }
						 
					  }//for
					}
					if ($ld_total_programado_if> 0)
					{
					 $ld_porcentual = ($ld_total_ejecutado_if/$ld_total_programado_if)*100;
					}
					else
					{
					 $ld_porcentual = 0;
					}
					$this->dts_reporte->insertRow("cuenta","");
					$this->dts_reporte->insertRow("denominacion",'<b>INGRESOS FINANCIEROS</b>');
					$this->dts_reporte->insertRow("asignado",$ld_total_asignado_if);
					$this->dts_reporte->insertRow("modificado",$ld_total_asignado_modificado_if);
					$this->dts_reporte->insertRow("programado",$ld_total_programado_if);
					$this->dts_reporte->insertRow("ejecutado",$ld_total_ejecutado_if);		
					$this->dts_reporte->insertRow("absoluto",abs($ld_total_ejecutado_if - $ld_total_programado_if));		
					$this->dts_reporte->insertRow("porcentual",$ld_porcentual);		
					$this->dts_reporte->insertRow("programado_acumulado", $ld_total_programado_acumulado_if);
					$this->dts_reporte->insertRow("ejecutado_acumulado",$ld_total_ejecutado_acumulado_if);  
				 break;
				 
				 case 49:
				   $ld_asignado_si=$this->dts_reporte->getValue("asignado",1);
				   $ld_asignado_modificado_si=$this->dts_reporte->getValue("modificado",1);
				   $ld_programado_si=$this->dts_reporte->getValue("programado",1);
				   $ld_ejecutado_si=$this->dts_reporte->getValue("ejecutado",1);
				   $ld_variacion_absoluta_si=$this->dts_reporte->getValue("absoluto",1);
				   $ld_programado_acumulado_si=$this->dts_reporte->getValue("programado_acumulado",1);
				   $ld_ejecutado_acumulado_si=$this->dts_reporte->getValue("ejecutado_acumulado",1);
				  
				   $ld_asignado_i=$this->dts_reporte->getValue("asignado",2);
				   $ld_asignado_modificado_i=$this->dts_reporte->getValue("modificado",2);
				   $ld_programado_i=$this->dts_reporte->getValue("programado",2);
				   $ld_ejecutado_i=$this->dts_reporte->getValue("ejecutado",2);
				   $ld_variacion_absoluta_i=$this->dts_reporte->getValue("absoluto",2);
				   $ld_programado_acumulado_i=$this->dts_reporte->getValue("programado_acumulado",2);
				   $ld_ejecutado_acumulado_i=$this->dts_reporte->getValue("ejecutado_acumulado",2);
				   
				   if (($ld_programado_si+$ld_programado_i)>0)
				   {
					$ld_porcentual = (($ld_ejecutado_si+$ld_ejecutado_i)/($ld_programado_si+$ld_programado_i))*100;
				   }
				   else
				   {
					$ld_porcentual = 0;
				   }
				   
				   $this->dts_reporte->insertRow("cuenta","");
				   $this->dts_reporte->insertRow("denominacion",'<b>SALDO INICIAL + INGRESOS </b>');
				   $this->dts_reporte->insertRow("asignado",$ld_asignado_si+$ld_asignado_i);
				   $this->dts_reporte->insertRow("modificado",$ld_asignado_modificado_si+$ld_asignado_modificado_i);
				   $this->dts_reporte->insertRow("programado",$ld_programado_si+$ld_programado_i);
				   $this->dts_reporte->insertRow("ejecutado",$ld_ejecutado_si+$ld_ejecutado_i);		
				   $this->dts_reporte->insertRow("absoluto",abs(($ld_ejecutado_si+$ld_ejecutado_i)-($ld_programado_si+$ld_programado_i)));		
				   $this->dts_reporte->insertRow("porcentual",$ld_porcentual);		
				   $this->dts_reporte->insertRow("programado_acumulado",$ld_programado_acumulado_si+$ld_programado_acumulado_i);
				   $this->dts_reporte->insertRow("ejecutado_acumulado",$ld_ejecutado_acumulado_si+$ld_ejecutado_acumulado_i);
				   $lb_valido=$this->uf_spg_reporte_total_egresos($adt_fecdes,$adt_fechas,$as_mesdes,$as_meshas);
				   break;
				 
				 case 50:  //EGRESOS DE CONSUMO
					$ld_total_asignado=0;
					$ld_total_asignado_modificado=0;
					$ld_total_programado=0;
					$ld_total_ejecutado=0;
					$ld_total_variacion_absoluta=0;
					$ld_total_variacion_porcentual=0;
					$ld_total_programado_acumulado=0;
					$ld_total_ejecutado_acumulado=0;
					$li_total=$this->dts_egresos_consumo->getRowCount("cuenta");
					if($li_total>0)
					{
					  for($li=1;$li<=$li_total;$li++)
					  {
						 $ls_cuenta=$this->dts_egresos_consumo->getValue("cuenta",$li);
						 $ls_denominacion=$this->dts_egresos_consumo->getValue("denominacion",$li);
						 $ld_asignado=$this->dts_egresos_consumo->getValue("asignado",$li);
						 $ld_asignado_modificado=$this->dts_egresos_consumo->getValue("modificado",$li);
						 $ld_programado=$this->dts_egresos_consumo->getValue("programado",$li);
						 $ld_ejecutado=$this->dts_egresos_consumo->getValue("ejecutado",$li);
						 $ld_variacion_absoluta=$this->dts_egresos_consumo->getValue("absoluto",$li);
						 $ld_variacion_porcentual=$this->dts_egresos_consumo->getValue("porcentual",$li);
						 $ld_programado_acumulado=$this->dts_egresos_consumo->getValue("programado_acumulado",$li);
						 $ld_ejecutado_acumulado=$this->dts_egresos_consumo->getValue("ejecutado_acumulado",$li);
						 $li_nivel=$this->dts_egresos_consumo->getValue("nivel",$li);
						 
						 if($li_nivel == 1)
						 {
							 $ld_total_asignado=$ld_total_asignado + $ld_asignado;
							 $ld_total_asignado_modificado=$ld_total_asignado_modificado + $ld_asignado_modificado;
							 $ld_total_programado=$ld_total_programado + $ld_programado;
							 $ld_total_ejecutado=$ld_total_ejecutado + $ld_ejecutado;
							 $ld_total_variacion_absoluta=$ld_total_variacion_absoluta + $ld_variacion_absoluta;
							 $ld_total_variacion_porcentual=$ld_total_variacion_porcentual + $ld_variacion_porcentual;
							 $ld_total_programado_acumulado=$ld_total_programado_acumulado + $ld_programado_acumulado;
							 $ld_total_ejecutado_acumulado=$ld_total_ejecutado_acumulado + $ld_ejecutado_acumulado;
						 }	 
					  }//for
					if ($ld_total_programado> 0)
					{
					 $ld_porcentual = ($ld_total_ejecutado/$ld_total_programado)*100;
					}
					else
					{
					 $ld_porcentual = 0;
					}
					$this->dts_reporte->insertRow("cuenta","");
					$this->dts_reporte->insertRow("denominacion",'<b>EGRESOS DE CONSUMO</b>');
					$this->dts_reporte->insertRow("asignado",$ld_total_asignado);
					$this->dts_reporte->insertRow("modificado",$ld_total_asignado_modificado);
					$this->dts_reporte->insertRow("programado",$ld_total_programado);
					$this->dts_reporte->insertRow("ejecutado",$ld_total_ejecutado);		
					$this->dts_reporte->insertRow("absoluto",abs($ld_total_ejecutado-$ld_total_programado));		
					$this->dts_reporte->insertRow("porcentual",$ld_porcentual);		
					$this->dts_reporte->insertRow("programado_acumulado",$ld_total_programado_acumulado);
					$this->dts_reporte->insertRow("ejecutado_acumulado",$ld_total_programado_acumulado);
					}  
				 break;
				 
				 case 56:  //EGRESOS CORRIENTES
					$ld_total_asignado=0;
					$ld_total_asignado_modificado=0;
					$ld_total_programado=0;
					$ld_total_ejecutado=0;
					$ld_total_variacion_absoluta=0;
					$ld_total_variacion_porcentual=0;
					$ld_total_programado_acumulado=0;
					$ld_total_ejecutado_acumulado=0;
					$li_total=$this->dts_egresos_corrientes->getRowCount("cuenta");
					if($li_total>0)
					{
					  for($li=1;$li<=$li_total;$li++)
					  {
						 $ls_cuenta=$this->dts_egresos_corrientes->getValue("cuenta",$li);
						 $ls_denominacion=$this->dts_egresos_corrientes->getValue("denominacion",$li);
						 $ld_asignado=$this->dts_egresos_corrientes->getValue("asignado",$li);
						 $ld_asignado_modificado=$this->dts_egresos_corrientes->getValue("modificado",$li);
						 $ld_programado=$this->dts_egresos_corrientes->getValue("programado",$li);
						 $ld_ejecutado=$this->dts_egresos_corrientes->getValue("ejecutado",$li);
						 $ld_variacion_absoluta=$this->dts_egresos_corrientes->getValue("absoluto",$li);
						 $ld_variacion_porcentual=$this->dts_egresos_corrientes->getValue("porcentual",$li);
						 $ld_programado_acumulado=$this->dts_egresos_corrientes->getValue("programado_acumulado",$li);
						 $ld_ejecutado_acumulado=$this->dts_egresos_corrientes->getValue("ejecutado_acumulado",$li);
						 $li_nivel=$this->dts_egresos_corrientes->getValue("nivel",$li);
						 
						 if($li_nivel == 1)
						 {
							 $ld_total_asignado=$ld_total_asignado + $ld_asignado;
							 $ld_total_asignado_modificado=$ld_total_asignado_modificado + $ld_asignado_modificado;
							 $ld_total_programado=$ld_total_programado + $ld_programado;
							 $ld_total_ejecutado=$ld_total_ejecutado + $ld_ejecutado;
							 $ld_total_variacion_absoluta=$ld_total_variacion_absoluta + $ld_variacion_absoluta;
							 $ld_total_variacion_porcentual=$ld_total_variacion_porcentual + $ld_variacion_porcentual;
							 $ld_total_programado_acumulado=$ld_total_programado_acumulado + $ld_programado_acumulado;
							 $ld_total_ejecutado_acumulado=$ld_total_ejecutado_acumulado + $ld_ejecutado_acumulado;
						 }
						 
					  }//for
					}
					if ($ld_total_programado> 0)
					{
					 $ld_porcentual = ($ld_total_ejecutado/$ld_total_programado)*100;
					}
					else
					{
					 $ld_porcentual = 0;
					}
					$this->dts_reporte->insertRow("cuenta","");
					$this->dts_reporte->insertRow("denominacion",'<b>OTROS EGRESOS CORRIENTES</b>');
					$this->dts_reporte->insertRow("asignado",$ld_total_asignado);
					$this->dts_reporte->insertRow("modificado",$ld_total_asignado_modificado);
					$this->dts_reporte->insertRow("programado",$ld_total_programado);
					$this->dts_reporte->insertRow("ejecutado",$ld_total_ejecutado);		
					$this->dts_reporte->insertRow("absoluto",abs($ld_total_ejecutado- $ld_total_programado));		
					$this->dts_reporte->insertRow("porcentual",$ld_porcentual);		
					$this->dts_reporte->insertRow("programado_acumulado",$ld_total_programado_acumulado);
					$this->dts_reporte->insertRow("ejecutado_acumulado",$ld_total_ejecutado_acumulado);  
				 break;
				 
			  }//switch	 
					   $ls_cuenta=$la_cuenta[$i];
					   $li_pos=$this->dts_reporte_temporal->find("cuenta",$ls_cuenta);
					   $ld_asignado_e = 0;
					   $ld_asignado_modificado_e = 0;
					   $ld_programado_e= 0;
					   $ld_ejecutado_e= 0;
					   $ld_variacion_absoluta_e= 0;
					   $ld_variacion_porcentual_e= 0;
					   $ld_programado_acumulado_e= 0;
					   $ld_ejecutado_acumulado_e= 0;
					   
					   if(($li_pos>0)&&($i!=49))
					   { 		
						 $ls_spg_cuenta=$this->dts_reporte_temporal->getValue("cuenta",$li_pos);
						 $ls_denominacion=$this->dts_reporte_temporal->getValue("denominacion",$li_pos);
						 $ld_asignado=$this->dts_reporte_temporal->getValue("asignado",$li_pos);
						 $ld_asignado_modificado=$this->dts_reporte_temporal->getValue("modificado",$li_pos);
						 $ld_programado=$this->dts_reporte_temporal->getValue("programado",$li_pos);
						 $ld_ejecutado=$this->dts_reporte_temporal->getValue("ejecutado",$li_pos);
						 $ld_variacion_absoluta=$this->dts_reporte_temporal->getValue("absoluto",$li_pos);
						 $ld_variacion_porcentual=$this->dts_reporte_temporal->getValue("porcentual",$li_pos);
						 $ld_programado_acumulado=$this->dts_reporte_temporal->getValue("programado_acumulado",$li_pos);
						 $ld_ejecutado_acumulado=$this->dts_reporte_temporal->getValue("ejecutado_acumulado",$li_pos);
						  if (($i == 17)||($i == 18)||($i == 19))
						 {
						  $ld_asignado_vn = $ld_asignado_vn + $ld_asignado;
						  $ld_asignado_modificado_vn = $ld_asignado_modificado_vn + $ld_asignado_modificado;
						  $ld_programado_vn = $ld_programado_vn + $ld_programado;
						  $ld_ejecutado_vn = $ld_ejecutado_vn + $ld_ejecutado;
						  $ld_variacion_absoluta_vn = $ld_variacion_absoluta_vn + $ld_variacion_absoluta;
						  $ld_variacion_porcentual_vn = $ld_variacion_porcentual_vn + $ld_variacion_porcentual;
						  $ld_programado_acumulado_vn = $ld_programado_acumulado_vn + $ld_programado_acumulado;
						  $ld_ejecutado_acumulado_vn = $ld_ejecutado_acumulado_vn + $ld_programado_acumulado;
						 }
						 if($i == 20)
						 {
						  $ld_asignado_e=$this->dts_reporte_temporal->getValue("asignado",$li_pos);
						  $ld_asignado_modificado_e=$this->dts_reporte_temporal->getValue("modificado",$li_pos);
						  $ld_programado_e=$this->dts_reporte_temporal->getValue("programado",$li_pos);
						  $ld_ejecutado_e=$this->dts_reporte_temporal->getValue("ejecutado",$li_pos);
						  $ld_variacion_absoluta_e=$this->dts_reporte_temporal->getValue("absoluto",$li_pos);
						  $ld_variacion_porcentual_e=$this->dts_reporte_temporal->getValue("porcentual",$li_pos);
						  $ld_programado_acumulado_e=$this->dts_reporte_temporal->getValue("programado_acumulado",$li_pos);
						  $ld_ejecutado_acumulado_e=$this->dts_reporte_temporal->getValue("ejecutado_acumulado",$li_pos);
						 }
						  $ld_asignado_vn = $ld_asignado_vn - $ld_asignado_e;
						  $ld_asignado_modificado_vn = $ld_asignado_modificado_vn - $ld_asignado_modificado_e;
						  $ld_programado_vn = $ld_programado_vn - $ld_programado_e;
						  $ld_ejecutado_vn = $ld_ejecutado_vn - $ld_ejecutado_e;
						  $ld_variacion_absoluta_vn = $ld_variacion_absoluta_vn - $ld_variacion_absoluta_e;
						  $ld_variacion_porcentual_vn = $ld_variacion_porcentual_vn - $ld_variacion_porcentual_e;
						  $ld_programado_acumulado_vn = $ld_programado_acumulado_vn - $ld_programado_acumulado_e;
						  $ld_ejecutado_acumulado_vn = $ld_ejecutado_acumulado_vn - $ld_programado_acumulado_e;
						  if($i == 21)
						  {
							  $this->dts_reporte->insertRow("cuenta","");
							  $this->dts_reporte->insertRow("denominacion","Ventas Netas");
							  $this->dts_reporte->insertRow("asignado",$ld_asignado_vn);
							  $this->dts_reporte->insertRow("modificado",ld_asignado_modificado_vn);
							  $this->dts_reporte->insertRow("programado",$ld_programado_vn);
							  $this->dts_reporte->insertRow("ejecutado",$ld_ejecutado_vn);		
							  $this->dts_reporte->insertRow("absoluto",0);		
							  $this->dts_reporte->insertRow("porcentual",0);		
							  $this->dts_reporte->insertRow("programado_acumulado",$ld_programado_acumulado_vn);
							  $this->dts_reporte->insertRow("ejecutado_acumulado",$ld_ejecutado_acumulado_vn);
						  
						 }
						 $this->dts_reporte->insertRow("cuenta",$ls_spg_cuenta);
						 $this->dts_reporte->insertRow("denominacion",$ls_denominacion);
						 $this->dts_reporte->insertRow("asignado",$ld_asignado);
						 $this->dts_reporte->insertRow("modificado",$ld_asignado_modificado);
						 $this->dts_reporte->insertRow("programado",$ld_programado);
						 $this->dts_reporte->insertRow("ejecutado",$ld_ejecutado);		
						 $this->dts_reporte->insertRow("absoluto",$ld_variacion_absoluta);		
						 $this->dts_reporte->insertRow("porcentual",$ld_variacion_porcentual);		
						 $this->dts_reporte->insertRow("programado_acumulado",$ld_programado_acumulado);
						 $this->dts_reporte->insertRow("ejecutado_acumulado",$ld_ejecutado_acumulado);
						 
						  if($i==2) // Detalles del las cuentas 305010100
						  {
							 $li_total=$this->dts_ingresos_corrientes->getRowCount("cuenta");
							 if($li_total>0)
							 {
							  for($li=1;$li<=$li_total;$li++)
							  {
							 
								 $ls_cuenta=$this->dts_ingresos_corrientes->getValue("cuenta",$li);
								 if('305010100'.$ls_ceros != trim($ls_cuenta))
								 {
								  if('3050101' == substr($ls_cuenta,0,7))
								  {
									 $ls_denominacion=$this->dts_ingresos_corrientes->getValue("denominacion",$li);
									 $ld_asignado=$this->dts_ingresos_corrientes->getValue("asignado",$li);
									 $ld_asignado_modificado=$this->dts_ingresos_corrientes->getValue("modificado",$li);
									 $ld_programado=$this->dts_ingresos_corrientes->getValue("programado",$li);
									 $ld_ejecutado=$this->dts_ingresos_corrientes->getValue("ejecutado",$li);
									 $ld_variacion_absoluta=$this->dts_ingresos_corrientes->getValue("absoluto",$li);
									 $ld_variacion_porcentual=$this->dts_ingresos_corrientes->getValue("porcentual",$li);
									 $ld_programado_acumulado=$this->dts_ingresos_corrientes->getValue("programado_acumulado",$li);
									 $ld_ejecutado_acumulado=$this->dts_ingresos_corrientes->getValue("ejecutado_acumulado",$li);
									 
									 $this->dts_reporte->insertRow("cuenta",$ls_cuenta);
									 $this->dts_reporte->insertRow("denominacion",$ls_denominacion);
									 $this->dts_reporte->insertRow("asignado",$ld_asignado);
									 $this->dts_reporte->insertRow("modificado",$ld_asignado_modificado);
									 $this->dts_reporte->insertRow("programado",$ld_programado);
									 $this->dts_reporte->insertRow("ejecutado",$ld_ejecutado);		
									 $this->dts_reporte->insertRow("absoluto",$ld_variacion_absoluta);		
									 $this->dts_reporte->insertRow("porcentual",$ld_variacion_porcentual);		
									 $this->dts_reporte->insertRow("programado_acumulado",$ld_programado_acumulado);
									 $this->dts_reporte->insertRow("ejecutado_acumulado",$ld_ejecutado_acumulado);
								  }
								 }
								 
								 
							  }
							 }
					   }
						  
						  if($i==21) // Detalles del las cuentas 301030000
						  {
							 $li_total=$this->dts_ingresos_corrientes->getRowCount("cuenta");
							 if($li_total>0)
							 {
							  for($li=1;$li<=$li_total;$li++)
							  {
							 
								 $ls_cuenta=$this->dts_ingresos_corrientes->getValue("cuenta",$li);
								 
								 if('301030000'.$ls_ceros != trim($ls_cuenta))
								 {
								  if('30103' == substr($ls_cuenta,0,5))
								  {
									 $ls_denominacion=$this->dts_ingresos_corrientes->getValue("denominacion",$li);
									 $ld_asignado=$this->dts_ingresos_corrientes->getValue("asignado",$li);
									 $ld_asignado_modificado=$this->dts_ingresos_corrientes->getValue("modificado",$li);
									 $ld_programado=$this->dts_ingresos_corrientes->getValue("programado",$li);
									 $ld_ejecutado=$this->dts_ingresos_corrientes->getValue("ejecutado",$li);
									 $ld_variacion_absoluta=$this->dts_ingresos_corrientes->getValue("absoluto",$li);
									 $ld_variacion_porcentual=$this->dts_ingresos_corrientes->getValue("porcentual",$li);
									 $ld_programado_acumulado=$this->dts_ingresos_corrientes->getValue("programado_acumulado",$li);
									 $ld_ejecutado_acumulado=$this->dts_ingresos_corrientes->getValue("ejecutado_acumulado",$li);
									 
									 $this->dts_reporte->insertRow("cuenta",$ls_cuenta);
									 $this->dts_reporte->insertRow("denominacion",$ls_denominacion);
									 $this->dts_reporte->insertRow("asignado",$ld_asignado);
									 $this->dts_reporte->insertRow("modificado",$ld_asignado_modificado);
									 $this->dts_reporte->insertRow("programado",$ld_programado);
									 $this->dts_reporte->insertRow("ejecutado",$ld_ejecutado);		
									 $this->dts_reporte->insertRow("absoluto",$ld_variacion_absoluta);		
									 $this->dts_reporte->insertRow("porcentual",$ld_variacion_porcentual);		
									 $this->dts_reporte->insertRow("programado_acumulado",$ld_programado_acumulado);
									 $this->dts_reporte->insertRow("ejecutado_acumulado",$ld_ejecutado_acumulado);
								  }
								 }
								 
								 
							  }
							 }
					   }
					   
						  if($i==29) // Detalles del las cuentas 301110000
						  {
							 $li_total=$this->dts_ingresos_corrientes->getRowCount("cuenta");
							 if($li_total>0)
							 {
							  for($li=1;$li<=$li_total;$li++)
							  {
							 
								 $ls_cuenta=$this->dts_ingresos_corrientes->getValue("cuenta",$li);
								 
								 if('301110000'.$ls_ceros != trim($ls_cuenta))
								 {
				
								  if('30111' == substr($ls_cuenta,0,5))
								  {
									 $ls_denominacion=$this->dts_ingresos_corrientes->getValue("denominacion",$li);
									 $ld_asignado=$this->dts_ingresos_corrientes->getValue("asignado",$li);
									 $ld_asignado_modificado=$this->dts_ingresos_corrientes->getValue("modificado",$li);
									 $ld_programado=$this->dts_ingresos_corrientes->getValue("programado",$li);
									 $ld_ejecutado=$this->dts_ingresos_corrientes->getValue("ejecutado",$li);
									 $ld_variacion_absoluta=$this->dts_ingresos_corrientes->getValue("absoluto",$li);
									 $ld_variacion_porcentual=$this->dts_ingresos_corrientes->getValue("porcentual",$li);
									 $ld_programado_acumulado=$this->dts_ingresos_corrientes->getValue("programado_acumulado",$li);
									 $ld_ejecutado_acumulado=$this->dts_ingresos_corrientes->getValue("ejecutado_acumulado",$li);
									 
									 $this->dts_reporte->insertRow("cuenta",$ls_cuenta);
									 $this->dts_reporte->insertRow("denominacion",$ls_denominacion);
									 $this->dts_reporte->insertRow("asignado",$ld_asignado);
									 $this->dts_reporte->insertRow("modificado",$ld_asignado_modificado);
									 $this->dts_reporte->insertRow("programado",$ld_programado);
									 $this->dts_reporte->insertRow("ejecutado",$ld_ejecutado);		
									 $this->dts_reporte->insertRow("absoluto",$ld_variacion_absoluta);		
									 $this->dts_reporte->insertRow("porcentual",$ld_variacion_porcentual);		
									 $this->dts_reporte->insertRow("programado_acumulado",$ld_programado_acumulado);
									 $this->dts_reporte->insertRow("ejecutado_acumulado",$ld_ejecutado_acumulado);
								  }
								 }
								 
								 
							  }
							 }
						   }
						   
						   if($i==46) // Detalles de las cuentas 311000000
						  {
							 $li_total=$this->dts_ingresos_financieros->getRowCount("cuenta");
							 if($li_total>0)
							 {
							  for($li=1;$li<=$li_total;$li++)
							  {
							 
								 $ls_cuenta=$this->dts_ingresos_financieros->getValue("cuenta",$li);
								 
								 if('311000000'.$ls_ceros != trim($ls_cuenta))
								 {
								  if('311' == substr($ls_cuenta,0,3))
								  {
									 $ls_denominacion=$this->dts_ingresos_financieros->getValue("denominacion",$li);
									 $ld_asignado=$this->dts_ingresos_financieros->getValue("asignado",$li);
									 $ld_asignado_modificado=$this->dts_ingresos_financieros->getValue("modificado",$li);
									 $ld_programado=$this->dts_ingresos_financieros->getValue("programado",$li);
									 $ld_ejecutado=$this->dts_ingresos_financieros->getValue("ejecutado",$li);
									 $ld_variacion_absoluta=$this->dts_ingresos_financieros->getValue("absoluto",$li);
									 $ld_variacion_porcentual=$this->dts_ingresos_financieros->getValue("porcentual",$li);
									 $ld_programado_acumulado=$this->dts_ingresos_financieros->getValue("programado_acumulado",$li);
									 $ld_ejecutado_acumulado=$this->dts_ingresos_financieros->getValue("ejecutado_acumulado",$li);
									 
									 $this->dts_reporte->insertRow("cuenta",$ls_cuenta);
									 $this->dts_reporte->insertRow("denominacion",$ls_denominacion);
									 $this->dts_reporte->insertRow("asignado",$ld_asignado);
									 $this->dts_reporte->insertRow("modificado",$ld_asignado_modificado);
									 $this->dts_reporte->insertRow("programado",$ld_programado);
									 $this->dts_reporte->insertRow("ejecutado",$ld_ejecutado);		
									 $this->dts_reporte->insertRow("absoluto",$ld_variacion_absoluta);		
									 $this->dts_reporte->insertRow("porcentual",$ld_variacion_porcentual);		
									 $this->dts_reporte->insertRow("programado_acumulado",$ld_programado_acumulado);
									 $this->dts_reporte->insertRow("ejecutado_acumulado",$ld_ejecutado_acumulado);
								  }
								 }
								 
								 
							  }
							 }
					   }
						  
						  if($i==47) // Detalles de las cuentas 312000000
						  {
							 $li_total=$this->dts_ingresos_financieros->getRowCount("cuenta");
							 if($li_total>0)
							 {
							  for($li=1;$li<=$li_total;$li++)
							  {
							 
								 $ls_cuenta=$this->dts_ingresos_financieros->getValue("cuenta",$li);
								 
								 if('312000000'.$ls_ceros != trim($ls_cuenta))
								 {
								  if('312' == substr($ls_cuenta,0,3))
								  {
									 $ls_denominacion=$this->dts_ingresos_financieros->getValue("denominacion",$li);
									 $ld_asignado=$this->dts_ingresos_financieros->getValue("asignado",$li);
									 $ld_asignado_modificado=$this->dts_ingresos_financieros->getValue("modificado",$li);
									 $ld_programado=$this->dts_ingresos_financieros->getValue("programado",$li);
									 $ld_ejecutado=$this->dts_ingresos_financieros->getValue("ejecutado",$li);
									 $ld_variacion_absoluta=$this->dts_ingresos_financieros->getValue("absoluto",$li);
									 $ld_variacion_porcentual=$this->dts_ingresos_financieros->getValue("porcentual",$li);
									 $ld_programado_acumulado=$this->dts_ingresos_financieros->getValue("programado_acumulado",$li);
									 $ld_ejecutado_acumulado=$this->dts_ingresos_financieros->getValue("ejecutado_acumulado",$li);
									 
									 $this->dts_reporte->insertRow("cuenta",$ls_cuenta);
									 $this->dts_reporte->insertRow("denominacion",$ls_denominacion);
									 $this->dts_reporte->insertRow("asignado",$ld_asignado);
									 $this->dts_reporte->insertRow("modificado",$ld_asignado_modificado);
									 $this->dts_reporte->insertRow("programado",$ld_programado);
									 $this->dts_reporte->insertRow("ejecutado",$ld_ejecutado);		
									 $this->dts_reporte->insertRow("absoluto",$ld_variacion_absoluta);		
									 $this->dts_reporte->insertRow("porcentual",$ld_variacion_porcentual);		
									 $this->dts_reporte->insertRow("programado_acumulado",$ld_programado_acumulado);
									 $this->dts_reporte->insertRow("ejecutado_acumulado",$ld_ejecutado_acumulado);
								  }
								 }
								 
								 
							  }
							 }
					   }
						  
						  if($i==48) // Detalles de las cuentas 313000000
						  {
							 $li_total=$this->dts_ingresos_financieros->getRowCount("cuenta");
							 if($li_total>0)
							 {
							  for($li=1;$li<=$li_total;$li++)
							  {
							 
								 $ls_cuenta=$this->dts_ingresos_financieros->getValue("cuenta",$li);
								 
								 if('313000000'.$ls_ceros != trim($ls_cuenta))
								 {
								  if('313' == substr($ls_cuenta,0,3))
								  {
									 $ls_denominacion=$this->dts_ingresos_financieros->getValue("denominacion",$li);
									 $ld_asignado=$this->dts_ingresos_financieros->getValue("asignado",$li);
									 $ld_asignado_modificado=$this->dts_ingresos_financieros->getValue("modificado",$li);
									 $ld_programado=$this->dts_ingresos_financieros->getValue("programado",$li);
									 $ld_ejecutado=$this->dts_ingresos_financieros->getValue("ejecutado",$li);
									 $ld_variacion_absoluta=$this->dts_ingresos_financieros->getValue("absoluto",$li);
									 $ld_variacion_porcentual=$this->dts_ingresos_financieros->getValue("porcentual",$li);
									 $ld_programado_acumulado=$this->dts_ingresos_financieros->getValue("programado_acumulado",$li);
									 $ld_ejecutado_acumulado=$this->dts_ingresos_financieros->getValue("ejecutado_acumulado",$li);
									 
									 $this->dts_reporte->insertRow("cuenta",$ls_cuenta);
									 $this->dts_reporte->insertRow("denominacion",$ls_denominacion);
									 $this->dts_reporte->insertRow("asignado",$ld_asignado);
									 $this->dts_reporte->insertRow("modificado",$ld_asignado_modificado);
									 $this->dts_reporte->insertRow("programado",$ld_programado);
									 $this->dts_reporte->insertRow("ejecutado",$ld_ejecutado);		
									 $this->dts_reporte->insertRow("absoluto",$ld_variacion_absoluta);		
									 $this->dts_reporte->insertRow("porcentual",$ld_variacion_porcentual);		
									 $this->dts_reporte->insertRow("programado_acumulado",$ld_programado_acumulado);
									 $this->dts_reporte->insertRow("ejecutado_acumulado",$ld_ejecutado_acumulado);
								  }
								 }
								 
								 
							  }
							 }
					   }
					   
						  if($i==58) // Detalles de las cuentas 407010300
						  {
							 $li_total=$this->dts_egresos_corrientes->getRowCount("cuenta");
							 if($li_total>0)
							 {
							  for($li=1;$li<=$li_total;$li++)
							  {
							 
								 $ls_cuenta=$this->dts_egresos_corrientes->getValue("cuenta",$li);
								 
								 if('407010300'.$ls_ceros != trim($ls_cuenta))
								 {
								  if('4070103' == substr($ls_cuenta,0,7))
								  {
									 $ls_denominacion=$this->dts_egresos_corrientes->getValue("denominacion",$li);
									 $ld_asignado=$this->dts_egresos_corrientes->getValue("asignado",$li);
									 $ld_asignado_modificado=$this->dts_egresos_corrientes->getValue("modificado",$li);
									 $ld_programado=$this->dts_egresos_corrientes->getValue("programado",$li);
									 $ld_ejecutado=$this->dts_egresos_corrientes->getValue("ejecutado",$li);
									 $ld_variacion_absoluta=$this->dts_egresos_corrientes->getValue("absoluto",$li);
									 $ld_variacion_porcentual=$this->dts_egresos_corrientes->getValue("porcentual",$li);
									 $ld_programado_acumulado=$this->dts_egresos_corrientes->getValue("programado_acumulado",$li);
									 $ld_ejecutado_acumulado=$this->dts_egresos_corrientes->getValue("ejecutado_acumulado",$li);
									 
									 $this->dts_reporte->insertRow("cuenta",$ls_cuenta);
									 $this->dts_reporte->insertRow("denominacion",$ls_denominacion);
									 $this->dts_reporte->insertRow("asignado",$ld_asignado);
									 $this->dts_reporte->insertRow("modificado",$ld_asignado_modificado);
									 $this->dts_reporte->insertRow("programado",$ld_programado);
									 $this->dts_reporte->insertRow("ejecutado",$ld_ejecutado);		
									 $this->dts_reporte->insertRow("absoluto",$ld_variacion_absoluta);		
									 $this->dts_reporte->insertRow("porcentual",$ld_variacion_porcentual);		
									 $this->dts_reporte->insertRow("programado_acumulado",$ld_programado_acumulado);
									 $this->dts_reporte->insertRow("ejecutado_acumulado",$ld_ejecutado_acumulado);
								  }
								 }
								 
								 
							  }
							 }
					   }
					   
						  if($i==65) // Detalles de las cuentas 405000000
						  {
							 $li_total=$this->dts_egresos_corrientes->getRowCount("cuenta");
							 if($li_total>0)
							 {
							  for($li=1;$li<=$li_total;$li++)
							  {
							 
								 $ls_cuenta=$this->dts_egresos_corrientes->getValue("cuenta",$li);
								 
								 if('405000000'.$ls_ceros != trim($ls_cuenta))
								 {
								  if('405' == substr($ls_cuenta,0,3))
								  {
									 $ls_denominacion=$this->dts_egresos_corrientes->getValue("denominacion",$li);
									 $ld_asignado=$this->dts_egresos_corrientes->getValue("asignado",$li);
									 $ld_asignado_modificado=$this->dts_egresos_corrientes->getValue("modificado",$li);
									 $ld_programado=$this->dts_egresos_corrientes->getValue("programado",$li);
									 $ld_ejecutado=$this->dts_egresos_corrientes->getValue("ejecutado",$li);
									 $ld_variacion_absoluta=$this->dts_egresos_corrientes->getValue("absoluto",$li);
									 $ld_variacion_porcentual=$this->dts_egresos_corrientes->getValue("porcentual",$li);
									 $ld_programado_acumulado=$this->dts_egresos_corrientes->getValue("programado_acumulado",$li);
									 $ld_ejecutado_acumulado=$this->dts_egresos_corrientes->getValue("ejecutado_acumulado",$li);
									 
									 $this->dts_reporte->insertRow("cuenta",$ls_cuenta);
									 $this->dts_reporte->insertRow("denominacion",$ls_denominacion);
									 $this->dts_reporte->insertRow("asignado",$ld_asignado);
									 $this->dts_reporte->insertRow("modificado",$ld_asignado_modificado);
									 $this->dts_reporte->insertRow("programado",$ld_programado);
									 $this->dts_reporte->insertRow("ejecutado",$ld_ejecutado);		
									 $this->dts_reporte->insertRow("absoluto",$ld_variacion_absoluta);		
									 $this->dts_reporte->insertRow("porcentual",$ld_variacion_porcentual);		
									 $this->dts_reporte->insertRow("programado_acumulado",$ld_programado_acumulado);
									 $this->dts_reporte->insertRow("ejecutado_acumulado",$ld_ejecutado_acumulado);
								  }
								 }
								 
								 
							  }
							 }
					   }
					   
						  if($i==66) // Detalles de las cuentas 411000000
						  {
							 $li_total=$this->dts_egresos_corrientes->getRowCount("cuenta");
							 if($li_total>0)
							 {
							  for($li=1;$li<=$li_total;$li++)
							  {
							 
								 $ls_cuenta=$this->dts_egresos_corrientes->getValue("cuenta",$li);
								 
								 if('411000000'.$ls_ceros != trim($ls_cuenta))
								 {
								  if('411' == substr($ls_cuenta,0,3))
								  {
									 $ls_denominacion=$this->dts_egresos_corrientes->getValue("denominacion",$li);
									 $ld_asignado=$this->dts_egresos_corrientes->getValue("asignado",$li);
									 $ld_asignado_modificado=$this->dts_egresos_corrientes->getValue("modificado",$li);
									 $ld_programado=$this->dts_egresos_corrientes->getValue("programado",$li);
									 $ld_ejecutado=$this->dts_egresos_corrientes->getValue("ejecutado",$li);
									 $ld_variacion_absoluta=$this->dts_egresos_corrientes->getValue("absoluto",$li);
									 $ld_variacion_porcentual=$this->dts_egresos_corrientes->getValue("porcentual",$li);
									 $ld_programado_acumulado=$this->dts_egresos_corrientes->getValue("programado_acumulado",$li);
									 $ld_ejecutado_acumulado=$this->dts_egresos_corrientes->getValue("ejecutado_acumulado",$li);
									 
									 $this->dts_reporte->insertRow("cuenta",$ls_cuenta);
									 $this->dts_reporte->insertRow("denominacion",$ls_denominacion);
									 $this->dts_reporte->insertRow("asignado",$ld_asignado);
									 $this->dts_reporte->insertRow("modificado",$ld_asignado_modificado);
									 $this->dts_reporte->insertRow("programado",$ld_programado);
									 $this->dts_reporte->insertRow("ejecutado",$ld_ejecutado);		
									 $this->dts_reporte->insertRow("absoluto",$ld_variacion_absoluta);		
									 $this->dts_reporte->insertRow("porcentual",$ld_variacion_porcentual);		
									 $this->dts_reporte->insertRow("programado_acumulado",$ld_programado_acumulado);
									 $this->dts_reporte->insertRow("ejecutado_acumulado",$ld_ejecutado_acumulado);
								  }
								 }
								 
								 
							  }
							 }
					   }
					   
						  if($i==67) // Detalles de las cuentas 412000000
						  {
							 $li_total=$this->dts_egresos_corrientes->getRowCount("cuenta");
							 if($li_total>0)
							 {
							  for($li=1;$li<=$li_total;$li++)
							  {
							 
								 $ls_cuenta=$this->dts_egresos_corrientes->getValue("cuenta",$li);
								 
								 if('412000000'.$ls_ceros != trim($ls_cuenta))
								 {
								  if('412' == substr($ls_cuenta,0,3))
								  {
									 $ls_denominacion=$this->dts_egresos_corrientes->getValue("denominacion",$li);
									 $ld_asignado=$this->dts_egresos_corrientes->getValue("asignado",$li);
									 $ld_asignado_modificado=$this->dts_egresos_corrientes->getValue("modificado",$li);
									 $ld_programado=$this->dts_egresos_corrientes->getValue("programado",$li);
									 $ld_ejecutado=$this->dts_egresos_corrientes->getValue("ejecutado",$li);
									 $ld_variacion_absoluta=$this->dts_egresos_corrientes->getValue("absoluto",$li);
									 $ld_variacion_porcentual=$this->dts_egresos_corrientes->getValue("porcentual",$li);
									 $ld_programado_acumulado=$this->dts_egresos_corrientes->getValue("programado_acumulado",$li);
									 $ld_ejecutado_acumulado=$this->dts_egresos_corrientes->getValue("ejecutado_acumulado",$li);
									 
									 $this->dts_reporte->insertRow("cuenta",$ls_cuenta);
									 $this->dts_reporte->insertRow("denominacion",$ls_denominacion);
									 $this->dts_reporte->insertRow("asignado",$ld_asignado);
									 $this->dts_reporte->insertRow("modificado",$ld_asignado_modificado);
									 $this->dts_reporte->insertRow("programado",$ld_programado);
									 $this->dts_reporte->insertRow("ejecutado",$ld_ejecutado);		
									 $this->dts_reporte->insertRow("absoluto",$ld_variacion_absoluta);		
									 $this->dts_reporte->insertRow("porcentual",$ld_variacion_porcentual);		
									 $this->dts_reporte->insertRow("programado_acumulado",$ld_programado_acumulado);
									 $this->dts_reporte->insertRow("ejecutado_acumulado",$ld_ejecutado_acumulado);
								  }
								 }
								 
								 
							  }
							 }
					   }
					  }//if
					  else
					  {
						 $ls_denom="";
						 $lb_valido=$this->uf_spg_reportes_select_denominacion($ls_cuenta,$ls_denom);
						 if($lb_valido)
						 {
							 if($i == 20)
							 {
							  $ls_denom = " Menos: ".$ls_denom;
							  $this->dts_reporte->insertRow("cuenta",$ls_cuenta);
							  $this->dts_reporte->insertRow("denominacion",$ls_denom);
							  $this->dts_reporte->insertRow("asignado",0);
							  $this->dts_reporte->insertRow("modificado",0);
							  $this->dts_reporte->insertRow("programado",0);
							  $this->dts_reporte->insertRow("ejecutado",0);		
							  $this->dts_reporte->insertRow("absoluto",0);		
							  $this->dts_reporte->insertRow("porcentual",0);		
							  $this->dts_reporte->insertRow("programado_acumulado",0);
							  $this->dts_reporte->insertRow("ejecutado_acumulado",0);
							 }
							 else
							 {
							  if($i!=49)
							  { 
							   if($i == 21)
							   {
								  $this->dts_reporte->insertRow("cuenta","");
								  $this->dts_reporte->insertRow("denominacion","Ventas Netas");
								  $this->dts_reporte->insertRow("asignado",$ld_asignado_vn);
								  $this->dts_reporte->insertRow("modificado",$ld_asignado_modificado_vn);
								  $this->dts_reporte->insertRow("programado",$ld_programado_vn);
								  $this->dts_reporte->insertRow("ejecutado",$ld_ejecutado_vn);		
								  $this->dts_reporte->insertRow("absoluto",0);		
								  $this->dts_reporte->insertRow("porcentual",0);		
								  $this->dts_reporte->insertRow("programado_acumulado",$ld_programado_acumulado_vn);
								  $this->dts_reporte->insertRow("ejecutado_acumulado",$ld_ejecutado_acumulado_vn);  
							   }
							   
							   $this->dts_reporte->insertRow("cuenta",$ls_cuenta);
							   $this->dts_reporte->insertRow("denominacion",$ls_denom);
							   $this->dts_reporte->insertRow("asignado",0);
							   $this->dts_reporte->insertRow("modificado",0);
							   $this->dts_reporte->insertRow("programado",0);
							   $this->dts_reporte->insertRow("ejecutado",0);		
							   $this->dts_reporte->insertRow("absoluto",0);		
							   $this->dts_reporte->insertRow("porcentual",0);		
							   $this->dts_reporte->insertRow("programado_acumulado",0);
							   $this->dts_reporte->insertRow("ejecutado_acumulado",0);
							  } 
							 }
						 }//if
					  }//else
					 
					  
			}		
		 }	  
					 
	  return $lb_valido;
	}//fin uf_spg_reportes_presupuesto_de_caja
	

	function uf_spi_reportes_ingresos_corrientes($adt_fecdes,$adt_fechas,$adts_datastore,$as_mesdes,$as_meshas)
    {////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function : uf_spi_reportes_ingresos_corrientes
	 //     Argumentos : adt_fecdes ... adt_fechas  // rango de fecha del reporte
	 //                  adts_datastore  // datastore que imprime el reporte
     //	       Returns : Retorna true o false si se realizo la consulta para el reporte
	 //	   Description : Reporte que genera salida del Presupuesto de Caja
	 //     Creado por : Ing. Arnaldo Suarez
	 // Fecha Creación : 18/06/2008                       Fecha última Modificacion :      Hora :
  	 ///////////////////////////////////////////////////////////////////////////////////////////////////////
	  $lb_valido=true;
	  $ls_sql=" SELECT spi_cuenta,max(denominacion) as denominacion,max(status) as status,        ".
              "        sum(previsto) as previsto,                                                       			".
			  "        sum(enero+febrero+marzo) as trimestrei, sum(abril+mayo+junio) as trimestreii,       			".
			  "        sum(julio+agosto+septiembre) as trimestreiii,                                     		 	".
			  "        sum(octubre+noviembre+diciembre) as trimestreiv                                    			".
	  	      " FROM   spi_cuentas ".
			  " WHERE  codemp='".$this->ls_codemp."' AND ".
			  "        (spi_cuenta like '30501%' OR spi_cuenta like '3050101%' OR spi_cuenta like '3050103%' ".
			  "        OR spi_cuenta like '305010301%' OR".
			  "        spi_cuenta like '305010302%' OR spi_cuenta like '305010303%' OR spi_cuenta like '305010304%' OR ".
			  "        spi_cuenta like '305010305%' OR spi_cuenta like '305010306%' OR spi_cuenta like '305010307%' OR ".
			  "        spi_cuenta like '305010308%' OR spi_cuenta like '305010309%' OR spi_cuenta like '3050105%' OR ".
			  "        spi_cuenta like '305010501%' OR spi_cuenta like '305010502%' OR spi_cuenta like '305010503%' OR ".
			  "        spi_cuenta like '30109%' OR spi_cuenta like '3010901%' OR spi_cuenta like '3010902%' OR ".
			  "        spi_cuenta like '3010999%') ". 
			 // " 	   status = 'C' ".
			  "GROUP BY spi_cuenta ".
			  "ORDER BY spi_cuenta";  
	  //echo $ls_sql."<br>";
	  $rs_data=$this->io_sql->select($ls_sql);
	  if($rs_data===false)
	  { // error interno sql
		$this->io_mensajes->message("CLASE->sigesp_spi_class_reportes_instructivos ". 
			                        "MÉTODO->uf_spi_reportes_ingresos_corrientes ".
									"ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		$lb_valido = false;
 	  }
	  else
	  {
		$li_numrows=$this->io_sql->num_rows($rs_data);	
		if($li_numrows>=0)
		{
		     while(!$rs_data->EOF)
			 {
			   $ls_spi_cuenta=$rs_data->fields["spi_cuenta"];
			   $ls_denominacion=$rs_data->fields["denominacion"];
			   $ls_status=$rs_data->fields["status"];
			   $ld_previsto=$rs_data->fields["previsto"];
			   $ld_trimetreI=$rs_data->fields["trimestrei"]; 
			   $ld_trimetreII=$rs_data->fields["trimestreii"]; 
			   $ld_trimetreIII=$rs_data->fields["trimestreiii"]; 
			   $ld_trimetreIV=$rs_data->fields["trimestreiv"]; 
			   $ls_detallar=true;
			   $ld_recaudado = 0;
			   $ld_cobrado= 0;
			   $ld_devengado= 0;
			   $ld_aumento= 0;
			   $ld_disminucion= 0; 
			   $ld_recaudado_acumulado= 0;
			   $ld_cobrado_acumulado= 0;
			   $ld_devengado_acumulado= 0;
			   $ld_aumento_acumulado= 0;
			   $ld_disminucion_acumulado= 0;

			   $lb_valido=$this->uf_spi_ejecutado_trimestral($ls_spi_cuenta,$adt_fecdes,$adt_fechas,&$ld_recaudado,
			                                                 &$ld_cobrado,&$ld_devengado,&$ld_aumento,&$ld_disminucion);
			   if($lb_valido)
		       {
				   $lb_valido=$this->uf_spi_ejecutado_acumulado($ls_spi_cuenta,$adt_fechas,&$ld_recaudado_acumulado,
																&$ld_cobrado_acumulado,&$ld_devengado_acumulado,
																&$ld_aumento_acumulado,&$ld_disminucion_acumulado);
			   }//if
			   if($as_mesdes=='Enero')
		       {
				   $ld_programado_trimestral=$ld_trimetreI;
				   $ld_programado_acumulado=$ld_trimetreI;
			   }//if
			   if($as_mesdes=='Abril')
		       {
				   $ld_programado_trimestral=$ld_trimetreII;
				   $ld_programado_acumulado=$ld_trimetreI+$ld_trimetreII;
			   }//if
			   if($as_mesdes=='Julio')
		       {
				   $ld_programado_trimestral=$ld_trimetreIII;
				   $ld_programado_acumulado=$ld_trimetreI+$ld_trimetreII+$ld_trimetreIII;
			   }//if
			   if($as_mesdes=='Octubre')
		       {
				   $ld_programado_trimestral=$ld_trimetreIV;
				   $ld_programado_acumulado=$ld_trimetreI+$ld_trimetreII+$ld_trimetreIII+$ld_trimetreIV;
			   }//if
			   $ld_previsto_modificado=$ld_previsto+$ld_aumento_acumulado-$ld_disminucion_acumulado;
			   if ($ld_programado_trimestral > 0)
			   {
			    $ld_porcentual =($ld_cobrado/$ld_programado_trimestral)*100;
			   }
			   else
			   {
			    $ld_porcentual =0;
			   }
			   $this->dts_ingresos_corrientes->insertRow("cuenta",$ls_spi_cuenta);
			   $this->dts_ingresos_corrientes->insertRow("denominacion",$ls_denominacion);
			   $this->dts_ingresos_corrientes->insertRow("asignado",$ld_previsto);
			   $this->dts_ingresos_corrientes->insertRow("modificado",$ld_previsto_modificado);
			   $this->dts_ingresos_corrientes->insertRow("programado",$ld_programado_trimestral);
		  	   $this->dts_ingresos_corrientes->insertRow("ejecutado",$ld_cobrado);		
		  	   $this->dts_ingresos_corrientes->insertRow("absoluto",abs($ld_cobrado-$ld_programado_trimestral));		
		  	   $this->dts_ingresos_corrientes->insertRow("porcentual", $ld_porcentual);		
			   $this->dts_ingresos_corrientes->insertRow("programado_acumulado",$ld_programado_acumulado);
		  	   $this->dts_ingresos_corrientes->insertRow("ejecutado_acumulado",$ld_cobrado_acumulado);
			   $this->dts_ingresos_corrientes->insertRow("status",$ls_status);
			   /// datastore  del reportes
			   $this->dts_reporte_temporal->insertRow("cuenta",$ls_spi_cuenta);
			   $this->dts_reporte_temporal->insertRow("denominacion",$ls_denominacion);
			   $this->dts_reporte_temporal->insertRow("asignado",$ld_previsto);
			   $this->dts_reporte_temporal->insertRow("modificado",$ld_previsto_modificado);
			   $this->dts_reporte_temporal->insertRow("programado",$ld_programado_trimestral);
		  	   $this->dts_reporte_temporal->insertRow("ejecutado",$ld_cobrado);		
		  	   $this->dts_reporte_temporal->insertRow("absoluto",abs($ld_cobrado-$ld_programado_trimestral));		
		  	   $this->dts_reporte_temporal->insertRow("porcentual", $ld_porcentual);		
			   $this->dts_reporte_temporal->insertRow("programado_acumulado",$ld_programado_acumulado);
		  	   $this->dts_reporte_temporal->insertRow("ejecutado_acumulado",$ld_cobrado_acumulado);
			   $lb_valido=true;
			   $rs_data->MoveNext();
		    }//while
	    }//if	
	    $this->io_sql->free_result($rs_data);
	  }//else
	  
	   $ls_sql=" SELECT spg_cuenta, max(denominacion) as denominacion, max(status) as status,". 
	           "        sum(asignado) as asignado, ".
			   "        sum(enero+febrero+marzo) as trimestrei, sum(abril+mayo+junio) as trimestreii,       ".
			   "        sum(julio+agosto+septiembre) as trimestreiii,                                      ".
			   "        sum(octubre+noviembre+diciembre) as trimestreiv                                    ".
	  	       " FROM   spg_cuentas ".
			   " WHERE  codemp='".$this->ls_codemp."' AND ".
			   "        spg_cuenta like '40807%' ".
			   " GROUP BY spg_cuenta".
			   " ORDER BY spg_cuenta ";	  
	  $rs_data=$this->io_sql->select($ls_sql);
	  if($rs_data===false)
	  { // error interno sql
		$this->io_mensajes->message("CLASE->sigesp_spi_class_reportes_instructivos ". 
			                        "MÉTODO->uf_spi_reportes_ingresos_corrientes ".
									"ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		$lb_valido = false;
 	  }
	  else
	  {
		$li_numrows=$this->io_sql->num_rows($rs_data);	
		if($li_numrows>=0)
		{
		     while(!$rs_data->EOF)
			 {
			   $ls_spg_cuenta=$rs_data->fields["spg_cuenta"];
			   //$ls_spg_cuenta = substr($ls_spg_cuenta,0,9);
			   $ls_denominacion=$rs_data->fields["denominacion"];
			   $ls_status=$rs_data->fields["status"];
			   $ld_asignado=$rs_data->fields["asignado"];
			   $ld_trimetreI=$rs_data->fields["trimestrei"]; 
			   $ld_trimetreII=$rs_data->fields["trimestreii"]; 
			   $ld_trimetreIII=$rs_data->fields["trimestreiii"]; 
			   $ld_trimetreIV=$rs_data->fields["trimestreiv"]; 
			   $ls_detallar=false; 
			   $ld_recaudado = 0;
			   $ld_cobrado= 0;
			   $ld_devengado= 0;
			   $ld_aumento= 0;
			   $ld_disminucion= 0; 
			   $ld_recaudado_acumulado= 0;
			   $ld_cobrado_acumulado= 0;
			   $ld_devengado_acumulado= 0;
			   $ld_aumento_acumulado= 0;
			   $ld_disminucion_acumulado= 0;
			   $lb_valido=$this->uf_spg_ejecutado_trimestral_estado_resultado($ls_spg_cuenta,$adt_fecdes,$adt_fechas,
			                                                                  &$ld_comprometer,&$ld_causado,&$ld_pagado,
																			  &$ld_aumento,&$ld_disminucion,$ls_detallar);
			   if($lb_valido)
		       {
				   $lb_valido=$this->uf_spg_ejecutado_acumulado_estado_resultado($ls_spg_cuenta,$adt_fechas,&$ld_comprometer_acumulado,
				                                                                 &$ld_causado_acumulado,&$ld_pagado_acumulado,
																				 &$ld_aumento_acumulado,&$ld_disminucion_acumulado,
																                 $ls_detallar);
			   }//if
			   if($as_mesdes=='Enero')
		       {
				   $ld_programado_trimestral=$ld_trimetreI;
				   $ld_programado_acumulado=$ld_trimetreI;
			   }//if
			   if($as_mesdes=='Abril')
		       {
				   $ld_programado_trimestral=$ld_trimetreII;
				   $ld_programado_acumulado=$ld_trimetreI+$ld_trimetreII;
			   }//if
			   if($as_mesdes=='Julio')
		       {
				   $ld_programado_trimestral=$ld_trimetreIII;
				   $ld_programado_acumulado=$ld_trimetreI+$ld_trimetreII+$ld_trimetreIII;
			   }//if
			   if($as_mesdes=='Octubre')
		       {
				   $ld_programado_trimestral=$ld_trimetreIV;
				   $ld_programado_acumulado=$ld_trimetreI+$ld_trimetreII+$ld_trimetreIII+$ld_trimetreIV;
			   }//if
			   $ld_asignado_modificado=$ld_asignado+$ld_aumento_acumulado-$ld_disminucion_acumulado;
			    if ($ld_programado_trimestral > 0)
			   {
			    $ld_porcentual =($ld_causado/$ld_programado_trimestral)*100;
			   }
			   else
			   {
			    $ld_porcentual =0;
			   }
			   $this->dts_ingresos_corrientes->insertRow("cuenta",$ls_spg_cuenta);
			   $this->dts_ingresos_corrientes->insertRow("denominacion",$ls_denominacion);
			   $this->dts_ingresos_corrientes->insertRow("asignado",$ld_asignado);
			   $this->dts_ingresos_corrientes->insertRow("modificado",$ld_asignado_modificado);
			   $this->dts_ingresos_corrientes->insertRow("programado",$ld_programado_trimestral);
		  	   $this->dts_ingresos_corrientes->insertRow("ejecutado",$ld_causado);		
		  	   $this->dts_ingresos_corrientes->insertRow("absoluto",abs($ld_causado-$ld_programado_trimestral));		
		  	   $this->dts_ingresos_corrientes->insertRow("porcentual", $ld_porcentual);		
			   $this->dts_ingresos_corrientes->insertRow("programado_acumulado",$ld_programado_acumulado);
		  	   $this->dts_ingresos_corrientes->insertRow("ejecutado_acumulado",$ld_causado_acumulado);
			   $this->dts_ingresos_corrientes->insertRow("status",$ls_status);
			   /// datastore  del reportes
			   $this->dts_reporte_temporal->insertRow("cuenta",$ls_spg_cuenta);
			   $this->dts_reporte_temporal->insertRow("denominacion",$ls_denominacion);
			   $this->dts_reporte_temporal->insertRow("asignado",$ld_asignado);
			   $this->dts_reporte_temporal->insertRow("modificado",$ld_asignado_modificado);
			   $this->dts_reporte_temporal->insertRow("programado",$ld_programado_trimestral);
		  	   $this->dts_reporte_temporal->insertRow("ejecutado",$ld_causado);		
		  	   $this->dts_reporte_temporal->insertRow("absoluto",abs($ld_causado-$ld_programado_trimestral));		
		  	   $this->dts_reporte_temporal->insertRow("porcentual", $ld_porcentual);		
			   $this->dts_reporte_temporal->insertRow("programado_acumulado",$ld_programado_acumulado);
		  	   $this->dts_reporte_temporal->insertRow("ejecutado_acumulado",$ld_causado_acumulado);
			   $lb_valido=true;
			   $rs_data->MoveNext();
		    }//while
	    }//if	
	    $this->io_sql->free_result($rs_data);
	  }//else
	  
	  $ls_sql=" SELECT spi_cuenta, max(denominacion) as denominacion,                  ".
              "        sum(previsto) as previsto, max(status) as status,                                  ".
			  "        sum(enero+febrero+marzo) as trimestrei, sum(abril+mayo+junio) as trimestreii,       ".
			  "        sum(julio+agosto+septiembre) as trimestreiii,                                      ".
			  "        sum(octubre+noviembre+diciembre) as trimestreiv                                    ".
	  	      " FROM   spi_cuentas ".
			  " WHERE  codemp='".$this->ls_codemp."' AND ".
			  "        spi_cuenta like '30103%' OR spi_cuenta like '30104%' OR spi_cuenta like '30105%' ".
			  "        OR spi_cuenta like '30110%' OR".
			  "        spi_cuenta like '301100401%' OR spi_cuenta like '3011004%' OR spi_cuenta like '3011005%' OR ".
			  "        spi_cuenta like '3011008%' OR spi_cuenta like '30111%' ".
			  "GROUP BY spi_cuenta ".
			  "ORDER BY spi_cuenta ";  	  
	  $rs_data=$this->io_sql->select($ls_sql);
	  if($rs_data===false)
	  { // error interno sql
		$this->io_mensajes->message("CLASE->sigesp_spi_class_reportes_instructivos ". 
			                        "MÉTODO->uf_spi_reportes_ingresos_corrientes ".
									"ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		$lb_valido = false;
 	  }
	  else
	  {
		$li_numrows=$this->io_sql->num_rows($rs_data);	
		if($li_numrows>=0)
		{
		     while(!$rs_data->EOF)
			 {
			   $ls_spi_cuenta=$rs_data->fields["spi_cuenta"];
			   //$ls_spi_cuenta = substr($ls_spi_cuenta,0,9);
			   $ls_denominacion=$rs_data->fields["denominacion"];
			   $ld_previsto=$rs_data->fields["previsto"];
			   $ld_trimetreI=$rs_data->fields["trimestrei"]; 
			   $ld_trimetreII=$rs_data->fields["trimestreii"]; 
			   $ld_trimetreIII=$rs_data->fields["trimestreiii"]; 
			   $ld_trimetreIV=$rs_data->fields["trimestreiv"];
			   $ls_status=$rs_data->fields["status"]; 
               $ls_detallar = true;
			   $ld_recaudado = 0;
			   $ld_cobrado= 0;
			   $ld_devengado= 0;
			   $ld_aumento= 0;
			   $ld_disminucion= 0; 
			   $ld_recaudado_acumulado= 0;
			   $ld_cobrado_acumulado= 0;
			   $ld_devengado_acumulado= 0;
			   $ld_aumento_acumulado= 0;
			   $ld_disminucion_acumulado= 0;
			   $lb_valido=$this->uf_spi_ejecutado_trimestral($ls_spi_cuenta,$adt_fecdes,$adt_fechas,&$ld_recaudado,
			                                                 &$ld_cobrado,&$ld_devengado,&$ld_aumento,&$ld_disminucion);
			   if($lb_valido)
		       {
				   $lb_valido=$this->uf_spi_ejecutado_acumulado($ls_spi_cuenta,$adt_fechas,&$ld_recaudado_acumulado,
																&$ld_cobrado_acumulado,&$ld_devengado_acumulado,
																&$ld_aumento_acumulado,&$ld_disminucion_acumulado);
			   }//if
			   if($as_mesdes=='Enero')
		       {
				   $ld_programado_trimestral=$ld_trimetreI;
				   $ld_programado_acumulado=$ld_trimetreI;
			   }//if
			   if($as_mesdes=='Abril')
		       {
				   $ld_programado_trimestral=$ld_trimetreII;
				   $ld_programado_acumulado=$ld_trimetreI+$ld_trimetreII;
			   }//if
			   if($as_mesdes=='Julio')
		       {
				   $ld_programado_trimestral=$ld_trimetreIII;
				   $ld_programado_acumulado=$ld_trimetreI+$ld_trimetreII+$ld_trimetreIII;
			   }//if
			   if($as_mesdes=='Octubre')
		       {
				   $ld_programado_trimestral=$ld_trimetreIV;
				   $ld_programado_acumulado=$ld_trimetreI+$ld_trimetreII+$ld_trimetreIII+$ld_trimetreIV;
			   }//if
			   $ld_previsto_modificado=$ld_previsto+$ld_aumento_acumulado-$ld_disminucion_acumulado;
			   if ($ld_programado_trimestral > 0)
			   {
			    $ld_porcentual =($ld_cobrado/$ld_programado_trimestral)*100;
			   }
			   else
			   {
			    $ld_porcentual =0;
			   }
			   //echo "3.- ".$ls_spi_cuenta." - ".$ls_status." - ".$ld_previsto."<br><br>";
			   $this->dts_ingresos_corrientes->insertRow("cuenta",$ls_spi_cuenta);
			   $this->dts_ingresos_corrientes->insertRow("denominacion",$ls_denominacion);
			   $this->dts_ingresos_corrientes->insertRow("asignado",$ld_previsto);
			   $this->dts_ingresos_corrientes->insertRow("modificado",$ld_previsto_modificado);
			   $this->dts_ingresos_corrientes->insertRow("programado",$ld_programado_trimestral);
		  	   $this->dts_ingresos_corrientes->insertRow("ejecutado",$ld_cobrado);		
		  	   $this->dts_ingresos_corrientes->insertRow("absoluto",abs($ld_cobrado-$ld_programado_trimestral));		
		  	   $this->dts_ingresos_corrientes->insertRow("porcentual", $ld_porcentual);		
			   $this->dts_ingresos_corrientes->insertRow("programado_acumulado",$ld_programado_acumulado);
		  	   $this->dts_ingresos_corrientes->insertRow("ejecutado_acumulado",$ld_cobrado_acumulado);
			   $this->dts_ingresos_corrientes->insertRow("status",$ls_status);
			   /// datastore  del reportes
			   $this->dts_reporte_temporal->insertRow("cuenta",$ls_spi_cuenta);
			   $this->dts_reporte_temporal->insertRow("denominacion",$ls_denominacion);
			   $this->dts_reporte_temporal->insertRow("asignado",$ld_previsto);
			   $this->dts_reporte_temporal->insertRow("modificado",$ld_previsto_modificado);
			   $this->dts_reporte_temporal->insertRow("programado",$ld_programado_trimestral);
		  	   $this->dts_reporte_temporal->insertRow("ejecutado",$ld_cobrado);		
		  	   $this->dts_reporte_temporal->insertRow("absoluto",abs($ld_cobrado-$ld_programado_trimestral));		
		  	   $this->dts_reporte_temporal->insertRow("porcentual", $ld_porcentual);		
			   $this->dts_reporte_temporal->insertRow("programado_acumulado",$ld_programado_acumulado);
		  	   $this->dts_reporte_temporal->insertRow("ejecutado_acumulado",$ld_cobrado_acumulado);
			   $lb_valido=true;
			   $rs_data->MoveNext();
		    }//while
	    }//if	
	    $this->io_sql->free_result($rs_data);
	  }//else
    return $lb_valido;
    }//fin uf_spg_reportes_gastos_corrientes
	

	function uf_spi_reportes_ingresos_capital($adt_fecdes,$adt_fechas,$adts_datastore,$as_mesdes,$as_meshas)
    {////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function : uf_spi_reportes_ingresos_capital
	 //     Argumentos : adt_fecdes ... adt_fechas  // rango de fecha del reporte
	 //                  adts_datastore  // datastore que imprime el reporte
     //	       Returns : Retorna true o false si se realizo la consulta para el reporte
	 //	   Description : Reporte que genera salida del PResupuesto de Caja
	 //     Creado por : Ing. Arnaldo Suarez
	 // Fecha Creación : 18/06/2008                       Fecha última Modificacion :      Hora :
  	 ///////////////////////////////////////////////////////////////////////////////////////////////////////
	  $lb_valido=true;
	  $ls_sql=" SELECT spi_cuenta,max(denominacion) as denominacion, max(status) as status,                ".
              "        sum(previsto) as previsto,                                                          ".
			  "        sum(enero+febrero+marzo) as trimestrei, sum(abril+mayo+junio) as trimestreii,       ".
			  "        sum(julio+agosto+septiembre) as trimestreiii,                                       ".
			  "        sum(octubre+noviembre+diciembre) as trimestreiv                                     ".
	  	      " FROM   spi_cuentas ".
			  " WHERE  codemp='".$this->ls_codemp."' AND ".
			  "        spi_cuenta like '305020000%' OR spi_cuenta like '305020100%' OR spi_cuenta like '305020300%' OR  ".
			  "        spi_cuenta like '305020301%' OR spi_cuenta like '305020302%' OR spi_cuenta like '305020308%' OR ".
			  "        spi_cuenta like '305020309%' OR spi_cuenta like '305020500%' OR spi_cuenta like '305020501%' OR ".
			  "        spi_cuenta like '305020502%' OR spi_cuenta like '305020503%' OR spi_cuenta like '306010000%' OR ".
			  "        spi_cuenta like '306020000%' ".
			  " GROUP BY spi_cuenta";  	  
	  $rs_data=$this->io_sql->select($ls_sql);
	  if($rs_data===false)
	  { // error interno sql
		$this->io_mensajes->message("CLASE->sigesp_spi_class_reportes_instructivos ". 
			                        "MÉTODO->uf_spi_reportes_ingresos_capital ".
									"ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		$lb_valido = false;
 	  }
	  else
	  {
		$li_numrows=$this->io_sql->num_rows($rs_data);	
		if($li_numrows>=0)
		{
		     while(!$rs_data->EOF)
			 {
			   $ls_spi_cuenta=$rs_data->fields["spi_cuenta"];
			   //$ls_spi_cuenta = substr($ls_spi_cuenta,0,9);
			   $ls_denominacion=$rs_data->fields["denominacion"];
			   $ls_status=$rs_data->fields["status"];
			   $ld_previsto=$rs_data->fields["previsto"];
			   $ld_trimetreI=$rs_data->fields["trimestrei"]; 
			   $ld_trimetreII=$rs_data->fields["trimestreii"]; 
			   $ld_trimetreIII=$rs_data->fields["trimestreiii"]; 
			   $ld_trimetreIV=$rs_data->fields["trimestreiv"]; 
			   $ls_detallar=false;
			   $ld_recaudado = 0;
			   $ld_cobrado= 0;
			   $ld_devengado= 0;
			   $ld_aumento= 0;
			   $ld_disminucion= 0; 
			   $ld_recaudado_acumulado= 0;
			   $ld_cobrado_acumulado= 0;
			   $ld_devengado_acumulado= 0;
			   $ld_aumento_acumulado= 0;
			   $ld_disminucion_acumulado= 0; 
			   $lb_valido=$this->uf_spi_ejecutado_trimestral($ls_spi_cuenta,$adt_fecdes,$adt_fechas,&$ld_recaudado,
			                                                 &$ld_cobrado,&$ld_devengado,&$ld_aumento,&$ld_disminucion);
			   if($lb_valido)
		       {
				   $lb_valido=$this->uf_spi_ejecutado_acumulado($ls_spi_cuenta,$adt_fechas,&$ld_recaudado_acumulado,
																&$ld_cobrado_acumulado,&$ld_devengado_acumulado,
																&$ld_aumento_acumulado,&$ld_disminucion_acumulado);
			   }//if
			   if($as_mesdes=='Enero')
		       {
				   $ld_programado_trimestral=$ld_trimetreI;
				   $ld_programado_acumulado=$ld_trimetreI;
			   }//if
			   if($as_mesdes=='Abril')
		       {
				   $ld_programado_trimestral=$ld_trimetreII;
				   $ld_programado_acumulado=$ld_trimetreI+$ld_trimetreII;
			   }//if
			   if($as_mesdes=='Julio')
		       {
				   $ld_programado_trimestral=$ld_trimetreIII;
				   $ld_programado_acumulado=$ld_trimetreI+$ld_trimetreII+$ld_trimetreIII;
			   }//if
			   if($as_mesdes=='Octubre')
		       {
				   $ld_programado_trimestral=$ld_trimetreIV;
				   $ld_programado_acumulado=$ld_trimetreI+$ld_trimetreII+$ld_trimetreIII+$ld_trimetreIV;
			   }//if
			   $ld_previsto_modificado=$ld_previsto+$ld_aumento_acumulado-$ld_disminucion_acumulado;
			   if ($ld_programado_trimestral > 0)
			   {
			    $ld_porcentual =($ld_cobrado/$ld_programado_trimestral)*100;
			   }
			   else
			   {
			    $ld_porcentual =0;
			   }
			   $this->dts_ingresos_capital->insertRow("cuenta",$ls_spi_cuenta);
			   $this->dts_ingresos_capital->insertRow("denominacion",$ls_denominacion);
			   $this->dts_ingresos_capital->insertRow("asignado",$ld_previsto);
			   $this->dts_ingresos_capital->insertRow("modificado",$ld_previsto_modificado);
			   $this->dts_ingresos_capital->insertRow("programado",$ld_programado_trimestral);
		  	   $this->dts_ingresos_capital->insertRow("ejecutado",$ld_cobrado);		
		  	   $this->dts_ingresos_capital->insertRow("absoluto",abs($ld_cobrado-$ld_programado_trimestral));		
		  	   $this->dts_ingresos_capital->insertRow("porcentual",$ld_porcentual);		
			   $this->dts_ingresos_capital->insertRow("programado_acumulado",$ld_programado_acumulado);
		  	   $this->dts_ingresos_capital->insertRow("ejecutado_acumulado",$ld_cobrado_acumulado);
			   $this->dts_ingresos_capital->insertRow("status",$ls_status);
			   /// datastore  del reportes
			   $this->dts_reporte_temporal->insertRow("cuenta",$ls_spi_cuenta);
			   $this->dts_reporte_temporal->insertRow("denominacion",$ls_denominacion);
			   $this->dts_reporte_temporal->insertRow("asignado",$ld_previsto);
			   $this->dts_reporte_temporal->insertRow("modificado",$ld_previsto_modificado);
			   $this->dts_reporte_temporal->insertRow("programado",$ld_programado_trimestral);
		  	   $this->dts_reporte_temporal->insertRow("ejecutado",$ld_cobrado);		
		  	   $this->dts_reporte_temporal->insertRow("absoluto",abs($ld_cobrado-$ld_programado_trimestral));		
		  	   $this->dts_reporte_temporal->insertRow("porcentual",$ld_porcentual);		
			   $this->dts_reporte_temporal->insertRow("programado_acumulado",$ld_programado_acumulado);
		  	   $this->dts_reporte_temporal->insertRow("ejecutado_acumulado",$ld_cobrado_acumulado);
			   $lb_valido=true;
			   $rs_data->MoveNext();
		    }//while
	    }//if	
	    $this->io_sql->free_result($rs_data);
	  }//else
    return $lb_valido;
    }//fin uf_spg_reportes_gastos_capital

	function uf_spi_reportes_ingresos_financieros($adt_fecdes,$adt_fechas,$adts_datastore,$as_mesdes,$as_meshas)
    {////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function : uf_spi_reportes_ingresos_financieros
	 //     Argumentos : adt_fecdes ... adt_fechas  // rango de fecha del reporte
	 //                  adts_datastore  // datastore que imprime el reporte
     //	       Returns : Retorna true o false si se realizo la consulta para el reporte
	 //	   Description : Reporte que genera salida del PResupuesto de Caja
	 //     Creado por : Ing. Arnaldo Suarez
	 // Fecha Creación : 18/06/2008                       Fecha última Modificacion :      Hora :
  	 ///////////////////////////////////////////////////////////////////////////////////////////////////////
	  $lb_valido=true;
	  $ls_sql=" SELECT spi_cuenta,max(denominacion) as denominacion,max(status) as status,                  ".
              "        sum(previsto) as previsto, ".
			  "        sum(enero+febrero+marzo) as trimestrei, sum(abril+mayo+junio) as trimestreii,       ".
			  "        sum(julio+agosto+septiembre) as trimestreiii,                                      ".
			  "        sum(octubre+noviembre+diciembre) as trimestreiv                                    ".
	  	      " FROM   spi_cuentas ".
			  " WHERE  codemp='".$this->ls_codemp."' AND ".
			  "        spi_cuenta like '307000000%' OR spi_cuenta like '308000000%' OR spi_cuenta like '309000000%' ".
			  "        OR spi_cuenta like '311%' OR".
			  "        spi_cuenta like '312%' OR spi_cuenta like '313%' ".
			  " GROUP BY spi_cuenta".
			  " ORDER BY spi_cuenta";
	  //echo $ls_sql."<br>";		  		   	  
	  $rs_data=$this->io_sql->select($ls_sql);
	  if($rs_data===false)
	  { // error interno sql
		$this->io_mensajes->message("CLASE->sigesp_spi_class_reportes_instructivos ". 
			                        "MÉTODO->uf_spi_reportes_ingresos_capital ".
									"ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		$lb_valido = false;
 	  }
	  else
	  {
		$li_numrows=$this->io_sql->num_rows($rs_data);	
		if($li_numrows>=0)
		{
		     while(!$rs_data->EOF)
			 {
			   $ls_spi_cuenta=$rs_data->fields["spi_cuenta"];
			   //$ls_spi_cuenta = substr($ls_spi_cuenta,0,9);
			   $ls_denominacion=$rs_data->fields["denominacion"];
			   $ls_status=$rs_data->fields["status"];
			   $ld_previsto=$rs_data->fields["previsto"];
			   $ld_trimetreI=$rs_data->fields["trimestrei"]; 
			   $ld_trimetreII=$rs_data->fields["trimestreii"]; 
			   $ld_trimetreIII=$rs_data->fields["trimestreiii"]; 
			   $ld_trimetreIV=$rs_data->fields["trimestreiv"];
			   $ld_recaudado = 0;
			   $ld_cobrado= 0;
			   $ld_devengado= 0;
			   $ld_aumento= 0;
			   $ld_disminucion= 0; 
			   $ld_recaudado_acumulado= 0;
			   $ld_cobrado_acumulado= 0;
			   $ld_devengado_acumulado= 0;
			   $ld_aumento_acumulado= 0;
			   $ld_disminucion_acumulado= 0; 
			   if(($ls_spi_cuenta=='311000000')||($ls_spi_cuenta=='312000000')||($ls_spi_cuenta=='313000000'))
			   {
			     $ls_detallar=true; 
			   }
			   else
			   {
			     $ls_detallar=false; 
			   }
			   $lb_valido=$this->uf_spi_ejecutado_trimestral($ls_spi_cuenta,$adt_fecdes,$adt_fechas,&$ld_recaudado,
			                                                 &$ld_cobrado,&$ld_devengado,&$ld_aumento,&$ld_disminucion);
			   if($lb_valido)
		       {
				   $lb_valido=$this->uf_spi_ejecutado_acumulado($ls_spi_cuenta,$adt_fechas,&$ld_recaudado_acumulado,
																&$ld_cobrado_acumulado,&$ld_devengado_acumulado,
																&$ld_aumento_acumulado,&$ld_disminucion_acumulado);
			   }//if
			   if($as_mesdes=='Enero')
		       {
				   $ld_programado_trimestral=$ld_trimetreI;
				   $ld_programado_acumulado=$ld_trimetreI;
			   }//if
			   if($as_mesdes=='Abril')
		       {
				   $ld_programado_trimestral=$ld_trimetreII;
				   $ld_programado_acumulado=$ld_trimetreI+$ld_trimetreII;
			   }//if
			   if($as_mesdes=='Julio')
		       {
				   $ld_programado_trimestral=$ld_trimetreIII;
				   $ld_programado_acumulado=$ld_trimetreI+$ld_trimetreII+$ld_trimetreIII;
			   }//if
			   if($as_mesdes=='Octubre')
		       {
				   $ld_programado_trimestral=$ld_trimetreIV;
				   $ld_programado_acumulado=$ld_trimetreI+$ld_trimetreII+$ld_trimetreIII+$ld_trimetreIV;
			   }//if
			   $ld_previsto_modificado=$ld_previsto+$ld_aumento_acumulado-$ld_disminucion_acumulado;
			   if ($ld_programado_trimestral > 0)
			   {
			    $ld_porcentual =($ld_cobrado/$ld_programado_trimestral)*100;
			   }
			   else
			   {
			    $ld_porcentual =0;
			   }
			   $this->dts_ingresos_financieros->insertRow("cuenta",$ls_spi_cuenta);
			   $this->dts_ingresos_financieros->insertRow("denominacion",$ls_denominacion);
			   $this->dts_ingresos_financieros->insertRow("asignado",$ld_previsto);
			   $this->dts_ingresos_financieros->insertRow("modificado",$ld_previsto_modificado);
			   $this->dts_ingresos_financieros->insertRow("programado",$ld_programado_trimestral);
		  	   $this->dts_ingresos_financieros->insertRow("ejecutado",$ld_cobrado);		
		  	   $this->dts_ingresos_financieros->insertRow("absoluto",abs($ld_cobrado-$ld_programado_trimestral));		
		  	   $this->dts_ingresos_financieros->insertRow("porcentual",$ld_porcentual);		
			   $this->dts_ingresos_financieros->insertRow("programado_acumulado",$ld_programado_acumulado);
		  	   $this->dts_ingresos_financieros->insertRow("ejecutado_acumulado",$ld_cobrado_acumulado);
			   $this->dts_ingresos_financieros->insertRow("status",$ls_status);
			   /// datastore  del reportes
			   $this->dts_reporte_temporal->insertRow("cuenta",$ls_spi_cuenta);
			   $this->dts_reporte_temporal->insertRow("denominacion",$ls_denominacion);
			   $this->dts_reporte_temporal->insertRow("asignado",$ld_previsto);
			   $this->dts_reporte_temporal->insertRow("modificado",$ld_previsto_modificado);
			   $this->dts_reporte_temporal->insertRow("programado",$ld_programado_trimestral);
		  	   $this->dts_reporte_temporal->insertRow("ejecutado",$ld_cobrado);		
		  	   $this->dts_reporte_temporal->insertRow("absoluto",abs($ld_cobrado-$ld_programado_trimestral));		
		  	   $this->dts_reporte_temporal->insertRow("porcentual",$ld_porcentual);		
			   $this->dts_reporte_temporal->insertRow("programado_acumulado",$ld_programado_acumulado);
		  	   $this->dts_reporte_temporal->insertRow("ejecutado_acumulado",$ld_cobrado_acumulado); 
			   $lb_valido=true;
			   $rs_data->MoveNext();
		    }//while
	    }//if	
	    $this->io_sql->free_result($rs_data);
	  }//else
    return $lb_valido;
    }//fin uf_spi_reportes_gastos_financieros


	function uf_spi_reportes_gastos_de_consumo($adt_fecdes,$adt_fechas,$adts_datastore,$as_mesdes,$as_meshas)
    {////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function : uf_spi_reportes_gastos_de_consumo
	 //     Argumentos : adt_fecdes ... adt_fechas  // rango de fecha del reporte
	 //                  adts_datastore  // datastore que imprime el reporte
     //	       Returns : Retorna true o false si se realizo la consulta para el reporte
	 //	   Description : Reporte que genera salida del Presupuesto de Caja
	 //     Creado por : Ing. Arnaldo Suárez
	 // Fecha Creación : 18/06/2008                       Fecha última Modificacion :      Hora :
  	 ///////////////////////////////////////////////////////////////////////////////////////////////////////
	   $lb_valido=true;
	  $ls_sql=" SELECT spg_cuenta, max(denominacion) as denominacion, max(status) as status, ".
              "        max(nivel) as nivel, sum(asignado) as asignado,".
			  "        sum(enero+febrero+marzo) as trimestrei, sum(abril+mayo+junio) as trimestreii,       ".
			  "        sum(julio+agosto+septiembre) as trimestreiii,                                      ".
			  "        sum(octubre+noviembre+diciembre) as trimestreiv                                    ".
	  	      " FROM   spg_cuentas ".
			  " WHERE  codemp='".$this->ls_codemp."' AND ".
			  "        spg_cuenta like '401000000%' OR spg_cuenta like '402000000%' OR spg_cuenta like '403000000%' OR ".
			  "        spg_cuenta like '408020000%' OR spg_cuenta like '408060000%' ".
			  " GROUP BY spg_cuenta".
			  " UNION ".
			  " SELECT spg_cuentas.spg_cuenta, max(spg_cuentas.denominacion) as denominacion, max(spg_cuentas.status) as status, ".
			  "	MAX(spg_cuentas.nivel) as nivel,(SUM(spg_cuentas.asignado) - (SELECT SUM(spg_cuentas.asignado) FROM spg_cuentas WHERE codemp='".$this->ls_codemp."' AND spg_cuentas.spg_cuenta like '408010000%')) as asignado, ".
			  " (SUM(spg_cuentas.enero+spg_cuentas.febrero+spg_cuentas.marzo) - (SELECT SUM(spg_cuentas.enero+spg_cuentas.febrero+spg_cuentas.marzo) FROM spg_cuentas WHERE  codemp='".$this->ls_codemp."' AND spg_cuentas.spg_cuenta like '408010000%')) as trimestrei, ".
			  " (SUM(spg_cuentas.abril+spg_cuentas.mayo+spg_cuentas.junio) - (SELECT SUM(spg_cuentas.abril+spg_cuentas.mayo+spg_cuentas.junio) FROM spg_cuentas WHERE codemp='".$this->ls_codemp."' AND spg_cuentas.spg_cuenta like '408010000%')) as trimestreii, ".
			  " (SUM(spg_cuentas.julio+spg_cuentas.agosto+spg_cuentas.septiembre) - (SELECT SUM(spg_cuentas.julio+spg_cuentas.agosto+spg_cuentas.septiembre) FROM spg_cuentas WHERE codemp='".$this->ls_codemp."' AND spg_cuentas.spg_cuenta like '408010000%')) as trimestreiii, ".
			  " (SUM(spg_cuentas.octubre+spg_cuentas.noviembre+spg_cuentas.diciembre) - (SELECT SUM(spg_cuentas.octubre+spg_cuentas.noviembre+spg_cuentas.diciembre) FROM spg_cuentas WHERE codemp='".$this->ls_codemp."' AND spg_cuentas.spg_cuenta like '408010000%')) as trimestreiv ".
              " FROM spg_cuentas where codemp='".$this->ls_codemp."' AND spg_cuenta like '408000000%' ".
              " GROUP BY spg_cuentas.spg_cuenta".
			  " ORDER BY spg_cuenta ";	  
	  $rs_data=$this->io_sql->select($ls_sql);
	  if($rs_data===false)
	  { // error interno sql
		$this->io_mensajes->message("CLASE->sigesp_spg_class_reportes_instructivos ". 
			                        "MÉTODO->uf_spg_reportes_gastos_de_consumo ".
									"ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		$lb_valido = false;
 	  }
	  else
	  {
		$li_numrows=$this->io_sql->num_rows($rs_data);	
		if($li_numrows>=0)
		{
		     while(!$rs_data->EOF)
			 {
			   $ls_spg_cuenta=$rs_data->fields["spg_cuenta"];
			   //$ls_spg_cuenta = substr($ls_spg_cuenta,0,9);
			   $ls_denominacion=$rs_data->fields["denominacion"];
			   $ls_status=$rs_data->fields["status"];
			   $li_nivel=$rs_data->fields["nivel"];
			   $ld_asignado=$rs_data->fields["asignado"];
			   $ld_trimetreI=$rs_data->fields["trimestrei"]; 
			   $ld_trimetreII=$rs_data->fields["trimestreii"]; 
			   $ld_trimetreIII=$rs_data->fields["trimestreiii"]; 
			   $ld_trimetreIV=$rs_data->fields["trimestreiv"]; 
			   $ls_detallar=false;
			   $ld_comprometer=0;
			   $ld_causado = 0;
			   $ld_pagado = 0;
			   $ld_aumento = 0;
			   $ld_disminucion = 0;
			   $ld_comprometer_acumulado = 0;
			   $ld_causado_acumulado = 0;
			   $ld_pagado_acumulado = 0;
			   $ld_aumento_acumulado = 0;
			   $ld_disminucion_acumulado = 0;
			   $lb_valido=$this->uf_spg_ejecutado_trimestral_estado_resultado($ls_spg_cuenta,$adt_fecdes,$adt_fechas,
			                                                                  &$ld_comprometer,&$ld_causado,&$ld_pagado,
																			  &$ld_aumento,&$ld_disminucion,$ls_detallar,true);
			   if($lb_valido)
		       {
				   $lb_valido=$this->uf_spg_ejecutado_acumulado_estado_resultado($ls_spg_cuenta,$adt_fechas,&$ld_comprometer_acumulado,
				                                                                 &$ld_causado_acumulado,&$ld_pagado_acumulado,
																				 &$ld_aumento_acumulado,&$ld_disminucion_acumulado,
																                 $ls_detallar,true);
			   }//if
			   if($as_mesdes=='Enero')
		       {
				   $ld_programado_trimestral=$ld_trimetreI;
				   $ld_programado_acumulado=$ld_trimetreI;
			   }//if
			   if($as_mesdes=='Abril')
		       {
				   $ld_programado_trimestral=$ld_trimetreII;
				   $ld_programado_acumulado=$ld_trimetreI+$ld_trimetreII;
			   }//if
			   if($as_mesdes=='Julio')
		       {
				   $ld_programado_trimestral=$ld_trimetreIII;
				   $ld_programado_acumulado=$ld_trimetreI+$ld_trimetreII+$ld_trimetreIII;
			   }//if
			   if($as_mesdes=='Octubre')
		       {
				   $ld_programado_trimestral=$ld_trimetreIV;
				   $ld_programado_acumulado=$ld_trimetreI+$ld_trimetreII+$ld_trimetreIII+$ld_trimetreIV;
			   }//if
			   $ld_asignado_modificado=$ld_asignado+$ld_aumento_acumulado-$ld_disminucion_acumulado;
			    if ($ld_programado_trimestral > 0)
			   {
			    $ld_porcentual =($ld_causado/$ld_programado_trimestral)*100;
			   }
			   else
			   {
			    $ld_porcentual =0;
			   }
			   $this->dts_egresos_consumo->insertRow("cuenta",$ls_spg_cuenta);
			   $this->dts_egresos_consumo->insertRow("denominacion",$ls_denominacion);
			   $this->dts_egresos_consumo->insertRow("asignado",$ld_asignado);
			   $this->dts_egresos_consumo->insertRow("modificado",$ld_asignado_modificado);
			   $this->dts_egresos_consumo->insertRow("programado",$ld_programado_trimestral);
		  	   $this->dts_egresos_consumo->insertRow("ejecutado",$ld_causado);		
		  	   $this->dts_egresos_consumo->insertRow("absoluto",abs($ld_causado-$ld_programado_trimestral));		
		  	   $this->dts_egresos_consumo->insertRow("porcentual",$ld_porcentual);		
			   $this->dts_egresos_consumo->insertRow("programado_acumulado",$ld_programado_acumulado);
		  	   $this->dts_egresos_consumo->insertRow("ejecutado_acumulado",$ld_comprometer_acumulado);
			   $this->dts_egresos_consumo->insertRow("status",$ls_status);
			   $this->dts_egresos_consumo->insertRow("nivel",$li_nivel);
			   /// datastore  del reportes
			   $this->dts_reporte_temporal->insertRow("cuenta",$ls_spg_cuenta);
			   $this->dts_reporte_temporal->insertRow("denominacion",$ls_denominacion);
			   $this->dts_reporte_temporal->insertRow("asignado",$ld_asignado);
			   $this->dts_reporte_temporal->insertRow("modificado",$ld_asignado_modificado);
			   $this->dts_reporte_temporal->insertRow("programado",$ld_programado_trimestral);
		  	   $this->dts_reporte_temporal->insertRow("ejecutado",$ld_causado);		
		  	   $this->dts_reporte_temporal->insertRow("absoluto",abs($ld_causado-$ld_programado_trimestral));		
		  	   $this->dts_reporte_temporal->insertRow("porcentual",$ld_porcentual);		
			   $this->dts_reporte_temporal->insertRow("programado_acumulado",$ld_programado_acumulado);
		  	   $this->dts_reporte_temporal->insertRow("ejecutado_acumulado",$ld_causado_acumulado);
			   $lb_valido=true;
			   $rs_data->MoveNext();
		    }//while
	    }//if	
	    $this->io_sql->free_result($rs_data);
		}//else
    return $lb_valido;
    }//fin uf_spg_reportes_gastos_de_consumo
	//-----------------------------------------------------------------------------------------------------------------------------------

	function uf_spi_reportes_gastos_corrientes($adt_fecdes,$adt_fechas,$adts_datastore,$as_mesdes,$as_meshas)
    {////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function : uf_spi_reportes_gastos_corrientes
	 //     Argumentos : adt_fecdes ... adt_fechas  // rango de fecha del reporte
	 //                  adts_datastore  // datastore que imprime el reporte
     //	       Returns : Retorna true o false si se realizo la consulta para el reporte
	 //	   Description : Reporte que genera salida del Presupuesto de Caja
	 //     Creado por : Ing. Arnaldo Suárez
	 // Fecha Creación : 18/06/2008                       Fecha última Modificacion :      Hora :
  	 ///////////////////////////////////////////////////////////////////////////////////////////////////////
	   $lb_valido=true;
	  $ls_sql=" SELECT spg_cuenta, max(denominacion) as denominacion, sum(asignado) as asignado, max(status) as status, ".
              "        max(nivel) as nivel, ".
			  "        sum(enero+febrero+marzo) as trimestrei, sum(abril+mayo+junio) as trimestreii,       ".
			  "        sum(julio+agosto+septiembre) as trimestreiii,                                      ".
			  "        sum(octubre+noviembre+diciembre) as trimestreiv                                    ".
	  	      " FROM   spg_cuentas ".
			  " WHERE  codemp='".$this->ls_codemp."' AND ".
			  "        spg_cuenta like '407000000%' OR spg_cuenta like '407010100%' OR spg_cuenta like '4070103%' OR ".
			  "        spg_cuenta like '407020000%' OR spg_cuenta like '407030100%' OR spg_cuenta like '407030300%' OR ".
			  "        spg_cuenta like '408080000%' OR spg_cuenta like '404000000%' OR spg_cuenta like '405%' OR ".
			  "        spg_cuenta like '411%' OR spg_cuenta like '412%' ".
			  " GROUP BY spg_cuenta".
			  " ORDER BY spg_cuenta ";
	  $rs_data=$this->io_sql->select($ls_sql);
	  if($rs_data===false)
	  { // error interno sql
		$this->io_mensajes->message("CLASE->sigesp_spg_class_reportes_instructivos ". 
			                        "MÉTODO->uf_spg_reportes_gastos_de_consumo ".
									"ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		$lb_valido = false;
 	  }
	  else
	  {
		$li_numrows=$this->io_sql->num_rows($rs_data);	
		if($li_numrows>=0)
		{
		     while(!$rs_data->EOF)
			 {
			   $ls_spg_cuenta=$rs_data->fields["spg_cuenta"];
			   //$ls_spg_cuenta = substr($ls_spg_cuenta,0,9);
			   $ls_denominacion=$rs_data->fields["denominacion"];
			   $ls_status=$rs_data->fields["status"];
			   $li_nivel=$rs_data->fields["nivel"];
			   $ld_asignado=$rs_data->fields["asignado"];
			   $ld_trimetreI=$rs_data->fields["trimestrei"]; 
			   $ld_trimetreII=$rs_data->fields["trimestreii"]; 
			   $ld_trimetreIII=$rs_data->fields["trimestreiii"]; 
			   $ld_trimetreIV=$rs_data->fields["trimestreiv"];
			   $ld_comprometer=0;
			   $ld_causado = 0;
			   $ld_pagado = 0;
			   $ld_aumento = 0;
			   $ld_disminucion = 0;
			   $ld_comprometer_acumulado = 0;
			   $ld_causado_acumulado = 0;
			   $ld_pagado_acumulado = 0;
			   $ld_aumento_acumulado = 0;
			   $ld_disminucion_acumulado = 0; 
			   if(($ls_spg_cuenta=='407010300')||($ls_spg_cuenta=='405000000')||($ls_spg_cuenta=='411000000')||($ls_spg_cuenta=='412000000'))
			   {
			     $ls_detallar=true; 
			   }
			   else
			   {
			     $ls_detallar=false; 
			   }
			   $lb_valido=$this->uf_spg_ejecutado_trimestral_estado_resultado($ls_spg_cuenta,$adt_fecdes,$adt_fechas,
			                                                                  &$ld_comprometer,&$ld_causado,&$ld_pagado,
																			  &$ld_aumento,&$ld_disminucion,$ls_detallar);
			   if($lb_valido)
		       {
				   $lb_valido=$this->uf_spg_ejecutado_acumulado_estado_resultado($ls_spg_cuenta,$adt_fechas,&$ld_comprometer_acumulado,
				                                                                 &$ld_causado_acumulado,&$ld_pagado_acumulado,
																				 &$ld_aumento_acumulado,&$ld_disminucion_acumulado,
																                 $ls_detallar);
			   }//if
			   if($as_mesdes=='Enero')
		       {
				   $ld_programado_trimestral=$ld_trimetreI;
				   $ld_programado_acumulado=$ld_trimetreI;
			   }//if
			   if($as_mesdes=='Abril')
		       {
				   $ld_programado_trimestral=$ld_trimetreII;
				   $ld_programado_acumulado=$ld_trimetreI+$ld_trimetreII;
			   }//if
			   if($as_mesdes=='Julio')
		       {
				   $ld_programado_trimestral=$ld_trimetreIII;
				   $ld_programado_acumulado=$ld_trimetreI+$ld_trimetreII+$ld_trimetreIII;
			   }//if
			   if($as_mesdes=='Octubre')
		       {
				   $ld_programado_trimestral=$ld_trimetreIV;
				   $ld_programado_acumulado=$ld_trimetreI+$ld_trimetreII+$ld_trimetreIII+$ld_trimetreIV;
			   }//if
			   $ld_asignado_modificado=$ld_asignado+$ld_aumento_acumulado-$ld_disminucion_acumulado;
			    if ($ld_programado_trimestral > 0)
			   {
			    $ld_porcentual =($ld_causado/$ld_programado_trimestral)*100;
			   }
			   else
			   {
			    $ld_porcentual =0;
			   }
			   $this->dts_egresos_corrientes->insertRow("cuenta",$ls_spg_cuenta);
			   $this->dts_egresos_corrientes->insertRow("denominacion",$ls_denominacion);
			   $this->dts_egresos_corrientes->insertRow("asignado",$ld_asignado);
			   $this->dts_egresos_corrientes->insertRow("modificado",$ld_asignado_modificado);
			   $this->dts_egresos_corrientes->insertRow("programado",$ld_programado_trimestral);
		  	   $this->dts_egresos_corrientes->insertRow("ejecutado",$ld_causado);		
		  	   $this->dts_egresos_corrientes->insertRow("absoluto",abs($ld_causado-$ld_programado_trimestral));		
		  	   $this->dts_egresos_corrientes->insertRow("porcentual",$ld_porcentual);		
			   $this->dts_egresos_corrientes->insertRow("programado_acumulado",$ld_programado_acumulado);
		  	   $this->dts_egresos_corrientes->insertRow("ejecutado_acumulado",$ld_causado_acumulado);
			   $this->dts_egresos_corrientes->insertRow("status",$ls_status);
			   $this->dts_egresos_corrientes->insertRow("nivel",$li_nivel);
			   /// datastore  del reportes
			   $this->dts_reporte_temporal->insertRow("cuenta",$ls_spg_cuenta);
			   $this->dts_reporte_temporal->insertRow("denominacion",$ls_denominacion);
			   $this->dts_reporte_temporal->insertRow("asignado",$ld_asignado);
			   $this->dts_reporte_temporal->insertRow("modificado",$ld_asignado_modificado);
			   $this->dts_reporte_temporal->insertRow("programado",$ld_programado_trimestral);
		  	   $this->dts_reporte_temporal->insertRow("ejecutado",$ld_causado);		
		  	   $this->dts_reporte_temporal->insertRow("absoluto",abs($ld_causado-$ld_programado_trimestral));		
		  	   $this->dts_reporte_temporal->insertRow("porcentual",$ld_porcentual);		
			   $this->dts_reporte_temporal->insertRow("programado_acumulado",$ld_programado_acumulado);
		  	   $this->dts_reporte_temporal->insertRow("ejecutado_acumulado",$ld_causado_acumulado);
			   $lb_valido=true;
			   $rs_data->MoveNext();
		    }//while
	    }//if	
	    $this->io_sql->free_result($rs_data);
		}//else
    return $lb_valido;
    }//fin uf_spg_reportes_gastos_de_consumo
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	function uf_spg_reporte_select_saldo_empresa()
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_select_saldo_empresa
	 //         Access :	private
	 //     Argumentos :    $ad_salinipro --> saldo inicial programado(referencia) 
	 //                     $ad_salinieje --> saldo inicial ejecutado(referencia)
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	Reporte por referencia los saldos iniciales programados y ejecutados.   
	 //     Creado por :    Ing. Arnaldo Suárez.
	 // Fecha Creación :    18/08/2008               Fecha última Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  $lb_valido = true;	 
	  $ls_sql=" SELECT salinipro, salinieje  FROM sigesp_empresa WHERE codemp='".$this->ls_codemp."' ";
	  $rs_data=$this->io_sql->select($ls_sql);
	  if($rs_data===false)
	  {   // error interno sql
		  $this->io_msg->message("CLASE->sigesp_spi_class_reportes_instructivos  MÉTODO->uf_spg_reporte_select_saldo_empresa  ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
		  $lb_valido = false;
	  }
	  else
	  {
		if(!$rs_data->EOF)
		{
			  $ad_salinipro=$rs_data->fields["salinipro"];
			  $ad_salinieje=$rs_data->fields["salinieje"];	
			  
			  $this->dts_reporte->insertRow("cuenta","");
			  $this->dts_reporte->insertRow("denominacion",'<b>SALDO INICIAL</b>');
			  $this->dts_reporte->insertRow("asignado",$ad_salinipro);
			  $this->dts_reporte->insertRow("modificado",0);
			  $this->dts_reporte->insertRow("programado",0);
			  $this->dts_reporte->insertRow("ejecutado",0);		
			  $this->dts_reporte->insertRow("absoluto",0);		
			  $this->dts_reporte->insertRow("porcentual",0);		
			  $this->dts_reporte->insertRow("programado_acumulado",0);
			  $this->dts_reporte->insertRow("ejecutado_acumulado",$ad_salinieje); 
	    }
		$this->io_sql->free_result($rs_data);
      }//else
	  return $lb_valido;
   }//fin uf_spg_reporte_select_saldo_empresa
   

/********************************************************************************************************************************/
/*                                                        FIN PRESUPUESTO DE CAJA INSTRUCTIVO 07                                                  */
/********************************************************************************************************************************/

/********************************************************************************************************************************/
/*                                                        PRESUPUESTO DE CAJA INSTRUCTIVO 08                                                 */
/********************************************************************************************************************************/

 	function uf_spi_reportes_ingresos_pres_caja_inst_08($as_spi_cuenta,$adt_fecdes,$adt_fechas,$as_mesdes,$as_meshas,$ab_detallar,&$rs_data)
    {////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function : uf_spi_reportes_ingresos_pres_caja_inst_08
	 //     Argumentos : $as_spi_cuenta // Cuenta de Ingreso
	 //                  $adt_fecdes ... adt_fechas  // rango de fecha del reporte
	 //                  $ab_detallar // Indica si se debe o no mostrar detalle de la cuenta (subcuentas)
     //	       Returns : Retorna true o false si se realizo la consulta para el reporte
	 //	   Description : Reporte que genera salida del Presupuesto de Caja Inst. 08
	 //     Creado por : Ing. Arnaldo Suarez
	 // Fecha Creación : 10/02/2010                       Fecha última Modificacion :      Hora :
  	 ///////////////////////////////////////////////////////////////////////////////////////////////////////
	  $lb_valido=true;
	  $ls_cadena_cuenta = "";
	  $ls_cadena_filtro = "";
	  $ls_formpre=$_SESSION["la_empresa"]["formpre"];
	  $ls_formpre=str_replace('-','',$ls_formpre);
	  $li_len=strlen($ls_formpre);
	  $li_len=$li_len-9;
	  $ls_ceros=$this->io_funciones->uf_cerosderecha("",$li_len);
	  if($ab_detallar)
	  {
	   $ls_spi_cuenta = $this->io_sigesp_int_spg->uf_spg_cuenta_sin_cero($as_spi_cuenta)."%";
	   $ls_cadena_cuenta =  " spi_cuenta like '".$ls_spi_cuenta."'";
	  }
	  else
	  {
	   $ls_cadena_cuenta =  " spi_cuenta = '".$as_spi_cuenta."'";
	  }
	  switch($as_spi_cuenta)
	  {
	   case '303000000'.$ls_ceros :
	   $ls_sql=	  " SELECT '303000000".$ls_ceros."' as spi_cuenta,max(denominacion) as denominacion,max(status) as status,                  ".
				  "        sum(previsto) as previsto, ".
				  "        sum(enero+febrero+marzo) as trimestrei, sum(abril+mayo+junio) as trimestreii,       ".
				  "        sum(julio+agosto+septiembre) as trimestreiii,                                      ".
				  "        sum(octubre+noviembre+diciembre) as trimestreiv                                    ".
				  " FROM   spi_cuentas ".
				  " WHERE  codemp='".$this->ls_codemp."' AND spi_cuenta IN ('303010000".$ls_ceros."','303020000".$ls_ceros."','303990000".$ls_ceros."')".
				  " GROUP BY 1".
				  " ORDER BY spi_cuenta";
	   break;
	   case '301100000'.$ls_ceros :
	   $ls_sql=	  " SELECT '301100000".$ls_ceros."' as spi_cuenta,max(denominacion) as denominacion,max(status) as status,                  ".
				  "        sum(previsto) as previsto, ".
				  "        sum(enero+febrero+marzo) as trimestrei, sum(abril+mayo+junio) as trimestreii,       ".
				  "        sum(julio+agosto+septiembre) as trimestreiii,                                      ".
				  "        sum(octubre+noviembre+diciembre) as trimestreiv                                    ".
				  " FROM   spi_cuentas ".
				  " WHERE  codemp='".$this->ls_codemp."' AND spi_cuenta IN ('301100401".$ls_ceros."','301100500".$ls_ceros."','301100800".$ls_ceros."')".
				  " GROUP BY 1".
				  " ORDER BY spi_cuenta";
	   break;
	   case '306000000'.$ls_ceros :
	   $ls_sql=	  " SELECT '306000000".$ls_ceros."' as spi_cuenta,max(denominacion) as denominacion,max(status) as status,                  ".
				  "        sum(previsto) as previsto, ".
				  "        sum(enero+febrero+marzo) as trimestrei, sum(abril+mayo+junio) as trimestreii,       ".
				  "        sum(julio+agosto+septiembre) as trimestreiii,                                      ".
				  "        sum(octubre+noviembre+diciembre) as trimestreiv                                    ".
				  " FROM   spi_cuentas ".
				  " WHERE  codemp='".$this->ls_codemp."' AND spi_cuenta IN ('306010000".$ls_ceros."','306020000".$ls_ceros."')".
				  " GROUP BY 1".
				  " ORDER BY spi_cuenta";
	   break;
	   case '311000000'.$ls_ceros :
	   $ls_sql=	  " SELECT '311000000".$ls_ceros."' as spi_cuenta,max(denominacion) as denominacion,max(status) as status,                  ".
				  "        sum(previsto) as previsto, ".
				  "        sum(enero+febrero+marzo) as trimestrei, sum(abril+mayo+junio) as trimestreii,       ".
				  "        sum(julio+agosto+septiembre) as trimestreiii,                                      ".
				  "        sum(octubre+noviembre+diciembre) as trimestreiv                                    ".
				  " FROM   spi_cuentas ".
				  " WHERE  codemp='".$this->ls_codemp."' AND spi_cuenta IN ('311020000".$ls_ceros."','311030000".$ls_ceros."','311990100".$ls_ceros."')".
				  " GROUP BY 1".
				  " ORDER BY spi_cuenta";
	   break;
	   case '312000000'.$ls_ceros :
	   $ls_sql=	  " SELECT '312000000".$ls_ceros."' as spi_cuenta,max(denominacion) as denominacion,max(status) as status,                  ".
				  "        sum(previsto) as previsto, ".
				  "        sum(enero+febrero+marzo) as trimestrei, sum(abril+mayo+junio) as trimestreii,       ".
				  "        sum(julio+agosto+septiembre) as trimestreiii,                                      ".
				  "        sum(octubre+noviembre+diciembre) as trimestreiv                                    ".
				  " FROM   spi_cuentas ".
				  " WHERE  codemp='".$this->ls_codemp."' AND spi_cuenta IN ('312010000".$ls_ceros."','312020000".$ls_ceros."','312030000".$ls_ceros."','312040000".$ls_ceros."','312050000".$ls_ceros."','312060000".$ls_ceros."','312070000".$ls_ceros."','312080000".$ls_ceros."','312090000".$ls_ceros."','312990000".$ls_ceros."')".
				  " GROUP BY 1".
				  " ORDER BY spi_cuenta";
	   break;
	   default :
	   $ls_sql=	  " SELECT spi_cuenta,max(denominacion) as denominacion,max(status) as status,                  ".
				  "        sum(previsto) as previsto, ".
				  "        sum(enero+febrero+marzo) as trimestrei, sum(abril+mayo+junio) as trimestreii,       ".
				  "        sum(julio+agosto+septiembre) as trimestreiii,                                      ".
				  "        sum(octubre+noviembre+diciembre) as trimestreiv                                    ".
				  " FROM   spi_cuentas ".
				  " WHERE  codemp='".$this->ls_codemp."' AND ".$ls_cadena_cuenta.
				  " GROUP BY spi_cuenta".
				  " ORDER BY spi_cuenta"; 
	  }
	  $rs_data=$this->io_sql->select($ls_sql);
	  if($rs_data===false)
	  { // error interno sql
		$this->io_mensajes->message("CLASE->sigesp_spi_class_reportes_instructivos ". 
			                        "MÉTODO->uf_spi_reportes_ingresos_pres_caja_inst_08 ".
									"ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		$lb_valido = false;
 	  }
    return $lb_valido;
    }//fin uf_spi_reportes_ingresos_pres_caja_inst_08
	
	function uf_spg_reportes_ingresos_pres_caja_inst_08($as_spg_cuenta,$adt_fecdes,$adt_fechas,$as_mesdes,$as_meshas,$ab_detallar,&$rs_data)
    {////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function : uf_spg_reportes_ingresos_pres_caja_inst_08
	 //     Argumentos : $as_spg_cuenta // Cuenta de Gasto
	 //                  $adt_fecdes ... adt_fechas  // rango de fecha del reporte
	 //                  $as_mesdes  // Nombre del Mes de inicio del trimestre
	 //                  $as_meshas  // Nombre del Mes de culminación del trimestre
	 //                  $ab_detallar // Indica si se debe o no mostrar detalle de la cuenta (subcuentas)
     //	       Returns : Retorna true o false si se realizo la consulta para el reporte
	 //	   Description : Reporte que genera salida del Presupuesto de Caja Inst. 08
	 //     Creado por : Ing. Arnaldo Suarez
	 // Fecha Creación : 10/02/2010                       Fecha última Modificacion :      Hora :
  	 ///////////////////////////////////////////////////////////////////////////////////////////////////////
	  $lb_valido=true;
	  $ls_cadena_cuenta = "";
	  if($ab_detallar)
	  {
	   $ls_spg_cuenta = $this->io_sigesp_int_spg->uf_spg_cuenta_sin_cero($as_spg_cuenta)."%";
	   $ls_cadena_cuenta =  " spg_cuenta like '".$ls_spg_cuenta."'";
	  }
	  else
	  {
	   $ls_cadena_cuenta =  " spg_cuenta = '".$as_spg_cuenta."'";
	  }
	  switch($as_spg_cuenta)
	  {
	   case '405000000'.$ls_ceros :
	   $ls_sql=	  " SELECT '405000000".$ls_ceros."' as spg_cuenta,max(denominacion) as denominacion,max(status) as status,                  ".
				  "        sum(asignado) as asignado, ".
				  "        sum(enero+febrero+marzo) as trimestrei, sum(abril+mayo+junio) as trimestreii,       ".
				  "        sum(julio+agosto+septiembre) as trimestreiii,                                      ".
				  "        sum(octubre+noviembre+diciembre) as trimestreiv                                    ".
				  " FROM   spg_cuentas ".
				  " WHERE  codemp='".$this->ls_codemp."' AND spg_cuenta IN ('405020000".$ls_ceros."','405030000".$ls_ceros."','405040000".$ls_ceros."','405050200".$ls_ceros."')".
				  " GROUP BY 1".
				  " ORDER BY spg_cuenta";
	   break;
	   case '408000000'.$ls_ceros :
	   $ls_sql=	  " SELECT '408000000".$ls_ceros."' as spg_cuenta,max(denominacion) as denominacion,max(status) as status,                  ".
				  "        sum(asignado) as asignado, ".
				  "        sum(enero+febrero+marzo) as trimestrei, sum(abril+mayo+junio) as trimestreii,       ".
				  "        sum(julio+agosto+septiembre) as trimestreiii,                                      ".
				  "        sum(octubre+noviembre+diciembre) as trimestreiv                                    ".
				  " FROM   spg_cuentas ".
				  " WHERE  codemp='".$this->ls_codemp."' AND spg_cuenta IN ('408020000".$ls_ceros."','408060000".$ls_ceros."','408070300".$ls_ceros."')".
				  " GROUP BY 1".
				  " ORDER BY spg_cuenta";
	   break;
	   case '411000000'.$ls_ceros :
	   $ls_sql=	  " SELECT '411000000".$ls_ceros."' as spg_cuenta,max(denominacion) as denominacion,max(status) as status,                  ".
				  "        sum(asignado) as asignado, ".
				  "        sum(enero+febrero+marzo) as trimestrei, sum(abril+mayo+junio) as trimestreii,       ".
				  "        sum(julio+agosto+septiembre) as trimestreiii,                                      ".
				  "        sum(octubre+noviembre+diciembre) as trimestreiv                                    ".
				  " FROM   spg_cuentas ".
				  " WHERE  codemp='".$this->ls_codemp."' AND spg_cuenta IN ('411070000".$ls_ceros."','411080000".$ls_ceros."','411090000".$ls_ceros."','411110000".$ls_ceros."','411990000".$ls_ceros."')".
				  " GROUP BY 1".
				  " ORDER BY spg_cuenta";
	   break;
	   default :
	   $ls_sql=   " SELECT spg_cuenta, max(denominacion) as denominacion, max(status) as status, ".
				  "        max(nivel) as nivel, sum(asignado) as asignado,".
				  "        sum(enero+febrero+marzo) as trimestrei, sum(abril+mayo+junio) as trimestreii,       ".
				  "        sum(julio+agosto+septiembre) as trimestreiii,                                      ".
				  "        sum(octubre+noviembre+diciembre) as trimestreiv                                    ".
				  " FROM   spg_cuentas ".
				  " WHERE  codemp='".$this->ls_codemp."' AND ". $ls_cadena_cuenta.
				  " GROUP BY spg_cuenta"; 
	  }
	  	  		   	  
	  $rs_data=$this->io_sql->select($ls_sql);
	  if($rs_data===false)
	  { // error interno sql
		$this->io_mensajes->message("CLASE->sigesp_spi_class_reportes_instructivos ". 
			                        "MÉTODO->uf_spg_reportes_ingresos_pres_caja_inst_08 ".
									"ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		$lb_valido = false;
 	  }
    return $lb_valido;
    }//fin uf_spg_reportes_ingresos_pres_caja_inst_08
	
	
	
	function uf_spg_reportes_presupuesto_de_caja_inst_08($adt_fecdes,$adt_fechas,$adts_datastore,$as_mesdes,$as_meshas)
	{////////////////////////////////////////////////////////////////////////////////////////////////////////
		 //	      Function :  uf_spg_reportes_presupuesto_de_caja
		 //     Argumentos : adt_fecdes ... adt_fechas  // rango de fecha del reporte
		 //                  adts_datastore  // datastore que imprime el reporte
		 //	       Returns : Retorna true o false si se realizo la consulta para el reporte
		 //	   Description : Reporte que genera salida del Presupuesto de Caja
		 //     Creado por : Ing. Arnaldo Suárez
		 // Fecha Creación : 18/06/2008                       Fecha última Modificacion :      Hora :
		 ///////////////////////////////////////////////////////////////////////////////////////////////////////
		 $ld_asignado_i = 0;
		 $ld_asignado_modificado_i = 0;
		 $ld_programado_i = 0;
		 $ld_ejecutado_i = 0;
		 $ld_variacion_absoluta_i = 0;
		 $ld_variacion_porcentual_i = 0;
		 $ld_programado_acumulado_i = 0;
		 $ld_ejecutado_acumulado_i = 0;
		 $ls_formpre=$_SESSION["la_empresa"]["formpre"];
		 $ls_formpre=str_replace('-','',$ls_formpre);
		 $li_len=strlen($ls_formpre);
		 $li_len=$li_len-9;
		 $ls_ceros=$this->io_funciones->uf_cerosderecha("",$li_len);
		 
		 //$lb_valido = $this->uf_spg_reporte_select_saldo_empresa();
		 $lb_valido = $this->uf_spg_reporte_select_saldo_inicial_int_08($as_mesdes,$adt_fecdes,$adt_fechas);
		 if($lb_valido)
		 {
		  $la_cuentas_ingreso[0]  = '303010000'.$ls_ceros;
		  $la_cuentas_ingreso[1]  = '303020000'.$ls_ceros;
		  $la_cuentas_ingreso[2]  = '303990000'.$ls_ceros;
		  $la_cuentas_ingreso[3]  = '301100401'.$ls_ceros;
		  $la_cuentas_ingreso[4]  = '301100500'.$ls_ceros;
		  $la_cuentas_ingreso[5]  = '301100800'.$ls_ceros;
		  $la_cuentas_ingreso[6]  = '301110000'.$ls_ceros;
		  $la_cuentas_ingreso[7]  = '305000000'.$ls_ceros;
		  $la_cuentas_ingreso[8]  = '306010000'.$ls_ceros;
		  $la_cuentas_ingreso[9]  = '306020000'.$ls_ceros;
		  $la_cuentas_ingreso[10] = '307000000'.$ls_ceros;
		  $la_cuentas_ingreso[11] = '308000000'.$ls_ceros;
		  $la_cuentas_ingreso[12] = '309000000'.$ls_ceros;
		  $la_cuentas_ingreso[13] = '311020100'.$ls_ceros;
		  $la_cuentas_ingreso[14] = '311030000'.$ls_ceros;
		  $la_cuentas_ingreso[15] = '311990100'.$ls_ceros;
		  $la_cuentas_ingreso[16] = '312010000'.$ls_ceros;
		  $la_cuentas_ingreso[17] = '312020000'.$ls_ceros;
		  $la_cuentas_ingreso[18] = '312030000'.$ls_ceros;
		  $la_cuentas_ingreso[19] = '312040000'.$ls_ceros;
		  $la_cuentas_ingreso[20] = '312050000'.$ls_ceros;
		  $la_cuentas_ingreso[21] = '312060000'.$ls_ceros;
		  $la_cuentas_ingreso[22] = '312070000'.$ls_ceros;
		  $la_cuentas_ingreso[23] = '312080000'.$ls_ceros;
		  $la_cuentas_ingreso[24] = '312090000'.$ls_ceros;
		  $la_cuentas_ingreso[25] = '312990000'.$ls_ceros;
		  $la_cuentas_ingreso[26] = '313000000'.$ls_ceros;
		  $lb_valido=$this->uf_spi_reporte_total_ingresos($adt_fecdes,$adt_fechas,$as_mesdes,$as_meshas,$la_cuentas_ingreso);
		 }
		 if($lb_valido)
		 {	
			$la_cuenta[1] ='300000000'.$ls_ceros;
			$la_cuenta[2] ='303000000'.$ls_ceros;
			$la_cuenta[3] ='303010000'.$ls_ceros;
			$la_cuenta[4] ='303020000'.$ls_ceros;
			$la_cuenta[5] ='303990000'.$ls_ceros;
			$la_cuenta[6] ='304000000'.$ls_ceros;
			$la_cuenta[7] ='301100000'.$ls_ceros;
			$la_cuenta[8] ='301100400'.$ls_ceros;
			$la_cuenta[9] ='301100401'.$ls_ceros;
			$la_cuenta[10]='301100500'.$ls_ceros;
			$la_cuenta[11]='301100800'.$ls_ceros;
			$la_cuenta[12]='301110000'.$ls_ceros;
			$la_cuenta[13]='305000000'.$ls_ceros;
			$la_cuenta[14]='306000000'.$ls_ceros;
			$la_cuenta[15]='306010000'.$ls_ceros;
			$la_cuenta[16]='306020000'.$ls_ceros;
			$la_cuenta[17]='307000000'.$ls_ceros;
			$la_cuenta[18]='308000000'.$ls_ceros;
			$la_cuenta[19]='309000000'.$ls_ceros;
			$la_cuenta[20]='311000000'.$ls_ceros;
			$la_cuenta[21]='311020000'.$ls_ceros;
			$la_cuenta[22]='311030000'.$ls_ceros;
			$la_cuenta[23]='311990100'.$ls_ceros;
			$la_cuenta[24]='312000000'.$ls_ceros;
			$la_cuenta[25]='312010000'.$ls_ceros;
			$la_cuenta[26]='312020000'.$ls_ceros;
			$la_cuenta[27]='312030000'.$ls_ceros;
			$la_cuenta[28]='312040000'.$ls_ceros;
			$la_cuenta[29]='312050000'.$ls_ceros;
			$la_cuenta[30]='312060000'.$ls_ceros;
			$la_cuenta[31]='312070000'.$ls_ceros;
			$la_cuenta[32]='312080000'.$ls_ceros;
			$la_cuenta[33]='312090000'.$ls_ceros;
			$la_cuenta[34]='312990000'.$ls_ceros;
			$la_cuenta[35]='313000000'.$ls_ceros;
			$la_cuenta[36]='400000000'.$ls_ceros;
			$la_cuenta[37]='401000000'.$ls_ceros;
			$la_cuenta[38]='402000000'.$ls_ceros;
			$la_cuenta[39]='403000000'.$ls_ceros;
			$la_cuenta[40]='404000000'.$ls_ceros;
			$la_cuenta[41]='405000000'.$ls_ceros;
			$la_cuenta[42]='405020000'.$ls_ceros;
			$la_cuenta[43]='405030000'.$ls_ceros;
			$la_cuenta[44]='405040000'.$ls_ceros;
			$la_cuenta[45]='405050200'.$ls_ceros;
			$la_cuenta[46]='407000000'.$ls_ceros;
			$la_cuenta[47]='408000000'.$ls_ceros;
			$la_cuenta[48]='408020000'.$ls_ceros;
			$la_cuenta[49]='408060000'.$ls_ceros;
			$la_cuenta[50]='408070300'.$ls_ceros;
			$la_cuenta[51]='411000000'.$ls_ceros;
			$la_cuenta[52]='411070000'.$ls_ceros;
			$la_cuenta[53]='411080000'.$ls_ceros;
			$la_cuenta[54]='411090000'.$ls_ceros;
			$la_cuenta[55]='411110000'.$ls_ceros;
			$la_cuenta[56]='411990000'.$ls_ceros;
			$la_cuenta[57]='412000000'.$ls_ceros;
			
			$ld_asignado_e = 0;
		    $ld_asignado_modificado_e = 0;
		    $ld_programado_e= 0;
		    $ld_ejecutado_e= 0;
		    $ld_variacion_absoluta_e= 0;
		    $ld_variacion_porcentual_e= 0;
		    $ld_programado_acumulado_e= 0;
		    $ld_ejecutado_acumulado_e= 0;
			
			$ld_asignado_i = 0;
		    $ld_asignado_modificado_i = 0;
		    $ld_programado_i= 0;
		    $ld_ejecutado_i= 0;
		    $ld_variacion_absoluta_i= 0;
		    $ld_variacion_porcentual_i= 0;
		    $ld_programado_acumulado_i= 0;
		    $ld_ejecutado_acumulado_i= 0;
			
			
			for($i=2;$i<=57;$i++)
			{
			  $ls_cuenta=$la_cuenta[$i];
			  $rs_ingreso=NULL;
			  $rs_gasto=NULL;
			  $lb_detallar = false;
			  switch(substr($ls_cuenta,0,9))
			  {
			    case '304000000':
				                  $lb_detallar = true;
					 break;
					 
				case '303020000':
				                  $lb_detallar = true;
					 break;
					 
				case '301110000':
				                  $lb_detallar = true;
					 break;
					 
				case '305000000':
				                  $lb_detallar = true;
					 break;
					 
				case '307000000':
				                  $lb_detallar = true;
					 break;
					 
				case '308000000':
				                  $lb_detallar = true;
					 break;
					 
				case '309000000':
				                  $lb_detallar = true;
					 break;
					 
				case '311020000':
				                  $lb_detallar = true;
					 break;
				
				case '312990000':
				                  $lb_detallar = true;
					 break;
					 
				case '411990000':
				                  $lb_detallar = true;
					 break;
					 
				case '412000000':
				                  $lb_detallar = true;
					 break;
			   }
			  if($i<=35)
			  {
			   $lb_valido=$this->uf_spi_reportes_ingresos_pres_caja_inst_08($ls_cuenta,$adt_fecdes,$adt_fechas,$as_mesdes,$as_meshas,$lb_detallar,&$rs_ingreso);
			   if(!$rs_ingreso->EOF)
			   {
				 while(!$rs_ingreso->EOF)
				 {
				   $ls_spi_cuenta=$rs_ingreso->fields["spi_cuenta"];
				   $ls_denominacion="";
				   $lb_valido=$this->uf_spg_reportes_select_denominacion($ls_spi_cuenta,$ls_denominacion);
				   if(empty($ls_denominacion))
				   {
				    $ls_denominacion = $rs_ingreso->fields["denominacion"];
				   }
				   $ls_status=$rs_ingreso->fields["status"];
				   $ld_previsto=$rs_ingreso->fields["previsto"];
				   $ld_trimetreI=$rs_ingreso->fields["trimestrei"]; 
				   $ld_trimetreII=$rs_ingreso->fields["trimestreii"]; 
				   $ld_trimetreIII=$rs_ingreso->fields["trimestreiii"]; 
				   $ld_trimetreIV=$rs_ingreso->fields["trimestreiv"];
				   $ld_recaudado = 0;
				   $ld_cobrado= 0;
				   $ld_devengado= 0;
				   $ld_aumento= 0;
				   $ld_disminucion= 0; 
				   $ld_recaudado_acumulado= 0;
				   $ld_cobrado_acumulado= 0;
				   $ld_devengado_acumulado= 0;
				   $ld_aumento_acumulado= 0;
				   $ld_disminucion_acumulado= 0;
				   $la_cuentas = NULL; // Variable tipo array que sirve para determinar si para los montos de una cuenta
				                       // se usan todas las cuentas hijas asociadas, o si el monto que muestre es de sólo
									   // algunas, Eje: Para el reporte de Presupuesto de Caja Inst 8, la cuenta totalizadora
									   // 303000000 muestra la suma de las cuentas 303010000, 303020000 y 303990000, el resto
									   // de cuentas será omitido, si existiese la 303030000 no será tomada en cuenta para 
									   // la totalización de la cuenta.
				   switch($ls_spi_cuenta)
				   {
					   case '303000000'.$ls_ceros :
							   $la_cuentas[0] = "303010000".$ls_ceros;
							   $la_cuentas[1] = "303020000".$ls_ceros;
							   $la_cuentas[2] = "303990000".$ls_ceros;
					   break;
					   case '301100000'.$ls_ceros :
							   $la_cuentas[0] = "301100401".$ls_ceros;
							   $la_cuentas[1] = "301100500".$ls_ceros;
							   $la_cuentas[2] = "301100800".$ls_ceros;
					   break;
					   case '306000000'.$ls_ceros :
							   $la_cuentas[0] = "306010000".$ls_ceros;
							   $la_cuentas[1] = "306020000".$ls_ceros;
					   break;
					   case '311000000'.$ls_ceros :
							   $la_cuentas[0] = "311020000".$ls_ceros;
							   $la_cuentas[1] = "311030000".$ls_ceros;
							   $la_cuentas[2] = "311990100".$ls_ceros;
					   break;
					   case '312000000'.$ls_ceros :
							   $la_cuentas[0] = "312010000".$ls_ceros;
							   $la_cuentas[1] = "312020000".$ls_ceros;
							   $la_cuentas[2] = "312030000".$ls_ceros;
							   $la_cuentas[3] = "312040000".$ls_ceros;
							   $la_cuentas[4] = "312050000".$ls_ceros;
							   $la_cuentas[5] = "312060000".$ls_ceros;
							   $la_cuentas[6] = "312070000".$ls_ceros;
							   $la_cuentas[7] = "312080000".$ls_ceros;
							   $la_cuentas[8] = "312090000".$ls_ceros;
							   $la_cuentas[9] = "312990000".$ls_ceros;
					   break;
				   }
				   $lb_valido=$this->uf_spi_ejecutado_trimestral($ls_spi_cuenta,$adt_fecdes,$adt_fechas,&$ld_recaudado,
																 &$ld_cobrado,&$ld_devengado,&$ld_aumento,&$ld_disminucion,$la_cuentas);
				   if($lb_valido)
				   {
					   $lb_valido=$this->uf_spi_ejecutado_acumulado($ls_spi_cuenta,$adt_fechas,&$ld_recaudado_acumulado,
																	&$ld_cobrado_acumulado,&$ld_devengado_acumulado,
																	&$ld_aumento_acumulado,&$ld_disminucion_acumulado,$la_cuentas);
				   }//if
				   if($as_mesdes=='Enero')
				   {
					   $ld_programado_trimestral=$ld_trimetreI;
					   $ld_programado_acumulado=$ld_trimetreI;
				   }//if
				   if($as_mesdes=='Abril')
				   {
					   $ld_programado_trimestral=$ld_trimetreII;
					   $ld_programado_acumulado=$ld_trimetreI+$ld_trimetreII;
				   }//if
				   if($as_mesdes=='Julio')
				   {
					   $ld_programado_trimestral=$ld_trimetreIII;
					   $ld_programado_acumulado=$ld_trimetreI+$ld_trimetreII+$ld_trimetreIII;
				   }//if
				   if($as_mesdes=='Octubre')
				   {
					   $ld_programado_trimestral=$ld_trimetreIV;
					   $ld_programado_acumulado=$ld_trimetreI+$ld_trimetreII+$ld_trimetreIII+$ld_trimetreIV;
				   }//if
				   $ld_previsto_modificado=$ld_previsto+$ld_aumento_acumulado-$ld_disminucion_acumulado;
				   if ($ld_programado_trimestral > 0)
				   {
					$ld_porcentual =($ld_cobrado/$ld_programado_trimestral)*100;
				   }
				   else
				   {
					$ld_porcentual =0;
				   }
				   /// datastore  del reportes
				   $this->dts_reporte->insertRow("cuenta",$ls_spi_cuenta);
				   $this->dts_reporte->insertRow("denominacion",$ls_denominacion);
				   $this->dts_reporte->insertRow("asignado",$ld_previsto);
				   $this->dts_reporte->insertRow("modificado",$ld_previsto_modificado);
				   $this->dts_reporte->insertRow("programado",$ld_programado_trimestral);
				   $this->dts_reporte->insertRow("ejecutado",$ld_cobrado);		
				   $this->dts_reporte->insertRow("absoluto",abs($ld_cobrado-$ld_programado_trimestral));		
				   $this->dts_reporte->insertRow("porcentual",$ld_porcentual);		
				   $this->dts_reporte->insertRow("programado_acumulado",$ld_programado_acumulado);
				   $this->dts_reporte->insertRow("ejecutado_acumulado",$ld_cobrado_acumulado);
				   $rs_ingreso->MoveNext(); 
				 }//while
			   }
			   else
			   {
			    $ls_denominacion="";
			    $lb_valido=$this->uf_spg_reportes_select_denominacion($ls_cuenta,$ls_denominacion);
			    $this->dts_reporte->insertRow("cuenta",$ls_cuenta);
				$this->dts_reporte->insertRow("denominacion",$ls_denominacion);
				$this->dts_reporte->insertRow("asignado",0);
				$this->dts_reporte->insertRow("modificado",0);
				$this->dts_reporte->insertRow("programado",0);
				$this->dts_reporte->insertRow("ejecutado",0);		
				$this->dts_reporte->insertRow("absoluto",0);		
				$this->dts_reporte->insertRow("porcentual",0);		
				$this->dts_reporte->insertRow("programado_acumulado",0);
				$this->dts_reporte->insertRow("ejecutado_acumulado",0);
			   }
			  }
			  else
			  {
			   if($i==36)
			   {
			       $ld_asignado_si=$this->dts_reporte->getValue("asignado",1);
				   $ld_asignado_modificado_si=$this->dts_reporte->getValue("modificado",1);
				   $ld_programado_si=$this->dts_reporte->getValue("programado",1);
				   $ld_ejecutado_si=$this->dts_reporte->getValue("ejecutado",1);
				   $ld_variacion_absoluta_si=$this->dts_reporte->getValue("absoluto",1);
				   $ld_programado_acumulado_si=$this->dts_reporte->getValue("programado_acumulado",1);
				   $ld_ejecutado_acumulado_si=$this->dts_reporte->getValue("ejecutado_acumulado",1);
				  
				   $ld_asignado_i=$this->dts_reporte->getValue("asignado",2);
				   $ld_asignado_modificado_i=$this->dts_reporte->getValue("modificado",2);
				   $ld_programado_i=$this->dts_reporte->getValue("programado",2);
				   $ld_ejecutado_i=$this->dts_reporte->getValue("ejecutado",2);
				   $ld_variacion_absoluta_i=$this->dts_reporte->getValue("absoluto",2);
				   $ld_programado_acumulado_i=$this->dts_reporte->getValue("programado_acumulado",2);
				   $ld_ejecutado_acumulado_i=$this->dts_reporte->getValue("ejecutado_acumulado",2);
				   
				   if (($ld_programado_si+$ld_programado_i)>0)
				   {
					$ld_porcentual = (($ld_ejecutado_si+$ld_ejecutado_i)/($ld_programado_si+$ld_programado_i))*100;
				   }
				   else
				   {
					$ld_porcentual = 0;
				   }
				   
				   $this->dts_reporte->insertRow("cuenta","");
				   $this->dts_reporte->insertRow("denominacion",'<b>SALDO INICIAL + INGRESOS </b>');
				   $this->dts_reporte->insertRow("asignado",$ld_asignado_si+$ld_asignado_i);
				   $this->dts_reporte->insertRow("modificado",$ld_asignado_modificado_si+$ld_asignado_modificado_i);
				   $this->dts_reporte->insertRow("programado",$ld_programado_si+$ld_programado_i);
				   $this->dts_reporte->insertRow("ejecutado",$ld_ejecutado_si+$ld_ejecutado_i);		
				   $this->dts_reporte->insertRow("absoluto",abs(($ld_ejecutado_si+$ld_ejecutado_i)-($ld_programado_si+$ld_programado_i)));		
				   $this->dts_reporte->insertRow("porcentual",$ld_porcentual);		
				   $this->dts_reporte->insertRow("programado_acumulado",$ld_programado_acumulado_si+$ld_programado_acumulado_i);
				   $this->dts_reporte->insertRow("ejecutado_acumulado",$ld_ejecutado_acumulado_si+$ld_ejecutado_acumulado_i);
			   }
			   else
			   {
				   if($i==37)
			       {
				     $la_cuentas_gasto[0]  = '401000000'.$ls_ceros;
					 $la_cuentas_gasto[1]  = '402000000'.$ls_ceros;
					 $la_cuentas_gasto[2]  = '403000000'.$ls_ceros;
					 $la_cuentas_gasto[3]  = '404000000'.$ls_ceros;
					 $la_cuentas_gasto[4]  = '405020000'.$ls_ceros;
					 $la_cuentas_gasto[5]  = '405030000'.$ls_ceros;
					 $la_cuentas_gasto[6]  = '405040000'.$ls_ceros;
					 $la_cuentas_gasto[7]  = '405050200'.$ls_ceros;
					 $la_cuentas_gasto[8]  = '407000000'.$ls_ceros;
					 $la_cuentas_gasto[9]  = '408020000'.$ls_ceros;
					 $la_cuentas_gasto[10] = '408060000'.$ls_ceros;
					 $la_cuentas_gasto[11] = '408070300'.$ls_ceros;
					 $la_cuentas_gasto[12] = '411070000'.$ls_ceros;
					 $la_cuentas_gasto[13] = '411080000'.$ls_ceros;
					 $la_cuentas_gasto[14] = '411090000'.$ls_ceros;
					 $la_cuentas_gasto[15] = '411110000'.$ls_ceros;
					 $la_cuentas_gasto[16] = '411990000'.$ls_ceros;
					 $la_cuentas_gasto[17] = '412000000'.$ls_ceros;
				     
			        $lb_valido=$this->uf_spg_reporte_total_egresos($adt_fecdes,$adt_fechas,$as_mesdes,$as_meshas,$la_cuentas_gasto);
			       }
				   $lb_valido=$this->uf_spg_reportes_ingresos_pres_caja_inst_08($ls_cuenta,$adt_fecdes,$adt_fechas,$as_mesdes,$as_meshas,$ab_detallar,&$rs_gasto);
				   if(!$rs_gasto->EOF)
				   {
					 while(!$rs_gasto->EOF)
					 {
					   $ls_spg_cuenta=$rs_gasto->fields["spg_cuenta"];
					   $ls_denominacion="";
					   $lb_valido=$this->uf_spg_reportes_select_denominacion($ls_spg_cuenta,$ls_denominacion);
					   $ls_status=$rs_gasto->fields["status"];
					   $ld_asignado=$rs_gasto->fields["asignado"];
					   $ld_trimetreI=$rs_gasto->fields["trimestrei"]; 
					   $ld_trimetreII=$rs_gasto->fields["trimestreii"]; 
					   $ld_trimetreIII=$rs_gasto->fields["trimestreiii"]; 
					   $ld_trimetreIV=$rs_gasto->fields["trimestreiv"];
					   $ld_recaudado = 0;
					   $ld_cobrado= 0;
					   $ld_devengado= 0;
					   $ld_aumento= 0;
					   $ld_disminucion= 0; 
					   $ld_recaudado_acumulado= 0;
					   $ld_cobrado_acumulado= 0;
					   $ld_devengado_acumulado= 0;
					   $ld_aumento_acumulado= 0;
					   $ld_disminucion_acumulado= 0;
					   $la_cuentas = NULL;
					   switch($ls_spg_cuenta)
					   {
						   case '405000000'.$ls_ceros :
								   $la_cuentas[0] = "405020000".$ls_ceros;
								   $la_cuentas[1] = "405030000".$ls_ceros;
								   $la_cuentas[2] = "405040000".$ls_ceros;
								   $la_cuentas[3] = "405050200".$ls_ceros;
						   break;
						   case '408000000'.$ls_ceros :
								   $la_cuentas[0] = "408020000".$ls_ceros;
								   $la_cuentas[1] = "408060000".$ls_ceros;
								   $la_cuentas[2] = "408070300".$ls_ceros;
						   break;
						   case '411000000'.$ls_ceros :
								   $la_cuentas[0] = "411070000".$ls_ceros;
								   $la_cuentas[1] = "411080000".$ls_ceros;
								   $la_cuentas[2] = "411090000".$ls_ceros;
								   $la_cuentas[3] = "411110000".$ls_ceros;
								   $la_cuentas[4] = "411990000".$ls_ceros;
						   break;
					   }
					    
					   
					   $lb_valido=$this->uf_spg_ejecutado_trimestral_estado_resultado($ls_spg_cuenta,$adt_fecdes,$adt_fechas,
																					  &$ld_comprometer,&$ld_causado,&$ld_pagado,
																					  &$ld_aumento,&$ld_disminucion,false,true,$la_cuentas);
					   if($lb_valido)
					   {
						   $lb_valido=$this->uf_spg_ejecutado_acumulado_estado_resultado($ls_spg_cuenta,$adt_fechas,&$ld_comprometer_acumulado,
																						 &$ld_causado_acumulado,&$ld_pagado_acumulado,
																						 &$ld_aumento_acumulado,&$ld_disminucion_acumulado,
																						 false,true,$la_cuentas);
					   }//if
					   if($as_mesdes=='Enero')
					   {
						   $ld_programado_trimestral=$ld_trimetreI;
						   $ld_programado_acumulado=$ld_trimetreI;
					   }//if
					   if($as_mesdes=='Abril')
					   {
						   $ld_programado_trimestral=$ld_trimetreII;
						   $ld_programado_acumulado=$ld_trimetreI+$ld_trimetreII;
					   }//if
					   if($as_mesdes=='Julio')
					   {
						   $ld_programado_trimestral=$ld_trimetreIII;
						   $ld_programado_acumulado=$ld_trimetreI+$ld_trimetreII+$ld_trimetreIII;
					   }//if
					   if($as_mesdes=='Octubre')
					   {
						   $ld_programado_trimestral=$ld_trimetreIV;
						   $ld_programado_acumulado=$ld_trimetreI+$ld_trimetreII+$ld_trimetreIII+$ld_trimetreIV;
					   }//if
					   $ld_asignado_modificado=$ld_asignado+$ld_aumento_acumulado-$ld_disminucion_acumulado;
						if ($ld_programado_trimestral > 0)
					   {
						$ld_porcentual =($ld_pagado/$ld_programado_trimestral)*100;
					   }
					   else
					   {
						$ld_porcentual =0;
					   }
					   $this->dts_reporte->insertRow("cuenta",$ls_spg_cuenta);
					   $this->dts_reporte->insertRow("denominacion",$ls_denominacion);
					   $this->dts_reporte->insertRow("asignado",$ld_asignado);
					   $this->dts_reporte->insertRow("modificado",$ld_asignado_modificado);
					   $this->dts_reporte->insertRow("programado",$ld_programado_trimestral);
					   $this->dts_reporte->insertRow("ejecutado",$ld_pagado);		
					   $this->dts_reporte->insertRow("absoluto",abs($ld_pagado-$ld_programado_trimestral));		
					   $this->dts_reporte->insertRow("porcentual",$ld_porcentual);		
					   $this->dts_reporte->insertRow("programado_acumulado",$ld_programado_acumulado);
					   $this->dts_reporte->insertRow("ejecutado_acumulado",$ld_pagado_acumulado);
					   $rs_gasto->MoveNext();
					}//while
				   }
				   else
				   {
					$ls_denominacion="";
					$lb_valido=$this->uf_spg_reportes_select_denominacion($ls_cuenta,$ls_denominacion);
					$this->dts_reporte->insertRow("cuenta",$ls_cuenta);
					$this->dts_reporte->insertRow("denominacion",$ls_denominacion);
					$this->dts_reporte->insertRow("asignado",0);
					$this->dts_reporte->insertRow("modificado",0);
					$this->dts_reporte->insertRow("programado",0);
					$this->dts_reporte->insertRow("ejecutado",0);		
					$this->dts_reporte->insertRow("absoluto",0);		
					$this->dts_reporte->insertRow("porcentual",0);		
					$this->dts_reporte->insertRow("programado_acumulado",0);
					$this->dts_reporte->insertRow("ejecutado_acumulado",0);
				   }
			   }
			  } 	  
			}		
		 }	  
					 
	  return $lb_valido;
	}
	
function uf_spg_reporte_select_saldo_inicial_int_08($as_mesdes,$adt_fecdes,$adt_fechas)
{
 ////////////////////////////////////////////////////////////////////////////////////////////////////////
 //	      Function : uf_spg_reporte_select_saldo_inicial_int_08
 //     Argumentos : as_mesdes   // Nombre del mes con el que comienza el trimestre
 //                  adt_fecdes  // Fecha de inicio del trimestre
 //                  adt_fechas  // Fecha de fin    del trimestre
 //	       Returns : 
 //	   Description : Función que calcula el saldo inicial para el Presupuesto de Caja del Instructivo 8
 //     Creado por : Ing. Arnaldo Suárez
 // Fecha Creación : 26/02/2010                      Fecha última Modificacion :      Hora :
 ///////////////////////////////////////////////////////////////////////////////////////////////////////
  $lb_valido = true;
  $ls_formpre=$_SESSION["la_empresa"]["formpre"];
  $ldt_periodo=$_SESSION["la_empresa"]["periodo"];
  $li_ano=substr($ldt_periodo,0,4);
  $ls_formpre=str_replace('-','',$ls_formpre);
  $li_len=strlen($ls_formpre);
  $li_len=$li_len-9;
  $ls_ceros=$this->io_funciones->uf_cerosderecha("",$li_len);
    
  $ld_salinipro=0;
  $ld_saliniproant=0;
  $ld_salinieje=0;
  $ld_saliniejeant=0;
  $ld_totejeingresos = 0;
  $ld_totejegastos = 0;
  $ld_totingmod = 0;
  $ld_totgasmod = 0;
  $ld_totingpre = 0;
  $ld_totgasasi = 0;
  $ld_totmodant = 0;
  $la_cuentas_ingreso = NULL;
  $la_cuentas_gasto = NULL;
  switch($as_mesdes)
  {
   case 'Enero':
   		 $ls_sql_programado=" SELECT salinipro, salinieje, salinipro as saliniproant  FROM sigesp_empresa WHERE codemp='".$this->ls_codemp."' ";
   break;
   case 'Abril':
         $ls_sql_programado = "SELECT salinipro, salinieje, (salinipro+(SELECT SUM(enero+febrero+marzo) ".
						      "                     FROM spi_cuentas ".
						      "	                 WHERE spi_cuenta IN ('303010000".$ls_ceros."','303020000".$ls_ceros."','303990000".$ls_ceros."','301100401".$ls_ceros."','301100500".$ls_ceros."','301100800".$ls_ceros."','301110000".$ls_ceros."','305000000".$ls_ceros."','306010000".$ls_ceros."',  ".
						      "	 					                  '306020000".$ls_ceros."','307000000".$ls_ceros."','308000000".$ls_ceros."','309000000".$ls_ceros."','311020100".$ls_ceros."','311030000".$ls_ceros."','311990100".$ls_ceros."','312010000".$ls_ceros."','312020000".$ls_ceros."',  ".
						      "						                  '312030000".$ls_ceros."','312040000".$ls_ceros."','312050000".$ls_ceros."','312060000".$ls_ceros."','312070000".$ls_ceros."','312080000".$ls_ceros."','312090000".$ls_ceros."','312990000".$ls_ceros."','313000000".$ls_ceros."'))- ".
						      "				  (SELECT SUM(enero+febrero+marzo) ".
						      "		             FROM spg_cuentas ".
						      "	                  WHERE spg_cuenta IN ('401000000".$ls_ceros."','402000000".$ls_ceros."','403000000".$ls_ceros."','404000000".$ls_ceros."','405020000".$ls_ceros."','405030000".$ls_ceros."','405040000".$ls_ceros."','405050200".$ls_ceros."','407000000".$ls_ceros."', ".
						      "						                   '408020000".$ls_ceros."','408060000".$ls_ceros."','408070300".$ls_ceros."','411070000".$ls_ceros."','411080000".$ls_ceros."','411090000".$ls_ceros."','411110000".$ls_ceros."','411990000".$ls_ceros."','412000000".$ls_ceros."'))) ".
						      "	as saliniproant , ".
                              "(SELECT SUM(previsto) ". 
                              "   FROM spi_cuentas ".
                              " WHERE spi_cuenta IN ('303010000".$ls_ceros."','303020000".$ls_ceros."','303990000".$ls_ceros."', ".
                              "                      '301100401".$ls_ceros."','301100500".$ls_ceros."','301100800".$ls_ceros."', ".
                              "                      '301110000".$ls_ceros."','305000000".$ls_ceros."','306010000".$ls_ceros."', ". 
                              "                      '306020000".$ls_ceros."','307000000".$ls_ceros."','308000000".$ls_ceros."', ".
                              "                      '309000000".$ls_ceros."','311020100".$ls_ceros."','311030000".$ls_ceros."', ".
                              "                      '311990100".$ls_ceros."','312010000".$ls_ceros."','312020000".$ls_ceros."', ". 
                              "                      '312030000".$ls_ceros."','312040000".$ls_ceros."','312050000".$ls_ceros."', ".
                              "                      '312060000".$ls_ceros."','312070000".$ls_ceros."','312080000".$ls_ceros."', ".
                              "                      '312090000".$ls_ceros."','312990000".$ls_ceros."','313000000".$ls_ceros."')) ". 
                              "    as previsto, ". 
                              "(SELECT SUM(asignado) ". 
                              "   FROM spg_cuentas ".
                              "   WHERE spg_cuenta IN ('401000000".$ls_ceros."','402000000".$ls_ceros."','403000000".$ls_ceros."', ".
						      "                        '404000000".$ls_ceros."','405020000".$ls_ceros."','405030000".$ls_ceros."', ".
						      "                        '405040000".$ls_ceros."','405050200".$ls_ceros."','407000000".$ls_ceros."', ". 
						      "                        '408020000".$ls_ceros."','408060000".$ls_ceros."','408070300".$ls_ceros."', ".
                              "                        '411070000".$ls_ceros."','411080000".$ls_ceros."','411090000".$ls_ceros."', ".
						      "                        '411110000".$ls_ceros."','411990000".$ls_ceros."','412000000".$ls_ceros."'))". 
			                  " as asignado".
						      "	FROM sigesp_empresa WHERE codemp='".$this->ls_codemp."' ";
		$ldt_ult_dia=$this->io_fecha->uf_last_day('03',$li_ano);
   break;
   case 'Julio':
        $ls_sql_programado =  "SELECT salinipro, salinieje, (salinipro+(SELECT SUM(enero+febrero+marzo+abril+mayo+junio) ".
						      "                     FROM spi_cuentas ".
						      "	                 WHERE spi_cuenta IN ('303010000".$ls_ceros."','303020000".$ls_ceros."','303990000".$ls_ceros."','301100401".$ls_ceros."','301100500".$ls_ceros."','301100800".$ls_ceros."','301110000".$ls_ceros."','305000000".$ls_ceros."','306010000".$ls_ceros."',  ".
						      "	 					                  '306020000".$ls_ceros."','307000000".$ls_ceros."','308000000".$ls_ceros."','309000000".$ls_ceros."','311020100".$ls_ceros."','311030000".$ls_ceros."','311990100".$ls_ceros."','312010000".$ls_ceros."','312020000".$ls_ceros."',  ".
						      "						                  '312030000".$ls_ceros."','312040000".$ls_ceros."','312050000".$ls_ceros."','312060000".$ls_ceros."','312070000".$ls_ceros."','312080000".$ls_ceros."','312090000".$ls_ceros."','312990000".$ls_ceros."','313000000".$ls_ceros."'))- ".
						      "				  (SELECT SUM(enero+febrero+marzo+abril+mayo+junio) ".
						      "		             FROM spg_cuentas ".
						      "	                  WHERE spg_cuenta IN ('401000000".$ls_ceros."','402000000".$ls_ceros."','403000000".$ls_ceros."','404000000".$ls_ceros."','405020000".$ls_ceros."','405030000".$ls_ceros."','405040000".$ls_ceros."','405050200".$ls_ceros."','407000000".$ls_ceros."', ".
						      "						                   '408020000".$ls_ceros."','408060000".$ls_ceros."','408070300".$ls_ceros."','411070000".$ls_ceros."','411080000".$ls_ceros."','411090000".$ls_ceros."','411110000".$ls_ceros."','411990000".$ls_ceros."','412000000".$ls_ceros."'))) ".
						      "	as saliniproant , ".
                              "(SELECT SUM(previsto) ". 
                              "   FROM spi_cuentas ".
                              " WHERE spi_cuenta IN ('303010000".$ls_ceros."','303020000".$ls_ceros."','303990000".$ls_ceros."', ".
                              "                      '301100401".$ls_ceros."','301100500".$ls_ceros."','301100800".$ls_ceros."', ".
                              "                      '301110000".$ls_ceros."','305000000".$ls_ceros."','306010000".$ls_ceros."', ". 
                              "                      '306020000".$ls_ceros."','307000000".$ls_ceros."','308000000".$ls_ceros."', ".
                              "                      '309000000".$ls_ceros."','311020100".$ls_ceros."','311030000".$ls_ceros."', ".
                              "                      '311990100".$ls_ceros."','312010000".$ls_ceros."','312020000".$ls_ceros."', ". 
                              "                      '312030000".$ls_ceros."','312040000".$ls_ceros."','312050000".$ls_ceros."', ".
                              "                      '312060000".$ls_ceros."','312070000".$ls_ceros."','312080000".$ls_ceros."', ".
                              "                      '312090000".$ls_ceros."','312990000".$ls_ceros."','313000000".$ls_ceros."')) ". 
                              "    as previsto, ". 
                              "(SELECT SUM(asignado) ". 
                              "   FROM spg_cuentas ".
                              "   WHERE spg_cuenta IN ('401000000".$ls_ceros."','402000000".$ls_ceros."','403000000".$ls_ceros."', ".
						      "                        '404000000".$ls_ceros."','405020000".$ls_ceros."','405030000".$ls_ceros."', ".
						      "                        '405040000".$ls_ceros."','405050200".$ls_ceros."','407000000".$ls_ceros."', ". 
						      "                        '408020000".$ls_ceros."','408060000".$ls_ceros."','408070300".$ls_ceros."', ".
                              "                        '411070000".$ls_ceros."','411080000".$ls_ceros."','411090000".$ls_ceros."', ".
						      "                        '411110000".$ls_ceros."','411990000".$ls_ceros."','412000000".$ls_ceros."'))". 
			                  " as asignado".
						      "	FROM sigesp_empresa WHERE codemp='".$this->ls_codemp."' ";
		//echo $ls_sql_programado;
		$ldt_ult_dia=$this->io_fecha->uf_last_day('06',$li_ano);
   break;
   case 'Octubre':
        $ls_sql_programado =  "SELECT salinipro, salinieje, (salinipro+(SELECT SUM(enero+febrero+marzo+abril+mayo+junio+julio+agosto+septiembre) ".
						      "                     FROM spi_cuentas ".
						      "	                 WHERE spi_cuenta IN ('303010000".$ls_ceros."','303020000".$ls_ceros."','303990000".$ls_ceros."','301100401".$ls_ceros."','301100500".$ls_ceros."','301100800".$ls_ceros."','301110000".$ls_ceros."','305000000".$ls_ceros."','306010000".$ls_ceros."',  ".
						      "	 					                  '306020000".$ls_ceros."','307000000".$ls_ceros."','308000000".$ls_ceros."','309000000".$ls_ceros."','311020100".$ls_ceros."','311030000".$ls_ceros."','311990100".$ls_ceros."','312010000".$ls_ceros."','312020000".$ls_ceros."',  ".
						      "						                  '312030000".$ls_ceros."','312040000".$ls_ceros."','312050000".$ls_ceros."','312060000".$ls_ceros."','312070000".$ls_ceros."','312080000".$ls_ceros."','312090000".$ls_ceros."','312990000".$ls_ceros."','313000000".$ls_ceros."'))- ".
						      "				  (SELECT SUM(enero+febrero+marzo+abril+mayo+junio+julio+agosto+septiembre) ".
						      "		             FROM spg_cuentas ".
						      "	                  WHERE spg_cuenta IN ('401000000".$ls_ceros."','402000000".$ls_ceros."','403000000".$ls_ceros."','404000000".$ls_ceros."','405020000".$ls_ceros."','405030000".$ls_ceros."','405040000".$ls_ceros."','405050200".$ls_ceros."','407000000".$ls_ceros."', ".
						      "						                   '408020000".$ls_ceros."','408060000".$ls_ceros."','408070300".$ls_ceros."','411070000".$ls_ceros."','411080000".$ls_ceros."','411090000".$ls_ceros."','411110000".$ls_ceros."','411990000".$ls_ceros."','412000000".$ls_ceros."'))) ".
						      "	as saliniproant, ".
                              "(SELECT SUM(previsto) ". 
                              "   FROM spi_cuentas ".
                              " WHERE spi_cuenta IN ('303010000".$ls_ceros."','303020000".$ls_ceros."','303990000".$ls_ceros."', ".
                              "                      '301100401".$ls_ceros."','301100500".$ls_ceros."','301100800".$ls_ceros."', ".
                              "                      '301110000".$ls_ceros."','305000000".$ls_ceros."','306010000".$ls_ceros."', ". 
                              "                      '306020000".$ls_ceros."','307000000".$ls_ceros."','308000000".$ls_ceros."', ".
                              "                      '309000000".$ls_ceros."','311020100".$ls_ceros."','311030000".$ls_ceros."', ".
                              "                      '311990100".$ls_ceros."','312010000".$ls_ceros."','312020000".$ls_ceros."', ". 
                              "                      '312030000".$ls_ceros."','312040000".$ls_ceros."','312050000".$ls_ceros."', ".
                              "                      '312060000".$ls_ceros."','312070000".$ls_ceros."','312080000".$ls_ceros."', ".
                              "                      '312090000".$ls_ceros."','312990000".$ls_ceros."','313000000".$ls_ceros."')) ". 
                              "    as previsto, ". 
                              "(SELECT SUM(asignado) ". 
                              "   FROM spg_cuentas ".
                              "   WHERE spg_cuenta IN ('401000000".$ls_ceros."','402000000".$ls_ceros."','403000000".$ls_ceros."', ".
						      "                        '404000000".$ls_ceros."','405020000".$ls_ceros."','405030000".$ls_ceros."', ".
						      "                        '405040000".$ls_ceros."','405050200".$ls_ceros."','407000000".$ls_ceros."', ". 
						      "                        '408020000".$ls_ceros."','408060000".$ls_ceros."','408070300".$ls_ceros."', ".
                              "                        '411070000".$ls_ceros."','411080000".$ls_ceros."','411090000".$ls_ceros."', ".
						      "                        '411110000".$ls_ceros."','411990000".$ls_ceros."','412000000".$ls_ceros."'))". 
			                  " as asignado".
						      "	FROM sigesp_empresa WHERE codemp='".$this->ls_codemp."' ";
		$ldt_ult_dia=$this->io_fecha->uf_last_day('09',$li_ano);
   break;
  }
  $fechas=$ldt_ult_dia;
  $ldt_fechas=$this->io_funciones->uf_convertirdatetobd($fechas);
  
  $rs_data=$this->io_sql->select($ls_sql_programado);
  if($rs_data===false)
  {   // error interno sql
	  $this->io_msg->message("CLASE->sigesp_spi_class_reportes_instructivos  MÉTODO->uf_spg_reporte_select_saldo_inicial_int_08  ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
	  $lb_valido = false;
  }
  else
  {
	if(!$rs_data->EOF)
	{
		  $ld_salinipro=$rs_data->fields["salinipro"];
		  $ld_saliniproant=$rs_data->fields["saliniproant"];
		  $ld_salinieje=$rs_data->fields["salinieje"];
		  $ld_totingpre=$rs_data->fields["previsto"];
		  $ld_totgasasi=$rs_data->fields["asignado"];
	}
	$this->io_sql->free_result($rs_data);
  }//else
  
  if($as_mesdes != "Enero")
  {
	  $la_cuentas_ingreso[0]  = '303010000'.$ls_ceros;
	  $la_cuentas_ingreso[1]  = '303020000'.$ls_ceros;
	  $la_cuentas_ingreso[2]  = '303990000'.$ls_ceros;
	  $la_cuentas_ingreso[3]  = '301100401'.$ls_ceros;
	  $la_cuentas_ingreso[4]  = '301100500'.$ls_ceros;
	  $la_cuentas_ingreso[5]  = '301100800'.$ls_ceros;
	  $la_cuentas_ingreso[6]  = '301110000'.$ls_ceros;
	  $la_cuentas_ingreso[7]  = '305000000'.$ls_ceros;
	  $la_cuentas_ingreso[8]  = '306010000'.$ls_ceros;
	  $la_cuentas_ingreso[9]  = '306020000'.$ls_ceros;
	  $la_cuentas_ingreso[10] = '307000000'.$ls_ceros;
	  $la_cuentas_ingreso[11] = '308000000'.$ls_ceros;
	  $la_cuentas_ingreso[12] = '309000000'.$ls_ceros;
	  $la_cuentas_ingreso[13] = '311020100'.$ls_ceros;
	  $la_cuentas_ingreso[14] = '311030000'.$ls_ceros;
	  $la_cuentas_ingreso[15] = '311990100'.$ls_ceros;
	  $la_cuentas_ingreso[16] = '312010000'.$ls_ceros;
	  $la_cuentas_ingreso[17] = '312020000'.$ls_ceros;
	  $la_cuentas_ingreso[18] = '312030000'.$ls_ceros;
	  $la_cuentas_ingreso[19] = '312040000'.$ls_ceros;
	  $la_cuentas_ingreso[20] = '312050000'.$ls_ceros;
	  $la_cuentas_ingreso[21] = '312060000'.$ls_ceros;
	  $la_cuentas_ingreso[22] = '312070000'.$ls_ceros;
	  $la_cuentas_ingreso[23] = '312080000'.$ls_ceros;
	  $la_cuentas_ingreso[24] = '312090000'.$ls_ceros;
	  $la_cuentas_ingreso[25] = '312990000'.$ls_ceros;
	  $la_cuentas_ingreso[26] = '313000000'.$ls_ceros;
	  
	  $li_toting = count($la_cuentas_ingreso);
	  for($i=0;$i<$li_toting;$i++)
	  {
		$lb_valido=$this->uf_spi_ejecutado_acumulado($la_cuentas_ingreso[$i],$ldt_fechas,&$ld_recaudado_acumulado,
																		&$ld_cobrado_acumulado,&$ld_devengado_acumulado,
																		&$ld_aumento_acumulado,&$ld_disminucion_acumulado);
		$ld_totejeingresos += $ld_cobrado_acumulado;
		$ld_totingmod +=  $ld_aumento_acumulado - $ld_disminucion_acumulado; 
	  }
	  
	  $la_cuentas_gasto[0]  = '401000000'.$ls_ceros;
	  $la_cuentas_gasto[1]  = '402000000'.$ls_ceros;
	  $la_cuentas_gasto[2]  = '403000000'.$ls_ceros;
	  $la_cuentas_gasto[3]  = '404000000'.$ls_ceros;
	  $la_cuentas_gasto[4]  = '405020000'.$ls_ceros;
	  $la_cuentas_gasto[5]  = '405030000'.$ls_ceros;
	  $la_cuentas_gasto[6]  = '405040000'.$ls_ceros;
	  $la_cuentas_gasto[7]  = '405050200'.$ls_ceros;
	  $la_cuentas_gasto[8]  = '407000000'.$ls_ceros;
	  $la_cuentas_gasto[9]  = '408020000'.$ls_ceros;
	  $la_cuentas_gasto[10] = '408060000'.$ls_ceros;
	  $la_cuentas_gasto[11] = '408070300'.$ls_ceros;
	  $la_cuentas_gasto[12] = '411070000'.$ls_ceros;
	  $la_cuentas_gasto[13] = '411080000'.$ls_ceros;
	  $la_cuentas_gasto[14] = '411090000'.$ls_ceros;
	  $la_cuentas_gasto[15] = '411110000'.$ls_ceros;
	  $la_cuentas_gasto[16] = '411990000'.$ls_ceros;
	  $la_cuentas_gasto[17] = '412000000'.$ls_ceros;
	  
	  $li_totgas = count($la_cuentas_gasto);
	  for($j=0;$j<$li_totgas;$j++)
	  {
		$lb_valido=$this->uf_spg_ejecutado_acumulado_estado_resultado($la_cuentas_gasto[$j],$ldt_fechas,&$ld_comprometer_acumulado,
																	 &$ld_causado_acumulado,&$ld_pagado_acumulado,
																	 &$ld_aumento_acumulado,&$ld_disminucion_acumulado,
																	 false,true);
		$ld_totejegastos += $ld_pagado_acumulado;
		$ld_totgasmod +=  ($ld_aumento_acumulado - $ld_disminucion_acumulado);  
	  }
	  $ld_saliniejeant = $ld_salinieje + $ld_totejeingresos - $ld_totejegastos;
  }
  else
  {
   $ld_saliniejeant = $ld_salinieje;
  }
  $ld_totingpre += $ld_totingmod;
  $ld_totgasasi += $ld_totgasmod;
  $ld_totmodant = $ld_totingpre - $ld_totgasasi;
  if ($ld_saliniproant > 0)
  { 
   $ld_porcentual =($ld_saliniejeant/$ld_saliniproant)*100;
  }
  else
  {
   $ld_porcentual =0;
  }
  $this->dts_reporte->insertRow("cuenta","");
  $this->dts_reporte->insertRow("denominacion",'<b>SALDO INICIAL</b>');
  $this->dts_reporte->insertRow("asignado",$ld_salinipro);
  $this->dts_reporte->insertRow("modificado",$ld_totmodant);
  $this->dts_reporte->insertRow("programado",$ld_saliniproant);
  $this->dts_reporte->insertRow("ejecutado", $ld_saliniejeant);		
  $this->dts_reporte->insertRow("absoluto",abs($ld_saliniejeant-$ld_saliniproant));		
  $this->dts_reporte->insertRow("porcentual",$ld_porcentual);		
  $this->dts_reporte->insertRow("programado_acumulado",$ld_salinipro);
  $this->dts_reporte->insertRow("ejecutado_acumulado", $ld_salinieje); 
   
  return $lb_valido;
}
 
/********************************************************************************************************************************/
/*                                                        FIN PRESUPUESTO DE CAJA INSTRUCTIVO 08                                                 */
/********************************************************************************************************************************/
}//fin de clase
?>