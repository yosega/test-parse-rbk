<?php

namespace App\Http\Controllers;

use App\Models\NewsModel as ModelsNewsModel;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Http\Request;

class ParseNewsController extends Controller
{
    // хранит модель новостей
    private $news = null;

    function __construct() {
        $this->news = new ModelsNewsModel();
    }

    /**
     * Запустить парсинг
     *
     * @return void
     */
    public function parseNews() {

        $linkList = $this->getLastNewsList('https://rostov.rbc.ru/');
        
        sleep(1);

        foreach ($linkList as $key =>  $link) {

            $parseData = $this->getAndParseNewsByLinks($link);

            if (!empty($parseData['header'])) {
                $this->addNewsToDB($parseData);
            }
            
            sleep(1);
        }

        return response()->json(['result' => 'OK'], 200);
    }

    /**
     * Получить список последних новостей с сайта
     *
     * @param mixed $url ссылка на сайт
     *
     * @return array
     */
    private function getLastNewsList($url):array {

        $content = $this->getHtml($url)['content'] ?? '';

        $crawler = new Crawler($content);

        $linkList = $crawler->filter('.js-news-feed-list > a')->each(function (Crawler $node, $i) {
            return $node->link()->getUri();
        });

        return $linkList;
    }
    
    /**
     * Добавить новость в БД
     *
     * @param array $itemNews ItemNews
     *
     * @return void
     */
    private function addNewsToDB(array $itemNews) {

        $newsModel = $this->news;

        $newsModel = $newsModel::firstOrNew(['origin_url' => $itemNews['link']]);

        $newsModel->title      = $itemNews['header'];
        $newsModel->body       = $itemNews['body'];
        $newsModel->img_url    = $itemNews['imgUrl'];
        $newsModel->origin_url = $itemNews['link'];

        $newsModel->save();
    }

    /**
     * Получить и спарсить одну новость по ссылке
     *
     * @param string $url ссылка на новость
     *
     * @return array
     */
    private function getAndParseNewsByLinks(string $url):array {

        $data = [
            'link'    => $url,
            'header'  => '',
            'body'    => '',
            'imgUrl' => ''
        ];

        if (empty($url)) {
            return $data;
        }

        $content = $this->getHtml($url)['content'] ?? '';

        $crawler = new Crawler($content);

        $cssPatchList = [
            'header'  => ['.article__header__title-in', '.article__title', '.article__subtitle'],
            'body'    => ['.article__text', '.article__body']
        ];

        
        $cssPatchImgUrl = [
            '.article__main-image__image', 
            '.article__image article__image--main > img'
        ];

        foreach($cssPatchList as $key => $itemsPath) {
            foreach($itemsPath as $path) {
                if ($crawler->filter($path)->text('pathNotFind', false) !== 'pathNotFind') {
                    $data[$key] = $crawler->filter($path)->text('pathNotFind', false);
                    break;
                }
            }
        }

        foreach($cssPatchImgUrl as $path) {
            if (empty($crawler->filter($path)->eq(0)->text('pathNotFind', false))) {
                $data['imgUrl'] = $crawler->filter($path)->eq(0)->image()->getUri();
                break;
            }
        }

        return $data;  
    }
    
    /**
     * Получить HTML по ссылке через curl
     *
     * @param string $url ссылка, по котором получаем HTML
     *
     * @return void
     */
    private function getHtml(string $url) {

        $options = array(
            CURLOPT_RETURNTRANSFER => true,     // return web page
            CURLOPT_HEADER         => false,    // don't return headers
            CURLOPT_FOLLOWLOCATION => true,     // follow redirects
            CURLOPT_ENCODING       => "",       // handle all encodings
            CURLOPT_USERAGENT      => "google chrome", // who am i
            CURLOPT_AUTOREFERER    => true,     // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
            CURLOPT_TIMEOUT        => 120,      // timeout on response
            CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
            CURLOPT_SSL_VERIFYPEER => false     // Disabled SSL Cert checks
        );
    
        $ch      = curl_init( $url );
        curl_setopt_array( $ch, $options );
        $content = curl_exec( $ch );
        $err     = curl_errno( $ch );
        $errmsg  = curl_error( $ch );
        $header  = curl_getinfo( $ch );
        curl_close( $ch );
    
        $header['errno']   = $err;
        $header['errmsg']  = $errmsg;
        $header['content'] = $content;
        return $header;
    }
}
