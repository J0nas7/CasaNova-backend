<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\Message;
use App\Models\Favorite;
use App\Models\Notification;

use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class MyDemoSeeder extends Seeder
{
    public function run(): void
    {
        // DB::statement('PRAGMA foreign_keys = OFF'); // Important for SQLite
        // DB::table('CN_Users')->truncate();
        // DB::table('CN_Properties')->truncate();
        // DB::table('CN_Property_Images')->truncate();
        // DB::table('CN_Messages')->truncate();
        // DB::table('CN_Favorites')->truncate();
        // DB::statement('PRAGMA foreign_keys = ON');

        $faker = Faker::create();

        // Create Admin User
        $admin = User::firstOrCreate([
            'User_First_Name'  => 'Admin',
            'User_Last_Name'   => 'User',
            'User_Email'       => 'admin@casanova.com',
            'User_Password'    => Hash::make('admin123'),
            'User_Role'        => 'Administrator',
            'User_Profile_Picture' => null,
            'User_Address'     => 'Admin Street, Main City',
            'User_CreatedAt'   => now(),
            'User_UpdatedAt'   => now(),
        ]);

        // Create Landlords
        $landlords = [];
        for ($i = 1; $i <= 3; $i++) {
            $landlords[] = User::firstOrCreate([
                'User_First_Name'  => "Landlord$i",
                'User_Last_Name'   => "Lastname$i",
                'User_Email'       => "landlord$i@casanova.com",
                'User_Password'    => Hash::make('password123'),
                'User_Role'        => 'Landlord',
                'User_Profile_Picture' => null,
                'User_Address'     => "Street $i, City $i",
                'User_CreatedAt'   => now(),
                'User_UpdatedAt'   => now(),
            ]);
        }

        // Create Tenants
        $tenants = [];
        for ($i = 1; $i <= 5; $i++) {
            $tenants[] = User::firstOrCreate([
                'User_First_Name'  => "Tenant$i",
                'User_Last_Name'   => "Lastname$i",
                'User_Email'       => "tenant$i@casanova.com",
                'User_Password'    => Hash::make('password123'),
                'User_Role'        => 'Tenant',
                'User_Profile_Picture' => null,
                'User_Address'     => "Street $i, City $i",
                'User_CreatedAt'   => now(),
                'User_UpdatedAt'   => now(),
            ]);
        }

        // Create Properties
        $properties = [];
        $cities = ['New York', 'Los Angeles', 'Chicago', 'San Francisco', 'Miami', 'Houston'];

        foreach ($landlords as $landlord) {
            for ($i = 1; $i <= 2; $i++) {
                $properties[] = Property::create([
                    'User_ID'               => $landlord->User_ID,
                    'Property_Title'        => $faker->sentence(3),
                    'Property_Description'  => $faker->paragraph(),
                    'Property_Address'      => $faker->address,
                    'Property_Latitude'     => $faker->latitude(54.5, 56.2), // Latitude range for Sjælland, Denmark
                    'Property_Longitude'    => $faker->longitude(10.9, 12.8), // Longitude range for Sjælland, Denmark
                    'Property_City'         => $cities[array_rand($cities)], // Randomly assign a city
                    // 'Property_State'        => $faker->state, // Use Faker to generate a random state
                    'Property_Zip_Code'     => rand(10000, 99999), // Random zip code
                    'Property_Price_Per_Month' => rand(1000, 5000), // Random price between $1000-$5000
                    'Property_Num_Bedrooms' => rand(1, 5), // Random number of bedrooms
                    'Property_Num_Bathrooms' => rand(1, 3), // Random number of bathrooms
                    'Property_Square_Feet'  => rand(500, 3000), // Random square footage
                    'Property_Amenities'    => json_encode(['Pool', 'Gym', 'Parking']), // Example amenities
                    'Property_Property_Type' => rand(1, 4), // Random property type
                    'Property_Available_From' => now()->addDays(rand(1, 30)), // Available in 1-30 days
                    'Property_Available_To' => '12', // Available in x months
                    'Property_Is_Active'    => true,
                ]);
            }
        }

        // Create Property Images with $images array
        $exteriorImages = [
            '/listings/exterior-1.jpg',
            '/listings/exterior-2.jpg',
            '/listings/exterior-3.jpg',
            '/listings/exterior-4.jpg',
            '/listings/exterior-5.jpg',
            '/listings/exterior-6.webp',
        ];

        // Interior images (for non-featured images)
        $interiorImages = [];
        for ($i = 1; $i <= 20; $i++) {
            $interiorImages[] = '/listings/interior-' . $i . '.jpeg';
        }

        // Array to track already assigned featured images
        $usedFeaturedImages = [];

        foreach ($properties as $property) {
            // Shuffle images to ensure randomness
            shuffle($exteriorImages);
            shuffle($interiorImages);

            // Select a unique featured image
            do {
                $featuredImage = $exteriorImages[array_rand($exteriorImages)];
            } while (in_array($featuredImage, $usedFeaturedImages));

            // Mark the featured image as used
            $usedFeaturedImages[] = $featuredImage;

            // Create the featured property image
            PropertyImage::create([
                'Property_ID'      => $property->Property_ID,
                'Image_URL'        => $featuredImage,
                'Image_Order'      => 1
            ]);

            // Select up to 2 non-featured images (interiors)
            $selectedInteriorImages = array_slice($interiorImages, 0, 2);

            $imageOrder = 2; // Start with 2 since 1 is for the featured image
            foreach ($selectedInteriorImages as $imageUrl) {
                PropertyImage::create([
                    'Property_ID'      => $property->Property_ID,
                    'Image_URL'        => $imageUrl,
                    'Image_Order'      => $imageOrder++
                ]);
            }
        }

        // Create Messages (between landlords & tenants)
        /*foreach ($tenants as $tenant) {
            foreach ($properties as $property) {
                if (rand(0, 1)) { // Randomly assign messages
                    Message::create([
                        'Sender_ID'    => $tenant->User_ID,
                        'Receiver_ID'  => $property->User_ID,
                        'Property_ID'  => $property->Property_ID,
                        'Message_Text' => "Hello, I'm interested in {$property->Property_Title}. Is it available?",
                        'Message_CreatedAt'    => now(),
                        'Message_UpdatedAt'    => now(),
                    ]);
                }
            }
        }*/

        // More natural conversation between admin and landlords
        foreach ($properties as $property) {
            if ($property->Property_ID % 2 == 0) { // Only for odd Property_IDs
                continue; // Skip even Property_IDs
            }

            // Admin sends initial inquiry
            Message::create([
                'Sender_ID'    => $admin->User_ID,
                'Receiver_ID'  => $property->User_ID,
                'Property_ID'  => $property->Property_ID,
                'Message_Text' => "Hello, I'm interested in {$property->Property_Title}. Is it still available?",
                'Message_CreatedAt'    => now(),
                'Message_UpdatedAt'    => now(),
            ]);

            // Property owner responds
            Message::create([
                'Sender_ID'    => $property->User_ID,
                'Receiver_ID'  => $admin->User_ID,
                'Property_ID'  => $property->Property_ID,
                'Message_Text' => "Hi! Yes, it's available. Would you like to schedule a viewing?",
                'Message_CreatedAt'    => now()->addMinutes(2),
                'Message_UpdatedAt'    => now()->addMinutes(2),
            ]);

            // Admin replies
            Message::create([
                'Sender_ID'    => $admin->User_ID,
                'Receiver_ID'  => $property->User_ID,
                'Property_ID'  => $property->Property_ID,
                'Message_Text' => "That sounds great! I'm available this weekend. Does that work for you?",
                'Message_CreatedAt'    => now()->addMinutes(5),
                'Message_UpdatedAt'    => now()->addMinutes(5),
            ]);

            // Property owner confirms
            Message::create([
                'Sender_ID'    => $property->User_ID,
                'Receiver_ID'  => $admin->User_ID,
                'Property_ID'  => $property->Property_ID,
                'Message_Text' => "Yes, this weekend works! I’ll send you the address and details shortly.",
                'Message_CreatedAt'    => now()->addMinutes(7),
                'Message_UpdatedAt'    => now()->addMinutes(7),
            ]);

            // Admin thanks the owner
            Message::create([
                'Sender_ID'    => $admin->User_ID,
                'Receiver_ID'  => $property->User_ID,
                'Property_ID'  => $property->Property_ID,
                'Message_Text' => "Perfect! Looking forward to it. Thanks!",
                'Message_CreatedAt'    => now()->addMinutes(10),
                'Message_UpdatedAt'    => now()->addMinutes(10),
            ]);
        }

        // Create Favorite Listings
        foreach ($tenants as $tenant) {
            foreach ($properties as $property) {
                if (rand(0, 1)) { // Randomly assign favorites
                    Favorite::create([
                        'Tenant_ID'   => $tenant->User_ID,
                        'Property_ID' => $property->Property_ID,
                        'Favorite_CreatedAt'  => now(),
                        'Favorite_UpdatedAt'  => now(),
                    ]);
                }
            }
        }
    }
}
