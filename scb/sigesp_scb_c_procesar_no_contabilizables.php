<?php
class sigesp_scb_c_procesar_no_contabilizables{

	function sigesp_scb_c_procesar_no_contabilizables()
	{
	  require_once("sigesp_scb_c_movbanco.php");
	  require_once("../shared/class_folder/class_sql.php");
	  require_once("../shared/class_folder/class_mensajes.php");
	  require_once("../shared/class_folder/sigesp_include.php");
	  require_once("../shared/class_folder/class_funciones.php");
	  require_once("../shared/class_folder/sigesp_c_seguridad.php");
	  	  	  
	  $io_include   	  = new sigesp_include();
	  $ls_conect    	  = $io_include->uf_conectar();
	  $this->io_sql 	  = new class_sql($ls_conect);
	  $this->io_msg 	  = new class_mensajes();
	  $this->io_function  = new class_funciones();
	  $this->io_seguridad = new sigesp_c_seguridad();
	  $this->io_movbco    = new sigesp_scb_c_movbanco($this->io_seguridad);
	}

function uf_cargar_documentos($as_codemp,$as_numdoc,$as_codban,$as_ctaban,$as_codope,$ad_fecmov,&$object,&$li_totrows,$as_orden,$as_accion)
{
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//	      Function: uf_cargar_documentos
//          Access: private
//       Arguments: $as_codemp  = Código de la Empresa.
//                  $as_numdoc  = Número del Documento Bancario.
//                  $as_codban  = Código del Banco asociado al documento. 
//                  $as_ctaban  = Cuenta Bancaria usada.
//                  $as_codope  = Código de la Operación CH=Cheque.
//                  $ad_fecmov  = Fecha en la que se realizó el movimiento bancario.
//                  $object     = Arreglo cargado con la información resultante del select.
//                  $li_totrows = Número total de filas encontradas por el select.
//                  $as_orden   = Parámetro que indicará a través de que campo se realizará el ordenamiento.
//					$as_accion  = Tipo de acción a realizar P = Procesar Documento y R =  Reverso de Procesamiento del documento.
//	   Description: Función que se encarga de obtener todos los documentos L= no contabilizables para ser llevados posteriormente
//                  al estatus P = procesado o viceversa, para que puedan o no ser producto de modificaciones. En estatus L el 
//                  movimiento bancario pueder ser modificado en estatus P no.
//	         Autor: Ing. Néstor Falcón.
//  Fecha Creación: 09/04/2008.     				Fecha Última Modificación: 09/04/2008.
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

  $lb_valido  = true;
  $ls_sqlaux  = "";
  $li_totrows = $li_i = 0;
  if ($as_codope=='-')
     {
	   $as_codope='';
	 }
  if (!empty($ad_fecmov))
     {
	   $ld_fecmov = $this->io_function->uf_convertirdatetobd($ad_fecmov);
	   $ls_sqlaux = " AND scb_movbco.fecmov = '".$ld_fecmov."'";
	 }
  if ($as_accion=='P')
     {
	   $ls_estmov = 'L';
	 }
  elseif($as_accion=='R')
     {
	   $ls_estmov = 'P';
	 }
  $ls_sql     = "SELECT scb_movbco.codemp,scb_movbco.numdoc,scb_movbco.codban,scb_banco.nomban,scb_movbco.ctaban,
  						scb_ctabanco.dencta,scb_movbco.codope,scb_movbco.fecmov,scb_movbco.nomproben,scb_movbco.tipo_destino,
						scb_movbco.cod_pro,scb_movbco.ced_bene, scb_movbco.conmov,scb_movbco.monto 
  				   FROM scb_movbco, scb_banco, scb_ctabanco
				  WHERE scb_movbco.codemp = '".$as_codemp."'
				    AND scb_movbco.numdoc like '%".$as_numdoc."%'
				    AND scb_movbco.codban like '%".$as_codban."%'
					AND scb_movbco.ctaban like '%".$as_ctaban."%'
					AND scb_movbco.codope like '%".$as_codope."%'
					AND scb_movbco.estmov = '".$ls_estmov."'  
					AND scb_movbco.estbpd = 'D'
					AND scb_movbco.procede='SCBBCH' $ls_sqlaux
					AND scb_movbco.codemp=scb_banco.codemp
					AND scb_movbco.codban=scb_banco.codban
					AND scb_movbco.codemp=scb_ctabanco.codemp
					AND scb_movbco.codban=scb_ctabanco.codban
					AND scb_movbco.ctaban=scb_ctabanco.ctaban
					AND scb_ctabanco.codemp=scb_banco.codemp
					AND scb_ctabanco.codban=scb_banco.codban 
			  	  ORDER BY $as_orden";
  $rs_data = $this->io_sql->select($ls_sql);
  if ($rs_data===false)
     {
	   $lb_valido = false;
	   $this->io_msg->message("ERROR. CLASE->sigesp_scb_c_procesar_no_contabilizables.php;Función->uf_cargar_documentos();".$this->io_function->uf_convertirmsg($this->io_sql->message));
	   echo $this->io_sql->message;
	 }  
  else
     {
       $li_totrows = $this->io_sql->num_rows($rs_data);
	   while ($row=$this->io_sql->fetch_row($rs_data))
	         {
		       $li_i++;
			   $ls_numdoc 	 = $row["numdoc"];
			   $ls_codban 	 = $row["codban"];
			   $ls_denban 	 = $row["nomban"];
			   $ls_ctaban    = $row["ctaban"];
			   $ls_denctaban = $row["dencta"];
			   $ls_conmov    = $row["conmov"];
			   $ld_monmov    = number_format($row["monto"],2,',','.');
			   $ls_tipproben = $row["tipo_destino"];
			   $ls_nomproben = $row["nomproben"];
			   $ls_codpro    = $row["cod_pro"];
			   $ls_cedben    = $row["ced_bene"];
		       if ($ls_tipproben=='P')
			      {
				    $ls_codproben = $ls_codpro;
				  }
			   elseif($ls_tipproben=='B')
			      {
				    $ls_codproben = $ls_cedben;
				  }
			   $ld_fecmov = $this->io_function->uf_convertirfecmostrar($row["fecmov"]);
			   $ls_codope = $row["codope"];
			   if ($ls_codope=='CH')
			      {
				    $ls_denope = 'CHEQUE';
				  } 
			   $ls_title = $ls_denope.'-'.$ls_denban.'-Cuenta:'.$ls_ctaban.'-'.$ls_denctaban;
			   $object[$li_i][1] = "<input name=chk".$li_i." 	      type=checkbox id=chk".$li_i."          value=1  class=sin-borde  onClick=javascript:uf_select_documentos($li_i);>";
			   $object[$li_i][2] = "<input name=txtnumdoc".$li_i."    type=text     id=txtnumdoc".$li_i."    value='".$ls_numdoc."'    class=sin-borde style=text-align:center size=15 maxlength=15  readonly title='".$ls_title."'><input name=hidcodban".$li_i." type=hidden id=hidcodban".$li_i." value='".$ls_codban."'>";
			   $object[$li_i][3] = "<input name=txtcodproben".$li_i." type=text     id=txtcodproben".$li_i." value='".$ls_nomproben."' class=sin-borde style=text-align:left   size=33 maxlength=254 readonly title='".$ls_codproben.' - '.$ls_nomproben."'><input name=hidctaban".$li_i." type=hidden id=hidctaban".$li_i." value='".$ls_ctaban."'>";
		       $object[$li_i][4] = "<input name=txtfecmov".$li_i."    type=text     id=txtfecmov".$li_i."    value='".$ld_fecmov."'    class=sin-borde style=text-align:center size=8  maxlength=254 readonly><input name=hidcodope".$li_i." type=hidden id=hidcodope".$li_i." value='".$ls_codope."'>";
		       $object[$li_i][5] = "<input name=txtconmov".$li_i."    type=text     id=txtconmov".$li_i."    value='".$ls_conmov."'    class=sin-borde style=text-align:left   size=45 maxlength=254 readonly title='".$ls_conmov."'>";
		       $object[$li_i][6] = "<input name=txtmonmov".$li_i."    type=text     id=txtfecmov".$li_i."    value='".$ld_monmov."'    class=sin-borde style=text-align:right  size=8  maxlength=254 readonly>";
			 }
	      if ($li_totrows==0)
		     {
			   $li_totrows = 1;
			   $object[$li_totrows][1] = "<input name=chk".$li_totrows." 	      type=checkbox id=chk".$li_totrows."          value=1  class=sin-borde>";
			   $object[$li_totrows][2] = "<input name=txtnumdoc".$li_totrows."    type=text     id=txtnumdoc".$li_totrows."    value='' class=sin-borde  style=text-align:center size=15  maxlength=15  readonly><input name=hidcodban".$li_totrows." type=hidden id=hidcodban".$li_totrows." value=''>";
		       $object[$li_totrows][3] = "<input name=txtcodproben".$li_totrows." type=text     id=txtcodproben".$li_totrows." value='' class=sin-borde  style=text-align:left   size=33  maxlength=254 readonly><input name=hidctaban".$li_totrows." type=hidden id=hidctaban".$li_totrows." value=''>";
			   $object[$li_totrows][4] = "<input name=txtfecmov".$li_totrows."    type=text     id=txtfecmov".$li_totrows."    value='' class=sin-borde  style=text-align:center size=8   maxlength=254 readonly><input name=hidcodope".$li_totrows." type=hidden id=hidcodope".$li_totrows." value=''>";
		       $object[$li_totrows][5] = "<input name=txtconmov".$li_totrows."    type=text     id=txtconmov".$li_totrows."    value='' class=sin-borde  style=text-align:left   size=45  maxlength=254 readonly>";
		       $object[$li_totrows][6] = "<input name=txtmonmov".$li_totrows."    type=text     id=txtmonmov".$li_totrows."    value='' class=sin-borde  style=text-align:right  size=8   maxlength=254 readonly>";
			 }
	 }
}//function uf_cargar_documentos().

function uf_procesar_documentos($as_codemp,$as_numdoc,$as_codban,$as_ctaban,$as_codope,$as_accion,$aa_seguridad)
{
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//	      Function: uf_procesar_documentos
//          Access: private
//       Arguments: $as_codemp  = Código de la Empresa.
//                  $as_numdoc  = Número del Documento Bancario.
//                  $as_codban  = Código del Banco asociado al documento. 
//                  $as_ctaban  = Cuenta Bancaria usada.
//                  $as_codope  = Código de la Operación (CH=Cheque,ND=Nota de Débito,NC=Nota de Crédito,DP=Depósito,RE=Retiro).
//					$as_accion  = Tipo de acción a realizar P = Procesar Documento y R =  Reverso de Procesamiento del documento.
//	   Description: Función que se encarga de actualizar el estatus del documento bancario a P=Procesado o devolverlo al estatus
//					L = No contabilizable, para que pueda o no ser modificado.
//	         Autor: Ing. Néstor Falcón.
//  Fecha Creación: 09/04/2008.     				Fecha Última Modificación: 09/04/2008.
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

  $lb_valido = true;
  if ($as_accion=='P')
     {
	   $ls_nueest = 'P';//Estatus a ser asignado.
	   $ls_estmov = 'L';//Estatus en que debe encontrarse el movimiento bancario en scb_movbco.
	 }
  elseif($as_accion=='R')
     {
	   $ls_nueest = 'L';
	   $ls_estmov = 'P';
	 }
  $this->io_sql->begin_transaction();
  $lb_valido = $this->uf_duplicar_movimiento_bancario($as_codemp,$as_numdoc,$as_codban,$as_ctaban,$as_codope,$ls_estmov,$ls_nueest);
  if ($lb_valido)
     {
	   $lb_valido = $this->uf_duplicar_fuente_financiamiento($as_codemp,$as_numdoc,$as_codban,$as_ctaban,$as_codope,$ls_estmov,$ls_nueest);
	 }
  if ($lb_valido)
     {
	   $this->uf_delete_all_movimientos($as_codemp,$as_numdoc,$as_codban,$as_ctaban,$as_codope,$ls_estmov);
	 }
  return $lb_valido;
}//function uf_procesar_documentos().

function uf_duplicar_movimiento_bancario($as_codemp,$as_numdoc,$as_codban,$as_ctaban,$as_codope,$as_estmov,$as_nueest)
{
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//	      Function: uf_duplicar_movimiento_bancario
//          Access: private
//       Arguments: $as_codemp  = Código de la Empresa.
//                  $as_numdoc  = Número del Documento Bancario.
//                  $as_codban  = Código del Banco asociado al documento. 
//                  $as_ctaban  = Cuenta Bancaria usada.
//                  $as_codope  = Código de la Operación (CH=Cheque,ND=Nota de Débito,NC=Nota de Crédito,DP=Depósito,RE=Retiro).
//					$as_estmov  = Estatus en el cual se encuentra el movimiento bancario.
//					$as_nueest  = Estatus al cual será actualizado el nuevo movimiento.
//	   Description: Función que se encarga de realizar una copia exacta del Movimiento Bancario pero con la variante del estatus
//                  del mismo, actualizando a P si la accion es procesar o a L si la accion es Reverso de Procesamiento.
//	         Autor: Ing. Néstor Falcón.
//  Fecha Creación: 21/04/2008.     				Fecha Última Modificación: 21/04/2008.
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

  $lb_valido = true;
  $ls_sql = "INSERT INTO scb_movbco (codemp,codusu,codban,ctaban,numdoc,codope,fecmov,conmov,codconmov,cod_pro,ced_bene,nomproben,
                                     monto,monobjret,monret,chevau,estmov,estmovint,estcobing,esttra,estbpd,estcon,feccon,
									 estreglib,tipo_destino,fecha,procede,codfuefin)
     						 (SELECT codemp,codusu,codban,ctaban,numdoc,codope,fecmov,conmov,codconmov,cod_pro,ced_bene,nomproben,
							         monto,monobjret,monret,chevau,'".$as_nueest."',estmovint,estcobing,esttra,estbpd,estcon,feccon,estreglib,
									 tipo_destino,fecha,procede,codfuefin
							    FROM scb_movbco 
							   WHERE codemp='".$as_codemp."'
								 AND numdoc='".$as_numdoc."'
								 AND codban='".$as_codban."'
								 AND ctaban='".$as_ctaban."'
						         AND codope='".$as_codope."'
					 	         AND estmov='".$as_estmov."')";
  $rs_data = $this->io_sql->execute($ls_sql);
  if ($rs_data===false)
     {
	   $lb_valido = false;
	   $this->io_msg->message("ERROR. CLASS->sigesp_scb_c_procesar_no_contabilizables.php;Función->uf_duplicar_movimiento_bancario();".$this->io_function->uf_convertirmsg($this->io_sql->message));
	   echo $this->io_sql->message;
	 }
  return $lb_valido;
}//function uf_duplicar_movimiento_bancario().

function uf_duplicar_fuente_financiamiento($as_codemp,$as_numdoc,$as_codban,$as_ctaban,$as_codope,$as_estmov,$as_nueest)
{
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//	      Function: uf_duplicar_fuente_financiamiento
//          Access: private
//       Arguments: $as_codemp  = Código de la Empresa.
//                  $as_numdoc  = Número del Documento Bancario.
//                  $as_codban  = Código del Banco asociado al documento. 
//                  $as_ctaban  = Cuenta Bancaria usada.
//                  $as_codope  = Código de la Operación (CH=Cheque,ND=Nota de Débito,NC=Nota de Crédito,DP=Depósito,RE=Retiro).
//					$as_estmov  = Estatus en el cual se encuentra el movimiento bancario.
//					$as_nueest  = Estatus al cual será actualizado el nuevo movimiento.
//	   Description: Función que se encarga de realizar una copia exacta del Movimiento Bancario pero con la variante del estatus
//                  del mismo, actualizando a P si la accion es procesar o a L si la accion es Reverso de Procesamiento.
//	         Autor: Ing. Néstor Falcón.
//  Fecha Creación: 21/04/2008.     				Fecha Última Modificación: 21/04/2008.
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

  $lb_valido = true;
  $ls_sql = "INSERT INTO scb_movbco_fuefinanciamiento (codemp, codban, ctaban, numdoc, codope, estmov, codfuefin)
     						     (SELECT codemp, codban, ctaban, numdoc, codope, '".$as_nueest."', codfuefin
							        FROM scb_movbco_fuefinanciamiento 
							       WHERE codemp='".$as_codemp."'
								     AND numdoc='".$as_numdoc."'
								     AND codban='".$as_codban."'
								     AND ctaban='".$as_ctaban."'
						             AND codope='".$as_codope."'
					 	             AND estmov='".$as_estmov."')";
  $rs_data = $this->io_sql->execute($ls_sql);
  if ($rs_data===false)
     {
	   $lb_valido = false;
	   $this->io_msg->message("ERROR. CLASS->sigesp_scb_c_procesar_no_contabilizables.php;Función->uf_duplicar_fuente_financiamiento();".$this->io_function->uf_convertirmsg($this->io_sql->message));
	   echo $this->io_sql->message;
	 }
  return $lb_valido;
}//function uf_duplicar_fuente_financiamiento().

function uf_delete_all_movimientos($as_codemp,$as_numdoc,$as_codban,$as_ctaban,$as_codope,$as_estmov)
{
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//	      Function: uf_delete_all_movimientos
//          Access: private
//       Arguments: $as_codemp  = Código de la Empresa.
//                  $as_numdoc  = Número del Documento Bancario.
//                  $as_codban  = Código del Banco asociado al documento. 
//                  $as_ctaban  = Cuenta Bancaria usada.
//                  $as_codope  = Código de la Operación (CH=Cheque,ND=Nota de Débito,NC=Nota de Crédito,DP=Depósito,RE=Retiro).
//					$as_estmov  = Estatus en el cual se encuentra el movimiento bancario.
//	   Description: Función que se encarga de eliminar el movimiento original para solo dejar la nueva copia en el estatus requerido.
//	         Autor: Ing. Néstor Falcón.
//  Fecha Creación: 22/04/2008.     				Fecha Última Modificación: 22/04/2008.
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

  $lb_valido = true;
  $ls_sql = "DELETE FROM scb_movbco_fuefinanciamiento 
              WHERE codemp='".$as_codemp."'
			    AND numdoc='".$as_numdoc."'
				AND codban='".$as_codban."'
				AND ctaban='".$as_ctaban."'
				AND codope='".$as_codope."'
				AND estmov='".$as_estmov."'";
  $rs_data = $this->io_sql->execute($ls_sql);
  if ($rs_data===false)
     {
	   $lb_valido = false;
	   $this->io_msg->message("ERROR. CLASS->sigesp_scb_c_procesar_no_contabilizables.php;Función->uf_delete_all_movimientos();".$this->io_function->uf_convertirmsg($this->io_sql->message));
	   echo $this->io_sql->message;
	 }
  else
     {
	   $ls_sql = "DELETE FROM scb_movbco
				   WHERE codemp='".$as_codemp."'
					 AND numdoc='".$as_numdoc."'
					 AND codban='".$as_codban."'
					 AND ctaban='".$as_ctaban."'
					 AND codope='".$as_codope."'
					 AND estmov='".$as_estmov."'";
	 
	   $rs_data = $this->io_sql->execute($ls_sql);
	   if ($rs_data===false)
		  {
		    $lb_valido = false;
		    $this->io_msg->message("ERROR. CLASS->sigesp_scb_c_procesar_no_contabilizables.php;Función->uf_delete_all_movimientos();".$this->io_function->uf_convertirmsg($this->io_sql->message));
		    echo $this->io_sql->message;
		  }
	 }
  return $lb_valido;
}//function uf_delete_all_movimientos().
}
?>