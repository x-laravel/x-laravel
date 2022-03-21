# Example Proje Şablonu

Yeni projelerde kullanılmak üzere hazır proje şablonlarının
bulunduğu [Github Repository](https://github.com/x-laravel/x-laravel/tree/master)

## Proje Şablonları

### [simple-web](https://github.com/x-laravel/x-laravel/tree/simple-web)

Web projeleri için hazırlanmış proje şablonu.

````
git clone https://github.com/x-laravel/x-laravel.git -b simple-web web
````

### [laravel-web](https://github.com/x-laravel/x-laravel/tree/laravel-web)

Laravel projeleri için hazırlanmış proje şablonu.

````
git clone https://github.com/x-laravel/x-laravel.git -b laravel-web www
````

### [restApi](https://github.com/x-laravel/x-laravel/tree/restApi)

laravel-web dalının klonudur. Laravel ile geliştirilecek api projeleri için hazırlanmış proje şablonudur. Şifre
sıfırlama özelliği eklenmiştir.

````
git clone https://github.com/x-laravel/x-laravel.git -b restApi api
````

### [restApi-sanctum](https://github.com/x-laravel/x-laravel/tree/restApi-sanctum)

restApi dalının klonudur. Kimlik doğrulama için [laravel/sanctum](https://laravel.com/docs/sanctum) kullanılır.

````
git clone https://github.com/x-laravel/x-laravel.git -b restApi-sanctum api
````

### [restApi-passport](https://github.com/x-laravel/x-laravel/tree/restApi-passport)

restApi dalının klonudur. Kimlik doğrulama için [laravel/passport](https://laravel.com/docs/passport) kullanılır.

````
git clone https://github.com/x-laravel/x-laravel.git -b restApi-passport api
````

### [restApi-passport-multiGuard](https://github.com/x-laravel/x-laravel/tree/restApi-passport-multiGuard)

restApi-passport dalının klonudur. **user** ve **admin** olarak birden fazla guard kullanılabilir.

````
git clone https://github.com/x-laravel/x-laravel.git -b restApi-passport-multiGuard api
````

### [restApi-tenancy](https://github.com/x-laravel/x-laravel/tree/restApi-tenancy)

restApi-passport-multiGuard dalının klonudur. Tenant özelliği eklenmiştir.

````
git clone https://github.com/x-laravel/x-laravel.git -b restApi-tenancy api
````

## Laravel'de Yapılan İşlemler

### Eklenen Özellikler

- Laravel Türkçe dil dosyaları eklendi, uygulama dili Türkçe olarak değiştirildi.
- Zaman dilimi Europe/Istanbul olarak güncellendi.
- Otomatik deploy özelliği eklendi.
- Laravel log dosyasının tek dosya yerine günlere göre ayrılması sağlandı.
- log:clear komutu eklenerek log dosyalarının temizlenebilmesi sağlandı.
- reinstall komutu ile otoamatik olarak uygulama tekrar kurulabilmesi sağlandı.
- default, email, listener, notification kuyruk görevleri için Supervisor yapılandırması eklendi.
- Laravel veritabanı için MongoDB kullanılabilmesi sağlandı.
- Model değişiklik geçmişinin veritabanına kaydedilmesi sağlandı.
- Sistem bakım ve yedekleme özelliği eklendi.
- DigitalOcean'ın Space servisinin kullanılabilmesi için gerekli paketler ve tanımlamalar yapıldı.
- cdn() yardımcı fonksiyonu eklendi.
- ResponseMacroServiceProvider ile hazır json dönüş tipleri eklendi. Handler.php dosyası düzenlendi ve hata mesajlarının
  json formatına dönüştürülmesi sağlandı.
- Kontrolörden hata mesajlarını yerelleştirme özelliği eklendi.
- Rest Api isteklerinin MongoDB veritabanına kaydedilmesi sağlandı.

### Laravel'e Eklenen Paketler

- [x-laravel/model-settings-bag](https://github.com/x-laravel/model-settings-bag)
- [x-laravel/str-extend](https://github.com/x-laravel/str-tr)
- [x-laravel/str-tr-extend](https://github.com/x-laravel/str-tr-extend)
- [x-laravel/validation-extend](https://github.com/x-laravel/validation-extend)
- [spiritix/lada-cache](https://github.com/spiritix/lada-cache)
- [laravel/slack-notification-channel](https://github.com/laravel/slack-notification-channel)
- [spatie/laravel-activitylog](https://github.com/spatie/laravel-activitylog)
- [jenssegers/mongodb](https://github.com/jenssegers/laravel-mongodb)
- [league/flysystem-aws-s3-v3](https://github.com/thephpleague/flysystem-aws-s3-v3)
- [spatie/laravel-backup](https://github.com/spatie/laravel-backup)

## Rest Api Testi

Postman'da kullanılmak üzere her bir proje şablonu için koleksiyonlar eklenmiştir. Seçtiğiniz şablona göre birini seçip
Postman'dan içe aktarıp kullanabilirsiniz.

- [restApi](https://raw.githubusercontent.com/x-laravel/x-laravel/master/postman/collections/restApi.collection.json)
- [restApi-sanctum](https://raw.githubusercontent.com/x-laravel/x-laravel/master/postman/collections/restApi-sanctum.collection.json)
- [restApi-passport](https://raw.githubusercontent.com/x-laravel/x-laravel/master/postman/collections/restApi-passport.collection.json)
- [restApi-passport-multiGuard](https://raw.githubusercontent.com/x-laravel/x-laravel/master/postman/collections/restApi-passport-multiGuard.collection.json)
- [restApi-tenancy](https://raw.githubusercontent.com/x-laravel/x-laravel/master/postman/collections/restApi-tenancy.collection.json)

[Local Ortam Değişkeni](https://raw.githubusercontent.com/x-laravel/x-laravel/master/postman/environments/local.environment.json)