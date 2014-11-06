<?php

if( !class_exists( 'FMS_Scanner' ) ) {
	
	class FMS_Scanner {

		private $_api_key;

		function FMS_Scanner( $api_key ) {

			$this->_api_key = $api_key;

		}

		function scan( $scan_args ) {

			$args = array(
				'post_type' => 'post',
				'posts_per_page' => -1,
				'orderby' => 'date',
				'order' => 'ASC'
			);

			if( isset( $scan_args[ 'category'] ) ) {
				$args[ 'category__in' ] = array( $scan_args[ 'category'] );
			}

			if( isset( $scan_args[ 'tags' ] ) ) {
				$args[ 'tag_slug__and' ] = explode( ',', $scan_args[ 'tags' ] );
			}

			$query = new WP_Query( $args );
			
			if( $query->have_posts() ) {
				$array_image_ids = array();
				while( $query->have_posts() ) {
					$query->the_post();
					$post_content = get_the_content();
					$result = preg_match_all( '/src="(.*?)"/', $post_content, $matches );
					if( $result > 0 ) {
						$array_tmp = array();
						foreach( $matches[ 1 ] as $flickr_img ) {
							$flickr_img_array = explode( '/', $flickr_img );
							if( strpos( $flickr_img, 'static.flickr.com' ) > -1 ) {
								$array_img_temp = explode( '_', $flickr_img_array[ 4 ] );
								$array_tmp[] = $array_img_temp[ 0 ];
							}							
						}
						if( count( $array_tmp ) > 0 ) {
							$array_image_ids[ get_the_ID() ] = array( get_the_title(), get_permalink( get_the_ID() ), $array_tmp );
						}
					}
				}

				echo '<div class="fms-posts-table"><table cellpadding="10" border="1">';
				foreach( $array_image_ids as $key => $value ) {
					$post_images = $value[ 2 ];
					$post_images = array_unique( $post_images );
					echo '<tr><td><a href="' . $value[ 1 ] . '" target="_blank"><strong>' . $value[ 0 ] . '</strong></a></td><td>';
					foreach( $post_images as $post_image ) {
						echo '<div id="fms-image-' . $post_image . '" data-image="' . $post_image . '" class="fms-image-in-post fms-loading"></div>';
					}
					echo '</td></tr>';
				}
				echo '</table></div>';
				echo '<script>var fms_api_key = "' . $this->_api_key . '";</script>';	

			} else {
				echo '<p>No posts match your criteria. Try again!</p>';
			}

			wp_reset_query();
			wp_reset_postdata();
					
		}
		
	}
	
}
