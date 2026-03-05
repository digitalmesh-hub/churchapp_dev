<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="theme-color" content="#1c1408">
<title>Scripture Reader</title>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=Cinzel:wght@400;600&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

:root {
  --parchment: #f5efe0;
  --parchment-dark: #e8dfc8;
  --ink: #1c1408;
  --ink-light: #5a4b2e;
  --gold: #b8860b;
  --gold-light: #d4a520;
  --red: #8b1a1a;
  --bg: #1c1408;
}

html, body { height: 100%; }

body {
  background: var(--bg);
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  font-family: 'Cormorant Garamond', Georgia, serif;
  -webkit-tap-highlight-color: transparent;
}

/* HEADER */
.header {
  text-align: center;
  padding: 20px 16px 14px;
  background: var(--bg);
  border-bottom: 1px solid rgba(184,134,11,0.18);
}
.header-cross { color: var(--gold); font-size: 0.9rem; opacity: 0.55; letter-spacing: 0.6em; margin-bottom: 6px; }
.header h1 {
  font-family: 'Cinzel', serif;
  font-size: 1.35rem;
  font-weight: 600;
  color: var(--gold-light);
  letter-spacing: 0.2em;
  text-shadow: 0 1px 10px rgba(180,130,20,0.4);
}

/* CONTROLS */
.controls {
  background: linear-gradient(160deg, var(--parchment) 0%, var(--parchment-dark) 100%);
  padding: 16px 16px 14px;
  border-bottom: 2px solid rgba(184,134,11,0.3);
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.field label {
  display: block;
  font-family: 'Cinzel', serif;
  font-size: 0.58rem;
  font-weight: 600;
  letter-spacing: 0.16em;
  color: var(--ink-light);
  text-transform: uppercase;
  margin-bottom: 5px;
}

/* Language toggle pills */
.lang-toggle {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 0;
  border: 1.5px solid rgba(184,134,11,0.45);
  border-radius: 8px;
  overflow: hidden;
}

.lang-btn {
  background: rgba(255,255,255,0.5);
  border: none;
  padding: 13px 8px;
  font-family: 'Cinzel', serif;
  font-size: 0.78rem;
  font-weight: 600;
  letter-spacing: 0.1em;
  color: var(--ink-light);
  cursor: pointer;
  transition: background 0.2s, color 0.2s;
  -webkit-appearance: none;
  box-shadow: none;
}

.lang-btn:first-child { border-right: 1px solid rgba(184,134,11,0.3); }

.lang-btn.active {
  background: linear-gradient(180deg, #c9980a 0%, #8b6208 100%);
  color: #fff8e8;
}

.lang-btn.active-mal {
  background: linear-gradient(180deg, #2e7d32 0%, #1b5e20 100%);
  color: #e8f5e9;
}

/* Book takes full row */
select, input[type="number"] {
  width: 100%;
  background: rgba(255,255,255,0.7);
  border: 1px solid rgba(139,104,26,0.3);
  border-bottom: 2.5px solid rgba(184,134,11,0.5);
  border-radius: 8px 8px 2px 2px;
  padding: 13px 10px 11px;
  font-family: 'Cormorant Garamond', serif;
  color: var(--ink);
  font-size: 16px;
  outline: none;
  appearance: none;
  -webkit-appearance: none;
  transition: border-color 0.2s, background 0.2s;
}

select {
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='7' viewBox='0 0 12 7'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%23b8860b' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 10px center;
  padding-right: 28px;
}

select:focus, input[type="number"]:focus {
  border-bottom-color: var(--gold);
  background: rgba(255,255,255,0.9);
}

input[type="number"]::placeholder { color: rgba(74,59,30,0.35); }
input[type=number]::-webkit-inner-spin-button,
input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
input[type=number] { -moz-appearance: textfield; text-align: center; }

.field-row-3 {
  display: grid;
  grid-template-columns: 1fr 1fr 1fr;
  gap: 10px;
}

/* Translation sub-select — only shown for English */
.translation-row { transition: opacity 0.2s; }
.translation-row.hidden { display: none; }

.go-btn {
  width: 100%;
  border: none;
  border-radius: 10px;
  color: #fff8e8;
  font-family: 'Cinzel', serif;
  font-size: 0.88rem;
  font-weight: 600;
  letter-spacing: 0.18em;
  text-transform: uppercase;
  padding: 15px;
  cursor: pointer;
  box-shadow: 0 4px 16px rgba(0,0,0,0.4);
  transition: opacity 0.15s, transform 0.1s;
  -webkit-appearance: none;
  margin-top: 2px;
}
.go-btn.en { background: linear-gradient(180deg, #c9980a 0%, #8b6208 100%); }
.go-btn.ml { background: linear-gradient(180deg, #2e7d32 0%, #1b5e20 100%); }
.go-btn:active { opacity: 0.85; transform: scale(0.985); }

/* READING AREA */
.reading {
  flex: 1;
  overflow-y: auto;
  -webkit-overflow-scrolling: touch;
  padding: 24px 14px 50px;
  background: var(--bg);
}

/* PASSAGE */
.passage-wrap { max-width: 560px; margin: 0 auto; animation: fadeIn 0.5s ease; }
@keyframes fadeIn { from { opacity:0; transform:translateY(6px); } to { opacity:1; transform:translateY(0); } }

.passage-header { text-align: center; margin-bottom: 16px; }

.passage-ref {
  font-family: 'Cinzel', serif;
  font-size: 1.2rem;
  color: var(--gold-light);
  letter-spacing: 0.12em;
  text-shadow: 0 1px 10px rgba(180,130,20,0.35);
}

/* Malayalam reference uses a softer green tint */
.passage-ref.ml { color: #81c784; text-shadow: 0 1px 10px rgba(46,125,50,0.3); }

.passage-translation {
  font-style: italic;
  font-size: 0.82rem;
  color: rgba(212,165,32,0.5);
  margin-top: 4px;
}
.passage-translation.ml { color: rgba(129,199,132,0.55); }

.divider {
  display: flex; align-items: center; gap: 10px;
  margin: 4px 0 18px;
}
.divider-line { flex:1; height:1px; background: linear-gradient(90deg, transparent, rgba(212,165,32,0.35), transparent); }
.divider-line.ml { background: linear-gradient(90deg, transparent, rgba(129,199,132,0.3), transparent); }
.divider-icon { color: var(--gold); font-size: 0.9rem; opacity: 0.5; }
.divider-icon.ml { color: #81c784; }

.passage-card {
  background: linear-gradient(160deg, var(--parchment) 0%, #ede3cc 100%);
  border: 1px solid rgba(184,134,11,0.28);
  border-radius: 14px;
  padding: 22px 18px;
  box-shadow: 0 8px 40px rgba(0,0,0,0.45), inset 0 1px 0 rgba(255,255,255,0.5);
  position: relative;
  overflow: hidden;
}

.passage-card.ml { border-color: rgba(46,125,50,0.25); }

.passage-card::after {
  content: '✝';
  position: absolute; bottom: -12px; right: 14px;
  font-size: 7rem; color: rgba(184,134,11,0.04);
  pointer-events: none; line-height: 1;
}

.verse { display: flex; gap: 11px; margin-bottom: 14px; }
.verse:last-child { margin-bottom: 0; }

.verse-num {
  font-family: 'Cinzel', serif;
  font-size: 0.6rem; font-weight: 600;
  color: var(--red); min-width: 18px;
  padding-top: 5px; opacity: 0.8; flex-shrink: 0;
}

.verse-text {
  font-size: 1.07rem; color: var(--ink);
  font-weight: 300; line-height: 1.85;
}

/* Malayalam text: use system font for proper rendering */
.verse-text.ml {
  font-family: 'Noto Sans Malayalam', 'Manjari', sans-serif;
  font-size: 1.05rem;
  font-weight: 400;
  line-height: 2;
}

.single-verse .verse-text { font-size: 1.15rem; font-style: italic; }
.single-verse .verse-text.ml { font-style: normal; }

.status {
  text-align: center; padding: 60px 20px;
  font-style: italic; font-size: 1.1rem;
  color: rgba(212,165,32,0.5);
}
.status.error { color: rgba(220,80,80,0.75); }

.chapter-nav {
  display: flex; justify-content: space-between;
  align-items: center; margin-top: 18px; gap: 10px;
}

.nav-btn {
  flex: 1;
  background: rgba(212,165,32,0.07);
  border: 1px solid rgba(212,165,32,0.28);
  border-radius: 8px;
  color: rgba(212,165,32,0.75);
  font-family: 'Cinzel', serif;
  font-size: 0.68rem; letter-spacing: 0.1em;
  padding: 14px 8px; cursor: pointer;
  -webkit-appearance: none; box-shadow: none;
  transition: background 0.15s;
}
.nav-btn.ml {
  background: rgba(46,125,50,0.07);
  border-color: rgba(46,125,50,0.28);
  color: rgba(129,199,132,0.75);
}
.nav-btn:active { background: rgba(212,165,32,0.18); }
.nav-btn.ml:active { background: rgba(46,125,50,0.18); }
.nav-btn:disabled { opacity: 0.2; cursor: default; }

.nav-center {
  font-style: italic; color: rgba(212,165,32,0.38);
  font-size: 0.82rem; white-space: nowrap; flex-shrink: 0;
}
.nav-center.ml { color: rgba(129,199,132,0.38); }
</style>
</head>
<body>

<div class="header">
  <div class="header-cross">✦ ✝ ✦</div>
  <h1>Scripture Reader</h1>
</div>

<div class="controls">

  <!-- Language Toggle -->
  <div class="field">
    <label>Language</label>
    <div class="lang-toggle">
      <button class="lang-btn active" id="btnEn" onclick="setLang('en')">English</button>
      <button class="lang-btn" id="btnMl" onclick="setLang('ml')">മലയാളം</button>
    </div>
  </div>

  <!-- Book -->
  <div class="field">
    <label>Book</label>
    <select id="bookSelect"></select>
  </div>

  <!-- Chapter / From / To -->
  <div class="field-row-3">
    <div class="field">
      <label>Chapter</label>
      <input type="number" id="chapterInput" min="1" value="1" inputmode="numeric" placeholder="1">
    </div>
    <div class="field">
      <label>From</label>
      <input type="number" id="verseFrom" min="1" placeholder="—" inputmode="numeric">
    </div>
    <div class="field">
      <label>To</label>
      <input type="number" id="verseTo" min="1" placeholder="—" inputmode="numeric">
    </div>
  </div>

  <!-- English translation picker (hidden for Malayalam) -->
  <div class="field translation-row" id="translationRow">
    <label>Translation</label>
    <select id="translationSelect">
      <option value="web">World English Bible (WEB)</option>
      <option value="kjv">King James Version (KJV)</option>
      <option value="bbe">Bible in Basic English (BBE)</option>
      <option value="oeb-us">Open English Bible (US)</option>
    </select>
  </div>

  <button class="go-btn en" id="goBtn" onclick="fetchPassage()">Open Scripture</button>

</div>

<div class="reading">
  <div id="output"></div>
</div>

<script>
// ── Book data ────────────────────────────────────────────────────────────────
// English names for bible-api.com (slug form)
const EN_BOOKS = [
  "Genesis","Exodus","Leviticus","Numbers","Deuteronomy","Joshua","Judges","Ruth",
  "1 Samuel","2 Samuel","1 Kings","2 Kings","1 Chronicles","2 Chronicles","Ezra",
  "Nehemiah","Esther","Job","Psalms","Proverbs","Ecclesiastes","Song of Solomon",
  "Isaiah","Jeremiah","Lamentations","Ezekiel","Daniel","Hosea","Joel","Amos",
  "Obadiah","Jonah","Micah","Nahum","Habakkuk","Zephaniah","Haggai","Zechariah",
  "Malachi","Matthew","Mark","Luke","John","Acts","Romans","1 Corinthians",
  "2 Corinthians","Galatians","Ephesians","Philippians","Colossians","1 Thessalonians",
  "2 Thessalonians","1 Timothy","2 Timothy","Titus","Philemon","Hebrews","James",
  "1 Peter","2 Peter","1 John","2 John","3 John","Jude","Revelation"
];

// Malayalam book names (same order, 1-indexed = bookid for bolls.life)
const ML_BOOKS = [
  "ഉൽപത്തി","പുറപ്പാടു","ലേവ്യർ","സംഖ്യ","ആവർത്തനം","യോശുവ","ന്യായാധിപന്മാർ","രൂത്ത്",
  "1 ശമൂവേൽ","2 ശമൂവേൽ","1 രാജാക്കന്മാർ","2 രാജാക്കന്മാർ","1 ദിനവൃത്താന്തം","2 ദിനവൃത്താന്തം","എസ്രാ",
  "നെഹെമ്യാവ്","എസ്ഥേർ","ഇയ്യോബ്","സങ്കീർത്തനങ്ങൾ","സദൃശ്യവാക്യങ്ങൾ","സഭാപ്രസംഗി","ഉത്തമഗീതം",
  "യെശയ്യാവ്","യിരെമ്യാവ്","വിലാപങ്ങൾ","യെഹെസ്കേൽ","ദാനിയേൽ","ഹോശേയ","യോവേൽ","ആമോസ്",
  "ഓബദ്യാവ്","യോനാ","മീഖാ","നഹൂം","ഹബക്കൂക്","സെഫന്യാവ്","ഹഗ്ഗായി","സെഖര്യാവ്",
  "മലാഖി","മത്തായി","മർക്കൊസ്","ലൂക്കൊസ്","യോഹന്നാൻ","പ്രവൃത്തികൾ","റോമർ","1 കൊരിന്ത്യർ",
  "2 കൊരിന്ത്യർ","ഗലാത്യർ","എഫേസ്യർ","ഫിലിപ്പ്യർ","കൊലൊസ്സ്യർ","1 തെസ്സലൊനീക്യർ",
  "2 തെസ്സലൊനീക്യർ","1 തിമൊഥെയൊസ്","2 തിമൊഥെയൊസ്","തീത്തൊസ്","ഫിലേമോൻ","എബ്രായർ","യാക്കോബ്",
  "1 പത്രൊസ്","2 പത്രൊസ്","1 യോഹന്നാൻ","2 യോഹന്നാൻ","3 യോഹന്നാൻ","യൂദാ","വെളിപ്പാടു"
];

let currentLang = 'en';

// ── Populate book dropdown ───────────────────────────────────────────────────
function populateBooks(lang) {
  const sel = document.getElementById('bookSelect');
  const books = lang === 'en' ? EN_BOOKS : ML_BOOKS;
  sel.innerHTML = '';
  books.forEach((name, i) => {
    const opt = document.createElement('option');
    opt.value = i + 1; // 1-based index = bolls.life bookid
    opt.textContent = name;
    sel.appendChild(opt);
  });
  // Default to John (book 43)
  sel.value = 43;
}

populateBooks('en');
document.getElementById('chapterInput').value = 3;

// ── Language switch ──────────────────────────────────────────────────────────
function setLang(lang) {
  currentLang = lang;
  const btnEn = document.getElementById('btnEn');
  const btnMl = document.getElementById('btnMl');
  const goBtn = document.getElementById('goBtn');
  const transRow = document.getElementById('translationRow');

  if (lang === 'en') {
    btnEn.className = 'lang-btn active';
    btnMl.className = 'lang-btn';
    goBtn.className = 'go-btn en';
    transRow.classList.remove('hidden');
  } else {
    btnEn.className = 'lang-btn';
    btnMl.className = 'lang-btn active-mal active';
    goBtn.className = 'go-btn ml';
    transRow.classList.add('hidden');
  }

  // Remember selected book index and restore in new language
  const currentIdx = parseInt(document.getElementById('bookSelect').value) - 1;
  populateBooks(lang);
  document.getElementById('bookSelect').value = currentIdx + 1;

  // Clear output
  document.getElementById('output').innerHTML = '';
}

// ── Fetch ────────────────────────────────────────────────────────────────────
async function fetchPassage() {
  const bookIdx = parseInt(document.getElementById('bookSelect').value); // 1-based
  const bookName = document.getElementById('bookSelect').options[document.getElementById('bookSelect').selectedIndex].text;
  const chapter = parseInt(document.getElementById('chapterInput').value) || 1;
  const verseFrom = document.getElementById('verseFrom').value;
  const verseTo = document.getElementById('verseTo').value;

  const out = document.getElementById('output');
  out.innerHTML = `<div class="status">✦ Opening the Word ✦</div>`;
  document.querySelector('.reading').scrollTo({ top: 0, behavior: 'smooth' });
  document.activeElement && document.activeElement.blur();

  try {
    if (currentLang === 'en') {
      await fetchEnglish(bookIdx, bookName, chapter, verseFrom, verseTo);
    } else {
      await fetchMalayalam(bookIdx, bookName, chapter, verseFrom, verseTo);
    }
  } catch(err) {
    out.innerHTML = `<div class="status error">Could not load passage.<br><small>${err.message}</small><br><br><em>Check the book, chapter, and verse numbers.</em></div>`;
  }
}

// ── English via bible-api.com ────────────────────────────────────────────────
async function fetchEnglish(bookIdx, bookName, chapter, verseFrom, verseTo) {
  const enName = EN_BOOKS[bookIdx - 1].toLowerCase().replace(/ /g, '+');
  const translation = document.getElementById('translationSelect').value;
  const transSel = document.getElementById('translationSelect');
  const transName = transSel.options[transSel.selectedIndex].text;

  let ref = `${enName}+${chapter}`;
  if (verseFrom && verseTo) ref += `:${verseFrom}-${verseTo}`;
  else if (verseFrom) ref += `:${verseFrom}`;

  const res = await fetch(`https://bible-api.com/${ref}?translation=${translation}`);
  if (!res.ok) throw new Error(`Error ${res.status}`);
  const data = await res.json();
  if (data.error) throw new Error(data.error);

  let refLabel;
  if (verseFrom && verseTo) refLabel = `${EN_BOOKS[bookIdx-1]} ${chapter}:${verseFrom}–${verseTo}`;
  else if (verseFrom) refLabel = `${EN_BOOKS[bookIdx-1]} ${chapter}:${verseFrom}`;
  else refLabel = `${EN_BOOKS[bookIdx-1]} ${chapter}`;

  renderPassage(data.verses, refLabel, transName, 'en', chapter, !!verseFrom);
}

// ── Malayalam via bolls.life ─────────────────────────────────────────────────
async function fetchMalayalam(bookIdx, bookName, chapter, verseFrom, verseTo) {
  const res = await fetch(`https://bolls.life/get-text/MOV/${bookIdx}/${chapter}/`);
  if (!res.ok) throw new Error(`Error ${res.status}`);
  const allVerses = await res.json();

  // Filter by verse range if specified
  let verses = allVerses;
  if (verseFrom && verseTo) {
    verses = allVerses.filter(v => v.verse >= parseInt(verseFrom) && v.verse <= parseInt(verseTo));
  } else if (verseFrom) {
    verses = allVerses.filter(v => v.verse === parseInt(verseFrom));
  }

  if (!verses.length) throw new Error('No verses found for that range.');

  // bolls.life returns HTML in text field — strip tags
  const cleaned = verses.map(v => ({
    verse: v.verse,
    text: v.text.replace(/<[^>]+>/g, '').trim()
  }));

  let refLabel;
  if (verseFrom && verseTo) refLabel = `${ML_BOOKS[bookIdx-1]} ${chapter}:${verseFrom}–${verseTo}`;
  else if (verseFrom) refLabel = `${ML_BOOKS[bookIdx-1]} ${chapter}:${verseFrom}`;
  else refLabel = `${ML_BOOKS[bookIdx-1]} ${chapter}`;

  renderPassage(cleaned, refLabel, 'സത്യവേദപുസ്തകം O.V.', 'ml', chapter, !!verseFrom);
}

// ── Render ───────────────────────────────────────────────────────────────────
function renderPassage(verses, ref, transName, lang, chapter, hasVerseFilter) {
  const out = document.getElementById('output');
  const isSingle = verses.length === 1;
  const chNum = parseInt(chapter);
  const navClass = lang === 'ml' ? 'ml' : '';

  let versesHtml = '';
  verses.forEach(v => {
    versesHtml += `
      <div class="verse">
        ${!isSingle ? `<span class="verse-num">${v.verse}</span>` : ''}
        <span class="verse-text ${lang === 'ml' ? 'ml' : ''}">${v.text}</span>
      </div>`;
  });

  out.innerHTML = `
    <div class="passage-wrap">
      <div class="passage-header">
        <div class="passage-ref ${navClass}">${ref}</div>
        <div class="passage-translation ${navClass}">${transName}</div>
      </div>
      <div class="divider">
        <div class="divider-line ${navClass}"></div>
        <div class="divider-icon ${navClass}">✦</div>
        <div class="divider-line ${navClass}"></div>
      </div>
      <div class="passage-card ${isSingle ? 'single-verse' : ''} ${navClass}">
        ${versesHtml}
      </div>
      ${!hasVerseFilter ? `
      <div class="chapter-nav">
        <button class="nav-btn ${navClass}" onclick="navigate(${chNum - 1})" ${chNum <= 1 ? 'disabled' : ''}>← ${lang === 'ml' ? 'മുൻ' : 'Prev'}</button>
        <span class="nav-center ${navClass}">${lang === 'ml' ? 'അ.' : 'Ch.'} ${chNum}</span>
        <button class="nav-btn ${navClass}" onclick="navigate(${chNum + 1})">${lang === 'ml' ? 'അടുത്ത' : 'Next'} →</button>
      </div>` : ''}
    </div>
  `;
}

function navigate(ch) {
  if (ch < 1) return;
  document.getElementById('chapterInput').value = ch;
  document.getElementById('verseFrom').value = '';
  document.getElementById('verseTo').value = '';
  fetchPassage();
}

fetchPassage();
</script>
</body>
</html>
