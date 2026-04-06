// Gallery JavaScript with thumbnails, lightbox, and pagination
let allImages = [];
let displayedCount = 20;
let isLoading = false;

// Lightbox elements
const lightbox = document.getElementById('lightbox');
const lightboxImg = document.getElementById('lightbox-img');
const lightboxClose = document.querySelector('.lightbox-close');

// Load images from PHP script
async function loadGallery() {
    if (isLoading) return;
    isLoading = true;
    
    try {
        const response = await fetch('get-images.php');
        const serverImages = await response.json();
        
        // Sort by date (newest first) - assuming newer images have higher IDs
        const sortedImages = serverImages.sort((a, b) => b.id - a.id);
        
        const stored = localStorage.getItem("gby_uploaded_images");
        const uploadedImages = stored ? JSON.parse(stored) : [];
        
        // Combine server images + uploaded images, sort by ID (newest first)
        allImages = [...sortedImages, ...uploadedImages].sort((a, b) => b.id - a.id);
        
        renderMasonryGrid();
    } catch (error) {
        console.error('Error loading images:', error);
        const stored = localStorage.getItem("gby_uploaded_images");
        allImages = stored ? JSON.parse(stored) : [];
        renderMasonryGrid();
    }
    isLoading = false;
}

function saveUploadedImages() {
    const uploadedOnly = allImages.filter(img => img.isUploaded === true);
    const limited = uploadedOnly.slice(0, 20);
    localStorage.setItem("gby_uploaded_images", JSON.stringify(limited));
}

function renderMasonryGrid() {
    const grid = document.getElementById("masonryGrid");
    const loadMoreBtn = document.getElementById("loadMoreBtn");
    
    if (!grid) return;

    if (allImages.length === 0) {
        grid.innerHTML = `<div class="empty-gallery"><i class="fa-regular fa-images"></i><p>No photos yet. Add images to /images/gallery/ folder!</p></div>`;
        loadMoreBtn.classList.add('hidden');
        return;
    }

    // Show only first 'displayedCount' images
    const imagesToShow = allImages.slice(0, displayedCount);
    
    // Hide load more button if no more images
    if (displayedCount >= allImages.length) {
        loadMoreBtn.classList.add('hidden');
    } else {
        loadMoreBtn.classList.remove('hidden');
    }
    
    grid.innerHTML = '';
    
    const columnCount = window.innerWidth < 640 ? 1 : window.innerWidth < 900 ? 2 : window.innerWidth < 1200 ? 3 : 4;
    let columns = Array(columnCount).fill().map(() => []);
    
    // Distribute images to columns based on height
    imagesToShow.forEach((image) => {
        let shortest = 0;
        let shortestHeight = Infinity;
        for (let i = 0; i < columns.length; i++) {
            let colHeight = columns[i].reduce((sum, img) => sum + (img.thumbHeight || img.height || 300), 0);
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
            
            // Use thumbnail if available, otherwise use full image (but smaller)
            const thumbnailSrc = image.thumb || image.src;
            
            card.innerHTML = `
                <div class="image-wrapper">
                    <img 
                        src="${thumbnailSrc}" 
                        alt="${escapeHtml(image.caption)}" 
                        loading="lazy"
                        decoding="async"
                        data-fullsrc="${image.src}"
                        data-id="${image.id}"
                        class="gallery-thumb"
                    >
                    ${image.isUploaded ? `<button class="delete-btn" data-id="${image.id}"><i class="fa-regular fa-trash-alt"></i></button>` : ''}
                </div>
                <div class="gallery-caption">
                    <span>${escapeHtml(image.caption)}</span>
                    <i class="fa-regular fa-heart"></i>
                </div>
            `;
            columnDiv.appendChild(card);
        });
        fragment.appendChild(columnDiv);
    });
    
    grid.innerHTML = '';
    grid.appendChild(fragment);
    
// Add click handlers for thumbnails (open lightbox with full image)
// Check login status from body class
const isLoggedIn = document.body.classList.contains('logged-in');

document.querySelectorAll('.gallery-thumb').forEach(img => {
    img.addEventListener('click', (e) => {
        e.stopPropagation();
        
        // If not logged in, redirect to login page
        if (!isLoggedIn) {
            window.location.href = 'login.php?redirect=gallery.php';
            return;
        }
        
        // Logged in - show lightbox
        const fullSrc = img.getAttribute('data-fullsrc');
        if (fullSrc) {
            lightboxImg.src = fullSrc;
            lightbox.classList.add('active');
        }
    });
});
    
    // Add delete handlers for uploaded images
    document.querySelectorAll(".delete-btn").forEach(btn => {
        btn.addEventListener("click", (e) => {
            e.stopPropagation();
            const id = parseInt(btn.getAttribute("data-id"));
            if (confirm("Remove this photo?")) {
                allImages = allImages.filter(img => img.id !== id);
                saveUploadedImages();
                // Reset displayed count to maintain 20 limit
                displayedCount = Math.min(20, allImages.length);
                renderMasonryGrid();
            }
        });
    });

    card.innerHTML = `
    <div class="image-wrapper">
        <img 
            src="${thumbnailSrc}" 
            alt="${escapeHtml(image.caption)}" 
            loading="lazy"
            decoding="async"
            data-fullsrc="${image.src}"
            data-id="${image.id}"
            class="gallery-thumb"
            onerror="this.src='https://picsum.photos/400/300?random=1'"
        >
        ${image.isUploaded ? `<button class="delete-btn" data-id="${image.id}"><i class="fa-regular fa-trash-alt"></i></button>` : ''}
    </div>
    <div class="gallery-caption">
        <span>${escapeHtml(image.caption)}</span>
        <i class="fa-regular fa-heart"></i>
    </div>
`;
}

function loadMoreImages() {
    displayedCount += 20;
    renderMasonryGrid();
}

function escapeHtml(str) {
    return String(str).replace(/[&<>]/g, function(m) {
        if (m === "&") return "&amp;";
        if (m === "<") return "&lt;";
        if (m === ">") return "&gt;";
        return m;
    });
}

// Compress image before upload
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

// Handle image upload
async function handleUpload() {
    const fileInput = document.getElementById("fileInput");
    fileInput.click();
    fileInput.onchange = async (event) => {
        const file = event.target.files[0];
        if (file && file.type.startsWith("image/")) {
            const caption = prompt("Add a caption:", "Youth memories ✨");
            if (caption !== null) {
                const grid = document.getElementById("masonryGrid");
                grid.innerHTML = '<div class="loading">⏳ Compressing image...</div>';
                
                try {
                    const compressed = await compressImage(file, 800, 0.6);
                    const newImage = {
                        id: Date.now(),
                        src: compressed.src,
                        thumb: compressed.src, // Use same for thumb (already compressed)
                        caption: caption || "Youth moment 📸",
                        height: compressed.height,
                        width: compressed.width,
                        thumbHeight: compressed.height,
                        isUploaded: true
                    };
                    allImages.unshift(newImage);
                    saveUploadedImages();
                    displayedCount = Math.min(20, allImages.length);
                    renderMasonryGrid();
                } catch(err) {
                    alert("Upload failed, try a smaller image");
                    renderMasonryGrid();
                }
            }
        } else {
            alert("Please select an image file.");
        }
    };
}

// Lightbox close handlers
lightboxClose.addEventListener('click', () => {
    lightbox.classList.remove('active');
    lightboxImg.src = '';
});

lightbox.addEventListener('click', (e) => {
    if (e.target === lightbox) {
        lightbox.classList.remove('active');
        lightboxImg.src = '';
    }
});

// Keyboard handler for lightbox
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && lightbox.classList.contains('active')) {
        lightbox.classList.remove('active');
        lightboxImg.src = '';
    }
});

// Load more button
document.getElementById('loadMoreBtn').addEventListener('click', loadMoreImages);

// Debounced resize
let resizeTimeout;
window.addEventListener('resize', () => {
    clearTimeout(resizeTimeout);
    resizeTimeout = setTimeout(() => renderMasonryGrid(), 150);
});

// Initial load
document.addEventListener("DOMContentLoaded", () => {
    loadGallery();
    document.getElementById("uploadBtn").addEventListener("click", handleUpload);
});

