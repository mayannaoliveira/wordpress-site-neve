// add delete buttons
jQuery.fn.add_delete = function () {
    this.append('<div class="tnpc-row-delete" title="Delete"><img src="' + TNP_PLUGIN_URL + '/emails/tnp-composer/_assets/delete.png" width="32"></div>');
    this.find('.tnpc-row-delete').perform_delete();
};

// delete row
jQuery.fn.perform_delete = function () {
    this.click(function () {
        // hide block edit form
        jQuery("#tnpc-block-options").hide();
        // remove block
        jQuery(this).parent().remove();
        tnpc_mobile_preview();
    });
}

// add edit button
jQuery.fn.add_block_edit = function () {
    this.append('<div class="tnpc-row-edit-block" title="Edit"><img src="' + TNP_PLUGIN_URL + '/emails/tnp-composer/_assets/edit.png" width="32"></div>');
    this.find('.tnpc-row-edit-block').perform_block_edit();
}

// add clone button
jQuery.fn.add_block_clone = function () {
    this.append('<div class="tnpc-row-clone" title="Clone"><img src="' + TNP_PLUGIN_URL + '/emails/tnp-composer/_assets/copy.png" width="32"></div>');
    this.find('.tnpc-row-clone').perform_clone();
}

let start_options = null;
let container = null;

jQuery.fn.perform_block_edit = function () {

    jQuery(".tnpc-row-edit-block").click(function (e) {
        e.preventDefault()
    });

    this.click(function (e) {

        e.preventDefault();

        target = jQuery(this).parent().find('.edit-block');

        jQuery("#tnpc-edit-block .bgcolor").val(target.css("background-color"));
        jQuery("#tnpc-edit-block .font").val(target.css("font-family"));

        // The row container which is a global variable and used later after the options save
        container = jQuery(this).closest("table");

        if (container.hasClass('tnpc-row-block')) {

            jQuery("#tnpc-block-options").fadeIn(500);
            var options = container.find(".tnpc-block-content").attr("data-json");
            // Compatibility
            if (!options) {
                options = target.attr("data-options");
            }

            jQuery("#tnpc-block-options-form").load(ajaxurl, {
                action: "tnpc_options",
                id: container.data("id"),
                context_type: tnp_context_type,
                options: options
            }, function () {
                start_options = jQuery("#tnpc-block-options-form").serialize();
            });

        } else {
            alert("This is deprecated block version and cannot be edited. Please replace it with a new one.");
        }

    });

};

jQuery.fn.perform_clone = function () {

    jQuery(".tnpc-row-clone").click(function (e) {
        e.preventDefault()
    });

    this.click(function (e) {

        e.preventDefault();

        // hide block edit form
        jQuery("#tnpc-block-options").hide();

        // find the row
        let row = jQuery(this).closest('.tnpc-row');

        // clone the block
        let new_row = row.clone();
        new_row.find(".tnpc-row-delete").remove();
        new_row.find(".tnpc-row-edit-block").remove();
        new_row.find(".tnpc-row-clone").remove();

        new_row.add_delete();
        new_row.add_block_edit();
        new_row.add_block_clone();
        // if (new_row.hasClass('tnpc-row-block')) {
        //     new_row.find(".tnpc-row-edit-block i").click();
        // }
        new_row.insertAfter(row);
        tnpc_mobile_preview();
    });
};


jQuery(function () {

    // collapse wp menu
    jQuery('body').addClass('folded');

    // open blocks tab
    document.getElementById("defaultOpen").click();

    // preload content from a body named input
    var preloadedContent = jQuery('input[name="body"]').val();
    if (!preloadedContent) {
        preloadedContent = jQuery('input[name="options[body]"]').val();
    }
    if (!preloadedContent) {
        tnpc_show_presets();
    } else {
        jQuery('#newsletter-builder-area-center-frame-content').html(preloadedContent);
        start_composer();
    }

    // subject management
    jQuery('#options-title').val(jQuery('#tnpc-form input[name="options[subject]"]').val());

});

function start_composer() {

    //Drag & Drop
    jQuery("#newsletter-builder-area-center-frame-content").sortable({
        revert: false,
        placeholder: "placeholder",
        forcePlaceholderSize: true,
        opacity: 0.6,
        tolerance: "pointer",
        helper: function (e) {
            var helper = jQuery(document.getElementById("sortable-helper")).clone();
            return helper;
        },
        update: function (event, ui) {

            if (ui.item.attr("id") == "draggable-helper") {
                loading_row = jQuery('<div style="text-align: center; padding: 20px; background-color: #d4d5d6; color: #52BE7F;"><i class="fa fa-cog fa-2x fa-spin" /></div>');
                ui.item.before(loading_row);
                ui.item.remove();
                var data = {
                    'action': 'tnpc_render',
                    'b': ui.item.data("id"),
                    'full': 1
                };
                jQuery.post(ajaxurl, data, function (response) {
                    new_row = jQuery(response);
//                    ui.item.before(new_row);
//                    ui.item.remove();
                    loading_row.before(new_row);
                    loading_row.remove();
                    new_row.add_delete();
                    new_row.add_block_edit();
                    new_row.add_block_clone();
                    // new_row.find(".tnpc-row-edit").hover_edit();
                    if (new_row.hasClass('tnpc-row-block')) {
                        new_row.find(".tnpc-row-edit-block").click();
                    }
                    tnpc_mobile_preview();
                }).fail(function () {
                    alert("Block rendering failed.");
                    loading_row.remove();
                });
            } else {
                tnpc_mobile_preview();
            }
        }
    });

    jQuery(".newsletter-sidebar-buttons-content-tab").draggable({
        connectToSortable: "#newsletter-builder-area-center-frame-content",

        // Build the helper for dragging
        helper: function (e) {
            var helper = jQuery(document.getElementById("draggable-helper")).clone();
            // Do not uset .data() with jQuery
            helper.attr("data-id", e.currentTarget.dataset.id);
            helper.html(e.currentTarget.dataset.name);
            return helper;
        },
        revert: false,
        start: function () {
            if (jQuery('.tnpc-row').length) {
            } else {
                jQuery('#newsletter-builder-area-center-frame-content').append('<div class="tnpc-drop-here">Drag&Drop blocks here!</div>');
            }
        },
        stop: function (event, ui) {
            jQuery('.tnpc-drop-here').remove();
        }
    });

    // Closes the block options layer (without saving)
    jQuery("#tnpc-block-options-cancel").click(function () {
        jQuery(this).parent().parent().fadeOut(500);
        jQuery.post(ajaxurl, start_options, function (response) {
            target.html(response);
            jQuery("#tnpc-block-options-form").html("");
        });
    });

    // Fires the save event for block options
    jQuery("#tnpc-block-options-save").click(function (e) {
        e.preventDefault();
        // fix for Codemirror
        if (typeof templateEditor !== 'undefined') {
            templateEditor.save();
        }

        if (window.tinymce)
            window.tinymce.triggerSave();

        jQuery("#tnpc-block-options").fadeOut(500);

        var data = jQuery("#tnpc-block-options-form").serialize();

        jQuery.post(ajaxurl, data, function (response) {
            target.html(response);
            tnpc_mobile_preview();
            //target.attr("data-options", options);
            //target.find(".tnpc-row-edit").hover_edit();
            jQuery("#tnpc-block-options-form").html("");
        });
    });

    // live preview from block options *** EXPERIMENTAL ***
    jQuery('#tnpc-block-options-form').change(function (event) {
        var data = jQuery("#tnpc-block-options-form").serialize();
        jQuery.post(ajaxurl, data, function (response) {
            target.html(response);
            if (event.target.dataset.afterRendering === 'reload') {
                container.find(".tnpc-row-edit-block").click();
            }
        }).fail(function () {
            alert("Block rendering failed");
        });



    });

    jQuery(".tnpc-row").add_delete();
    jQuery(".tnpc-row").add_block_edit();
    jQuery(".tnpc-row").add_block_clone();


    tnpc_mobile_preview();

}

function tnpc_mobile_preview() {

    var d = document.getElementById("tnpc-mobile-preview").contentWindow.document;
    d.open();

    d.write("<!DOCTYPE html>\n<html>\n<head>\n");
    d.write("<link rel='stylesheet' href='" + TNP_HOME_URL + "?na=emails-composer-css&ver=" + Math.random() + "' type='text/css'>");
    d.write("<style type='text/css'>.tnpc-row-delete, .tnpc-row-edit-block, .tnpc-row-clone { display: none; }</style>");
    d.write("</head>\n<body style='margin: 0; padding: 0;'><div style='width: 320px!important'>");
    d.write(jQuery("#newsletter-builder-area-center-frame-content").html());
    d.write("</div>\n</body>\n</html>");
    d.close();
}

function tnpc_save(form) {

    jQuery("#newsletter-preloaded-export").html(jQuery("#newsletter-builder-area-center-frame-content").html());

    jQuery("#newsletter-preloaded-export .tnpc-row-delete").remove();
    jQuery("#newsletter-preloaded-export .tnpc-row-edit-block").remove();
    jQuery("#newsletter-preloaded-export .tnpc-row-clone").remove();
    jQuery("#newsletter-preloaded-export .tnpc-row").removeClass("ui-draggable");

    let preload_export_html = jQuery("#newsletter-preloaded-export").html();
    preload_export_html = jQuery.trim(preload_export_html);

    let css = jQuery.trim(form.elements["options[css]"].value);

    let export_content = '<!DOCTYPE html>\n<html>\n<head>\n<title>{email_subject}</title>\n<meta charset="utf-8">\n<meta name="viewport" content="width=device-width, initial-scale=1">\n<meta http-equiv="X-UA-Compatible" content="IE=edge">\n';
    export_content += '<style type="text/css">' + css + '</style>';
    export_content += '</head>\n<body style="margin: 0; padding: 0;">\n';
    export_content += preload_export_html;
    export_content += '\n</body>\n</html>';
    form.elements["options[body]"].value = export_content;
    form.elements["options[subject]"].value = jQuery('#options-title').val();
    jQuery("#newsletter-preloaded-export").html(' ');

}

function tnpc_test() {
    let form = document.getElementById("tnpc-form");
    tnpc_save(form);
    form.act.value = "test";
    form.submit();
}

function openTab(evt, tabName) {
    evt.preventDefault();
    // Declare all variables
    var i, tabcontent, tablinks;

    // Get all elements with class="tabcontent" and hide them
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }

    // Get all elements with class="tablinks" and remove the class "active"
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }

    // Show the current tab, and add an "active" class to the button that opened the tab
    document.getElementById(tabName).style.display = "flex";
    evt.currentTarget.className += " active";
}


function tnpc_show_presets() {

    jQuery('.tnpc-controls input').attr('disabled', true);
    jQuery('#newsletter-builder-area-center-frame-content').load(ajaxurl, {
        action: "tnpc_presets",
    });

}

function tnpc_load_preset(id) {

    jQuery('#newsletter-builder-area-center-frame-content').load(ajaxurl, {
        action: "tnpc_presets",
        id: id
    }, function () {
        start_composer();
        jQuery('.tnpc-controls input').attr('disabled', false);
    });

}

function tnpc_scratch() {

    jQuery('#newsletter-builder-area-center-frame-content').html(" ");
    start_composer();

}

function tnpc_reload_options(e) {
    e.preventDefault();
    let options = jQuery("#tnpc-block-options-form").serializeArray();
    for (let i=0; i<options.length; i++) {
        if (options[i].name == 'action') {
            options[i].value = 'tnpc_options';
        }
    }

    options["action"] = "tnpc_options";
    options["id"] = container.data("id");
    jQuery("#tnpc-block-options-form").load(ajaxurl, options);
}
