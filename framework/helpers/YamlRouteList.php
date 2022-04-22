<?php

use Illuminate\Support\Arr;

function YamlRouteList(array $menu, array &$result, array $parent = [], string $section = null)
{
    if (empty($parent)) {
        $parent = empty($menu['reference']) ? [''] : ['', $menu['reference']];
        //$path = empty($menu['reference'])?'/':'/'.$menu['reference'];
    } else {
        $parent = array_merge($parent, [$menu['reference']]);
    }
    $path = implode('/', $parent);

    if (is_null($section)) {
        $result[] = array_merge(Arr::except($menu, ['items']), ['reference' => $path]);
    }
    if (array_key_exists('items', $menu)) {
        foreach ($menu['items'] as $item) {
            if (is_null($section) || $section == $item['component']) {
                YamlRouteList($item, $result, $parent);
                if ($section === $item['component']) {
                    $result = array_slice($result, 1);
                }
            }

        }
    }
}
