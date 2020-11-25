<?php
namespace Rainsens\Rbac\Traits;

use Rainsens\Rbac\Facades\Rbac;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasPermits
{
	public function permits(): BelongsToMany
	{
		return $this->morphToMany(
			Rbac::authorize()->permitClass,
			'permitable',
			Rbac::authorize()->permitUsersTable,
			'permitable_id',
			'permit_id'
		);
	}
}
