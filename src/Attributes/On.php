<?php

namespace Linkion\Attributes;

use Attribute;
/**
 * Attribute class for Linkion events
 */
#[Attribute(Attribute::TARGET_METHOD)]
class On {       
    public function __construct(public $event){}
}