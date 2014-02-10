/*
 * Ext JS Library 2.0.2
 * Copyright(c) 2006-2008, Ext JS, LLC.
 * licensing@extjs.com
 * http://extjs.com/license
 */



var panel = '';
var gridMontos='';
var gridSinG='';
var gridUnaVez=false;
var ventana='';
var RegistroSelVar='';
var auxGridMetas = '';
var radio1='';
var auxCampo='';
var acuMonto=0;
var codart = '';
var jsonIpc='';
var jsonOtr='';
var jsonIva='';

function LlenaMontoEq(Obj)
{
	ValorEq = Ext.get('montorep').getValue();
	if(Obj.get('mes')!='Total')
	{
		Monto=ValorEq;
		acuMonto+=parseInt(ValorEq);
	}
	else if(Obj.get('mes')=='Total')
	{
		Monto=acuMonto;
	}
	if(ValorEq!='')
	{
		if(auxCampo=='fem')
		{
			Obj.set('cantfemenino',Monto);
		}
		else if(auxCampo=='mas')
		{
			Obj.set('cantmasculino',Monto);
		}
		else if(auxCampo=='dos')
		{
			Obj.set('cantmasculino',Monto);
			Obj.set('cantfemenino',Monto);
		}	
	}	
}

function ObtenerInflacion(numes)
{	
		mes = numes;
		switch(mes)
		{	
			case '1':
				if(jsonIpc.raiz!=null)
				return ue_formato_calculo(jsonIpc.raiz[0].enero)
				break;
			case '2':
				if(jsonIpc.raiz!=null)
				return ue_formato_calculo(jsonIpc.raiz[0].febrero)
				break;
			case '3':
				if(jsonIpc.raiz!=null)
				return ue_formato_calculo(jsonIpc.raiz[0].marzo)
				break;
			case '4':
				if(jsonIpc.raiz!=null)
				return ue_formato_calculo(jsonIpc.raiz[0].abril)
				break;
			case '5':
				if(jsonIpc.raiz!=null)
				return ue_formato_calculo(jsonIpc.raiz[0].mayo)
				break;
			case '6':
				if(jsonIpc.raiz!=null)
				return ue_formato_calculo(jsonIpc.raiz[0].junio)
				break;
			case '7':
				if(jsonIpc.raiz!=null)
				return ue_formato_calculo(jsonIpc.raiz[0].julio)
				break;
			case '8':
				if(jsonIpc.raiz!=null)
				return ue_formato_calculo(jsonIpc.raiz[0].agosto)
				break;
			case '9':
				if(jsonIpc.raiz!=null)
				return ue_formato_calculo(jsonIpc.raiz[0].septiembre)
				break;
			case '10':
				if(jsonIpc.raiz!=null)
				return ue_formato_calculo(jsonIpc.raiz[0].octubre)
				break;
			case '11':
				if(jsonIpc.raiz!=null)
				return ue_formato_calculo(jsonIpc.raiz[0].noviembre)
				break;
			case '12':
				if(jsonIpc.raiz!=null)
				return ue_formato_calculo(jsonIpc.raiz[0].diciembre)
				break;
		}	

	return parseInt(0);
}


function ObtenerOtimp(numes)
{
	mes = numes;
	auxcantidad = jsonOtr.raiz.length;
		switch(mes)
		{	
			case '1':
				return ue_formato_calculo(jsonOtr.raiz[0].enero)
				break;
			case '2':
				return ue_formato_calculo(jsonOtr.raiz[0].febrero)
				break;
			case '3':
				return ue_formato_calculo(jsonOtr.raiz[0].marzo)
				break;
			case '4':
				return ue_formato_calculo(jsonOtr.raiz[0].abril)
				break;
			case '5':
				return ue_formato_calculo(jsonOtr.raiz[0].mayo)
				break;
			case '6':
				return ue_formato_calculo(jsonOtr.raiz[0].junio)
				break;
			case '7':
				return ue_formato_calculo(jsonOtr.raiz[0].julio)
				break;
			case '8':
				return ue_formato_calculo(jsonOtr.raiz[0].agosto)
				break;
			case '9':
				return ue_formato_calculo(jsonOtr.raiz[0].septiembre)
				break;
			case '10':
				return ue_formato_calculo(jsonOtr.raiz[0].octubre)
				break;
			case '11':
				return ue_formato_calculo(jsonOtr.raiz[0].noviembre)
				break;
			case '12':
				return ue_formato_calculo(jsonOtr.raiz[0].diciembre)
				break;
		}	
	return parseInt(0);

}



function repMontos()
{
	acuMonto=0
	valor = Ext.getCmp('tipodist').getValue();
	//alert(valor);
	if(valor)
	{
		auxCampo='mas';
	}
	else
	{
		if(Ext.getCmp('fem').getValue()==true)
		{
			auxCampo='fem';
		}
		if(Ext.getCmp('mas').getValue()==true)
		{
			auxCampo='mas';	
		}
		if(Ext.getCmp('todos').getValue()==true)
		{
			auxCampo='dos';	
		}
	}	
		//alert(auxCampo);
		auxGridMetas.store.each(LlenaMontoEq);
}

function traervalores()
{
	var myJSONObject={
		"oper": 'traervalores',
		"codart":codart,
		'cuenta':cuenta
	};

	ObjSon=JSON.stringify(myJSONObject);
	parametros = 'ObjSon='+ObjSon; 
	Ext.Ajax.request({
	url : rutaprogCompra,
	params : parametros,
	method: 'POST',
	success: function ( resultado, request )
	{ 
		datos = resultado.responseText;
		//alert(datos);
		if(datos!='')
		{
			arrResp = datos.split("|");
			jsonIpc = eval('(' + arrResp[0] + ')');
			jsonOtr = eval('(' + arrResp[1] + ')');
			jsonIva = eval('(' + arrResp[2] + ')');
		}	
	}
});
}


function getGridMontosArts(Obj1,Row,col,Obj)
{
			
			RegistroActual = gridIntVar.store.getAt(Row);		
			codart = RegistroActual.get('codart');
			denart = RegistroActual.get('denart');
			costo=RegistroActual.get('cosproart');
			unidad=RegistroActual.get('denunimed');
			//traervalores();
			RegistroSelVar = gridIntVar.getSelectionModel().getSelected();
        	radio1 = new Ext.form.Hidden
			(
				  {
		               name:'hid1',
				  	   value:true,
				  	   id:'tipodist'
			  	  }
	  		)	    
        	
           Meses =
			[
		        ['1','Enero',gridIntVar.getSelectionModel().getSelected().get('enero_masc')],
		        ['2','Febrero',gridIntVar.getSelectionModel().getSelected().get('febrero_masc')],
		        ['3','Marzo',gridIntVar.getSelectionModel().getSelected().get('marzo_masc')],
		        ['4','Abril',gridIntVar.getSelectionModel().getSelected().get('abril_masc')],
		        ['5','Mayo',gridIntVar.getSelectionModel().getSelected().get('mayo_masc')],
		        ['6','Junio',gridIntVar.getSelectionModel().getSelected().get('junio_masc')],
		        ['7','Julio',gridIntVar.getSelectionModel().getSelected().get('julio_masc')],
		        ['8','Agosto',gridIntVar.getSelectionModel().getSelected().get('agosto_masc')],
		        ['9','Septiembre',gridIntVar.getSelectionModel().getSelected().get('septiembre_masc')],
		        ['10','Octubre',gridIntVar.getSelectionModel().getSelected().get('octubre_masc')],
		        ['11','Noviembre',gridIntVar.getSelectionModel().getSelected().get('noviembre_masc')],
		        ['12','Diciembre',gridIntVar.getSelectionModel().getSelected().get('diciembre_masc')],
		        ['13','Total',gridIntVar.getSelectionModel().getSelected().get('totalm')]
			]
			
				
	CodMeta = gridIntVar.getSelectionModel().getSelected().get('cod_var');
	DenMeta = gridIntVar.getSelectionModel().getSelected().get('meta'); 
	EsNuevo = gridIntVar.getSelectionModel().getSelected().get('NuevoRegistro');
	MontGlobal = gridIntVar.getSelectionModel().getSelected().get('montoGlobal');
	Unidad = gridIntVar.getSelectionModel().getSelected().get('unidad');

     var storeMeses = new Ext.data.SimpleStore({
        fields: ['nombre'],
        data : Meses // from states.js
    });
    

	  panelMetas = new Ext.FormPanel({
	  labelWidth:140, // label settings here cascade unless overridden,
	  labelAlign:'right',
	  title: 'Distribución de la Compra',
	  bodyStyle:'padding-top:0px',
	  height:200,  
	  items:[
		{
		  xtype:'textfield', 
		  fieldLabel: 'Código',
		  name: 'meta',
		  value:codart,
		  readOnly:true,
		  id: 'Meta',
		  maxLength: 25,
		  maxLengthText: 'El campo excede la longitud máxima',
		  allowBlank:false,
		  width: 80
	    },{
		  xtype:'textfield', 
		  fieldLabel:'Denominación',
		  name: 'denominacion',
		  readOnly:true,
		  value:denart,
		  id: 'denom',
		  maxLength: 470,
		  maxLengthText: 'El campo excede la longitud máxima',
		  allowBlank:false,
		  width: 370
	    },
	    {
			  xtype:'textfield', 
			  fieldLabel:'Unidad de Medida',
			  name: 'unimed',
			  readOnly:true,
			  value:unidad,
			  id: 'unidad',
			  maxLength: 470,
			  maxLengthText: 'El campo excede la longitud máxima',
			  allowBlank:false,
			  width: 370
		}
	    ,
	    {
		  xtype:'textfield', 
		  fieldLabel: 'Precio Unitario',
		  name: 'precio',
		  readOnly:true,
		  value:costo,
		  id: 'precio',
		  maxLength: 470,
		  maxLengthText: 'El campo excede la longitud máxima',
		  allowBlank:false,
		  width: 100
	    }
	    ,
	    radio1
	    ,
	    {
	   	  xtype:'button',
		  handler:repMontos,
		  text:'Repetir Cantidades',
		  style:'position:absolute;left:140px;top:110px'
	    }
	   ]
	});
			gridUnaVez=true;
		//	var DatosNuevo={"raiz":[{"programatica":'',"spg_cuenta":'',"year":'',"monto":''}]};	
			var storeMeses = new Ext.data.SimpleStore
			({
			        fields: 
					[
			           {name: 'numes'},
			           {name: 'mes'},
			           {name: 'cantidad'},
			           {name: 'costoest'},
			           {name: 'montoiva'},
			           {name: 'montoinf'},
			           {name: 'montoimp'},
			           {name: 'montototal'}  
			        ]
			 });
	
			storeMeses.loadData(Meses);
			gridConG = new Ext.grid.EditorGridPanel({
  			width:550,
  			style:'margin-left:0px',
  			title: 'Distribución Mensual por Género',
			autoScroll:true,
            border:true,
            ds:storeMeses,
            cm: new Ext.grid.ColumnModel([
            {header: "Mes", width: 50, sortable: true,   dataIndex: 'mes'},
			{header: "Cantidad", width: 50, sortable: true, dataIndex: 'cantidad',editor: new Ext.form.NumberField({allowBlank:false,allowDecimals:false,id:'cantidad'})},
			{header: "Costo Estimado", width: 50, sortable: true, dataIndex: 'costoest',editor: new Ext.form.NumberField({allowBlank:false,allowDecimals:false,id:'costoest'})},
			{header: "IVA", width: 50, sortable: true, dataIndex: 'montoiva',editor: new Ext.form.NumberField({allowBlank:false,allowDecimals:false,id:'montoiva'})},
			{header: "Inflación", width: 30, sortable: true, dataIndex: 'montoinf',editor: new Ext.form.NumberField({allowBlank:false,allowDecimals:false,id:'montoinf'})},
			{header: "Otros Impuestos", width: 60, sortable: true, dataIndex: 'montoimp',editor: new Ext.form.NumberField({allowBlank:false,allowDecimals:false,id:'montoimp'})},
			{header: "Total", width: 50, sortable: true, dataIndex: 'montototal',editor: new Ext.form.NumberField({allowBlank:false,allowDecimals:false,id:'montototal'})}							
			])
			,
			selModel: new Ext.grid.RowSelectionModel({singleSelect:false}),
            viewConfig: {
            	forceFit:true	
            }
            ,
			autoHeight:true
            });
            
                  ventana = new Ext.Window(
                 {
                    layout:'anchor',
                    title: 'Distribución de la Compra',
		    		autoScroll:true,
                    width:650,
                    height:500,
                    modal: true,
                    closeAction:'hide',
                    plain: false,
                    items:[panelMetas,gridConG],
                    buttons: [{
                     text:'Aceptar',  
                     handler: function()
                     {
		      			PasarDatosGridDist(gridConG,RegistroActual);
		      			ventana.destroy();
		      			panelMetas.destroy();
                     }
                    },{
                     text: 'Salir',
                     handler: function()
                     {
                     	 ventana.destroy();
                     	 panelMetas.destroy();
		      			 radio1=''
                     }
                    }]
                   });
                   
            ventana.show();
            AcuMontoMasAux=0;
            AcuMontoTotAux=0;
 			gridConG.on('afteredit',function(Obj){
 			// if(!MontGlobal)
			// {
	 			Rec = Obj.record;
			    Cantidad = ue_formato_calculo(Rec.get('cantidad'));
			    Costoestimado = costo* Cantidad;
			//  alert(jsonIva.raiz[0].porcar);
			    if(jsonIva.raiz!=null)
			    {
				    if(jsonIva.raiz[0].porcar)
				    {
				    	porcar=jsonIva.raiz[0].porcar;
				    }
				    else
				    {
				    	porcar=0;
				    }
			    }
			    else
			    {
			    	porcar=0;
			    }
			    Costoiva = Costoestimado*(porcar/100)
			    tasainf=ObtenerInflacion(Rec.get('numes'));
			    tasaotr=ObtenerOtimp(Rec.get('numes'));
			    Costoinf = Costoestimado*(tasainf/100);
			    Costootr = Costoestimado*(tasaotr/100);
			    Costototal = Costoestimado+Costoiva+Costoinf+Costootr; 
			    Rec.set('costoest',Costoestimado);
			    Rec.set('montoiva',Costoiva);
			    Rec.set('montoinf',Costoinf);
			    Rec.set('montoimp',Costootr);
			    Rec.set('montototal',Costototal);
			    
			    //alert(tasainf); 
			    if(Obj.originalValue!='')
			    {
			    	MontoDisponible = parseInt(MontoDisponible)+parseInt(ue_formato_calculo(Obj.originalValue));
					MontoDistribuido = parseInt(MontoDistribuido)- parseInt(ue_formato_calculo(Obj.originalValue));
			    }
			    if(Obj.value=='')
			    {
			    	Obj.value=0;
			    }	
			    
		  //  }
		  actualizartotal(gridConG);
		})
	 
		  	gridConG.getView().getRowClass = function(record, index){
		  	if(record.data.mes=='Total')
		  		{
		  			return 'Total';
		  		}	
    		}; 
          traervalores();                        
}


function PasarDatosGridDist(grid,regitroactual)
{
	numrec = grid.store.getCount()-1;
	numselecciones = grid.store.getRange(0,numrec);
	for(i=0;i<numselecciones.length;i++)
	{
		Auxmes=nombredemes(numselecciones[i].get('numes'));
		Auxmes2=Auxmes+'tot';	
		regitroactual.set(Auxmes,numselecciones[i].get('cantidad'));
		regitroactual.set(Auxmes2,numselecciones[i].get('montototal'));
	}
	regitroactual.set('totalm',numFormat(grid.store.getAt(12).get('montototal'),2,'.'));
}



function actualizartotal(grid)
{
	numrec = grid.store.getCount()-2;
	arrec = grid.store.getRange(0,numrec);
	totalcant=0;
	totalimp =0;
	totaliva = 0;
	totalinf = 0;
	totalcosto = 0;
	totalgen = 0;
	for(i=0;i<arrec.length;i++)
	{
		if(arrec[i].get('cantidad')!='')
		{
			totalcant+=parseInt(arrec[i].get('cantidad'));			
		}
		if(arrec[i].get('costoest')!='')
		{
			totalcosto+=parseInt(arrec[i].get('costoest'));			
		}
		if(arrec[i].get('montoiva')!='')
		{
			totaliva+=parseInt(arrec[i].get('montoiva'));			
		}
		if(arrec[i].get('montoinf')!='')
		{
			totalinf+=parseInt(arrec[i].get('montoinf'));			
		}
		if(arrec[i].get('montototal')!='')
		{
			totalgen+=parseInt(arrec[i].get('montototal'));			
		}
		if(arrec[i].get('montoimp')!='')
		{
			totalimp+=parseInt(arrec[i].get('montoimp'));			
		}
	}
	grid.store.getAt(12).set('cantidad',totalcant);
	grid.store.getAt(12).set('costoest',totalcosto);
	grid.store.getAt(12).set('montoiva',totaliva);
	grid.store.getAt(12).set('montoinf',totalinf);
	grid.store.getAt(12).set('montototal',totalgen);
	grid.store.getAt(12).set('montoimp',totalimp);
}



