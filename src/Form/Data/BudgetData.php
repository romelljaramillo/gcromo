<?php

namespace Gcromo\Form\Data;

use DateTimeImmutable;
use DateTimeInterface;

class BudgetData
{
    private ?int $id = null;

    private ?string $quoteReference = null;

    private ?DateTimeInterface $quoteDate = null;

    private ?int $customerId = null;

    private string $customerName = '';

    private ?string $productName = null;

    private ?string $productSummary = null;

    private ?string $workScope = null;

    private ?float $dimensionHeightCm = null;

    private ?float $dimensionWidthPrimaryCm = null;

    private ?float $dimensionWidthSecondaryCm = null;

    private ?string $salesRep = null;

    private string $status = 'draft';

    public static function fromArray(array $data): self
    {
        $budget = new self();
        $budget->id = isset($data['id_gcromo_budget']) ? (int) $data['id_gcromo_budget'] : null;
        $budget->quoteReference = $data['quote_reference'] ?? null;
        $budget->quoteDate = empty($data['quote_date']) ? null : new DateTimeImmutable($data['quote_date']);
        $budget->customerId = isset($data['customer_id']) ? (int) $data['customer_id'] : null;
        $budget->customerName = $data['customer_name'] ?? '';
        $budget->productName = $data['product_name'] ?? null;
        $budget->productSummary = $data['product_summary'] ?? null;
        $budget->workScope = $data['work_scope'] ?? null;
        $budget->dimensionHeightCm = isset($data['dimension_height_cm']) ? (float) $data['dimension_height_cm'] : null;
        $budget->dimensionWidthPrimaryCm = isset($data['dimension_width_primary_cm']) ? (float) $data['dimension_width_primary_cm'] : null;
        $budget->dimensionWidthSecondaryCm = isset($data['dimension_width_secondary_cm']) ? (float) $data['dimension_width_secondary_cm'] : null;
        $budget->salesRep = $data['sales_rep'] ?? null;
        $budget->status = $data['status'] ?? 'draft';

        return $budget;
    }

    public function toDatabaseArray(): array
    {
        return [
            'quote_reference' => $this->quoteReference,
            'quote_date' => $this->quoteDate ? $this->quoteDate->format('Y-m-d') : null,
            'customer_id' => $this->customerId,
            'customer_name' => $this->customerName,
            'product_name' => $this->productName,
            'product_summary' => $this->productSummary,
            'work_scope' => $this->workScope,
            'dimension_height_cm' => $this->dimensionHeightCm,
            'dimension_width_primary_cm' => $this->dimensionWidthPrimaryCm,
            'dimension_width_secondary_cm' => $this->dimensionWidthSecondaryCm,
            'sales_rep' => $this->salesRep,
            'status' => $this->status,
        ];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getQuoteReference(): ?string
    {
        return $this->quoteReference;
    }

    public function setQuoteReference(?string $quoteReference): self
    {
        $this->quoteReference = $quoteReference ?: null;

        return $this;
    }

    public function getQuoteDate(): ?DateTimeInterface
    {
        return $this->quoteDate;
    }

    public function setQuoteDate(?DateTimeInterface $quoteDate): self
    {
        $this->quoteDate = $quoteDate;

        return $this;
    }

    public function getCustomerId(): ?int
    {
        return $this->customerId;
    }

    public function setCustomerId(?int $customerId): self
    {
        $this->customerId = $customerId;

        return $this;
    }

    public function getCustomerName(): string
    {
        return $this->customerName;
    }

    public function setCustomerName(?string $customerName): self
    {
        $this->customerName = $customerName ? (string) $customerName : '';

        return $this;
    }

    public function getProductName(): ?string
    {
        return $this->productName;
    }

    public function setProductName(?string $productName): self
    {
        $this->productName = $productName;

        return $this;
    }

    public function getProductSummary(): ?string
    {
        return $this->productSummary;
    }

    public function setProductSummary(?string $productSummary): self
    {
        $this->productSummary = $productSummary;

        return $this;
    }

    public function getWorkScope(): ?string
    {
        return $this->workScope;
    }

    public function setWorkScope(?string $workScope): self
    {
        $this->workScope = $workScope;

        return $this;
    }

    public function getDimensionHeightCm(): ?float
    {
        return $this->dimensionHeightCm;
    }

    public function setDimensionHeightCm(?float $dimensionHeightCm): self
    {
        $this->dimensionHeightCm = $dimensionHeightCm;

        return $this;
    }

    public function getDimensionWidthPrimaryCm(): ?float
    {
        return $this->dimensionWidthPrimaryCm;
    }

    public function setDimensionWidthPrimaryCm(?float $dimensionWidthPrimaryCm): self
    {
        $this->dimensionWidthPrimaryCm = $dimensionWidthPrimaryCm;

        return $this;
    }

    public function getDimensionWidthSecondaryCm(): ?float
    {
        return $this->dimensionWidthSecondaryCm;
    }

    public function setDimensionWidthSecondaryCm(?float $dimensionWidthSecondaryCm): self
    {
        $this->dimensionWidthSecondaryCm = $dimensionWidthSecondaryCm;

        return $this;
    }

    public function getSalesRep(): ?string
    {
        return $this->salesRep;
    }

    public function setSalesRep(?string $salesRep): self
    {
        $this->salesRep = $salesRep;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }
}
