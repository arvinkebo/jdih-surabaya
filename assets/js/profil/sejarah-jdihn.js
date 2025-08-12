document.addEventListener('DOMContentLoaded', function() {
    const timelineData = [
        // TIMELINE PERTAMA : 1974
        {
        year: "1974",
        title: "Embrio JDIHN",
        icon: "fas fa-seedling", // Icon baru
        content: `
            <p>Gagasan JDIHN berawal dari <strong>Seminar Hukum Nasional III</strong> di Surabaya yang diselenggarakan oleh BPHN.</p>
            <div class="highlight-box">
                <p><strong>Masalah utama:</strong> Sistem dokumentasi hukum nasional belum mampu menyediakan informasi hukum secara cepat, tepat, dan mudah diakses.</p>
                <p><strong>Rekomendasi:</strong> Dibutuhkan sistem jaringan dokumentasi hukum terpadu untuk:</p>
                <ul>
                    <li>Penelitian hukum</li>
                    <li>Penyusunan peraturan perundang-undangan</li>
                    <li>Pembentukan kebijakan</li>
                </ul>
            </div>
        `
        },
        // TIMELINE KEDUA : 1975-1978
        {
        year: "1975-1978",
        title: "Persiapan Infrastruktur",
        icon: "fas fa-cogs",
        content: `
            <p>BPHN menyelenggarakan serangkaian lokakarya penting:</p>
            <ul class="timeline-events">
                <li><span class="year">1975:</span> Jaringan Dokumentasi & Informasi Hukum (Jakarta)</li>
                <li><span class="year">1977:</span> Sistem Penemuan Kembali Peraturan (Malang)</li>
                <li><span class="year">1977:</span> Penyebarluasan Peraturan (Pontianak)</li>
            </ul>
            <div class="highlight-box">
                <p><strong>Kesepakatan:</strong> BPHN ditetapkan sebagai Pusat Jaringan dengan tugas utama:</p>
                <ul>
                    <li>Pelatihan tenaga ahli</li>
                    <li>Koordinasi unit-unit jaringan</li>
                </ul>
            </div>
        `
        },
        // TIMELINE KETIGA : 1988
        {
        year: "1988",
        title: "Pedoman Operasional",
        icon: "fas fa-book",
        content: `
            <p>BPHN menerbitkan <strong>Manual Unit JDIH</strong> sebagai panduan teknis pertama yang memuat:</p>
            <ul>
                <li>Standar pengelolaan dokumen hukum</li>
                <li>Prosedur pendokumentasian</li>
                <li>Mekanisme pelayanan informasi</li>
            </ul>
            <p>Manual ini menjadi acuan operasional bagi seluruh unit jaringan dalam melaksanakan tugas dokumentasi hukum.</p>
        `
        },
        // TIMELINE KEEMPAT : 1978-1999
        {
        year: "1978-1999",
        title: "Tantangan Era Orde Baru",
        icon: "fas fa-exclamation-triangle",
        content: `
            <p>Selama dua dekade, JDIH beroperasi tanpa payung hukum formal, hanya mengandalkan kesepakatan lokakarya 1978.</p>
            <div class="highlight-box">
                <p><strong>Kendala Utama:</strong></p>
                <ul>
                    <li>Koordinasi antar instansi yang lemah</li>
                    <li>Sistem temu kembali informasi masih manual</li>
                    <li>Standar dokumen yang tidak seragam</li>
                </ul>
            </div>
            <p>Meskipun <strong>GBHN 1993</strong> menyebut JDIH sebagai sarana penunjang pembangunan hukum, implementasinya tidak diiringi kebijakan konkret.</p>
        `
        },
        // TIMELINE KELIMA : 1999-2013
        {
        year: "1999-2013",
        title: "Payung Hukum Formal",
        icon: "fas fa-gavel",
        content: `
            <p>Era Reformasi membawa perubahan mendasar melalui:</p>
                <ul class="timeline-events">
                    <li><span class="year">1999:</span> <strong>Keppres No. 91/1999</strong> - Dasar hukum pertama pembentukan JDIHN</li>
                    <li><span class="year">2011:</span> <strong>Inpres No. 9/2011</strong> - Revitalisasi untuk mendukung pemberantasan korupsi</li>
                    <li><span class="year">2012:</span> <strong>Perpres No. 33/2012</strong> - Penyempurnaan struktur organisasi</li>
                    <li><span class="year">2013:</span> <strong>Permenkumham No. 2/2013</strong> - Standarisasi teknis pengelolaan</li>
                </ul>
            <div class="highlight-box">
                <p><strong>Perkembangan regulasi ini mentransformasi JDIHN menjadi jaringan dokumentasi hukum modern yang terintegrasi.</strong></p>
            </div>
        `
        },
    ];
    // Data referensi baru
    const referencesList = [
        "Keputusan Presiden Republik Indonesia Nomor 91 Tahun 1999 tentang Jaringan Dokumentasi dan Informasi Hukum Nasional",
        "Peraturan Presiden Republik Indonesia Nomor 33 Tahun 2012 tentang Jaringan Dokumentasi dan Informasi Hukum Nasional",
        "Peraturan Menteri Hukum dan Hak Asasi Manusia Republik Indonesia Nomor 2 Tahun 2013 tentang Standardisasi Pengelolaan Teknis Dokumentasi dan Informasi Hukum",
        "Instruksi Presiden Republik Indonesia Nomor 9 Tahun 2011 tentang Rencana Aksi Pencegahan dan Pemberantasan Korupsi Tahun 2011",
        "Manual Unit Jaringan Dokumentasi dan Informasi Hukum, BPHN, 1988",
        "Arsip Badan Pembinaan Hukum Nasional (BPHN)"
    ];

    const markers = document.querySelectorAll('.timeline-year-marker');
    const yearBadge = document.querySelector('.year-badge');
    const titleElement = document.querySelector('.title');
    const descElement = document.querySelector('.desc');
    // Tambahkan kode untuk switch view
    const tabButtons = document.querySelectorAll('.tab-button');
    const timelineView = document.querySelector('.timeline-view');
    const textView = document.querySelector('.text-view');
    const referencesView = document.querySelector('.references-view'); // Elemen baru

    let currentIndex = 0; // Default mulai dari 1974

    // Fungsi switch tab yang diperbarui
    function switchTab(view) {
        // Update active tab
        tabButtons.forEach(btn => btn.classList.remove('active'));
        document.querySelector(`.tab-button[data-view="${view}"]`).classList.add('active');
        
        // Update view
        timelineView.classList.remove('active');
        textView.classList.remove('active');
        referencesView.classList.remove('active');
        
        document.querySelector(`.${view}-view`).classList.add('active');
        
        // Render konten jika diperlukan
        if(view === 'text') {
            textView.innerHTML = generateTextViewContent();
        } 
        else if(view === 'references') {
            renderReferences();
        }
    }

    // Event listener untuk tab
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            switchTab(this.getAttribute('data-view'));
        });
    });

    // Fungsi untuk generate konten text view
    // function generateTextViewContent() {
    //     let html = '<div class="text-content">';
        
    //     timelineData.forEach(item => {
    //         html += `
    //             <div class="text-item">
    //                 <div class="year-badge">${item.year}</div>
    //                 <h3><i class="fas fa-file-contract"></i> ${item.title}</h3>
    //                 <div class="desc">${item.desc}</div>
    //             </div>
    //         `;
    //     });
        
    //     html += '</div>';
    //     return html;
    // }

    // Fungsi untuk generate konten text view (versi ringkasan)
function generateTextViewContent() {
    let html = '<div class="text-content">';
    
    // Poin 1: Embrio JDIHN (1974)
    html += `
        <div class="text-item">
            <div class="year-badge">1974</div>
            <h3><i class="fas fa-seedling"></i> Embrio JDIHN</h3>
            <div class="desc">
                <p>Gagasan JDIHN berawal dari <strong>Seminar Hukum Nasional III</strong> di Surabaya yang diselenggarakan oleh BPHN.</p>
                <div class="highlight-box">
                    <p><strong>Masalah utama:</strong> Sistem dokumentasi hukum nasional belum mampu menyediakan informasi hukum secara cepat, tepat, dan mudah diakses.</p>
                    <p><strong>Rekomendasi:</strong> Dibutuhkan sistem jaringan dokumentasi hukum terpadu untuk:</p>
                    <ul>
                        <li>Penelitian hukum</li>
                        <li>Penyusunan peraturan perundang-undangan</li>
                        <li>Pembentukan kebijakan</li>
                    </ul>
                </div>
            </div>
        </div>
    `;

    // Poin 2: Persiapan Infrastruktur (1975-1978)
    html += `
        <div class="text-item">
            <div class="year-badge">1975-1978</div>
            <h3><i class="fas fa-cogs"></i> Persiapan Infrastruktur</h3>
            <div class="desc">
                <p>BPHN menyelenggarakan serangkaian lokakarya penting:</p>
                <ul class="timeline-events">
                    <li><span class="year">1975:</span> Jaringan Dokumentasi & Informasi Hukum (Jakarta)</li>
                    <li><span class="year">1977:</span> Sistem Penemuan Kembali Peraturan (Malang)</li>
                    <li><span class="year">1977:</span> Penyebarluasan Peraturan (Pontianak)</li>
                    <li><span class="year">1978:</span> Organisasi & Komunikasi Sistem JDIH (Jakarta)</li>
                </ul>
                <div class="highlight-box">
                    <p><strong>Kesepakatan:</strong> BPHN ditetapkan sebagai Pusat Jaringan dengan tugas utama:</p>
                    <ul>
                        <li>Pelatihan tenaga ahli</li>
                        <li>Koordinasi unit-unit jaringan</li>
                        <li>Pengembangan sistem berkelanjutan</li>
                    </ul>
                </div>
            </div>
        </div>
    `;

    // Poin 3: Pedoman Operasional (1988)
    html += `
        <div class="text-item">
            <div class="year-badge">1988</div>
            <h3><i class="fas fa-book"></i> Pedoman Operasional</h3>
            <div class="desc">
                <p>BPHN menerbitkan <strong>Manual Unit JDIH</strong> sebagai panduan teknis pertama yang memuat:</p>
                <ul>
                    <li>Standar pengelolaan dokumen hukum</li>
                    <li>Prosedur pendokumentasian</li>
                    <li>Mekanisme pelayanan informasi</li>
                </ul>
                <p>Manual ini menjadi acuan operasional bagi seluruh unit jaringan dalam melaksanakan tugas dokumentasi hukum.</p>
            </div>
        </div>
    `;

    // Poin 4: Tantangan Era Orde Baru (1978-1999)
    html += `
        <div class="text-item">
            <div class="year-badge">1978-1999</div>
            <h3><i class="fas fa-exclamation-triangle"></i> Tantangan Era Orde Baru</h3>
            <div class="desc">
                <p>Selama dua dekade, JDIH beroperasi tanpa payung hukum formal, hanya mengandalkan kesepakatan lokakarya 1978.</p>
                <div class="highlight-box">
                    <p><strong>Kendala Utama:</strong></p>
                    <ul>
                        <li>Koordinasi antar instansi yang lemah</li>
                        <li>Sistem temu kembali informasi masih manual</li>
                        <li>Standar dokumen yang tidak seragam</li>
                    </ul>
                </div>
                <p>Meskipun <strong>GBHN 1993</strong> menyebut JDIH sebagai sarana penunjang pembangunan hukum, implementasinya tidak diiringi kebijakan konkret.</p>
            </div>
        </div>
    `;

    // Poin 5: Payung Hukum Formal (1999-2013)
    html += `
        <div class="text-item">
            <div class="year-badge">1999-2013</div>
            <h3><i class="fas fa-gavel"></i> Payung Hukum Formal</h3>
            <div class="desc">
                <p>Era Reformasi membawa perubahan mendasar melalui:</p>
                <ul class="timeline-events">
                    <li><span class="year">1999:</span> <strong>Keppres No. 91/1999</strong> - Dasar hukum pertama pembentukan JDIHN</li>
                    <li><span class="year">2011:</span> <strong>Inpres No. 9/2011</strong> - Revitalisasi untuk mendukung pemberantasan korupsi</li>
                    <li><span class="year">2012:</span> <strong>Perpres No. 33/2012</strong> - Penyempurnaan struktur organisasi</li>
                    <li><span class="year">2013:</span> <strong>Permenkumham No. 2/2013</strong> - Standarisasi teknis pengelolaan</li>
                </ul>
                <div class="highlight-box">
                <p><strong>Perkembangan regulasi ini mentransformasi JDIHN menjadi jaringan dokumentasi hukum modern yang terintegrasi.</strong></p>
                </div>
            </div>
        </div>
    `;

    html += '</div>';
    return html;
}

    // Animasi ganti konten
    function updateContent(index) {
        const data = timelineData[index];
        
        // Update konten
        yearBadge.textContent = data.year;
        titleElement.innerHTML = `<i class="${data.icon}"></i> ${data.title}`;
        descElement.innerHTML = data.content;
        
        // Update marker aktif
        markers.forEach(marker => marker.classList.remove('active'));
        markers[index].classList.add('active');
        
        // // Update navigasi
        // prevBtn.disabled = index === 0;
        // nextBtn.disabled = index === timelineData.length - 1;
        
        // Tambahkan efek animasi jika diperlukan
        const timelineDetail = document.querySelector('.timeline-detail');
        timelineDetail.style.animation = 'none';
        void timelineDetail.offsetWidth; // Trigger reflow
        timelineDetail.style.animation = 'fadeIn 0.5s ease';
    }

    // Klik marker
    markers.forEach(marker => {
        marker.addEventListener('click', function() {
            currentIndex = parseInt(this.getAttribute('data-index'));
            updateContent(currentIndex);
        });
    });

    // Tombol navigasi
    prevBtn.addEventListener('click', function() {
        if (currentIndex > 0) {
            currentIndex--;
            updateContent(currentIndex);
        }
    });

    nextBtn.addEventListener('click', function() {
        if (currentIndex < timelineData.length - 1) {
            currentIndex++;
            updateContent(currentIndex);
        }
    });

    // Fungsi render referensi
    function renderReferences() {
        const listContainer = document.getElementById('references-list');
        listContainer.innerHTML = referencesList.map(ref => 
            `<li>${ref}</li>`
        ).join('');
    }
    
    // Inisialisasi
    updateContent(currentIndex);

    // Tambahkan animasi CSS
    const style = document.createElement('style');
    style.textContent = `
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .timeline-detail {
            animation: fadeIn 0.5s ease;
        }
    `;
    document.head.appendChild(style);
});