/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'Magento_Checkout/js/view/summary/abstract-total',
    'Magento_Checkout/js/model/quote'
], function ($, Component, quote) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Magento_Checkout/summary/shipping'
        },
        quoteIsVirtual: quote.isVirtual(),
        totals: quote.getTotals(),

        /**
         * @return {*}
         */
        getShippingMethodTitle: function () {
            var shippingMethod;

            if (!this.isCalculated()) {
                return '';
            }
            shippingMethod = quote.shippingMethod();

            return shippingMethod ? shippingMethod['carrier_title'] + ' - ' + shippingMethod['method_title'] : '';
        },

        /**
         * @return {*|Boolean}
         */
        isCalculated: function () {
            return this.totals() && this.isFullMode() && quote.shippingMethod() != null; //eslint-disable-line eqeqeq
        },

        /**
         * @return {*}
         */
        getValue: function () {
            var price;

            if (!this.isCalculated()) {
                return this.notCalculatedMessage;
            }
            price =  this.totals()['shipping_amount'];

            return this.getFormatTotals(price);
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
