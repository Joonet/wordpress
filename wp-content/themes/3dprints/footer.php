    </div><!-- .wrapper -->     
    <footer class="footer clearfix">
        <div class="wrap">
            <div class="copyright"><?php echo get_field('copyright', 'options'); ?></div>
            <div class="footer-number"><?php echo get_field('footer_number', 'options'); ?>
                <?php if ( pll_current_language() == 'zh' ) { ?>
                    <div class="footer-right">备案号：粤ICP备17026157号-1</div>
                <?php } ?>
            </div>            
        </div>
    </footer><!-- .footer -->
    
    <?php if ( get_field('fixed_info-block', 'options') ) { ?>
    <div class="fixed-block">
        <a href="#" onclick="javascript:return false;"><i class="fa fa-info-circle" aria-hidden="true"></i>
            <span class="inner">
                <span class="inner-text">
                    <?php echo get_field('fixed_info-block', 'options'); ?>
                </span>
            </span>
        </a>
    <div>
    <?php } ?>

    <?php wp_footer(); ?>
    
    <?php 
    if ( is_page_template('page-prints.php') ) {
        if ( isset( $_COOKIE['session_files'] ) ) {
            $session_files = unserialize( stripcslashes( $_COOKIE['session_files'] ) );
            if ( ! empty( $session_files ) ) {
                ?>
                <script>
                jQuery(document).ready(function($){
                    $('.load-file').block({ message: null, overlayCSS: { background: '#fff', opacity: 0.6 } });
                    $.post(
                        myajax.url,
                        {
                            'action'    : 'update_from_session_files'                                    
                        },
                        function(data){
                            //console.log( data );
                            
                            enter_data_files( data );
                            $('.load-file').unblock();
                        }
                    );
                });
                </script>
                <?php
            }
        }
    }
    ?>
</body>
</html>
