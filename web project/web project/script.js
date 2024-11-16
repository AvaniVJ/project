// Dynamically generate the grid for the background animation
const gridContainer = document.querySelector('.background-grid');

const createGrid = () => {
  const columns = 10; // Number of columns
  const rows = 10; // Number of rows
  const totalItems = columns * rows;

  for (let i = 0; i < totalItems; i++) {
    const gridItem = document.createElement('div');
    gridContainer.appendChild(gridItem);
  }
};

// Call the function to create the grid
createGrid();
