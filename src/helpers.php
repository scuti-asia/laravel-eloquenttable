
<?php

use Scuti\EloquentTable\EloquentTableServiceProvider;

if (!function_exists('sortableUrlLink')) {
    function sortableUrlLink($title, $parameters)
    {
        $field = Request::get('field');
        $sort = strtolower(Request::get('sort'));

        if ($sort === 'desc') {
            $parameters['sort'] = 'asc';
        } else {
            $parameters['sort'] = 'desc';
        }

        if ($field === $parameters['field']) {
            switch ($parameters['sort']) {
                case 'asc':
                    $icon = Config::get('eloquenttable.default_sorting_icons.desc_sort_class');
                    break;

                case 'desc';
                    $icon = Config::get('eloquenttable.default_sorting_icons.asc_sort_class');
                    break;
                default:
                    break;
            }
        } else {
            $icon = sprintf('%s', Config::get('eloquenttable.default_sorting_icons.sort_class'));
        }

        $parameters = array_merge(Request::query(), $parameters);

        return sprintf('<a class="link-sort" href="%s">%s <i class="%s"></i></a>', Request::url().'?'.http_build_query($parameters), $title, $icon);
    }
}