<?php
    $action = $record->is_disabled ? 'enable' : 'disable';
?>
<label class="custom-switch m-b-0" data-check="oc-disable-<?= $record->id ?>">
    <input data-request="onBulkAction"
        data-request-data="action: '<?= $action ?>', checked: [<?= $record->id ?>]"
        data-request-update="list_manage_toolbar: '#plugin-toolbar'"
        type="checkbox"
        name="disable_<?= $record->id ?>"
        value="<?= !$record->is_disabled ?>"
        <?php if (!$record->is_disabled): ?>checked<?php endif ?>
        data-stripe-load-indicator
    >
    <span>
        <span><?= e(trans('system::lang.plugins.check_yes')) ?></span>
        <span><?= e(trans('system::lang.plugins.check_no')) ?></span>
    </span>
    <a class="slide-button"></a>
</label>
