<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;

uses(RefreshDatabase::class);

test('submit form registrasi penjual dengan semua field valid', function () {
	Storage::fake('public');

	$user = User::factory()->create();

	$response = actingAs($user)->post(route('seller.store'), [
		'storeName' => 'Toko Sumber Rejeki',
		'storeDescription' => 'Toko kebutuhan harian lengkap dan terjangkau.',
		'picName' => 'Andi Pratama',
		'picPhone' => '081234567890',
		'picEmail' => 'andi.pratama@example.com',
		'picStreet' => 'Jl. Melati No. 10',
		'picRT' => '001',
		'picRW' => '002',
		'picVillage' => 'Kebon Jeruk',
		'picCity' => 'Jakarta Barat',
		'picProvince' => 'DKI Jakarta',
		'picKtpNumber' => '3174010101010001',
		'picPhotoPath' => UploadedFile::fake()->image('foto-pic.jpg'),
		'picKtpFilePath' => UploadedFile::fake()->image('ktp-pic.jpg'),
	]);

	$response->assertRedirect(route('seller.register.waiting', absolute: false));
	$response->assertSessionHas('success', 'Pendaftaran berhasil! Silakan cek email Anda untuk konfirmasi.');

	assertDatabaseHas('users', [
		'name' => 'Andi Pratama',
		'email' => 'andi.pratama@example.com',
		'role' => User::ROLE_PENJUAL,
		'status_verifikasi' => User::STATUS_PENDING,
		'nama_toko' => 'Toko Sumber Rejeki',
		'deskripsi_singkat' => 'Toko kebutuhan harian lengkap dan terjangkau.',
		'no_handphone_pic' => '081234567890',
		'alamat_pic' => 'Jl. Melati No. 10',
		'rt' => '001',
		'rw' => '002',
		'nama_kelurahan' => 'Kebon Jeruk',
		'kabupaten_kota' => 'Jakarta Barat',
		'provinsi' => 'DKI Jakarta',
		'no_ktp_pic' => '3174010101010001',
	]);

	$seller = User::where('email', 'andi.pratama@example.com')->firstOrFail();

	expect($seller->foto_pic)->not->toBeNull();
	expect($seller->file_upload_ktp_pic)->not->toBeNull();
	expect(Storage::disk('public')->exists($seller->foto_pic))->toBeTrue();
	expect(Storage::disk('public')->exists($seller->file_upload_ktp_pic))->toBeTrue();
});
