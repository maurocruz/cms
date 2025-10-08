<?php
namespace Plinct\Cms\Response\Fragment\Button;

use Plinct\Cms\CmsFactory;

class Button
{
    /**
     * @param string|int $idIsPartOf
     * @param string $tableISPartOf
     * @param string|int|null $idHasPart
     * @param string|null $tableHasPart
     * @param array|null $attributes
     * @return array
     */
    public function buttonDelete(string|int $idIsPartOf, string $tableISPartOf, string|int $idHasPart = null, string $tableHasPart = null, array $attributes = null): array
    {
        $form = CmsFactory::response()->fragment()->form($attributes);
        $form->action("/admin/$tableISPartOf/erase")->method('post');
        $form->input('idIsPartOf', $idIsPartOf, 'hidden')
            ->input('tableIsPartOf', $tableISPartOf, 'hidden')
            ->input('idHasPart', $idHasPart, 'hidden')
            ->input('tableHasPart', $tableHasPart, 'hidden');
        $form->content("<button type='submit' class='button-submit' onclick='return confirm(\"" . _("Do you really want to delete this item?") . "\")'>"
                . CmsFactory::response()->fragment()->icon()->delete()
            ."</button>");

        return $form->ready();
    }
}
