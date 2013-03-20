<?php
/**
 * Template Functions
 */

/*****************************************
 * LOAD TEMPLATES
 *****************************************/

/**
 * Load content template based on current post type
 *
 * ctc_make_friendy() turns ccm_gallery_item into gallery-item for cleaner template names.
 */

function ctc_load_content_template() {

	get_template_part( 'content', ctc_make_friendly( get_post_type() ) ); 

}