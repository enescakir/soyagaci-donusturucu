## Soyağacı Dönüştürücü
E-devlet kapısı üzerinden oluşturulan "*Alt-Üst Soy Bilgisi Sorgulama*" listesini interaktif bir soyağacına çevirmek üzerine geliştirilmiş projedir.
Projeyi bilgisayarınıza indirerek kendi görsellerinizi oluşturabilirsiniz.

Projenin altyapısında [Laravel 5.6](https://laravel.com), soyağacının görüntülenmesinde [D3.js](https://d3js.org) kullanılmıştır.

### Yükleme
1. PHP 7.1 ve `composer` gerekmektedir. macOS'ta [brew](https://brew.sh/index_tr.html) ile yükleyebilirsiniz
2. Eğer PDF'ten dönüştürecekseniz `pdftohtml` ve `ghostscript` uygulamaları gerekmektedir.
    * macOS 
        - `brew install pdftohtml` 
        - `brew install ghostscript` 
    * Ubuntu
        - `sudo apt-get install poppler-utils` 
        - `sudo apt-get install ghostscript` 
2. Projeyi bilgisayarınıza indirin `git clone https://github.com/EnesCakir/soyagaci-donusturucu.git`
3. Proje klasörünün içine girin `cd soyagaci-donusturucu`
4. PHP kütüphanelerini yükleyin `composer install`
5. `.env` dosyanızı oluşturun `cp .env.example .env`
6. Eğer PDF dönüştürecekseniz `.env` dosyanızda `pdftohtml` konumunu ayarlayın
7. Uygulama için gizli anahtar oluşturun `php artisan key:generate`
8. Uygulamanızı ayağa kaldırın `php artisan serve`
9. Tarayıcınıza `localhost:8000` yazarak uygulamaya erişebilirsiniz

### Soy Ağacı Oluşturma Aşamaları
"*Alt-Üst Soy Bilgisi Sorgulama*" sistemi ilk açıldığında sonuçları PDF olarak oluşturuyordu. Bu da işlenmesini zor hale getiriyordu. 14 Şubat 2018'teki güncelleme ile sonuçları HTML olarak vermeye başladılar. Bu da bizim işimizi epey kolaylaştırdı.
#### 1. Verilerin Okunması
##### PDF
Öncelikle `pdftohtml -noframes -i -c` ile PDF'i HTML dosyasına dönüştürüyoruz. `-noframes` argümanıyla PDF'in tek sayfa olmasını `-c` argümanıyla `ghostscript`'i kullanarak yazıların daha doğru poziyonlarda yer almasını sağlıyoruz. 

Oluşan HTML dosyasındaki gereksiz HTML `tag`lerini PHP'nin `strip_tags` fonksiyonu ile temizliyoruz. `regex` kullarak da `AÇIKLAMALAR`, `İÇİŞLERİ BAKANLIĞI`, `NÜFUS VE VATANDAŞLIK İŞLERİ GENEL MÜDÜRLÜĞÜ` gibi verimizi kirleten başlıkları siliyoruz.

Elimizde sırasıyla aile bireylerinin `Sıra`, `Cinsiyet (E|K)`, `Yakınlık Derecesi`, `AD`, `SOYAD`, `BABA ADI`, `ANA ADI`, `DOĞUM YERİ`, `İl`, `İlçe`, `MAHALLE`, `Birey Sıra`, `Medeni Hali`, `Durumu`, `Doğum Tarihi`, `Ölüm Tarihi` bilgilerinin satır satır bulunduğu bir veri kalıyor. Burada tamamen rakamlardan oluşan kısım sadece `Sıra` olduğu için bu listeyi sadece rakam bulunan satırlardan bölüyoruz ve her bir parçayı bir kişi olarak belirliyoruz. Sonra sırasıyla bu parçalardaki bilgileri kullanarak kişileri oluşturuyoruz. Buradaki zorluk ise bazı alanlar bir satırdan fazla olabiliyor ya da `-` kullanılarak hiç belirtilmemiş oluyor. Mesela ismi uzun olan bir kişinin `Ad`ı tek satır yerine çift satır olunca bizim bunu anlamamız zorlaşıyor. Büyüklük küçüklük, içinde sayı bulunup bulunmamasına göre bu kişileri inşaa ediyoruz. `Person::parseFromPdf($parts)` methodundan bu eşleştirmeleri daha ayrıntılı inceleyebilirsiniz.

##### HTML
HTML dosylarının okunmasında PDF dosyalarına göre çok basit. Çünkü sabit bir yapısı var ve `class` ve `tag`leri kullanarak istediğiniz kısmı kolaylıkla seçebiliyorsunuz.

`.resultTable > tbody > tr` seçicisini kullanarak tablonun satırlarını seçiyoruz. Sonra `td` etiketiyle bu satırları sütunlara bölüyoruz ve bilgileri sıraylar okuyarak kişileri oluşturuyoruz.

#### 2. İlişkilerin Kurulması
Burada bireylerin `Yakınlık Derecesi` sütunundaki bilgiyi kullarak ilişkileri belirliyoruz. Kişinin `Yakınlık Derecesi`nin içinde geçen `Anne`, `Baba`, `Kızı`, `Oğlu` kelimelerinin sayısını kullanarak ağaçlari seviyesinin belirliyoruz. `Kendisi` yazanın seviyesi `0` oluyor. Yani ağacımız kökü. Ağacımızın kökünden başlayarak kişileri artan seviye sırasında inceliyoruz ve `Anne`, `Baba` ilişkilerini kuruyoruz. Yani ağacımızı `Breadth First Search` olarak geziyoruz. 

Bu ilişkiler kurulduktan sonra ağacımızın yapraklarındaki kişilerin anne ve babalarının sadece adları bulunuyor. Tabloda bir satırları yok. Onların adlarından da `dummy` bireyler oluşturup ağacımıza ekliyoruz.

#### 3. Görüntülenme
`D3.js` kütüphanesi `JSON` veri tipini kabul ediyor. Bu sebeple her bireyin anne ve babasının iç içe gömülmüş bir şekilde olduğu bir `JSON` oluşturuyoruz.

Ağacın kökü `JSON` oluştururken anne, babasının da `->json()` fonksiyonun çağırıyor. Onlar da kendi anne babalarınınkini. Böylelikle `recursive` olarak `JSON` dosyamız oluşmuş oluyor.