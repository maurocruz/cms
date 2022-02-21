<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Fragment\Button;

use Plinct\Cms\WebSite\Fragment\Fragment;

class Button
{
    /**
     * @param string $idIsPartOf
     * @param string $tableISPartOf
     * @param string|null $idHasPart
     * @param string|null $tableHasPart
     * @param array|null $attributes
     * @return array
     */
    public function buttonDelete(string $idIsPartOf, string $tableISPartOf, string $idHasPart = null, string $tableHasPart = null, array $attributes = null): array
    {
        $form = Fragment::form($attributes);
        $form->action("/admin/$tableISPartOf/erase")->method('post');
        $form->input('idIsPartOf', $idIsPartOf, 'hidden')
            ->input('tableIsPartOf', $tableISPartOf, 'hidden')
            ->input('idHasPart', $idHasPart, 'hidden')
            ->input('tableHasPart', $tableHasPart, 'hidden');
        $form->content("<button type='submit' class='button-submit' onclick='return confirm(\"" . _("Do you really want to delete this item?") . "\")'>"
                . Fragment::icon()->delete()
            ."</button>");

        return $form->ready();
    }
}
