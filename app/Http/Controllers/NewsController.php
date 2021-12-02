<?php

namespace App\Http\Controllers;

use App\Models\NewsModel as ModelsNewsModel;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    // хранит модель новостей
    private $news = null;

    function __construct() {
        $this->news = new ModelsNewsModel();
    }

     /**
     * Загрузка главной страницы
     *
     * @return void
     */
    public function home() {
        $newsList = $this->getNewsFromLocalAll();
        return view('news/home', ['newsList' => $newsList]);
    }

    /**
     * Для роута Получить одну новость по $request['id']
     *
     * @param Request $request Request
     *
     * @return void
     */
    public function getNewsById(Request $request) {
        $newsData = $this->getNewsFromLocalById($request['id']);
        if (!empty($newsData)) {
            $newsData = $newsData[0];
        }
        return view('news/newsItem', ['newsData' => $newsData]);
    }

    /**
     * Получить одну новость по идентификатору
     *
     * @param int $id идентификатор новости
     *
     * @return void
     */
    private function getNewsFromLocalById(int $id) {

        $newsData = $this->news->where('id', $id)->get();
        return $newsData;
    }

    /**
     * Получить ВСЕ новости локально из БД
     *
     * @return void
     */
    private function getNewsFromLocalAll() {

        $newsData = $this->news->orderBy('id', 'desc')->take(15)->get();
        return $newsData;
    }
}