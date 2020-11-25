<?php
namespace Rainsens\Authorize\Traits;

use Rainsens\Authorize\Facades\Authorize;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasPermits
{
	public static function bootHasPermissions()
	{
		static::deleting(function ($model) {
			if (method_exists($model, 'isForceDeleting') && ! $model->isForceDeleting()) {
				return;
			}
			$model->selfPermits()->detach();
		});
	}
	
	/**
	 * Permits associated with user.
	 */
	public function permits(): BelongsToMany
	{
		return $this->morphToMany(
			Authorize::permitClass(), 'permitable'
		);
	}
	
	public function giveUserPermits($permits)
	{
		$permitIds = collect($permits)
			->flatten()
			->map(function ($permit) {
				if (empty($permit)) {
					return null;
				}
				return $this->getPermitsModel($permit);
			})
			->map
			->id
			->all();
		
		$this->permits()->sync($permitIds, false);
		
		return $this;
	}
}
