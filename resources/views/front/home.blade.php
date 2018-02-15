@extends('front.app') 

@section('title', 'E-devlet Dosyasını Soyağacına Dönüştür')

@section('style')
@endsection

@section('content')
<!-- Masthead -->
<header class="masthead text-white text-center">
    <div class="overlay"></div>
    <div class="container">
        <div class="row">
            <div class="col-xl-10 mx-auto">
                <h1 class="mb-5">E-devlet'ten aldığınız dosyayı soyağacına
                    <span style="text-decoration:underline">ücretsiz</span> dönüştürün.</h1>
            </div>
            <div class="col-md-10 col-lg-8 col-xl-7 mx-auto">
                <form method="POST" action="{{ route('tree.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="form-row">
                        <div class="col-12 col-md-12" style="padding-bottom:1em">
                            <input id="file" type="file" 
                                class="inputfile inputfile-1" 
                                name="file" 
                                value="{{ old('file') }}"
                                required> 
                                <label for="file"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="17" viewBox="0 0 20 17"><path d="M10 0l-5.2 4.9h3.3v5.1h3.8v-5.1h3.3l-5.2-4.9zm9.3 11.5l-3.2-2.1h-2l3.4 2.6h-3.5c-.1 0-.2.1-.2.1l-.8 2.3h-6l-.8-2.2c-.1-.1-.1-.2-.2-.2h-3.6l3.4-2.6h-2l-3.2 2.1c-.4.3-.7 1-.6 1.5l.6 3.1c.1.5.7.9 1.2.9h16.3c.6 0 1.1-.4 1.3-.9l.6-3.1c.1-.5-.2-1.2-.7-1.5z"/></svg> <span>Dosya Seç&hellip;</span></label>
                            @if ($errors->has('file'))
                            <span class="invalid-feedback">
                                <strong>{{ $errors->first('file') }}</strong>
                            </span>
                            @endif

                        </div>
                        <div class="col-12 col-md-9 mb-2 mb-md-0">
                            <input id="email" type="email" class="form-control form-control-lg{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email"
                                value="{{ old('email') }}" placeholder="E-postanızı girin..." required autofocus>
                                @if ($errors->has('email'))
                            <span class="invalid-feedback">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                            @endif

                        </div>

                        <div class="col-12 col-md-3">
                            <button type="submit" class="btn btn-block btn-lg btn-primary">Dönüştür!</button>
                        </div>
                        <div class="col-xl-9 mx-auto" style="padding-top:4em">
                        E-devlet kapısında ilgili sayfadayken menüden "Dosya > Sayfayı Kaydet (CTRL + S)" işlemini kullanarak oluşturduğunuz dosyayı seçerek soyağacınızı oluşturabilirsiniz.<br>
                        Sayfayı nasıl kaydedeceğinizi <a href="{{ route('how') }}">buradan</a> öğrenebilirsiniz.<br>
                        Dönüştüre basarak <a href="{{ route('usage') }}">Kullanım Koşulları</a>nı kabul ediyorsunuz.</p>
                        </div>
                </form>
                </div>
            </div>
        </div>
</header>


<!-- Testimonials -->
<section class="testimonials text-center bg-light">
    <div class="container">
        <h2 class="mb-5">Ekip</h2>
        <div class="row">
            <div class="col-lg-6">
                <div class="testimonial-item mx-auto mb-5 mb-lg-0">
                    <a href="https://www.linkedin.com/in/menescakir/" target="_blank">
                        <img class="img-fluid rounded-circle mb-3" src="{{ asset('img/enes-cakir.jpg') }}" alt="Enes Çakır">
                    </a>
                    <h5>Enes Çakır</h5>
                    <p class="font-weight-light mb-0">CTO / Biryudumkitap, FOLX</p>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="testimonial-item mx-auto mb-5 mb-lg-0">
                    <a href="https://www.linkedin.com/in/alparslandemir/" target="_blank">
                        <img class="img-fluid rounded-circle mb-3" src="{{ asset('img/alparslan-demir.jpg') }}" alt="Alparslan Demir">
                    </a>
                    <h5>Alparslan Demir</h5>
                    <p class="font-weight-light mb-0">Chief Entrepreneur / Biryudumkitap, FOLX</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('script')
@endsection