jQuery.cookie = function (name, value, options) {
    if (typeof value != 'undefined') { // name and value given, set cookie
        options = options || {};
        if (value === null) {
            value = '';
            options.expires = -1;
        }
        var expires = '';
        if (options.expires && (typeof options.expires == 'number' || options.expires.toUTCString)) {
            var date;
            if (typeof options.expires == 'number') {
                date = new Date();
                date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000));
            } else {
                date = options.expires;
            }
            expires = '; expires=' + date.toUTCString(); // use expires attribute, max-age is not supported by IE
        }
        // CAUTION: Needed to parenthesize options.path and options.domain
        // in the following expressions, otherwise they evaluate to undefined
        // in the packed version for some reason...
        var path = options.path ? '; path=' + (options.path) : '';
        var domain = options.domain ? '; domain=' + (options.domain) : '';
        var secure = options.secure ? '; secure' : '';
        document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join('');
    } else { // only name given, get cookie
        var cookieValue = null;
        if (document.cookie && document.cookie != '') {
            var cookies = document.cookie.split(';');
            for (var i = 0; i < cookies.length; i++) {
                var cookie = jQuery.trim(cookies[i]);
                // Does this cookie string begin with the name we want?
                if (cookie.substring(0, name.length + 1) == (name + '=')) {
                    cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
                    break;
                }
            }
        }
        return cookieValue;
    }
};

function tnp_toggle_schedule() {
    jQuery("#tnp-schedule-button").toggle();
    jQuery("#tnp-schedule").toggle();
}

function tnp_select_toggle(s, t) {
    if (s.value == 1) {
        jQuery("#options-" + t).show();
    } else {
        jQuery("#options-" + t).hide();
    }
}

/*
 * Used by the date field of NewsletterControls
 */
function tnp_date_onchange(field) {
    let id = field.id.substring(0, field.id.lastIndexOf('_'));
    let base_field = document.getElementById('options-' + id);
    let year = document.getElementById(id + '_year');
    let month = document.getElementById(id + '_month');
    let day = document.getElementById(id + '_day');
    if (year.value === '' || month.value === '' || day.value === '') {
        base_field.value = 0;
    } else {
        base_field.value = new Date(year.value, month.value, day.value, 12, 0, 0).getTime() / 1000;
    }
    //this.form.elements['options[" . esc_attr($name) . "]'].value = new Date(document.getElementById('" . esc_attr($name) . "_year').value, document.getElementById('" . esc_attr($name) . "_month').value, document.getElementById('" . esc_attr($name) . "_day').value, 12, 0, 0).getTime()/1000";
}

window.onload = function () {
    jQuery('.tnp-counter-animation').each(function () {
        var _this = jQuery(this);

        var val = null;
        if (!isFloat(_this.text())) {
            val = {
                parsed: parseInt(_this.text()),
                rounded: function (value) {
                    return Math.ceil(value);
                }
            };

            if (_this.hasClass('percentage')) {
                _this.css('min-width', '60px');
            }
        } else {
            val = {
                parsed: parseFloat(_this.text()),
                rounded: function (value) {
                    return value.toFixed(1);
                }
            };
        }

        jQuery({counter: 0}).animate({counter: val.parsed}, {
            duration: 1000,
            easing: 'swing',
            step: function () {
                _this.text(val.rounded(this.counter));
            }
        });

        function isFloat(value) {
            return !isNaN(Number(value)) && Number(value).toString().indexOf('.') !== -1;
        }

    });

    (function targetinFormOnChangeHandler() {

        if (isNewsletterOptionsPage()) {

            var newsletterStatusScheduleValue = jQuery('#tnp-nl-status .tnp-nl-status-schedule-value');

            jQuery('#newsletter-form').change(function (event) {

                if (isElementInsideTargettingTab(event.target)) {
                    newsletterStatusScheduleValue.text(tnp_translations['save_to_update_counter']);
                }

                function isElementInsideTargettingTab(element) {
                    return jQuery(element).parents('#tabs-options').length === 1
                }

            });
        }

        function isNewsletterOptionsPage() {
            return jQuery("#tnp-nl-status").length
                    && jQuery("#newsletter-form").length;
        }

    })();

};

/**
 * Initialize the color pickers (is invoked on document load and on AJAX forms load in the composer.
 * https://seballot.github.io/spectrum/
 */
function tnp_controls_init() {
    jQuery(".tnpc-color").spectrum({
        type: 'color',
        allowEmpty: true,
        showAlpha: false,
        showInput: true,
        preferredFormat: 'hex'
    });
}

function tnp_fields_media_mini_select(el) {
    event.preventDefault();

    let name = jQuery(el).data("name");

    let tnp_uploader = wp.media({
        title: "Select an image",
        button: {
            text: "Select"
        },
        multiple: false
    }).on("select", function () {
        let media = tnp_uploader.state().get("selection").first();
        let $field = jQuery("#" + name + "_id");
        $field.val(media.id);
        $field.trigger("change");

        var img_url = media.attributes.url;
        if (typeof media.attributes.sizes.thumbnail !== "undefined")
            img_url = media.attributes.sizes.thumbnail.url;
        document.getElementById(name + "_img").src = img_url;
    }).open();
}

function tnp_fields_url_select(el) {
    event.preventDefault();

    let field_id = jQuery(el).data("field");

    let tnp_uploader = wp.media({
        title: "Select an image",
        button: {
            text: "Select"
        },
        multiple: false
    }).on("select", function () {
        let media = tnp_uploader.state().get("selection").first();
        let $field = jQuery("#" + field_id);
        $field.val(media.attributes.url);
        $field.trigger("change");
    }).open();
}

function tnp_fields_media_mini_remove(name) {
    event.preventDefault();
    event.stopPropagation();
    let $field = jQuery("#" + name + "_id");
    $field.val("");
    $field.trigger("change");
    document.getElementById(name + "_img").src = "";
}

function tnp_lists_toggle(e) {
    jQuery('#' + e.id + '-notes > div').hide();
    jQuery('#' + e.id + '-notes .list_' + e.value).show();
}
