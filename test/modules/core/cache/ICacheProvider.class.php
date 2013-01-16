<?php

/**
 * 
 * @version 0.2.20111204
 * @author Zhiji Gu <gu_zhiji@163.com>
 * @copyright &copy; 2010-2013 InterBox Core 1.2 for PHP, GuZhiji Studio
 * @package interbox.core.cache
 */
interface ICacheProvider {

    /**
     *  get a cache editor object.
     * 
     * @param string $group
     */
    public function GetEditor($group);

    /**
     * get a cache reader object.
     * 
     * @param string $group 
     */
    public function GetReader($group);
}