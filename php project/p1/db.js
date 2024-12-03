const mysql = require('mysql2');

// Create a connection to the database
const db = mysql.createConnection({
  host: 'localhost',       // Host for the database
  user: 'root',            // XAMPP default username for MySQL
  password: '',            // Leave empty if no password is set in XAMPP
  database: 'exam_system'  // Your database name
});

// Connect to the database
db.connect((err) => {
  if (err) {
    console.error('Error connecting to the database:', err.message);
  } else {
    console.log('Connected to the MySQL database.');
  }
});

// Export the connection
module.exports = db;
