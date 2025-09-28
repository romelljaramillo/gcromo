<?php

namespace Gcromo\Provider;

final class BudgetStatusProvider
{
    public const STATUSES = [
        'draft' => 'Draft',
        'pending' => 'Pending review',
        'approved' => 'Approved',
        'won' => 'Won',
        'lost' => 'Lost',
    ];

    private const STATUS_BADGE_CLASSES = [
        'draft' => 'badge-secondary',
        'pending' => 'badge-warning',
        'approved' => 'badge-info',
        'won' => 'badge-success',
        'lost' => 'badge-danger',
    ];

    /**
     * Returns a choices array compatible with Symfony ChoiceType.
     *
     * @return array<string, string>
     */
    public static function choices(): array
    {
        $choices = [];
        foreach (self::STATUSES as $value => $label) {
            $choices[$label] = $value;
        }

        return $choices;
    }

    /**
     * Returns a mapping between status value and Bootstrap badge classes.
     *
     * @return array<string, string>
     */
    public static function badgeClasses(): array
    {
        return self::STATUS_BADGE_CLASSES;
    }
}
