<?php
/**
 * Plugin Name: Lookalike
 * Plugin URI: https://github.com/hrock/lookalike
 * Description: Simple author image plugin for wordpress
 * Version: 0.1.1
 * Author: Daniel Ma
 * Author URI: https://github.com/danielma
 * License: MIT
 */

namespace Lookalike;

// this won't do anything without ACF being installed
if(!function_exists("register_field_group")) {
  add_action('admin_notices', function() {
?>
  <div class='error'>Lookalike cannot run without the <a href='http://www.advancedcustomfields.com/'>Advanced Custom Fields</a> plugin. Please install and activate it before trying to use this plugin</div>
<?php
  });

  return;
}

add_filter('get_avatar' , __NAMESPACE__ . '\\my_custom_avatar' , 1 , 5);

function my_custom_avatar($avatar, $id_or_email, $size, $default, $alt) {
  $user = false;

  if (is_numeric($id_or_email)) {
    $id = (int) $id_or_email;
    $user = get_user_by('id' , $id);
  } elseif (is_object($id_or_email)) {
    if (! empty($id_or_email->user_id)) {
      $id = (int) $id_or_email->user_id;
      $user = get_user_by('id' , $id);
    }
  } else {
    $user = get_user_by('email', $id_or_email);
  }

  if ($user && is_object($user)) {
    $avatar_obj = get_field('author_image', $user);
    if (!empty($avatar_obj)) {
      $size_name = 'thumbnail';
      $thumbnail = $avatar_obj['sizes'][$size_name];
      $avatar = "<img alt='{$alt}' src='{$thumbnail}' class='avatar avatar-{$size} photo' height='{$size}' width='{$size}' />";
    }
  }

  return $avatar;
}

register_field_group(array (
  'id' => 'acf_author-image',
  'title' => 'Author Image',
  'fields' => array (
    array (
      'key' => 'field_553eb5b46e59f',
      'label' => 'Author Image',
      'name' => 'author_image',
      'type' => 'image',
      'save_format' => 'object',
      'preview_size' => 'thumbnail',
      'library' => 'all',
    ),
  ),
  'location' => array (
    array (
      array (
        'param' => 'ef_user',
        'operator' => '==',
        'value' => 'all',
        'order_no' => 0,
        'group_no' => 0,
      ),
    ),
  ),
  'options' => array (
    'position' => 'normal',
    'layout' => 'no_box',
    'hide_on_screen' => array (
    ),
  ),
  'menu_order' => 0,
));
