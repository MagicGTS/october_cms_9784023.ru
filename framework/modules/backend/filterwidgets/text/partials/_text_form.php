<div class="filter-box loading-indicator-container size-input-text">
    <?php if (is_array($scope->conditions) && count($scope->conditions) === 1): ?>
        <?php foreach ($scope->conditions as $condition => $value): ?>
            <div class="filter-facet">
                <div class="facet-item">
                    <input type="hidden" name="Filter[condition]" value="<?= $condition ?>" />
                    <span><?= $this->getConditionLang($condition) ?></span>
                </div>
                <?= $this->makePartial('item_single') ?>
            </div>
        <?php endforeach ?>
    <?php else: ?>
        <div class="filter-facet">
            <div class="facet-item is-grow">
                <select name="Filter[condition]" class="form-control custom-select select-no-search input-sm">
                    <?php foreach ((array) $scope->conditions as $condition => $value): ?>
                        <option
                            value="<?= $condition ?>"
                            <?= $scope->condition === $condition ? 'selected="selected"' : '' ?>
                        ><?= $this->getConditionLang($condition) ?></option>
                    <?php endforeach ?>
                </select>
            </div>
        </div>
        <div class="filter-facet">
            <div class="facet-item">
                <span>└─</span>
            </div>
            <?= $this->makePartial('item_single') ?>
        </div>
    <?php endif ?>

    <div class="filter-buttons">
        <button class="btn btn-xs btn-primary" data-filter-action="apply">
            <?= __("Apply") ?>
        </button>
        <div class="flex-grow-1"></div>
        <button class="btn btn-xs btn-secondary" data-filter-action="clear">
            <?= __("Clear") ?>
        </button>
    </div>
</div>