define([
    'jquery',
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote'
], function ($, wrapper,quote) {
    'use strict';
           
    return function (setShippingInformationAction) {
        return wrapper.wrap(setShippingInformationAction, function (originalAction, messageContainer) {
            
            if (messageContainer.custom_attributes != undefined) {                


                $.each(messageContainer.custom_attributes , function( key, value ) {
                    messageContainer['custom_attributes'][key] = {'attribute_code':key,'value':value};
                });

            } else {
                messageContainer['custom_attributes']  = {};
                
                messageContainer['custom_attributes']['county'] =  {'attribute_code':'county','value':messageContainer['county']}; 
                messageContainer['custom_attributes']['district'] = {'attribute_code':'district','value':messageContainer['district']}; 
                messageContainer['custom_attributes']['town'] = {'attribute_code':'town','value':messageContainer['town']}; 
            }            

            return originalAction(messageContainer);
        });
    };
});