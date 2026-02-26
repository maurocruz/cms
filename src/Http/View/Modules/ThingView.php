<?php
namespace Plinct\Cms\Http\View\Modules;

use Plinct\Cms\Http\View\Abstracts\ModuleViewAbstract;
use Plinct\Cms\Http\View\Component\Form\Form;
use Plinct\Cms\Support\Support;

class ThingView extends ModuleViewAbstract
{
	protected string $type;
	protected ?string $idthing = null;

	public function build(array $data = null): void
	{
		// TODO: Implement build() method.
	}

	/**
	 * @param string $idthing
	 */
	public function setIdthing(string $idthing): void
	{
		$this->idthing = $idthing;
	}

	/**
	 * @param string $type
	 */
	public function setType(string $type): void
	{
		$this->type = $type;
	}

	protected function formThing(Form $form, array $value = null, string $nameOfName = null, array $excludes = []): Form
	{
		$case = 'new';
		$idthing = null;
		$type = $value['@type'] ?? null;
		$name = $value['name'] ?? null;
		$alternateName = $value['alternateName'] ?? null;
		$description = $value['description'] ?? null;
		$disambiguatingDescription = $value['disambiguatingDescription'] ?? null;
		$apihost = $this->getContext()->getApiHost();
		$url = $value['url'] ?? null;
		$image = $value['image'] ?? null;
		if ($value) {
			$typeBuilder = Support::typeBuilder($value);
			$idthing = $typeBuilder->getPropertyValue('idthing') ?? null;
			$dateRegistered = $typeBuilder->getPropertyValue('dateRegistered') ?? null;
			$lastModified = $typeBuilder->getPropertyValue('lastModified') ?? null;
			$case = 'edit';
		}
		// Expandig box
		$form->content($this->box()->expandingBoxWithoutContent(_("Thing"), "form-thing", true));

		if (!$form->getIdform()) {
			$form->setIdform("form-".($type ?? "type")."-".($idthing ?? 'new'));
		}
		$form->addMandatories(['name']);
		// CONTENT

		$form->content("<div class='form-thing-extract'>");
		$form->content("<p class='form-thing-extract-type'>$type</p>");
		// name
		$form->fieldsetWithInput('name', $name, $nameOfName ?? _('Name'));
		// alternateName
		if (!in_array('alternateName', $excludes)) {
			$form->fieldsetWithInput('alternateName', $alternateName, _('Alternate name'));
		}
		// disambiguatingDescription
		if (!in_array('disambiguatingDescription', $excludes)) {
			$form->fieldsetWithTextarea('disambiguatingDescription', $disambiguatingDescription, _('Short description for disambiguating'),['class'=>'thing-disambiguatingDescription']);
		}
		// description
		$form->content(
			$this->box()->expandingBox(_('Description'),"<textarea name='description' class='thing-description' id='description$idthing' style='min-height: 300px;'>$description</textarea>", false,'width: 100%;')
		);
		$form->setEditor("description$idthing", "editor$case$idthing");
		// url
		$form->fieldsetWithInput('url', $url, _('url'));
		// image url
		$form->fieldsetWithInput('image',$image, _('Image path'));
		// additionalType
		$form->content("<div class='plinct-shell' data-property='additionalType' data-idhaspart='$this->idthing' data-apihost='$apihost'></div>");
		// attachments
		if (!$value) {
			$accept = match ($this->type) {
				"audioObject" => "audio/*",
				"imageObject" => "image/*",
				"videoObject" => "video/*",
				default => null
			};
			$form->fieldsetWithInput('uploadfile[]', null, _('Attachment'), 'file', null, ['accept' => $accept]);
		} else {
			// date registered
			$form->fieldsetWithInput("dateRegistered", $dateRegistered, _("Date registered"), "datetime-local", null, ["disabled"]);
			// last modified
			$form->fieldsetWithInput("lastModified", $lastModified, _("Last modified"), "datetime-local", null, ["disabled"]);
		}
		// end form-thing-extract
		$form->content("</div>");

		// end expanding box
		$form->content("</div>");
		//
		return $form;
	}
}
