<?php

/**
 * Add filter_template to all template_hierarchy filters
 * This will allow us to look if a file .blade.php exists for this template
 * And run it with BladeOne instead of letting WP manage it
 */
collect([
    'index', '404', 'archive', 'author', 'category', 'tag', 'taxonomy', 'date', 'home',
    'frontpage', 'page', 'paged', 'search', 'single', 'singular', 'attachment', 'embed'
])->map(function ($type) {
    add_filter("{$type}_template_hierarchy", 'bladeify_templates');
});

/**
 * Render page using Blade
 */
add_filter('template_include', function ($template) {
    if (is_blade_template($template)) {
        echo template($template);
        return __DIR__ . '/empty.php';
    }

    return $template;
}, PHP_INT_MAX);

/**
 * Render comments.blade.php
 */
add_filter('comments_template', function ($comments_template) {
    $comments_template = str_replace(
        [get_stylesheet_directory(), get_template_directory()],
        '',
        $comments_template
    );

    $theme_template = locate_template(bladeify_templates([$comments_template]));

    if (is_blade_template($theme_template)) {
        echo template($theme_template);
        return __DIR__ . '/empty.php';
    }

    return $comments_template;
}, 100);


/**
 * For each template file, prioritize the version with a .blade.php extension
 * @param string|string[] $templates Possible template files
 * @return array
 */
function bladeify_templates($templates)
{
    $new_templates = collect($templates)
        ->map(function ($template) {
            /** Remove .blade.php/.blade/.php from template names */
            $template = preg_replace('#\.(blade\.?)?(php)?$#', '', ltrim($template));

            return $template;
        })
        ->flatMap(function ($template) {
            return collect([
                    "{$template}.blade.php",
                    "{$template}.php",
                ]);
        })
        ->filter()
        ->unique()
        ->all();

    return $new_templates;
}

/**
 * Check if $file is a blade template
 * @param string $file
 */
function is_blade_template($file)
{
    return wp_bladeone()->isBladeTemplate($file);
}

/**
 * @param string $file
 * @param array $data
 * @return string
 */
function template($file, $data = [])
{
    // remove views path from start
    $file = preg_replace('#^('.get_stylesheet_directory().')/#', '', $file);

    // remove .blade.php if needed
    if (strpos($file, '/') === false) {
        $file = preg_replace('#\.blade\.php$#', '', $file);
    }

    // return te compiled content
    return wp_bladeone()->run($file, $data);
}

/**
 * Updates the `$post` variable in blade templates when `the_post()` is called
 * Note: updated value is only available for subsequently loaded views, such as partials
 */
add_action('the_post', function ($post) {
    wp_bladeone()->share('post', $post);
});
