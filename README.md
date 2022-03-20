# Example Projesi

Laravel ile geliştirilen api projesidir.

## Kurulum

Kurulum için aşağıdaki adımları sırası ile uygulayınız.

### Vagrant

Aşağıda listesi verilen programların ve eklentilerin kurulumlarını tamamlayınız:

- [VirtualBox](https://www.virtualbox.org/wiki/Downloads)
- [VirtualBox Extension Pack](https://www.virtualbox.org/wiki/Downloads#Extension_packs)
- [Vagrant](https://www.vagrantup.com/)

Vagrantfile.example dosyasını kopyalayınız ve adını Vagrantfile olarak değiştiriniz.

### Host Dosyasında Adres Tanımalama

İşletim sisteminizdeki host dosyasının içine aşağıdaki tanımlamayı yapınız:

````
127.0.0.1  api.local.example.com
````

Windows için: C:\Windows\System32\drivers\etc\hosts

Linux için: /etc/hosts

### Nginx

nginx.conf.example dosyasını kopyalayınız ve adını nginx.conf olarak değiştiriniz.

### Projeyi Ayağa Kaldırma

Proje dizininde terminali açıp aşağıdaki komutu koşturunuz:

````
vagrant up
````

Terminalde "Happy codings :)." yazısını görene kadar bekleyiniz.

### Composer

Vagrant kurulumu bittikten sonra aşağıdaki komut ile sanal makinenin terminaline geçiş yapınız:

````
vagrant ssh
````

Aşağıdaki komut ile sanal makine içinde proje dizinine geçiş yapınız:

````
cd /var/www/example.com/api
````

ve

````
composer i
````

komutu ile composer kurulumunu yapınız.

### Laravel

.env.example dosyasını kopyalayınız ve adını .env olarak değiştiriniz.

Terminalde aşağıdaki komutu çalıştırarak uygulama anahtarını ayarlayınız:

````
php artisan key:generate
````

### Veritabanı

Aşağıdaki komut ile veritabanının oluşturulmasını sağlayınız:

````
php artisan reinstall
````

## Final

Eğer tüm aşamaları doğru şekilde yaptıysanız, internet
tarayıcınızdan [http://api.local.example.com](http://api.local.example.com/) adresini açtığınızda Laravel'in karşılama
sayfasını görmeniz gerekir.

Eğer Laravel'in karşılama sayfasını görmüyorsanız bir şeyler yanlış gitmiş olabilir. Lütfen yaptığınız işlemlerin bu
yönergede belirtilen adımları takip ettiğinden emin olunuz.
