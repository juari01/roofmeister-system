<?php

class Content {

    public function get_pagination( $count, $index ) {
       
        // Build the page navigation
        $total_pages = ceil( $count / 20 );
        if( $total_pages == 1 ) {
            $page_nav = "[1]";
        } else {
            $start = floor( $index / 10 ) * 10;
            $end = $start + 10;
            if( $end > $total_pages ) {
                $end = $total_pages + 1;
            }
            $page_nav    = "";
            if( $index > 1 ) {
                $page_nav .= "<span class=\"nav-link\" data-function=\"view-page\" data-page-num=\"1\">|&lt;&lt;</span>&nbsp;";
                $page_nav .= "<span class=\"nav-link\" data-function=\"view-page\" data-page-num=\"" . ( $index - 1 ) . "\">&lt;</span>&nbsp;";
            }
            if( $index > 10 ) {
                $page_nav .= "<span class=\"nav-link\" data-function=\"view-page\" data-page-num=\"" . ( $index - 10 ) . "\">...</span>&nbsp;";
            }
            for( $i = $start; $i < $end; ++$i ) {
                if( $i == 0 ) {

                } elseif( $i == $index ) {
                    $page_nav .= "[$i]\n";
                } else {
                    $page_nav .= "<span class=\"nav-link\" data-function=\"view-page\" data-page-num=\"$i\">$i</span>&nbsp;";
                }
            }
            if( $index < ( $total_pages - 10 )) {
                $page_nav .= "<span class=\"nav-link\" data-function=\"view-page\" data-page-num=\"" . ( $index + 10 ) . "\">...</span>&nbsp;";
            }
            if( $index < $total_pages ) {
                $page_nav .= "<span class=\"nav-link\" data-function=\"view-page\" data-page-num=\"" . ( $index + 1 ) . "\">&gt;</span>&nbsp;";
                $page_nav .= "<span class=\"nav-link\" data-function=\"view-page\" data-page-num=\"" . ( $total_pages ) . "\">&gt;&gt;|</span>&nbsp;";
            }
        }

        return $page_nav;
    }

}

?>
