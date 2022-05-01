<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Fragment\Miscellaneous;

use Plinct\Cms\App;
use Plinct\Cms\WebSite\Fragment\Fragment;

class Miscellaneous implements MiscellaneousInterface
{
    /**
     * @param string $message
     * @param array|string[] $attributes
     * @return array
     */
    public function message(string $message = "No content", array $attributes = ['class'=>'warning']): array
    {
        return ['tag'=>'p','attributes'=>$attributes,'content'=>_($message)];
    }

    /**
     * @param $data
     * @return array
     */
    public function sitemap($data): array
    {
        $div = null;
        foreach ($data as $valueRow) {
					if (is_string($valueRow['type'])) {
						// vars
						$type = $valueRow['type'];
						$file = $valueRow['file'];
						$errors = $valueRow['errors'];
						$errorText = $errors ? ' <span style="color: red;">ERROR!</span> ' : NULL;
						$extension = $valueRow['extension'] ?? "simple";
						$link = $file ? sprintf('<a href="%s/%s" target="_blank">%s</a>%s', App::getURL(), $file, $file, $errorText) : null;
						$text = $file ? "Update sitemap" : "Create sitemap";
						// form
						$form = Fragment::form(['class' => 'formPadrao form-sitemap']);
						$form->action("/admin/" . lcfirst($type) . "/sitemap")->method('post');
						$form->content("<p style='display: inline-block;'>" . _("Type") . ": " . $extension . "</p>");
						$form->content("<button style='margin-left: 5px; height: 30px;'>" . _($text) . "</button>");
						if ($errors) $form->content("<p style='color: red; background-color: black; padding: 7px 12px; font-weight: bold; text-align: center; display: inline-block;'>" . ($errors[0])->message . "</p>");
						// box items
						$div[] = Fragment::box()->expandingBox(sprintf('%s %s', _($type), $link), $form->ready());
					}
        }
        return Fragment::box()->simpleBox($div, _("Types"));
    }
}