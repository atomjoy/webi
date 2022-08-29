<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class WebiSeeder extends Seeder
{
	function run()
	{
		$this->call([
			WebiUserSeeder::class
		]);
	}

	function sampleRelation()
	{
		// $users = User::factory()->count(5)->create();
		// $users->each(function ($u) {
		// 	$issue = Issues::factory()->count(1)->make();
		// 	$u->issues()->saveMany($issue);
		// 	$isues = Issues::factory()->count(2)->make();
		// 	$u->issues()->saveMany($isues);
		// });
	}
}
