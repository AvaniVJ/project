const express = require('express');
const db = require('./db'); // Import the database connection
const cors = require('cors');
const app = express();

app.use(cors());
app.use(express.json());

// API to fetch student seat allotment details by student ID
app.get('/api/seat/:student_id', (req, res) => {
  const studentId = req.params.student_id;
  const sql = 'SELECT name, seat_number, room FROM students WHERE student_id = ?';

  db.query(sql, [studentId], (err, result) => {
    if (err) {
      res.status(500).json({ error: 'Database error' });
    } else if (result.length === 0) {
      res.status(404).json({ message: 'Student not found' });
    } else {
      res.json(result[0]);
    }
  });
});

// Start the server
const PORT = 3000;
app.listen(PORT, () => {
  console.log(`Server running on http://localhost:${PORT}`);
});
