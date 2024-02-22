// Slider logic
window.onload = () => {
    slideOne();
    slideTwo();
};

const sliderOne = document.getElementById("slider-1");
const sliderTwo = document.getElementById("slider-2");
const displayValOne = document.getElementById("range1");
const displayValTwo = document.getElementById("range2");
const minGap = 0;
const sliderTrack = document.querySelector(".slider-track");

const slideOne = () => {
    if (parseInt(sliderTwo.value) - parseInt(sliderOne.value) <= minGap) {
        sliderOne.value = parseInt(sliderTwo.value) - minGap;
    }
    displayValOne.textContent = sliderOne.value;
    fillColor();
}

const slideTwo = () => {
    if (parseInt(sliderTwo.value) - parseInt(sliderOne.value) <= minGap) {
        sliderTwo.value = parseInt(sliderOne.value) + minGap;
    }
    displayValTwo.textContent = sliderTwo.value;
    fillColor();
}

const fillColor = () => {
    let totalRange = sliderOne.max - sliderOne.min;
    let percent1 = ((sliderOne.value - sliderOne.min) / totalRange) * 100;
    let percent2 = ((sliderTwo.value - sliderOne.min) / totalRange) * 100;
    sliderTrack.style.background = `linear-gradient(to right, #dadae5 ${percent1}% , #154726c2 ${percent1}% , #154726c2 ${percent2}%, #dadae5 ${percent2}%)`;
}

// Filter logic
const originalItemsArray = Array.from(document.querySelectorAll('.sortable-item'));

function baseEventListener(name, customBehaviorOne = () => {}, customBehaviorTwo = () => {}) {
    const divFilter = document.getElementById('fruit-active-filter');
    const existingButtons = divFilter.children;
    let exists = false;
    for (let i = 0; i < existingButtons.length; i++) {
        if (existingButtons[i].id === name) {
            exists = true;
            setNewOrder(originalItemsArray);
            divFilter.removeChild(existingButtons[i])
            break;
        }
    }

    if (!exists) {
        customBehaviorTwo();
        const btn = document.createElement('button');
        btn.innerHTML = '&#x2716; ' + name;
        btn.className = 'fruit-filter-active-btn';
        btn.id = name;

        btn.addEventListener('click', function () {
            document.getElementById('fruit-active-filter').removeChild(this);
            customBehaviorOne();
            setNewOrder(originalItemsArray);
        });

        divFilter.appendChild(btn);
    }
}


document.getElementById('fruit-filter-genre').addEventListener('click', function () {
    const applyFilterByGenre = () => {
        const uniqueGenres = new Set();
        const currentItemArray = Array.from(document.querySelectorAll('.sortable-item'));

        currentItemArray.forEach(element => {
            const genres = element.getAttribute('data-genres').split(',');
            genres.forEach((genre) => {
                const genreCleanStr = genre.replace(/\W/g, '');
                if (genreCleanStr) uniqueGenres.add(genreCleanStr);
            });
        });

        setOrderByGenre(Array.from(uniqueGenres).sort(), currentItemArray);
    }

    baseEventListener(this.name, undefined, () => {
        applyFilterByGenre();
    });
});

document.getElementById('fruit-filter-annee').addEventListener('click', function () {
    baseEventListener(this.name, () => {
        const sliderWrapper = document.getElementById('slider-wrapper');
        sliderWrapper.style.visibility = sliderWrapper.style.visibility === 'visible' ? 'hidden' : 'visible';
    });

    const sliderWrapper = document.getElementById('slider-wrapper');
    sliderWrapper.style.visibility = sliderWrapper.style.visibility === 'visible' ? 'hidden' : 'visible';
});

document.getElementById('slider-1').addEventListener('change', function () {
    sliderEventListener();
});

document.getElementById('slider-2').addEventListener('change', function () {
    sliderEventListener();
});

const sliderEventListener = () => {
    const minYear = document.getElementById('slider-1').value;
    const maxYear = document.getElementById('slider-2').value;

    setNewOrder(originalItemsArray.filter(album => {
        const year = album.getAttribute('data-year');
        const intYear = year ? parseInt(year, 10) : Number.MAX_SAFE_INTEGER;
        return intYear >= minYear && intYear <= maxYear;
    }));
};

const setNewOrder = (itemsArray) => {
    function arraysAreEqual(arr1, arr2) {
        if (arr1.length !== arr2.length) {
            return false;
        }
        for (let i = 0; i < arr1.length; i++) {
            if (arr1[i] !== arr2[i]) {
                return false;
            }
        }
        return true;
    }

    const arrayEqual = !arraysAreEqual(originalItemsArray, Array.from(document.querySelectorAll('.sortable-item')));

    if (itemsArray !== originalItemsArray || arrayEqual) {
        console.log(itemsArray)
        const container = document.querySelector('.albums-wrapper');
        container.innerHTML = '';
        itemsArray.forEach(item => {
            container.appendChild(item);
        });
    }
}

const setOrderByGenre = (genres, currentItemArray) => {
    const orderedByGenre = [];

    genres.forEach(genre => {
        const genreItems = currentItemArray.filter(album => {
            const localGenres = album.getAttribute('data-genres').split(',').map(genre => genre.replace(/\W/g, ''));
            return localGenres.includes(genre);
        });
        orderedByGenre.push(genreItems);
    });

    const container = document.querySelector('.albums-wrapper');
    container.innerHTML = '';

    orderedByGenre.forEach((item, index) => {
        const div = document.createElement('div');
        div.style.cssText = 'display: flex; flex-direction: column; width: 100%';
        const h2 = document.createElement('h2');
        h2.innerHTML = genres[index];
        h2.className = 'albums-genre';
        const divider = document.createElement('div');
        divider.className = 'albums-genre-divider';
        div.appendChild(h2);
        div.appendChild(divider);
        container.appendChild(div);

        item.forEach(album => {
            const clone = album.cloneNode(true);
            container.appendChild(clone);
        });
    });
}