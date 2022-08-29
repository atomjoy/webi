<?php

namespace Webi\Traits\Tests;

use App\Models\User;
use Database\Seeders\WebiSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;
use Webi\Enums\User\UserRole;

abstract class AuthenticatedTestCase extends TestCase
{
	use RefreshDatabase; // Refresh db before each test

	protected ?User $user = null; // Logged user obj

	protected UserRole $authWithRole = UserRole::USER; // Logged user role

	protected $seed = false; // Run seeder before each test.

	protected $seeder = WebiSeeder::class; // Choose seeder class

	protected function setUp(): void
	{
		parent::setUp(); // Run parent setUp

		$this->user = User::factory()
			->role($this->authWithRole)
			->create(); // Create user in db

		$this->actingAs($this->user); // Login user

		config()->set('services.mailgun.secret', '');

		Route::get('web/api/tests', function () {
			return 'OK';
		})->middleware(['webi-role:user']);
	}

	function seedWebi()
	{
		$this->seed(WebiSeeder::class); // Run db seeder
	}

	static function getPassword($html)
	{
		preg_match('/word>[a-zA-Z0-9]+<\/pass/', $html, $matches, PREG_OFFSET_CAPTURE);
		return str_replace(['word>', '</pass'], '', end($matches)[0]);
	}

	static function buildMailgunSignature($timestamp, $token)
	{
		return hash_hmac(
			'sha256',
			sprintf('%s%s', $timestamp, $token),
			config('services.mailgun.secret')
		);
	}
}
