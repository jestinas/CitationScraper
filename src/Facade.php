<?php
namespace Sturt\Citationscraper;

class Facade extends \Illuminate\Support\Facades\Facade
{
    /**
     * {@inheritDoc}
     */
    protected static function getFacadeAccessor()
    {
        return 'citationscraper';
    }
}
