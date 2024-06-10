<?php
declare(strict_types=1);
namespace Plinct\Cms\View\WebSite\Type\Intangible;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\TypeBuilder;

class ProgramMembership
{
	/**
	 * @param array $data
	 * @return array
	 */
	public static function edit(array $data): array
	{
		$content = [];
		if (is_array($data['memberOf'])) {
			foreach ($data['memberOf'] as $value) {
				$content[] = CmsFactory::view()->fragment()->box()->expandingBox($value['programName'], self::form('edit', $value), false, 'margin: 3px 5px;');
			}
		}
		$content[] = CmsFactory::view()->fragment()->box()->expandingBox(_("Add new program membership"), self::form('new', $data), false, 'margin: 3px 5px;');
		return $content;
	}

	/**
	 * @param string $case
	 * @param array|null $value
	 * @return array
	 */
	private static function form(string $case, array $value): array
	{
		$programName = $value['programName'] ?? null;
		$hostingOrganization = $value['hostingOrganization'] ?? null;
		$member = $value['member'] ?? null;
		$membershipNumber = $value['membershipNumber'] ?? null;
		$membershipPointsEarned = $value['membershipPointsEarned'] ?? null;
		$form = CmsFactory::view()->fragment()->form(['class'=>'form-basic form-programMembership']);
		$form->action("/admin/programMembership/$case")->method('post');
		if ($case === 'new') {
			$typeBuilder = new TypeBuilder('person', $value);
			$idperson = $typeBuilder->getId();
			$form->input('member', (string) $idperson, 'hidden');
		}
		if($case === 'edit') {
			$typeBuilder = new TypeBuilder('programMembership', $value);
			$idprogramMembership = $typeBuilder->getId();
			$form->input('idprogramMembership', (string) $idprogramMembership, 'hidden');
			$form->input('member', (string) $member, 'hidden');
		}
		// program name
		$form->fieldsetWithInput('programName', $programName, _("Program name"));

		// HOSTING ORGANIZATION
		$form->content(CmsFactory::view()->fragment()->reactShell('organization')
			->setAttribute('data-action','getItemType')
			->setAttribute('data-legend',_("Hosting organization"))
			->setAttribute('data-propertyName','hostingOrganization')
			->setAttribute('data-value',(string) $hostingOrganization ?? '')
			->ready());

		$form->fieldsetWithInput('membershipNumber', $membershipNumber, _("Membership number"));
		$form->fieldsetWithInput('membershipPointsEarned', $membershipPointsEarned, _("Points earned"));
		// SUBMIT BUTTONS
		$form->submitButtonSend();
		if ($case === 'edit') {
			$form->submitButtonDelete('/admin/programMembership/erase');
		}
		return $form->ready();
	}
}
