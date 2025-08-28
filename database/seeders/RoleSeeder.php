<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Silber\Bouncer\BouncerFacade as Bouncer;
use App\Models\User;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create Roles
        Bouncer::role()->create(['name' => 'admin']);
        Bouncer::role()->create(['name' => 'employee']);

        // Create Abilities
        Bouncer::ability()->create(['name' => 'create']);
        Bouncer::ability()->create(['name' => 'edit']);
        Bouncer::ability()->create(['name' => 'view']);
        Bouncer::ability()->create(['name' => 'delete']);

        // Assign roles to users (example: admin role to user with ID 1)
        $user = User::find(1);
        if ($user) {
            Bouncer::assign('admin')->to($user);  // Assign admin role to the user
        }

        // Allow roles to perform certain actions
        Bouncer::allow('admin')->to('create');
        Bouncer::allow('admin')->to('edit');
        Bouncer::allow('admin')->to('delete');

        Bouncer::allow('employee')->to('view');
    }
}
