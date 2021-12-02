@extends('layout\layout')

@section('title')Главная@endsection

@section('content')
<section class="py-5 text-center container">
  <div class="row py-lg-5">
    <div class="col-lg-6 col-md-8 mx-auto">
      <p class="lead text-muted">Новости с сайта rbk.ru</p>
      <p class="lead text-muted">Чтобы спарсить последние новости нажмите "Обновить"</p>
      <p>
        <button class="btn btn-primary my-2" id="parse-news">Обновить</button>
      </p>
    </div>
  </div>
</section>

<div class="album py-5 bg-light">
  <div class="container">

    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
      @foreach ($newsList as $item)
      <div class="col">
          <div class="card shadow-sm">
            <svg class="bd-placeholder-img card-img-top" width="100%" height="225" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Placeholder: Thumbnail" preserveAspectRatio="xMidYMid slice" focusable="false" style="
                background: url({{ !empty($item['img_url']) ? $item['img_url'] : '/assets/img/no_img.jpg' }});
                background-size: cover;
            "></svg>
            <div class="card-body">
              <p class="card-text">{{ mb_strlen(trim($item['body'])) > 200 
                  ? mb_substr(trim($item['body']), 0, 200).'...' 
                  : trim($item['body']) }}
              </p>
              <div class="d-flex justify-content-between align-items-center">
                <div class="btn-group">
                  <a href="/news/{{ $item['id'] }}" type="button" class="btn btn-sm btn-outline-secondary">Подробнее</a>
                </div>
              </div>
            </div>
          </div>
        </div>
        @endforeach  
    </div>
  </div>
</div>
<script>
const getParseNews = (requestURL, element) => {
  element.srcElement.innerText = 'Ожидайте... (~30 сек)';
  element.srcElement.disabled = true;
  const xhr = new XMLHttpRequest();
  xhr.open('GET', requestURL);
  xhr.onreadystatechange = function() {
    element.srcElement.innerText = 'Обновить';
    element.srcElement.disabled = false;
  }
  xhr.send();
}
window.document.getElementById('parse-news').onclick = (element) => {
  element.preventDefault(0);
  getParseNews('/api/run-parse-news', element);
}
</script>
@endsection