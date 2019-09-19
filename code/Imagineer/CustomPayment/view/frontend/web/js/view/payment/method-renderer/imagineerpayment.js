define([
    'jquery',
    'Magento_Payment/js/view/payment/cc-form'
],
function ($, Component) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Imagineer_CustomPayment/payment/imagineerpayment'
        },

        context: function() {
            return this;
        },

        getCode: function() {
            return 'imagineer_custompayment';
        },

        isActive: function() {
            return true;
        }
    });
}
);