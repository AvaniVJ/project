function fetchSeatAllotment() {
    const studentId = document.getElementById('studentId').value;
    
    if (!studentId) {
      document.getElementById('seatInfo').innerText = 'Please enter a Student ID.';
      return;
    }
  
    fetch(`http://localhost:3000/api/seat/${studentId}`)
      .then(response => {
        if (!response.ok) {
          throw new Error('Student not found');
        }
        return response.json();
      })
      .then(data => {
        document.getElementById('seatInfo').innerText = 
          `Name: ${data.name}\nSeat Number: ${data.seat_number}\nRoom: ${data.room}`;
      })
      .catch(error => {
        document.getElementById('seatInfo').innerText = error.message;
      });
  }
  