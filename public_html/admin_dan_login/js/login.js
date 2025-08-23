
    // 1. Pilih elemen yang kita butuhkan dari halaman HTML
    // 'passwordInput' adalah kolom input untuk password
    const passwordInput = document.querySelector('input[name="password"]');
    // 'toggleIcon' adalah ikon mata yang akan kita klik
    const toggleIcon = document.querySelector('.password-toggle');

    // 2. Tambahkan 'pendengar acara' (event listener) untuk 'klik' pada ikon mata
    toggleIcon.addEventListener('click', function () {
        // 'this' merujuk pada elemen yang diklik, yaitu ikon mata itu sendiri

        // 3. Ubah tipe input password
        // Ini adalah shortcut if/else (ternary operator):
        // JIKA tipe input sekarang adalah 'password', MAKA ubah jadi 'text'.
        // JIKA BUKAN, MAKA ubah kembali jadi 'password'.
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        
        // 4. Ganti ikonnya agar sesuai
        // 'classList.toggle' akan menambah class jika belum ada, dan menghapus jika sudah ada.
        // Ini membuat ikon mata biasa dan mata coret bisa bergantian dengan mudah.
        this.classList.toggle('fa-eye');
        this.classList.toggle('fa-eye-slash');
    });


    