var mysql=require("mysql2");
 var con =mysql.createConnection({
    host:"localhost",
    user:"root",
    password:"",
    database:"exam_seating"
 });
 // Connect to the database
con.connect((err) => {
    if (err) {
      console.error('Error connecting to the database:', err.message);
    } else {
      console.log('Connected to the MySQL database.');
    }
  });

  module.exports = con;