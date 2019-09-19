define([
    'jquery',
    'Magento_Payment/js/view/payment/cc-form'
],
function ($, Component) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Imagineer_CustomCredit/payment/imagineercredit'
        },

        context: function() {
            return this;
        },

        getCode: function() {
            return 'imagineer_customcredit';
        },

        isActive: function() {
            return true;
        }
    });
}
);