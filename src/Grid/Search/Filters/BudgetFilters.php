<?php

namespace Gcromo\Grid\Search\Filters;

use Gcromo\Grid\Definition\BudgetGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Search\Filters;

final class BudgetFilters extends Filters
{
    protected $filterId = BudgetGridDefinitionFactory::GRID_ID;

    public static function getDefaults(): array
    {
        return [
            'limit' => 20,
            'offset' => 0,
            'orderBy' => 'id_gcromo_budget',
            'sortOrder' => 'desc',
            'filters' => [],
        ];
    }
}
