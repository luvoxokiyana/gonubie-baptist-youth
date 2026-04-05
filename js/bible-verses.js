    // Bible verses database - different verse for each day of the year
        // Using date-based indexing so same date always shows same verse
        const bibleVerses = [
            { text: "I can do all things through Christ who strengthens me.", reference: "Philippians 4:13", context: "Paul wrote this while in prison, showing that true strength comes from God, not circumstances." },
            { text: "For God so loved the world that he gave his one and only Son, that whoever believes in him shall not perish but have eternal life.", reference: "John 3:16", context: "The gospel in one verse - God's love, sacrifice, and the gift of eternal life." },
            { text: "Trust in the Lord with all your heart and lean not on your own understanding.", reference: "Proverbs 3:5-6", context: "Wisdom from Solomon about complete dependence on God rather than our limited perspective." },
            { text: "Be strong and courageous. Do not be afraid; do not be discouraged, for the Lord your God will be with you wherever you go.", reference: "Joshua 1:9", context: "God's encouragement to Joshua as he led Israel into the Promised Land." },
            { text: "Love the Lord your God with all your heart and with all your soul and with all your mind.", reference: "Matthew 22:37", context: "Jesus' greatest commandment - wholehearted devotion to God." },
            { text: "Your word is a lamp to my feet and a light to my path.", reference: "Psalm 119:105", context: "A reminder that Scripture guides us through life's dark and uncertain moments." },
            { text: "Cast all your anxiety on him because he cares for you.", reference: "1 Peter 5:7", context: "Peter's encouragement to release our worries to God, who genuinely cares." },
            { text: "For I know the plans I have for you, plans to prosper you and not to harm you, plans to give you hope and a future.", reference: "Jeremiah 29:11", context: "God's promise of hope to Israel during exile - He has a good plan." },
            { text: "Therefore, if anyone is in Christ, the new creation has come: The old has gone, the new is here!", reference: "2 Corinthians 5:17", context: "Paul explains that faith in Christ makes us completely new." },
            { text: "Rejoice always, pray continually, give thanks in all circumstances.", reference: "1 Thessalonians 5:16-18", context: "Paul's simple but challenging call to constant joy, prayer, and gratitude." },
            { text: "But the fruit of the Spirit is love, joy, peace, forbearance, kindness, goodness, faithfulness, gentleness and self-control.", reference: "Galatians 5:22-23", context: "The character qualities that grow in us as we walk with the Holy Spirit." },
            { text: "Do not conform to the pattern of this world, but be transformed by the renewing of your mind.", reference: "Romans 12:2", context: "Paul's call to resist cultural pressure and let God reshape our thinking." },
            { text: "Have I not commanded you? Be strong and courageous. Do not be afraid; do not be discouraged.", reference: "Joshua 1:9", context: "Repeated encouragement for when fear tries to hold you back." },
            { text: "The Lord is my shepherd; I shall not want.", reference: "Psalm 23:1", context: "David's declaration of complete trust in God's provision and care." },
            { text: "Come to me, all you who are weary and burdened, and I will give you rest.", reference: "Matthew 11:28", context: "Jesus' invitation for anyone feeling exhausted or overwhelmed." },
            { text: "For we live by faith, not by sight.", reference: "2 Corinthians 5:7", context: "A reminder that faith often means trusting God when we can't see the outcome." },
            { text: "I have fought the good fight, I have finished the race, I have kept the faith.", reference: "2 Timothy 4:7", context: "Paul's confident reflection at the end of his life." },
            { text: "Let no one despise you for your youth, but set the believers an example in speech, in conduct, in love, in faith, in purity.", reference: "1 Timothy 4:12", context: "Perfect for youth - Paul tells Timothy that young people can lead by example." },
            { text: "Draw near to God, and he will draw near to you.", reference: "James 4:8", context: "A simple promise about the closeness God offers when we seek Him." },
            { text: "For where two or three gather in my name, there am I with them.", reference: "Matthew 18:20", context: "Jesus' promise about the power of gathering together as a community." },
            { text: "Be still, and know that I am God.", reference: "Psalm 46:10", context: "A call to pause, rest, and remember who is really in control." },
            { text: "Therefore encourage one another and build each other up.", reference: "1 Thessalonians 5:11", context: "Our calling to speak life and strength into each other." },
            { text: "I sought the Lord, and he answered me; he delivered me from all my fears.", reference: "Psalm 34:4", context: "David's testimony that God responds when we call out to Him." },
            { text: "And we know that in all things God works for the good of those who love him.", reference: "Romans 8:28", context: "Paul's assurance that nothing is wasted in God's plan." },
            { text: "Carry each other's burdens, and in this way you will fulfill the law of Christ.", reference: "Galatians 6:2", context: "Community means helping each other through hard times." }
        ];

        // Get verse based on current date (same verse all day, changes at midnight)
        function getDailyVerse() {
            const today = new Date();
            const dayOfYear = Math.floor((today - new Date(today.getFullYear(), 0, 0)) / (1000 * 60 * 60 * 24));
            const verseIndex = dayOfYear % bibleVerses.length;
            return bibleVerses[verseIndex];
        }

        // Get verses for the next 7 days
        function getWeekVerses() {
            const today = new Date();
            const weekVerses = [];

            for (let i = 0; i < 7; i++) {
                const date = new Date(today);
                date.setDate(today.getDate() + i);
                const dayOfYear = Math.floor((date - new Date(date.getFullYear(), 0, 0)) / (1000 * 60 * 60 * 24));
                const verseIndex = dayOfYear % bibleVerses.length;

                const options = { weekday: 'short', month: 'short', day: 'numeric' };
                weekVerses.push({
                    date: date.toLocaleDateString('en-US', options),
                    verse: bibleVerses[verseIndex],
                    fullDate: date
                });
            }
            return weekVerses;
        }

        // Format date nicely
        function getFormattedDate() {
            const today = new Date();
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            return today.toLocaleDateString('en-US', options);
        }

        // Render the daily verse
        function renderDailyVerse() {
            const verse = getDailyVerse();
            const verseText = document.getElementById('verseText');
            const verseReference = document.getElementById('verseReference');
            const verseDate = document.getElementById('verseDate');
            const contextText = document.getElementById('contextText');
            const verseContext = document.getElementById('verseContext');

            verseText.textContent = `"${verse.text}"`;
            verseReference.textContent = `— ${verse.reference}`;
            verseDate.textContent = getFormattedDate();
            contextText.textContent = verse.context;

            // Show context with animation
            setTimeout(() => {
                verseContext.style.display = 'block';
            }, 500);
        }

        // Render this week's verses
        function renderWeekVerses() {
            const weekVerses = getWeekVerses();
            const container = document.getElementById('weekVerses');

            container.innerHTML = weekVerses.map(day => `
                <div class="verse-preview" onclick="alert('${day.verse.text} — ${day.verse.reference}')">
                    <div class="date">📅 ${day.date}</div>
                    <div class="preview-text">"${day.verse.text.substring(0, 50)}${day.verse.text.length > 50 ? '...' : ''}"</div>
                    <div style="font-size:0.7rem; color:#1a5c3a; margin-top:0.3rem;">${day.verse.reference}</div>
                </div>
            `).join('');
        }

        // Share via WhatsApp
        function shareWhatsApp() {
            const verse = getDailyVerse();
            const text = `"${verse.text}" — ${verse.reference}\n\nDaily verse from Gonubie Baptist Youth 🙏`;
            const url = `https://wa.me/?text=${encodeURIComponent(text)}`;
            window.open(url, '_blank');
        }

        // Copy verse to clipboard
        function copyVerse() {
            const verse = getDailyVerse();
            const text = `"${verse.text}" — ${verse.reference}`;

            navigator.clipboard.writeText(text).then(() => {
                const copyBtn = document.getElementById('shareCopy');
                const originalText = copyBtn.innerHTML;
                copyBtn.innerHTML = '<i class="fa-regular fa-check"></i> Copied!';
                setTimeout(() => {
                    copyBtn.innerHTML = originalText;
                }, 2000);
            }).catch(() => {
                alert('Press Ctrl+C to copy');
            });
        }

        // Check if date has changed and refresh verse
        let lastDate = new Date().toDateString();
        function checkForNewDay() {
            const currentDate = new Date().toDateString();
            if (currentDate !== lastDate) {
                lastDate = currentDate;
                renderDailyVerse();
                renderWeekVerses();
            }
        }

        // Initial load
        document.addEventListener('DOMContentLoaded', () => {
            renderDailyVerse();
            renderWeekVerses();

            // Set up share buttons
            document.getElementById('shareWhatsApp').addEventListener('click', shareWhatsApp);
            document.getElementById('shareCopy').addEventListener('click', copyVerse);

            // Check for new day every minute
            setInterval(checkForNewDay, 60000);
        });