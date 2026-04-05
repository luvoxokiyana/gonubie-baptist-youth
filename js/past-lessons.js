      let lessons = [];

        // Load lessons from localStorage
        function loadLessons() {
            const stored = localStorage.getItem('gby_lessons');
            if (stored) {
                lessons = JSON.parse(stored);
            } else {
                // Sample lessons to demonstrate
                lessons = [
                    {
                        id: 1,
                        title: "Purpose Over Pressure",
                        date: "2026-04-11",
                        description: "Finding your identity in Christ when life feels overwhelming. Key verses: Jeremiah 29:11, Romans 8:28",
                        pdfData: null,
                        pdfName: "purpose-over-pressure.pdf"
                    },
                    {
                        id: 2,
                        title: "Friendship & Faith",
                        date: "2026-04-04",
                        description: "How to build relationships that honor God and help you grow spiritually. Proverbs 13:20, 1 Corinthians 15:33",
                        pdfData: null,
                        pdfName: "friendship-and-faith.pdf"
                    }
                ];
                saveLessons();
            }
            renderLessons();
        }

        function saveLessons() {
            localStorage.setItem('gby_lessons', JSON.stringify(lessons));
        }

        function renderLessons() {
            const grid = document.getElementById('lessonsGrid');
            if (!grid) return;

            if (lessons.length === 0) {
                grid.innerHTML = `
                    <div class="empty-lessons">
                        <i class="fa-regular fa-folder-open"></i>
                        <p>No lessons uploaded yet. Check back soon!</p>
                    </div>
                `;
                return;
            }

            grid.innerHTML = lessons.map(lesson => `
                <div class="lesson-card">
                    <div class="lesson-icon">
                        <i class="fa-solid fa-file-pdf"></i>
                    </div>
                    <div class="lesson-content">
                        <div class="lesson-title">${escapeHtml(lesson.title)}</div>
                        <div class="lesson-date"><i class="fa-regular fa-calendar"></i> ${formatDate(lesson.date)}</div>
                        <div class="lesson-description">${escapeHtml(lesson.description)}</div>
                        <div class="lesson-buttons">
                            <button class="view-btn" onclick="viewLesson(${lesson.id})"><i class="fa-regular fa-eye"></i> View Slideshow</button>
                            <button class="view-btn delete-lesson" onclick="deleteLesson(${lesson.id})"><i class="fa-regular fa-trash-alt"></i> Delete</button>
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
            return str.replace(/[&<>]/g, function (m) {
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
            currentPdfData = lesson.pdfData;
            currentPdfName = lesson.pdfName;

            if (lesson.pdfData) {
                // Show the PDF using embed
                pdfContainer.innerHTML = `
                    <iframe src="${lesson.pdfData}" type="application/pdf"></iframe>
                `;
            } else {
                // No PDF uploaded yet - show placeholder
                pdfContainer.innerHTML = `
                    <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; background: #161b22; border-radius: 12px; text-align: center; padding: 2rem;">
                        <i class="fa-solid fa-file-pdf" style="font-size: 4rem; color: #1a5c3a; margin-bottom: 1rem;"></i>
                        <p style="color: #8b949e;">PDF slideshow not yet uploaded for this lesson.</p>
                        <p style="color: #6e7a76; font-size: 0.8rem; margin-top: 0.5rem;">The pastor will upload the slideshow soon!</p>
                    </div>
                `;
            }

            // Setup download button
            if (lesson.pdfData) {
                downloadBtn.onclick = () => {
                    const link = document.createElement('a');
                    link.href = lesson.pdfData;
                    link.download = lesson.pdfName || `${lesson.title}.pdf`;
                    link.click();
                };
                downloadBtn.style.display = 'inline-flex';
            } else {
                downloadBtn.style.display = 'none';
            }

            modal.style.display = 'block';
        }

        function deleteLesson(id) {
            if (confirm('Are you sure you want to delete this lesson?')) {
                lessons = lessons.filter(l => l.id !== id);
                saveLessons();
                renderLessons();
            }
        }

        // Upload lesson (Admin only - password protected)
        function showUploadSection() {
            const password = prompt('Enter admin password to upload lessons:');
            if (password === 'youth2026') {
                document.getElementById('uploadSection').style.display = 'block';
                // Scroll to upload section
                document.getElementById('uploadSection').scrollIntoView({ behavior: 'smooth' });
            } else if (password !== null) {
                alert('Incorrect password');
            }
        }

        function uploadLesson() {
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

            const file = pdfInput.files[0];
            if (file.type !== 'application/pdf') {
                alert('Please upload a PDF file');
                return;
            }

            const reader = new FileReader();
            reader.onload = function (e) {
                const newLesson = {
                    id: Date.now(),
                    title: title,
                    date: date,
                    description: description,
                    pdfData: e.target.result,
                    pdfName: file.name
                };
                lessons.unshift(newLesson);
                saveLessons();
                renderLessons();

                // Clear form
                document.getElementById('lessonTitle').value = '';
                document.getElementById('lessonDate').value = '';
                document.getElementById('lessonDesc').value = '';
                document.getElementById('lessonPDF').value = '';

                alert('Lesson uploaded successfully!');

                // Optionally hide upload section
                // document.getElementById('uploadSection').style.display = 'none';
            };
            reader.readAsDataURL(file);
        }

        // Triple-click user icon to show admin upload
        let clickCount = 0;
        const userIcon = document.querySelector('.right-container i');
        if (userIcon) {
            userIcon.addEventListener('click', () => {
                clickCount++;
                if (clickCount === 3) {
                    showUploadSection();
                    clickCount = 0;
                }
                setTimeout(() => { clickCount = 0; }, 1000);
            });
        }

        // Modal close
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

        const uploadBtn = document.getElementById('uploadLessonBtn');
        if (uploadBtn) {
            uploadBtn.addEventListener('click', uploadLesson);
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            loadLessons();
        });