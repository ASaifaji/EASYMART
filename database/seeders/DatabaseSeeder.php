<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        // Seed data yang sudah ada di database
        $this->call([
            // Indonesia location data (order matters!)
            IndonesiaProvincesTableSeeder::class,
            IndonesiaCitiesTableSeeder::class,
            IndonesiaDistrictsTableSeeder::class,
            IndonesiaVillagesTableSeeder::class,
            
            // Master data
            CategoriesTableSeeder::class,
            
            // User & Seller data
            UsersTableSeeder::class,
            SellersTableSeeder::class,
            
            // Product data
            ProductsTableSeeder::class,
            ProductImagesTableSeeder::class,
            
            // Review data
            ReviewsTableSeeder::class,
        ]);

        Schema::enableForeignKeyConstraints();
    }
}