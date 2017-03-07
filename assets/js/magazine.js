/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


jQuery(document).ready(function () {
    business.init();
});

business = function () {
    return {
        init: function () {

            kazist.loadScriptByUrl(kazicode.url + '/applications/Business/Components/assets/js/business_photos.js');

            business.addEvents(jQuery('body'));

        }, addEvents: function (html) {

            html = business.addPhotosEvents(html);

            html.find(".package_input input").on('ifChecked', function (event) {
                business.showPackagePrice(jQuery(this), html);
            });

            html.find(".products-navigation a").on('click', function (event) {
                business.loadProducts(jQuery(this));
            });

            html.find(".country_id").on('change', function (event) {
                business.loadLocations(jQuery(this));
            });

            return html;

        }, addPhotosEvents: function (html) {

            html.find('.photo-input').each(function () {
                business_photos.photoClickEvent(jQuery(this));
            });

            return html;

        }, showPackagePrice: function (this_element, html) {

            var package_id = this_element.val();

            // Reset Previous Selection
            html.find('.package_price_id .package_price_input').hide();
            html.find('.package_price_id .package_price_input input').iCheck('uncheck');

            //Show Appropriates
            html.find('.package_price_id .package_price_input_' + package_id)
                    .show()
                    .first()
                    .find('input')
                    .iCheck('check');

            jQuery('.package_changed').show();

        }, loadProducts: function (this_element) {

            var action = this_element.attr('action');
            var offset = this_element.attr('offset');
            var business_id = this_element.attr('business_id');
            var data_object = {action: action, offset: offset, business_id: business_id};

            var form = kazist.callAjaxByRoute('businesses.businesses.ajaxloadproducts', data_object, true);

            jQuery('.business-products-listing').html(form);

            business.addEvents(jQuery('.business-products-listing'));

        }, loadLocations: function (this_element) {

            var country_id = this_element.val();
            var data_object = {country_id: country_id};

            var form = kazist.callAjaxByRoute('businesses.businesses.ajaxloadlocations', data_object, true);

            jQuery('.location_id').html(form);

        }

    };
}();