@extends('layout\layout')

@section('title'){{ $newsData['title'] }}@endsection

@section('content')
<section class="py-5 text-center container">
  <div class="row py-lg-5">
    <div class="col-lg-6 col-md-8 mx-auto">
      <h1 class="fw-light">{{ $newsData['title'] }}</h1>
      @if (!empty($newsData['img_url']))
        <svg class="bd-placeholder-img card-img-top" width="100%" height="225" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Placeholder: Thumbnail" preserveAspectRatio="xMidYMid slice" focusable="false" style="
            background: url({{ $newsData['img_url'] }});
            background-size: cover;
        "></svg>
      @endif
      <p class="lead text-muted">{{ $newsData['body'] }}</p>
      <p>
        <a href="/" class="btn btn-primary my-2">Назад к списку новостей</a>
      </p>
    </div>
  </div>
</section>
@endsection