<?php
class sigesp_scg_class_bal_general
{
	var $la_empresa;
	var $io_fun;
	var $io_sql;
	var $io_sql_aux;
	var $io_msg;
	var $int_scg;
	var $ds_reporte;
	var $ds_prebalance;
	var $ds_balance1;
	var $ds_cuentas;
	var $ds_cuentas_acreedoras;
	var $ia_niveles;
	var $io_fecha;
	var $ls_gestor;
	var $int_spi;
	var $int_spg;
	var $ls_activo;
	var $ls_pasivo;
	var $ls_resultado;
	var $ls_cta_resultado;
	var $ls_capital;
	var $ls_ingreso;
	var $ls_gastos; 
	var $ls_orden_d;
	var $ls_orden_h;
	var $ds_ctas_temp;
	
	function sigesp_scg_class_bal_general()
	{
		$this->io_fun = new class_funciones();
		$this->siginc=new sigesp_include();
		$this->con=$this->siginc->uf_conectar();
		$this->io_sql= new class_sql($this->con);
		$this->io_sql_aux= new class_sql($this->con);
		$this->io_msg= new class_mensajes();		
		$this->io_fecha=new class_fecha();
		$this->la_empresa=$_SESSION["la_empresa"];
		$this->ds_reporte=new class_datastore();
		$this->ds_Prebalance=new class_datastore();
        $this->ds_reg_niveles=new class_datastore(); 
		$this->ds_Balance1=new class_datastore();
		$this->ds_cuentas=new class_datastore();
		$this->ds_reporte=new class_datastore();
        $this->ds_reportef=new class_datastore();
		$this->ds_cuentas_acreedoras=new class_datastore();
		$this->ds_ctas_temp=new class_datastore();
		$this->int_scg=new class_sigesp_int_scg();
		$this->ls_gestor = $_SESSION["ls_gestor"];
		$this->ia_niveles=array();
	}
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	////////************************************BALANCE GENERAL*************************************************////////////////
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	function uf_escalera_cuenta($cuenta)
	{
		$formato = $this->la_empresa["formcont"];
		$auxArr = explode("-",$formato);
		$auxtam  =0;
		$j = 0;
		for($i=0;$i<count($auxArr);$i++)
		{
			if(strlen($auxArr[$i])>0)
			{
				$auxtam+=strlen($auxArr[$i]);
				$arrEsc[$j] = str_pad(substr($cuenta,0,$auxtam),strlen($cuenta),"0");
				$j++;
			}
		}
		return $arrEsc;
	}
	
	
	function uf_balance_general($ad_fecfin,$ai_nivel)
	{
		$lb_valido=true;
		$ds_Balance2=new class_datastore();
		$ldec_resultado=0;
		$ld_saldo_ganancia=0;
		$this->ls_activo=trim($this->la_empresa["activo"]);
		$this->ls_pasivo=trim($this->la_empresa["pasivo"]);
		$this->cuentaresultado = trim($this->la_empresa["c_resultad"]);
		$this->criteriores = substr($this->cuentaresultado,0,5);
	//	$this->criteriores = array(substr($this->cuentaresultado,0,5));
		$arrCuenta = $this->uf_escalera_cuenta($this->cuentaresultado);
		$this->ls_resultado=trim($this->la_empresa["resultado"]);
		$this->ls_capital=trim($this->la_empresa["capital"]);
		$this->ls_orden_d=trim($this->la_empresa["orden_d"]);
		$this->ls_orden_h=trim($this->la_empresa["orden_h"]);
		$this->ls_ingreso=trim($this->la_empresa["ingreso"]);
		$this->ls_gastos =trim($this->la_empresa["gasto"]);
		$this->ls_cta_resultado = trim($this->la_empresa["c_resultad"]);
		$ad_fecfin=$this->io_fun->uf_convertirdatetobd($ad_fecfin);
		$ls_codemp=$this->la_empresa["codemp"];
		$as_sc_cuenta='';
        $as_denominacion='';
        $as_status='';
        $as_rnivel='';
        $ad_total_debe='';
        $ad_total_haber='';
        
        if($_SESSION["ls_gestor"]=='INFORMIX')
        {            
             $ls_sql="SELECT SC.sc_cuenta,SC.denominacion,SC.status,SC.nivel as rnivel, 
                             (select case sum(debe_mes) when null then 0 else sum(debe_mes) end FROM scg_saldos WHERE codemp='".$ls_codemp."' AND fecsal<='".$ad_fecfin."' AND sc_cuenta=SC.sc_cuenta GROUP BY codemp,sc_cuenta) as total_debe,
                             (select case sum(haber_mes) when null then 0 else sum(haber_mes) end FROM scg_saldos WHERE codemp='".$ls_codemp."' AND fecsal<='".$ad_fecfin."' AND sc_cuenta=SC.sc_cuenta GROUP BY codemp,sc_cuenta) as total_haber,
                             0 as nivel,SC.cueproacu  
                      FROM scg_cuentas SC 
                      where  (SC.sc_cuenta like '".$this->ls_activo."%' OR SC.sc_cuenta like '".$this->ls_pasivo."%' 
                           OR SC.sc_cuenta like '".$this->ls_resultado."%' OR SC.sc_cuenta like '".$this->ls_capital."%' 
                           OR SC.sc_cuenta like '".$this->ls_orden_d."%' OR SC.sc_cuenta like '".$this->ls_orden_h."%') 
                      ORDER BY SC.sc_cuenta ";
        }
        else
        {
        /* $ls_sql=" SELECT SC.sc_cuenta,SC.denominacion,SC.status,SC.nivel as rnivel, ".
              "        coalesce(curSaldo.T_Debe,0) as total_debe, ".
              "        coalesce(curSaldo.T_Haber,0) as total_haber,0 as nivel,trim(SC.cueproacu) as cueproacu ".
              " FROM scg_cuentas SC LEFT OUTER JOIN (SELECT codemp,sc_cuenta, coalesce(sum(debe_mes),0)as T_Debe, ".
              "                                             coalesce(sum(haber_mes),0) as T_Haber ".
              "                                      FROM   scg_saldos ".
              "                                      WHERE  codemp='".$ls_codemp."' AND fecsal<='".$ad_fecfin."' ".
              "                                      GROUP BY codemp,sc_cuenta) curSaldo ".
              " ON SC.sc_cuenta=curSaldo.sc_cuenta ".
              " WHERE SC.codemp=curSaldo.codemp AND  curSaldo.codemp='".$ls_codemp."' AND ".
              "       (SC.sc_cuenta like '".$this->ls_activo."%' OR SC.sc_cuenta like '".$this->ls_pasivo."%' OR ".
              "        SC.sc_cuenta like '".$this->ls_resultado."%' OR  SC.sc_cuenta like '".$this->ls_capital."%' OR ".
              "        SC.sc_cuenta like '".$this->ls_orden_d."%' OR SC.sc_cuenta like '".$this->ls_orden_h."%') ".
              " ORDER BY trim(SC.sc_cuenta) "; */
			  
			$ls_sql=  " SELECT SC.sc_cuenta,SC.denominacion,SC.status,SC.nivel as rnivel, ".
					  "        coalesce(curSaldo.T_Debe,0) as total_debe, ".
					  "        coalesce(curSaldo.T_Haber,0) as total_haber,0 as nivel,trim(SC.cueproacu) as cueproacu, 1 as tiporden ".
					  " FROM scg_cuentas SC LEFT OUTER JOIN (SELECT codemp,sc_cuenta, coalesce(sum(debe_mes),0)as T_Debe, ".
					  "                                             coalesce(sum(haber_mes),0) as T_Haber ".
					  "                                      FROM   scg_saldos ".
					  "                                      WHERE  codemp='".$ls_codemp."' AND fecsal<='".$ad_fecfin."' ".
					  "                                      GROUP BY codemp,sc_cuenta) curSaldo ".
					  " ON SC.sc_cuenta=curSaldo.sc_cuenta ".
					  " WHERE SC.codemp=curSaldo.codemp AND  curSaldo.codemp='".$ls_codemp."' AND ".
					  "       (SC.sc_cuenta like '".$this->ls_activo."%' OR SC.sc_cuenta like '".$this->ls_orden_d."%') ".
					  "UNION".
					  " SELECT SC.sc_cuenta,SC.denominacion,SC.status,SC.nivel as rnivel, ".
					  "        coalesce(curSaldo.T_Debe,0) as total_debe, ".
					  "        coalesce(curSaldo.T_Haber,0) as total_haber,0 as nivel,trim(SC.cueproacu) as cueproacu, 2 as tiporden ".
					  " FROM scg_cuentas SC LEFT OUTER JOIN (SELECT codemp,sc_cuenta, coalesce(sum(debe_mes),0)as T_Debe, ".
					  "                                             coalesce(sum(haber_mes),0) as T_Haber ".
					  "                                      FROM   scg_saldos ".
					  "                                      WHERE  codemp='".$ls_codemp."' AND fecsal<='".$ad_fecfin."' ".
					  "                                      GROUP BY codemp,sc_cuenta) curSaldo ".
					  " ON SC.sc_cuenta=curSaldo.sc_cuenta ".
					  " WHERE SC.codemp=curSaldo.codemp AND  curSaldo.codemp='".$ls_codemp."' AND ".
					  "       (SC.sc_cuenta like '".$this->ls_pasivo."%' OR SC.sc_cuenta like '".$this->ls_resultado."%' OR  SC.sc_cuenta like '".$this->ls_capital."%' )".
					  //"        SC.sc_cuenta like '".$this->ls_orden_h."%') ".
					  " ORDER BY 9,1";
        }
	 //echo $ls_sql."<br>";
     $rs_data=$this->io_sql->select($ls_sql);
     if($rs_data===false)
     {// error interno sql
        $this->is_msg_error="Error en consulta metodo uf_scg_reporte_balance_general ".$this->io_fun->uf_convertirmsg($this->io_sql->message);
        //print $this->io_sql->message;
        $lb_valido = false;
     }
	 else
	 {
        $ld_saldo_ganancia=0;
		while($row=$this->io_sql->fetch_row($rs_data))
		{
		  $ls_sc_cuenta=$row["sc_cuenta"];
		  $ls_denominacion=$row["denominacion"];
		  $ls_status=$row["status"];
		  $ls_rnivel=$row["rnivel"];
		  $ld_total_debe=$row["total_debe"];
		  $ld_total_haber=$row["total_haber"];
          $ls_cueproacu = $row["cueproacu"];
		 
		  if($ls_status=="C")
		  {
    		$ls_nivel="4";		
		  }//if
		  else
		  {
    		$ls_nivel=$ls_rnivel;		
		  }//else
		 if($ls_nivel<=$ai_nivel)
		  {
			  $this->ds_Prebalance->insertRow("sc_cuenta",$ls_sc_cuenta);          // print "ls_sc_cuenta  :  $ls_sc_cuenta <br> " ;
			  $this->ds_Prebalance->insertRow("denominacion",$ls_denominacion);
			  $this->ds_Prebalance->insertRow("status",$ls_status);
			  $this->ds_Prebalance->insertRow("nivel",$ls_nivel);
			  $this->ds_Prebalance->insertRow("rnivel",$ls_rnivel);
			  $this->ds_Prebalance->insertRow("total_debe",$ld_total_debe);
			  $this->ds_Prebalance->insertRow("total_haber",$ld_total_haber);
              $this->ds_Prebalance->insertRow("cueproacu",0);
		      $lb_valido = true;
		  }//if
          if (!empty($ls_cueproacu))
          {
              $lb_valido2 = $this->uf_scg_cueproacu_saldo($ls_cueproacu,$ad_fecfin,&$as_sc_cuenta,&$as_denominacion,&$as_status ,&$as_rnivel,&$ad_total_debe,&$ad_total_haber );
              if($lb_valido2)
              {
                  if($as_status=="C")
                  {
                    $ls_nivel="4";        
                  }//if
                  else
                  {
                    $ls_nivel=$ls_rnivel;        
                  }//else
                  if($ls_nivel<=$ai_nivel)
                  {
					  $this->ds_Prebalance->insertRow("sc_cuenta",$as_sc_cuenta);            
                      $this->ds_Prebalance->insertRow("denominacion",$as_denominacion);
                      $this->ds_Prebalance->insertRow("status",$as_status);
                      $this->ds_Prebalance->insertRow("nivel",$ls_nivel);
                      $this->ds_Prebalance->insertRow("rnivel",$as_rnivel);
                      $this->ds_Prebalance->insertRow("total_debe",$ad_total_debe);
                      $this->ds_Prebalance->insertRow("total_haber",$ad_total_haber);
                      $this->ds_Prebalance->insertRow("cueproacu",1);
                      $lb_valido2 = true;         // print "incluy�  $as_sc_cuenta <br>";
                  }//if  
              }   
          }
		}//while
	    $li=$this->ds_Prebalance->getRowCount("sc_cuenta");
		if($li==0)
		{
		  $lb_valido = false;
		  return false;
		}//if
	 } //else
	 $ld_saldo_i=0;			
	 if($lb_valido)
	 {
	   $lb_valido=$this->uf_scg_reporte_select_saldo_ingreso_BG($ad_fecfin,$this->ls_ingreso,$ld_saldo_i);
	 } 
     if($lb_valido)
	 {
       $ld_saldo_g=0;	 
	   $lb_valido=$this->uf_scg_reporte_select_saldo_gasto_BG($ad_fecfin,$this->ls_gastos,$ld_saldo_g);  
	 }//if
	 if($lb_valido)
	 {
	   $ld_saldo_ganancia=($ld_saldo_ganancia+($ld_saldo_i-$ld_saldo_g))*-1;
	 }//if
	 $la_sc_cuenta=array();
	 $la_denominacion=array();
	 $la_saldo=array();
	 for($i=1;$i<=$ai_nivel;$i++)
	 {
		 $la_sc_cuenta[$i]="";
		 $la_denominacion[$i]="";
		 $la_saldo[$i]=0;
	 }//for
	 $li_nro_reg=0;
     $ld_saldo_resultado=0;
	 $li_row=$this->ds_Prebalance->getRowCount("sc_cuenta");	
	 for($li_z=1;$li_z<=$li_row;$li_z++)
	 {
		$ls_sc_cuenta=$this->ds_Prebalance->getValue("sc_cuenta",$li_z);
        $ls_cueproacu=$this->ds_Prebalance->getValue("cueproacu",$li_z); 
		$ldec_debe=$this->ds_Prebalance->getValue("total_debe",$li_z);
		$ldec_haber=$this->ds_Prebalance->getValue("total_haber",$li_z);
		$li_nivel=$this->ds_Prebalance->getValue("nivel",$li_z);	 
		$ls_denominacion=$this->ds_Prebalance->getValue("denominacion",$li_z);	
		$ls_tipo_cuenta=substr($ls_sc_cuenta,0,1);
        if  ($ls_cueproacu==1)
        {
             $ls_tipo_cuenta=$this->ls_activo;
        }
	 	switch($ls_tipo_cuenta){
			case $this->ls_activo:
				$ls_orden=1;
				break;
			case $this->ls_pasivo:
				$ls_orden=2;
				break;
			case $this->ls_capital:
				$ls_orden=3;
				break;				
			case $this->ls_resultado:
				$ls_orden=4;
				break;
			case $this->ls_orden_d:
				$ls_orden=5;
				break;		
			case $this->ls_orden_h:
				$ls_orden=6;
				break;
			default:
				$ls_orden=7;		
		}
		
		$ldec_saldo=$ldec_debe-$ldec_haber;
			
		if((($ls_tipo_cuenta==$this->ls_pasivo)||($ls_tipo_cuenta==$this->ls_resultado)||($ls_tipo_cuenta==$this->ls_capital))&&($li_nivel==1))
		{
			if($ldec_saldo<0)
			{
				$ldec_saldoAux = abs($ldec_saldo);
			}
			else
			{
				$ldec_saldoAux = $ldec_saldo;
			}
			$ld_saldo_resultado=$ld_saldo_resultado+$ldec_saldoAux;
		}	
		
		if($li_nivel==4)	
		{
			$li_nro_reg=$li_nro_reg+1;
			$this->ds_Balance1->insertRow("orden",$ls_orden);
		    $this->ds_Balance1->insertRow("num_reg",$li_nro_reg);
		    $this->ds_Balance1->insertRow("sc_cuenta",$ls_sc_cuenta);
		    $this->ds_Balance1->insertRow("denominacion",$ls_denominacion);
			$this->ds_Balance1->insertRow("nivel",$li_nivel);
			$this->ds_Balance1->insertRow("saldo",$ldec_saldo);
		}
		else
		{
			if(empty($la_sc_cuenta[$li_nivel]))
			{
			   $la_sc_cuenta[$ls_nivel]=$ls_sc_cuenta;
			   $la_denominacion[$ls_nivel]=$ls_denominacion;
			   $la_saldo[$ls_nivel]=$ldec_saldo;
		       $li_nro_reg=$li_nro_reg+1;
			   $this->ds_Balance1->insertRow("orden",$ls_orden);
			   $this->ds_Balance1->insertRow("num_reg",$li_nro_reg);
			   $this->ds_Balance1->insertRow("sc_cuenta",$ls_sc_cuenta);       // print "A: $ls_sc_cuenta : $ls_nivel , <br>";      
			   $this->ds_Balance1->insertRow("denominacion",$ls_denominacion);
			   $this->ds_Balance1->insertRow("nivel",-$li_nivel);
			   $this->ds_Balance1->insertRow("saldo",$ldec_saldo);
			}
			else
			{
			   $this->uf_scg_reporte_calcular_total_BG($li_nro_reg,$ls_prev_nivel,$ls_nivel,$la_sc_cuenta,$la_denominacion,$la_saldo); 
			   $la_sc_cuenta[$ls_nivel]=$ls_sc_cuenta;
			   $la_denominacion[$ls_nivel]=$ls_denominacion;
			   $la_saldo[$ls_nivel]=$ldec_saldo;
		       $li_nro_reg=$li_nro_reg+1;
			   $this->ds_Balance1->insertRow("orden",$ls_orden);
			   $this->ds_Balance1->insertRow("num_reg",$li_nro_reg);
			   $this->ds_Balance1->insertRow("sc_cuenta",$ls_sc_cuenta);       
			   $this->ds_Balance1->insertRow("denominacion",$ls_denominacion);
			   $this->ds_Balance1->insertRow("nivel",-$li_nivel);
			   $this->ds_Balance1->insertRow("saldo",$ldec_saldo);
			}
		}

		$ls_prev_nivel=$li_nivel;            
	 }
	 $this->uf_scg_reporte_calcular_total_BG(&$li_nro_reg,$ls_prev_nivel,1,$la_sc_cuenta,$la_denominacion,$la_saldo); 			
	 $ld_saldo_resultado=($ld_saldo_resultado+$ld_saldo_ganancia);
	 $this->uf_scg_reporte_actualizar_resultado_BG($this->ls_cta_resultado,$ld_saldo_ganancia,$li_nro_reg,$ls_orden,$ai_nivel); 

	 $li_total=$this->ds_Balance1->getRowCount("sc_cuenta");
	                                                                      
	 for ($li_i=1;$li_i<=$li_total;$li_i++)
	     {	
		   $ls_sc_cuenta    = $this->ds_Balance1->data["sc_cuenta"][$li_i];
		   $ls_orden        = $this->ds_Balance1->data["orden"][$li_i];
		   $li_nro_reg      = $this->ds_Balance1->data["num_reg"][$li_i];
		   $ls_denominacion = $this->ds_Balance1->data["denominacion"][$li_i];
		   $ls_nivel        = $this->ds_Balance1->data["nivel"][$li_i];
		   $ld_saldo        = $this->ds_Balance1->data["saldo"][$li_i];
		   $li_pos          = $this->ds_Prebalance->find("sc_cuenta",$ls_sc_cuenta);
		   if ($li_pos>0)
		      { 
		        $ls_rnivel=$this->ds_Prebalance->data["rnivel"][$li_pos];
		      }
		   else
		      {
		        $ls_rnivel=0;
		      }
           if ($ls_nivel<=$ai_nivel)   
           {
	           $ds_Balance2->insertRow("orden",$ls_orden);
	           $ds_Balance2->insertRow("num_reg",$li_nro_reg);
	           $ds_Balance2->insertRow("sc_cuenta",$ls_sc_cuenta);            //print "$ls_sc_cuenta : $ls_nivel , <br>";  
	           $ds_Balance2->insertRow("denominacion",$ls_denominacion);
	           $ds_Balance2->insertRow("nivel",$ls_nivel);
	           $ds_Balance2->insertRow("saldo",$ld_saldo);
	           $ds_Balance2->insertRow("rnivel",$ls_rnivel);
		       $ds_Balance2->insertRow("total",$ld_saldo_resultado);
           }
	     }
	 $li_tot = $ds_Balance2->getRowCount("sc_cuenta");
	 
	 for ($li_i=1;$li_i<=$li_tot;$li_i++)
	 { 
		   $ls_sc_cuenta       = $ds_Balance2->data["sc_cuenta"][$li_i];
		   $ls_orden           = $ds_Balance2->data["orden"][$li_i];
		   $li_nro_reg         = $ds_Balance2->data["num_reg"][$li_i];
		   $ls_denominacion    = $ds_Balance2->data["denominacion"][$li_i];
		   $ls_nivel           = $ds_Balance2->data["nivel"][$li_i];
		   $ld_saldo           = $ds_Balance2->data["saldo"][$li_i];
		   $ls_rnivel          = $ds_Balance2->data["rnivel"][$li_i];
		   $ld_saldo_resultado = $ds_Balance2->data["total"][$li_i];
	   
		   if ($ls_rnivel<=$ai_nivel)
		      {
		      	
			    $this->ds_reporte->insertRow("orden",$ls_orden);
			    $this->ds_reporte->insertRow("num_reg",$li_nro_reg);
			    $this->ds_reporte->insertRow("sc_cuenta",$ls_sc_cuenta);
			    $this->ds_reporte->insertRow("denominacion",$ls_denominacion);
			    $this->ds_reporte->insertRow("nivel",$ls_nivel);
			    $this->ds_reporte->insertRow("saldo",$ld_saldo);
			    $this->ds_reporte->insertRow("rnivel",$ls_rnivel);
			    $this->ds_reporte->insertRow("total",$ld_saldo_resultado);
		      }	  
	  }
     unset($this->ds_Prebalance,$this->ds_Balance1,$ds_Balance2);
	 //var_dump($this->ds_reporte->data);
	 return $lb_valido;  
	}
    
/****************************************************************************************************************************************/    
    function uf_balance_general_formato2($ad_fecfin,$ai_nivel)
    {
        $lb_valido=true;
        $ds_Balance2=new class_datastore();
        $ds_reg_niveles = new class_datastore();
        $ldec_resultado=0;
        $ld_saldo_ganancia=0;
        $this->ls_activo=trim($this->la_empresa["activo"]);
        $this->ls_pasivo=trim($this->la_empresa["pasivo"]);
        $this->cuentaresultado = trim($this->la_empresa["c_resultad"]);
        $this->criteriores = substr($this->cuentaresultado,0,5);
    //    $this->criteriores = array(substr($this->cuentaresultado,0,5));
        $arrCuenta = $this->uf_escalera_cuenta($this->cuentaresultado);
        $this->ls_resultado=trim($this->la_empresa["resultado"]);
        $this->ls_capital=trim($this->la_empresa["capital"]);
        $this->ls_orden_d=trim($this->la_empresa["orden_d"]);
        $this->ls_orden_h=trim($this->la_empresa["orden_h"]);
        $this->ls_ingreso=trim($this->la_empresa["ingreso"]);
        $this->ls_gastos =trim($this->la_empresa["gasto"]);
        $this->ls_cta_resultado = trim($this->la_empresa["c_resultad"]);
        $ad_fecfin=$this->io_fun->uf_convertirdatetobd($ad_fecfin);
        $ls_codemp=$this->la_empresa["codemp"];
		$ls_ceros = "";
		$ls_formcont = trim($this->la_empresa["formcont"]);
		$ls_formcont = trim(str_replace("-","",$ls_formcont));
		$ls_ceros = str_pad("",strlen($ls_formcont)-strlen(trim(substr($this->ls_orden_d,0,1))),"0");
		$ls_cuenta_tot_deudora = trim(substr($this->ls_orden_d,0,1));
		if(!empty($ls_cuenta_tot_deudora))
		{
		 $ls_cuenta_tot_deudora .= $ls_ceros;
		}
		else
		{
		 $ls_cuenta_tot_deudora = "";
		}
        $as_sc_cuenta='';
        $as_denominacion='';
        $as_status='';
        $as_rnivel='';
        $ad_total_debe='';
        $ad_total_haber='';        
        $aa_cuentas_pa = array();
        $aa_cuentas_pa = $this->uf_scg_crea_array_cueproacu();
                          
        if($_SESSION["ls_gestor"]=='INFORMIX')
        {            
             $ls_sql="SELECT SC.sc_cuenta,SC.denominacion,SC.status,SC.nivel as rnivel,sc.referencia as referencia,  
                             (select case sum(debe_mes) when null then 0 else sum(debe_mes) end FROM scg_saldos WHERE codemp='".$ls_codemp."' AND fecsal<='".$ad_fecfin."' AND sc_cuenta=SC.sc_cuenta GROUP BY codemp,sc_cuenta) as total_debe,
                             (select case sum(haber_mes) when null then 0 else sum(haber_mes) end FROM scg_saldos WHERE codemp='".$ls_codemp."' AND fecsal<='".$ad_fecfin."' AND sc_cuenta=SC.sc_cuenta GROUP BY codemp,sc_cuenta) as total_haber,
                             0 as nivel,SC.cueproacu  
                      FROM scg_cuentas SC 
                      where  (SC.sc_cuenta like '".$this->ls_activo."%' OR SC.sc_cuenta like '".$this->ls_pasivo."%' 
                           OR SC.sc_cuenta like '".$this->ls_resultado."%' OR SC.sc_cuenta like '".$this->ls_capital."%' 
                           OR SC.sc_cuenta like '".$this->ls_orden_d."%' OR SC.sc_cuenta like '".$this->ls_orden_h."%') 
                      ORDER BY SC.sc_cuenta ";
        }
        else
        {
         /*$ls_sql=" SELECT SC.sc_cuenta,SC.denominacion,SC.status,SC.nivel as rnivel,SC.referencia as referencia,  ".
              "        coalesce(curSaldo.T_Debe,0) as total_debe, ".
              "        coalesce(curSaldo.T_Haber,0) as total_haber,0 as nivel,SC.cueproacu ".
              " FROM scg_cuentas SC LEFT OUTER JOIN (SELECT codemp,sc_cuenta, coalesce(sum(debe_mes),0)as T_Debe, ".
              "                                             coalesce(sum(haber_mes),0) as T_Haber ".
              "                                      FROM   scg_saldos ".
              "                                      WHERE  codemp='".$ls_codemp."' AND fecsal<='".$ad_fecfin."' ".
              "                                      GROUP BY codemp,sc_cuenta) curSaldo ".
              " ON SC.sc_cuenta=curSaldo.sc_cuenta ".
              " WHERE SC.codemp=curSaldo.codemp AND  curSaldo.codemp='".$ls_codemp."' AND ".
              "       (SC.sc_cuenta like '".$this->ls_activo."%' OR SC.sc_cuenta like '".$this->ls_pasivo."%' OR ".
              "        SC.sc_cuenta like '".$this->ls_resultado."%' OR  SC.sc_cuenta like '".$this->ls_capital."%' OR ".
              "        SC.sc_cuenta like '".$this->ls_orden_d."%' OR SC.sc_cuenta like '".$this->ls_orden_h."%') ".
              " ORDER BY trim(SC.sc_cuenta) "; */
			  
		  $ls_sql=" SELECT SC.sc_cuenta,SC.denominacion,SC.status,SC.nivel as rnivel,SC.referencia as referencia,  ".
				  "        coalesce(curSaldo.T_Debe,0) as total_debe, ".
				  "        coalesce(curSaldo.T_Haber,0) as total_haber,0 as nivel,SC.cueproacu, 1 as tiporden ".
				  " FROM scg_cuentas SC LEFT OUTER JOIN (SELECT codemp,sc_cuenta, coalesce(sum(debe_mes),0)as T_Debe, ".
				  "                                             coalesce(sum(haber_mes),0) as T_Haber ".
				  "                                      FROM   scg_saldos ".
				  "                                      WHERE  codemp='".$ls_codemp."' AND fecsal<='".$ad_fecfin."' ".
				  "                                      GROUP BY codemp,sc_cuenta) curSaldo ".
				  " ON SC.sc_cuenta=curSaldo.sc_cuenta ".
				  " WHERE SC.codemp=curSaldo.codemp AND  curSaldo.codemp='".$ls_codemp."' AND ".
				  "       (SC.sc_cuenta like '".$this->ls_activo."%') ".
				  " UNION ".
				  " SELECT DISTINCT '".$ls_cuenta_tot_deudora."' as sc_cuenta, 'CUENTAS DE ORDEN' as denominacion, 'S' as status, 1 as rnivel,'' as referencia,  ".
				  "        coalesce(SUM(curSaldo.T_Debe),0) as total_debe, ".
				  "        coalesce(SUM(curSaldo.T_Haber),0) as total_haber,0 as nivel, '' as cueproacu, 1 as tiporden ".
				  " FROM scg_cuentas SC LEFT OUTER JOIN (SELECT codemp,sc_cuenta, coalesce(sum(debe_mes),0)as T_Debe, ".
				  "                                             coalesce(sum(haber_mes),0) as T_Haber ".
				  "                                      FROM   scg_saldos ".
				  "                                      WHERE  codemp='".$ls_codemp."' AND fecsal<='".$ad_fecfin."' ".
				  "                                      GROUP BY codemp,sc_cuenta) curSaldo ".
				  " ON SC.sc_cuenta=curSaldo.sc_cuenta ".
				  " WHERE SC.codemp=curSaldo.codemp AND  curSaldo.codemp='".$ls_codemp."' AND ".
				  "      SC.sc_cuenta like '".$this->ls_orden_d."%' AND SC.status = 'C'".
				  " UNION ".
				  " SELECT SC.sc_cuenta,SC.denominacion,SC.status,SC.nivel as rnivel,SC.referencia as referencia,  ".
				  "        coalesce(curSaldo.T_Debe,0) as total_debe, ".
				  "        coalesce(curSaldo.T_Haber,0) as total_haber,0 as nivel,SC.cueproacu, 1 as tiporden ".
				  " FROM scg_cuentas SC LEFT OUTER JOIN (SELECT codemp,sc_cuenta, coalesce(sum(debe_mes),0)as T_Debe, ".
				  "                                             coalesce(sum(haber_mes),0) as T_Haber ".
				  "                                      FROM   scg_saldos ".
				  "                                      WHERE  codemp='".$ls_codemp."' AND fecsal<='".$ad_fecfin."' ".
				  "                                      GROUP BY codemp,sc_cuenta) curSaldo ".
				  " ON SC.sc_cuenta=curSaldo.sc_cuenta ".
				  " WHERE SC.codemp=curSaldo.codemp AND  curSaldo.codemp='".$ls_codemp."' AND ".
				  "       (SC.sc_cuenta like '".$this->ls_orden_d."%') ".
				  " UNION ".
				  " SELECT SC.sc_cuenta,SC.denominacion,SC.status,SC.nivel as rnivel,SC.referencia as referencia,  ".
				  "        coalesce(curSaldo.T_Debe,0) as total_debe, ".
				  "        coalesce(curSaldo.T_Haber,0) as total_haber,0 as nivel,SC.cueproacu,2 as tiporden ".
				  " FROM scg_cuentas SC LEFT OUTER JOIN (SELECT codemp,sc_cuenta, coalesce(sum(debe_mes),0)as T_Debe, ".
				  "                                             coalesce(sum(haber_mes),0) as T_Haber ".
				  "                                      FROM   scg_saldos ".
				  "                                      WHERE  codemp='".$ls_codemp."' AND fecsal<='".$ad_fecfin."' ".
				  "                                      GROUP BY codemp,sc_cuenta) curSaldo ".
				  " ON SC.sc_cuenta=curSaldo.sc_cuenta ".
				  " WHERE SC.codemp=curSaldo.codemp AND  curSaldo.codemp='".$ls_codemp."' AND ".
				  "       (SC.sc_cuenta like '".$this->ls_pasivo."%' OR ".
				  "        SC.sc_cuenta like '".$this->ls_resultado."%' OR  SC.sc_cuenta like '".$this->ls_capital."%') ".
				  " ORDER BY 10,1,4 ";
        }
	 	
     $rs_data=$this->io_sql->select($ls_sql);
     if($rs_data===false)
     {// error interno sql
        $this->is_msg_error="Error en consulta metodo uf_scg_reporte_balance_general_formato2 ".$this->io_fun->uf_convertirmsg($this->io_sql->message);
        
        $lb_valido = false;
     }
     else
     {
        $ld_saldo_ganancia=0;
        $ls_nivel_act = "1";
        $ls_status_act = "S";
        
        while($row=$this->io_sql->fetch_row($rs_data))
        {
            $ls_sc_cuenta=$row["sc_cuenta"];
            $ls_denominacion=$row["denominacion"];
            $ls_status=$row["status"];
            $ls_rnivel=$row["rnivel"];
            $ld_total_debe=$row["total_debe"];
            $ld_total_haber=$row["total_haber"];
            $ls_cueproacu = $row["cueproacu"];
            $ls_referencia = $row["referencia"];
            
            $this->uf_cuenta_por_nivel($ls_sc_cuenta,&$ls_cuentasalida,&$lr_nivel); 
            
            if (trim($ls_sc_cuenta)=='2250204010004')
            {
                $x=0;
            }
            
            $lb_excluir  =  $this->uf_verificar_cuentaproacu($ls_sc_cuenta,&$aa_cuentas_pa);
            
          if($ls_status=="C")
          {
            $ls_nivel=$ls_rnivel;        //"4"
          }//if
          else
          {
            $ls_nivel=$ls_rnivel;        
          }//else
          if($ls_nivel<=$ai_nivel)
          {
              if (!$lb_excluir)
              {
                  $this->ds_Prebalance->insertRow("sc_cuenta",$ls_sc_cuenta);          
                  $this->ds_Prebalance->insertRow("denominacion",$ls_denominacion);
                  $this->ds_Prebalance->insertRow("status",$ls_status);
                  $this->ds_Prebalance->insertRow("nivel",$ls_nivel);
                  $this->ds_Prebalance->insertRow("rnivel",$ls_rnivel);
                  $this->ds_Prebalance->insertRow("total_debe",$ld_total_debe);
                  $this->ds_Prebalance->insertRow("total_haber",$ld_total_haber);
                  $this->ds_Prebalance->insertRow("cueproacu",0);
                  $this->ds_Prebalance->insertRow("referencia",$ls_referencia);
                  $this->ds_Prebalance->insertRow("cuenta_salida",$ls_cuentasalida);
                  
                  $lb_valido = true;                  
              }
          }//if
          
              if (!empty($ls_cueproacu))
              {
                  $lb_valido2 = $this->uf_scg_cueproacu_saldo($ls_cueproacu,$ad_fecfin,&$as_sc_cuenta,&$as_denominacion,&$as_status ,&$as_rnivel,&$ad_total_debe,&$ad_total_haber );
                  if($lb_valido2)
                  {
                      if($as_status=="C")
                      {
                        $ls_nivel=$ls_rnivel;        //"4"
                      }//if
                      else
                      {
                        $ls_nivel=$ls_rnivel;        
                      }//else
                      
                      if($ls_nivel<=$ai_nivel)
                      {                          
                          $this->ds_Prebalance->insertRow("sc_cuenta",$as_sc_cuenta);            
                          $this->ds_Prebalance->insertRow("denominacion",$as_denominacion);
                          $this->ds_Prebalance->insertRow("status",$as_status);
                          $this->ds_Prebalance->insertRow("nivel",$ls_nivel);
                          $this->ds_Prebalance->insertRow("rnivel",$as_rnivel);
                          $this->ds_Prebalance->insertRow("total_debe",$ad_total_debe);
                          $this->ds_Prebalance->insertRow("total_haber",$ad_total_haber);
                          $this->ds_Prebalance->insertRow("cueproacu",1);
                          $this->ds_Prebalance->insertRow("referencia",$ls_referencia);
                          $this->ds_Prebalance->insertRow("cuenta_salida",$ls_cuentasalida);
                          $lb_valido2 = true;         
                      }//if  
                  }   
              }
              if (($ls_status_act=='C'))
              {
                  if (!$lb_excluir)
                  {
                      $this->ds_Prebalance->insertRow("sc_cuenta",'T'.$as_sc_cuenta);            
                      $this->ds_Prebalance->insertRow("denominacion",'Total ->'.$as_denominacion);
                      $this->ds_Prebalance->insertRow("status",$as_status);
                      $this->ds_Prebalance->insertRow("nivel",$ls_nivel);
                      $this->ds_Prebalance->insertRow("rnivel",$as_rnivel);
                      $this->ds_Prebalance->insertRow("total_debe",$ad_total_debe);
                      $this->ds_Prebalance->insertRow("total_haber",$ad_total_haber);
                      $this->ds_Prebalance->insertRow("cueproacu",9);
                      $this->ds_Prebalance->insertRow("referencia",$ls_referencia);
                      $this->ds_Prebalance->insertRow("cuenta_salida",$ls_cuentasalida);
                      $ls_nivel_act= $row["rnivel"];
                  }

              }         
        }//while
        $li=$this->ds_Prebalance->getRowCount("sc_cuenta");
        if($li==0)
        {
              $lb_valido = false;
              return false;
        }//if
     } //else
     $ld_saldo_i=0;            
     if($lb_valido)
     {
       $lb_valido=$this->uf_scg_reporte_select_saldo_ingreso_BG($ad_fecfin,$this->ls_ingreso,$ld_saldo_i);
     } 
     if($lb_valido)
     {
       $ld_saldo_g=0;     
       $lb_valido=$this->uf_scg_reporte_select_saldo_gasto_BG($ad_fecfin,$this->ls_gastos,$ld_saldo_g);  
     }//if
     if($lb_valido)
     {
       $ld_saldo_ganancia=($ld_saldo_ganancia+($ld_saldo_i-$ld_saldo_g))*-1;
     }//if
     
     $la_sc_cuenta=array();
     $la_denominacion=array();
     $la_saldo=array();
     for($i=1;$i<=$ai_nivel;$i++)
     {
         $la_sc_cuenta[$i]="";
         $la_denominacion[$i]="";
         $la_saldo[$i]=0;
     }//for
     $li_nro_reg=0;
     $ld_saldo_resultado=0;
     $li_row=$this->ds_Prebalance->getRowCount("sc_cuenta"); 
    
     for($li_z=1;$li_z<=$li_row;$li_z++)
     {
        $ls_sc_cuenta=$this->ds_Prebalance->getValue("sc_cuenta",$li_z);
        $ls_cueproacu=$this->ds_Prebalance->getValue("cueproacu",$li_z); 
        $ldec_debe=$this->ds_Prebalance->getValue("total_debe",$li_z);
        $ldec_haber=$this->ds_Prebalance->getValue("total_haber",$li_z);
        $li_nivel=$this->ds_Prebalance->getValue("nivel",$li_z);
        $li_rnivel=$this->ds_Prebalance->getValue("rnivel",$li_z);     
        $li_status=$this->ds_Prebalance->getValue("status",$li_z);     
        $ls_denominacion=$this->ds_Prebalance->getValue("denominacion",$li_z); 
        $ls_referencia=$this->ds_Prebalance->getValue("referencia",$li_z); 
        $ls_cuenta_salida=$this->ds_Prebalance->getValue("cuenta_salida",$li_z); 
                       
        $ls_tipo_cuenta=substr($ls_sc_cuenta,0,1);
        if  ($ls_cueproacu==1)
        {
             $ls_tipo_cuenta=$this->ls_activo;
        }
         switch($ls_tipo_cuenta){
            case $this->ls_activo:
                $ls_orden=1;
                break;
            case $this->ls_pasivo:
                $ls_orden=2;
                break;
            case $this->ls_capital:
                $ls_orden=3;
                break;                
            case $this->ls_resultado:
                $ls_orden=4;
                break;
            case $this->ls_orden_d:
                $ls_orden=5;
                break;        
            case $this->ls_orden_h:
                $ls_orden=6;
                break;
            default:
                $ls_orden=7;        
        }
        
        $ldec_saldo=$ldec_debe-$ldec_haber;
        if  ($ls_cueproacu==1)
        {
             $ldec_saldo=-$ldec_saldo;
        }    
        if((($ls_tipo_cuenta==$this->ls_pasivo)||($ls_tipo_cuenta==$this->ls_resultado)||($ls_tipo_cuenta==$this->ls_capital))&&($li_nivel==1))
        {
            if($ldec_saldo<0)
            {
                $ldec_saldoAux = abs($ldec_saldo);
            }
            else
            {
                $ldec_saldoAux = $ldec_saldo;
            }
            $ld_saldo_resultado=$ld_saldo_resultado+$ldec_saldoAux;
        }    
        
        if($li_nivel==4)    
        {            
            $li_nro_reg=$li_nro_reg+1;
            $this->ds_Balance1->insertRow("orden",$ls_orden);
            $this->ds_Balance1->insertRow("num_reg",$li_nro_reg);
            $this->ds_Balance1->insertRow("sc_cuenta",$ls_sc_cuenta);
            $this->ds_Balance1->insertRow("denominacion",$ls_denominacion);
            $this->ds_Balance1->insertRow("nivel",$li_nivel);
            $this->ds_Balance1->insertRow("rnivel",$li_rnivel);
            $this->ds_Balance1->insertRow("status",$ls_status);
            $this->ds_Balance1->insertRow("saldo",$ldec_saldo);
            $this->ds_Balance1->insertRow("referencia",$ls_referencia);
            $this->ds_Balance1->insertRow("cuenta_salida",$ls_cuenta_salida);            
        }
        else
        {
            if(empty($la_sc_cuenta[$li_nivel]))
            {
               $la_sc_cuenta[$ls_nivel]=$ls_sc_cuenta;
               $la_denominacion[$ls_nivel]=$ls_denominacion;
               $la_saldo[$ls_nivel]=$ldec_saldo;
               $li_nro_reg=$li_nro_reg+1;
               $this->ds_Balance1->insertRow("orden",$ls_orden);
               $this->ds_Balance1->insertRow("num_reg",$li_nro_reg);
               $this->ds_Balance1->insertRow("sc_cuenta",$ls_sc_cuenta);       
               $this->ds_Balance1->insertRow("denominacion",$ls_denominacion);
               $this->ds_Balance1->insertRow("status",$ls_status);
               $this->ds_Balance1->insertRow("nivel",-$li_nivel);
               $this->ds_Balance1->insertRow("rnivel",$li_rnivel);
               $this->ds_Balance1->insertRow("saldo",$ldec_saldo);
               $this->ds_Balance1->insertRow("referencia",$ls_referencia);
               $this->ds_Balance1->insertRow("cuenta_salida",$ls_cuenta_salida);
            }
            else
            {
               $this->uf_scg_reporte_calcular_total_BG($li_nro_reg,$ls_prev_nivel,$ls_nivel,$la_sc_cuenta,$la_denominacion,$la_saldo); 
               $la_sc_cuenta[$ls_nivel]=$ls_sc_cuenta;
               $la_denominacion[$ls_nivel]=$ls_denominacion;
               $la_saldo[$ls_nivel]=$ldec_saldo;
               $li_nro_reg=$li_nro_reg+1;
               $this->ds_Balance1->insertRow("orden",$ls_orden);
               $this->ds_Balance1->insertRow("num_reg",$li_nro_reg);
               $this->ds_Balance1->insertRow("sc_cuenta",$ls_sc_cuenta);       
               $this->ds_Balance1->insertRow("denominacion",$ls_denominacion);
               $this->ds_Balance1->insertRow("nivel",-$li_nivel);
               $this->ds_Balance1->insertRow("status",$ls_status);
               $this->ds_Balance1->insertRow("rnivel",$li_rnivel);
               $this->ds_Balance1->insertRow("saldo",$ldec_saldo);
               $this->ds_Balance1->insertRow("referencia",$ls_referencia);
               $this->ds_Balance1->insertRow("cuenta_salida",$ls_cuenta_salida);
            }
        }
        $ls_prev_nivel=$li_nivel;            
     }

     $this->uf_scg_reporte_calcular_total_BG(&$li_nro_reg,$ls_prev_nivel,1,$la_sc_cuenta,$la_denominacion,$la_saldo);             

     $ld_saldo_resultado=($ld_saldo_resultado+$ld_saldo_ganancia);

     $this->uf_scg_reporte_actualizar_resultado_BG($this->ls_cta_resultado,$ld_saldo_ganancia,$li_nro_reg,$ls_orden,$ai_nivel); 
     $li_total=$this->ds_Balance1->getRowCount("sc_cuenta");
                                                                          
     for ($li_i=1;$li_i<=$li_total;$li_i++)
         {    
           $ls_sc_cuenta    = $this->ds_Balance1->data["sc_cuenta"][$li_i];
           $ls_orden        = $this->ds_Balance1->data["orden"][$li_i];
           $li_nro_reg      = $this->ds_Balance1->data["num_reg"][$li_i];
           $ls_denominacion = $this->ds_Balance1->data["denominacion"][$li_i];
           $ls_nivel        = $this->ds_Balance1->data["nivel"][$li_i];
           $ls_status        = $this->ds_Balance1->data["status"][$li_i];
           $ls_rnivel        = $this->ds_Balance1->data["rnivel"][$li_i];           
           $ld_saldo        = $this->ds_Balance1->data["saldo"][$li_i];
           $ls_referencia   = $this->ds_Balance1->data["referencia"][$li_i];
           $ls_cuenta_salida = $this->ds_Balance1->data["cuenta_salida"][$li_i];
           if (!empty($ls_sc_cuenta))
           {
               $li_pos          = $this->ds_Prebalance->find("sc_cuenta",$ls_sc_cuenta);
               if ($li_pos>0)
                  { 
                    $ls_rnivel=$this->ds_Prebalance->data["rnivel"][$li_pos];
                  }
               else
                  {
                    $ls_rnivel=0;
                  }
               if ($ls_nivel<=$ai_nivel)   
               {
                   $ds_Balance2->insertRow("orden",$ls_orden);
                   $ds_Balance2->insertRow("num_reg",$li_nro_reg);
                   $ds_Balance2->insertRow("sc_cuenta",$ls_sc_cuenta);           
                   $ds_Balance2->insertRow("denominacion",$ls_denominacion);
                   $ds_Balance2->insertRow("nivel",$ls_nivel);
                   $ds_Balance2->insertRow("rnivel",$ls_rnivel);
                   $ds_Balance2->insertRow("status",$ls_status);
                   $ds_Balance2->insertRow("saldo",$ld_saldo);
                   $ds_Balance2->insertRow("rnivel",$ls_rnivel);
                   $ds_Balance2->insertRow("total",$ld_saldo_resultado);
                   $ds_Balance2->insertRow("referencia",$ls_referencia); 
                   $ds_Balance2->insertRow("cuenta_salida",cuenta_salida);                
               }
           }
         }
     $li_tot = $ds_Balance2->getRowCount("sc_cuenta");
     
    // var_dump($this->ls_resultado);
     //die();
     global $arr_cuenta,$arr_denomina;
     $arr_cuenta    = array($ai_nivel);
     $arr_denomina  = array($ai_nivel);
     $arr_saldos    = array($ai_nivel);
     
     for ($li_i=1;$li_i<=$li_tot;$li_i++)
     { 
           $ls_sc_cuenta       = $ds_Balance2->data["sc_cuenta"][$li_i];
           $ls_orden           = $ds_Balance2->data["orden"][$li_i];
           $li_nro_reg         = $ds_Balance2->data["num_reg"][$li_i];
           $ls_denominacion    = $ds_Balance2->data["denominacion"][$li_i];
           $ls_nivel           = $ds_Balance2->data["nivel"][$li_i];
           $ls_status           = $ds_Balance2->data["status"][$li_i];
           $ld_saldo           = $ds_Balance2->data["saldo"][$li_i];
           $ls_rnivel          = $ds_Balance2->data["rnivel"][$li_i];
           $ld_saldo_resultado = $ds_Balance2->data["total"][$li_i];
           $ls_referencia      = $ds_Balance2->data["referencia"][$li_i]; 
           $ls_cuenta_salida   = $ds_Balance2->data["cuenta_salida"][$li_i]; 
           
           if ($ls_rnivel<=$ai_nivel)
              {                  
                $this->ds_reporte->insertRow("orden",$ls_orden);
                $this->ds_reporte->insertRow("num_reg",$li_nro_reg);
                $this->ds_reporte->insertRow("sc_cuenta",$ls_sc_cuenta);
                $this->ds_reporte->insertRow("denominacion",$ls_denominacion);
                $this->ds_reporte->insertRow("status",$ls_status);
                $this->ds_reporte->insertRow("nivel",$ls_nivel);
                $this->ds_reporte->insertRow("saldo",$ld_saldo);
                $this->ds_reporte->insertRow("rnivel",$ls_rnivel);
                $this->ds_reporte->insertRow("total",$ld_saldo_resultado);
                $this->ds_reporte->insertRow("referencia",$ls_referencia);
                $this->ds_reporte->insertRow("cuenta_salida",$ls_cuenta_salida);
                $this->ds_reporte->insertRow("cerrado",'');
              }      
      }

    //chequeo los niveles y ajusto los valores de nivel, status y  referencia
    $li_row=$this->ds_reporte->getRowCount("sc_cuenta"); 
    for($li_z=1;$li_z<=$li_row;$li_z++)
    {
        $ls_sc_cuenta   = $this->ds_reporte->getValue("sc_cuenta",$li_z); 
        $li_pos         = $this->ds_Prebalance->find("sc_cuenta",$ls_sc_cuenta);
        if ($li_pos>0)
        { 
            $ls_rnivel=$this->ds_Prebalance->data["rnivel"][$li_pos];
            $ls_status=$this->ds_Prebalance->data["status"][$li_pos];
            $ls_referencia=$this->ds_Prebalance->data["referencia"][$li_pos];
            $this->ds_reporte->data["rnivel"][$li_z]=$ls_rnivel;
            $this->ds_reporte->data["status"][$li_z]=$ls_status;
            if ($ls_status=='S')
            {
                $this->ds_reg_niveles->insertRow("sc_cuenta",$ls_sc_cuenta);
                $this->ds_reg_niveles->insertRow("cerrado",'N');
                $this->ds_reg_niveles->insertRow("referencia",$ls_referencia);
            } 
        }
        else
        {
            $ls_rnivel=0;
        }       
    }

    // armo el datastore final
    
    $an_nivel = intval($ai_nivel);
    
    $nPrevNivel = intval($this->ds_reporte->data["rnivel"][$li_row]); 
    
    $nRegNo = 0;
    
    $li_row=$this->ds_reporte->getRowCount("sc_cuenta"); 
    for($li_z=1;$li_z<=$li_row;$li_z++)
    {
           $ls_sc_cuenta       = $this->ds_reporte->data["sc_cuenta"][$li_z];
           $ls_orden           = $this->ds_reporte->data["orden"][$li_z];
           $li_nro_reg         = $this->ds_reporte->data["num_reg"][$li_z];
           $ls_denominacion    = $this->ds_reporte->data["denominacion"][$li_z];
           $ls_nivel           = $this->ds_reporte->data["nivel"][$li_z];
           $ls_status          = $this->ds_reporte->data["status"][$li_z];
           $ld_saldo           = $this->ds_reporte->data["saldo"][$li_z];
           $ls_rnivel          = $this->ds_reporte->data["rnivel"][$li_z];
           $ld_saldo_resultado = $this->ds_reporte->data["total"][$li_z];
           $ls_referencia      = $this->ds_reporte->data["referencia"][$li_z]; 
           $ls_cuenta_salida   = $this->ds_reporte->data["cuenta_salida"][$li_z]; 
           $ls_cerrado         = $this->ds_reporte->data["cerrado"][$li_z]; 
           $ln_nivel           = intval($ls_rnivel);
           if (empty($ld_saldo))
           {
               $ld_saldo=0;
           }
           if ($ln_nivel==$an_nivel)
           {
                $nRegNo++;
                $this->ds_reportef->insertRow("sc_cuenta",$ls_sc_cuenta); 
                $this->ds_reportef->insertRow("orden",$ls_orden);
                $this->ds_reportef->insertRow("num_reg",$li_nro_reg);                    
                $this->ds_reportef->insertRow("denominacion",$ls_denominacion);
                $this->ds_reportef->insertRow("nivel",$ls_nivel);
                $this->ds_reportef->insertRow("status",$ls_status);
                $this->ds_reportef->insertRow("saldo",$ld_saldo);
                $this->ds_reportef->insertRow("rnivel",$ls_rnivel);
                $this->ds_reportef->insertRow("total",$ld_saldo_resultado);
                $this->ds_reportef->insertRow("referencia",$ls_referencia);
                $this->ds_reportef->insertRow("cuenta_salida",$ls_cuenta_salida);
                $this->ds_reportef->insertRow("cerrado",'');
           }
           else
           {
                if (empty($arr_cuenta[intval($ls_rnivel)]))
                   {
                        $arr_cuenta[$ln_nivel]      = $ls_sc_cuenta;
                        $arr_denomina[$ln_nivel]    = $ls_denominacion;
                        $arr_saldos[$ln_nivel]      = $ld_saldo;
                        $nRegNo++;
                        $this->ds_reportef->insertRow("sc_cuenta",$ls_sc_cuenta); 
                        $this->ds_reportef->insertRow("orden",$ls_orden);
                        $this->ds_reportef->insertRow("num_reg",$li_nro_reg);                    
                        $this->ds_reportef->insertRow("denominacion",$ls_denominacion);
                        $this->ds_reportef->insertRow("nivel",$ls_nivel);
                        $this->ds_reportef->insertRow("status",$ls_status);
                        $this->ds_reportef->insertRow("saldo",$ld_saldo);
                        $this->ds_reportef->insertRow("rnivel",$ls_rnivel);
                        $this->ds_reportef->insertRow("total",$ld_saldo_resultado);
                        $this->ds_reportef->insertRow("referencia",$ls_referencia);
                        $this->ds_reportef->insertRow("cuenta_salida",$ls_cuenta_salida);
                        $this->ds_reportef->insertRow("cerrado",'');
                   }
                   else
                   {
                        $a=1;
                        $this->uf_downstair($nRegNo,$nPrevNivel,intval($ls_rnivel),&$arr_cuenta,&$arr_denomina,&$arr_saldos);
                        $arr_cuenta[$ln_nivel]      = $ls_sc_cuenta;
                        $arr_denomina[$ln_nivel]    = $ls_denominacion;
                        $arr_saldos[$ln_nivel]      = $ld_saldo;
                        $nRegNo++;
                        $this->ds_reportef->insertRow("sc_cuenta",$ls_sc_cuenta); 
                        $this->ds_reportef->insertRow("orden",$ls_orden);
                        $this->ds_reportef->insertRow("num_reg",$li_nro_reg);                    
                        $this->ds_reportef->insertRow("denominacion",$ls_denominacion);
                        $this->ds_reportef->insertRow("nivel",$ls_nivel);
                        $this->ds_reportef->insertRow("status",$ls_status);
                        $this->ds_reportef->insertRow("saldo",$ld_saldo);
                        $this->ds_reportef->insertRow("rnivel",$ls_rnivel);
                        $this->ds_reportef->insertRow("total",$ld_saldo_resultado);
                        $this->ds_reportef->insertRow("referencia",$ls_referencia);
                        $this->ds_reportef->insertRow("cuenta_salida",$ls_cuenta_salida);
                        
                        $this->ds_reportef->insertRow("cerrado",'');
                   }  
           }

           $nPrevNivel = intval($ls_rnivel); 
    }//for
    
    $this->uf_downstair($nRegNo,$nPrevNivel,1,&$arr_cuenta,&$arr_denomina,&$arr_saldos);    
     //$ds_reg_niveles
     $li_registro = 0;
     unset($this->ds_Prebalance,$this->ds_Balance1,$ds_Balance2);
     return $lb_valido;  
    }



/****************************************************************************************************************************************/
function uf_downstair($li_npos,$nHighStair,$nLowerStair,$arr_cuenta,$arr_denomina,&$arr_saldos)
{
    for($li_z=($nHighStair-1);$li_z>=$nLowerStair;$li_z--) 
    {
        if (!empty($arr_cuenta[$li_z]))
        {
            $nRegNo++; 
            //inserta en el datastore final
            $this->ds_reportef->insertRow("sc_cuenta",$arr_cuenta[$li_z]); 
            $this->ds_reportef->insertRow("orden",$ls_orden);
            $this->ds_reportef->insertRow("num_reg",$li_nro_reg);                    
            $this->ds_reportef->insertRow("denominacion",'TOTAL '.$arr_denomina[$li_z]);
            $this->ds_reportef->insertRow("nivel",$ls_nivel);
            
            $this->ds_reportef->insertRow("saldo",$arr_saldos[$li_z]);
            
                    $ls_sc_cuentat   = $arr_cuenta[$li_z]; 
                    $li_pos         = $this->ds_Prebalance->find("sc_cuenta",$ls_sc_cuentat);
                    if ($li_pos>0)
                    { 
                        $ls_rnivel=$this->ds_Prebalance->data["rnivel"][$li_pos];
                        $ls_status=$this->ds_Prebalance->data["status"][$li_pos];
                    }
                    else
                    {
                        $ls_rnivel=0;
                    }            
            $this->ds_reportef->insertRow("status",$ls_status);
            $this->ds_reportef->insertRow("rnivel",$ls_rnivel);
            $this->ds_reportef->insertRow("total",$ld_saldo_resultado);
            $this->ds_reportef->insertRow("referencia",$ls_referencia);
            $this->ds_reportef->insertRow("cuenta_salida",$ls_cuenta_salida);
            $this->ds_reportef->insertRow("cerrado",'S');

            $arr_cuenta[$li_z]      = null;
            $arr_denomina[$li_z]    = null;
            $arr_saldos[$li_z]      = null;
        }//if
    }//for
    
}
      
/****************************************************************************************************************************************/
    //Function uf_cuenta_por_nivel
    //retorna un substring de la cuanta contable segun el nivel que tenga
    //no retorna texto formateado segun la mascara, solo substring
    function uf_cuenta_por_nivel($as_cuenta,&$ls_cuentasalida,&$ls_nivel)
    {
        $ls_nivel=$this->int_scg->uf_scg_obtener_nivel($as_cuenta);    
        $ls_mascara= trim($_SESSION["la_empresa"]["formcont"]).'-';
        $li_niveles = substr_count($ls_mascara,'-',1);
        $pos = 0;
		$cant_espacios = 0;        
        for ( $li_pos=1;$li_pos<=$li_niveles;$li_pos++)
        {
            $cadena='';
            $lbValido=true;
            while($lbValido)
            {
                $cad = substr($ls_mascara,$pos,1);
                if ($cad=='-')
                {
                    $pos++;
                    $lbValido=false;
                }    
                else
                {
                    $cadena = $cadena.$cad;
                    $pos++;
                }            
            }
            $cant_espacios += strlen($cadena);
            $espacios[$li_pos]=$cant_espacios;            
        }
        $ls_cuentasalida = substr($as_cuenta,0,$espacios[$ls_nivel]);               
    }



/****************************************************************************************************************************************/        
function denominacion_cuenta_totalizadora($as_cuenta,&$as_denominacion,&$ls_referencia_ct,&$li_nivel_ct)
{
    $li_pos  = $this->ds_Prebalance->find("sc_cuenta",$as_cuenta);
    if ($li_pos>0)
    { 
        $as_denominacion    = $this->ds_Prebalance->data["denominacion"][$li_pos]; 
        $ls_referencia_ct   = $this->ds_Prebalance->data["referencia"][$li_pos]; 
        $li_nivel_ct        = $this->ds_Prebalance->data["rnivel"][$li_pos]; 
    }
    else
    {
        $as_denominacion    = '';
        $ls_referencia_ct   = '';
        $li_nivel_ct        = '';
    }        
}

    
/****************************************************************************************************************************************/    
function uf_scg_cueproacu_saldo($cueproacu,$ad_fecfin,&$as_sc_cuenta,&$as_denominacion,&$as_status ,&$as_rnivel,&$ad_total_debe,&$ad_total_haber)
{
     //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
     //       Function :    uf_scg_cueproacu_saldo
     //         Access :    private
     //     Argumentos :    cueproacu  // cuenta de provisiones acumuladas o de depreciacion                    
     //        Returns :    Retorna datastore con informacion de la cuenta
     //    Description :    Busca en scg_cuentas la informacion de una cuenta, usando la misma consulta del balance general  
     //     Creado por :    
     // Fecha Creacion :    20/01/2010                          Fecha ltima Modificacion :      Hora :
     ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
     $cueproacu = trim($cueproacu);
     $ls_codemp=$this->la_empresa["codemp"];
     $ad_fecfin=$this->io_fun->uf_convertirdatetobd($ad_fecfin);
    
    if($_SESSION["ls_gestor"]=='INFORMIX')
    {            
        $ls_sql="SELECT SC.sc_cuenta,SC.denominacion,SC.status,SC.nivel as rnivel, 
            (select case sum(debe_mes) when null then 0 else sum(debe_mes) end FROM scg_saldos WHERE codemp='".$ls_codemp."' AND fecsal<='".$ad_fecfin."' AND sc_cuenta=SC.sc_cuenta GROUP BY codemp,sc_cuenta) as total_debe,
            (select case sum(haber_mes) when null then 0 else sum(haber_mes) end FROM scg_saldos WHERE codemp='".$ls_codemp."' AND fecsal<='".$ad_fecfin."' AND sc_cuenta=SC.sc_cuenta GROUP BY codemp,sc_cuenta) as total_haber,
            0 as nivel,SC.cueproacu  
            FROM scg_cuentas SC 
            where  (SC.sc_cuenta like '".$cueproacu."' ) 
            ORDER BY SC.sc_cuenta ";
    }
    else
    {
        $ls_sql=" SELECT SC.sc_cuenta,SC.denominacion,SC.status,SC.nivel as rnivel, ".
            "        coalesce(curSaldo.T_Debe,0) as total_debe, ".
            "        coalesce(curSaldo.T_Haber,0) as total_haber,0 as nivel,SC.cueproacu ".
            " FROM scg_cuentas SC LEFT OUTER JOIN (SELECT codemp,sc_cuenta, coalesce(sum(debe_mes),0)as T_Debe, ".
            "                                             coalesce(sum(haber_mes),0) as T_Haber ".
            "                                      FROM   scg_saldos ".
            "                                      WHERE  codemp='".$ls_codemp."' AND fecsal<='".$ad_fecfin."' ".
            "                                      GROUP BY codemp,sc_cuenta) as curSaldo ".
            " ON curSaldo.codemp = SC.codemp AND SC.sc_cuenta=curSaldo.sc_cuenta ".
            " WHERE SC.codemp='".$ls_codemp."' AND ".
            "       SC.sc_cuenta like '".$cueproacu."%'  ". 
            " ORDER BY trim(SC.sc_cuenta) "; 
    }
     $lb_valido = true;
     $rs_data_cta=$this->io_sql->select($ls_sql);
     if($rs_data_cta===false)
     {// error interno sql
        $this->is_msg_error="Error en consulta metodo uf_scg_cueproacu_saldo ".$this->io_fun->uf_convertirmsg($this->io_sql->message);
        //print $this->io_sql->message;
        $lb_valido = false;
     }       
     else
     {
        $ld_saldo_ganancia=0;
        while($row=$this->io_sql->fetch_row($rs_data_cta))
        {
            $as_sc_cuenta=$row["sc_cuenta"];
            $as_denominacion=$row["denominacion"];
            $as_status=$row["status"];
            $as_rnivel=$row["rnivel"];      
            $ad_total_debe=$row["total_debe"];
            $ad_total_haber=$row["total_haber"];               
        } 
     }  
      
    return $lb_valido;    
         
}

/****************************************************************************************************************************************/    
function uf_scg_crea_array_cueproacu()
{
    $ls_codemp=$this->la_empresa["codemp"];    
    
    if($_SESSION["ls_gestor"]=='INFORMIX')
    {            
        $ls_sql= "select cueproacu as sc_cuenta
                  from   scg_cuentas
                  where  codemp='$ls_codemp'        AND 
                         (cueproacu is not null)    AND 
                         (length(cueproacu)<>0)";
    }
    else
    {
        $ls_sql= "select cueproacu as sc_cuenta
                  from   scg_cuentas
                  where  codemp='$ls_codemp'        AND
                         (cueproacu is not null)    AND
                         (length(cueproacu)<>0)";    
    }    
    $lb_valido = true;
    $rs_data_ctpa=$this->io_sql->select($ls_sql);
     if($rs_data_ctpa===false)
     {// error interno sql
            $this->is_msg_error="Error en consulta metodo uf_scg_cueproacu_saldo ".$this->io_fun->uf_convertirmsg($this->io_sql->message);
            //print $this->io_sql->message;
            $lb_valido = false;
     }       
     else
     {        
        $pos=0; 
        while($row=$this->io_sql->fetch_row($rs_data_ctpa))
        {
            $pos++;
            $as_cuenta_pa[$pos]=$row["sc_cuenta"];
        } 
     }  
     return $as_cuenta_pa;          
}

function uf_verificar_cuentaproacu($as_sc_cuenta,&$aa_cuentas_pa)
{
    //$as_cuenta_pa
    $lb_encontrado = false;
    $li_tot=count($aa_cuentas_pa);
    for($li_i=1;$li_i<=$li_tot;$li_i++)
    {
        if (trim($as_sc_cuenta)==trim($aa_cuentas_pa[$li_i]))
        {
            $lb_encontrado = true;
        }
    }
    return $lb_encontrado;
}

/****************************************************************************************************************************************/    

    
/****************************************************************************************************************************************/	
function  uf_scg_reporte_select_saldo_ingreso_BG($adt_fecini,$ai_ingreso,&$ad_saldo) 
{				 
	 //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_scg_reporte_select_saldo_ingreso_BG
	 //         Access :	private
	 //     Argumentos :    $adt_fecini  // fecha  desde 
     //              	    $ai_ingreso  // numero de la cuenta de ingraso 
	 //                     $ad_saldo  //  total saldo (referencia)
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	Reporte que genera salida  del Estado de Resultado  
	 //     Creado por :    Ing. Yozelin Barragan.
	 // Fecha Creacion :    02/05/2006          Fecha ltima Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $ls_codemp = $this->la_empresa["codemp"];
	 $lb_valido=true;
	 if($_SESSION["ls_gestor"]=='INFORMIX')
	    {
	     $ls_sql=" SELECT case sum(SD.debe_mes-SD.haber_mes) when null then 0 else sum(SD.debe_mes-SD.haber_mes) end saldo ".
                 " FROM   scg_cuentas SC, scg_saldos SD ".
                 " WHERE (SC.sc_cuenta = SD.sc_cuenta) AND (SC.codemp = SD.codemp) AND (SC.status='C') AND ".
			     "        fecsal<='".$adt_fecini."' AND (SC.sc_cuenta like '".$ai_ingreso."%') ";			
		}
		else
		{
		  $ls_sql=" SELECT COALESCE(sum(SD.haber_mes-SD.debe_mes),0) as saldo ".
                 " FROM   scg_cuentas SC, scg_saldos SD ".
                 " WHERE (SC.sc_cuenta = SD.sc_cuenta) AND (SC.codemp = SD.codemp) AND (SC.status='C') AND ".
			     "        fecsal<='".$adt_fecini."' AND (SC.sc_cuenta like '".$ai_ingreso."%') ";
		}
		
		
	 $rs_data=$this->io_sql->select($ls_sql);
	 if($rs_data===false)
	 {// error interno sql
		$this->is_msg_error="Error en consulta metodo uf_scg_reporte_select_saldo_ingreso_BG ".$this->io_fun->uf_convertirmsg($this->io_sql->message);
		$lb_valido = false;
	 }
	 else
	 {
		if($row=$this->io_sql->fetch_row($rs_data))
		{
		   $ad_saldo=$row["saldo"];
		}
		$this->io_sql->free_result($rs_data);
	 } 
	 return $lb_valido;   
   }//fin uf_scg_reporte_obtener_saldo_ingreso

function  uf_scg_reporte_select_saldo_gasto_BG($adt_fecini,$ai_gasto,&$ad_saldo) 
{				 
	 //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_scg_reporte_select_saldo_gasto_BG
	 //         Access :	private
	 //     Argumentos :    $adt_fecini  // fecha  desde 
     //              	    $ai_gasto  // numero de la cuenta de gasto
	 //                     $ad_saldo  //  total saldo (referencia)
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	Reporte que genera salida  del Estado de Resultado  
	 //     Creado por :    Ing. Yozelin Barragan.
	 // Fecha Creacion :    02/05/2006          Fecha ltima Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $ls_codemp = $this->la_empresa["codemp"];
	 $lb_valido=true;
	 if($_SESSION["ls_gestor"]=='INFORMIX')
	    {
	     $ls_sql=" SELECT case sum(SD.debe_mes-SD.haber_mes) when null then 0 else sum(SD.debe_mes-SD.haber_mes) end saldo ".
                 " FROM   scg_cuentas SC, scg_saldos SD ".
                 " WHERE (SC.sc_cuenta = SD.sc_cuenta) AND (SC.codemp = SD.codemp) AND (SC.status='C') AND ".
			     "        fecsal<='".$adt_fecini."' AND (SC.sc_cuenta like '".$ai_gasto."%') ";			
		}
	 else 
	   {
	    $ls_sql=" SELECT COALESCE(sum(SD.debe_mes-SD.haber_mes),0) as saldo ".
             " FROM   scg_cuentas SC, scg_saldos SD ".
             " WHERE (SC.sc_cuenta = SD.sc_cuenta) AND (SC.codemp = SD.codemp) AND (SC.status='C') AND ".
			 "        fecsal<='".$adt_fecini."' AND (SC.sc_cuenta like '".$ai_gasto."%') ";			 
	   }
	 //  var_dump($ls_sql);
	//	die();
	 $rs_data=$this->io_sql->select($ls_sql);
	 if($rs_data===false)
	 {// error interno sql
		$this->is_msg_error="Error en consulta metodo uf_scg_reporte_select_saldo_gasto_BG ".$this->io_fun->uf_convertirmsg($this->io_sql->message);
		$lb_valido = false;
	 }
	 else
	 {
		if($row=$this->io_sql->fetch_row($rs_data))
		{
		   $ad_saldo=$row["saldo"];
		}
		$this->io_sql->free_result($rs_data);
	 } 
	 return $lb_valido;   
   }//fin uf_scg_reporte_select_saldo_gasto_BG

function  uf_scg_reporte_calcular_total_BG(&$ai_nro_regi,$as_prev_nivel,$as_nivel,&$aa_sc_cuenta,$aa_denominacion,$aa_saldo) 
{				 
	 //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_scg_reporte_calcular_total_BG
	 //         Access :	private
	 //     Argumentos :    $as_prev_nivel  // nivel de la cuenta anterior
     //              	    $as_nivel  // nivel de  la cuenta 
	 //                     $ai_nro_regi  //  numero de registro (referencia)
	 //                     $aa_sc_cuenta  // arreglo de cuentas (referencia)
	 //                     $aa_denominacion // arreglo de denominacion         
	 //                     $aa_saldo // arreglo de saldo         
     //	       Returns :	Retorna true o false si se realizo el calculo del total para el reporte
	 //	   Description :	Metodo que genera un monto total para la cuenta del balance general 
	 //     Creado por :    Ing. Yozelin Barragan.
	 // Fecha Creacion :    08/05/2006          Fecha ltima Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $i=$as_prev_nivel-1;
	 $x=$as_nivel-1;
	 if($i>$x)
	 {
		  $ls_tipo_cuenta=substr($aa_sc_cuenta[$i],0,1);
		  if($ls_tipo_cuenta==$this->ls_activo) {	$ls_orden="1"; }	
		  if($ls_tipo_cuenta==$this->ls_pasivo) {	$ls_orden="2"; }	
		  if($ls_tipo_cuenta==$this->ls_capital) { $ls_orden="3"; }	
		  if($ls_tipo_cuenta==$this->ls_resultado) { $ls_orden="4"; }	
		  if($ls_tipo_cuenta==$this->ls_orden_d) { $ls_orden="5"; }
		  if($ls_tipo_cuenta==$this->ls_orden_h){ $ls_orden="6"; }
		  else{$ls_orden="7";}
          if(!empty($aa_sc_cuenta[$i]))
		  {
	 	    $ai_nro_regi=$ai_nro_regi+1;
		    $this->ds_Balance1->insertRow("orden",$ls_orden);
		    $this->ds_Balance1->insertRow("num_reg",$ai_nro_regi);
		    $this->ds_Balance1->insertRow("sc_cuenta",$aa_sc_cuenta[$i]);
		    $this->ds_Balance1->insertRow("denominacion","Total ".$aa_denominacion[$i]);
		    $this->ds_Balance1->insertRow("nivel",$i);
		    $this->ds_Balance1->insertRow("saldo",$aa_saldo[$i]);
			$aa_sc_cuenta[$i]="";
			$i--;
		  }//if
	 }//if
    }//uf_scg_reporte_calcular_total_BG

function  uf_scg_reporte_actualizar_resultado_BG($ai_c_resultad,$ad_saldo_ganancia,$ai_nro_reg,$as_orden,$ai_nivel) 
{				 
	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_scg_reporte_actualizar_resultado_BG
	 //         Access :	private
	 //     Argumentos :    $ai_c_resultad  // cuenta de resultado
     //              	    $ad_saldo_ganancia  // saldo 
     //              	    $as_sc_cuenta  // cuenta
     //	       Returns :	Retorna true o false si se realizo el calculo para el reporte
	 //	   Description :	Metodo que genera un monto actualizado de la cuenta del resultado
	 //     Creado por :    Ing. Yozelin Barragan
	 // Fecha Creacion:     08/05/2006          Fecha ltima Modificacion :      Hora :
  	 ///////////////////////////////////////////////////////////////////////////////////////////////////////////////
     $ls_next_cuenta=$ai_c_resultad;
	  $ld_saldo=0;
	 $ls_nivel=$this->int_scg->uf_scg_obtener_nivel($ls_next_cuenta);
	 while($ls_nivel>=1)
	 {
		  $li_pos=$this->ds_Balance1->find("sc_cuenta",$ls_next_cuenta);
		  if($li_pos>0)
		  {
			  $ld_saldo=$this->ds_Balance1->getValue("saldo",$li_pos);
			  $ld_saldo=$ld_saldo+$ad_saldo_ganancia;
			  $this->ds_Balance1->updateRow("saldo",$ld_saldo,$li_pos);
			  //print "NIVEL->".$ls_nivel."   CUENTA->".$ls_next_cuenta."  SALDO->".$ld_saldo."  GANANCIA->".$ad_saldo_ganancia."<br>";
		  }	 
		  else
		  {

			    $lb_valido=$this->uf_select_denominacion($ls_next_cuenta,$ls_denominacion);			
			    if($lb_valido)
				{
                   if ($ls_nivel<=$ai_nivel) 
                   {
                       $li_nro_reg=$ai_nro_reg+1;
				       $this->ds_Balance1->insertRow("orden",$as_orden);
				       $this->ds_Balance1->insertRow("num_reg",$li_nro_reg);
				       $this->ds_Balance1->insertRow("sc_cuenta",$ls_next_cuenta);     
				       $this->ds_Balance1->insertRow("denominacion",$ls_denominacion);
				       $this->ds_Balance1->insertRow("nivel",$ls_nivel);
				       $this->ds_Balance1->insertRow("saldo",$ad_saldo_ganancia);				  
                   }
				}   
		  } 													
		  if($ls_nivel==1)
		  {
			 return;
		  }//if
		  $ls_next_cuenta=$this->int_scg->uf_scg_next_cuenta_nivel($ls_next_cuenta);
		  $ls_nivel=$this->int_scg->uf_scg_obtener_nivel($ls_next_cuenta);
		  
	 }//while	 
   }//uf_scg_reporte_actualizar_resultado_BG
   
function uf_select_denominacion($as_sc_cuenta,&$as_denominacion)
{
	//////////////////////////////////////////////////////////////////////////////////////////////////
	//	      Function:  uf_select_denominacion 
	//	     Arguments:  $as_sc_cuenta  // codigo de la cuenta
	//                   $as_denominacion  // denominacion de la cuenta (referencia)
	//	       Returns:	 retorna un arreglo con las cuentas inferiores  
	//	   Description:  Busca la denominacion de la cuenta
	//     Creado por :  Ing. Yozelin Barragan
	// Fecha Creacion :  14/08/2006                      Fecha ltima Modificacion : 
	///////////////////////////////////////////////////////////////////////////////////////////////////
	$lb_valido=true;
    $ls_codemp = $this->la_empresa["codemp"];
	$ls_sql = "SELECT denominacion FROM scg_cuentas WHERE sc_cuenta='".$as_sc_cuenta."' AND codemp='".$ls_codemp."' ";
    $rs_data=$this->io_sql->select($ls_sql);
	if($rs_data===false)
	{
	    $lb_valido=false;
		$this->is_msg_error="Error en consulta metodo uf_select_denominacion ".$this->io_fun->uf_convertirmsg($this->io_sql->message);
	}
	else
	{
	   if($row=$this->io_sql->fetch_row($rs_data))
	   {
	      $as_denominacion=$row["denominacion"];
	   }
	   $this->io_sql->free_result($rs_data);
	}
    return  $lb_valido;
 }//uf_select_denominacion
   
function uf_balance_general_consolidado($ad_fecfin)
{
	$lb_valido=true;
	$ds_Balance2=new class_datastore();
	$ldec_resultado=0;
	$ld_saldo_ganancia=0;
	$this->ls_activo    = trim($this->la_empresa["activo"]);
	$this->ls_pasivo    = trim($this->la_empresa["pasivo"]);		
	$this->ls_capital   = trim($this->la_empresa["capital"]);
	$this->ls_orden_d   = trim($this->la_empresa["orden_d"]);
	$this->ls_orden_h   = trim($this->la_empresa["orden_h"]);
	$this->ls_ingreso   = trim($this->la_empresa["ingreso"]);
	$this->ls_gastos    = trim($this->la_empresa["gasto"]);
	
	$this->ls_cta_resultado = trim($this->la_empresa["c_resultad"]);
	$this->ls_resultado = trim($this->la_empresa["resultado"]);
	
	$ad_fecfin=$this->io_fun->uf_convertirdatetobd($ad_fecfin);
	$ls_codemp=$this->la_empresa["codemp"];
		
	 /*$ls_sql = "SELECT TRIM(scg_cuentas_consolida.sc_cuenta) as sc_cuenta, scg_cuentas_consolida.nivel,
					   scg_cuentas_consolida.denominacion,0 as mondeb, 0 as monhab
				  FROM scg_cuentas_consolida
				 WHERE sc_cuenta like '".$this->ls_activo."%' 
					OR sc_cuenta like '".$this->ls_pasivo."%' 
					OR sc_cuenta like '".$this->ls_resultado."%' 
					OR sc_cuenta like '".$this->ls_capital."%' 
					OR sc_cuenta like '".$this->ls_orden_d."%' 
					OR sc_cuenta like '".$this->ls_orden_h."%'
				 UNION
				SELECT TRIM(sc_cuenta) as sc_cuenta,0 as nivel,'' as denominacion,coalesce(sum(debe_mes),0)as mondeb, 
				       coalesce(sum(haber_mes),0) as monhab 
				  FROM scg_saldos_consolida 
				 WHERE (sc_cuenta like '".$this->ls_activo."%' 
					OR sc_cuenta like '".$this->ls_pasivo."%' 
					OR sc_cuenta like '".$this->ls_resultado."%' 
					OR sc_cuenta like '".$this->ls_capital."%' 
					OR sc_cuenta like '".$this->ls_orden_d."%' 
					OR sc_cuenta like '".$this->ls_orden_h."%')
				   AND fecsal<='".$ad_fecfin."'
				 GROUP BY sc_cuenta
				 ORDER BY sc_cuenta";*/

$ls_sql=" SELECT SC.sc_cuenta,SC.denominacion,SC.status,SC.nivel as rnivel, coalesce(curSaldo.mondeb,0) as mondeb,
                 coalesce(curSaldo.monhab,0) as monhab,0 as nivel
            FROM scg_cuentas_consolida SC LEFT OUTER JOIN (SELECT codemp,sc_cuenta, coalesce(sum(debe_mes),0)as mondeb,
																  coalesce(sum(haber_mes),0) as monhab
															 FROM scg_saldos_consolida
															WHERE fecsal<='".$ad_fecfin."'
															GROUP BY codemp,sc_cuenta) curSaldo
              ON SC.sc_cuenta=curSaldo.sc_cuenta
           WHERE SC.sc_cuenta like '".$this->ls_activo."%' 
              OR SC.sc_cuenta like '".$this->ls_pasivo."%'
			  OR SC.sc_cuenta like '".$this->ls_resultado."%'
			  OR SC.sc_cuenta like '".$this->ls_capital."%'
			  OR SC.sc_cuenta like '".$this->ls_orden_d."%'
			  OR SC.sc_cuenta like '".$this->ls_orden_h."%'
           ORDER BY trim(SC.sc_cuenta)";
                     echo $ls_sql;die();
     $rs_data=$this->io_sql->select($ls_sql);
	 if ($rs_data===false)
	    {
		  $this->is_msg_error="Error en consulta metodo uf_scg_reporte_balance_general_consolidado;".$this->io_fun->uf_convertirmsg($this->io_sql->message);
		  $lb_valido = false;
	    }
	 else
	    {
          $ld_saldo_ganancia=0;
		  while(!$rs_data->EOF)
		       {
			     $ls_scgcta = trim($rs_data->fields["sc_cuenta"]);
				 $ls_dencta = $rs_data->fields["denominacion"];				 
				 $ld_mondeb = number_format($rs_data->fields["mondeb"],2,'.','');
				 $ld_monhab = number_format($rs_data->fields["monhab"],2,'.','');
				 $li_nivcta = $rs_data->fields["rnivel"];
				 $ls_nivcta = $li_nivcta;
				 $this->ds_Prebalance->insertRow("scgcta",$ls_scgcta);
				 $this->ds_Prebalance->insertRow("dencta",$ls_dencta);
				 $this->ds_Prebalance->insertRow("mondeb",$ld_mondeb);
				 $this->ds_Prebalance->insertRow("monhab",$ld_monhab);
				 $this->ds_Prebalance->insertRow("nivcta",$ls_nivcta);
				 $this->ds_Prebalance->insertRow("rnivcta",$li_nivcta);				 
			     $rs_data->MoveNext();
			   }
		  
		  $this->ds_Prebalance->group_by(array('0'=>'scgcta'),array('0'=>'monhab'),'scgcta');
		  $this->ds_Prebalance->sortData('scgcta');
	      $li_totrows = $this->ds_Prebalance->getRowCount("scgcta");
		  if ($li_totrows==0)
		     {
		       $lb_valido = false;
		       return false;
		     }
	    }
	 $ld_saldo_i=0;		
	 if ($lb_valido)
	    {
	      $lb_valido = $this->uf_scg_reporte_select_saldo_ingreso_consolida($ad_fecfin,$this->ls_ingreso,$ld_saldo_i);
	    }  
     if ($lb_valido)
	    {
          $ld_saldo_g=0;	 
	      $lb_valido=$this->uf_scg_reporte_select_saldo_gasto_consolida($ad_fecfin,$this->ls_gastos,$ld_saldo_g);  
	    }
	 if ($lb_valido)
	    {
	      $ld_saldo_ganancia=$ld_saldo_ganancia+($ld_saldo_i+$ld_saldo_g);
	    }
	 
	 $la_sc_cuenta	  =	array();
	 $la_denominacion = array();
	 $la_saldo		  = array();
	 for ($i=1;$i<=$li_nivcta;$i++)
		 {
		   $la_sc_cuenta[$i]="";
		   $la_denominacion[$i]="";
		   $la_saldo[$i]=0;
		 }
		 
	 $ld_saldo_resultado=0;
	 for ($li_z=1;$li_z<=$li_totrows;$li_z++)
	     {
		   $ls_scgcta = trim($this->ds_Prebalance->getValue("scgcta",$li_z));
		   $ld_mondeb = $this->ds_Prebalance->getValue("mondeb",$li_z);
		   $ld_monhab = $this->ds_Prebalance->getValue("monhab",$li_z);
		   $ls_dencta = $this->ds_Prebalance->getValue("dencta",$li_z);
		   $li_nivcta = $this->ds_Prebalance->getValue("nivcta",$li_z);
		   $ls_nivcta = $this->ds_Prebalance->getValue("rnivcta",$li_z);
		   $ls_tipcta = substr($ls_scgcta,0,1);
	 	   switch($ls_tipcta){
			  case $this->ls_activo:
				$ls_orden=1;
			  break;
			  case $this->ls_pasivo:
				$ls_orden=2;
			  break;
			  case $this->ls_capital:
				$ls_orden=3;
			  break;				
			  case $this->ls_resultado:
				$ls_orden=4;
			  break;
			  case $this->ls_orden_d:
				$ls_orden=5;
			  break;		
			  case $this->ls_orden_h:
				$ls_orden=6;
			  break;
			  default:
				$ls_orden=7;		
		   }
		   $ldec_saldo=$ld_mondeb-$ld_monhab;
		   if (($ls_tipcta==$this->ls_pasivo || $ls_tipcta==$this->ls_resultado || $ls_tipcta==$this->ls_capital)&&($li_nivcta==1))
		      {
			    $ld_saldo_resultado = $ld_saldo_resultado+$ldec_saldo;
		      }	
           $li_nro_reg=0;		
		   if ($li_nivcta==4)	
		      {
			    $li_nro_reg++;
				$la_sc_cuenta[$ls_nivcta]    = $ls_scgcta;
				$la_denominacion[$ls_nivcta] = $ls_dencta;
				$la_saldo[$ls_nivcta]        = $ldec_saldo;
			    $this->ds_Balance1->insertRow("orden",$ls_orden);
		        $this->ds_Balance1->insertRow("num_reg",$li_nro_reg);
				$this->ds_Balance1->insertRow("sc_cuenta",$ls_scgcta);
				$this->ds_Balance1->insertRow("denominacion",$ls_dencta);
				$this->ds_Balance1->insertRow("nivel",$li_nivcta);
				$this->ds_Balance1->insertRow("saldo",$ldec_saldo);
		      }
		   else
		      {
			    if (empty($la_sc_cuenta[$li_nivcta]))
				   {
				     $li_nro_reg++;
					 $la_sc_cuenta[$ls_nivcta]    = $ls_scgcta;
				     $la_denominacion[$ls_nivcta] = $ls_dencta;
				     $la_saldo[$ls_nivcta]        = $ldec_saldo;				     
				     $this->ds_Balance1->insertRow("orden",$ls_orden);
				     $this->ds_Balance1->insertRow("num_reg",$li_nro_reg);
				     $this->ds_Balance1->insertRow("sc_cuenta",$ls_scgcta);
				     $this->ds_Balance1->insertRow("denominacion",$ls_dencta);
				     $this->ds_Balance1->insertRow("nivel",-$li_nivcta);
				     $this->ds_Balance1->insertRow("saldo",$ldec_saldo);
				   }
			    else
				   {
				     $li_nro_reg++;
					 $this->uf_scg_reporte_calcular_total_BG($li_nro_reg,$ls_prev_nivel,$ls_nivcta,$la_sc_cuenta,$la_denominacion,$la_saldo); 
				     $la_sc_cuenta[$ls_nivcta]    = $ls_scgcta;
				     $la_denominacion[$ls_nivcta] = $ls_dencta;
				     $la_saldo[$ls_nivcta]	 	  = $ldec_saldo;
				     $this->ds_Balance1->insertRow("orden",$ls_orden);
				     $this->ds_Balance1->insertRow("num_reg",$li_nro_reg);
				     $this->ds_Balance1->insertRow("sc_cuenta",$ls_scgcta);
				     $this->ds_Balance1->insertRow("denominacion",$ls_dencta);
				     $this->ds_Balance1->insertRow("nivel",-$li_nivcta);
				     $this->ds_Balance1->insertRow("saldo",$ldec_saldo);
				   }
			 }
		   $ls_prev_nivel=$li_nivcta;			
	     }
	 $this->uf_scg_reporte_calcular_total_BG(&$li_nro_reg,$ls_prev_nivel,1,$la_sc_cuenta,$la_denominacion,$la_saldo); 			
	 $ld_saldo_resultado=($ld_saldo_resultado+$ld_saldo_ganancia);
	
	 $this->uf_scg_reporte_actualizar_resultado_BG($this->ls_cta_resultado,$ld_saldo_ganancia,$li_nro_reg,$ls_orden); 
	 $this->ds_Balance1->sortData("sc_cuenta");
	  
	 $li_total=$this->ds_Balance1->getRowCount("sc_cuenta");
	 
	 for ($li_i=1;$li_i<=$li_total;$li_i++)
	     {	
		   $ls_sc_cuenta	= $this->ds_Balance1->data["sc_cuenta"][$li_i];
		   $ls_orden		= $this->ds_Balance1->data["orden"][$li_i];
		   $li_nro_reg		= $this->ds_Balance1->data["num_reg"][$li_i];
		   $ls_denominacion = $this->ds_Balance1->data["denominacion"][$li_i];
		   $ls_nivel		= $this->ds_Balance1->data["nivel"][$li_i];
		   $ld_saldo		= $this->ds_Balance1->data["saldo"][$li_i];
		   $li_pos			= $this->ds_Prebalance->find("scgcta",$ls_sc_cuenta);
		   if ($li_pos>0)
		      {  
		        $ls_rnivel = $this->ds_Prebalance->data["rnivcta"][$li_pos];
		      }
		   else
		      {
		        $ls_rnivel=0;
		      }
	       $ds_Balance2->insertRow("orden",$ls_orden);
	       $ds_Balance2->insertRow("num_reg",$li_nro_reg);
	       $ds_Balance2->insertRow("sc_cuenta",$ls_sc_cuenta);
	       $ds_Balance2->insertRow("denominacion",$ls_denominacion);
	       $ds_Balance2->insertRow("nivel",$ls_nivel);
	       $ds_Balance2->insertRow("saldo",$ld_saldo);
	       $ds_Balance2->insertRow("rnivel",$ls_rnivel);
		   $ds_Balance2->insertRow("total",$ld_saldo_resultado);
	     }//for
	 
	 $li_tot=$ds_Balance2->getRowCount("sc_cuenta");
	 for ($li_i=1;$li_i<=$li_tot;$li_i++)
	     {  
		   $ls_sc_cuenta	   = $ds_Balance2->data["sc_cuenta"][$li_i];
		   $ls_orden		   = $ds_Balance2->data["orden"][$li_i];
		   $li_nro_reg		   = $ds_Balance2->data["num_reg"][$li_i];
		   $ls_denominacion    = $ds_Balance2->data["denominacion"][$li_i];
		   $ls_nivel		   = $ds_Balance2->data["nivel"][$li_i];
		   $ld_saldo		   = $ds_Balance2->data["saldo"][$li_i];
		   $ls_rnivel		   = $ds_Balance2->data["rnivel"][$li_i];
		   $ld_saldo_resultado = $ds_Balance2->data["total"][$li_i];
		   $this->ds_reporte->insertRow("orden",$ls_orden);
		   $this->ds_reporte->insertRow("num_reg",$li_nro_reg);
		   $this->ds_reporte->insertRow("sc_cuenta",$ls_sc_cuenta);
		   $this->ds_reporte->insertRow("denominacion",$ls_denominacion);
		   $this->ds_reporte->insertRow("nivel",$ls_nivel);
		   $this->ds_reporte->insertRow("saldo",$ld_saldo);
		   $this->ds_reporte->insertRow("rnivel",$ls_rnivel);
		   $this->ds_reporte->insertRow("total",$ld_saldo_resultado);
	     }//for
	 unset($this->ds_Prebalance,$this->ds_Balance1,$ds_Balance2);
	 return $lb_valido;  
	}

function uf_scg_reporte_select_saldo_ingreso_consolida($adt_fecini,$ai_ingreso,&$ad_saldo) 
{				 
	 //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_scg_reporte_select_saldo_ingreso_consolida
	 //         Access :	private
	 //     Argumentos :    $adt_fecini  // fecha  desde 
     //              	    $ai_ingreso  // numero de la cuenta de ingraso 
	 //                     $ad_saldo  //  total saldo (referencia)
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	Reporte que genera salida  del Estado de Resultado  
	 //     Creado por :    Ing. Yozelin Barragan.
	 // Fecha Creacion :    02/05/2006          Fecha ltima Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $ls_codemp = $this->la_empresa["codemp"];
	 $lb_valido=true;
	 if ($_SESSION["ls_gestor"]=='INFORMIX')
	    {
	     $ls_sql=" SELECT case sum(SD.debe_mes-SD.haber_mes) when null then 0 else sum(SD.debe_mes-SD.haber_mes) end saldo ".
                 "   FROM scg_cuentas_consolida SC, scg_saldos_consolida SD ".
                 "  WHERE SC.status='S'
					  AND fecsal<='".$adt_fecini."' 
					  AND SC.sc_cuenta like '".$ai_ingreso."%'				 
				      AND SC.sc_cuenta = SD.sc_cuenta 
				      AND SC.codemp = SD.codemp";
		}
	 else
		{
		  $ls_sql="SELECT COALESCE(sum(scg_saldos_consolida.debe_mes-scg_saldos_consolida.haber_mes),0) as saldo
                     FROM scg_cuentas_consolida, scg_saldos_consolida
                    WHERE scg_cuentas_consolida.status='S' 
					  AND scg_saldos_consolida.fecsal<='".$adt_fecini."' 
					  AND scg_cuentas_consolida.sc_cuenta like '".$ai_ingreso."%'
					  AND scg_cuentas_consolida.codemp=scg_saldos_consolida.codemp
					  AND scg_cuentas_consolida.sc_cuenta=scg_saldos_consolida.sc_cuenta";
		}
	 $rs_data=$this->io_sql->select($ls_sql);
	 if ($rs_data===false)
	    {
		  $this->is_msg_error="CLASS->sigesp_scg_class_bal_general.php;M�todo->uf_scg_reporte_select_saldo_ingreso_consolida();".$this->io_fun->uf_convertirmsg($this->io_sql->message);
		  $lb_valido = false;
	    }
	 else
	    {
		  if ($row=$this->io_sql->fetch_row($rs_data))
		     {
		       $ad_saldo=$row["saldo"];
		     }
		  $this->io_sql->free_result($rs_data);
	    } 
	 return $lb_valido;   
   }//fin uf_scg_reporte_select_saldo_ingreso_consolida

function  uf_scg_reporte_select_saldo_gasto_consolida($adt_fecini,$ai_gasto,&$ad_saldo) 
{				 
	 //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_scg_reporte_select_saldo_gasto_consolida
	 //         Access :	private
	 //     Argumentos :    $adt_fecini  // fecha  desde 
     //              	    $ai_gasto  // numero de la cuenta de gasto
	 //                     $ad_saldo  //  total saldo (referencia)
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	Reporte que genera salida  del Estado de Resultado  
	 //     Creado por :    Ing. Yozelin Barragan.
	 // Fecha Creacion :    02/05/2006          Fecha ltima Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $ls_codemp = $this->la_empresa["codemp"];
	 $lb_valido=true;
	 if ($_SESSION["ls_gestor"]=='INFORMIX')
	    {
	      $ls_sql = "SELECT CASE SUM(scg_saldos_consolida.debe_mes-scg_saldos_consolida.haber_mes) 
		                    WHEN NULL THEN 0 ELSE SUM(scg_saldos_consolida.debe_mes-scg_saldos_consolida.haber_mes) end saldo
                       FROM scg_cuentas_consolida, scg_saldos_consolida
                      WHERE scg_cuentas_consolida.status='S'
						AND scg_saldos_consolida.fecsal<='".$adt_fecini."'
						AND scg_cuentas_consolida.sc_cuenta like '".$ai_gasto."%'
					    AND scg_cuentas_consolida.codemp = scg_saldos_consolida.codemp
						AND TRIM(scg_cuentas_consolida.sc_cuenta) = TRIM(scg_saldos_consolida.sc_cuenta)";
		}
	 else 
	    {
	      $ls_sql = "SELECT COALESCE(sum(scg_saldos_consolida.debe_mes-scg_saldos_consolida.haber_mes),0) as saldo
                       FROM scg_cuentas_consolida, scg_saldos_consolida
                      WHERE scg_cuentas_consolida.status='S'
					    AND scg_saldos_consolida.fecsal<='".$adt_fecini."' 
					    AND scg_cuentas_consolida.sc_cuenta like '".$ai_gasto."%'
						AND scg_cuentas_consolida.codemp = scg_saldos_consolida.codemp
						AND TRIM(scg_cuentas_consolida.sc_cuenta) = TRIM(scg_saldos_consolida.sc_cuenta)";
	    }
	 $rs_data=$this->io_sql->select($ls_sql);
	 if ($rs_data===false)
	    {
		  $this->is_msg_error="CLASS->sigesp_scg_class_bal_general.php;M�todo->uf_scg_reporte_select_saldo_gasto_consolida();".$this->io_fun->uf_convertirmsg($this->io_sql->message);
		  $lb_valido = false;
	    }
	 else
	    {
		  if ($row=$this->io_sql->fetch_row($rs_data))
	 	     {
		       $ad_saldo=$row["saldo"];
		     }
		  $this->io_sql->free_result($rs_data);
	 } 
	 return $lb_valido;   
   }//fin uf_scg_reporte_select_saldo_gasto_consolida
   
   
  function uf_obtener_cuentas_acreedoras($ad_fecfin,$ai_nivel)
  {
     //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_obtener_cuentas_acreedoras
	 //         Access :	private
	 //     Argumentos :    $adt_fecfin  // fecha  hasta
	 //                     $ai_nivel    // Nivel de las Cuentas
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	Reporte que genera salida  del Estado de Resultado para las Cuentas Acreedoras
	 //     Creado por :    Ing. Arnaldo Su�rez
	 // Fecha Creacion :    14/05/2010          Fecha ltima Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $ad_fecfin=$this->io_fun->uf_convertirdatetobd($ad_fecfin);
	 $ls_codemp=$this->la_empresa["codemp"];
	 $lb_valido = true;
	 $ls_sql=  " SELECT SC.sc_cuenta,SC.denominacion,SC.status,SC.nivel as rnivel, ".
			   "        coalesce(curSaldo.T_Debe,0) as total_debe, ".
			   "        coalesce(curSaldo.T_Haber,0) as total_haber,0 as nivel,trim(SC.cueproacu) as cueproacu ".
			   " FROM scg_cuentas SC LEFT OUTER JOIN (SELECT codemp,sc_cuenta, coalesce(sum(debe_mes),0)as T_Debe, ".
			   "                                             coalesce(sum(haber_mes),0) as T_Haber ".
			   "                                      FROM   scg_saldos ".
			   "                                      WHERE  codemp='".$ls_codemp."' AND fecsal<='".$ad_fecfin."' ".
			   "                                      GROUP BY codemp,sc_cuenta) curSaldo ".
			   " ON SC.sc_cuenta=curSaldo.sc_cuenta ".
			   " WHERE SC.codemp=curSaldo.codemp AND  curSaldo.codemp='".$ls_codemp."' AND ".
			   "       SC.sc_cuenta like '".$this->ls_orden_h."%'".
			   " ORDER BY 1";
	$rs_data=$this->io_sql->select($ls_sql);
	if($rs_data === false)
	{
	 $this->is_msg_error="CLASS->sigesp_scg_class_bal_general.php;M�todo->uf_obtener_cuentas_acreedoras();".$this->io_fun->uf_convertirmsg($this->io_sql->message);
     $lb_valido = false;
	}
	else
	{
	 if($rs_data->EOF)
	 {
	  $lb_valido = false;
	 }
	}
	
	if($lb_valido)
	{
	  while(!$rs_data->EOF)
	 {
		  $ls_sc_cuenta    = trim($rs_data->fields["sc_cuenta"]);
		  $ls_denominacion = trim($rs_data->fields["denominacion"]);
		  $ld_saldo        = $rs_data->fields["total_debe"] - $rs_data->fields["total_haber"];
		  $ls_rnivel       = $rs_data->fields["rnivel"];
		  $ls_status       = $rs_data->fields["status"];
		  if($ls_status=="C")
		  {
			$ls_nivel="4";		
		  }//if
		  else
		  {
			$ls_nivel=$ls_rnivel;		
		  }//else
		  if($ls_nivel<=$ai_nivel)
		  {
			  $this->ds_cuentas_acreedoras->insertRow("sc_cuenta",$ls_sc_cuenta);
			  $this->ds_cuentas_acreedoras->insertRow("denominacion",$ls_denominacion);
			  $this->ds_cuentas_acreedoras->insertRow("nivel",$ls_nivel);
			  $this->ds_cuentas_acreedoras->insertRow("saldo",$ld_saldo);
			  $this->ds_cuentas_acreedoras->insertRow("rnivel",$ls_rnivel);
		   }//if
		   $rs_data->MoveNext();
	  }
	  $this->io_sql->free_result($rs_data);
	}
	 
   return $lb_valido;
 }
 
   function uf_obtener_cuentas_acreedoras_formato2($ad_fecfin,$ai_nivel)
  {
     //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_obtener_cuentas_acreedoras_formato2
	 //         Access :	private
	 //     Argumentos :    $adt_fecfin  // fecha  hasta
	 //                     $ai_nivel    // Nivel de las Cuentas
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	Reporte que genera salida  del Estado de Resultado para las Cuentas Acreedoras - Formato 2
	 //     Creado por :    Ing. Arnaldo Su�rez
	 // Fecha Creacion :    17/05/2010          Fecha �ltima Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $this->ls_orden_h=trim($this->la_empresa["orden_h"]);
	 $ls_ceros = "";
	 $ls_formcont = trim($this->la_empresa["formcont"]);
	 $ls_formcont = trim(str_replace("-","",$ls_formcont));
	 $ls_ceros = str_pad("",strlen($ls_formcont)-1,"0");
	 $ls_cuenta_tot_acreedora = trim(substr($this->ls_orden_h,0,1));
	 if(!empty($ls_cuenta_tot_acreedora))
	 {
	  $ls_cuenta_tot_acreedora .= $ls_ceros;
	 }
	 else
	 {
	  $ls_cuenta_tot_acreedora = "";
	 }
	 $ad_fecfin=$this->io_fun->uf_convertirdatetobd($ad_fecfin);
	 $ls_codemp=$this->la_empresa["codemp"];
	 $lb_valido = true;
	 $ls_sql=  " SELECT DISTINCT '".$ls_cuenta_tot_acreedora."' as sc_cuenta, 'CUENTAS DE ORDEN' as denominacion, 'S' as status, 1 as rnivel,'' as referencia,  ".
				  "        coalesce(SUM(curSaldo.T_Debe),0) as total_debe, ".
				  "        coalesce(SUM(curSaldo.T_Haber),0) as total_haber,0 as nivel, '' as cueproacu, 1 as tiporden ".
				  " FROM scg_cuentas SC LEFT OUTER JOIN (SELECT codemp,sc_cuenta, coalesce(sum(debe_mes),0)as T_Debe, ".
				  "                                             coalesce(sum(haber_mes),0) as T_Haber ".
				  "                                      FROM   scg_saldos ".
				  "                                      WHERE  codemp='".$ls_codemp."' AND fecsal<='".$ad_fecfin."' ".
				  "                                      GROUP BY codemp,sc_cuenta) curSaldo ".
				  " ON SC.sc_cuenta=curSaldo.sc_cuenta ".
				  " WHERE SC.codemp=curSaldo.codemp AND  curSaldo.codemp='".$ls_codemp."' AND ".
				  "      SC.sc_cuenta like '".$this->ls_orden_h."%' AND SC.status = 'C'".
				  " UNION ".
				  " SELECT SC.sc_cuenta,SC.denominacion,SC.status,SC.nivel as rnivel,SC.referencia as referencia,  ".
				  "        coalesce(curSaldo.T_Debe,0) as total_debe, ".
				  "        coalesce(curSaldo.T_Haber,0) as total_haber,0 as nivel,SC.cueproacu, 1 as tiporden ".
				  " FROM scg_cuentas SC LEFT OUTER JOIN (SELECT codemp,sc_cuenta, coalesce(sum(debe_mes),0)as T_Debe, ".
				  "                                             coalesce(sum(haber_mes),0) as T_Haber ".
				  "                                      FROM   scg_saldos ".
				  "                                      WHERE  codemp='".$ls_codemp."' AND fecsal<='".$ad_fecfin."' ".
				  "                                      GROUP BY codemp,sc_cuenta) curSaldo ".
				  " ON SC.sc_cuenta=curSaldo.sc_cuenta ".
				  " WHERE SC.codemp=curSaldo.codemp AND  curSaldo.codemp='".$ls_codemp."' AND ".
				  "       (SC.sc_cuenta like '".$this->ls_orden_h."%') ";
	$rs_data=$this->io_sql->select($ls_sql);
	if($rs_data === false)
	{
	 $this->is_msg_error="CLASS->sigesp_scg_class_bal_general.php;M�todo->uf_obtener_cuentas_acreedoras();".$this->io_fun->uf_convertirmsg($this->io_sql->message);
     $lb_valido = false;
	}
	else
	{
	 if($rs_data->EOF)
	 {
	  $lb_valido = false;
	 }
	}
	
	if($lb_valido)
	{
	  
	  
	  $arr_cuenta    = array($ai_nivel);
      $arr_denomina  = array($ai_nivel);
      $arr_saldos    = array($ai_nivel);
	  $li_row=0;
	  $an_nivel = 0;
	  $nPrevNivel = 0; 
	  $nRegNo = 0; 
	  while(!$rs_data->EOF)
	 {
		  $ls_sc_cuenta=trim($rs_data->fields["sc_cuenta"]);
		  $ls_denominacion=$rs_data->fields["denominacion"];
		  $ls_status=$rs_data->fields["status"];
		  $ls_rnivel=$rs_data->fields["rnivel"];
		  $ld_total_debe=$rs_data->fields["total_debe"];
		  $ld_total_haber=$rs_data->fields["total_haber"];
		  $ls_cueproacu = $rs_data->fields["cueproacu"];
		  $ls_referencia = $rs_data->fields["referencia"];
		  $ld_saldo        = $rs_data->fields["total_debe"] - $rs_data->fields["total_haber"];
		  $this->uf_cuenta_por_nivel($ls_sc_cuenta,&$ls_cuentasalida,&$lr_nivel); 
		  if($ls_status=="C")
		  {
			$ls_nivel="4";		
		  }//if
		  else
		  {
			$ls_nivel=$ls_rnivel;		
		  }//else
		  if($ls_nivel<=$ai_nivel)
		  {
			  $this->ds_ctas_temp->insertRow("sc_cuenta",$ls_sc_cuenta);
			  $this->ds_ctas_temp->insertRow("denominacion",$ls_denominacion);
			  $this->ds_ctas_temp->insertRow("nivel",$ls_nivel);
			  $this->ds_ctas_temp->insertRow("status",$ls_status);
			  $this->ds_ctas_temp->insertRow("saldo",$ld_saldo);
			  $this->ds_ctas_temp->insertRow("rnivel",$ls_rnivel);
			  $this->ds_ctas_temp->insertRow("cuenta_salida",$ls_cuentasalida);
			  $this->ds_ctas_temp->insertRow("referencia",$ls_referencia);
			  $this->ds_ctas_temp->insertRow("cerrado",'');
		   }//if
		   
		   $rs_data->MoveNext();
	  }
	  
	    $li_row=$this->ds_ctas_temp->getRowCount("sc_cuenta");
		$an_nivel = intval($ai_nivel);
		$nPrevNivel = intval($this->ds_ctas_temp->data["rnivel"][$li_row]); 
		$nRegNo = 0; 
		for($li_z=1;$li_z<=$li_row;$li_z++)
		{
			   $ls_sc_cuenta       = $this->ds_ctas_temp->data["sc_cuenta"][$li_z];
			   $ls_denominacion    = $this->ds_ctas_temp->data["denominacion"][$li_z];
			   $ls_nivel           = $this->ds_ctas_temp->data["nivel"][$li_z];
			   $ls_status          = $this->ds_ctas_temp->data["status"][$li_z];
			   $ld_saldo           = $this->ds_ctas_temp->data["saldo"][$li_z];
			   $ls_rnivel          = $this->ds_ctas_temp->data["rnivel"][$li_z];
			   $ls_referencia      = $this->ds_ctas_temp->data["referencia"][$li_z]; 
			   $ls_cuenta_salida   = $this->ds_ctas_temp->data["cuenta_salida"][$li_z]; 
			   $ls_cerrado         = $this->ds_ctas_temp->data["cerrado"][$li_z]; 
			   $ln_nivel           = intval($ls_rnivel);
			   if (empty($ld_saldo))
			   {
				   $ld_saldo=0;
			   }
			   if ($ln_nivel==$an_nivel)
			   {
					$nRegNo++;
					$this->ds_cuentas_acreedoras->insertRow("sc_cuenta",$ls_sc_cuenta);                     
					$this->ds_cuentas_acreedoras->insertRow("denominacion",$ls_denominacion);
					$this->ds_cuentas_acreedoras->insertRow("nivel",$ls_nivel);
					$this->ds_cuentas_acreedoras->insertRow("status",$ls_status);
					$this->ds_cuentas_acreedoras->insertRow("saldo",$ld_saldo);
					$this->ds_cuentas_acreedoras->insertRow("rnivel",$ls_rnivel);
					$this->ds_cuentas_acreedoras->insertRow("referencia",$ls_referencia);
					$this->ds_cuentas_acreedoras->insertRow("cuenta_salida",$ls_cuenta_salida);
					$this->ds_cuentas_acreedoras->insertRow("cerrado",'');
			   }
			   else
			   {
					if (empty($arr_cuenta[intval($ls_rnivel)]))
					   {
							$arr_cuenta[$ln_nivel]      = $ls_sc_cuenta;
							$arr_denomina[$ln_nivel]    = $ls_denominacion;
							$arr_saldos[$ln_nivel]      = $ld_saldo;
							$nRegNo++;
							$this->ds_cuentas_acreedoras->insertRow("sc_cuenta",$ls_sc_cuenta);                    
							$this->ds_cuentas_acreedoras->insertRow("denominacion",$ls_denominacion);
							$this->ds_cuentas_acreedoras->insertRow("nivel",$ls_nivel);
							$this->ds_cuentas_acreedoras->insertRow("status",$ls_status);
							$this->ds_cuentas_acreedoras->insertRow("saldo",$ld_saldo);
							$this->ds_cuentas_acreedoras->insertRow("rnivel",$ls_rnivel);
							$this->ds_cuentas_acreedoras->insertRow("referencia",$ls_referencia);
							$this->ds_cuentas_acreedoras->insertRow("cuenta_salida",$ls_cuenta_salida);
							$this->ds_cuentas_acreedoras->insertRow("cerrado",'');
					   }
					   else
					   {
							$a=1;
							$this->uf_downstair_acreedoras_formato2($nRegNo,$nPrevNivel,intval($ls_rnivel),&$arr_cuenta,&$arr_denomina,&$arr_saldos);
							$arr_cuenta[$ln_nivel]      = $ls_sc_cuenta;
							$arr_denomina[$ln_nivel]    = $ls_denominacion;
							$arr_saldos[$ln_nivel]      = $ld_saldo;
							$nRegNo++;
							$this->ds_cuentas_acreedoras->insertRow("sc_cuenta",$ls_sc_cuenta);                     
							$this->ds_cuentas_acreedoras->insertRow("denominacion",$ls_denominacion);
							$this->ds_cuentas_acreedoras->insertRow("nivel",$ls_nivel);
							$this->ds_cuentas_acreedoras->insertRow("status",$ls_status);
							$this->ds_cuentas_acreedoras->insertRow("saldo",$ld_saldo);
							$this->ds_cuentas_acreedoras->insertRow("rnivel",$ls_rnivel);
							$this->ds_cuentas_acreedoras->insertRow("referencia",$ls_referencia);
							$this->ds_cuentas_acreedoras->insertRow("cuenta_salida",$ls_cuenta_salida);
							$this->ds_cuentas_acreedoras->insertRow("cerrado",'');
					   }  
			   }
	
			   $nPrevNivel = intval($ls_rnivel); 
		}//for
	  
	  $this->io_sql->free_result($rs_data);
	}
   $this->uf_downstair_acreedoras_formato2($nRegNo,$nPrevNivel,1,&$arr_cuenta,&$arr_denomina,&$arr_saldos);
   unset($this->ds_ctas_temp);
   return $lb_valido;
 }
 
 /****************************************************************************************************************************************/
function uf_downstair_acreedoras_formato2($li_npos,$nHighStair,$nLowerStair,$arr_cuenta,$arr_denomina,&$arr_saldos)
{
    $nRegNo = 0;
	for($li_z=($nHighStair-1);$li_z>=$nLowerStair;$li_z--) 
    {
        if (!empty($arr_cuenta[$li_z]))
        {
            $nRegNo++; 
            //inserta en el datastore final
            $this->ds_cuentas_acreedoras->insertRow("sc_cuenta",$arr_cuenta[$li_z]);                    
            $this->ds_cuentas_acreedoras->insertRow("denominacion",'TOTAL '.$arr_denomina[$li_z]);
            $this->ds_cuentas_acreedoras->insertRow("saldo",$arr_saldos[$li_z]);
            
                    $ls_sc_cuentat   = $arr_cuenta[$li_z]; 
                    $li_pos         = $this->ds_ctas_temp->find("sc_cuenta",$ls_sc_cuentat);
                    if ($li_pos>0)
                    { 
                        $ls_rnivel=$this->ds_ctas_temp->data["rnivel"][$li_pos];
                        $ls_status=$this->ds_ctas_temp->data["status"][$li_pos];
						$ls_referencia=$this->ds_ctas_temp->data["referencia"][$li_pos];
						$ls_cuenta_salida=$this->ds_ctas_temp->data["cuenta_salida"][$li_pos];
						$ls_nivel=$this->ds_ctas_temp->data["nivel"][$li_pos];
                    }
                    else
                    {
                        $ls_rnivel=0;
						$ls_referencia="";
						$ls_cuenta_salida="";
						$ls_status = "";
						$ls_nivel = 0;
                    }            
            $this->ds_cuentas_acreedoras->insertRow("status",$ls_status);
            $this->ds_cuentas_acreedoras->insertRow("rnivel",$ls_rnivel);
            $this->ds_cuentas_acreedoras->insertRow("referencia",$ls_referencia);
            $this->ds_cuentas_acreedoras->insertRow("cuenta_salida",$ls_cuenta_salida);
			$this->ds_cuentas_acreedoras->insertRow("nivel",$ls_nivel);
            $this->ds_cuentas_acreedoras->insertRow("cerrado",'S');

            $arr_cuenta[$li_z]      = null;
            $arr_denomina[$li_z]    = null;
            $arr_saldos[$li_z]      = null;
        }//if
    }//for
    
}

}
?>