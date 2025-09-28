<?php

namespace Gcromo\Form\Data;

class ConfigurationData
{
    private string $referencePrefix = 'GC';

    private string $defaultStatus = 'draft';

    private ?string $defaultSalesRep = null;

    public static function fromConfiguration(): self
    {
        $data = new self();
        $data->referencePrefix = (string) \Configuration::get('GCROMO_REFERENCE_PREFIX', null, null, null, 'GC');
        $data->defaultStatus = (string) \Configuration::get('GCROMO_DEFAULT_STATUS', null, null, null, 'draft');
        $data->defaultSalesRep = \Configuration::get('GCROMO_DEFAULT_SALES_REP') ?: null;

        return $data;
    }

    public function toConfigurationArray(): array
    {
        return [
            'GCROMO_REFERENCE_PREFIX' => $this->referencePrefix,
            'GCROMO_DEFAULT_STATUS' => $this->defaultStatus,
            'GCROMO_DEFAULT_SALES_REP' => $this->defaultSalesRep,
        ];
    }

    public function getReferencePrefix(): string
    {
        return $this->referencePrefix;
    }

    public function setReferencePrefix(string $referencePrefix): self
    {
        $this->referencePrefix = $referencePrefix;

        return $this;
    }

    public function getDefaultStatus(): string
    {
        return $this->defaultStatus;
    }

    public function setDefaultStatus(string $defaultStatus): self
    {
        $this->defaultStatus = $defaultStatus;

        return $this;
    }

    public function getDefaultSalesRep(): ?string
    {
        return $this->defaultSalesRep;
    }

    public function setDefaultSalesRep(?string $defaultSalesRep): self
    {
        $this->defaultSalesRep = $defaultSalesRep;

        return $this;
    }
}
