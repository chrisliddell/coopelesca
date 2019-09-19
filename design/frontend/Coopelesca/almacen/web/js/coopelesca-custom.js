require(['jquery','jquery/ui','owlcarousel'], function($){

  var localAlmacen = localStorage.getItem("Almacen");
  var sessionAlmacen = sessionStorage.getItem("Almacen");
  var idioma = "";
  var almacen = "";
  var base_url = require.toUrl('');
  var urlLeft = base_url + "images/left-arrow.png";
  var urlRight = base_url + "images/right-arrow.png";


$(document).ready(function(){

    cargaListaAlmacenes();
    agregaOpcionComparar();
    cargaChat();
    estaLogueado();
    validaUrl();

    $(document).on("click","ul.dropdown.switcher-dropdown li.switcher-option a",function(event){
      event.preventDefault();      
      cambioIdioma();
    });
    
    var idioma = sessionStorage.getItem("Idioma");
    if(idioma === null){
      idioma = localStorage.getItem("Idioma");
    }
    if((idioma == "es")){
      sessionStorage.setItem("Idioma","es");      
      $("#langAlmacen").html("<i class='far fa-flag'><span>Inglés</span></i>");
    }else if((idioma == "en")){
      sessionStorage.setItem("Idioma","en");      
      $("#langAlmacen").html("<i class='far fa-flag'><span>Spanish</span></i>");
    }


    var almacen = sessionStorage.getItem("Almacen");
    if(almacen === null){
      almacen = localStorage.getItem("Almacen");
    }

    if((almacen != "")&&(almacen !== null)){
      sessionStorage.setItem("Almacen",almacen);
    }

    //Solamente cuando no tenemos definido el almacen de preferencia lo volvemos a definir
    if( (localAlmacen === null ) && (sessionAlmacen === null) ){           
       ventanaBienvenida(); 
    }

    if($("h2.titularCompra").length > 0){
      var url_compra_finalizada = window.location.href; 
      var url = new URL(url_compra_finalizada);      
      var numero_orden = url.searchParams.get("orden");      
      var direccion_orden = url.searchParams.get("direccion");
      var costo_envio = url.searchParams.get("costo_envio");              
      costo_envio =  costo_envio.replace(new RegExp('/₡/', 'gi'), "");
      var monto_descuento = url.searchParams.get("monto_descuento"); 
      monto_descuento = monto_descuento.replace(new RegExp('/₡/', 'gi'), ""); 
      monto_descuento = $.trim(monto_descuento);
      var total_orden = url.searchParams.get("total");
      var mensaje_coope = url.searchParams.get("mensaje_coope");      
      //Costo de envio puede venir como un string = Envio gratuito
      if(!isNaN(costo_envio) ){          
        costo_envio = $.trim(costo_envio);                    
      }else{
        costo_envio =  0;         
      }
      total_orden = parseFloat(total_orden) - parseFloat(costo_envio);
      var totalFinal = (parseFloat(total_orden)+parseFloat(costo_envio)) - parseFloat(monto_descuento);
      total_orden = total_orden.toFixed(2);
      totalFinal = totalFinal.toFixed(2);

      if((mensaje_coope!==null)&&($.trim(mensaje_coope) == "")){
        $("#codigoFacturaFinalizado").html(numero_orden);
        $("#nombreDireccionFinalizado").html(direccion_orden);                
        $("#subTotalFinalizado").html("₡ "+formateaNumeros(total_orden));        
        $("#envioFinalizado").html("₡ "+formateaNumeros(costo_envio));
        $("#descuentoFinalizado").html("₡ "+formateaNumeros(monto_descuento));        
        $("#totalFinalizado").html("₡ "+formateaNumeros(totalFinal));
      }else{  

        $("#numeroPedidoFinal").fadeOut("fast");
        $("#mensajeEstadoFinal").fadeOut("fast");
        $("#listadoOrdenFinal").fadeOut("fast");
        $("#finDireccion").fadeOut("fast");
        $("#finTotal").fadeOut("fast");
        $("#tituloDetalleOrden").fadeOut("fast");
        $("#mensajeCoopelescaDetalle").html(mensaje_coope);
                
        var idioma = sessionStorage.getItem("Idioma");
        if(idioma === null){
          idioma = localStorage.getItem("Idioma");
        }
        if((idioma == "es")){
          $("#tituloFinalizacion").html("Lo sentimos, no se ha podido finalizar el proceso de compra.");
          $("#mensajeAdicionalOrden").html("Mensajes adicionales");
        }else if((idioma == "en")){
          $("#tituloFinalizacion").html("Sorry, the purchase process could not be completed.");
          $("#mensajeAdicionalOrden").html("Aditional information");
        }  
      }
    }   

  var isMobile = {
    Android: function() {
        return navigator.userAgent.match(/Android/i);
    },
    BlackBerry: function() {
        return navigator.userAgent.match(/BlackBerry/i);
    },
    iOS: function() {
        return navigator.userAgent.match(/iPhone|iPad|iPod/i);
    },
    Opera: function() {
        return navigator.userAgent.match(/Opera Mini/i);
    },
    Windows: function() {
        return navigator.userAgent.match(/IEMobile/i);
    },
    any: function() {
        return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
    }
  };

  var sesion = localStorage.getItem("isUser"); 
  $("#isUser").val(sesion); 
  

  if( ($("#acciones-menu").length > 0)&&( $(window).width() >= 992 ) ){    

    if(isMobile.any()) {
      $("#acciones-menu").on({
        tap: function () {

          if( $(this).find("i.fas.fa-bars").length > 0 ){
            $(this).find("i.fas.fa-bars").removeClass().addClass("fas fa-times");

            $("#sombra").css("display","block");
            $("#categorias-iniciales").css("display","block"); 
            $("div.minicart-wrapper").fadeOut("fast");
            $("li.wishlist").fadeOut("fast");
            $("li.comparar").fadeOut("fast");
            $("li.authorization-link").fadeOut("fast");
          }else{
            $(this).find("i.fas.fa-times").removeClass().addClass("fas fa-bars");
            $("#sombra").css("display","none");
            $("#categorias-iniciales").css("display","none"); 

            $("div.minicart-wrapper").fadeIn("fast");
            $("li.wishlist").fadeIn("fast");
            $("li.comparar").fadeIn("fast");
            $("li.authorization-link").fadeIn("fast");
          }
         
                    
        }
      });
      $("#categorias-iniciales > li").on({
        tap: function (event) {
          $("#categorias-iniciales > li").find("ul").fadeOut("fast");   
          event.preventDefault();
          //<><><><><><><> Obtener espacio entre items y padre para alinearlos al top del padre <><><>
          var topMenu = $("#categorias-iniciales").offset().top;
          var topItem = $(this).offset().top;
          var restaNuevoItem = topItem - topMenu;
          restaNuevoItem = restaNuevoItem - 2;
          //<><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><>

          //<><><><><> Dejar el mismo alto en el contenedor padre y el hijo <><><><><><><>
          var contenedorPadre = $("#categorias-iniciales").innerHeight();              
          var contenedorHijo = $("#categorias-iniciales > li > ul").innerHeight();

          if(contenedorPadre > contenedorHijo){
            $("#categorias-iniciales > li > ul").css("height",contenedorPadre+"px");
          }
          //<><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><>

          $(this).find("ul").css("top","-"+restaNuevoItem+"px");
          $(this).find("ul").fadeIn("fast");
          $(this).find("ul > li").fadeIn("fast");

        }
      });
      $("#categorias-iniciales > li").on({
        taphold: function (event) {          
          var enlace = $(this).find("a");
          location.href=enlace.attr("href");
        }
      });  
      $(document).on("tap","#almacen-activo",function(){              
          $("#menu-almacen-desplegable").toggle();              
      });    
      $(document).on("tap","#salirResp",function(){              
        $("div.nav-sections").css("left","-9000px");
      });
      $(document).on("tap","#salirResp",function(){
        $("div.nav-sections").fadeOut("fast");
      });

    }else{

      $(document).on("click","#almacen-activo",function(){              
          $("#menu-almacen-desplegable").toggle();              
      });

      $("#acciones-menu, #categorias-iniciales").on({
        mouseenter: function () {
          $("#sombra").css("display","block");
          $("#categorias-iniciales").css("display","block"); 
          $("div.minicart-wrapper").fadeOut("fast");
          $("li.wishlist").fadeOut("fast");
          $("li.comparar").fadeOut("fast");
          $("li.authorization-link").fadeOut("fast");
        }
      });  
      $("#categorias-iniciales > li").on({
        mouseenter: function () {
          //<><><><><><><> Obtener espacio entre items y padre para alinearlos al top del padre <><><>
          var topMenu = $("#categorias-iniciales").offset().top;
          var topItem = $(this).offset().top;
          var restaNuevoItem = topItem - topMenu;
          restaNuevoItem = restaNuevoItem - 2;
          //<><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><>

          //<><><><><> Dejar el mismo alto en el contenedor padre y el hijo <><><><><><><>
          var contenedorPadre = $("#categorias-iniciales").innerHeight();              
          var contenedorHijo = $("#categorias-iniciales > li > ul").innerHeight();

          if(contenedorPadre > contenedorHijo){
            $("#categorias-iniciales > li > ul").css("height",contenedorPadre+"px");
          }
          //<><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><>

          $(this).find("ul").css("top","-"+restaNuevoItem+"px");
          $(this).find("ul").fadeIn("fast");
          $(this).find("ul > li").fadeIn("fast");

        },
        mouseleave: function () {
          $(this).find("ul").fadeOut("fast");    
        }
      });
    }
    
    
    
    $("#categorias-iniciales,#menu-coopelesca").on({
      mouseleave: function () {
        $("#sombra").css("display","none");
        $("#categorias-iniciales").css("display","none");  
        $("div.minicart-wrapper").fadeIn("fast");    
        $("li.wishlist").fadeIn("fast");
        $("li.comparar").fadeIn("fast");
        $("li.authorization-link").fadeIn("fast");
      }
    }); 


  }else{

    var evento = "click";
    if(isMobile.any()) {
      evento = "tap";
    }else{
      evento = "click";
    }
      $(document).on(evento,".uMobile",function(){
        $("#menuMobileCoope").show("fast");
      });
      $(document).on(evento,"#salirMenuMobile",function(){
        $("#menuMobileCoope").hide("fast");
      });
      $(document).on(evento,"#salirResp",function(){
        $("div.nav-sections").css("left","-9000px");
        $("html, body").animate({ scrollTop: 0 }, "slow");
        console.log("Hola");
      });
      $(document).on(evento,"#top-cart-btn-checkout-custom",function(){
        $("#btn-minicart-close").click();
      });
      $(document).on(evento,"#top-cart-btn-checkout-custom",function(){
        $("#btn-minicart-close").click();
      });
    

      if(($("#acciones-menu").length > 0)){
          
          $("#categorias-iniciales").css("display","block");             
          $("#categorias-iniciales ul li").css("display","block"); 
          $("#categorias-iniciales ul li").css("color","#FFF");

          $(document).on(evento,"#almacen-activo",function(){              
              $("#menu-almacen-desplegable").toggle();              
          });

          $(document).on(evento,"#categorias-iniciales li",function(event){   
                
              if( $(this).hasClass("level-top")&&$(this).hasClass("parent") ){                  
                event.preventDefault();
                $("#todasCategorias").remove();
                var primeraCategoria = $(this).find("a");                
                $("#categorias-iniciales ul").css("display","none");
                if(obtenerIdioma(window.location.pathname) == "es"){
                  $(this).find("ul.level0").append("<li id='todasCategorias' class='level1 category-item' style='display:block; color:#FFF;'><a href='"+primeraCategoria[0]+"'>--Todos los artículos--</a></li>");
                }else{
                  $(this).find("ul.level0").append("<li id='todasCategorias' class='level1 category-item' style='display:block; color:#FFF;'><a href='"+primeraCategoria[0]+"'>--All products--</a></li>");
                }
                
                if($(this).find("ul").hasClass("open")){
                  $(this).find("ul").css("display","none");
                  $("#categorias-iniciales li").find("ul").removeClass("open");                  
                }else{
                  $(this).find("ul").css("display","block");
                  $("#categorias-iniciales li").find("ul").removeClass("open");
                  $(this).find("ul").addClass("open");                               
                }                              
              }else{
                event.stopPropagation();
              }

          });              
      }
    
        
        if(obtenerIdioma(window.location.pathname) == "es"){
          $("div.section-item-title a.nav-sections-item-switch").append("<div id='salirResp'><i class='fas fa-times-circle'></i></div>");
        }else{
         $("div.section-item-title a.nav-sections-item-switch").append("<div id='salirResp'><i class='fas fa-times-circle'></i></div>"); 
        }

        var menucoopelesca = $("ul.menu-coopelesca").html();
        $("ul.menu-coopelesca").remove();
        $("#menu-coopelesca").append("<ul class='menu-coopelesca'>"+menucoopelesca+"</ul>");

        var discover = "";
        if(obtenerIdioma(window.location.pathname) == "es"){
          $("ul.menu-coopelesca").prepend("<p class='separaMenuMobile'>Navegar</p>");
          $("#categorias-iniciales").prev().append("<p class='separaMenuMobile'>Menú categorías</p>");
	        $("#categorias-iniciales").prev().prepend("<p class='separaMenuMobile'>Selección de almacén más cercano</p>"); 
          discover = "<p class='separaMenuMobile'>Acciones</p>";
        }else{
          $("ul.menu-coopelesca").prepend("<p class='separaMenuMobile'>Navigate</p>");
          $("#categorias-iniciales").prev().append("<p class='separaMenuMobile'>Categories Menu</p>");
          $("#categorias-iniciales").prev().prepend("<p class='separaMenuMobile'>Select a warehouse</p>");        
          discover = "<p class='separaMenuMobile'>Discover</p>";
        }
    
        var menuCoope = $("ul.menu-coopelesca").html();

        var estadoPedidoLink = "<li id='estadoPedidoLink'>"+$("#estadoPedidoLink").html()+"</li>";    
        var contactUsLink = "<li>"+$( "li#langAlmacen" ).next().next().html()+"</li>";    
        var helpLink = "<li>"+$( "li#langAlmacen" ).next().next().next().html()+"</li>";
        $( "#estadoPedidoLink" ).remove();
        $( "li#langAlmacen" ).next().remove();
        $( "li#langAlmacen" ).next().remove();


        $("ul.menu-coopelesca").html(menuCoope+discover+"<ul id='discover'>"+estadoPedidoLink+contactUsLink+helpLink+"</ul>");
      
        $("<i class='uMobile' />").insertAfter("div.minicart-wrapper");
          
  }
              
    $('#productosRelacionados,#newProducts,#productosOfertas').owlCarousel({          
          nav: true,          
          responsiveClass:true,
          responsive:{
              0:{
                  items:2,
                  nav:true,
                  navText: ["<img src='"+urlLeft+"' />","<img src='"+urlRight+"' />"]
              },            
              300:{
                  items:2,
                  nav:true,
                  navText: ["<img src='"+urlLeft+"' />","<img src='"+urlRight+"' />"]
              },
              600:{
                  items:3,
                  nav:true,
                  navText: ["<img src='"+urlLeft+"' />","<img src='"+urlRight+"' />"]
              },
              1000:{
                  items:5,
                  nav:true,
                  navText: ["<img src='"+urlLeft+"' />","<img src='"+urlRight+"' />"]
              }
          }
    }); 

    $(document).on("click","div.inicio div button.ui-dialog-titlebar-close",function() { 
        resetVentanaLogin();
    });    

    $(document).on("click","button.action-select-shipping-item",function(){
      $.ajax({
        url: BASE_URL+'inicio/index/Ajax',
        data: "accion=13",
        type: "POST",
        dataType: "json"
      }).done(function (data) {      
      });
    });

    $(document).on("click","li.wishlist a",function(event){
      event.preventDefault();

      estaLogueado(); 
      var sesion = localStorage.getItem("isUser");
      localStorage.setItem("guestAction","favoritos");
      if($.trim(sesion) == "1"){
        var idioma = sessionStorage.getItem("Idioma");
        var almacen = sessionStorage.getItem("Almacen");  
        var almacenRedirect = almacen.toUpperCase();
        var direccionWishlist="https://"+window.location.hostname+"/"+almacenRedirect+"_"+idioma+"/wishlist/?___store="+almacen+"_"+idioma;
        
        location.href = direccionWishlist;
      }else{
        $( "#ventana-inicio" ).dialog({
            modal: true,
            draggable: false,
            closeOnEscape: false,
            dialogClass: 'inicio'             
          });
          
      }


    });
    $(document).on("click","#estadoPedidoLink",function(){
      var almacen = sessionStorage.getItem("Almacen");
      if(almacen === null){
        almacen = sessionStorage.getItem("Almacen");
      }
      var almacenRedirect = almacen.toUpperCase();
      var path = window.location.pathname;
      var index = path.indexOf("_en");
      var idioma = "";

      if(index != -1){
        idioma = "en";
        location.href="https://"+window.location.hostname+"/"+almacenRedirect+"_"+idioma+"/estado-de-envio?___store="+almacen+"_"+idioma;
      }else{
        idioma = "es";
        location.href="https://"+window.location.hostname+"/"+almacenRedirect+"_"+idioma+"/estado-de-envio?___store="+almacen+"_"+idioma;  
      }
    });
    $(document).on("click","div.product-addto-links a.action.towishlist",function(e){
      e.preventDefault();
      localStorage.setItem("guestAction","favoritos");
    });
    $(document).on("click","div.product.details div.product-social-links div.actions-secondary a.towishlist",function(e){
      e.preventDefault();
      localStorage.setItem("guestAction","favoritos");
    });
    
    $(document).on("click","#langAlmacen",function(){
      $(this).html("<i class='fas fa-cog fa-spin' />");
      cambioIdioma();
    });
    
    $(document).on("mouseover",".tooltipCustom",function(){
      $(".tooltipCustomCedula").fadeIn("fast");
    });
    $(document).on("click",".tooltipCustom",function(){
      $(".tooltipCustomCedula").fadeIn("fast");
    });
    $(document).on("click","div.tooltipCustomCedula p.cerrar",function(){
      $(".tooltipCustomCedula").fadeOut("fast");
    });

    $(document).on("mouseover",".tooltipActivar",function(){
      $(".tooltipCustomActivar").fadeIn("fast");
    });
    $(document).on("click",".tooltipActivar",function(){
      $(".tooltipCustomActivar").fadeIn("fast");
    });
    $(document).on("click","div.tooltipCustomActivar p.cerrar",function(){
      $(".tooltipCustomActivar").fadeOut("fast");
    });
    $(document).on("click","#activarCuenta",function(){    
      window.open('https://oficinavirtual.coopelesca.com/#/iniciarsesion/activarcuentaverificarindentificacion','_blank');
    });
    

    $(document).on("click","div.panel.header span.action.nav-toggle",function(){
      $("div.nav-sections").fadeIn("fast");
      $("div.nav-sections").css("left","0");
    });
    
    
    
    $(document).on("click","input[name='opcionesEnvio']",function(){

      var montoCompra = $.trim($("#rawTotal").val());
      montoCompraOperaciones = formatoNormal(montoCompra);
      montoCompra = formateaNumeros(montoCompraOperaciones);

      $("#costoMontoCredito").val(formatoNormal(montoCompraOperaciones));
      $("#metodoEnvioCoopelesca").val($("input[name='opcionesEnvio']:checked").val());
      $("#costoEntregaCredito").val($("input[name='opcionesEnvio']:checked").val());
      $("#idTipoEntregaCredito").val($("input[name='opcionesEnvio']:checked").attr("id"));
      $("#idTipoEntregaOrder").val($("input[name='opcionesEnvio']:checked").attr("id"));
      $("#costoEntregaOrder").val($("input[name='opcionesEnvio']:checked").val());

      $("#lblCostoEnvio").remove();      
      $(".estimated-price").append("<span id='lblCostoEnvio'></span>" );     
      $("#lblCostoEnvio").text("");
     

      var shipping = parseFloat($("#costoEntregaOrder").val()).toFixed(2);

      let descuento = parseFloat($("#montoDescuentoCredito").val());
      let subShipping = parseFloat(montoCompraOperaciones) + parseFloat(descuento) + parseFloat(shipping);
      let totalDescuento = parseFloat(subShipping) - parseFloat(descuento);

      $("#montoDescuentoCredito").val(descuento);
      //$("#x_amount").val(totalDescuento);
      $("#x_amount").val("1.00");
      $.ajax({
        url: BASE_URL+'inicio/index/Ajax',
        data: "accion=10&x_login="+$("#x_login").val()+"&x_fp_sequence="+$("#x_fp_sequence").val()+"&x_fp_timestamp="+$("#x_fp_timestamp").val()+"&x_amount="+parseFloat($("#x_amount").val()),
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

      $("#lblSubtotal").val(formatoNormal(totalDescuento));
      $("#montoCompraCredito").val(formatoNormal(totalDescuento));
      $("#precioTotalCredito").html(formatoNormal(totalDescuento));   
      $("#baseFinanciar").html(formatoNormal(totalDescuento));   
      $("#montoOrder").val(formatoNormal(totalDescuento));
      /**  DJL 31-01-19 */ 
      //$("#x_amount").val(totalDescuento);
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
          
          $.ajax({
            url: BASE_URL+'inicio/index/Ajax',
            data: $("#dataOrder").serialize(),
            type: "POST",
            dataType: "json"
          }).done(function (data) { });
      }); 
      

      
          
    });
    $(document).on("click","#top-cart-btn-checkout-custom",function(event){
        event.preventDefault();

        $.ajax({
          url: BASE_URL+'inicio/index/Ajax',
          data: "accion=16",
          type: "POST",
          dataType: "json"
        }).done(function (data) { 

            if(data.mensaje == false){
                if(obtenerIdioma(window.location.pathname) == "es"){
                  require([
                      'Magento_Ui/js/modal/alert'
                  ], function(alert) {  
                      alert({
                          title: 'Info',
                          content: 'No hay cantidad disponible en este almacen para el/los producto(s): '+data.productos+','+data.precios,
                          actions: {
                              always: function(){
                                return false;
                              }
                          }
                      });
                   
                  });
                }else{
                  require([
                      'Magento_Ui/js/modal/alert'
                  ], function(alert) {  
                      alert({
                          title: 'Info',
                          content: "Articles unavailable in this warehouse: "+data.productos+','+data.precios,
                          actions: {
                              always: function(){
                                return false;
                              }
                          }
                      });
                   
                  });
                }
            }else{
                localStorage.setItem("guestAction","carrito");
                var sesion = localStorage.getItem("isUser");
              
                if( ($.trim(sesion) == "")&&(localStorage.getItem("cedula_asociado") === null) ){
                  $("#btn-minicart-close").click();
                  $( "#ventana-inicio" ).dialog({
                    modal: true,
                    draggable: false,
                    closeOnEscape: false,
                    dialogClass: 'inicio'             
                  });
                  
                }else{
                  var almacen = sessionStorage.getItem("Almacen");
                  if(almacen === null){
                    almacen  = localStorage.getItem("Almacen");
                  }
                  var almacenRedirect = almacen.toUpperCase();
                  idioma = sessionStorage.getItem("Idioma");
                  location.href = "/"+almacenRedirect+"_"+idioma+"/checkout#shipping";
                }
            }               
        });
                              
    });  
    $(document).on("click","#editarCompraCredito",function(event){
      var almacen = sessionStorage.getItem("Almacen");
      if(almacen === null){
        almacen  = localStorage.getItem("Almacen");
      }
      var almacenRedirect = almacen.toUpperCase();
      idioma = sessionStorage.getItem("Idioma");
      if(idioma === null){
        idioma = localStorage.getItem("Idioma");
      }
      location.href = "/"+almacenRedirect+"_"+idioma+"/checkout/cart/index";
    });
    $(document).on("click","#carritoPagar",function(event){
        event.preventDefault();        
        localStorage.setItem("guestAction","carrito");
        
        var sesion = localStorage.getItem("guestAction");
        if( $.trim(sesion) == "" ){
          $("#btn-minicart-close").click();
          $( "#ventana-inicio" ).dialog({
            modal: true,
            draggable: false,
            closeOnEscape: false,
            dialogClass: 'inicio'             
          });
          
        }else{
          var almacen = sessionStorage.getItem("Almacen");
          if(almacen === null){
            almacen = localStorage.getItem("Almacen");
          }
          var almacenRedirect = almacen.toUpperCase();
          var idioma = sessionStorage.getItem("Idioma");
          location.href = "/"+almacenRedirect+"_"+idioma+"/checkout#shipping";
        }
    });
    
    $(document).on("click","li.authorization-link a",function(event){
      event.preventDefault();
      localStorage.setItem("guestAction","ingresar");
      if( ($.trim($(this).text()) == "Ingresar")||
          ($.trim($(this).text()) == "Sign In") ){
        
          event.preventDefault();
          $( "#ventana-inicio" ).dialog({
            modal: true,
            draggable: false,
            closeOnEscape: false,
            dialogClass: 'inicio'             
          });
      }    
         
    });
    $(document).on("click","#botonIngresoPreferencia, #ingresarPreferencia > div > button",function(){

      var idioma = $("#opciones-idioma").val();
      var almacen = $("#opciones-almacen").val();
      var almacenRedirect = almacen.toUpperCase();

      if($.trim(almacen) != ""){

        if( $("#recordar-almacen").prop("checked") == true ){
            localStorage.setItem("Almacen", almacen);
            sessionStorage.setItem("Almacen", almacen);
        }else{
            sessionStorage.setItem("Almacen", almacen);            
        }

        if( $("#recordar-idioma").prop("checked") == true ){
            localStorage.setItem("Idioma", idioma);
            sessionStorage.setItem("Idioma", idioma);
        }else{
            sessionStorage.setItem("Idioma", idioma);
        }

        $("#ventana-bienvenido").dialog("close");
        var path = window.location.pathname;
        path = remplazaPath(path);
        location.href=window.location.origin+"/"+almacenRedirect+"_"+idioma+path+"?___store="+almacen+"_"+idioma;

      }else{
        $("#mensaje-bienvenido").html("");
        $("#opciones-almacen").css("border","2px solid red");
       if(obtenerIdioma(window.location.pathname) == "es"){
        $("#mensaje-bienvenido").html("<p>Por favor seleccione su almacén de preferencia.</p>");
       }else{
        $("#mensaje-bienvenido").html("<p>You must choose a warehouse</p>");
       }
        
        $("#mensaje-bienvenido").fadeIn("fast");
        setTimeout(function(){
          $("#mensaje-bienvenido").fadeOut("fast");
          $("#opciones-almacen").css("border","2px solid #414042");
          $("#mensaje-bienvenido").html("");
        },3000);  
      }

  });

  $(document).on("click","#calcularCompraCredito",function(){

    var primaFormatoOperacion = formatoNormal($("#primaCreditoCalculadora").val());

    if($.trim($("#plazosFinanciar").val()) == ""){
      if(obtenerIdioma(window.location.pathname) == "es"){
          require([
              'Magento_Ui/js/modal/alert'
          ], function(alert) {  
              alert({
                  title: 'Info',
                  content: 'No se puede estimar el crédito sin un plazo',
                  actions: {
                      always: function(){
                        return false;
                      }
                  }
              });
           
          });
       }else{
         require([
              'Magento_Ui/js/modal/alert'
          ], function(alert) {  
              alert({
                  title: 'Info',
                  content: "It's not possible to estimate the order with no period selected",
                  actions: {
                      always: function(){
                        return false;
                      }
                  }
              });
           
          });
       }
    }
    if($.trim($("#contratoFinancia").val()) == ""){
      if(obtenerIdioma(window.location.pathname) == "es"){
          require([
              'Magento_Ui/js/modal/alert'
          ], function(alert) {  
              alert({
                  title: 'Info',
                  content: 'No se puede estimar el crédito sin un contrato seleccionado',
                  actions: {
                      always: function(){
                        return false;
                      }
                  }
              });
           
          });
       }else{
         require([
              'Magento_Ui/js/modal/alert'
          ], function(alert) {  
              alert({
                  title: 'Info',
                  content: "It's not possible to estimate the order with no contract selected",
                  actions: {
                      always: function(){
                        return false;
                      }
                  }
              });
           
          });
       }
    }

    if(( primaFormatoOperacion < parseFloat(sessionStorage.getItem("primaCredito")) )){
      if(obtenerIdioma(window.location.pathname) == "es"){
          require([
              'Magento_Ui/js/modal/alert'
          ], function(alert) {  
              alert({
                  title: 'Info',
                  content: 'La prima debe ser igual o mayor a la prima sugerida',
                  actions: {
                      always: function(){}
                  }
              });
           
          });
       }else{
         require([
              'Magento_Ui/js/modal/alert'
          ], function(alert) {  
              alert({
                  title: 'Info',
                  content: 'The advance payment must be equal or more than the suggested advance payment',
                  actions: {
                      always: function(){}
                  }
              });
           
          });
       }   
    }

        $("#primaCredito").val(primaFormatoOperacion);
        $("#plazoCredito").val($("#plazosFinanciar").val());
        $("#plazoOrder").val($("#plazosFinanciar").val());
        
        
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
                
           var credito = data.CalculoCredito;
           var creditoFinacia = JSON.parse(credito);
           var financiamiento = creditoFinacia.CalculoFinanciamiento;

           $("#mensajeCalculadoraCredito").html("");
           $("#mensajeCalculadoraCredito").fadeOut("fast");

           setTimeout(function(){
            $("#mensajeCalculadoraCredito").html("");
            $("#mensajeCalculadoraCredito").fadeOut("fast");
           },3500);
          
           if(financiamiento !== null){
                var montoCompra = $("#montoOrder").val();
                

                 if(obtenerIdioma(window.location.pathname) == "es"){
                  $("#wsInfoCredito").html("Monto total a financiar: <span class='resaltaFinancia'>₡"+montoCompra+"</span>, Cuota: <span class='resaltaFinancia'>₡"+formateaNumeros(financiamiento.Cuota)+"</span>, Plazo: <span class='resaltaFinancia'>"+financiamiento.Plazo+" meses</span>");
                 }else{
                  $("#wsInfoCredito").html("Total financing amount: <span class='resaltaFinancia'>₡"+montoCompra+"</span>, Fee: <span class='resaltaFinancia'>₡"+formateaNumeros(financiamiento.Cuota)+"</span>, Time limit: <span class='resaltaFinancia'>"+financiamiento.Plazo+" months</span>");
                 }
               $("#primaCreditoCalculadora").val(formateaNumeros(financiamiento.Prima));
               $("#primaOrder").val(formateaNumeros(financiamiento.Prima));
               $("#baseFinanciar").html(formateaNumeros(financiamiento.BaseFinanciamiento));
               $("#baseFinanciaOrder").val(financiamiento.BaseFinanciamiento);
               $("#tasaAnualFinanciar").html(financiamiento.TasaInteres);
               $("#tasaOrder").val(financiamiento.TasaInteres);
               $("#cuotaFinanciar").html(formateaNumeros(financiamiento.Cuota));
               $("#cuotaOrder").val(formateaNumeros(financiamiento.Cuota));
               $("#interesFinanciar").html(formateaNumeros(financiamiento.Intereses));
               $("#interesMontoOrder").val(financiamiento.Intereses);
               $("#totalFinanciar").html(formateaNumeros(financiamiento.MontoTotalFinanciamiento));
               $("#totalMontoOrder").val(financiamiento.MontoTotalFinanciamiento);
               
               actualizaSesion();
               /*   CARGAR PLAZOS  */
               $("#plazosFinanciar").html("");
               $.each(financiamiento.Plazos.reverse(), function(item,value) {        
                 if($("#plazoCredito").val() == value){
                  $("#plazosFinanciar").append("<option value="+value+" selected='selected'>"+value+"</option>");
                 }else{
                  $("#plazosFinanciar").append("<option value="+value+">"+value+"</option>");
                 }
               });
           }

        });
   

  });


  /** PARA AGREGAR CANTIDAD DETALLE DE PRODUCTOS */
  
  $(document).on("click",".mas",function(){
    var identificador = $(this).data("identificador");  
    console.log(identificador);  
    var cantidad = parseInt($(".cantidades_"+identificador).val());
    cantidad += 1;
    $(".cantidades_"+identificador).val(cantidad);
  });
  $(document).on("click",".menos",function(){
    var identificador = $(this).data("identificador");
    var cantidad = parseInt($(".cantidades_"+identificador).val());
    if(cantidad == 1){
      $(".cantidades_"+identificador).val(cantidad);
    }else{
      cantidad -= 1;
      $(".cantidades_"+identificador).val(cantidad);
    } 
  });

  $(document).on("click","#mas",function(){
    var cantidad = parseInt($("#qty").val());
    cantidad += 1;
    $("#qty").val(cantidad);
  });
  $(document).on("click","#menos",function(){
    var cantidad = parseInt($("#qty").val());
    if(cantidad == 1){
      $("#qty").val(cantidad);
    }else{
      cantidad -= 1;
      $("#qty").val(cantidad);
    } 
  });
  //************************************************

  // DEFINIR EL ALMACEN SELECCIONADO Y CREAR EVENTO PARA CUANDO CAMBIE DE ALMACEN
  $(document).on("click","#menu-almacen-desplegable li",function(){
        var almacen  = $(this).data("key");

        if(sessionStorage.getItem("Idioma") !== null ){

           //Redirigir a la vista de tienda + idioma seleccionado
           sessionStorage.setItem("Almacen", almacen);
           var idioma = sessionStorage.getItem("Idioma");              
           var almacenRedirect = almacen.toUpperCase();   

           $.ajax({
              url: BASE_URL+'inicio/index/Ajax',
              data: "accion=14&almacen="+almacenRedirect+"_"+sessionStorage.getItem("Idioma"),
              type: "POST",
              dataType: "json"
            }).done(function (data) { 
              var path = window.location.pathname;
              path = remplazaPath(path);
              var sniffSearch = path.indexOf("catalogsearch/result");

              if(sniffSearch === -1){
                location.href=window.location.origin+"/"+almacenRedirect+"_"+idioma+path+"?___store="+almacen+"_"+sessionStorage.getItem("Idioma");
              }else{                
                var q = $("#search").val();
                location.href=window.location.origin+"/"+almacenRedirect+"_"+idioma+path+"?___store="+almacen+"_"+sessionStorage.getItem("Idioma")+"&q="+q;
              }
              
            }); 
           

        }else{
           var almacenRedirect = almacen.toUpperCase();
           sessionStorage.setItem("Almacen", almacenRedirect);
           var idioma = sessionStorage.getItem("Idioma"); 
           if($.trim(idioma) == ""){
            sessionStorage.setItem("Idioma","es"); 
           }              
            //Redirigir a la vista de tienda + idioma seleccionado                 
            $.ajax({
              url: BASE_URL+'inicio/index/Ajax',
              data: "accion=14&almacen="+$("#select-menu-almacen").val()+"_es",
              type: "POST",
              dataType: "json"
            }).done(function (data) { 
               var path = window.location.pathname;
               path = remplazaPath(path);

               var sniffSearch = path.indexOf("catalogsearch/result");

              if(sniffSearch === -1){
                location.href=window.location.origin+"/"+almacenRedirect+"_"+idioma+path+"?___store="+almacen+"_es";
              }else{                
                var q = $("#search").val();
                location.href=window.location.origin+"/"+almacenRedirect+"_"+idioma+path+"?___store="+almacen+"_es&q="+q;
              }

            });  

        }
  });
  // <><><><><><><><><><><><><><><><><><><><><><><><><><><><>

  $("#CedulaAsociadoLogin").keydown(function(event){
    // Utilizar cualquier tecla numerica y guiones, cualquier otra no la permite
    if( !((event.keyCode >= '48')&&(event.keyCode <= '57')||(event.keyCode == '173')||(event.keyCode == '8')) ) {
      event.preventDefault();
      return false;
    }
    if (event.ctrlKey==true && (event.which == '118' || event.which == '86')) {
          event.preventDefault();
          return false;
    }

  });
          

});
//<><><><><><><><><>Funcion para validar asociado<><><><>
  $(document).on("keyup","#CedulaAsociadoLogin",function(e){
    e.preventDefault();
    if(e.which == '13') {
      procesoAsociado();
    }
  });
  $(document).on("keyup","#claveAsociado",function(e){
    e.preventDefault();
    if(e.which == '13') {
      $("#ingresarAsociado").click();
    }
  });  
  $(document).on("click","#continuarAsociado",function(e){     
    e.preventDefault();
    procesoAsociado();
  });
//<><><><><><><><><><><><><><><><><><><><><><><><><><><><>
$(document).on("click","#confirmarCompraCredito",function(){


  if( ($.trim($("#plazosFinanciar").val()) != "")&&($.trim($("#contratoFinancia").val()) != "") ){
               
    $("#provinciaMetodoEnvio").val($("#provinciaOrder").val());
    $("#cantonMetodoEnvio").val($("#cantonOrder").val());
    $("#distritoMetodoEnvio").val($("#distritoOrder").val());
    $("#pobladoMetodoEnvio").val($("#pobladoOrder").val());
    $("#direccionMetodoEnvio").val($("#direccionOrder").val());
     
    $("#montoOrder").val(formatoNormal($.trim($("#precioTotalCredito").html())) );

    $("#totalMontoCredito").val(formatoNormal($.trim($("#totalFinanciar").html())) );
    $("#totalMontoOrder").val(formatoNormal($.trim($("#totalFinanciar").html())) );

    $("#costoMontoCredito").val(formatoNormal($.trim($("#totalFinanciar").val())) );
    $("#costoMontoOrder").val(formatoNormal($.trim($("#totalFinanciar").val())) );

    $("#montoCompraCredito").val(formatoNormal($.trim($("#baseFinanciar").html())) );
    $("#baseFinanciaOrder").val(formatoNormal($.trim($("#baseFinanciar").html())) );
    
    $("#contratoCredito").val($.trim($("#contratoFinancia").val()) );
    $("#contratoOrder").val($.trim($("#contratoFinancia").val()) );    
    $("#primaCredito").val(formatoNormal($("#primaCreditoCalculadora").val()));
    $("#primaOrder").val(formatoNormal($("#primaCreditoCalculadora").val()));
    $("#plazoCredito").val($("#plazosFinanciar").val());
    $("#plazoOrder").val($("#plazosFinanciar").val());


    

    actualizaSesion();

    if($.trim($("#autorizadoRetirarOrder").val()) == ""){
      $("#autorizadoRetirarCredito").val("");
    }else{
      $("#autorizadoRetirarCredito").val($("#autorizadoRetirarOrder").val());
    }
    
    $.ajax({
          url: BASE_URL+'inicio/index/Ajax',
          beforeSend: function(){
           if(obtenerIdioma(window.location.pathname) == "es"){
            $("#mensajeCalculadoraCredito").html("Generando orden de crédito...  <i class='fas fa-cog fa-spin'></i>");
           }else{
            $("#mensajeCalculadoraCredito").html("Creating credit order...  <i class='fas fa-cog fa-spin'></i>");
           }  
           
           $("#mensajeCalculadoraCredito").fadeIn("fast");
          },
          data: $("#listArticles").serialize(),
          type: "POST",
          dataType: "json"
     }).done(function (data) {  

        $("#mensajeCalculadoraCredito").html("");
        if( (parseInt($("#primaCreditoCalculadora").val()) == 0) && (parseInt(sessionStorage.getItem("primaCredito")) == parseInt("0")) ){

          $.ajax({
              url: 'respuesta/imagineer/pago',
              beforeSend: function(){

                 if(obtenerIdioma(window.location.pathname) == "es"){
                  $("#mensajeCalculadoraCredito").html("Enviando orden crédito...  <i class='fas fa-cog fa-spin'></i>");
                 }else{
                  $("#mensajeCalculadoraCredito").html("Sending the credit order...  <i class='fas fa-cog fa-spin'></i>");
                 }
                          
              },
              data: "x_response_code=1&"+$("#listArticles").serialize(),
              type: "POST"
            }).done(function (data) { 

                var idioma = sessionStorage.getItem("Idioma");
                var almacen = localStorage.getItem("Almacen"); 
                if($.trim(idioma) == ""){
                  idioma = localStorage.getItem("Idioma");
                }
                if($.trim(almacen) == ""){
                  almacen = sessionStorage.getItem("Almacen");
                }
                var almacenRedirect = almacen.toUpperCase();

                var path = window.location.pathname;
                path = remplazaPath(path);
        
                window.location.href = data;              
                
            }); 

        }else{    
          //DJL  31-01-19                         
            //$("#x_amount").val(formatoNormal($("#primaCreditoCalculadora").val()));          
      	    $("#x_amount").val("1.00");
            $.ajax({
              url: BASE_URL+'inicio/index/Ajax',
              beforeSend: function(){
                 if(obtenerIdioma(window.location.pathname) == "es"){
                  $("#mensajeCalculadoraCredito").html("Enviando cobro de prima...  <i class='fas fa-cog fa-spin'></i>");
                 }else{
                  $("#mensajeCalculadoraCredito").html("Sending the payment advance...  <i class='fas fa-cog fa-spin'></i>");
                 }
                          
              },
              data: "accion=10&x_login="+$("#x_login").val()+"&x_fp_sequence="+$("#x_fp_sequence").val()+"&x_fp_timestamp="+$("#x_fp_timestamp").val()+"&x_amount="+$("#x_amount").val(),
              type: "POST",
              dataType: "json"
            }).done(function (data) { 
                var pagoPrima = data.Mensaje;
                var valores = JSON.parse(pagoPrima);
                
                $("#x_fp_hash").val(valores.hash);
                $("#tipoCompra").val("FIN");
                $("#tipoPago").val("FIN");                
                $("#tipoCompraOrder").val("FIN");
  
                
                $("#x_amount").val("1.00");

                actualizaSesion();
                $("#pagoContado").fadeOut("fast");
                setTimeout(function(){
                  $("#bcrForm").submit();
                },3000);
            }); 
        }
     });

   }else{
          if(obtenerIdioma(window.location.pathname) == "es"){
              require([
                  'Magento_Ui/js/modal/alert'
              ], function(alert) {  
                  alert({
                      title: 'Info',
                      content: 'Hay campos requeridos que no estan seleccionados',
                      actions: {
                          always: function(){}
                      }
                  });
              
              });
          }else{
            require([
                  'Magento_Ui/js/modal/alert'
              ], function(alert) {  
                  alert({
                      title: 'Info',
                      content: 'You must fill all the required fields',
                      actions: {
                          always: function(){}
                      }
                  });
              
              });
          }
        }
});
   
$(document).on("click","#ingresarAsociado",function(){

     $("#ventana-inicio > form").find("input[name='accion']").val("3");
     var accion = $("#ventana-inicio > form").find("input[name='accion']").val();

     var cedula = sessionStorage.getItem("cedula_asociado");  
     var postDatos = "accion="+accion+"&claveAsociado="+$("#claveAsociado").val()+"&CedulaAsociadoLogin="+$("#cedulaValida").text();    
     $("#clienteCedula").val(cedula);
     $("#idCuentaCredito").val(cedula); 
     $("#idCuentaCreditoLista").val(cedula);                
     $("#idCuentaContrato").val(cedula);     
     $("#cedulaOrder").val(cedula);
     $("#CedulaAsociadoLogin").val(cedula);

     $.ajax({
          url: BASE_URL+'inicio/index/Ajax',
          beforeSend: function(){
            if(obtenerIdioma(window.location.pathname) == "es"){
               $("#mensaje-inicio").html("Enviando...  <i class='fas fa-cog fa-spin'></i>");
            }else{
              $("#mensaje-inicio").html("Sending data...  <i class='fas fa-cog fa-spin'></i>");
            } 
           
          },
          data: postDatos,
          type: "POST",
          dataType: "json"
     }).done(function (data) {  
           if(obtenerIdioma(window.location.pathname) == "es"){
            $("#mensaje-inicio").html("<p>Bienvenido: asociado!</p>");
           }else{
            $("#mensaje-inicio").html("<p>Welcome: associate!</p>");
           }
          
          setTimeout(function(){
            $("#mensaje-inicio").html("");
          },3000);

          var ingresaAsociado = data.Asociado;

          if( (ingresaAsociado !== undefined) ){

            if( ingresaAsociado.CodigoRespuesta == "0" ){ 

               if( $("#tipoPago").length > 0 ){

                if($.trim(localStorage.getItem("guestAction")) == ""){
                    estaLogueado(); 
                    var sesion = sessionStorage.getItem("isUser");

                    if( $.trim(sesion) == "" ){                  
                      opcionLogin();
                    }else{
                      opcionPago();   
                    }
                }else{
                  var idioma = sessionStorage.getItem("Idioma");
                  var almacen = sessionStorage.getItem("Almacen");  
                  if($.trim(idioma) == ""){
                    idioma = localStorage.getItem("Idioma");
                  }
                  if($.trim(almacen) == ""){
                    almacen = localStorage.getItem("Almacen");  
                  }
                  var almacenRedirect = almacen.toUpperCase();

                  var path = window.location.pathname;
                  path = remplazaPath(path);
           

                  if(localStorage.getItem("guestAction") == "ingresar" ){
                    var direccionActual="https://"+window.location.hostname+"/"+almacenRedirect+"_"+idioma+path+"?___store="+almacen+"_"+idioma;
                    location.href = direccionActual;
                  }else{
                    var sniffCart = path.indexOf("checkout");

                    if(sniffCart === -1){
                      var direccionWishlist="https://"+window.location.hostname+"/"+almacenRedirect+"_"+idioma+"/wishlist/?___store="+almacen+"_"+idioma;
                      location.href = direccionWishlist;
                    }else{
                      var direccionCheck="https://"+window.location.hostname+"/"+almacenRedirect+"_"+idioma+"/checkout";
                      location.href = direccionCheck;
                    }
                  }
                  
                }    
                              
               }
               
            }
              
          }else{
            $("#mensaje-inicio").html("<p class='warning'>Lo sentimos, la contraseña indicada no es válida. Por favor intente de nuevo.</p>");
            setTimeout(function(){
              $("#mensaje-inicio").html("");
            },2500);
          }

     });
});
function agregaOpcionComparar(){
  if(sessionStorage.getItem("Idioma") === null){
    var idioma = "es";
  }else{
    var idioma = sessionStorage.getItem("Idioma");
  }
  if(sessionStorage.getItem("Almacen") === null){
    var almacen = "T001";
  }else{
    var almacen = sessionStorage.getItem("Almacen");
  }  

  var almacenRedirect = almacen.toUpperCase();
  var direccionCompare="https://"+window.location.hostname+"/"+almacenRedirect+"_"+idioma+"/catalog/product_compare/index/?___store="+almacen+"_"+idioma;

  if(idioma == "es"){
    $("li.link.wishlist").after("<li class='link comparar'><a href='"+direccionCompare+"'>Comparar</a></li>");
  }else{
    $("li.link.wishlist").after("<li class='link comparar'><a href='"+direccionCompare+"'>Compare</a></li>");
  } 
  
}
function remplazaPath(path){
  path = path.split("/");

  var array = $.map(path, function(value, index) {
    return [value];
  });
  
  if(array.length >= 2){ 
    array.splice(1,1);
  }
  path = array.join("/");
  return path;
}
function cargaChat(){
  //<><><><><><><><><> CHAT <><><><><><><><><><>
    window.intercomSettings = { app_id: "vlg7a39u"};
    (function(){var w=window;var ic=w.Intercom;if(typeof ic==="function"){ic('reattach_activator');ic('update',intercomSettings);}else{var d=document;var i=function(){i.c(arguments)};i.q=[];i.c=function(args){i.q.push(args)};w.Intercom=i;function l(){var s=d.createElement('script');s.type='text/javascript';s.async=true;s.src='https://widget.intercom.io/widget/vlg7a39u';var x=d.getElementsByTagName('script')[0];x.parentNode.insertBefore(s,x);}if(w.attachEvent){w.attachEvent('onload',l);}else{w.addEventListener('load',l,false);}}})()
    //<><><><><><><<><><><><><><><><><><><><><<><>
}
function cargaListaAlmacenes(){

    $.ajax({ 
          url: BASE_URL+'inicio/index/Ajax',
          type: "POST",
          data: {accion: "2"},
          dataType: "json"
    }).done(function (data) {

          if(data !== null){
          var datos = JSON.parse(data.Almacenes);
          $.each( datos.ListaAlmacenes, function( key, value ) {
          
            $("#select-menu-almacen").append($('<option>', {                        
                                                          value: value.IdAlmacen,
                                                          text: value.Nombre
                                                        }));
            var selectOptim = "";
            $.each( $("#select-menu-almacen option"), function( key, valor ) {
              selectOptim += "<li data-key='"+valor.value+"'>"+valor.text+"</li>";
            });
            $("#menu-almacen-desplegable").html(selectOptim);
         });

          var idioma = sessionStorage.getItem("Idioma")
          if(  idioma === null ){
            idioma = localStorage.getItem("Idioma");
          }
         
          var almacen = sessionStorage.getItem("Almacen");
          if( almacen === null ){
            almacen = localStorage.getItem("Almacen");            
          }

          $('#menu-almacen-desplegable *[data-key="'+almacen+'"]').text();
          $("#select-menu-almacen").find("option[value='"+almacen+"']").attr("selected","selected");
          $("#almacen-activo").html($('#menu-almacen-desplegable *[data-key="'+almacen+'"]').text());
        }else{
           ventanaBienvenida(); 
        }
    });

    

}
function calculaCredito(){
  $.ajax({
    url: BASE_URL+'inicio/index/Ajax',
    beforeSend: function(){
       if(obtenerIdioma(window.location.pathname) == "es"){
        $("#mensajeCalculadoraCredito").html("Calculando...  <i class='fas fa-cog fa-spin'></i>");
       }else{
        $("#mensajeCalculadoraCredito").html("Obtaining data...  <i class='fas fa-cog fa-spin'></i>");
       }
      
      $("#mensajeCalculadoraCredito").fadeIn("fast");
    },
    data: $("#listArticles").serialize(),
    type: "POST",
    dataType: "json"
    }).done(function (data) {    
            
      $.ajax({
        url: BASE_URL+'inicio/index/Ajax',
        data: $("#contratosForm").serialize(),
        type: "POST",
        dataType: "json"
      }).done(function (data) { 
          var listaAsociadosCredito = data.ListaContratosAsociado;
          $.each(listaAsociadosCredito, function(item,value) {
            $("#contratoFinancia").append("<option value="+value+">"+value+"</option>");
          });
      });

      setTimeout(function(){
        $("#mensajeCalculadoraCredito").html("");
        $("#mensajeCalculadoraCredito").fadeOut("fast");  
      },3000);
    

       var credito = data.CalculoCredito;
       var creditoFinacia = JSON.parse(credito);       
       var financiamiento = creditoFinacia.CalculoFinanciamiento;
       var mensaje = creditoFinacia.DescripcionRespuesta;

       if(financiamiento === null ){

         var montoCompra = $("#montoOrder").val();
        

         if(obtenerIdioma(window.location.pathname) == "es"){
          $("#wsInfoCredito").html("Monto total a financiar: <span class='resaltaFinancia'>₡"+montoCompra+"</span>, Cuota: <span class='resaltaFinancia'>₡"+formateaNumeros(financiamiento.Cuota)+"</span>, Plazo: <span class='resaltaFinancia'>"+financiamiento.Plazo+" meses</span>");
         }else{
          $("#wsInfoCredito").html("Total financing amount: <span class='resaltaFinancia'>₡"+montoCompra+"</span>, Fee: <span class='resaltaFinancia'>₡"+formateaNumeros(financiamiento.Cuota)+"</span>, Time limit: <span class='resaltaFinancia'>"+financiamiento.Plazo+" months</span>");
         }
         
         $("#baseFinanciar").html(formateaNumeros(financiamiento.BaseFinanciamiento));
         $("#tasaAnualFinanciar").html(financiamiento.TasaInteres);
         $("#cuotaFinanciar").html(formateaNumeros(financiamiento.Cuota));
         $("#interesFinanciar").html(formateaNumeros(financiamiento.Intereses));
         $("#totalFinanciar").html(formateaNumeros(financiamiento.MontoTotalFinanciamiento));
           /*   CARGAR PLAZOS  */
           $("#plazosFinanciar").html("");
           $.each(financiamiento.Plazos.reverse(), function(item,value) {
             $("#plazosFinanciar").append("<option value="+value+">"+value+"</option>");
           });
           /*******************/
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
function procesoAsociado(){
    var cedula = $("#CedulaAsociadoLogin").val();
    if( (($.trim($("div.panel.header ul.header.links li.authorization-link a").text())) == "Ingresar")||
        (($.trim($("div.panel.header ul.header.links li.authorization-link a").text())) == "Sign In") ){  // guest :|
      solicitarLogin(true,cedula);
    }else{
      solicitarLogin(false,cedula);
    }

}
function opcionPago(){
  if($.trim($("#tipoPago").val()) == "FIN"){
     $("#ventana-inicio").dialog("close");
     location.reload(true);
  }else{
    if($.trim($("#tipoPago").val()) == "CON"){
      $("button.ui-dialog-titlebar-close").click();
      $("#x_amount").val("1.00");
      $("#tipoPago").val("CON");
      $("#tipoCompraOrder").val("CON");      
      actualizaSesion();
      $("#pagoContado").fadeOut("fast");
      setTimeout(function(){
        $("#bcrForm").submit();
      },3000);
    }else{
      $("#ventana-inicio").dialog("close");
      location.reload(true);
    }
  }
}
function opcionLogin(){

  $( "#ventana-inicio" ).dialog("close");
  var idioma = sessionStorage.getItem("Idioma");
  var almacen = sessionStorage.getItem("Almacen");  
  if($.trim(idioma) == ""){
    idioma = localStorage.getItem("Idioma");
  }
  if($.trim(almacen) == ""){
    almacen = localStorage.getItem("Almacen");
  }
  var almacenRedirect = almacen.toUpperCase();

  var path = window.location.pathname;
  path = remplazaPath(path);

  var shippingLogin="https://"+window.location.hostname+"/"+almacenRedirect+"_"+idioma+"/checkout";
  location.href = shippingLogin;
  
}
function solicitarLogin(guest,cedula){
      $("#ventana-inicio > form").find("input[name='accion']").val("1");
      $.ajax({
            url: BASE_URL+'inicio/index/Ajax',
            beforeSend: function(){
              if(obtenerIdioma(window.location.pathname) == "es"){
                  $("#mensaje-inicio").html("Enviando...  <i class='fas fa-cog fa-spin'></i>");
                 }else{
                  $("#mensaje-inicio").html("Sending...  <i class='fas fa-cog fa-spin'></i>");
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
                
                if( ($.trim(localStorage.getItem("guestAction")) == "ingresar")||($.trim(localStorage.getItem("guestAction")) == "favoritos") ){
                    mostrarClave();                    
                }else{
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
                    localStorage.setItem("isUser","");

                   
                      //Cerrar la ventana de la cédula
                      $("button.ui-dialog-titlebar-close").click();
                      var almacen = sessionStorage.getItem("Almacen"); 
                      if($.trim(almacen) == ""){
                        almacen = localStorage.getItem("Almacen"); 
                      }

                      var idioma = sessionStorage.getItem("Idioma"); 
                      if($.trim(almacen) == ""){
                        idioma = localStorage.getItem("Idioma"); 
                      }  
                      var almacenRedirect = almacen.toUpperCase();
                      var direccionCheckout="https://"+window.location.hostname+"/"+almacenRedirect+"_"+idioma+"/checkout#shipping";
                      location.href = direccionCheckout;
                    
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
function mostrarClave(){
  $("#ventana-inicio > form").find("input[name='accion']").val("3");
  $("#CedulaAsociadoLogin").fadeOut("fast");
  $("#titularAsociado").css("font-size","17px");
   if(obtenerIdioma(window.location.pathname) == "es"){
    $("#titularAsociado").text("Confirmar datos del asociado");
   }else{
    $("#titularAsociado").text("Confirm associate's data");
   }
  

  $("#campoPass, #claveAsociado").fadeIn("fast");
  $("#cedulaValida").text( $("#CedulaAsociadoLogin").val() );
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
function resetVentanaLogin(){
  var cedula = sessionStorage.getItem("cedula_asociado");  
  var accion = localStorage.getItem("guestAction");
  if( (cedula !== null)&&(accion == "ingresar") ){
    localStorage.setItem("guestAction","");
    
    if(obtenerIdioma(window.location.pathname) == "es"){
      $("#titularAsociado").text("Datos del Asociado");
    }else{
      $("#titularAsociado").text("Associate's data");
    }
    
    $("#campoPass, #claveAsociado").fadeOut("fast");
    $("#cedulaValida").fadeOut("fast");
    $("#ventana-inicio form label").fadeIn("fast");

    $("#continuarAsociado").fadeIn("fast");
    $(".tooltipClave").fadeOut("fast");
    $(".tooltipActivar").fadeOut("fast");
    $("#ingresarAsociado").fadeOut("fast");
    $("#activarCuenta").fadeOut("fast");
    $("#CedulaAsociadoLogin").fadeIn("fast");
    $("#olvidePass").fadeIn("fast");
    $("img.tooltipCustom").fadeIn("fast");
  }else{
    var almacen = sessionStorage.getItem("Almacen"); 
    if($.trim(almacen) == ""){
      almacen = localStorage.getItem("Almacen"); 
    }

    var idioma = sessionStorage.getItem("Idioma"); 
    if($.trim(almacen) == ""){
      idioma = localStorage.getItem("Idioma"); 
    } 
    location.href=BASE_URL+"?___store="+almacen+"_"+idioma;
  }
}
function ventanaBienvenida(){
  $( "#ventana-bienvenido" ).dialog({
      modal: true,
      draggable: false,
      closeOnEscape: false,
      dialogClass: 'no-close',
      dialogClass: 'bienvenido',
      buttons: {
        "Ingresar": function() {
            
            var idioma = $("#opciones-idioma").val();
            var almacen = $("#opciones-almacen").val();

            if($.trim(almacen) != ""){

              if( $("#recordar-almacen").prop("checked") ){
                  localStorage.setItem("Almacen", almacen);
                
              }else{
                  sessionStorage.setItem("Almacen", almacen);
              }

              if( $("#recordar-idioma").prop("checked") ){
                  localStorage.setItem("Idioma", idioma);
              }else{
                  sessionStorage.setItem("Idioma", idioma);
              }
              var almacenRedirect = almacen.toUpperCase();

              location.href=BASE_URL+almacenRedirect+"_"+idioma+"?___store="+almacen+"_"+idioma;

            }else{
              $("#mensaje-bienvenido").html("");
              $("#opciones-almacen").css("border","2px solid red");
              if(obtenerIdiomaLogin() == "es"){
                $("#mensaje-bienvenido").html("<p>Por favor seleccione su almacén de preferencia.</p>");
               }else{
                $("#mensaje-bienvenido").html("<p>You must choose a warehouse</p>");
               } 
              
              $("#mensaje-bienvenido").fadeIn("fast");
              setTimeout(function(){
                $("#mensaje-bienvenido").fadeOut("fast");
                $("#opciones-almacen").css("border","2px solid #414042");
                $("#mensaje-bienvenido").html("");
              },3000);  
            }
          
        }
      },
      open: function( event, ui ) {
          
          $(".ui-dialog .ui-dialog-titlebar").css("display","none");
          $.ajax({
                url: BASE_URL+'inicio/index/Ajax',
                data: $("#ventana-bienvenido > form").serialize(),
                type: "POST",
                dataType: "json"
          }).done(function (data) {
                var datos = JSON.parse(data.Almacenes); 
                 if(obtenerIdiomaLogin() == "es"){
                  $("#opciones-idioma").html('<option selected="selected" value="es">Español</option><option value="en">Inglés</option>');
                  $("#opciones-almacen").append('<option selected="selected" value="">Seleccione un almacén</option>');
                 }else{
                  $("#opciones-idioma").html('<option selected="selected" value="en">English</option><option value="es">Spanish</option>');
                  $("#opciones-almacen").append('<option selected="selected" value="">Select a warehouse</option>');
                 }                
               $.each( datos.ListaAlmacenes, function( key, value ) {

                  $("#opciones-almacen").append($('<option>', {                        
                                                                value: value.IdAlmacen,
                                                                text: value.Nombre
                                                              }));

                  $("#select-menu-almacen").append($('<option>', {                        
                                                                value: value.IdAlmacen,
                                                                text: value.Nombre
                                                              }));
                
               });
          });
      }
  });
}
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
function cambioIdioma(){
  

  if( sessionStorage.getItem("Almacen") !== null ){
   var almacen = sessionStorage.getItem("Almacen");
   var almacenRedirect = almacen.toUpperCase();
  }else{
    if( localStorage.getItem("Almacen") !== null ){
      var almacen = sessionStorage.getItem("Almacen");
      var almacenRedirect = almacen.toUpperCase();
    }else{
      var almacen = "T001";
      var almacenRedirect = almacen.toUpperCase();
    }     
  }

  if( sessionStorage.getItem("Idioma") !== null ){
   var idioma = sessionStorage.getItem("Idioma");
  }else{
    if( localStorage.getItem("Idioma") !== null ){
      var idioma = localStorage.getItem("Idioma");
    }else{
      var idioma = "es"; 
    }
  } 
  var path = window.location.pathname;
  //validar que el URL que esta en el path sea el de sesión correcto
  if(idioma== "es"){
    idioma = "en";
    if( sessionStorage.getItem("Idioma") !== null ){
      sessionStorage.setItem("Idioma","en");
     }else{
       if( localStorage.getItem("Idioma") !== null ){
        localStorage.setItem("Idioma","en");
       }else{
         var idioma = "en"; 
       }
     } 
  }else if(idioma == "en"){
    idioma = "es";
    if( sessionStorage.getItem("Idioma") !== null ){
      sessionStorage.setItem("Idioma","es");
     }else{
       if( localStorage.getItem("Idioma") !== null ){
        localStorage.setItem("Idioma","es");
       }else{
         var idioma = "es"; 
       }
     } 
  }
  path = remplazaPath(path);
  var sniffSearch = path.indexOf("catalogsearch/result");

  if(sniffSearch === -1){
    location.href=window.location.origin+"/"+almacenRedirect+"_"+idioma+path+"?___store="+almacen+"_"+idioma;
  }else{                
    var q = $("#search").val();
    location.href=window.location.origin+"/"+almacenRedirect+"_"+idioma+path+"?___store="+almacen+"_"+idioma+"&q="+q;
  }
  
  
}
function obtenerIdioma(path){
  var index = path.indexOf("_en");

  if(index != -1){
    return "en";
  }else{
    return "es";
  }

}
function obtenerIdiomaLogin(){
  var idioma = sessionStorage.getItem("Idioma");
  if(idioma === null){
    idioma = localStorage.getItem("Idioma");
  }
  if(idioma === null){
    idioma = "es";
  }
  return idioma;
}
function validaUrl(){

  var path = window.location.pathname;
  var idioma = sessionStorage.getItem("Idioma");
  
  var almacen = sessionStorage.getItem("Almacen");
  //validar si el usuario definio almacen por preferencia
  if(almacen === null){
    almacen = localStorage.getItem("Almacen");
  }
  //Si ya no existe en ningun tipo de sesion definir el default
  if(almacen === null){
    almacen = "T001";
  }

  if(idioma === null){
    idioma = localStorage.getItem("Idioma");
  }
  if(idioma === null){
    idioma = "es";
  }
  //validar que el URL que esta en el path sea el de sesión correcto
  var sniffUrlPath = path.indexOf(almacen);

  var indexEN = path.indexOf("_en");
  var indexES = path.indexOf("_es");

  var sniffUrlUpdateCart = path.indexOf("checkout/cart");

  if(sniffUrlUpdateCart === -1){
    sessionStorage.removeItem("accionCarrito");
  }

  if( ($("#menu-herramientas").length > 0)&&((sniffUrlUpdateCart === -1)) ){
    localStorage.removeItem("cedula_asociado");
    localStorage.setItem("isUser","");
  }

  if( ((indexES === -1)&&(indexEN === -1))||(sniffUrlPath === -1) ){

    if(idioma !== null){
      var path = window.location.pathname;
      path = remplazaPath(path);
      location.href=window.location.origin+"/"+almacen+"_"+idioma+path+"?___store="+almacen+"_"+idioma;
    }  

  }

}

});
