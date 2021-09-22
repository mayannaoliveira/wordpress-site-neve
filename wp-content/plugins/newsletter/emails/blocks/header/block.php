<?php

/*
 * Name: Header
 * Section: header
 * Description: Default header with company info
 */

$default_options = array(
    'font_family' => '',
    'font_size' => '',
    'font_color' => '',
    'font_weight' => '',
    'logo_height' => 100,
    'block_padding_top' => 15,
    'block_padding_bottom' => 15,
    'block_padding_left' => 15,
    'block_padding_right' => 15,
    'block_background' => '',
    'layout' => ''
);
$options = array_merge($default_options, $options);

$text_font_family = empty($options['font_family']) ? $global_text_font_family : $options['font_family'];
$text_font_size = empty($options['font_size']) ? $global_text_font_size : $options['font_size'];
$text_font_color = empty($options['font_color']) ? $global_text_font_color : $options['font_color'];
$text_font_weight = empty($options['font_weight']) ? $global_text_font_weight : $options['font_weight'];

if (empty($info['header_logo']['id'])) {
    $media = false;
} else {
    $media = tnp_get_media($info['header_logo']['id'], 'large');
    if ($media) {
        $media->alt = $info['header_title'];
        $media->link = home_url();
        $media->set_height($options['logo_height']);
    }
}

$empty = !$media && empty($info['header_sub']) && empty($info['header_title']);

if ($empty) {
    echo '<p>Please, set your company info.</p>';
} elseif ($options['layout'] === 'logo') {
    include __DIR__ . '/layout-logo.php';
    return;
} elseif ($options['layout'] === 'titlemotto') {
    include __DIR__ . '/layout-titlemotto.php';
    return;
} else {
    include __DIR__ . '/layout-default.php';
    return;
}
?>

