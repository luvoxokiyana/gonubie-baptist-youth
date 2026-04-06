// js/voting.js - Updated with separate description panel
let votesData = {
    bible: { options: [], has_voted: false, user_choice: null },
    game: { options: [], has_voted: false, user_choice: null },
    event: { options: [], has_voted: false, user_choice: null }
};

// Store current selected option data
let currentSelected = {
    bible: null,
    game: null,
    event: null
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

// Show description in the panel
function showDescription(pollType, optionId) {
    const poll = votesData[pollType];
    if (!poll) return;
    
    const selectedOption = poll.options.find(opt => opt.option_id === optionId);
    if (!selectedOption) return;
    
    const descriptionContainer = document.getElementById('description-content');
    if (!descriptionContainer) return;
    
    const isGame = pollType === 'game';
    const description = selectedOption.game_rules || selectedOption.description;
    
    if (description) {
        if (isGame) {
            // Format game rules as bullet points
            const rulesHtml = description.split('\n').map(line => {
                if (line.trim().startsWith('•') || line.trim().startsWith('-')) {
                    return `<li>${escapeHtml(line.trim().substring(1).trim())}</li>`;
                }
                return line.trim() ? `<li>${escapeHtml(line.trim())}</li>` : '';
            }).filter(line => line).join('');
            
            descriptionContainer.innerHTML = `
                <div class="topic-detail">
                    <h4><i class="fa-solid fa-dice-d6 game-icon"></i> ${escapeHtml(selectedOption.option_name)}</h4>
                    <p>${escapeHtml(description.split('\n')[0])}</p>
                    ${rulesHtml ? `<ul class="rules-list">${rulesHtml}</ul>` : ''}
                </div>
            `;
        } else {
            descriptionContainer.innerHTML = `
                <div class="topic-detail">
                    <h4><i class="fa-regular fa-lightbulb topic-icon"></i> About "${escapeHtml(selectedOption.option_name)}"</h4>
                    <p>${escapeHtml(description).replace(/\n/g, '<br>')}</p>
                </div>
            `;
        }
    } else {
        descriptionContainer.innerHTML = `
            <div class="topic-detail">
                <p>More information about this option coming soon!</p>
            </div>
        `;
    }
    
    // Update selected styling
    document.querySelectorAll(`.poll-option`).forEach(opt => {
        opt.classList.remove('selected');
    });
    const selectedRadio = document.querySelector(`input[name="${pollType}_vote"][value="${optionId}"]`);
    if (selectedRadio) {
        selectedRadio.closest('.poll-option').classList.add('selected');
    }
}

// Render poll options
function renderPoll(pollType) {
    const poll = votesData[pollType];
    const optionsContainer = document.getElementById(`${pollType}-options`);
    const resultsContainer = document.getElementById(`${pollType}-results`);
    const votedMessageDiv = document.getElementById(`${pollType}-voted-message`);
    const voteBtn = document.getElementById(`vote-${pollType}-btn`);

    if (!optionsContainer || !poll) return;

    optionsContainer.innerHTML = '';
    poll.options.forEach(opt => {
        const div = document.createElement('div');
        div.className = 'poll-option';
        const isChecked = poll.user_choice === opt.option_id;
        
        div.innerHTML = `
            <input type="radio" name="${pollType}_vote" value="${opt.option_id}" id="${opt.option_id}_${pollType}" ${isChecked ? 'checked' : ''} ${poll.has_voted ? 'disabled' : ''}>
            <label for="${opt.option_id}_${pollType}">${escapeHtml(opt.option_name)}</label>
            ${isChecked ? '<small>✓ Your vote</small>' : ''}
        `;
        optionsContainer.appendChild(div);
    });

    // Add event listeners to show description when radio is clicked
    document.querySelectorAll(`input[name="${pollType}_vote"]`).forEach(radio => {
        radio.addEventListener('change', (e) => {
            showDescription(pollType, e.target.value);
        });
        
        // If this radio is checked, show its description
        if (radio.checked) {
            showDescription(pollType, radio.value);
        }
    });

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

    // Render results
    const totalVotes = poll.options.reduce((sum, opt) => sum + (opt.votes || 0), 0);

    if (totalVotes > 0) {
        resultsContainer.innerHTML = `
            <div style="margin-bottom: 0.5rem; font-size: 0.75rem; color: #6b6a66;">📊 Current Results:</div>
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
            alert(`✅ Vote recorded!`);
        } else {
            if (result.error === 'You must be logged in to vote') {
                alert('Please login to vote');
                window.location.href = 'login.php?redirect=voting.php';
            } else {
                alert(`❌ ${result.error}`);
            }
        }
    } catch (error) {
        console.error('Error casting vote:', error);
        alert('An error occurred. Please try again.');
    }
}

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
            alert(`❌ ${result.error}`);
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

    const bibleBtn = document.getElementById('vote-bible-btn');
    const gameBtn = document.getElementById('vote-game-btn');
    const eventBtn = document.getElementById('vote-event-btn');
    const submitBtn = document.getElementById('submit-suggestion');
    const suggestionInput = document.getElementById('suggestion-input');

    if (bibleBtn) bibleBtn.addEventListener('click', () => castVote('bible'));
    if (gameBtn) gameBtn.addEventListener('click', () => castVote('game'));
    if (eventBtn) eventBtn.addEventListener('click', () => castVote('event'));
    if (submitBtn) submitBtn.addEventListener('click', addSuggestion);
    if (suggestionInput) suggestionInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') addSuggestion();
    });
});