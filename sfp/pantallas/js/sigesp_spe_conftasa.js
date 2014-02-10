
var gridMontos=null;
var gridMontos2 = null;
var grid2=null
var tabs = null;
var gridTasa=null;
ruta2 ='../../procesos/sigesp_sfp_conftasapr.php';
pantalla ='sigesp_spe_conftasa.php';

Ext.onReady(function(){
ObtenerSesion(ruta2,pantalla);
function getobject2()
{
	var myJSONObject ={
		"oper": 'datostasas2'
	};	
		
	ObjSon=JSON.stringify(myJSONObject);
	parametros = 'ObjSon='+ObjSon; 
	Ext.Ajax.request({
	url : ruta2,
	params : parametros,
	method: 'POST',
	success: function(resultado, request){
	datos = resultado.responseText;
	if(datos!='')
	{
		datos=datos.split("|");
		DatosNuevo2 = eval('(' + datos[1] + ')');
		if(DatosNuevo2.raiz==null)
		{
			var DatosNuevo={"raiz":[{"spi_cuenta":'',"denominacion":'',"montoGlobal":'',"NuevoRegistro":''}]};
		}
		else
		{
			arrObj = gridMontos2.store.getRange(0,11)
			for(i=0;i<DatosNuevo2.raiz.length;i++)
			{
				auxMes = DatosNuevo2.raiz[i].mes
				for(j=0;j<arrObj.length;j++)
				{
					if(auxMes==numerodemes(arrObj[j].get('numes')))
					{
						arrObj[j].set('monto',DatosNuevo2.raiz[i].monto);
					}
				}
			}
			gridMontos2.store.commitChanges();	
		}
	}
  }
});      	 					     	 					
}


function getobject()
{		
		var agregar = new Ext.Action(
		{
			text: 'Agregar',
			//handler: irAgregar,
			iconCls: 'bmenuagregar',
        	tooltip: 'Agregar cuenta'
		});
		
		var modificar = new Ext.Action(
		{
			text: 'Modificar',
	//		handler: getActualizar,
			iconCls: 'bmenumodif',
        	tooltip: 'Modificar Monto Asignado'
		});
		
		var quitar = new Ext.Action(
		{
			text: 'Quitar',
	//		handler: irQuitar,
			iconCls: 'bmenuquitar',
        	tooltip: 'Eliminar Cuenta'
		});
		
		
		Meses =
		[
	        ['m','Enero',0],
	        ['l','Febrero',0],
	        ['k','Marzo',0],
	        ['j','Abril',0],
	        ['i','Mayo',0],
	        ['h','Junio',0],
	        ['g','Julio',0],
	        ['f','Agosto',0],
	        ['e','Septiembre',0],
	        ['d','Octubre',0],
	        ['c','Noviembre',0],
	        ['b','Diciembre',0],
	        ['a','Total',0]
		]	
		
		var storeMeses = new Ext.data.SimpleStore({
			fields: 
			[
				{name: 'numes'},
				{name: 'mes'},
				{name: 'monto'}
			]
		});
			
			storeMeses.loadData(Meses);
			storeMeses.sort('numes','DESC');	
			gridMontos = new Ext.grid.EditorGridPanel({
 			width:385,
 			style:'margin-left:146px',
 			title:'Montos Mensuales',
			autoScroll:true,
           border:true,
           ds:storeMeses,
           cm: new Ext.grid.ColumnModel([
           
           {header: "Mes", width:40,sortable:false,dataIndex:'mes'},
		   {header: "Porcentaje", width: 50,dataIndex: 'monto',editor: new Ext.form.TextField({allowBlank: false,allowDecimals:false,id:'montomes'})}							
		
		]),
		selModel: new Ext.grid.RowSelectionModel({singleSelect:false}),
             viewConfig: {
            forceFit:true	
            }
           ,
		autoHeight:true,
		stripeRows: true
		 });

	//	gridMontos.render('grid-example');
	//	gridMontos.addListener('celldblclick',getGridMontos);		
 		Ext.state.Manager.setProvider(new Ext.state.CookieProvider());        
      
 		  ForMontos = new Ext.FormPanel({
 			  labelWidth:140, // label settings here cascade unless overridden,
 			  labelAlign:'right',
 			  style:"padding-top:10px",
 			  applyTo:"form1",
 			  height:550,  
 			  items:[
 				{
 				  xtype:'textfield', 
 				  fieldLabel: 'Cuenta Presupuestaria',
 				  name: 'Cuenta',
 				  readOnly:false,
 				  id: 'cuentapre',
 				  maxLength:45,
 				  width: 580
 			    }
 				,
 				
 					gridMontos
 				,
 				{
 	 				  xtype:'hidden', 
 	 				  id: 'codcuenta'
 	 			}
 				
 				
 			    ]
 		  })
		var storeMeses2 = new Ext.data.SimpleStore({
			fields: 
			[
				{name: 'numes'},
				{name: 'mes'},
				{name: 'monto'}
			]
		});
			
		   storeMeses2.loadData(Meses);
		   storeMeses2.sort('numes','DESC');	
		   gridMontos2 = new Ext.grid.EditorGridPanel({
 		   width:400,
		   style:'margin-left:170px',
 		   title:'Montos Mensuales',
		   autoScroll:true,
           border:true,
           ds:storeMeses2,
           cm: new Ext.grid.ColumnModel([
           
           {header: "Mes", width:40,sortable:false,dataIndex:'mes'},
		   {header: "Porcentaje", width: 50,dataIndex: 'monto',editor: new Ext.form.TextField({allowBlank: false,allowDecimals:false,id:'montomes'})}							
		
		]),
		selModel: new Ext.grid.RowSelectionModel({singleSelect:false}),
             viewConfig: {
            forceFit:true	
            }
           ,
		autoHeight:true,
		stripeRows: true
		 });

		gridMontos2.render('grid-example2');
	//	gridMontos.addListener('celldblclick',getGridMontos);		
 		Ext.state.Manager.setProvider(new Ext.state.CookieProvider());        
      
      	 					tabs = new Ext.TabPanel({
                            border:true,
                            activeTab:0,
                            width:780,
                            height:480,
                        	style:'position:absolute;left:120px;top:50px',
                            renderTo:'tabPrin1',
                            items:[{
	                            contentEl:'form1',
								title: 'Tasa de IPC',
								id:'0',
								height:400,
								autoScroll:true
                            }
                            ,
							{
                                contentEl:'grid-example2',
                                title: 'Otros Impuestos',
                                height:400,
                                autoScroll:true,
                                id:'1'
                            }							
                            ] 
        })

      
        var viewport = new Ext.Viewport({
            layout:'border',
            items:[
                new Ext.BoxComponent({ // raw
                    region:'north',
                    el: 'norte',
                    height:50
                })
                ,
                new Ext.BoxComponent({ // raw
                    region:'center',
                    el: 'tabPrin1',
                     height:750,
                    style:'padding-top:60px;padding-left:110px'
                })
				
           	]
         })
      	 					
		Ext.get('cuentapre').on('dblclick', function()
			{
				irAgregarCuentas();
			}
		)
		
		
		 gridMontos.on('afteredit',function(e){
			  e.record.commit();
		 })   
		 
		 gridMontos2.on('afteredit',function(e){
			  e.record.commit();
		 })   


}




function ActualizarDataCuentas(criterio,valor)
{
	var myJSONObject ={
		"oper": 'plancuentas', 
		"criterio":criterio, 
		"cadena": valor
	};	
	
	ObjSon=JSON.stringify(myJSONObject);
	parametros = 'ObjSon='+ObjSon; 
	Ext.Ajax.request({
	url : ruta2,
	params : parametros,
	method: 'POST',
	success: function ( resultado, request ) 
	{ 
		datos = resultado.responseText;	  
		//alert(datos);
		DatosNuevo = eval('(' + datos + ')');
		 if(DatosNuevo.raiz!=null)
		 {	
	
		
		  }
		else
		 {
			var DatosNuevo={"raiz":[{"spi_cuenta":'',"denominacion":'',"montoGlobal":'',"NuevoRegistro":''}]};
		 }
		grid2.store.loadData(DatosNuevo);
	}
});
}





function irAgregarCuentas()
{
	
	  Ext.MessageBox.show({
           msg: 'Por Favor Espere',
           title: 'Cargando Datos',
           progressText: 'Cargando Datos',
           width:300,
           wait:true,
           waitConfig: {interval:40},
           animEl: 'mb7'
       });

		  
 		var myObject = {"raiz":[{"codigo":'',"denominacion":''}]};
		var RecordDef = Ext.data.Record.create([
			{name:'codigo'},     
			{name:'denominacion'},
			{name:'codigodebe'},
			{name:'dendebe'},	
			{name:'codigohaber'},
			{name:'denhaber'},
			{name:'codcaif'},
			{name:'codvarhaber'},	
			{name:'codvardebe'},
			{name:'denvarhaber'},
			{name:'denvardebe'},
			{name:'monto_anest'},
			{name:'monto_anreal'}
			]);
       

            grid2 = new Ext.grid.GridPanel({
			width:770,
			autoScroll:true,
            border:true,
            ds: new Ext.data.Store({
		//	proxy: new Ext.data.MemoryProxy(myObject),
			reader: new Ext.data.JsonReader({
			    root:'raiz',                // The property which contains an Array of row objects
			     id: "id"  
			},
                    RecordDef
			     
			      ),
				data: myObject
                        }),
                        cm: new Ext.grid.ColumnModel([
                            new Ext.grid.CheckboxSelectionModel(),
                            {header: "Código", width: 20, sortable: true,   dataIndex: 'codigo'},
                            {header: "Denominación", width: 50, sortable: true, dataIndex: 'denominacion'}]),
						sm:new Ext.grid.CheckboxSelectionModel({singleSelect:false}),
                        viewConfig:{
                            forceFit:true
                        },
			autoHeight:true,
			stripeRows: true
        });

	
	function vercuentas()
	{
		 ActualizarDataCuentas('sig_cuenta','');
	}
	var MostrarCuentas = new Ext.Action(
	{
		text: 'Cuentas',
		handler: vercuentas,
		iconCls: 'bmenuagregar',
        tooltip: 'Mostrar Todas las cuentas'
	});	

		simpleCuentasIn = new Ext.FormPanel({
        labelWidth:75, // label settings here cascade unless overridden
        //url:'save-form.php',
        frame:true,
        bbar:[MostrarCuentas],
        title: 'Búsqueda',
        bodyStyle:'padding:5px 5px 0;height:50px',
        width: 350,
		height:120,
        defaults:{width: 230},
        defaultType: 'textfield',
		items: [{
                fieldLabel: 'Código',
                name:'codigoCuenta',
				id:'codigoCuenta',
				changeCheck: function(){
							  var v = this.getValue();
							 ActualizarDataCuentas('sig_cuenta',v);
							if(String(v) !== String(this.startValue)){
								this.fireEvent('change', this, v, this.startValue);
							} 
							 },
							 
							initEvents : function()
							{
								AgregarKeyPress(this);
							}
               
            	},{
                fieldLabel: 'Denominacion',
                name: 'denCuenta',
				changeCheck: function(){
							  var v = this.getValue();
							 ActualizarDataCuentas('denominacion',v);
							if(String(v) !== String(this.startValue))
							{
								this.fireEvent('change', this, v, this.startValue);
							} 
							 }
            				,
							 
							initEvents : function()
							{
								AgregarKeyPress(this);
							}
            }
             ]
		});
	
	   
                   winCatCuentas = new Ext.Window(
                   {
	                 title: 'Catálogo de Cuentas de Ingresos',
			   		 autoScroll:true,
	                 width:800,
	                 height:400,
	                 modal: true,
	                 plain: false,
	                 closable:false,
	                 items:[simpleCuentasIn,grid2],
	                 buttons: [{
                     text:'Aceptar',  
                     handler: function()
                     {
	                	 
	                	 seleccion =grid2.getSelectionModel().getSelected();
	                	 if(seleccion)
	                	 {
	                		 coden=seleccion.get('codigo')+' '+seleccion.get('denominacion');
	                		 codigo=seleccion.get('codigo');
	                		 
	                		 Ext.get('codcuenta').dom.value=codigo;
	                		 Ext.get('cuentapre').dom.value=coden;
	                	 }
     
	                  	winCatCuentas.destroy();
	                  	simpleCuentasIn.destroy();
	                  	grid2.destroy(); 
	                  	winCatCuentas="";
	                  	simpleCuentasIn="";
	                  		
                     }
                    }
                    ,
                    {
                     text: 'Salir',
                     handler: function()
                     {
	                        winCatCuentas.destroy();
	                  		simpleCuentasIn.destroy();
	                  		grid2.destroy();
	                  		winCatCuentas="";
		                  	simpleCuentasIn="";                		
                     }
                    }]
                   });
                   
                   Ext.MessageBox.hide();
                   winCatCuentas.show();
                  
	               
	               
        //},
//        failure: function ( resultado, request){ 
//                   Ext.MessageBox.alert('Error', resultado.responseText); 
//        }
	
	
  // });
	
}
//);






function ActualizarDataCuentas2(criterio,valor)
{
	var myJSONObject ={
		"oper": 'datostasas', 
		"criterio":criterio, 
		"cadena": valor
	};	
	
	ObjSon=JSON.stringify(myJSONObject);
	parametros = 'ObjSon='+ObjSon; 
	Ext.Ajax.request({
	url : ruta2,
	params : parametros,
	method: 'POST',
	success: function ( resultado, request ) 
	{ 
		datos = resultado.responseText;	  
		//alert(datos);
		DatosNuevo = eval('(' + datos + ')');
		 if(DatosNuevo.raiz!=null)
		 {	
	
		
		  }
		else
		 {
			var DatosNuevo={"raiz":[{"spi_cuenta":'',"denominacion":'',"montoGlobal":'',"NuevoRegistro":''}]};
		 }
		 gridTasa.store.loadData(DatosNuevo);
	}
});
}



function irAgregarCuentas2()
{
	
	  Ext.MessageBox.show({
           msg: 'Por Favor Espere',
           title: 'Cargando Datos',
           progressText: 'Cargando Datos',
           width:300,
           wait:true,
           waitConfig: {interval:40},
           animEl: 'mb7'
       });

		  
 		var myObject = {"raiz":[{"codigo":'',"denominacion":''}]};
		var RecordDef = Ext.data.Record.create([
			{name:'codigo'},     
			{name:'denominacion'},
			{name: 'enero'},
			{name: 'febrero'},
			{name: 'marzo'},
			{name: 'abril'},
			{name: 'mayo'},
			{name: 'junio'},
			{name: 'julio'},
			{name: 'agosto'},
			{name: 'septiembre'},
			{name: 'octubre'},
			{name: 'noviembre'},
			{name: 'diciembre'}
			]);
       
            gridTasa = new Ext.grid.GridPanel({
			width:770,
			autoScroll:true,
            border:true,
            ds: new Ext.data.Store({
		//	proxy: new Ext.data.MemoryProxy(myObject),
			reader: new Ext.data.JsonReader({
			    root:'raiz',                // The property which contains an Array of row objects
			     id: "id"  
			},
                    RecordDef
			     
			      ),
				data: myObject
                        }),
                        cm: new Ext.grid.ColumnModel([
                            new Ext.grid.CheckboxSelectionModel(),
                            {header: "Código", width: 20, sortable: true,   dataIndex: 'codigo'},
                            {header: "Denominación", width: 50, sortable: true, dataIndex: 'denominacion'}]),
						sm:new Ext.grid.CheckboxSelectionModel({singleSelect:false}),
                        viewConfig:{
                            forceFit:true
                        },
			autoHeight:true,
			stripeRows: true
        });

	
	function vercuentas()
	{
		 ActualizarDataCuentas('sig_cuenta','');
	}
	var MostrarCuentas = new Ext.Action(
	{
		text: 'Cuentas',
		handler: vercuentas,
		iconCls: 'bmenuagregar',
        tooltip: 'Mostrar Todas las cuentas'
	});	

		simpleCuentasIn2 = new Ext.FormPanel({
        labelWidth:75, // label settings here cascade unless overridden
        //url:'save-form.php',
        frame:true,
        bbar:[MostrarCuentas],
        title: 'Búsqueda',
        bodyStyle:'padding:5px 5px 0;height:50px',
        width: 350,
		height:120,
        defaults:{width: 230},
        defaultType: 'textfield',
		items: [{
                fieldLabel: 'Código',
                name:'codigoCuenta',
				id:'codigoCuenta',
				changeCheck: function(){
							  var v = this.getValue();
							 ActualizarDataCuentas2('sig_cuenta',v);
							if(String(v) !== String(this.startValue)){
								this.fireEvent('change', this, v, this.startValue);
							} 
							 },
							 
							initEvents : function()
							{
								AgregarKeyPress(this);
							}
               
            	},{
                fieldLabel: 'Denominacion',
                name: 'denCuenta',
				changeCheck: function(){
							  var v = this.getValue();
							 ActualizarDataCuentas2('denominacion',v);
							if(String(v) !== String(this.startValue))
							{
								this.fireEvent('change', this, v, this.startValue);
							} 
							 }
            				,
							 
							initEvents : function()
							{
								AgregarKeyPress(this);
							}
            }
             ]
		});
	
		
		
                   winCatCuentas2 = new Ext.Window(
                   {
	                 title:'Catálogo de Cuentas de Ingresos',
			   		 autoScroll:true,
	                 width:800,
	                 height:400,
	                 modal: true,
	                 plain: false,
	                 closable:false,
	                 items:[simpleCuentasIn2,gridTasa],
	                 buttons: [{
                     text:'Aceptar',  
                     handler: function()
                     {
	                	 seleccion =gridTasa.getSelectionModel().getSelected();
	                	 if(seleccion)
	                	 {
	                		 coden=seleccion.get('codigo')+' '+seleccion.get('denominacion');
	                		 codigo=seleccion.get('codigo');
	                		 Ext.get('codcuenta').dom.value=codigo;
	                		 Ext.get('cuentapre').dom.value=coden;
	                		 
	                			Meses =
	                				[
	                			        ['m','Enero',gridTasa.getSelectionModel().getSelected().get('enero')],
	                			        ['l','Febrero',gridTasa.getSelectionModel().getSelected().get('febrero')],
	                			        ['k','Marzo',gridTasa.getSelectionModel().getSelected().get('marzo')],
	                			        ['j','Abril',gridTasa.getSelectionModel().getSelected().get('abril')],
	                			        ['i','Mayo',gridTasa.getSelectionModel().getSelected().get('mayo')],
	                			        ['h','Junio',gridTasa.getSelectionModel().getSelected().get('junio')],
	                			        ['g','Julio',gridTasa.getSelectionModel().getSelected().get('julio')],
	                			        ['f','Agosto',gridTasa.getSelectionModel().getSelected().get('agosto')],
	                			        ['e','Septiembre',gridTasa.getSelectionModel().getSelected().get('septiembre')],
	                			        ['d','Octubre',gridTasa.getSelectionModel().getSelected().get('octubre')],
	                			        ['c','Noviembre',gridTasa.getSelectionModel().getSelected().get('noviembre')],
	                			        ['b','Diciembre',gridTasa.getSelectionModel().getSelected().get('diciembre')]
	                				]	
	                				

	                				gridMontos.store.loadData(Meses);
	                		 
	                		 
	                		 
	                	 }
	                  	winCatCuentas2.destroy();
	                  	simpleCuentasIn2.destroy();
	                  	gridTasa.destroy(); 
	                  	winCatCuentas2="";
	                  	simpleCuentasIn="";
	                  		
                     }
                    }
                    ,
                    {
                     text: 'Salir',
                     handler: function()
                     {
	                        winCatCuentas2.destroy();
	                  		simpleCuentasIn2.destroy();
	                  		gridTasa.destroy();
	                  		winCatCuentas2="";
		                  	simpleCuentasIn="";                		
                     }
                    }]
                   });
                   
                   Ext.MessageBox.hide();
                   winCatCuentas2.show();
                  	               
	               
        //},
//        failure: function ( resultado, request){ 
//                   Ext.MessageBox.alert('Error', resultado.responseText); 
//        }
	
	
  // });
                   

	
}
//);



Ext.get('BtnBuscar').on('click',irAgregarCuentas2)





function actualizardatos()
{
	var myJSONObject ={
		"oper": 'datostasas'
	};	
		
	ObjSon=JSON.stringify(myJSONObject);
	parametros = 'ObjSon='+ObjSon; 
	Ext.Ajax.request({
	url : ruta2,
	params : parametros,
	method: 'POST',
	success: function(resultado, request){ 
	datos = resultado.responseText;
	if(datos!='')
	{
		datos=datos.split("|");
		DatosNuevo1 = eval('(' + datos[0] + ')');
		DatosNuevo2 = eval('(' + datos[1] + ')');
		if(DatosNuevo1.raiz==null)
		{
			var DatosNuevo={"raiz":[{"spi_cuenta":'',"denominacion":'',"montoGlobal":'',"NuevoRegistro":''}]};
		}
		else
		{
			arrObj = gridMontos.store.getRange(1,11)
			for(i=0;i<DatosNuevo1.raiz.length;i++)
			{
				auxMes = DatosNuevo1.raiz[i].mes
				for(j=0;j<arrObj.length;j++)
				{
					if(auxMes==numerodemes(arrObj[j].get('numes')))
					{
						arrObj[j].set('monto',DatosNuevo1.raiz[i].monto);
					}
				}
			}
			gridMontos.store.commitChanges();
			arrObj = gridMontos2.store.getRange(1,11)
			for(i=0;i<DatosNuevo2.raiz.length;i++)
			{
				auxMes = DatosNuevo2.raiz[i].mes
				for(j=0;j<arrObj.length;j++)
				{
					if(auxMes==numerodemes(arrObj[j].get('numes')))
					{
						arrObj[j].set('monto',DatosNuevo2.raiz[i].monto);
					}
				}
			}
			gridMontos2.store.commitChanges();	
		}
	}
  }
});	
}


function obtenersesion()
{
	var myJSONObject =
	{
		"oper": 'leersesion'
	};	
	
	ObjSon=JSON.stringify(myJSONObject);
	//alert(ObjSon);
	//return false;
	parametros = 'ObjSon='+ObjSon;
	Ext.Ajax.request
	({
		url :ruta2,
		params : parametros,
		method: 'POST',
		success: function (resultado, request) 
		{
			 datos = resultado.responseText;
			 if(datos!='')
			 { 	
			 	Sesion=Ext.util.JSON.decode(datos);
			 }
		}
	})
}


Ext.get('BtnSalir').on('click',function()
{
	location.href='sigesp_windowblank2.php';
})

function BlanquearMontoEq(Obj)
{
	Obj.set('monto',0000);
	Obj.set('cobrado',0000);
	Obj.set('porcobrar',0000);
}


function irQuitar()
{
	var selectedKeys = grid.selModel.selections.keys;
        if(selectedKeys.length > 0) {
            Ext.Msg.confirm('Mensaje','Realmente desea eliminar el registro?', deleteRecord);
        } else {
            Ext.Msg.alert('Mensaje','Seleccione un registro para eliminar');
        }

}
//);

 
 

function AgregarKeyPress(Obj)
{
		Ext.form.TextField.superclass.initEvents.call(Obj);
		if(Obj.validationEvent == 'keyup')
		{
			Obj.validationTask = new Ext.util.DelayedTask(Obj.validate, Obj);
			Obj.el.on('keyup', Obj.filterValidation, Obj);
		}
		else if(Obj.validationEvent !== false)
		{
			Obj.el.on(Obj.validationEvent, Obj.validate, Obj, {buffer: Obj.validationDelay});
		}
		if(Obj.selectOnFocus || Obj.emptyText)
		{
			Obj.on("focus", Obj.preFocus, Obj);
			if(Obj.emptyText)
			{
				Obj.on('blur', Obj.postBlur, Obj);
				Obj.applyEmptyText();
			}
		}
		if(Obj.maskRe || (Obj.vtype && Obj.disableKeyFilter !== true && (Obj.maskRe = Ext.form.VTypes[Obj.vtype+'Mask']))){
			Obj.el.on("keypress", Obj.filterKeys, Obj);
		}
		if(Obj.grow)
		{
			Obj.el.on("keyup", Obj.onKeyUp,  Obj, {buffer:50});
			Obj.el.on("click", Obj.autoSize,  Obj);
		}
			Obj.el.on("keyup", Obj.changeCheck, Obj);
}

obtenersesion();
getobject();
getobject2();

Ext.get('BtnNuevo').on('click',function()
{
	location.href='sigesp_spe_conftasa.php'
})


Ext.get('BtnGrabar').on('click',function(){

	if(tabs.getActiveTab().id=='0')
	{
		tipo='IPC';
		cuenta=Ext.get('codcuenta').dom.value;
		numero=gridMontos.store.getCount()-1
		Registros = gridMontos.store.getRange(0,numero);
	}
	else
	{
		cuenta='000000000';
		tipo='OTR';
		numero=gridMontos2.store.getCount()-1
		Registros = gridMontos2.store.getRange(0,numero);
	}
	
	
	reg="{'oper':'grabarconf','movimientos':[";	
			reg=reg+"{'tipotasa':'"+tipo+"','enero':'"+ue_formato_calculo(Registros[0].get('monto'))+"','febrero':'"+ue_formato_calculo(Registros[1].get('monto'))+"','marzo':'"+ue_formato_calculo(Registros[2].get('monto'))+"','abril':'"+ue_formato_calculo(Registros[3].get('monto'))+"','mayo':'"+ue_formato_calculo(Registros[4].get('monto'))+"','junio':'"+ue_formato_calculo(Registros[5].get('monto'))+"','julio':'"+ue_formato_calculo(Registros[6].get('monto'))+"','agosto':'"+ue_formato_calculo(Registros[7].get('monto'))+"','septiembre':'"+ue_formato_calculo(Registros[8].get('monto'))+"','octubre':'"+ue_formato_calculo(Registros[9].get('monto'))+"','noviembre':'"+ue_formato_calculo(Registros[10].get('monto'))+"','diciembre':'"+ue_formato_calculo(Registros[11].get('monto'))+"','cuenta':'"+cuenta+"'}";

	reg = reg+"]}";
	Obj= Ext.util.JSON.decode(reg);
	ObjSon=Ext.util.JSON.encode(Obj);
	parametros = 'ObjSon='+ObjSon; 
	Ext.Ajax.request({
	url : ruta2,
	params : parametros,
	method: 'POST',
	success: function(resultad, request){ 
    datos = resultad.responseText;
	//alert(datos);
	//return false;
	
	if(datos=='1')
	{
			Ext.MessageBox.alert('Mensaje', 'La operación se realizó con éxito');
			location.href='sigesp_spe_conftasa.php';
	}
	else
	{
		Ext.MessageBox.alert('Mensaje','No se pudo realizar la operación');				
	}
  }
  ,
	failure: function ( result, request)
	{ 
		Ext.MessageBox.alert('Error', result.responseText); 
	} 
    });
})

});

 
              
             




