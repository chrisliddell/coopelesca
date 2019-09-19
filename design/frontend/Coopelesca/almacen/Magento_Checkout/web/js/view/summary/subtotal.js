/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'Magento_Checkout/js/view/summary/abstract-total',
    'Magento_Checkout/js/model/quote'
], function (Component, quote) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Magento_Checkout/summary/subtotal'
        },

        /**
         * Get pure value.
         *
         * @return {*}
         */
        getPureValue: function () {
            var totals = quote.getTotals()();

            if (totals) {
                return totals.subtotal;
            }

            return quote.subtotal;
        },

        /**
         * @return {*|String}
         */
        getValue: function () {
            return this.getFormatTotals(this.getPureValue());
        },
        /* DJL */
        getFormatTotals: function(n){               
            n = n.toString();                  
            if( n.length >= 7 ){                
                n = n.replace(/,/g, '');                
                var formato = n.split(".");         
                var pattern = formato[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");  
                var decimales = formato[1].substr(0,2);                            
                var numeroFinal = pattern+"."+decimales;                        
                return "₡"+numeroFinal;
            }else{
                return "₡"+n;
            }
        }

    });
});
