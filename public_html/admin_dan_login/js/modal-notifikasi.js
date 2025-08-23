// modal-notifikasi.js
// Modal and Notification System
class ModalNotifikasi {
    constructor() {
        // Cek apakah instance sudah ada
        if (window.modalNotifikasiInstance) {
            return window.modalNotifikasiInstance;
        }
        
        this.notificationQueue = [];
        this.isShowingNotification = false;
        this.init();
        this.setupUrlParamHandling();
        
        // Simpan instance
        window.modalNotifikasiInstance = this;
    }

    init() {
        // Buat container notifikasi jika belum ada
        // Pasang event listeners
        this.attachDeleteListeners();
        this.attachFileUploadListeners();
        this.attachRestoreListeners();
    }

    setupUrlParamHandling() {
        const urlParams = new URLSearchParams(window.location.search);
        
        if (urlParams.has('sukses')) {
            const action = urlParams.get('action') || 'operasi';
            const judul = urlParams.get('judul') || 'Dokumen';
            
            let message = `Dokumen "${judul}" berhasil ${action}`;
            if (action === 'pulihkan') {
                message = `Dokumen "${judul}" berhasil dipulihkan`;
            }
            
            // Tampilkan notifikasi
            this.showSuccess(message, 'Berhasil');
            
            // Hapus parameter URL
            this.removeNotificationParamsFromUrl();
        }
        
        if (urlParams.has('error')) {
            this.showError('Terjadi kesalahan saat melakukan operasi.', 'Error');
            
            // Hapus parameter URL
            this.removeNotificationParamsFromUrl();
        }
    }

    removeNotificationParamsFromUrl() {
        const url = new URL(window.location);
        const hasSukses = url.searchParams.has('sukses');
        const hasError = url.searchParams.has('error');
        
        if (hasSukses || hasError) {
            // Hapus parameter
            url.searchParams.delete('sukses');
            url.searchParams.delete('error');
            url.searchParams.delete('action');
            url.searchParams.delete('judul');
            
            // Update URL tanpa reload
            window.history.replaceState({}, '', url);
        }
    }

    // Modal functions
    showModal(options) {
        const { title, message, type = 'confirm', onConfirm, onCancel } = options;

        const overlay = document.createElement('div');
        overlay.className = 'modal-overlay';
        overlay.innerHTML = `
            <div class="modal-container">
                <div class="modal-header">
                    <h3 class="modal-title">${title}</h3>
                    <button class="modal-close">&times;</button>
                </div>
                <div class="modal-body">
                    <p class="modal-message">${message}</p>
                </div>
                <div class="modal-footer">
                    ${type === 'confirm' ? 
                        `<button class="modal-btn modal-btn-cancel">Batal</button>
                         <button class="modal-btn modal-btn-confirm">Ya, Lanjutkan</button>` :
                        `<button class="modal-btn modal-btn-success">Mengerti</button>`
                    }
                </div>
            </div>
        `;

        document.body.appendChild(overlay);

        // Tampilkan modal dengan animasi
        setTimeout(() => overlay.classList.add('active'), 10);

        // Event listeners
        const closeModal = () => {
            overlay.classList.remove('active');
            setTimeout(() => overlay.remove(), 300);
        };

        overlay.querySelector('.modal-close').addEventListener('click', closeModal);
        overlay.querySelector('.modal-btn-cancel')?.addEventListener('click', () => {
            closeModal();
            onCancel?.();
        });

        overlay.querySelector('.modal-btn-confirm')?.addEventListener('click', () => {
            closeModal();
            onConfirm?.();
        });

        overlay.querySelector('.modal-btn-success')?.addEventListener('click', closeModal);

        // Close ketika klik di luar modal
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) closeModal();
        });
    }

    // Notification functions
    // Notification functions
    showNotification(options) {
        if (this.isShowingNotification) {
            this.notificationQueue.push(options);
            return;
        }
        
        this.isShowingNotification = true;
        
        const { title, message, type = 'info', duration = 5000, icon = true } = options;

        // Buat overlay
        const overlay = document.createElement('div');
        overlay.className = 'notification-overlay';
        
        // Buat notifikasi
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        
        let iconHtml = '';
        if (icon) {
            switch (type) {
                case 'success':
                    iconHtml = '<div class="checkmark-animation"><svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52"><circle class="checkmark-circle" cx="26" cy="26" r="25" fill="none"/><path class="checkmark-check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/></svg></div>';
                    break;
                case 'error':
                    iconHtml = '<div class="delete-animation"><svg class="delete-cross" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52"><circle class="delete-circle" cx="26" cy="26" r="25" fill="none"/><path class="delete-cross" fill="none" d="M16 16 36 36 M36 16 16 36"/></svg></div>';
                    break;
                case 'warning':
                    iconHtml = '<div class="notification-icon">⚠️</div>';
                    break;
                default:
                    iconHtml = '<div class="notification-icon">ℹ️</div>';
            }
        }

        notification.innerHTML = `
            ${iconHtml}
            <div class="notification-content">
                <div class="notification-title">${title}</div>
                <p class="notification-message">${message}</p>
            </div>
        `;

        overlay.appendChild(notification);
        document.body.appendChild(overlay); // LANGSUNG KE BODY, BUKAN CONTAINER

        // Tampilkan dengan animasi
        setTimeout(() => {
            overlay.classList.add('active');
        }, 10);

        // Auto remove
        const autoRemove = setTimeout(() => {
            this.closeNotification(overlay);
        }, duration);

        // SIMPAN REFERENCE
        overlay.autoRemoveTimeout = autoRemove;

        // EVENT LISTENER SEDERHANA
        overlay.onclick = (e) => {
            if (e.target === overlay) {
                clearTimeout(overlay.autoRemoveTimeout);
                this.closeNotification(overlay);
            }
        };

        // Prevent notification click from closing
        notification.onclick = (e) => {
            e.stopPropagation();
        };
    }

    // Method untuk menutup notifikasi
    closeNotification(overlay) {
        overlay.classList.remove('active');
        
        setTimeout(() => {
            if (overlay && overlay.parentNode) {
                overlay.parentNode.removeChild(overlay);
            }
            this.isShowingNotification = false;
            this.processNextNotification();
        }, 300);
    }
    
    processNextNotification() {
        if (this.notificationQueue.length > 0) {
            const nextNotification = this.notificationQueue.shift();
            setTimeout(() => {
                this.showNotification(nextNotification);
            }, 300);
        }
    }

    // Event listeners
    attachDeleteListeners() {
        document.addEventListener('click', (e) => {
            const deleteBtn = e.target.closest('.btn-delete');
            if (deleteBtn) {
                e.preventDefault();
                const href = deleteBtn.href;
                const row = deleteBtn.closest('tr');
                const judul = row.querySelector('.long-title')?.textContent || 
                            row.querySelector('td:nth-child(3)')?.textContent || 
                            'peraturan ini';
                
                this.showModal({
                    title: 'Konfirmasi Hapus',
                    message: `Apakah Anda yakin ingin menghapus "${judul}"? Tindakan ini tidak dapat dibatalkan.`,
                    type: 'confirm',
                    onConfirm: () => {
                        window.location.href = href;
                    },
                    onCancel: () => {
                        console.log('Hapus dibatalkan');
                    }
                });
            }
        });
    }

    attachFileUploadListeners() {
        // File upload events
        const fileInputs = document.querySelectorAll('input[type="file"]');
        fileInputs.forEach(input => {
            input.addEventListener('change', (e) => {
                if (e.target.files.length > 0) {
                    const fileName = e.target.files[0].name;
                    this.showNotification({
                        title: 'File Dipilih',
                        message: `File "${fileName}" siap untuk diupload.`,
                        type: 'info',
                        duration: 3000
                    });
                }
            });
        });

        // Form submission events
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', (e) => {
                const formType = form.getAttribute('action');
                if (formType.includes('proses_upload')) {
                    this.showNotification({
                        title: 'Mengupload Dokumen',
                        message: 'Sedang mengupload dokumen, harap tunggu...',
                        type: 'info',
                        duration: 2000
                    });
                } else if (formType.includes('proses_edit')) {
                    this.showNotification({
                        title: 'Memperbarui Dokumen',
                        message: 'Sedang memperbarui dokumen, harap tunggu...',
                        type: 'info',
                        duration: 2000
                    });
                }
            });
        });
    }

    attachRestoreListeners() {
        document.addEventListener('click', (e) => {
            const restoreBtn = e.target.closest('.btn-pulihkan');
            if (restoreBtn) {
                e.preventDefault();
                const href = restoreBtn.href;
                const row = restoreBtn.closest('tr');
                const judul = row.querySelector('td:nth-child(2)')?.textContent || 'peraturan ini';
                
                this.showModal({
                    title: 'Konfirmasi Pemulihan',
                    message: `Apakah Anda yakin ingin memulihkan "${judul}"?`,
                    type: 'confirm',
                    onConfirm: () => {
                        window.location.href = href;
                    }
                });
            }
        });
    }

    // Utility functions
    showSuccess(message, title = 'Berhasil') {
        this.showNotification({ title, message, type: 'success' });
    }

    showError(message, title = 'Error') {
        this.showNotification({ title, message, type: 'error' });
    }

    showWarning(message, title = 'Peringatan') {
        this.showNotification({ title, message, type: 'warning' });
    }

    showInfo(message, title = 'Informasi') {
        this.showNotification({ title, message, type: 'info' });
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    if (!window.modalNotifikasi) {
        window.modalNotifikasi = new ModalNotifikasi();
    }
});

// Global functions
function showConfirmationModal(options) {
    if (window.modalNotifikasi) {
        window.modalNotifikasi.showModal(options);
    }
}

function showNotification(options) {
    if (window.modalNotifikasi) {
        window.modalNotifikasi.showNotification(options);
    }
}