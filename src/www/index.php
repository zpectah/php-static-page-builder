<?php
const PATH_ROOT = '../';
require PATH_ROOT . 'core/index.php';

$page = new \core\Page;

$meta = $page -> get_meta();
$scripts = $page -> get_scripts();
$styles = $page -> get_styles();
?>

<!doctype html>
<html lang="<?=($meta['lang']) ?>">
<head>
    <meta charset="<?=($meta['charset']) ?>" />
    <meta name="viewport" content="<?=($meta['viewport']) ?>" />
    <title><?=($meta['title']) ?></title>
    <meta name="description" content="<?=($meta['description']) ?>" />
    <meta name="keywords" content="<?=($meta['keywords']) ?>" />
    <meta name="robots" content="<?=($meta['robots']) ?>" />
    <meta name="author" content="<?=($meta['author']) ?>" />
    <meta name="url" content="<?=($meta['url']) ?>" />
    <meta name="og:url" content="<?=($meta['og:url']) ?>" />

    <?php foreach ($styles['head']['rest'] as $item) {
        echo ('<link href="' . $item . '" rel="stylesheet" />');
    } ?>
    <link href="<?=($styles['head']['main']) ?>" rel="stylesheet" />
    <script>
        window.APP_ENV = window.APP_ENV || '<?=(ENV) ?>';
        window.APP_TIMESTAMP = window.APP_TIMESTAMP || '<?=(TIMESTAMP) ?>';
        window.APP_TOKEN = window.APP_TOKEN || '...'; // TODO
        window.APP_LANG = window.APP_LANG || '...'; // TODO
    </script>
    <?php foreach ($scripts['head']['rest'] as $item) {
        echo ('<script src="' . $item . '"></script>');
    } ?>
</head>
<body
    id="page"
    class="page"
>
    <?php foreach ($styles['body']['rest'] as $item) {
        echo ('<link href="' . $item . '" rel="stylesheet" />');
    } ?>
    <div
        class="page-view"
        id="vue-app"
    >
        <?php $page -> render() ?>
    </div>
    <?php foreach ($scripts['body']['rest'] as $item) {
        echo ('<script src="' . $item . '"></script>');
    } ?>
    <script src="<?=($scripts['body']['main']) ?>"></script>
</body>
</html>
