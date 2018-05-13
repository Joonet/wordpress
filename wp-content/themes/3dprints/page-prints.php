<?php /* Template Name: 3D Prints */
get_header(); ?>

	<main class="content ">
        <div class="wrap">                
            <form id="form_files" method="post" enctype="multipart/form-data" class="clearfix">
                <label>
                    <div class="button"><?php _e( 'Add 3D STL Files', '3dprint' ); ?></div>                               
                    <input type="file" id="files_stl" name="files_stl[]" multiple="" accept=".stl" >
                </label>
                <?php wp_nonce_field( 'upload_stl_file' ); ?>
                <input type="hidden" name="action" value="upload_stl_file">                        
                <input type="hidden" name="uploaded_files" id="uploaded_files" value="">
                
                <div class="other_formats"><?php _e( 'Other formats? Please send through', '3dprint' ); ?> <a href="#" class="chat chat1"></a><a href="#" class="chat chat2"><div class="qrcode"><div id="zxzx_weixin_code" class="zxzx_weixin_code"></div></div></a> <a href="tel:075525583221" class="chat chat3"></a></div>
            </form>                 
            <?php _e( '1. Only one part allow in one STL file. &nbsp;&nbsp;2. Max 10 STL files for one upload. Max 64M for each file.', '3dprint' ); ?>
        </div>
        
        <section class="load-preview">
            <div class="wrap clearfix">
                <div class="load-file">
                    <div class="files_name_block">
                        <div id="files_name_scroll">
                            <ul id="files_name">
                                <!--
                                <li class="file_name"><a href="#" class="preview-link">shell-of-ordiod_R3.0-17.stl</a> <a href="#" class="delete-link"><i class="fa fa-times" aria-hidden="true"></i></a></li>
                                <li class="file_name"><a href="#" class="preview-link">PS4_Camera_Stand4_Stand.stl</a> <a href="#" class="delete-link"><i class="fa fa-times" aria-hidden="true"></i></a></li>
                                -->
                            </ul>
                        </div>
                    </div>
                    <div class="clear-all"><button type="button" class="button" id="clear-bt"><?php _e( 'Clear All', '3dprint' ); ?></button></div>
                    <ul class="specif">                        
                        <li><span class="label"><?php _e( 'Total files:', '3dprint' ); ?></span> <span id="total_files">--</span></li>
                        <li><span class="label"><?php _e( 'Total capacity:', '3dprint' ); ?></span> <span id="total_capacity">--</span></li>                    
                    </ul>                    
                </div>

                <div class="preview-file">
                    <div class="preview-inner">
                        <div class="viewer-box">
                            <div id="viewer"></div>
                        </div>
                        
                        <div class="preview-info">
                            <div class="title">
                                <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/3dpreview-2.jpg" width="453" height="256" alt="">
                                <?php _e( '3D Preview', '3dprint' ); ?>
                            </div>                            
                                
                            <div id="progress">
                                <div id="bar" style="width: 100%;"></div>
                                <div id="percent">100%</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <?php 
        $posts_material = get_posts(array(
            'post_type'      => 'material',
            'posts_per_page' => -1,
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
        ));
        if ( $posts_material ) {
        ?>
        <section class="materials">
            <div class="wrap clearfix">
                <!--
                <button class="button select-materials">Material Options</button>
                <div class="material-options">
                    <div class="file-box"><label><input type="checkbox" name="painting" value="1"> <span>Painting</span></label></div>
                    <div class="file-box"><label><input type="checkbox" name="screen" value="1"> <span>Screen printing</span></label></div>
                </div>
                -->
                
                <div class="table-head clearfix">
                    <div class="col-1"><?php _e( 'Material', '3dprint' ); ?></div>
                    <div class="col-2"><?php _e( 'Color', '3dprint' ); ?></div>
                    <div class="col-3"><?php _e( 'Accuracy', '3dprint' ); ?></div>
                    <div class="col-4"><?php _e( 'Tolerance Time', '3dprint' ); ?></div>
                    <div class="col-5"><?php _e( 'Estimated Delivery', '3dprint' ); ?></div>
                    <div class="col-6"><?php _e( 'Files', '3dprint' ); ?></div>                    
                    <div class="col-8"><?php _e( 'Total Cost', '3dprint' ); ?></div>
                    <div class="col-9">&nbsp;</div>
                </div>
                <?php 
                foreach ( $posts_material as $post ) { setup_postdata($post);
                    $price              = get_post_meta( $post->ID, 'price', 1 );
                    $color              = get_post_meta( $post->ID, 'color', 1 );
                    $accuracy           = get_post_meta( $post->ID, 'accuracy', 1 );
                    $tolerance_time     = get_post_meta( $post->ID, 'tolerance_time', 1 );
                    $estimated_delivery = get_post_meta( $post->ID, 'estimated_delivery', 1 );
                    $time_rate          = get_post_meta( $post->ID, 'seven_days_time_rate', 1 );
                    $painting           = get_post_meta( $post->ID, 'painting', 1 );
                    $price_painting     = get_post_meta( $post->ID, 'price_painting', 1 );
                    $silk_screen        = get_post_meta( $post->ID, 'silk_screen', 1 );
                    $price_silk_screen  = get_post_meta( $post->ID, 'price_silk_screen', 1 );
                    $date_delivery      = date( 'F d', (current_time('timestamp') + $estimated_delivery * 3600) );
                    ?>
                    <div class="box-material disabled" id="material-<?php echo $post->ID; ?>" data-id="<?php echo $post->ID; ?>" data-price_item="<?php echo $price; ?>" data-is_painting="<?php echo $painting; ?>" data-price_painting="<?php echo $price_painting; ?>" data-is_screen="<?php echo $silk_screen; ?>" data-price_silk_screen="<?php echo $price_silk_screen; ?>" >
                        <table>
                            <tr class="material">
                                <td class="col-1"><span><?php the_title(); ?></span></td>
                                <td class="col-2"><?php echo $color; ?></td>
                                <td class="col-3"><?php echo $accuracy; ?></td>
                                <td class="col-4"><?php echo str_replace( "\n", '<br/>', $tolerance_time ); ?></td>
                                <td class="col-5">
                                    <?php printf( __( '%s hours (at 22:00 on %s before) last', '3dprint' ), $estimated_delivery, $date_delivery ); ?>
                                    <br>
                                    <?php _e( 'seven days time rate', '3dprint' ); ?>: <?php echo $time_rate; ?>%</td>
                                <td class="col-6 files">0</td>                                
                                <td class="col-8 price">¥ <span>0</span></td>
                                <td class="col-9"><button class="button" disabled><?php _e( 'Add to Cart', '3dprint' ); ?></button></td>
                            </tr> 
                        </table>
                        <div class="files-box">
                            
                        </div>
                    </div>
                    <?php 
                } 
                wp_reset_postdata(); 
                ?>
            </div>
        </section>
        <div class="wrap button-cart-footer clearfix"><?php storefront_header_cart(); ?></div>
        <?php } ?>
    </main><!-- .content -->

<?php get_footer(); ?>