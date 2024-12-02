const express = require('express');
const bodyParser = require('body-parser');
const mysql = require('mysql2');

const app = express();
const port = 3000;

// Create a MySQL connection
const connection = mysql.createConnection({
    host: 'localhost',    // or '127.0.0.1'
    user: 'root',         // Adjust if necessary
    password: '',         // Usually empty in XAMPP
    database: 'seating_allocation',
    port: 3308            // Ensure this is the correct port
  });

// Middleware to parse JSON bodies
app.use(bodyParser.json());

// Student Registration Route
app.post('/api/student/register', async (req, res) => {
    const { name, email, phone_number, role, usn, semester, department } = req.body;
    
    // SQL query to insert the student data into the database
    const query = `
        INSERT INTO students (name, email, phone_number, role, usn, semester, department) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    `;
    
    db.query(query, [name, email, phone_number, role, usn, semester, department], (err, result) => {
        if (err) {
            return res.status(500).json({ msg: 'Error registering student', error: err });
        }
        res.status(200).json({ msg: 'Student registered successfully' });
    });
});

// Faculty Registration Route
app.post('/api/faculty/register', async (req, res) => {
    const { name, email, phone_number, role, faculty_id, department } = req.body;
    
    // SQL query to insert the faculty data into the database
    const query = `
        INSERT INTO faculty (name, email, phone_number, role, faculty_id, department) 
        VALUES (?, ?, ?, ?, ?, ?)
    `;
    
    db.query(query, [name, email, phone_number, role, password, faculty_id, department], (err, result) => {
        if (err) {
            return res.status(500).json({ msg: 'Error registering faculty', error: err });
        }
        res.status(200).json({ msg: 'Faculty registered successfully' });
    });
});

// Start the server
app.listen(port, () => {
    console.log(`Server running on http://localhost:${port}`);
});
app.get('/',function(req,res){
    res.sendFile(__dirname+'/index.html');
});