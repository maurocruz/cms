<?php
namespace Plinct\Cms\View\Html;

use Locale;

class HtmlViewAbstract {
    protected $settings;
    protected $html;
    protected $language;
    protected $siteName;
    protected $content = [];

    protected $userName;
    protected $userAdmin;

    public function __construct() {
        // language
        $this->setLanguage();
        // set html
        $this->html = [ "tag" => "html", "attributes" => [ "lang" => $this->language ] ];
    }
    
    private function setLanguage() {
        $this->language = (new Locale())->acceptFromHttp(filter_input(INPUT_SERVER, 'HTTP_ACCEPT_LANGUAGE'));
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
