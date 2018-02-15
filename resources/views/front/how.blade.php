@extends('front.app') 

@section('title', 'Nasıl Sayfa Kaydedilir?')

@section('content')
    <section class="testimonials bg-light">
      <div class="container">
      <h1>Soyağacı oluşturmak için sayfa nasıl kaydedilir?</h1>
       <h5> 1. E-devlet kapısında ilgili sayfayı açın</h5>
       <h5> 2. İnternet tarayıcınızın menüsünden  "Dosya > Sayfayı Kaydet (CTRL + S)" işlemini kullanarak dosyanızı oluşturun </h5>
       <h5> 3. <a href="{{ route('home') }}">Anasayfamızdan</a> oluşturduğunuz dosyayı seçerek soyağacınızı oluşturun.</h5>
       <p class="text-center">
          <img src="{{ asset('img/how.gif') }}" alt="Sayfa Nasıl Kaydedilir?" class="img-responsive">
       </p>
      </div>
    </section>
@endsection