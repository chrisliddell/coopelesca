/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'Magento_Catalog/js/price-utils',
    'Magento_Checkout/js/model/totals',
    'Magento_Checkout/js/model/sidebar',
    'Magento_Ui/js/modal/confirm'
], function ($, Component, quote, priceUtils, totals, sidebarModel,confirmation) {
    'use strict';

    return Component.extend({
        isLoading: totals.isLoading,
        popUp: false,
        onRenderComplete: function () {
            
            setTimeout(function(){
                $("#seleccionMetodoPago").fadeOut("fast");
                $("#opc-sidebar").fadeIn("fast");  
                $("div[name='shippingAddress.postcode']").remove();
                $("#resumenCarrito").html($("#opc-sidebar").html());
                $("#opc-sidebar").fadeOut("fast");
                if($("button.action-select-shipping-item").length > 0){        
                  var botones = $("button.action-select-shipping-item");
                  botones[0].click();    
                } 
            },4500);
                                       
        },
        /**
         * @param {Object} popUp
         */
        setPopup: function (popUp) {
            this.popUp = popUp;
        },

        /**
         * Show popup.
         */
        show: function () {
            if (this.popUp) {
                this.popUp.modal('openModal');
            }
        },

        /**
         * Hide popup.
         */
        hide: function () {
            if (this.popUp) {
                this.popUp.modal('closeModal');
            }
        },
        getQuantity: function () {
            if (totals.totals()) {
                return parseInt(totals.totals()['items_qty']);
            }

            return 0;
        },
        getVaciarCarrito: function(){

           if(this.getIdioma(window.location.pathname) == "es"){
            return "Vaciar carrito";
           }else{
            return "Empty cart";
           }  
               
        },
        getEditarCarrito: function(){

            if(this.getIdioma(window.location.pathname) == "es"){
             return "Editar carrito";
            }else{
             return "Update cart";
            }  
                
         },
        getSeguirComprando: function(){
            
           if(this.getIdioma(window.location.pathname) == "es"){
            return "Seguir comprando";
           }else{
            return "Continue shopping";
           }            
                
        },  
        getIdioma: function(path){

            var index = path.indexOf("_en");

            if(index != -1){
              return "en";
            }else{
              return "es";
            }

        },
        vaciaCarrito: function(){
            var path = window.location.pathname;
            var idioma = "";
            var index = path.indexOf("_en");
  
            confirmation({

                title: ( (index != -1) ? 'Are you sure to clear the cart ?'  : '¿ Está seguro que quiere borrar todo el carrito ?'),
                content: ( (index != -1) ? 'All the cart information is going to be removed' : 'Toda la información del carrito de compras se borrará'),      
                actions: {
                    confirm: function(){
                        $.ajax({
                          url: BASE_URL+'inicio/index/Ajax',
                          data: "accion=15",
                          type: "POST",
                          dataType: "json"
                        }).done(function (data) {  
                         var mensaje = data.mensaje;
                          if(mensaje.toLowerCase() == "correcto"){
                            var path = window.location.pathname;
                            var idioma = "";
                            var index = path.indexOf("_en");
                            if(index != -1){
                              idioma = "en";
                            }else{
                              idioma = "es";
                            }
                            var ruta = path.split("/");
                            var rutaArr = Object.values(ruta);

                            if(rutaArr.length >= 2){ 
                             rutaArr.splice(2,1);
                            }
                            path = rutaArr.join("/");
                            

                            location.href="https://"+window.location.hostname+path;
                          }
                        });
                    },
                    cancel: function(){},
                    always: function(){}
                }
            });

            
           
        },
        editarCarrito: function(){
            var path = window.location.pathname;
            var idioma = this.getIdioma(path);
            var ruta = path.split("/");
            var rutaArr = Object.values(ruta);

            if(rutaArr.length >= 2){ 
             rutaArr.splice(2,1);
            }
            path = rutaArr.join("/");
            
            sessionStorage.setItem("accionCarrito","editando");
            location.href="https://"+window.location.hostname+path+"/checkout/cart/";
        },
        sigueComprando: function(){
            var path = window.location.pathname;
            var idioma = this.getIdioma(path);
            var ruta = path.split("/");
            var rutaArr = Object.values(ruta);

            if(rutaArr.length >= 2){ 
             rutaArr.splice(2,1);
            }
            path = rutaArr.join("/");
            

            location.href="https://"+window.location.hostname+path;
        },
        /**
         * @return {Number}
         */
        getPureValue: function () {

            if (totals.totals()) {
                return totals.getSegment('grand_total').value;
            }

            return 0;
        },
        
        /**
         * Show sidebar.
         */
        showSidebar: function () {
            sidebarModel.show();
        },
        /* DJL */
        getFormatTotals: function(n){ 
            if(n !== null){          
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
            }else{
              var numeroFinal = "0.00";
            }
            
          
          
          return "₡"+numeroFinal;
        },
        /* DJL */
        isFloat: function(n){
            return n != "" && !isNaN(n) && Math.round(n) != n;
        }, 
        getFormattedPrice: function (price) {                        
            return price;
        },
        getValue: function () {            
            return this.getFormatTotals(this.getPureValue());
        }
    });
});
