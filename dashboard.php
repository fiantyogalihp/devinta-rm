<?php
require_once 'config/database.php';
require_once 'config/session.php';

requireLogin();

$user = getCurrentUser();
$patients = [];
$searched = false;
$searchQuery = '';
$searchType = 'nama';
$successMessage = '';

// Pagination variables
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = isset($_GET['per_page']) && in_array(intval($_GET['per_page']), [10, 20, 50, 100]) 
    ? intval($_GET['per_page']) : 20;
$offset = ($page - 1) * $perPage;
$totalRecords = 0;
$totalPages = 0;

// Helper function to build pagination URLs
function buildPaginationUrl($page, $perPage = null) {
    $params = $_GET;
    $params['page'] = $page;
    if ($perPage !== null) {
        $params['per_page'] = $perPage;
    }
    return 'dashboard.php?' . http_build_query($params);
}

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id = intval($_POST['id'] ?? 0);
    if ($id > 0) {
        $conn = getDBConnection();
        $stmt = $conn->prepare("DELETE FROM patients WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $successMessage = 'Data pasien berhasil dihapus';
        }
        $stmt->close();
        closeDBConnection($conn);
    }
}

// Handle search or show all patients
$conn = getDBConnection();

if (isset($_GET['q']) && !empty($_GET['q'])) {
    $searched = true;
    $searchQuery = trim($_GET['q']);
    $searchType = $_GET['type'] ?? 'nama';
    
    // Build WHERE clause
    $whereClause = "";
    $param = "%{$searchQuery}%";
    
    switch ($searchType) {
        case 'no_rm':
            $whereClause = "no_rm LIKE ?";
            break;
        case 'alamat':
            $whereClause = "alamat LIKE ?";
            break;
        case 'no_telepon':
            $whereClause = "no_telepon LIKE ?";
            break;
        case 'tanggal_lahir':
            $whereClause = "tanggal_lahir LIKE ?";
            break;
        case 'nomor_identitas':
            $whereClause = "nomor_identitas LIKE ?";
            break;
        case 'usia':
            $whereClause = "usia = ?";
            $param = intval($searchQuery);
            break;
        default: // nama
            $whereClause = "nama LIKE ?";
    }
    
    // Count total records
    $countSql = "SELECT COUNT(*) as total FROM patients WHERE " . $whereClause;
    $countStmt = $conn->prepare($countSql);
    if ($searchType === 'usia') {
        $countStmt->bind_param("i", $param);
    } else {
        $countStmt->bind_param("s", $param);
    }
    $countStmt->execute();
    $countResult = $countStmt->get_result();
    $totalRecords = $countResult->fetch_assoc()['total'];
    $totalPages = ceil($totalRecords / $perPage);
    $countStmt->close();
    
    // Validate page number
    if ($page > $totalPages && $totalPages > 0) {
        $page = $totalPages;
        $offset = ($page - 1) * $perPage;
    }
    
    // Get paginated records
    $sql = "SELECT * FROM patients WHERE " . $whereClause . " ORDER BY created_at DESC LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    if ($searchType === 'usia') {
        $stmt->bind_param("iii", $param, $perPage, $offset);
    } else {
        $stmt->bind_param("sii", $param, $perPage, $offset);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $patients = $result->fetch_all(MYSQLI_ASSOC);
    
    $stmt->close();
} else {
    // Show all patients by default
    $searched = true; // Set to true to show the table
    
    // Count total records
    $countSql = "SELECT COUNT(*) as total FROM patients";
    $countResult = $conn->query($countSql);
    $totalRecords = $countResult->fetch_assoc()['total'];
    $totalPages = ceil($totalRecords / $perPage);
    
    // Validate page number
    if ($page > $totalPages && $totalPages > 0) {
        $page = $totalPages;
        $offset = ($page - 1) * $perPage;
    }
    
    // Get paginated records
    $sql = "SELECT * FROM patients ORDER BY created_at DESC LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $perPage, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    $patients = $result->fetch_all(MYSQLI_ASSOC);
    
    $stmt->close();
}

closeDBConnection($conn);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Hospital RM Search</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="dashboard">
        <header>
            <div class="header-content">
                <h1>🏥 Sistem Pencarian Rekam Medis</h1>
                <div class="user-info">
                    <span>👤 <?php echo htmlspecialchars($user['username']); ?></span>
                    <a href="logout.php" class="btn-logout">Logout</a>
                </div>
            </div>
        </header>

        <main>
            <div class="search-section">
                <h2>Pencarian Pasien</h2>
                <form method="GET" action="" class="search-form">
                    <div class="search-row">
                        <select name="type" class="search-type">
                            <option value="nama" <?php echo $searchType === 'nama' ? 'selected' : ''; ?>>Nama</option>
                            <option value="no_rm" <?php echo $searchType === 'no_rm' ? 'selected' : ''; ?>>No. RM</option>
                            <option value="alamat" <?php echo $searchType === 'alamat' ? 'selected' : ''; ?>>Alamat</option>
                            <option value="no_telepon" <?php echo $searchType === 'no_telepon' ? 'selected' : ''; ?>>No. Telepon</option>
                            <option value="tanggal_lahir" <?php echo $searchType === 'tanggal_lahir' ? 'selected' : ''; ?>>Tanggal Lahir</option>
                            <option value="nomor_identitas" <?php echo $searchType === 'nomor_identitas' ? 'selected' : ''; ?>>No. Identitas</option>
                            <option value="usia" <?php echo $searchType === 'usia' ? 'selected' : ''; ?>>Usia</option>
                        </select>
                        <input type="text" name="q" value="<?php echo htmlspecialchars($searchQuery); ?>" 
                               placeholder="Masukkan kata kunci pencarian..." class="search-input">
                        <button type="submit" class="btn-search">🔍 Cari</button>
                    </div>
                </form>

                <div class="action-buttons">
                    <a href="patient_add.php" class="btn-primary">➕ Tambah Pasien Baru</a>
                </div>
            </div>

            <?php if ($successMessage): ?>
            <div class="success-message">
                <?php echo htmlspecialchars($successMessage); ?>
            </div>
            <?php endif; ?>

            <?php if (!empty($patients)): ?>
            <div class="results-section">
                <h3><?php echo isset($_GET['q']) && !empty($_GET['q']) ? 'Hasil Pencarian' : 'Daftar Semua Pasien'; ?> (<?php echo $totalRecords; ?> pasien)</h3>
                
                <?php if ($totalPages > 1): ?>
                <div class="pagination-container top">
                    <div class="pagination-info">
                        <span>Menampilkan <?php echo $offset + 1; ?>-<?php echo min($offset + $perPage, $totalRecords); ?> dari <?php echo $totalRecords; ?> data</span>
                    </div>
                    <div class="pagination-controls">
                        <label>Tampilkan per halaman:</label>
                        <select onchange="location.href=this.value" class="per-page-select">
                            <option value="<?php echo buildPaginationUrl($page, 10); ?>" <?php echo $perPage == 10 ? 'selected' : ''; ?>>10</option>
                            <option value="<?php echo buildPaginationUrl($page, 20); ?>" <?php echo $perPage == 20 ? 'selected' : ''; ?>>20</option>
                            <option value="<?php echo buildPaginationUrl($page, 50); ?>" <?php echo $perPage == 50 ? 'selected' : ''; ?>>50</option>
                            <option value="<?php echo buildPaginationUrl($page, 100); ?>" <?php echo $perPage == 100 ? 'selected' : ''; ?>>100</option>
                        </select>
                    </div>
                </div>
                <?php endif; ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>No. RM</th>
                                <th>Nama</th>
                                <th>Alamat</th>
                                <th>No. Telepon</th>
                                <th>Tanggal Lahir</th>
                                <th>No. Identitas</th>
                                <th>Usia</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($patients as $patient): ?>
                            <tr>
                                <td class="no-rm"><?php echo htmlspecialchars($patient['no_rm']); ?></td>
                                <td><?php echo htmlspecialchars($patient['nama']); ?></td>
                                <td><?php echo htmlspecialchars($patient['alamat']); ?></td>
                                <td><?php echo htmlspecialchars($patient['no_telepon']); ?></td>
                                <td><?php echo $patient['tanggal_lahir'] ? htmlspecialchars($patient['tanggal_lahir']) : '-'; ?></td>
                                <td><?php echo $patient['nomor_identitas'] ? htmlspecialchars($patient['nomor_identitas']) : '-'; ?></td>
                                <td><?php echo $patient['usia'] ? htmlspecialchars($patient['usia']) : '-'; ?></td>
                                <td class="action-cell">
                                    <a href="patient_edit.php?id=<?php echo $patient['id']; ?>" class="btn-edit">Edit</a>
                                    <form method="POST" action="" style="display:inline;" 
                                          onsubmit="return confirm('Yakin ingin menghapus data pasien <?php echo htmlspecialchars($patient['nama']); ?>?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo $patient['id']; ?>">
                                        <button type="submit" class="btn-delete">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <?php if ($totalPages > 1): ?>
                <div class="pagination-container bottom">
                    <div class="pagination-nav">
                        <?php if ($page > 1): ?>
                            <a href="<?php echo buildPaginationUrl(1); ?>" class="page-link">First</a>
                            <a href="<?php echo buildPaginationUrl($page - 1); ?>" class="page-link">Previous</a>
                        <?php else: ?>
                            <span class="page-link disabled">First</span>
                            <span class="page-link disabled">Previous</span>
                        <?php endif; ?>
                        
                        <?php
                        // Generate page numbers
                        $startPage = max(1, $page - 2);
                        $endPage = min($totalPages, $page + 2);
                        
                        // Show first page if not in range
                        if ($startPage > 1) {
                            echo '<a href="' . buildPaginationUrl(1) . '" class="page-link">1</a>';
                            if ($startPage > 2) {
                                echo '<span class="page-ellipsis">...</span>';
                            }
                        }
                        
                        // Show page numbers
                        for ($i = $startPage; $i <= $endPage; $i++) {
                            if ($i == $page) {
                                echo '<span class="page-link active">' . $i . '</span>';
                            } else {
                                echo '<a href="' . buildPaginationUrl($i) . '" class="page-link">' . $i . '</a>';
                            }
                        }
                        
                        // Show last page if not in range
                        if ($endPage < $totalPages) {
                            if ($endPage < $totalPages - 1) {
                                echo '<span class="page-ellipsis">...</span>';
                            }
                            echo '<a href="' . buildPaginationUrl($totalPages) . '" class="page-link">' . $totalPages . '</a>';
                        }
                        ?>
                        
                        <?php if ($page < $totalPages): ?>
                            <a href="<?php echo buildPaginationUrl($page + 1); ?>" class="page-link">Next</a>
                            <a href="<?php echo buildPaginationUrl($totalPages); ?>" class="page-link">Last</a>
                        <?php else: ?>
                            <span class="page-link disabled">Next</span>
                            <span class="page-link disabled">Last</span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="pagination-info">
                        <span>Halaman <?php echo $page; ?> dari <?php echo $totalPages; ?></span>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <?php elseif ($searched): ?>
            <div class="no-results">
                <p>Tidak ada data pasien yang ditemukan.</p>
            </div>
            <?php else: ?>
            <div class="welcome-message">
                <p>Gunakan form pencarian di atas untuk mencari data pasien.</p>
                <p>Atau klik tombol "Tambah Pasien Baru" untuk menambahkan data pasien.</p>
            </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
