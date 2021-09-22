<?php

function simpleicons_setup_menu(){
	add_options_page( 'Popular Brand SVG Icons - Simple Icons', 'Simple Icons', 'edit_posts', 'simpleicons', 'simpleicons_admin_page_init' );
}
add_action('admin_menu', 'simpleicons_setup_menu');


function simpleicons_admin_page_init(){
	$copy_icon = '<svg role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M433.941 65.941l-51.882-51.882A48 48 0 0 0 348.118 0H176c-26.51 0-48 21.49-48 48v48H48c-26.51 0-48 21.49-48 48v320c0 26.51 21.49 48 48 48h224c26.51 0 48-21.49 48-48v-48h80c26.51 0 48-21.49 48-48V99.882a48 48 0 0 0-14.059-33.941zM266 464H54a6 6 0 0 1-6-6V150a6 6 0 0 1 6-6h74v224c0 26.51 21.49 48 48 48h96v42a6 6 0 0 1-6 6zm128-96H182a6 6 0 0 1-6-6V54a6 6 0 0 1 6-6h106v88c0 13.255 10.745 24 24 24h88v202a6 6 0 0 1-6 6zm6-256h-64V48h9.632c1.591 0 3.117.632 4.243 1.757l48.368 48.368a6 6 0 0 1 1.757 4.243V112z"></path></svg>';
	
	?>

	<div class="wrap">
		<div style="display:flex; justify-content:space-between; align-items: center;">
			<h1>Popular Brand SVG Icons - Simple Icons</h1>
			<p>Like this plugin? Please <a href="https://wordpress.org/support/plugin/simple-icons/reviews/#new-post" target="_blank">leave us a review!</a></p>
		</div>
		<hr class="wp-header-end">
		<div class="card" style="max-width:100%">
			<h2 class="title">Shortcode Generator</h2>
			<p>Select an icon below to generate your icon shortcode or menu text. <a target="_blank" href="https://wordpress.org/plugins/simple-icons/#description" title="Simple Icons">View documentation</a> | <a href="<?php echo admin_url( 'index.php?page=simpleicons-welcome' ) ?>">Video Tutorial</a></p>
		
			<pre><strong id="shortcode-preview">[simple_icon name="wordpress"]</strong> <span id="copy-shortcode" class="copy-icon"><?php echo $copy_icon; ?></span> <span id="copy-shortcode-msg" class="copy-msg"></span></pre>
			<pre><strong id="placeholder-preview">#wordpress#</strong> <span id="copy-placeholder" class="copy-icon"><?php echo $copy_icon; ?></span> <span id="copy-placeholder-msg" class="copy-msg"></span></pre>
			<div id="icon-preview"></div>
		</div>

		<div class="simple-icons-search-wrapper">
			<div class="search-results"></div>
			<div><input type="text" id="simple-icons-search" placeholder="Search by brand..." /></div>
		</div>
		<hr>

    	<div class="simpleicons-list-wrapper">
	    	<ul class="simpleicons-list">
	    		// load via javascript
	    	</ul>
	    </div>
	</div>
<?php }