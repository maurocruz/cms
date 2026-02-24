<?php
namespace Plinct\Cms\Application\User;

class UserCommand
{
	public function comparePrivileges($privileges, $function, $action, $namespace): bool
	{
		if ($privileges) {
			foreach ($privileges as $privilege) {
				if ($privilege['function'] == $function && $privilege['action'] == $action && $privilege['namespace'] == $namespace) {
					return true;
				}
			}
		}
		return false;
	}
}
