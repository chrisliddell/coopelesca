define([
    'uiComponent',
    'Magento_Checkout/js/model/payment/renderer-list'
],
function (Component, rendererList) {
    'use strict';

    rendererList.push(
        {
            type: 'imagineer_customcredit',
            component: 'Imagineer_CustomCredit/js/view/payment/method-renderer/imagineercredit'
        }
    );

    /** Add view logic here if needed */
    return Component.extend({});
});