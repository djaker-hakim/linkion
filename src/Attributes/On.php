<?php

namespace Linkion\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class On {

        
    public function __construct(public $event)
    {
        
    }

}