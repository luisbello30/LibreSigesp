/*
Catalo go de problemas
*/

var gridOnOff = false;
var winOnOff = false;
var datos = null;
var grid = null;
var win = null;
var unavez = false;
var parametros='';
var ruta = '';
var Mygrid="";
var simple ='';
var gridEst='';
var ParamGridTarget='';
rutaProb2 ='../../procesos/sigesp_spe_problemaspr.php';
Ext.onReady(function(){

	

})
function LimpiarPantalla()
{
	gridEstPreSelec.store.removeAll();
	gridIntGastos.store.removeAll();
	gridIntVar.store.removeAll();
}


function PasarDatosGrids2(Registro)
{
		LimpiarPantalla();
		codinte=Registro.get('codinte');
		IdPadre=codinte;
		r=new RecordDefPlaPre
		(
			{
				'codigo':'',
				'descripcion':''				
			}
		);
	   gridEstPreSelec.store.insert(gridEstPreSelec.store.getCount(),r);
	   r.set('codigo',Registro.get('codigo'));
	   r.set('descripcion',Registro.get('descripcion'));
	var myJSONObject =
	{
		"oper": 'buscardetalles',
		"codinte":codinte
	};

	ObjSon=JSON.stringify(myJSONObject);
	//alert(ObjSon);
	parametros = 'ObjSon='+ObjSon; 
	Ext.Ajax.request({
	url : rutaIntepr,
	params : parametros,
	method: 'POST',
	success: function ( resultado, request ){ 
		  datos = resultado.responseText;
				if(datos!='|0')
				{
					  	ArrayObject = datos.split('|');
					  	var DatJsonMetas = eval('(' + ArrayObject[0] + ')');
					  	var DatJsonCuentas = eval('(' + ArrayObject[1] + ')');		  
			  	  		var DatJsonIndis = eval('(' + ArrayObject[2] + ')');
			  	}
	  	   		if(DatJsonCuentas.raiz!=null)
				{
				    gridIntGastos.store.loadData(DatJsonCuentas);
				}
				if(DatJsonMetas.raiz!=null)
				{
				    gridIntVar.store.loadData(DatJsonMetas);
				}	
				if(DatJsonIndis.raiz!=null)
				{
				    gridIntInd.store.loadData(DatJsonIndis);
				}		  
	  }
	  })
}
      
               
function irAgregarEstPre()
{
	ruta ='sigesp_scg_aux_estadoresultadosest.php';
	
	var myJSONObject =
	{
		"oper": 'catalogoEstInt'
	};

	ObjSon=Ext.util.JSON.encode(myJSONObject);
	parametros = 'ObjSon='+ObjSon; 
	Ext.Ajax.request({
	url : ruta,
	params: parametros,
	method:'POST',
	success: function ( resultado, request ){
		  datos = resultado.responseText; 
		  var myObject = eval('(' + datos + ')');
		 if(myObject.raiz==null)
		 {
			var myObject={"raiz":[{"codigo":'',"descripcion":''}]};
		 }
		
		var RecordDefEst = Ext.data.Record.create([
			{name: 'codigo'},     
			{name: 'codestpro1'},
			{name: 'denestpro1'},
			{name: 'estcla'},
			{name: 'codestpro2'},
			{name: 'denestpro2'},	
			{name: 'codestpro3'},
			{name: 'denestpro3'},
			{name: 'codinte'}
		]);

            gridEst = new Ext.grid.GridPanel({
			width:700,
			title:'Para seleccionar más de una estructura presupuestaria, selecciónelas en la tabla manteniendo presionado la tecla Ctrl del teclado',
			height:400,
			applyTo:'gridestructuras',
			autoScroll:true,
            border:true,
            ds: new Ext.data.Store({
			proxy: new Ext.data.MemoryProxy(myObject),
			reader: new Ext.data.JsonReader({
			    root: 'raiz',                
			     id: "id"   
            }
			,
               RecordDefEst
			)
			,
			data: myObject
             })
             ,
                   cm: new Ext.grid.ColumnModel([
                        new Ext.grid.CheckboxSelectionModel(),
                        {header: "Código", width: 70, sortable: true,   dataIndex: 'codestpro1'},
                        {header: "Descripción", width: 150, sortable: true, dataIndex:'denestpro1'},
                        {header: "Código", width: 70, sortable: true,   dataIndex: 'codestpro2'},
                        {header: "Descripción", width: 150, sortable: true, dataIndex:'denestpro2'},
                        {header: "Código", width: 70, sortable: true,   dataIndex: 'codestpro3'},
                        {header: "Descripción", width: 180, sortable: true, dataIndex:'denestpro3'},
			           ])
			           ,
			           sm:new Ext.grid.CheckboxSelectionModel({singleSelect:false,checkOnly:true}),
						stripeRows: true
                   }); 
    }
    ,
    failure: function ( resultado, request) { 
          Ext.MessageBox.alert('Error', resultado.responseText); 
     }
   });
};

irAgregarEstPre()