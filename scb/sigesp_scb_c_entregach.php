<?php
class sigesp_scb_c_entregach
{
	var $dat;
 	var $SQL;
	var $is_msg_error;
	var $fun;
	var $la_security;
        var $io_validacion;
	function sigesp_scb_c_entregach($aa_security)
	{
		require_once("../shared/class_folder/class_sql.php");
		require_once("../shared/class_folder/sigesp_include.php");
		require_once("../shared/class_folder/class_funciones.php");
		require_once("../shared/class_folder/sigesp_c_seguridad.php");
                require_once("../shared/class_folder/class_validacion.php");
                

		$this->dat			= $_SESSION["la_empresa"];
		$this->sig_inc		= new sigesp_include();
		$con				= $this->sig_inc->uf_conectar();
		$this->SQL			= new class_sql($con);
		$this->fun			= new class_funciones();
		$this->la_security  = $aa_security; 
		$this->io_seguridad = new sigesp_c_seguridad();
                $this->io_validacion=new class_validacion();
	}

	function uf_cargar_cheques($as_codproben,$as_tipproben,$as_fechainicio,$as_fechafin,$object,$li_row)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	Function:	   uf_cargar_cheques
		// Access:			public
		//	Returns:			Boolean Retorna si proceso correctamente
		//	Description:	Funcion que se encarga de cargar el datastore con los cheques
		//						pendientes para ser entregados a los porveedores o beneficiarios
		//////////////////////////////////////////////////////////////////////////////

                
		
		$li_row    = 0;
		$ls_codemp = $this->dat["codemp"];
		$ls_sqlaux = "";
                $ls_fechactual =  mktime(0,0,0,date('m'),date('d'),date('Y'));
                $as_fechainicio=$this->fun->uf_convertirdatetobd($as_fechainicio);
                $as_fechafin=$this->fun->uf_convertirdatetobd($as_fechafin);
                $criterio_fecha='';

                if ($as_fechainicio!='')
                {

                    $criterio_fecha = "AND a.fecmov between '".$as_fechainicio."' and '".$as_fechafin."'";

                }

		if($as_tipproben=='P')
		{
			if (!empty($as_codproben))
			   {
			     $ls_sqlaux = " AND a.cod_pro = '".$as_codproben."' ";
			   }

			$ls_sql =  "SELECT a.numdoc as numdoc,a.conmov as conmov,a.fecmov as fecmov,a.monto as monto,a.chevau as chevau,
			                   a.codban as codban,b.nomban as nomban,a.ctaban as ctaban,c.dencta as dencta,a.cod_pro as codproben,
							   d.nompro as nomproben,a.tipo_destino
						  FROM scb_movbco a,scb_banco b,scb_ctabanco c,rpc_proveedor d 
						 WHERE a.codemp = '".$ls_codemp."' $ls_sqlaux
						   AND a.tipo_destino='P'
						   AND a.estmov='C' 
						   AND a.codope='CH' 
						   AND (a.estbpd='P' OR a.estbpd='D') 
                                                   ".$criterio_fecha."
						   AND a.estimpche=1 
						   AND a.emicheproc=0 
						   AND a.codban = b.codban 
						   AND a.ctaban=c.ctaban 
						   AND a.cod_pro=d.cod_pro 
						   AND a.codemp = b.codemp 
						   AND c.codemp=a.codemp 
						   AND d.codemp = a.codemp ";
		}
		elseif($as_tipproben=='B')
		{
			if (!empty($as_codproben))
			   {
			     $ls_sqlaux = " AND trim(a.ced_bene)= trim('".$as_codproben."') ";
			   }
			$ls_sql =	"SELECT a.numdoc as numdoc,a.conmov as conmov,a.fecmov as fecmov,a.monto as monto,a.chevau as chevau,a.codban as codban,b.nomban as nomban,a.ctaban as ctaban,c.dencta as dencta,a.ced_bene as codproben ,d.nombene||' '||d.apebene as nomproben,a.tipo_destino
						   FROM scb_movbco a,scb_banco b,scb_ctabanco c,rpc_beneficiario d 
						  WHERE a.estmov='C' $ls_sqlaux
						    AND (a.estbpd='B' OR a.estbpd='D') 
							AND a.estimpche=1 
							AND a.emicheproc=0 
							AND a.codope='CH'   
                                                        AND a.tipo_destino='B'
                                                        AND a.codemp = '".$ls_codemp."'
                                                        ".$criterio_fecha."
							AND a.codban = b.codban 
							AND a.ctaban=c.ctaban 
							AND a.ced_bene=d.ced_bene 
							AND a.codemp = b.codemp 
							AND c.codemp=a.codemp 
							AND d.codemp = a.codemp ";
		}
		else
		{
			$ls_sql =	"SELECT a.numdoc as numdoc,a.conmov as conmov,a.fecmov as fecmov,a.monto as monto,a.chevau as chevau,
			                   a.codban as codban,b.nomban as nomban,a.ctaban as ctaban,c.dencta as dencta,a.cod_pro as codproben,
							   d.nompro as nomproben,a.tipo_destino
						  FROM scb_movbco a,scb_banco b,scb_ctabanco c,rpc_proveedor d 
						 WHERE a.codemp = '".$ls_codemp."' 
						   AND a.tipo_destino='P'
						   AND a.estmov='C' 
						   AND a.codope='CH' 
						   AND (a.estbpd='P' OR a.estbpd='D')
                                                   ".$criterio_fecha."
						   AND a.estimpche=1 
						   AND a.emicheproc=0 
						   AND a.codban = b.codban 
						   AND a.ctaban=c.ctaban 
						   AND a.cod_pro=d.cod_pro 
						   AND a.codemp = b.codemp 
						   AND c.codemp=a.codemp 
						   AND d.codemp = a.codemp UNION
                                                   SELECT a.numdoc as numdoc,a.conmov as conmov,a.fecmov as fecmov,a.monto as monto,a.chevau as chevau,a.codban as codban,
                                                   b.nomban as nomban,a.ctaban as ctaban,c.dencta as dencta,a.ced_bene as codproben ,d.nombene||' '||d.apebene as nomproben,a.tipo_destino
						   FROM scb_movbco a,scb_banco b,scb_ctabanco c,rpc_beneficiario d
						  WHERE a.estmov='C'
						    AND (a.estbpd='B' OR a.estbpd='D')
							AND a.estimpche=1
							AND a.emicheproc=0
							AND a.codope='CH'
						    AND a.tipo_destino='B'
						    AND a.codemp = '".$ls_codemp."'
							AND a.codban = b.codban
							AND a.ctaban=c.ctaban
							AND a.ced_bene=d.ced_bene
							AND a.codemp = b.codemp
                                                        ".$criterio_fecha."
							AND c.codemp=a.codemp
							AND d.codemp = a.codemp";
		}
              
		$rs_result=$this->SQL->select($ls_sql);
		if(($rs_result===false))
		{
			print $this->SQL->message;	
			$lb_valido=false;
		}
		else
		{
			$lb_valido=true;
			while($row=$this->SQL->fetch_row($rs_result))
			{
				$li_row++;
				$ls_numdoc	= trim($row["numdoc"]);
				$ls_desdoc	= $row["conmov"];
				$ldec_monto	= $row["monto"];
				$ls_codban	= trim($row["codban"]);
				$ls_ctaban	= trim($row["ctaban"]);
				$ls_voucher = trim($row["chevau"]);

                                $ls_fecmov  = $this->fun->uf_formatovalidofecha($row["fecmov"]);
				$ls_fecmov  = $this->fun->uf_convertirfecmostrar($ls_fecmov);
                               
                                $ls_nombre_entrega = trim($row["nomproben"]);
                                $ls_cedula_entrega=trim($row["codproben"]);
                                $ls_tipodestino = trim($row["tipo_destino"]);
                                $ld_fecvenc=$this->io_validacion->RelativeDate($ls_fecmov,90);
                                $ld_fecvenc1  = explode('-',$ld_fecvenc);
                                $ld_fecvenc_dia = $ld_fecvenc1[0];
                                $ld_fecvenc_mes = $ld_fecvenc1[1];
                                $ld_fecvenc_ano = $ld_fecvenc1[2];
                                $ld_fecvenc_final = mktime(0,0,0,$ld_fecvenc_mes,$ld_fecvenc_dia,$ld_fecvenc_ano);
                                

                                   
                                 
				/*if ($ls_fechactual > $ld_fecvenc_final)
                                {*/
                                    //echo 'Fecha mov: '.$ls_fechactual.' fecha vencimiento: '.$ld_fecvenc_final.'<hr>';
                                    $object[$li_row][1] = "<input type=checkbox name=chksel".$li_row."   id=chksel".$li_row." value=1 style=width:15px;height:15px>";
                                    $object[$li_row][2] = "<input type=text     name=txtnumdoc".$li_row."    value='".$ls_numdoc."' class=sin-borde readonly style=text-align:center size=15 maxlength=15>";
                                    $object[$li_row][3] = "<input type=text     name=txtdesdoc".$li_row."    value='".$ls_desdoc."' title='".$ls_desdoc."' class=sin-borde readonly style=text-align:left size=30 maxlength=254>";
                                    $object[$li_row][4] = "<input type=text     name=txtmonto".$li_row."     value='".number_format($ldec_monto,2,",",".")."' class=sin-borde readonly style=text-align:right size=18 maxlength=22>";
                                    $object[$li_row][5] = "<input type=text     name=txtfecmov".$li_row."    value='".$ls_fecmov."' class=sin-borde readonly style=text-align:center size=8 maxlength=10>";
                                    $object[$li_row][6] = "<input type=text     name=txtcodban".$li_row."    value='".$ls_codban."' class=sin-borde readonly style=text-align:center size=3 maxlength=3>";
                                    $object[$li_row][7] = "<input type=text     name=txtcuenta".$li_row."    value='".$ls_ctaban."' class=sin-borde readonly style=text-align:center size=27 maxlength=25>";
                                    $object[$li_row][8] = "<input type=text     name=txtvoucher".$li_row."   value='".$ls_voucher."' class=sin-borde readonly style=text-align:center size=27 maxlength=25>";
                                    $object[$li_row][9] = "<input type=text     name=txtcedulaentrega".$li_row."   value='".$ls_cedula_entrega."' class=sin-borde readonly style=text-align:center size=27 maxlength=25>";
                                    $object[$li_row][10] = "<input type=text     name=txtnombrentrega".$li_row."   value='".$ls_nombre_entrega."' class=sin-borde readonly style=text-align:center size=27 maxlength=25>";
                                    $object[$li_row][11] = "<input type=text     name=txtipobenprov".$li_row."   value='".$ls_tipodestino."' class=sin-borde readonly style=text-align:center size=27 maxlength=25>";
                               //}
			}
			if($li_row==0)
			{
				$li_total=5;
				for($li_row=1;$li_row<=$li_total;$li_row++)
				{
					$object[$li_row][1] = "<input type=checkbox name=chksel".$li_row."   id=chksel".$li_row." value=1 style=width:15px;height:15px onClick='return false;'>";		
					$object[$li_row][2] = "<input type=text     name=txtnumdoc".$li_row."       value='' class=sin-borde readonly style=text-align:center size=15 maxlength=15>";
					$object[$li_row][3] = "<input type=text     name=txtdesdoc".$li_row."    value='' class=sin-borde readonly style=text-align:left size=30 maxlength=254>";
					$object[$li_row][4] = "<input type=text     name=txtmonto".$li_row."     value='' class=sin-borde readonly style=text-align:center size=18 maxlength=22>";
					$object[$li_row][5] = "<input type=text     name=txtfecmov".$li_row."    value='' class=sin-borde readonly style=text-align:center size=8 maxlength=10>"; 
					$object[$li_row][6] = "<input type=text     name=txtcodban".$li_row."    value='' class=sin-borde readonly style=text-align:center size=5 maxlength=3>"; 
					$object[$li_row][7] = "<input type=text     name=txtcuenta".$li_row."    value='' class=sin-borde readonly style=text-align:right size=22 maxlength=22>";
					$object[$li_row][8] = "<input type=text     name=txtvoucher".$li_row."   value='' class=sin-borde readonly style=text-align:right size=27 maxlength=25>";
                                        $object[$li_row][9] = "<input type=text     name=txtcedulaentrega".$li_row."   value='' class=sin-borde readonly style=text-align:center size=27 maxlength=25>";
                                        $object[$li_row][10] = "<input type=text     name=txtnombrentrega".$li_row."   value='' class=sin-borde readonly style=text-align:center size=27 maxlength=25>";
                                        $object[$li_row][11] = "<input type=text     name=txtipobenprov".$li_row."   value='' class=sin-borde readonly style=text-align:center size=27 maxlength=25>";
				}
				$li_row=$li_total;
			}
		}		
		return $lb_valido;
	}

	function uf_procesar_entregach($arr_entregach,$as_codproben,$as_tipproben,$ad_fechaentrega,$as_cedula,$as_nombre,$ai_procesado)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	Function:	uf_procesar_entregach
		// Access:			public
		//	Returns:			Boolean Retorna si proceso correctamente
		//	Description:	Funcion que se encarga de procesar la entrega del cheque al 
		//						proveedor o al beneficiario actualizando los campos de 
		//                cedula,nombre y fecha de la persona que recibio los cheques
		//////////////////////////////////////////////////////////////////////////////
		$ls_sqlaux = "";
		$ls_codemp = $this->dat["codemp"];
		$li_total  = count($arr_entregach["numdoc"]);
		$ad_fechaentrega=$this->fun->uf_convertirdatetobd($ad_fechaentrega);

               /* echo '<pre>';
                print_r($arr_entregach);
                echo '</pre>';die();*/
		$this->SQL->begin_transaction();
		for ($li_i=1;$li_i<=$li_total;$li_i++)
		    {
			  $ls_codban = $arr_entregach["codban"][$li_i];
			  $ls_ctaban = $arr_entregach["ctaban"][$li_i];
			  $ls_numdoc = $arr_entregach["numdoc"][$li_i];
                          $ls_cedula = $arr_entregach["cedula"][$li_i];
                          $ls_nombre = $arr_entregach["nombre"][$li_i];
                          $ls_tipoprovbene= $arr_entregach["tipo_destino"][$li_i];
                          
			  $ls_descripcion = "Se realiz� la entrega del cheque No $ls_numdoc ";
			  if ($ls_tipoprovbene=='P')
			  {
				   if (!empty($as_codproben))
				      {
					    $ls_sqlaux 	   = " AND cod_pro='".$as_codproben."'";

                                            if ($as_nombre!='' && $as_cedula!='')
                                                $ls_descripcion = $ls_descripcion." del proveedor ".$as_codproben." y fue entregado a $as_nombre de cedula $as_cedula";
                                            else
                                                $ls_descripcion = $ls_descripcion." del proveedor ".$as_codproben." y fue entregado a $ls_nombre de cedula $ls_cedula";
                                    }
			}
			else
			{
				   if (!empty($as_codproben))
				      {
				   	    $ls_sqlaux 	   = " AND ced_bene='".$as_codproben."'";

                                            if ($as_nombre!='' && $as_cedula!='')
                                                $ls_descripcion = $ls_descripcion." del beneficiario ".$as_codproben." y fue entregado a $as_nombre de cedula $as_cedula";
                                            else
                                                $ls_descripcion = $ls_descripcion." del beneficiario ".$as_codproben." y fue entregado a $ls_nombre de cedula $ls_cedula";
				      }
			 }

                         if ($as_nombre!='' && $as_cedula!='')
                                $cadena = "emicheced='".$as_cedula."',emichenom='".$as_nombre."',";
                         else
                                 $cadena = "emicheced='".$ls_cedula."',emichenom='".$ls_nombre."',";




			 $ls_sql = "UPDATE scb_movbco
					        SET emicheproc='".$ai_procesado."', ".$cadena." emichefec='".$ad_fechaentrega."'
					      WHERE codemp = '".$ls_codemp."'  
						    AND codban = '".$ls_codban."' 
						    AND ctaban = '".$ls_ctaban."' 
						    AND numdoc = '".$ls_numdoc."'
						    AND codope = 'CH' $ls_sqlaux";

                        
			  $rs_data = $this->SQL->execute($ls_sql);
			  if ($rs_data===false)
			     {
				   $lb_valido=false;
				   $this->is_msg_error="Error en actualizar entrega de cheque, ".$this->fun->uf_convertirmsg($this->SQL->message);
			     }
			  else
			     {
		           $lb_valido=true;
			       ///////////////////////////////////Parametros de seguridad/////////////////////////////////////////////////
			       $ls_evento="UPDATE";						
			       $lb_valido = $this->io_seguridad->uf_sss_insert_eventos_ventana($this->la_security["empresa"],$this->la_security["sistema"],$ls_evento,$this->la_security["logusr"],$this->la_security["ventanas"],$ls_descripcion);
				   ////////////////////////////////////////////////////////////////////////////////////////////////////////////								
		         }
		    }		
		return $lb_valido;
	}
}
?>