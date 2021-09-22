<?php

add_action( 'admin_init', 'simple_icons_do_activation_redirect' );
function simple_icons_do_activation_redirect() {
  // Bail if no activation redirect
    if ( ! get_transient( '_simple_icons_activation_redirect' ) ) {
    return;
  }

  // Delete the redirect transient
  delete_transient( '_simple_icons_activation_redirect' );

  // Bail if activating from network, or bulk
  if ( is_network_admin() || isset( $_GET['activate-multi'] ) ) {
    return;
  }

  // Redirect to plugin welcome page
  wp_safe_redirect( add_query_arg( array( 'page' => 'simpleicons-welcome' ), admin_url( 'index.php' ) ) );

}

add_action('admin_menu', 'simple_icons_pages');

function simple_icons_pages() {
  add_dashboard_page(
    'Welcome To Simple Icons',
    'Simple Icons',
    'read',
    'simpleicons-welcome',
    'simple_icons_welcome'
  );
}

function simple_icons_welcome() {
    simple_icons_welcome_header();
    ?>

    <div class="full-width">
        <h2>Video Tutorial</h2>
      <iframe width="100%" height="600px" src="https://www.youtube.com/embed/4GrHU-hyXs0" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
    </div>
  </div>
  <?php
}

function simple_icons_welcome_header() {
  $screen = get_current_screen();
  ?>
  <div class="wrap about-wrap full-width-layout">
    <h1>Welcome to Simple Icons v<?php echo SIMPLE_ICONS_VERSION; ?></h1>

    
    <p class="about-text">
      An easy to use SVG icons plugin with over 500+ brand icons. Use these icons in your menus, widgets, posts, or pages. <a href="https://thememason.com/plugins/popular-brand-svg-icons-simple-icons/" title="Simple Icons" target="_blank">Learn more</a> | <a href="<?php echo admin_url( 'options-general.php?page=simpleicons') ?>">View Icons</a>
    </p>
    <div class="wp-badge" style="background-color: #111111; background-image:url(<?php echo plugin_dir_url( __FILE__ ) . 'imgs/simpleicons.svg'; ?>)">Version <?php echo SIMPLE_ICONS_VERSION; ?></div>
    
    <h2 class="nav-tab-wrapper wp-clearfix">
      <a href="<?php echo admin_url( 'index.php?page=simpleicons-welcome') ?>" class="nav-tab<?php echo ($screen->id == 'dashboard_page_simpleicons-welcome' ? ' nav-tab-active' : ''); ?>">Get Started</a>
    </h2>
  <?php
}

add_action( 'admin_head', 'simple_icons_remove_menus' );
function simple_icons_remove_menus() {
    remove_submenu_page( 'index.php', 'simpleicons-welcome' );
    remove_submenu_page( 'index.php', 'simpleicons-examples' );
}
