/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'Magento_Checkout/js/view/summary/abstract-total'
], function (viewModel) {
    'use strict';

    return viewModel.extend({
        defaults: {
            displayArea: 'after_details',
            template: 'Magento_Checkout/summary/item/details/subtotal'
        },

        /**
         * @param {Object} quoteItem
         * @return {*|String}
         */
        getValue: function (quoteItem) {
            return this.getFormatTotals(quoteItem['row_total']);
        },
        getFormatTotals: function(n){  
        
            n = n.toString();                  
            if( n.length >= 7 ){               
                n = n.replace(/,/g, '');                
                var formato = n.split(".");   
                var pattern = formato[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");  
                if(formato[1] !== undefined){
                    var decimales = formato[1].substr(0,2);                            
                }else{
                    var decimales = "00";
                }    
                var numeroFinal = pattern+"."+decimales;                        
                return "₡"+numeroFinal;
            }else{
                return "₡"+n;
            }
            
        }
    });
});
