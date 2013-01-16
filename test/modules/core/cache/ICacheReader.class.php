<?php

/**
 * a standard interface for accessing a cache file
 * 
 * @version 0.3.20120220
 * @author Zhiji Gu <gu_zhiji@163.com>
 * @license MIT License
 * @copyright &copy; 2010-2013 InterBox Core 1.2 for PHP, GuZhiji Studio
 * @package interbox.core.cache
 */
interface ICacheReader {

    /**
     * set a function for refreshing cache
     * 
     * @param callback $function function name, without parameters
     */
    public function SetRefreshFunction($function);

    /**
     * fetch a value with a key and may refresh the cache if it has expired
     * 
     * @param string $key
     * @param int $version  the known latest version number to be compared with
     * the version number stored with data, typically using values returned by 
     * the php function time()
     * @param int $randomfactor     a parameter to control overall number of
     *  refreshes by generating random numbers
     * <ul>
     * <li>=0 - do not refresh data, use the old version</li>
     * <li>=1 - refresh data if necessary</li>
     * <li>&gt;1 - refresh data at the possibility of 1/randomfactor</li>
     * </ul>
     * @return mixed 
     */
    public function GetValue($key, $version = 0, $randomfactor = 1);

    /**
     * get an array of all available keys
     */
    public function GetKeys();
}
