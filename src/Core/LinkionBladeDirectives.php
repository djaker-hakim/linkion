<?php

namespace Linkion\Core;

use Illuminate\Support\Facades\Blade;

class LinkionBladeDirectives {



    public static function setup(){
        // linkion component directive @lnknComponent @endlnknComponent
        Blade::directive('lnknComponent', function(){
            return '<div lnkn-id=' . '<?php echo "$_id" ?> lnkn-data=' . '<?php echo "$_data" ?> >' ;
        });

        Blade::directive('endlnknComponent', function () {
            return "<?php echo '</div>' ?>";
        });

        // linkion asset script <script @lnknAsset ></script>
        Blade::directive('lnknAsset', function(){
            return 'lnkn-asset=' . '<?php echo "$componentName" ?>' ;
        });

        // linkion script <script @lnknScript ></script>
        Blade::directive('lnknScript', function(){
            return 'lnkn-script=' . '<?php echo "$_id" ?>' ;
        });

        // linkion core js script @linkionScripts
        Blade::directive('linkionScripts', function($withAlpine){
            $csrf = csrf_token();
            
            if($withAlpine == 'true' || $withAlpine == ""){
                $src = '/linkionWithAlpine/script';
            }else if($withAlpine == 'false'){
                $src = '/linkion/script';
            }else{
                $src='';
            }
            return "<script defer data-token=\"$csrf\" src=\"$src\"></script>" ;
        });
    }

}