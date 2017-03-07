/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

business_photos = function () {
    return {
        photoClickEvent: function (this_element) {

            this_element.find('.photo-wrapper').on('click', function (e) {

                var upload_photo = this_element.closest('.photo').find('.upload_photo');
                upload_photo.click();
                business_photos.addNewPhotoDiv();
            });

            this_element.find('.upload_photo').on('change', function (e) {
                business_photos.uploadInputChanged(this, jQuery(this));
            });
        },
        addNewPhotoDiv: function () {
            var total_div_holder = jQuery('.photos .photo').length;
            var total_image_holder = jQuery('.photos .photo img').length;


            console.log('total_div_holder = ' + total_div_holder);
            console.log('total_image_holder = ' + total_image_holder);

            if (total_div_holder - 1 === total_image_holder) {

                var html = '<div class="photo photo-input">';
                html += '<div class="photo-wrapper">';
                html += '<i class="fa fa-plus"></i> Add Photos';
                html += '</div>';
                html += '<input class="upload_photo" type="file"  name="form[photo][]" style="display: none;">';
                html += '</div>';

                html = jQuery(html);

                business_photos.photoClickEvent(html);

                html.appendTo('.photos');
            }
        },
        uploadInputChanged: function (this_element, jquery_this_element) {
            {


                if (this_element.files && this_element.files[0]) {
                    var reader = new FileReader();

                    reader.onload = function (e) {
                        //  $('#blah').attr('src', e.target.result);
                        var image_src = '<img src="' + e.target.result + '">';
                        jquery_this_element.closest('.photo').find('.photo-wrapper').html(image_src);
                    }

                    reader.readAsDataURL(this_element.files[0]);
                }
            }
        }
    };
}();