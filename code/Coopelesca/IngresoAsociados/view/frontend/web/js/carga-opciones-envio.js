require(['jquery','jquery/ui','ko'], function($){ 
  window.inputsEnvioCheck = ""; 
  $(document).ready(function(){
  
    
      $(document).on("change","select[name='town']",function(){
        definirShipping();
        cargaMetodosEnvio();
      });

      if(($("#cartListCheckout").length > 0)&&($(".direccionEnvio").is(":visible") ) ){
        cargaMetodosEnvio();        
      }

      $(document).on("change","#reciboPedido",function(){
        $("#recibePedido").toggle();
      });
 
  
  //VALIDAR SI ESTOY EN LA PAGINA DEL DETALLE DE PRODUCTO
  if($("#mas").length){
      $(document).on("blur","#qty",function(){
        $("#cantidad").val($("#qty").val());
      });
      
      var idioma = sessionStorage.getItem("Idioma")
      if( idioma  !== null){
        if(idioma == "en"){
            var inputsEnvio = "<h3 style='margin-top:0px;'>Shipping options</h3>";
                inputsEnvio += "<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'><i style='margin-left:10px;color:#105baa' class='fas fa-store'></i><strong style='margin-left:10px;'>Store Pick Up</strong></div>";
		inputsEnvio += "<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'><i style='margin-left:10px;color:#105baa' class='fas fa-shipping-fast'></i><strong style='margin-left:10px;'>Quick delivery</strong></div>";
                inputsEnvio += "<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'><i style='margin-left:10px;color:#105baa' class='fas fa-map-marked-alt'></i><strong style='margin-left:10px;'>Standard delivery</strong></div>";         
          }else{
            var inputsEnvio = "<h3 style='margin-top:0px;'>Opciones de envío</h3>";
              inputsEnvio += "<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'><i style='margin-left:10px;color:#105baa' class='fas fa-store'></i><strong style='margin-left:10px;'>Retirar en almacén</strong></div>";
              inputsEnvio += "<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'><i style='margin-left:10px;color:#105baa' class='fas fa-shipping-fast'></i><strong style='margin-left:10px;'>Envío rápido</strong></div>";
              inputsEnvio += "<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'><i style='margin-left:10px;color:#105baa' class='fas fa-map-marked-alt'></i><strong style='margin-left:10px;'>Envío estándar</strong></div>";   
          } 
        
      }else{
        idioma = localStorage.getItem("Idioma");

        if(idioma !== null){
          if(idioma == "en"){
            var inputsEnvio = "<h3 style='margin-top:0px;'>Shipping options</h3>";
                inputsEnvio += "<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'><i style='margin-left:10px;color:#105baa' class='fas fa-store'></i><strong style='margin-left:10px;'>Deliver in the warehouse</strong></div>";
                inputsEnvio += "<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'><i style='margin-left:10px;color:#105baa' class='fas fa-shipping-fast'></i><strong style='margin-left:10px;'>Quick delivery</strong></div>";
                inputsEnvio += "<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'><i style='margin-left:10px;color:#105baa' class='fas fa-map-marked-alt'></i><strong style='margin-left:10px;'>Standard delivery</strong></div>";         
          }else{
            var inputsEnvio = "<h3 style='margin-top:0px;'>Opciones de envío</h3>";
              inputsEnvio += "<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'><i style='margin-left:10px;color:#105baa' class='fas fa-store'></i><strong style='margin-left:10px;'>Retirar en almacén</strong></div>";
              inputsEnvio += "<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'><i style='margin-left:10px;color:#105baa' class='fas fa-shipping-fast'></i><strong style='margin-left:10px;'>Envío rápido</strong></div>";
              inputsEnvio += "<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'><i style='margin-left:10px;color:#105baa' class='fas fa-map-marked-alt'></i><strong style='margin-left:10px;'>Envío estándar</strong></div>";   
          }      
        }else{             
              var inputsEnvio = "<h3 style='margin-top:0px;'>Opciones de envío</h3>";
              inputsEnvio += "<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'><i style='margin-left:10px;color:#105baa' class='fas fa-store'></i><strong style='margin-left:10px;'>Retirar en almacén</strong></div>";
              inputsEnvio += "<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'><i style='margin-left:10px;color:#105baa' class='fas fa-shipping-fast'></i><strong style='margin-left:10px;'>Envío rápido</strong></div>";
              inputsEnvio += "<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'><i style='margin-left:10px;color:#105baa' class='fas fa-map-marked-alt'></i><strong style='margin-left:10px;'>Envío estándar</strong></div>"; 
        }

        
      }      
               
      $("#metodosEnvio").html(inputsEnvio);
                      
  }
   });
  function definirShipping(){

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

          localStorage.setItem("isGuest",logueado.estaLogueado);          
        }else{
          localStorage.setItem("isGuest","");
        }
      });

}
  function estaLogueado(){
      
      $.ajax({
            url: BASE_URL+'inicio/index/Ajax',
            data: "accion=11",
            type: "POST",
            dataType: "json"        
      }).done(function (data) { 
          var resultado  = data.Mensaje;
          var logueado = JSON.parse(resultado);

          localStorage.setItem("isGuest",logueado.estaLogueado);          
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
  function cargaMetodosEnvio(){
    $.ajax({
        url: BASE_URL+'inicio/index/Ajax',
        beforeSend: function(){
          $("#metodosEnvioShipping").html("");
          if(obtenerIdioma(window.location.pathname) == "es"){
            $("#metodosEnvioShipping").html("Cargando métodos de envío... <i class='fas fa-cog fa-spin'></i>");
          }else{
            $("#metodosEnvioShipping").html("Loading shipping methods... <i class='fas fa-cog fa-spin'></i>");
          }          
        },
        data: $("#cartListCheckout").serialize(),
        type: "POST",
        dataType: "json"
    }).done(function (data) {              
        var opcionesEnvio = JSON.parse(data.metodosEnvioCheck);              
        var listaOpciones = opcionesEnvio.ListaOpcionesEnvio;
        
        if(!(listaOpciones===null)){

              var inputsEnvioCheck = "";
              $.each(listaOpciones , function(idx,value){
                inputsEnvioCheck += "<div class='neCheck col-lg-12 col-md-12 col-sm-12 col-xs-12'><input type='radio' id='"+value.IdTipoEntrega+"' value='"+value.Costo+"' name='opcionesEnvio' /><label for='"+value.IdTipoEntrega+"'><strong>"+value.Descripcion+"</strong></label></div>";
              });

              if(obtenerIdioma(window.location.pathname) == "es"){
                inputsEnvioCheck = "<div id='opcionesEnvioShipping' class='col-lg-5 col-md-5 col-sm-12 col-xs-12'><h3 class='step-title'>Opciones de env&iacute;o</h3>"+inputsEnvioCheck+"</div><div id='otroAutorizado' class='col-lg-5 col-md-5 col-sm-12 col-xs-12'><h3 class='step-title col-lg-8 col-md-8 col-sm-12 col-xs-12'>¿Alguien mas recibe el pedido?</h3><span><select id='reciboPedido' class='col-lg-2 col-md-2 col-sm-12 col-xs-12'><option  value='NO'>NO</option><option value='SI'>SI</option></select></span><p class='col-lg-10 col-md-10 col-sm-12 col-xs-12'>Persona quien recibe el pedido ( En caso de que sea diferente al Asociado que hace la compra )</p><input type='text' id='recibePedido' name='recibePedido' /></div>";
              }else{
                inputsEnvioCheck = "<div id='opcionesEnvioShipping' class='col-lg-5 col-md-5 col-sm-12 col-xs-12'><h3 class='step-title'>Shipping methods</h3>"+inputsEnvioCheck+"</div><div id='otroAutorizado' class='col-lg-5 col-md-5 col-sm-12 col-xs-12'><h3 class='step-title col-lg-8 col-md-8 col-sm-12 col-xs-12'>Does someone else receive the order?</h3><span><select id='reciboPedido' class='col-lg-2 col-md-2 col-sm-12 col-xs-12'><option  value='NO'>NO</option><option value='SI'>YES</option></select></span><p class='col-lg-10 col-md-10 col-sm-12 col-xs-12'>Person who receives the order (In case it's different from the associate who makes the purchase)</p><input type='text' id='recibePedido' name='recibePedido' /></div>";  
              }
              window.inputsEnvioCheck = inputsEnvioCheck;                

        }else{       
          if(obtenerIdioma(window.location.pathname) == "es"){
            var inputsEnvioCheck = "<div class='neCheck col-lg-12 col-md-12 col-sm-12 col-xs-12'><strong>No hay opciones de envío disponibles</strong></div>";         
            inputsEnvioCheck = "<div id='opcionesEnvioShipping' class='col-lg-5 col-md-5 col-sm-12 col-xs-12'><h3 class='step-title'>Opciones de env&iacute;o</h3>"+inputsEnvioCheck+"</div><div id='otroAutorizado' class='col-lg-5 col-md-5 col-sm-12 col-xs-12'><h3 class='step-title col-lg-8 col-md-8 col-sm-12 col-xs-12'>¿Alguien mas recibe el pedido?</h3><span><select id='reciboPedido' class='col-lg-2 col-md-2 col-sm-12 col-xs-12'><option  value='NO'>NO</option><option value='SI'>SI</option></select></span><p class='col-lg-10 col-md-10 col-sm-12 col-xs-12'>Persona quien recibe el pedido ( En caso de que sea diferente al Asociado que hace la compra )</p><input type='text' id='recibePedido' name='recibePedido' /></div>";
          }else{
            var inputsEnvioCheck = "<div class='neCheck col-lg-12 col-md-12 col-sm-12 col-xs-12'><strong>There's no shipping methods available</strong></div>";         
            inputsEnvioCheck = "<div id='opcionesEnvioShipping' class='col-lg-5 col-md-5 col-sm-12 col-xs-12'><h3 class='step-title'>Shipping methods</h3>"+inputsEnvioCheck+"</div><div id='otroAutorizado' class='col-lg-5 col-md-5 col-sm-12 col-xs-12'><h3 class='step-title col-lg-8 col-md-8 col-sm-12 col-xs-12'>Does someone else receive the order?</h3><span><select id='reciboPedido' class='col-lg-2 col-md-2 col-sm-12 col-xs-12'><option  value='NO'>NO</option><option value='SI'>YES</option></select></span><p class='col-lg-10 col-md-10 col-sm-12 col-xs-12'>Person who receives the order (In case it's different from the associate who makes the purchase)</p><input type='text' id='recibePedido' name='recibePedido' /></div>";    
          }  
          window.inputsEnvioCheck = inputsEnvioCheck;  
        }  

        $("#metodosEnvioShipping").html(window.inputsEnvioCheck);
    });
  }


});
