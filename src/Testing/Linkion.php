<?php

    namespace Linkion\Testing;

/**
 * remote class for the LinkionTestCase class
 */
class Linkion {

    /**
     * linkion testing var
     * @var LinkionTestCase
     */
    protected static LinkionTestCase $LINKION;

    /**
     * initiate the testing process
     * @param string $component
     * @param array $args
     * @return LinkionTestCase
     */
    public static function test(string $component, array $args = []): LinkionTestCase{
        return (new LinkionTestCase())->test($component, $args);
    }

}