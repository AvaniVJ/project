const mysql = require('mysql2');

const connection = mysql.createConnection({
  host: 'localhost',    // or '127.0.0.1'
  user: 'root',         // Adjust if necessary
  password: '',         // Usually empty in XAMPP
  database: 'seating_allocation',
  port: 3308            // Ensure this is the correct port
});

connection.connect((err) => {
  if (err) {
    console.error('Error connecting to the database:', err);
    return;
  }
  console.log('Connected to MySQL!');
});

module.exports = connection;  // Export if used in other files
