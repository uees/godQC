<?php

use App\Role;
use App\User;
use Illuminate\Database\Seeder;
use Identicon\Identicon;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $identicon = new Identicon;

        // create admin user
        $admin = new User;
        $admin->fill([
            'name' => 'Admin',
            'email' => 'admin@szrd.com',
            'password' => bcrypt('pwd1234'),
            'avatar' => $identicon->getImageDataUri('Admin', 256),
        ])->save();
        $adminRole = Role::where('name', 'admin')->first();
        $admin->roles()->attach($adminRole->id);

        // create fqc user
        $fqc = new User;
        $fqc->fill([
            'name' => 'FQC',
            'email' => 'fqc@szrd.com',
            'password' => bcrypt('123456'),
            'avatar' => $identicon->getImageDataUri('FQC', 256),
        ])->save();
        $fqcRole = Role::where('name', 'fqc')->first();
        $fqc->roles()->attach($fqcRole->id);

        // create iqc user
        $iqc = new User;
        $iqc->fill([
            'name' => 'IQC',
            'email' => 'iqc@szrd.com',
            'password' => bcrypt('123456'),
            'avatar' => $identicon->getImageDataUri('IQC', 256),
        ])->save();
        $iqcRole = Role::where('name', 'iqc')->first();
        $iqc->roles()->attach($iqcRole->id);
    }
}
