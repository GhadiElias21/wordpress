<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php bloginfo('name'); ?></title>
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="<?php echo home_url(); ?>"><?php bloginfo('name'); ?></a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="<?php echo home_url(); ?>">Main</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo home_url('/catalog'); ?>">Catalog</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo home_url('/personal-account'); ?>">Personal account</a>
            </li>
        </ul>
    </div>
</nav>
<?php wp_footer(); ?>
</body>
</html>
