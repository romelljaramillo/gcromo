<?php

namespace Gcromo\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\AbstractDoctrineQueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineSearchCriteriaApplicator;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

final class BudgetQueryBuilder extends AbstractDoctrineQueryBuilder
{
    private DoctrineSearchCriteriaApplicator $searchCriteriaApplicator;

    public function __construct(
        Connection $connection,
        string $dbPrefix,
        DoctrineSearchCriteriaApplicator $searchCriteriaApplicator
    ) {
        parent::__construct($connection, $dbPrefix);

        $this->searchCriteriaApplicator = $searchCriteriaApplicator;
    }

    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->connection->createQueryBuilder()
            ->from($this->dbPrefix . 'gcromo_budget', 'b')
            ->select(
                'b.id_gcromo_budget',
                'b.quote_reference',
                'b.quote_date',
                'b.customer_id',
                'b.customer_name',
                'b.product_name',
                'b.product_summary',
                'b.work_scope',
                'b.dimension_height_cm',
                'b.dimension_width_primary_cm',
                'b.dimension_width_secondary_cm',
                'b.sales_rep',
                'b.status',
                'b.created_at',
                'b.updated_at'
            );

        $this->applyFilters($searchCriteria->getFilters(), $qb);

        $this->searchCriteriaApplicator
            ->applyPagination($searchCriteria, $qb)
            ->applySorting($searchCriteria, $qb, 'b.id_gcromo_budget', 'desc');

        return $qb;
    }

    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('COUNT(b.id_gcromo_budget)')
            ->from($this->dbPrefix . 'gcromo_budget', 'b');

        $this->applyFilters($searchCriteria->getFilters(), $qb);

        return $qb;
    }

    private function applyFilters(array $filters, QueryBuilder $qb): void
    {
        foreach ($filters as $filterName => $filterValue) {
            if (null === $filterValue || '' === $filterValue) {
                continue;
            }

            switch ($filterName) {
                case 'quote_reference':
                case 'customer_name':
                case 'sales_rep':
                    if (!is_string($filterValue)) {
                        break;
                    }

                    $value = trim($filterValue);
                    if ($value === '') {
                        break;
                    }

                    $qb->andWhere(sprintf('b.%s LIKE :%s', $filterName, $filterName));
                    $qb->setParameter($filterName, '%' . $value . '%');
                    break;
                case 'status':
                    if (!is_string($filterValue) || $filterValue === '') {
                        break;
                    }

                    $qb->andWhere('b.status = :status');
                    $qb->setParameter('status', $filterValue, Types::STRING);
                    break;
                case 'quote_date':
                    if (is_array($filterValue)) {
                        if (!empty($filterValue['from'])) {
                            $qb->andWhere('b.quote_date >= :quote_date_from');
                            $qb->setParameter('quote_date_from', $filterValue['from'], Types::DATE_MUTABLE);
                        }
                        if (!empty($filterValue['to'])) {
                            $qb->andWhere('b.quote_date <= :quote_date_to');
                            $qb->setParameter('quote_date_to', $filterValue['to'], Types::DATE_MUTABLE);
                        }
                    }
                    break;
                default:
                    break;
            }
        }
    }
}
