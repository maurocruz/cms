<?php
namespace Plinct\Cms\View\Html;

use Plinct\Tool\Locale;

class HtmlViewAbstract {
    protected array $html;
    protected string $siteName;
    protected array $content = [];

    public function __construct() {
        $this->html = [ "tag" => "html", "attributes" => [ "lang" => Locale::getServerLanguage() ] ];
    }

    protected function setSiteName($siteName) {
        $this->siteName = $siteName;
        $this->addHead([ "tag" => "title", "content" => _("Painel CMS [ ".$siteName." ]") ]);
    }
        
    // ADD HEAD
    protected function addHead($content) {
        $this->html['content'][0]['content'][] = $content;
    }
    
    // ADD HEAD
    protected function addBody($content) {
        $this->html['content'][1]['content'][] = $content;
    }

    // ADD HEADER
    protected function addHeader($content, $position = null) {
        if ($position === 1) {
            array_unshift($this->html['content'][1]['content'][0]['content'][0]['content'], $content);
        } else {
            $this->html['content'][1]['content'][0]['content'][0]['content'][] = $content;
        }
    }
    
    // ADD NAVBAR
    public function addNavBar(array $data) {
        $this->addHeader([ "object"=>"navbar", "attributes" => $data['attributes'], "content" => $data['list'], "title" => $data['title'] ?? null, "append" => $data['append'] ?? null ]);
    }
    
    // ADD MAIN
    public function addMain($content) {        
        $this->html['content'][1]['content'][0]['content'][1]['content'][] = $content;
    }
    
    // ADD FOOTER
    protected function addFooter($content) {
        $this->html['content'][1]['content'][0]['content'][2]['content'][] = $content;
    }
}
