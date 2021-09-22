<?php
/*
 * Name: Text
 * Section: content
 * Description: Free text block
 *
 */

/* @var $options array */

$default_options = array(
    'html'=>'<p style="text-align: left; margin: 0">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam vitae sodales nulla, nec blandit velit. Morbi feugiat imperdiet augue, vel mattis augue sagittis rutrum. Sed.</p>',
    'font_family'=>'',
    'font_size'=>'',
    'font_color'=>'',
    'block_padding_left'=>15,
    'block_padding_right'=>15,
    'block_padding_top' => 20,
    'block_padding_bottom' => 20,
    'block_background'=>''
);

$options = array_merge($default_options, $options);
/*
$options['html'] = str_replace('<p>', '<p style="">', $options['html']);
$style = 'font-family: ' . $options['font_family'] . ';font-size: ' . $options['font_size'] . 'px; color: <?php echo $options['font_color']?>;
$options['html'] = str_replace('<p', '<p inline-class="text-p"', $options['html']);
 */

$text_font_family = empty( $options['font_family'] ) ? $global_text_font_family : $options['font_family'];
$text_font_size   = empty( $options['font_size'] ) ? $global_text_font_size : $options['font_size'];
$text_font_color  = empty( $options['font_color'] ) ? $global_text_font_color : $options['font_color'];
$text_font_weight = empty( $options['font_weight'] ) ? $global_text_font_weight : $options['font_weight'];

 ?>
<style>
    .text {
        font-family: <?php echo $text_font_family ?>;
        font-size: <?php echo $text_font_size ?>px;
        font-weight: <?php echo $text_font_weight ?>;
        color: <?php echo $text_font_color ?>;
        line-height: 1.5;
    }
</style>
<table width="100%" style="width: 100%!important" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td width="100%" valign="top" align="left" inline-class="text">
            <?php echo $options['html'] ?>
        </td>
    </tr>
</table>

