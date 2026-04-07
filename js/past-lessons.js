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
    // Just open the view-lesson.php which will serve the PDF
    window.open(`view-lesson.php?id=${id}`, '_blank');
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

    // Read PDF as base64
    const reader = new FileReader();
    reader.onload = async function(e) {
        const pdfBase64 = e.target.result.split(',')[1]; // Remove data:application/pdf;base64,
        
        const data = {
            title: title,
            date: date,
            description: description,
            pdf_filename: pdfInput.files[0].name,
            pdf_data: pdfBase64
        };

        try {
            const response = await fetch('upload-lesson.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
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
    };
    reader.readAsDataURL(pdfInput.files[0]);
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

// ============================================
// 4. SOCIAL ICONS (Footer)
// ============================================
function setupSocialIcons() {
    const socialLinks = {
        'fa-instagram': 'https://www.instagram.com/gonubiebaptistyouth/',
        'fa-whatsapp': '', //
        'fa-envelope': ''
    };
    
    const socialIcons = document.querySelectorAll('.social-icons i');
    socialIcons.forEach(icon => {
        icon.addEventListener('click', () => {
            for (const [className, url] of Object.entries(socialLinks)) {
                if (icon.classList.contains(className)) {
                    window.open(url, '_blank');
                    return;
                }
            }
        });
    });
}

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