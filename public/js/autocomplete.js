let suggestions = [];
const xhr = new XMLHttpRequest();
xhr.open('GET', '/autocompleteList', true);

xhr.onload = function () {
    if (this.status === 200) {
        suggestions = JSON.parse(this.responseText);
        console.log(suggestions);
        setUpAutocompleteLogic(suggestions);
        setUpSearchLogic();
    } else {
        console.error('Failed to fetch autocomplete list', this.statusText);
    }
};

xhr.send();


const setUpAutocompleteLogic = (suggestions) => {
    document.getElementById('search-input').addEventListener('input', function () {
        updateSuggestions(this.value.toLowerCase(), suggestions);
    });

    document.getElementById('search-input').addEventListener('click', function () {
        if (this.value === '')
            updateSuggestions('', suggestions);
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
};


const updateSuggestions = (input, suggestions) => {
    const resultsContainer = document.getElementById('autocomplete-results');
    const filteredSuggestions = input === '' ? suggestions : suggestions.filter(suggestion =>
        suggestion.toLowerCase().startsWith(input)
    );
    resultsContainer.innerHTML = '';

    if (filteredSuggestions.length) {
        const searchInput = document.getElementById('search-input');
        searchInput.classList.add('autocomplete-input');
        searchInput.classList.remove('navbar-search-input');
        resultsContainer.style.display = 'block';
        filteredSuggestions.forEach(suggestion => {
            const li = document.createElement('li');
            li.textContent = suggestion;
            li.addEventListener('click', () => {
                searchInput.value = suggestion;
                searchInput.classList.add('navbar-search-input');
                searchInput.classList.remove('autocomplete-input');
                resultsContainer.style.display = 'none';
            });
            resultsContainer.appendChild(li);
            resultsContainer.appendChild(document.createElement('div'));
        });
    } else {
        resultsContainer.style.display = 'none';
    }
};

const setUpSearchLogic = () => {
    document.getElementById('search-input').addEventListener('keydown', function (event) {
        if (event.key === "Enter") {
            event.preventDefault();
            performSearch(this.value);
        }
    });

    document.getElementById('autocomplete-search-button').addEventListener('click', function () {
        performSearch(document.getElementById('search-input').value);
    });

    const performSearch = (searchQuery) => {
        console.log("Search for: ", searchQuery);
        if (searchQuery) window.location.href = '/search/' + searchQuery + '/1';
    };
};