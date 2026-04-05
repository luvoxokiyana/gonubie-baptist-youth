    // Voting data structure
        let votes = {
            bible: {
                options: [
                    { id: 'b1', name: 'Overcoming Anxiety', votes: 0 },
                    { id: 'b2', name: 'Friendship & Faith', votes: 0 },
                    { id: 'b3', name: 'Purpose & Calling', votes: 0 },
                    { id: 'b4', name: 'Social Media & Identity', votes: 0 }
                ],
                voted: false,
                userChoice: null
            },
            game: {
                options: [
                    { id: 'g1', name: 'Capture the Flag (Glow)', votes: 0 },
                    { id: 'g2', name: 'Minute to Win It', votes: 0 },
                    { id: 'g3', name: 'Karaoke Battle', votes: 0 },
                    { id: 'g4', name: 'Escape Room Challenge', votes: 0 }
                ],
                voted: false,
                userChoice: null
            },
            event: {
                options: [
                    { id: 'e1', name: 'Beach Braai & Bonfire', votes: 0 },
                    { id: 'e2', name: 'Lock-in (All Night)', votes: 0 },
                    { id: 'e3', name: 'Movie & Popcorn Night', votes: 0 },
                    { id: 'e4', name: 'Sports Tournament', votes: 0 }
                ],
                voted: false,
                userChoice: null
            }
        };

        let suggestions = [];

        // Load saved data from localStorage
        function loadData() {
            const savedVotes = localStorage.getItem('gby_votes');
            if (savedVotes) {
                const parsed = JSON.parse(savedVotes);
                if (parsed.bible) votes.bible = parsed.bible;
                if (parsed.game) votes.game = parsed.game;
                if (parsed.event) votes.event = parsed.event;
            }

            const savedSuggestions = localStorage.getItem('gby_suggestions');
            if (savedSuggestions) {
                suggestions = JSON.parse(savedSuggestions);
            }
        }

        // Save votes to localStorage
        function saveVotes() {
            localStorage.setItem('gby_votes', JSON.stringify(votes));
        }

        function saveSuggestions() {
            localStorage.setItem('gby_suggestions', JSON.stringify(suggestions));
        }

        // Render poll options and results
        function renderPoll(pollType) {
            const poll = votes[pollType];
            const optionsContainer = document.getElementById(`${pollType}-options`);
            const resultsContainer = document.getElementById(`${pollType}-results`);
            const votedMessageDiv = document.getElementById(`${pollType}-voted-message`);

            if (!optionsContainer) return;

            // Render radio options
            optionsContainer.innerHTML = '';
            poll.options.forEach(opt => {
                const div = document.createElement('div');
                div.className = 'poll-option';
                div.innerHTML = `
                    <input type="radio" name="${pollType}_vote" value="${opt.id}" id="${opt.id}" ${poll.userChoice === opt.id ? 'checked disabled' : ''}>
                    <label for="${opt.id}">${opt.name}</label>
                    ${poll.userChoice === opt.id ? '<small>✓ Your vote</small>' : ''}
                `;
                optionsContainer.appendChild(div);
            });

            // If already voted, disable all radios
            if (poll.voted) {
                const radios = optionsContainer.querySelectorAll('input');
                radios.forEach(radio => radio.disabled = true);
            }

            // Render results
            const totalVotes = poll.options.reduce((sum, opt) => sum + opt.votes, 0);

            if (totalVotes > 0) {
                resultsContainer.innerHTML = `
                    <div style="margin-bottom: 0.5rem; font-size: 0.75rem; color: #8b949e;">📊 Current Results:</div>
                    ${poll.options.map(opt => {
                    const percent = (opt.votes / totalVotes) * 100;
                    return `
                            <div class="result-bar">
                                <div class="result-label">
                                    <span>${opt.name}</span>
                                    <span>${Math.round(percent)}% (${opt.votes} votes)</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: ${percent}%"></div>
                                </div>
                            </div>
                        `;
                }).join('')}
                    <div class="total-votes">Total votes: ${totalVotes}</div>
                `;
            } else {
                resultsContainer.innerHTML = '<div class="total-votes">✨ No votes yet. Be the first!</div>';
            }

            // Show "already voted" message
            if (poll.voted) {
                const selectedOption = poll.options.find(opt => opt.id === poll.userChoice);
                votedMessageDiv.innerHTML = `<div class="already-voted"><i class="fa-regular fa-circle-check"></i> You voted for: ${selectedOption ? selectedOption.name : 'something'}</div>`;
            } else {
                votedMessageDiv.innerHTML = '';
            }
        }

        function renderAllPolls() {
            renderPoll('bible');
            renderPoll('game');
            renderPoll('event');
            renderSuggestions();
        }

        // Cast vote
        function castVote(pollType) {
            if (votes[pollType].voted) {
                alert(`You've already voted on this!`);
                return;
            }

            const selectedRadio = document.querySelector(`input[name="${pollType}_vote"]:checked`);
            if (!selectedRadio) {
                alert(`Please select an option first!`);
                return;
            }

            const selectedId = selectedRadio.value;
            const poll = votes[pollType];
            const selectedOption = poll.options.find(opt => opt.id === selectedId);

            if (selectedOption) {
                selectedOption.votes++;
                poll.voted = true;
                poll.userChoice = selectedId;
                saveVotes();
                renderPoll(pollType);

                // Show confirmation
                alert(`✅ You voted for: ${selectedOption.name}\nThanks for making your voice heard!`);
            }
        }

        // Render suggestions
        function renderSuggestions() {
            const container = document.getElementById('suggestions-list');
            if (!container) return;

            if (suggestions.length === 0) {
                container.innerHTML = '<span class="suggestion-tag">No suggestions yet. Be the first!</span>';
                return;
            }

            container.innerHTML = suggestions.slice(-10).reverse().map(sugg =>
                `<span class="suggestion-tag"><i class="fa-regular fa-star"></i> ${escapeHtml(sugg)}</span>`
            ).join('');
        }

        function addSuggestion() {
            const input = document.getElementById('suggestion-input');
            const text = input.value.trim();
            if (text === '') {
                alert('Please enter a suggestion!');
                return;
            }

            suggestions.push(text);
            saveSuggestions();
            renderSuggestions();
            input.value = '';
            alert(`Thanks for your suggestion! We'll consider it for future polls.`);
        }

        function escapeHtml(str) {
            return str.replace(/[&<>]/g, function (m) {
                if (m === '&') return '&amp;';
                if (m === '<') return '&lt;';
                if (m === '>') return '&gt;';
                return m;
            });
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            loadData();
            renderAllPolls();

            // Vote buttons
            document.getElementById('vote-bible-btn').addEventListener('click', () => castVote('bible'));
            document.getElementById('vote-game-btn').addEventListener('click', () => castVote('game'));
            document.getElementById('vote-event-btn').addEventListener('click', () => castVote('event'));

            // Suggestion button
            document.getElementById('submit-suggestion').addEventListener('click', addSuggestion);
            document.getElementById('suggestion-input').addEventListener('keypress', (e) => {
                if (e.key === 'Enter') addSuggestion();
            });
        });