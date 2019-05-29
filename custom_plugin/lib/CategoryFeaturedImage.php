<?php /**
 * Plugin class
 **/
if (!class_exists('CategoryFeaturedImage')) {

    class CategoryFeaturedImage
    {

        public function __construct()
        {

        }

        /*
         * Initialize the class and start calling our hooks and filters
        */
        public function init()
        {
            add_action('category_add_form_fields', array($this, 'add_category_image'), 10, 2);
            add_action('created_category', array($this, 'save_category_image'), 10, 2);
            add_action('created_category', array($this, 'save_category_video'), 10, 2);
            add_action('category_edit_form_fields', array($this, 'edit_form_field'), 10, 2);
            add_action('edited_category', array($this, 'updated_category_image'), 10, 2);
            add_action('edited_category', array($this, 'updated_category_video'), 10, 2);
            add_action('admin_enqueue_scripts', array($this, 'load_media'));
            add_action('admin_footer', array($this, 'add_script'));
            add_filter('manage_edit-category_columns', array($this, 'add_term_columns'));
            add_filter('manage_category_custom_column', array($this, 'add_term_custom_column'), 10, 3);
            add_filter('get_the_archive_title', array($this, 'display_term_meta'), 10, 2);
        }

        public function load_media()
        {
            wp_enqueue_media();
        }

        /*
         * Display category custom field on the front-end
        */
        public function display_term_meta($title)
        {
            if (is_category()) {
                $title = single_cat_title('', false);
            }

            echo '<h2>' . $title . '</h2>';

            $cat = get_queried_object();
            $image_id = get_term_meta($cat->term_id, 'category-image-id', true);
            $video_id = get_term_meta($cat->term_id, 'category-video-id', true);

            $video_frame = preg_replace(
                "/\s*[a-zA-Z\/\/:\.]*youtu(be.com\/watch\?v=|.be\/)([a-zA-Z0-9\-_]+)([a-zA-Z0-9\/\*\-\_\?\&\;\%\=\.]*)/i",
                "<iframe width=\"100%\" height=\"450\" src=\"//www.youtube.com/embed/$2\" allowfullscreen></iframe>",
                $video_id
            );
            ?>
            <div class="category-thumbnail">
                <?php if ($image_id) { ?>
                    <?php echo wp_get_attachment_image($image_id, 'full'); ?>
                <?php } ?>
            </div>
            <div class="category-video">
                <?php if ($video_id) { ?>
                    <?php echo $video_frame; ?>
                <?php } ?>
            </div>
            <?php
        }

        /*
         * Add a form field in the new category page
        */
        public function add_category_image($taxonomy)
        { ?>
            <div class="form-field term-group">
                <label for="category-image-id"><?php _e('Featured image', 'hero-theme'); ?></label>
                <input type="hidden" id="category-image-id" name="category-image-id" class="custom_media_url" value="">
                <div id="category-image-wrapper"></div>
                <p>
                    <input type="button" class="button button-secondary ct_tax_media_button" id="ct_tax_media_button"
                           name="ct_tax_media_button" value="<?php _e('Add Image', 'hero-theme'); ?>"/>
                    <input type="button" class="button button-secondary ct_tax_media_remove" id="ct_tax_media_remove"
                           name="ct_tax_media_remove" value="<?php _e('Remove Image', 'hero-theme'); ?>"/>
                </p>
                <span class="description"><?php _e('Select featured image for your category'); ?></span>
            </div>
            <div class="form-field term-video-group">
                <label for="category-video-id"><?php _e('Featured video', 'hero-theme'); ?></label>
                <input type="hidden" id="category-video-id" name="category-video-id" class="custom_media_url" value="">
                <div id="category-video-wrapper"></div>
                <input type="text" name="category-video-link" id="category-video-link" size="3" style="width:60%;"
                       value=""><br/>
                <p>
                    <input type="button" class="button button-secondary video_media_button" id="video_media_button"
                           name="video_media_button" value="<?php _e('Add Link', 'hero-theme'); ?>"/>
                    <input type="button" class="button button-secondary video_media_remove" id="video_media_remove"
                           name="video_media_remove" value="<?php _e('Remove Link', 'hero-theme'); ?>"/>
                </p>
                <span class="description"><?php _e('Youtube/Vimeo link for category: use full url with '); ?></span>
            </div>
            <?php
        }

        /*
         * Save the form field
        */
        public function save_category_image($term_id, $tt_id)
        {
            if (isset($_POST['category-image-id']) && '' !== $_POST['category-image-id']) {
                $image = $_POST['category-image-id'];
                add_term_meta($term_id, 'category-image-id', $image, true);
            }
        }

        public function save_category_video($term_id, $tt_id)
        {
            if (isset($_POST['category-video-id']) && '' !== $_POST['category-video-id']) {
                $video = $_POST['category-video-id'];
                add_term_meta($term_id, 'category-video-id', $video, true);
            }
        }

        /*
         * Edit the form field
        */
        public function edit_form_field($term, $taxonomy)
        { ?>
            <tr class="form-field term-group-wrap">
                <th scope="row">
                    <label for="category-image-id"><?php _e('Featured image', 'hero-theme'); ?></label>
                </th>
                <td>
                    <?php $image_id = get_term_meta($term->term_id, 'category-image-id', true); ?>
                    <input type="hidden" id="category-image-id" name="category-image-id"
                           value="<?php echo $image_id; ?>">
                    <div id="category-image-wrapper">
                        <?php if ($image_id) { ?>
                            <?php echo wp_get_attachment_image($image_id, 'medium'); ?>
                        <?php } ?>
                    </div>
                    <p>
                        <input type="button" class="button button-secondary ct_tax_media_button"
                               id="ct_tax_media_button" name="ct_tax_media_button"
                               value="<?php _e('Add Image', 'hero-theme'); ?>"/>
                        <input type="button" class="button button-secondary ct_tax_media_remove"
                               id="ct_tax_media_remove" name="ct_tax_media_remove"
                               value="<?php _e('Remove Image', 'hero-theme'); ?>"/>
                    </p>
                </td>
            </tr>
            <tr class="form-field term-video-group">
                <th scope="row">
                    <label for="category-video-id"><?php _e('Featured video link', 'hero-theme'); ?></label>
                </th>
                <td>
                    <?php $video_id = get_term_meta($term->term_id, 'category-video-id', true); ?>
                    <input type="hidden" id="category-video-id" name="category-video-id"
                           value="<?php echo $video_id; ?>">
                    <?php
                    //Vimeo/Youtube thumbnail
                    /**
                     * Retrieves the thumbnail from a youtube or vimeo video
                     *
                     * @param - $src: the url of the "player"
                     *
                     * @return - string
                     *
                     **/
                    $url_pieces = $video_id;
                    $url_pieces = explode('/', $url_pieces);
                    if ($url_pieces[2] == 'vimeo.com') { // If Vimeo
                        $id = $url_pieces[3];
                        $id = explode('?', $id);
                        $hash = unserialize(file_get_contents('http://vimeo.com/api/v2/video/' . $id[0] . '.php'));
                        $thumbnail = $hash[0]['thumbnail_medium'];
                    } elseif ($url_pieces[2] == 'www.youtube.com') { // If Youtube
                        $extract_id = explode('?v=', $url_pieces[3]);
                        $id = $extract_id[1];
                        $thumbnail = 'http://img.youtube.com/vi/' . $id . '/mqdefault.jpg';
                    }
                    ?>
                    <div id="category-video-wrapper">
                        <img class="custom_media_video" src="<?php echo $thumbnail; ?>" alt="">
                    </div>
                    <input type="text" class="cat-video-link" name="category-video-link" id="category-video-link"
                           size="3" style="width:60%;"
                           value=""><br/>
                    <span class="description"><?php _e('Youtube/Vimeo link for category: use full url with '); ?></span>
                    <p>
                        <input type="button" class="button button-secondary video_media_button video_remove"
                               id="video_media_button"
                               name="video_media_button" value="<?php _e('Add Link', 'hero-theme'); ?>"/>
                        <input type="button" class="button button-secondary video_media_remove" id="video_media_remove"
                               name="video_media_remove" value="<?php _e('Remove Link', 'hero-theme'); ?>"/>
                    </p>
                </td>
            </tr>
            <?php
        }

        /*
         * Update the form field value
         */
        public function updated_category_image($term_id, $tt_id)
        {
            if (isset($_POST['category-image-id']) && '' !== $_POST['category-image-id']) {
                $image = $_POST['category-image-id'];
                update_term_meta($term_id, 'category-image-id', $image);
            } else {
                update_term_meta($term_id, 'category-image-id', '');
            }
        }

        public function updated_category_video($term_id, $tt_id)
        {
            if (isset($_POST['category-video-id']) && '' !== $_POST['category-video-id']) {
                $video = $_POST['category-video-id'];
                update_term_meta($term_id, 'category-video-id', $video);
            } else {
                update_term_meta($term_id, 'category-video-id', '');
            }
        }

        /*
         * Custom column
         */
        public function add_term_columns($columns)
        {

            return array_merge(
                array_slice($columns, 0, 2),
                array(
                    'image' => __('Featured Image'),
                ),
                array_slice($columns, 2)
            );

        }

        /*
         * Custom column content
         */
        public function add_term_custom_column($content, $column_name, $term_id)
        {

            if ('image' === $column_name) {
                $featured_image_id = get_term_meta($term_id, 'category-image-id', true);
                $content = wp_get_attachment_image($featured_image_id, array(64, 64));
            }

            return $content;

        }

        /*
         * Add script
         */
        public function add_script()
        { ?>
            <script>
                jQuery(document).ready(function ($) {
                    function ct_media_upload(button_class) {
                        var _custom_media = true,
                            _orig_send_attachment = wp.media.editor.send.attachment;
                        $('body').on('click', button_class, function (e) {
                            var button_id = '#' + $(this).attr('id');
                            var send_attachment_bkp = wp.media.editor.send.attachment;
                            var button = $(button_id);
                            _custom_media = true;
                            wp.media.editor.send.attachment = function (props, attachment) {
                                if (_custom_media) {
                                    $('#category-image-id').val(attachment.id);
                                    $('#category-image-wrapper').html('<img class="custom_media_image" src="" style="margin:0;padding:0;max-height:100px;float:none;" />');
                                    $('#category-image-wrapper .custom_media_image').attr('src', attachment.url).css('display', 'block');
                                } else {
                                    return _orig_send_attachment.apply(button_id, [props, attachment]);
                                }
                            }
                            wp.media.editor.open(button);
                            return false;
                        });
                    }

                    ct_media_upload('.ct_tax_media_button.button');
                    $('body').on('click', '.ct_tax_media_remove', function () {
                        $('#category-image-id').val('');
                        $('#category-image-wrapper').html('<img class="custom_media_image" src="" style="margin:0;padding:0;max-height:100px;float:none;" />');
                    });
                    $(document).ajaxComplete(function (event, xhr, settings) {
                        var queryStringArr = settings.data.split('&');
                        if ($.inArray('action=add-tag', queryStringArr) !== -1) {
                            var xml = xhr.responseXML;
                            $response = $(xml).find('term_id').text();
                            if ($response != "") {
                                $('#category-image-wrapper').html('');
                                $('#category-video-wrapper').html('');
                            }
                        }
                    });

                    $('#category-video-link').keyup(function () {
                        video_val = $('#category-video-link').val();
                        $('#category-video-id').val(video_val);
                    });


                    $('.video_media_button').on('click', function () {
                        var video_url = $('#category-video-id').val();

                        function get_video_thumb(url, callback) {
                            var id = get_video_id(url);
                            if (id['type'] == 'y') {
                                return processYouTube(id);
                            } else if (id['type'] == 'v') {

                                $.ajax({
                                    url: 'http://vimeo.com/api/v2/video/' + id['id'] + '.json',
                                    dataType: 'jsonp',
                                    success: function (data) {
                                        callback({type: 'v', id: id['id'], url: data[0].thumbnail_large});
                                    }
                                });
                            }

                            function processYouTube(id) {
                                if (!id) {
                                    throw new Error('Unsupported YouTube URL');
                                }

                                callback({
                                    type: 'y',
                                    id: id['id'],
                                    url: 'http://i2.ytimg.com/vi/' + id['id'] + '/hqdefault.jpg'
                                });
                            }
                        }

                        function get_video_id(url) {
                            var id;
                            var a;
                            if (url.indexOf('youtube.com') > -1) {
                                if (url.indexOf('v=') > -1) {
                                    id = url.split('v=')[1].split('&')[0];
                                } else if (url.indexOf('embed') > -1) {
                                    id = url.split('embed/')[1].split('?')[0];
                                }
                                ;
                                return processYouTube(id);
                            } else if (url.indexOf('youtu.be') > -1) {
                                id = url.split('/')[1];
                                return processYouTube(id);
                            } else if (url.indexOf('vimeo.com') > -1) {
                                if (url.match(/https?:\/\/(www\.)?vimeo.com\/(\d+)($|\/)/)) {
                                    id = url.split('/')[3];
                                } else if (url.match(/^vimeo.com\/channels\/[\d\w]+#[0-9]+/)) {
                                    id = url.split('#')[1];
                                } else if (url.match(/vimeo.com\/groups\/[\d\w]+\/videos\/[0-9]+/)) {
                                    id = url.split('/')[4];
                                } else if (url.match(/player.vimeo.com\/video\/[0-9]+/)) {
                                    id = url.split('/')[2];
                                } else {
                                    throw new Error('Unsupported Vimeo URL');
                                }

                            } else {
                                throw new Error('Unrecognised URL');
                            }
                            a = {type: 'v', id: id};
                            return a;

                            function processYouTube(id) {
                                if (!id) {
                                    throw new Error('Unsupported YouTube URL');
                                }
                                a = {type: 'y', id: id};
                                return (a); // default.jpg OR hqdefault.jpg
                            }
                        }

                        function callback(video) {
                            $('#category-video-wrapper').html('<img class="custom_media_video" src="" style="margin:0;padding:0;max-height:100px;float:none;" />');
                            $('#category-video-wrapper .custom_media_video').attr('src', video.url).css('display', 'block');
                            $('.cat-video-link').hide();
                        }

                        get_video_thumb(video_url, callback);
                    })

                    if ($('.custom_media_video').attr('src') !== '') {
                        $('.cat-video-link').hide();
                        $('.video_remove').hide();
                    }

                    $('.video_media_remove').on('click', function () {
                        $('#category-video-link').show();
                        $('#video_media_button').show();
                        $('#category-video-id').val('');
                        $('#category-video-wrapper').html('<img class="custom_media_video" src="" style="margin:0;padding:0;max-height:100px;float:none;" />');
                        $('#category-video-link').val('');
                    })
                });
            </script>
        <?php }

    }

    $CategoryFeaturedImage = new CategoryFeaturedImage();
    $CategoryFeaturedImage->init();

}