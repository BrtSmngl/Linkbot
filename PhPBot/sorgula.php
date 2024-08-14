<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="60">
    <title>Sorgula</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <?php

            date_default_timezone_set('Europe/Istanbul');
            $vt_sunucu = "localhost";
            $vt_kullanici = "root";
            $vt_sifre = "";
            $vt_adi = "linkbot";

            $baglan = mysqli_connect($vt_sunucu, $vt_kullanici, $vt_sifre, $vt_adi);

            if (!$baglan) {
                die("Bağlantı hatası: " . mysqli_connect_error());
            }

            // Sağlık durumu 0 olan bir web sitesini seç
            $sql_check = "SELECT * FROM website WHERE saglik_durum = 0 ORDER BY RAND() LIMIT 1";
            $result_check = mysqli_query($baglan, $sql_check);

            if (mysqli_num_rows($result_check) > 0) {
                $selected_website = mysqli_fetch_assoc($result_check);
                $selected_id = $selected_website["id"];

                // Seçilen web sitesinin sağlık durumunu 1 olarak ve kontrol_tarih'i güncelle
                $sql_update = "UPDATE website SET saglik_durum = 1, kontrol_tarih = ? WHERE id = ?";
                $stmt = mysqli_prepare($baglan, $sql_update);
                $current_time = date('Y-m-d H:i:s');
                mysqli_stmt_bind_param($stmt, "si", $current_time, $selected_id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);

                echo '<div class="alert alert-info">"' . htmlspecialchars($selected_website["website_link"]) . '" kontrol ediliyor</div>';
                echo '<div class="alert alert-danger">';
                echo "ID: " . $selected_website["id"] . " - Link: " . $selected_website["website_link"] . " - Sağlık Durumu: " . $selected_website["saglik_durum"] . " - Kontrol Tarihi: " . $selected_website["kontrol_tarih"];
                echo '</div>';

                // Seçilen web sitesinin sayfasını al
                $url = $selected_website["website_link"];
                $page = file_get_contents($url);
                preg_match_all("/href=\"([^\"]+)/i", $page, $links);

                // Yalnızca http ve https ile başlayan bağlantıları filtrele
                $filtered_links = array_filter($links[1], function($link) {
                    return preg_match("/^https?:\/\//", $link);
                });

                if (!empty($filtered_links)) {
                    echo '<div class="mt-4"><h4>Sayfadaki Bağlantılar:</h4><ul>';
                    foreach ($filtered_links as $link) {
                        echo '<li><a href="' . htmlspecialchars($link) . '" target="_blank">' . htmlspecialchars($link) . '</a></li>';
                    }
                    echo '</ul></div>';
                } else {
                    echo '<div class="alert alert-info">Bu sayfada bağlantı bulunamadı.</div>';
                }
            } else {
                echo '<div class="alert alert-info">Tüm sayfalar kontrol edildi</div>';
            }

            mysqli_close($baglan);
            ?>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-7Vx12NY/6cb7QGdEisE0H0Bb1XfH+xB7Hn2a90xXZ2I6W3z7NEe5bF9B0Vjv9TyY" crossorigin="anonymous"></script>
</body>
</html>
