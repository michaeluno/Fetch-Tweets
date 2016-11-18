<?php
/**
 *    Provides utility plugin specific methods.
 *
 * @package     Fetch Tweets
 * @copyright   Copyright (c) 2013, Michael Uno
 * @authorurl   http://michaeluno.jp
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.3.6
 * 
 */

class FetchTweets_PluginUtility extends FetchTweets_WPUtility {
    
    /**
     * @return      string
     */
    static public function getCurrentURL( /* $aQueries=array() */ ) {
        $_aParams = func_get_args() + array( array() );
        $aQueries = $_aParams[ 0 ];
        return home_url( 
            add_query_arg( 
                $aQueries, 
                $GLOBALS[ 'wp' ]->request 
            )
        );
    }
    
    /**
     * Returns an array of language list for the Twitter Search API.
     * @since       2.4.7
     * @see         https://dev.twitter.com/rest/reference/get/search/tweets
     * @return      array
     */
    static public function getLanguageListForSearchAPI() {

        return array( 
            'none' => __( 'Any Language', 'fetch-tweets' ),
            'ab'    => __( 'Abkhaz (аҧсуа бызшәа, аҧсшәа)', 'fetch-tweets' ),
            'aa'    => __( 'Afar (Afaraf)', 'fetch-tweets' ),
            'af'    => __( 'Afrikaans (Afrikaans)', 'fetch-tweets' ),
            'ak'    => __( 'Akan (Akan)', 'fetch-tweets' ),
            'sq'    => __( 'Albanian (Shqip)', 'fetch-tweets' ),
            'am'    => __( 'Amharic (አማርኛ)', 'fetch-tweets' ),
            'ar'    => __( 'Arabic (العربية)', 'fetch-tweets' ),
            'an'    => __( 'Aragonese (aragonés)', 'fetch-tweets' ),
            'hy'    => __( 'Armenian (Հայերեն)', 'fetch-tweets' ),
            'as'    => __( 'Assamese (অসমীয়া)', 'fetch-tweets' ),
            'av'    => __( 'Avaric (авар мацӀ, магӀарул мацӀ)', 'fetch-tweets' ),
            'ae'    => __( 'Avestan (avesta)', 'fetch-tweets' ),
            'ay'    => __( 'Aymara (aymar aru)', 'fetch-tweets' ),
            'az'    => __( 'Azerbaijani (azərbaycan dili)', 'fetch-tweets' ),
            'bm'    => __( 'Bambara (bamanankan)', 'fetch-tweets' ),
            'ba'    => __( 'Bashkir (башҡорт теле)', 'fetch-tweets' ),
            'eu'    => __( 'Basque (euskara, euskera)', 'fetch-tweets' ),
            'be'    => __( 'Belarusian (беларуская мова)', 'fetch-tweets' ),
            'bn'    => __( 'Bengali, Bangla (বাংলা)', 'fetch-tweets' ),
            'bh'    => __( 'Bihari (भोजपुरी)', 'fetch-tweets' ),
            'bi'    => __( 'Bislama (Bislama)', 'fetch-tweets' ),
            'bs'    => __( 'Bosnian (bosanski jezik)', 'fetch-tweets' ),
            'br'    => __( 'Breton (brezhoneg)', 'fetch-tweets' ),
            'bg'    => __( 'Bulgarian (български език)', 'fetch-tweets' ),
            'my'    => __( 'Burmese (ဗမာစာ)', 'fetch-tweets' ),
            'ca'    => __( 'Catalan (català)', 'fetch-tweets' ),
            'ch'    => __( 'Chamorro (Chamoru)', 'fetch-tweets' ),
            'ce'    => __( 'Chechen (нохчийн мотт)', 'fetch-tweets' ),
            'ny'    => __( 'Chichewa, Chewa, Nyanja (chiCheŵa, chinyanja)', 'fetch-tweets' ),
            'zh'    => __( 'Chinese (中文 (Zhōngwén), 汉语, 漢語)', 'fetch-tweets' ),
            'cv'    => __( 'Chuvash (чӑваш чӗлхи)', 'fetch-tweets' ),
            'kw'    => __( 'Cornish (Kernewek)', 'fetch-tweets' ),
            'co'    => __( 'Corsican (corsu, lingua corsa)', 'fetch-tweets' ),
            'cr'    => __( 'Cree (ᓀᐦᐃᔭᐍᐏᐣ)', 'fetch-tweets' ),
            'hr'    => __( 'Croatian (hrvatski jezik)', 'fetch-tweets' ),
            'cs'    => __( 'Czech (čeština, český jazyk)', 'fetch-tweets' ),
            'da'    => __( 'Danish (dansk)', 'fetch-tweets' ),
            'dv'    => __( 'Divehi, Dhivehi, Maldivian (ދިވެހި)', 'fetch-tweets' ),
            'nl'    => __( 'Dutch (Nederlands, Vlaams)', 'fetch-tweets' ),
            'dz'    => __( 'Dzongkha (རྫོང་ཁ)', 'fetch-tweets' ),
            'en'    => __( 'English (English)', 'fetch-tweets' ),
            'eo'    => __( 'Esperanto (Esperanto)', 'fetch-tweets' ),
            'et'    => __( 'Estonian (eesti, eesti keel)', 'fetch-tweets' ),
            'ee'    => __( 'Ewe (Eʋegbe)', 'fetch-tweets' ),
            'fo'    => __( 'Faroese (føroyskt)', 'fetch-tweets' ),
            'fj'    => __( 'Fijian (vosa Vakaviti)', 'fetch-tweets' ),
            'fi'    => __( 'Finnish (suomi, suomen kieli)', 'fetch-tweets' ),
            'fr'    => __( 'French (français, langue française)', 'fetch-tweets' ),
            'ff'    => __( 'Fula, Fulah, Pulaar, Pular (Fulfulde, Pulaar, Pular)', 'fetch-tweets' ),
            'gl'    => __( 'Galician (galego)', 'fetch-tweets' ),
            'ka'    => __( 'Georgian (ქართული)', 'fetch-tweets' ),
            'de'    => __( 'German (Deutsch)', 'fetch-tweets' ),
            'el'    => __( 'Greek (modern) (ελληνικά)', 'fetch-tweets' ),
            'gn'    => __( 'Guaraní (Avañe\'ẽ)', 'fetch-tweets' ),
            'gu'    => __( 'Gujarati (ગુજરાતી)', 'fetch-tweets' ),
            'ht'    => __( 'Haitian, Haitian Creole (Kreyòl ayisyen)', 'fetch-tweets' ),
            'ha'    => __( 'Hausa ((Hausa) هَوُسَ)', 'fetch-tweets' ),
            'he'    => __( 'Hebrew (modern) (עברית)', 'fetch-tweets' ),
            'hz'    => __( 'Herero (Otjiherero)', 'fetch-tweets' ),
            'hi'    => __( 'Hindi (हिन्दी, हिंदी)', 'fetch-tweets' ),
            'ho'    => __( 'Hiri Motu (Hiri Motu)', 'fetch-tweets' ),
            'hu'    => __( 'Hungarian (magyar)', 'fetch-tweets' ),
            'ia'    => __( 'Interlingua (Interlingua)', 'fetch-tweets' ),
            'id'    => __( 'Indonesian (Bahasa Indonesia)', 'fetch-tweets' ),
            'ie'    => __( 'Interlingue (Interlingue)', 'fetch-tweets' ),
            'ga'    => __( 'Irish (Gaeilge)', 'fetch-tweets' ),
            'ig'    => __( 'Igbo (Asụsụ Igbo)', 'fetch-tweets' ),
            'ik'    => __( 'Inupiaq (Iñupiaq, Iñupiatun)', 'fetch-tweets' ),
            'io'    => __( 'Ido (Ido)', 'fetch-tweets' ),
            'is'    => __( 'Icelandic (Íslenska)', 'fetch-tweets' ),
            'it'    => __( 'Italian (italiano)', 'fetch-tweets' ),
            'iu'    => __( 'Inuktitut (ᐃᓄᒃᑎᑐᑦ)', 'fetch-tweets' ),
            'ja'    => __( 'Japanese (日本語)', 'fetch-tweets' ),
            'jv'    => __( 'Javanese (basa Jawa)', 'fetch-tweets' ),
            'kl'    => __( 'Kalaallisut, Greenlandic (kalaallisut, kalaallit oqaasii)', 'fetch-tweets' ),
            'kn'    => __( 'Kannada (ಕನ್ನಡ)', 'fetch-tweets' ),
            'kr'    => __( 'Kanuri (Kanuri)', 'fetch-tweets' ),
            'ks'    => __( 'Kashmiri (कश्मीरी, كشميري‎)', 'fetch-tweets' ),
            'kk'    => __( 'Kazakh (қазақ тілі)', 'fetch-tweets' ),
            'km'    => __( 'Khmer (ខ្មែរ, ខេមរភាសា, ភាសាខ្មែរ)', 'fetch-tweets' ),
            'ki'    => __( 'Kikuyu, Gikuyu (Gĩkũyũ)', 'fetch-tweets' ),
            'rw'    => __( 'Kinyarwanda (Ikinyarwanda)', 'fetch-tweets' ),
            'ky'    => __( 'Kyrgyz (Кыргызча, Кыргыз тили)', 'fetch-tweets' ),
            'kv'    => __( 'Komi (коми кыв)', 'fetch-tweets' ),
            'kg'    => __( 'Kongo (Kikongo)', 'fetch-tweets' ),
            'ko'    => __( 'Korean (한국어, 조선어)', 'fetch-tweets' ),
            'ku'    => __( 'Kurdish (Kurdî, كوردی‎)', 'fetch-tweets' ),
            'kj'    => __( 'Kwanyama, Kuanyama (Kuanyama)', 'fetch-tweets' ),
            'la'    => __( 'Latin (latine, lingua latina)', 'fetch-tweets' ),
            'lb'    => __( 'Luxembourgish, Letzeburgesch (Lëtzebuergesch)', 'fetch-tweets' ),
            'lg'    => __( 'Ganda (Luganda)', 'fetch-tweets' ),
            'li'    => __( 'Limburgish, Limburgan, Limburger (Limburgs)', 'fetch-tweets' ),
            'ln'    => __( 'Lingala (Lingála)', 'fetch-tweets' ),
            'lo'    => __( 'Lao (ພາສາລາວ)', 'fetch-tweets' ),
            'lt'    => __( 'Lithuanian (lietuvių kalba)', 'fetch-tweets' ),
            'lu'    => __( 'Luba-Katanga (Tshiluba)', 'fetch-tweets' ),
            'lv'    => __( 'Latvian (latviešu valoda)', 'fetch-tweets' ),
            'gv'    => __( 'Manx (Gaelg, Gailck)', 'fetch-tweets' ),
            'mk'    => __( 'Macedonian (македонски јазик)', 'fetch-tweets' ),
            'mg'    => __( 'Malagasy (fiteny malagasy)', 'fetch-tweets' ),
            'ms'    => __( 'Malay (bahasa Melayu, بهاس ملايو‎)', 'fetch-tweets' ),
            'ml'    => __( 'Malayalam (മലയാളം)', 'fetch-tweets' ),
            'mt'    => __( 'Maltese (Malti)', 'fetch-tweets' ),
            'mi'    => __( 'Māori (te reo Māori)', 'fetch-tweets' ),
            'mr'    => __( 'Marathi (Marāṭhī) (मराठी)', 'fetch-tweets' ),
            'mh'    => __( 'Marshallese (Kajin M̧ajeļ)', 'fetch-tweets' ),
            'mn'    => __( 'Mongolian (монгол)', 'fetch-tweets' ),
            'na'    => __( 'Nauru (Ekakairũ Naoero)', 'fetch-tweets' ),
            'nv'    => __( 'Navajo, Navaho (Diné bizaad)', 'fetch-tweets' ),
            'nd'    => __( 'Northern Ndebele (isiNdebele)', 'fetch-tweets' ),
            'ne'    => __( 'Nepali (नेपाली)', 'fetch-tweets' ),
            'ng'    => __( 'Ndonga (Owambo)', 'fetch-tweets' ),
            'nb'    => __( 'Norwegian Bokmål (Norsk bokmål)', 'fetch-tweets' ),
            'nn'    => __( 'Norwegian Nynorsk (Norsk nynorsk)', 'fetch-tweets' ),
            'no'    => __( 'Norwegian (Norsk)', 'fetch-tweets' ),
            'ii'    => __( 'Nuosu (ꆈꌠ꒿ Nuosuhxop)', 'fetch-tweets' ),
            'nr'    => __( 'Southern Ndebele (isiNdebele)', 'fetch-tweets' ),
            'oc'    => __( 'Occitan (occitan, lenga d\'òc)', 'fetch-tweets' ),
            'oj'    => __( 'Ojibwe, Ojibwa (ᐊᓂᔑᓈᐯᒧᐎᓐ)', 'fetch-tweets' ),
            'cu'    => __( 'Old Church Slavonic, Church Slavonic, Old Bulgarian (ѩзыкъ словѣньскъ)', 'fetch-tweets' ),
            'om'    => __( 'Oromo (Afaan Oromoo)', 'fetch-tweets' ),
            'or'    => __( 'Oriya (ଓଡ଼ିଆ)', 'fetch-tweets' ),
            'os'    => __( 'Ossetian, Ossetic (ирон æвзаг)', 'fetch-tweets' ),
            'pa'    => __( 'Panjabi, Punjabi (ਪੰਜਾਬੀ, پنجابی‎)', 'fetch-tweets' ),
            'pi'    => __( 'Pāli (पाऴि)', 'fetch-tweets' ),
            'fa'    => __( 'Persian (Farsi) (فارسی)', 'fetch-tweets' ),
            'pl'    => __( 'Polish (język polski, polszczyzna)', 'fetch-tweets' ),
            'ps'    => __( 'Pashto, Pushto (پښتو)', 'fetch-tweets' ),
            'pt'    => __( 'Portuguese (português)', 'fetch-tweets' ),
            'qu'    => __( 'Quechua (Runa Simi, Kichwa)', 'fetch-tweets' ),
            'rm'    => __( 'Romansh (rumantsch grischun)', 'fetch-tweets' ),
            'rn'    => __( 'Kirundi (Ikirundi)', 'fetch-tweets' ),
            'ro'    => __( 'Romanian (limba română)', 'fetch-tweets' ),
            'ru'    => __( 'Russian (Русский)', 'fetch-tweets' ),
            'sa'    => __( 'Sanskrit (Saṁskṛta) (संस्कृतम्)', 'fetch-tweets' ),
            'sc'    => __( 'Sardinian (sardu)', 'fetch-tweets' ),
            'sd'    => __( 'Sindhi (सिन्धी, سنڌي، سندھی‎)', 'fetch-tweets' ),
            'se'    => __( 'Northern Sami (Davvisámegiella)', 'fetch-tweets' ),
            'sm'    => __( 'Samoan (gagana fa\'a Samoa)', 'fetch-tweets' ),
            'sg'    => __( 'Sango (yângâ tî sängö)', 'fetch-tweets' ),
            'sr'    => __( 'Serbian (српски језик)', 'fetch-tweets' ),
            'gd'    => __( 'Scottish Gaelic, Gaelic (Gàidhlig)', 'fetch-tweets' ),
            'sn'    => __( 'Shona (chiShona)', 'fetch-tweets' ),
            'si'    => __( 'Sinhala, Sinhalese (සිංහල)', 'fetch-tweets' ),
            'sk'    => __( 'Slovak (slovenčina, slovenský jazyk)', 'fetch-tweets' ),
            'sl'    => __( 'Slovene (slovenski jezik, slovenščina)', 'fetch-tweets' ),
            'so'    => __( 'Somali (Soomaaliga, af Soomaali)', 'fetch-tweets' ),
            'st'    => __( 'Southern Sotho (Sesotho)', 'fetch-tweets' ),
            'es'    => __( 'Spanish (español)', 'fetch-tweets' ),
            'su'    => __( 'Sundanese (Basa Sunda)', 'fetch-tweets' ),
            'sw'    => __( 'Swahili (Kiswahili)', 'fetch-tweets' ),
            'ss'    => __( 'Swati (SiSwati)', 'fetch-tweets' ),
            'sv'    => __( 'Swedish (svenska)', 'fetch-tweets' ),
            'ta'    => __( 'Tamil (தமிழ்)', 'fetch-tweets' ),
            'te'    => __( 'Telugu (తెలుగు)', 'fetch-tweets' ),
            'tg'    => __( 'Tajik (тоҷикӣ, toçikī, تاجیکی‎)', 'fetch-tweets' ),
            'th'    => __( 'Thai (ไทย)', 'fetch-tweets' ),
            'ti'    => __( 'Tigrinya (ትግርኛ)', 'fetch-tweets' ),
            'bo'    => __( 'Tibetan Standard, Tibetan, Central (བོད་ཡིག)', 'fetch-tweets' ),
            'tk'    => __( 'Turkmen (Türkmen, Түркмен)', 'fetch-tweets' ),
            'tl'    => __( 'Tagalog (Wikang Tagalog, ᜏᜒᜃᜅ᜔ ᜆᜄᜎᜓᜄ᜔)', 'fetch-tweets' ),
            'tn'    => __( 'Tswana (Setswana)', 'fetch-tweets' ),
            'to'    => __( 'Tonga (Tonga Islands) (faka Tonga)', 'fetch-tweets' ),
            'tr'    => __( 'Turkish (Türkçe)', 'fetch-tweets' ),
            'ts'    => __( 'Tsonga (Xitsonga)', 'fetch-tweets' ),
            'tt'    => __( 'Tatar (татар теле, tatar tele)', 'fetch-tweets' ),
            'tw'    => __( 'Twi (Twi)', 'fetch-tweets' ),
            'ty'    => __( 'Tahitian (Reo Tahiti)', 'fetch-tweets' ),
            'ug'    => __( 'Uyghur (ئۇيغۇرچە‎, Uyghurche)', 'fetch-tweets' ),
            'uk'    => __( 'Ukrainian (українська мова)', 'fetch-tweets' ),
            'ur'    => __( 'Urdu (اردو)', 'fetch-tweets' ),
            'uz'    => __( 'Uzbek (Oʻzbek, Ўзбек, أۇزبېك‎)', 'fetch-tweets' ),
            've'    => __( 'Venda (Tshivenḓa)', 'fetch-tweets' ),
            'vi'    => __( 'Vietnamese (Việtnam)', 'fetch-tweets' ),
            'vo'    => __( 'Volapük (Volapük)', 'fetch-tweets' ),
            'wa'    => __( 'Walloon (walon)', 'fetch-tweets' ),
            'cy'    => __( 'Welsh (Cymraeg)', 'fetch-tweets' ),
            'wo'    => __( 'Wolof (Wollof)', 'fetch-tweets' ),
            'fy'    => __( 'Western Frisian (Frysk)', 'fetch-tweets' ),
            'xh'    => __( 'Xhosa (isiXhosa)', 'fetch-tweets' ),
            'yi'    => __( 'Yiddish (ייִדיש)', 'fetch-tweets' ),
            'yo'    => __( 'Yoruba (Yorùbá)', 'fetch-tweets' ),
            'za'    => __( 'Zhuang, Chuang (Saɯ cueŋƅ, Saw cuengh)', 'fetch-tweets' ),
            'zu'    => __( 'Zulu (isiZulu)', 'fetch-tweets' ),            
        );    // ' syntax fixer
  
    }

    /**
     * Checks whether the page is loaded in one of the plugin admin pages.
     * 
     * @since       2.3.6
     */
    static public function isInPluginAdminPage() {
        
        static $_bIsPluginAdminPage;
        
        if ( isset( $_bIsPluginAdminPage ) ) {
            return $_bIsPluginAdminPage;
        }
        
        if ( ! is_admin() ) {
            return false;
        }
        if ( ! isset( $GLOBALS['pagenow'] ) ) {
            return false;
        }
        if ( ! in_array( $GLOBALS['pagenow'], array( 'edit.php', 'plugins.php' ) ) ) {
            return false;                
        }
        if ( 'plugins.php' === $GLOBALS['pagenow'] ) {
            return true;
        }
        if ( ! isset( $_GET['post_type'] ) ) {
            return false;
        }
        $_bIsPluginAdminPage = ( FetchTweets_Commons::PostTypeSlug === $_GET['post_type'] );
        return $_bIsPluginAdminPage;
            
    }

    /*
     * MISC methods.
     */
    /**
     * Returns an array holding the labels(names) of activated templates.
     * 
     * This is used for the widget form or the template meta box to let the user select a template.
     * 
     * @since       unknown
     * @since       2.3.9           Moved form the templates class.
     */
    static public function getTemplateArrayForSelectLabel( $aTemplates=null ) {
        
        $_oOption = FetchTweets_Option::getInstance();
        if ( ! $aTemplates ) {
            $aTemplates = $_oOption->getActiveTemplates();
        }

        $_aLabels = array();
        foreach ( $aTemplates as $_sSlug => $_aTemplate ) {
            $_oTemplate = new FetchTweets_Template( $_aTemplate['sSlug'] );
            $_sName     = $_oTemplate->get( 'sName' );            
            if ( ! $_sName ) { continue; }   // it may be broken.
            $_aLabels[ $_aTemplate['sSlug'] ] = $_sName;
        }
        return $_aLabels;
        
    }    
            
}
