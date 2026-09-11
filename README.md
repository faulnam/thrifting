# thrifting

# FIFA — Curated Vintage & Thrifting E-Commerce Platform

Platform e-commerce premium untuk busana vintage, streetwear 90s/Y2K, jaket workwear, band tees single-stitch, celana denim, topi snapback, dan aksesori 1-of-1 terkurasi.

## Fitur Utama

- **Kurasi 1-of-1 Otentik**: Pemeriksaan keaslian tag era, single-stitch, zipper, dan grading kondisi transparan (10/10, 9.5/10, 9.0/10).
- **Sanitasi & Higienis**: Semua item telah melalui proses pencucian deep clean, anti-bakteri, dan steam suhu tinggi siap pakai.
- **Pengukuran Nyata (PxL)**: Detail ukuran Panjang x Lebar dada dalam sentimeter pada setiap item.
- **Katalog Lengkap**:
  - **Topi**: Snapback 90s (Green Underbrim), Beanie Knit, Bucket Hat, Dad Cap.
  - **Baju & Kaos**: Vintage Band Tees (Nirvana, Metallica), Graphic Tees 90s, Kemeja Flannel, Stussy 8-Ball, Harley Davidson.
  - **Celana & Denim**: Levi's 501 Made in USA, Y2K Woodland Camo Cargo, Wide-Wale Corduroy, Dickies 874.
  - **Jaket & Outerwear**: Carhartt Detroit Duck Canvas, Nike Center Swoosh Hoodie, Vintage Moto Leather Racing, Varsity Letterman, Patagonia Fleece.
  - **Sepatu**: Retro Skate Sneakers, Chunky Leather Loafers, Vintage Boots.
  - **Aksesoris & Tas**: Vintage Leather Crossbody Bag, Oval Wire Sunglasses, Vintage Leather Belt.
- **Pencarian & Filter Interaktif**: Live search real-time, filter kategori, gender (Pria/Wanita), ukuran, dan sorting harga.
- **Integrasi Pembayaran & Pengiriman**: Midtrans Payment Gateway & Biteship Logistics.
- **AI Virtual Shopping Assistant**: Asisten pintar responsif untuk panduan ukuran, autentisitas, dan rekomendasi item.
- **Panel Manajemen Admin**: Manajemen produk, varian, gambar, kategori, banner hero lifestyle, lokasi toko, artikel blog, kupon diskon, dan pesanan.

## Instalasi & Menjalankan

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve
```
