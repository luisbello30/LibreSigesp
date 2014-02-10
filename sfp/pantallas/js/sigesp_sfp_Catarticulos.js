/*
Catalogo de Articulos
*/

var gridOnOff = false;
var winOnOff = false;
var datos = null;
var radio1=null;
var gridMeta = '';
var win = null;
var unavez = false;
var parametros='';
var ruta = '';
var Mygrid="";
var panelMeta ='';
var ParamGridTarget='';
var tiporeg =null;
rutaArt ='../../procesos/sigesp_spe_articulopr.php';
function CatArticulo()
{		
	this.MostrarCatalogo =MostrarCatalogoArticulo;
	this.ActualizarData=ActualizarData;  
}

function ActualizarDataMeta(criterio,cadena)
{
	
	bienes=Ext.get('bie').dom.checked;
	if(bienes==true)
	{
		operacion ='buscarcadena';
		tiporeg ='bien';
		
	}
	else
	{
		tiporeg ='ser';
		operacion='buscarservicios';
		if(criterio=='codart')
		{
			criterio='codser';	
		}
		else
		{
			if(criterio=='denart')
			{
				criterio='denser';
			}
		}
	}
	var myJSONObject ={
		"oper": operacion,
		"cadena": cadena,
		"criterio": criterio,
		"cuenta":cuenta
	};

	ObjSon=JSON.stringify(myJSONObject);
	
	parametros = 'ObjSon='+ObjSon; 
	Ext.Ajax.request({
	url : rutaArt,
	params : parametros,
	method: 'POST',
	success: function ( resultado, request ){ 
	datos = resultado.responseText;
	if(datos!='')
	{
		var DatosNuevo = eval('(' + datos + ')');
		gridMeta.store.loadData(DatosNuevo);
	}	
	}
});
	
}
			

function PasarDatosGrids3()
{
	numselecciones = gridMeta.getSelectionModel().getSelections();
	for(i=0;i<numselecciones.length;i++)
	{
		
	//	alert(tiporeg);
	//	return false;
		p = new RecordDefVar
		({
			'codart':'',
			'denart':'',
			'cosproart':''
			
		});	
		gridIntVar.store.insert(0,p);
		//gridIntProb.startEditing(0,0);
		p.set('codart',numselecciones[i].get('codigo'));
		p.set('denart',numselecciones[i].get('denominacion'));
		p.set('cosproart',numselecciones[i].get('precio'));
		p.set('denunimed',numselecciones[i].get('denunimed'));
		p.set('NuevoRegistro',true);
		p.set('tiporeg',tiporeg);
		//gridIntProb.stopEditing();	
	}
}
               
function MostrarCatalogoArticulo()
{
		var myObject={"raiz":[{"cod_var":'',"meta":'',"cod_uni":'',"unidad":'',"genero":''}]};	
		var RecordDef = Ext.data.Record.create([
			{name: 'codigo'},     
			{name: 'denominacion'},
			{name: 'denunimed'},
			{name: 'precio'}
		]);


            gridMeta = new Ext.grid.GridPanel({
			width:600,
			autoScroll:true,
            border:true,
            ds: new Ext.data.Store({
			proxy: new Ext.data.MemoryProxy(myObject),		
			reader: new Ext.data.JsonReader({
			    root: 'raiz',                
			     id: "id"   
            }
			,
               RecordDef
			),
			data: myObject
             }),
                        cm: new Ext.grid.ColumnModel([
                        new Ext.grid.CheckboxSelectionModel(),
                        	{header: "Código", width: 100, sortable:true,dataIndex:'codigo'},
                            {header: "Denominación", width: 200,dataIndex:'denominacion'}
                        ]),
                       sm:new Ext.grid.CheckboxSelectionModel({singleSelect:false}),
                        viewConfig:{
                        forceFit:true
                        },
			autoHeight:true,
			stripeRows: true
        });
        
        radio1 = new Ext.form.RadioGroup(
        {
              id:'tipobus',
              labelSeparator:'',
              items:
              [
                  {boxLabel: 'Bienes', name: 'rb-auto', inputValue: 1 ,checked: true,id:'bie'},
                  {boxLabel: 'Servicios', name: 'rb-auto', inputValue: 2,id:'ser'}  
              ]
         })
		panelMeta = new Ext.FormPanel({
        labelWidth: 75, // label settings here cascade unless overridden
        url:'save-form.php',
        frame:true,
        title: 'Búsqueda',
        bodyStyle:'padding:5px 5px 0',
        width: 350,
		height:120,
        defaults: {width: 230},
        defaultType: 'textfield',
		items: [{
                fieldLabel: 'Código',
                name: 'codigom',
				id:'codigom',
				changeCheck: function(){
							  var v = this.getValue();
							 ActualizarDataMeta('codart',v);
							if(String(v) !== String(this.startValue)){
								this.fireEvent('change', this, v, this.startValue);
							} 
							 }
							 ,
							initEvents : function()
							{
								AgregarKeyPress(this);
							}
               
            },{
			                fieldLabel: 'Denominación',
			                name: 'den',
							changeCheck: function(){
							  var v = this.getValue();
							 ActualizarDataMeta('denart',v);
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
            },
            radio1
            ]
		});
				
                 
                   win = new Ext.Window(
                   {
                    //layout:'fit',
                    title: Titulogrid,
		    		autoScroll:true,
                    width:600,
                    height:400,
                    modal: true,
                    closeAction:'hide',
                    plain: false,
                    items:[panelMeta,gridMeta],
                    buttons: [{
                     text:'Aceptar',  
                     handler: function()
                     {
	                    PasarDatosGrids3();	    
			      		win.hide();
                      
                     }
                    },
                    {
                     text: 'Salir',
                     handler: function()
                     {
                      win.hide();
                     }
                    }]
                   });
                  	win.show();
                    gridMeta.getSelectionModel().selectFirstRow();
      
 };
