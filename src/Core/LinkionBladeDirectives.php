<?php

namespace Linkion\Core;

use Illuminate\Support\Facades\Blade;

/**
 * class for setting up Blade directives for the linkion components
 */
class LinkionBladeDirectives {



    public static function setup(){
        // linkion component directive @lnknComponent @endlnknComponent
        Blade::directive('lnknComponent', function(){
            return '<div lnkn-id="' . '<?= $_id ?>" lnkn-data="' . '<?= $_data ?>" >' ;
        });

        Blade::directive('endlnknComponent', function () {
            return "<?= '</div>' ?>";
        });

        // linkion asset script <script @lnknAsset ></script>
        Blade::directive('lnknAsset', function(){
            return 'lnkn-asset="' . '<?= $componentName ?>"' ;
        });

        // linkion script <script @lnknScript ></script>
        Blade::directive('lnknScript', function(){
            return 'lnkn-script="' . '<?= $_id ?>"' ;
        });
    }

}