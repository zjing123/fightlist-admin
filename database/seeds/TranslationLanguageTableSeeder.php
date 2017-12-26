<?php

use Illuminate\Database\Seeder;
use App\Models\TranslationLanguage;

class TranslationLanguageTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (TranslationLanguage::count() == 0) {
            TranslationLanguage::create([
                'locale' => 'zh_CN',
                'title' => '中文简体'
            ]);
        }
    }
}

//['locale' => 'zh_CN', 'title' => '中文简体'],
//                ['locale' => 'zh_TW', 'title' => '繁体(臺灣)'],
//                ['locale' => 'zh_HK', 'title' => '繁体(香港)']