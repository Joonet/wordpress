jQuery(document).ready(function($){
    $(document.body).trigger('wc_fragment_refresh');
    
    $('body').on('click', '.bt-plus, .bt-minus', function(e){
        e.preventDefault();
        var update_val      = false;
        var box             = $(this).closest('.box-material');
        var box_id          = box.attr('id');
        var price_item      = box.attr('data-price_item');        
   
        var input      = $(this).parent().find('input');
        var value      = parseInt( input.val() );
        if ( $(this).hasClass('bt-plus') ) {
            value = value + 1; 
            update_val = true;
        }
        else if ( value > 0 ) {
            value = value - 1;            
            update_val = true;
        }
        
        if ( update_val == true ) {
            input.val( value );
            if ( $('input[name="update_cart"]').length > 0 )
                $('input[name="update_cart"]').prop('disabled', false).trigger("click");
        }

        if ( box_id != 'undefined' ) {
            var price_file_html = $(this).closest('.file-box').find('.col-price span');
            //if ( $(this).closest('.file-box').find('input.selected').prop('checked') == true )
                price_file_html.html( ( price_item * value ).toFixed(2) );        
        
            update_price_box_material( box_id );
        }
    });
    
    
    $('body').on('change', '.plus-minus, .quantity .qty', function(){
        value = Math.abs( $(this).val() * 1 );        
        if ( ! Number.isInteger( value ) ) {            
            $(this).val( 1 );
        }
        
        var box         = $(this).closest('.box-material');
        var box_id      = box.attr('id');
        var price_item  = box.attr('data-price_item');
        
        if ( box_id != 'undefined' ) {
            var price_file_html = $(this).closest('.file-box').find('.col-price span');
            price_file_html.html( ( price_item * value ).toFixed(2) );        
        
            update_price_box_material( box_id );
        }
        
        if ( $('input[name="update_cart"]').length > 0 ) {
            $('input[name="update_cart"]').prop('disabled', false).trigger("click");
        }
    });    
    
    
    // $('body').on('change', 'input.selected', function(e){
        // var box_id = $(this).closest('.box-material').attr('id');
        // if ( $(this).prop('checked') == false ) {
            // $(this).closest('.file-box').find('.col-price span').html('0');
            
            // if ( $('#'+box_id).find('input.selected:checked').length == 0 )
                // $('#'+box_id).find('.col-9 .button').prop('disabled', true);    
        // }
        // else {
            // var price_item = $('#'+box_id).attr('data-price_item');
            // var value      = $(this).closest('.file-box').find('input.plus-minus').val()*1;
            
            // $(this).closest('.file-box').find('.col-price span').html( ( price_item * value ).toFixed(2) );
            // $('#'+box_id).find('.col-9 .button').prop('disabled', false);
        // }
        
        // update_price_box_material( box_id );
    // });
    
    
    // $('body').on('change', 'input[name="painting"], input[name="screen"]', function(e){
        // var box_id = $(this).closest('.box-material').attr('id');
        // update_price_box_material( box_id );
    // });   


    // $('.select-materials').click(function(){
        // $('.material-options').slideToggle();
    // });
    
    
    function update_price_box_material( box_id ) {
        var price_item      = $('#'+box_id).attr('data-price_item') * 1; 
        var price_painting  = $('#'+box_id).attr('data-price_painting') * 1; 
        var price_screen    = $('#'+box_id).attr('data-price_silk_screen') * 1;         
        var price_total     = 0;
        var files_total     = 0;
        $('#'+box_id+' .file-box').each(function(){
            var count_el = $(this).find('input.plus-minus').val()*1;
            
            if ( count_el ) {           
                var price_file_html = $(this).find('.col-price span');                
                
                // var is_painting     = $(this).find('input[name="painting"]').prop('checked');
                // var is_screen       = $(this).find('input[name="screen"]').prop('checked');
                var count_el_painting = 0;
                var count_el_screen   = 0;
                
                // if ( is_painting == true ) {
                    // count_el_painting =  count_el;                  
                // }                
                // if ( is_screen == true ) {
                    // count_el_screen =  count_el;            
                // }                
                
                var price_file = count_el * price_item + count_el_painting * price_painting + count_el_screen * price_screen;
                price_file_html.html( price_file.toFixed(2) );
                
                price_total = price_total + price_file;
                files_total = files_total + 1;
            }
        });
        
        $('#'+box_id).find('td.price span').html( price_total.toFixed(2) );
        $('#'+box_id).find('td.files').html( files_total );
        
        if ( $('#'+box_id+' .file-box').length == 0 ) {
            $('#'+box_id).removeClass('open');
            $('#'+box_id).addClass('disabled');
            $('#'+box_id).find('td.col-9 .button').prop('disabled', true);
        }
    }
    
    
    function update_total_files_info() {
        if ( $('#files_name li').length > 0 ) {
            var total_volume  = 0;
            $('#files_name li').each(function(){
                total_volume  = total_volume + $(this).attr('data-volume')*1;
            });
            
            $('#total_files').html( $('#files_name li').length );
            $('#total_capacity').html( total_volume.toFixed(2) + ' mm<sup>3</sup>' );
        }
        else {
            $('#total_files').html('--');
            $('#total_capacity').html('--');
            
            $('.box-material .file-box').remove();
        }
        
        $('.box-material').each(function(){
            update_price_box_material( $(this).attr('id') );
        });
    }
    
    
    // Show/Hide Fieles
    $('body').on('click', '.box-material:not(.disabled) td', function(){
        if ( $(this).attr('class') != 'col-9' ) {
            $(this).closest('.box-material').toggleClass('open').find('.files-box').slideToggle();
        }
    });    
    
    // AJAX load STL files
    var load_progress = $('#progress');    
    var bar = $('#bar');    
    var percent = $('#percent');
    
    $('#files_stl').change(function(){
        if ( $(this).val() != '' ) {   
            var error = false;
            var error_msg = '';
            
            $('#viewer').empty();
            //$('#files_name').empty();
            $('.preview-info').show();
            
            $('#total_files').html('--');
            $('#total_capacity').html('--');
            // $('#total_weight').html('--');
            // $('#total_density').html('--');
            // $('#total_area').html('--');
            
            $('.box-material').each(function(i){
                $(this).addClass('disabled');
                $(this).find('td.col-7 span').html('');
                $(this).find('td.files').html('0');
                $(this).find('td.price span').html('0');
                $(this).find('td.col-9 .button').prop('disabled', true);
                $(this).find('.files-box').hide().html('');
            });
            
            $.each( this.files, function(i, file){
                if ( file.size > 64000000 ) {
                    error = true;
                    error_msg = 'Error: single file does not exceed 64M.';
                }
                else if ( i > 9 ) {
                    error = true;
                    error_msg = 'Error: single can upload up to 10 files.';
                }
                
                //console.log( file );
            }); 
            
            if ( error == false ) {                
                $('#form_files').ajaxSubmit({
                    url: myajax.url,
                    beforeSend: function() {
                        //status.empty();
                        var percentVal = '0%';
                        bar.width(percentVal)
                        percent.html(percentVal);
                        load_progress.show();
                    },
                    uploadProgress: function(event, position, total, percentComplete) {
                        var percentVal = percentComplete + '%';
                        bar.width(percentVal)
                        percent.html(percentVal);
                        //console.log(percentVal, position, total);
                    },
                    success: function(data) {
                        var percentVal = '100%';
                        bar.width(percentVal)
                        percent.html(percentVal); 
                        
                        //console.log( data );
                        if ( data != 0 ) { 
                            enter_data_files( data );
                        }
                        else 
                            alert('Error data');
                    },
                    complete: function(xhr) {
                        //status.html(xhr.responseText);
                        load_progress.hide();
                    }
                }); 
            }
            else {
                if ( ! error_msg )
                    error_msg = 'Error STL File';
                
                $(this).val('');
                alert( error_msg );                
            }
        }
    });
      
    // Show Preview STL file
    if ( $('#files_name_scroll').length > 0 ) {
        CFInstall.check({
            mode: "inline", // standaard
            node: "prompt"
        });
    }
      
    // Show Preview STL file
    thingiurlbase = myajax.theme_url + "/js";
    $('body').on('click', '.preview-link', function(e){
        e.preventDefault();
        
        $('.preview-info').hide();
        $('li.file_name.active').removeClass('active');
        
        var closest_block = $(this).closest('.file_name');
        if ( closest_block.length > 0 ) {
            closest_block.addClass('active');
        }
        
        thingiview = new Thingiview("viewer");
        thingiview.setShowPlane(false);
        thingiview.setObjectColor('#ffffff');
        thingiview.initScene();
        thingiview.loadSTL( $(this).attr('href') );
    });
    
        // in Cart
    if ( $('body').hasClass('woocommerce-cart') ) {
        $('.viewer_product').each(function(){    
            thingiview = new Thingiview( $(this).attr('id') );

            thingiview.setShowPlane(false);
            thingiview.setObjectColor('#ffffff');                      
            thingiview.setRotation( false );
            thingiview.initScene();
            thingiview.loadSTL( $(this).attr('data-href') );            
        });   
    }
    
    // AJAX - delete STL file
    delete_link_click = false;
    $('body').on('click', 'a.delete-link', function(e){
        e.preventDefault();
        if ( ! delete_link_click ) {
            var file_name = $(this).attr('data-file');
            delete_stl_file( file_name );
            
            var closest_block = $(this).closest('.file_name');
            if ( closest_block.hasClass('active') ) {
                $('#viewer').empty();
                $('.preview-info').show();
            }
            
            closest_block.remove();
            if ( $('#files_name li').length == 0 ) {
                $('#viewer').empty();
                $('.preview-info').show();
                $('#uploaded_files').val('');
                $('#files_name li').hide();
                $('.clear-all').hide();    

                setHeightPreviewBlock(); 
            }
            
            $('#files_name_scroll').perfectScrollbar('update');
            
            $('.file-box').each(function(){
                if ( $(this).attr('data-file') == file_name )
                    $(this).remove();
            });
            
            update_total_files_info();
        }
    });
    
    function delete_stl_file( file_name ) {
        if ( file_name ) {
            $.post(
                myajax.url,
                {
                    'action'    : 'delete_stl_file',
                    'file_name' : file_name
                },
                function(data){
                    //console.log(data);
                    
                    delete_link_click = false;
                }
            );
        }
    }
    
    
    // AJAX - button "Add to cart"
    $('.col-9 .button').click(function(){
        var box_material = $(this).closest('.box-material');        
        
        $('#uploaded_files').val('');
        var files = [];        
        var box_id = $(this).closest('.box-material').attr('id');
        update_price_box_material( box_id );
        
        $('#'+box_id+' .file-box').each(function(i){
            if ( $(this).find('input.plus-minus').val()*1 > 0 ) {             
                var file_item = {
                    material_id : $(this).closest('.box-material').attr('data-id'),
                    file_name   : $(this).find('.col:eq(0)').find('a').text(),
                    quantity    : $(this).find('input.plus-minus').val()
                    // is_painting : $(this).find('input[name="painting"]').prop('checked'),
                    // is_screen   : $(this).find('input[name="screen"]').prop('checked'),
                };
                
                files.push( file_item );
            }
        });
        
        console.log( files );
        
        if ( files.length > 0 ) {
            box_material.block({ message: null, overlayCSS: { background: '#fff', opacity: 0.6 } });
            
            $.get( 'http://www.3silkworm.com/shop/?add-to-cart=233', '', function(){
                $.post(
                    myajax.url,
                    {
                        'action': 'prints_create_product',
                        'files' : files
                    },
                    function(data){
                        console.log( data );
                        if ( data != 0 ) {
                            data = JSON.parse( data );                      
                            
                            if ( data.error ) {
                                alert( data.error );
                            }
                            else {
                                if ( data.products.length > 0 ) {                                    
                                    $('html, body').stop().animate({
                                        'scrollTop': $('body').offset().top
                                    }, 600 );
                                    
                                    $('.site-header-cart a.cart-contents').html( $(data.html_cart['a.cart-contents']).html() );                            
                                    
                                    box_material.removeClass('open').addClass('disabled');
                                    box_material.find('td.col-7 span').html('');
                                    // box_material.find('td.files').html('0');
                                    // box_material.find('td.price span').html('0');
                                    box_material.find('td.col-9 .button').prop('disabled', true);
                                    box_material.find('.files-box').hide();                                
                                    
                                    divform = $('.cart-contents');
                                    divform.animate({opacity: '0.7'}, "slow");
                                    divform.animate({opacity: '1'}, "slow");
                                    divform.animate({opacity: '0.7'}, "slow");
                                    divform.animate({opacity: '1'}, "slow");
                                    divform.animate({opacity: '0.7'}, "slow");
                                    divform.animate({opacity: '1'}, "slow");
                                    
                                    $.each( files, function( index, file ){
                                        $('a.delete-link[data-file="'+ file.file_name +'"]').attr('data-file', file.file_name+'-noExist');
                                        $('.file-box[data-file="'+ file.file_name +'"]').attr('data-file', file.file_name+'-noExist');
                                    })
                                }
                            }
                            
                            box_material.unblock();
                        }
                    }
                );
            });
        }
    });
    
    // Clear - STL files
    $('#clear-bt').click(function(){
        $('#viewer').empty();
        $('.preview-info').show();
        $('#uploaded_files').val('');
        $('#files_name li').hide();
        $('.clear-all').hide();
        
        setHeightPreviewBlock();
        
        var file_names = [];
        $('#files_name li a.delete-link').each(function(){
            var file_name = $(this).attr('data-file');
            file_names.push( file_name );                     
        });
        delete_stl_file( file_names );
        $('#files_name').empty();
        $('#files_name_scroll').perfectScrollbar('update');
        
        update_total_files_info();
    });
    
    // LightBox - Web Chat
    $('#BizQQWPA').click(function(e){
        e.preventDefault();
        $('.WPA3-SELECT-PANEL').css('left', '50%');
    });
    $('#WPA3-SELECT-PANEL-CLOSE').click(function(e){
        e.preventDefault();
        $('.WPA3-SELECT-PANEL').css('left', '-50%');
    });
    
    $('a.chat1').click(function(e){
        e.preventDefault();        
        window.open("http://wpa.qq.com/msgrd?v=3&uin=19670324&site=qq&menu=yes", "网页聊天", "width=600,height=400");
        $('.WPA3-SELECT-PANEL').css('left', '-50%');
    });
    
    if ( $('#files_name_scroll').length > 0 ) {
        $('#files_name_scroll').perfectScrollbar();
    }
    
    // Checkout Page
    $('.show_hide_fields').click(function(e){
        e.preventDefault();
        
        $(this).hide().closest('div').find('.hide_fields').slideToggle();
        $(this).closest('div').find('div.clear').hide();
    });
    
    if ( $('select#billing_country').length > 0 ) {
        $('select#billing_country, select#billing_state').select2();
    }
    if ( $('select#shipping_country').length > 0 ) {
        $('select#shipping_country').select2();
    }
    
    if ( $('select#shipping_state').length > 0 ) {
        $('select#shipping_state').select2();
    }
    
    if ( $('select#vat_invoice').length > 0 ) {
        $('select#vat_invoice').select2();
    }
    
    if ( $('select.city_select').length > 0 ) {
        $('select.city_select').select2();
    }
    
    if ( $('select#sunfeng').length > 0 ) {
        $('select#sunfeng').select2();
        
        $('body').on('change', 'select#sunfeng', function(){
            $('ul#shipping_method input:checked').prop('checked', false);
            
            $('ul#shipping_method input[value="'+ $('select#sunfeng').val() +'"]').prop('checked', true);

            jQuery("body").trigger("update_checkout");
        });
    }    
    
    
    $('body').on('change', '#vat_invoice', function(){
        if ( typeof wc_checkout_params === 'undefined' )
            return false;

        //update_order_review();
        jQuery("body").trigger("update_checkout");
    });
});

function setHeightPreviewBlock(){
    var height_left_block = jQuery('.load-file').outerHeight();
    jQuery('.preview-inner').css('height', height_left_block);
};


function enter_data_files( data ) {
    data = JSON.parse( data );
    //console.log( data );        
    
    if ( data.error ) {
        alert( data.error );
    }
    else {         
        var total_volume   = 0;
        var total_weight   = 0;
        var total_density  = 0;
        var total_area     = 0;
        var uploaded_files = '';
        var class_link     = '';
        var count_link     = 0;
        jQuery('#files_name').empty();
        
        jQuery.each(data.files, function(i, file){
            total_volume  = total_volume + file[2];
            total_weight  = total_weight + file[3];
            total_density = total_density + file[4];
            total_area = total_area + file[5];
            
            uploaded_files = uploaded_files + file[1] + '|';
            class_link = '';            
            if ( count_link == 0 ) {
                class_link = ' active';
            }
            count_link++;
            
            jQuery('#files_name').append('<li class="file_name '+ class_link +'" data-volume="'+ file[2].toFixed(2) +'"><a href="'+ file[0] +'" class="preview-link '+ class_link +'">'+ file[1] +'</a> <a href="#" class="delete-link" data-file="'+ file[1] +'"><i class="fa fa-times" aria-hidden="true"></i></a></li>');
        });
        jQuery('#uploaded_files').val( uploaded_files );
        
        jQuery('#files_name_scroll').perfectScrollbar('update');
        
        jQuery('.clear-all').show();
        setHeightPreviewBlock();                                
        
        jQuery('#total_files').html( Object.keys( data.files ).length ); 
        jQuery('#total_capacity').html( total_volume.toFixed(2) + ' mm<sup>3</sup>' );
        // jQuery('#total_weight').html( total_weight.toFixed(3) + ' g' );
        // jQuery('#total_density').html( total_density.toFixed(3) + ' g/cm<sup>3</sup>' );
        // jQuery('#total_area').html( total_area.toFixed(2) + ' mm<sup>2</sup>' );                        
        
        // Show Preview
        jQuery('.preview-info').hide();        
        thingiview = new Thingiview("viewer");
        thingiview.setObjectColor('#ffffff');
        thingiview.initScene();
        jQuery.each( data.files, function( i, file_item ){
            thingiview.loadSTL( file_item[0] );  
            return false;
        })
        
        // Add Files in Table
        jQuery('.box-material').each(function(i){                                  
            var price       = jQuery(this).attr('data-price_item');
            var is_painting = jQuery(this).attr('data-is_painting');
            var is_screen   = jQuery(this).attr('data-is_screen');
            var box         = jQuery(this).find('.files-box');
            box.empty();
            
            jQuery(this).find('.files').html( Object.keys( data.files ).length );
            jQuery(this).find('.price span').html( (Object.keys( data.files ).length * price).toFixed(2) );
            
            var html = '';
            // if ( is_painting == 1 )
                // is_painting = '<div class="col"><label><input type="checkbox" name="painting" value="1"> <span>Painting</span></label></div>';
            // else 
                is_painting = '';
            
            // if ( is_screen == 1 )
                // is_screen = '<div class="col"><label><input type="checkbox" name="screen" value="1"> <span>Screen printing</span></label></div>';
            // else 
                is_screen = '';
            
            jQuery.each(data.files, function(i, file){                             
                html = '<div class="file-box" data-file="'+ file[1] +'">'+
                            '<div class="col"><a class="preview-link" href="'+ file[0] +'">'+ file[1] +'</a></div>'+
                            //'<div class="col"><a class="preview-link" href="'+ file[0] +'"><span class="icon-play"><i class="fa fa-play" aria-hidden="true"></i></span> Preview files</a></div>'+                                                    
                            '<div class="col col-quantity"><button class="bt-minus"></button><input type="text" class="plus-minus" name="quantity" value="1" maxlength="2"><button class="bt-plus"></button></div>'+
                            '<div class="col col-price">¥ <span>'+ (price*1).toFixed(2) +'</span></div>'+
                            is_painting + 
                            is_screen +                                                    
                            //'<div class="col"><label><input type="checkbox" name="selected" class="selected" value="1" checked> <span>Select</span></label></div>'+                                                    
                        '</div>';
                        
                box.append( html );
            });
            
            jQuery(this).removeClass('disabled');
            jQuery(this).find('.col-9 .button').prop('disabled', false);
            if ( i == 0 )
               jQuery(this).addClass('open').find('.files-box').show();
        });
    }                            
}
