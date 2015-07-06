/**
 * Source maps
 *
 * @link http://github.com/twolfson/grunt-jsmin-sourcemap
 */

module.exports = function (grunt, options) {

    var jqueryPlugins = options.gc.jqueryPlugins,
        libraryFiles = options.gc.libraryFiles;

    return {
        catalog: {
            src: ['dev/catalog/*.js'],
            dest: 'prod/catalog.js',
            destMap: 'prod/catalog.js.map',
            srcRoot: '/js',
            cwd: 'web/js'
        },
		gift: {
			src: ['dev/gift/*.js'],
			dest: 'prod/gift.js',
			destMap: 'prod/gift.js.map',
			srcRoot: '/js',
			cwd: 'web/js'
		},
        ports: {
            src: ['dev/ports/*.js'],
            dest: 'prod/ports.js',
            destMap: 'prod/ports.js.map',
            srcRoot: '/js',
            cwd: 'web/js'
        },
        debugPanel: {
            src: ['dev/debug-panel/*.js'],
            dest: 'prod/debug-panel.js',
            destMap: 'prod/debug-panel.js.map',
            srcRoot: '/js',
            cwd: 'web/js'
        },
        cart: {
            src: ['dev/cart/*.js'],
            dest: 'prod/cart.js',
            destMap: 'prod/cart.js.map',
            srcRoot: '/js',
            cwd: 'web/js'
        },
		compare: {
            src: ['dev/compare/*.js'],
            dest: 'prod/compare.js',
            destMap: 'prod/compare.js.map',
            srcRoot: '/js',
            cwd: 'web/js'
        },
        common: {
            src: ['dev/common/*.js'],
            dest: 'prod/common.js',
            destMap: 'prod/common.js.map',
            srcRoot: '/js',
            cwd: 'web/js'
        },
        infopage: {
            src: ['dev/infopage/*.js'],
            dest: 'prod/infopage.js',
            destMap: 'prod/infopage.js.map',
            srcRoot: '/js',
            cwd: 'web/js'
        },
        library: {
            src: [
                'dev/library/getKeysLength.js',
                'dev/library/JSON.js',
                'dev/library/isTrueEmail.js',
                'dev/library/printPrice.js',
                'dev/library/doc_cookies.js',
                'dev/library/simple_templating.js',
                'dev/library/black_box.js',
                'dev/library/formValidator.js',
                'dev/library/addParameterToUrl.js',
                'dev/library/*.js'
            ],
                dest: 'prod/library.js',
                destMap: 'prod/library.js.map',
                srcRoot: '/js',
                cwd: 'web/js'
        },
        jqueryPlugins: {
            src: [
                'dev/jquery-plugins/jquery.kladr.js',
                'dev/jquery-plugins/smart-address.js',
                'dev/jquery-plugins/jquery.lightbox_me.js',
                'dev/jquery-plugins/jquery.scrollto.js',
                'dev/jquery-plugins/jquery.placeholder.js',
                'dev/jquery-plugins/jquery.infinityCarousel.js',
                'dev/jquery-plugins/jquery.visible.js',
                'dev/jquery-plugins/jquery.maskedinput.js',
                'dev/jquery-plugins/jquery.put_cursor_at_end.js',
                'dev/jquery-plugins/goodsCounter.js',
                'dev/jquery-plugins/jquery.deparam.js',
                'dev/jquery-plugins/jquery.elevatezoom.js',
                'dev/jquery-plugins/customDropDown.js',
                'dev/jquery-plugins/goodsSlider.js',
                'dev/jquery-plugins/jquery-ui-1.10.3.custom.js'
            ],
                dest: 'prod/jquery-plugins.js',
                destMap: 'prod/jquery-plugins.js.map',
                srcRoot: '/js',
                cwd: 'web/js'
        },
        lk: {
            src: ['dev/lk/*.js'],
                dest: 'prod/lk.js',
                destMap: 'prod/lk.js.map',
                srcRoot: '/js',
                cwd: 'web/js'
        },
        enterprize: {
            src: ['dev/enterprize/*.js'],
                dest: 'prod/enterprize.js',
                destMap: 'prod/enterprize.js.map',
                srcRoot: '/js',
                cwd: 'web/js'
        },
        main: {
            src: ['dev/main/*.js'],
                dest: 'prod/main.js',
                destMap: 'prod/main.js.map',
                srcRoot: '/js',
                cwd: 'web/js'
        },
        orderV31ClickJS: {
            src: ['dev/order-v3-1click/*.js'],
            dest: 'prod/order-v3-1click.js',
            destMap: 'prod/order-v3-1click.js.map',
            srcRoot: '/js',
            cwd: 'web/js'
        },
        orderV3newJS: {
            src: ['dev/order-v3-new/*.js'],
            dest: 'prod/order-v3-new.js',
            destMap: 'prod/order-v3-new.js.map',
            srcRoot: '/js',
            cwd: 'web/js'
        },
		orderV3lifegiftJS: {
            src: ['dev/order-v3-lifegift/*.js'],
            dest: 'prod/order-v3-lifegift.js',
            destMap: 'prod/order-v3-lifegift.js.map',
            srcRoot: '/js',
            cwd: 'web/js'
        },
        pandora: {
            src: ['dev/pandora/*.js'],
                dest: 'prod/pandora.js',
                destMap: 'prod/pandora.js.map',
                srcRoot: '/js',
                cwd: 'web/js'
        },
        product: {
            src: ['dev/product/*.js'],
                dest: 'prod/product.js',
                destMap: 'prod/product.js.map',
                srcRoot: '/js',
                cwd: 'web/js'
        },
        shop: {
            src: ['dev/shop/*.js'],
                dest: 'prod/shop.js',
                destMap: 'prod/shop.js.map',
                srcRoot: '/js',
                cwd: 'web/js'
        },
        watch3d: {
            src: ['dev/watch3d/*.js'],
                dest: 'prod/watch3d.js',
                destMap: 'prod/watch3d.js.map',
                srcRoot: '/js',
                cwd: 'web/js'
        },

        serviceHa: {
            src: ['dev/service_ha/*.js'],
                dest: 'prod/service_ha.js',
                destMap: 'prod/service_ha.js.map',
                srcRoot: '/js',
                cwd: 'web/js'
        },


        supplier: {
            src: ['dev/supplier/*.js'],
            dest: 'prod/supplier.js',
            destMap: 'prod/supplier.js.map',
            srcRoot: '/js',
            cwd: 'web/js'
        }
    }
};