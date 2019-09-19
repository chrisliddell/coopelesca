/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'Magento_Customer/js/model/authentication-popup',
    'Magento_Customer/js/customer-data'
], function ($, authenticationPopup, customerData) {
    'use strict';

    return function (config, element) {
        $(element).click(function (event) {
            var cart = customerData.get('cart'),
                customer = customerData.get('customer');

            event.preventDefault();

            if (!customer().firstname && cart().isGuestCheckoutAllowed === false) {
                authenticationPopup.showModal();

                return false;
            }
            $(element).attr('disabled', true);

            $.ajax({
                url: BASE_URL+'inicio/index/Ajax',
                data: "accion=16",
                type: "POST",
                dataType: "json"
              }).done(function(data){ 
          
                  if(data.mensaje == false){
                      if(obtenerIdioma(window.location.pathname) == "es"){
                        mostrarMensaje('No hay cantidad disponible en este almacen para el/los producto(s): '+data.productos+','+data.precios);                  
                        window.history.back();
                      }else{
                        mostrarMensaje("Articles unavailable in this warehouse: "+data.productos+','+data.precios);
                        window.history.back();
                      }
                  }else{
                    location.href = config.checkoutUrl;
                  }
              });
            
        });

    };
});
