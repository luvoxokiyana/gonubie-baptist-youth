// Homepage JavaScript for Gonubie Baptist Youth

// ============================================
// 1. DYNAMIC VERSE OF THE DAY (Changes daily)
// ============================================
const bibleVerses = [
    { text: "I can do all things through Christ who strengthens me.", reference: "Philippians 4:13" },
    { text: "Trust in the Lord with all your heart and lean not on your own understanding.", reference: "Proverbs 3:5-6" },
    { text: "Be strong and courageous. Do not be afraid; do not be discouraged, for the Lord your God will be with you wherever you go.", reference: "Joshua 1:9" },
    { text: "For God so loved the world that he gave his one and only Son, that whoever believes in him shall not perish but have eternal life.", reference: "John 3:16" },
    { text: "Love the Lord your God with all your heart and with all your soul and with all your mind.", reference: "Matthew 22:37" },
    { text: "Your word is a lamp to my feet and a light to my path.", reference: "Psalm 119:105" },
    { text: "Cast all your anxiety on him because he cares for you.", reference: "1 Peter 5:7" },
    { text: "For I know the plans I have for you, plans to prosper you and not to harm you, plans to give you hope and a future.", reference: "Jeremiah 29:11" },
    { text: "Therefore, if anyone is in Christ, the new creation has come: The old has gone, the new is here!", reference: "2 Corinthians 5:17" },
    { text: "Rejoice always, pray continually, give thanks in all circumstances.", reference: "1 Thessalonians 5:16-18" },
    { text: "But the fruit of the Spirit is love, joy, peace, forbearance, kindness, goodness, faithfulness, gentleness and self-control.", reference: "Galatians 5:22-23" },
    { text: "Do not conform to the pattern of this world, but be transformed by the renewing of your mind.", reference: "Romans 12:2" },
    { text: "The Lord is my shepherd; I shall not want.", reference: "Psalm 23:1" },
    { text: "Come to me, all you who are weary and burdened, and I will give you rest.", reference: "Matthew 11:28" },
    { text: "Let no one despise you for your youth, but set the believers an example in speech, in conduct, in love, in faith, in purity.", reference: "1 Timothy 4:12" },
    { text: "For where two or three gather in my name, there am I with them.", reference: "Matthew 18:20" },
    { text: "Be still, and know that I am God.", reference: "Psalm 46:10" },
    { text: "Therefore encourage one another and build each other up.", reference: "1 Thessalonians 5:11" },
    { text: "I sought the Lord, and he answered me; he delivered me from all my fears.", reference: "Psalm 34:4" },
    { text: "And we know that in all things God works for the good of those who love him.", reference: "Romans 8:28" },
    { text: "Carry each other's burdens, and in this way you will fulfill the law of Christ.", reference: "Galatians 6:2" },
    { text: "Draw near to God, and he will draw near to you.", reference: "James 4:8" },
    { text: "I have fought the good fight, I have finished the race, I have kept the faith.", reference: "2 Timothy 4:7" },
    { text: "For we live by faith, not by sight.", reference: "2 Corinthians 5:7" },
    { text: "Have I not commanded you? Be strong and courageous. Do not be afraid; do not be discouraged.", reference: "Joshua 1:9" }
];

// Update verse of the day based on date
function updateVerseOfTheDay() {
    const today = new Date();
    const dayOfYear = Math.floor((today - new Date(today.getFullYear(), 0, 0)) / (1000 * 60 * 60 * 24));
    const verseIndex = dayOfYear % bibleVerses.length;
    const verse = bibleVerses[verseIndex];
    
    const verseElement = document.querySelector('.verse-text');
    if (verseElement) {
        verseElement.innerHTML = `"${verse.text}" — ${verse.reference}`;
    }
}

// ============================================
// 2. CTA BUTTON - Join Us This Friday
// ============================================
function setupCTAButton() {
    const ctaBtn = document.querySelector('.cta-btn');
    if (ctaBtn) {
        ctaBtn.addEventListener('click', () => {
            // Show a nice modal or alert with details
            showNotification(
                ' This Friday at GBY \n\n Topic: "Purpose Over Pressure"\n Game: Human Knot Challenge + Ultimate Dodgeball\n Time: 7PM\n Location: Gonubie Baptist Church Hall\n\nBring a friend! ',
                'info'
            );
        });
    }
}

// ============================================
// 3. CONTACT BUTTON - WhatsApp
// ============================================
function setupContactButton() {
    const contactBtn = document.querySelector('.contact-btn');
    if (contactBtn) {
        contactBtn.addEventListener('click', () => {
            // Replace with your actual WhatsApp number
            const whatsappNumber = '27693741222'; 
            const message = encodeURIComponent('Hi! I\'m interested in joining Gonubie Baptist Youth. Can you share more info?');
            const whatsappUrl = `https://wa.me/${whatsappNumber}?text=${message}`;
            window.open(whatsappUrl, '_blank');
        });
    }
}

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

// ============================================
// 5. NOTIFICATION SYSTEM (Custom alert)
// ============================================
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `custom-notification ${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fa-solid ${type === 'info' ? 'fa-circle-info' : 'fa-circle-check'}"></i>
            <p>${message.replace(/\n/g, '<br>')}</p>
        </div>
        <button class="notification-close">&times;</button>
    `;
    
    // Add styles
    notification.style.cssText = `
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: #161b22;
        border-left: 4px solid ${type === 'info' ? '#1a5c3a' : '#0f4a2f'};
        border-radius: 12px;
        padding: 1rem;
        color: #e6edf3;
        z-index: 1000;
        max-width: 350px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        animation: slideIn 0.3s ease;
    `;
    
    const contentDiv = notification.querySelector('.notification-content');
    contentDiv.style.cssText = `
        display: flex;
        align-items: center;
        gap: 12px;
        flex: 1;
    `;
    
    const closeBtn = notification.querySelector('.notification-close');
    closeBtn.style.cssText = `
        background: none;
        border: none;
        color: #8b949e;
        font-size: 1.2rem;
        cursor: pointer;
        padding: 0 4px;
    `;
    closeBtn.onclick = () => notification.remove();
    
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification && notification.remove) {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => notification.remove(), 300);
        }
    }, 5000);
}

// Add animation styles
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

// ============================================
// 6. SMOOTH SCROLLING for anchor links
// ============================================
function setupSmoothScrolling() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
}

// ============================================
// 7. ADD HOVER EFFECTS to event items
// ============================================
function setupEventHoverEffects() {
    const eventItems = document.querySelectorAll('.event-item');
    eventItems.forEach(item => {
        item.addEventListener('click', () => {
            const eventTitle = item.querySelector('h3')?.innerText || 'Event';
            const eventDate = item.querySelector('.event-date')?.innerText || '';
            showNotification(` ${eventTitle}\n${eventDate} - Don't miss out!`, 'info');
        });
    });
}

// ============================================
// 8. FEATURE CARD INTERACTIONS
// ============================================
function setupFeatureCards() {
    const features = document.querySelectorAll('.feature');
    const featureMessages = {
        'Community': 'Join our small groups and make real friends who will walk with you through life! ',
        'Faith': 'Engaging worship, relevant Bible talks, and opportunities to grow in your relationship with God. ',
        'Fun': 'Epic games, lock-ins, camps, and events you won\'t want to miss! '
    };
    
    features.forEach(feature => {
        feature.addEventListener('click', () => {
            const title = feature.querySelector('h3')?.innerText;
            if (title && featureMessages[title]) {
                showNotification(featureMessages[title], 'info');
            }
        });
    });
}

// ============================================
// 9. COUNTDOWN TO NEXT FRIDAY 
// ============================================
function updateFridayCountdown() {
    const now = new Date();
    const daysUntilFriday = (5 - now.getDay() + 7) % 7 || 7; // 5 = Friday
    const nextFriday = new Date(now);
    nextFriday.setDate(now.getDate() + daysUntilFriday);
    nextFriday.setHours(19, 0, 0, 0); // 7PM
    
    const diff = nextFriday - now;
    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
    const hours = Math.floor((diff % (86400000)) / (3600000));
    const minutes = Math.floor((diff % 3600000) / 60000);
    
}

// ============================================
// 10. INITIALIZE EVERYTHING
// ============================================
function initHomepage() {
    updateVerseOfTheDay();
    setupCTAButton();
    setupContactButton();
    setupSocialIcons();
    setupSmoothScrolling();
    setupEventHoverEffects();
    setupFeatureCards();
    updateFridayCountdown();
    
}

// Run when DOM is fully loaded
document.addEventListener('DOMContentLoaded', initHomepage);