# 📊 Muhasebe Yönetim Paneli

Bu proje, bireysel kullanıcıların veya küçük ölçekli işletmelerin finansal gelir ve gider hareketlerini dijital ortamda takip edebilmeleri için geliştirilmiş, PHP tabanlı bir yönetim panelidir. Modern arayüzü sayesinde mali durumunuzu anlık olarak izlemenize olanak tanır.

## 🚀 Projenin Amacı
Projenin temel amacı, karmaşık muhasebe süreçlerini basitleştirerek; kullanıcının anlık **toplam gelir**, **toplam gider** ve **net bakiye** (kasa durumu) bilgilerine ulaşmasını sağlamaktır. Kullanıcılar her türlü finansal işlemi kategorize edebilir, güncelleyebilir veya silebilirler.

## 🛠️ Teknik Özellikler
* **Backend:** PHP (PDO Veritabanı Sürücüsü)
* **Frontend:** HTML5, Tailwind CSS, FontAwesome İkon Seti
* **Veritabanı:** MySQL
* **Güvenlik:** SQL Injection saldırılarına karşı `Prepared Statements` (hazırlanmış ifadeler) ve XSS saldırılarına karşı `htmlspecialchars` fonksiyonları kullanılmıştır.

## 📦 Kurulum ve Çalıştırma Talimatı

Sistemi yerel sunucunuzda (localhost) çalıştırmak için aşağıdaki adımları izleyin:

### 1. Veritabanı Kurulumu
* `localhost/phpmyadmin` paneline gidin.
* **`muhasebe_db`** adında yeni bir veritabanı oluşturun.
* Aşağıdaki SQL kodunu SQL sekmesinde çalıştırarak tabloyu oluşturun:

```sql
CREATE TABLE `islemler` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `baslik` varchar(255) NOT NULL,
  `kategori` varchar(100) NOT NULL,
  `tutar` decimal(10,2) NOT NULL,
  `tip` enum('gelir','gider') NOT NULL,
  `tarih` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;