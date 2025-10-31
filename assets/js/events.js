document.addEventListener("DOMContentLoaded", function () {
  loadEvents();
});

function loadEvents() {
  const container = document.getElementById("opportunities-container");

  // Add loading indicator
  container.innerHTML = `
        <div class="text-center" id="loading-events">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Chargement...</span>
            </div>
            <p class="mt-2">Chargement des opportunités...</p>
        </div>
    `;

  fetch("api/get_events.php")
    .then((response) => response.json())
    .then((data) => {
      if (data.success && data.events) {
        displayEvents(data.events);
      } else {
        throw new Error(data.error || "Failed to load events");
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      container.innerHTML = `
                <div class="alert alert-warning" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Une erreur est survenue lors du chargement des opportunités.
                </div>
            `;
    });
}

function displayEvents(events) {
  const container = document.getElementById("opportunities-container");

  if (!events.length) {
    container.innerHTML = `
            <div class="alert alert-info" role="alert">
                <i class="bi bi-info-circle me-2"></i>
                Aucune opportunité n'est disponible pour le moment.
            </div>
        `;
    return;
  }

  // Get at least 4 events
  const minEvents = 4;
  while (events.length < minEvents) {
    const lastEvent = events[events.length - 1];
    events.push({
      ...lastEvent,
      id: "placeholder-" + events.length,
      event_name: "Nouvelle opportunité à venir",
      description:
        "De nouvelles opportunités seront bientôt disponibles. Restez à l'écoute !",
      ville: "À déterminer",
      event_date: new Date(
        Date.now() + events.length * 7 * 24 * 60 * 60 * 1000
      ).toISOString(), // Add weeks
    });
  }

  const eventsHTML = events
    .map(
      (event) => `
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card h-100" data-aos="fade-up">
                <div class="card-body d-flex flex-column">
                    <div class="card-category mb-2">
                        <span class="badge bg-primary">
                            ${escapeHtml(event.category_name || "Autre")}
                        </span>
                    </div>
                    <h5 class="card-title mb-3">${escapeHtml(
                      event.event_name
                    )}</h5>
                    <p class="card-text flex-grow-1">${escapeHtml(
                      event.description || ""
                    )}</p>
                    <div class="event-details mt-3">
                        <div class="location mb-2">
                            <i class="bi bi-geo-alt-fill"></i>
                            ${escapeHtml(
                              event.ville +
                                (event.pays ? ", " + event.pays : "")
                            )}
                        </div>
                        <div class="date mb-2">
                            <i class="bi bi-calendar-event-fill"></i>
                            ${formatDate(event.event_date)}
                        </div>
                    </div>
                    <button class="btn btn-primary mt-3" onclick="participateInEvent('${
                      event.id
                    }')">
                        <i class="bi bi-person-plus-fill me-2"></i>Participer
                    </button>
                </div>
            </div>
        </div>
    `
    )
    .join("");

  container.innerHTML = `
        <div class="row">
            ${eventsHTML}
        </div>
        <div class="text-center mt-4">
            <a href="views/FrontOffice/EcoEvent.php" class="btn btn-outline-primary">
                <i class="bi bi-eye-fill me-2"></i>Voir toutes les opportunités
            </a>
        </div>
    `;

  // Initialize AOS animations for the newly added elements
  AOS.refresh();
}

function escapeHtml(unsafe) {
  if (!unsafe) return "";
  return unsafe
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");
}

function formatDate(dateString) {
  if (!dateString) return "Date à définir";
  const date = new Date(dateString);
  return date.toLocaleDateString("fr-FR", {
    year: "numeric",
    month: "long",
    day: "numeric",
  });
}

function participateInEvent(eventId) {
  // TODO: Implement participation logic
  alert(
    "Fonctionnalité de participation en cours de développement. Event ID: " +
      eventId
  );
}
