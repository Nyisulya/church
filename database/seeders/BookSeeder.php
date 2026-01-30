<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Book;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ellen G. White books in Swahili - using online links
        // These PDFs are hosted online and free to access
        $books = [
            [
                'title' => 'Hatua kwa Kristo (Steps to Christ)',
                'description' => 'Kitabu hiki kinaelezea jinsi ya kuwa Mkristo na kukua katika imani. Je, unawezaje kupata amani na furaha ya kweli? Kitabu hiki kinaeleza njia rahisi ya kuja kwa Kristo.',
                'file_path' => 'https://m.egwwritings.org/pdf/sw_SC(SW).pdf',
                'cover_image_path' => null,
            ],
            [
                'title' => 'Njia ya Kristo (Christ\'s Object Lessons)',
                'description' => 'Mafundisho ya Yesu kupitia mifano na misemo. Kitabu hiki kinafafanua mithali ya Yesu kwa njia rahisi kuelewa.',
                'file_path' => 'https://m.egwwritings.org/pdf/sw_COL(SW).pdf',
                'cover_image_path' => null,
            ],
            [
                'title' => 'Mapambano Makuu (The Great Controversy)',
                'description' => 'Historia ya mapambano kati ya mema na mabaya, kuanzia uharibifu wa Yerusalemu hadi mwisho wa wakati.',
                'file_path' => 'https://m.egwwritings.org/pdf/sw_GC(SW).pdf',
                'cover_image_path' => null,
            ],
            [
                'title' => 'Uzazi wa Wahenga (Patriarchs and Prophets)',
                'description' => 'Historia ya ulimwengu kuanzia uumbaji hadi utawala wa Mfalme Daudi.',
                'file_path' => 'https://m.egwwritings.org/pdf/sw_PP(SW).pdf',
                'cover_image_path' => null,
            ],
            [
                'title' => 'Maneno ya Waokozi (Words to the Little Flock)',
                'description' => 'Ujumbe wa matumaini kwa familia ya Mungu.',
                'file_path' => 'https://m.egwwritings.org/pdf/sw_WLF(SW).pdf',
                'cover_image_path' => null,
            ],
            [
                'title' => 'Ushuhuda wa Yesu (Testimonies for the Church Vol 1)',
                'description' => 'Ushuhuda na mawaidha kwa kanisa.',
                'file_path' => 'https://m.egwwritings.org/pdf/sw_1T(SW).pdf',
                'cover_image_path' => null,
            ],
            [
                'title' => 'Sheria ya Mungu (The Law of God)',
                'description' => 'Ufafanuzi wa Amri Kumi na umuhimu wake leo.',
                'file_path' => 'https://m.egwwritings.org/pdf/sw_LOG(SW).pdf',
                'cover_image_path' => null,
            ],
            [
                'title' => 'Uzima Bora (The Ministry of Healing)',
                'description' => 'Miongozo ya maisha yenye afya, msimamo wa Kikristo kuhusu tiba na afya.',
                'file_path' => 'https://m.egwwritings.org/pdf/sw_MH(SW).pdf',
                'cover_image_path' => null,
            ],
        ];

        foreach ($books as $bookData) {
            Book::create([
                'title' => $bookData['title'],
                'author' => 'Ellen G. White',
                'language' => 'sw',
                'description' => $bookData['description'],
                'file_path' => $bookData['file_path'],
                'cover_image_path' => $bookData['cover_image_path'],
            ]);
        }
    }
}
