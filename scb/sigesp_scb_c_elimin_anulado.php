<?php
  ////////////////////////////////////////////////////////////////////////////////////////////////////////
  //       Class : class_sigesp_sep_integracion_php                                                     //    
  // Description : Esta clase tiene todos los metodos necesario para el manejo de la rutina integradora //
  //               con el sistema de presupuesto de  gasto y el sistema de compra.                      //               
  ////////////////////////////////////////////////////////////////////////////////////////////////////////
require_once("../shared/class_folder/class_sql.php");  
require_once("../shared/class_folder/sigesp_include.php");
require_once("../shared/class_folder/class_funciones.php");
class sigesp_scb_c_elimin_anulado
{
	//Instancia de la clase funciones.
    var $is_msg_error;
	var $dts_empresa; 
	var $dts_solicitud;
	var $obj="";
	var $io_sql;
	var $io_include;
	var $io_connect;
	var $io_function;	
	function sigesp_scb_c_elimin_anulado()
	{
		$this->io_function = new class_funciones() ;
		$this->io_include  = new sigesp_include();
		$this->io_connect  = $this->io_include->uf_conectar();
		$this->io_sql      = new class_sql($this->io_connect);		
		$this->dts_empresa = $_SESSION["la_empresa"];
	}

    // MOVIMIENTOS BANCARIOS
	function uf_select_banco_contabilizar( $as_operacion_bco, &$arr_object ,&$ai_total_record,$as_estatus,$as_numdoc,$ad_fecha )
	{	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	   Function :	uf_select_banco_contabilizar
		//       Access :	public
		//   Argumentos :   $as_operacion_bco->operacion movimiento de banco 
		//                  &$arr_object-> arreglo de objetos pantalla pintar 
		//                  &$ai_total_record->total de registros por valor 
		//                  $as_estatus->estatus de los movimientos a consultar
		//	    Returns :	movimiento contabilizado boolean
		//	Description :	Método que obtiene todas aquellos movimientos de banco en estatus 
		//                  para su contabilizacion
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$li_row=0;
        $lb_valido = true;
		$ls_codemp = $this->dts_empresa["codemp"];
		$ls_aux="";
		if($ad_fecha!="")
		{
			$ld_fecha=$this->io_function->uf_convertirdatetobd($ad_fecha);			
			$ls_aux=" AND fecmov='".$ld_fecha."' ";
		}
		if($as_numdoc!="")
		{
			$ls_aux=$ls_aux." AND numdoc like '%".$as_numdoc."%' ";
		}
		$ls_mysql  = " SELECT * ".
                     "   FROM scb_movbco ".
					 "  WHERE codemp='".$ls_codemp."' AND estmov='".$as_estatus."' AND codope='".$as_operacion_bco."' AND monto=0 ".$ls_aux;
		$rs_data=$this->io_sql->select($ls_mysql);
		if($rs_data===false)
		{   // error interno sql
			$this->is_msg_error="Error en consulta metodo ".$this->io_function->uf_convertirmsg($this->io_sql->message);
	        print "ERROR->".$this->is_msg_error;
		}
		else
		{
			$lb_valido=true;
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$li_row++;
				$ls_codban = $row["codban"];
				$ls_ctaban = $row["ctaban"];
				$ls_estmov = $row["estmov"];
				$ls_numdoc = $row["numdoc"];
				$ls_fecmov = $this->io_function->uf_formatovalidofecha($row["fecmov"]);
				$ls_fecmov = $this->io_function->uf_convertirfecmostrar($ls_fecmov);
				$ls_conmov = $row["conmov"];
				$ls_estcon = $row["estcon"];
				$arr_object[$li_row][1] = "<input type=checkbox name=chksel".$li_row."   id=chksel".$li_row." value=1 style=width:15px;height:15px >";		
				$arr_object[$li_row][2] = "<input type=text     name=txtnumdoc".$li_row." value='".$ls_numdoc."' class=sin-borde readonly style=text-align:center size=17 maxlength=15><input name=estcon".$li_row."  type=hidden  value=".$ls_estcon.">";
				$arr_object[$li_row][3] = "<input type=text     name=txtfecmov".$li_row." value='".$ls_fecmov."' class=sin-borde readonly style=text-align:center size=12 maxlength=10>";
				$arr_object[$li_row][4] = "<input type=text     name=txtconmov".$li_row." value='".$ls_conmov."' class=sin-borde readonly style=text-align:left size=60 maxlength=60>".
     				                      "<input type=hidden   name=txtcodban".$li_row." value='".$ls_codban."'>".
	   				                      "<input type=hidden   name=txtctaban".$li_row." value='".$ls_ctaban."'>".
										  "<input type=hidden   name=txtestmov".$li_row." value='".$ls_estmov."'>";
										  
			}
			if($li_row==0)
			{
				$li_total=1;
				for($li_row=1;$li_row<=$li_total;$li_row++)
				{
					$arr_object[$li_row][1] = "<input type=checkbox name=chksel".$li_row."   id=chksel".$li_row." value=1 style=width:15px;height:15px onClick='return false;'>";		
					$arr_object[$li_row][2] = "<input type=text     name=txtnumdoc".$li_row."       value='' class=sin-borde readonly style=text-align:center size=17 maxlength=15>";
					$arr_object[$li_row][3] = "<input type=text     name=txtfecmov".$li_row."       value='' class=sin-borde readonly style=text-align:left size=12 maxlength=10>";
					$arr_object[$li_row][4] = "<input type=text     name=txtconmov".$li_row."       value='' class=sin-borde readonly style=text-align:center size=60 maxlength=60>".
     				                          "<input type=hidden   name=txtcodban".$li_row."       value='' >".
	   				                          "<input type=hidden   name=txtctaban".$li_row."       value='' >".
										      "<input type=hidden   name=txtestmov".$li_row."       value='' >";
					
				}
				$li_row=$li_total;
			}
		    $this->io_sql->free_result($rs_data);					
		}
		$ai_total_record = $li_row;		
		return $lb_valido;
	}  // fin function 
	function uf_select_banco_contabilizar_documento( $as_operacion_bco, &$arr_object ,&$ai_total_record,$as_estatus,$as_numdoc,$ad_fecha )
	{	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	   Function :	uf_select_banco_contabilizar
		//       Access :	public
		//   Argumentos :   $as_operacion_bco->operacion movimiento de banco 
		//                  &$arr_object-> arreglo de objetos pantalla pintar 
		//                  &$ai_total_record->total de registros por valor 
		//                  $as_estatus->estatus de los movimientos a consultar
		//	    Returns :	movimiento contabilizado boolean
		//	Description :	Método que obtiene todas aquellos movimientos de banco en estatus 
		//                  para su contabilizacion
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$li_row=0;
        $lb_valido = true;
		$ls_codemp = $this->dts_empresa["codemp"];
		$ls_aux="";
		if($ad_fecha!="")
		{
			$ld_fecha=$this->io_function->uf_convertirdatetobd($ad_fecha);			
			$ls_aux=" AND fecmov='".$ld_fecha."' ";
		}
		if($as_numdoc!="")
		{
			$ls_aux=$ls_aux." AND numdoc like '%".$as_numdoc."%' ";
		}
		$ls_mysql  = " SELECT * ".
                     "   FROM scb_movbco ".
					 "  WHERE codemp='".$ls_codemp."' AND (estmov='N' OR estmov='L') ".$ls_aux;
		$rs_data=$this->io_sql->select($ls_mysql);
		if($rs_data===false)
		{   // error interno sql
			$this->is_msg_error="Error en consulta metodo ".$this->io_function->uf_convertirmsg($this->io_sql->message);
	        print "ERROR->".$this->is_msg_error;
		}
		else
		{
			$lb_valido=true;
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$li_row++;
				$ls_codban = $row["codban"];
				$ls_ctaban = $row["ctaban"];
				$ls_estmov = $row["estmov"];
				$ls_numdoc = $row["numdoc"];
				$ls_fecmov = $this->io_function->uf_formatovalidofecha($row["fecmov"]);
				$ls_fecmov = $this->io_function->uf_convertirfecmostrar($ls_fecmov);
				$ls_conmov = $row["conmov"];
				$ls_estcon = $row["estcon"];
				$arr_object[$li_row][1] = "<input type=checkbox name=chksel".$li_row."   id=chksel".$li_row." value=1 style=width:15px;height:15px >";		
				$arr_object[$li_row][2] = "<input type=text     name=txtnumdoc".$li_row." value='".$ls_numdoc."' class=sin-borde readonly style=text-align:center size=17 maxlength=15><input name=estcon".$li_row."  type=hidden  value=".$ls_estcon.">";
				$arr_object[$li_row][3] = "<input type=text     name=txtfecmov".$li_row." value='".$ls_fecmov."' class=sin-borde readonly style=text-align:center size=12 maxlength=10>";
				$arr_object[$li_row][4] = "<input type=text     name=txtconmov".$li_row." value='".$ls_conmov."' class=sin-borde readonly style=text-align:left size=60 maxlength=60>".
     				                      "<input type=hidden   name=txtcodban".$li_row." value='".$ls_codban."'>".
	   				                      "<input type=hidden   name=txtctaban".$li_row." value='".$ls_ctaban."'>".
										  "<input type=hidden   name=txtestmov".$li_row." value='".$ls_estmov."'>";
										  
			}
			if($li_row==0)
			{
				$li_total=1;
				for($li_row=1;$li_row<=$li_total;$li_row++)
				{
					$arr_object[$li_row][1] = "<input type=checkbox name=chksel".$li_row."   id=chksel".$li_row." value=1 style=width:15px;height:15px onClick='return false;'>";		
					$arr_object[$li_row][2] = "<input type=text     name=txtnumdoc".$li_row."       value='' class=sin-borde readonly style=text-align:center size=17 maxlength=15>";
					$arr_object[$li_row][3] = "<input type=text     name=txtfecmov".$li_row."       value='' class=sin-borde readonly style=text-align:left size=12 maxlength=10>";
					$arr_object[$li_row][4] = "<input type=text     name=txtconmov".$li_row."       value='' class=sin-borde readonly style=text-align:center size=60 maxlength=60>".
     				                          "<input type=hidden   name=txtcodban".$li_row."       value='' >".
	   				                          "<input type=hidden   name=txtctaban".$li_row."       value='' >".
										      "<input type=hidden   name=txtestmov".$li_row."       value='' >";
					
				}
				$li_row=$li_total;
			}
		    $this->io_sql->free_result($rs_data);					
		}
		$ai_total_record = $li_row;		
		return $lb_valido;
	}  // fin function 
	
	
	function uf_select_banco_contabilizar_documento2( $as_operacion_bco, &$arr_object ,&$ai_total_record,$as_estatus,$as_numdoc,$ad_fecha )
	{	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	   Function :	uf_select_banco_contabilizar
		//       Access :	public
		//   Argumentos :   $as_operacion_bco->operacion movimiento de banco 
		//                  &$arr_object-> arreglo de objetos pantalla pintar 
		//                  &$ai_total_record->total de registros por valor 
		//                  $as_estatus->estatus de los movimientos a consultar
		//	    Returns :	movimiento contabilizado boolean
		//	Description :	Método que obtiene todas aquellos movimientos de banco en estatus 
		//                  para su contabilizacion
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$li_row=0;
        $lb_valido = true;
		$ls_codemp = $this->dts_empresa["codemp"];
		$ls_aux="";
		if($ad_fecha!="")
		{
			$ld_fecha=$this->io_function->uf_convertirdatetobd($ad_fecha);			
			$ls_aux=" AND fecmov='".$ld_fecha."' ";
		}
		if($as_numdoc!="")
		{
			$ls_aux=$ls_aux." AND numdoc like '%".$as_numdoc."%' ";
		}
		$ls_mysql  = " SELECT * ".
                     "   FROM scb_movbco ".
					 "  WHERE codemp='".$ls_codemp."' AND estmov='N' ".$ls_aux;
		$rs_data=$this->io_sql->select($ls_mysql);
		if($rs_data===false)
		{   // error interno sql
			$this->is_msg_error="Error en consulta metodo ".$this->io_function->uf_convertirmsg($this->io_sql->message);
	        print "ERROR->".$this->is_msg_error;
		}
		else
		{
			$lb_valido=true;
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$li_row++;
				$ls_codban = $row["codban"];
				$ls_ctaban = $row["ctaban"];
				$ls_estmov = $row["estmov"];
				$ls_numdoc = $row["numdoc"];
				$ls_fecmov = $this->io_function->uf_formatovalidofecha($row["fecmov"]);
				$ls_fecmov = $this->io_function->uf_convertirfecmostrar($ls_fecmov);
				$ls_conmov = $row["conmov"];
				$ls_estcon = $row["estcon"];
				//$arr_object[$li_row][1] = "<input type=checkbox name=chksel".$li_row."   id=chksel".$li_row." value=1 style=width:15px;height:15px >";		
				$arr_object[$li_row][1] = "<input type=text     name=txtnumdoc".$li_row." value='".$ls_numdoc."' class=sin-borde readonly style=text-align:center size=17 maxlength=15><input name=estcon".$li_row."  type=hidden  value=".$ls_estcon.">";
				$arr_object[$li_row][2] = '<input type=text   onBlur="rellenar_cad(this.value,15,'."'".'doc'."'".',this)"     name=txtnuevo'.$li_row." value='' class=sin-borde style=text-align:center size=17 maxlength=15>";
				$arr_object[$li_row][3] = "<input type=text     name=txtfecmov".$li_row." value='".$ls_fecmov."' class=sin-borde readonly style=text-align:center size=12 maxlength=10>";
				$arr_object[$li_row][4] = "<input type=text     name=txtconmov".$li_row." value='".$ls_conmov."' class=sin-borde readonly style=text-align:left size=60 maxlength=60>".
     				                      "<input type=hidden   name=txtcodban".$li_row." value='".$ls_codban."'>".
	   				                      "<input type=hidden   name=txtctaban".$li_row." value='".$ls_ctaban."'>".
										  "<input type=hidden   name=txtestmov".$li_row." value='".$ls_estmov."'>";
										  
			}
			if($li_row==0)
			{
				$li_total=1;
				for($li_row=1;$li_row<=$li_total;$li_row++)
				{
					//$arr_object[$li_row][1] = "<input type=checkbox name=chksel".$li_row."   id=chksel".$li_row." value=1 style=width:15px;height:15px onClick='return false;'>";		
					$arr_object[$li_row][1] = "<input type=text     name=txtnumdoc".$li_row."       value='' class=sin-borde readonly style=text-align:center size=17 maxlength=15>";
					$arr_object[$li_row][2] = '<input type=text   onBlur="rellenar_cad(this.value,15,'."'".'doc'."'".',this)"    name=txtnuevo'.$li_row." value='' class=sin-borde style=text-align:center size=17 maxlength=15>";
					$arr_object[$li_row][3] = "<input type=text     name=txtfecmov".$li_row."       value='' class=sin-borde readonly style=text-align:left size=12 maxlength=10>";
					$arr_object[$li_row][4] = "<input type=text     name=txtconmov".$li_row."       value='' class=sin-borde readonly style=text-align:center size=60 maxlength=60>".
     				                          "<input type=hidden   name=txtcodban".$li_row."       value='' >".
	   				                          "<input type=hidden   name=txtctaban".$li_row."       value='' >".
										      "<input type=hidden   name=txtestmov".$li_row."       value='' >";
					
				}
				$li_row=$li_total;
			}
		    $this->io_sql->free_result($rs_data);					
		}
		$ai_total_record = $li_row;		
		return $lb_valido;
	}  // fin function 



function uf_select_banco_contabilizar_documento3( $as_operacion_bco, &$arr_object ,&$ai_total_record,$as_estatus,$as_numdoc,/*$ad_fecha*/$datos )
	{	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	   Function :	uf_select_banco_contabilizar
		//       Access :	public
		//   Argumentos :   $as_operacion_bco->operacion movimiento de banco 
		//                  &$arr_object-> arreglo de objetos pantalla pintar 
		//                  &$ai_total_record->total de registros por valor 
		//                  $as_estatus->estatus de los movimientos a consultar
		//	    Returns :	movimiento contabilizado boolean
		//	Description :	Método que obtiene todas aquellos movimientos de banco en estatus 
		//                  para su contabilizacion
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$li_row=0;
        $lb_valido = true;
		$ls_codemp = $this->dts_empresa["codemp"];
		$ls_aux="";
		
		
		
		/*if($ad_fecha!="")
		{
			$ld_fecha=$this->io_function->uf_convertirdatetobd($ad_fecha);			
			$ls_aux=" AND fecmov='".$ld_fecha."' ";
		}*/
		if($as_numdoc!="")
		{
			$ls_aux=$ls_aux." AND numdoc ILIKE '%".$as_numdoc."%' ";
		}
		if($datos["cedtit"]){
				$ls_aux=$ls_aux." AND titular.cedper ILIKE '%".$datos["cedtit"]."%' ";
		}
		if($datos["nomtit"]){
				$ls_aux=$ls_aux." AND (titular.nomper ILIKE '%".$datos["nomtit"]."%' OR titular.apeper ILIKE '%".$datos["nomtit"]."%') ";
		}
		if($datos["cedcau"]){
				$ls_aux=$ls_aux." AND causante.cedper ILIKE '%".$datos["cedcau"]."%' ";
		}
		if($datos["nomcau"]){
				$ls_aux=$ls_aux." AND (causante.nomper ILIKE '%".$datos["nomcau"]."%' OR causante.apeper ILIKE '%".$datos["nomcau"]."%') ";
		}
		if($datos["cedbene"]){
				$ls_aux=$ls_aux." AND b.ced_bene ILIKE '%".$datos["cedbene"]."%' ";
		}
		if($datos["nombene"]){
				$ls_aux=$ls_aux." AND ( nombene ILIKE '%".$datos["nombene"]."%' OR apebene ILIKE '%".$datos["nombene"]."%') ";
		}
		if($datos["codperi"]){
				$ls_aux=$ls_aux." AND  codperi='".$datos["codperi"]."' ";
		}
		$ls_mysql  = " SELECT mb.*, titular.cedper AS cedtit, causante.cedper AS cedcau, 
		 					  titular.nomper AS nomtit, titular.apeper AS apetit,
							  causante.nomper AS nomcau, causante.apeper AS apecau,
							  titular.tipnip AS tipniptit,causante.tipnip AS tipnipcau, 
							  nombene,apebene,titular.nomaut,titular.cedaut,titular.tipautor 							    		   			  
					   FROM scb_movbco mb
					   LEFT JOIN rpc_beneficiario b ON b.ced_bene = mb.ced_bene
					   LEFT JOIN sno_personal titular ON titular.codper = mb.codper
					   LEFT JOIN sno_personal causante ON causante.cedper = titular.cedmil
					   LEFT JOIN sno_tipo_pensionado tpt ON tpt.tippensionado = titular.tippensionado
					   LEFT JOIN sno_tipo_pensionado tpc ON tpc.tippensionado = causante.tippensionado					   
					   WHERE b.codemp='".$ls_codemp."' AND mb.estmov='N' ".$ls_aux." ORDER BY lpad(titular.cedper, 10,'0')";
		$rs_data=$this->io_sql->select($ls_mysql);
		if($rs_data===false)
		{   // error interno sql
			$this->is_msg_error="Error en consulta metodo ".$this->io_function->uf_convertirmsg($this->io_sql->message);
	        print "ERROR->".$this->is_msg_error;
		}
		else
		{
			$lb_valido=true;
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$li_row++;
				$ls_codban = $row["codban"];
				$ls_ctaban = $row["ctaban"];
				$ls_estmov = $row["estmov"];
				$ls_numdoc = $row["numdoc"];
				$ls_fecmov = $this->io_function->uf_formatovalidofecha($row["fecmov"]);
				$ls_fecmov = $this->io_function->uf_convertirfecmostrar($ls_fecmov);
				$ls_conmov = $row["conmov"];
				$ls_estcon = $row["estcon"];
				$ls_chevau = $row["chevau"];
				$ls_codope = $row["codope"];
				$nombre_tit = $row["nomtit"].' '.$row["apetit"];
				$nombre_cau = $row["nomcau"].' '.$row["apecau"];
				//$datos_persona = '<b>TITULAR:</b> '.$datos_tit.'<br><b>CAUSANTE:</b> '.$datos_tit; -------> No se sabe quien es $datos_tit!!
				$nombene = $row["nombene"].' '.$row["apebene"];
				$cedbene = $row["ced_bene"];
				if($row["cedaut"]){$nombene = $row["nomaut"]; $cedbene=$row["tipautor"].$row["cedaut"];}
				$estatus_imp = '';
				if($row["estimpche"]){$estatus_imp = '&nbsp;&nbsp;<b>** Impreso **</b>';}
				
				//$arr_object[$li_row][1] = "<input type=checkbox name=chksel".$li_row."   id=chksel".$li_row." value=1 style=width:15px;height:15px >";		
				$arr_object[$li_row][1] = "<input type=text     name=txtnumdoc".$li_row." value='".$ls_numdoc."' class=sin-borde readonly style=text-align:center size=17 maxlength=15><input name=estcon".$li_row."  type=hidden  value=".$ls_estcon.">";
				$arr_object[$li_row][2] = '<input type=text   onBlur="rellenar_cad(this.value,15,'."'".'doc'."'".',this)"     name=txtnuevo'.$li_row." value='' class=sin-borde style=text-align:center size=17 maxlength=15>";
				$arr_object[$li_row][3] = "<input type=text     name=txttit".$li_row." value='".$row["cedtit"]."' class=sin-borde readonly style=text-align:center size=10 maxlength=10>";
				$arr_object[$li_row][4] = "<input type=text     name=txtnomtit".$li_row." value='".$nombre_tit."' class=sin-borde readonly style=text-align:center size=25 >";				
				$arr_object[$li_row][5] = "<input type=text     name=txtcau".$li_row." value='".$row["cedcau"]."' class=sin-borde readonly style=text-align:center size=10 maxlength=10>";
				$arr_object[$li_row][6] = "<input type=text     name=txtnomcau".$li_row." value='".$nombre_cau."' class=sin-borde readonly style=text-align:center size=25 >";
				$arr_object[$li_row][7] = "<input type=text     name=txtmonto".$li_row." ".' style="background-color:#FFFFFF; font-weight:bold; color:#000066; text-align:right;" '."value='".number_format($row["monto"],2,',','.')."' class=sin-borde readonly style=text-align:right size=12>";
				$arr_object[$li_row][8] = "<input type=text     name=txtfecmov".$li_row." value='".$ls_fecmov."' class=sin-borde readonly style=text-align:center size=12 maxlength=10>";
				$arr_object[$li_row][9] = '<input type=text name=txtiforma class=sin-borde style="background-color:#BFBFFF; font-weight:bold; color:#000066;" value="***** DATOS DEL BENEFICIARIO *****" size=35><div style="width:250px text-align:left;"><b>Cédula:</b>'.$cedbene.'<br><b>Nombre:</b> '.$nombene.' <br><b>Concepto:</b> '.$ls_conmov.'</div>'.
     				                      'Imprimir Cheque: <a href="javascript:ue_imprimir('."'$ls_codban','$ls_ctaban','$ls_numdoc','$ls_chevau','$ls_codope'".');"><img src="../shared/imagebank/tools20/imprimir.gif" alt="Imprimir" title="Imprimir" width="20" height="20" border="0"></a>'.$estatus_imp.'
										   <br>Imprimir Carta: <a href="javascript:ue_imprimir_carta('."'$ls_codban','$ls_ctaban','$ls_numdoc','$ls_chevau','$ls_codope'".');"><img src="../shared/imagebank/tools20/imprimir.gif" alt="Imprimir" title="Imprimir" width="20" height="20" border="0"></a>'.
										  
										  "<input type=hidden   name=txtcodban".$li_row." value='".$ls_codban."'>".
	   				                      "<input type=hidden   name=txtctaban".$li_row." value='".$ls_ctaban."'>".
										  "<input type=hidden   name=txtestmov".$li_row." value='".$ls_estmov."'>";
										  
			}
			if($li_row==0)
			{
				$li_total=1;
				for($li_row=1;$li_row<=$li_total;$li_row++)
				{
					//$arr_object[$li_row][1] = "<input type=checkbox name=chksel".$li_row."   id=chksel".$li_row." value=1 style=width:15px;height:15px onClick='return false;'>";		
					$arr_object[$li_row][1] = "<input type=text     name=txtnumdoc".$li_row."       value='' class=sin-borde readonly style=text-align:center size=17 maxlength=15>";
					$arr_object[$li_row][2] = '<input type=text   onBlur="rellenar_cad(this.value,15,'."'".'doc'."'".',this)"    name=txtnuevo'.$li_row." value='' class=sin-borde style=text-align:center size=17 maxlength=15>";
					$arr_object[$li_row][3] = "<input type=text     name=txttit".$li_row." value='' class=sin-borde readonly style=text-align:center size=12 maxlength=10>";
					$arr_object[$li_row][4] = "<input type=text     name=txtnomtit".$li_row." value='' class=sin-borde readonly style=text-align:center size=10 >";
					$arr_object[$li_row][5] = "<input type=text     name=txtcau".$li_row." value='' class=sin-borde readonly style=text-align:center size=12 maxlength=10>";
					$arr_object[$li_row][6] = "<input type=text     name=txtnomcau".$li_row." value='' class=sin-borde readonly style=text-align:center size=10>";
					$arr_object[$li_row][7] = "<input type=text     name=txtmonto".$li_row." value='' class=sin-borde readonly style=text-align:center size=12>";
					$arr_object[$li_row][8] = "<input type=text     name=txtfecmov".$li_row."       value='' class=sin-borde readonly style=text-align:left size=12 maxlength=10>";
					$arr_object[$li_row][9] = "<input type=text     name=txtinf".$li_row." value='' class=sin-borde readonly style=text-align:left size=25 >".
     				                          "<input type=hidden   name=txtcodban".$li_row."       value='' >".
	   				                          "<input type=hidden   name=txtctaban".$li_row."       value='' >".
										      "<input type=hidden   name=txtestmov".$li_row."       value='' >";
					
				}
				$li_row=$li_total;
			}
		    $this->io_sql->free_result($rs_data);					
		}
		$ai_total_record = $li_row;		
		return $lb_valido;
	}  // fin function 
	
} // end class
?>
