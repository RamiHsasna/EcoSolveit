/**
 * Template Name: Landify
 * Template URL: https://bootstrapmade.com/landify-bootstrap-landing-page-template/
 * Updated: Aug 04 2025 with Bootstrap v5.3.7
 * Author: BootstrapMade.com
 * License: https://bootstrapmade.com/license/
 */

(function () {
  "use strict";

  /**
   * Apply .scrolled class to the body as the page is scrolled down
   */
  function toggleScrolled() {
    const selectBody = document.querySelector("body");
    const selectHeader = document.querySelector("#header");
    if (
      !selectHeader.classList.contains("scroll-up-sticky") &&
      !selectHeader.classList.contains("sticky-top") &&
      !selectHeader.classList.contains("fixed-top")
    )
      return;
    window.scrollY > 100
      ? selectBody.classList.add("scrolled")
      : selectBody.classList.remove("scrolled");
  }

  document.addEventListener("scroll", toggleScrolled);
  window.addEventListener("load", toggleScrolled);

  /**
   * Mobile nav toggle
   */
  const mobileNavToggleBtn = document.querySelector(".mobile-nav-toggle");

  function mobileNavToogle() {
    document.querySelector("body").classList.toggle("mobile-nav-active");
    mobileNavToggleBtn.classList.toggle("bi-list");
    mobileNavToggleBtn.classList.toggle("bi-x");
  }
  if (mobileNavToggleBtn) {
    mobileNavToggleBtn.addEventListener("click", mobileNavToogle);
  }

  /**
   * Hide mobile nav on same-page/hash links
   */
  document.querySelectorAll("#navmenu a").forEach((navmenu) => {
    navmenu.addEventListener("click", () => {
      if (document.querySelector(".mobile-nav-active")) {
        mobileNavToogle();
      }
    });
  });

  /**
   * Toggle mobile nav dropdowns
   */
  document.querySelectorAll(".navmenu .toggle-dropdown").forEach((navmenu) => {
    navmenu.addEventListener("click", function (e) {
      e.preventDefault();
      this.parentNode.classList.toggle("active");
      this.parentNode.nextElementSibling.classList.toggle("dropdown-active");
      e.stopImmediatePropagation();
    });
  });

  /**
   * Scroll top button
   */
  let scrollTop = document.querySelector(".scroll-top");

  function toggleScrollTop() {
    if (scrollTop) {
      window.scrollY > 100
        ? scrollTop.classList.add("active")
        : scrollTop.classList.remove("active");
    }
  }
  scrollTop.addEventListener("click", (e) => {
    e.preventDefault();
    window.scrollTo({
      top: 0,
      behavior: "smooth",
    });
  });

  window.addEventListener("load", toggleScrollTop);
  document.addEventListener("scroll", toggleScrollTop);

  /**
   * Animation on scroll function and init
   */
  function aosInit() {
    AOS.init({
      duration: 600,
      easing: "ease-in-out",
      once: true,
      mirror: false,
    });
  }
  window.addEventListener("load", aosInit);

  /**
   * Initiate glightbox
   */
  const glightbox = GLightbox({
    selector: ".glightbox",
  });

  /**
   * Init swiper sliders
   */
  function initSwiper() {
    document.querySelectorAll(".init-swiper").forEach(function (swiperElement) {
      let config = JSON.parse(
        swiperElement.querySelector(".swiper-config").innerHTML.trim()
      );

      if (swiperElement.classList.contains("swiper-tab")) {
        initSwiperWithCustomPagination(swiperElement, config);
      } else {
        new Swiper(swiperElement, config);
      }
    });
  }

  window.addEventListener("load", initSwiper);

  /**
   * Initiate Pure Counter
   */
  new PureCounter();

  /*
   * Pricing Toggle
   */

  const pricingContainers = document.querySelectorAll(
    ".pricing-toggle-container"
  );

  pricingContainers.forEach(function (container) {
    const pricingSwitch = container.querySelector(
      '.pricing-toggle input[type="checkbox"]'
    );
    const monthlyText = container.querySelector(".monthly");
    const yearlyText = container.querySelector(".yearly");

    pricingSwitch.addEventListener("change", function () {
      const pricingItems = container.querySelectorAll(".pricing-item");

      if (this.checked) {
        monthlyText.classList.remove("active");
        yearlyText.classList.add("active");
        pricingItems.forEach((item) => {
          item.classList.add("yearly-active");
        });
      } else {
        monthlyText.classList.add("active");
        yearlyText.classList.remove("active");
        pricingItems.forEach((item) => {
          item.classList.remove("yearly-active");
        });
      }
    });
  });

  /**
   * Frequently Asked Questions Toggle
   */
  document
    .querySelectorAll(
      ".faq-item h3, .faq-item .faq-toggle, .faq-item .faq-header"
    )
    .forEach((faqItem) => {
      faqItem.addEventListener("click", () => {
        faqItem.parentNode.classList.toggle("faq-active");
      });
    });

  /**
   * Correct scrolling position upon page load for URLs containing hash links.
   */
  window.addEventListener("load", function (e) {
    if (window.location.hash) {
      if (document.querySelector(window.location.hash)) {
        setTimeout(() => {
          let section = document.querySelector(window.location.hash);
          let scrollMarginTop = getComputedStyle(section).scrollMarginTop;
          window.scrollTo({
            top: section.offsetTop - parseInt(scrollMarginTop),
            behavior: "smooth",
          });
        }, 100);
      }
    }
  });

  /**
   * Navmenu Scrollspy
   */
  let navmenulinks = document.querySelectorAll(".navmenu a");

  function navmenuScrollspy() {
    navmenulinks.forEach((navmenulink) => {
      if (!navmenulink.hash) return;
      let section = document.querySelector(navmenulink.hash);
      if (!section) return;
      let position = window.scrollY + 200;
      if (
        position >= section.offsetTop &&
        position <= section.offsetTop + section.offsetHeight
      ) {
        document
          .querySelectorAll(".navmenu a.active")
          .forEach((link) => link.classList.remove("active"));
        navmenulink.classList.add("active");
      } else {
        navmenulink.classList.remove("active");
      }
    });
  }
  window.addEventListener("load", navmenuScrollspy);
  document.addEventListener("scroll", navmenuScrollspy);

  // SECTION NOTIFS (remplace l'ancienne – unifié "api")

  const notificationBtn = document.querySelector(".btn-notification");
  const notifBox = document.getElementById("notif-box");
  const notifList = document.getElementById("notif-list");
  const notifBadge = document.getElementById("notif-badge");
  const notifCount = document.getElementById("notif-count");
  const closeNotif = document.getElementById("close-notif");

  let userCity = "";
  let userId = "";

  async function loadUserData() {
    try {
      const response = await fetch("/EcoSolveit/api/user_data.php");
      if (!response.ok) throw new Error("Erreur user data");
      const data = await response.json();
      userCity = data.user_city || "Monastir";
      userId = data.user_id.toString() || "1";
      localStorage.setItem("userCity", userCity);
      localStorage.setItem("userId", userId);
      console.log("Debug: Ville:", userCity, "ID:", userId);
      loadNotificationCount();
    } catch (e) {
      console.error("Erreur user data:", e);
      userCity = localStorage.getItem("userCity") || "Monastir";
      userId = localStorage.getItem("userId") || "1";
      console.log("Debug (fallback): Ville:", userCity, "ID:", userId);
      loadNotificationCount();
    }
  }

  function loadNotificationCount() {
    if (!userId) return;
    const countUrl = `/EcoSolveit/api/get_notifications.php?user_id=${userId}&count_only=1`;
    console.log("Fetch count:", countUrl);
    fetch(countUrl)
      .then((res) => {
        if (!res.ok) throw new Error("HTTP " + res.status);
        return res.json();
      })
      .then((data) => {
        console.log("Count data:", data); // DEBUG
        updateBadge(data.unread_count || 0);
      })
      .catch((err) => {
        console.error("Erreur count:", err);
        updateBadge(0);
      });
  }

  function updateBadge(count) {
    if (!notifCount || !notifBadge) return;
    notifCount.textContent = count;
    notifBadge.textContent = count > 99 ? "99+" : count;
    notifBadge.style.display = count > 0 ? "block" : "none";
  }

  async function loadNotifications(silent = false) {
    if (!notifList || !userId) return;

    if (!silent) {
      notifList.innerHTML = `
      <div class="loading-notif text-center py-3">
        <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
        <small class="d-block text-muted mt-1">Chargement...</small>
      </div>
    `;
    }

    try {
      const url = `/EcoSolveit/api/get_notifications.php?user_id=${userId}`;
      console.log("Fetch notifications:", url);
      const response = await fetch(url);
      if (!response.ok) throw new Error(`HTTP ${response.status}`);
      const data = await response.json();
      const notifications = data.notifications || []; // Extraire array
      console.log("Notifs reçues:", notifications); // DEBUG : Voir la nouvelle

      if (notifications.length === 0) {
        notifList.innerHTML =
          '<p class="text-center text-muted py-3">Aucune nouvelle notification.</p>';
        updateBadge(0);
        return;
      }

      let html = "";
      let unreadCount = 0;

      notifications.forEach((notif) => {
        const isUnread = !notif.read_status; // FIX : read_status (JSON)
        if (isUnread) unreadCount++;

        html += `
        <a href="${
          notif.link || "#"
        }" class="notification-item d-flex align-items-start p-3 border-bottom ${
          isUnread ? "unread" : ""
        }" 
           style="${isUnread ? "background-color: rgba(0,123,255,0.1);" : ""}" 
           onclick="markAsRead(${notif.id}); return false;">
          <div class="notification-icon me-3">
            <i class="bi bi-bell fs-5"></i>
          </div>
          <div class="notification-content flex-grow-1">
            <h6 class="mb-1 text-truncate">${
              notif.title ||
              notif.description.substring(0, 30) ||
              "Nouvel événement"
            }</h6>
            <p class="mb-1 text-muted small text-truncate">${(
              notif.description || ""
            ).substring(0, 80)}${
          (notif.description || "").length > 80 ? "..." : ""
        }</p>
            <small class="text-muted">${
              notif.created_at || "Date inconnue"
            }</small>
          </div>
          ${
            isUnread
              ? '<span class="badge bg-primary ms-auto">Nouveau</span>'
              : ""
          }
        </a>
      `;
      });

      notifList.innerHTML = html;
      updateBadge(unreadCount);
    } catch (error) {
      console.error("Erreur fetch:", error);
      notifList.innerHTML =
        '<p class="text-center text-muted py-3">Erreur de chargement. <button class="btn btn-sm btn-outline-primary mt-2" onclick="loadNotifications()">Réessayer</button></p>';
      updateBadge(0);
    }
  }

  // Marque une notif individuelle comme lue
  async function markAsRead(id) {
    if (!userId) return;
    try {
      await fetch("/EcoSolveit/api/mark_read.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `notif_id=${id}&user_id=${userId}`,
      });
      loadNotifications(true);
    } catch (e) {
      console.error("Erreur mark:", e);
    }
  }

  // Marque TOUS les notifs comme lus
  async function markAllAsRead() {
    if (!userId) return;
    try {
      const response = await fetch("/EcoSolveit/api/mark_all_read.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `user_id=${userId}`,
      });
      if (response.ok) {
        loadNotifications(true);
        loadNotificationCount();
      }
    } catch (e) {
      console.error("Erreur mark all:", e);
      updateBadge(0);
    }
  }

  // Rendre globale
  window.markAsRead = markAsRead;

  // Toggle dropdown
  if (notificationBtn && notifBox) {
    notificationBtn.addEventListener("click", (e) => {
      e.preventDefault();
      e.stopPropagation();
      notifBox.classList.toggle("show");
      if (notifBox.classList.contains("show")) {
        loadNotifications();
        markAllAsRead();
      }
    });
  }

  if (closeNotif) {
    closeNotif.addEventListener("click", (e) => {
      e.preventDefault();
      notifBox.classList.remove("show");
    });
  }

  document.addEventListener("click", (e) => {
    if (!notifBox.contains(e.target) && !notificationBtn.contains(e.target))
      notifBox.classList.remove("show");
  });

  window.addEventListener("load", loadUserData);

  // Function to load categories for the signalement modal
  async function loadCategories() {
    console.log("Loading categories...");
    try {
      const response = await fetch("/EcoSolveit/api/get_categories.php");
      console.log("Response status:", response.status);
      const data = await response.json();
      console.log("Categories data:", data);

      if (data.success && data.categories && Array.isArray(data.categories)) {
        const categorieSelect = document.getElementById("categorie");
        if (categorieSelect) {
          console.log("Found categorie select element");
          // Clear existing options except the first one
          categorieSelect.innerHTML =
            '<option value="">Sélectionnez...</option>';

          // Add categories from the database
          data.categories.forEach((category) => {
            const option = document.createElement("option");
            option.textContent = category.category_name;
            categorieSelect.appendChild(option);
          });
          console.log(
            "Categories loaded successfully:",
            data.categories.length,
            "categories"
          );
        } else {
          console.error("Could not find categorie select element");
        }
      } else {
        console.error("Invalid data structure received:", data);
        if (data.error) {
          console.error("API error:", data.error);
        }
      }
    } catch (error) {
      console.error("Error loading categories:", error);
    }
  }

  // Set up modal event listener for categories when DOM is loaded
  document.addEventListener("DOMContentLoaded", function () {
    console.log("DOM loaded, setting up signalement modal event listener");
    const signalementModal = document.getElementById("signalementModal");
    if (signalementModal) {
      console.log("Found signalement modal, adding event listener");
      signalementModal.addEventListener("show.bs.modal", function () {
        console.log("Modal is opening, loading categories");
        loadCategories();
      });
    } else {
      console.error(
        "Could not find signalement modal element with ID: signalementModal"
      );
    }

    // Initialize authentication status
    checkAuthStatus();

    // Also check auth status when page regains focus (useful for login in another tab)
    window.addEventListener("focus", () => {
      console.log("Page focused, rechecking auth status");
      checkAuthStatus();
    });

    // Check auth status periodically (every 5 minutes)
    setInterval(checkAuthStatus, 5 * 60 * 1000);
  });
})();

// Authentication Management Functions
async function checkAuthStatus() {
  console.log("Checking authentication status...");
  try {
    const response = await fetch("/EcoSolveit/api/get_session.php");

    // Check if response is actually JSON
    const contentType = response.headers.get("content-type");
    if (!contentType || !contentType.includes("application/json")) {
      console.error("Response is not JSON:", contentType);
      console.error("Response status:", response.status);
      const responseText = await response.text();
      console.error("Response text:", responseText.substring(0, 200) + "...");
      throw new Error("Invalid response format");
    }

    const data = await response.json();
    console.log("Auth status received:", data);

    if (data.success) {
      updateAuthButton(data.logged_in, data);

      // Store user data in localStorage if logged in
      if (data.logged_in) {
        localStorage.setItem("userId", data.user_id);
        localStorage.setItem("username", data.username || "");
        localStorage.setItem("user_type", data.user_type || "");
      } else {
        // Clear localStorage if not logged in
        localStorage.removeItem("userId");
        localStorage.removeItem("username");
        localStorage.removeItem("user_type");
      }
    }
  } catch (error) {
    console.error("Error checking auth status:", error);
    // Default to logged out state on error
    updateAuthButton(false);
  }
}

function updateAuthButton(isLoggedIn, userData = {}) {
  const authBtn = document.getElementById("auth-btn");

  if (!authBtn) {
    console.error("Auth button not found");
    return;
  }

  // Remove loading state
  authBtn.classList.remove("loading");

  if (isLoggedIn) {
    // User is logged in - show logout button
    const username = userData.username;

    if (username) {
      authBtn.innerHTML = `
        <i class="bi bi-person-circle me-2"></i>
        ${username}
      `;
      authBtn.title = "Cliquer pour se déconnecter";
    } else {
      authBtn.innerHTML = `
        <i class="bi bi-box-arrow-right me-2"></i>
        Se déconnecter
      `;
    }

    authBtn.href = "#";
    authBtn.onclick = function (e) {
      e.preventDefault();
      logout();
    };

    console.log("Updated button to logout state");
  } else {
    // User is not logged in - show login button
    authBtn.innerHTML = `
      <i class="bi bi-box-arrow-in-right me-2"></i>
      Se connecter
    `;
    authBtn.href = "views/FrontOffice/login.php";
    authBtn.onclick = null;
    authBtn.title = "Cliquer pour se connecter";

    console.log("Updated button to login state");
  }
}

async function logout() {
  console.log("Logging out user...");

  const authBtn = document.getElementById("auth-btn");

  // Show loading state
  if (authBtn) {
    authBtn.classList.add("loading");
    authBtn.innerHTML = `
      <i class="bi bi-spinner-border loading-spinner me-2"></i>
      Déconnexion...
    `;
  }

  try {
    console.log("Making logout request...");

    const response = await fetch("/EcoSolveit/api/logout.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
    });

    console.log("Logout response status:", response.status);

    if (!response.ok) {
      const errorText = await response.text();
      console.error("Logout request failed:", errorText);
      throw new Error(`HTTP error! status: ${response.status} - ${errorText}`);
    }

    const data = await response.json();
    console.log("Logout response data:", data);

    if (data.success) {
      console.log("Logout successful");

      // Clear localStorage
      localStorage.removeItem("userId");
      localStorage.removeItem("username");
      localStorage.removeItem("user_type");

      // Update button state
      updateAuthButton(false);

      // Optionally show success message
      showNotification("Déconnexion réussie", "success");

      // Reload events to show public view
      if (typeof loadEvents === "function") {
        loadEvents();
      }

      // Reload the page to ensure clean state
      setTimeout(() => {
        window.location.reload();
      }, 1500);
    } else {
      console.error("Logout failed:", data.error);
      showNotification("Erreur lors de la déconnexion", "error");
      updateAuthButton(true); // Restore previous state
    }
  } catch (error) {
    console.error("Error during logout:", error);
    showNotification("Erreur lors de la déconnexion", "error");
    updateAuthButton(true); // Restore previous state
  }
}

// Simple notification function
function showNotification(message, type = "info") {
  // Create notification element
  const notification = document.createElement("div");
  notification.className = `alert alert-${
    type === "success" ? "success" : type === "error" ? "danger" : "info"
  } alert-dismissible fade show position-fixed`;
  notification.style.cssText = `
    top: 20px;
    right: 20px;
    z-index: 9999;
    min-width: 300px;
  `;
  notification.innerHTML = `
    ${message}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  `;

  document.body.appendChild(notification);

  // Auto-remove after 5 seconds
  setTimeout(() => {
    if (notification.parentNode) {
      notification.parentNode.removeChild(notification);
    }
  }, 5000);
}

// Debug function to test logout API - can be called from browser console
window.debugLogout = async function () {
  console.log("=== DEBUGGING LOGOUT API ===");

  // Test 1: Check if the logout.php file exists
  try {
    console.log("Test 1: Checking if logout.php exists...");
    const testResponse = await fetch("/EcoSolveit/api/logout.php", {
      method: "GET", // This should return "Method not allowed" but confirm the file exists
    });
    console.log("GET request status:", testResponse.status);
    const getResult = await testResponse.text();
    console.log("GET request response:", getResult);
  } catch (error) {
    console.error("Error accessing logout.php:", error);
  }

  // Test 2: Try POST request
  try {
    console.log("Test 2: Trying POST request...");
    const postResponse = await fetch("/EcoSolveit/api/logout.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
    });
    console.log("POST request status:", postResponse.status);
    const postResult = await postResponse.text();
    console.log("POST request response:", postResult);
  } catch (error) {
    console.error("Error with POST request:", error);
  }

  console.log("=== DEBUG COMPLETE ===");
};

// Debug function to test session API - can be called from browser console
window.debugSession = async function () {
  console.log("=== DEBUGGING SESSION API ===");

  // Test 1: Check current URL and paths
  console.log("Current URL:", window.location.href);
  console.log("Current pathname:", window.location.pathname);

  // Test 2: Try to access session API
  try {
    console.log("Testing session API...");
    const response = await fetch("/EcoSolveit/api/get_session.php");
    console.log("Session API status:", response.status);
    console.log("Session API headers:", [...response.headers.entries()]);

    const contentType = response.headers.get("content-type");
    console.log("Content type:", contentType);

    if (contentType && contentType.includes("application/json")) {
      const data = await response.json();
      console.log("Session API response:", data);
    } else {
      const text = await response.text();
      console.log("Session API response (text):", text.substring(0, 500));
    }
  } catch (error) {
    console.error("Error accessing session API:", error);
  }

  // Test 3: Test alternative paths
  const paths = [
    "api/get_session.php",
    "./api/get_session.php",
    "../api/get_session.php",
  ];

  for (const path of paths) {
    try {
      console.log(`Testing path: ${path}`);
      const response = await fetch(path);
      console.log(`Path ${path} - Status: ${response.status}`);
      if (response.status === 200) {
        console.log(`✅ Working path found: ${path}`);
        break;
      }
    } catch (error) {
      console.log(`❌ Path ${path} failed:`, error.message);
    }
  }

  console.log("=== SESSION DEBUG COMPLETE ===");
};
