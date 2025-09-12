<?php

namespace Database\Seeders;

use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $warehouses = [
            [
                'name' => 'Bakı Anbarı',
                'code' => 'BAK',
                'address' => 'Bakı şəhəri, Nəsimi rayonu, Azadlıq prospekti 123',
                'manager' => 'Əli Məmmədov',
                'phone' => '+994 50 123 45 67',
                'is_active' => true
            ],
            [
                'name' => 'Gəncə Anbarı',
                'code' => 'GAN',
                'address' => 'Gəncə şəhəri, Nizami küçəsi 45',
                'manager' => 'Vüsal İbrahimov',
                'phone' => '+994 55 987 65 43',
                'is_active' => true
            ]
        ];

        foreach ($warehouses as $warehouse) {
            Warehouse::updateOrCreate(
                ['code' => $warehouse['code']], // Unique field
                $warehouse
            );
        }

        $this->command->info('Warehouses created successfully!');

        // Statistics göstər
        $bakiWarehouse = Warehouse::getBaku();
        $ganjaWarehouse = Warehouse::getGanja();

        if ($bakiWarehouse) {
            $this->command->info("Bakı anbarında {$bakiWarehouse->getTotalProductsCount()} məhsul var.");
        }

        if ($ganjaWarehouse) {
            $this->command->info("Gəncə anbarında {$ganjaWarehouse->getTotalProductsCount()} məhsul var.");
        }
    }
}
