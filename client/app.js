const API_BASE = 'http://localhost:3000';
let currentCity = 'manila';

// Handle search form submission
document.getElementById('searchForm').addEventListener('submit', async (e) => {
  e.preventDefault();
  currentCity = document.getElementById('searchCity').value;
  document.getElementById('cityName').textContent =
    currentCity.charAt(0).toUpperCase() + currentCity.slice(1);

  // Fetch hotels
  await fetchHotels(currentCity);
  // Fetch places
  await fetchPlaces(currentCity);
});

async function fetchHotels(city) {
  const stayRowMain = document.getElementById('stayRowMain');
  const stayRowMore = document.getElementById('stayRowMore');
  const showMoreContainer = document.getElementById('showMoreContainer');

  stayRowMain.innerHTML = `
    <div class="col-12 text-center py-5">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
      <p class="mt-2">Loading hotels in ${city}...</p>
    </div>
  `;

  if (stayRowMore) stayRowMore.innerHTML = '';
  if (showMoreContainer) showMoreContainer.style.display = 'none';

  try {
    const params = new URLSearchParams({
      checkin: '2025-12-25',
      checkout: '2025-12-26',
      adults: '2',
      city,
      countryCode: 'PH',
      environment: 'sandbox'
    });

    const response = await fetch(`${API_BASE}/search-hotels?${params.toString()}`);
    if (!response.ok) {
      throw new Error(`Server error: ${response.status}`);
    }

    const data = await response.json();
    const hotels = data.rates || [];

    displayHotels(hotels);
  } catch (error) {
    console.error('Error fetching hotels:', error);
    stayRowMain.innerHTML = `
      <div class="col-12">
        <div class="alert alert-danger" role="alert">
          <strong>Error loading hotels</strong><br>
          ${error.message}<br>
          <small>Make sure the Node.js server is running (<code>npm start</code>).</small>
        </div>
      </div>
    `;
  }
}

function displayHotels(hotels) {
  const stayRowMain = document.getElementById('stayRowMain');
  const stayRowMore = document.getElementById('stayRowMore');
  const showMoreContainer = document.getElementById('showMoreContainer');

  if (!hotels || hotels.length === 0) {
    stayRowMain.innerHTML = `
      <div class="col-12">
        <p>No hotels found.</p>
      </div>
    `;
    if (stayRowMore) stayRowMore.innerHTML = '';
    if (showMoreContainer) showMoreContainer.style.display = 'none';
    return;
  }

  // Display first 3 hotels
  stayRowMain.innerHTML = hotels
    .slice(0, 3)
    .map((hotel, index) => createHotelCard(hotel, index))
    .join('');

  // Display remaining in collapse
  if (hotels.length > 3 && stayRowMore) {
    stayRowMore.innerHTML = hotels
      .slice(3)
      .map((hotel, index) => createHotelCard(hotel, index + 3))
      .join('');
    if (showMoreContainer) showMoreContainer.style.display = 'block';
  } else if (showMoreContainer) {
    showMoreContainer.style.display = 'none';
  }
}

function createHotelCard(hotel, index) {
  const hotelInfo = hotel.hotel || {};
  const name = hotelInfo.name || 'Unnamed Hotel';
  const hotelId = hotelInfo.hotelId || hotelInfo.id || (hotelInfo.code ?? '');
  const image =
    (hotelInfo.images && hotelInfo.images[0] && hotelInfo.images[0].url) ||
    'https://images.pexels.com/photos/417074/pexels-photo-417074.jpeg';
  const rating = hotelInfo.rating || 'N/A';
  const address = hotelInfo.address || hotelInfo.city || 'No address available';

  const price =
    hotel.roomTypes?.[0]?.rates?.[0]?.retailRate?.total?.[0]?.amount ??
    'Contact for price';

  return `
    <div class="col-md-4 mb-4">
      <div class="hotel-card h-100 d-flex flex-column">
        <img src="${image}" class="card-img-top" alt="${name}">
        <div class="card-body d-flex flex-column">
          <div class="d-flex justify-content-between align-items-start mb-2">
            <h5 class="card-title mb-0">${name}</h5>
            <span class="rating-badge">${rating}</span>
          </div>
          <p class="card-text">${address}</p>
          <div class="mt-auto d-flex justify-content-between align-items-center">
            <span class="price-new">
              ${typeof price === 'number' ? price.toFixed(2) : price}
            </span>
            <button 
              type="button"
              class="btn btn-gold btn-sm mt-3 book-btn"
              data-hotel-name="${encodeURIComponent(name)}"
              data-hotel-id="${hotelId}"
              data-hotel-image="${encodeURIComponent(image)}"
            >
              Book
            </button>
          </div>
        </div>
      </div>
    </div>
  `;
}

async function fetchPlaces(city) {
  const exploreContent = document.getElementById('exploreContent');
  const exploreImage = document.getElementById('exploreImage');

  try {
    const params = new URLSearchParams({
      countryCode: 'PH',
      cityName: city,
      environment: 'sandbox'
    });

    const response = await fetch(`${API_BASE}/search-places?${params.toString()}`);
    if (!response.ok) {
      throw new Error(`Server error: ${response.status}`);
    }

    const data = await response.json();
    const places = data.places || [];

    displayPlaces(places, city);
  } catch (error) {
    console.error('Error fetching places:', error);
    if (exploreContent) {
      exploreContent.innerHTML = `
        <h3>Featured in ${city.charAt(0).toUpperCase() + city.slice(1)}</h3>
        <p class="text-muted">Could not load places information.</p>
      `;
    }
  }
}

function displayPlaces(places, city) {
  const exploreContent = document.getElementById('exploreContent');
  const exploreImage = document.getElementById('exploreImage');

  if (!exploreContent || !exploreImage) return;

  if (!places || places.length === 0) {
    exploreContent.innerHTML = `
      <h3>Featured in ${city.charAt(0).toUpperCase() + city.slice(1)}</h3>
      <p>Explore attractions and landmarks</p>
    `;
    return;
  }

  const place = places[0];
  const image =
    (place.images && place.images[0]) ||
    'https://images.pexels.com/photos/4328791/pexels-photo-4328791.jpeg';

  exploreImage.style.backgroundImage = `url('${image}')`;
  exploreImage.style.backgroundSize = 'cover';
  exploreImage.style.backgroundPosition = 'center';

  exploreContent.innerHTML = `
    <h3>Featured ${place.name} – ${
      city.charAt(0).toUpperCase() + city.slice(1)
    }</h3>
    <p>${
      place.description ||
      `Explore the best attractions and landmarks in ${city}.`
    }</p>
    <button class="btn btn-dark" onclick="alert('Explore ${place.name}')">Learn More</button>
  `;
}

// Global click handler for Book buttons → booking.php
document.addEventListener('click', (e) => {
  const btn = e.target.closest('.book-btn');
  if (!btn) return;

  const hotelName = btn.dataset.hotelName;
  const hotelId = btn.dataset.hotelId;
  const hotelImage = btn.dataset.hotelImage;

  const params = new URLSearchParams({
    hotel: hotelName,
    hotel_id: hotelId,
    image: hotelImage
  });

  window.location.href = `booking.php?${params.toString()}`;
});

// Load default city on page load
window.addEventListener('load', () => {
  fetchHotels(currentCity);
  fetchPlaces(currentCity);
});
