define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/shipping-rate-service',
        'Imagineer_Address/js/model/shipping-rate-processor/customer-address',
        'Magento_Checkout/js/model/shipping-save-processor',
        'Imagineer_Address/js/model/shipping-save-processor/default'
    ],
    function (
        Component,
        shippingRateService,
        customShippingRateProcessor,
        shippingSaveProcessor,
        customShippingSaveProcessor
    ) {
        'use strict';

        /** Register rate processor */
        shippingRateService.registerProcessor(customShippingRateProcessor, customShippingRateProcessor);

        /** Register save shipping address processor */
        shippingSaveProcessor.registerProcessor(customShippingSaveProcessor, custormShippingSaveProcessor);

        /** Add view logic here if needed */
        return Component.extend({});
    }
);