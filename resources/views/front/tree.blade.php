@extends('front.app')

@section('title', $tree["name"])

@section('seo-tag')
<meta name="author" content="Soyağacı.co" />
<meta name="keywords" content="soyağacı, soyağacı oluşturucu, edevlet soyağacı, soyağacını öğren" />
<meta name="description" content="Soyağacı, e-devlet kapısı üzerinden oluşturduğunuz dosyayı gerçek bir soyağacına dönüştürür."
/>
<meta property="og:type" content="Website" />
<meta property="og:site_name" content="Soyağacı" />
<meta property="og:url" content="{{ $tree["path"] }}" />
<meta property="og:title" content="{{ $tree["name"] }}" />
<meta property="og:description" content="Soyağacı, e-devlet kapısı üzerinden oluşturduğunuz dosyayı gerçek bir soyağacına dönüştürür." />
<meta property="og:image" content="http://www.soyagaci.co/img/social.jpg" />
<meta property="twitter:card" content="summary_large_image" />
@show

@section('style')
    <link rel="stylesheet" href="{{ asset('css/tree.min.css') }}">
@endsection

@section('toolbar')
    <div class="row">
        <div class="input-group my-2">
            <input id="tree-path" class="form-control" type="text" placeholder="Link" value="{{ $tree["path"] }}" readonly data-toggle="tooltip"
                data-placement="bottom" title="Kopyalandı">
            <div class="input-group-append">
                <button class="btn btn-success" type="button" data-clipboard-target="#tree-path">Kopyala</button>
                <button type="button" class="d-inline-block d-sm-none btn btn-danger dropdown-toggle dropdown-toggle-split" data-toggle="dropdown">
                    <span class="sr-only">Toggle Dropdown</span>
                </button>
                <div class="dropdown-menu">
                    <button class="delete btn btn-danger" tree-slug={{ $tree["slug"] }}>Hemen Sil</button>
                    <div role="separator" class="dropdown-divider"></div>
                    <a class="dropdown-item" href="{{ route('home') }}">Sen de Oluştur</a>
                </div>
            </div>
        </div>
    </div>
    <div class="btn-group d-none d-sm-inline-flex" role="group">
        <button class="download btn btn-success"><i class="fa fa-download"></i> İndir</button>
        <button class="delete btn btn-danger" tree-slug={{ $tree["slug"] }}><i class="fa fa-trash"></i> Hemen Sil</button>
        <a class="btn btn-primary" href="{{ route('home') }}"> <i class="fa fa-refresh"></i> Sen de Oluştur</a>
    </div>
@endsection

@section('content')
    @if($tree["status"] == \App\Enums\TreeStatus::SUCCESS)
        <div id="tree"></div>
        <div class="modal fade" id="person-modal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><span class="fname"></span> <span class="lname"></span></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-0">
                        <style>
                            table th {
                                text-align: right;
                            }
                        </style>
                        <table class="table table-striped table-sm">
                            <tbody>
                                <tr><th class="w-50">Adı:</th><td class="fname w-50">-</td></tr>
                                <tr><th>Soyadı:</th><td class="lname">-</td></tr>
                                <tr><th>Doğum Yeri:</th><td class="bplace">-</td></tr>
                                <tr><th>Doğum Tarihi:</th><td class="bday">-</td></tr>
                                <tr><th>Ölüm Tarihi:</th><td class="dday">-</td></tr>
                                <tr><th>Yakınlık:</th><td class="rel">-</td></tr>
                                <tr><th>Baba Adı:</th><td class="fatname">-</td></tr>
                                <tr><th>Anne Adı:</th><td class="motname">-</td></tr>
                                <tr><th>İl:</th><td class="city">-</td></tr>
                                <tr><th>İlçe:</th><td class="district">-</td></tr>
                                <tr><th>Köy:</th><td class="hometown">-</td></tr>
                                <tr><th>Medeni Hali:</th><td class="mstatus">-</td></tr>
                                <tr><th>Cinsiyeti:</th><td class="gender">-</td></tr>
                                <tr><th>Durumu:</th><td class="status">-</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer p-0">
                        <button type="button" class="btn btn-danger btn-block" data-dismiss="modal">Kapat</button>
                    </div>
                </div>
            </div>
        </div>
    @elseif($tree["status"] == \App\Enums\TreeStatus::PARSING)
        <header class="text-white text-center">
            <div class="overlay"></div>
            <div class="container">
                <div class="row">
                    <div class="col-xl-9 mx-auto" style="color:black;padding-top:8rem;padding-bottom:8rem;">
                        <img src="{{ asset('img/gear.gif') }}">
                        <h1 class="mb-5">Soyağacınız Dönüştürülüyor</h1>
                    </div>
                </div>
            </div>
        </header>
    @else
        <header class="text-white text-center">
            <div class="overlay"></div>
            <div class="container">
                <div class="row">
                    <div class="col-xl-9 mx-auto" style="color:black;padding-top:8rem;padding-bottom:8rem;">
                        <img src="{{ asset('img/error.png') }}" style="margin-bottom:20px; width:200px; height:200px;">
                        <h1 class="mb-5">Yüklediğiniz PDF'te hatalar bulunuyor</h1>
                    </div>
                </div>
            </div>
        </header>
    @endif
@endsection

@section('footer')
@endsection

@section('script')
    @if($tree["status"] == \App\Enums\TreeStatus::SUCCESS)
        <script src="//d3js.org/d3.v4.min.js"></script>
        <script>
            var data = {!! json_encode($tree["people"]) !!}
        </script>
        <script src="{{ asset('js/tree.min.js') }}"></script>
        <script src="{{ asset('js/saveSvgAsPng.js') }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/1.7.1/clipboard.min.js"></script>
        <script>
            $(function() {
                var clipboard = new Clipboard('.btn');
                clipboard.on('success', function (e) {
                    $('#tree-path').tooltip('show')
                    setTimeout(function () {
                        $('#tree-path').tooltip('hide');
                        e.clearSelection();
                    }, 1000);
                });
                d3.select(window).on("load", zoomFit(0.85)); 
            });
            $(".download").click(function (e) {
                zoomFit(0.85)
                saveSvgAsPng($("#tree > svg").get(0), "soy-agacim.png", {
                    scale: 3
                });
            })
        </script>
    @else
        <script>
            setTimeout(function(){
                window.location.reload(1);
            }, 3000);
        </script>
    @endif
    <script>
        $.ajaxSetup({ headers: {'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content') }});
        $(".delete").click(function () {
            var slug = $(this).attr('tree-slug')
            if (confirm("Soyağacı silmek istediğinize emin misiniz?")) {
                $.ajax({
                    url: "/agac/" + slug,
                    method: "DELETE",
                    dataType: "json",
                    success: function (result) {
                        window.location.href = "http://www.soyagaci.co";
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        alert("Silenemedi");
                    }
                });
                return;
            }
        })

    </script>
@endsection