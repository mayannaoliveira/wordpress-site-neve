<?php
$size = array('width' => 600, 'height' => 400, "crop" => true);
$total_width = 600 - $options['block_padding_left'] - $options['block_padding_right'];
$column_width = $total_width / 2 - 20;

$title_style = TNP_Composer::get_style($options, 'title', $composer, 'title', ['scale' => .8]);
$text_style = TNP_Composer::get_style($options, '', $composer, 'text');
?>
<style>
    .title {
        font-family: <?php echo $title_style->font_family ?>;
        font-size: <?php echo $title_style->font_size ?>px;
        font-weight: <?php echo $title_style->font_weight ?>;
        color: <?php echo $title_style->font_color ?>;
        line-height: 1.3em;
        padding: 15px 0 0 0;
    }

    .excerpt {
        font-family: <?php echo $text_style->font_family ?>;
        font-size: <?php echo $text_style->font_size ?>px;
        font-weight: <?php echo $text_style->font_weight ?>;
        color: <?php echo $text_style->font_color ?>;
        line-height: 1.4em;
        padding: 5px 0 0 0;
    }

    .meta {
        font-family: <?php echo $text_style->font_family ?>;
        color: <?php echo $text_style->font_color ?>;
        font-size: <?php echo round($text_style->font_size * 0.9) ?>px;
        font-weight: <?php echo $text_style->font_weight ?>;
        padding: 10px 0 0 0;
        font-style: italic;
        line-height: normal !important;
    }
    .button {
        padding: 15px 0;
    }
    .column-left {
        padding-right: 10px; 
        padding-bottom: 20px;
    }
    .column-right {
        padding-left: 10px; 
        padding-bottom: 20px;
    }

</style>

<table cellspacing="0" cellpadding="0" border="0" width="100%">

    <?php foreach (array_chunk($posts, 2) AS $row) { ?>
        <?php
        $media = null;
        if ($show_image) {
            $media = tnp_composer_block_posts_get_media($row[0], $size, $image_placeholder_url);
            $media->link = tnp_post_permalink($row[0]);
            $media->set_width($column_width);
        }

        $meta = [];

        if ($show_date) {
            $meta[] = tnp_post_date($row[0]);
        }

        if ($show_author) {
            $author_object = get_user_by('id', $row[0]->post_author);
            if ($author_object) {
                $meta[] = $author_object->display_name;
            }
        }

        $button_options['button_url'] = tnp_post_permalink($row[0]);
        ?>
        <tr>
            <td inline-class="column-left" width="50%" valign="top" class="responsive">


                <table cellpadding="0" cellspacing="0" border="0" width="100%">
                    <?php if ($media) { ?>
                        <tr>
                            <td align="center" valign="middle">
                                <?php echo TNP_Composer::image($media, ['class' => 'fluid']) ?>
                            </td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td align="center" inline-class="title" class="title tnpc-row-edit tnpc-inline-editable" data-type="title" data-id="<?php echo $row[0]->ID ?>">
                            <?php
                            echo TNP_Composer::is_post_field_edited_inline($options['inline_edits'], 'title', $row[0]->ID) ?
                                    TNP_Composer::get_edited_inline_post_field($options['inline_edits'], 'title', $row[0]->ID) :
                                    tnp_post_title($row[0])
                            ?>
                        </td>
                    </tr>
                    <?php if ($meta) { ?>
                        <tr>
                            <td inline-class="meta" class="meta">
                                <?php echo esc_html(implode(' - ', $meta)) ?>
                            </td>
                        </tr>
                    <?php } ?>


                    <?php if ($excerpt_length) { ?>
                        <tr>
                            <td align="center" inline-class="excerpt" class="title tnpc-row-edit tnpc-inline-editable" data-type="text" data-id="<?php echo $row[0]->ID ?>">
                                <?php
                                echo TNP_Composer::is_post_field_edited_inline($options['inline_edits'], 'text', $row[0]->ID) ?
                                        TNP_Composer::get_edited_inline_post_field($options['inline_edits'], 'text', $row[0]->ID) :
                                        tnp_post_excerpt($row[0], $excerpt_length)
                                ?>
                            </td>
                        </tr>
                    <?php } ?>

                    <?php if ($show_read_more_button) { ?>
                        <tr>
                            <td align="center" inline-class="button">
                                <?php echo TNP_Composer::button($button_options) ?>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            </td>

            <td inline-class="column-right" width="50%" valign="top" class="responsive">
                <?php
                if (isset($row[1])) {

                    $media = null;
                    if ($show_image) {
                        $media = tnp_composer_block_posts_get_media($row[1], $size, $image_placeholder_url);
                        $media->link = tnp_post_permalink($row[1]);
                        $media->set_width($column_width);
                    }

                    $meta = [];

                    if ($show_date) {
                        $meta[] = tnp_post_date($row[1]);
                    }

                    if ($show_author) {
                        $author_object = get_user_by('id', $row[1]->post_author);
                        if ($author_object) {
                            $meta[] = $author_object->display_name;
                        }
                    }

                    $button_options['button_url'] = tnp_post_permalink($row[1]);
                    ?>


                    <table cellpadding="0" cellspacing="0" border="0" width="100%">
                        <?php if ($media) { ?>
                            <tr>
                                <td align="center" valign="middle">
                                    <?php echo TNP_Composer::image($media, ['class' => 'fluid']) ?>
                                </td>
                            </tr>
                        <?php } ?>
                        <tr>
                            <td align="center" inline-class="title" class="tnpc-row-edit tnpc-inline-editable" data-type="title" data-id="<?php echo $row[1]->ID ?>">
                                <?php
                                echo TNP_Composer::is_post_field_edited_inline($options['inline_edits'], 'title', $row[1]->ID) ?
                                        TNP_Composer::get_edited_inline_post_field($options['inline_edits'], 'title', $row[1]->ID) :
                                        tnp_post_title($row[1])
                                ?>
                            </td>
                        </tr>
                        <?php if ($meta) { ?>
                            <tr>
                                <td inline-class="meta">
                                    <?php echo esc_html(implode(' - ', $meta)) ?>
                                </td>
                            </tr>
                        <?php } ?>

                        <tr>
                            <td align="center" inline-class="excerpt" class="tnpc-row-edit tnpc-inline-editable" data-type="text" data-id="<?php echo $row[1]->ID ?>">
                                <?php
                                echo TNP_Composer::is_post_field_edited_inline($options['inline_edits'], 'text', $row[1]->ID) ?
                                        TNP_Composer::get_edited_inline_post_field($options['inline_edits'], 'text', $row[1]->ID) :
                                        tnp_post_excerpt($row[1], $excerpt_length)
                                ?>
                            </td>
                        </tr>
                        <?php if ($show_read_more_button) { ?>
                            <tr>
                                <td align="center" inline-class="button">
                                    <?php echo TNP_Composer::button($button_options) ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </table>
                <?php } ?>


            </td>
        </tr>

    <?php } ?>

</table>

