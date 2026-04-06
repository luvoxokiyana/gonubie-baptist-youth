// js/voting.js - Updated to use backend API
let votesData = {
    bible: { options: [], has_voted: false, user_choice: null },
    game: { options: [], has_voted: false, user_choice: null },
    event: { options: [], has_voted: false, user_choice: null }
};

// API base URL
const API_URL = 'api.php';

// Load all data from server
async function loadData() {
    try {
        const response = await fetch(`${API_URL}?action=get_votes`);
        const result = await response.json();
        if (result.success) {
            votesData = result.data;
            renderAllPolls();
        } else {
            console.error('Failed to load votes:', result.error);
        }
    } catch (error) {
        console.error('Error loading votes:', error);
    }

    await loadSuggestions();
}

async function loadSuggestions() {
    try {
        const response = await fetch(`${API_URL}?action=get_suggestions`);
        const result = await response.json();
        if (result.success) {
            renderSuggestions(result.data);
        }
    } catch (error) {
        console.error('Error loading suggestions:', error);
    }
}

// Render poll options and results
function renderPoll(pollType) {
    const poll = votesData[pollType];
    const optionsContainer = document.getElementById(`${pollType}-options`);
    const resultsContainer = document.getElementById(`${pollType}-results`);
    const votedMessageDiv = document.getElementById(`${pollType}-voted-message`);
    const voteBtn = document.getElementById(`vote-${pollType}-btn`);

    if (!optionsContainer || !poll) return;

    // Render radio options with descriptions
    optionsContainer.innerHTML = '';
    poll.options.forEach(opt => {
        const div = document.createElement('div');
        div.className = 'poll-option';
        const isChecked = poll.user_choice === opt.option_id;
        
        // Check if this is a game (has game_rules) or bible topic (has description)
        const description = opt.game_rules || opt.description;
        
        div.innerHTML = `
            <div class="option-header">
                <input type="radio" name="${pollType}_vote" value="${opt.option_id}" id="${opt.option_id}" ${isChecked ? 'checked' : ''}>
                <label for="${opt.option_id}">${escapeHtml(opt.option_name)}</label>
                ${isChecked ? '<small>✓ Your vote</small>' : ''}
            </div>
            ${description ? `<div class="option-description">${escapeHtml(description).replace(/\n/g, '<br>')}</div>` : ''}
        `;
        optionsContainer.appendChild(div);
    });

    // Rest of the function remains the same...
    if (poll.has_voted) {
        const radios = optionsContainer.querySelectorAll('input');
        radios.forEach(radio => radio.disabled = true);
        if (voteBtn) voteBtn.disabled = true;
        voteBtn.style.opacity = '0.5';
        voteBtn.style.cursor = 'not-allowed';
    } else {
        if (voteBtn) voteBtn.disabled = false;
        voteBtn.style.opacity = '1';
        voteBtn.style.cursor = 'pointer';
    }

    const totalVotes = poll.options.reduce((sum, opt) => sum + (opt.votes || 0), 0);

    if (totalVotes > 0) {
        resultsContainer.innerHTML = `
            <div style="margin-bottom: 0.5rem; font-size: 0.75rem; color: #8b949e;">📊 Current Results:</div>
            ${poll.options.map(opt => {
                const percent = ((opt.votes || 0) / totalVotes) * 100;
                return `
                    <div class="result-bar">
                        <div class="result-label">
                            <span>${escapeHtml(opt.option_name)}</span>
                            <span>${Math.round(percent)}% (${opt.votes || 0} votes)</span>
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

    if (poll.has_voted && poll.user_choice) {
        const selectedOption = poll.options.find(opt => opt.option_id === poll.user_choice);
        votedMessageDiv.innerHTML = `<div class="already-voted"><i class="fa-regular fa-circle-check"></i> You voted for: ${selectedOption ? escapeHtml(selectedOption.option_name) : 'something'}</div>`;
    } else {
        votedMessageDiv.innerHTML = '';
    }

    document.querySelectorAll(`input[name="${pollType}_vote"]`).forEach(radio => {
    // Remove existing listeners to avoid duplicates
    radio.removeEventListener('change', radio._listener);
    
    const listener = (e) => {
        const selectedId = e.target.value;
        const selectedOption = poll.options.find(opt => opt.option_id === selectedId);
        const descriptionContainer = document.getElementById(`${pollType}-description`);
        
        if (selectedOption && descriptionContainer) {
            const description = selectedOption.game_rules || selectedOption.description;
            if (description) {
                descriptionContainer.innerHTML = `<i class="fa-regular fa-lightbulb"></i> ${escapeHtml(description).replace(/\n/g, '<br>')}`;
                descriptionContainer.classList.add('visible');
            } else {
                descriptionContainer.innerHTML = '';
                descriptionContainer.classList.remove('visible');
            }
        }
    };
    
    radio.addEventListener('change', listener);
    radio._listener = listener; // Store for cleanup
});
}

function renderAllPolls() {
    renderPoll('bible');
    renderPoll('game');
    renderPoll('event');
}

// Cast vote via API
async function castVote(pollType) {
    if (votesData[pollType].has_voted) {
        alert(`You've already voted on this!`);
        return;
    }

    const selectedRadio = document.querySelector(`input[name="${pollType}_vote"]:checked`);
    if (!selectedRadio) {
        alert(`Please select an option first!`);
        return;
    }

    const selectedId = selectedRadio.value;
    
    try {
        const response = await fetch(`${API_URL}?action=cast_vote`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ poll_type: pollType, option_id: selectedId })
        });
        
        const result = await response.json();
        
        if (result.success) {
            await loadData();
            alert(`Vote recorded!`);
        } else {
            // Handle login required error - redirect to login
            if (result.error === 'You must be logged in to vote') {
                alert('Please login to vote');
                window.location.href = 'login.php?redirect=voting.php';
            } else {
                alert(`${result.error}`);
            }
        }
    } catch (error) {
        console.error('Error casting vote:', error);
        alert('An error occurred. Please try again.');
    }
}

// Render suggestions
function renderSuggestions(suggestions) {
    const container = document.getElementById('suggestions-list');
    if (!container) return;

    if (!suggestions || suggestions.length === 0) {
        container.innerHTML = '<span class="suggestion-tag">No suggestions yet. Be the first!</span>';
        return;
    }

    container.innerHTML = suggestions.slice(0, 20).map(sugg =>
        `<span class="suggestion-tag"><i class="fa-regular fa-star"></i> ${escapeHtml(sugg)}</span>`
    ).join('');
}

// Add suggestion via API
async function addSuggestion() {
    const input = document.getElementById('suggestion-input');
    const text = input.value.trim();
    if (text === '') {
        alert('Please enter a suggestion!');
        return;
    }

    try {
        const response = await fetch(`${API_URL}?action=add_suggestion`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ suggestion: text })
        });
        
        const result = await response.json();
        
        if (result.success) {
            await loadSuggestions();
            input.value = '';
            alert(`Thanks for your suggestion! We'll consider it for future polls.`);
        } else {
            alert(`${result.error}`);
        }
    } catch (error) {
        console.error('Error adding suggestion:', error);
        alert('An error occurred. Please try again.');
    }
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

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    loadData();

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
