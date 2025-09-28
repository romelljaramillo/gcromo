<?php

namespace Gcromo\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Gcromo\Form\Data\BudgetData;
use Tools;

class BudgetRepository
{
    private Connection $connection;

    private string $tableName;

    public function __construct(Connection $connection, string $dbPrefix)
    {
        $this->connection = $connection;
        $this->tableName = sprintf('%sgcromo_budget', $dbPrefix);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function findAll(): array
    {
        $sql = sprintf('SELECT * FROM `%s` ORDER BY `created_at` DESC', $this->tableName);

        return $this->connection->fetchAllAssociative($sql);
    }

    public function find(int $budgetId): ?array
    {
        $sql = sprintf('SELECT * FROM `%s` WHERE `id_gcromo_budget` = :id LIMIT 1', $this->tableName);
        $result = $this->connection->fetchAssociative($sql, ['id' => $budgetId], ['id' => ParameterType::INTEGER]);

        return $result ?: null;
    }

    public function save(BudgetData $budget): int
    {
        $payload = $budget->toDatabaseArray();

        if (empty($payload['quote_reference'])) {
            $payload['quote_reference'] = $this->generateReference(
                \Configuration::get('GCROMO_REFERENCE_PREFIX', null, null, null, 'GC')
            );
            $budget->setQuoteReference($payload['quote_reference']);
        }

        if (empty($payload['customer_name']) && $budget->getCustomerId()) {
            $payload['customer_name'] = $this->fetchCustomerName($budget->getCustomerId());
        }

        if ($budget->getId()) {
            $this->connection->update(
                $this->tableName,
                $payload,
                ['id_gcromo_budget' => $budget->getId()],
                [
                    'quote_date' => ParameterType::STRING,
                    'customer_id' => ParameterType::INTEGER,
                    'customer_name' => ParameterType::STRING,
                    'product_name' => ParameterType::STRING,
                    'product_summary' => ParameterType::STRING,
                    'work_scope' => ParameterType::STRING,
                    'dimension_height_cm' => ParameterType::STRING,
                    'dimension_width_primary_cm' => ParameterType::STRING,
                    'dimension_width_secondary_cm' => ParameterType::STRING,
                    'sales_rep' => ParameterType::STRING,
                    'status' => ParameterType::STRING,
                ]
            );

            return (int) $budget->getId();
        }

        $this->connection->insert(
            $this->tableName,
            $payload,
            [
                'quote_date' => ParameterType::STRING,
                'customer_id' => ParameterType::INTEGER,
                'customer_name' => ParameterType::STRING,
                'product_name' => ParameterType::STRING,
                'product_summary' => ParameterType::STRING,
                'work_scope' => ParameterType::STRING,
                'dimension_height_cm' => ParameterType::STRING,
                'dimension_width_primary_cm' => ParameterType::STRING,
                'dimension_width_secondary_cm' => ParameterType::STRING,
                'sales_rep' => ParameterType::STRING,
                'status' => ParameterType::STRING,
            ]
        );

        return (int) $this->connection->lastInsertId();
    }

    public function delete(int $budgetId): bool
    {
        return (bool) $this->connection->delete(
            $this->tableName,
            ['id_gcromo_budget' => $budgetId],
            ['id_gcromo_budget' => ParameterType::INTEGER]
        );
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function findCustomerSummary(): array
    {
        $sql = sprintf(
            'SELECT customer_id, customer_name, COUNT(*) AS total_budgets, MAX(updated_at) AS last_updated
            FROM `%s`
            WHERE customer_id IS NOT NULL
            GROUP BY customer_id, customer_name
            ORDER BY last_updated DESC',
            $this->tableName
        );

        return $this->connection->fetchAllAssociative($sql);
    }

    public function generateReference(string $prefix): string
    {
        $prefix = strtoupper(trim($prefix)) ?: 'GC';

        do {
            $reference = sprintf('%s-%s', $prefix, strtoupper(Tools::passwdGen(6, 'ALPHANUM')));
            $exists = $this->connection->fetchOne(
                sprintf('SELECT 1 FROM `%s` WHERE `quote_reference` = :reference LIMIT 1', $this->tableName),
                ['reference' => $reference]
            );
        } while ($exists);

        return $reference;
    }

    private function fetchCustomerName(int $customerId): string
    {
        if ($customerId <= 0) {
            return '';
        }

    $table = sprintf('%scustomer', _DB_PREFIX_);
    $sql = sprintf('SELECT CONCAT(firstname, " ", lastname) FROM `%s` WHERE id_customer = :id LIMIT 1', $table);
    $name = $this->connection->fetchOne($sql, ['id' => $customerId], ['id' => ParameterType::INTEGER]);

        return $name ? (string) $name : '';
    }
}
