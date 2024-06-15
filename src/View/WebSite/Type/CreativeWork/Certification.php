<?php
declare(strict_types=1);
namespace Plinct\Cms\View\WebSite\Type\CreativeWork;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\Thing\Thing;
use Plinct\Cms\View\WebSite\Type\TypeBuilder;
use Plinct\Cms\View\WebSite\Type\TypeInterface;

class Certification implements TypeInterface
{

	public function __construct()
	{
		CmsFactory::view()->addHeader(
			CreativeWork::navbar()
		);
		CmsFactory::view()->addHeader(
			CmsFactory::view()->fragment()->navbar()
				->type('certification')
				->title(_("Certification"))
				->level(3)
				->newTab('/admin/certification',  CmsFactory::view()->fragment()->icon()->home(18,18))
				->newTab('/admin/certification/new',  CmsFactory::view()->fragment()->icon()->plus(18,18))
				->search('/admin/certification')
				->ready()
		);
	}

	/**
	 * @param array|null $value
	 * @return void
	 */
	public function index(?array $value)
	{
		CmsFactory::view()->addMain(
			CmsFactory::view()->fragment()->reactShell('certification')->setColumnsTable(['alternateName'=>_('Alternamte name'),'certificationIdentification'=>_("Certification identification")])->ready()
		);
	}

	/**
	 * @param array|null $data
	 * @return void
	 */
	public function edit(?array $data)
	{
		if (!empty($data)) {
			$value = $data[0];
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->expandingBox(_("Certification"),$this->form('edit', $value), true));
		} else {
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->noContent(sprintf(_("No %s were found!"), _('certification'))));
		}
	}

	public function new(?array $value)
	{
		CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->simpleBox($this->form(), _("Add new")));
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
		if (is_array($data['hasCertification'])) {
			foreach ($data['hasCertification'] as $value) {
				$content[] = CmsFactory::view()->fragment()->box()->expandingBox($value['name'], self::form('edit', $value), false, 'margin: 3px 5px;');
			}
		}
		$content[] = CmsFactory::view()->fragment()->box()->expandingBox(_("Add new")." "._("certification"), self::form('new', null, (int) $idType), false, 'margin: 3px 5px;');
		return $content;
	}

	private static function form(string $case = "new", array $value = null, int $about = null): array
	{
		$about = $about ?? $value['about'] ?? null;
		$issuedBy = $value['issuedBy'] ?? null;
		$certificationStatus = $value['certificationStatus'] ?? null;
		$datePublished = isset($value['datePublished']) ? substr($value['datePublished'],0,10) : null;
		$expires = isset($value['expires']) ? substr($value['expires'],0,10) : null;
		$certificationIdentification = $value['certificationIdentification'] ?? null;
		// FORM
		$form = CmsFactory::view()->fragment()->form(['class'=>'form-basic form-certification']);
		$form->action("/admin/certification/$case")->method('post');
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
		$form->content(CmsFactory::view()->fragment()->reactShell('thing')->getItemType(_("About"),'about',$about)->ready());
		// issuedBy
		$form->content(CmsFactory::view()->fragment()->reactShell('organization')->getItemType(_("Issued by"),'issuedBy', $issuedBy)->ready());
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