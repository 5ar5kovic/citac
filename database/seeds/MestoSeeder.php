<?php

use Illuminate\Database\Seeder;

class MestoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('mesto')->insert([
            'id' => '1',
            'naziv' => 'Малча',
            'created_at' => now(),
        ]);
        DB::table('mesto')->insert([
            'id' => '2',
            'naziv' => 'Јасеновик',
            'created_at' => now(),
        ]);
        DB::table('mesto')->insert([
            'id' => '3',
            'naziv' => 'Врело',
            'created_at' => now(),
        ]);
        DB::table('mesto')->insert([
            'id' => '4',
            'naziv' => 'Пасјача',
            'created_at' => now(),
        ]);
        DB::table('mesto')->insert([
            'id' => '5',
            'naziv' => 'Ореовац',
            'created_at' => now(),
        ]);
    }
}
