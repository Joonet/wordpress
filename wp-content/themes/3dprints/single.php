<?php get_header(); ?>

	<main class="content">
        <div class="header-title">
            <div class="wrap">
                <h1><?php the_title(); ?></h1>
            </div>
        </div>
        
        <section class="">
            <div class="wrap editor">
                <?php the_content(); ?>
            </div>
        </section>
    </main><!-- .content -->

<?php get_footer(); ?>