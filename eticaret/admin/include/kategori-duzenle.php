<?php
// Hata gösterimi
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Mesaj değişkenleri
$mesaj = "";
$mesajTur = "";

// Tablo listesi
$tablolar = array(
    "elektronik" => "Elektronik",
    "giyim-ve-moda" => "Giyim ve Moda",
    "ev-ve-yasam" => "Ev ve Yaşam",
    "modul" => "Modül",
    "urunler" => "Ürünler"
);

// ID kontrolü
if (!empty($_GET["ID"])) {
    $kategoriID = $VT->filter($_GET["ID"]);
    $kategori = $VT->VeriGetir("kategoriler", "WHERE ID = ?", array($kategoriID), "ORDER BY ID ASC", 1);
    
    // Kategori var mı kontrol et
    if ($kategori != false) {
        // Form gönderildiğinde işlemi yap
        if ($_POST) {
            // Form verilerini al ve kontrol et
            if (!empty($_POST["tablo"]) && !empty($_POST["baslik"]) && !empty($_POST["anahtar"]) && 
                !empty($_POST["aciklama"]) && !empty($_POST["sirano"])) {
                
                $baslik = $VT->filter($_POST["baslik"]);
                $anahtar = $VT->filter($_POST["anahtar"]);
                $aciklama = $VT->filter($_POST["aciklama"]);
                $sirano = $VT->filter($_POST["sirano"]);
                $tablo = $VT->filter($_POST["tablo"]);
                $seflink = $VT->seflink($baslik);
                $resimAdi = $kategori[0]["resim"]; // Mevcut resim
                
                // Resim yükleme kontrolü
                if (!empty($_FILES["resim"]["name"])) {
                    $yukle = $VT->upload("resim", "../images/kategoriler/");
                    if ($yukle != false) {
                        $resimAdi = $yukle;
                    } else {
                        $mesaj = "Resim yükleme hatası!";
                        $mesajTur = "danger";
                    }
                }
                
                // Mesaj hatası yoksa güncelleme işlemi yap
                if (empty($mesaj)) {
                    // Manuel SQL sorgusu oluştur - parametreli sorgu çalışmadığı için
                    $sql = "UPDATE kategoriler SET 
                        baslik='".$baslik."', 
                        seflink='".$seflink."', 
                        tablo='".$tablo."', 
                        resim='".$resimAdi."', 
                        anahtar='".$anahtar."', 
                        description='".$aciklama."', 
                        durum=1, 
                        sirano=".$sirano.", 
                        tarih='".date("Y-m-d")."' 
                        WHERE ID=".$kategoriID;
                    
                    $guncelle = $VT->SorguCalistir($sql);
                    
                    if ($guncelle != false) {
                        $mesaj = "Kategori başarıyla güncellendi!";
                        $mesajTur = "success";
                        echo '<meta http-equiv="refresh" content="2;url='.SITE.'kategori-liste">';
                    } else {
                        $mesaj = "Güncelleme hatası!";
                        $mesajTur = "danger";
                    }
                }
            } else {
                $mesaj = "Tüm alanları doldurun!";
                $mesajTur = "warning";
            }
        }
        
        // Mesaj varsa göster
        if (!empty($mesaj)) {
            echo '<div class="alert alert-'.$mesajTur.'">'.$mesaj.'</div>';
        }
?>

<div class="content">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">Kategori Düzenle</h4>
            <div>
                <a href="<?=SITE?>kategori-liste" class="btn btn-info btn-sm"><i class="fas fa-list"></i> LİSTE</a>
                <a href="<?=SITE?>kategori-ekle" class="btn btn-success btn-sm"><i class="fas fa-plus"></i> YENİ EKLE</a>
            </div>
        </div>
        <div class="card-body">
            <!-- Kategorinin Mevcut Bilgileri -->
            <div class="row mb-4">
                <div class="col-md-8 mx-auto">
                    <div class="card bg-light">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Mevcut Kategori Bilgileri</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>ID:</strong> <?=$kategori[0]["ID"]?></p>
                                    <p><strong>Başlık:</strong> <?=$kategori[0]["baslik"]?></p>
                                    <p><strong>Tablo:</strong> 
                                        <?php 
                                        if(isset($tablolar[$kategori[0]["tablo"]])) {
                                            echo $tablolar[$kategori[0]["tablo"]]; 
                                        } else {
                                            echo $kategori[0]["tablo"];
                                        }
                                        ?>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Sıra No:</strong> <?=$kategori[0]["sirano"]?></p>
                                    <p><strong>Durum:</strong> <?=$kategori[0]["durum"] == 1 ? 'Aktif' : 'Pasif'?></p>
                                    <p><strong>Tarih:</strong> <?=date("d.m.Y", strtotime($kategori[0]["tarih"]))?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <form method="post" action="" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-8 mx-auto">
                        <div class="form-group mb-3">
                            <label>Tablo <span class="text-danger">*</span></label>
                            <select name="tablo" class="form-control">
                                <?php foreach($tablolar as $key => $value) { ?>
                                    <option value="<?=$key?>" <?php if($kategori[0]["tablo"] == $key) {echo "selected";} ?>><?=$value?></option>
                                <?php } ?>
                            </select>
                            <small class="form-text text-muted">Bu alan kategorinin ait olduğu tabloyu belirler.</small>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label>Başlık <span class="text-danger">*</span></label>
                            <input type="text" name="baslik" class="form-control" value="<?=$kategori[0]["baslik"]?>">
                        </div>
                        
                        <div class="form-group mb-3">
                            <label>Anahtar <span class="text-danger">*</span></label>
                            <input type="text" name="anahtar" class="form-control" value="<?=$kategori[0]["anahtar"]?>">
                        </div>
                        
                        <div class="form-group mb-3">
                            <label>Açıklama <span class="text-danger">*</span></label>
                            <textarea name="aciklama" class="form-control" rows="3"><?=$kategori[0]["description"]?></textarea>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label>Sıra No <span class="text-danger">*</span></label>
                            <input type="number" name="sirano" class="form-control" value="<?=$kategori[0]["sirano"]?>">
                        </div>
                        
                        <div class="form-group mb-3">
                            <label>Mevcut Resim</label>
                            <?php if (!empty($kategori[0]["resim"])) { ?>
                                <div class="mb-2">
                                    <img src="../images/kategoriler/<?=$kategori[0]["resim"]?>" class="img-thumbnail" style="max-width: 200px;">
                                </div>
                            <?php } else { ?>
                                <div class="alert alert-warning">Resim yok</div>
                            <?php } ?>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label>Yeni Resim (Opsiyonel)</label>
                            <input type="file" name="resim" class="form-control">
                        </div>
                        
                        <div class="form-group text-end mb-3">
                            <button type="submit" class="btn btn-primary">GÜNCELLE</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
    } else {
        echo '<div class="alert alert-danger">Kategori bulunamadı!</div>';
    }
} else {
    echo '<div class="alert alert-danger">Geçersiz ID!</div>';
}
?>