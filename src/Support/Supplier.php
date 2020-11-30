<?php
namespace Rainsens\Rbac\Support;

use Rainsens\Rbac\Exceptions\InvalidArgumentException;
use Rainsens\Rbac\Facades\Rbac;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Rainsens\Rbac\Contracts\PermitContract;
use Rainsens\Rbac\Contracts\RoleContract;

class Supplier
{
	public function pathArgs(): Collection
	{
		return collect([
			'path' => request()->path() ? '/' . request()->path() : '/',
			'method' => request()->method() ?? null,
		]);
	}
	
	public function permitArgs(...$args): Collection
	{
		return collect($args)->flatten()
			->map(function ($value) {
				if (is_object($value)) {
					return $value->id;
				} else {
					return $value;
				}
			});
	}
	
	public function findExpectedModels(Model $expectedRecordType, ...$args): Collection
	{
		$args = $this->permitArgs($args);
		
		if ($args->isEmpty()) {
			throw new InvalidArgumentException('Please provide necessary arguments.');
		};
		
		$args = $args->toArray();
		$userClass = Rbac::authorize()->userClass;
		
		if ($expectedRecordType instanceof PermitContract or $expectedRecordType instanceof RoleContract) {
			return $expectedRecordType->where('guard', Rbac::guard()->name)
				->whereIn('id', $args)
				->orWhereIn('slug', $args)
				->get();
		} elseif ($expectedRecordType instanceof $userClass) {
			return $expectedRecordType
				->whereIn('id', $args)
				->orWhereIn('name', $args)
				->get();
		}
		return collect();
	}
}
