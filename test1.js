

site.conversionTracking = (function () {
    var dom = document;
    var sessionStorage = easy.store;
    var initialized = false;


    /**
     * Send product conversion to back-end
     * @param  {DOM} $html [HTML DOM]
     */
    function _sendProductConversion($html) {
        var products = sessionStorage.get('productBasket');
        var orderTransactionTotal = sessionStorage.get('transactionTotal') || null;


        // Exit if there are no products
        if (!products || !products.length) {
            return;
        }


        // Handling (sending) the transaction
        easy.addExceptionHandling(function () {
            easy.dataLayer.handleItem({
                transactionId: _getTransactionID($html),
                transactionProducts: products,
                transactionTotal: orderTransactionTotal
            });


            _clearBasketProducts();
            _clearBasketAddToCartProducts();
        }, {
            code: easy.ERROR_CONVERSION_SEND
        });
    }


    /**
     * Get transaction ID from DOM
     * @param  {DOM Node} $html [the current product node]
     * @return {String}
     */
    function _getTransactionID($html) {
        return $html.querySelector('.main a') ? $html.querySelector('.main a').innerHTML : $html.querySelector('.main p').innerHTML.split(" ")[2].replace(/[^0-9]+/g, "");
    }


    /**
     * Get product data from the page and save to local storage with easy store
     * @param  {HTML DOM} $html [the DOM]
     */
    function _saveProductsToLocalStorage($html) {
        var products = [];
        var addToCartProducts = [];
        var productList = $html.querySelectorAll('table#shopping-cart-table tbody tr');
        var productPageList = sessionStorage.get('addToCartProducts') || [];


        // Clear all products if basket page is empty
        if (!productList.length) {
            _clearBasketProducts();
            _clearBasketAddToCartProducts();
            return;
        }


        easy.addExceptionHandling(function () {
            easy.utils.each(productList, function (product) {
                var comparedId = _getProductId(product);


                products.push({
                    id: comparedId,
                    name: _getProductName(product),
                    category: _getProductCategory(product),
                    price: _getProductPrice(comparedId, product),
                    quantity: _getProductQuantity(product)
                });


                // Reduce the size addToCartProducts in sessionStorage
                var existProduct = easy.utils.find(productPageList, function (product) {
                    return product.id === comparedId;
                });


                if (existProduct) {
                    addToCartProducts.push(existProduct);
                }
            });


            var transactionTotal = _getTransactionTotal($html);
            sessionStorage.set('productBasket', products);
            sessionStorage.set('transactionTotal', transactionTotal);
            sessionStorage.set('addToCartProducts', addToCartProducts);
        }, {
            code: easy.ERROR_PRODUCTS_GET_ERROR_CONVERSION
        });
    }


    /**
     * Get transaction total from DOM
     * @param  {DOM Node} $html [the current product node]
     * @return {String}
     */
    function _getTransactionTotal($html) {
        return site.utils.formatCurrency($html.querySelector('.mnm-grand-total span.price').innerHTML);
    }


    /**
     * Get Product id from DOM
     * @param  {DOM Node} $html [the current product node]
     * @return {String}
     */
    function _getProductId($html) {
        return $html.querySelector('a.product-image').getAttribute("title").split(" ")[0];
    }


    /**
     * Get current product name from DOM
     * @param  {DOM Node} $html [the current product node]
     * @return {String}
     */
    function _getProductName($html) {
        return $html.querySelector('a.product-image').getAttribute("title");
    }


    /**
     * Get current product category from DOM
     * @param  {DOM Node} $html [the current product node]
     * @return {String}
     */
    function _getProductCategory($html) {
        return $html.querySelector('a.product-image').getAttribute("title").split(" ")[1];
    }


    /**
     * Get current product quantity from DOM
     * @param  {DOM Node} $html [the current product node]
     * @return {String}
     */
    function _getProductQuantity($html) {
        return parseInt($html.querySelector('input.qty').getAttribute("value"), 10);
    }


    /**
     * Get current price since the price showing in the basket page is incorrect
     * Current price is taken from sessionStorage saved in previous pages
     * @param  {String} id [id of the current product]
     * @param  {DOM Node} $html [HTML node of the current product]
     * @return {String} [Product price]
     */
    function _getProductPrice(id, $html) {
        var basketPrice = site.utils.formatCurrency($html.querySelector('span.cart-price span.price').innerHTML);
        var productList = sessionStorage.get('addToCartProducts');


        // Return normal basket price if we didn't get any price from previous product pages
        if (!productList) {
            return basketPrice;
        }


        // Check the real price we stored from previous page
        var existProduct = easy.utils.find(productList, function (product) {
            return product.id === id;
        });


        return existProduct ? existProduct.price : basketPrice;
    }


    /**
     * Clear products that users add to cart in product page
     */
    function _clearBasketAddToCartProducts() {
        sessionStorage.remove('addToCartProducts');
    }


    /**
     * Clear products in basket page that are supposed to be empty
     */
    function _clearBasketProducts() {
        sessionStorage.remove('productBasket');
    }


    /**
     * Save product when users click on add to cart in Product Page
     * @param  {DOM} $html [HTML DOM of the current product page]
     * @param  {function} varOriginalClickEvent [Original click event]
     * @param  {click event} mouseEvent [Click event by default]
     */
    function _saveProductWhenAddToCartIsClicked($html, varOriginalClickEvent, mouseEvent) {
        var productList = sessionStorage.get('addToCartProducts') || [];
        var existProduct = null;
        var comparedId = $html.querySelector(".product-definitions h2").innerHTML.split(" ")[0];


        // Check if we already have the product in the storage
        existProduct = easy.utils.find(productList, function (product) {
            return product.id === comparedId;
        });


        // Save product if not exists in our storage
        if (!existProduct) {
            var rulePriceSelector = $html.querySelector(".price-info .special-price.has-rule-price .rule-price");
            var priceSelector = rulePriceSelector ? rulePriceSelector : $html.querySelector(".price-info .special-price .price");
            var currentPrice = site.utils.formatCurrency(priceSelector.innerHTML);


            productList.push({
                id: comparedId,
                price: currentPrice
            });


            sessionStorage.set('addToCartProducts', productList);
        }


        // Return normal click event;
        varOriginalClickEvent();
    }


    /**
     * Save product when users click on add to cart in Category Page
     * @param  {function} varOriginalClickEvent [Original click event]
     */
    function _saveProductWhenAddInCatPage(varOriginalClickEvent) {
        var target = event.srcElement;
        var parent = target.parentElement.parentElement;
        parent = parent.className !== "product-info" ? parent.parentElement.parentElement : parent;
        var myPrice = site.utils.formatCurrency(parent.querySelector('p.special-price span.price').innerHTML);
        var myId = parent.querySelector('h2.product-name a').innerHTML.trim().split(" ")[0];
        var productList = sessionStorage.get('addToCartProducts') || [];
        productList.push({
            id: myId,
            price: myPrice
        });
        sessionStorage.set('addToCartProducts', productList);


        // Return normal click event;
        varOriginalClickEvent();
    }


    function init() {
        easy.events.on(easy.EVENT_DOM_READY, easy.addExceptionHandling(function () {
            // Handle add to product cart in product page
            if (site.pages.isProductPage()) {
                var btn = dom.querySelector('button.btn-cart');
                var varOriginalClickEvent = btn.onclick;
                btn.onclick = null;
                easy.domEvents.on(btn, 'click', _saveProductWhenAddToCartIsClicked.bind(this, dom, varOriginalClickEvent));
            }


            // Handle add to product cart in category page
            if (site.pages.isCategoryPage()) {
                var addToCartBtns = dom.getElementsByClassName("btn-cart");
                easy.utils.each(addToCartBtns, function (btn) {
                    var varOriginalClickEvent = btn.onclick;
                    btn.onclick = null;
                    easy.domEvents.on(btn, 'click', _saveProductWhenAddInCatPage.bind(this, varOriginalClickEvent));
                });
            }
            // Handle in basket page
            if (site.pages.isBasketPage()) {
                _saveProductsToLocalStorage.apply(this, [dom]);
            }


            // Send conversion in checkout successful page
            if (site.pages.isCheckoutPage()) {
                _sendProductConversion.apply(this, [dom]);
            }
        }));
        initialized = true;
    }
    return {
        init: init,
        initialized: initialized,
        dom: dom
    };
})();


