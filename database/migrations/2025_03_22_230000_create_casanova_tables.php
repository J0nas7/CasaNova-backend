<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Helpers\MigrationHelper;

class CreateCasaNovaTables extends Migration
{
    public function up()
    {
        // Users table (Landlords and Tenants)
        Schema::create('CN_Users', function (Blueprint $table) {
            $prefix = 'User_';

            $table->bigIncrements($prefix . 'ID'); // Primary key
            $table->string($prefix . 'First_Name', 255);
            $table->string($prefix . 'Last_Name', 255);
            $table->string($prefix . 'Email')->unique(); // Unique email
            $table->string($prefix . 'Password');
            $table->string($prefix . 'Phone_Number')->nullable();
            $table->enum($prefix . 'Role', ['Tenant', 'Landlord', 'Administrator']);
            $table->string($prefix . 'Profile_Picture')->nullable();
            $table->string($prefix . 'Address')->nullable();

            MigrationHelper::addDateTimeFields($table, $prefix); // Add common dateTime fields
        });

        // Properties table (Landlords create property listings)
        Schema::create('CN_Properties', function (Blueprint $table) {
            $prefix = 'Property_';

            $table->bigIncrements($prefix . 'ID'); // Primary key
            $table->bigInteger('User_ID')->unsigned(); // Foreign key to CN_Users (Landlord)
            $table->string($prefix . 'Title', 255); // Property title
            $table->text($prefix . 'Description')->nullable(); // Property description
            $table->string($prefix . 'Address', 500);
            $table->decimal($prefix . 'Latitude');
            $table->decimal($prefix . 'Longitude');
            $table->string($prefix . 'City', 255);
            // $table->string($prefix . 'State', 255);
            $table->integer($prefix . 'Zip_Code');
            $table->decimal($prefix . 'Price_Per_Month', 10, 2); // Monthly price
            $table->integer($prefix . 'Num_Bedrooms');
            $table->integer($prefix . 'Num_Bathrooms');
            $table->integer($prefix . 'Square_Feet');
            $table->json($prefix . 'Amenities')->nullable(); // List of amenities
            $table->string($prefix . 'Property_Type', 50); // Apartment, House, etc.
            $table->date($prefix . 'Available_From')->nullable();
            $table->string($prefix . 'Available_To', 50); // 3-6 months, 6-12 months, 1-2 years, 2+ years etc.
            $table->boolean($prefix . 'Is_Active')->default(true); // Property availability

            MigrationHelper::addDateTimeFields($table, $prefix); // Add common dateTime fields

            $table->foreign('User_ID')->references('User_ID')->on('CN_Users')->onDelete('cascade');
        });

        // Property Images table
        Schema::create('CN_Property_Images', function (Blueprint $table) {
            $prefix = 'Image_';

            $table->bigIncrements($prefix . 'ID'); // Primary key
            $table->bigInteger('Property_ID')->unsigned(); // Foreign key to CN_Properties
            $table->string($prefix . 'Name', 255)->nullable(); // File name
            $table->string($prefix . 'Path', 512)->nullable(); // Storage path
            $table->string($prefix . 'Type', 100)->nullable(); // File type (image, video, etc.)
            $table->string($prefix . 'URL', 512)->nullable(); // Image URL
            $table->integer($prefix . 'Order'); // Image Order

            MigrationHelper::addDateTimeFields($table, $prefix); // Add common dateTime fields

            $table->foreign('Property_ID')->references('Property_ID')->on('CN_Properties')->onDelete('cascade');
        });

        // Messages table (communication between tenants and landlords)
        Schema::create('CN_Messages', function (Blueprint $table) {
            $prefix = 'Message_';

            $table->bigIncrements($prefix . 'ID'); // Primary key
            $table->bigInteger('Sender_ID')->unsigned(); // Foreign key to CN_Users (sender)
            $table->bigInteger('Receiver_ID')->unsigned(); // Foreign key to CN_Users (receiver)
            $table->bigInteger('Property_ID')->unsigned(); // Foreign key to CN_Properties (property)
            $table->text($prefix . 'Text'); // Message content
            $table->timestamp($prefix . 'Read_At')->nullable(); // Read timestamp (nullable)

            MigrationHelper::addDateTimeFields($table, $prefix); // Add common dateTime fields

            $table->foreign('Sender_ID')->references('User_ID')->on('CN_Users')->onDelete('cascade');
            $table->foreign('Receiver_ID')->references('User_ID')->on('CN_Users')->onDelete('cascade');
            $table->foreign('Property_ID')->references('Property_ID')->on('CN_Properties')->onDelete('cascade');
        });

        // Favorites table (tenants can favorite properties they are interested in)
        Schema::create('CN_Favorites', function (Blueprint $table) {
            $prefix = 'Favorite_';

            $table->bigIncrements($prefix . 'ID'); // Primary key
            $table->bigInteger('Tenant_ID')->unsigned(); // Foreign key to CN_Users (tenant)
            $table->bigInteger('Property_ID')->unsigned(); // Foreign key to CN_Properties

            MigrationHelper::addDateTimeFields($table, $prefix); // Add common dateTime fields

            $table->foreign('Tenant_ID')->references('User_ID')->on('CN_Users')->onDelete('cascade');
            $table->foreign('Property_ID')->references('Property_ID')->on('CN_Properties')->onDelete('cascade');
        });

        // Notifications table (for system messages like property availability)
        Schema::create('CN_Notifications', function (Blueprint $table) {
            $prefix = 'Notification_';

            $table->bigIncrements($prefix . 'ID'); // Primary key
            $table->bigInteger('User_ID')->unsigned(); // User receiving the notification
            $table->string($prefix . 'Message', 500); // Notification message
            $table->boolean($prefix . 'Read')->default(false); // Read status

            MigrationHelper::addDateTimeFields($table, $prefix); // Add common dateTime fields

            $table->foreign('User_ID')->references('User_ID')->on('CN_Users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('CN_Notifications');
        Schema::dropIfExists('CN_Favorites');
        Schema::dropIfExists('CN_Messages');
        Schema::dropIfExists('CN_Property_Images');
        Schema::dropIfExists('CN_Properties');
        Schema::dropIfExists('CN_Users');
    }
}
