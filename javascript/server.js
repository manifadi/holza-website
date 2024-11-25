const express = require('express');
const app = express();
const port = 3000; // Wählen Sie einen geeigneten Port
const filepath = "/Users/manuel/Documents/Arbeit und Projekte/Website - Holza/redesign_2023_10"

// Statische Dateien im "public"-Ordner servieren
app.use(express.static('/Users/manuel/Documents/Arbeit und Projekte/Website - Holza/redesign_2023_10'));

// Routen definieren
app.get('/', (req, res) => {
  res.sendFile(filepath + '/index.html');
});

// Server starten
app.listen(port, () => {
  console.log(`Server is running on port ${port}`);
});
