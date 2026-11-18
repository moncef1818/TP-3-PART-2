<?php
session_set_cookie_params([
    'lifetime' => 3600, 'path' => '/', 'httponly' => true, 'samesite' => 'Lax'
]);
session_start();
$_SESSION['logged_in'] = true;
$_SESSION['username'] = 'treasury_admin';
$weak_token = md5($_SESSION['username']);

$conn = new mysqli(
    getenv('DB_HOST') ?: 'netbank-db',
    getenv('DB_USER') ?: 'netbank_user',
    getenv('DB_PASSWORD') ?: 'netbank_pass123',
    getenv('DB_NAME') ?: 'netbank_db'
);
if ($conn->connect_error) { http_response_code(500); die("Database connection failed."); }
$banner_msg = '';

if (isset($_POST['reset_lab'])) {
    $conn->query("UPDATE corporate_accounts SET email='cfo@atlas-netbank.dz',
      balance_dzd=2500000, api_status='Standard Production Key' WHERE id=1");
    $conn->query("DELETE FROM wire_transfers WHERE id > 1");
    $banner_msg = "Lab state reset to default.";
}
if (isset($_GET['new_email'])) {
    $email = $conn->real_escape_string($_GET['new_email']);
    $conn->query("UPDATE corporate_accounts SET email='$email' WHERE id=1");
    if ($email !== 'cfo@atlas-netbank.dz') {
        $banner_msg = "Email updated! Audit Flag 1: FLAG{G3T_St4t3_Ch4ng3_CSRF_01}";
    }
}
if (isset($_POST['recipient'], $_POST['amount_dzd'])) {
    $rec = $conn->real_escape_string($_POST['recipient']);
    $amt = (int)$_POST['amount_dzd'];
    $conn->query("UPDATE corporate_accounts SET balance_dzd = balance_dzd - $amt WHERE id=1");
    $flag_note = ($amt >= 500000) ? "FLAG{P0ST_Aut0Subm1t_W1r3_02}" : "Transfer processed";
    $stmt = $conn->prepare("INSERT INTO wire_transfers (recipient, amount_dzd, status_note) VALUES (?, ?, ?)");
    $stmt->bind_param("sis", $rec, $amt, $flag_note);
    $stmt->execute();
    $banner_msg = "Wire transfer of $amt DZD executed.";
}
if (isset($_POST['rotate_api'], $_POST['csrf_token'])) {
    if (hash_equals($weak_token, $_POST['csrf_token'])) {
        $conn->query("UPDATE corporate_accounts SET api_status='ROTATED - FLAG{W34k_T0k3n_F0rg3ry_03}' WHERE id=1");
        $banner_msg = "Master API Key Rotated Successfully!";
    } else {
        $banner_msg = "CSRF Error: Invalid Security Token.";
    }
}
$acct = $conn->query("SELECT * FROM corporate_accounts WHERE id=1")->fetch_assoc();
$transfers = $conn->query("SELECT * FROM wire_transfers ORDER BY id DESC")->fetch_all(MYSQLI_ASSOC);
?>
<!doctype html><html><head><meta charset="utf-8"><title>Atlas NetBank</title></head>
<body style="font-family:sans-serif;max-width:820px;margin:35px auto">
<h2>Atlas NetBank — Corporate Treasury Portal</h2>
<p><b>Authenticated Session:</b> <?=htmlspecialchars($acct['username'])?> |
<b>Recovery Email:</b> <?=htmlspecialchars($acct['email'])?> |
<b>Balance:</b> <?=number_format($acct['balance_dzd'])?> DZD</p>
<p><b>Master API Key Status:</b> <?=htmlspecialchars($acct['api_status'])?></p>
<?php if ($banner_msg): ?><p style="padding:10px;background:#fef3c7"><?=htmlspecialchars($banner_msg)?></p><?php endif; ?>
<hr><h3>1. Update Account Recovery Email (GET Request)</h3>
<form method="GET">New Email: <input type="email" name="new_email" required><button>Update Email</button></form>
<hr><h3>2. Initiate Corporate Wire Transfer (POST Request)</h3>
<form method="POST">Recipient Account: <input name="recipient" required>
Amount (DZD): <input type="number" name="amount_dzd" required><button>Execute Wire</button></form>
<hr><h3>3. Rotate Master API Key (Weak Token)</h3>
<form method="POST"><input type="hidden" name="csrf_token" value="<?=htmlspecialchars($weak_token)?>">
<input type="hidden" name="rotate_api" value="1"><button>Rotate Production API Key</button></form>
<form method="POST" style="margin-top:10px"><button name="reset_lab" value="1">Reset Lab State</button></form>
<h3>Recent Wire Transfer Ledger</h3><table border="1" cellpadding="6" style="width:100%;border-collapse:collapse">
<tr><th>ID</th><th>Recipient</th><th>Amount</th><th>Audit / Status Note</th></tr>
<?php foreach ($transfers as $tr): ?><tr><td><?=$tr['id']?></td><td><?=htmlspecialchars($tr['recipient'])?></td>
<td><?=number_format($tr['amount_dzd'])?> DZD</td><td><?=htmlspecialchars($tr['status_note'])?></td></tr><?php endforeach; ?>
</table></body></html>
