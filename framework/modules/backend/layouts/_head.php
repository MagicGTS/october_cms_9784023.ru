<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1, user-scalable=0, minimal-ui">
<meta name="robots" content="noindex">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="app-timezone" content="<?= e(Config::get('app.timezone')) ?>">
<meta name="backend-base-path" content="<?= Backend::baseUrl() ?>">
<meta name="backend-timezone" content="<?= e(Backend\Models\Preference::get('timezone')) ?>">
<meta name="backend-locale" content="<?= e(Backend\Models\Preference::get('locale')) ?>">
<meta name="csrf-token" content="<?= csrf_token() ?>">
<link rel="icon" type="image/png" href="<?= e(Backend\Models\BrandSetting::getFavicon()) ?>">
<title data-title-template="<?= empty($this->pageTitleTemplate) ? '%s' : e($this->pageTitleTemplate) ?> | <?= e(Backend\Models\BrandSetting::get('app_name')) ?>">
    <?= e(trans($this->pageTitle)) ?> | <?= e(Backend\Models\BrandSetting::get('app_name')) ?>
</title>
<?php
    $coreBuild = Backend::assetVersion();

    $styles = [
        Url::asset('modules/system/assets/ui/storm.css'),
        Backend::skinAsset('assets/css/october.css'),
    ];

    $scripts = [
        Url::asset('modules/system/assets/js/vendor/jquery.min.js'),
        Url::asset('modules/system/assets/js/framework.js'),
        Url::asset('modules/system/assets/ui/storm-min.js'),
        Url::asset('modules/system/assets/js/vue.bundle-min.js'),
        Backend::skinAsset('assets/js/october-min.js'),
        Url::asset('modules/system/assets/js/lang/lang.'.App::getLocale().'.js'),
        Backend::skinAsset('assets/js/october.flyout.js'),
        Backend::skinAsset('assets/js/october.tabformexpandcontrols.js'),
    ];
?>

<?php foreach ($styles as $style): ?>
    <link href="<?= $style . '?v=' . $coreBuild ?>" rel="stylesheet" importance="high" />
<?php endforeach ?>

<?php foreach ($scripts as $script): ?>
    <script src="<?= $script . '?v=' . $coreBuild ?>" importance="high"></script>
<?php endforeach ?>

<?php if (!Config::get('backend.enable_service_workers', false)): ?>
    <script> unregisterServiceWorkers() </script>
<?php endif ?>

<?= $this->makeAssets() ?>
<?= Block::placeholder('head') ?>
<?= $this->makeLayoutPartial('custom_styles') ?>
