<?php

namespace Sturt\CitationScraper\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

Class Citation
{

    public function __construct()
    {
        header("Content-Type: application/json");
    }


    protected $error_string = "<p style='text-align: center;color: darkred;
                        font-size: 18px;
                        font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif'>
            In-valid payload</p>";

    static function listProviders()
    {
        $data = config('citationscraper.modules');
        return array_keys($data);
    }


    public static function index(Request $request)
    {

        $data = [
            'data_mondovo_name'      =>     urldecode($request->get('name')),
            'data_mondovo_address'      =>     urldecode($request->get('address')),
            'data_mondovo_phone'      =>      urldecode($request->get('phone')),
            'data_mondovo_zipcode'      =>      urldecode($request->get('zip')),
            'module_id'                 =>      urldecode($request->get('provider'))
        ];
        return self::ScrapeYext($data);
    }

    /**
     * @param This is on hold
     * @return mixed
     */
    static function getDBSites()
    {
        $data = \DB::table('websites')->get()->all();
        return $data;
    }

    /**
     * @param string $str - String to search the Scraped Content
     * @param string $URL - Which URL to be scraped
     * @param string $Xpath - Target of scraped html element path
     * @return array - returns the target Xpath node value
     */
    public function NativeScrape()
    {
        $output = [];

        echo "<pre>";

        print_r(self::GetFourSQ("mondovo", "Houston,TX"));

        foreach (self::getDBSites() as $site) {

            $URL = base64_encode(str_replace("[%keyword%]", "mondovo+address", $site->site_url));
            $scraped_content = $this->getURLData($URL);


            if ($scraped_content != '_error'):
                $DOM = new \DOMDocument;

                libxml_use_internal_errors(true);

                if (!$DOM->loadHTML($scraped_content)) {
                    $errors = "";
                    foreach (libxml_get_errors() as $error) {
                        $errors .= $error->message . "<br/>";
                    }
                    libxml_clear_errors();

                }
                $xpath = new \DOMXPath($DOM);
                $case1 = $xpath->query($site->pattern_1)->item(0);
                $case2 = $xpath->query($site->pattern_2)->item(0);
                $case3 = $xpath->query($site->pattern_3)->item(0);

                if (!empty($case1)):

                    print_r(self::getZipCodes($case2->nodeValue));

                    $output[] = [
                        'result' => [
                            'agent' => $site->site_name,
                            'pattern_1' => $case1->nodeValue,
                            'pattern_2' => $case2->nodeValue,
                            'pattern_3' => $case3->nodeValue,
                        ]
                    ];
                else:
                    $output[] = [
                        'result' => [
                            'agent' => $site->site_name,
                            'searched_string' => "",
                            'founded_string' => '404'
                        ]
                    ];
                endif;

            else:

                $output[] = $this->error_string;

            endif;

        }

        return $output;
    }

    /**
     * @param string $url - Base64 Encoded data
     * @return string
     **/

    public function getURLData($url = '')
    {

        if ($this->checkPayload($url)):
            $d_url = base64_decode($url);
            return $this->fcurl($d_url);
        else:
            return "_error";
        endif;
    }

    /**
     * @param $s - String to check the string is valid Base54 Encoded or Not
     * @return bool
     */

    public function checkPayload($s)
    {
        if (!preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $s)) return false;
        $decoded = base64_decode($s, true);
        if (false === $decoded) return false;

        if (0 < preg_match('/((?![[:graph:]])(?!\s)(?!\p{L}))./', $decoded, $matched)) return false;

        if (base64_encode($decoded) != $s) return false;

        return true;
    }

    /**
     * @param $URL - Url to fetch the contents
     * @return mixed
     */
    public function fcurl($URL)
    {

        $curl = curl_init($URL);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        $content = curl_exec($curl);
        if (curl_errno($curl)) {
            echo 'Scraper error: ' . curl_error($curl);
            exit;
        }
        return $content;
        curl_close($curl);
    }

    /**
     * @param $string -  A String contains Zip code
     * @return string
     */
    static function getZipCodes($string = '')
    {
        try {

            $zipcode = preg_match("/\b[A-Z]{2}\s+\d{5}(-\d{4})?\b/", $string, $m);
            return $m[0];

        } catch (\Exception $e) {
            return $e->getMessage();
        }

    }


    public function GetFourSQ($query = '', $where = '')
    {

        $d = date('Ymd');

        $client_id = config('citationscraper.four_square_client_id');
        $client_secret = config('citationscraper.four_square_client_secret');
        $params = "v=$d&ll=44.3,37.2&client_id=$client_id&client_secret=$client_secret&query=$query&near=$where";

        $url = config('citationscraper.four_square_end_point') . $params;

        $data = json_decode(self::fcurl($url));
        return $data;

    }

    static function ScrapeYext($args = [])
    {

        try {


            $url = "https://www.yext.com/pl/showmelocal-listing/sms_search";

            $providers = config('citationscraper.modules');



            $error = false;
            if ($args['data_mondovo_name'] == ""):
                $error = true;
            elseif ($args['data_mondovo_address'] == ""):
                $error = true;
            elseif ($args['data_mondovo_phone'] == ""):
                $error = true;
            elseif ($args['data_mondovo_zipcode'] == ""):
                $error = true;
            else:
                $error = false;
            endif;

            if (!$error):

            $data = [
                'name' => $args['data_mondovo_name'],
                'address' => $args['data_mondovo_address'],
                'phone' => $args['data_mondovo_phone'],
                'zip' => $args['data_mondovo_zipcode'],
                'country' => '',
                'provider' => 'sms_simple',
                'timeout' => '10',
                'session_id' => '203729008',
                'partner_ids' => $providers[$args['module_id']],
                'locati
                on_id' => ''
            ];

            $r = self::post_curl($url, $data);


            try {
                $result[$args['module_id']] = [
                    'status' => 200,
                    'found' => $r[0]->found,
                    'name' => $r[0]->name,
                    'match_name' => $r[0]->match_name,
                    'phone' => $r[0]->phone,
                    'match_phone' => $r[0]->match_phone,
                    'address' => $r[0]->address,
                    'match_address' => $r[0]->match_address,
                    'listing_url' => $r[0]->url
                ];
            } catch (\Exception $e) {

                $result[$args['module_id']] = [
                    'status' => 204,
                    'found' => FALSE,
                ];
            }

            else:
                $result[$args['module_id']] = [
                    'status' => 204,
                    'msg'       =>  'validation error'
                ];
                endif;



            return $result;


        } catch (\Exception  $exception) {
            return $exception->getMessage();
        }
    }


    /**
     * This functions Sends Post curl request
     * @param  string $url url where posts request is send
     * @param  array $post_data array containing post data with key value pairs
     * @return string
     */
    public static function post_curl($url, $post_data)
    {

        $ch = curl_init();
        $useragent = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.139 Safari/537.36";

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $output = curl_exec($ch);
        curl_close($ch);

        $r = json_decode($output);
        return $r;
    }

}
