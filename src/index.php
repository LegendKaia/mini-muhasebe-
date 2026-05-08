<?php
// 1. Veritabanı Bağlantısını Çağır
require_once 'baglan.php';

// 2. Düzenleme Modu Kontrolü
$duzenlenecek_id = isset($_GET['duzenle']) ? (int)$_GET['duzenle'] : null;
$duzenle_modu = false;
$v_edit = ['baslik' => '', 'kategori' => '', 'tutar' => ''];

if ($duzenlenecek_id) {
    $sorgu = $db->prepare("SELECT * FROM islemler WHERE id = ?");
    $sorgu->execute([$duzenlenecek_id]);
    $sonuc = $sorgu->fetch(PDO::FETCH_ASSOC);
    if ($sonuc) {
        $duzenle_modu = true;
        $v_edit = $sonuc;
    }
}

// 3. Ekleme veya Güncelleme İşlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['kaydet'])) {
    $baslik   = htmlspecialchars($_POST['baslik']);
    $kategori = htmlspecialchars($_POST['kategori']);
    $tutar    = (float)$_POST['tutar'];
    $tip      = ($tutar > 0) ? 'gelir' : 'gider';
    $islem_id = $_POST['islem_id'];

    if (!empty($islem_id)) {
        // Güncelleme Yap
        $guncelle = $db->prepare("UPDATE islemler SET baslik=?, kategori=?, tutar=?, tip=? WHERE id=?");
        $guncelle->execute([$baslik, $kategori, $tutar, $tip, $islem_id]);
    } else {
        // Yeni Kayıt Ekle
        $ekle = $db->prepare("INSERT INTO islemler (baslik, kategori, tutar, tip) VALUES (?, ?, ?, ?)");
        $ekle->execute([$baslik, $kategori, $tutar, $tip]);
    }
    header("Location: index.php");
    exit;
}

// 4. Silme İşlemi
if (isset($_GET['sil'])) {
    $id = (int)$_GET['sil'];
    $sil = $db->prepare("DELETE FROM islemler WHERE id = ?");
    $sil->execute([$id]);
    header("Location: index.php");
    exit;
}

// 5. Verileri Çek ve Hesapla
$islemler = $db->query("SELECT * FROM islemler ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
$gelir    = $db->query("SELECT SUM(tutar) FROM islemler WHERE tip='gelir'")->fetchColumn() ?: 0;
$gider    = $db->query("SELECT SUM(tutar) FROM islemler WHERE tip='gider'")->fetchColumn() ?: 0;
$net      = $gelir + $gider;
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Muhasebe Yönetim Paneli</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-[#0f172a] text-slate-200 min-h-screen p-4">
    <div class="max-w-6xl mx-auto pt-10">
        
        <!-- Üst Özet Kartları -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-slate-800 p-6 rounded-2xl border-l-4 border-emerald-500 shadow-xl">
                <span class="text-xs text-slate-400 uppercase font-bold">Toplam Gelir</span>
                <div class="text-3xl font-black text-emerald-400 mt-1"><?= number_format($gelir, 2, ',', '.') ?> ₺</div>
            </div>
            <div class="bg-slate-800 p-6 rounded-2xl border-l-4 border-rose-500 shadow-xl">
                <span class="text-xs text-slate-400 uppercase font-bold">Toplam Gider</span>
                <div class="text-3xl font-black text-rose-400 mt-1"><?= number_format(abs($gider), 2, ',', '.') ?> ₺</div>
            </div>
            <div class="bg-slate-800 p-6 rounded-2xl border-l-4 border-cyan-500 shadow-xl">
                <span class="text-xs text-slate-400 uppercase font-bold">Net Durum</span>
                <div class="text-3xl font-black text-cyan-400 mt-1"><?= number_format($net, 2, ',', '.') ?> ₺</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Form Bölümü -->
            <div class="lg:col-span-4">
                <div class="bg-slate-800 p-6 rounded-2xl border border-slate-700 shadow-2xl sticky top-10">
                    <h2 class="text-xl font-bold mb-6 flex items-center gap-2 <?= $duzenle_modu ? 'text-orange-400' : 'text-cyan-400' ?>">
                        <i class="fas <?= $duzenle_modu ? 'fa-edit' : 'fa-plus-circle' ?>"></i>
                        <?= $duzenle_modu ? 'İŞLEMİ DÜZENLE' : 'YENİ İŞLEM EKLE' ?>
                    </h2>
                    <form action="index.php" method="POST" class="space-y-5">
                        <input type="hidden" name="islem_id" value="<?= $duzenle_modu ? $v_edit['id'] : '' ?>">
                        
                        <div>
                            <label class="block text-sm text-slate-400 mb-1">İşlem Adı</label>
                            <input type="text" name="baslik" value="<?= htmlspecialchars($v_edit['baslik']) ?>" required class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 focus:border-cyan-500 outline-none transition-all">
                        </div>
                        
                        <div>
                            <label class="block text-sm text-slate-400 mb-1">Kategori</label>
                            <input type="text" name="kategori" value="<?= htmlspecialchars($v_edit['kategori']) ?>" required class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 focus:border-cyan-500 outline-none transition-all">
                        </div>
                        
                        <div>
                            <label class="block text-sm text-slate-400 mb-1">Tutar (Gider için eksi - koyun)</label>
                            <input type="number" step="0.01" name="tutar" value="<?= $v_edit['tutar'] ?>" required class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 focus:border-cyan-500 outline-none transition-all">
                        </div>
                        
                        <button type="submit" name="kaydet" class="w-full py-4 rounded-xl font-bold shadow-lg transition-all <?= $duzenle_modu ? 'bg-orange-600 hover:bg-orange-500' : 'bg-cyan-600 hover:bg-cyan-500' ?>">
                            <?= $duzenle_modu ? 'GÜNCELLEMEYİ KAYDET' : 'SİSTEME KAYDET' ?>
                        </button>
                        
                        <?php if($duzenle_modu): ?>
                            <a href="index.php" class="block text-center text-sm text-slate-500 hover:text-white underline mt-2">Vazgeç</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <!-- Liste Bölümü -->
            <div class="lg:col-span-8">
                <div class="bg-slate-800 rounded-2xl border border-slate-700 shadow-2xl overflow-hidden">
                    <table class="w-full">
                        <thead class="bg-slate-900/50">
                            <tr class="text-slate-500 text-xs uppercase tracking-widest font-black">
                                <th class="p-5 text-left">İşlem Detayı</th>
                                <th class="p-5 text-right">Tutar</th>
                                <th class="p-5 text-center">İşlem</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-700/50">
                            <?php foreach ($islemler as $islem): ?>
                            <tr class="hover:bg-slate-700/30 transition-all">
                                <td class="p-5">
                                    <div class="font-bold text-slate-200"><?= htmlspecialchars($islem['baslik']) ?></div>
                                    <div class="text-[10px] text-cyan-500 font-bold uppercase tracking-wider"><?= htmlspecialchars($islem['kategori']) ?></div>
                                    <div class="text-[9px] text-slate-500 mt-1 font-mono"><?= $islem['tarih'] ?></div>
                                </td>
                                <td class="p-5 text-right font-black text-lg <?= $islem['tip'] == 'gelir' ? 'text-emerald-400' : 'text-rose-500' ?>">
                                    <?= $islem['tip'] == 'gelir' ? '+' : '' ?><?= number_format($islem['tutar'], 2, ',', '.') ?> ₺
                                </td>
                                <td class="p-5 text-center">
                                    <div class="flex items-center justify-center gap-4 text-lg">
                                        <a href="index.php?duzenle=<?= $islem['id'] ?>" class="text-slate-500 hover:text-orange-400 transition-colors"><i class="fas fa-edit"></i></a>
                                        <a href="index.php?sil=<?= $islem['id'] ?>" onclick="return confirm('Silmek istiyor musun?')" class="text-slate-500 hover:text-rose-500 transition-colors"><i class="fas fa-trash"></i></a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if(!$islemler): ?>
                                <tr><td colspan="3" class="p-10 text-center text-slate-500 italic">Henüz bir veri girişi yapılmadı.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>