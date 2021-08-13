<?php
/**
 * Functions related to different part rendering.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   4.1.7
 */

// -----------------------------------------------------------------#
// Rendering: website meta tags
// -----------------------------------------------------------------#

if ( ! function_exists( 'adventure_tours_render_website_meta_tags' ) ) {
	function adventure_tours_render_website_meta_tags(){
		printf( '<meta charset="%s">' . PHP_EOL, get_bloginfo( 'charset', 'display' ) );
		if ( apply_filters( 'adventure_tours_render_website_description_meta', ! adventure_tours_check( 'is_wordpress_seo_in_use' ) ) ) {
			printf( '<meta name="description" content="%s">' . PHP_EOL, get_bloginfo( 'description', 'display' ) );
		}
		print( '<meta name="viewport" content="width=device-width, initial-scale=1.0">' . PHP_EOL );
	}
	add_filter( 'wp_head', 'adventure_tours_render_website_meta_tags', 0 );
	// add_filter( 'adventure_tours_render_website_description_meta', '__return_false' );
}

// -----------------------------------------------------------------#
// Rendering: helper functions
// -----------------------------------------------------------------#

if ( ! function_exists( 'adventure_tours_get_tour_layout_item_thumbnail' ) ) {
	function adventure_tours_get_tour_layout_item_thumbnail($postId, $viewSettings, array $attributes = array() ) {
		$thumbnail_id = get_post_thumbnail_id( $postId );
		$imageSize = isset($viewSettings['image_size']) ? $viewSettings['image_size'] : null;

		if ( $imageSize && $thumbnail_id ) {
			$layout = isset($viewSettings['view_type']) ? $viewSettings['view_type'] : 'list';

			if ( ! isset( $attributes['sizes'] ) ) {
				if ( 'list' == $layout ) {
					$attributes['sizes'] = '(max-width:768px) 95vw, (max-width:1200px) 252px, 296px';
				} else {
					$columns = !empty($viewSettings['columns']) ? $viewSettings['columns'] : 2;
					if ( $columns > 3 ) {
						$attributes['sizes'] = '(max-width:330px) 85vw, (max-width:540px) 330px, (max-width:767px) 46vw, (max-width:991px) 345px, (max-width:1199px) 213px, 262px';
					} elseif ( $columns == 3 ) {
						$attributes['sizes'] = '(max-width:330px) 85vw, (max-width:540px) 330px, (max-width:767px) 46vw, (max-width:1199px) 334px, 345px';
					} else {
						$attributes['sizes'] = '(max-width:330px) 85vw, (max-width:540px) 330px, (max-width:767px) 46vw, (max-width:1199px) 334px, 409px';
					}
				}
			}

			static $relatedSizesMap;
			if ( null === $relatedSizesMap ) {
				$relatedSizesMap = apply_filters( 'adventure_tours_tour_item_thumbnail_related_sizes_map', array(
					'thumb_tour_box' => array(
						'thumb_tour_box_small',
					)
				) );

				if ( null === $relatedSizesMap ) {
					$relatedSizesMap = array();
				}
			}
			$relSizes = isset( $relatedSizesMap[ $imageSize ] ) ? $relatedSizesMap[ $imageSize ] : null;
			if ( $relSizes ) {
				$t_meta = wp_get_attachment_metadata($thumbnail_id);
				foreach ($relSizes as $_size) {
					if ( empty( $t_meta['sizes'][ $_size ] ) ) {
						wp_get_attachment_image($thumbnail_id, $_size, false, $attributes);
					}
				}
			}
		}

		return adventure_tours_get_the_post_thumbnail($postId, $imageSize, $attributes);
	}
}

if ( ! function_exists( 'adventure_tours_get_the_post_thumbnail' ) ) {
	/**
	 * Returns post featured image in requested size.
	 * Returns grey image in case if after import attachments have not been downloaded.
	 *
	 * @param  int    $postId             post id.
	 * @param  string $size               code of the image size.
	 * @param  string $attributes         attributes that should be applied for the img tag.
	 * @param  array  $undefinedSize      size of the image that should be returned (used if $size is undefined).
	 * @return string
	 */
	function adventure_tours_get_the_post_thumbnail($postId = null, $size = 'full', array $attributes = array(), array $undefinedSize = array( 770, 514 )) {
		$result = '';

		if ( null === $postId ) {
			$postId = get_the_ID();
		}

		if ( ! has_post_thumbnail( $postId ) ) {
			return $result;
		}

		if ( $postId ) {
			$result = get_the_post_thumbnail( $postId, $size, $attributes );
		}

		if ( ! $result ) {
			$imageManger = adventure_tours_di( 'image_manager' );
			if ( ! $imageManger ) {
				return $result;
			}

			$sizeDetails = $imageManger->getImageSizeDetails( $size );
			if ( $sizeDetails ) {
				$width = isset( $sizeDetails['width'] ) ? $sizeDetails['width'] : null;
				$height = isset( $sizeDetails['height'] ) ? $sizeDetails['height'] : null;

				if ( $height > $width * 5 ) {
					$height = round( $width * 1.5 );
				}
			} else {
				$width = isset( $undefinedSize[0] ) ? $undefinedSize[0] : null;
				$height = isset( $undefinedSize[1] ) ? $undefinedSize[1] : null;
			}

			if ( $width && $height ) {
				$result = $imageManger->getPlaceholdImage( $width, $height, '', true, $attributes );
			}
			// Else throw new Exception("Image size {$size} not defined."); .
		}

		return $result;
	}
}

if ( ! function_exists( 'adventure_tours_placeholder_img_src' ) ) {
	/**
	 * Returns url to the placeholder image saved in theme options.
	 *
	 * @param  string $size image size code.
	 * @return string
	 */
	function adventure_tours_placeholder_img_src( $size = 'large' ) {
		static $url;
		if ( null === $url ) {
			$url = adventure_tours_get_option( 'placeholder_image', '' );
			if ( ! $url ) {
				$url = get_template_directory_uri() . '/assets/images/placeholder.png';
			}
		}

		return $url;
	}
}

if ( ! function_exists( 'adventure_tours_placeholder_img' ) ) {
	/**
	 * Returns html with img that renders placeholder image.
	 *
	 * @param  string $size image size code.
	 * @return string
	 */
	function adventure_tours_placeholder_img( $size = 'large' ) {
		$dimensions = adventure_tours_di( 'image_manager' )->getImageSizeDetails( $size );
		if ( ! $dimensions ) {
			$dimensions = adventure_tours_di( 'image_manager' )->getImageSizeDetails( 'large' );
		}

		return apply_filters( 'adventure_tours_placeholder_img', '<img src="' . adventure_tours_placeholder_img_src( $size ) . '" alt="' . esc_attr__( 'Placeholder', 'adventure-tours' ) . '" width="' . esc_attr( $dimensions['width'] ) . '" class="woocommerce-placeholder wp-post-image" height="' . esc_attr( $dimensions['height'] ) . '" />', $size, $dimensions );
	}
}

if ( ! function_exists( 'adventure_tours_render_template_part' ) ) {
	/**
	 * Analog for the get_template_part function.
	 * Allows pass params to view file.
	 *
	 * @param  string  $templateName    view name.
	 * @param  string  $templatePostfix optional postfix.
	 * @param  array   $data            assoc array with variables that should be passed to view.
	 * @param  boolean $return          if result should be returned instead of outputting.
	 * @return string
	 */
	function adventure_tours_render_template_part($templateName, $templatePostfix = '', array $data = array(), $return = false) {
		static $app;
		if ( ! $app ) {
			$app = adventure_tours_di( 'app' );
		}
		return $app->renderTemplatePart( $templateName, $templatePostfix, $data, $return );
	}
}

if ( ! function_exists( 'adventure_tours_the_content' ) ) {
	/**
	 * Determines what function should be used for content section rendering the_excerpt or
	 * the_content based on 'is_excerpt' theme option value.
	 *
	 * @return void
	 */
	function adventure_tours_the_content() {
		if ( adventure_tours_get_option( 'is_excerpt' ) ) {
			the_excerpt();
		} else {
			the_content( adventure_tours_get_option( 'excerpt_text' ) );
		}
	}
}

if ( ! function_exists( 'adventure_tours_wp_title_filter' ) ) {
	/**
	 * Title rendering filter function.
	 *
	 * @param  string $title
	 * @param  string $sep
	 * @param  string $seplocation
	 * @return string
	 */
	function adventure_tours_wp_title_filter( $title, $sep, $seplocation ) {
		if ( is_feed() ) {
			return $title;
		}

		if ( ! $title) {
			$title = get_bloginfo('name', 'display') . ( $sep && 'right' == $seplocation ? " $sep " : '' );
		}

		if (!$sep) {
			return trim($title);
		}

		global $page, $paged;
		$fullSep = $sep ? " $sep " : ' ';
		$firstSep = 'right' == $seplocation ? '' : $fullSep;

		if ( ( is_home() || is_front_page() ) && ($site_description = get_bloginfo( 'description', 'display' ))) {
			$title .= $firstSep . $site_description;
		} else {
			$title .= $firstSep . get_bloginfo( 'name', 'display' );
		}

		// Add a page number if necessary:
		if ( ( $paged > 1 || $page > 1 ) && ! is_404() ) {
			$title .= $fullSep . sprintf( esc_html__( 'Page %s', 'adventure-tours' ), max( $paged, $page ) );
		}

		return $title;
	}
	// add_filter( 'wp_title', 'adventure_tours_wp_title_filter', 10, 3 );
}

// -----------------------------------------------------------------#
// Rendering: excerpt
// -----------------------------------------------------------------#
if ( ! function_exists( 'adventure_tours_do_excerpt' ) ) {
	/**
	 * Custom excerpt text generation function.
	 *
	 * @param  string $string     text that should be truncated.
	 * @param  int    $word_limit max allowed number of words.
	 * @return string
	 */
	function adventure_tours_do_excerpt( $string, $word_limit ) {
		if ( $string ) {
			if ( '0' === $word_limit || 0 === $word_limit ) {
				return '';
			}
			$string = strip_shortcodes( $string );
			$string = $word_limit > 0 ? wp_trim_words( $string, $word_limit, '') : wp_strip_all_tags( $string );
		}

		return $string;
	}
}
if ( ! function_exists( 'adventure_tours_get_short_description' ) ) {
	/**
	 * Returns short description for current post or for the specefied post.
	 *
	 * @param  WP_Post $forPost    optional post object (if empty - current post will be used).
	 * @param  int     $word_limit max allowed words count.
	 * @return string
	 */
	function adventure_tours_get_short_description( $forPost = null, $word_limit = null ) {
		if ( null === $forPost ) {
			$forPost = get_post();
		}

		if ( ! $forPost ) {
			return '';
		}

		$text = $forPost->post_excerpt ? $forPost->post_excerpt : $forPost->post_content;
		if ( $text ) {
			return adventure_tours_do_excerpt( $text, $word_limit );
		} else {
			return $text;
		}
	}
}

if ( ! function_exists( 'adventure_tours_excerpt_more_link' ) ) {
	/**
	 * Filter for formatting excerpt more link.
	 * Depends on theme options.
	 *
	 * @return string
	 */
	function adventure_tours_excerpt_more_link() {
		if ( adventure_tours_di( 'register' )->getVar( 'disable_excerpt_more_link' ) ) {
			return '';
		}
		static $moreText;
		if ( null === $moreText ) {
			$moreText = esc_html( adventure_tours_get_option( 'excerpt_text' ) );
		}

		return sprintf(
			'<div class="padding-top text-center"><a href="%s" class="atbtn atbtn--medium atbtn--rounded atbtn--transparent">%s<i class="atbtn__icon atbtn__icon--right fa fa-long-arrow-right"></i></a></div>',
			esc_url( get_permalink() ),
			esc_html( $moreText )
		);
	}
	add_filter( 'excerpt_more', 'adventure_tours_excerpt_more_link', 9, 1 );
}

if ( ! function_exists( 'adventure_tours_custom_excerpt_length' ) ) {
	/**
	 * Filter for excerpt_length hook.
	 *
	 * @param  int $length current length value.
	 * @return int
	 */
	function adventure_tours_custom_excerpt_length($length) {
		return adventure_tours_get_option( 'excerpt_length' );
	}
	add_filter( 'excerpt_length', 'adventure_tours_custom_excerpt_length', 999 );
}

if ( ! function_exists( 'adventure_tours_content_more_link_filter' ) ) {
	/**
	 * Filter for content more link text.
	 *
	 * @param  string $link     link html.
	 * @param  string $linkText text.
	 * @return string
	 */
	function adventure_tours_content_more_link_filter($link, $linkText = '') {
		if ( ! $link ) {
			return '';
		}

		static $moreText;
		if ( null === $moreText ) {
			$moreText = esc_html( adventure_tours_get_option( 'excerpt_text' ) );
		}

		return sprintf(
			'<div class="padding-top text-center"><a href="%s" class="atbtn atbtn--medium atbtn--rounded atbtn--transparent">%s<i class="atbtn__icon atbtn__icon--right fa fa-long-arrow-right"></i></a></div>',
			esc_url( get_permalink() ),
			esc_html( $moreText )
		);
	}
	add_filter( 'the_content_more_link', 'adventure_tours_content_more_link_filter', 10, 2 );
}

if ( ! function_exists( 'adventure_tours_fix_broken_p' ) ) {
	/**
	 * Removes and fixes broken P tags.
	 *
	 * @param  string $content
	 * @return string
	 */
	function adventure_tours_fix_broken_p( $content ) {
		// $is_vc_frontend = isset( $_GET['vc_editable'] ) && isset ( $_GET['vc_post_id'] );

		// To prevent processing content that contains revslider related elements,
		// as force_balance_tags brokes revslider javascript code.
		if ( strpos( $content, '<script' ) !== false || strpos( $content, 'class="rev_slider' ) !== false ) {
			return $content;
		}

		// Removes broken <p> tags added by wpuatop filter in case if sorce code editor has been used for content edition.
		$result = preg_replace(
			array(
				'`<p>\s*<div([^>]*)>(?:\s*</p>)?|<div([^>]*)>\s*</p>`', // <p><div></p>
				'`(<p>\s*)?</div>\s*</p>|<p>\s*</div>`', // <p></div></p>
			),
			array(
				'<div$1$2>',
				'</div>',
			),
			$content
		);
		// Fixes unclosed/unopened P tags
		return force_balance_tags( $result );
	}
	add_filter( 'the_content', 'adventure_tours_fix_broken_p', 11, 1 );
}

if ( ! function_exists( 'adventure_tours_esc_text' ) ) {
	/**
	 * Escapes multi line text.
	 *
	 * @param  string  $text
	 * @param  string  $context      name of the context
	 * @param  boolean $is_mutliline is multi line text
	 * @return string
	 */
	function adventure_tours_esc_text( $text, $context = 'post', $is_mutliline = false ) {
		$result = '';
		if ($text) {
			$tagsWhiteList = wp_kses_allowed_html( $context );
			$result = wp_kses( $text, $tagsWhiteList );
			if ($is_mutliline) {
				$result = nl2br( $result );
			}
		}
		return $result ? force_balance_tags( $result ) : '';
	}
}

if ( ! function_exists( 'adventure_tours_kses_allowed_html_filter' ) ) {
	/**
	 * Applies specific tags settings based on context.
	 *
	 * @param  assoc  $tags    list of allowed tags.
	 * @param  string $context text content id.
	 * @return assoc
	 */
	function adventure_tours_kses_allowed_html_filter( $tags, $context = '' ) {
		switch ( $context ) {
		case 'option_input':
			$tags['span'] = array(
				'class' => true,
				'id' => true,
			);
			$tags['i'] = array(
				'class' => true,
				'id' => true,
			);
			$tags['a'] = array(
				'title' => true,
				'href' => true,
				'target' => true,
				'class' => true,
				'id' => true,
			);
			break;
		}
		return $tags;
	}
	add_filter( 'wp_kses_allowed_html', 'adventure_tours_kses_allowed_html_filter', 10, 2 );
}

// -----------------------------------------------------------------#
// Renderind: paginations
// -----------------------------------------------------------------#
if ( ! function_exists( 'adventure_tours_render_pagination' ) ) {
	/**
	 * Renders posts pagination.
	 *
	 * @param  string   $before  prefix text.
	 * @param  string   $after   postfix text.
	 * @param  WP_Query $query   query.
	 * @param  assoc    $options assoc that accepts 'base', 'format', 'current' and 'total' options for paginate_links function
	 * @return void
	 */
	function adventure_tours_render_pagination( $before = '', $after = '', $query = null, $options = array() ) {
		global $wp_query;
		if ( ! $query ) {
			$query = $wp_query;
		}

		if ( $query->max_num_pages <= 1 ) {
			return '';
		}

		$big = 999999999;
		$current = max( 1, $options && isset( $options['current'] ) ? $options['current'] : get_query_var( 'paged' ) );
		$prev_link_text = '<i class="fa fa-angle-left"></i>';
		$next_link_text = '<i class="fa fa-angle-right"></i>';

		$paginate = paginate_links( array(
			'base' => $options && isset( $options['base'] ) ? $options['base'] : str_replace( $big, '%#%', get_pagenum_link( $big, false ) ),
			'type' => 'array',
			'total' => $options && isset( $options['total'] ) ? $options['total'] : $query->max_num_pages,
			'format' => $options && isset( $options['format'] ) ? $options['format'] : '?paged=%#%',
			'mid_size' => 1,
			'current' => $current,
			'prev_text' => $prev_link_text,
			'next_text' => $next_link_text,
		) );

		$pages_html = '';
		$prev_link = '<div class="navigation__item navigation__prev navigation__item--disable"><span class="prev page-numbers">' . $prev_link_text . '</span></div>';
		$next_link = '<div class="navigation__item navigation__next navigation__item--disable"><span class="next page-numbers">' . $next_link_text . '</span></div>';

		foreach ( $paginate as $index => $page ) {
			if ( preg_match( '/class="prev page-numbers"/', $page ) ) {
				$prev_link = '<div class="navigation__item navigation__prev">' . $page . '</div>';
				continue;
			}

			if ( preg_match( '/class="next page-numbers"/', $page ) ) {
				$next_link = '<div class="navigation__item navigation__next">' . $page . '</div>';
				continue;
			}

			$pages_html .= '<div class="navigation__item">' . $page . '</div>';
		}

		printf(
			 '%s<div class="navigation">' .
				'<div class="navigation__content">%s<div class="navigation__items">%s</div>%s</div>' .
			'</div>%s',
			$before,
			$prev_link,
			$pages_html,
			$next_link,
			$after
		);
	}
}

// pagination rendering on the tours archive page
// add_action( 'adventure_tours_after_tours_loop', 'woocommerce_pagination', 10 );
//fix for issues with pagination rendering since WooCommerce 3.3.3
if ( ! function_exists( 'adventure_tours_woocommerce_pagination' ) ) {
	function adventure_tours_woocommerce_pagination(){
		if ( wc_get_loop_prop( 'total_pages' ) > 1 ) {
			// this "hack" guarantees that woocommerce_get_loop_display_mode() returns "products"
			// otherwise woocommerce_products_will_display() may block woocommerce_pagination() function
			wc_set_loop_prop( 'is_search', true );

			woocommerce_pagination();
		}
	}
	add_action( 'adventure_tours_after_tours_loop', 'adventure_tours_woocommerce_pagination', 10 );
}

if ( ! function_exists( 'adventure_tours_render_post_pagination' ) ) {
	/**
	 * Renders pagination for post pages.
	 *
	 * @return void
	 */
	function adventure_tours_render_post_pagination() {
		global $page, $numpages;
		if ( $numpages < 2 ) {
			return '';
		}

		$res = wp_link_pages(array(
			'before' => '',
			'after' => '',
			'separator' => "\n",
			'next_or_number' => 'number',
			'echo' => false,
		));

		$parts = explode( "\n", $res );
		if ( count( $parts ) < 2 ) {
			return '';
		}

		$activeIndex = $page -1;
		$itemsHtml = '';

		foreach ( $parts as $index => $itemHtml ) {
			if ( $index === $activeIndex ) {
				$itemsHtml .= '<div class="post-page-navigation__item"><span class="current">' . $page . '</span></div>';
			} else {
				$itemsHtml .= '<div class="post-page-navigation__item">' . $itemHtml . '</div>';
			}
		}

		$prev = $page - 1;
		$prevText = esc_html__( 'Previous', 'adventure-tours' );
		if ( $prev ) {
			$linkPrev = '<div class="post-page-navigation__item post-page-navigation__prev">' . _wp_link_page( $prev ) . $prevText . '</a></div>';
		} else {
			$linkPrev = '<div class="post-page-navigation__item post-page-navigation__item--disable post-page-navigation__prev"><span>' . $prevText . '</span></div>';
		}

		$next = $page + 1;
		$nextText = esc_html__( 'Next', 'adventure-tours' );
		if ( $next <= $numpages ) {
			$linkNext = '<div class="post-page-navigation__item post-page-navigation__next">' . _wp_link_page( $next ) . $nextText . '</a></div>';
		} else {
			$linkNext = '<div class="post-page-navigation__item post-page-navigation__item--disable post-page-navigation__next"><span>' . $nextText . '</span></div>';
		}

		printf(
			'<div class="post-page-navigation">' .
				'%s<div class="post-page-navigation__items">%s</div>%s' .
			'</div>',
			$linkPrev,
			$itemsHtml,
			$linkNext
		);
	}
}

if ( ! function_exists( 'adventure_tours_comments_pagination' ) ) {
	/**
	 * Comments pagination functionality.
	 *
	 * @return string
	 */
	function adventure_tours_comments_pagination() {
		$numpages = get_option( 'page_comments' ) ? get_comment_pages_count() : 0;
		if ( $numpages <= 1 ) {
			return '';
		}

		$prev_next_link_title = 'prev_next_link';
		// $paginationLinks = apply_filters( 'adventure_tours_comments_pagination_link_args', array() );
		$paginationLinks = paginate_comments_links(array(
			'show_all' => false,
			'type' => 'array',
			'echo' => false,
			'prev_text' => $prev_next_link_title,
			'next_text' => $prev_next_link_title,
		));

		$linksHtml = '';
		foreach ( $paginationLinks as $link ) {
			if ( false === strpos( $link, $prev_next_link_title ) ) {
				$linksHtml .= '<div class="comments__navigation__item">' . $link . '</div>';
			}
		}

		$nextLink = get_next_comments_link( esc_html__( 'next', 'adventure-tours' ) );
		$prevLink = get_previous_comments_link( esc_html__( 'previous', 'adventure-tours' ) );

		$nextLink = ( ! empty( $nextLink ))
		?
		'<div class="comments__navigation__item comments__navigation__next">' . $nextLink . '</div>'
				:
				'<div class="comments__navigation__item comments__navigation__item--disable comments__navigation__next"><span>' . esc_html__( 'next', 'adventure-tours' ) . '</span></div>';

		$prevLink = ( ! empty( $prevLink ))
		?
		'<div class="comments__navigation__item comments__navigation__prev">' . $prevLink . '</div>'
				:
				'<div class="comments__navigation__item comments__navigation__item--disable comments__navigation__prev"><span>' . esc_html__( 'previous', 'adventure-tours' ) . '</span></div>';

		printf( 
			'<div class="comments__navigation">' .
				'<div class="comments__navigation__content padding-left padding-right">' .
					'%s<div class="comments__navigation__items">%s</div>%s' .
				'</div>' .
			'</div>',
			$prevLink,
			$linksHtml,
			$nextLink
		);
	}
}

if ( ! function_exists( 'adventure_tours_render_tour_search_form' ) ) {
	/**
	 * Renders tours search form.
	 *
	 * @param  boolean $allow_cache
	 * @return string
	 */
	function adventure_tours_render_tour_search_form( $allow_cache = true ) {
		static $cache;

		if ( ! $allow_cache || null === $cache ) {
			$title = adventure_tours_get_option( 'tours_search_form_title' );
			$note = adventure_tours_get_option( 'tours_search_form_note' );

			$cache = do_shortcode( sprintf('[tour_search_form title="%s" note="%s"]',
				$title,
				$note
			) );
		}

		return $cache;
	}
}

// booking form rendering - start

if ( ! function_exists( 'adventure_tours_render_tour_booking_form' ) ) {
	/**
	 * Renders tours booking form.
	 *
	 * @param  WC_Product_Tour $product
	 * @return string
	 */
	function adventure_tours_render_tour_booking_form( $product = null ) {
		return adventure_tours_di( 'booking_form' )->render( $product );
	}
}

if ( ! function_exists( 'adventure_tours_action_sidebar_booking_form' ) ) {
	/**
	 * Renders booking form in a sidebar if 'sidbar' is seleted as a location for the booking form.
	 *
	 * @return void
	 */
	function adventure_tours_action_sidebar_booking_form(){
		echo adventure_tours_render_tour_booking_form_for_location( 'sidebar' );
	}
	add_action( 'adventure_tours_sidebar_booking_form', 'adventure_tours_action_sidebar_booking_form' );
}

if ( ! function_exists( 'adventure_tous_action_tour_single_before_tabs_booking_form' ) ) {
	/**
	 * Renders booking form above tour tabs if 'above_tabs' is seleted as a location for the booking form.
	 *
	 * @return void
	 */
	function adventure_tous_action_tour_single_before_tabs_booking_form() {
		echo adventure_tours_render_tour_booking_form_for_location( 'above_tabs', array(
			'before_form' => '<div class="booking-form-wrapper booking-form-wrapper--above-tabs">',
			'after_form' => '</div>',
		) );
	}
	add_action( 'adventure_tous_tour_single_before_tabs', 'adventure_tous_action_tour_single_before_tabs_booking_form' );
}

if ( ! function_exists( 'adventure_tous_action_tour_single_under_tabs_booking_form' ) ) {
	/**
	 * Renders booking form under tour tabs if 'under_tabs' is seleted as a location for the booking form.
	 *
	 * @return void
	 */
	function adventure_tous_action_tour_single_under_tabs_booking_form() {
		echo adventure_tours_render_tour_booking_form_for_location( 'under_tabs', array(
			'before_form' => '<div class="booking-form-wrapper booking-form-wrapper--under-tabs">',
			'after_form' => '</div>'
		) );
	}
	add_action( 'adventure_tous_tour_single_after_tabs', 'adventure_tous_action_tour_single_under_tabs_booking_form' );
}

if ( ! function_exists( 'adventure_tous_render_fixed_booking_button' ) ) {
	function adventure_tous_render_fixed_booking_button() {
		if ( ! adventure_tours_get_option( 'tours_booking_form_enable_fixed_booking_btn' ) ) {
			return;
		}

		printf(
			'<div id="fixedTourBookingBtnBox" class="tour-fixed-booking-btn-box"><a href="#tourBooking" class="tour-fixed-booking-btn-box__btn atbtn atbtn--secondary">%s</a></div>',
				apply_filters( 'adventure_tours_booking_form_btn_text', esc_html__( 'Book now', 'adventure-tours'), wc_get_product() )
		);
		TdJsClientScript::addScript(
			'initFixedTourBookingButton',
			'Theme.tourBookingForm.initFixedTourBookingButtonScroller("#fixedTourBookingBtnBox", "tour-booking-form-is-scrolled-out");'
		);
	}
	add_action( 'adventure_tous_tour_single_after_tabs', 'adventure_tous_render_fixed_booking_button' );
}

if ( ! function_exists( 'adventure_tours_render_tour_booking_form_for_location' ) ) {
	/**
	 * Checks if $location equal with settings for booking form location and renders booking form in this case.
	 *
	 * @param  string $location allowed values are: 'sidebar', 'above_tabs', 'under_tabs'
	 * @param  array  $options  assoc that may contains 'before_form' and/or 'after_form' keys (may contain pice of html)
	 * @return string           html
	 */
	function adventure_tours_render_tour_booking_form_for_location( $location, $options = array() ) {
		$product = is_singular( 'product' ) ? wc_get_product() : null;
		$result = '';
		if ( $location && $location == adventure_tours_get_booking_form_location_for_tour( $product ) ) {

			// renders price decoration element
			$result = adventure_tours_render_template_part( 'templates/tour/price-decoration', '', array(), true );

			$result .= adventure_tours_render_tour_booking_form( $product );
			if ( $result ) {
				if ( ! empty( $options['before_form'] ) ) {
					$result = $options['before_form'] . $result;
				}

				if ( ! empty( $options['after_form'] ) ) {
					$result = $result . $options['after_form'];
				}
			}
		}

		return $result;
	}
}

// booking form rendering - end

if ( ! function_exists( 'adventure_tours_render_tour_categories' ) ) {
	/**
	 * Renders list of tour categories related to current tour category (if page is taxonomy) or top level tour categories.
	 *
	 * @param  array $args assoc that contains rendering settings.
	 * @return void
	 */
	function adventure_tours_render_tour_categories( $args = array() ) {
		if ( ! adventure_tours_check( 'tour_category_taxonomy_exists' ) ) {
			return;
		}

		$args = wp_parse_args( $args, array(
			'before' => '',
			'after' => '',
		) );

		$term = get_queried_object();
		$parent_id = empty( $term->term_id ) ? 0 : $term->term_id;

		$tour_categories = get_categories( apply_filters( 'woocommerce_product_subcategories_args', array(
			'parent'       => $parent_id,
			'menu_order'   => 'ASC',
			'hide_empty'   => 0,
			'hierarchical' => 1,
			'taxonomy'     => 'tour_category',
			'pad_counts'   => 1,
		) ) );
		$product_categories = wp_list_filter( $tour_categories, array( 'count' => 0 ), 'NOT' );

		if ( $product_categories ) {
			$items_html = '';
			foreach ( $product_categories as $category ) {
				$items_html .= adventure_tours_render_template_part( 'templates/tour/content-tour_category', '', array(
					'category' => $category,
				), true );
			}

			printf(
				'%s%s%s',
				$args['before'],
				$items_html,
				$args['after']
			);
		}
	}
}

if ( ! function_exists( 'adventure_tours_render_category_thumbnail' ) ) {
	/**
	 * Show subcategory thumbnail.
	 *
	 * @param  mixed $category
	 * @return void
	 */
	function adventure_tours_render_category_thumbnail( $category ) {
		$small_thumbnail_size = apply_filters( 'single_product_small_thumbnail_size', 'shop_catalog' );
		$dimensions = wc_get_image_size( $small_thumbnail_size );
		$thumbnail_id = AtTourHelper::get_tour_category_thumbnail( $category );
		$image = null;

		if ( $thumbnail_id ) {
			$image = wp_get_attachment_image_src( $thumbnail_id, $small_thumbnail_size );
			if ( $image ) {
				$image = $image[0];
			}
		}

		if ( ! $image ) {
			$image = wc_placeholder_img_src();
		}

		if ( $image ) {
			// Prevent esc_url from breaking spaces in urls for image embeds
			// Ref: http://core.trac.wordpress.org/ticket/23605
			$image = str_replace( ' ', '%20', $image );

			echo '<img src="' . esc_url( $image ) . '" alt="' . esc_attr( $category->name ) . '" width="' . esc_attr( $dimensions['width'] ) . '" height="' . esc_attr( $dimensions['height'] ) . '" />';
		}
	}
}

if ( ! function_exists( 'adventure_tours_render_tour_icons' ) ) {
	/**
	 * Renders tour categories related to current tour item.
	 *
	 * @param  array $args   assoc that contains rendering settings.
	 * @param  int   $postId optional post id.
	 * @return void
	 */
	function adventure_tours_render_tour_icons( $args = array(), $postId = null ) {
		if ( ! adventure_tours_check( 'tour_category_taxonomy_exists' ) ) {
			return;
		}

		$args = wp_parse_args( $args, array(
			'before' => '',
			'after' => '',
			'limit' => 0,     // int, use "-1" as a value to prevent icons rendering at all
			'parent' => null, // array, set of allowed parent categories items from that can be rendered: array( 80, 81 )
		) );

		$allowed_for_render = empty( $args['limit'] ) ? 0 : $args['limit'];
		if ( $allowed_for_render < 0 ) {
			return;
		}

		if ( null === $postId ) {
			$postId = get_the_ID();
		}

		$items = $postId ? wp_get_object_terms( $postId, 'tour_category' ) : null;
		if ( ! $items ) {
			return;
		}
		$parent_allowed_list = $args['parent']
			? (
				! is_array( $args['parent'] ) ? explode( ',', $args['parent'] ) : $args['parent'] 
			)
			: null;

		$items_html = '';
		foreach ( $items as $item ) {
			if ( $parent_allowed_list && ! in_array( $item->parent, $parent_allowed_list ) ) {
				continue;
			}

			$icon_class = AtTourHelper::get_tour_category_icon_class( $item );
			if ( $icon_class ) {
				$items_html .= sprintf('<a href="%s"><i data-toggle="tooltip" title="%s" class="%s"></i></a>',
					esc_url( get_term_link( $item->slug, 'tour_category' ) ),
					esc_attr( $item->name ),
					esc_attr( $icon_class )
				);

				if ( $allowed_for_render > 0 && --$allowed_for_render < 1 ) {
					break;
				}
			}
		}

		if ( $items_html ) {
			printf( '%s%s%s',
				$args['before'],
				$items_html,
				$args['after']
			);
		}
	}
}

if ( ! function_exists( 'adventure_tours_render_product_attributes' ) ) {
	/**
	 * Renders product attributes on archive page.
	 *
	 * @param  array $args assoc that contains rendering settings.
	 * @param  int   $postId optional post id.
	 * @return void
	 */
	function adventure_tours_render_product_attributes( $args = array(), $postId = null ) {
		global $product;

		$curProduct = $postId ? wc_get_product( $postId ) : $product;

		$list = AtTourHelper::get_tour_details_attributes( $curProduct, true );
		if ( ! $list ) {
			return;
		}

		$defaults = array(
			'before' => '',
			'after' => '',
			'before_each' => '',
			'after_each' => '',
			'limit' => 5,
			'values_limit' => 0,
		);

		$args = wp_parse_args( $args, $defaults );

		$values_limit = $args['values_limit'] > 0 ? $args['values_limit'] : 0;

		$items_html = '';
		foreach ( $list as $attribute ) {
			$values_text = $values_limit > 0 && count( $attribute['values'] ) > $values_limit ? join( ', ', array_slice( $attribute['values'], 0, $values_limit ) ) : $attribute['text'];
			$items_html .= $args['before_each'] .
				'<div class="item-attributes__item__content">' .
					( $attribute['icon_class'] ? '<div class="item-attributes__item__content__item"><i class="' . esc_attr( $attribute['icon_class'] ) . '"></i></div>' : '' ) .
					'<div class="item-attributes__item__content__item item-attributes__item__content__item--text"><span>' . $values_text . '</span></div>' .
				'</div>' .
			$args['after_each'];

			if ( $args['limit'] > 0 ) {
				$args['limit']--;
				if ( $args['limit'] < 1 ) {
					break;
				}
			}
		}

		printf( '%s%s%s',
			$args['before'],
			$items_html,
			$args['after']
		);
	}
}

if ( ! function_exists( 'adventure_tours_post_gallery_filter' ) ) {
	/**
	 * Filter used to customize galleries output.
	 *
	 * @param  string $empty
	 * @param  assoc  $attr gallery shortcode attributes
	 * @return string
	 */
	function adventure_tours_post_gallery_filter( $empty, $attr ) {
		global $post;

		extract( shortcode_atts( array(
			'order' => 'ASC',
			'orderby' => 'menu_order ID',
			'id' => $post ? $post->ID : 0,
			'include' => '',
			'exclude' => '',
			// custom attributes
			'layout' => 'default',
			'pagination' => '',
			'filter' => '',
			'single_page' => '',
			'columns' => 3,
		), $attr, 'gallery' ) );

		// get attachments set
		$queryArgs = array(
			'post_status' => 'inherit',
			'post_type' => 'attachment',
			'post_mime_type' => 'image',
			'order' => $order,
			'orderby' => $orderby,
		);

		$id = intval( $id );

		if ( $include ) {
			$queryArgs['include'] = $include;
		} else {
			$queryArgs['post_parent'] = $id;
			if ( $exclude ) {
				$queryArgs['exclude'] = $exclude;
			}
		}

		$attachments = get_posts( $queryArgs );
		if ( ! $attachments ) {
			if ( $include && ! is_feed() ) {
				// allow to render gallery with placeholders (for galleries that have been imported with content)
			} else {
				return '';
			}
		}
		// For RSS
		if ( is_feed() ) {
			$output = "\n";
			foreach ( $attachments as $attachment ) {
				$output .= wp_get_attachment_link( $attachment->ID ) . "\n";
			}
			return $output;
		}

		$defaultThumbSize = 'thumb_gallery';
		$defaultFullSize = 'full';

		$galleryLayouts = array(
			'default' => array(
				'showCategories' => true,
				'allowPagination' => true,
				'thumbSize' => $defaultThumbSize,
			),
		);

		if ( ! $layout || ! isset( $galleryLayouts[$layout] ) ) {
			$layout = 'default';
		}

		$layoutConfig = $galleryLayouts[$layout];
		$thumbSize = isset( $layoutConfig['thumbSize'] ) ? $layoutConfig['thumbSize'] : $defaultThumbSize;
		$fullSize = isset( $layoutConfig['fullSize'] ) ? $layoutConfig['fullSize'] : $defaultFullSize;

		$showCategories = ! empty( $layoutConfig['showCategories'] ) && adventure_tours_check( 'media_category_taxonomy_exists' );
		$is_filter = adventure_tours_di( 'shortcodes_helper' )->attribute_is_true( $filter );
		if ( $is_filter && ! $showCategories ) {
			$is_filter = false;
		}

		$is_pagination = adventure_tours_di( 'shortcodes_helper' )->attribute_is_true( $pagination );
		if ( empty( $layoutConfig['allowPagination'] ) && $is_pagination ) {
			$is_pagination = false;
		}

		$gallery_images = array();
		$full_categories_list = array();
		if ( $attachments ) {
			foreach ( $attachments as $attachment ) {
				$attachemntId = $attachment->ID;

				// Get image link to a specific sizes
				// Image attribute [0] => url [1] => width [2] => height
				$image_attributes_full = wp_get_attachment_image_src( $attachemntId, $fullSize );
				$image_attributes_custom_size = wp_get_attachment_image_src( $attachemntId, $thumbSize );
				$link_full = ! empty( $image_attributes_full[0] ) ? $image_attributes_full[0] : '';
				$link_custom_size = ! empty( $image_attributes_custom_size[0] ) ? $image_attributes_custom_size[0] : '';

				// categories
				$image_categories = array();
				if ( $showCategories ) {
					$taxonomies = get_the_terms( $attachemntId, 'media_category' ); // 'category'
					if ( $taxonomies ) {
						foreach ( $taxonomies as $taxonomy ) {
							$full_categories_list[$taxonomy->slug] = $taxonomy->name;
							$image_categories[$taxonomy->slug] = $taxonomy->name;
						}
					}
				}

				$alt = get_post_meta( $attachemntId, '_wp_attachment_image_alt', true );
				$gallery_images[] = array(
					'id' => $attachemntId,
					'attachmentId' => $attachemntId,
					'link_full' => $link_full,
					'link_custom_size' => $link_custom_size,
					'title' => $attachment->post_title,
					'caption' => wp_get_attachment_caption( $attachemntId ),
					'categories' => $image_categories,
					'alt' => $alt ? $alt : $attachment->post_title,
				);
			}
		} elseif ( $include ) {
			$imageManager = adventure_tours_di( 'image_manager' );
			$fullSizeDetails = $imageManager->getImageSizeDetails( $fullSize == 'full' ? 'large' : $fullSize );
			$thumbSizeDetails = $imageManager->getImageSizeDetails( $thumbSize );

			$includeIds = explode( ',', trim( $include,', ' ) );
			foreach ( $includeIds as $attachemntId ) {
				$dummyTitle = '#' . $attachemntId;
				$placeholdText = urlencode( $dummyTitle ); // find why additional encode is required

				$placeholdThumbUrl = $imageManager->getPlaceholdImage( $thumbSizeDetails['width'], $thumbSizeDetails['height'], $placeholdText );

				$fullImageUrl = $fullSizeDetails
					? $imageManager->getPlaceholdImage( $fullSizeDetails['width'], $fullSizeDetails['height'], $placeholdText )
					: $placeholdThumbUrl;

				$gallery_images[] = array(
					'id' => $attachemntId,
					'link_full' => $fullImageUrl,
					'link_custom_size' => $placeholdThumbUrl,
					'title' => $dummyTitle,
					'categories' => array(),
					'alt' => $dummyTitle,
				);
			}
		}

		if ( ! $gallery_images ) {
			return '';
		}

		$output = '';

		// get gallery id
		static $galleryCounter;
		if ( null == $galleryCounter ) {
			$galleryCounter = 1;
		} else {
			$galleryCounter++;
		}
		$galleryId = 'gallery_' . $galleryCounter;

		$classWithBanner = adventure_tours_di( 'register' )->getVar( 'is_banner' ) ? ' gallery--withbanner' : '';
		$classSinglePageMode = adventure_tours_di( 'shortcodes_helper' )->attribute_is_true( $single_page ) && $is_filter ? ' gallery--page' : '';
		$output .= '<div id="' . esc_attr( $galleryId ) . '" class="gallery' . esc_attr( $classSinglePageMode ) . esc_attr( $classWithBanner ) . '">';

		if ( $is_filter && $full_categories_list ) {
			$filterHtml = '<div class="gallery__navigation margin-bottom">' .
				'<ul>' .
					'<li class="gallery__navigation__item-current"><a href="#" data-filterid="all">' . esc_html__( 'all', 'adventure-tours' ) . '</a></li>';

			foreach ( $full_categories_list as $category_slug => $category_name ) {
				$filterHtml .= '<li><a href="#" data-filterid="' . esc_attr( $category_slug ) . '">' . esc_html( $category_name ) . '</a></li>';
			}

			$filterHtml .= '</ul></div>';

			$output .= $filterHtml;
		}

		ob_start();
		include locate_template( 'templates/gallery/' . $layout . '.php' );
		$output .= ob_get_clean();

		if ( $is_pagination ) {
			wp_enqueue_script( 'jPages' );
			$output .= '<div class="pagination margin-top"></div>';
		}

		$output .= '</div>';

		return $output;
	}

	add_filter( 'post_gallery', 'adventure_tours_post_gallery_filter', 10, 2 );
}

if ( ! function_exists( 'adventure_tours_renders_stars_rating' ) ) {
	/**
	 * Renders stars rating element.
	 *
	 * @param  int   $rating rating value in the [1-5] range.
	 * @param  array $args   rendering options.
	 * @return void
	 */
	function adventure_tours_renders_stars_rating( $rating, $args = array() ) {
		if ( $rating <= 0 ) {
			return;
		}

		$args = wp_parse_args( $args, array(
			'before' => '',
			'after' => '',
			'before_each' => '',
			'after_each' => '',
		) );

		$stars_html = '';
		for ( $i = 0; $i < $rating; $i++ ) {
			$stars_html .= $args['before_each'] . '<i class="fa fa-star"></i>' . $args['after_each'];
		}
		printf(
			'%s%s%s',
			$args['before'],
			$stars_html,
			$args['after']
		);
	}
}

if ( ! function_exists( 'adventure_tours_renders_tour_badge' ) ) {
	/**
	 * Renders badge for specefied tour.
	 *
	 * @param  array $args assoc that contains rendering settings.
	 * @return void
	 */
	function adventure_tours_renders_tour_badge( $args = array() ) {
		$defaults = array(
			'tour_id' => null,
			'text_before' => '',
			'text_after' => '',
			'wrap_css_class' => '',
			'wrap_container_tag' => 'div',
			'css_class' => '',
			'container_tag' => 'div',
		);

		$args = wp_parse_args( $args, $defaults );

		extract( $args );

		if ( null === $tour_id ) {
			$tour_id = get_the_ID();
		}

		$badge = adventure_tours_di( 'tour_badge_service' )->get_tour_badge( $tour_id );
		if ( $badge ) {
			echo strtr('<{wrap_tag} class="{wrap_class}"><{tag} class="{class}" style="background-color:{bg_color}">{text_before}{text}{text_after}</{tag}></{wrap_tag}>', array(
				'{wrap_tag}' => $wrap_container_tag ? $wrap_container_tag : 'div',
				'{wrap_class}' => esc_attr( $wrap_css_class ),
				'{tag}' => $container_tag ? $container_tag : 'div',
				'{class}' => esc_attr( $css_class ),
				'{bg_color}' => $badge['color'],
				'{text}' => esc_html( $badge['title'] ),
				'{text_before}' => $text_before,
				'{text_after}' => $text_after,
			) );
		}
	}
}

if ( ! function_exists( 'adventure_tours_get_footer_columns' ) ) {
	/**
	 * Returns number of columns that should be rendered in the footer.
	 *
	 * @return int
	 */
	function adventure_tours_get_footer_columns() {
		$footerLayout = adventure_tours_get_option( 'footer_layout' );
		$laoutToColumns = array(
			'3columns' => 3,
			'2columns' => 2,
		);
		return isset( $laoutToColumns[$footerLayout] ) ? $laoutToColumns[$footerLayout] : 4;
	}
}

// -----------------------------------------------------------------#
// Renderind: tour page details, tabs rendering
// -----------------------------------------------------------------#
if ( ! function_exists( 'adventure_tours_filter_tour_tabs' ) ) {
	/**
	 * Tour details page, tabs filter function.
	 * Defines what tabs should be rendered.
	 *
	 * @return void
	 */
	function adventure_tours_filter_tour_tabs( $tabs ) {
		global $product;
		if ( empty( $product ) ) {
			return $tabs;
		}

		$tabs['description'] = array(
			'title' => esc_html__( 'Details', 'adventure-tours' ),
			'priority' => 10,
			'top_section_callback' => 'adventure_tours_render_tab_description_top_section',
			'callback' => 'adventure_tours_render_tab_description',
		);

		$additionalTabs = vp_metabox( 'tour_tabs_meta.tabs' );
		if ( $additionalTabs ) {
			foreach ( $additionalTabs as $key => $tabFields ) {
				$tabContent = apply_filters( 'the_content', $tabFields['content'] );
				if ( $tabContent ) {
					$tabs[ 'atab' . $key ] = array(
						'priority' => 20,
						'title' => esc_html( $tabFields['title'] ),
						'content' => $tabContent,
					);
				}
			}
		}

		// Photos tab rendering.
		ob_start();
		adventure_tours_render_tab_photos();
		$photosTabContent = ob_get_clean();
		if ( $photosTabContent ) {
			$tabs['photos'] = array(
				'title' => esc_html__( 'Photos', 'adventure-tours' ),
				'priority' => 25,
				'content' => $photosTabContent,
			);
		}

		if ( comments_open() && adventure_tours_render_reviews_in_tab() ) {
			$tabs['reviews'] = array(
				'title'    => sprintf( esc_html__( 'Reviews (%d)', 'adventure-tours' ), $product->get_review_count() ),
				'priority' => 30,
				'callback' => 'comments_template',
			);
		}

		$booking_form_location = adventure_tours_get_booking_form_location_for_tour( $product );
		$booking_dates = $booking_form_location ? adventure_touts_get_tour_booking_dates( $product->get_id() ) : null;
		if ( $booking_dates ) {
			$tabs['booking_form'] = array(
				'title' => apply_filters( 'adventure_tours_booking_form_title', esc_html__( 'Book the tour', 'adventure-tours'), $product ),
				'tab_css_class' => 'visible-xs booking-form-scroller',
				'priority' => 35,
				'content' => ''
			);
		}

		return $tabs;
	}
}

if ( ! function_exists( 'adventure_tours_render_tab_description_top_section' ) ) {
	/**
	 * Tour details page, tab description icons/attributes rendeing function.
	 *
	 * @return void
	 */
	function adventure_tours_render_tab_description_top_section() {
		global $product;
		$all_attributes = AtTourHelper::get_tour_details_attributes( $product, null );
		$header_attributes = $all_attributes ? AtTourHelper::get_tour_details_attributes( $product, true ) : array();
		if ( $header_attributes ) {
			$count = count( $header_attributes );
			$count_to_batch_size = $count < 5 ? $count : (
				$count % 3 == 0 ? 3 : ( $count % 5 == 0 ? 5 : 4 )
			);
			/*
			$count_to_batch_size = $count < 4 ? $count : (
				$count % 4 == 0 ? 4 : 3
			);*/
			$attributes_batches = array_chunk( $header_attributes, $count_to_batch_size );
			foreach ( $attributes_batches as $attrib_batch ) {
				echo '<div class="tours-tabs__info">';
				foreach ( $attrib_batch as $attribute ) {
					echo strtr('<div class="tours-tabs__info__item">
						<div class="tours-tabs__info__item__content">
							<div class="tours-tabs__info__item__icon"><i class="{icon_class}"></i></div>
							<div class="tours-tabs__info__item__title">{value}</div>
							<div class="tours-tabs__info__item__description">{label}</div>
						</div>
					</div>', array(
						'{icon_class}' => esc_attr( $attribute['icon_class'] ),
						'{value}' => $attribute['text'],
						'{label}' => $attribute['label'],
					));
				}
				echo '</div>';
			}
		}

		$additional_attributes = $all_attributes && $header_attributes ? array_diff_key( $all_attributes, $header_attributes ) : $all_attributes;
		if ( $additional_attributes ) {
			$GLOBALS['_tour_additional_attributes'] = $additional_attributes;
		}
	}
}

if ( ! function_exists( 'adventure_tours_render_tab_description' ) ) {
	/**
	 * Tour details page, tab description rendeing function.
	 *
	 * @return void
	 */
	function adventure_tours_render_tab_description() {
		global $product;

		if ( adventure_tours_check( 'tour_category_taxonomy_exists' ) ) {
			$taxonomy = 'tour_category';
			$terms = get_the_terms( $product->get_id(), $taxonomy );
			if ( $terms ) {
				echo '<ul class="tour-categories-list list-block list-block--tour-tabs">';
				foreach ( $terms as $term ) {
					echo '<li><a href="' . get_term_link( $term->slug, $taxonomy ) . '">' . $term->name . '</a></li>';
				}
				echo '</ul>';
			}
		}

		the_content();

		if ( ! empty( $GLOBALS['_tour_additional_attributes'] ) ) {
			adventure_tours_render_template_part( 'templates/tour/additional-attributes', '', array(
				'title' => esc_html__( 'Additional information', 'adventure-tours' ),
				'attributes' => $GLOBALS['_tour_additional_attributes'],
			) );
		}

		// renders product tags
		/*if ( version_compare( WC_VERSION, '3.0.0', '<') ) {
			echo $product->get_tags(
				' ', // delimiter
				sprintf( '<div class="post-tags margin-top"><span><i class="fa fa-tags"></i>%s</span>', 'Tags:' ), // before
				'</div>' // after
			);
		} else {
			echo wc_get_product_tag_list(
				$product->get_id(),
				' ', //delimiter
				sprintf( '<div class="post-tags margin-top"><span><i class="fa fa-tags"></i>%s</span>', 'Tags:' ), // before
				'</div>' // after
			); 
		}*/
	}
}

if ( ! function_exists( 'adventure_tours_render_tab_photos' ) ) {
	/**
	 * Tour details page, tab photos rendeing function.
	 *
	 * @return void
	 */
	function adventure_tours_render_tab_photos() {
		$thumbnail = adventure_tours_get_the_post_thumbnail();
		if ( $thumbnail ) {
			wp_enqueue_style( 'swipebox' );
			wp_enqueue_script( 'swipebox' );
			TdJsClientScript::addScript( 'initProductSwipebox', "(function(s){jQuery(s).swipebox({useSVG:true,hideBarsDelay:0},s)})('.product-thumbnails .swipebox');" );
			echo sprintf( '<div class="row product-thumbnails"><div class="col-md-12"><a href="%s" class="woocommerce-main-image swipebox" title="%s">%s</a></div></div>',
				esc_url( wp_get_attachment_url( get_post_thumbnail_id() ) ),
				esc_attr( get_the_title() ),
				$thumbnail
			);
		}
		woocommerce_show_product_thumbnails( );
	}
}

if ( ! function_exists( 'adventure_tours_render_reviews_in_tab' ) ) {
	/**
	 * Determines if tour reviews should be rendered in additional tab or below tabs ( default ).
	 *
	 * @return boolean
	 */
	function adventure_tours_render_reviews_in_tab() {
		return false;
	}
}

if ( ! function_exists( 'adventure_tours_render_tours_ordering' ) ) {
	function adventure_tours_render_tours_ordering() {
		$list = adventure_tours_get_tours_archive_orderby();
		if ( ! $list ) {
			return;
		}

		$default_val = $current_val = adventure_tours_get_option( 'tours_archive_orderby' );

		if ( ! empty( $_GET['orderby'] ) && isset( $list[ $_GET['orderby'] ] ) ) {
			$current_val = $_GET['orderby'];
		}

		wc_get_template( 'loop/orderby.php', array(
			'catalog_orderby_options' => $list,
			'orderby' => $current_val,
			'show_default_orderby' => $default_val
		) );
	}
}

if ( ! function_exists( 'adventure_tours_render_tour_loop_header' ) ) {
	function adventure_tours_render_tour_loop_header() {
		echo '<div>';
		woocommerce_result_count();
		adventure_tours_render_tours_ordering();
		echo '<div class="clearfix"></div></div>';
	}
	// add this line to child theme to allow change tours sorting
	// add_action( 'adventure_tours_before_tours_loop', 'adventure_tours_render_tour_loop_header', 20 );
}


if ( ! function_exists( 'adventure_tours_hs_layout_make_rows' ) ) {
	/**
	 * Splits fields cells set into rows and calculates field cell sizes in a each row.
	 */
	function adventure_tours_hs_layout_make_rows( $cells, $row_capacity = 12 ) {
		$current_capacity = array_sum( $cells );
		$result = array();
		if ( $current_capacity > $row_capacity ) {
			$k_sum = 0;
			$k_row = array();
			$max_fields_per_row = 4;
			$fields_count = count( $cells );
			if ( $fields_count > $max_fields_per_row ) {
				$max_fields_per_row = $fields_count == 5 ? 2 : 3;
			}
			$row_size = 0;
			$cell_indexes = array_keys( $cells );
			$las_field_index = end( $cell_indexes );

			foreach( $cells as $ci => $k ) {
				if ( ( $row_size >= $max_fields_per_row && $las_field_index != $ci ) || $k_sum + $k > $row_capacity ) {
					$result[] = adventure_tours_hs_layout_expand_cells( $k_row, false, $row_capacity, $k_sum );
					$k_sum = $k;
					$row_size = 1;
					$k_row = array( $k );
				} else {
					$k_sum += $k;
					$row_size++;
					$k_row[] = $k;
				}
			}
			if ( $k_row ) {
				$result[] = adventure_tours_hs_layout_expand_cells( $k_row, true, $row_capacity, $k_sum );
			}
		} else {
			$result[] = adventure_tours_hs_layout_expand_cells( $cells, true, $row_capacity, $current_capacity );
		}
		return $result;
	}
}

if ( ! function_exists( 'adventure_tours_hs_layout_expand_cells' ) ) {
	/**
	 * Calculates field cell sizes in a singre row for tour search in horizontal mode.
	 */
	function adventure_tours_hs_layout_expand_cells( $cells, $is_last_row = true, $row_capacity = 12, $current_capacity = null ) {
		if ( null === $current_capacity ) {
			$current_capacity = array_sum( $cells );
		}

		if ( $current_capacity < $row_capacity ) {
			$free_slots = $row_capacity - $current_capacity;
			$resizble_fields = count( $cells ) - ( $is_last_row ? 1 : 0 );
			if ( $free_slots == $resizble_fields ) {
				for( $i=0; $i<$resizble_fields; $i++) {
					$cells[ $i ]++;
				}
			} else {
				$_c_fields = array();
				if ( $free_slots < $resizble_fields ) {
					$max_cell = max( $cells );
					for( $i=0; $i<$resizble_fields; $i++ ) {
						if ( $cells[ $i ] < $max_cell ) {
							$_c_fields[ $i ] = &$cells[ $i ];
						}
					}
				}

				if ( ! $_c_fields ) {
					$_c_fields = &$cells;
				} else {
					$resizble_fields = count( $_c_fields );
				}

				if ( $free_slots > $resizble_fields ) {
					$expand_count = $resizble_fields;
				} else {
					$expand_count = $free_slots;
				}
				$per_unit = ceil( $free_slots / $expand_count );
				$last_resizble_delta = $free_slots - $per_unit * ( $expand_count - 1 );

				$_c_fields_indexes = array_keys( $_c_fields );
				$last_index_key = end( $_c_fields );
				reset( $_c_fields );
				while( --$expand_count >= 0 ){
					$cells[ key( $_c_fields ) ] += $expand_count == 0 ? $last_resizble_delta : $per_unit;
					next( $_c_fields );
				}
			}
			return $cells;
		} else {
			return $cells;
		}
	}
}

if ( ! function_exists( 'adventure_tours_load_datepicker_assets' ) ) {
	/**
	 * Loads assets related to the datepicker GUI component.
	 *
	 * @return void
	 */
	function adventure_tours_load_datepicker_assets() {
		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_style( 'jquery-ui-datepicker-custom' );

		wp_localize_script( 'jquery-ui-datepicker', 'ThemeATDatepickerCfg', array(
			'firstDay' => get_option('start_of_week', 0),
			'monthNames' => array(
				_x( 'January', 'datepicker', 'adventure-tours' ),
				_x( 'February', 'datepicker', 'adventure-tours' ),
				_x( 'March', 'datepicker', 'adventure-tours' ),
				_x( 'April', 'datepicker', 'adventure-tours' ),
				_x( 'May', 'datepicker', 'adventure-tours' ),
				_x( 'June', 'datepicker', 'adventure-tours' ),
				_x( 'July', 'datepicker', 'adventure-tours' ),
				_x( 'August', 'datepicker', 'adventure-tours' ),
				_x( 'September', 'datepicker', 'adventure-tours' ),
				_x( 'October', 'datepicker', 'adventure-tours' ),
				_x( 'November', 'datepicker', 'adventure-tours' ),
				_x( 'December', 'datepicker', 'adventure-tours' ),
			),
			'monthNamesShort' => array(
				_x( 'Jan', 'datepicker', 'adventure-tours' ),
				_x( 'Feb', 'datepicker', 'adventure-tours' ),
				_x( 'Mar', 'datepicker', 'adventure-tours' ),
				_x( 'Apr', 'datepicker', 'adventure-tours' ),
				_x( 'May', 'datepicker', 'adventure-tours' ),
				_x( 'Jun', 'datepicker', 'adventure-tours' ),
				_x( 'Jul', 'datepicker', 'adventure-tours' ),
				_x( 'Aug', 'datepicker', 'adventure-tours' ),
				_x( 'Sep', 'datepicker', 'adventure-tours' ),
				_x( 'Oct', 'datepicker', 'adventure-tours' ),
				_x( 'Nov', 'datepicker', 'adventure-tours' ),
				_x( 'Dec', 'datepicker', 'adventure-tours' ),
			),
			'dayNames' => array(
				_x( 'Sunday', 'datepicker', 'adventure-tours' ),
				_x( 'Monday', 'datepicker', 'adventure-tours' ),
				_x( 'Tuesday', 'datepicker', 'adventure-tours' ),
				_x( 'Wednesday', 'datepicker', 'adventure-tours' ),
				_x( 'Thursday', 'datepicker', 'adventure-tours' ),
				_x( 'Friday', 'datepicker', 'adventure-tours' ),
				_x( 'Saturday', 'datepicker', 'adventure-tours' ),
			),
			'dayNamesShort' => array(
				_x( 'Sun', 'datepicker', 'adventure-tours' ),
				_x( 'Mon', 'datepicker', 'adventure-tours' ),
				_x( 'Tue', 'datepicker', 'adventure-tours' ),
				_x( 'Wed', 'datepicker', 'adventure-tours' ),
				_x( 'Thu', 'datepicker', 'adventure-tours' ),
				_x( 'Fri', 'datepicker', 'adventure-tours' ),
				_x( 'Sat', 'datepicker', 'adventure-tours' ),
			),
			'dayNamesMin' => array(
				_x( 'Su', 'datepicker', 'adventure-tours' ),
				_x( 'Mo', 'datepicker', 'adventure-tours' ),
				_x( 'Tu', 'datepicker', 'adventure-tours' ),
				_x( 'We', 'datepicker', 'adventure-tours' ),
				_x( 'Th', 'datepicker', 'adventure-tours' ),
				_x( 'Fr', 'datepicker', 'adventure-tours' ),
				_x( 'Sa', 'datepicker', 'adventure-tours' ),
			)
		) );
	}
}

if ( ! function_exists( 'adventure_tours_filter_faq_categories_sorting_field' ) ) {
	/**
	 * Filter function for get_terms arguments used in template-faq.php template to apply FAQ categories sorting
	 * settings depends on Theme Options section configuration.
	 *
	 * @param  assoc $args arguments for get_terms function
	 * @return assoc
	 */
	function adventure_tours_filter_faq_categories_sorting_field( $args ) {
		$option_value = adventure_tours_get_option( 'faq_categories_order_by_field' );

		if ( '' != $option_value && 'name' != $option_value ) {
			$order_parts = explode('|', $option_value);
			$args['orderby'] = $order_parts[0];
			if ( isset( $order_parts[1] ) ) {
				$args['order'] = $order_parts[1];
			}
		}

		return $args;
	}
	add_filter( 'adventure_tours_faq_categories_term_args', 'adventure_tours_filter_faq_categories_sorting_field' );
}
