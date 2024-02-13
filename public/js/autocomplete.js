const suggestions = ['Pomme', 'Banane', 'Fraise', 'Peche', 'Poire'];

const separator = () => {
    return document.createElement('div');
};

document.getElementById('search-input').addEventListener('input', function () {
    const input = this.value.toLowerCase();
    const resultsContainer = document.getElementById('autocomplete-results');
    if (input === '') {
        if (this.classList.contains('autocomplete-input')) {
            this.classList.add('navbar-search-input');
            this.classList.remove('autocomplete-input');
            resultsContainer.style.display = 'none';
        }
    } else {

        const filteredSuggestions = suggestions.filter(suggestion =>
            suggestion.toLowerCase().startsWith(input)
        );
        resultsContainer.innerHTML = '';

        if (filteredSuggestions.length && input !== '') {
            this.classList.add('autocomplete-input');
            this.classList.remove('navbar-search-input');
            resultsContainer.style.display = 'block';
            filteredSuggestions.forEach(suggestion => {
                const li = document.createElement('li');
                li.textContent = suggestion;
                li.addEventListener('click', () => {
                    document.getElementById('search-input').value = suggestion;
                    this.classList.add('navbar-search-input');
                    this.classList.remove('autocomplete-input');
                    resultsContainer.style.display = 'none';
                });
                resultsContainer.appendChild(li);
                resultsContainer.appendChild(separator());
            });
        } else {
            resultsContainer.style.display = 'none';
        }
    }
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