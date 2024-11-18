const express = require('express');
const mysql = require('mysql2');
const bcrypt = require('bcryptjs');
const bodyParser = require('body-parser');

const app = express();
const port = 5000;

app.use(bodyParser.json());

// Create MySQL connection
const db = mysql.createConnection({
  host: 'localhost',
  user: 'root',
  password: 'Diya@2004', // replace with your MySQL root password
  database: 'exam_seating'
});

db.connect(err => {
  if (err) {
    console.error('Error connecting to MySQL:', err);
    return;
  }
  console.log('Connected to MySQL');
});

// POST route for login
app.post('/api/login', (req, res) => {
  const { username, password, role } = req.body;
  const table = role === 'Admin' ? 'admins' : role === 'Faculty' ? 'faculty' : 'students';

  const sql = `SELECT * FROM ${table} WHERE username = ?`;
  db.query(sql, [username], (err, results) => {
    if (err) return res.status(500).send('Database query error');
    if (results.length === 0) return res.status(400).send('User not found');

    const user = results[0];
    bcrypt.compare(password, user.password, (err, match) => {
      if (err) return res.status(500).send('Error comparing password');
      if (!match) return res.status(400).send('Invalid credentials');

      // Return user details (password excluded for security)
      res.json({
        id: user.id,
        username: user.username,
        role: role
      });
    });
  });
});

// GET route for admin dashboard stats
app.get('/api/stats', (req, res) => {
  const stats = {
    totalStudents: 1200, // You can replace this with a database query
    totalFaculty: 80, // Replace with actual data
    totalExamHalls: 15 // Replace with actual data
  };
  res.json(stats);
});

app.listen(port, () => {
  console.log(`Server running on http://localhost:${port}`);
});
