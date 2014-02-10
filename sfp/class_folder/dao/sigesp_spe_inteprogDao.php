<?php
session_start();
require_once('../class_folder/dao/sigesp_sfp_con_estplandao.php');
require_once("../class_folder/sigesp_conexion_dao.php");
require_once("sigesp_sfp_intefuenteDao.php");
require_once("sigesp_sfp_inteAdDao.php");
require_once("sigesp_sfp_intGastosDao.php");
require_once("sigesp_sfp_intProbDao.php");
require_once("sigesp_sfp_intMetaDao.php");
require_once('sigesp_spe_asientosDao.php');
require_once('../class_folder/dao/sigesp_spe_asientosDao.php');
require_once('../class_folder/dao/sigesp_sfp_plan_unico_reDao.php');
require_once('sigesp_sfp_plan_unico_reDao.php');
class IntegracionPre extends ADODB_Active_Record
{
	var $_table='spe_relacion_es';
	var $Probs=array();
	var $Ads = array();
	var $Ubs = array();
	var $Gastos= array();
	var $Metas =array();
	var $Indis =array();
	//var $o
	
	public function completarEstructurasPresupuestariasNivel4()
	{
		global $db;
		$lb_valido = false;
		$db->StartTrans();
		$ls_cadena_concat="";
		switch(strtoupper($_SESSION["ls_gestor"]))
		{
		 case "POSTGRES" : $ls_cadena_concat="codemp||ano_presupuesto||codestpro1||codestpro2||codestpro3||estcla";
		       break;
			   
		 case "MYSQLT"   : $ls_cadena_concat="CONCAT(codemp,ano_presupuesto,codestpro1,codestpro2,codestpro3,estcla)";
		       break;
		}	
		$ls_sql = "INSERT INTO sfp_estpro4 (
					codemp, ano_presupuesto, codestpro1, codestpro2, codestpro3, 
					codestpro4, estcla, denestpro4) SELECT codemp,ano_presupuesto, codestpro1,codestpro2,codestpro3,'0000000000000000000000000' as codestpro4, estcla, 'NINGUNA' as denestpro4 FROM sfp_estpro3 ".
				  "	WHERE ".$ls_cadena_concat." ".
				  "NOT IN (SELECT ".$ls_cadena_concat." FROM sfp_estpro4)";
		$db->Execute($ls_sql);
		if($db->CompleteTrans())
		{
		  $lb_valido = true;
		}	 
		return $lb_valido;
	}
	
	public function completarEstructurasPresupuestariasNivel5()
	{
		global $db;
		$lb_valido = false;
		$db->StartTrans();
		$ls_cadena_concat="";
		switch(strtoupper($_SESSION["ls_gestor"]))
		{
		 case "POSTGRES" : $ls_cadena_concat="codemp||ano_presupuesto||codestpro1||codestpro2||codestpro3||codestpro4||estcla";
		       break;
			   
		 case "MYSQLT"   : $ls_cadena_concat="CONCAT(codemp,ano_presupuesto,codestpro1,codestpro2,codestpro3,codestpro4,estcla)";
		       break;
		}	
		$ls_sql = " INSERT INTO sfp_estpro5 (
					codemp, ano_presupuesto, codestpro1, codestpro2, codestpro3, 
					codestpro4, codestpro5, estcla, denestpro5) SELECT codemp,ano_presupuesto,codestpro1,codestpro2,codestpro3,codestpro4,'0000000000000000000000000' as codestpro5, estcla, 'NINGUNA' as denestpro5 FROM sfp_estpro4 ".
				  " WHERE ".$ls_cadena_concat." ".
				  " NOT IN (SELECT ".$ls_cadena_concat." FROM sfp_estpro5);";
		$db->Execute($ls_sql);
		if($db->CompleteTrans())
		{
		  $lb_valido = true;
		}	 
		return $lb_valido;
	}
	
	public function completarEstructurasPlanNivel5()
	{
		global $db;
		$lb_valido = false;
		$db->StartTrans();
		switch(strtoupper($_SESSION["ls_gestor"]))
		{
		 case "POSTGRES" : $ls_cadena_concat="codemp||ano_presupuesto||codest1||codest2||codest3||codest4";
		       break;
			   
		 case "MYSQLT"   : $ls_cadena_concat="CONCAT(codemp,ano_presupuesto,codest1,codest2,codest3,codest4)";
		       break;
		}	
		$ls_sql = " INSERT INTO spe_estpro5 (
					codemp, ano_presupuesto, codest1, codest2, codest3, codest4, 
					codest5, estcla_p, denest5) SELECT codemp, ano_presupuesto, codest1,codest2,codest3,codest4,'0000000000000000000000000' as codest5, estcla_p, 'NINGUNA' as denest5 FROM spe_estpro4 ".
				  "	WHERE ".$ls_cadena_concat." ".
				  " NOT IN (SELECT ".$ls_cadena_concat." FROM spe_estpro5)";
		$db->Execute($ls_sql);
		if($db->CompleteTrans())
		{
		  $lb_valido = true;
		}	 
		return $lb_valido;
	}
	
	
						
	public function LeerUno()
	{
		global $db;
             //   $db->debug=1;
		$this->crearcondicion(&$conSelect,&$conFrom,&$conWhere);	
		$sql = "select distinct i.codinte{$conSelect} from spe_relacion_es as i {$conFrom} where i.codemp='{$this->codemp}' and i.ano_presupuesto='{$this->ano_presupuesto}' {$conWhere} ";
		//die();
		$Rs = $db->Execute($sql);	 
		return $Rs;
	}
	public function LeerTodos()
	{
		global $db;
        //$db->debug=true;
		$this->crearcondicionsinfiltro(&$conSelect,&$conFrom,&$conWhere);	
		$sql = "select distinct i.codinte{$conSelect} from spe_relacion_es as i {$conFrom} where i.codemp='{$this->codemp}' and i.ano_presupuesto='{$this->ano_presupuesto}'  {$conWhere}";
		//echo $sql;
		//die();
		
		$Rs = $db->Execute($sql);	 
		return $Rs;
	}
	public function LeerTodosCat()
	{
		global $db;
		$this->crearcondicionsinfiltrocat(&$conSelect,&$conFrom,&$conWhere);	
		$sql = "select i.codinte{$conSelect} from spe_relacion_es as i {$conFrom} where i.codemp='{$this->codemp}' and i.ano_presupuesto='{$this->ano_presupuesto}'  {$conWhere}";		
		$Rs = $db->Execute($sql);	 
		return $Rs;
	}
	
	public function LeerPlanIntegrados()
	{
		global $db;
		$db->debug=true;
		$this->crearcondicionplansinfiltro(&$conSelect,&$conFrom,&$conWhere);	
		$sql = "select distinct {$conSelect} from spe_relacion_es as i {$conFrom} where i.codemp='{$this->codemp}' and i.ano_presupuesto = '{$this->ano_presupuesto}' {$conWhere}";		
		$Rs = $db->Execute($sql);	 
		return $Rs;
	}
	
	public function LeerTodosCatNivel1()
	{
		global $db;
		//$db->debug=true;
		$oNivel=new ConfNivelDao();
		$oNivel->tipo="PR";
		$oNivel->nivel="1";
		$nomultimo = $oNivel->LeerNombreUltnivel();
		$tama = $oNivel->LeerNumCar();
		$pos=(25-$tama)+1;
		$conSelect.="e1.codestpro1,substr(e1.codestpro1,{$pos},{$tama}) as codigo,e1.estcla,e1.denestpro1 as descripcion";			
		$conFrom.=" sfp_estpro1 as e1";
	//	$conWhere.="and e1.codestpro1=i.codestpro1 ";
		$sql = "select {$conSelect} from {$conFrom} where e1.codemp='{$this->codemp}' and e1.ano_presupuesto='{$this->ano_presupuesto}'  {$conWhere}";		
		$Rs = $db->Execute($sql);	 
		return $Rs;
	}
	
	
	public function LeerTodosReporte()
	{
		global $db;
        //  $db->debug=1;
		$this->crearcondicionsinfiltrocat(&$conSelect,&$conFrom,&$conWhere);	
		$sql = "select i.codinte{$conSelect}, enero+febrero+marzo+abril+mayo+junio+julio+agosto+septiembre+octubre+noviembre+diciembre as monto,
				spe_int_cuentas.sig_cuenta,sigesp_sfp_plancuentas.denominacion from spe_relacion_es as i {$conFrom},spe_int_cuentas,sigesp_sfp_plancuentas where i.codemp='{$this->codemp}'  {$conWhere} and 
				i.codinte=spe_int_cuentas.codinte and spe_int_cuentas.sig_cuenta=sigesp_sfp_plancuentas.sig_cuenta";		
		
		$Rs = $db->Execute($sql);	 
		return $Rs;
	}
	
	public function LeerUnoPlan()
	{
		global $db;
		$this->crearcondicionplan(&$conSelect,&$conFrom,&$conWhere);	
		$sql = "select distinct i.codinte{$conSelect} from spe_relacion_es as i {$conFrom} where i.codemp='{$this->codemp}'  and i.ano_presupuesto = '{$this->ano_presupuesto}' and i.codinte = {$this->codinte} {$conWhere}";
		//ver($sql);
		$Rs = $db->Execute($sql);	 
		return $Rs;
	}
	
	public function Eliminar()
	{
		if($this->delete())
			return true;
		else
		{
			return false;
		}		
	}
	
	
	public function existeFuenteCuentas()
	{
		global $db;
		$sql = "select fuentecuentas from spe_relacion_es where fuentecuentas='1' and codemp='{$this->codemp}' and ano_presupuesto={$this->ano_presupuesto}";
		$Rs = $db->Execute($sql);	 
		if($Rs->RecordCount()>0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	public function marcarFuenteCuenta()
	{
		global $db;
		$db->StartTrans();
		$sql = "update spe_relacion_es set fuentecuentas='1' 
				where codinte='{$this->codinte}' and codemp='{$this->codemp}' 
				and ano_presupuesto='{$this->ano_presupuesto}'";
		//echo $sql;
		//die();
		$db->Execute($sql);	 
		if($db->CompleteTrans())
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	
	public function obtenerUltimoCodigo()
	{ 
		global $db;
		$sql="select codinte as Ultimo from {$this->_table} where codest1='{$this->codest1}' and codest2='{$this->codest2}' and codest3='{$this->codest3}' and codest4='{$this->codest4}' and codest5='{$this->codest5}' and codestpro1='{$this->codestpro1}' and codestpro2='{$this->codestpro2}' and codestpro3='{$this->codestpro3}' and codestpro4='{$this->codestpro4}' and codestpro5='{$this->codestpro5}' and {$this->_table}.codemp='{$this->codemp}'";
		$Rs = $db->Execute($sql); 
		return $Rs->fields["Ultimo"];
	}
	
	public function IncluirTodos()
	{
		global $db;
		//$db->debug=true;
		$lb_valido = $this->completarEstructurasPresupuestariasNivel4();
		if($lb_valido)
		{
		 $lb_valido=$this->completarEstructurasPresupuestariasNivel5();
		}
		$lb_valido = $this->completarEstructurasPlanNivel5();
		
		if($this->Save())
		{
			return "1";
		}
		else
		{
			return $db->ErrorNo();
		}
			
	 }

	public function IniciarTran()
	{
		global $db;
		$db->StartTrans();
	} 
	public function CompletarTran()
	{
		global $db;
		if($db->CompleteTrans())
		{
			return "1";
		}	
		else
		{
			return "0";
		}
	}
	
	
	public function ActualizarTodos()
	{
		global $db;
	//	$db->StartTrans();
//		$this->save();
//		if($db->CompleteTrans())
//		{
		//	$IdPadre = $this->obtenerUltimoCodigo();
			$db->debug=true;	
			$this->IniciarTran();
			for($i=0;$i<count($this->Ads);$i++)
			{	
				$this->Ads[$i]->codinte = $this->codinte;
				$this->Ads[$i]->Incluir();
			}
			for($i=0;$i<count($this->Ubs);$i++)
			{	
				$this->Ubs[$i]->codinte = $this->codinte;
				$this->Ubs[$i]->Incluir();
			}	
			
			if(count($this->Metas)>0)
			{
				for($i=0;$i<count($this->Metas);$i++)
				{	
					$this->Metas[$i]->codinte = $this->codinte;
					$this->Metas[$i]->Incluir();
				}
			}
			
			
			if(count($this->Indis)>0)
			{
				
				for($i=0;$i<count($this->Indis);$i++)
				{	
					$this->Indis[$i]->codinte = $this->codinte;
					$this->Indis[$i]->Incluir();
				}
			}
			
			
			if(count($this->Gastos)>0)
			{
				if($this->existeFuenteCuentas()===false)
				{
					$this->marcarFuenteCuenta();
				}	
				for($i=0;$i<count($this->Gastos);$i++)
				{	
					$this->Gastos[$i]->codinte = $this->codinte;	
					$this->Gastos[$i]->Incluir();
				}
			}	
			if($this->CompletarTran())
			{
				return "1";
			}
			else
			{
				return "0";
			}
	}


	public function IncluirEnc()
	{
		try
		{
			global $db;
			$db->StartTrans();
			$this->save();
			$db->CompleteTrans();
			$IdPadre = $this->obtenerUltimoCodigo();
			return "{$IdPadre}";
		}
		catch (Exception $e) 
		{
			//mandar a un archivo de logs con los eventos fallidos 	
    		return "0";
		}
		
	}

	public function ObtenerCodigo()
	{
		global $db;
		global $_SESSION;
		$sql="select coalesce(max(codinte)+1,1) as codigo  from spe_relacion_es where codemp='{$_SESSION["codemp"]}' and ano_presupuesto={$_SESSION["ano_presupuesto"]}";
		$Rs = $db->Execute($sql);
		return $Rs->fields["codigo"];
	}
	
	public function crearcondicionplansinfiltro($conSelect,$conFrom,$conWhere)
	{
        global $db;
		$oNiveles = new ConfNivelDao();
		$nivelPlan = $oNiveles->ObtenerNivelPlan();
		$nivelPro = $oNiveles->ObtenerNivelProg();	
		switch($nivelPlan)
		{
			case '1':
                $oNivel=new ConfNivelDao();
				$oNivel->tipo="PL";
				$oNivel->nivel="1";
				$tama = $oNivel->LeerNumCar();
				$pos=(25-$tama)+1;   
				
				$strcon = $db->Concat("substr(p1.codest1,{$pos},{$tama})","'-'","substr(p2.codest2,{$pos1},{$tama1})");
				
				$conSelect .="p1.codest1,{$strcon} as codigo,p1.denest1 as denominacion";
				$conFrom.=",spe_estpro1 as p1";
				$conWhere.="and p1.codest1=i.codest1 and p1.codemp=i.codemp and p1.ano_presupuesto = i.ano_presupuesto ";
				break;	
			case '2':	
                $oNivel=new ConfNivelDao();
				$oNivel->tipo="PL";
				$oNivel->nivel="1";
				$tama = $oNivel->LeerNumCar();
				$pos=(25-$tama)+1;
                $oNivel->nivel="2";
				$tama1 = $oNivel->LeerNumCar();
				$pos1=(25-$tama1)+1;
				
				$strcon = $db->Concat("substr(p1.codest1,{$pos},{$tama})","'-'","substr(p2.codest2,{$pos1},{$tama1})");
				
				$conSelect .=" p1.codest1,p2.codest2,{$strcon} as codigo,p2.denest2 as denominacion";
				$conFrom.=",spe_estpro1 as p1,spe_estpro2 as p2";
				$conWhere.=" and p2.codest1=i.codest1 and p2.codest2=i.codest2
				 and p1.codest1=i.codest1 and p1.codemp=i.codemp and p1.ano_presupuesto = i.ano_presupuesto and p2.codemp=i.codemp and p2.ano_presupuesto = i.ano_presupuesto";
				break;	
			case '3':	
                $oNivel=new ConfNivelDao();
				$oNivel->tipo="PL";
				$oNivel->nivel="1";
				$tama = $oNivel->LeerNumCar();
				$pos=(25-$tama)+1;
                $oNivel->nivel="2";
				$tama1 = $oNivel->LeerNumCar();
				$pos1=(25-$tama1)+1;
                $oNivel->nivel="3";
				$tama2 = $oNivel->LeerNumCar();
				$pos2=(25-$tama2)+1;
				$strcon = $db->Concat("substr(p1.codest1,{$pos},{$tama})","'-'","substr(p2.codest2,{$pos1},{$tama1})","'-'","substr(p3.codest3,{$pos2},{$tama2})");
				
				$conSelect .="p1.codest1,p2.codest2,p3.codest3,{$strcon} as codigo,p3.denest3 as denominacion";
				$conFrom.=",spe_estpro1 as p1,spe_estpro2 as p2,spe_estpro3 as p3";
				$conWhere.=" and p3.codest1=i.codest1 and p3.codest2=i.codest2 and p3.codest3=i.codest3 and p3.codemp=i.codemp and p3.ano_presupuesto = i.ano_presupuesto and
							 p2.codest1=i.codest1 and p2.codest2=i.codest2 and p2.codemp=i.codemp and p2.ano_presupuesto = i.ano_presupuesto
							 and p1.codest1=i.codest1 and p1.codemp=i.codemp and p1.ano_presupuesto = i.ano_presupuesto";
				break;	
			case '4':	
                $oNivel=new ConfNivelDao();
				$oNivel->tipo="PL";
				$oNivel->nivel="1";
				$tama = $oNivel->LeerNumCar();
				$pos=(25-$tama)+1;
                $oNivel->nivel="2";
				$tama1 = $oNivel->LeerNumCar();
				$pos1=(25-$tama1)+1;
                $oNivel->nivel="3";
				$tama2 = $oNivel->LeerNumCar();
				$pos2=(25-$tama1)+1;
                $oNivel->nivel="4";
				$tama3 = $oNivel->LeerNumCar();
				$pos3=(25-$tama3)+1;  
				
				$strcon = $db->Concat("substr(p1.codest1,{$pos},{$tama})","'-'","substr(p2.codest2,{$pos1},{$tama1})","'-'","substr(p3.codest3,{$pos2},{$tama2})","'-'","substr(p4.codest4,{$pos3},{$tama3})");
				
				$conSelect .="p1.codest1,p2.codest2,p3.codest3,p4.codest4,{$strcon} as codigo,p4.denest4 as denominacion";
				$conFrom.=",spe_estpro1 as p1,spe_estpro2 as p2,spe_estpro3 as p3,spe_estpro4 as p4";
				$conWhere.=" and p4.codest1=i.codest1 and p4.codest2=i.codest2 and p4.codest3=i.codest3 and p4.codest4=i.codest4 and p4.codemp=i.codemp and p4.ano_presupuesto = i.ano_presupuesto and p3.codest1=i.codest1 and p3.codest2=i.codest2 and p3.codest3=i.codest3 and p3.codemp=i.codemp and p3.ano_presupuesto = i.ano_presupuesto and
							p2.codest1=i.codest1 and p2.codest2=i.codest2 and p2.codemp=i.codemp and p2.ano_presupuesto = i.ano_presupuesto
							and p1.codest1=i.codest1 and p1.codemp=i.codemp and p1.ano_presupuesto = i.ano_presupuesto";
				break;	
			case '5':	
                $oNivel=new ConfNivelDao();
				$oNivel->tipo="PL";
				$oNivel->nivel="1";
				$tama = $oNivel->LeerNumCar();
				$pos=(25-$tama)+1;
                $oNivel->nivel="2";
				$tama1 = $oNivel->LeerNumCar();
				$pos1=(25-$tama1)+1;
                $oNivel->nivel="3";
				$tama2 = $oNivel->LeerNumCar();
				$pos2=(25-$tama1)+1;
                $oNivel->nivel="4";
				$tama3 = $oNivel->LeerNumCar();
				$pos3=(25-$tama3)+1;
                $oNivel->nivel="5";
				$tama4 = $oNivel->LeerNumCar();
				$pos4=(25-$tama4)+1;
				
				$strcon = $db->Concat("substr(p1.codest1,{$pos},{$tama})","'-'","substr(p2.codest2,{$pos1},{$tama1})","'-'","substr(p3.codest3,{$pos2},{$tama2})","'-'","substr(p4.codest4,{$pos3},{$tama3})","'-'","substr(p5.codest5,{$pos4},{$tama4})");
				
				$conSelect .="codest1,p2.codest2, p3.codest3,p4.codest4,p5.codest5,{$strcon} as codigo,p5.denest5 as denominacion";
				$conFrom.=",spe_estpro1 as p1,spe_estpro2 as p2,spe_estpro3 as p3,spe_estpro4 as p4,spe_estpro5 as p5";
				$conWhere.=" and p5.codest1=i.codest1 and p5.codest2=i.codest2 and p5.codest3=i.codest3 and p5.codest4=i.codest4 and p5.codest5=i.codest5 and
							p4.codest1=i.codest1 and p4.codest2=i.codest2 and p4.codest3=i.codest3 and p4.codest4=i.codest4 and p3.codest1=i.codest1 and p3.codest2=i.codest2 and p3.codest3=i.codest3 and
							p2.codest1=i.codest1 and p2.codest2=i.codest2
							and p1.codest1=i.codest1 ";
					break;
		}
	}
	
		
 	public function crearcondicion($conSelect,$conFrom,$conWhere)
	{
        global $db;
		$oNiveles = new ConfNivelDao();
		$nivelPlan = $oNiveles->ObtenerNivelPlan();
		$nivelPro = $oNiveles->ObtenerNivelProg();	
		
		switch($nivelPlan)
		{
			case '1':
                $oNivel=new ConfNivelDao();
				$oNivel->tipo="PL";
				$oNivel->nivel="1";
				$tama = $oNivel->LeerNumCar();
				$pos=(25-$tama)+1;         
				$conSelect .=",substr(p1.codest1,{$pos},{$tama}) as codest1,p1.denest1";
				$conFrom.=",spe_estpro1 as p1";
				$conWhere.="and p1.codest1=i.codest1 and p1.codemp=i.codemp and p1.ano_presupuesto = i.ano_presupuesto";
				break;	
			case '2':	
                $oNivel=new ConfNivelDao();
				$oNivel->tipo="PL";
				$oNivel->nivel="1";
				$tama = $oNivel->LeerNumCar();
				$pos=(25-$tama)+1;
                $oNivel->nivel="2";
				$tama1 = $oNivel->LeerNumCar();
				$pos1=(25-$tama1)+1;
				$conSelect .=",substr(p1.codest1,{$pos},{$tama}) as codest1,p1.denest1,substr(p2.codest2,{$pos1},{$tama1}) as codest2,p2.denest2";
				$conFrom.=",spe_estpro1 as p1,spe_estpro2 as p2";
				$conWhere.=" and p2.codest1=i.codest1 and p2.codest2=i.codest2 and p2.codemp=i.codemp and p2.ano_presupuesto = i.ano_presupuesto
				 and p1.codest1=i.codest1 and p1.codemp=i.codemp and p1.ano_presupuesto = i.ano_presupuesto ";
				break;	
			case '3':	
                $oNivel=new ConfNivelDao();
				$oNivel->tipo="PL";
				$oNivel->nivel="1";
				$tama = $oNivel->LeerNumCar();
				$pos=(25-$tama)+1;
                                $oNivel->nivel="2";
				$tama1 = $oNivel->LeerNumCar();
				$pos1=(25-$tama1)+1;
                                $oNivel->nivel="3";
				$tama2 = $oNivel->LeerNumCar();
				$pos2=(25-$tama2)+1;
				$conSelect .=",substr(p1.codest1,{$pos},{$tama})  as codest1,p1.denest1,substr(p2.codest2,{$pos1},{$tama1}) as codest2,p2.denest2, substr(p3.codest3,{$pos2},{$tama2})as codest3,p3.denest3";
				$conFrom.=",spe_estpro1 as p1,spe_estpro2 as p2,spe_estpro3 as p3";
				$conWhere.=" and p3.codest1=i.codest1 and p3.codest2=i.codest2 and p3.codest3=i.codest3 and p3.codemp=i.codemp and p3.ano_presupuesto = i.ano_presupuesto and
p2.codest1=i.codest1 and p2.codest2=i.codest2 and p2.codemp=i.codemp and p2.ano_presupuesto = i.ano_presupuesto
 and p1.codest1=i.codest1 and p1.codemp=i.codemp and p1.ano_presupuesto = i.ano_presupuesto";
				break;	
			case '4':	
                                $oNivel=new ConfNivelDao();
				$oNivel->tipo="PL";
				$oNivel->nivel="1";
				$tama = $oNivel->LeerNumCar();
				$pos=(25-$tama)+1;
                                $oNivel->nivel="2";
				$tama1 = $oNivel->LeerNumCar();
				$pos1=(25-$tama1)+1;
                                $oNivel->nivel="3";
				$tama2 = $oNivel->LeerNumCar();
				$pos2=(25-$tama1)+1;
                                $oNivel->nivel="4";
				$tama3 = $oNivel->LeerNumCar();
				$pos3=(25-$tama3)+1;
                                
				$conSelect .=",substr(p1.codest1,{$pos},{$tama}) as codest1,p1.denest1,substr(p2.codest2,{$pos1},{$tama1}) as codest2,p2.denest2, substr(p3.codest3,{$pos2},{$tama2})as codest3,p3.denest3,substr(p4.codest4,{$pos3},{$tama3})as codest4,p4.denest4";
				$conFrom.=",spe_estpro1 as p1,spe_estpro2 as p2,spe_estpro3 as p3,spe_estpro4 as p4";
				$conWhere.=" and p4.codest1=i.codest1 and p4.codest2=i.codest2 and p4.codest3=i.codest3 and p4.codest4=i.codest4 and p4.codemp=i.codemp and p4.ano_presupuesto = i.ano_presupuesto and p3.codest1=i.codest1 and p3.codest2=i.codest2 and p3.codest3=i.codest3 and p3.codemp=i.codemp and p3.ano_presupuesto = i.ano_presupuesto and
p2.codest1=i.codest1 and p2.codest2=i.codest2 and p2.codemp=i.codemp and p2.ano_presupuesto = i.ano_presupuesto
 and p1.codest1=i.codest1 and p1.codemp=i.codemp and p1.ano_presupuesto = i.ano_presupuesto";
				break;	
			case '5':	
                $oNivel=new ConfNivelDao();
				$oNivel->tipo="PL";
				$oNivel->nivel="1";
				$tama = $oNivel->LeerNumCar();
				$pos=(25-$tama)+1;
                                $oNivel->nivel="2";
				$tama1 = $oNivel->LeerNumCar();
				$pos1=(25-$tama1)+1;
                                $oNivel->nivel="3";
				$tama2 = $oNivel->LeerNumCar();
				$pos2=(25-$tama1)+1;
                                $oNivel->nivel="4";
				$tama3 = $oNivel->LeerNumCar();
				$pos3=(25-$tama3)+1;
                                $oNivel->nivel="5";
				$tama4 = $oNivel->LeerNumCar();
				$pos4=(25-$tama4)+1;
				$conSelect .=",substr(p1.codest1,{$pos},{$tama}) as codest1) as codest1,p1.denest1,substr(p2.codest2,{$pos1},{$tama1}) as codest2,p2.denest2, substr(p3.codest3,{$pos2},{$tama2})as codest3,p3.denest3,substr(p4.codest4,{$pos3},{$tama3})as codest4,p4.denest4,substr(p5.codest5,{$pos4},{$tama4})as codest5,p5.denest5";
				$conFrom.=",spe_estpro1 as p1,spe_estpro2 as p2,spe_estpro3 as p3,spe_estpro4 as p4,spe_estpro5 as p5";
				$conWhere.=" and p5.codest1=i.codest1 and p5.codest2=i.codest2 and p5.codest3=i.codest3 and p5.codest4=i.codest4 and p5.codest5=i.codest5 and p5.codemp=i.codemp and p5.ano_presupuesto = i.ano_presupuesto and
p4.codest1=i.codest1 and p4.codest2=i.codest2 and p4.codest3=i.codest3 and p4.codest4=i.codest4 and p4.codemp=i.codemp and p4.ano_presupuesto = i.ano_presupuesto and p3.codest1=i.codest1 and p3.codest2=i.codest2 and p3.codest3=i.codest3 and p3.codemp=i.codemp and p3.ano_presupuesto = i.ano_presupuesto and
p2.codest1=i.codest1 and p2.codest2=i.codest2 and p2.codemp=i.codemp and p2.ano_presupuesto = i.ano_presupuesto
 and p1.codest1=i.codest1 and p1.codemp=i.codemp and p1.ano_presupuesto = i.ano_presupuesto";
				break;	

			
		}
		

		switch($nivelPro)
		{
			case '1':
				$oNivel=new ConfNivelDao();
				$oNivel->tipo="PR";
				$oNivel->nivel="1";
				$tama = $oNivel->LeerNumCar();
				$pos=(25-$tama)+1;
				$conSelect .=",substr(codestpro1,{$pos},{$tama}) as codigo,e1.denestpro1 as Descripcion";				$conFrom.=",sfp_estpro1 as e1";
				$conWhere.="and e1.codestpro1=i.codestpro1 and e1.codestpro1='{$this->codestpro1}'";
				break;	
			case '2':	
                 $oNivel=new ConfNivelDao();
				$oNivel->tipo="PR";
				$oNivel->nivel="2";
				$tama1 = $oNivel->LeerNumCar();
				$pos1=(25-$tama1)+1;
				$oNivel->nivel="1";
				$tama2 = $oNivel->LeerNumCar();
				$pos2=(25-$tama2)+1;	
				$strcon = $db->Concat("substr(e2.codestpro1,{$pos2},{$tama2})","'-'","substr(e2.codestpro2,{$pos1},{$tama1})");
				$conSelect .=",{$strcon} as codigo,e2.denestpro2 as Descripcion ";
				$conFrom.=",sfp_estpro1 as e1,sfp_estpro2 as e2";
				$conWhere.=" and e2.codestpro1=i.codestpro1 and e2.codestpro2=i.codestpro2 and e1.codestpro1=i.codestpro1 and e2.codestpro1='{$this->codestpro1}' and e2.codestpro2='{$this->codestpro2}'";
				break;	
			case '3':
                $oNivel=new ConfNivelDao();
				$oNivel->tipo="PR";
				$oNivel->nivel="2";
				$tama1 = $oNivel->LeerNumCar();
				$pos1=(25-$tama1)+1;
				$oNivel->nivel="1";
				$tama2 = $oNivel->LeerNumCar();
				$pos2=(25-$tama2)+1;
                                $oNivel->nivel="3";
				$tama3 = $oNivel->LeerNumCar();
				$pos3=(25-$tama3)+1;
                $strcon = $db->Concat("substr(e2.codestpro1,{$pos2},{$tama2})","'-'","substr(e2.codestpro2,{$pos1},{$tama1})","'-'","substr(e3.codestpro3,{$pos3},{$tama3})");        
				$conSelect .=",{$strcon} as codigo, e2.codestpro2,e2.denestpro2,e1.denestpro1,e1.codestpro1,e3.codestpro3,e3.denestpro3 as descripcion";
				$conFrom.=",sfp_estpro1 as e1,sfp_estpro2 as e2,sfp_estpro3 as e3";
				$conWhere.=" and e3.codestpro1=i.codestpro1 and e3.codestpro2=i.codestpro2 and e3.codestpro3=i.codestpro3 and e2.codestpro1=i.codestpro1 and e2.codestpro2=i.codestpro2 and e1.codestpro1=i.codestpro1 and e3.codestpro1='{$this->codestpro1}' and e3.codestpro2='{$this->codestpro2}' and e3.codestpro3='{$this->codestpro3}'";
				break;	
			case '4':
                        
				$oNivel=new ConfNivelDao();
				$oNivel->tipo="PR";
				$oNivel->nivel="2";
				$tama1 = $oNivel->LeerNumCar();
				$pos1=(25-$tama1)+1;
				$oNivel->nivel="1";
				$tama2 = $oNivel->LeerNumCar();
				$pos2=(25-$tama2)+1;
                $oNivel->nivel="3";
				$tama3 = $oNivel->LeerNumCar();
				$pos3=(25-$tama3)+1;
                $oNivel->nivel="4";
				$tama4 = $oNivel->LeerNumCar();
				$pos4=(25-$tama4)+1;
                $strcon = $db->Concat("substr(e2.codestpro1,{$pos2},{$tama2})","'-'","substr(e2.codestpro2,{$pos1},{$tama1})","'-'","substr(e3.codestpro3,{$pos3},{$tama3})","'-'","substr(e4.codestpro4,{$pos4},{$tama4})");        
				$conSelect .=",{$strcon} as codigo, e2.codestpro2,e2.denestpro2,e1.denestpro1,e1.codestpro1,e3.codestpro3,e4.denestpro4 as descripcion";
				$conFrom.=",sfp_estpro1 as e1,sfp_estpro2 as e2,sfp_estpro3 as e3,sfp_estpro4 as e4";
				$conWhere.=" and e4.codestpro1=i.codestpro1 and e4.codestpro2=i.codestpro2 and e4.codestpro3=i.codestpro3 and e4.codestpro4=i.codestpro4 and e3.codestpro1=i.codestpro1 and e3.codestpro2=i.codestpro2 and e3.codestpro3=i.codestpro3 and e2.codestpro1=i.codestpro1 and e2.codestpro2=i.codestpro2 and e1.codestpro1=i.codestpro1 and e4.codestpro1='{$this->codestpro1}' and e4.codestpro2='{$this->codestpro2}' and e4.codestpro3='{$this->codestpro3}' and e4.codestpro4='{$this->codestpro4}'";
				break;	
			case '5':	
				$oNivel=new ConfNivelDao();
				$oNivel->tipo="PR";
				$oNivel->nivel="2";
				$tama1 = $oNivel->LeerNumCar();
				$pos1=(25-$tama1)+1;
				$oNivel->nivel="1";
				$tama2 = $oNivel->LeerNumCar();
				$pos2=(25-$tama2)+1;
                $oNivel->nivel="3";
				$tama3 = $oNivel->LeerNumCar();
				$pos3=(25-$tama3)+1;
                $oNivel->nivel="4";
				$tama4 = $oNivel->LeerNumCar();
				$pos4=(25-$tama4)+1;
                $oNivel->nivel="5";
				$tama5 = $oNivel->LeerNumCar();
				$pos5=(25-$tama5)+1;
                $strcon = $db->Concat("substr(e2.codestpro1,{$pos2},{$tama2})","'-'","substr(e2.codestpro2,{$pos1},{$tama1})","'-'","substr(e3.codestpro3,{$pos3},{$tama3})","'-'","substr(e4.codestpro4,{$pos4},{$tama4})","'-'","substr(e5.codestpro5,{$pos5},{$tama5})");        
				$conSelect .=",{$strcon} as codigo, e2.codestpro2,e2.denestpro2,e1.denestpro1,e1.codestpro1,e3.codestpro3,e5.denestpro5 as descripcion";
				$conFrom.=",sfp_estpro1 as e1,sfp_estpro2 as e2,sfp_estpro3 as e3,sfp_estpro4 as e4,sfp_estpro5 as e5";
				$conWhere.=" and e5.codestpro1=i.codestpro1 and e5.codestpro2=i.codestpro2 and e5.codestpro3=i.codestpro3 and e5.codestpro4=i.codestpro4 and e5.codestpro5=i.codestpro5 and
e4.codestpro1=i.codestpro1 and e4.codestpro2=i.codestpro2 and e4.codestpro3=i.codestpro3 and e4.codestpro4=i.codestpro4 and e3.codestpro1=i.codestpro1 and e3.codestpro2=i.codestpro2 and e3.codestpro3=i.codestpro3 and e2.codestpro1=i.codestpro1 and e2.codestpro2=i.codestpro2 and e1.codestpro1=i.codestpro1 and e5.codestpro1='{$this->codestpro1}' and e5.codestpro2='{$this->codestpro2}' and e5.codestpro3='{$this->codestpro3}' and e5.codestpro4='{$this->codestpro4}' and e5.codestpro5='{$this->codestpro5}'";
	break;	

			
		}

		$condicion="{$conSelect} {$conFrom} {$conWhere}";
		return $condicion;
	}
	
	public function crearcondicionsinfiltro($conSelect,$conFrom,$conWhere)
	{
        global $db;
		$oNiveles = new ConfNivelDao();
		$oNiveles->tipo="UG";
		$nomultimo = $oNiveles->LeerNombreUltnivel();
		$nivelPlan = $oNiveles->ObtenerNivelPlan();
		$nivelPro = $oNiveles->ObtenerNivelProg();	
		$nivelUb=$oNiveles->ObtenerNivelUb();
		switch($nivelUb)
		{
			case '1':
				$conSelect .=",sigesp_ub1.codubgeo1 as codub, sigesp_ub1.denominacion,'{$nomultimo}' as nombrenivelub ";
				$conFrom.=",spe_int_ub1,sigesp_ub1";
				$conWhere.=" and sigesp_ub1.codemp = i.codemp and i.codinte=spe_int_ub1.codinte and spe_int_ub1.codubgeo1=sigesp_ub1.codubgeo1 and spe_int_ub1.codemp=i.codemp and spe_int_ub1.ano_presupuesto = i.ano_presupuesto";	
			break;
			case '2':
				$conSelect .=",sigesp_ub2.codubgeo1,sigesp_ub2.codubgeo2 as codub,sigesp_ub2.denominacion,'{$nomultimo}' as nombrenivelub";
				$conFrom.=",spe_int_ub2,sigesp_ub2";
				$conWhere.=" and sigesp_ub2.codemp = i.codemp  and i.codinte=spe_int_ub2.codinte and spe_int_ub2.codubgeo1=sigesp_ub2.codubgeo1 and spe_int_ub2.codubgeo2=sigesp_ub2.codubgeo2 and spe_int_ub2.codemp=i.codemp and spe_int_ub2.ano_presupuesto = i.ano_presupuesto";	
			break;
			case '3':
				$conSelect .=",sigesp_ub3.codubgeo1,sigesp_ub3.codubgeo3 as codub,sigesp_ub3.denominacion,'{$nomultimo}' as nombrenivelub";
				$conFrom.=",spe_int_ub3,sigesp_ub3";
				$conWhere.=" and sigesp_ub3.codemp = i.codemp and i.codinte=spe_int_ub3.codinte and spe_int_ub3.codubgeo1=sigesp_ub3.codubgeo1 and spe_int_ub3.codubgeo2=sigesp_ub3.codubgeo2 and spe_int_ub3.codubgeo3=sigesp_ub3.codubgeo3 and spe_int_ub3.codemp=i.codemp and spe_int_ub3.ano_presupuesto = i.ano_presupuesto";	
			break;
			case '4':
				$conSelect .=",sigesp_ub4.codubgeo1,sigesp_ub4.codubgeo4 as codub,sigesp_ub4.denominacion,'{$nomultimo}' as nombrenivelub";
				$conFrom.=",spe_int_ub4,sigesp_ub4";
				$conWhere.=" and sigesp_ub4.codemp = i.codemp and i.codinte=spe_int_ub4.codinte and spe_int_ub4.codubgeo1=sigesp_ub4.codubgeo1 and spe_int_ub4.codubgeo2=sigesp_ub4.codubgeo2 and spe_int_ub4.codubgeo3=sigesp_ub4.codubgeo3 and spe_int_ub4.codubgeo4=sigesp_ub4.codubgeo4 and spe_int_ub4.codemp=i.codemp and spe_int_ub4.ano_presupuesto = i.ano_presupuesto";	
			break;		
			case '5':
				$conSelect .=",sigesp_ub5.codubgeo1,sigesp_ub5.codubgeo4 as codub,sigesp_ub5.denominacion,'{$nomultimo}' as nombrenivelub";
				$conFrom.=",spe_int_ub5,sigesp_ub5";
				$conWhere.=" and sigesp_ub5.codemp = i.codemp and i.codinte=spe_int_ub5.codinte and spe_int_ub5.codubgeo1=sigesp_ub5.codubgeo1 and spe_int_ub5.codubgeo2=sigesp_ub5.codubgeo2 and spe_int_ub5.codubgeo3=sigesp_ub5.codubgeo3 and spe_int_ub5.codubgeo4=sigesp_ub5.codubgeo4 and spe_int_ub5.codubgeo5=sigesp_ub5.codubgeo5 and spe_int_ub5.codemp=i.codemp and spe_int_ub5.ano_presupuesto = i.ano_presupuesto";	
			break;		
			
			
		}
		
		switch($nivelPlan)
		{
			case '1':
                $oNivel=new ConfNivelDao();
				$oNivel->tipo="PL";
				$nomultimo = $oNivel->LeerNombreUltnivel();
				$oNivel->nivel="1";
				$tama = $oNivel->LeerNumCar();
				$pos=(25-$tama)+1;         
				$conSelect .=",substr(p1.codest1,{$pos},{$tama}) as codplan,p1.denest1 as denplan, '{$nomultimo}' as nombrenivelpl";
				$conFrom.=",spe_estpro1 as p1";
				$conWhere.=" and p1.codest1=i.codest1 and p1.codemp=i.codemp and p1.ano_presupuesto = i.ano_presupuesto";
				break;	
			case '2':	
                $oNivel=new ConfNivelDao();
				$oNivel->tipo="PL";
				$nomultimo = $oNivel->LeerNombreUltnivel();
				$oNivel->nivel="1";
				$tama = $oNivel->LeerNumCar();
				$pos=(25-$tama)+1;
                $oNivel->nivel="2";
				$tama1 = $oNivel->LeerNumCar();
				$pos1=(25-$tama1)+1;
				
				$strcon = $db->Concat("substr(p1.codest1,{$pos},{$tama})","'-'","substr(p2.codest2,{$pos1},{$tama1})");
				
				$conSelect .=",$strcon as codplan,p2.denest2 as denplan, '{$nomultimo}' as nombrenivelpl";
				$conFrom.=",spe_estpro1 as p1,spe_estpro2 as p2";
				$conWhere.=" and p2.codest1=i.codest1 and p2.codest2=i.codest2 and p2.codemp=i.codemp and p2.ano_presupuesto = i.ano_presupuesto
 and p1.codest1=i.codest1 and p1.codemp=i.codemp and p1.ano_presupuesto = i.ano_presupuesto";
				break;	
			case '3':	
                $oNivel=new ConfNivelDao();
				$oNivel->tipo="PL";
				$oNivel->nivel="1";
				$nomultimo = $oNivel->LeerNombreUltnivel();
				$tama = $oNivel->LeerNumCar();
				$pos=(25-$tama)+1;
                $oNivel->nivel="2";
				$tama1 = $oNivel->LeerNumCar();
				$pos1=(25-$tama1)+1;
                $oNivel->nivel="3";
				$tama2 = $oNivel->LeerNumCar();
				$pos2=(25-$tama2)+1;
				$strcon = $db->Concat("substr(p1.codest1,{$pos},{$tama})","'-'","substr(p2.codest2,{$pos1},{$tama1})","'-'","substr(p3.codest3,{$pos2},{$tama2})");
				
				$conSelect .=",substr(p1.codest1,{$pos},{$tama})  as codest1,p1.denest1,substr(p2.codest2,{$pos1},{$tama1}) as codest2,p2.denest2,$strcon as codplan,p3.denest3 as denplan,'{$nomultimo}' as nombrenivelpl";
				$conFrom.=",spe_estpro1 as p1,spe_estpro2 as p2,spe_estpro3 as p3";
				$conWhere.=" and p3.codest1=i.codest1 and p3.codest2=i.codest2 and p3.codest3=i.codest3 and p3.codemp=i.codemp and p3.ano_presupuesto = i.ano_presupuesto and
 p2.codest1=i.codest1 and p2.codest2=i.codest2 and p2.codemp=i.codemp and p2.ano_presupuesto = i.ano_presupuesto
 and p1.codest1=i.codest1 and p1.codemp=i.codemp and p1.ano_presupuesto = i.ano_presupuesto";
				break;	
			case '4':	
                $oNivel=new ConfNivelDao();
				$oNivel->tipo="PL";
				$oNivel->nivel="1";
				$nomultimo = $oNivel->LeerNombreUltnivel();
				$tama = $oNivel->LeerNumCar();
				$pos=(25-$tama)+1;
                $oNivel->nivel="2";
				$tama1 = $oNivel->LeerNumCar();
				$pos1=(25-$tama1)+1;
                $oNivel->nivel="3";
				$tama2 = $oNivel->LeerNumCar();
				$pos2=(25-$tama1)+1;
                $oNivel->nivel="4";
				$tama3 = $oNivel->LeerNumCar();
				$pos3=(25-$tama3)+1;    
				$strcon = $db->Concat("substr(p1.codest1,{$pos},{$tama})","'-'","substr(p2.codest2,{$pos1},{$tama1})","'-'","substr(p3.codest3,{$pos1},{$tama1})","'-'","substr(p4.codest4,{$pos3},{$tama3})");            
				$conSelect .=",substr(p1.codest1,{$pos},{$tama}) as codest1,p1.denest1,substr(p2.codest2,{$pos1},{$tama1}) as codest2,p2.denest2, substr(p3.codest3,{$pos2},{$tama2})as codest3,p3.denest3,{$strcon} as codplan,p4.denest4 as denplan,'{$nomultimo}' as nombrenivelpl";
				$conFrom.=",spe_estpro1 as p1,spe_estpro2 as p2,spe_estpro3 as p3,spe_estpro4 as p4";
				$conWhere.=" and p4.codest1=i.codest1 and p4.codest2=i.codest2 and p4.codest3=i.codest3 and p4.codest4=i.codest4 and p4.codemp=i.codemp and p4.ano_presupuesto = i.ano_presupuesto and p3.codest1=i.codest1 and p3.codest2=i.codest2 and p3.codest3=i.codest3 and p3.codemp=i.codemp and p3.ano_presupuesto = i.ano_presupuesto and
p2.codest1=i.codest1 and p2.codest2=i.codest2 and p2.codemp=i.codemp and p2.ano_presupuesto = i.ano_presupuesto
 and p1.codest1=i.codest1 and p1.codemp=i.codemp and p1.ano_presupuesto = i.ano_presupuesto";
				break;	
			case '5':	
                $oNivel=new ConfNivelDao();
				$oNivel->tipo="PL";
				$oNivel->nivel="1";
				$tama = $oNivel->LeerNumCar();
				$pos=(25-$tama)+1;
                $oNivel->nivel="2";
                $nomultimo = $oNivel->LeerNombreUltnivel();
				$tama1 = $oNivel->LeerNumCar();
				$pos1=(25-$tama1)+1;
                $oNivel->nivel="3";
				$tama2 = $oNivel->LeerNumCar();
				$pos2=(25-$tama1)+1;
                $oNivel->nivel="4";
				$tama3 = $oNivel->LeerNumCar();
				$pos3=(25-$tama3)+1;
                $oNivel->nivel="5";
				$tama4 = $oNivel->LeerNumCar();
				$pos4=(25-$tama4)+1;
				$strcon = $db->Concat("substr(p1.codest1,{$pos},{$tama})","'-'","substr(p2.codest2,{$pos1},{$tama1})","'-'","substr(p3.codest3,{$pos1},{$tama1})","'-'","substr(p4.codest4,{$pos3},{$tama3})","substr(p5.codest5,{$pos4},{$tama4})");
				
				$conSelect .=",substr(p1.codest1,{$pos},{$tama}) as codest1) as codest1,p1.denest1,substr(p2.codest2,{$pos1},{$tama1}) as codest2,p2.denest2, substr(p3.codest3,{$pos2},{$tama2})as codest3,p3.denest3,substr(p4.codest4,{$pos3},{$tama3})as codest4,p4.denest4,{$strcon} as codplan,p5.denest5 as denplan,'{$nomultimo}' as nombrenivelpl";
				$conFrom.=",spe_estpro1 as p1,spe_estpro2 as p2,spe_estpro3 as p3,spe_estpro4 as p4,spe_estpro5 as p5";
				$conWhere.=" and p5.codest1=i.codest1 and p5.codest2=i.codest2 and p5.codest3=i.codest3 and p5.codest4=i.codest4 and p5.codest5=i.codest5 and p5.codemp=i.codemp and p5.ano_presupuesto = i.ano_presupuesto and
p4.codest1=i.codest1 and p4.codest2=i.codest2 and p4.codest3=i.codest3 and p4.codest4=i.codest4 and p4.codemp=i.codemp and p4.ano_presupuesto = i.ano_presupuesto and p3.codest1=i.codest1 and p3.codest2=i.codest2 and p3.codest3=i.codest3 and p3.codemp=i.codemp and p3.ano_presupuesto = i.ano_presupuesto and
p2.codest1=i.codest1 and p2.codest2=i.codest2 and p2.codemp=i.codemp and p2.ano_presupuesto = i.ano_presupuesto
 and p1.codest1=i.codest1 and p1.codemp=i.codemp and p1.ano_presupuesto = i.ano_presupuesto";
				break;	

			
		}
		

		switch($nivelPro)
		{
			case '1':
				$oNivel=new ConfNivelDao();
				$oNivel->tipo="PR";
				$oNivel->nivel="1";
				$nomultimo = $oNivel->LeerNombreUltnivel();
				$tama = $oNivel->LeerNumCar();
				$pos=(25-$tama)+1;
				$conSelect .=",substr(codestpro1,{$pos},{$tama}) as codigo,e1.estcla,e1.denestpro1 as Descripcion,'{$nomultimo}' as nombrenivelpr";			
				$conFrom.=",sfp_estpro1 as e1";
				$conWhere.=" and e1.codestpro1=i.codestpro1 and e1.codemp=i.codemp and e1.ano_presupuesto = i.ano_presupuesto";
				break;	
			case '2':	
                $oNivel=new ConfNivelDao();
				$oNivel->tipo="PR";
				$oNivel->nivel="2";
				$tama1 = $oNivel->LeerNumCar();
				$pos1=(25-$tama1)+1;
				$nomultimo = $oNivel->LeerNombreUltnivel();
				$oNivel->nivel="1";
				$tama2 = $oNivel->LeerNumCar();
				$pos2=(25-$tama2)+1;	
				$strcon = $db->Concat("substr(e2.codestpro1,{$pos2},{$tama2})","'-'","substr(e2.codestpro2,{$pos1},{$tama1})");
				$conSelect .=",{$strcon} as codigo,e2.estcla,e2.denestpro2 as Descripcion,'{$nomultimo}' as nombrenivelpr";
				$conFrom.=",sfp_estpro1 as e1,sfp_estpro2 as e2";
				$conWhere.=" and e2.codestpro1=i.codestpro1 and e2.codestpro2=i.codestpro2 and e2.codemp=i.codemp and e2.ano_presupuesto = i.ano_presupuesto and e1.codestpro1=i.codestpro1 and e1.codemp=i.codemp and e1.ano_presupuesto = i.ano_presupuesto";
				break;	
			case '3':
                $oNivel=new ConfNivelDao();
				$oNivel->tipo="PR";
				$oNivel->nivel="3";
				$nomultimo = $oNivel->LeerNombreUltnivel();
				$tama1 = $oNivel->LeerNumCar();
				$pos1=(25-$tama1)+1;
				$oNivel->nivel="1";
				$tama2 = $oNivel->LeerNumCar();
				$pos2=(25-$tama2)+1;
                                $oNivel->nivel="3";
				$tama3 = $oNivel->LeerNumCar();
				$pos3=(25-$tama3)+1;
                                $strcon = $db->Concat("substr(e2.codestpro1,{$pos2},{$tama2})","'-'","substr(e2.codestpro2,{$pos1},{$tama1})","'-'","substr(e3.codestpro3,{$pos3},{$tama3})");        
				$conSelect .=",{$strcon} as codigo, e2.codestpro2,e2.estcla,e2.denestpro2,e1.denestpro1,e1.codestpro1,e3.codestpro3,e3.denestpro3 as descripcion,'{$nomultimo}' as nombrenivelpr";
				$conFrom.=",sfp_estpro1 as e1,sfp_estpro2 as e2,sfp_estpro3 as e3";
				$conWhere.=" and e3.codestpro1=i.codestpro1 and e3.codestpro2=i.codestpro2 and e3.codestpro3=i.codestpro3 and e3.codemp=i.codemp and e3.ano_presupuesto = i.ano_presupuesto and e2.codestpro1=i.codestpro1 and e2.codestpro2=i.codestpro2 and e2.codemp=i.codemp and e2.ano_presupuesto = i.ano_presupuesto and e1.codestpro1=i.codestpro1 and e1.codemp=i.codemp and e1.ano_presupuesto = i.ano_presupuesto";
				break;	
			case '4':
                        
				$oNivel=new ConfNivelDao();
				$oNivel->tipo="PR";
				$oNivel->nivel="4";
				$tama1 = $oNivel->LeerNumCar();
				$pos1=(25-$tama1)+1;
				$oNivel->nivel="1";
				$nomultimo = $oNivel->LeerNombreUltnivel();
				$tama2 = $oNivel->LeerNumCar();
				$pos2=(25-$tama2)+1;
                                $oNivel->nivel="3";
				$tama3 = $oNivel->LeerNumCar();
				$pos3=(25-$tama3)+1;
                                 $oNivel->nivel="4";
				$tama4 = $oNivel->LeerNumCar();
				$pos4=(25-$tama4)+1;
                                $strcon = $db->Concat("substr(e2.codestpro1,{$pos2},{$tama2})","'-'","substr(e2.codestpro2,{$pos1},{$tama1})","'-'","substr(e3.codestpro3,{$pos3},{$tama3})","'-'","substr(e4.codestpro4,{$pos4},{$tama4})");        
				$conSelect .=",{$strcon} as codigo, e2.codestpro2,e2.estcla,e2.denestpro2,e1.denestpro1,e1.codestpro1,e3.codestpro3,e4.denestpro4 as descripcion,'{$nomultimo}' as nombrenivelpr";
				$conFrom.=",sfp_estpro1 as e1,sfp_estpro2 as e2,sfp_estpro3 as e3,sfp_estpro4 as e4";
				$conWhere.=" and e4.codestpro1=i.codestpro1 and e4.codestpro2=i.codestpro2 and e4.codestpro3=i.codestpro3 and e4.codestpro4=i.codestpro4 and e4.codemp=i.codemp and e4.ano_presupuesto = i.ano_presupuesto and e3.codestpro1=i.codestpro1 and e3.codestpro2=i.codestpro2 and e3.codestpro3=i.codestpro3 and e3.codemp=i.codemp and e3.ano_presupuesto = i.ano_presupuesto and e2.codestpro1=i.codestpro1 and e2.codestpro2=i.codestpro2 and e2.codemp=i.codemp and e2.ano_presupuesto = i.ano_presupuesto and e1.codestpro1=i.codestpro1 and e1.codemp=i.codemp and e1.ano_presupuesto = i.ano_presupuesto";
				break;	
			case '5':	
				$oNivel=new ConfNivelDao();
				$oNivel->tipo="PR";
				$oNivel->nivel="5";
				$nomultimo = $oNivel->LeerNombreUltnivel();
				$tama1 = $oNivel->LeerNumCar();
				$pos1=(25-$tama1)+1;
				$oNivel->nivel="1";
				$tama2 = $oNivel->LeerNumCar();
				$pos2=(25-$tama2)+1;
                                $oNivel->nivel="3";
				$tama3 = $oNivel->LeerNumCar();
				$pos3=(25-$tama3)+1;
                                 $oNivel->nivel="4";
				$tama4 = $oNivel->LeerNumCar();
				$pos4=(25-$tama4)+1;
                                 $oNivel->nivel="5";
				$tama5 = $oNivel->LeerNumCar();
				$pos5=(25-$tama5)+1;
                                $strcon = $db->Concat("substr(e2.codestpro1,{$pos2},{$tama2})","'-'","substr(e2.codestpro2,{$pos1},{$tama1})","'-'","substr(e3.codestpro3,{$pos3},{$tama3})","'-'","substr(e4.codestpro4,{$pos4},{$tama4})","'-'","substr(e5.codestpro5,{$pos5},{$tama5})");        
				$conSelect .=",{$strcon} as codigo, e2.codestpro2,e2.estcla,e2.denestpro2,e1.denestpro1,e1.codestpro1,e3.codestpro3,e5.denestpro5 as descripcion,'{$nomultimo}' as nombrenivelpr";
				$conFrom.=",sfp_estpro1 as e1,sfp_estpro2 as e2,sfp_estpro3 as e3,sfp_estpro4 as e4,sfp_estpro5 as e5";
				$conWhere.=" and e5.codestpro1=i.codestpro1 and e5.codestpro2=i.codestpro2 and e5.codestpro3=i.codestpro3 and e5.codestpro4=i.codestpro4 and e5.codestpro5=i.codestpro5 and e5.codemp=i.codemp and e5.ano_presupuesto = i.ano_presupuesto and
				e4.codestpro1=i.codestpro1 and e4.codestpro2=i.codestpro2 and e4.codestpro3=i.codestpro3 and e4.codestpro4=i.codestpro4 and e4.codemp=i.codemp and e4.ano_presupuesto = i.ano_presupuesto and e3.codestpro1=i.codestpro1 and e3.codestpro2=i.codestpro2 and e3.codestpro3=i.codestpro3 and e3.codemp=i.codemp and e3.ano_presupuesto = i.ano_presupuesto and e2.codestpro1=i.codestpro1 and e2.codestpro2=i.codestpro2 and e2.codemp=i.codemp and e2.ano_presupuesto = i.ano_presupuesto and e1.codestpro1=i.codestpro1 and e1.codemp=i.codemp and e1.ano_presupuesto = i.ano_presupuesto";
				break;	
		}
		
		$oNivel->tipo="EA";
		$nomultimo = $oNivel->LeerNombreUltnivel();
		$conSelect.=",sfp_estructura_ad.coduac, sfp_estructura_ad.denuac,'{$nomultimo}' as nombrenivelea";
		$conFrom.=",sfp_estructura_ad, spe_inte_unadmin";
		$conWhere.=" and i.codinte=spe_inte_unadmin.codinte  and spe_inte_unadmin.codemp=i.codemp and spe_inte_unadmin.ano_presupuesto = i.ano_presupuesto and spe_inte_unadmin.codemp=sfp_estructura_ad.codemp and spe_inte_unadmin.coduac=sfp_estructura_ad.coduac and spe_inte_unadmin.nivel=sfp_estructura_ad.nivel";
		$condicion="{$conSelect} {$conFrom} {$conWhere}";
		return $condicion;
	}

	public function crearcondicionsinfiltrocat($conSelect,$conFrom,$conWhere)
	{
        global $db;
		$oNiveles = new ConfNivelDao();
		$oNiveles->tipo="UG";
		$nomultimo = $oNiveles->LeerNombreUltnivel();
		$nivelPlan = $oNiveles->ObtenerNivelPlan();
		$nivelPro = $oNiveles->ObtenerNivelProg();	
		$nivelUb=$oNiveles->ObtenerNivelUb();
		
		
		switch($nivelPro)
		{
			case '1':
				$oNivel=new ConfNivelDao();
				$oNivel->tipo="PR";
				$oNivel->nivel="1";
				$nomultimo = $oNivel->LeerNombreUltnivel();
				$tama = $oNivel->LeerNumCar();
				$pos=(25-$tama)+1;
				$conSelect .=",substr(codestpro1,{$pos},{$tama}) as codigo,e1.estcla,e1.denestpro1 as Descripcion,'{$nomultimo}' as nombrenivelpr";			
				$conFrom.=",sfp_estpro1 as e1";
				$conWhere.="and e1.codestpro1=i.codestpro1 ";
				break;	
			case '2':	
                $oNivel=new ConfNivelDao();
				$oNivel->tipo="PR";
				$oNivel->nivel="2";
				$tama1 = $oNivel->LeerNumCar();
				$pos1=(25-$tama1)+1;
				$nomultimo = $oNivel->LeerNombreUltnivel();
				$oNivel->nivel="1";
				$tama2 = $oNivel->LeerNumCar();
				$pos2=(25-$tama2)+1;	
				$strcon = $db->Concat("substr(e2.codestpro1,{$pos2},{$tama2})","'-'","substr(e2.codestpro2,{$pos1},{$tama1})");
				$conSelect .=",{$strcon} as codigo,e2.estcla,e2.denestpro2 as Descripcion,'{$nomultimo}' as nombrenivelpr";
				$conFrom.=",sfp_estpro1 as e1,sfp_estpro2 as e2";
				$conWhere.=" and e2.codestpro1=i.codestpro1 and e2.codestpro2=i.codestpro2 and e1.codestpro1=i.codestpro1";
				break;	
			case '3':
                $oNivel=new ConfNivelDao();
				$oNivel->tipo="PR";
				$oNivel->nivel="3";
				$nomultimo = $oNivel->LeerNombreUltnivel();
				$tama1 = $oNivel->LeerNumCar();
				$pos1=(25-$tama1)+1;
				$oNivel->nivel="1";
				$tama2 = $oNivel->LeerNumCar();
				$pos2=(25-$tama2)+1;
                                $oNivel->nivel="3";
				$tama3 = $oNivel->LeerNumCar();
				$pos3=(25-$tama3)+1;
                                $strcon = $db->Concat("substr(e2.codestpro1,{$pos2},{$tama2})","'-'","substr(e2.codestpro2,{$pos1},{$tama1})","'-'","substr(e3.codestpro3,{$pos3},{$tama3})");        
				$conSelect .=",{$strcon} as codigo, e2.codestpro2,e2.denestpro2,e1.estcla,e1.denestpro1,e1.codestpro1,e3.codestpro3,e3.denestpro3 as descripcion,'{$nomultimo}' as nombrenivelpr";
				$conFrom.=",sfp_estpro1 as e1,sfp_estpro2 as e2,sfp_estpro3 as e3";
				$conWhere.=" and e3.codestpro1=i.codestpro1 and e3.codestpro2=i.codestpro2 and e3.codestpro3=i.codestpro3 and e2.codestpro1=i.codestpro1 and e2.codestpro2=i.codestpro2 and e1.codestpro1=i.codestpro1";
				break;	
			case '4':
                        
				$oNivel=new ConfNivelDao();
				$oNivel->tipo="PR";
				$oNivel->nivel="4";
				$tama1 = $oNivel->LeerNumCar();
				$pos1=(25-$tama1)+1;
				$oNivel->nivel="1";
				$nomultimo = $oNivel->LeerNombreUltnivel();
				$tama2 = $oNivel->LeerNumCar();
				$pos2=(25-$tama2)+1;
                                $oNivel->nivel="3";
				$tama3 = $oNivel->LeerNumCar();
				$pos3=(25-$tama3)+1;
                                 $oNivel->nivel="4";
				$tama4 = $oNivel->LeerNumCar();
				$pos4=(25-$tama4)+1;
                                $strcon = $db->Concat("substr(e2.codestpro1,{$pos2},{$tama2})","'-'","substr(e2.codestpro2,{$pos1},{$tama1})","'-'","substr(e3.codestpro3,{$pos3},{$tama3})","'-'","substr(e4.codestpro4,{$pos4},{$tama4})");        
				$conSelect .=",{$strcon} as codigo, e2.codestpro2,e2.denestpro2,e1.denestpro1,e1.estcla,e1.codestpro1,e3.codestpro3,e4.denestpro4 as descripcion,'{$nomultimo}' as nombrenivelpr";
				$conFrom.=",sfp_estpro1 as e1,sfp_estpro2 as e2,sfp_estpro3 as e3,sfp_estpro4 as e4";
				$conWhere.=" and e4.codestpro1=i.codestpro1 and e4.codestpro2=i.codestpro2 and e4.codestpro3=i.codestpro3 and e4.codestpro4=i.codestpro4 and e3.codestpro1=i.codestpro1 and e3.codestpro2=i.codestpro2 and e3.codestpro3=i.codestpro3 and e2.codestpro1=i.codestpro1 and e2.codestpro2=i.codestpro2 and e1.codestpro1=i.codestpro1";
				break;	
			case '5':	
				$oNivel=new ConfNivelDao();
				$oNivel->tipo="PR";
				$oNivel->nivel="5";
				$nomultimo = $oNivel->LeerNombreUltnivel();
				$tama1 = $oNivel->LeerNumCar();
				$pos1=(25-$tama1)+1;
				$oNivel->nivel="1";
				$tama2 = $oNivel->LeerNumCar();
				$pos2=(25-$tama2)+1;
                                $oNivel->nivel="3";
				$tama3 = $oNivel->LeerNumCar();
				$pos3=(25-$tama3)+1;
                                 $oNivel->nivel="4";
				$tama4 = $oNivel->LeerNumCar();
				$pos4=(25-$tama4)+1;
                                 $oNivel->nivel="5";
				$tama5 = $oNivel->LeerNumCar();
				$pos5=(25-$tama5)+1;
                                $strcon = $db->Concat("substr(e2.codestpro1,{$pos2},{$tama2})","'-'","substr(e2.codestpro2,{$pos1},{$tama1})","'-'","substr(e3.codestpro3,{$pos3},{$tama3})","'-'","substr(e4.codestpro4,{$pos4},{$tama4})","'-'","substr(e5.codestpro5,{$pos5},{$tama5})");        
				$conSelect .=",{$strcon} as codigo, e2.codestpro2,e2.denestpro2,e1.estcla,e1.denestpro1,e1.codestpro1,e3.codestpro3,e5.denestpro5 as descripcion,'{$nomultimo}' as nombrenivelpr";
				$conFrom.=",sfp_estpro1 as e1,sfp_estpro2 as e2,sfp_estpro3 as e3,sfp_estpro4 as e4,sfp_estpro5 as e5";
				$conWhere.=" and e5.codestpro1=i.codestpro1 and e5.codestpro2=i.codestpro2 and e5.codestpro3=i.codestpro3 and e5.codestpro4=i.codestpro4 and e5.codestpro5=i.codestpro5 and
				e4.codestpro1=i.codestpro1 and e4.codestpro2=i.codestpro2 and e4.codestpro3=i.codestpro3 and e4.codestpro4=i.codestpro4 and e3.codestpro1=i.codestpro1 and e3.codestpro2=i.codestpro2 and e3.codestpro3=i.codestpro3 and e2.codestpro1=i.codestpro1 and e2.codestpro2=i.codestpro2 and e1.codestpro1=i.codestpro1";
				break;	
		}
		return $condicion;
	}
	
	public function crearcondicionplan($conSelect,$conFrom,$conWhere)
	{
		global $db;
		$oNiveles = new ConfNivelDao();
		$nivelPlan = $oNiveles->ObtenerNivelPlan();
		$nivelPro = $oNiveles->ObtenerNivelProg();	
		
		switch($nivelPlan)
		{
			case '1':
                        
				$conSelect .=",p1.codest1,p1.denest1";
				$conFrom.=",spe_estpro1 as p1";
				$conWhere.="and p1.codest1=i.codest1 and p1.codest1='{$this->codest1}' and p1.ano_presupuesto = i.ano_presupuesto and p1.codemp = i.codemp ";
				break;	
			case '2':	
				$conSelect .=",p1.codest1,p1.denest1,p2.codest2,p2.denest2";
				$conFrom.=",spe_estpro1 as p1,spe_estpro2 as p2";
				$conWhere.=" and p2.codest1=i.codest1 and p2.codest2=i.codest2
 							  and p2.codest1='{$this->codest1}' and p2.codest2='{$this->codest2}' and p2.ano_presupuesto = i.ano_presupuesto and p2.codemp = i.codemp ";
				break;	
			case '3':	
				$conSelect .=",p1.codest1,p1.denest1,p2.codest2,p2.denest2,p3.codest3,p3.denest3";
				$conFrom.=",spe_estpro1 as p1,spe_estpro2 as p2,spe_estpro3 as p3";
				$conWhere.=" and p3.codest1=i.codest1 and p3.codest2=i.codest2 and p3.codest3=i.codest3 and
p2.codest1=i.codest1 and p2.codest2=i.codest2 and p3.codest1='{$this->codest1}' and p3.codest2='{$this->codest2}' and p3.codest3='{$this->codest3}'
 and p1.codest1=i.codest1 and p3.ano_presupuesto = i.ano_presupuesto and p3.codemp = i.codemp ";
				break;	
					
			case '4':	
				$conSelect.=",p1.codest1,p1.denest1,p2.codest2,p2.denest2,p3.codest3,p3.denest3,
							p4.codest4,p4.denest4";
				$conFrom.=",spe_estpro1 as p1,spe_estpro2 as p2,spe_estpro3 as p3,spe_estpro4 as p4";
				$conWhere.=" and p4.codest1=i.codest1 and p4.codest2=i.codest2 and p4.codest3=i.codest3 and p4.codest4=i.codest4 and p3.codest1=i.codest1 and p3.codest2=i.codest2 and p3.codest3=i.codest3 and
p2.codest1=i.codest1 and p2.codest2=i.codest2
 and p1.codest1=i.codest1 and p4.codest1='{$this->codest1}' and p4.codest2='{$this->codest2}' and p4.codest3='{$this->codest3}' and p4.codest4='{$this->codest4}' and p4.ano_presupuesto = i.ano_presupuesto and p4.codemp = i.codemp ";
				break;	
			case '5':	
				$conSelect .=",i.codinte,p1.codest1,p1.denest1,p2.codest2,p2.denest2,p3.codest3,p3.denest3,p4.codest4,p4.denest4,p5.codest5,p5.denest5";
				$conFrom.=",spe_estpro1 as p1,spe_estpro2 as p2,spe_estpro3 as p3,spe_estpro4 as p4,spe_estpro5 as p5";
				$conWhere.=" and p5.codest1=i.codest1 and p5.codest2=i.codest2 and p5.codest3=i.codest3 and p5.codest4=i.codest4 and p5.codest5=i.codest5 and
p4.codest1=i.codest1 and p4.codest2=i.codest2 and p4.codest3=i.codest3 and p4.codest4=i.codest4 and p3.codest1=i.codest1 and p3.codest2=i.codest2 and p3.codest3=i.codest3 and
p2.codest1=i.codest1 and p2.codest2=i.codest2
 and p1.codest1=i.codest1 and p5.codest1='{$this->codest1}' and p5.codest2='{$this->codest2}' and p5.codest3='{$this->codest3}' and p5.codest4='{$this->codest4}' and p5.codest5='{$this->codest5} and p5.ano_presupuesto = i.ano_presupuesto and p5.codemp = i.codemp ";
				break;	

			
		}

		switch($nivelPro)
		{
			case '1':
				$oNivel=new ConfNivelDao();
				$oNivel->tipo="PR";
				$oNivel->nivel="1";
				$tama = $oNivel->LeerNumCar();
				$pos=(25-$tama)+1;
				$conSelect .=",substr(codestpro1,{$pos},{$tama}) as codigo,e1.denestpro1 as Descripcion";
				$conFrom.=",sfp_estpro1 as e1";
				$conWhere.="and e1.codestpro1=i.codestpro1 and e1.codemp = i.codemp and e1.ano_presupuesto = i.ano_presupuesto ";
				break;	
			case '2':	
				$oNivel=new ConfNivelDao();
				$oNivel->tipo="PR";
				$oNivel->nivel="2";
				$tama1 = $oNivel->LeerNumCar();
				$pos1=(25-$tama1)+1;
				$oNivel->nivel="1";
				$tama2 = $oNivel->LeerNumCar();
				$pos2=(25-$tama2)+1;	
				$strcon = $db->Concat("substr(e2.codestpro1,{$pos2},{$tama2})","'-'","substr(e2.codestpro2,{$pos1},{$tama1})");
				$conSelect .=",{$strcon} as codigo,e2.denestpro2 as Descripcion ";
				$conFrom.=",sfp_estpro1 as e1,sfp_estpro2 as e2";
				$conWhere.=" and e2.codestpro1=i.codestpro1 and e2.codestpro2=i.codestpro2  and e2.codemp = i.codemp and e2.ano_presupuesto = i.ano_presupuesto and and e1.codestpro1=i.codestpro1  and e1.codemp = i.codemp and e1.ano_presupuesto = i.ano_presupuesto ";
				break;	
			case '3':	
                                $oNivel=new ConfNivelDao();
				$oNivel->tipo="PR";
				$oNivel->nivel="2";
				$tama1 = $oNivel->LeerNumCar();
				$pos1=(25-$tama1)+1;
				$oNivel->nivel="1";
				$tama2 = $oNivel->LeerNumCar();
				$pos2=(25-$tama2)+1;
                                $oNivel->nivel="3";
				$tama3 = $oNivel->LeerNumCar();
				$pos3=(25-$tama3)+1;
                                $strcon = $db->Concat("substr(e2.codestpro1,{$pos2},{$tama2})","'-'","substr(e2.codestpro2,{$pos1},{$tama1})","'-'","substr(e3.codestpro3,{$pos3},{$tama3})");
                                
				$conSelect .=",{$strcon} as codigo, e2.codestpro2,e2.denestpro2,e1.denestpro1,e1.codestpro1,e3.codestpro3,e3.denestpro3 as descripcion";
				$conFrom.=",sfp_estpro1 as e1,sfp_estpro2 as e2,sfp_estpro3 as e3";
				$conWhere.=" and e3.codestpro1=i.codestpro1 and e3.codestpro2=i.codestpro2 and e3.codestpro3=i.codestpro3  and e3.codemp = i.codemp and e3.ano_presupuesto = i.ano_presupuesto and e2.codestpro1=i.codestpro1 and e2.codestpro2=i.codestpro2  and e2.codemp = i.codemp and e2.ano_presupuesto = i.ano_presupuesto and e1.codestpro1=i.codestpro1  and e1.codemp = i.codemp and e1.ano_presupuesto = i.ano_presupuesto";
				break;	
					
			case '4':	
				$conSelect.=",e1.codestpro1,e1.denestpro1,e2.codestpro2,e2.denestpro2,e3.codestpro3,e3.denestpro3,e4.codestpro4,e4.denestpro4";
				$conFrom.=",sfp_estpro1 as e1,sfp_estpro2 as e2,sfp_estpro3 as e3,sfp_estpro4 as e4";
				$conWhere.=" and e4.codestpro1=i.codestpro1 and e4.codestpro2=i.codestpro2 and e4.codestpro3=i.codestpro3 and e4.codestpro4=i.codestpro4  and e4.codemp = i.codemp and e4.ano_presupuesto = i.ano_presupuesto and e3.codestpro1=i.codestpro1 and e3.codestpro2=i.codestpro2 and e3.codestpro3=i.codestpro3  and e3.codemp = i.codemp and e3.ano_presupuesto = i.ano_presupuesto and e2.codestpro1=i.codestpro1 and e2.codestpro2=i.codestpro2  and e2.codemp = i.codemp and e2.ano_presupuesto = i.ano_presupuesto and e1.codestpro1=i.codestpro1  and e1.codemp = i.codemp and e1.ano_presupuesto = i.ano_presupuesto";
				break;	
			case '5':	
				$conSelect .=",e1.codestpro1,e1.denestpro1,e2.codestpro2,e2.denestpro2,e3.codestpro3,e3.denestpro3,e4.codestpro4,e4.denestpro4,e5.codestpro5,e5.denestpro5";
				$conFrom.=",sfp_estpro1 as e1,sfp_estpro2 as e2,sfp_estpro3 as e3,sfp_estpro4 as e4,sfp_estpro5 as e5";
				$conWhere.=" and e5.codestpro1=i.codestpro1 and e5.codestpro2=i.codestpro2 and e5.codestpro3=i.codestpro3 and e5.codestpro4=i.codestpro4 and e5.codestpro5=i.codestpro5  and e5.codemp = i.codemp and e5.ano_presupuesto = i.ano_presupuesto and
e4.codestpro1=i.codestpro1 and e4.codestpro2=i.codestpro2 and e4.codestpro3=i.codestpro3 and e4.codestpro4=i.codestpro4  and e4.codemp = i.codemp and e4.ano_presupuesto = i.ano_presupuesto and e3.codestpro1=i.codestpro1 and e3.codestpro2=i.codestpro2 and e3.codestpro3=i.codestpro3  and e3.codemp = i.codemp and e3.ano_presupuesto = i.ano_presupuesto and e2.codestpro1=i.codestpro1 and e2.codestpro2=i.codestpro2  and e2.codemp = i.codemp and e2.ano_presupuesto = i.ano_presupuesto and e1.codestpro1=i.codestpro1  and e1.codemp = i.codemp and e1.ano_presupuesto = i.ano_presupuesto";
	break;	
		}
		
		$condicion="{$conSelect} {$conFrom} {$conWhere}";
		return $condicion;
				
	}
	
	
	public function BuscarInt()
	{
		global $db;
		$sql = "select * from {$this->_table} where codest1='{$this->codest1}' and codest2='{$this->codest2}' and codest3='{$this->codest3}' and codest4='{$this->codest4}' and codest5='{$this->codest5}' and codestpro1='{$this->codestpro1}' and codestpro2='{$this->codestpro2}' and codestpro3='{$this->codestpro3}' and codestpro4='{$this->codestpro4}' and codestpro5='{$this->codestpro5}' and {$this->_table}.codemp='{$this->codemp}'";
		
		
		$Rs= $db->Execute($sql);
		return $Rs;

	}
	public function obtenerProblemas()
	{
		$oProb = new intProbDao();
		$rs = $oProb->BuscarProblemas($this->codinte);
		return $rs;

	}
	public function obtenerMetas()
	{
		$oMeta = new intMetasDao();
		$rs = $oMeta->BuscarMetas($this->codinte,$this->codemp);
		return $rs;
	}
	public function obtenerIndicadores()
	{
		$oInd = new intIndiDao();
		$rs = $oInd->BuscarIndicadores($this->codinte,$this->codemp);
		return $rs;
	}
	
	public function obtenerUnidades()
	{
		$oUnidades = new intUniAdDao();
		$rs = $oUnidades->BuscarUnidades($this->codinte);
		return $rs;
	}
	
	public function eliminarUnidad($oBj)
	{
		$Re = $oBj->Eliminar();
		return $Re;
	}
	
	public function eliminarUbicacion($oBj)
	{
		$Re = $oBj->Eliminar();
		return $Re;
	}
	public function eliminarProblemas($oBj)
	{
		$Re = $oBj->Eliminar();
		return $Re;
	}
	
	public function eliminarMetas($oBj)
	{
		$Re = $oBj->Eliminar();
		return $Re;
	}
	
	public function eliminarIndicadores($oBj)
	{
		$Re = $oBj->Eliminar();
		return $Re;
	}
	public function eliminarCuentas($oBj)
	{
		if($oBj->Existe())
		{
			$Re = $oBj->Eliminar();
			return $Re;
		}
		else
		{
			return "2";
		}
		
	}

	public function obtenerUbicaciones()
	{
		$oUbicacion=$this->ObtenerObjetoActual();
		$Ar[0] = $oUbicacion["rs"]->BuscarUbicaciones($this->codinte);
		$Ar[1] = $oUbicacion["nivel"];
		return $Ar;
	}
	
	public function obtenerCuentas()
	{
		$oCuenta=new intGastosDao();
		$rs = $oCuenta->BuscarCuentasGasto($this->codinte);
		return $rs;
	}

	public function ObtenerObjetoActual()
	{
		$oNiveles = new ConfNivelDao();
		$nivel = $oNiveles->ObtenerNivelUb();		
		switch($nivel)
		{
			case '1':
				$oUbicacion = new intUb1Dao();
				break;
			case '2':
				$oUbicacion = new intUb2Dao();
				break;
			case '3':
				$oUbicacion = new intUb3Dao();
				break;
			case '4':
				$oUbicacion = new intUb4Dao();
				break;
			case '5':
				$oUbicacion = new intUb5Dao();
				break;
				
		}
		$ar['rs']=$oUbicacion;
		$ar['nivel']=$nivel;
		return $ar;
	}
	public function IncluirFuente($ofuente)
	{
			$oFuente->Incluir();	
			return $rs;

	}	
	public function LeerAsientos()
	{
		
		global $db;
		//$db->debug=true;
		$oAsiento = new Asientos();
		$oAsiento->codemp=$this->codemp;
		$oAsiento->sig_cuenta=$this->sig_cuenta;
		$oAsiento->ano_presupuesto=$this->ano_presupuesto;
		$oAsiento->codinte=$this->codinte;
		$RsA = $oAsiento->LeerAsientoGasto();
		
		$oAsientoVar = new AsientoVariacionDao();
		$oAsientoVar->sig_cuenta=$this->sig_cuenta; 
		$oAsientoVar->ano_presupuesto=$this->ano_presupuesto;
		$oAsientoVar->codinte=$this->codinte; 
		$RsC = $oAsientoVar->LeerAsientoGastos();
//		$oPlanCuentas = new planUnicoRe();
//		$oPlanCuentas->sig_cuenta=$this->sig_cuenta; 
//		$RsC = $oPlanCuentas->LeerUna();
//		$oPlanCuentas->sig_cuenta=$RsA->fields["sig_cuenta"];
//		$RsC2 = $oPlanCuentas->LeerUna(); 
		$ArAux["variacion"]=$RsA;
		$ArAux["caif"]=$RsC;
		$ArAux["caif2"]=$RsC2;  
		return $ArAux;

	}
}
?>