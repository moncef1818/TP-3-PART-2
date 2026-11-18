CREATE DATABASE IF NOT EXISTS medcare_db;
USE medcare_db;
CREATE TABLE IF NOT EXISTS support_tickets (
  id INT AUTO_INCREMENT PRIMARY KEY,
  author VARCHAR(50),
  department VARCHAR(50),
  message TEXT,
  internal_audit_note VARCHAR(255)
);
INSERT INTO support_tickets (author, department, message, internal_audit_note) VALUES
('dr_amrani', 'Cardiology', 'Workstation VPN client fails to connect after kernel update.', 'Normal priority ticket'),
('admin_sec', 'SOC Audit', 'Verify persistent output encoding on ticket feed.', 'FLAG{St0r3d_Sink_Aud1t_02}'),
('n_bouzid', 'Pharmacy', 'Barcode scanner latency in inventory module.', 'Hardware replacement scheduled');
