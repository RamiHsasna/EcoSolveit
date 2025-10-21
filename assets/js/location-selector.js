class LocationSelector {
  constructor(countrySelectId, citySelectId, apiKey) {
    this.countrySelect = document.getElementById(countrySelectId);
    this.citySelect = document.getElementById(citySelectId);
    this.apiKey = apiKey;
    this.baseUrl = "https://api.countrystatecity.in/v1";

    this.init();
  }

  async init() {
    await this.loadCountries();
    this.setupEventListeners();
  }

  async loadCountries() {
    try {
      console.log(
        "Loading countries with API key:",
        this.apiKey.substring(0, 10) + "..."
      );

      // Add loading state
      this.countrySelect.classList.add("loading");
      this.countrySelect.disabled = true;

      const response = await fetch(`${this.baseUrl}/countries`, {
        headers: {
          "X-CSCAPI-KEY": this.apiKey,
        },
      });

      if (!response.ok) {
        console.error("API Response status:", response.status);
        throw new Error(`Failed to load countries: ${response.status}`);
      }

      const countries = await response.json();
      console.log("Countries loaded:", countries.length);

      // Remove loading state
      this.countrySelect.classList.remove("loading");
      this.countrySelect.disabled = false;

      // Clear existing options except the first
      this.countrySelect.innerHTML =
        '<option value="">Sélectionnez un pays</option>';

      // Add countries to dropdown
      countries.forEach((country) => {
        const option = document.createElement("option");
        option.value = country.iso2; // Use ISO2 code as value
        option.textContent = `${country.emoji} ${country.name}`;
        option.title = country.name; // Tooltip for long names
        this.countrySelect.appendChild(option);
      });

      console.log("Countries populated successfully");
    } catch (error) {
      console.error("Error loading countries:", error);
      this.countrySelect.classList.remove("loading");
      this.countrySelect.disabled = false;
      this.showError("Erreur lors du chargement des pays: " + error.message);
    }
  }

  async loadCities(countryIso2) {
    try {
      this.citySelect.disabled = true;
      this.citySelect.classList.add("loading");
      this.citySelect.innerHTML = '<option value="">Chargement...</option>';

      const response = await fetch(
        `${this.baseUrl}/countries/${countryIso2}/cities`,
        {
          headers: {
            "X-CSCAPI-KEY": this.apiKey,
          },
        }
      );

      if (!response.ok) throw new Error("Failed to load cities");

      const cities = await response.json();

      // Remove loading state
      this.citySelect.classList.remove("loading");

      // Clear existing options
      this.citySelect.innerHTML =
        '<option value="">Sélectionnez une ville</option>';

      // Add cities to dropdown
      cities.forEach((city) => {
        const option = document.createElement("option");
        option.value = city.name;
        option.textContent = city.name;
        option.title = city.name; // Tooltip for long names
        this.citySelect.appendChild(option);
      });

      this.citySelect.disabled = false;
    } catch (error) {
      console.error("Error loading cities:", error);
      this.citySelect.classList.remove("loading");
      this.citySelect.innerHTML =
        '<option value="">Erreur de chargement</option>';
      this.showError("Erreur lors du chargement des villes");
    }
  }

  setupEventListeners() {
    this.countrySelect.addEventListener("change", (e) => {
      const selectedCountry = e.target.value;

      if (selectedCountry) {
        this.loadCities(selectedCountry);
      } else {
        // Reset city dropdown
        this.citySelect.innerHTML =
          '<option value="">Sélectionnez d\'abord un pays</option>';
        this.citySelect.disabled = true;
      }
    });
  }

  showError(message) {
    // Simple error display - you can enhance this
    alert(message);
  }
}

// Initialize selectors when DOM is loaded
document.addEventListener("DOMContentLoaded", function () {
  console.log("DOM loaded, initializing location selectors");
  initializeLocationSelectors();
});

// Also initialize when modal is shown (for Bootstrap modals)
document.addEventListener("shown.bs.modal", function (event) {
  console.log("Modal shown, checking for location selectors");
  initializeLocationSelectors();
});

function initializeLocationSelectors() {
  // Get API key - this will be different for each page
  let apiKey;

  // For PHP pages (Authentification), get from server-side
  if (typeof CSC_API_KEY !== "undefined") {
    apiKey = CSC_API_KEY;
    console.log("Using PHP API key");
  } else {
    // For static HTML pages, use the API key from config
    apiKey = "ZTFSZnU2UTVBNGZkYlpzam4wNVdSalpsbUVQMmZFeDJmcG91bVFicg==";
    console.log("Using hardcoded API key");
  }

  console.log("API key available:", apiKey ? "Yes" : "No");

  // Initialize for forms that have both pays and ville selects
  if (document.getElementById("pays") && document.getElementById("ville")) {
    console.log("Initializing location selector for pays/ville form");
    new LocationSelector("pays", "ville", apiKey);
  } else {
    console.log("pays or ville elements not found");
  }
}
