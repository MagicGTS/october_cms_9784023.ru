<?php
    $activeValue = $scope->scopeValue !== null ? $scope->value : $scope->default;
?>
<div
    class="filter-scope checkbox custom-checkbox is-indeterminate"
    data-scope-name="<?= $scope->scopeName ?>">
    <input type="checkbox" id="<?= $scope->getId() ?>" data-checked="<?= $activeValue ?: '0' ?>" />
    <label class="storm-icon-pseudo" for="<?= $scope->getId() ?>"><?= e(trans($scope->label)) ?></label>
</div>
