# Project Rules

## Dokumentasi Wajib
Setiap kali ada perubahan apa pun di dalam proyek, termasuk:
- penambahan fitur
- perbaikan bug
- perubahan desain atau arsitektur
- penghapusan file atau fitur
- penyesuaian konfigurasi

maka wajib melakukan dokumentasi ke dalam file markdown di dalam proyek.

## Cara Penerapan
1. Perubahan terbaru harus dicatat di `PROJECT_BLUEPRINT.md` atau `PROJECT_RULES.md`.
2. Tambahkan ringkasan aksi, alasan, dan daftar file yang diubah.
3. Jika dilakukan pembaruan besar, tambahkan poin baru di `PROJECT_BLUEPRINT.md` dan perbarui bagian implementasi / risiko / arsitektur.
4. Jika perubahan bersifat minor, tambahkan catatan singkat di bawah bagian "Strategi Peningkatan" atau buat sub-bagian baru.

## Ketentuan untuk AI dan Analisis Otomatis
- AI harus membaca `PROJECT_RULES.md` dan `PROJECT_BLUEPRINT.md` sebelum melakukan analisis.
- AI wajib mematuhi aturan bahwa setiap aksi harus memiliki dokumentasi yang tercatat.
- Jika analisis menemukan perubahan yang tidak terdokumentasi, AI harus menandai ini sebagai pelanggaran aturan dokumentasi.
- AI dapat menggunakan `PROJECT_RULES.md` sebagai sumber kebenaran untuk kebijakan dokumentasi.

## Format Dokumentasi
- Gunakan markdown
- Gunakan judul dan daftar agar mudah dibaca
- Sertakan tanggal dan ringkasan perubahan jika perlu
- Sertakan referensi file yang diubah

## Tujuan
Menjaga agar semua perubahan proyek terdokumentasi secara konsisten, mudah dilacak, dan bisa dipahami oleh manusia maupun sistem AI.