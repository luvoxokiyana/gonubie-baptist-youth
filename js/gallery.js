   let galleryImages = [];
        let isLoading = false;

        // Load images from PHP (server images are fast, only paths not base64)
        async function loadGallery() {
            if (isLoading) return;
            isLoading = true;

            try {
                const response = await fetch('get-images.php');
                const serverImages = await response.json();

                // User uploaded images (only stored as paths if possible, but for demo keep small)
                let uploadedImages = [];
                try {
                    const stored = localStorage.getItem("gby_uploaded");
                    if (stored) {
                        uploadedImages = JSON.parse(stored);
                        // Clean up old base64 images that are too big (keep only last 5)
                        if (uploadedImages.length > 5) {
                            uploadedImages = uploadedImages.slice(0, 5);
                            localStorage.setItem("gby_uploaded", JSON.stringify(uploadedImages));
                        }
                    }
                } catch (e) { uploadedImages = []; }

                galleryImages = [...serverImages, ...uploadedImages];
                renderMasonryGrid();
            } catch (error) {
                console.error('Error:', error);
                const stored = localStorage.getItem("gby_uploaded");
                galleryImages = stored ? JSON.parse(stored) : [];
                renderMasonryGrid();
            }
            isLoading = false;
        }

        function saveUploadedImages() {
            const uploadedOnly = galleryImages.filter(img => img.isUploaded === true);
            // Limit uploaded images to 5 to keep localStorage fast
            const limited = uploadedOnly.slice(0, 5);
            localStorage.setItem("gby_uploaded", JSON.stringify(limited));
        }

        // Optimized render with requestAnimationFrame
        let renderQueued = false;
        function renderMasonryGrid() {
            if (renderQueued) return;
            renderQueued = true;

            requestAnimationFrame(() => {
                const grid = document.getElementById("masonryGrid");
                if (!grid) return;

                if (galleryImages.length === 0) {
                    grid.innerHTML = `<div class="empty-gallery"><i class="fa-regular fa-images"></i><p>No photos yet. Add images to /images/gallery/ folder!</p></div>`;
                    renderQueued = false;
                    return;
                }

                const columnCount = window.innerWidth < 640 ? 1 : window.innerWidth < 900 ? 2 : window.innerWidth < 1200 ? 3 : 4;
                let columns = Array(columnCount).fill().map(() => []);

                // Distribute images based on height
                galleryImages.forEach((image) => {
                    let shortest = 0;
                    let shortestHeight = Infinity;
                    for (let i = 0; i < columns.length; i++) {
                        let colHeight = columns[i].reduce((sum, img) => sum + (img.height || 300), 0);
                        if (colHeight < shortestHeight) {
                            shortestHeight = colHeight;
                            shortest = i;
                        }
                    }
                    columns[shortest].push(image);
                });

                // Use DocumentFragment for faster DOM updates
                const fragment = document.createDocumentFragment();

                columns.forEach(columnImages => {
                    const columnDiv = document.createElement('div');
                    columnDiv.className = 'masonry-column';

                    columnImages.forEach(image => {
                        const card = document.createElement('div');
                        card.className = 'gallery-card';
                        // Add loading="lazy" for native lazy loading
                        card.innerHTML = `
                            <img src="${image.src}" alt="${escapeHtml(image.caption)}" loading="lazy" decoding="async">
                            <div class="gallery-caption">
                                <span>${escapeHtml(image.caption)}</span>
                                ${image.isUploaded ? `<button class="delete-btn" data-id="${image.id}" style="background:none; border:none; color:#ff6b6b; cursor:pointer;"><i class="fa-regular fa-trash-alt"></i></button>` : '<i class="fa-regular fa-heart" style="color:#ff6b6b;"></i>'}
                            </div>
                        `;
                        columnDiv.appendChild(card);
                    });
                    fragment.appendChild(columnDiv);
                });

                grid.innerHTML = '';
                grid.appendChild(fragment);

                // Add delete handlers
                document.querySelectorAll(".delete-btn").forEach(btn => {
                    btn.addEventListener("click", (e) => {
                        e.stopPropagation();
                        const id = parseInt(btn.getAttribute("data-id"));
                        if (confirm("Remove this photo?")) {
                            galleryImages = galleryImages.filter(img => img.id !== id);
                            saveUploadedImages();
                            renderMasonryGrid();
                        }
                    });
                });

                renderQueued = false;
            });
        }

        function escapeHtml(str) {
            return String(str).replace(/[&<>]/g, function (m) {
                if (m === "&") return "&amp;";
                if (m === "<") return "&lt;";
                if (m === ">") return "&gt;";
                return m;
            });
        }

        // Optimized upload - compress image before saving
        function compressImage(file, maxWidth = 800, quality = 0.7) {
            return new Promise((resolve) => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const img = new Image();
                    img.onload = () => {
                        let width = img.width;
                        let height = img.height;

                        if (width > maxWidth) {
                            height = (height * maxWidth) / width;
                            width = maxWidth;
                        }

                        const canvas = document.createElement('canvas');
                        canvas.width = width;
                        canvas.height = height;
                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0, width, height);

                        canvas.toBlob((blob) => {
                            const compressedReader = new FileReader();
                            compressedReader.onloadend = () => {
                                resolve({
                                    src: compressedReader.result,
                                    width: width,
                                    height: height
                                });
                            };
                            compressedReader.readAsDataURL(blob);
                        }, 'image/jpeg', quality);
                    };
                    img.src = e.target.result;
                };
                reader.readAsDataURL(file);
            });
        }

        async function handleUpload() {
            const fileInput = document.getElementById("fileInput");
            fileInput.click();
            fileInput.onchange = async (event) => {
                const file = event.target.files[0];
                if (file && file.type.startsWith("image/")) {
                    const caption = prompt("Add a caption:", "Youth memories ✨");
                    if (caption !== null) {
                        // Show loading indicator
                        const grid = document.getElementById("masonryGrid");
                        const originalHTML = grid.innerHTML;
                        grid.innerHTML = '<div class="loading">⏳ Compressing image...</div>';

                        try {
                            const compressed = await compressImage(file, 800, 0.6);
                            const newImage = {
                                id: Date.now(),
                                src: compressed.src,
                                caption: caption || "Youth moment 📸",
                                height: compressed.height,
                                width: compressed.width,
                                isUploaded: true
                            };
                            galleryImages.unshift(newImage);
                            saveUploadedImages();
                            renderMasonryGrid();
                        } catch (err) {
                            alert("Upload failed, try a smaller image");
                            renderMasonryGrid();
                        }
                    }
                } else {
                    alert("Please select an image file.");
                }
            };
        }

        // Debounced resize
        let resizeTimeout;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(() => renderMasonryGrid(), 150);
        });

        document.addEventListener("DOMContentLoaded", () => {
            loadGallery();
            document.getElementById("uploadBtn").addEventListener("click", handleUpload);
        });