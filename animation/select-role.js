document.querySelectorAll('.select-btn').forEach(button => {
    button.addEventListener('click', (e) => {
      const role = e.target.closest('.card').querySelector('h3').innerText;
      alert(`You selected: ${role}`);
    });
  });
  