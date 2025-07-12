<?php
namespace Plinct\Cms\View\WebSite\Type\Intangible;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\Organization\Organization;
use Plinct\Cms\View\WebSite\Type\Person\PersonView;
use Plinct\Cms\View\WebSite\Type\Thing\ThingView;
use Plinct\Cms\View\WebSite\Type\TypeViewInterface;

class RoleView implements TypeViewInterface
{
	private ?string $refererType;
	private ?string $refererName;
	private ?string $refererId;
	private ?string $refererIdthing;
	private ?string $querystring = null;

	/**
	 * @return void
	 */
	private function navbar(): void {
		if($this->refererType && $this->refererName && $this->refererId && $this->refererIdthing) {
			if (strtolower($this->refererType) == "organization") {
				Organization::navbarEdit($this->refererName, $this->refererId, $this->refererIdthing);
			} else if (strtolower($this->refererType) == "person") {
				PersonView::navbarEdit($this->refererName, $this->refererId, $this->refererIdthing);
			}
			$querystring = "refererType=$this->refererType&refererName=$this->refererName&refererId=$this->refererId&refererIdthing=$this->refererIdthing";
			$this->querystring = $querystring;
		}
		CmsFactory::view()->addHeader(
			CmsFactory::view()->fragment()->navbar()
				->type('role')
				->title(_('Role'))
				->level(4)
				->newTab("/admin/role?".$this->querystring, CmsFactory::view()->fragment()->icon()->home())
				->newTab("/admin/role/new?".$this->querystring, CmsFactory::view()->fragment()->icon()->plus())
				->ready());
	}

	/**
	 * @param array|null $data
	 * @param array|null $queryParams
	 * @return void
	 */
	public function index(?array $data, array $queryParams = null): void
	{
		$this->refererType = $queryParams['refererType'] ?? null;
		$this->refererName = $queryParams['refererName'] ?? null;
		$this->refererId = $queryParams['refererId'] ?? null;
		$this->refererIdthing = $queryParams['refererIdthing'] ?? null;

		// TABLE
		if (isset($data[0])) {
			$rows = $data;
		} elseif (!empty($data)) {
			$tbreferer = CmsFactory::toolBox()::typeBuilder($data);
			$this->refererType = $tbreferer->getType();
			$this->refererId = $tbreferer->getId();
			$this->refererName = $tbreferer->getValue('name');
			$this->refererIdthing = $tbreferer->getIdthing();
			$rows = $data['member'] ?? null;
		} else {
			$rows = null;
		}
		// NAVBAR
		self::navbar();

		$table = CmsFactory::view()->fragment()->table(['class'=>'table table-role']);
		$table->setCaption(_('Roles'));
		$table->labels(_('Person name'), _('Role name'),  _('Organization'));
		// ROWS
		if ($rows) {
			foreach ($rows as $item) {
				// role
				$tbRole = CmsFactory::toolBox()::typeBuilder($item);
				$idrole = $tbRole->getId();
				$roleName = $item['name'];
				// person
				$member = $item['member'];
				$tbMember = CmsFactory::toolBox()::typeBuilder($member);
				$idperson = $tbMember->getId();
				$namePerson = $member['name'];
				// organization
				$organization = $item['memberOf'];
				$tbOrganization = CmsFactory::toolBox()::typeBuilder($organization);
				$idorganization = $tbOrganization->getId();
				$nameOrganization = $organization['name'];
				$table->buttonEdit("/admin/role/edit/$idrole?".$this->querystring);
				$table->addRow("<a href='/admin/person/edit/$idperson'>$namePerson</a>", $roleName, "<a href='/admin/organization/edit/$idorganization'>$nameOrganization</a>");
				$table->buttonDelete('role', $idrole);
			}
		}
		CmsFactory::view()->addMain($table->ready());
	}

	/**
	 * @param array|null $data
	 * @param array|null $queryParams
	 * @return void
	 */
	public function edit(?array $data, array $queryParams = null): void
	{
		$this->refererType = $queryParams['refererType'] ?? null;
		$this->refererName = $queryParams['refererName'] ?? null;
		$this->refererId = $queryParams['refererId'] ?? null;
		$this->refererIdthing = $queryParams['refererIdthing'] ?? null;
		if (isset($data[0])) {
			$value = $data[0];
			// NAVBAR
			self::navbar();
			CmsFactory::view()->addMain(
				CmsFactory::view()->fragment()->box()->simpleBox($this->formRole($value), _("Role"))
			);
		}
	}

	/**
	 * @param array|null $data
	 * @param array|null $queryParams
	 * @return void
	 */
	public function new(?array $data, array $queryParams = null): void
	{
		$this->refererType = $queryParams['refererType'] ?? null;
		$this->refererName = $queryParams['refererName'] ?? null;
		$this->refererId = $queryParams['refererId'] ?? null;
		$this->refererIdthing = $queryParams['refererIdthing'] ?? null;
		// NAVBAR
		self::navbar();
		CmsFactory::view()->addMain(
			CmsFactory::view()->fragment()->box()->simpleBox($this->formRole(), _("Role"))
		);
	}

	/**
	 * @param array|null $value
	 * @return array
	 */
	private function formRole(?array $value = null): array
	{
		$idperson = $this->refererType && lcfirst($this->refererType) == 'person' ? $this->refererId : null;
		$idorganization = $this->refererType && lcfirst($this->refererType) == 'organization' ? $this->refererId : null;

		$form = CmsFactory::view()->fragment()->form("form-role");
		$form->attributes(['class'=>'form-basic form-role']);
		$form->method('post');
		if ($value) {
			$typeBuilder = CmsFactory::toolBox()::typeBuilder($value);
			$idrole = $typeBuilder->getId();
			if (isset($value['member'])) {
				$tbMember = CmsFactory::toolBox()::typeBuilder($value['member']);
				$idperson = $tbMember->getId();
			}
			if (isset($value['memberOf'])) {
				$tbMemberOf = CmsFactory::toolBox()::typeBuilder($value['memberOf']);
				$idorganization = $tbMemberOf->getId();
			}
			$form->action('/admin/role/edit?'.$this->querystring);
			$form->input('idrole', (string) $idrole, 'hidden');
		} else {
			$form->action('/admin/role/new?'.$this->querystring);
		}
		// PERSON
		$form->relationshipOneToOne('Person',_('Person'),'person',$idperson);
		// THING
		$form = ThingView::formThing($form, $value, _('Role name'));
		// SECUNDARY TYPE
		$form->fieldsetWithInput('secondaryRole',$value['secondaryRole'] ?? null, _('Secondary role'));
		// START DATE
		$form->fieldsetWithInput('startDate',$value['startDate'] ?? null, _('Start date'), 'date');
		// END DATE
		$form->fieldsetWithInput('endDate',$value['endDate'] ?? null, _('End date'), 'date');
		// ORGANIZATION
		$form->relationshipOneToOne('Organization',_('Organization'),'organization',$idorganization);
		// buttons
		$form->submitButtonSend();
		if ($value) {
			$form->submitButtonDelete('/admin/role/delete?'.$this->querystring);
		}
		return $form->ready();
	}
}
