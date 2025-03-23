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

class MyDemoSeeder extends Seeder
{
    public function run(): void
    {
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
        foreach ($landlords as $landlord) {
            for ($i = 1; $i <= 2; $i++) {
                $properties[] = Property::create([
                    'User_ID'               => $landlord->User_ID,
                    'Property_Title'        => "Property $i by {$landlord->User_First_Name}",
                    'Property_Description'  => "Description of property $i",
                    'Property_Address'      => "Address $i",
                    'Property_City'         => "City $i",
                    'Property_State'        => "State $i",
                    'Property_Zip_Code'     => "12345",
                    'Property_Price_Per_Month' => rand(1000, 5000),
                    'Property_Num_Bedrooms' => rand(1, 5),
                    'Property_Num_Bathrooms'=> rand(1, 3),
                    'Property_Square_Feet'  => rand(500, 2000),
                    'Property_Amenities'    => json_encode(['WiFi', 'Parking', 'Pool']),
                    'Property_Property_Type'=> 'Apartment',
                    'Property_Available_From' => now()->addDays(rand(5, 30)),
                    'Property_Is_Active'    => true,
                    'Property_CreatedAt'    => now(),
                    'Property_UpdatedAt'    => now(),
                ]);
            }
        }

        // Create Property Images with placecats.com
        foreach ($properties as $property) {
            for ($i = 1; $i <= 3; $i++) {
                PropertyImage::create([
                    'Property_ID'     => $property->Property_ID,
                    'Image_Image_URL' => "https://www.placecats.com/800/600?random=$i",
                    'Image_Is_Featured' => $i === 1,
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
?>