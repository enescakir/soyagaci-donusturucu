<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Soyağacı - @yield('title')</title>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- SEO Tags -->
    @section('seo-tag')
    <meta name="author" content="Soyağacı.co" />
    <meta name="keywords" content="soyağacı, soyağacı oluşturucu, edevlet soyağacı, soyağacını öğren" />
    <meta name="description" content="Soyağacı, e-devlet kapısı üzerinden oluşturduğunuz dosyayı gerçek bir soyağacına dönüştürür."
    />
    <meta property="og:type" content="Website" />
    <meta property="og:site_name" content="Soyağacı" />
    <meta property="og:url" content="http://www.soyagaci.co" />
    <meta property="og:title" content="Soyağacı Dönüştürücü" />
    <meta property="og:description" content="Soyağacı, e-devlet kapısı üzerinden oluşturduğunuz dosyayı gerçek bir soyağacına dönüştürür." />
    <meta property="og:image" content="http://www.soyagaci.co/img/social.jpg" />
    <meta property="twitter:card" content="summary_large_image" />
    @show

    <!-- Bootstrap core CSS -->
    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Custom fonts for this template -->
    <link href="{{ asset('vendor/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('vendor/simple-line-icons/css/simple-line-icons.css') }}" rel="stylesheet" type="text/css">
    <!-- <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700,300italic,400italic,700italic&subset=latin-ext" rel="stylesheet" type="text/css"> -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700&amp;subset=latin-ext" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    @yield('style')
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-light bg-light static-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">Soyağacı.co</a>
            @section('toolbar')
            <a class="btn btn-primary d-none d-sm-block" href="{{ route('pdf.show') }}">Eski PDF Yükle</a>
            @show
        </div>
    </nav>
    @yield('content')

    <!-- Footer -->
    @section('footer')
    <footer class="footer bg-light">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 h-100 text-center text-lg-left my-auto">
                    <ul class="list-inline mb-2">
                        <li class="list-inline-item">
                            <a href="mailto:bilgi@folx.com.tr">İletişim</a>
                        </li>
                        <li class="list-inline-item">&sdot;</li>
                        <li class="list-inline-item">
                            <a href="{{ route('usage') }}">Kullanım Koşulları</a>
                        </li>
                    </ul>
                    <p class="text-muted small mb-4 mb-lg-0">&copy;
                        <a href="https://www.linkedin.com/in/menescakir/" target="_blank">Enes Çakır</a> tarafından İstanbul'da geliştirildi. Bütün hakları saklıdır.</p>
                </div>
                <div class="col-lg-6 h-100 text-center text-lg-right my-auto">
                    <ul class="list-inline mb-0">
                        <li class="list-inline-item mr-3">
                            <a href="https://www.facebook.com/folxdigital" target="_blank">
                                <i class="fa fa-facebook fa-2x fa-fw"></i>
                            </a>
                        </li>
                        <li class="list-inline-item mr-3">
                            <a href="https://twitter.com/folxdigital" target="_blank">
                                <i class="fa fa-twitter fa-2x fa-fw"></i>
                            </a>
                        </li>
                        <li class="list-inline-item">
                            <a href="https://www.linkedin.com/company/folx-digital" target="_blank">
                                <i class="fa fa-linkedin fa-2x fa-fw"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>
    @show

    <!-- Bootstrap core JavaScript -->
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    
<script>
    'use strict';

    ;
    (function (document, window, index) {
        var inputs = document.querySelectorAll('.inputfile');
        Array.prototype.forEach.call(inputs, function (input) {
            var label = input.nextElementSibling,
                labelVal = label.innerHTML;

            input.addEventListener('change', function (e) {
                var fileName = '';
                if (this.files && this.files.length > 1)
                    fileName = (this.getAttribute('data-multiple-caption') || '').replace('{count}',
                        this.files.length);
                else
                    fileName = e.target.value.split('\\').pop();

                if (fileName)
                    label.querySelector('span').innerHTML = fileName;
                else
                    label.innerHTML = labelVal;
            });

            // Firefox bug fix
            input.addEventListener('focus', function () {
                input.classList.add('has-focus');
            });
            input.addEventListener('blur', function () {
                input.classList.remove('has-focus');
            });
        });
    }(document, window, 0));
</script>

    @yield('script')
</body>

</html>