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

    private const STATUS_BADGE_COLORS = [
        'draft' => '#6c757d',
        'pending' => '#ffc107',
        'approved' => '#17a2b8',
        'won' => '#28a745',
        'lost' => '#dc3545',
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

    /**
     * Returns a mapping between status value and CSS background colors.
     *
     * @return array<string, string>
     */
    public static function badgeColors(): array
    {
        return self::STATUS_BADGE_COLORS;
    }
}
