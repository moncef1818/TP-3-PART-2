<?php
session_set_cookie_params([
    'lifetime' => 3600, 'path' => '/', 'httponly' => false, 'samesite' => 'None'
]);
session_start();
$_SESSION['audit_token'] = "FLAG{R3fl3ct3d_C0nt3xt_Tr4c3_01}";
setcookie("MEDCARE_ADMIN_TOKEN", "FLAG{St0r3d_Sink_Aud1t_02}", time()+3600, "/", "", false, false);

$conn = new mysqli(
    getenv('DB_HOST') ?: 'helpdesk-db',
    getenv('DB_USER') ?: 'helpdesk_user',
    getenv('DB_PASSWORD') ?: 'helpdesk_pass123',
    getenv('DB_NAME') ?: 'medcare_db'
);
if ($conn->connect_error) { http_response_code(500); die("Database connection failed."); }
if (isset($_POST['submit_ticket'])) {
    $author = $conn->real_escape_string($_POST['author']);
    $dept = $conn->real_escape_string($_POST['department']);
    $msg = $conn->real_escape_string($_POST['message']);
    $conn->query("INSERT INTO support_tickets (author, department, message, internal_audit_note)
      VALUES ('$author', '$dept', '$msg', 'User submitted')");
}
if (isset($_POST['reset_tickets'])) $conn->query("DELETE FROM support_tickets WHERE id > 3");
$tickets = $conn->query("SELECT id, author, department, message FROM support_tickets ORDER BY id DESC")->fetch_all(MYSQLI_ASSOC);
?>
<!doctype html><html><head><meta charset="utf-8"><title>MedCare IT HelpDesk Portal</title></head>
<body style="font-family:sans-serif;max-width:800px;margin:40px auto">
<h2>MedCare Internal IT HelpDesk</h2>
<!-- Audit Reference: <?=$_SESSION['audit_token']?> -->
<form method="GET"><h3>1. Knowledge Base & Ticket Search</h3>
Filter Keyword: <input name="q"><button>Filter</button></form>
<?php if (isset($_GET['q'])): ?><p>Showing results for filter: <strong><?=$_GET['q']?></strong></p><?php endif; ?>
<hr><form method="POST"><h3>2. Submit New Support Ticket</h3>
Username: <input name="author" required><br><br>Department: <input name="department" required><br><br>
Issue Description:<br><textarea name="message" rows="3" cols="60" required></textarea><br><br>
<button name="submit_ticket" value="1">Post Ticket</button>
<button name="reset_tickets" value="1" formnovalidate>Reset Queue</button></form>
<h3>Active Support Queue</h3><table border="1" cellpadding="6" style="width:100%;border-collapse:collapse">
<tr><th>ID</th><th>Author</th><th>Department</th><th>Issue Description</th></tr>
<?php foreach ($tickets as $t): ?><tr><td><?=$t['id']?></td><td><?=$t['author']?></td>
<td><?=$t['department']?></td><td><?=$t['message']?></td></tr><?php endforeach; ?></table>
<hr><h3>3. Department Quick-Tab Selector</h3>
<p>Append <code>#tab=DepartmentName</code> to the URL and reload to test client-side tab rendering.</p>
<div id="tab-banner" style="padding:10px;background:#eef2ff;border:1px solid #94a3b8">Active Department View: Default</div>
<script>
const DOM_AUDIT_FLAG = "FLAG{D0M_S1nk_Inn3rHTML_03}";
window.addEventListener('DOMContentLoaded', function () {
  const hashParams = new URLSearchParams(window.location.hash.substring(1));
  const activeTab = hashParams.get('tab');
  if (activeTab) document.getElementById('tab-banner').innerHTML = "Active Department View: " + activeTab;
});
</script></body></html>
