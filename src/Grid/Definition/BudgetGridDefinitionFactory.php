<?php

namespace Gcromo\Grid\Definition;

use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\SimpleGridAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BadgeColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\AbstractFilterableGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\DeleteActionTrait;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShopBundle\Form\Admin\Type\DateRangeType;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Tools;

class BudgetGridDefinitionFactory extends AbstractFilterableGridDefinitionFactory
{
    use DeleteActionTrait;

    public const GRID_ID = 'gcromo_budget';

    protected function getId()
    {
        return self::GRID_ID;
    }

    protected function getName()
    {
        return $this->trans('Budgets', [], 'Modules.Gcromo.Admin');
    }

    protected function getColumns()
    {
        $columns = (new ColumnCollection())
            ->add(
                (new DataColumn('id_gcromo_budget'))
                    ->setName($this->trans('ID', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'id_gcromo_budget',
                        'alignment' => 'center',
                    ])
            )
            ->add(
                (new DataColumn('quote_reference'))
                    ->setName($this->trans('Reference', [], 'Modules.Gcromo.Admin'))
                    ->setOptions([
                        'field' => 'quote_reference',
                    ])
            )
            ->add(
                (new DataColumn('quote_date'))
                    ->setName($this->trans('Quote date', [], 'Modules.Gcromo.Admin'))
                    ->setOptions([
                        'field' => 'quote_date',
                    ])
            )
            ->add(
                (new DataColumn('customer_name'))
                    ->setName($this->trans('Customer', [], 'Modules.Gcromo.Admin'))
                    ->setOptions([
                        'field' => 'customer_name',
                    ])
            )
            ->add(
                (new DataColumn('product_name'))
                    ->setName($this->trans('Product', [], 'Modules.Gcromo.Admin'))
                    ->setOptions([
                        'field' => 'product_name',
                    ])
            )
            ->add(
                (new DataColumn('work_scope'))
                    ->setName($this->trans('Work scope', [], 'Modules.Gcromo.Admin'))
                    ->setOptions([
                        'field' => 'work_scope',
                    ])
            )
            ->add(
                (new DataColumn('dimension_height_cm'))
                    ->setName($this->trans('Height (cm)', [], 'Modules.Gcromo.Admin'))
                    ->setOptions([
                        'field' => 'dimension_height_cm',
                        'alignment' => 'right',
                    ])
            )
            ->add(
                (new DataColumn('dimension_width_primary_cm'))
                    ->setName($this->trans('Primary width (cm)', [], 'Modules.Gcromo.Admin'))
                    ->setOptions([
                        'field' => 'dimension_width_primary_cm',
                        'alignment' => 'right',
                    ])
            )
            ->add(
                (new DataColumn('dimension_width_secondary_cm'))
                    ->setName($this->trans('Secondary width (cm)', [], 'Modules.Gcromo.Admin'))
                    ->setOptions([
                        'field' => 'dimension_width_secondary_cm',
                        'alignment' => 'right',
                    ])
            )
            ->add(
                (new DataColumn('sales_rep'))
                    ->setName($this->trans('Sales representative', [], 'Modules.Gcromo.Admin'))
                    ->setOptions([
                        'field' => 'sales_rep',
                    ])
            )
            ->add(
                (new BadgeColumn('status'))
                    ->setName($this->trans('Status', [], 'Modules.Gcromo.Admin'))
                    ->setOptions([
                        'field' => 'status_label',
                        'color_field' => 'status_color',
                        'badge_type' => '',
                        'alignment' => 'center',
                    ])
            )
            ->add(
                (new ActionColumn('actions'))
                    ->setName($this->trans('Actions', [], 'Modules.Gcromo.Admin'))
                    ->setOptions([
                        'actions' => $this->getRowActions(),
                    ])
            );

        return $columns;
    }

    protected function getFilters()
    {
        return (new FilterCollection())
            ->add(
                (new Filter('quote_reference', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => [
                            'placeholder' => $this->trans('Reference', [], 'Modules.Gcromo.Admin'),
                        ],
                    ])
                    ->setAssociatedColumn('quote_reference')
            )
            ->add(
                (new Filter('customer_name', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => [
                            'placeholder' => $this->trans('Customer', [], 'Modules.Gcromo.Admin'),
                        ],
                    ])
                    ->setAssociatedColumn('customer_name')
            )
            ->add(
                (new Filter('status', ChoiceType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'choices' => [
                            $this->trans('Draft', [], 'Modules.Gcromo.Admin') => 'draft',
                            $this->trans('Pending review', [], 'Modules.Gcromo.Admin') => 'pending',
                            $this->trans('Approved', [], 'Modules.Gcromo.Admin') => 'approved',
                            $this->trans('Won', [], 'Modules.Gcromo.Admin') => 'won',
                            $this->trans('Lost', [], 'Modules.Gcromo.Admin') => 'lost',
                        ],
                        'placeholder' => $this->trans('All statuses', [], 'Modules.Gcromo.Admin'),
                    ])
                    ->setAssociatedColumn('status')
            )
            ->add(
                (new Filter('sales_rep', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => [
                            'placeholder' => $this->trans('Sales representative', [], 'Modules.Gcromo.Admin'),
                        ],
                    ])
                    ->setAssociatedColumn('sales_rep')
            )
            ->add(
                (new Filter('quote_date', DateRangeType::class))
                    ->setTypeOptions([
                        'required' => false,
                    ])
                    ->setAssociatedColumn('quote_date')
            )
            ->add(
                (new Filter('actions', SearchAndResetType::class))
                    ->setTypeOptions([
                        'reset_route' => 'admin_common_reset_search_by_filter_id',
                        'reset_route_params' => [
                            'filterId' => self::GRID_ID,
                        ],
                        'redirect_route' => 'admin_gcromo_budget_index',
                    ])
                    ->setAssociatedColumn('actions')
            );
    }

    protected function getGridActions(): GridActionCollectionInterface
    {
        return (new GridActionCollection())
            ->add(
                (new SimpleGridAction('common_refresh_list'))
                    ->setName($this->trans('Refresh list', [], 'Admin.Advparameters.Feature'))
                    ->setIcon('refresh')
            );
    }

    private function getRowActions(): RowActionCollection
    {
        return (new RowActionCollection())
            ->add(
                (new LinkRowAction('edit'))
                    ->setName($this->trans('Edit', [], 'Admin.Actions'))
                    ->setIcon('edit')
                    ->setOptions([
                        'route' => 'admin_gcromo_budget_edit',
                        'route_param_name' => 'budgetId',
                        'route_param_field' => 'id_gcromo_budget',
                    ])
            )
            ->add(
                (new LinkRowAction('delete'))
                    ->setName($this->trans('Delete', [], 'Admin.Actions'))
                    ->setIcon('delete')
                    ->setOptions([
                        'route' => 'admin_gcromo_budget_delete',
                        'route_param_name' => 'budgetId',
                        'route_param_field' => 'id_gcromo_budget',
                        'extra_route_params' => [
                            'token' => Tools::getAdminTokenLite('AdminGcromoBudget'),
                        ],
                    ])
            );
    }
}
