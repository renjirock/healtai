<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
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
    </head>

    <body id="page-top">
        <!-- Navigation-->
        @include('layouts.nav')
        <!-- Post header-->
        <header class="py-5 border-bottom mb-4" style="background: rgb(0,0,0); background: linear-gradient(144deg, rgba(0,0,0,1) 52%, rgba(100,161,157,1) 100%);">
            <div class="container">
                <div class="text-center my-5">
                    <!-- Post title-->
                    <h1 class="fw-bolder mb-1" style="color: white;">{{$blog->title}}</h1>
                    <!-- Post meta content-->
                    <div class="text-muted fst-italic mb-2">Posted on {{$blog->created_at}} by {{$blog->author}}</div>
                    <!-- Post categories-->
                    <a class="badge bg-secondary text-decoration-none link-light" href="#!">Web Design</a>
                    <a class="badge bg-secondary text-decoration-none link-light" href="#!">Freebies</a>
                </div>
            </div>
        </header>
        <!-- Page content-->
        <div class="container mt-5">
            <div class="row">
                <div class="col-lg-8">
                    <!-- Post content-->
                    <article>
                        <!-- Preview image figure-->
                        <figure class="mb-4"><img class="img-fluid rounded" src="{{ asset('/storage/images/' . $blog->img)}}" alt="{{$blog->title}}" /></figure>
                        <!-- Post content-->
                        <section class="mb-5">
                            <p class="fw-bolder mb-4 mt-5">Ingredients</p>
                            <ul class="list-group list-group-flush">
                                @foreach ($ingredientsList as $insgredients)
                                    <li class="list-group-item">{{$insgredients->original}}</li>
                                @endforeach
                              </ul>
                            <p class="fw-bolder mb-4 mt-5">{!! $blog->content !!}</p>
                            <script>
                                const p = document.getElementsByTagName("p");
                                p[1].classList.add("d-none");
                                for (let index = 1; index < p.length; index++) {
                                    p[index].classList.add("fs-5");
                                    p[index].classList.add("mb-4");
                                }
                            </script>
                        </section>
                    </article>
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
                    <!-- Categories widget-->
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
                    </div>
                    <!-- Side widget-->
                    <div class="card mb-4">
                        <div class="card-header">Side Widget</div>
                        <div class="card-body">You can put anything you want inside of these side widgets. They are easy to use, and feature the Bootstrap 5 card component!</div>
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
