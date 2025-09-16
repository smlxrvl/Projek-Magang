# AGENT.md

## Project Structure
- PHP-based attendance system (presensi-web) using mysqli
- Two main roles: admin and peserta (participant)
- Frontend uses Tabler CSS framework + SweetAlert2 for alerts
- Authentication via session management

## Database (presensi_magang)
- **peserta**: kode_magang, nim, nama, jenis_kelamin, alamat, no_handphone, universitas, lokasi_presensi, foto
- **users**: id_magang, username, password, status, role, divisi
- **presensi**: id_peserta, tanggal_datang, jam_datang, tanggal_pulang, jam_pulang, latitude, longitude
- **lokasi_presensi**: nama_lokasi, latitude, longitude, radius, jam_masuk, jam_pulang
- **divisi**: divisi name for organizational units

## File Structure
- `/admin/` - Admin dashboard and management
- `/peserta/` - Participant dashboard and attendance
- `/auth/` - Login/logout functionality
- `/assets/` - Static assets (CSS, JS, images)

## Code Conventions
- PHP short tags <?= for output
- Snake_case for variables and database fields
- Session-based authentication ($_SESSION)
- Form validation with error message arrays
- XSS protection using htmlspecialchars()
- Password hashing with PASSWORD_DEFAULT
- File uploads to assets/img/foto_peserta/

## No build/test commands - pure PHP project served via XAMPP
