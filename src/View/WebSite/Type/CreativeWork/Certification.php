<?php
namespace Plinct\Cms\View\WebSite\Type\CreativeWork;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\Thing\Thing;
use Plinct\Cms\View\WebSite\Type\TypeBuilder;
use Plinct\Cms\View\WebSite\Type\TypeViewInterface;

class Certification extends CreativeWorkView implements TypeViewInterface
{

	public function __destruct()
	{
		CmsFactory::view()->addHeader(
			CmsFactory::view()->fragment()->navbar()
				->type('certification')
				->title(_("Certification"))
				->level(3)
				->newTab('/admin/certification',  CmsFactory::view()->fragment()->icon()->home(18,18))
				->newTab('/admin/certification/new',  CmsFactory::view()->fragment()->icon()->plus(18,18))
				->search()
				->ready()
		);
	}

	/**
	 * @param array|null $data
	 * @param array|null $queryParams
	 * @return void
	 */
	public function index(?array $data, array $queryParams = null): void
	{
		CmsFactory::view()->addMain(
			CmsFactory::view()->fragment()->reactShell('certification')->setColumnsTable(['alternateName'=>_('Alternate name'),'certificationIdentification'=>_("Certification identification")])->ready()
		);
	}

	/**
	 * @param array|null $data
	 * @param array|null $queryParams
	 * @return void
	 */
	public function new(?array $data, array $queryParams = null): void
	{
		CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->simpleBox($this->form(), _("Add new")));
	}

	/**
	 * @param array|null $data
	 * @param array|null $queryParams
	 * @return void
	 */
	public function edit(?array $data, array $queryParams = null): void
	{
		if (!empty($data)) {
			$value = $data[0];
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->expandingBox(_("Certification"),$this->form('edit', $value), true));
		} else {
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->noContent(sprintf(_("No %s were found!"), _('certification'))));
		}
	}

	/**
	 * @param array|null $data
	 * @return array
	 */
	public static function hasCertification(array $data = null): array
	{
		$type = $data['@type'];
		$typeBuilder = new TypeBuilder($type,$data);
		$idType = $typeBuilder->getPropertyValue('idthing');
		$content = [];
		if (isset($data['hasCertification']) && is_array($data['hasCertification'])) {
			foreach ($data['hasCertification'] as $value) {
				$content[] = CmsFactory::view()->fragment()->box()->expandingBox($value['name'], (new Certification)->form('edit', $value), false, 'margin: 3px 5px;');
			}
		}
		$content[] = CmsFactory::view()->fragment()->box()->expandingBox(_("Add new")." "._("certification"), (new Certification)->form('new', null, (int) $idType), false, 'margin: 3px 5px;');
		return $content;
	}

	protected function form(string $case = "new", array $value = null, int $about = null): array
	{
		$about = $about ?? $value['about'] ?? null;
		$issuedBy = $value['issuedBy'] ?? null;
		$certificationStatus = $value['certificationStatus'] ?? null;
		$datePublished = isset($value['datePublished']) ? substr($value['datePublished'],0,10) : null;
		$expires = isset($value['expires']) ? substr($value['expires'],0,10) : null;
		$certificationIdentification = $value['certificationIdentification'] ?? null;
		// FORM
		$form = CmsFactory::view()->fragment()->form("form-certification",['class'=>'form-basic form-certification']);
		$form->action("/admin/certification/$case")->method('post');
		$form->addMandatories('issuedBy');
		if ($case == 'edit') {
			$typeBuilder = new TypeBuilder('certification', $value);
			$idcertification = $typeBuilder->getId();
			$form->input('idcertification',(string) $idcertification, 'hidden');
		} elseif($about) {
			$form->input('action','redirectToSamePage', 'hidden');
		}
		// THING
		$form = Thing::formContent($form, $value);
		// certificationIdentification
		$form->fieldsetWithInput('certificationIdentification', $certificationIdentification, _('Certification identification'));
		// about
		$form->relationshipOneToOne('thing', _("About"), 'about', $about);
		// issuedBy
		$form->relationshipOneToOne('organization', _("Issued by"), 'issuedBy', $issuedBy);
		// certification status
		$form->fieldsetWithRadio('certificationStatus',['0'=>_('inactive'), '1'=>_('active')], $certificationStatus, _("Certification status"));
		// datePublished
		$form->fieldsetWithInput('datePublished', $datePublished, _("Date published"), 'date');
		// expíres
		$form->fieldsetWithInput('expires', $expires, _("Date expires"), 'date');
		// submit
		$form->submitButtonSend();
		if ($case === 'edit') {
			$form->submitButtonDelete('/admin/certification/delete');
		}
		return $form->ready();
	}
}