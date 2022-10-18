## YÖK Atlas
YÖK Atlas web sitesindeki lisans program bilgilerinin alınmasını sağlamaktadır.

## Composer ile kurulum (Terminal)
Proje ana dizininde aşağıdaki komutu çalıştırın.
```
$ composer require empatisoft/yok-atlas:dev-master --prefer-source
```
## Composer ile kurulum (JSON)
composer.json dosyanızın require değerlerine ekleyip "composer update" komutunu çalıştırın.
```
"empatisoft/yok-atlas": "dev-master"
```
## Projenize elle ekleme
Sınıfı indirip proje dizininize kopyalayıp kullanabilirsiniz.

## Örnek Kullanım

```php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ERROR);

define('DIR', DIRECTORY_SEPARATOR);
define('ROOT', $_SERVER['DOCUMENT_ROOT'].DIR);

require_once ROOT."vendor".DIR."autoload.php";
require_once "helpers.php";

$atlas = new \Empatisoft\YokAtlas();
```

### Üniversiteye ait programları çekmek için

```
$universityCode = 1000;
$atlas->setUniversity($universityCode);
$programs = $atlas->getPrograms();
```

### Program bilgisini çekmek için

```
$programCode = 1000;
$year = 2021;
$atlas->setProgram($programCode);
$atlas->setYear($year);
$program = $atlas->getProgram();
```

## ->setUniversity() Metodu
Programlarını çekmek istediğiniz üniversiteye ait ÖSYM kodunu ayarlamanızı sağlar. (getPrograms metodu için zorunludur.)

## ->setYear() Metodu
Verilerini çekmek istediğiniz yılı ayarlar.

## ->getPrograms() Metodu
Üniversiteye ait tüm programları listeler. Bu metodun çalışabilmesi için "setUniversity" metodu ile üniversite kodu ayarlanmalıdır.

## ->getProgram() Metodu
Programa ait tüm analizlerin çekilmesini sağlar. Bu metodun çalışabilmesi için "setYear" metodu ile veri yılı ayarlanmalıdır.