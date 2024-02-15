const suggestions = ['Pomme', 'Banane', 'Fraise', 'Peche', 'Poire'];

// Autocomplete logic
document.getElementById('search-input').addEventListener('input', function () {
    updateSuggestions(this.value.toLowerCase());
});

document.getElementById('search-input').addEventListener('click', function () {
    if (this.value === '')
        updateSuggestions('');
});

document.addEventListener('click', function (event) {
    const searchInput = document.getElementById('search-input');
    const autocompleteResults = document.getElementById('autocomplete-results');

    if (!searchInput.contains(event.target) && !autocompleteResults.contains(event.target)) {
        searchInput.classList.add('navbar-search-input');
        searchInput.classList.remove('autocomplete-input');
        autocompleteResults.style.display = 'none';
    }
});

function updateSuggestions(input) {
    const resultsContainer = document.getElementById('autocomplete-results');
    const filteredSuggestions = input === '' ? suggestions : suggestions.filter(suggestion =>
        suggestion.toLowerCase().startsWith(input)
    );
    resultsContainer.innerHTML = '';

    if (filteredSuggestions.length) {
        document.getElementById('search-input').classList.add('autocomplete-input');
        document.getElementById('search-input').classList.remove('navbar-search-input');
        resultsContainer.style.display = 'block';
        filteredSuggestions.forEach(suggestion => {
            const li = document.createElement('li');
            li.textContent = suggestion;
            li.addEventListener('click', () => {
                document.getElementById('search-input').value = suggestion;
                document.getElementById('search-input').classList.add('navbar-search-input');
                document.getElementById('search-input').classList.remove('autocomplete-input');
                resultsContainer.style.display = 'none';
            });
            resultsContainer.appendChild(li);
            resultsContainer.appendChild(document.createElement('div'));
        });
    } else {
        resultsContainer.style.display = 'none';
    }
}

// Search logic
document.getElementById('search-input').addEventListener('keydown', function(event) {
    if (event.key === "Enter") {
        event.preventDefault();
        performSearch(this.value);
    }
});

document.getElementById('autocomplete-search-button').addEventListener('click', function() {
    const inputValue = document.getElementById('search-input').value;
    performSearch(inputValue);
});

function performSearch(searchQuery) {
    console.log("Search for: ", searchQuery);
    window.location.href = 'searchAbout' + searchQuery;
}