<?php

class Offsprout_Debug{
    static function write_debug( $print, $overwrite = false ){
        if( ! OCB_TESTING )
            return;

        $file = OCB_DIR . 'admin/debug/debug.txt';
        $current = file_get_contents($file);
        ob_start();
        print_r( $print );
        if( $overwrite ){
            $current = ob_get_clean();
            $current .= "\n";
        } else {
            $current .= ob_get_clean();
            $current .= "\n";
        }
        file_put_contents($file, $current);
    }
}
function ocb_var_dump( $something ){
    ob_start();
    echo '<pre>';
    var_dump( $something );
    echo '</pre>';
    echo ob_get_clean();
}