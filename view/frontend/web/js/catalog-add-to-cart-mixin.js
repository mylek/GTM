define([
    'jquery',
    'mage/translate',
    'underscore',
    'Magento_Catalog/js/product/view/product-ids-resolver',
    'jquery-ui-modules/widget'
], function ($, $t, _, idsResolver) {
    'use strict';

    return function (widget) {
        $.widget('mage.catalogAddToCart', widget, {
            /**
             * @param {jQuery} form
             */
            ajaxSubmit: function (form) {
                var self = this,
                    productIds = idsResolver(form),
                    formData;

                $(self.options.minicartSelector).trigger('contentLoading');
                self.disableAddToCartButton(form);
                formData = new FormData(form[0]);

                $.ajax({
                    url: form.attr('action'),
                    data: formData,
                    type: 'post',
                    dataType: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,

                    /** @inheritdoc */
                    beforeSend: function () {
                        if (self.isLoaderEnabled()) {
                            $('body').trigger(self.options.processStart);
                        }
                    },

                    /** @inheritdoc */
                    success: function (res) {
                        var eventData, parameters;

                        var variants = [];
                        $('div.swatch-attribute .swatch-option').each(function (k,v) {
                            var valSelector = $(v).attr('aria-checked');
                            if (valSelector === "false") {
                                return;
                            }
                            var val = $(v).attr('aria-label');
                            var idOptions = $(v).attr('aria-describedby');
                            var name = $('#' + idOptions).text();

                            if (val !== null && name !== null) {
                                variants.push(name + ': ' + val);
                            }
                        });

                        $('.product-options-wrapper .field').each(function (k,v) {
                            var name = $(v).find('.label span').text();
                            var val = $(v).find('.select2-selection__rendered').text();

                            if (val !== null && name !== null) {
                                variants.push(name + ': ' + val);
                            }
                        });

                        $(document).trigger('ajax:addToCart', {
                            'sku': form.data().productSku,
                            'productIds': productIds,
                            'form': form,
                            'response': res
                        });

                        var price = $('.product-info-price span meta[itemprop="price"]').attr("content");
                        var name = $('h1.page-title span').text();
                        var categoryName = $('.breadcrumbs .items .item.product strong').text();
                        var qty = $('#qty').val();
                        var currency = $('meta[property="product:price:currency"]').attr("content");

                        dataLayer.push({ ecommerce: null });
                        dataLayer.push({
                            'event': 'addToCart',
                            'ecommerce': {
                                'currencyCode': currency,
                                'add': {
                                    'products': [{
                                        'name': name,
                                        'id': productIds[0],
                                        'price': price,
                                        'brand': '',
                                        'category': categoryName,
                                        'variant': variants.join(', '),
                                        'quantity': qty
                                    }]
                                }
                            }
                        });

                        if (self.isLoaderEnabled()) {
                            $('body').trigger(self.options.processStop);
                        }

                        if (res.backUrl) {
                            eventData = {
                                'form': form,
                                'redirectParameters': []
                            };
                            // trigger global event, so other modules will be able add parameters to redirect url
                            $('body').trigger('catalogCategoryAddToCartRedirect', eventData);

                            if (eventData.redirectParameters.length > 0) {
                                parameters = res.backUrl.split('#');
                                parameters.push(eventData.redirectParameters.join('&'));
                                res.backUrl = parameters.join('#');
                            }

                            self._redirect(res.backUrl);

                            return;
                        }

                        if (res.messages) {
                            $(self.options.messagesSelector).html(res.messages);
                        }

                        if (res.minicart) {
                            $(self.options.minicartSelector).replaceWith(res.minicart);
                            $(self.options.minicartSelector).trigger('contentUpdated');
                        }

                        if (res.product && res.product.statusText) {
                            $(self.options.productStatusSelector)
                                .removeClass('available')
                                .addClass('unavailable')
                                .find('span')
                                .html(res.product.statusText);
                        }
                        self.enableAddToCartButton(form);
                    },

                    /** @inheritdoc */
                    error: function (res) {
                        $(document).trigger('ajax:addToCart:error', {
                            'sku': form.data().productSku,
                            'productIds': productIds,
                            'form': form,
                            'response': res
                        });
                    },

                    /** @inheritdoc */
                    complete: function (res) {
                        if (res.state() === 'rejected') {
                            location.reload();
                        }
                    }
                });
            },
        });
    }
});
