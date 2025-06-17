function filterTable() {
    const input = document.getElementById('search-input').value.toLowerCase();
    const table = document.getElementById('example1');
    const rows = table.getElementsByTagName('tr');

    for (let i = 1; i < rows.length; i++) { // Start from 1 to skip table header
      const cells = rows[i].getElementsByTagName('td');
      let found = false;

      for (let j = 0; j < cells.length; j++) {
        const cell = cells[j];
        if (cell.textContent.toLowerCase().includes(input)) {
          found = true;
          break;
        }
      }

      if (found) {
        rows[i].style.display = '';
      } else {
        rows[i].style.display = 'none';
      }
    }
  }

  // Optional: Filter the table as you type
  document.getElementById('search-input').addEventListener('input', filterTable);