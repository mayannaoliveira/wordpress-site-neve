<?php

defined('ABSPATH') || exit;

function tnp_post_thumbnail_src($post, $size = 'thumbnail', $alternative = '') {
    if (is_object($post)) {
        $post = $post->ID;
    }

    // Find a media id to be used as featured image
    $media_id = get_post_thumbnail_id($post);
    if (empty($media_id)) {
        $attachments = get_children(array('numberpost' => 1, 'post_parent' => $post, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'menu_order'));
        if (!empty($attachments)) {
            foreach ($attachments as $id => &$attachment) {
                $media_id = $id;
                break;
            }
        }
    }

    if (!$media_id) {
        return $alternative;
    }

    if (!defined('NEWSLETTER_MEDIA_RESIZE') || NEWSLETTER_MEDIA_RESIZE) {
        if (is_array($size)) {
            $src = tnp_media_resize($media_id, $size);
            if (is_wp_error($src)) {
                Newsletter::instance()->logger->error($src);
                return $alternative;
            } else {
                return $src;
            }
        }
    }

    $media = wp_get_attachment_image_src($media_id, $size);
    if (strpos($media[0], 'http') !== 0) {
        $media[0] = 'http:' . $media[0];
    }
    return $media[0];
}

/**
 * @param WP_Post $post
 * @param int $length
 *
 * @return string
 */
function tnp_post_excerpt($post, $length = 30) {
    $excerpt = tnp_delete_all_shordcodes_tags(get_the_excerpt($post->ID));
    $excerpt = wp_trim_words($excerpt, $length);

    return $excerpt;
}

function tnp_delete_all_shordcodes_tags($post_content = '') {
    //Delete open tags
    $post_content = preg_replace("/\[[a-zA-Z0-9_-]*?(\s.*?)?\]/", '', $post_content);
    //Delete close tags
    $post_content = preg_replace("/\[\/[a-zA-Z0-9_-]*?\]/", '', $post_content);

    return $post_content;
}

function tnp_post_permalink($post) {
    return get_permalink($post->ID);
}

function tnp_post_content($post) {
    return $post->post_content;
}

function tnp_post_title($post) {
    return $post->post_title;
}

function tnp_post_date($post, $format = null) {
    if (empty($format)) {
        $format = get_option('date_format');
    }
    return mysql2date($format, $post->post_date);
}

/**
 * Tries to create a resized version of a media uploaded to the media library.
 * Returns an empty string if the media does not exists or generally if the attached file
 * cannot be found. If the resize fails for whatever reason, fall backs to the
 * standard image source returned by WP which is usually not exactly the
 * requested size.
 *
 * @param int $media_id
 * @param array $size
 * @return string
 */
function tnp_media_resize($media_id, $size) {
    if (empty($media_id)) {
        return '';
    }

    $relative_file = get_post_meta($media_id, '_wp_attached_file', true);
    if (empty($relative_file)) {
        return '';
    }

    $width = $size[0];
    $height = $size[1];
    $crop = false;
    if (isset($size[2])) {
        $crop = (boolean) $size[2];
    }

    $uploads = wp_upload_dir();

    // Based on _wp_relative_upload_path() function for blog which store the
    // full patch of media files
    if (0 === strpos($relative_file, $uploads['basedir'])) {
        $relative_file = str_replace($uploads['basedir'], '', $relative_file);
        $relative_file = ltrim($relative_file, '/');
    }

    $absolute_file = $uploads['basedir'] . '/' . $relative_file;
    // Relative and absolute name of the thumbnail.
    $pathinfo = pathinfo($relative_file);
    $relative_thumb = $pathinfo['dirname'] . '/' . $pathinfo['filename'] . '-' . $width . 'x' .
            $height . ($crop ? '-c' : '') . '.' . $pathinfo['extension'];
    $absolute_thumb = $uploads['basedir'] . '/newsletter/thumbnails/' . $relative_thumb;

    // Thumbnail generation if needed.
    if (!file_exists($absolute_thumb) || filemtime($absolute_thumb) < filemtime($absolute_file)) {
        $r = wp_mkdir_p($uploads['basedir'] . '/newsletter/thumbnails/' . $pathinfo['dirname']);

        if (!$r) {
            $src = wp_get_attachment_image_src($media_id, 'full');
            return $src[0];
        }

        $editor = wp_get_image_editor($absolute_file);
        if (is_wp_error($editor)) {
            $src = wp_get_attachment_image_src($media_id, 'full');
            return $src[0];
            //return $editor;
            //return $uploads['baseurl'] . '/' . $relative_file;
        }

        $original_size = $editor->get_size();
        if ($width > $original_size['width'] || $height > $original_size['height']) {
            $src = wp_get_attachment_image_src($media_id, 'full');
            return $src[0];
        }

        $editor->set_quality(80);
        $resized = $editor->resize($width, $height, $crop);

        if (is_wp_error($resized)) {
            $src = wp_get_attachment_image_src($media_id, 'full');
            return $src[0];
        }

        $saved = $editor->save($absolute_thumb);
        if (is_wp_error($saved)) {
            $src = wp_get_attachment_image_src($media_id, 'full');
            return $src[0];
            //return $saved;
            //return $uploads['baseurl'] . '/' . $relative_file;
        }
    }

    return $uploads['baseurl'] . '/newsletter/thumbnails/' . $relative_thumb;
}

function _tnp_get_default_media($media_id, $size) {

    $src = wp_get_attachment_image_src($media_id, $size);
    if (!$src) {
        return null;
    }
    $media = new TNP_Media();
    $media->id = $media_id;
    $media->url = $src[0];
    $media->width = $src[1];
    $media->height = $src[2];
    return $media;
}

function tnp_get_media($media_id, $size) {
    $src = wp_get_attachment_image_src($media_id, $size);
    if (!$src) {
        return null;
    }
    $media = new TNP_Media();
    $media->id = $media_id;
    $media->url = $src[0];
    $media->width = $src[1];
    $media->height = $src[2];
    return $media;
}

/**
 * Create a resized version of the media stored in the WP media library.
 *
 * @param int $media_id
 * @param array $size
 * @return TNP_Media
 */
function tnp_resize($media_id, $size) {
    if (empty($media_id)) {
        return null;
    }

    $relative_file = get_post_meta($media_id, '_wp_attached_file', true);

    if (empty($relative_file)) {
        return null;
    }

    $uploads = wp_upload_dir();

    // Based on _wp_relative_upload_path() function for blog which store the
    // full patch of media files
    if (0 === strpos($relative_file, $uploads['basedir'])) {
        $relative_file = str_replace($uploads['basedir'], '', $relative_file);
        $relative_file = ltrim($relative_file, '/');
    }

    $width = $size[0];
    $height = $size[1];
    $crop = false;
    if (isset($size[2])) {
        $crop = (boolean) $size[2];
    }

    $absolute_file = $uploads['basedir'] . '/' . $relative_file;

    if (substr($relative_file, -4) === '.gif') {
        $editor = wp_get_image_editor($absolute_file);
        if (is_wp_error($editor)) {
            return _tnp_get_default_media($media_id, $size);
        }
        $new_size = $editor->get_size();
        $media = new TNP_Media();
        $media->id = $media_id;
        $media->width = $new_size['width'];
        $media->height = $new_size['height'];
        if ($media->width > $width) {
            $media->set_width($width);
        }
        $media->url = $uploads['baseurl'] . '/' . $relative_file;
        return $media;
    }

    // Relative and absolute name of the thumbnail.
    $pathinfo = pathinfo($relative_file);
    $relative_thumb = $pathinfo['dirname'] . '/' . $pathinfo['filename'] . '-' . $width . 'x' . $height . ($crop ? '-c' : '') . '.' . $pathinfo['extension'];
    $absolute_thumb = $uploads['basedir'] . '/newsletter/thumbnails/' . $relative_thumb;

    // Thumbnail generation if needed.
    if (!file_exists($absolute_thumb) || filemtime($absolute_thumb) < filemtime($absolute_file)) {
        $r = wp_mkdir_p($uploads['basedir'] . '/newsletter/thumbnails/' . $pathinfo['dirname']);

        if (!$r) {
            Newsletter::instance()->logger->error('Unable to create dir ' . $uploads['basedir'] . '/newsletter/thumbnails/' . $pathinfo['dirname']);
            return _tnp_get_default_media($media_id, $size);
        }

        $editor = wp_get_image_editor($absolute_file);
        if (is_wp_error($editor)) {
            Newsletter::instance()->logger->error($editor);
            Newsletter::instance()->logger->error('File: ' . $absolute_file);
            return _tnp_get_default_media($media_id, $size);
        }

        $original_size = $editor->get_size();
        if ($width > $original_size['width'] && $height > $original_size['height']) {
            Newsletter::instance()->logger->error('Requested size larger than the original one');
            return _tnp_get_default_media($media_id, $size);
        }

        $editor->set_quality(85);
        $resized = $editor->resize($width, $height, $crop);

        if (is_wp_error($resized)) {
            Newsletter::instance()->logger->error($resized);
            Newsletter::instance()->logger->error('File: ' . $absolute_file);
            return _tnp_get_default_media($media_id, $size);
        }

        $saved = $editor->save($absolute_thumb);
        if (is_wp_error($saved)) {
            Newsletter::instance()->logger->error($saved);
            return _tnp_get_default_media($media_id, $size);
        }
        $new_size = $editor->get_size();

        $media = new TNP_Media();
        $media->width = $new_size['width'];
        $media->height = $new_size['height'];
        $media->url = $uploads['baseurl'] . '/newsletter/thumbnails/' . $relative_thumb;
    } else {
        $media = new TNP_Media();
        $new_size = getimagesize($absolute_thumb);
        $media->width = $new_size[0];
        $media->height = $new_size[1];
        $media->url = $uploads['baseurl'] . '/newsletter/thumbnails/' . $relative_thumb;
    }

    return $media;
}

function tnp_resize_2x($media_id, $size) {
	$size[0] = $size[0] * 2;
	$size[1] = $size[1] * 2;
	$media = tnp_resize($media_id, $size);
        if (!$media) return $media;
	$media->set_width( $size[0] / 2 );
	return $media;
}

/**
 * Get media for "posts" composer block
 *
 * @param WP_Post post
 * @param array $size
 * @param string $default_image_url
 *
 * @return TNP_Media
 */
function tnp_composer_block_posts_get_media($post, $size, $default_image_url = null) {
    $post_thumbnail_id = TNP_Composer::get_post_thumbnail_id($post);

    $media = null;

    if (!empty($post_thumbnail_id)) {
        $media = tnp_resize($post_thumbnail_id, array_values($size));
    } else if ($default_image_url) {
        Newsletter::instance()->logger->error('Thumbnail id not found');
        $media = new TNP_Media();
        $media->url = $default_image_url;
        $media->width = $size['width'];
        $media->height = $size['height'];
    }
    return $media;
}

function tnp_outlook_wrapper_open($width = 600) {
    return NewsletterEmails::get_outlook_wrapper_open($width);
}

function tnp_outlook_wrapper_close() {
    return NewsletterEmails::get_outlook_wrapper_close();
}
