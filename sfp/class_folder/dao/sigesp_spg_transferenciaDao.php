<?php
require_once('sigesp_conexiondin_dao.php');
require_once('sigesp_sfp_estprog1Dao.php');
require_once('sigesp_sfp_estprog2Dao.php');
require_once('sigesp_sfp_estprog3Dao.php');
require_once('sigesp_sfp_estprog4Dao.php');
require_once('sigesp_sfp_estprog5Dao.php');
require_once('sigesp_spg_estprog1Dao.php');
require_once('sigesp_sfp_planingresoDao.php');
require_once('sigesp_sfp_intGastosDao.php');
require_once('sigesp_spg_spicuentasDao.php');
require_once('sigesp_spg_spgcuentasDao.php');
require_once('sigesp_sfp_empresasDao.php');
require_once('sigesp_sfp_empresaDao.php');
require_once('sigesp_sfp_estAdminDao.php');
require_once('sigesp_spg_estadminDao.php');
require_once('sigesp_spg_dtestadminDao.php');
require_once('sigesp_sfp_inteAdDao.php');

class Transferencia 
{
	public function CrearConexion($ArJson)
	{
		try
		{
			$oConexion = new Conexion();
			$oConexion->gestor=$ArJson->gestor;
			$oConexion->host=$ArJson->hostname;
			$oConexion->user=$ArJson->login;
			$oConexion->pass=$ArJson->password;
			$oConexion->base=$ArJson->database;
			$con2 = $oConexion->crearconexion();
			return $con2;				
		}
		catch (Exception $e) 
		{
    		return "0";
		}
	}
	
	public function TransferirDatos($ArJson)
	{
//		$this->actualizarPresupuesto($ArJson);

		$this->PasarUnidadAdmin($ArJson);
		$this->PasacuentasIngreso($ArJson);
		$arrEps = array('spg_ep1','spg_ep2','spg_ep3','spg_ep4','spg_ep5');
		for($i=0;$i<count($arrEps);$i++)
		{
			$this->Pasarep($ArJson,$arrEps[$i]);
		}
		$this->PasacuentasGasto($ArJson);
		$this->PasardtUnidadAdmin($ArJson);
		
	}	
	
	public function Pasarep($ArJson,$tabla)
	{
		switch($tabla)
		{
			case 'spg_ep1':
				$EpOrigen = new estprog1Dao();
			break;
			case 'spg_ep2':
				$EpOrigen = new estprog2Dao();
			break;
			case 'spg_ep3':
				$EpOrigen = new estprog3Dao();
			break;
			case 'spg_ep4':
				$EpOrigen = new estprog4Dao();
			break;
			case 'spg_ep5':
				$EpOrigen = new estprog5Dao();
			break;
		}
		$rs  = 	$EpOrigen->LeerTodas();
		$db1 = $this->CrearConexion($ArJson);
		spgestprogDao::IniciarTran($db1);
		while(!$rs->EOF)
		{
			$EpDestino = new spgestprogDao($tabla);
			$this->pasardatos($rs->fields,$EpDestino);
			$EpDestino->Incluir($db1);
			$rs->MoveNext();
		}
		spgestprogDao::CompletarTran($db1);
	}
	
	public function PasarEmpresa($ArJson)
	{
		$oOrigen =  new empresas();
		$rs  = 	$oOrigen->LeerTodos();
		$db1 = $this->CrearConexion($ArJson);
		spgestprogDao::IniciarTran($db1);
		while(!$rs->EOF)
		{
			$EpDestino = new Empresa();
			$this->pasardatos($rs->fields,$EpDestino);
			$EpDestino->Incluir($db1);
			$rs->MoveNext();
		}
		$res = spgestprogDao::CompletarTran($db1);
		if($res=="1")
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function PasarUnidadAdmin($ArJson)
	{
		$oOrigen =  new EstAdmin();
		$rs  = 	$oOrigen->LeerEstadmin();
		$db1 = $this->CrearConexion($ArJson);
		spgestprogDao::IniciarTran($db1);
		while(!$rs->EOF)
		{
			$EpDestino = new spgestadminDao();
			$this->pasardatos($rs->fields,$EpDestino);
			$EpDestino->Incluir($db1);
			$rs->MoveNext();
		}
		$res = spgestprogDao::CompletarTran($db1);
		if($res=='0')
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	
	public function PasardtUnidadAdmin($ArJson)
	{
		$oOrigen =  new intUniAdDao();
		$rs  = 	$oOrigen->Leerdtunidades();
		$db1 = $this->CrearConexion($ArJson);
		spgestprogDao::IniciarTran($db1);
		while(!$rs->EOF)
		{
			$EpDestino = new spgedtstadminDao();
			$this->pasardatos($rs->fields,$EpDestino);
			$EpDestino->Incluir($db1);
			$rs->MoveNext();
		}
		$res = spgestprogDao::CompletarTran($db1);
		if($res=='0')
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	
	public function PasacuentasIngreso($ArJson)
	{
		$oOrigen =  new planIngreso();
		$rs  = 	$oOrigen->LeerDistribucionTran();
		$db1 = $this->CrearConexion($ArJson);
		spgestprogDao::IniciarTran($db1);
		while(!$rs->EOF)
		{
			$EpDestino = new spicuentasDao();
			$this->pasardatos($rs->fields,$EpDestino);
			//$EpDestino->sc_cuenta = leerCuentaContable();
			$EpDestino->sc_cuenta = '';
			$EpDestino->nivel = '3';
			if($EpDestino->referencia==NULL)
			{
				$EpDestino->referencia='';
			}
			$EpDestino->Incluir($db1);
			$rs->MoveNext();
		}
		spgestprogDao::CompletarTran($db1);
	}
	
	
	public function PasacuentasGasto($ArJson)
	{
		$oOrigen =  new intGastosDao();
		$rs  = 	$oOrigen->LeerDistribucion();
		$db1 = $this->CrearConexion($ArJson);
		spgestprogDao::IniciarTran($db1);
		while(!$rs->EOF)
		{
			$EpDestino = new spgcuentasDao();
			$this->pasardatos($rs->fields,$EpDestino);
			//$EpDestino->sc_cuenta = leerCuentaContable();
			$EpDestino->sc_cuenta = '';
			if($EpDestino->referencia==NULL)
			{
				$EpDestino->referencia='';
			}
			$EpDestino->Incluir($db1);
			$rs->MoveNext();
		}
		spgestprogDao::CompletarTran($db1);
	}
	
	function uf_total_niveles($as_formato)
	{
			$li_count=0;
			$i=0;
			$arr=str_split($as_formato);
			$arr2=split("-",$as_formato);
			$tot=count($arr);
			for($i=0;$i<$tot;$i++) 
			{
				if($arr[$i]=="-")
				{
					$li_count=$li_count+1;
				}
			}
			$arr=array("cantidad"=>$li_count+1,"niveles"=>$arr2);
		    return $arr;	
	}// end function uf_total_niveles
	
	
	public function actualizarPresupuesto($ArJson)
	{
		$db1 = $this->CrearConexion($ArJson);
		$sql="select * from spg_cuentas";
		$rs = $db1->Execute($sql);
		//ver($rs);
		if($rs!=false && $rs->RecordCount()>0)
		{
			
			while(!$rs->EOF)
			{
					$cuentActual = $rs->fields["spg_cuenta"];
					$tamCuentActual = strlen(trim($cuentActual));
					$oEmp = new empresas();
					$rsEmp = $oEmp->LeerUno();
					$formatogastos = $rsEmp->fields["formpre"];
					$formatoingresos = $rsEmp->fields["formspi"];		
					$Grupocuenta = substr($rs->fields["spg_cuenta"],0,1);
					if($Grupocuenta=='4')
					{
						$formato = $formatoingresos;
					}
					else
					{
						$formato = $formatogastos;
					}
					
					$Cantformatogen = strlen(trim(str_replace("-","",$formato))); 
					$totalNiveles = $this->uf_total_niveles($formato);
					$acuNiveles=0;
					$acuNivelAnt=0;
					$referencia="";
					if($totalNiveles["cantidad"]==4)
					{
						$auxtotal = $totalNiveles["cantidad"];
					}
					elseif($totalNiveles["cantidad"]==5 && $tamCuentActual=='9')
					{
						$auxtotal = $totalNiveles["cantidad"]-1;
					}
					elseif($totalNiveles["cantidad"]==5 && $tamCuentActual!='9')
					{
						$auxtotal = $totalNiveles["cantidad"];
					}
					spgcuentasDao::IniciarTran($db1);
					$inicio=0;
					for($i=0;$i<$auxtotal;$i++)										
					{	
						$oCuenta = new spgcuentasDao();
					//	$rs = $oCuenta->leerEstructuras($db1,substr($cuentActual,$inicio,count($totalNiveles["niveles"][$i]);
						if($rs!=false)
						{
							$this->pasardatos($rs->fields,$oCuenta);
							$oCuenta->Incluir($db1);	
						}
						else
						{
							echo "ejecucion fallida archivo:sigesp_spg_transferenciaDao.php";
							die();
						}		
					}
					$res = spgcuentasDao::CompletarTran($db1);
					if($res==true)
					{
						return "1";
					}	
					else
					{
						return "0";
					}	
				$rs->MoveNext();			
			}
		}
	}
	
	public function  pasardatos($origen,&$destino)
	{
		foreach($origen as $camporigen=>$valororigen)
		{
			foreach($destino as $campodestino=>$valordestino)
			{
				if($camporigen==$campodestino && !is_numeric($camporigen) && !is_numeric($campodestino))
				{
					if($origen[$camporigen]!='')
					{
						$destino->$campodestino = $origen[$camporigen];		
					}
					elseif(substr($camporigen,0,9)=='denestpro')
					{
						$destino->$campodestino = 'NINGUNO';	
					}
				}
				else
				{
					continue;
				}
			}
		}
	}
}
?>