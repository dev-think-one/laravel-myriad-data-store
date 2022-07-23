<?php

namespace MyriadDataStore;

class MyriadDataDownloader
{
    /**
     * Indicates if laravel should run migrations for package.
     *
     * @var bool
     */
    public static bool $runsMigrations = true;

    /**
     * UsedContactModel.
     *
     * @var string
     */
    public static string $contactModel = \MyriadDataStore\Models\MyriadContact::class;

    /**
     * Configure laravel to not register current package migrations.
     *
     * @return static
     */
    public static function ignoreMigrations(): static
    {
        static::$runsMigrations = false;

        return new static;
    }

    /**
     * Specify contact model to use.
     *
     * @param  string  $modelClass
     * @return static
     */
    public static function useContactModel(string $modelClass): static
    {
        static::$contactModel = $modelClass;

        return new static;
    }
}
