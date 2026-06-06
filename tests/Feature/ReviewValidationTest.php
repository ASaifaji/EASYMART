<?php

use App\Models\Product;
use App\Models\Review;
use App\Models\Seller;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;
use function Pest\Laravel\assertDatabaseMissing;

uses(RefreshDatabase::class);

test('menolak ulasan tanpa rating', function () {
	// Buat user pembeli
	$user = User::factory()->create();

	// Buat seller dan produk
	$seller = Seller::create([
		'user_id' => User::factory()->create([
			'role' => User::ROLE_PENJUAL,
		])->id,
		'storeName' => 'Toko Elektronik Sejaya',
		'storeDescription' => 'Toko elektronik berkualitas dan terpercaya.',
		'picName' => 'Budi Santoso',
		'picPhone' => '081234567890',
		'picEmail' => 'budi@example.com',
		'picStreet' => 'Jl. Ahmad Yani No. 50',
		'picRT' => '001',
		'picRW' => '003',
		'picVillage' => 'Cipete',
		'picCity' => 'Jakarta Selatan',
		'picProvince' => 'DKI Jakarta',
		'picKtpNumber' => '3175010101010003',
		'picPhotoPath' => null,
		'picKtpFilePath' => null,
		'status' => 'approved',
	]);

	$product = Product::create([
		'seller_id' => $seller->id,
		'name' => 'Laptop Gaming Terbaru',
		'description' => 'Laptop gaming dengan spesifikasi tinggi.',
		'price' => 12000000,
		'stock' => 20,
		'category' => 'Elektronik',
		'image' => null,
	]);

	// Kirim ulasan tanpa rating
	$response = actingAs($user)->post(route('products.reviews.store', $product), [
		'comment' => 'Produknya bagus dan pengiriman cepat.',
		'rating' => '', // Rating kosong
		'provinsi' => 'DKI Jakarta',
	]);

	// Verifikasi response menampilkan error validasi
	$response->assertSessionHasErrors('rating');
	$response->assertSessionHasErrors();

	// Verifikasi review tidak tersimpan di database
	assertDatabaseMissing('reviews', [
		'product_id' => $product->id,
		'user_id' => $user->id,
		'comment' => 'Produknya bagus dan pengiriman cepat.',
	]);

	// Verifikasi tidak ada review untuk produk ini
	expect(Review::where('product_id', $product->id)->count())->toBe(0);
});