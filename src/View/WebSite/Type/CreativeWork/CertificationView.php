<?php
namespace Plinct\Cms\View\WebSite\Type\CreativeWork;

use DOMException;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\Thing\ThingView;
use Plinct\Cms\View\WebSite\Type\TypeBuilder;
use Plinct\Cms\View\WebSite\Type\TypeViewInterface;

class CertificationView extends CreativeWorkView implements TypeViewInterface
{

	public function __destruct()
	{
		CmsFactory::view()->addHeader(
			CmsFactory::view()->fragment()->navbar()
				->type('certification')
				->title(_("Certification"))
				->level(3)
				->newTab('/admin/certification',  CmsFactory::view()->fragment()->icon()->home())
				->newTab('/admin/certification/new',  CmsFactory::view()->fragment()->icon()->plus())
				->newTab('/admin/certification/sitemap',  CmsFactory::view()->fragment()->icon()->sitemap())
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
		CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->simpleBox(self::formCertification(), _("Add new")));
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
			$tbCertification = CmsFactory::helpers()->typeBuilder($value);
			$this->idcreativeWork = $tbCertification->getPropertyValue('idcreativeWork');
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->expandingBox(_("Certification"),self::formCertification('edit', $value), true));
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
				$content[] = CmsFactory::view()->fragment()->box()->expandingBox($value['name'], self::formCertification('edit', $value), false, 'margin: 3px 5px;');
			}
		}
		$content[] = CmsFactory::view()->fragment()->box()->expandingBox(_("Add new")." "._("certification"), self::formCertification('new', null, (int) $idType), false, 'margin: 3px 5px;');
		return $content;
	}

	protected static function formCertification(string $case = "new", array $value = null, int $about = null): array
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
		$form = ThingView::formThing($form, $value);
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

	/**
	 * @param array $data
	 * @param array|null $queryParams
	 * @return void
	 * @throws DOMException
	 */
	public function sitemap(array $data, array $queryParams = null): void
	{
		$this->sitemapFilename = 'sitemap-certification.xml';
		parent::sitemap($data);
	}
}
