<?php

namespace Database\Seeders;

use App\Models\Division;
use App\Models\District;
use Illuminate\Database\Seeder;

class BangladeshGeoSeeder extends Seeder
{
    public function run(): void
    {
        $divisions = [
            ['name' => 'ঢাকা', 'name_en' => 'Dhaka', 'slug' => 'dhaka'],
            ['name' => 'চট্টগ্রাম', 'name_en' => 'Chittagong', 'slug' => 'chittagong'],
            ['name' => 'রাজশাহী', 'name_en' => 'Rajshahi', 'slug' => 'rajshahi'],
            ['name' => 'খুলনা', 'name_en' => 'Khulna', 'slug' => 'khulna'],
            ['name' => 'বরিশাল', 'name_en' => 'Barisal', 'slug' => 'barisal'],
            ['name' => 'সিলেট', 'name_en' => 'Sylhet', 'slug' => 'sylhet'],
            ['name' => 'রংপুর', 'name_en' => 'Rangpur', 'slug' => 'rangpur'],
            ['name' => 'ময়মনসিংহ', 'name_en' => 'Mymensingh', 'slug' => 'mymensingh'],
        ];

        foreach ($divisions as $data) {
            Division::updateOrCreate(['slug' => $data['slug']], $data);
        }

        $divisionMap = Division::pluck('id', 'slug');

        $districts = [
            // Dhaka (8)
            ['name' => 'ঢাকা', 'name_en' => 'Dhaka', 'slug' => 'dhaka', 'division_slug' => 'dhaka'],
            ['name' => 'গাজীপুর', 'name_en' => 'Gazipur', 'slug' => 'gazipur', 'division_slug' => 'dhaka'],
            ['name' => 'কিশোরগঞ্জ', 'name_en' => 'Kishoreganj', 'slug' => 'kishoreganj', 'division_slug' => 'dhaka'],
            ['name' => 'মানিকগঞ্জ', 'name_en' => 'Manikganj', 'slug' => 'manikganj', 'division_slug' => 'dhaka'],
            ['name' => 'মুন্সীগঞ্জ', 'name_en' => 'Munshiganj', 'slug' => 'munshiganj', 'division_slug' => 'dhaka'],
            ['name' => 'নারায়ণগঞ্জ', 'name_en' => 'Narayanganj', 'slug' => 'narayanganj', 'division_slug' => 'dhaka'],
            ['name' => 'নরসিংদী', 'name_en' => 'Narsingdi', 'slug' => 'narsingdi', 'division_slug' => 'dhaka'],
            ['name' => 'টাঙ্গাইল', 'name_en' => 'Tangail', 'slug' => 'tangail', 'division_slug' => 'dhaka'],
            ['name' => 'ফরিদপুর', 'name_en' => 'Faridpur', 'slug' => 'faridpur', 'division_slug' => 'dhaka'],
            ['name' => 'গোপালগঞ্জ', 'name_en' => 'Gopalganj', 'slug' => 'gopalganj', 'division_slug' => 'dhaka'],
            ['name' => 'মাদারীপুর', 'name_en' => 'Madaripur', 'slug' => 'madaripur', 'division_slug' => 'dhaka'],
            ['name' => 'রাজবাড়ী', 'name_en' => 'Rajbari', 'slug' => 'rajbari', 'division_slug' => 'dhaka'],
            ['name' => 'শরীয়তপুর', 'name_en' => 'Shariatpur', 'slug' => 'shariatpur', 'division_slug' => 'dhaka'],
            // Chittagong (11)
            ['name' => 'চট্টগ্রাম', 'name_en' => 'Chattogram', 'slug' => 'chattogram', 'division_slug' => 'chittagong'],
            ['name' => 'বান্দরবান', 'name_en' => 'Bandarban', 'slug' => 'bandarban', 'division_slug' => 'chittagong'],
            ['name' => 'ব্রাহ্মণবাড়িয়া', 'name_en' => 'Brahmanbaria', 'slug' => 'brahmanbaria', 'division_slug' => 'chittagong'],
            ['name' => 'চাঁদপুর', 'name_en' => 'Chandpur', 'slug' => 'chandpur', 'division_slug' => 'chittagong'],
            ['name' => 'কুমিল্লা', 'name_en' => 'Comilla', 'slug' => 'comilla', 'division_slug' => 'chittagong'],
            ['name' => 'কক্সবাজার', 'name_en' => 'Cox\'s Bazar', 'slug' => 'coxs-bazar', 'division_slug' => 'chittagong'],
            ['name' => 'ফেনী', 'name_en' => 'Feni', 'slug' => 'feni', 'division_slug' => 'chittagong'],
            ['name' => 'খাগড়াছড়ি', 'name_en' => 'Khagrachhari', 'slug' => 'khagrachhari', 'division_slug' => 'chittagong'],
            ['name' => 'লক্ষ্মীপুর', 'name_en' => 'Lakshmipur', 'slug' => 'lakshmipur', 'division_slug' => 'chittagong'],
            ['name' => 'নোয়াখালী', 'name_en' => 'Noakhali', 'slug' => 'noakhali', 'division_slug' => 'chittagong'],
            ['name' => 'রাঙ্গামাটি', 'name_en' => 'Rangamati', 'slug' => 'rangamati', 'division_slug' => 'chittagong'],
            // Rajshahi (8)
            ['name' => 'রাজশাহী', 'name_en' => 'Rajshahi', 'slug' => 'rajshahi-city', 'division_slug' => 'rajshahi'],
            ['name' => 'বগুড়া', 'name_en' => 'Bogura', 'slug' => 'bogura', 'division_slug' => 'rajshahi'],
            ['name' => 'জয়পুরহাট', 'name_en' => 'Joypurhat', 'slug' => 'joypurhat', 'division_slug' => 'rajshahi'],
            ['name' => 'নওগাঁ', 'name_en' => 'Naogaon', 'slug' => 'naogaon', 'division_slug' => 'rajshahi'],
            ['name' => 'নাটোর', 'name_en' => 'Natore', 'slug' => 'natore', 'division_slug' => 'rajshahi'],
            ['name' => 'চাঁপাইনবাবগঞ্জ', 'name_en' => 'Chapainawabganj', 'slug' => 'chapainawabganj', 'division_slug' => 'rajshahi'],
            ['name' => 'পাবনা', 'name_en' => 'Pabna', 'slug' => 'pabna', 'division_slug' => 'rajshahi'],
            ['name' => 'সিরাজগঞ্জ', 'name_en' => 'Sirajganj', 'slug' => 'sirajganj', 'division_slug' => 'rajshahi'],
            // Khulna (10)
            ['name' => 'খুলনা', 'name_en' => 'Khulna', 'slug' => 'khulna-city', 'division_slug' => 'khulna'],
            ['name' => 'বাগেরহাট', 'name_en' => 'Bagerhat', 'slug' => 'bagerhat', 'division_slug' => 'khulna'],
            ['name' => 'চুয়াডাঙ্গা', 'name_en' => 'Chuadanga', 'slug' => 'chuadanga', 'division_slug' => 'khulna'],
            ['name' => 'যশোর', 'name_en' => 'Jashore', 'slug' => 'jashore', 'division_slug' => 'khulna'],
            ['name' => 'ঝিনাইদহ', 'name_en' => 'Jhenaidah', 'slug' => 'jhenaidah', 'division_slug' => 'khulna'],
            ['name' => 'কুষ্টিয়া', 'name_en' => 'Kushtia', 'slug' => 'kushtia', 'division_slug' => 'khulna'],
            ['name' => 'মাগুরা', 'name_en' => 'Magura', 'slug' => 'magura', 'division_slug' => 'khulna'],
            ['name' => 'মেহেরপুর', 'name_en' => 'Meherpur', 'slug' => 'meherpur', 'division_slug' => 'khulna'],
            ['name' => 'নড়াইল', 'name_en' => 'Narail', 'slug' => 'narail', 'division_slug' => 'khulna'],
            ['name' => 'সাতক্ষীরা', 'name_en' => 'Satkhira', 'slug' => 'satkhira', 'division_slug' => 'khulna'],
            // Barisal (6)
            ['name' => 'বরিশাল', 'name_en' => 'Barisal', 'slug' => 'barisal-city', 'division_slug' => 'barisal'],
            ['name' => 'ভোলা', 'name_en' => 'Bhola', 'slug' => 'bhola', 'division_slug' => 'barisal'],
            ['name' => 'ঝালকাঠি', 'name_en' => 'Jhalokati', 'slug' => 'jhalokati', 'division_slug' => 'barisal'],
            ['name' => 'পটুয়াখালী', 'name_en' => 'Patuakhali', 'slug' => 'patuakhali', 'division_slug' => 'barisal'],
            ['name' => 'পিরোজপুর', 'name_en' => 'Pirojpur', 'slug' => 'pirojpur', 'division_slug' => 'barisal'],
            ['name' => 'বরগুনা', 'name_en' => 'Barguna', 'slug' => 'barguna', 'division_slug' => 'barisal'],
            // Sylhet (4)
            ['name' => 'সিলেট', 'name_en' => 'Sylhet', 'slug' => 'sylhet-city', 'division_slug' => 'sylhet'],
            ['name' => 'হবিগঞ্জ', 'name_en' => 'Habiganj', 'slug' => 'habiganj', 'division_slug' => 'sylhet'],
            ['name' => 'মৌলভীবাজার', 'name_en' => 'Moulvibazar', 'slug' => 'moulvibazar', 'division_slug' => 'sylhet'],
            ['name' => 'সুনামগঞ্জ', 'name_en' => 'Sunamganj', 'slug' => 'sunamganj', 'division_slug' => 'sylhet'],
            // Rangpur (8)
            ['name' => 'রংপুর', 'name_en' => 'Rangpur', 'slug' => 'rangpur-city', 'division_slug' => 'rangpur'],
            ['name' => 'কুড়িগ্রাম', 'name_en' => 'Kurigram', 'slug' => 'kurigram', 'division_slug' => 'rangpur'],
            ['name' => 'লালমনিরহাট', 'name_en' => 'Lalmonirhat', 'slug' => 'lalmonirhat', 'division_slug' => 'rangpur'],
            ['name' => 'নীলফামারী', 'name_en' => 'Nilphamari', 'slug' => 'nilphamari', 'division_slug' => 'rangpur'],
            ['name' => 'দিনাজপুর', 'name_en' => 'Dinajpur', 'slug' => 'dinajpur', 'division_slug' => 'rangpur'],
            ['name' => 'ঠাকুরগাঁও', 'name_en' => 'Thakurgaon', 'slug' => 'thakurgaon', 'division_slug' => 'rangpur'],
            ['name' => 'পঞ্চগড়', 'name_en' => 'Panchagarh', 'slug' => 'panchagarh', 'division_slug' => 'rangpur'],
            ['name' => 'গাইবান্ধা', 'name_en' => 'Gaibandha', 'slug' => 'gaibandha', 'division_slug' => 'rangpur'],
            // Mymensingh (4)
            ['name' => 'ময়মনসিংহ', 'name_en' => 'Mymensingh', 'slug' => 'mymensingh-city', 'division_slug' => 'mymensingh'],
            ['name' => 'জামালপুর', 'name_en' => 'Jamalpur', 'slug' => 'jamalpur', 'division_slug' => 'mymensingh'],
            ['name' => 'নেত্রকোনা', 'name_en' => 'Netrokona', 'slug' => 'netrokona', 'division_slug' => 'mymensingh'],
            ['name' => 'শেরপুর', 'name_en' => 'Sherpur', 'slug' => 'sherpur', 'division_slug' => 'mymensingh'],
        ];

        foreach ($districts as $data) {
            $divisionId = $divisionMap[$data['division_slug']] ?? null;
            if ($divisionId === null) {
                continue;
            }

            District::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'division_id' => $divisionId,
                    'name' => $data['name'],
                    'name_en' => $data['name_en'],
                ],
            );
        }
    }
}
