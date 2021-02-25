<?php

namespace ScraperBot\Source;

/**
 * A source of links (URLs).
 *
 * @package ScraperBot\Source
 */
interface SourceInterface {

    /**
     * Return the list of links derived from this source.
     *
     * @return mixed
     */
    public function getLinks();

    /**
     * Add a link to this source.
     *
     * @param $url
     * @return mixed
     */
    public function addLink($url);
}
