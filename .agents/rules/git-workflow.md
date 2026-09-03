# Automated Git Workflow Rules

Setiap kali pengguna meminta untuk membuat fitur baru, memperbaiki bug, atau melakukan refactoring:

1. **Pre-Task Check (Sebelum Ngoding)**:
   - Periksa status Git (`git status`) untuk memastikan state saat ini.
   - Deteksi apakah perintah pengguna merupakan fitur baru, bugfix, atau refactoring.
   - Buatkan branch khusus yang rapi (misal: `feature/...`, `fix/...`) dengan konfirmasi.
   - Pastikan cabang selalu up-to-date (`git pull`).

2. **Post-Task Check (Setelah Selesai Ngoding)**:
   - Tampilkan ringkasan file yang diubah (`git status -s`).
   - Buatkan draft pesan commit menggunakan standar *Conventional Commits* (`feat: ...`, `fix: ...`).
   - Minta konfirmasi pengguna sebelum mengeksekusi `git add`, `git commit`, dan `git push`.
