define(
    [
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/resource-url-manager',
        'mage/storage',
        'Magento_Checkout/js/model/payment-service',
        'Magento_Checkout/js/model/error-processor',
        'Magento_Checkout/js/model/payment/method-converter'
    ],
    function (quote, resourceUrlManager, storage, paymentService, errorProcessor, methodConverter) {
        'use strict';
        return {
            saveShippingInformation: function() {
                var shippingAddress = {},
                    payload;

                shippingAddress.extension_attributes = {
                    %address extension attributes%
                };

                payload = {
                    addressInformation: {
                        shipping_address: shippingAddress,
                        shipping_method_code: quote.shippingMethod().method_code,
                        shipping_carrier_code: quote.shippingMethod().carrier_code
                    }
                };

                return storage.post(
                    resourceUrlManager.getUrlForSetShippingInformation(quote),
                    JSON.stringify(payload)
                ).done(
                    function (response) {
                        paymentService.setPaymentMethods(methodConverter(response.payment_methods));
                        quote.setTotals(response.totals)
                    }
                ).fail(
                    function (response) {
                        errorProcessor.process(response);
                    }
                );
            }
        }
    }
);