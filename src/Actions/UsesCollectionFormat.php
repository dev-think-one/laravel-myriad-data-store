<?php

namespace MyriadDataStore\Actions;

trait UsesCollectionFormat
{
    protected function collectionFormat()
    {
        if (property_exists($this, 'collectionFormat')
            && is_array(static::$collectionFormat)
        ) {
            return static::$collectionFormat;
        }

        return $this->defaultCollectionFormat();
    }

    abstract protected function defaultCollectionFormat(): array;
}
