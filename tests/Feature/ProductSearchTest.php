<?php

use App\Models\Product;
use App\Models\Seller;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

test('menampilkan produk sesuai kata kunci pencarian', function () {
	// Buat user sebagai penjual
	$user = User::factory()->create([
		'role' => User::ROLE_PENJUAL,
	]);

	// Buat seller berdasarkan user dengan data lengkap
	$seller = Seller::create([
		'user_id' => $user->id,
		'storeName' => 'Toko Kecantikan Mitra',
		'storeDescription' => 'Toko kosmetik dan produk kecantikan pilihan.',
		'picName' => 'Siti Nurhaliza',
		'picPhone' => '081234567890',
		'picEmail' => 'siti@example.com',
		'picStreet' => 'Jl. Sudirman No. 45',
		'picRT' => '001',
		'picRW' => '002',
		'picVillage' => 'Senayan',
		'picCity' => 'Jakarta Pusat',
		'picProvince' => 'DKI Jakarta',
		'picKtpNumber' => '3175010101010002',
		'picPhotoPath' => null,
		'picKtpFilePath' => null,
		'status' => 'approved',
	]);

	// Buat produk dengan kata kunci "Liptint"
	$product = Product::create([
		'seller_id' => $seller->id,
		'name' => 'Liptint Warna Merah',
		'description' => 'Liptint berkualitas tinggi dengan warna yang tahan lama dan tahan air.',
		'price' => 75000,
		'stock' => 50,
		'category' => 'Kosmetik',
		'image' => null,
	]);

	// Buat produk lain yang tidak sesuai dengan pencarian
	$otherProduct = Product::create([
		'seller_id' => $seller->id,
		'name' => 'Lipstik Klasik',
		'description' => 'Lipstik dengan formula yang lembut dan nyaman.',
		'price' => 85000,
		'stock' => 30,
		'category' => 'Kosmetik',
		'image' => null,
	]);

	// Lakukan pencarian dengan kata kunci "Liptint"
	$response = get(route('products.search', ['q' => 'Liptint']));

	// Verifikasi response status
	$response->assertStatus(200);

	// Verifikasi halaman berisi data produk yang sesuai
	$response->assertViewHas('products');
	$response->assertViewHas('query', 'Liptint');
	$response->assertViewHas('total');

	// Verifikasi produk "Liptint" ada dalam hasil pencarian
	$searchResults = $response->viewData('products');
	expect($searchResults->total())->toBe(1);
	expect($searchResults->first()->id)->toBe($product->id);
	expect($searchResults->first()->name)->toContain('Liptint');
});