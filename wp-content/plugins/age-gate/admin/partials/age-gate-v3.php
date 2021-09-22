<div class="wrap">
  <h2><?php echo esc_html(get_admin_page_title()); ?></h2>

    <p>Age Gate version 3 will be releasing soon with:</p>

    <h3>Improved performance</h3>
    <p>Age Gate has been rewritten from the ground up with improved performance,</p>

    <h3>Even more customisable</h3>
    <p>Age Gate 3 provides more presentation options to fit your site and style better than ever.</p>

    <p>Version 3 also introduces support in the WordPress customiser so you can see your changes in real time, as well as creating completely custom templates for seasoned developers.</p>

    <h3>Better hooks</h3>
    <p>All the hooks and filters have been reworked with many supported natively in JavaScript as well as PHP.</p>

    <p>Age Gate 3 is currently available in development builds. If your site is in development, perhaps give it a shot.</p>

    <h3>Upgrading</h3>

    <p>When the time comes, upgrading to version 3 should be fairly seamless for most users. However, if you are extending Age Gate via hooks, there will be some changes. Over the next few weeks we'll be putting together information on how this can best be achieved.</p>

    // find filters
    <?php
        $files = glob(AGE_GATE_PATH . '**/*.php');

    ?>
    <pre>
        <?php print_r($files); ?>

        <?php foreach ($files as $file) {
        $fileData = file_get_contents($file);
        // preg_match_all('/apply_filters\((?:\'|")(.*?)(?:\'|")/', $fileData, $matches);
        preg_match_all('/apply_filters\((.*?)\)/', $fileData, $matches);
        print_r($matches[1] ?? '');
    } ?>
    </pre>
</div>
