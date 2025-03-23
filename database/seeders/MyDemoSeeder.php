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

class MyDemoSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // Create Admin User
        $admin = User::create([
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
            $landlords[] = User::create([
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
            $tenants[] = User::create([
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
                    'Property_City'         => $cities[array_rand($cities)], // Randomly assign a city
                    'Property_State'        => 'State Placeholder', // Add appropriate state logic if needed
                    'Property_Zip_Code'     => rand(10000, 99999), // Random zip code
                    'Property_Price_Per_Month' => rand(1000, 5000), // Random price between $1000-$5000
                    'Property_Num_Bedrooms' => rand(1, 5), // Random number of bedrooms
                    'Property_Num_Bathrooms' => rand(1, 3), // Random number of bathrooms
                    'Property_Square_Feet'  => rand(500, 3000), // Random square footage
                    'Property_Amenities'    => json_encode(['Pool', 'Gym', 'Parking']), // Example amenities
                    'Property_Property_Type' => rand(1, 4), // Random property type
                    'Property_Available_From' => now()->addDays(rand(1, 30)), // Available in 1-30 days
                    'Property_Is_Active'    => true,
                ]);
            }
        }

        // Create Property Images with $images array
        $exteriorImages = [
            'https://images.squarespace-cdn.com/content/v1/58487dc4b8a79b6d02499b60/1568694792952-WBFS9R58HTK5R3AQ8RYN/93d1c77d-e64e-4c4c-9c13-ac34c1bfe057-0.jpg?format=1000w',
            'https://hips.hearstapps.com/hmg-prod/images/bojnice-castle-1603142898.jpg?crop=0.668xw:1.00xh;0.116xw,0&resize=980:*',
            'https://www.theadvertiser.com/gcdn/-mm-/b1e69d941f2b942ae73b95e0443233dd7c8240ae/c=0-67-1280-790/local/-/media/2016/05/04/LAGroup/LafayetteLA/635979569706799881-Seafair-Air.jpg?width=660&height=373&fit=crop&format=pjpg&auto=webp',
            'https://cdn.vox-cdn.com/thumbor/i87U94wmqg-o_-uqHb2agO9A51A=/0x0:665x441/1200x800/filters:focal(258x126:364x232)/cdn.vox-cdn.com/uploads/chorus_image/image/53240683/genMid.08838540_1_6.0.jpg',
            'https://www.shutterstock.com/image-photo/narrow-victorian-row-houses-peaked-600nw-2320936199.jpg',
            'https://townsquare.media/site/392/files/2020/11/The-Mansion.jpg'
        ];

        // Interior images (for non-featured images)
        $interiorImages = [
            'https://images.pexels.com/photos/1643383/pexels-photo-1643383.jpeg?auto=compress&cs=tinysrgb&w=1200',
            'https://images.pexels.com/photos/1571459/pexels-photo-1571459.jpeg?auto=compress&cs=tinysrgb&w=1200',
            'https://images.pexels.com/photos/271816/pexels-photo-271816.jpeg?auto=compress&cs=tinysrgb&w=1200',
            'https://images.pexels.com/photos/1457842/pexels-photo-1457842.jpeg?auto=compress&cs=tinysrgb&w=1200',
            'https://images.pexels.com/photos/667838/pexels-photo-667838.jpeg?auto=compress&cs=tinysrgb&w=1200',
            'https://images.pexels.com/photos/1643384/pexels-photo-1643384.jpeg?auto=compress&cs=tinysrgb&w=1200',
            'https://images.pexels.com/photos/1648776/pexels-photo-1648776.jpeg?auto=compress&cs=tinysrgb&w=1200',
            'https://images.pexels.com/photos/1571458/pexels-photo-1571458.jpeg?auto=compress&cs=tinysrgb&w=1200',
            'https://images.pexels.com/photos/827518/pexels-photo-827518.jpeg?auto=compress&cs=tinysrgb&w=1200',
            'https://images.pexels.com/photos/1648768/pexels-photo-1648768.jpeg?auto=compress&cs=tinysrgb&w=1200',
            'https://images.pexels.com/photos/1571452/pexels-photo-1571452.jpeg?auto=compress&cs=tinysrgb&w=1200',
            'https://images.pexels.com/photos/439227/pexels-photo-439227.jpeg?auto=compress&cs=tinysrgb&w=1200',
            'https://images.pexels.com/photos/1648771/pexels-photo-1648771.jpeg?auto=compress&cs=tinysrgb&w=1200',
            'https://images.pexels.com/photos/2826787/pexels-photo-2826787.jpeg?auto=compress&cs=tinysrgb&w=1200',
            'https://images.pexels.com/photos/1125136/pexels-photo-1125136.jpeg?auto=compress&cs=tinysrgb&w=1200',
            'https://images.pexels.com/photos/271795/pexels-photo-271795.jpeg?auto=compress&cs=tinysrgb&w=1200',
            'https://images.pexels.com/photos/1571468/pexels-photo-1571468.jpeg?auto=compress&cs=tinysrgb&w=1200',
            'https://images.pexels.com/photos/53577/hotel-architectural-tourism-travel-53577.jpeg?auto=compress&cs=tinysrgb&w=1200',
            'https://images.pexels.com/photos/1457847/pexels-photo-1457847.jpeg?auto=compress&cs=tinysrgb&w=1200',
            'https://images.pexels.com/photos/275484/pexels-photo-275484.jpeg?auto=compress&cs=tinysrgb&w=1200',
            'https://images.pexels.com/photos/210265/pexels-photo-210265.jpeg?auto=compress&cs=tinysrgb&w=1200'
        ];        

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
                'Image_Image_URL'  => $featuredImage,
                'Image_Is_Featured' => true,
            ]);

            // Select up to 2 non-featured images (interiors)
            $selectedInteriorImages = array_slice($interiorImages, 0, 2);
            
            foreach ($selectedInteriorImages as $imageUrl) {
                PropertyImage::create([
                    'Property_ID'      => $property->Property_ID,
                    'Image_Image_URL'  => $imageUrl,
                    'Image_Is_Featured' => false,
                ]);
            }
        }

        // Create Messages (between landlords & tenants)
        foreach ($tenants as $tenant) {
            foreach ($properties as $property) {
                if (rand(0, 1)) { // Randomly assign messages
                    Message::create([
                        'Sender_ID'    => $tenant->User_ID,
                        'Receiver_ID'  => $property->User_ID,
                        'Property_ID'  => $property->Property_ID,
                        'Message_Message_Text' => "Hello, I'm interested in {$property->Property_Title}. Is it available?",
                        'Message_Sent_At' => now(),
                        'Message_CreatedAt'    => now(),
                        'Message_UpdatedAt'    => now(),
                    ]);
                }
            }
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
