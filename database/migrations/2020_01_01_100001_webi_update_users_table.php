<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class WebiUpdateUsersTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('users', function (Blueprint $table) {
			if (!Schema::hasColumn('users', 'email_verified_at')) {
				$table->timestamp('email_verified_at')->nullable(true);
			}
			if (!Schema::hasColumn('users', 'username')) {
				$table->string('username', 30)->unique();
			}
			if (!Schema::hasColumn('users', 'role')) {
				$table->enum('role', ['user', 'worker', 'admin'])->nullable()->default('user');
			}
			if (!Schema::hasColumn('users', 'mobile_prefix')) {
				$table->string('mobile_prefix', 10)->nullable(true);
			}
			if (!Schema::hasColumn('users', 'mobile')) {
				$table->string('mobile', 30)->nullable(true);
			}
			if (!Schema::hasColumn('users', 'code')) {
				$table->string('code', 30)->unique()->nullable(true);
			}
			if (!Schema::hasColumn('users', 'locale')) {
				$table->string('locale', 2)->nullable()->default(config('app.locale'));
			}
			if (!Schema::hasColumn('users', 'ip')) {
				$table->string('ip')->nullable(true);
			}
			if (!Schema::hasColumn('users', 'remember_token')) {
				$table->string('remember_token')->nullable(true);
			}
			if (!Schema::hasColumn('users', 'newsletter_on')) {
				$table->tinyInteger('newsletter_on')->nullable(true)->default(1);
			}
			if (!Schema::hasColumn('users', 'image')) {
				$table->string('image')->nullable(true);
			}
			if (!Schema::hasColumn('users', 'website')) {
				$table->string('website')->nullable(true);
			}
			if (!Schema::hasColumn('users', 'location')) {
				$table->string('location')->nullable(true);
			}
			if (!Schema::hasColumn('users', 'deleted_at')) {
				$table->softDeletes();
			}
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('users', function (Blueprint $table) {
			$table->dropColumn([
				'username', 'role', 'mobile_prefix', 'mobile',
				'code', 'locale', 'ip', 'newsletter_on',
				'image', 'website', 'location'
			]);
		});
	}
}
