<?php

namespace Database\Seeders;

use App\Models\Items;
use App\Models\Sites;
use App\Models\Types;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'admin',
        ]);

        // Tehnicians

        User::factory()->create([
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'role' => 'technician',
        ]);

        User::factory()->create([
            'name' => 'Jane Smith',
            'email' => 'janesmith@example.com',
            'role' => 'technician',
        ]);

        User::factory()->create([
            'name' => 'Mike Johnson',
            'email' => 'mikejohnson@example.com',
            'role' => 'technician',
        ]);

        // types

        Types::create(['name' => 'Router']);
        Types::create(['name' => 'Hub']);
        Types::create(['name' => 'Switch']);
        Types::create(['name' => 'Modem']);
        Types::create(['name' => 'Access Point']);
        Types::create(['name' => 'Repeater']);
        Types::create(['name' => 'Firewall']);
        Types::create(['name' => 'Bridge']);
        Types::create(['name' => 'Load Balancer']);
        Types::create(['name' => 'Gateway']);

        // Sites
        
        $faker = Faker::create();

        // Create multiple sites with a valid phone number
        Sites::create([
            'name' => 'Allen Central Elementary School',
            'phonenumber' => $faker->numerify('09#########') // Generates a phone number with '09' followed by 9 digits
        ]);

        Sites::create([
            'name' => 'Greenwood High School',
            'phonenumber' => $faker->numerify('09#########')
        ]);

        Sites::create([
            'name' => 'Riverdale Middle School',
            'phonenumber' => $faker->numerify('09#########')
        ]);

        Sites::create([
            'name' => 'Sunnybrook Academy',
            'phonenumber' => $faker->numerify('09#########')
        ]);

        Sites::create([
            'name' => 'Maplewood Primary School',
            'phonenumber' => $faker->numerify('09#########')
        ]);

        Sites::create([
            'name' => 'Blue Ridge International School',
            'phonenumber' => $faker->numerify('09#########')
        ]);

        Sites::create([
            'name' => 'Westlake High School',
            'phonenumber' => $faker->numerify('09#########')
        ]);

        Sites::create([
            'name' => 'Oak Ridge College',
            'phonenumber' => $faker->numerify('09#########')
        ]);

        Sites::create([
            'name' => 'Pinecrest Secondary School',
            'phonenumber' => $faker->numerify('09#########')
        ]);

        Sites::create([
            'name' => 'Cedar Valley Institute',
            'phonenumber' => $faker->numerify('09#########')
        ]);


        // Items

        // Fetch all Types (these are already seeded)
        $types = Types::all();

        // Create 20 items with random data and corresponding type IDs
        Items::create([
            'itemId' => 'ITEM0001',
            'name' => 'TP-Link Router',
            'model' => 'Archer C7',
            'brand' => 'TP-Link',
            'types_id' => $types->where('name', 'Router')->first()->id,
            'description' => $faker->sentence,
            'unit' => 'piece',
            'quantity' => 100
        ]);

        Items::create([
            'itemId' => 'ITEM0002',
            'name' => 'Netgear Hub',
            'model' => 'GS308',
            'brand' => 'Netgear',
            'types_id' => $types->where('name', 'Hub')->first()->id,
            'description' => $faker->sentence,
            'unit' => 'piece',
            'quantity' => 100
        ]);

        Items::create([
            'itemId' => 'ITEM0003',
            'name' => 'Cisco Switch',
            'model' => 'SG350-28',
            'brand' => 'Cisco',
            'types_id' => $types->where('name', 'Switch')->first()->id,
            'description' => $faker->sentence,
            'unit' => 'piece',
            'quantity' => 100
        ]);

        Items::create([
            'itemId' => 'ITEM0004',
            'name' => 'Motorola Modem',
            'model' => 'MB7621',
            'brand' => 'Motorola',
            'types_id' => $types->where('name', 'Modem')->first()->id,
            'description' => $faker->sentence,
            'unit' => 'piece',
            'quantity' => 100
        ]);

        Items::create([
            'itemId' => 'ITEM0005',
            'name' => 'Ubiquiti Access Point',
            'model' => 'UniFi 6 LR',
            'brand' => 'Ubiquiti',
            'types_id' => $types->where('name', 'Access Point')->first()->id,
            'description' => $faker->sentence,
            'unit' => 'piece',
            'quantity' => 100
        ]);

        Items::create([
            'itemId' => 'ITEM0006',
            'name' => 'Asus Router',
            'model' => 'RT-AC66U',
            'brand' => 'Asus',
            'types_id' => $types->where('name', 'Router')->first()->id,
            'description' => $faker->sentence,
            'unit' => 'piece',
            'quantity' => 100
        ]);

        Items::create([
            'itemId' => 'ITEM0007',
            'name' => 'D-Link Hub',
            'model' => 'DGS-1016D',
            'brand' => 'D-Link',
            'types_id' => $types->where('name', 'Hub')->first()->id,
            'description' => $faker->sentence,
            'unit' => 'piece',
            'quantity' => 100
        ]);

        Items::create([
            'itemId' => 'ITEM0008',
            'name' => 'Linksys Switch',
            'model' => 'LGS308',
            'brand' => 'Linksys',
            'types_id' => $types->where('name', 'Switch')->first()->id,
            'description' => $faker->sentence,
            'unit' => 'piece',
            'quantity' => 100
        ]);

        Items::create([
            'itemId' => 'ITEM0009',
            'name' => 'Arris Modem',
            'model' => 'SB6183',
            'brand' => 'Arris',
            'types_id' => $types->where('name', 'Modem')->first()->id,
            'description' => $faker->sentence,
            'unit' => 'piece',
            'quantity' => 100
        ]);

        Items::create([
            'itemId' => 'ITEM0010',
            'name' => 'TP-Link Repeater',
            'model' => 'RE450',
            'brand' => 'TP-Link',
            'types_id' => $types->where('name', 'Repeater')->first()->id,
            'description' => $faker->sentence,
            'unit' => 'piece',
            'quantity' => 100
        ]);

        Items::create([
            'itemId' => 'ITEM0011',
            'name' => 'Zyxel Router',
            'model' => 'NBG6716',
            'brand' => 'Zyxel',
            'types_id' => $types->where('name', 'Router')->first()->id,
            'description' => $faker->sentence,
            'unit' => 'piece',
            'quantity' => 100
        ]);

        Items::create([
            'itemId' => 'ITEM0012',
            'name' => 'TP-Link Hub',
            'model' => 'TL-SG1008D',
            'brand' => 'TP-Link',
            'types_id' => $types->where('name', 'Hub')->first()->id,
            'description' => $faker->sentence,
            'unit' => 'piece',
            'quantity' => 100
        ]);

        Items::create([
            'itemId' => 'ITEM0013',
            'name' => 'Ubiquiti Switch',
            'model' => 'EdgeSwitch 24',
            'brand' => 'Ubiquiti',
            'types_id' => $types->where('name', 'Switch')->first()->id,
            'description' => $faker->sentence,
            'unit' => 'piece',
            'quantity' => 100
        ]);

        Items::create([
            'itemId' => 'ITEM0014',
            'name' => 'ARRIS Access Point',
            'model' => 'WAP2000',
            'brand' => 'ARRIS',
            'types_id' => $types->where('name', 'Access Point')->first()->id,
            'description' => $faker->sentence,
            'unit' => 'piece',
            'quantity' => 100
        ]);

        Items::create([
            'itemId' => 'ITEM0015',
            'name' => 'Cisco Repeater',
            'model' => 'WAP121',
            'brand' => 'Cisco',
            'types_id' => $types->where('name', 'Repeater')->first()->id,
            'description' => $faker->sentence,
            'unit' => 'piece',
            'quantity' => 100
        ]);

        Items::create([
            'itemId' => 'ITEM0016',
            'name' => 'D-Link Modem',
            'model' => 'DSL-2740B',
            'brand' => 'D-Link',
            'types_id' => $types->where('name', 'Modem')->first()->id,
            'description' => $faker->sentence,
            'unit' => 'piece',
            'quantity' => 100
        ]);

        Items::create([
            'itemId' => 'ITEM0017',
            'name' => 'Linksys Repeater',
            'model' => 'RE7000',
            'brand' => 'Linksys',
            'types_id' => $types->where('name', 'Repeater')->first()->id,
            'description' => $faker->sentence,
            'unit' => 'piece',
            'quantity' => 100
        ]);

        Items::create([
            'itemId' => 'ITEM0018',
            'name' => 'Netgear Access Point',
            'model' => 'WAC510',
            'brand' => 'Netgear',
            'types_id' => $types->where('name', 'Access Point')->first()->id,
            'description' => $faker->sentence,
            'unit' => 'piece',
            'quantity' => 100
        ]);

        Items::create([
            'itemId' => 'ITEM0019',
            'name' => 'Asus Modem',
            'model' => 'DSL-AC68U',
            'brand' => 'Asus',
            'types_id' => $types->where('name', 'Modem')->first()->id,
            'description' => $faker->sentence,
            'unit' => 'piece',
            'quantity' => 100
        ]);

    }
}
