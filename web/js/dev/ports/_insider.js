ANALYTICS.insiderJS = function(){

    var InsiderProduct, fillProducts, products = [];

    InsiderProduct = function (data) {
        this.category = [];
        if (data.category && $.isArray(data.category)) this.category = data.category;
        if (data.name) this.name = data.name;
        if (data.img) this.img = data.img;
        if (data.link) this.url = data.link;
        if (data.price) this.price = '' + data.price;
        return this;
    };

    fillProducts = function(data) {
        $.each(data, function(i,val){
            products.push(new InsiderProduct(val))
        });
        window.spApiPaidProducts = products;
    };

    if (ENTER.UserModel && ENTER.UserModel.cart().products()) fillProducts(ENTER.UserModel.cart().products());

    $body.on('addtocart', function(e,data){
        if (data.setProducts) {
            window.spApiPaidProducts = window.spApiPaidProducts || [];
            $.each(data.setProducts, function(key, setProduct) {
                setProduct.category = null; // TODO временно, пока не отдаются категории в едином виде
                window.spApiPaidProducts.push(new InsiderProduct(setProduct));
            });
        }
    })
};