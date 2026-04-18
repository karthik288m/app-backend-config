<?php

declare(strict_types=1);

define('ADMIN_LOGIN_URL', 'admin/login.php');
require __DIR__ . '/admin/auth.php';
require_login();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Backend JSON admin</title>
  <style>
    :root {
      --bg: #0f1419;
      --panel: #1a2332;
      --border: #2d3a4d;
      --text: #e7edf4;
      --muted: #8b9bb0;
      --accent: #4d9fff;
      --danger: #f07178;
      --ok: #7fd962;
    }
    * { box-sizing: border-box; }
    body {
      margin: 0;
      font-family: system-ui, -apple-system, Segoe UI, Roboto, sans-serif;
      background: var(--bg);
      color: var(--text);
      line-height: 1.45;
      min-height: 100vh;
    }
    header {
      padding: 1rem 1.25rem;
      border-bottom: 1px solid var(--border);
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      gap: 0.75rem 1.25rem;
      background: var(--panel);
    }
    header h1 {
      margin: 0;
      font-size: 1.1rem;
      font-weight: 600;
    }
    .header-actions {
      margin-left: auto;
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }
    .header-actions a {
      color: var(--accent);
      text-decoration: none;
      font-size: 0.9rem;
    }
    .header-actions a:hover { text-decoration: underline; }
    .layout {
      display: grid;
      grid-template-columns: 220px 1fr;
      min-height: calc(100vh - 57px);
    }
    @media (max-width: 720px) {
      .layout { grid-template-columns: 1fr; }
      nav { border-right: none; border-bottom: 1px solid var(--border); }
    }
    nav {
      padding: 1rem;
      border-right: 1px solid var(--border);
      background: #121a24;
    }
    nav button {
      display: block;
      width: 100%;
      text-align: left;
      padding: 0.55rem 0.65rem;
      margin-bottom: 0.35rem;
      border-radius: 8px;
      border: 1px solid transparent;
      background: transparent;
      color: var(--text);
      cursor: pointer;
      font-size: 0.95rem;
    }
    nav button:hover { background: rgba(77, 159, 255, 0.12); }
    nav button.active {
      background: rgba(77, 159, 255, 0.2);
      border-color: var(--border);
    }
    main { padding: 1.25rem; overflow: auto; }
    .toolbar {
      display: flex;
      flex-wrap: wrap;
      gap: 0.5rem;
      margin-bottom: 1rem;
      align-items: center;
    }
    button.primary, button.ghost, button.danger {
      padding: 0.45rem 0.85rem;
      border-radius: 8px;
      font-size: 0.9rem;
      cursor: pointer;
      border: 1px solid var(--border);
      background: var(--panel);
      color: var(--text);
    }
    button.primary {
      background: var(--accent);
      border-color: #3d8ae6;
      color: #061018;
      font-weight: 600;
    }
    button.danger {
      border-color: #c44;
      color: #ffc8c8;
      background: #2a1518;
    }
    button.ghost:hover, button.primary:hover, button.danger:hover {
      filter: brightness(1.08);
    }
    button:disabled { opacity: 0.45; cursor: not-allowed; }
    .status {
      font-size: 0.85rem;
      color: var(--muted);
    }
    .status.err { color: var(--danger); }
    .status.ok { color: var(--ok); }
    table {
      width: 100%;
      border-collapse: collapse;
      font-size: 0.9rem;
      background: var(--panel);
      border-radius: 10px;
      overflow: hidden;
      border: 1px solid var(--border);
    }
    th, td {
      padding: 0.65rem 0.75rem;
      text-align: left;
      border-bottom: 1px solid var(--border);
    }
    th { background: #141c28; color: var(--muted); font-weight: 600; }
    tr:last-child td { border-bottom: none; }
    code { font-size: 0.85em; color: #b8d4ff; }
    .toggle {
      position: relative;
      width: 44px;
      height: 24px;
      border-radius: 999px;
      border: none;
      cursor: pointer;
      background: #3a4556;
      transition: background 0.15s;
    }
    .toggle.on { background: #2d6a3e; }
    .toggle::after {
      content: "";
      position: absolute;
      top: 3px;
      left: 3px;
      width: 18px;
      height: 18px;
      border-radius: 50%;
      background: #fff;
      transition: transform 0.15s;
    }
    .toggle.on::after { transform: translateX(20px); }
    .hint { color: var(--muted); font-size: 0.85rem; margin-top: 0.5rem; }
    .ads-list {
      display: flex;
      flex-direction: column;
      gap: 1rem;
      max-width: 960px;
    }
    .ad-card {
      background: var(--panel);
      border: 1px solid var(--border);
      border-radius: 10px;
      padding: 1rem 1.1rem;
    }
    .ad-card-head {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      gap: 0.65rem 1rem;
      margin-bottom: 1rem;
      padding-bottom: 0.85rem;
      border-bottom: 1px solid var(--border);
    }
    .ad-card-head .ad-id { font-size: 0.9rem; }
    .ad-type-label {
      font-size: 0.85rem;
      color: var(--muted);
      margin-left: auto;
    }
    .ad-card-grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 0.85rem 1rem;
    }
    @media (max-width: 720px) {
      .ad-card-grid { grid-template-columns: 1fr; }
    }
    .ad-field-full { grid-column: 1 / -1; }
    .ad-field label {
      display: block;
      font-size: 0.82rem;
      color: var(--muted);
      margin-bottom: 0.35rem;
    }
    .ad-input, .ad-textarea {
      width: 100%;
      padding: 0.45rem 0.55rem;
      border-radius: 8px;
      border: 1px solid var(--border);
      background: #0b0f14;
      color: var(--text);
      font-size: 0.88rem;
    }
    .ad-textarea {
      min-height: 4rem;
      resize: vertical;
      font-family: ui-monospace, Consolas, monospace;
    }
    .ad-nested-editor {
      margin-top: 0.35rem;
      padding: 0.75rem;
      max-width: none;
    }
    .ad-card-actions {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      gap: 0.75rem;
      margin-top: 1rem;
      padding-top: 0.85rem;
      border-top: 1px solid var(--border);
    }
    .ad-card-hint { font-size: 0.85rem; }
    .ad-card-hint.err { color: var(--danger); }
    .ad-card-hint.ok { color: var(--ok); }
    .json-gui-root {
      background: var(--panel);
      border: 1px solid var(--border);
      border-radius: 10px;
      padding: 1rem;
      max-width: 920px;
    }
    .json-node { margin: 0; }
    .json-node > .json-pairs,
    .json-node > .json-array-items {
      margin-left: 0.5rem;
      padding-left: 0.75rem;
      border-left: 2px solid var(--border);
    }
    .json-pair {
      display: grid;
      grid-template-columns: minmax(120px, 200px) 1fr auto;
      gap: 0.5rem 0.65rem;
      align-items: start;
      margin-bottom: 0.65rem;
    }
    @media (max-width: 640px) {
      .json-pair { grid-template-columns: 1fr; }
    }
    .json-key {
      width: 100%;
      padding: 0.45rem 0.55rem;
      border-radius: 8px;
      border: 1px solid var(--border);
      background: #0b0f14;
      color: var(--text);
      font-size: 0.88rem;
    }
    .json-value { min-width: 0; }
    .json-value > .json-node {
      padding: 0.5rem;
      background: rgba(0, 0, 0, 0.2);
      border-radius: 8px;
      border: 1px solid rgba(45, 58, 77, 0.6);
    }
    .json-array-item {
      margin-bottom: 0.75rem;
      padding-bottom: 0.75rem;
      border-bottom: 1px dashed var(--border);
    }
    .json-array-item:last-of-type { border-bottom: none; margin-bottom: 0.5rem; padding-bottom: 0; }
    .json-array-toolbar {
      display: flex;
      justify-content: flex-end;
      margin-bottom: 0.35rem;
    }
    .json-inp, .json-textarea {
      width: 100%;
      padding: 0.45rem 0.55rem;
      border-radius: 8px;
      border: 1px solid var(--border);
      background: #0b0f14;
      color: var(--text);
      font-size: 0.88rem;
      font-family: inherit;
    }
    .json-textarea {
      min-height: 2.5rem;
      resize: vertical;
      font-family: ui-monospace, Consolas, monospace;
    }
    .json-null-label {
      color: var(--muted);
      font-family: ui-monospace, Consolas, monospace;
      font-size: 0.9rem;
    }
    .json-bool-label {
      display: flex;
      align-items: center;
      gap: 0.35rem;
      cursor: pointer;
      font-size: 0.9rem;
    }
    .json-add-field, .json-add-array-item {
      margin-top: 0.35rem;
    }
    .json-remove-pair { align-self: start; margin-top: 0.15rem; }
    details.raw-json {
      margin-top: 1rem;
      max-width: 920px;
      border: 1px solid var(--border);
      border-radius: 10px;
      padding: 0.65rem 1rem;
      background: #121a24;
    }
    details.raw-json summary {
      cursor: pointer;
      color: var(--muted);
      font-size: 0.9rem;
      user-select: none;
    }
    textarea.raw-json-editor {
      width: 100%;
      min-height: 200px;
      margin-top: 0.75rem;
      font-family: ui-monospace, Consolas, monospace;
      font-size: 0.82rem;
      padding: 0.75rem;
      border-radius: 8px;
      border: 1px solid var(--border);
      background: #0b0f14;
      color: var(--text);
      resize: vertical;
    }
  </style>
</head>
<body>
  <header>
    <h1>Backend JSON admin</h1>
    <span id="conn" class="status"></span>
    <div class="header-actions">
      <a href="admin/password.php">Change password</a>
      <a href="admin/logout.php">Log out</a>
    </div>
  </header>
  <div class="layout">
    <nav id="nav"></nav>
    <main>
      <div id="ads-panel" hidden>
        <div class="toolbar">
          <button type="button" class="primary" id="btn-enable-all">Enable all</button>
          <button type="button" class="danger" id="btn-disable-all">Disable all</button>
          <button type="button" class="ghost" id="btn-reload">Reload</button>
          <span id="ads-status" class="status"></span>
        </div>
        <p class="hint" style="margin-top:0">Use <strong>Save this ad</strong> after changing URLs or frequency. The enable switch saves immediately.</p>
        <div id="ads-list" class="ads-list"></div>
      </div>
      <div id="json-panel" hidden>
        <div class="toolbar">
          <button type="button" class="primary" id="btn-save-json">Save</button>
          <button type="button" class="ghost" id="btn-format">Reformat</button>
          <span id="json-status" class="status"></span>
        </div>
        <div id="json-gui-root" class="json-gui-root" aria-label="JSON editor"></div>
        <details class="raw-json" id="raw-json-details">
          <summary>Raw JSON (advanced)</summary>
          <textarea id="json-raw-editor" class="raw-json-editor" spellcheck="false" aria-label="Raw JSON"></textarea>
          <div class="toolbar" style="margin-top:0.5rem">
            <button type="button" class="ghost" id="btn-refresh-raw">Refresh from form</button>
            <button type="button" class="primary" id="btn-apply-raw">Apply to form</button>
          </div>
        </details>
        <p class="hint">Edits write to the JSON files in this folder. Preserve <code>schema_version</code> when making breaking changes.</p>
      </div>
    </main>
  </div>
  <script>
    const state = { files: [], current: null };
    const el = (id) => document.getElementById(id);
    const API = "admin/api.php";
    const LOGIN_URL = "admin/login.php";

    function setConn(msg, kind) {
      const n = el("conn");
      n.textContent = msg;
      n.className = "status" + (kind ? " " + kind : "");
    }

    async function parseResponse(res) {
      const text = await res.text();
      let data = null;
      try {
        data = text ? JSON.parse(text) : null;
      } catch (_) {}
      if (res.status === 401) {
        window.location.href = LOGIN_URL;
        throw new Error("Session expired");
      }
      if (!res.ok) {
        const msg =
          (data && (data.error || data.description || data.message)) ||
          text ||
          res.statusText;
        throw new Error(String(msg).slice(0, 500) || "Request failed");
      }
      return data;
    }

    async function apiGet(params) {
      const q = new URLSearchParams(params);
      const res = await fetch(API + "?" + q.toString(), { credentials: "same-origin" });
      return parseResponse(res);
    }

    async function apiPost(body) {
      const res = await fetch(API, {
        method: "POST",
        credentials: "same-origin",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(body),
      });
      return parseResponse(res);
    }

    function deduceNewItem(arr) {
      if (!arr.length) return "";
      const last = arr[arr.length - 1];
      const lt = typeof last;
      if (last !== null && lt === "object" && !Array.isArray(last)) return {};
      if (Array.isArray(last)) return [];
      if (lt === "number") return 0;
      if (lt === "boolean") return false;
      return "";
    }

    function jsonToGui(value) {
      const node = document.createElement("div");
      node.className = "json-node";

      if (value === null) {
        node.dataset.kind = "null";
        const span = document.createElement("span");
        span.className = "json-null-label";
        span.textContent = "null";
        node.appendChild(span);
        return node;
      }

      const t = typeof value;
      if (t === "boolean") {
        node.dataset.kind = "bool";
        const lab = document.createElement("label");
        lab.className = "json-bool-label";
        const cb = document.createElement("input");
        cb.type = "checkbox";
        cb.checked = value;
        lab.appendChild(cb);
        lab.appendChild(document.createTextNode(" true"));
        node.appendChild(lab);
        return node;
      }
      if (t === "number") {
        node.dataset.kind = "number";
        const inp = document.createElement("input");
        inp.type = "number";
        inp.step = "any";
        inp.value = Number.isFinite(value) ? String(value) : "";
        inp.className = "json-inp";
        node.appendChild(inp);
        return node;
      }
      if (t === "string") {
        node.dataset.kind = "string";
        const ta = document.createElement("textarea");
        ta.rows = value.length > 120 ? 5 : 2;
        ta.value = value;
        ta.className = "json-textarea";
        node.appendChild(ta);
        return node;
      }
      if (Array.isArray(value)) {
        node.dataset.kind = "array";
        const items = document.createElement("div");
        items.className = "json-array-items";
        for (const item of value) {
          items.appendChild(makeArrayRow(jsonToGui(item)));
        }
        node.appendChild(items);
        const add = document.createElement("button");
        add.type = "button";
        add.className = "ghost json-add-array-item";
        add.textContent = "Add item";
        add.addEventListener("click", () => {
          const snapshot = [];
          for (const row of items.querySelectorAll(":scope > .json-array-item")) {
            const child = row.querySelector(":scope > .json-node");
            if (child) snapshot.push(guiToJson(child));
          }
          items.appendChild(makeArrayRow(jsonToGui(deduceNewItem(snapshot))));
        });
        node.appendChild(add);
        return node;
      }
      if (t === "object") {
        node.dataset.kind = "object";
        const pairs = document.createElement("div");
        pairs.className = "json-pairs";
        for (const [k, v] of Object.entries(value)) {
          pairs.appendChild(makePair(k, jsonToGui(v)));
        }
        node.appendChild(pairs);
        const add = document.createElement("button");
        add.type = "button";
        add.className = "ghost json-add-field";
        add.textContent = "Add field";
        add.addEventListener("click", () => {
          pairs.appendChild(makePair("", jsonToGui("")));
        });
        node.appendChild(add);
        return node;
      }

      node.dataset.kind = "string";
      const ta = document.createElement("textarea");
      ta.className = "json-textarea";
      ta.value = String(value);
      node.appendChild(ta);
      return node;
    }

    function makePair(key, valueNode) {
      const pair = document.createElement("div");
      pair.className = "json-pair";
      const keyInp = document.createElement("input");
      keyInp.type = "text";
      keyInp.className = "json-key";
      keyInp.placeholder = "key";
      keyInp.value = key;
      const valWrap = document.createElement("div");
      valWrap.className = "json-value";
      valWrap.appendChild(valueNode);
      const remove = document.createElement("button");
      remove.type = "button";
      remove.className = "ghost json-remove-pair";
      remove.textContent = "Remove";
      remove.addEventListener("click", () => pair.remove());
      pair.appendChild(keyInp);
      pair.appendChild(valWrap);
      pair.appendChild(remove);
      return pair;
    }

    function makeArrayRow(valueNode) {
      const row = document.createElement("div");
      row.className = "json-array-item";
      const toolbar = document.createElement("div");
      toolbar.className = "json-array-toolbar";
      const remove = document.createElement("button");
      remove.type = "button";
      remove.className = "ghost";
      remove.textContent = "Remove";
      remove.addEventListener("click", () => row.remove());
      toolbar.appendChild(remove);
      row.appendChild(toolbar);
      row.appendChild(valueNode);
      return row;
    }

    function guiToJson(node) {
      if (!node || !node.classList.contains("json-node")) {
        throw new Error("Invalid editor state");
      }
      const kind = node.dataset.kind;
      if (kind === "null") return null;
      if (kind === "bool") {
        const cb = node.querySelector('input[type="checkbox"]');
        return cb ? cb.checked : false;
      }
      if (kind === "number") {
        const inp = node.querySelector("input.json-inp");
        const raw = inp ? inp.value.trim() : "";
        if (raw === "") return 0;
        const n = Number(raw);
        if (!Number.isFinite(n)) throw new Error("Invalid number: " + raw);
        return n;
      }
      if (kind === "string") {
        const ta = node.querySelector("textarea.json-textarea");
        return ta ? ta.value : "";
      }
      if (kind === "object") {
        const out = {};
        const pairs = node.querySelector(":scope > .json-pairs");
        if (!pairs) return out;
        for (const pair of pairs.querySelectorAll(":scope > .json-pair")) {
          const keyInp = pair.querySelector(".json-key");
          const valWrap = pair.querySelector(":scope > .json-value");
          const child = valWrap ? valWrap.querySelector(":scope > .json-node") : null;
          const k = keyInp ? keyInp.value.trim() : "";
          if (!k) continue;
          if (out[k] !== undefined) throw new Error("Duplicate key: " + k);
          if (!child) throw new Error("Missing value for key: " + k);
          out[k] = guiToJson(child);
        }
        return out;
      }
      if (kind === "array") {
        const out = [];
        const items = node.querySelector(":scope > .json-array-items");
        if (!items) return out;
        for (const row of items.querySelectorAll(":scope > .json-array-item")) {
          const child = row.querySelector(":scope > .json-node");
          if (!child) continue;
          out.push(guiToJson(child));
        }
        return out;
      }
      throw new Error("Unknown field type in editor");
    }

    function renderJsonGuiInto(container, data) {
      container.innerHTML = "";
      container.appendChild(jsonToGui(data));
    }

    function readJsonGuiFrom(container) {
      const top = container.querySelector(":scope > .json-node");
      if (!top) throw new Error("Nothing to save");
      return guiToJson(top);
    }

    function renderJsonGui(data) {
      renderJsonGuiInto(el("json-gui-root"), data);
    }

    function readJsonGui() {
      return readJsonGuiFrom(el("json-gui-root"));
    }

    function safeAdFieldId(adId, suffix) {
      return ("ad-" + adId + "-" + suffix).replace(/[^a-zA-Z0-9_-]/g, "_");
    }

    function buildAdCard(ad) {
      const id = ad.id || "";
      const card = document.createElement("div");
      card.className = "ad-card";
      card.dataset.adId = id;

      const head = document.createElement("div");
      head.className = "ad-card-head";
      const enabled = !!ad.enabled;
      const tgl = document.createElement("button");
      tgl.type = "button";
      tgl.className = "toggle" + (enabled ? " on" : "");
      tgl.setAttribute("aria-label", "Toggle " + id);
      tgl.addEventListener("click", () => toggleAd(id, !tgl.classList.contains("on")));
      const idCode = document.createElement("code");
      idCode.className = "ad-id";
      idCode.textContent = id;
      const typeLbl = document.createElement("span");
      typeLbl.className = "ad-type-label";
      typeLbl.textContent = ad.type || "";
      head.appendChild(tgl);
      head.appendChild(idCode);
      head.appendChild(typeLbl);

      const grid = document.createElement("div");
      grid.className = "ad-card-grid";

      function addField(labelText, fieldName, value, multiline) {
        const wrap = document.createElement("div");
        wrap.className =
          fieldName === "image_url" || fieldName === "redirect_url" ? "ad-field ad-field-full" : "ad-field";
        const lab = document.createElement("label");
        lab.textContent = labelText;
        lab.setAttribute("for", safeAdFieldId(id, fieldName));
        let inp;
        if (multiline) {
          inp = document.createElement("textarea");
          inp.className = "ad-textarea";
          inp.rows = fieldName === "image_url" || fieldName === "redirect_url" ? 3 : 2;
        } else {
          inp = document.createElement("input");
          inp.type = "text";
          inp.className = "ad-input";
        }
        inp.id = safeAdFieldId(id, fieldName);
        inp.dataset.adField = fieldName;
        inp.value = value != null ? String(value) : "";
        wrap.appendChild(lab);
        wrap.appendChild(inp);
        grid.appendChild(wrap);
      }

      addField("Type", "type", ad.type || "", false);
      addField("Placement", "placement", ad.placement || "", false);
      addField("Title", "title", ad.title || "", false);
      addField("Image URL", "image_url", ad.image_url || "", true);
      addField("Redirect / click URL", "redirect_url", ad.redirect_url || "", true);

      const priWrap = document.createElement("div");
      priWrap.className = "ad-field";
      const priLab = document.createElement("label");
      priLab.textContent = "Priority";
      priLab.setAttribute("for", safeAdFieldId(id, "priority"));
      const priInp = document.createElement("input");
      priInp.type = "number";
      priInp.step = "1";
      priInp.className = "ad-input";
      priInp.id = safeAdFieldId(id, "priority");
      priInp.dataset.adField = "priority";
      priInp.value = String(ad.priority != null ? ad.priority : 0);
      priWrap.appendChild(priLab);
      priWrap.appendChild(priInp);
      grid.appendChild(priWrap);

      const freqWrap = document.createElement("div");
      freqWrap.className = "ad-field ad-field-full";
      const freqLab = document.createElement("label");
      freqLab.textContent = "Frequency (caps & timing)";
      freqWrap.appendChild(freqLab);
      const freqEditor = document.createElement("div");
      freqEditor.className = "json-gui-root ad-nested-editor ad-frequency-editor";
      freqWrap.appendChild(freqEditor);
      const freqObj =
        ad.frequency != null && typeof ad.frequency === "object" && !Array.isArray(ad.frequency)
          ? ad.frequency
          : {};
      renderJsonGuiInto(freqEditor, freqObj);
      grid.appendChild(freqWrap);

      const extWrap = document.createElement("div");
      extWrap.className = "ad-field ad-field-full";
      const extLab = document.createElement("label");
      extLab.textContent = "Extensions";
      extWrap.appendChild(extLab);
      const extEditor = document.createElement("div");
      extEditor.className = "json-gui-root ad-nested-editor ad-extensions-editor";
      extWrap.appendChild(extEditor);
      const extObj =
        ad.extensions != null && typeof ad.extensions === "object" && !Array.isArray(ad.extensions)
          ? ad.extensions
          : {};
      renderJsonGuiInto(extEditor, extObj);
      grid.appendChild(extWrap);

      const actions = document.createElement("div");
      actions.className = "ad-card-actions";
      const saveBtn = document.createElement("button");
      saveBtn.type = "button";
      saveBtn.className = "primary";
      saveBtn.textContent = "Save this ad";
      saveBtn.addEventListener("click", () => saveAdCard(card, id));
      const hint = document.createElement("span");
      hint.className = "ad-card-hint status";
      hint.dataset.role = "hint";
      actions.appendChild(saveBtn);
      actions.appendChild(hint);

      card.appendChild(head);
      card.appendChild(grid);
      card.appendChild(actions);
      return card;
    }

    async function saveAdCard(card, adId) {
      const hint = card.querySelector('[data-role="hint"]');
      hint.textContent = "Saving…";
      hint.className = "ad-card-hint status";
      try {
        const enabled = card.querySelector(".ad-card-head .toggle").classList.contains("on");
        const get = (name) => card.querySelector(`[data-ad-field="${name}"]`);
        const gType = get("type");
        const gTitle = get("title");
        const gImg = get("image_url");
        const gRedir = get("redirect_url");
        const gPlace = get("placement");
        const gPri = get("priority");
        if (!gType || !gTitle || !gImg || !gRedir || !gPlace || !gPri) {
          throw new Error("Form is incomplete");
        }
        const priNum = Number(gPri.value);
        if (!Number.isFinite(priNum)) {
          throw new Error("Priority must be a number");
        }
        const freqEl = card.querySelector(".ad-frequency-editor");
        const extEl = card.querySelector(".ad-extensions-editor");
        const patch = {
          enabled,
          type: gType.value,
          title: gTitle.value,
          image_url: gImg.value,
          redirect_url: gRedir.value,
          placement: gPlace.value,
          priority: priNum,
          frequency: readJsonGuiFrom(freqEl),
          extensions: readJsonGuiFrom(extEl),
        };
        await apiPost({ action: "update_ad_fields", name: state.current, id: adId, patch });
        const doc = await apiGet({ action: "file", name: state.current });
        renderAds(doc);
        el("ads-status").textContent = "Saved ad: " + adId;
        el("ads-status").className = "status ok";
      } catch (e) {
        hint.textContent = e.message || String(e);
        hint.className = "ad-card-hint status err";
        el("ads-status").textContent = e.message || String(e);
        el("ads-status").className = "status err";
      }
    }

    function syncRawFromGui() {
      try {
        const j = readJsonGui();
        el("json-raw-editor").value = JSON.stringify(j, null, 2);
      } catch (e) {
        el("json-raw-editor").value = "// " + (e.message || e);
      }
    }

    function applyRawToGui() {
      let parsed;
      try {
        parsed = JSON.parse(el("json-raw-editor").value);
      } catch (e) {
        throw new Error("Invalid JSON: " + (e.message || e));
      }
      if (parsed === null || typeof parsed !== "object" || Array.isArray(parsed)) {
        throw new Error("Root must be a JSON object");
      }
      renderJsonGui(parsed);
    }

    function renderNav() {
      const nav = el("nav");
      nav.innerHTML = "";
      for (const f of state.files) {
        const b = document.createElement("button");
        b.type = "button";
        b.textContent = f.name;
        if (f.name === state.current) b.classList.add("active");
        b.addEventListener("click", () => selectFile(f.name));
        nav.appendChild(b);
      }
    }

    function showAds(show) {
      el("ads-panel").hidden = !show;
      el("json-panel").hidden = show;
    }

    function currentKind() {
      const f = state.files.find((x) => x.name === state.current);
      return f ? f.kind : null;
    }

    async function loadFileList() {
      setConn("Loading…", "");
      const data = await apiGet({ action: "files" });
      state.files = data.files || [];
      if (!state.current && state.files.length) state.current = state.files[0].name;
      renderNav();
      setConn("Ready", "ok");
      if (state.current) await selectFile(state.current);
    }

    function renderAds(doc) {
      const list = el("ads-list");
      list.innerHTML = "";
      const ads = Array.isArray(doc.ads) ? doc.ads : [];
      for (const ad of ads) {
        list.appendChild(buildAdCard(ad));
      }
    }

    async function toggleAd(id, enabled) {
      el("ads-status").textContent = "Saving…";
      el("ads-status").className = "status";
      try {
        await apiPost({ action: "patch_ad", name: state.current, id, enabled });
        const cards = el("ads-list").querySelectorAll(".ad-card");
        for (const card of cards) {
          if (card.dataset.adId === id) {
            const t = card.querySelector(".ad-card-head .toggle");
            if (t) t.classList.toggle("on", enabled);
            break;
          }
        }
        el("ads-status").textContent = "Enabled flag saved.";
        el("ads-status").className = "status ok";
      } catch (e) {
        el("ads-status").textContent = e.message || String(e);
        el("ads-status").className = "status err";
      }
    }

    async function bulkAds(enabled) {
      el("ads-status").textContent = "Saving…";
      el("ads-status").className = "status";
      try {
        await apiPost({ action: "bulk_ads", name: state.current, enabled });
        const doc = await apiGet({ action: "file", name: state.current });
        renderAds(doc);
        el("ads-status").textContent = "Updated all ads.";
        el("ads-status").className = "status ok";
      } catch (e) {
        el("ads-status").textContent = e.message || String(e);
        el("ads-status").className = "status err";
      }
    }

    async function selectFile(name) {
      state.current = name;
      renderNav();
      el("json-status").textContent = "";
      el("ads-status").textContent = "";
      const kind = currentKind();
      if (kind === "ads") {
        showAds(true);
        try {
          const doc = await apiGet({ action: "file", name });
          renderAds(doc);
        } catch (e) {
          el("ads-status").textContent = e.message || String(e);
          el("ads-status").className = "status err";
        }
      } else {
        showAds(false);
        try {
          const doc = await apiGet({ action: "file", name });
          if (!doc || typeof doc !== "object" || Array.isArray(doc)) {
            throw new Error("Expected a JSON object");
          }
          renderJsonGui(doc);
          el("json-raw-editor").value = "";
          el("json-status").textContent = "Loaded.";
          el("json-status").className = "status ok";
        } catch (e) {
          el("json-gui-root").innerHTML = "";
          el("json-status").textContent = e.message || String(e);
          el("json-status").className = "status err";
        }
      }
    }

    async function saveJson() {
      el("json-status").textContent = "Saving…";
      el("json-status").className = "status";
      let parsed;
      try {
        parsed = readJsonGui();
      } catch (e) {
        el("json-status").textContent = e.message || String(e);
        el("json-status").className = "status err";
        return;
      }
      try {
        await apiPost({ action: "save", name: state.current, data: parsed });
        el("json-status").textContent = "Saved.";
        el("json-status").className = "status ok";
        syncRawFromGui();
      } catch (e) {
        el("json-status").textContent = e.message || String(e);
        el("json-status").className = "status err";
      }
    }

    el("btn-enable-all").addEventListener("click", () => bulkAds(true));
    el("btn-disable-all").addEventListener("click", () => bulkAds(false));
    el("btn-reload").addEventListener("click", () => selectFile(state.current));
    el("btn-save-json").addEventListener("click", saveJson);
    el("btn-format").addEventListener("click", () => {
      try {
        const j = readJsonGui();
        renderJsonGui(j);
        el("json-status").textContent = "Reformatted.";
        el("json-status").className = "status ok";
        syncRawFromGui();
      } catch (e) {
        el("json-status").textContent = e.message || String(e);
        el("json-status").className = "status err";
      }
    });

    el("raw-json-details").addEventListener("toggle", () => {
      if (el("raw-json-details").open) syncRawFromGui();
    });
    el("btn-refresh-raw").addEventListener("click", syncRawFromGui);
    el("btn-apply-raw").addEventListener("click", () => {
      el("json-status").textContent = "";
      el("json-status").className = "status";
      try {
        applyRawToGui();
        el("json-status").textContent = "Applied raw JSON to form.";
        el("json-status").className = "status ok";
      } catch (e) {
        el("json-status").textContent = e.message || String(e);
        el("json-status").className = "status err";
      }
    });

    loadFileList().catch((e) => {
      setConn(e.message || String(e), "err");
    });
  </script>
</body>
</html>
