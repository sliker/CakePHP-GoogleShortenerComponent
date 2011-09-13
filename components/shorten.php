<?php

class ShortenComponent extends Object
{
    var $name = 'Shorten';
    public $bitlyLogin; // obtain from https://bitly.com/a/your_api_key
    public $bitlyApiKey; // obtain from https://bitly.com/a/your_api_key
    public $bitlyFormat; // json, xml, txt

    public $kwnDomain; // kwn.me or quicklink.me
    public $kwnApiKey; // obtain from kwn.me/api?act=key

    public $yourlsDomain; // the domain YOURLS is installed on
    public $yourlsFormat;  // json, xml, simple 
    
    public $googleDomain; // Google API's URL
    public $googleApiKey; // obtain from https://code.google.com/apis/console/
    
    
    
    /**
     * ShortenComponent::shorten()
     * 
     * @param mixed $longurl
     * @param mixed $service
     * @param mixed $options
     * @return mixed $shorturl
     * 
     * @author Michael Burton (MadMikeyB)
     * @copyright 2011
     * @link http://onemorefunction.com/blog/posts/cakephp-shortencomponent-a-component-for-url-shortening/9/
     * @example http://onemorefunction.com/blog/examples/shorten
     * @license BSD - https://github.com/MadMikeyB/CakePHP-Scrapbook/blob/master/LICENSE
     */
     
    function shorten($longurl, $service, $options = array())
    {
        
        if (!empty($longurl)) {

            if ($service == 'kwnme') {
                // define all settings
                $this->kwnApiKey = 'REPLACEME'; // get from kwn.me/api?act=key
                $this->kwnDomain = 'kwn.me'; // can be kwn.me or quicklink.me
                // get stuff done
                $kwnmeshortenedURL = file_get_contents("http://{$this->kwnDomain}/api.php?act=shorten&key={$this->kwnApiKey}&opt=text&url=" . urlencode($longurl) . "&type=plain&recycle=true");
                if (!empty($kwnmeshortenedURL)) {
                    return $kwnmeshortenedURL;
                } else {
                    return 'error';
                }
            }

            if ($service == 'bitly') {
                // define all settings
                $this->bitlyLogin = 'REPLACEME'; // https://bitly.com/a/your_api_key
                $this->bitlyApiKey = 'REPLACEME'; // https://bitly.com/a/your_api_key
                $this->bitlyFormat = 'txt'; // txt, json or xml
                // get stuff done
                $bitlyshortenedURL = file_get_contents("http://api.bitly.com/v3/shorten?login={$this->bitlyLogin}&apiKey={$this->bitlyApiKey}&longUrl={$longurl}&format={$this->bitlyFormat}");
                if (!empty($bitlyshortenedURL)) {
                    return $bitlyshortenedURL;
                } else {
                    return 'error';
                }
            }
            
            if ($service == 'yourls') {
                // define all settings
                if (isset($options['domain'])) {
                    $this->yourlsDomain = $options['domain'];
                } else {
                    $this->yourlsDomain = 'topic.to';
                }
                $this->yourlsFormat = 'simple'; // json, xml or simple
                // get stuff done
                $yourlsshortenedURL = file_get_contents("http://{$this->yourlsDomain}/api.php?action=shorturl&url={$longurl}&format={$this->yourlsFormat}");
                if (!empty($yourlsshortenedURL)) {
                    return $yourlsshortenedURL;
                } else {
                    return 'error';
                }
            }
            
            if ($service == 'google') {
                // define all settings
                $this->googleDomain = 'https://www.googleapis.com/urlshortener/v1/url?key=';
                $this->googleApiKey = 'REPLACEME'; // get here: https://code.google.com/apis/console/
                /**
                 * @link https://github.com/fabricioferracioli/CakePHP-Google-URL-Shortener-Component/blob/master/google_url_shortener.php
                 **/
                App::import('Core', 'HttpSocket');
                $socket = new HttpSocket();

                $result = $socket->post(
                    $this->googleDomain . $this->googleApiKey,
                    json_encode(array('longUrl' => $longurl)),
                    array('header' => array('Content-Type' => 'application/json'))
                );
                $googleUrl = json_decode($result, true);
                return $googleUrl['id'];
                
            }
        }
    }

}

?>
