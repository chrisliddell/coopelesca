require(['jquery','jquery/ui','mask'], function($){   
  $(document).ready(function(){

    cargaContratos();
    estaLogueado();

    $("#ventana-inicio > form")[0].reset(); 
    var isLoggedIn = localStorage.getItem("isUser");
    $("#cedulaValida").val($("#CedulaAsociado").val());
    $("#idCuentaCreditoLista").val($("#cedulaValida").val());     
    $("#idCuentaContrato").val($("#cedulaValida").val()); 
    $("#idCuentaCredito").val($("#cedulaValida").val());           
    $("#cedulaOrder").val($("#cedulaValida").val());    
        
    //Cargar la cédula si el cliente es guest
    if( ($.trim(isLoggedIn) == "")&&(localStorage.getItem("cedula_asociado") === null)  ){        
        $( "#ventana-inicio" ).dialog({
              modal: true,
              draggable: false,
              closeOnEscape: false,
              dialogClass: 'inicio'             
        });        
    }
    //CARGAR DATOS DE CLIENTE EN SESION
    cargarDatosClienteSesion();

    if((window.location.hash == "#shipping")){      
      $("html, body").animate({ scrollTop: 0 }, "fast");
      $("#seleccionMetodoPago").fadeOut("fast");
      $.ajax({
        url: BASE_URL+'inicio/index/Ajax',
        data: "accion=13",
        type: "POST",
        dataType: "json"
      }).done(function (data) {      
      });
      actualizaSubtotales();
      actualizaSesion();
    }
    if(window.location.hash == "#payment"){
      $("#pagoContado").fadeOut("fast");
      $("#btnSolicitaCredito").fadeOut("fast");  

      if($.trim($("#tipoCompraOrder").val()) == 0){
        var almacen = sessionStorage.getItem("Almacen");
        if(almacen === null){
          almacen = localStorage.getItem("Almacen");
        }
         var almacenRedirect = almacen.toUpperCase();
         var idioma = "";
          if(obtenerIdioma(window.location.pathname) == "es"){
            idioma = "es";
          }else{
            idioma = "en";
          }

        
        location.href = "https://"+window.location.hostname+"/"+almacenRedirect+"_"+idioma+"/checkout#shipping"; 
      }
      calculaCreditoAsociado();
      actualizaCostoEnvio();
      $("#seleccionMetodoPago").fadeIn("fast");    
      $("#opc-sidebar").fadeOut("fast");  

      $("#lblCostoEnvio").remove();
      $("#lblSubtotal").remove();
      $(".estimated-price").append("<span id='lblCostoEnvio'></span>" );
      $("div.estimated-block").append("<p id='lblSubtotal'></p>" );

      $("#lblCostoEnvio").text("");
      $("#lblSubtotal").text("");

      var shipping = parseFloat($("#costoEntregaOrder").val()).toFixed(2);
      var montoCompra = $.trim($("#rawTotal").val());
      montoCompraOperaciones = formatoNormal(montoCompra);
      montoCompra = formateaNumeros(montoCompraOperaciones);

      let descuento = parseFloat($("#montoDescuentoCredito").val());
      let subShipping = montoCompraOperaciones + parseFloat(shipping);
      let totalDescuento = subShipping - descuento;

      $("#montoDescuentoCredito").val(descuento);
       /**  DJL 31-01-19 */
      $("#x_amount").val(totalDescuento);
      $("#x_amount").val("1.00");
      
      $.ajax({
        url: BASE_URL+'inicio/index/Ajax',
        data: "accion=10&x_login="+$("#x_login").val()+"&x_fp_sequence="+$("#x_fp_sequence").val()+"&x_fp_timestamp="+$("#x_fp_timestamp").val()+"&x_amount="+$("#x_amount").val(),
        type: "POST",
        dataType: "json"
      }).done(function (data) { 
        var pagoPrima = data.Mensaje;
        var valores = JSON.parse(pagoPrima);                      
        $("#x_fp_hash").val(valores.hash);            
      });
    
      if(obtenerIdioma(window.location.pathname) == "es"){        
        $("#lblSubtotal").html("Subtotal + envío (₡<span id='ship'>"+formateaNumeros(shipping)+"</span>) : ₡" + formateaNumeros(subShipping) );                 
        $("#lblDescuento").html("Descuento: ₡"+( formateaNumeros(descuento)) );
        $("#lblSubtotalDescuento").html("Subtotal con descuento : ₡"+formateaNumeros(totalDescuento) );      
      }else{                    
        $("#lblSubtotal").html("Total amount + shipping (₡"+formateaNumeros(shipping)+") : ₡" +formateaNumeros(subShipping)  );                
        $("#lblDescuento").html("Discount: ₡"+( formateaNumeros(descuento)) );
        $("#lblSubtotalDescuento").html("Subtotal with discount: ₡"+formateaNumeros(totalDescuento) );
      }

      $("#pagoContado").fadeIn("fast");
      $("#btnSolicitaCredito").fadeIn("fast");  

      $("#lblSubtotal").val(formatoNormal(totalDescuento));
      $("#montoCompraCredito").val(formatoNormal(totalDescuento));      
      $("#precioTotalCredito").html(formatoNormal(totalDescuento));   
      $("#baseFinanciar").html(formatoNormal(totalDescuento));    
      $("#montoOrder").val(formatoNormal(totalDescuento));

      calculaCreditoAsociado();
            
    }else if(window.location.hash == "#shipping"){    
      $("#ventana-inicio > form")[0].reset();              
    }
    
});

$(document).on("click","#discount-form div div button",function(){    
  actualizaSubtotales();
});
// <><><><><><><><><> EVENTOS DE TIPOS DE PAGO <><><><><><><><><>    
$(document).on("click","#pagoContado",function(event){

  
    $.ajax({
      url: BASE_URL+'inicio/index/Ajax',
      data: "accion=10&x_login="+$("#x_login").val()+"&x_fp_sequence="+$("#x_fp_sequence").val()+"&x_fp_timestamp="+$("#x_fp_timestamp").val()+"&x_amount="+$("#x_amount").val(),
      type: "POST",
      dataType: "json"
    }).done(function (data) { 
      var pagoPrima = data.Mensaje;
      var valores = JSON.parse(pagoPrima);                      
      $("#x_fp_hash").val(valores.hash);            
    });
  
  event.preventDefault();
  $("#tipoPago").val("CON");
  $("#tipoCompraOrder").val("CON");
  actualizaSesion();
  $.ajax({
    url: BASE_URL+'inicio/index/Ajax',
    data: $("#dataOrder").serialize(),
    type: "POST",
    dataType: "json"
  }).done(function (data) { });

  estaLogueado();

  var cedula = localStorage.getItem("cedula_asociado"); 
  var isLoggedIn = localStorage.getItem("isUser");
  //Cargar la cédula si el cliente es guest
  if( $.trim(isLoggedIn) == "" ){
      
      if( $.trim(cedula) == "" ){
        $( "#ventana-inicio" ).dialog({
              modal: true,
              draggable: false,
              closeOnEscape: false,
              dialogClass: 'inicio'             
        });
      
      }else{            
        $("button.ui-dialog-titlebar-close").click();
        $("#x_amount").val("1.00");
        actualizaSesion();
        $("#pagoContado").fadeOut("fast");
                setTimeout(function(){
                  $("#bcrForm").submit();
                },3000);
      } 

  }else{

    $("button.ui-dialog-titlebar-close").click();
    $("#x_amount").val("1.00");
    actualizaSesion();
    $("#pagoContado").fadeOut("fast");
    setTimeout(function(){
      $("#bcrForm").submit();
    },3000);   

  }


});

$(document).on("click","#btnSolicitaCredito",function(event){
      
      event.preventDefault();
      $("#tipoPago").val("FIN");  
      $("#tipoCompra").val("FIN");
      $("#tipoCompraOrder").val("FIN");
      
      sessionStorage.setItem("primaCredito", formatoNormal( $.trim($("#primaCreditoCalculadora").val())) ); 

        $("#precioTotalCredito").html(formateaNumeros( $.trim($("#montoOrder").val())));       
        $("#baseFinanciar").html(formateaNumeros( $.trim($("#montoOrder").val())));
        $("#cuotaFinanciar").html(formateaNumeros( $.trim($("#cuotaFinanciar").html()).toString() ));
        $("#interesFinanciar").html(formateaNumeros( $.trim($("#interesFinanciar").html()).toString() ));
        $("#totalFinanciar").html(formateaNumeros( $.trim($("#totalFinanciar").html()).toString() ));

      $.ajax({
        url: BASE_URL+'inicio/index/Ajax',
        data: $("#dataOrder").serialize(),
        type: "POST",
        dataType: "json"
      }).done(function (data) {});

      estaLogueado();
      var isLoggedIn = localStorage.getItem("isUser");
      var cedula = localStorage.getItem("cedula_asociado");          
      $("#idCuentaContrato").val($("#CedulaAsociado").val());
      $("#idCuentaCreditoLista").val($("#CedulaAsociado").val());
      $("#idCuentaCredito").val($("#CedulaAsociado").val());
      $("#cedulaOrder").val($("#CedulaAsociado").val());
      
      actualizaSesion();

      if( $.trim(isLoggedIn) == ""){
                  
            $( "#ventana-inicio" ).dialog({
                  modal: true,
                  draggable: false,
                  closeOnEscape: false,
                  dialogClass: 'inicio'             
            });
            mostrarClave();
        
      }else{  
          
          cargaContratos();
          
          $("#precioTotalCredito").html(formateaNumeros( $.trim($("#montoOrder").val())));         
          $( "#calculadoraDeCredito" ).dialog({
	        modal: true,
	        draggable: false,
	        closeOnEscape: false,
	        dialogClass: 'calculadoraDeCredito'             
	       });
          	$("#calcularCompraCredito").click();
            $("html, body").animate({ scrollTop: 0 }, "slow");                        
      }                  
  });
    //<><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><>
$(document).on("click",".cerrar",function(){
    $(this).parent().fadeOut("fast");
});
$(document).on("hover",".tooltipCustom",function(){
    $(".tooltipCustomClave").fadeOut("fast");
    $(".tooltipCustomActivar").fadeOut("fast");

    $(".tooltipCustomCedula").fadeIn("fast");
});
$(document).on("hover",".tooltipClave",function(){
  $(".tooltipCustomCedula").fadeOut("fast");
  $(".tooltipCustomActivar").fadeOut("fast");
  $(".tooltipCustomClave").fadeIn("fast");      
}); 
$(document).on("hover",".tooltipActivar",function(){
  $(".tooltipCustomCedula").fadeOut("fast");
  $(".tooltipCustomClave").fadeOut("fast");

  $(".tooltipCustomActivar").fadeIn("fast");          
}); 
    
$('#ventana-inicio').on('dialogclose', function(event) {          
  $("#numeroCedula, #campoPass, #claveAsociado").fadeOut("fast");
  $("#cedulaValida").text("");
  $("#CedulaAsociado").fadeIn("fast");
  $("#cedulaValida").fadeOut("fast");
  $("#titularAsociado").css("font-size","17px");

    if(obtenerIdioma(window.location.pathname) == "es"){
    $("#titularAsociado").text("Datos del Asociado");
    }else{
    $("#titularAsociado").text("Associate's data");
    }

  
  $("#ventana-inicio form label").fadeIn("fast");
  $(".tooltipCustom").fadeIn("fast");
  $(".tooltipClave").fadeOut("fast");
  $(".tooltipActivar").fadeOut("fast");
  $("#continuarAsociado").fadeIn("fast");
  $("#olvidePass").fadeOut("fast");
  $("#ingresarAsociado").fadeOut("fast");
  $("#activarCuenta").fadeOut("fast");
});

$("#notificacion").on('dialogclose', function(event) { 
  $("#numeroCedula, #campoPass, #claveAsociado").fadeOut("fast");
  $("#cedulaValida").text("");
  $("#CedulaAsociado").fadeIn("fast");
  $("#cedulaValida").fadeOut("fast");
  $("#titularAsociado").css("font-size","17px");
  if(obtenerIdioma(window.location.pathname) == "es"){
    $("#titularAsociado").text("Datos del Asociado");
    }else{
    $("#titularAsociado").text("Associate's data");
    }
  $("#ventana-inicio form label").fadeIn("fast");
  $(".tooltipCustom").fadeIn("fast");
  $(".tooltipClave").fadeOut("fast");
  $(".tooltipActivar").fadeOut("fast");
  $("#continuarAsociado").fadeIn("fast");
  $("#olvidePass").fadeOut("fast");
  $("#ingresarAsociado").fadeOut("fast");
  $("#activarCuenta").fadeOut("fast");

  if( $("#tipoPago").val() == "FIN" ){

    $("#ventanaInicio").dialog("close");            

    $( "#calculadoraDeCredito" ).dialog({
      modal: true,
      draggable: false,
      closeOnEscape: false,
      dialogClass: 'calculadoraDeCredito'             
    });
    $("html, body").animate({ scrollTop: 0 }, "slow");
  }

});              

  
$(document).on("click","#shipping-method-buttons-container div button",function(event){

    if( ($("#co-shipping-form").length > 0)&&( $("#co-shipping-form").is(":visible") ) ){
      $("#provinciaOrder").val($("select[name='region_id']").val());
      $("#cantonOrder").val($("select[name='county']").val());
      $("#distritoOrder").val($("select[name='district']").val());
      $("#pobladoOrder").val($("select[name='town']").val());
      $("#direccionOrder").val($("input[name='street[0]']").val()+$("input[name='street[1]']").val()+$("input[name='street[2]']").val());
      $("#provinciaMetodoEnvio").val($("select[name='region_id']").val());
      $("#cantonMetodoEnvio").val($("select[name='county']").val());
      $("#distritoMetodoEnvio").val($("select[name='district']").val());
      $("#pobladoMetodoEnvio").val($("select[name='town']").val());
      $("#direccionMetodoEnvio").val($("input[name='street[0]']").val()+$("input[name='street[1]']").val()+$("input[name='street[2]']").val());                    
    }else{                
      $("#provinciaOrder").val($("#provinciaMetodoEnvio").val());
      $("#cantonOrder").val($("#cantonMetodoEnvio").val());
      $("#distritoOrder").val($("#distritoMetodoEnvio").val());
      $("#pobladoOrder").val($("#pobladoMetodoEnvio").val());
      $("#direccionOrder").val($("#direccionMetodoEnvio").val());
    }
    $("#autorizadoRetirarOrder").val($("#recibePedido").val());
    $("#autorizadoRetirarCredito").val($("#recibePedido").val());
    actualizaSubtotales();
    actualizaSesion();

    $.ajax({
      url: BASE_URL+'inicio/index/Ajax',
      data: "accion=16",
      type: "POST",
      dataType: "json"
    }).done(function (data) { 
      var back = false;
      if( ($("#provinciaMetodoEnvio").val() == "")&&($("#provinciaOrder").val() == 0)&&($("#cantonMetodoEnvio").val() == "")&&($("#cantonOrder").val() == 0)&&($("#distritoMetodoEnvio").val() == "")&&($("#distritoOrder").val() == 0)&&($("#pobladoMetodoEnvio").val() == "")&&($("#pobladoOrder").val() == 0)&&($("#direccionMetodoEnvio").val() == "")&&($("#direccionOrder").val() == 0)  ){
        if(obtenerIdioma(window.location.pathname) == "es"){
          mostrarMensaje('No hay una dirección de envío definida.');                
        }else{
          mostrarMensaje('Please select a shipping address.');                        
        }
        back = true;
      }
      if(data.mensaje == false){
          if(obtenerIdioma(window.location.pathname) == "es"){
            mostrarMensaje('No hay cantidad disponible en este almacen para el/los producto(s): '+data.productos+','+data.precios);                                
          }else{
            mostrarMensaje("Articles unavailable in this warehouse: "+data.productos+','+data.precios);              
          }
      }else{
        estaLogueado();
        var isLoggedIn = localStorage.getItem("isUser");

        if( (typeof $("input[name='opcionesEnvio']:checked").attr("id") === "undefined")){
            if(obtenerIdioma(window.location.pathname) == "es"){
              mostrarMensaje('Por favor elija una opción de envío para su compra.');
            }else{
              mostrarMensaje('Please select a shipping option');
            }
            
            back = true;
        }else{

            actualizaCostoEnvio();

                  if(  $.trim(isLoggedIn) == ""  ){
                    if( ($.trim($("input[name='firstname']").val()) != "")&&($.trim($("input[name='lastname']").val()) != "")&&
                        ($.trim($("input[name='street[0]']").val()) != "")&&($.trim($("select[name='region_id']").val()) != "")&&
                        ($.trim($("select[name='country_id']").val()) != "")&&($.trim($("#customer-email").val()))&&
                        ($.trim($("select[name='county']").val()) != "")&&($.trim($("select[name='district']").val()) != "")
                        &&($.trim($("select[name='town']").val()) != "")&&($.trim($("input[name='telephone']").val()) != "") ){
                      
                          pasoPago();

                    }else{
                        if(obtenerIdioma(window.location.pathname) == "es"){
                          mostrarMensaje('Por favor indique los datos solicitados correctamente, para continuar con su compra.');
                        }else{
                          mostrarMensaje('Please fill up all the required fields to continue');
                        }
                      
                      back = true;
                      pasoPago(); 
                  }        
          }
      }  
      if(back){
        window.history.back();
        back = false;                      
      } 
      }           
  });
  $("#seleccionMetodoPago").fadeIn("fast");  
});

$(document).on("click","#checkout.checkout-container ul li.opc-progress-bar-item._complete span",function(event){
    actualizaSubtotales();
    actualizaSesion();
    var almacen = sessionStorage.getItem("Almacen");
    if(almacen === null){
      almacen = localStorage.getItem("Almacen");
    }
    var almacenRedirect = almacen.toUpperCase();
    var idioma = "";
    if(obtenerIdioma(window.location.pathname) == "es"){
      idioma = "es";
    }else{
      idioma = "en";
    }

    $("#seleccionMetodoPago").hide();      
    location.href = "/"+almacenRedirect+"_"+idioma+"/checkout#shipping";
});

$(document).on("click","#activarCuenta",function(){
    window.open('https://www.coopelescaenlinea.co.cr/SolicitudCuenta.aspx','_blank');
});

function actualizaSesion(){
             
  var listaOrder = $("#listaOrder").val();      
  var cedulaOrder = $("#cedulaOrder").val();
  if(cedulaOrder == "0"){
   cedulaOrder = $("#CedulaAsociado").val();
  }
  var almacenOrder = $("#almacenOrder").val();
  var idiomaOrder = $("#idiomaOrder").val();
  var montoOrder = formatoNormal($("#montoOrder").val());
  var plazoOrder = $("#plazoOrder").val();
  var primaOrder = formatoNormal($("#primaOrder").val());
  var baseFinanciaOrder = formatoNormal($("#baseFinanciaOrder").val());
  var tasaOrder = $("#tasaOrder").val();
  var cuotaOrder = formatoNormal($("#cuotaOrder").val());
  var interesMontoOrder = formatoNormal($("#interesMontoOrder").val());
  var totalMontoOrder = formatoNormal($("#montoOrder").val());
  var contratoOrder = $("#contratoOrder").val();
  var costoMontoOrder = formatoNormal($("#montoOrder").val());
  var tipoCompraOrder = $("#tipoPago").val();    
  var autorizadoRetirarCredito = $("#autorizadoRetirarCredito").val();
  var idTipoEntregaOrder = $("#idTipoEntregaOrder").val();
  var costoEntregaOrder = formatoNormal($("#costoEntregaOrder").val());
  var montoDescuentoOrder = formatoNormal($("#montoDescuentoOrder").val());
  var provinciaOrder = $("#provinciaOrder").val();
  var cantonOrder = $("#cantonMetodoEnvio").val();
  var distritoOrder = $("#distritoMetodoEnvio").val();
  var pobladoOrder = $("#pobladoMetodoEnvio").val();
  var direccionOrder = $("#direccionMetodoEnvio").val();
  if($.trim(direccionOrder) == ""){
    direccionOrder = $("#direccionOrder").val();
  }
      
  $.ajax({
    url: BASE_URL+'inicio/index/Ajax',
    data: "accion=9&listaOrder="+listaOrder+"&cedulaOrder="+cedulaOrder+"&almacenOrder="+almacenOrder+"&idiomaOrder="+idiomaOrder+"&montoOrder="+montoOrder+"&plazoOrder="+plazoOrder+"&primaOrder="+primaOrder+"&baseFinanciaOrder="+baseFinanciaOrder+"&tasaOrder="+tasaOrder+"&cuotaOrder="+cuotaOrder+"&interesMontoOrder="+interesMontoOrder+"&totalMontoOrder="+totalMontoOrder+"&contratoOrder="+contratoOrder+"&costoMontoOrder="+costoMontoOrder+"&tipoCompraOrder="+tipoCompraOrder+"&autorizadoRetirarOrder="+autorizadoRetirarCredito+"&idTipoEntregaOrder="+idTipoEntregaOrder+"&costoEntregaOrder="+costoEntregaOrder+"&montoDescuentoOrder="+montoDescuentoOrder+"&provinciaOrder="+provinciaOrder+"&cantonOrder="+cantonOrder+"&distritoOrder="+distritoOrder+"&pobladoOrder="+pobladoOrder+"&direccionOrder="+direccionOrder,
    type: "POST",
    dataType: "json"
  }).done(function (data) { console.log(data);});
}
function mostrarMensaje(mensaje){
  require([
      'Magento_Ui/js/modal/alert'
  ], function(alert) {  
      alert({
          title: 'Info',
          content: mensaje,
          actions: {
              always: function(){
                return false;
              }
          }
      });
   
  });
}
function pasoPago(){

  var cedula = localStorage.getItem("cedula_asociado"); 
  if($.trim(cedula) == ""){
    var cedula = $("#CedulaAsociado").val();
  }
  //s$("#isUser").val(cedula);
  $("#clienteCedula").val(cedula);
  $("#idCuentaCredito").val(cedula);
  $("#idCuentaCreditoLista").val(cedula);                
  $("#idCuentaContrato").val(cedula);     
  $("#cedulaOrder").val(cedula);
  $("#CedulaAsociadoLogin").val(cedula);

  $("#checkout-payment-method-load").fadeOut("fast");
  $("#idTipoEntregaCredito").val($("input[name='opcionesEnvio']:checked").attr("id"));
  $("#idTipoEntregaOrder").val($("input[name='opcionesEnvio']:checked").attr("id"));

  $("#provinciaOrder").val($("#provinciaMetodoEnvio").val());
  $("#cantonOrder").val($("#cantonMetodoEnvio").val());
  $("#distritoOrder").val($("#distritoMetodoEnvio").val());
  $("#pobladoOrder").val($("#pobladoMetodoEnvio").val());
  $("#direccionOrder").val($("#direccionMetodoEnvio").val());

  $("#opc-sidebar").fadeOut("fast");
  $("#seleccionMetodoPago").fadeIn("fast");
  $("#bcrForm").fadeIn("fast");
  $("#btnSolicitaCredito").fadeIn("fast");

  $("#autorizadoRetirarOrder").val($("#recibePedido").val());
  $("#autorizadoRetirarCredito").val($("#recibePedido").val());


  calculaCreditoAsociado();

}
function actualizaSubtotales(){

  setTimeout(function(){

    /* Actualizar la cédula una vez cargada la sección de shipping */
    var cedula = localStorage.getItem("cedula_asociado"); 
    if($.trim(cedula) == ""){
      var cedula = $("#CedulaAsociado").val();
    }

    $("#clienteCedula").val(cedula);
    $("#idCuentaCredito").val(cedula);
    $("#idCuentaCreditoLista").val(cedula);                
    $("#idCuentaContrato").val(cedula);     
    $("#cedulaOrder").val(cedula);
    $("#CedulaAsociadoLogin").val(cedula);
    /***********************************************/
    
    $.ajax({
      url: BASE_URL+'inicio/index/Ajax',
      beforeSend: function(){
        $("#pagoContado").fadeOut("fast");
        $("#btnSolicitaCredito").fadeOut("fast");        
        if(obtenerIdioma(window.location.pathname) == "es"){
          $("#lblSubtotal").html("Actualizando total...  <i class='fas fa-cog fa-spin'></i>");
         }else{
          $("#lblSubtotal").html("Updating total...  <i class='fas fa-cog fa-spin'></i>");
         }
        
      },
      data: "accion=12",
      type: "POST",
      dataType: "json"
    }).done(function (data) { 
      var resultado  = data.Mensaje;
      var totales = JSON.parse(resultado);

      $("#montoOrder").val(totales.subtotal);
      $("#totalMontoOrder").val(totales.subtotal);
      $("#totalCostoOrder").val(totales.subtotal);
      $("#costoMontoOrder").val(totales.subtotal);          
      $("#costoMontoCredito").val(totales.subtotal);
      $("#totalMontoCredito").val(totales.subtotal);
      $("#montoCompraCredito").val(totales.subtotal);

      var shipping = parseFloat($("#costoEntregaOrder").val()).toFixed(2);

      let descuento = parseFloat(totales.subtotal)-parseFloat(totales.subtotal_condescuento);
      let subShipping = parseFloat(totales.subtotal) + parseFloat(shipping);
      let totalDescuento = subShipping - descuento;

      $("#montoDescuentoCredito").val(descuento);
      $("#montoDescuentoOrder").val(descuento);
      
      if(obtenerIdioma(window.location.pathname) == "es"){        
        $("#lblSubtotal").html("Subtotal + envío (₡<span id='ship'>"+formateaNumeros(shipping)+"</span>) : ₡" + formateaNumeros(subShipping) );                 
        $("#lblDescuento").html("Descuento: ₡"+( formateaNumeros(descuento)) );
        $("#lblSubtotalDescuento").html("Subtotal con descuento : ₡"+formateaNumeros(totalDescuento) );      
      }else{                    
        $("#lblSubtotal").html("Total amount + shipping (₡"+formateaNumeros(shipping)+") : ₡" +formateaNumeros(subShipping)  );                
        $("#lblDescuento").html("Discount: ₡"+( formateaNumeros(descuento)) );
        $("#lblSubtotalDescuento").html("Subtotal with discount: ₡"+formateaNumeros(totalDescuento) );
      }
      if(totales.subtotal_condescuento == totales.subtotal){ 
        $("#lblDescuento").html(""); 
        $("#lblSubtotalDescuento").html("")          
      }
      
      $("#montoCompraCredito").val(totalDescuento);
      $("#totalMontoCredito").val(totalDescuento);
      $("#totalMontoCredito").val(totalDescuento);
      $("#costoMontoCredito").val(totalDescuento);

      $("#montoOrder").val(totalDescuento);
      $("#totalMontoOrder").val(totalDescuento);
      $("#totalMontoOrder").val(totalDescuento);
      $("#costoMontoOrder").val(totalDescuento);
      

      $("#x_amount").val(totalDescuento);
      $("#x_amount").val("1.00");
      $.ajax({
        url: BASE_URL+'inicio/index/Ajax',
        data: "accion=10&x_login="+$("#x_login").val()+"&x_fp_sequence="+$("#x_fp_sequence").val()+"&x_fp_timestamp="+$("#x_fp_timestamp").val()+"&x_amount="+$("#x_amount").val(),
        type: "POST",
        dataType: "json"
      }).done(function (data) { 
        var pagoPrima = data.Mensaje;
        var valores = JSON.parse(pagoPrima);                      
        $("#x_fp_hash").val(valores.hash);            
      });

    }).success(function(){
      $("#pagoContado").fadeIn("fast");
      $("#btnSolicitaCredito").fadeIn("fast");  
    });
    
    calculaCreditoAsociado();      
    
  },3500);
  
}
function actualizaCostoEnvio(){
  setTimeout(function(){
    $("#lblCostoEnvio").remove();
    $(".estimated-price").append("<span id='lblCostoEnvio'></span>" );
    $("#lblCostoEnvio").text("");

    var costoEntrega = $("#costoEntregaOrder").val();      
    costoEntrega = formateaNumeros(costoEntrega);

    if($("#costoEntregaOrder").val() == 0){
      if(obtenerIdioma(window.location.pathname) == "es"){
        $("#lblCostoEnvio").text("  [[ envío gratis ]]");
       }else{        
        $("#lblCostoEnvio").text("  [[ free shipping ]]");
       } 
    }else{
      if(obtenerIdioma(window.location.pathname) == "es"){
        $("#lblCostoEnvio").text(" + ₡"+costoEntrega+" (costo envío)");  
       }else{
        $("#lblCostoEnvio").text(" + ₡"+costoEntrega+" (shipping)");  
       }
              
    }
  },5000);
}
function procesoAsociado(){

  $.ajax({
        url: BASE_URL+'inicio/index/Ajax',
        beforeSend: function(){
          if(obtenerIdioma(window.location.pathname) == "es"){
          $("#mensaje-inicio").html("Enviando...  <i class='fas fa-cog fa-spin'></i>");
         }else{
          $("#mensaje-inicio").html("Sending data...  <i class='fas fa-cog fa-spin'></i>");
         }
           
        },
        data: $("#ventana-inicio > form").serialize(),
        type: "POST",
        dataType: "json"        
  }).done(function (data) {        
       var ConsultaAsociado = data.ConsultaAsociado;
       var asociado = JSON.parse(ConsultaAsociado);
       
       if(asociado.error === undefined){

         if(asociado.EsAsociado === true){

           if(obtenerIdioma(window.location.pathname) == "es"){
            $("#mensaje-inicio").html("<p>Bienvenido: asociado!</p>");
           }else{
            $("#mensaje-inicio").html("<p>Welcome: associate!</p>");
           }
            
            setTimeout(function(){
              $("#mensaje-inicio").html("");
            },3000);
            var cedula = $("#CedulaAsociadoLogin").val();
            $("#clienteCedula").val(cedula);
            $("#idCuentaCredito").val(cedula); 
            $("#idCuentaCreditoLista").val(cedula);                                      
            $("#idCuentaContrato").val(cedula);            
            $("#cedulaOrder").val(cedula);

            $.ajax({
              url: BASE_URL+'inicio/index/Ajax',
              data: $("#dataOrder").serialize(),
              type: "POST",
              dataType: "json"
            }).done(function (data) {});
            
            localStorage.setItem("cedula_asociado", cedula); 

            //Cerrar la ventana de la cédula
            $("button.ui-dialog-titlebar-close").click();
            
         }else{
           if(obtenerIdioma(window.location.pathname) == "es"){
            $("#mensaje-inicio").html("<p class='warning'>Lo sentimos, no se han encontrado Asociados registrados con esta identificación.\nVerifique el número que digitó o comuníquese con uno de nuestros agentes en línea.</p>");  
           }else{
            $("#mensaje-inicio").html("<p class='warning'>You're not yet an associate</p>");  
           }
                      
            setTimeout(function(){
              $("#mensaje-inicio").html("");
            },3000);
         } 

       }else{
          if(obtenerIdioma(window.location.pathname) == "es"){
            $("#mensaje-inicio").html("<p class='warning'>Lo sentimos, no se han encontrado Asociados registrados con esta identificación.\nVerifique el número que digitó o comuníquese con uno de nuestros agentes en línea.</p>");  
           }else{
            $("#mensaje-inicio").html("<p class='warning'>You're not yet an associate</p>");  
           }             
          setTimeout(function(){
            $("#mensaje-inicio").html("");
          },3000);
       }  
       
  });

}
function actualizaCostoEnvioTotal(){
  
  $.ajax({
    url: BASE_URL+'inicio/index/Ajax',    
    data: "accion=12",
    type: "POST",
    dataType: "json"
  }).done(function (data) { 
    var resultado  = data.Mensaje;
    var totales = JSON.parse(resultado);

    var costoEntregaOrder = parseFloat($("#costoEntregaOrder").val());
    var totalConCostoEnvio = parseFloat(totales.granTotal)+parseFloat(costoEntregaOrder);

    $("#montoOrder").val(totalConCostoEnvio);
    $("#totalMontoOrder").val(totalConCostoEnvio);
    $("#costoMontoOrder").val(totalConCostoEnvio);          
    $("#costoMontoCredito").val(totalConCostoEnvio);
    $("#totalMontoCredito").val(totalConCostoEnvio);
    $("#montoCompraCredito").val(totalConCostoEnvio);
    
    
    $("#x_amount").val(totalConCostoEnvio);
    $("#x_amount").val("1.00");
    
    $.ajax({
      url: BASE_URL+'inicio/index/Ajax',
      data: "accion=10&x_login="+$("#x_login").val()+"&x_fp_sequence="+$("#x_fp_sequence").val()+"&x_fp_timestamp="+$("#x_fp_timestamp").val()+"&x_amount="+$("#x_amount").val(),
      type: "POST",
      dataType: "json"
    }).done(function (data) { 
      var pagoPrima = data.Mensaje;
      var valores = JSON.parse(pagoPrima);                      
      $("#x_fp_hash").val(valores.hash);            
    });

  });
}
function mostrarClave(){
  var cedula = localStorage.getItem("cedula_asociado"); 

  $("#CedulaAsociadoLogin").fadeOut("fast");
  $("#titularAsociado").css("font-size","17px");
  if(obtenerIdioma(window.location.pathname) == "es"){
    $("#titularAsociado").text("Confirmar datos del asociado");
   }else{
    $("#titularAsociado").text("Confirm associate data");
   }


  $("#campoPass, #claveAsociado").fadeIn("fast");
  $("#cedulaValida").text( cedula );
  $("#cedulaValida").fadeIn("fast");            
  $("#ventana-inicio form label").fadeOut("fast");            
  $(".tooltipCustom").fadeOut("fast");

  $("#continuarAsociado").fadeOut("fast");
  $(".tooltipClave").fadeIn("fast");
  $(".tooltipActivar").fadeIn("fast");
  
  $("#olvidePass").fadeOut("fast");
  $("#ingresarAsociado").fadeIn("fast");
  $("#activarCuenta").fadeIn("fast");
  $("#CedulaAsociadoLogin").fadeOut("fast");
  $("#olvidePass").fadeOut("fast");
}
function cargaContratos(){

    $.ajax({
      url: BASE_URL+'inicio/index/Ajax',
      data: $("#contratosForm").serialize(),
      type: "POST",
      dataType: "json"
    }).done(function (data) { 
     
        var contratos = data.Contratos;
        var listaContratosAsociado = JSON.parse(contratos);
        $("#contratoFinancia").html("");
        $.each(listaContratosAsociado, function(item,value) {                      
          for (i = 0; i < (value.length); i++) { 
            var temp = value[i];        
            if(typeof temp.CodigoContrato != typeof undefined){              
              $("#contratoFinancia").append("<option value="+temp.CodigoProducto+">"+temp.DescripcionTipoProducto+"</option>");
            }
          }
        });
    });

}
function estaLogueado(){
      
      $.ajax({
            url: BASE_URL+'inicio/index/Ajax',
            data: "accion=11",
            type: "POST",
            dataType: "json"        
      }).done(function (data) { 
        if(data != null){
          var resultado  = data.Mensaje;
          var logueado = JSON.parse(resultado);

          localStorage.setItem("isUser",logueado.estaLogueado);          
        }else{
          localStorage.setItem("isUser","");
        }
      });

}
function formateaNumeros(n){
  
    n = n.toString();    
    var formato = n.split(".");

    if(formato[1] === undefined){
      var pattern = n.replace(/\B(?=(\d{3})+(?!\d))/g, ",");  
      var decimales = "00";
    }else{
      var pattern = formato[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");  
      var decimales = formato[1].substr(0,2);  
    }
    
    var numeroFinal = pattern+"."+decimales;

    return numeroFinal;
}
function formatoNormal(n) {
  n = n.toString();
  n = n.replace(/\,/g,"");  
  n = n.replace(/\₡/g,"");

  return n;
}
function isFloat(n){
    return n != "" && !isNaN(n) && Math.round(n) != n;
}
function cargarDatosClienteSesion(){
    $.ajax({
      url: BASE_URL+'inicio/index/Ajax',
      data: $("#dataOrder").serialize(),
      type: "POST",
      dataType: "json"
    }).done(function (data) {  }); 
}
function calculaCreditoAsociado(){
  
  if(localStorage.getItem("cedula_asociado") !== null ){
    $("#idCuentaCreditoLista").val(localStorage.getItem("cedula_asociado"));
    $("#CedulaAsociado").val(localStorage.getItem("cedula_asociado"));
  }
  
  $.ajax({
    url: BASE_URL+'inicio/index/Ajax',
    beforeSend: function(){
      if(obtenerIdioma(window.location.pathname) == "es"){
        $("#mensajeCalculadoraCredito").html("Calculando...  <i class='fas fa-cog fa-spin'></i>");
       }else{
        $("#mensajeCalculadoraCredito").html("Obtaining results...  <i class='fas fa-cog fa-spin'></i>");
       }
      
      $("#mensajeCalculadoraCredito").fadeIn("fast");
    },
    data: $("#listArticles").serialize(),
    type: "POST",
    dataType: "json"
    }).done(function (data) {    
            
      cargaContratos();

      setTimeout(function(){
        $("#mensajeCalculadoraCredito").html("");
        $("#mensajeCalculadoraCredito").fadeOut("fast");  
      },3000);
      
       var credito = data.CalculoCredito;
       var creditoFinacia = JSON.parse(credito);
       var financiamiento = creditoFinacia.CalculoFinanciamiento;
       var mensaje = creditoFinacia.DescripcionRespuesta;

       if( !(financiamiento === null) ){

          var montoCompra = $("#montoOrder").val();
          montoCompra = parseFloat(montoCompra).toFixed(2);
        
          if(obtenerIdioma(window.location.pathname) == "es"){
            $("#wsInfoCredito").html("Monto total a financiar: <span class='resaltaFinancia'>₡"+montoCompra+"</span>, Cuota: <span class='resaltaFinancia'>₡"+formateaNumeros(financiamiento.Cuota)+"</span>, Plazo: <span class='resaltaFinancia'>"+financiamiento.Plazo+" meses</span>");
          }else{
            $("#wsInfoCredito").html("Total financing amount: <span class='resaltaFinancia'>₡"+montoCompra+"</span>, Fee: <span class='resaltaFinancia'>₡"+formateaNumeros(financiamiento.Cuota)+"</span>, Time limit: <span class='resaltaFinancia'>"+financiamiento.Plazo+" months</span>");
          }
           
          $("#primaCreditoCalculadora").val(formateaNumeros(financiamiento.Prima));
          
          $("#baseFinanciar").html(financiamiento.BaseFinanciamiento);
          $("#tasaAnualFinanciar").html(financiamiento.TasaInteres);
          $("#cuotaFinanciar").html(financiamiento.Cuota);
          $("#interesFinanciar").html(financiamiento.Intereses);
          $("#totalFinanciar").html(financiamiento.MontoTotalFinanciamiento);

           /*   CARGAR PLAZOS  */
           $("#plazosFinanciar").html("");
           $.each(financiamiento.Plazos.reverse(), function(item,value) {
             $("#plazosFinanciar").append("<option value="+value+">"+value+"</option>");
           });
           /*******************/
        }else{
          $("#btnSolicitaCredito").remove();
        }
    });
}

function obtenerIdioma(path){
  var index = path.indexOf("_en");

  if(index != -1){
    return "en";
  }else{
    return "es";
  }
}

});
