<?php

namespace Plinct\Web\Widget;

class Navigation
{
    public function PageNavigation($numberOfItems, $limit, $offset, $attributes = null): ?array
    {
        $npages = ceil($numberOfItems/$limit);
        $search = filter_input(INPUT_GET, 'search') ?? null;
        $searchQuery = $search ? "&search=".$search : null;

        // BACKWARD BUTTON
        $styleBackwardButton = $offset < 1 ? "visibility: hidden;" : null;
        $backward = [ "tag" => "button", "attributes" => [ "style" => $styleBackwardButton ], "content" => '<<', "href" => "?offset=".($offset-$limit).$searchQuery];

        // PAGE BUTTONS
        for ($p = 0; $p < $npages; $p++) {
            $stylePageButton = $p * $limit == $offset ? "background-color: rgba(255,255,255,0.5);" : null;
            $buttonsPage[] = [ "tag" => "a", "attributes" => [ "style" => $stylePageButton , "href" => "?offset=".($p*$limit).$searchQuery ], "content" => (1+$p) ];
        }

        // FORWARD BUTTON
        $styleForwardButton = ($numberOfItems >= $limit && $numberOfItems > ($offset+$limit)) ? null : "visibility: hidden;";
        $forward = [ "tag" => "button", "attributes" => [ "style" => $styleForwardButton ], "content" => '>>', "href" => "?offset=".($offset+$limit).$searchQuery ];

        return $numberOfItems > $limit ? [ "tag" => "div", "attributes" => $attributes, "content" => [ $backward, [ "tag" => "div", "content" => $buttonsPage ], $forward ] ] : null;
    }
}