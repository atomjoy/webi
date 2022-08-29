<?php

namespace Webi\Traits\User;

use Webi\Enums\User\UserRole;

trait WebiHasRole
{
	function scopeHasRole($query, UserRole $role = UserRole::USER)
	{
		return $query->where('role', $role);
	}

	function isAdmin()
	{
		return ($this->role === UserRole::ADMIN);
	}
}
