<?php
    $activeValue = $scope->scopeValue !== null ? $scope->value : $scope->default;
?>
<div
    class="filter-scope checkbox custom-checkbox"
    data-scope-name="<?= $scope->scopeName ?>">
    <input type="checkbox" id="<?= $scope->getId() ?>" <?= $activeValue ? 'checked' : '' ?> />
    <label class="storm-icon-pseudo" for="<?= $scope->getId() ?>"><?= e(trans($scope->label)) ?></label>
</div>
