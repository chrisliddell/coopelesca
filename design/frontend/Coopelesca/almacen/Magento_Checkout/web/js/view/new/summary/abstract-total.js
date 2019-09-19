/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'Magento_Catalog/js/price-utils',
    'Magento_Checkout/js/model/totals',
    'Magento_Checkout/js/model/step-navigator'
], function (Component, quote, priceUtils, totals, stepNavigator) {
    'use strict';

    return Component.extend({
        /**
         * @param {*} price
         * @return {*|String}
         */
        getFormattedPrice: function (price) {
            return this.getFormatTotals(price);
        },

        /**
         * @return {*}
         */
        getTotals: function () {
            return totals.totals();
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
                return numeroFinal;
            }else{
                return n;
            }
            
        },
        /* DJL */
        isFloat: function(n){
            return n != "" && !isNaN(n) && Math.round(n) != n;
        },

        /**
         * @return {*}
         */
        isFullMode: function () {
            if (!this.getTotals()) {
                return false;
            }

            return stepNavigator.isProcessed('shipping');
        }
    });
});
