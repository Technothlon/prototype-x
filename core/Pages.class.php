<?php
if(!defined('xDEC')) exit;
//TODO  re-consider this class too
class Pages {
    private function start_head(){
        echo '<!DOCTYPE html>
              <html>
                <head>
                    <base href="'.get('home_url').'">
                    <title>';
                        get('Router')->title();
              echo '</title>';
        get('Extensions')->head_start();
    }

    private function build_head(){
        get('Router')->head();
    }

    private function end_head(){
        get('Extensions')->head_end();
    }

    private function start_body(){
        echo '<body>';
        get('Extensions')->body_start();
    }

    private function build_body(){
        get('Router')->body();
    }

    private function end_body(){
        get('Extensions')->body_end();
        echo '</body></html>';
    }

    function get(){
        if(get('Router')->navigate() == null){

            $this->start_head();
            $this->build_head();
            $this->end_head();

            $this->start_body();
            $this->build_body();
            $this->end_body();
        } else {
            $this->build_body();
        }
    }
}