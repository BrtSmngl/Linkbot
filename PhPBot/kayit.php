<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayıt</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>

<?php

$vt_sunucu = "localhost";
$vt_kullanici = "root";
$vt_sifre = "";
$vt_adi = "linkbot";

$baglan = mysqli_connect($vt_sunucu, $vt_kullanici, $vt_sifre, $vt_adi);

if (!$baglan) {
    die("Bağlantı hatası: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["submit"])) {
        // URL ekleme işlemi
        $url = isset($_POST["url"]) ? $_POST["url"] : '';

        if (!empty($url)) {
            $website_link = $url;
            $saglik_durum = 0;
            $kontrol_tarih = date('Y-m-d H:i:s');

            $ekle = "INSERT INTO website (website_link, saglik_durum, kontrol_tarih) VALUES ('$website_link', '$saglik_durum', '$kontrol_tarih')";

            if ($baglan->query($ekle) === TRUE) {
                echo "<div class='alert alert-success'>Link başarıyla kaydedildi.</div>";
                // Formu temizleyip sayfayı yenile
                echo "<meta http-equiv='refresh' content='0'>";
                exit();
            } else {
                echo "Hata: " . $ekle . "<br>" . $baglan->error . "<br>";
            }
        } else {
            echo "<div class='alert alert-warning'>URL boş bırakıldı, kaydedilmedi.</div>";
        }
    } elseif (isset($_POST["update"])) {
        // saglik_durum güncelleme işlemi
        $guncelle = "UPDATE website SET saglik_durum = 0 WHERE saglik_durum = 1";

        if ($baglan->query($guncelle) === TRUE) {
            echo "<div class='alert alert-success'>Durumlar başarıyla güncellendi.</div>";
            // Sayfayı yeniden yükleyip formu temizle
            echo "<meta http-equiv='refresh' content='0'>";
            exit();
        } else {
            echo "Hata: " . $guncelle . "<br>" . $baglan->error . "<br>";
        }
    }
}

$baglan->close();
?>

<div class="container mt-5">
    <form method="post">
        <div class="mb-3">
            <label class="form-label">Web Sayfası URL'si</label>
            <input type="text" class="form-control" name="url">
        </div>
        <button type="submit" class="btn btn-primary" name="submit">Linkleri Çek ve Kaydet</button>
        <button type="submit" class="btn btn-warning" name="update">Durumları Güncelle</button>
    </form>
</div>

</body>
</html>
