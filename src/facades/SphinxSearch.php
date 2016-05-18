<?php namespace WNeuteboom\SphinxSearch\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * This is the flysystem facade class.
 */
class SphinxSearch extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'sphinxsearch';
    }

}
