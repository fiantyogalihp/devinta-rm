<?php
require_once 'config/database.php';
require_once 'config/session.php';

requireLogin();

$id = intval($_GET['id'] ?? 0);
$error = '';
$errors = [];
$patient = null;

if ($id <= 0) {
    header('Location: dashboard.php');
    exit();
}

// Fetch patient data
$conn = getDBConnection();
$stmt = $conn->prepare("SELECT * FROM patients WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();
    closeDBConnection($conn);
    header('Location: dashboard.php');
    exit();
}

$patient = $result->fetch_assoc();
$stmt->close();
closeDBConnection($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $no_rm = trim($_POST['no_rm'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');
    $no_telepon = trim($_POST['no_telepon'] ?? '');
    $tanggal_lahir = trim($_POST['tanggal_lahir'] ?? '');
    $nomor_identitas = trim($_POST['nomor_identitas'] ?? '');
    $usia = trim($_POST['usia'] ?? '');
    
    // Update patient array with form data
    $patient = array_merge($patient, $_POST);
    
    // Validation
    if (empty($no_rm)) $errors['no_rm'] = 'Nomor rekam medis harus diisi';
    if (empty($nama)) $errors['nama'] = 'Nama harus diisi';
    if (empty($alamat)) $errors['alamat'] = 'Alamat harus diisi';
    if (empty($no_telepon)) $errors['no_telepon'] = 'Nomor telepon harus diisi';
    
    if (empty($errors)) {
        $conn = getDBConnection();
        
        // Check if no_rm already exists (excluding current patient)
        $stmt = $conn->prepare("SELECT id FROM patients WHERE no_rm = ? AND id != ?");
        $stmt->bind_param("si", $no_rm, $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error = 'Nomor rekam medis sudah digunakan';
            $stmt->close();
        } else {
            $stmt->close();
            
            // Update patient
            $stmt = $conn->prepare("UPDATE patients SET no_rm = ?, nama = ?, alamat = ?, no_telepon = ?, tanggal_lahir = ?, nomor_identitas = ?, usia = ? WHERE id = ?");
            
            $tanggal_lahir_val = !empty($tanggal_lahir) ? $tanggal_lahir : null;
            $nomor_identitas_val = !empty($nomor_identitas) ? $nomor_identitas : null;
            $usia_val = !empty($usia) ? intval($usia) : null;
            
            $stmt->bind_param("ssssssii", $no_rm, $nama, $alamat, $no_telepon, $tanggal_lahir_val, $nomor_identitas_val, $usia_val, $id);
            
            if ($stmt->execute()) {
                $stmt->close();
                closeDBConnection($conn);
                header('Location: dashboard.php?success=2');
                exit();
            } else {
                $error = 'Gagal mengupdate data pasien';
            }
            
            $stmt->close();
        }
        
        closeDBConnection($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pasien - Hospital RM Search</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="page-container">
        <header>
            <div class="header-content">
                <h1>🏥 Edit Data Pasien</h1>
                <a href="dashboard.php" class="btn-back">← Kembali ke Dashboard</a>
            </div>
        </header>

        <main>
            <div class="form-container">
                <form method="POST" action="">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="no_rm">Nomor Rekam Medis <span class="required">*</span></label>
                            <input type="text" id="no_rm" name="no_rm" required 
                                   placeholder="Contoh: RM-2024-001"
                                   value="<?php echo htmlspecialchars($patient['no_rm']); ?>">
                            <?php if (isset($errors['no_rm'])): ?>
                            <span class="error"><?php echo $errors['no_rm']; ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="nama">Nama Lengkap <span class="required">*</span></label>
                            <input type="text" id="nama" name="nama" required 
                                   placeholder="Nama lengkap pasien"
                                   value="<?php echo htmlspecialchars($patient['nama']); ?>">
                            <?php if (isset($errors['nama'])): ?>
                            <span class="error"><?php echo $errors['nama']; ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="alamat">Alamat <span class="required">*</span></label>
                        <textarea id="alamat" name="alamat" required rows="3" 
                                  placeholder="Alamat lengkap pasien"><?php echo htmlspecialchars($patient['alamat']); ?></textarea>
                        <?php if (isset($errors['alamat'])): ?>
                        <span class="error"><?php echo $errors['alamat']; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="no_telepon">Nomor Telepon <span class="required">*</span></label>
                            <input type="tel" id="no_telepon" name="no_telepon" required 
                                   placeholder="Contoh: 081234567890"
                                   value="<?php echo htmlspecialchars($patient['no_telepon']); ?>">
                            <?php if (isset($errors['no_telepon'])): ?>
                            <span class="error"><?php echo $errors['no_telepon']; ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="tanggal_lahir">Tanggal Lahir</label>
                            <input type="date" id="tanggal_lahir" name="tanggal_lahir"
                                   value="<?php echo htmlspecialchars($patient['tanggal_lahir'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="nomor_identitas">Nomor Identitas (KTP/SIM/Paspor)</label>
                            <input type="text" id="nomor_identitas" name="nomor_identitas" 
                                   placeholder="Nomor identitas (opsional)"
                                   value="<?php echo htmlspecialchars($patient['nomor_identitas'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="usia">Usia (tahun)</label>
                            <input type="number" id="usia" name="usia" min="0" max="150" 
                                   placeholder="Usia pasien (opsional)"
                                   value="<?php echo htmlspecialchars($patient['usia'] ?? ''); ?>">
                        </div>
                    </div>

                    <?php if ($error): ?>
                    <div class="error-message">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                    <?php endif; ?>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary">💾 Update Data Pasien</button>
                        <a href="dashboard.php" class="btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
