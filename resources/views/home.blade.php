<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="This medical and health blog provides up-to-date information on the latest medical news, treatments, and health tips for the US audience. Get the latest medical advice and stay informed on the latest health trends." />
        <meta name="author" content="medicalAi" />
        <meta name="keywords" content="Medical, Health, Healthcare, Wellness, Disease, Treatment, Prevention, Nutrition, Exercise, Mental Health, US, American, Doctor, Physician, Hospital, Clinic, Medical Advice, Medical News" />
        <title>HealtAi - healthy information</title>
        <link rel="icon" type="image/x-icon" href="{{ asset('/assets/favicon.ico') }}" />
        <!-- Font Awesome icons (free version)-->
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
        <!-- Google fonts-->
        <link href="https://fonts.googleapis.com/css?family=Varela+Round" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet" />
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=VT323&display=swap" rel="stylesheet">
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="{{ asset('/css/styles.css') }}" rel="stylesheet" />
        <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-8894681818507521"
     crossorigin="anonymous"></script>
    </head>

    <body id="page-top">
        <!-- Navigation-->
        @include('layouts.nav')
        <!-- Masthead-->
        <header class="masthead mb-4">
            <div class="container px-4 px-lg-5 d-flex h-100 align-items-center justify-content-center">
                <div class="d-flex justify-content-center">
                    <div class="text-center">
                        <img src="{{ asset('/assets/logo.png') }}" alt="Girl in a jacket"/>
                        <h1 class="mx-auto my-0 text-uppercase">Healt<strong style="font-family: 'VT323', monospace;">AI</strong></h1>
                        <h2 class="text-white-50 mx-auto mt-2 mb-5">healt information from a AI.</h2>
                        <a class="btn btn-primary" href="#content">Get Started</a>
                    </div>
                </div>
            </div>
        </header>
        <!-- Page content-->
        <div class="container"id='content'>
            <div class="row" >
                <!-- Blog entries-->
                <div class="col-lg-8">
                    <div class="card mb-4">
                        <a href="{{ url('/' . $blogs[0]->url) }}"><img class="card-img-top" src="{{ asset('/storage/images/' . $blogs[0]->img)}}" style="height: 350px;width: 100%" alt="{{$blogs[0]->title}}" /></a>
                        <div class="card-body">
                            <div class="small text-muted">{{$blogs[0]->created_at}}</div>
                            <h2 class="card-title">{{$blogs[0]->title}}</h2>
                            <p class="card-text">{{str_replace("<p>", " ", substr($blogs[0]->content, 0, 200));}}...</p>
                            <a class="btn btn-primary" href="{{ url('/' . $blogs[0]->url) }}">Read more →</a>
                        </div>
                    </div>
                    <!-- Featured blog post-->
                    <div class="row">
                            @foreach ($blogs as $index => $blog)
                                @if ($index != 0)
                                    @if ($index % 2 != 0)
                                        <div class="col-lg-6">
                                    @endif
                                    <!-- Blog post-->
                                    <div class="card mb-4">
                                        <a href="{{ url('/' . $blog->url) }}"><img class="card-img-top" src="{{ asset('/storage/images/' . $blog->img)}}" alt="{{$blog->title}}" /></a>
                                        <div class="card-body">
                                            <div class="small text-muted">{{$blog->created_at}}</div>
                                            <h2 class="card-title h4">{{$blog->title}}</h2>
                                            <p class="card-text">{{str_replace("<p>", " ", substr($blog->content, 0, 200));}}...</p>
                                            <a class="btn btn-primary" href="{{ url('/' . $blog->url) }}">Read more →</a>
                                        </div>
                                    </div>
                                    @if ($index % 2 == 0)
                                    </div>
                                    @endif
                                @endif
                            @endforeach
                    </div>
                    <!-- Pagination-->
                    <nav aria-label="Pagination">
                        <hr class="my-0" />
                        <ul class="pagination justify-content-center my-4">
                            <li class="page-item disabled"><a class="page-link" href="#" tabindex="-1" aria-disabled="true">Newer</a></li>
                            <li class="page-item active" aria-current="page"><a class="page-link" href="#!">1</a></li>
                            <li class="page-item"><a class="page-link" href="#!">2</a></li>
                            <li class="page-item"><a class="page-link" href="#!">3</a></li>
                            <li class="page-item disabled"><a class="page-link" href="#!">...</a></li>
                            <li class="page-item"><a class="page-link" href="#!">15</a></li>
                            <li class="page-item"><a class="page-link" href="#!">Older</a></li>
                        </ul>
                    </nav>
                </div>
                <!-- Side widgets-->
                <div class="col-lg-4">
                    <!-- Search widget-->
                    <div class="card mb-4">
                        <div class="card-header">Search</div>
                        <div class="card-body">
                            <div class="input-group">
                                <input class="form-control" type="text" placeholder="Enter search term..." aria-label="Enter search term..." aria-describedby="button-search" />
                                <button class="btn btn-primary" id="button-search" type="button">Go!</button>
                            </div>
                        </div>
                    </div>
                    {{-- <!-- Categories widget-->
                    <div class="card mb-4">
                        <div class="card-header">Categories</div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-6">
                                    <ul class="list-unstyled mb-0">
                                        <li><a href="#!">Web Design</a></li>
                                        <li><a href="#!">HTML</a></li>
                                        <li><a href="#!">Freebies</a></li>
                                    </ul>
                                </div>
                                <div class="col-sm-6">
                                    <ul class="list-unstyled mb-0">
                                        <li><a href="#!">JavaScript</a></li>
                                        <li><a href="#!">CSS</a></li>
                                        <li><a href="#!">Tutorials</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div> --}}
                    <!-- Side widget-->
                    <div class="card mb-4">

                    </div>
                </div>
            </div>
        </div>
        <!-- Footer-->
        @include('layouts.footer')
        <!-- Bootstrap core JS-->
        <script src="{{ asset('/js/bootstrap.bundle.min.js') }}"></script>
        <!-- Core theme JS-->
        <script src="{{ asset('/js/scripts.js') }}"></script>
    </body>
</html>
