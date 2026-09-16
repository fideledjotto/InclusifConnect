(() => {
  "use strict";

  const CATEGORIES = [
    { id: "moteur", label: "Handicap moteur", img: "images/categories/theme-moteur.jpg", desc: "Mobilité, aides techniques, aménagements." },
    { id: "sensoriel", label: "Handicap sensoriel", img: "images/categories/theme-sensoriel.jpg", desc: "Vision, audition, communication adaptée." },
    { id: "cognitif", label: "Handicap cognitif", img: "images/categories/theme-cognitif.jpg", desc: "Apprentissage, troubles DYS, autisme." },
    { id: "numerique", label: "Accessibilité numérique", img: "images/categories/theme-numerique.jpg", desc: "Sites web, applications, RGAA." },
    { id: "batiments", label: "Accessibilité des bâtiments", img: "images/categories/theme-batiments.jpg", desc: "ERP, voirie, normes de construction." },
    { id: "loi", label: "Textes de loi", img: "images/categories/theme-loi.jpg", desc: "Lois, décrets et droits des personnes." },
    { id: "apprentissage", label: "Outils d'apprentissage", img: "images/categories/theme-apprentissage.jpg", desc: "Formations, tutoriels, supports pédagogiques." }
  ];

  const DOCS = [
    { id: 1, title: "Guide pratique de l'accessibilité numérique RGAA", type: "Guide", cat: "numerique", author: "Équipe Inclusif Connect", date: "2026-06-12", pop: 98, kw: ["RGAA", "web", "normes"], url: "resources/documents/guide-rgaa.txt", img: "images/categories/theme-numerique.jpg" },
    { id: 2, title: "Comprendre les troubles DYS en milieu scolaire", type: "Document", cat: "cognitif", author: "C. Amoussou", date: "2026-05-28", pop: 76, kw: ["dys", "école", "cognitif"], url: "resources/pdf/dys-scolaire.pdf", img: "images/categories/theme-cognitif.jpg" },
    { id: 3, title: "Vidéo : se déplacer en fauteuil roulant en ville", type: "Vidéo", cat: "moteur", author: "Réseau Mobilité+", date: "2026-04-15", pop: 64, kw: ["mobilité", "fauteuil", "ville"], url: "resources/video/fauteuil-ville.mp4", img: "images/categories/theme-moteur.jpg" },
    { id: 4, title: "Initiation à la langue des signes (podcast)", type: "Audio", cat: "sensoriel", author: "Association Signes & Voix", date: "2026-07-02", pop: 53, kw: ["LSF", "audition"], url: "resources/audio/lsf-podcast.wav", img: "images/categories/theme-sensoriel.jpg" },
    { id: 5, title: "Loi sur l'accessibilité des ERP : ce qu'il faut savoir", type: "Document", cat: "loi", author: "Ministère des Solidarités", date: "2026-03-20", pop: 120, kw: ["loi", "ERP", "droit"], url: "resources/pdf/loi-erp.pdf", img: "images/categories/theme-loi.jpg" },
    { id: 6, title: "Aménager un bâtiment accessible : guide technique", type: "Guide", cat: "batiments", author: "Ordre des architectes", date: "2026-02-18", pop: 41, kw: ["bâtiment", "normes", "travaux"], url: "resources/pdf/guide-batiment.pdf", img: "images/categories/theme-batiments.jpg" },
    { id: 7, title: "Créer des documents accessibles avec Word", type: "Guide", cat: "numerique", author: "Équipe Inclusif Connect", date: "2026-07-20", pop: 88, kw: ["bureautique", "word", "numérique"], url: "resources/pdf/word-accessible.pdf", img: "images/categories/theme-numerique.jpg" },
    { id: 8, title: "Vidéo pédagogique : le braille au quotidien", type: "Vidéo", cat: "sensoriel", author: "Institut Louis Braille", date: "2026-01-30", pop: 59, kw: ["braille", "vision"], url: "resources/video/braille-quotidien.mp4", img: "images/categories/theme-sensoriel-braille.jpg" },
    { id: 9, title: "Outils numériques pour l'apprentissage inclusif", type: "Document", cat: "apprentissage", author: "F. Konaté", date: "2026-06-05", pop: 70, kw: ["éducation", "outils", "école"], url: "resources/pdf/outils-apprentissage.pdf", img: "images/categories/theme-apprentissage.jpg" },
    { id: 10, title: "Podcast : témoignages sur le handicap invisible", type: "Audio", cat: "cognitif", author: "Radio Inclusion", date: "2026-05-11", pop: 34, kw: ["témoignage", "invisible"], url: "resources/audio/temoignages-invisible.wav", img: "images/categories/theme-cognitif.jpg" },
    { id: 11, title: "Vos droits face à l'employeur : synthèse juridique", type: "Document", cat: "loi", author: "Défenseur des droits", date: "2026-04-02", pop: 112, kw: ["emploi", "droit", "loi"], url: "resources/pdf/droits-emploi.pdf", img: "images/categories/theme-loi.jpg" },
    { id: 12, title: "Aides techniques pour la mobilité réduite", type: "Guide", cat: "moteur", author: "CNSA", date: "2026-03-08", pop: 47, url: "resources/pdf/aides-mobilite.pdf", img: "images/categories/theme-moteur.jpg" }
  ];

  const catLabel = (id) => (CATEGORIES.find((c) => c.id === id) || {}).label || id;

  const savedFavs = JSON.parse(localStorage.getItem("ic-favs") || "[]");
  const state = {
    activeCat: null,
    activeType: null,
    query: "",
    sort: "date",
    favorites: new Set(savedFavs)
  };

  const live = document.getElementById("liveRegion");
  function announce(msg) {
    live.textContent = "";
    setTimeout(() => { live.textContent = msg; }, 30);
  }
  function formatDate(iso) {
    return new Date(iso).toLocaleDateString("fr-FR", { day: "numeric", month: "long", year: "numeric" });
  }
  function persistFavs() {
    localStorage.setItem("ic-favs", JSON.stringify([...state.favorites]));
    document.getElementById("favCount").textContent = state.favorites.size;
  }

  const catGrid = document.getElementById("catGrid");
  CATEGORIES.forEach((cat) => {
    const btn = document.createElement("button");
    btn.className = "cat-card";
    btn.type = "button";
    btn.setAttribute("aria-pressed", "false");
    btn.innerHTML = `<img src="${cat.img}" alt=""><span class="cat-copy"><h3>${cat.label}</h3><p>${cat.desc}</p></span>`;
    btn.addEventListener("click", () => {
      state.activeCat = state.activeCat === cat.id ? null : cat.id;
      renderAll();
      document.getElementById("ressources").scrollIntoView({ behavior: "smooth", block: "start" });
    });
    catGrid.appendChild(btn);
  });

  const TYPES = ["Document", "Vidéo", "Audio", "Guide"];
  const typeChips = document.getElementById("typeChips");
  TYPES.forEach((type) => {
    const chip = document.createElement("button");
    chip.className = "chip";
    chip.type = "button";
    chip.textContent = type;
    chip.setAttribute("aria-pressed", "false");
    chip.addEventListener("click", () => {
      state.activeType = state.activeType === type ? null : type;
      renderAll();
    });
    typeChips.appendChild(chip);
  });

  document.getElementById("heroSearchForm").addEventListener("submit", (e) => {
    e.preventDefault();
    state.query = document.getElementById("searchInput").value.trim().toLowerCase();
    state.activeType = document.getElementById("searchFormat").value || null;
    renderAll();
    document.getElementById("ressources").scrollIntoView({ behavior: "smooth", block: "start" });
  });
  document.getElementById("sortSelect").addEventListener("change", (e) => {
    state.sort = e.target.value;
    renderAll();
  });
  document.getElementById("btnReset").addEventListener("click", () => {
    state.activeCat = null;
    state.activeType = null;
    state.query = "";
    document.getElementById("searchInput").value = "";
    document.getElementById("searchFormat").value = "";
    renderAll();
    announce("Filtres réinitialisés.");
  });

  const docGrid = document.getElementById("docGrid");
  const emptyState = document.getElementById("emptyState");
  const resultsCount = document.getElementById("resultsCount");

  function getFiltered() {
    let list = DOCS.filter((d) => {
      if (state.activeCat && d.cat !== state.activeCat) return false;
      if (state.activeType && d.type !== state.activeType) return false;
      if (state.query) {
        const hay = `${d.title} ${d.author} ${d.kw.join(" ")} ${catLabel(d.cat)}`.toLowerCase();
        if (!hay.includes(state.query)) return false;
      }
      return true;
    });
    if (state.sort === "date") list.sort((a, b) => new Date(b.date) - new Date(a.date));
    if (state.sort === "popularite") list.sort((a, b) => b.pop - a.pop);
    if (state.sort === "type") list.sort((a, b) => a.type.localeCompare(b.type));
    return list;
  }

  function renderDocs() {
    const list = getFiltered();
    docGrid.innerHTML = "";
    resultsCount.textContent = `${list.length} ressource${list.length > 1 ? "s" : ""} trouvée${list.length > 1 ? "s" : ""}`;
    emptyState.hidden = list.length !== 0;

    list.forEach((doc) => {
      const isFav = state.favorites.has(doc.id);
      const card = document.createElement("article");
      card.className = "doc-card";
      card.innerHTML = `
        <div class="doc-cover">
          <img src="${doc.img}" alt="">
          <span class="type-badge">${doc.type}</span>
          <button class="fav-btn" type="button" aria-pressed="${isFav}" aria-label="${isFav ? "Retirer des favoris" : "Ajouter aux favoris"} : ${doc.title}">${isFav ? "♥" : "♡"}</button>
        </div>
        <div class="doc-card-body">
          <h3>${doc.title}</h3>
          <p class="doc-meta">${catLabel(doc.cat)} · ${doc.author} · ${formatDate(doc.date)}</p>
          <div class="doc-keywords doc-keywords-empty" aria-hidden="true"></div>
          <div class="doc-card-actions">
            <button class="icon-btn btn-open" type="button">Fiche</button>
            <button class="icon-btn btn-download" type="button">Télécharger</button>
            <button class="icon-btn btn-share" type="button">Partager</button>
          </div>
        </div>`;
      card.querySelector(".fav-btn").addEventListener("click", () => toggleFavorite(doc.id));
      card.querySelector(".btn-open").addEventListener("click", () => openDocModal(doc));
      card.querySelector(".btn-download").addEventListener("click", () => downloadDocument(doc));
      card.querySelector(".btn-share").addEventListener("click", () => openShareModal(doc));
      docGrid.appendChild(card);
    });
  }

  function toggleFavorite(id) {
    if (state.favorites.has(id)) state.favorites.delete(id);
    else state.favorites.add(id);
    persistFavs();
    renderDocs();
  }

  function syncFilterUI() {
    [...catGrid.children].forEach((btn, i) => btn.setAttribute("aria-pressed", CATEGORIES[i].id === state.activeCat));
    [...typeChips.children].forEach((chip) => chip.setAttribute("aria-pressed", chip.textContent === state.activeType));
  }
  function renderAll() {
    syncFilterUI();
    renderDocs();
  }

  document.getElementById("statDocs").textContent = DOCS.length;
  persistFavs();
  renderAll();

  const overlay = document.getElementById("modalOverlay");
  const modalContent = document.getElementById("modalContent");
  let lastFocused = null;
  function openModal(html) {
    modalContent.innerHTML = html;
    overlay.classList.add("open");
    lastFocused = document.activeElement;
    document.getElementById("modalClose").focus();
    document.addEventListener("keydown", trapEsc);
  }
  function closeModal() {
    overlay.classList.remove("open");
    document.removeEventListener("keydown", trapEsc);
    if (lastFocused) lastFocused.focus();
  }
  function trapEsc(e) { if (e.key === "Escape") closeModal(); }
  document.getElementById("modalClose").addEventListener("click", closeModal);
  overlay.addEventListener("click", (e) => { if (e.target === overlay) closeModal(); });

  function openDocModal(doc) {
    let playerHtml = "";
    if (doc.type === "Vidéo") {
      playerHtml = `<video width="100%" controls style="border-radius:8px;background:#000;margin:8px 0;"><source src="${doc.url}" type="video/mp4">Vidéo non disponible dans cette démo.</video>`;
    } else if (doc.type === "Audio") {
      playerHtml = `<audio controls style="width:100%;margin:8px 0;"><source src="${doc.url}" type="audio/wav">Audio non disponible dans cette démo.</audio>`;
    }
    openModal(`
      <h2 id="modalTitle">${doc.title}</h2>
      <img class="cover" src="${doc.img}" alt="">
      <p style="color:var(--ink-soft);">${catLabel(doc.cat)} · ${doc.type}</p>
      ${playerHtml}
      <p><strong>Auteur :</strong> ${doc.author}</p>
      <p><strong>Date :</strong> ${formatDate(doc.date)}</p>
      <p class="doc-keywords-space" aria-hidden="true"></p>
      <div style="display:flex;gap:10px;margin-top:16px;flex-wrap:wrap;">
        <button class="btn btn-primary" id="docDownloadBtn" type="button">Télécharger</button>
        <button class="btn btn-ghost" id="docShareBtn" type="button">Partager</button>
      </div>`);
    document.getElementById("docDownloadBtn").addEventListener("click", () => downloadDocument(doc));
    document.getElementById("docShareBtn").addEventListener("click", () => openShareModal(doc));
  }

  function openShareModal(doc) {
    const baseUrl = window.location.origin && window.location.origin !== "null" ? window.location.origin : window.location.href.split("?")[0];
    const docUrl = `${baseUrl}${baseUrl.endsWith("/") ? "" : "/"}?doc=${doc.id}`;
    const docText = `${doc.title} - Inclusif Connect`;
    openModal(`
      <h2 id="modalTitle">Partager</h2>
      <p style="color:var(--ink-soft);">${doc.title}</p>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin:16px 0;">
        <button class="share-btn whatsapp-btn" id="shareWhatsApp" type="button">WhatsApp</button>
        <button class="share-btn facebook-btn" id="shareFacebook" type="button">Facebook</button>
        <button class="share-btn twitter-btn" id="shareTwitter" type="button">X / Twitter</button>
        <button class="share-btn email-btn" id="shareEmail" type="button">Email</button>
        <button class="share-btn copy-btn" id="copyLink" type="button">Copier le lien</button>
      </div>
      <p class="form-note"><code>${docUrl}</code></p>`);
    document.getElementById("shareWhatsApp").onclick = () => window.open(`https://wa.me/?text=${encodeURIComponent(`${docText} ${docUrl}`)}`, "_blank");
    document.getElementById("shareFacebook").onclick = () => window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(docUrl)}`, "_blank");
    document.getElementById("shareTwitter").onclick = () => window.open(`https://twitter.com/intent/tweet?url=${encodeURIComponent(docUrl)}&text=${encodeURIComponent(docText)}`, "_blank");
    document.getElementById("shareEmail").onclick = () => { window.location.href = `mailto:?subject=${encodeURIComponent(doc.title)}&body=${encodeURIComponent(`${docText} ${docUrl}`)}`; };
    document.getElementById("copyLink").onclick = () => {
      navigator.clipboard.writeText(docUrl).then(() => announce("Lien copié.")).catch(() => announce("Copie impossible."));
    };
  }

  function downloadDocument(doc) {
    if (!doc.url) {
      announce("Fichier indisponible.");
      return;
    }
    const link = document.createElement("a");
    link.href = doc.url;
    link.download = "";
    document.body.appendChild(link);
    link.click();
    link.remove();
    announce(`Téléchargement de « ${doc.title} » lancé.`);
  }

  function bindDemoForm(form, success) {
    form.addEventListener("submit", (e) => {
      e.preventDefault();
      if (!form.reportValidity()) return;
      announce(success);
      form.reset();
      closeModal();
    });
  }

  document.getElementById("btnAccount").addEventListener("click", () => {
    openModal(`
      <h2 id="modalTitle">Créer un compte</h2>
      <p style="color:var(--ink-soft);">Sauvegardez vos favoris et recevez les nouveautés.</p>
      <form class="account-form" id="accountForm">
        <label>Nom complet<input type="text" name="name" required></label>
        <label>Adresse e-mail<input type="email" name="email" required></label>
        <label>Mot de passe<input type="password" name="password" required minlength="8"></label>
        <button class="btn btn-primary" type="submit" style="justify-content:center;">Créer mon compte</button>
      </form>`);
    bindDemoForm(document.getElementById("accountForm"), "Compte créé. Bienvenue sur Inclusif Connect.");
  });

  document.getElementById("btnPropose").addEventListener("click", () => {
    openModal(`
      <h2 id="modalTitle">Proposer un document</h2>
      <p style="color:var(--ink-soft);">Chaque ressource est vérifiée avant publication.</p>
      <form class="propose-form" id="proposeForm">
        <label>Titre<input type="text" required></label>
        <label>Thématique<select>${CATEGORIES.map((c) => `<option>${c.label}</option>`).join("")}</select></label>
        <label>Lien ou fichier<input type="text" required placeholder="URL ou nom du fichier"></label>
        <button class="btn btn-primary" type="submit" style="justify-content:center;">Envoyer pour vérification</button>
      </form>`);
    bindDemoForm(document.getElementById("proposeForm"), "Proposition envoyée. Merci pour votre contribution.");
  });

  function showFavorites() {
    const favDocs = DOCS.filter((d) => state.favorites.has(d.id));
    openModal(`
      <h2 id="modalTitle">Mes favoris</h2>
      ${favDocs.length === 0
        ? `<p style="color:var(--ink-soft);">Aucun favori pour le moment. Cliquez sur le cœur d'une ressource.</p>`
        : `<ul style="list-style:none;padding:0;display:flex;flex-direction:column;gap:12px;">
            ${favDocs.map((d) => `<li style="display:flex;gap:12px;align-items:center;border:1px solid var(--violet-100);border-radius:10px;padding:10px;"><img src="${d.img}" alt="" style="width:72px;height:56px;object-fit:cover;border-radius:8px;"><div><strong>${d.title}</strong><br><span style="color:var(--ink-soft);font-size:.85rem;">${catLabel(d.cat)}</span></div></li>`).join("")}
          </ul>`}`);
  }
  document.getElementById("btnFavView").addEventListener("click", showFavorites);
  document.getElementById("footerFav").addEventListener("click", (e) => { e.preventDefault(); showFavorites(); });
  document.getElementById("footerLogin").addEventListener("click", (e) => { e.preventDefault(); document.getElementById("btnAccount").click(); });

  document.getElementById("newsletterForm").addEventListener("submit", (e) => {
    e.preventDefault();
    if (!e.target.reportValidity()) return;
    announce("Inscription à la lettre d'information confirmée.");
    e.target.reset();
  });
  document.getElementById("contactForm").addEventListener("submit", (e) => {
    e.preventDefault();
    if (!e.target.reportValidity()) return;
    announce("Message envoyé. L'équipe vous répond sous 48 heures.");
    e.target.reset();
  });

  function openLegal(kind) {
    const pages = {
      a11y: `<div class="legal-block"><h2 id="modalTitle">Déclaration d'accessibilité</h2><p>Inclusif Connect vise la conformité RGAA / WCAG 2.2 niveau AA : navigation clavier, contrastes, textes alternatifs, lecture vocale, agrandissement et espacement du texte. Signalez un obstacle via le formulaire d'aide.</p></div>`,
      legal: `<div class="legal-block"><h2 id="modalTitle">Mentions légales</h2><p>Plateforme de démonstration Inclusif Connect. Les contenus illustratifs servent à présenter le catalogue de ressources sur le handicap et l'accessibilité. Données de formulaire conservées uniquement pour le traitement de votre demande (RGPD).</p></div>`
    };
    openModal(pages[kind]);
  }
  document.getElementById("linkA11y").addEventListener("click", (e) => { e.preventDefault(); openLegal("a11y"); });
  document.getElementById("linkLegal").addEventListener("click", (e) => { e.preventDefault(); openLegal("legal"); });
  document.getElementById("linkContact").addEventListener("click", (e) => {
    e.preventDefault();
    document.getElementById("aide").scrollIntoView({ behavior: "smooth" });
  });
  document.getElementById("linkNews").addEventListener("click", (e) => {
    e.preventDefault();
    document.getElementById("newsletterEmail").focus();
  });

  const menuToggle = document.getElementById("menuToggle");
  const mainNav = document.getElementById("mainNav");
  menuToggle.addEventListener("click", () => {
    const open = mainNav.classList.toggle("is-open");
    menuToggle.setAttribute("aria-expanded", open);
    menuToggle.setAttribute("aria-label", open ? "Fermer le menu" : "Ouvrir le menu");
  });
  mainNav.querySelectorAll("a").forEach((a) => a.addEventListener("click", () => {
    mainNav.classList.remove("is-open");
    menuToggle.setAttribute("aria-expanded", "false");
  }));

  let stepVal = Number(localStorage.getItem("ic-font") || 1);
  function applyFont() {
    document.documentElement.style.setProperty("--step", stepVal.toFixed(2));
    localStorage.setItem("ic-font", String(stepVal));
  }
  applyFont();
  document.getElementById("btnFontPlus").onclick = () => { stepVal = Math.min(1.5, stepVal + 0.1); applyFont(); announce("Taille du texte augmentée."); };
  document.getElementById("btnFontMinus").onclick = () => { stepVal = Math.max(0.85, stepVal - 0.1); applyFont(); announce("Taille du texte réduite."); };
  document.getElementById("btnFontReset").onclick = () => { stepVal = 1; applyFont(); announce("Taille du texte réinitialisée."); };

  const btnContrast = document.getElementById("btnContrast");
  if (localStorage.getItem("ic-contrast") === "1") {
    document.body.classList.add("contrast-high");
    btnContrast.setAttribute("aria-pressed", "true");
  }
  btnContrast.onclick = () => {
    const on = document.body.classList.toggle("contrast-high");
    btnContrast.setAttribute("aria-pressed", on);
    localStorage.setItem("ic-contrast", on ? "1" : "0");
    announce(on ? "Contraste élevé activé." : "Contraste élevé désactivé.");
  };

  const btnSpacing = document.getElementById("btnSpacing");
  if (localStorage.getItem("ic-space") === "1") {
    btnSpacing.setAttribute("aria-pressed", "true");
    document.documentElement.style.setProperty("--tracking", "0.03em");
  }
  btnSpacing.onclick = () => {
    const on = btnSpacing.getAttribute("aria-pressed") !== "true";
    btnSpacing.setAttribute("aria-pressed", on);
    document.documentElement.style.setProperty("--tracking", on ? "0.03em" : "0");
    localStorage.setItem("ic-space", on ? "1" : "0");
    announce(on ? "Espacement du texte augmenté." : "Espacement du texte réinitialisé.");
  };

  const btnReadAloud = document.getElementById("btnReadAloud");
  function stopReading() {
    if (window.speechSynthesis) window.speechSynthesis.cancel();
    btnReadAloud.setAttribute("aria-pressed", "false");
    btnReadAloud.textContent = "Lecture";
  }
  btnReadAloud.onclick = () => {
    if (btnReadAloud.getAttribute("aria-pressed") === "true") {
      stopReading();
      announce("Lecture arrêtée.");
      return;
    }
    if (!window.speechSynthesis) {
      announce("Lecture vocale indisponible.");
      return;
    }
    const utter = new SpeechSynthesisUtterance(document.getElementById("contenu").innerText.trim());
    utter.lang = "fr-FR";
    utter.onend = () => { btnReadAloud.setAttribute("aria-pressed", "false"); btnReadAloud.textContent = "Lecture"; };
    window.speechSynthesis.speak(utter);
    btnReadAloud.setAttribute("aria-pressed", "true");
    btnReadAloud.textContent = "Arrêter";
    announce("Lecture lancée.");
  };

  const params = new URLSearchParams(window.location.search);
  const deep = Number(params.get("doc"));
  if (deep) {
    const found = DOCS.find((d) => d.id === deep);
    if (found) openDocModal(found);
  }
})();
