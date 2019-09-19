/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'ko',
    'uiComponent',
    'Magento_Checkout/js/action/select-shipping-address',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/shipping-address/form-popup-state',
    'Magento_Checkout/js/checkout-data',
    'Magento_Customer/js/customer-data'
], function ($, ko, Component, selectShippingAddressAction, quote, formPopUpState, checkoutData, customerData) {
    'use strict';

    var countryData = customerData.get('directory-data');

    return Component.extend({
        defaults: {
            template: 'Magento_Checkout/shipping-address/address-renderer/default'
        },

    /** @inheritdoc */
    initObservable: function () {
        this._super();
        this.isSelected = ko.computed(function () {
            var isSelected = false,
                shippingAddress = quote.shippingAddress();

            if (shippingAddress) {
                isSelected = shippingAddress.getKey() == this.address().getKey(); //eslint-disable-line eqeqeq
            }

            return isSelected;
        }, this);

        return this;
    },
    /**
     * @param {String} countryId
     * @return {String}
     */
    getCountryName: function (countryId) {
       // return countryData()[countryId] != undefined ?   "Hola Costa Rica" : ''; //eslint-disable-line
       
       return countryData()[countryId] != undefined ?   countryData()[countryId].name : ''; //eslint-disable-line
    },
	getCountyName: function (test) {		

//		var settings = {
 // 			"async": false,
//			"crossDomain": true,
//			"url": "http://localhost/rest/V1/county/code/203",
//			"method": "GET",
//			"headers": {
//			    "cache-control": "no-cache",
//			    "Postman-Token": "21ffe3f8-458d-4d99-a27f-a974b737c72b"
//			}
//		}
//
//		$.ajax(settings).done(function (response) {
//	        	console.log(response);
//			return response.name;
//	        });
        },


        /** Set selected customer shipping address  */
        selectAddress: function () {
            selectShippingAddressAction(this.address());            
            checkoutData.setSelectedShippingAddress(this.address().getKey());

            var direccion = this.address();
            var customAttributes = direccion.customAttributes;
            var provincia = direccion.regionId;
            var canton = this.esEntero(customAttributes.county.value);
            var distrito = this.esEntero(customAttributes.district.value);
            var poblado = this.esEntero(customAttributes.town.value);  
            var calle_one = ( (typeof direccion.street[0] !== "undefined") ? direccion.street[0]+' ' : '' );
            var calle_two = ( (typeof direccion.street[1] !== "undefined") ? direccion.street[1]+' ' : '' );
            var calle_three = ( (typeof direccion.street[2] !== "undefined") ? direccion.street[2] : '' );
            var calle = calle_one+calle_two+calle_three;

            $("#provinciaMetodoEnvio").val(provincia);
            $("#provinciaOrder").val(provincia);
            $("#cantonMetodoEnvio").val(canton);
            $("#cantonOrder").val(canton);
            $("#distritoMetodoEnvio").val(distrito);
            $("#distritoOrder").val(distrito);
            $("#pobladoMetodoEnvio").val(poblado);
            $("#pobladoOrder").val(poblado);
            $("#direccionMetodoEnvio").val(calle);
            $("#direccionOrder").val(calle);
            var path = window.location.pathname;
            var index = path.indexOf("_en");
            var idioma = "";


            if(index != -1){
               idioma = "en";
              }else{
               idioma = "es";
            }
             $.ajax({
                url: BASE_URL+'inicio/index/Ajax',
                beforeSend: function(){
                  $("#metodosEnvioShipping").html("");
                  if(idioma == "es"){
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
                        inputsEnvioCheck += "<div class='neCheck col-lg-12 col-md-12 col-sm-12 col-xs-12'><input type='radio' id='"+value.IdTipoEntrega+"' value='"+value.Costo+"' name='opcionesEnvio' /><strong>"+value.Descripcion+"</strong></div>";
                      });

                      if(idioma == "es"){
                        inputsEnvioCheck = "<div id='opcionesEnvioShipping' class='col-lg-5 col-md-5 col-sm-12 col-xs-12'><h3 class='step-title'>Opciones de env&iacute;o</h3>"+inputsEnvioCheck+"</div><div id='otroAutorizado' class='col-lg-5 col-md-5 col-sm-12 col-xs-12'><h3 class='step-title col-lg-8 col-md-8 col-sm-12 col-xs-12'>¿Alguien mas recibe el pedido?</h3><span><select id='reciboPedido' class='col-lg-2 col-md-2 col-sm-12 col-xs-12'><option  value='NO'>NO</option><option value='SI'>SI</option></select></span><p class='col-lg-10 col-md-10 col-sm-12 col-xs-12'>Persona quien recibe el pedido ( En caso de que sea diferente al Asociado que hace la compra )</p><input type='text' id='recibePedido' name='recibePedido' /></div>";
                      }else{
                        inputsEnvioCheck = "<div id='opcionesEnvioShipping' class='col-lg-5 col-md-5 col-sm-12 col-xs-12'><h3 class='step-title'>Shipping methods</h3>"+inputsEnvioCheck+"</div><div id='otroAutorizado' class='col-lg-5 col-md-5 col-sm-12 col-xs-12'><h3 class='step-title col-lg-8 col-md-8 col-sm-12 col-xs-12'>Does someone else receive the order?</h3><span><select id='reciboPedido' class='col-lg-2 col-md-2 col-sm-12 col-xs-12'><option  value='NO'>NO</option><option value='SI'>YES</option></select></span><p class='col-lg-10 col-md-10 col-sm-12 col-xs-12'>Person who receives the order (In case it's different from the associate who makes the purchase)</p><input type='text' id='recibePedido' name='recibePedido' /></div>";  
                      }
                      window.inputsEnvioCheck = inputsEnvioCheck;                

                }else{       
                  if(idioma == "es"){
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
        },
        /**
         * Edit address.
         */
        editAddress: function () {
            formPopUpState.isVisible(true);
            this.showPopup();

        },
        esEntero: function(x){

            if( isNaN(parseInt(x)) ){
                return this.esEntero(x.value);
            }else{
                return x;
            }

        },
        /**
         * Show popup.
         */
        showPopup: function () {
            $('[data-open-modal="opc-new-shipping-address"]').trigger('click');
        }

    });
});
