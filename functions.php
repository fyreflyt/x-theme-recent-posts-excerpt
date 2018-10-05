// Displaying Excerpt in Recent Posts
// =============================================================================

function x_shortcode_recent_posts_v2( $atts ) {
  extract( shortcode_atts( array(
    'id'           => '',
    'class'        => '',
    'style'        => '',
    'type'         => 'post',
    'count'        => '',
    'category'     => '',
    'offset'       => '',
    'orientation'  => '',
    'show_excerpt' => 'true',
    'no_sticky'    => '',
    'no_image'     => '',
    'fade'         => ''
  ), $atts, 'x_recent_posts' ) );

  $allowed_post_types = apply_filters( 'cs_recent_posts_post_types', array( 'post' => 'post' ) );
  $type = ( isset( $allowed_post_types[$type] ) ) ? $allowed_post_types[$type] : 'post';

  $id            = ( $id           != ''     ) ? 'id="' . esc_attr( $id ) . '"' : '';
  $class         = ( $class        != ''     ) ? 'wec-recent-posts cf ' . esc_attr( $class ) : 'wec-recent-posts cf';
  $style         = ( $style        != ''     ) ? 'style="' . $style . '"' : '';
  $count         = ( $count        != ''     ) ? $count : 3;
  $category      = ( $category     != ''     ) ? $category : '';
  $category_type = ( $type         == 'post' ) ? 'category_name' : 'portfolio-category';
  $offset        = ( $offset       != ''     ) ? $offset : 0;
  $orientation   = ( $orientation  != ''     ) ? ' ' . $orientation : ' horizontal';
  $show_excerpt  = ( $show_excerpt == 'true' );
  $no_sticky     = ( $no_sticky    == 'true' );
  $no_image      = ( $no_image     == 'true' ) ? $no_image : '';
  $fade          = ( $fade         == 'true' ) ? $fade : 'false';

  $js_params = array(
    'fade' => ( $fade == 'true' )
  );

  $data = cs_generate_data_attributes( 'recent_posts', $js_params );

  $output = "<div {$id} class=\"{$class}{$orientation}\" {$style} {$data} data-fade=\"{$fade}\" >";

    $q = new WP_Query( array(
      'orderby'             => 'date',
      'post_type'           => "{$type}",
      'posts_per_page'      => "{$count}",
      'offset'              => "{$offset}",
      "{$category_type}"    => "{$category}",
      'ignore_sticky_posts' => $no_sticky
    ) );

    if ( $q->have_posts() ) : while ( $q->have_posts() ) : $q->the_post();

      if ( $no_image == 'true' ) {
        $image_output       = '';
        $image_output_class = 'no-image';
      } else {
        $image              = wp_get_attachment_image_src( get_post_thumbnail_id(), 'entry-cropped' );
        $bg_image           = ( $image[0] != '' ) ? $image[0] : 'https://source.unsplash.com/random/800x600';
        $image_output       = '<div class="x-recent-posts-img mbm"><img src="' . $bg_image . '"></div>';
        $image_output_class = 'with-image';
      }

      $excerpt = ( $show_excerpt ) ? '<div class="x-recent-posts-excerpt"><p>' . preg_replace('/<a.*?more-link.*?<\/a>/', '', cs_get_raw_excerpt() ) . '</p></div>' : '';

      // $output .= '<a class="x-recent-post' . $count . ' ' . $image_output_class . '" href="' . get_permalink( get_the_ID() ) . '" title="' . esc_attr( sprintf( csi18n('shortcodes.recent-posts-permalink'), the_title_attribute( 'echo=0' ) ) ) . '">'
      //            . '<article id="post-' . get_the_ID() . '" class="' . implode( ' ', get_post_class() ) . '">'
      //              . '<div class="entry-wrap">'
      //                . $image_output
      //                . '<div class="x-recent-posts-content">'
      //                  . '<h3 class="h-recent-posts">' . get_the_title() . '</h3>'
      //                  . '<span class="x-recent-posts-date">' . get_the_date() . '</span>'
      //                   . $excerpt
      //                . '</div>'
      //              . '</div>'
      //            . '</article>'
      //          . '</a>';
      $output .= '<article id="post-' . get_the_ID() . '" class="' . implode( ' ', get_post_class() ) . ' pvl">'
      // . '<div class="x-container">'
      . '<div class="x-column x-sm x-1-3">
          <a class="x-recent-post' . $count . ' ' . $image_output_class . '" href="' . get_permalink( get_the_ID() ) . '" title="' . get_the_title() . '">'
            .$image_output
          .'</a>'
          .'</div>'
         . '<div class="x-column x-sm x-2-3">'
         . '<div class="entry-wrap">'
              //. $image_output
              . '<div class="x-recent-posts-content">'
                . '<h2 class="mtn"><a href="' . get_permalink( get_the_ID() ) . '" title="' . get_the_title() . '">' . get_the_title() . '</a></h2>'
                . '<span class="x-recent-posts-date mbm">' . get_the_date() . '</span>'
                 . $excerpt
                 .'<a class="e9-35 x-anchor x-anchor-button" tabindex="0" href="' . get_permalink( get_the_ID() ) . '" title="' . get_the_title() . '">
                   <span class="x-anchor-content">
                         <span class="x-anchor-text"><span class="x-anchor-text-primary">Read More</span></span>
                 <span class="x-particle x-anchor-particle-primary" data-x-particle="scale-x_y overlap-c_l" aria-hidden="true">
                   <span style="transform: rotate(30deg);"></span>
                 </span>  </span>
                 </a>'
              . '</div>'
            // . '</div>'
         .'</div></div>'
         .'</article>';

    endwhile; endif; wp_reset_postdata();

  $output .= '</div>';

  return $output;

}

add_filter('wp_head', 'custom_recent_posts');

function custom_recent_posts() {
  remove_shortcode( 'x_recent_posts' );
  remove_shortcode( 'recent_posts' );
  add_shortcode( 'x_recent_posts', 'x_shortcode_recent_posts_v2' );
  add_shortcode( 'recent_posts', 'x_shortcode_recent_posts_v2' );
}
