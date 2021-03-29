<?php
namespace Plinct\Cms\View\Page;

use Plinct\Cms\App;

class IndexView {

    public function view($data = null): array {
        $content['main'] = [ "tag" => "p", "content" => "Control Panel CMSCruz - version " . App::getVersion() ];
        return $content;
    }

}