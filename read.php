<?php
session_start();
require_once __DIR__ . '/../content/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$book_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT b.title, b.book_file
    FROM books b
    JOIN reading_list r ON b.id = r.book_id
    WHERE r.user_id = ? AND b.id = ?
");
$stmt->bind_param("ii", $user_id, $book_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Book not found in your reading list.";
    exit();
}

$book = $result->fetch_assoc();
$bookFile = '../uploads/' . $book['book_file'];
$bookmarkKey = "bookmark_{$user_id}_{$book_id}";
$notesKey = "notes_{$user_id}_{$book_id}";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reading: <?php echo htmlspecialchars($book['title']); ?></title>
  <link rel="stylesheet" href="../css/home.css">
<style>
/* Full page layout */
body, html {
    margin: 0;
    padding: 0;
    font-family: Arial, sans-serif;
    height: 100%;
    width: 100%;
    display: flex;
    flex-direction: column;
}

/* Header stays on top */

/* Modern Orange-Red Header */
header {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 65px;
    background: linear-gradient(90deg, #ff4500, #ff7f50); /* orange to orange-red */
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 40px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.5);
    z-index: 100;
    font-family: 'Segoe UI', sans-serif;
}

header h1 {
    color: #fff;
    font-size: 24px;
    font-weight: bold;
    margin: 0;
    text-shadow: 1px 1px 3px rgba(0,0,0,0.5);
}

header nav a {
    color: #fff;
    text-decoration: none;
    margin-left: 20px;
    transition: color 0.3s, transform 0.2s;
}

header nav a:hover {
    color: #ffe0b2;
    transform: translateY(-2px);
}

header nav a.logout-btn {
    background: rgba(255,255,255,0.2);
    padding: 6px 12px;
    border-radius: 8px;
    transition: background 0.3s, transform 0.2s;
}

header nav a.logout-btn:hover {
    background: rgba(255,255,255,0.35);
    transform: translateY(-2px);
}

/* PDF iframe takes remaining height */
#iframe-container {
    flex: 1;
    position: relative;
    margin-top:70px;
}
iframe {
    width: 100%;
    height: 100%;
    border: none;
}
/* Floating Toolbar - Orange Theme */
#floating-toolbar { 
    position: fixed; 
    bottom: 20px; 
    right: 20px; 
    z-index: 1000; 
    display: flex;
    flex-direction: column;
    align-items: flex-end;
}

#main-button { 
    width: 60px; 
    height: 60px; 
    border-radius: 50%; 
    background: linear-gradient(145deg, #ff4500, #ff7f50); /* same orange gradient */
    color: #fff; 
    font-size: 28px; 
    display: flex; 
    align-items: center; 
    justify-content: center; 
    cursor: pointer; 
    box-shadow: 0 6px 12px rgba(0,0,0,0.4); 
    transition: transform 0.2s, box-shadow 0.2s;
}

#main-button:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.5);
}

#floating-buttons { 
    display: none; 
    flex-direction: column; 
    margin-bottom: 10px; 
}

.floating-btn { 
    margin-bottom: 10px; 
    width: 200px; 
    background: white;
        border-radius: 30px; 
    padding: 10px; 
    cursor: pointer; 
    text-align: center; 
    color: #3f1400ff;
    font-weight: 600;
    box-shadow: 0 4px 10px rgba(0,0,0,0.3); 
    transition: transform 0.2s, box-shadow 0.2s, background 0.3s;
}

.floating-btn:hover { 
    transform: translateY(-2px); 
    box-shadow: 0 6px 14px rgba(0,0,0,0.4); 
    background: linear-gradient(145deg, #ff4500, #ff7f50);
}

/* Modal Styles */
.modal { 
    display: none; 
    position: fixed; 
    z-index: 1001; 
    left: 0; 
    top: 0; 
    width: 100%; 
    height: 100%; 
    overflow: auto; 
    background: rgba(0,0,0,0.5);
}
.modal-content { 
    background: #fff; 
    margin: 10% auto; 
    padding: 20px; 
    border-radius: 8px; 
    width: 300px; 
    max-height: 70%; 
    overflow-y: auto; 
    box-shadow: 0 2px 10px rgba(0,0,0,0.2); 
}
.modal-content h3 { margin-top:0; }
.modal-content ul { list-style:none; padding:0; margin:0; }
.modal-content li { padding:8px 10px; border-bottom:1px solid #ddd; display:flex; justify-content:space-between; cursor:pointer; }
.modal-content li:hover { background:#f0f0f0; }
.remove-btn { color:red; font-weight:bold; margin-left:10px; cursor:pointer; }
.close { float:right; font-size:18px; font-weight:bold; cursor:pointer; }
textarea { resize:none; width:100%; padding:5px; }
button { cursor:pointer; }
</style>

<body>
<header>
    <h1>Librix</h1>
    <nav>
      <a href="home.php">Home</a>
      <a href="manage_books.php">Browse</a>
      <a href="library.php">Library</a>
      <a href="profile.php">Profile</a>
      <a href="logout.php" class="logout-btn">Logout</a>
    </nav>
</header>

<div id="iframe-container">
    <iframe id="pdf-frame" src="<?php echo $bookFile; ?>#page=1"></iframe>
</div>

<!-- Floating toolbar and modals stay the same -->

<!-- Floating Toolbar -->
<div id="floating-toolbar">
    <div id="floating-buttons" style="color:black;">
        <div class="floating-btn" id="bookmark-btn">📌 Add Bookmark</div>
        <div class="floating-btn" id="view-bookmarks">🔖 View Bookmarks</div>
        <div class="floating-btn" id="notes-btn">📝 Notes</div>
    </div>
    <div id="main-button">☰</div>
</div>

<!-- Bookmark Modal -->
<div id="bookmark-modal" class="modal">
    <div class="modal-content">
        <span class="close" id="close-modal">&times;</span>
        <h3>Bookmarked Pages</h3>
        <ul id="bookmark-list"></ul>
    </div>
</div>

<!-- Notes Modal -->
<div id="notes-modal" class="modal">
    <div class="modal-content">
        <span class="close" id="close-notes">&times;</span>
        <h3>My Notes</h3>
        <textarea id="note-input" placeholder="Write your note..." style="width:100%;height:80px;padding:5px;margin-bottom:10px;"></textarea>
        <button id="save-note" style="width:100%;padding:8px;background:#007bff;color:#fff;border:none;border-radius:4px;cursor:pointer;">Save Note</button>
        <ul id="notes-list" style="list-style:none;padding:0;margin-top:10px;"></ul>
    </div>
</div>

<script>
const pdfFrame = document.getElementById('pdf-frame');
const mainButton = document.getElementById('main-button');
const floatingButtons = document.getElementById('floating-buttons');

// Bookmark Elements
const bookmarkBtn = document.getElementById('bookmark-btn');
const viewBookmarksBtn = document.getElementById('view-bookmarks');
const modal = document.getElementById('bookmark-modal');
const closeModal = document.getElementById('close-modal');
const bookmarkList = document.getElementById('bookmark-list');

// Notes Elements
const notesBtn = document.getElementById('notes-btn');
const notesModal = document.getElementById('notes-modal');
const closeNotes = document.getElementById('close-notes');
const saveNoteBtn = document.getElementById('save-note');
const noteInput = document.getElementById('note-input');
const notesList = document.getElementById('notes-list');

const PDF_FILE = '<?php echo $bookFile; ?>';
const BOOKMARK_KEY = '<?php echo $bookmarkKey; ?>';
const NOTES_KEY = '<?php echo $notesKey; ?>';

let bookmarks = JSON.parse(localStorage.getItem(BOOKMARK_KEY) || '[]');
let notes = JSON.parse(localStorage.getItem(NOTES_KEY) || '[]');

// Toggle floating buttons
mainButton.addEventListener('click', ()=>{
    floatingButtons.style.display = floatingButtons.style.display==='flex'?'none':'flex';
});

// Add manual bookmark
bookmarkBtn.addEventListener('click', ()=>{
    let page = prompt("Enter the page number to bookmark:");
    if(page && !isNaN(page)){
        page=parseInt(page);
        if(!bookmarks.includes(page)){
            bookmarks.push(page);
            bookmarks.sort((a,b)=>a-b);
            localStorage.setItem(BOOKMARK_KEY, JSON.stringify(bookmarks));
            alert("Page "+page+" bookmarked!");
        } else alert("Page "+page+" already bookmarked.");
    }
});

// View bookmarks modal
viewBookmarksBtn.addEventListener('click', ()=>{
    renderBookmarks();
    modal.style.display='block';
});

// Close bookmark modal
closeModal.addEventListener('click', ()=>modal.style.display='none');
window.addEventListener('click', e=>{if(e.target==modal) modal.style.display='none';});

// Render bookmarks list
function renderBookmarks(){
    bookmarkList.innerHTML='';
    if(bookmarks.length===0){ bookmarkList.innerHTML='<li>No bookmarks yet</li>'; return; }
    bookmarks.forEach(p=>{
        const li=document.createElement('li');
        const span=document.createElement('span');
        span.textContent='Page '+p;
        span.style.flex='1';
        span.addEventListener('click', ()=>{ pdfFrame.src=PDF_FILE+'#page='+p; modal.style.display='none'; });

        const removeBtn=document.createElement('span');
        removeBtn.textContent='✖';
        removeBtn.className='remove-btn';
        removeBtn.addEventListener('click', ()=>{
            bookmarks = bookmarks.filter(b=>b!==p);
            localStorage.setItem(BOOKMARK_KEY, JSON.stringify(bookmarks));
            renderBookmarks();
        });

        li.appendChild(span);
        li.appendChild(removeBtn);
        bookmarkList.appendChild(li);
    });
}

// Open notes modal
notesBtn.addEventListener('click', ()=>{
    renderNotes();
    notesModal.style.display='block';
});

// Close notes modal
closeNotes.addEventListener('click', ()=>notesModal.style.display='none');
window.addEventListener('click', e=>{if(e.target==notesModal) notesModal.style.display='none';});

// Save note
saveNoteBtn.addEventListener('click', ()=>{
    const noteText = noteInput.value.trim();
    if(noteText){
        notes.push(noteText);
        localStorage.setItem(NOTES_KEY, JSON.stringify(notes));
        noteInput.value = '';
        renderNotes();
    }
});

// Render notes
function renderNotes(){
    notesList.innerHTML='';
    if(notes.length===0){ notesList.innerHTML='<li>No notes yet</li>'; return; }
    notes.forEach((note, index)=>{
        const li = document.createElement('li');
        li.style.display='flex';
        li.style.justifyContent='space-between';
        li.style.alignItems='center';
        li.style.padding='5px 0';
        li.style.borderBottom='1px solid #ddd';

        const span = document.createElement('span');
        span.textContent = note;
        span.style.flex='1';

        const removeBtn = document.createElement('span');
        removeBtn.textContent = '✖';
        removeBtn.style.color='red';
        removeBtn.style.cursor='pointer';
        removeBtn.style.marginLeft='10px';
        removeBtn.addEventListener('click', ()=>{
            notes.splice(index,1);
            localStorage.setItem(NOTES_KEY, JSON.stringify(notes));
            renderNotes();
        });

        li.appendChild(span);
        li.appendChild(removeBtn);
        notesList.appendChild(li);
    });
}
</script>
</body>
</html>
