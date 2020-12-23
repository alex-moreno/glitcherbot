<?php

namespace ScraperBot\Source;

interface SourceInterface {

    public function getLinks();

    public function addLink($url);
}
