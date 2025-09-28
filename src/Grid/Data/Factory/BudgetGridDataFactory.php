<?php

namespace Gcromo\Grid\Data\Factory;

use Gcromo\Provider\BudgetStatusProvider;
use PrestaShop\PrestaShop\Core\Grid\Data\Factory\GridDataFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class BudgetGridDataFactory implements GridDataFactoryInterface
{

    private GridDataFactoryInterface $dataFactory;
    private TranslatorInterface $translator;

    public function __construct(GridDataFactoryInterface $dataFactory, TranslatorInterface $translator)
    {
        $this->dataFactory = $dataFactory;
        $this->translator = $translator;
    }

    public function getData(SearchCriteriaInterface $searchCriteria): GridData
    {
        $data = $this->dataFactory->getData($searchCriteria);

        $records = $data->getRecords()->all();
        $statusColors = BudgetStatusProvider::badgeColors();

        foreach ($records as &$record) {
            $statusKey = $record['status'] ?? 'draft';
            if (!is_string($statusKey) || $statusKey === '') {
                $statusKey = 'draft';
            }

            $statusKey = strtolower($statusKey);

            $label = BudgetStatusProvider::STATUSES[$statusKey] ?? ucfirst($statusKey);
            $record['status_label'] = $this->translator->trans($label, [], 'Modules.Gcromo.Admin');
            $record['status_color'] = $statusColors[$statusKey] ?? $statusColors['draft'];

            $record['dimension_height_cm'] = $this->formatDecimal($record['dimension_height_cm'] ?? null);
            $record['dimension_width_primary_cm'] = $this->formatDecimal($record['dimension_width_primary_cm'] ?? null);
            $record['dimension_width_secondary_cm'] = $this->formatDecimal($record['dimension_width_secondary_cm'] ?? null);

            if (empty($record['customer_name'])) {
                $record['customer_name'] = $this->translator->trans('Guest', [], 'Admin.Global');
            }

            if (empty($record['product_name'])) {
                $record['product_name'] = '—';
            }

            if (empty($record['work_scope'])) {
                $record['work_scope'] = '—';
            }

            if (empty($record['sales_rep'])) {
                $record['sales_rep'] = '—';
            }
        }
        unset($record);

        return new GridData(
            new RecordCollection($records),
            $data->getRecordsTotal(),
            $data->getQuery()
        );
    }

    private function formatDecimal($value): string
    {
        if (null === $value || $value === '') {
            return '—';
        }

        return number_format((float) $value, 2, '.', ',');
    }
}
