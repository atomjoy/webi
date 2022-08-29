<?php

namespace Webi\Models;

use Database\Factories\WebiUserFactory;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webi\Enums\User\UserRole;
use Webi\Traits\User\WebiHasRole;

abstract class WebiUser extends Authenticatable implements HasLocalePreference
{
	use HasFactory, Notifiable, SoftDeletes, WebiHasRole;

	protected $fillable = [
		'name',
		'email',
		'password',
		'email_verified_at',
		'remember_token',
		'newsletter_on',
		'mobile_prefix',
		'mobile',
		'username',
		'location',
		'website',
		'locale',
		'image',
		'role',
		'code',
		'ip',
	];

	protected $hidden = [
		'code',
		'password',
		'remember_token',
	];

	protected $casts = [
		'role' => UserRole::class,
	];

	protected static function newFactory()
	{
		return WebiUserFactory::new();
	}

	protected function serializeDate(\DateTimeInterface $date)
	{
		return $date->format('Y-m-d H:i:s');
	}

	public function preferredLocale()
	{
		return $this->locale;
	}
}
