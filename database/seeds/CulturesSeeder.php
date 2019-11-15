<?php

use Illuminate\Database\Seeder;

class CulturesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $cultures = [
            [
                'code'             => 'en_US',
                'english_name'     => 'English (US)',
                'name_key'         => 'global.culture.english_us',
                'quest_culture_id' => 1,
                'enabled'          => true,
            ],
            [
                'code'             => 'da_DK',
                'english_name'     => 'Danish',
                'name_key'         => 'global.culture.danish',
                'quest_culture_id' => 2,
                'enabled'          => false,
            ],
            [
                'code'             => 'de_DE',
                'english_name'     => 'German',
                'name_key'         => 'global.culture.german',
                'quest_culture_id' => 3,
                'enabled'          => false,
            ],
            [
                'code'             => 'es_ES',
                'english_name'     => 'Spanish',
                'name_key'         => 'global.culture.spanish',
                'quest_culture_id' => 4,
                'enabled'          => false,
            ],
            [
                'code'             => 'fr_FR',
                'english_name'     => 'French',
                'name_key'         => 'global.culture.french',
                'quest_culture_id' => 5,
                'enabled'          => false,
            ],
            [
                'code'             => 'it_IT',
                'english_name'     => 'Italian',
                'name_key'         => 'global.culture.italian',
                'quest_culture_id' => 6,
                'enabled'          => false,
            ],
            [
                'code'             => 'nl_NL',
                'english_name'     => 'Dutch',
                'name_key'         => 'global.culture.dutch',
                'quest_culture_id' => 7,
                'enabled'          => false,
            ],
            [
                'code'             => 'pt_BR',
                'english_name'     => 'Portuguese',
                'name_key'         => 'global.culture.portuguese',
                'quest_culture_id' => 8,
                'enabled'          => false,
            ],
            [
                'code'             => 'sv_SE',
                'english_name'     => 'Swedish',
                'name_key'         => 'global.culture.swedish',
                'quest_culture_id' => 9,
                'enabled'          => false,
            ],
            [
                'code'             => 'zh_CN',
                'english_name'     => 'Chinese (Simplified)',
                'name_key'         => 'global.culture.chinese_simplified',
                'quest_culture_id' => 10,
                'enabled'          => false,
            ],
            [
                'code'             => 'en_GB',
                'english_name'     => 'English (UK)',
                'name_key'         => 'global.culture.english_uk',
                'quest_culture_id' => 11,
                'enabled'          => false,
            ],
            [
                'code'             => 'pl_PL',
                'english_name'     => 'Polish',
                'name_key'         => 'global.culture.polish',
                'quest_culture_id' => 12,
                'enabled'          => false,
            ],
            [
                'code'             => 'ja_JP',
                'english_name'     => 'Japanese',
                'name_key'         => 'global.culture.japanese',
                'quest_culture_id' => 13,
                'enabled'          => false,
            ],
            [
                'code'             => 'fi_FI',
                'english_name'     => 'Finnish',
                'name_key'         => 'global.culture.finnish',
                'quest_culture_id' => 14,
                'enabled'          => false,
            ],
            [
                'code'             => 'cs_CZ',
                'english_name'     => 'Czech',
                'name_key'         => 'global.culture.czech',
                'quest_culture_id' => 15,
                'enabled'          => false,
            ],
            [
                'code'             => 'nb_NO',
                'english_name'     => 'Norwegian',
                'name_key'         => 'global.culture.norwegian',
                'quest_culture_id' => 16,
                'enabled'          => false,
            ],
            [
                'code'             => 'ko_KR',
                'english_name'     => 'Korean',
                'name_key'         => 'global.culture.korean',
                'quest_culture_id' => 17,
                'enabled'          => false,
            ],
            [
                'code'             => 'ar_SA',
                'english_name'     => 'Arabic',
                'name_key'         => 'global.culture.arabic',
                'quest_culture_id' => 18,
                'enabled'          => false,
            ],
            [
                'code'             => 'zh_TW',
                'english_name'     => 'Chinese (Traditional)',
                'name_key'         => 'global.culture.chinese_traditional',
                'quest_culture_id' => 19,
                'enabled'          => false,
            ],
        ];

        foreach ($cultures as $culture) {
            \Momentum\Culture::updateOrCreate([
                'code' => $culture['code'],
            ],
            $culture);
        }
    }
}
