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

  fetch("/EcoSolveit/api/get_events.php")
    .then((response) => response.json())
    .then((data) => {
      if (data.success && data.events) {
        // Take only the first 4 events
        const limitedEvents = data.events.slice(0, 4);
        displayEvents(limitedEvents);
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

  // Get max 4 events
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
                    <button class="btn btn-primary mt-3" 
                            onclick="participateInEvent('${event.id}')" 
                            id="btn-event-${event.id}">
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

  // Check participation status for each event
  checkParticipationStatus(events);

  // Initialize AOS animations for the newly added elements
  AOS.refresh();
}

function checkParticipationStatus(events) {
  // Check if user is logged in first
  fetch("/EcoSolveit/api/get_session.php")
    .then((response) => response.json())
    .then((sessionData) => {
      if (sessionData.logged_in) {
        // User is logged in, check each event
        events.forEach((event) => {
          if (!event.id.toString().startsWith("placeholder-")) {
            fetch(
              `/EcoSolveit/api/check_participation.php?event_id=${event.id}`
            )
              .then((response) => response.json())
              .then((data) => {
                if (data.success && data.is_participating) {
                  updateParticipationButton(event.id, true);
                }
              })
              .catch((error) => {
                console.error(
                  "Error checking participation for event:",
                  event.id,
                  error
                );
              });
          }
        });
      }
    })
    .catch((error) => {
      console.error("Error checking session:", error);
    });
}

function updateParticipationButton(eventId, isParticipating) {
  const button = document.getElementById(`btn-event-${eventId}`);
  if (button && isParticipating) {
    button.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i>Inscrit';
    button.classList.remove("btn-primary");
    button.classList.add("btn-success");
    button.disabled = true;
  }
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
  // Check if user is logged in first
  fetch("/EcoSolveit/api/get_session.php")
    .then((response) => response.json())
    .then((sessionData) => {
      if (!sessionData.logged_in) {
        // User not logged in, redirect to login page
        alert("Vous devez être connecté pour participer à un événement.");
        // You can redirect to login page here
        // window.location.href = 'views/FrontOffice/login.php';
        return;
      }

      // User is logged in, proceed with participation
      submitParticipation(eventId);
    })
    .catch((error) => {
      console.error("Error checking session:", error);
      alert("Une erreur est survenue. Veuillez réessayer.");
    });
}

function submitParticipation(eventId) {
  // Show loading state
  const button = document.querySelector(
    `button[onclick="participateInEvent('${eventId}')"]`
  );
  const originalText = button.innerHTML;
  button.disabled = true;
  button.innerHTML =
    '<i class="spinner-border spinner-border-sm me-2"></i>Inscription...';

  fetch("/EcoSolveit/api/participate_event.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      event_id: eventId,
    }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        // Success - update button state
        button.innerHTML =
          '<i class="bi bi-check-circle-fill me-2"></i>Inscrit';
        button.classList.remove("btn-primary");
        button.classList.add("btn-success");

        // Show success message
        showNotification("Inscription réussie !", "success");
      } else {
        // Handle errors
        if (data.require_login) {
          alert("Vous devez être connecté pour participer à un événement.");
          // Optionally redirect to login
          // window.location.href = 'views/FrontOffice/login.php';
        } else {
          // Show more detailed error for debugging
          let errorMessage =
            data.error || "Une erreur est survenue lors de l'inscription.";
          if (data.debug) {
            errorMessage += "\n\nDétail technique: " + data.debug;
          }
          alert(errorMessage);
        }

        // Restore button state
        button.disabled = false;
        button.innerHTML = originalText;
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      alert("Une erreur réseau est survenue. Veuillez réessayer.");

      // Restore button state
      button.disabled = false;
      button.innerHTML = originalText;
    });
}

function showNotification(message, type = "info") {
  // Create notification element
  const notification = document.createElement("div");
  notification.className = `alert alert-${
    type === "success" ? "success" : "info"
  } alert-dismissible fade show position-fixed`;
  notification.style.cssText =
    "top: 20px; right: 20px; z-index: 9999; max-width: 350px;";
  notification.innerHTML = `
    ${message}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  `;

  // Add to body
  document.body.appendChild(notification);

  // Auto remove after 5 seconds
  setTimeout(() => {
    if (notification.parentNode) {
      notification.remove();
    }
  }, 5000);
}
