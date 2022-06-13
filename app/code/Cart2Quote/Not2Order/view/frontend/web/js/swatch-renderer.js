/*
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery'
], function ($) {
    'use strict';

    return function (widget) {
        $.widget('mage.SwatchRenderer', widget, {
            options: {
                selectorAddToCart: '[data-role=addToCartButton]',
                selectorAddToCartInstant: '#instant-purchase',
                selectorAddToCartInstantExtra: '[data-action=checkout-form-submit]',
                selectorPrice: '[data-role=priceBox]'
            },

            _init: function () {
                this._super();
                this._CheckForButtonChanges();
                this._CheckForPriceDisplayChanges();
            },

            _OnClick: function ($this, widget, eventName) {
                this._super($this, widget, eventName);
                widget._CheckForButtonChanges();
                widget._CheckForPriceDisplayChanges();
            },

            _CheckForPriceDisplayChanges: function () {
                var widget = this,
                    hideprice = widget.options.jsonConfig.not2order_hide_price,

                    displayPriceYesNo = widget.element.parents(widget.options.selectorProduct)
                        .find(widget.options.selectorPrice),
                    selectedProduct = this.getProduct(),

                    // default display price value is true
                    displayPrice = true;

                if (typeof selectedProduct !== "undefined") {
                    displayPrice = hideprice[selectedProduct] == 'undefined' ? false : hideprice[selectedProduct];
                }

                //show or hide price
                displayPriceYesNo.toggle(displayPrice);
            },

            _CheckForButtonChanges: function () {
                    var widget = this,
                        not2orderable = widget.options.jsonConfig.is_not2orderable,

                        cartButton = widget.element.parents(widget.options.selectorProduct)
                            .find(widget.options.selectorAddToCart),
                        cartInstant = widget.element.parents(widget.options.selectorProduct)
                            .find(widget.options.selectorAddToCartInstant),
                        cartInstantExtra = widget.element.parents(widget.options.selectorProduct)
                            .find(widget.options.selectorAddToCartInstantExtra),

                        selectedProduct = this.getProduct(),

                        //default cart button is true
                        showCartButton = true


                    if (typeof selectedProduct !== "undefined") {
                        showCartButton = not2orderable[selectedProduct] == 'undefined' ? false : not2orderable[selectedProduct];
                    }

                    //show or hide cart buttons
                    cartButton.toggle(showCartButton);
                    cartInstant.toggle(showCartButton);
                    cartInstantExtra.toggle(showCartButton);
            },
        });

        return $.mage.SwatchRenderer;
    }
});
