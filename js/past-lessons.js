// past-lessons.js - Complete version with modal PDF viewer

let lessons = [];

// Load lessons from server
async function loadLessons() {
    try {
        const response = await fetch('get-lessons.php');
        const result = await response.json();
        
        if (result.success) {
            lessons = result.lessons;
            renderLessons();
        } else {
            console.error('Failed to load lessons:', result.error);
            renderEmptyState();
        }
    } catch (error) {
        console.error('Error loading lessons:', error);
        renderEmptyState();
    }
}

function renderEmptyState() {
    const grid = document.getElementById('lessonsGrid');
    if (!grid) return;
    
    grid.innerHTML = `
        <div class="empty-lessons">
            <i class="fa-regular fa-folder-open"></i>
            <p>No lessons uploaded yet. Check back soon!</p>
        </div>
    `;
}

function renderLessons() {
    const grid = document.getElementById('lessonsGrid');
    if (!grid) return;

    if (lessons.length === 0) {
        renderEmptyState();
        return;
    }

    grid.innerHTML = lessons.map(lesson => `
        <div class="lesson-card">
            <div class="lesson-icon">
                <i class="fa-solid fa-file-pdf"></i>
            </div>
            <div class="lesson-content">
                <div class="lesson-title">${escapeHtml(lesson.title)}</div>
                <div class="lesson-date"><i class="fa-regular fa-calendar"></i> ${formatDate(lesson.lesson_date)}</div>
                <div class="lesson-description">${escapeHtml(lesson.description)}</div>
                <div class="lesson-buttons">
                    <button class="view-btn" onclick="viewLesson(${lesson.id})"><i class="fa-regular fa-eye"></i> View Slideshow</button>
                </div>
            </div>
        </div>
    `).join('');
}

function formatDate(dateString) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('en-US', options);
}

function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/[&<>]/g, function(m) {
        if (m === '&') return '&amp;';
        if (m === '<') return '&lt;';
        if (m === '>') return '&gt;';
        return m;
    });
}

let currentPdfData = null;
let currentPdfName = null;

function viewLesson(id) {
    const lesson = lessons.find(l => l.id === id);
    if (!lesson) return;

    const modal = document.getElementById('pdfModal');
    const modalTitle = document.getElementById('modalTitle');
    const pdfContainer = document.getElementById('pdfContainer');
    const downloadBtn = document.getElementById('downloadPdfBtn');

    modalTitle.textContent = lesson.title;
    currentPdfData = lesson.pdf_data;
    currentPdfName = lesson.pdf_filename;

    if (lesson.pdf_data) {
        // Convert binary data to blob URL
        const binaryData = atob(lesson.pdf_data);
        const byteArray = new Uint8Array(binaryData.length);
        for (let i = 0; i < binaryData.length; i++) {
            byteArray[i] = binaryData.charCodeAt(i);
        }
        const blob = new Blob([byteArray], { type: 'application/pdf' });
        const blobUrl = URL.createObjectURL(blob);
        
        pdfContainer.innerHTML = `
            <iframe src="${blobUrl}" type="application/pdf"></iframe>
        `;
    } else {
        pdfContainer.innerHTML = `
            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; background: #161b22; border-radius: 12px; text-align: center; padding: 2rem;">
                <i class="fa-solid fa-file-pdf" style="font-size: 4rem; color: #1a5c3a; margin-bottom: 1rem;"></i>
                <p style="color: #8b949e;">PDF slideshow not yet uploaded for this lesson.</p>
            </div>
        `;
    }

    if (lesson.pdf_data) {
        downloadBtn.onclick = () => {
            const link = document.createElement('a');
            const blobUrl = pdfContainer.querySelector('iframe')?.src;
            if (blobUrl) {
                link.href = blobUrl;
                link.download = currentPdfName || `${lesson.title}.pdf`;
                link.click();
            }
        };
        downloadBtn.style.display = 'inline-flex';
    } else {
        downloadBtn.style.display = 'none';
    }

    modal.style.display = 'block';
}

// Upload lesson
async function uploadLesson() {
    const title = document.getElementById('lessonTitle').value;
    const date = document.getElementById('lessonDate').value;
    const description = document.getElementById('lessonDesc').value;
    const pdfInput = document.getElementById('lessonPDF');

    if (!title || !date || !description) {
        alert('Please fill in all fields');
        return;
    }

    if (pdfInput.files.length === 0) {
        alert('Please select a PDF file');
        return;
    }

    const formData = new FormData();
    formData.append('title', title);
    formData.append('date', date);
    formData.append('description', description);
    formData.append('pdf', pdfInput.files[0]);

    try {
        const response = await fetch('upload-lesson.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('Lesson uploaded successfully!');
            document.getElementById('lessonTitle').value = '';
            document.getElementById('lessonDate').value = '';
            document.getElementById('lessonDesc').value = '';
            document.getElementById('lessonPDF').value = '';
            await loadLessons();
        } else {
            alert('Error: ' + result.error);
        }
    } catch (error) {
        console.error('Error uploading lesson:', error);
        alert('An error occurred. Please try again.');
    }
}

// Modal close handlers
const closeBtn = document.querySelector('.close-modal');
if (closeBtn) {
    closeBtn.addEventListener('click', () => {
        document.getElementById('pdfModal').style.display = 'none';
        document.getElementById('pdfContainer').innerHTML = '';
    });
}

window.addEventListener('click', (e) => {
    const modal = document.getElementById('pdfModal');
    if (e.target === modal) {
        modal.style.display = 'none';
        document.getElementById('pdfContainer').innerHTML = '';
    }
});

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    // Only attach upload button event if user is leader
    if (typeof IS_LEADER !== 'undefined' && IS_LEADER) {
        const uploadBtn = document.getElementById('uploadLessonBtn');
        if (uploadBtn) {
            uploadBtn.addEventListener('click', uploadLesson);
        }
    }
    
    loadLessons();
});